<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6">
                <!-- Tren Penjualan Harian -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4">📈 Tren Penjualan (7 Hari Terakhir)</h3>
                    <canvas id="trendChart"></canvas>
                </div>

                <!-- Top 5 Makanan Paling Laris -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4">🏆 Top 5 Produk Terlaris (30 Hari Terakhir)</h3>
                    <canvas id="topMakananChart"></canvas>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                <!-- Stok Per Kategori -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4">🏷️ Stok Per Kategori</h3>
                    <canvas id="stokKategoriChart"></canvas>
                </div>

                <!-- Info Tambahan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4">📋 Ringkasan Kategori</h3>
                    <div class="space-y-3">
                        @forelse($stokPerKategori as $kategori => $stok)
                            <div class="flex justify-between items-center pb-3 border-b text-sm">
                                <span class="text-gray-700">{{ $kategori }}</span>
                                <span class="font-bold text-blue-600">{{ number_format($stok) }} Pcs</span>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">Tidak ada data kategori</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
        // Warna palette
        const colors = {
            blue: 'rgb(59, 130, 246)',
            green: 'rgb(34, 197, 94)',
            red: 'rgb(239, 68, 68)',
            purple: 'rgb(147, 51, 234)',
            orange: 'rgb(249, 115, 22)',
            pink: 'rgb(236, 72, 153)',
        };

        // Responsive options
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    labels: { font: { size: window.innerWidth < 640 ? 10 : 12 } }
                }
            }
        };

        // 1. Tren Penjualan Harian (Line Chart)
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: [@foreach($trendPenjualan as $item) '{{ $item["tanggal"] }}', @endforeach],
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: [@foreach($trendPenjualan as $item) {{ $item["total"] }}, @endforeach],
                    borderColor: colors.blue,
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: colors.blue,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                }]
            },
            options: {
                ...chartOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        // 2. Top 5 Makanan Paling Laris (Bar Chart)
        const topMakananCtx = document.getElementById('topMakananChart').getContext('2d');
        new Chart(topMakananCtx, {
            type: 'bar',
            data: {
                labels: [@foreach($topMakanan as $item) '{{ $item["nama"] }}', @endforeach],
                datasets: [{
                    label: 'Jumlah Terjual (Pcs)',
                    data: [@foreach($topMakanan as $item) {{ $item["total_qty"] }}, @endforeach],
                    backgroundColor: [
                        colors.green,
                        colors.blue,
                        colors.purple,
                        colors.orange,
                        colors.pink,
                    ],
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        labels: { font: { size: 12 } }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // 3. Stok Per Kategori (Pie Chart)
        const stokKategoriCtx = document.getElementById('stokKategoriChart').getContext('2d');
        new Chart(stokKategoriCtx, {
            type: 'doughnut',
            data: {
                labels: [@foreach($stokPerKategori as $kategori => $stok) '{{ $kategori }}', @endforeach],
                datasets: [{
                    data: [@foreach($stokPerKategori as $kategori => $stok) {{ $stok }}, @endforeach],
                    backgroundColor: [
                        colors.green,
                        colors.blue,
                        colors.purple,
                        colors.orange,
                        colors.pink,
                        colors.red,
                    ],
                    borderColor: '#fff',
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { font: { size: 12 }, padding: 15 }
                    }
                }
            }
        });
    </script>
</x-app-layout>
