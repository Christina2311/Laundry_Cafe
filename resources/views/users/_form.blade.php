@php
$isEditing = isset($user);
$isSelf = $isEditing && auth()->id() === $user->id;
@endphp

<div class="space-y-6">

    {{-- Full Name --}}
    <div>
        <x-input-label for="name" value="Full Name" />

        <x-text-input
            id="name"
            name="name"
            type="text"
            class="mt-1 block w-full"
            :value="old('name', $user->name ?? '')"
            required
            autofocus />

        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    {{-- Email --}}
    <div>
        <x-input-label for="email" value="Email Address" />

        <x-text-input
            id="email"
            name="email"
            type="email"
            class="mt-1 block w-full"
            :value="old('email', $user->email ?? '')"
            required />

        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    {{-- Role --}}
    <div>
        <x-input-label for="role_id" value="Role" />

        <select
            id="role_id"
            name="role_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
            @disabled($isSelf)
            required>
            <option value="">Select a role</option>

            @foreach ($roles as $role)
            <option
                value="{{ $role->role_id }}"
                @selected(old('role_id', $user->role_id ?? '') == $role->role_id)
                >
                {{ $role->role_name }}
            </option>
            @endforeach
        </select>

        @if ($isSelf)
        <input type="hidden" name="role_id" value="{{ $user->role_id }}">

        <p class="mt-1 text-sm text-gray-500">
            You cannot change your own role.
        </p>
        @endif

        <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
    </div>

    {{-- Password --}}
    <div>
        <x-input-label for="password" value="Password" />

        <x-text-input
            id="password"
            name="password"
            type="password"
            class="mt-1 block w-full"
            autocomplete="new-password"
            :required="!$isEditing" />

        @if ($isEditing)
        <p class="mt-1 text-sm text-gray-500">
            Leave blank to keep the current password.
        </p>
        @else
        <p class="mt-1 text-sm text-gray-500">
            At least 8 characters with letters and numbers.
        </p>
        @endif

        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    {{-- Confirm Password --}}
    <div>
        <x-input-label
            for="password_confirmation"
            value="Confirm Password" />

        <x-text-input
            id="password_confirmation"
            name="password_confirmation"
            type="password"
            class="mt-1 block w-full"
            autocomplete="new-password"
            :required="!$isEditing" />

        <x-input-error
            :messages="$errors->get('password_confirmation')"
            class="mt-2" />
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-4">

        <x-primary-button>
            {{ $isEditing ? 'Save Changes' : 'Create User' }}
        </x-primary-button>

        <a href="{{ route('users.index') }}"
            class="text-sm text-gray-600 hover:underline">
            Cancel
        </a>

    </div>

</div>