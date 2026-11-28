<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role');

        // Search by name or email.
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role.
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->input('role_id'));
        }

        // Filter by account status.
        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status'));
        }

        $users = $query->latest()
            ->paginate(10)
            ->withQueryString();

        $roles = Role::orderBy('role_name')->get();

        return view('users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::orderBy('role_name')->get();

        return view('users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $user = new User();

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = $validated['password'];
        $user->role_id = $validated['role_id'];
        $user->is_active = true;

        $user->save();

        return redirect()
            ->route('users.index')
            ->with('success', 'User account created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('role_name')->get();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($request, $user, $validated) {

            // Serialize changes affecting Owner accounts.
            $ownerRole = Role::where('role_name', 'Owner')
                ->lockForUpdate()
                ->firstOrFail();

            $user = User::lockForUpdate()->findOrFail($user->id);

            $roleChanged = (int) $user->role_id
                !== (int) $validated['role_id'];

            // Prevent changing your own role.
            if ($request->user()->id === $user->id && $roleChanged) {
                throw ValidationException::withMessages([
                    'role_id' => 'You cannot change your own role.',
                ]);
            }

            // Protect the last active Owner.
            if (
                (int) $user->role_id === (int) $ownerRole->role_id
                && $user->is_active
                && $roleChanged
            ) {
                $activeOwners = User::where('role_id', $ownerRole->role_id)
                    ->where('is_active', true)
                    ->count();

                if ($activeOwners <= 1) {
                    throw ValidationException::withMessages([
                        'role_id' => 'The last active Owner cannot change roles.',
                    ]);
                }
            }

            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->role_id = $validated['role_id'];

            // Only update the password when provided.
            if (!empty($validated['password'])) {
                $user->password = $validated['password'];
            }

            $user->save();
        });

        return redirect()
            ->route('users.index')
            ->with('success', 'User account updated successfully.');
    }

    public function updateStatus(Request $request, User $user)
    {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $isActive = (bool) $validated['is_active'];

        DB::transaction(function () use ($request, $user, $isActive) {

            $ownerRole = Role::where('role_name', 'Owner')
                ->lockForUpdate()
                ->firstOrFail();

            $user = User::lockForUpdate()->findOrFail($user->id);

            // Prevent self-deactivation.
            if ($request->user()->id === $user->id && !$isActive) {
                throw ValidationException::withMessages([
                    'is_active' => 'You cannot deactivate your own account.',
                ]);
            }

            // Protect the last active Owner.
            if (
                (int) $user->role_id === (int) $ownerRole->role_id
                && $user->is_active
                && !$isActive
            ) {
                $activeOwners = User::where('role_id', $ownerRole->role_id)
                    ->where('is_active', true)
                    ->count();

                if ($activeOwners <= 1) {
                    throw ValidationException::withMessages([
                        'is_active' => 'The last active Owner cannot be deactivated.',
                    ]);
                }
            }

            $user->is_active = $isActive;
            $user->save();
        });

        return redirect()
            ->route('users.index')
            ->with(
                'success',
                $isActive
                    ? 'User account activated successfully.'
                    : 'User account deactivated successfully.'
            );
    }
}
