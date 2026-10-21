<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'online' => User::where('last_seen_at', '>=', now()->subMinutes(2))->count(),
            'messages' => Message::count(),
            'groups' => Conversation::where('type', 'group')->count(),
        ];
        $recentUsers = User::latest()->limit(8)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers'));
    }
}
