<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="font-semibold text-lg">Filter Statistik</h3>
                    <form method="GET" action="{{ route('dashboard') }}">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                            <div>
                                <label for="filter_bulan" class="block text-sm font-medium">Bulan</label>
                                <select name="bulan" id="filter_bulan" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 shadow-sm">
                                    <option value="all" {{ $selectedBulan == 'all' ? 'selected' : '' }}>Semua Bulan</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $selectedBulan == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label for="filter_tahun" class="block text-sm font-medium">Tahun</label>
                                <input type="number" name="tahun" id="filter_tahun" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 shadow-sm" value="{{ $selectedTahun }}">
                            </div>
                            <div class="self-end">
                                <button type="submit" class="w-full inline-flex justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                                    Tampilkan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h4 class="text-gray-500 dark:text-gray-400">Total Siswa</h4>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalSiswa }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h4 class="text-gray-500 dark:text-gray-400">Sudah Lunas
                        @if($selectedBulan == 'all')
                            (Tahun {{ $selectedTahun }})
                        @else
                            ({{ \Carbon\Carbon::create()->month( (int)$selectedBulan )->translatedFormat('F') }} {{ $selectedTahun }})
                        @endif
                    </h4>
                    <p class="text-3xl font-bold text-green-500">{{ $jumlahSudahLunas }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h4 class="text-gray-500 dark:text-gray-400">Belum Lunas
                         @if($selectedBulan == 'all')
                            (Tahun {{ $selectedTahun }})
                        @else
                            ({{ \Carbon\Carbon::create()->month( (int)$selectedBulan )->translatedFormat('F') }} {{ $selectedTahun }})
                        @endif
                    </h4>
                    <p class="text-3xl font-bold text-red-500">{{ $jumlahBelumLunas }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="font-semibold text-lg">Grafik Pembayaran SPP Tahun {{ $selectedTahun }}</h3>
                    <canvas id="sppChart" class="mt-4"></canvas>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="font-semibold text-lg">Generate Tagihan SPP Bulanan</h3>
                    @if(session('success')) <div class="mb-4 text-green-600">{{ session('success') }}</div> @endif
                    @if(session('error')) <div class="mb-4 text-red-600">{{ session('error') }}</div> @endif

                    <form action="{{ route('tagihan.generate') }}" method="POST" class="mt-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="gen_bulan" class="block text-sm font-medium">Bulan</label>
                                <select name="bulan" id="gen_bulan" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 shadow-sm" required>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label for="gen_tahun" class="block text-sm font-medium">Tahun</label>
                                <input type="number" name="tahun" id="gen_tahun" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 shadow-sm" value="{{ date('Y') }}" required>
                            </div>
                            <div>
                                <label for="biaya_spp_id" class="block text-sm font-medium">Pilih Biaya SPP</label>
                                <select name="biaya_spp_id" id="biaya_spp_id" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 shadow-sm" required>
                                    @forelse ($biayaSpps as $biaya)
                                        <option value="{{ $biaya->id }}">{{ $biaya->nama_biaya }} - (Rp {{ number_format($biaya->nominal, 0, ',', '.') }})</option>
                                    @empty
                                        <option disabled>Silakan tambah data biaya SPP dulu</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Generate Tagihan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('sppChart');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        {
                            label: 'Sudah Lunas',
                            data: @json($chartDataLunas),
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Belum Lunas',
                            data: @json($chartDataBelumLunas),
                            backgroundColor: 'rgba(255, 99, 132, 0.6)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                // Memastikan hanya angka bulat (integer) di sumbu Y
                                stepSize: 1,
                                callback: function(value) {if (Math.floor(value) === value) {return value;}}
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>