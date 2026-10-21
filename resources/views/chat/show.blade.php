@extends('layouts.app')

@php
    $chatTitle =
        $chatTitle ??
        ($isGroup ?? false ? $conversation->name ?? 'Group Conversation' : $other->name ?? 'Private Conversation');
@endphp

@section('title', $chatTitle)
@section('heading', $chatTitle)

@section('content')
    <div class="chat-room">

        <aside class="room-details" id="roomDetails">

            <a href="{{ route('chat.index') }}" class="back-link">
                <i class="fa-solid fa-arrow-left"></i>
                💬 All conversations
            </a>

            @if (session('success'))
                <div class="conversation-notice success" role="status">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="conversation-notice error" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="room-profile">
                @if ($isGroup)
                    <div class="room-avatar group-avatar">
                        <i class="fa-solid fa-users"></i>
                    </div>
                @else
                    <img class="room-avatar"
                        src="{{ $other?->avatar_url ?? ($currentUser->avatar_url ?? asset('images/default-avatar.png')) }}"
                        alt="{{ $chatTitle }}" loading="lazy">
                @endif

                <h3>
                    {{ $isGroup ? '👥' : '💜' }}
                    {{ $chatTitle }}
                </h3>

                <p>
                    @if ($isGroup)
                        {{ $conversation->participants->count() }} members 👥
                    @else
                        {{ $isOnline ? '🟢 Online now' : '💬 Private conversation' }}
                    @endif
                </p>

                <span class="online-label">
                    <span></span>
                    {{ $isGroup ? '👥 GROUP SPACE' : '💌 DIRECT MESSAGE' }}
                </span>
            </div>

            <div class="room-conversation-actions">

                @if ($isGroup && $isCreator)
                    <details class="conversation-edit room-edit">
                        <summary class="secondary-button">
                            <i class="fa-solid fa-pen"></i>
                            <span>Edit group name</span>
                        </summary>

                        <form method="POST" action="{{ route('chat.update', $conversation) }}"
                            class="conversation-edit-form">
                            @csrf
                            @method('PATCH')

                            <label for="roomGroupName">Group name</label>

                            <input type="text" id="roomGroupName" name="name"
                                value="{{ old('name', $conversation->name) }}" maxlength="100" required>

                            <button type="submit" class="primary-button full-button">
                                <i class="fa-solid fa-check"></i>
                                Save changes
                            </button>
                        </form>
                    </details>
                @endif

                <form method="POST" action="{{ route('chat.destroy', $conversation) }}"
                    onsubmit="return confirm('Remove this conversation from your inbox? Other members will keep their conversation.');">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="conversation-delete-button room-delete-button"
                        title="Remove conversation" aria-label="Remove conversation">
                        <i class="fa-solid fa-trash" aria-hidden="true"></i>
                        <span>Remove conversation</span>
                    </button>
                </form>
            </div>

            @if ($isGroup)
                <div class="room-members">

                    <div class="members-title">
                        👥 MEMBERS
                        <span>{{ $conversation->participants->count() }}</span>
                    </div>

                    @foreach ($conversation->participants as $member)
                        <div class="room-member">

                            <img src="{{ $member->avatar_url ?? asset('images/default-avatar.png') }}"
                                alt="" loading="lazy">

                            <span>
                                {{ $member->name }}

                                @if ((int) $member->id === (int) $currentUser->id)
                                    <small>(You 😊)</small>
                                @endif
                            </span>

                            @if ($isCreator && (int) $member->id !== (int) $currentUser->id)
                                <form method="POST"
                                    action="{{ route('chat.members.remove', [
                                        'conversation' => $conversation->id,
                                        'user' => $member->id,
                                    ]) }}"
                                    onsubmit="return confirm('Remove this member from the group?');">
                                    @csrf
                                    @method('DELETE')

                                    <button class="tiny-icon" type="submit" title="Remove member"
                                        aria-label="Remove {{ $member->name }} from group">
                                        <i class="fa-solid fa-user-minus"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach

                    @if ($isCreator)
                        <form class="add-member-form" method="POST"
                            action="{{ route('chat.members.add', $conversation) }}">
                            @csrf

                            <label for="addGroupMember">➕ Invite someone</label>

                            <select name="user_id" id="addGroupMember" required>
                                <option value="">Choose a member...</option>

                                @foreach ($users->reject(fn($u) => $conversation->participants->contains('id', $u->id)) as $person)
                                    <option value="{{ $person->id }}">
                                        {{ $person->name }}
                                    </option>
                                @endforeach
                            </select>

                            <button class="secondary-button full-button" type="submit">
                                <i class="fa-solid fa-user-plus"></i>
                                Add member 🫂
                            </button>
                        </form>
                    @endif
                </div>
            @endif

            <div class="room-private-note">
                <i class="fa-solid fa-shield-halved"></i>

                <span>
                    <b>🔒 A little space to connect</b>
                    <small>
                        💜 Keep conversations respectful, kind, and considerate.
                    </small>
                </span>
            </div>
        </aside>

        <section class="message-panel">

            <button type="button" class="room-details-toggle" id="roomDetailsToggle"
                aria-controls="roomDetails" aria-expanded="false">
                <i class="fa-solid fa-circle-info"></i>
                <span>Conversation details</span>
                <i class="fa-solid fa-chevron-down toggle-chevron"></i>
            </button>

            <div class="message-panel-head">
                <div class="message-head-person">

                    @if ($isGroup)
                        <div class="mini-group-icon">
                            <i class="fa-solid fa-users"></i>
                        </div>
                    @else
                        <img src="{{ $other?->avatar_url ?? ($currentUser->avatar_url ?? asset('images/default-avatar.png')) }}"
                            alt="" loading="lazy">
                    @endif

                    <div>
                        <b>
                            {{ $isGroup ? '👥' : '💜' }}
                            {{ $chatTitle }}
                        </b>

                        <small>
                            {{ $isGroup ? 'Group conversation 🫂' : 'Direct conversation 💌' }}
                        </small>
                    </div>
                </div>
            </div>

            <div class="messages-scroll" id="messagesScroll"
                data-conversation-id="{{ $conversation->id }}"
                data-current-user="{{ $currentUser->id }}">

                <div class="date-divider">
                    <span>💬 YOUR CONVERSATION</span>
                </div>

                @forelse ($messages as $message)
                    <div class="message-row {{ (int) $message->user_id === (int) $currentUser->id ? 'mine' : '' }}"
                        data-message-id="{{ $message->id }}">

                        <img class="message-avatar"
                            src="{{ $message->user?->avatar_url ?? asset('images/default-avatar.png') }}"
                            alt="" loading="lazy">

                        <div class="message-content">
                            <div class="message-meta">
                                <b>
                                    @if ((int) $message->user_id === (int) $currentUser->id)
                                        😊 You
                                    @else
                                        {{ $message->user?->name ?? 'Unknown user' }}
                                    @endif
                                </b>

                                <time>
                                    {{ $message->created_at?->format('g:i A') ?? '' }}
                                </time>
                            </div>

                            <div class="message-bubble">{{ $message->body }}</div>
                        </div>
                    </div>
                @empty
                    <div class="empty-messages" id="emptyMessages">
                        <div class="empty-icon">
                            <i class="fa-regular fa-paper-plane"></i>
                        </div>

                        <h3>👋 Start the conversation!</h3>

                        <p>
                            💬 Send the first message and make someone's day
                            a little brighter. 💜
                        </p>
                    </div>
                @endforelse
            </div>

            
