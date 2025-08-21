<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Data Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('siswa.update', $siswa->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="nis" class="block text-sm font-medium text-gray-700">NIS</label>
                            <input type="text" name="nis" id="nis" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ $siswa->nis }}" required>
                        </div>
                         <div class="mb-4">
                            <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ $siswa->nama_lengkap }}" required>
                        </div>
                         <div class="mb-4">
                            <label for="kelas" class="block text-sm font-medium text-gray-700">Kelas</label>
                            <input type="text" name="kelas" id="kelas" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ $siswa->kelas }}" required>
                        </div>
                        <div class="mb-4">
                            <label for="no_hp_wali" class="block text-sm font-medium text-gray-700">Nomor HP Wali</label>
                            <input type="text" name="no_hp_wali" id="no_hp_wali" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ $siswa->no_hp_wali }}" required>
                        </div>
                        <div class="mb-4">
                            <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                            <textarea name="alamat" id="alamat" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ $siswa->alamat }}</textarea>
                        </div>
                        <div class="flex items-center justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>