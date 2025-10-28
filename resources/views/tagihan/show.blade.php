<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Data Tagihan untuk: {{ $siswa->nama_lengkap }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Periode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal Bayar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Metode Pembayaran</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($tagihans as $tagihan)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::createFromDate($tagihan->tahun, $tagihan->bulan, 1)->translatedFormat('F') }} {{ $tagihan->tahun }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($tagihan->status == 'LUNAS')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                LUNAS
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                BELUM LUNAS
                                            </span>
                                        @endif
                                    </td>
                                    
                                    {{-- KODE YANG DIPERBAIKI DENGAN 'optional()' --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ optional(optional($tagihan->pembayaran)->first())->tanggal_bayar?->format('d/m/Y H:i') ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ optional(optional($tagihan->pembayaran)->first())->metode_pembayaran ?? '-' }}
                                    </td>
                                    {{-- BATAS AKHIR PERBAIKAN --}}

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if ($tagihan->status == 'BELUM LUNAS')
                                            @if (Auth::user()->role == 'admin' || Auth::user()->role == 'staf')
                                                <a href="{{ route('pembayaran.create', $tagihan->id) }}" class="text-indigo-600 hover:text-indigo-900">Bayar Cash</a>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center">Tidak ada data tagihan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>