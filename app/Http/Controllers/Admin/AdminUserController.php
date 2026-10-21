<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->when(request('q'), function ($query, $term) {
                $query->where(function ($search) use ($term) {
                    $search->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $user->status = ($user->status ?? 'active') === 'active'
            ? 'inactive'
            : 'active';

        $user->save();

        return back()->with('success', 'Client status updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()
            ->route('admin.users.index', request()->only('q', 'page'))
            ->with('success', 'Client account deleted successfully.');
    }
}
