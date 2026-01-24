<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-primary">
            Edit User
        </h2>
    </x-slot>

    <div class="p-6 max-w-xl mx-auto bg-white rounded-lg shadow">

        {{-- Error Validation --}}
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                <ul class="text-sm list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            {{-- Nama --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Nama</label>
                <input type="text" name="name"
                    value="{{ old('name', $user->name) }}"
                    class="w-full rounded border-gray-300 focus:border-primary focus:ring-primary">
            </div>

            {{-- Username --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Username</label>
                <input type="text" name="username"
                    value="{{ old('username', $user->username) }}"
                    class="w-full rounded border-gray-300 focus:border-primary focus:ring-primary">
            </div>

            {{-- Role --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Role</label>
                <select name="role"
                    class="w-full rounded border-gray-300 focus:border-primary focus:ring-primary">
                    <option value="kasir" @selected(old('role', $user->role) === 'kasir')>Kasir</option>
                    <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                </select>
            </div>

            {{-- Password Baru --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Password Baru</label>
                <input type="password" name="password"
                    class="w-full rounded border-gray-300 focus:border-primary focus:ring-primary"
                    placeholder="Kosongkan jika tidak ingin mengganti">
                <p class="text-xs text-gray-500 mt-1">
                    Isi hanya jika ingin mengganti password
                </p>
            </div>

            {{-- Konfirmasi Password --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation"
                    class="w-full rounded border-gray-300 focus:border-primary focus:ring-primary">
            </div>

            {{-- Tombol --}}
            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.users.index') }}"
                    class="px-4 py-2 border rounded hover:bg-gray-100">
                    Batal
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-primary text-white rounded hover:bg-primary/90">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
