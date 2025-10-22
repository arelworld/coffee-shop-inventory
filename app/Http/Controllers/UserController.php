<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        
        $stats = [
            'total_users' => User::count(),
            'managers' => User::where('role', 'manager')->count(),
            'staff' => User::where('role', 'staff')->count(),
            'active_users' => User::where('is_active', true)->count(),
        ];

        return view('users.index', compact('users', 'stats'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:manager,staff',
            'phone' => 'nullable|string|max:20',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'is_active' => true,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Staff member added successfully!');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:manager,staff',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        // Prevent managers from demoting themselves
        if (auth()->id() === $user->id && $request->role !== 'manager') {
            return redirect()->back()->with('error', 'You cannot change your own role from manager.');
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully!');
    }

    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Password updated successfully!');
    }

    public function toggleStatus(User $user)
    {
        // Prevent managers from deactivating themselves
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $action = $user->is_active ? 'activated' : 'deactivated';
        return redirect()->route('users.index')
            ->with('success', "User {$action} successfully!");
    }
}