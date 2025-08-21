<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Konfirmasi Pembayaran SPP
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="font-semibold text-lg mb-4">Detail Tagihan</h3>

                    <div class="mb-4">
                        <p><strong>Nama Siswa:</strong> {{ $tagihan->siswa->nama_lengkap }}</p>
                        <p><strong>NIS:</strong> {{ $tagihan->siswa->nis }}</p>
                        <p><strong>Kelas:</strong> {{ $tagihan->siswa->kelas }}</p>
                        <p><strong>Periode Tagihan:</strong> {{ \Carbon\Carbon::createFromDate($tagihan->tahun, $tagihan->bulan, 1)->translatedFormat('F Y') }}</p>
                        <p class="text-xl font-bold"><strong>Jumlah:</strong> Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</p>
                    </div>

                    <hr class="my-6 dark:border-gray-600">

                    <form action="{{ route('pembayaran.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tagihan_id" value="{{ $tagihan->id }}">
                        <input type="hidden" name="jumlah_bayar" value="{{ $tagihan->jumlah }}">

                        <div class="flex items-center justify-end">
                            <a href="{{ route('tagihan.show', $tagihan->siswa_id) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline mr-4">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Konfirmasi Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>