<form class="message-composer" id="messageForm" method="POST"
    action="{{ route('chat.messages.store', $conversation) }}">
    @csrf

    <div class="composer-box">
        <div class="composer-tools" role="toolbar" aria-label="Message tools">
            <button type="button" class="composer-tool" id="imageButton"
                title="Choose an image" aria-label="Choose an image">
                <i class="fa-regular fa-image"></i>
            </button>

            <button type="button" class="composer-tool" id="attachmentButton"
                title="Attach a file" aria-label="Attach a file">
                <i class="fa-solid fa-paperclip"></i>
            </button>

            <button type="button" class="composer-tool" id="locationButton"
                title="Share your location" aria-label="Share your location">
                <i class="fa-solid fa-location-dot"></i>
            </button>

            <button type="button" class="composer-tool" id="voiceButton"
                title="Record a voice message" aria-label="Record a voice message">
                <i class="fa-solid fa-microphone"></i>
            </button>
        </div>

        <input type="file" id="imageInput" accept="image/*" hidden>
        <input type="file" id="attachmentInput"
            accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.png,.jpg,.jpeg,.webp" hidden>

        <div id="composerPreview" class="composer-preview" hidden>
            <img id="composerImagePreview" alt="Selected image preview" hidden>
            <div class="composer-file-info">
                <i class="fa-solid fa-file" id="composerFileIcon"></i>
                <span id="composerFileName"></span>
            </div>
            <button type="button" id="removeComposerFile"
                class="composer-remove-file" aria-label="Remove selected file">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div id="voiceRecorder" class="voice-recorder" hidden>
            <span class="voice-recording-dot"></span>
            <span id="voiceStatus">Ready to record</span>
            <span id="voiceTimer">00:00</span>
            <button type="button" id="stopVoiceButton" aria-label="Stop recording">
                <i class="fa-solid fa-stop"></i> Stop
            </button>
            <button type="button" id="cancelVoiceButton" aria-label="Cancel recording">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <audio id="voicePreview" class="voice-preview" controls hidden></audio>

        <textarea name="body" id="messageInput" rows="1" maxlength="5000"
            placeholder="Write a message... 💬" required>{{ old('body') }}</textarea>

        <div class="composer-bottom">
            <span class="composer-security">
                <i class="fa-solid fa-lock"></i>
                Your conversation is for its members 🔒
            </span>

            <button type="submit" class="send-button"
                aria-label="Send message" title="Send message">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </div>

        <p id="composerStatus" class="composer-status" role="status" aria-live="polite"></p>
    </div>
