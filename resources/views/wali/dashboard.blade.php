<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Dashboard Wali Murid - {{ Auth::user()->nama }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="font-semibold text-lg">Riwayat Tagihan SPP untuk: {{ $siswa->nama_lengkap }}</h3>
                    
                    @if(session('error'))
                         <div class="mt-4 text-red-600 bg-red-100 border border-red-400 p-3 rounded">{{ session('error') }}</div>
                    @endif

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase">Periode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase">Jumlah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase">Status & Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($tagihans as $tagihan)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::createFromDate($tagihan->tahun, $tagihan->bulan, 1)->translatedFormat('F Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($tagihan->status == 'LUNAS')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">LUNAS</span>
                                            @else
                                                <form action="{{ route('wali.pembayaran.create', $tagihan) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-600 text-white hover:bg-indigo-500">
                                                        Bayar Sekarang
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="px-6 py-4 text-center">Belum ada data tagihan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk memunculkan pop-up Midtrans Snap --}}
    @push('scripts')
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script type="text/javascript">
        // Cek apakah ada snap token di session
        @if (session('snap_token'))
            window.snap.pay('{{ session('snap_token') }}', {
                onSuccess: function(result){
                    /* You may add your own implementation here */
                    // alert("payment success!"); 
                    console.log(result);
                    window.location.reload(); // Muat ulang halaman setelah sukses
                },
                onPending: function(result){
                    /* You may add your own implementation here */
                    // alert("wating your payment!"); 
                    console.log(result);
                    window.location.reload(); // Muat ulang halaman
                },
                onError: function(result){
                    /* You may add your own implementation here */
                    // alert("payment failed!"); 
                    console.log(result);
                    window.location.reload(); // Muat ulang halaman
                },
                onClose: function(){
                    /* You may add your own implementation here */
                    // alert('you closed the popup without finishing the payment');
                }
            });
        @endif
    </script>
    @endpush
</x-app-layout>