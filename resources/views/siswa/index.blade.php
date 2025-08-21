<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <div class="flex items-center mb-4">
                        <a href="{{ route('siswa.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Tambah Siswa
                        </a>
                        <a href="{{ route('siswa.cetak', request()->query()) }}" target="_blank" class="ms-3 inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                            Cetak Data
                        </a>
                    </div>


                    <form method="GET" action="{{ route('siswa.index') }}" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <input type="text" name="search" placeholder="Cari Nama atau NIS..." class="w-full rounded-md border-gray-300 dark:bg-gray-700 shadow-sm" value="{{ request('search') }}">
                            </div>
                            <div>
                                <select name="kelas" class="w-full rounded-md border-gray-300 dark:bg-gray-700 shadow-sm">
                                    <option value="">Semua Kelas</option>
                                    @foreach ($kelas_list as $kelas)
                                        <option value="{{ $kelas->kelas }}" {{ request('kelas') == $kelas->kelas ? 'selected' : '' }}>
                                            {{ $kelas->kelas }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                                Cari / Filter
                            </button>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase">NIS</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase">Nama Lengkap</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase">Kelas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase">No. HP Wali</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($siswas as $siswa)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $siswa->nis }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $siswa->nama_lengkap }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $siswa->kelas }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $siswa->no_hp_wali }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                             @if (!$siswa->wali)
                                                <a href="{{ route('wali.create', $siswa->id) }}" class="text-green-600 hover:text-green-900">Buat Akun Wali</a>
                                            @endif
                                            <a href="{{ route('tagihan.show', $siswa->id) }}" class="text-blue-600 hover:text-blue-900 ml-2">Lihat Tagihan</a>
                                            <a href="{{ route('siswa.edit', $siswa->id) }}" class="text-indigo-600 hover:text-indigo-900 ml-2">Edit</a>
                                            <form action="{{ route('siswa.destroy', $siswa->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 ml-2" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center">Tidak ada data siswa yang cocok.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{-- Menambahkan appends agar parameter filter tetap ada saat pindah halaman --}}
                        {{ $siswas->appends(request()->query())->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>