</form>


        </section>
    </div>
@endsection

@push('styles')
    <style>
        .room-details-toggle {
            display: none;
            width: 100%;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 16px;
            background: transparent;
            border-radius: 12px;
            color: #a99bbf;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: none;
            outline: none;
            transition: border-color 0.25s ease, color 0.25s ease;
        }

        .room-details-toggle:hover {
            background: transparent;
            border-color: rgba(190, 140, 255, 0.45);
            color: #cba6ff;
        }

        .room-details-toggle:focus,
        .room-details-toggle:active {
            background: transparent;
            box-shadow: none;
            outline: none;
        }

        .room-details-toggle .toggle-chevron {
            transition: transform 0.25s ease;
        }

        .room-details-toggle[aria-expanded="true"] .toggle-chevron {
            transform: rotate(180deg);
        }

        @media (max-width: 768px) {
            .room-details-toggle {
                display: flex;
            }
        }

        @media (min-width: 769px) {
            .chat-room {
                display: grid;
                grid-template-columns: minmax(250px, 300px) minmax(0, 1fr);
                align-items: stretch;
                gap: 20px;
            }

            .chat-room > .room-details {
                display: block !important;
                order: 0;
                min-width: 0;
            }

            .chat-room > .message-panel {
                order: 0;
                min-width: 0;
            }

            .room-details-toggle {
                display: none !important;
            }
        }

        @media (max-width: 768px) {
            .chat-room {
                display: flex;
                flex-direction: column;
                gap: 12px;
                min-width: 0;
            }

            .chat-room > .message-panel {
                display: flex;
                flex-direction: column;
                order: 1;
                width: 100%;
                min-width: 0;
            }

            .room-details-toggle {
                display: flex;
                flex-shrink: 0;
            }

            .chat-room > .room-details {
                display: none;
                order: 2;
                width: 100%;
                min-width: 0;
                max-height: 60dvh;
                overflow-y: auto;
                overscroll-behavior: contain;
                padding: 16px;
                background: transparent;
                border: 1px solid rgba(183, 126, 255, 0.15);
                border-radius: 18px;
                box-sizing: border-box;
                box-shadow: none;
                backdrop-filter: none;
                -webkit-backdrop-filter: none;
            }

            .chat-room > .room-details.is-visible {
                display: block;
                animation: roomDetailsReveal 0.22s ease;
            }

            .chat-room .message-panel-head {
                min-width: 0;
                gap: 10px;
            }

            .chat-room .message-head-person {
                min-width: 0;
            }

            .chat-room .message-head-person > div {
                min-width: 0;
                overflow-wrap: anywhere;
            }

            .chat-room .messages-scroll {
                width: 100%;
                min-width: 0;
                overflow-x: hidden;
            }

            .chat-room .message-composer {
                width: 100%;
                min-width: 0;
            }

            .chat-room .room-details .full-button {
                width: 100%;
            }
        }

        @keyframes roomDetailsReveal {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .room-details-toggle,
            .room-details-toggle .toggle-chevron,
            .room-details.is-visible {
                animation: none;
                transition: none;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('roomDetailsToggle');
            const details = document.getElementById('roomDetails');

            if (!toggle || !details) {
                return;
            }

            const mobileQuery = window.matchMedia('(max-width: 768px)');
            const label = toggle.querySelector('span');

            function setDetailsVisible(visible) {
                details.classList.toggle('is-visible', visible);
                toggle.setAttribute('aria-expanded', String(visible));

                if (label) {
                    label.textContent = visible ?
                        'Hide conversation details' :
                        'Conversation details';
                }
            }

            toggle.addEventListener('click', function() {
                if (!mobileQuery.matches) {
                    return;
                }

                setDetailsVisible(!details.classList.contains('is-visible'));
            });

            function syncLayout() {
                if (mobileQuery.matches) {
                    setDetailsVisible(false);
                } else {
                    details.classList.remove('is-visible');
                    toggle.setAttribute('aria-expanded', 'false');

                    if (label) {
                        label.textContent = 'Conversation details';
                    }
                }
            }

            if (typeof mobileQuery.addEventListener === 'function') {
                mobileQuery.addEventListener('change', syncLayout);
            } else {
                mobileQuery.addListener(syncLayout);
            }

            syncLayout();
        });
    </script>
@endpush