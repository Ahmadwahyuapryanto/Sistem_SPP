<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Biaya SPP Baru') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('biaya-spp.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="nama_biaya" class="block text-sm font-medium text-gray-700">Nama Biaya</label>
                            <input type="text" name="nama_biaya" id="nama_biaya" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>
                         <div class="mb-4">
                            <label for="nominal" class="block text-sm font-medium text-gray-700">Nominal (Rp)</label>
                            <input type="number" name="nominal" id="nominal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>
                        <div class="flex items-center justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>