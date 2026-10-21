<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $google = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->withErrors(['email' => 'Google sign-in failed. Check your OAuth configuration and try again.']);
        }

        $user = User::where('google_id', $google->getId())
            ->orWhere('email', $google->getEmail())
            ->first();

        if (!$user) {
            $user = User::create([
                'name' => $google->getName() ?: 'GhostOrchid User',
                'email' => $google->getEmail(),
                'google_id' => $google->getId(),
                'avatar' => $google->getAvatar(),
                'password' => Str::random(40),
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
        } else {
            $user->forceFill([
                'google_id' => $google->getId(),
                'avatar' => $google->getAvatar() ?: $user->avatar,
            ])->save();
        }

        if (($user->status ?? 'active') !== 'active') {
            return redirect()->route('login')->withErrors(['email' => 'Your account is inactive.']);
        }

        Auth::login($user, true);
        request()->session()->regenerate();
        $user->forceFill(['last_login_at' => now(), 'last_seen_at' => now()])->save();

        return redirect()->route('chat.index');
    }
}
