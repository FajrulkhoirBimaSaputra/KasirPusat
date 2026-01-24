<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-primary">
            Kelola User
        </h2>
    </x-slot>

    <div class="p-6">
        <div class="flex justify-between mb-4">
            <p class="text-gray-600">Manajemen akun kasir & admin</p>
            <a href="{{ route('register') }}"
               class="px-4 py-2 bg-primary text-white rounded-lg shadow hover:bg-red-700">
                + Tambah User
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full text-sm">
                <thead class="bg-secondary text-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3">Username</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr class="border-t">
                            <td class="px-4 py-3">{{ $user->name }}</td>
                            <td class="px-4 py-3">{{ $user->username }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-xs
                                    {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ strtoupper($user->role) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center space-x-2">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="text-blue-600 hover:underline">Edit</a>

                                @if($user->role !== 'admin')
                                    <form action="{{ route('admin.users.destroy', $user) }}"
                                          method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button onclick="return confirm('Hapus user ini?')"
                                                class="text-red-600 hover:underline">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
