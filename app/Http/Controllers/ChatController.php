<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class ChatController extends BaseController

{
    public function index()
    {
        $user = auth()->user();

        $conversations = $user->conversations()
            ->with([
                'latestMessage.user',
                'participants',
            ])
            ->orderByDesc('conversations.last_message_at')
            ->get();

        $users = User::query()
                ->where('id', '!=', $user->id)
                ->where('status', 'active')
                ->orderBy('name')
                ->get();
        return view('chat.index', compact(
            'conversations',
            'users'
        ));
        
    }

    public function show(Conversation $conversation)
    {
        $this->authorizeParticipant($conversation);

        $currentUser = auth()->user();

        $conversation->load('participants');

        $isGroup = $conversation->type === 'group';

        $isCreator = (int) $conversation->created_by
            === (int) $currentUser->id;

        $other = null;

        if (!$isGroup) {
            $other = $conversation->participants
                ->first(
                    fn ($participant) =>
                        (int) $participant->id !== (int) $currentUser->id
                );
        }

        $chatTitle = $isGroup
            ? ($conversation->name ?: 'Group Conversation')
            : ($other?->name ?? 'Private Conversation');

        $isOnline = false;

        if (!$isGroup && $other) {
            $lastSeen = $other->last_seen_at;

            $isOnline = $lastSeen !== null
                && $lastSeen->gt(now()->subMinutes(5));
        }

        $messages = Message::query()
            ->where('conversation_id', $conversation->id)
            ->with('user')
            ->orderByDesc('id')
            ->limit(300)
            ->get()
            ->reverse()
            ->values();

        $users = User::query()
            ->where('status', 'active')
            ->where('id', '!=', $currentUser->id)
            ->orderBy('name')
            ->limit(100)
            ->get();

        $currentUser->conversations()
            ->updateExistingPivot($conversation->id, [
                'last_read_at' => now(),
            ]);

        return view('chat.show', compact(
            'conversation',
            'messages',
            'users',
            'currentUser',
            'isGroup',
            'isCreator',
            'other',
            'chatTitle',
            'isOnline'
        ));
    }

    public function startPrivate(Request $request)
    {
        $data = $request->validate([
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
        ]);

        $currentId = (int) auth()->id();
        $otherId = (int) $data['user_id'];

        abort_if(
            $otherId === $currentId,
            422,
            'You cannot start a conversation with yourself.'
        );

        $conversation = Conversation::query()
            ->where('type', 'private')
            ->whereHas('participants', function ($query) use ($currentId) {
                $query->where('users.id', $currentId);
            })
            ->whereHas('participants', function ($query) use ($otherId) {
                $query->where('users.id', $otherId);
            })
            ->withCount('participants')
            ->get()
            ->first(
                fn ($item) => (int) $item->participants_count === 2
            );

        if (!$conversation) {
            $conversation = DB::transaction(
                function () use ($currentId, $otherId) {
                    $conversation = Conversation::create([
                        'type' => 'private',
                        'created_by' => $currentId,
                    ]);

                    $conversation->participants()->attach([
                        $currentId => [
                            'role' => 'member',
                            'joined_at' => now(),
                        ],
                        $otherId => [
                            'role' => 'member',
                            'joined_at' => now(),
                        ],
                    ]);

                    return $conversation;
                }
            );
        }

        return redirect()->route('chat.show', $conversation);
    }

    public function createGroup(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'members' => [
                'nullable',
                'array',
                'max:50',
            ],
            'members.*' => [
                'integer',
                'distinct',
                'exists:users,id',
            ],
        ]);

        $currentId = (int) auth()->id();

        $memberIds = collect($data['members'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->reject(fn ($id) => $id === $currentId)
            ->unique()
            ->values();

        $conversation = DB::transaction(
            function () use ($data, $memberIds, $currentId) {
                $conversation = Conversation::create([
                    'name' => trim($data['name']),
                    'type' => 'group',
                    'created_by' => $currentId,
                ]);

                $members = [
                    $currentId => [
                        'role' => 'admin',
                        'joined_at' => now(),
                    ],
                ];

                foreach ($memberIds as $memberId) {
                    $members[$memberId] = [
                        'role' => 'member',
                        'joined_at' => now(),
                    ];
                }

                $conversation->participants()->attach($members);

                return $conversation;
            }
        );

        return redirect()->route('chat.show', $conversation);
    }


public function sendMessage(
    Request $request,
    Conversation $conversation
) {
    $this->authorizeParticipant($conversation);

    $data = $request->validate([
        'body' => ['required', 'string', 'max:5000'],
    ]);

    $body = trim($data['body']);

    if ($body === '') {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Message cannot be empty.',
            ], 422);
        }

        return back()
            ->withErrors(['body' => 'Message cannot be empty.'])
            ->withInput();
    }

    $message = DB::transaction(function () use ($body, $conversation) {
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => auth()->id(),
            'body' => $body,
            'type' => 'text',
        ]);

        $conversation->update([
            'last_message_at' => now(),
        ]);

        auth()->user()->forceFill([
            'last_seen_at' => now(),
        ])->save();

        return $message->load('user');
    });

    broadcast(new MessageSent($message))->toOthers();

    if ($request->expectsJson()) {
        return response()->json([
            'message' => [
                'id' => $message->id,
                'conversation_id' => $message->conversation_id,
                'user_id' => $message->user_id,
                'body' => $message->body,
                'type' => $message->type,
                'created_at' => $message->created_at
                    ->timezone('Asia/Manila')
                    ->format('Y-m-d H:i:s'),
                'user' => [
                    'id' => $message->user->id,
                    'name' => $message->user->name,
                    'avatar_url' => $message->user->avatar_url,
                ],
            ],
        ], 201);
    }

    return redirect()->route('chat.show', $conversation);
}

    public function addMember(
        Request $request,
        Conversation $conversation
    ) {
        $this->authorizeParticipant($conversation);

        abort_unless(
            $conversation->type === 'group',
            422,
            'Members can only be added to groups.'
        );

        $data = $request->validate([
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
        ]);

        $currentId = (int) auth()->id();

        $this->authorizeGroupManager($conversation, $currentId);

        $userId = (int) $data['user_id'];

        $alreadyMember = $conversation->participants()
            ->where('users.id', $userId)
            ->exists();

        abort_if(
            $userId === $currentId,
            422,
            'You are already a member of this group.'
        );

        if (!$alreadyMember) {
            $conversation->participants()->attach($userId, [
                'role' => 'member',
                'joined_at' => now(),
            ]);
        }

        return back()->with(
            'success',
            $alreadyMember
                ? 'This user is already a group member.'
                : 'Member added successfully.'
        );
    }

    public function removeMember(
        Conversation $conversation,
        User $user
    ) {
        $this->authorizeParticipant($conversation);

        abort_unless(
            $conversation->type === 'group',
            422,
            'Members can only be removed from groups.'
        );

        $currentId = (int) auth()->id();

        $this->authorizeGroupManager($conversation, $currentId);

        abort_if(
            (int) $user->id === (int) $conversation->created_by,
            422,
            'The group creator cannot be removed.'
        );

        $isMember = $conversation->participants()
            ->where('users.id', $user->id)
            ->exists();

        abort_unless(
            $isMember,
            404,
            'This user is not a member of the group.'
        );

        $conversation->participants()->detach($user->id);

        return back()->with(
            'success',
            'Member removed successfully.'
        );
    }

    public function updateConversation(
        Request $request,
        Conversation $conversation
    ) {
        $this->authorizeParticipant($conversation);

        $currentId = (int) $request->user()->id;

        abort_unless(
            $conversation->type === 'group'
                && (int) $conversation->created_by === $currentId,
            403,
            'Only the group creator can edit this conversation.'
        );

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:1',
                'max:100',
            ],
        ]);

        $name = trim($validated['name']);

        abort_if(
            $name === '',
            422,
            'The group name cannot be empty.'
        );

        $conversation->update([
            'name' => $name,
        ]);

        return redirect()
            ->route('chat.show', $conversation)
            ->with(
                'success',
                'Conversation name updated successfully.'
            );
    }

    public function destroyConversation(
        Request $request,
        Conversation $conversation
    ) {
        $user = $request->user();

        $this->authorizeParticipant($conversation);

        DB::transaction(function () use ($conversation, $user) {
            $conversation->participants()->detach($user->id);

            if (!$conversation->participants()->exists()) {
                $conversation->messages()->delete();
                $conversation->delete();
            }
        });

        return redirect()
            ->route('chat.index')
            ->with(
                'success',
            );
    }

    private function authorizeParticipant(
        Conversation $conversation
    ): void {
        abort_unless(
            $conversation->participants()
                ->where('users.id', auth()->id())
                ->exists(),
            403,
            'You are not a member of this conversation.'
        );
    }

    private function authorizeGroupManager(
        Conversation $conversation,
        int $userId
    ): void {
        $isAdmin = $conversation->participants()
            ->where('users.id', $userId)
            ->wherePivot('role', 'admin')
            ->exists();

        abort_unless(
            $isAdmin
                || (int) $conversation->created_by === $userId,
            403,
            'Only group administrators can manage members.'
        );
    }
}
