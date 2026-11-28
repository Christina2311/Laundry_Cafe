<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            User Management
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
            <div class="mb-4 rounded bg-green-100 p-4 text-green-700">
                {{ session('success') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="mb-4 rounded bg-red-100 p-4 text-red-700">
                {{ $errors->first() }}
            </div>
            @endif

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">

                <div class="mb-6 flex items-center justify-between">
                    <h3 class="text-lg font-semibold">User Accounts</h3>

                    <a href="{{ route('users.create') }}"
                        class="rounded bg-gray-800 px-4 py-2 text-white">
                        + Add User
                    </a>
                </div>

                {{-- Search and Filters --}}
                <form method="GET"
                    action="{{ route('users.index') }}"
                    class="mb-6 flex flex-wrap items-center gap-3">

                    <input type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search name or email"
                        class="rounded-md border-gray-300">

                    <select name="role_id"
                        class="rounded-md border-gray-300">

                        <option value="">All Roles</option>

                        @foreach ($roles as $role)
                        <option value="{{ $role->role_id }}"
                            @selected(request('role_id')==$role->role_id)>
                            {{ $role->role_name }}
                        </option>
                        @endforeach
                    </select>

                    <select name="status"
                        class="rounded-md border-gray-300">

                        <option value="">All Statuses</option>

                        <option value="1"
                            @selected(request('status')==='1' )>
                            Active
                        </option>

                        <option value="0"
                            @selected(request('status')==='0' )>
                            Inactive
                        </option>
                    </select>

                    <x-primary-button>
                        Filter
                    </x-primary-button>

                    <a href="{{ route('users.index') }}"
                        class="text-sm text-gray-600">
                        Reset
                    </a>
                </form>

                {{-- User Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">

                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-3">Name</th>
                                <th class="p-3">Email</th>
                                <th class="p-3">Role</th>
                                <th class="p-3">Status</th>
                                <th class="p-3">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($users as $user)
                            <tr class="border-b">

                                <td class="p-3">
                                    {{ $user->name }}

                                    @if (auth()->id() === $user->id)
                                    <span class="text-xs text-gray-500">
                                        (You)
                                    </span>
                                    @endif
                                </td>

                                <td class="p-3">
                                    {{ $user->email }}
                                </td>

                                <td class="p-3">
                                    {{ $user->role?->role_name ?? 'Unassigned' }}
                                </td>

                                <td class="p-3">
                                    @if ($user->is_active)
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-green-700">
                                        Active
                                    </span>
                                    @else
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-red-700">
                                        Inactive
                                    </span>
                                    @endif
                                </td>

                                <td class="p-3">
                                    <div class="flex items-center gap-3">

                                        <a href="{{ route('users.edit', $user) }}"
                                            class="text-blue-600 hover:underline">
                                            Edit
                                        </a>

                                        @if (auth()->id() !== $user->id)
                                        <form method="POST"
                                            action="{{ route('users.update-status', $user) }}"
                                            onsubmit="return confirm('Are you sure you want to change this account status?')">

                                            @csrf
                                            @method('PATCH')

                                            <input type="hidden"
                                                name="is_active"
                                                value="{{ $user->is_active ? 0 : 1 }}">

                                            <button type="submit"
                                                class="{{ $user->is_active ? 'text-red-600' : 'text-green-600' }} hover:underline">

                                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}

                                            </button>
                                        </form>
                                        @endif

                                    </div>
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="5"
                                    class="p-6 text-center text-gray-500">
                                    No users found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

                <div class="mt-6">
                    {{ $users->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>