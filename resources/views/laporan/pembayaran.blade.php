<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Laporan Pembayaran SPP
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="font-semibold text-lg">Filter Laporan</h3>
                    <form method="GET" action="{{ route('laporan.pembayaran') }}" class="mt-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <div>
                                <label for="tanggal_mulai" class="block text-sm font-medium">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 shadow-sm" value="{{ $tanggalMulai }}">
                            </div>
                            <div>
                                <label for="tanggal_selesai" class="block text-sm font-medium">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 shadow-sm" value="{{ $tanggalSelesai }}">
                            </div>
                            <div>
                                <button type="submit" class="w-full inline-flex justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                                    Filter
                                </button>
                            </div>
                            <div>
                                <a href="{{ route('laporan.cetak_pdf', ['tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai]) }}" class="w-full inline-flex justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500" target="_blank">
                                    Cetak PDF
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                     <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Tgl Bayar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Nama Siswa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Periode Tagihan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Metode Bayar</th> <th class="px-6 py-3 text-left text-xs font-medium uppercase">Petugas</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($pembayarans as $pembayaran)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->isoFormat('D MMM Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $pembayaran->tagihan->siswa->nama_lengkap }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::createFromDate($pembayaran->tagihan->tahun, $pembayaran->tagihan->bulan, 1)->translatedFormat('F Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap capitalize">{{ $pembayaran->metode_pembayaran }}</td> <td class="px-6 py-4 whitespace-nowrap">{{ $pembayaran->user->name ?? 'Online' }}</td> </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center">Tidak ada data untuk periode yang dipilih.</td> </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-gray-100 dark:bg-gray-900">
                            <tr>
                                <td colspan="4" class="px-6 py-3 text-right font-bold uppercase">Total Pendapatan</td> <td colspan="2" class="px-6 py-3 font-bold">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td> </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>