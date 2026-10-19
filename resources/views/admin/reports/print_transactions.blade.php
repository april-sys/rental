<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Transaksi - {{ $dateFilter['label'] }}</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            @page {
                size: A4 landscape;
                margin: 12mm 10mm;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans p-4 sm:p-8">

    <!-- Screen Floating Action Bar (Hidden when printing) -->
    <div class="no-print max-w-6xl mx-auto mb-6 bg-white p-4 rounded-2xl shadow-md border border-slate-200 flex items-center justify-between">
        <div class="flex items-center space-x-2 text-xs font-semibold text-slate-600">
            <i class="fa-solid fa-file-pdf text-red-500 text-base"></i>
            <span>Pratinjau Cetak / PDF Laporan Transaksi (Auto-Print Aktif)</span>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm flex items-center space-x-2">
                <i class="fa-solid fa-print"></i>
                <span>Cetak / Simpan PDF</span>
            </button>
            <button onclick="window.close()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl">
                Tutup
            </button>
        </div>
    </div>

    <!-- Official Printable Sheet Container -->
    <div class="max-w-6xl mx-auto bg-white p-8 sm:p-12 rounded-2xl shadow-sm border border-slate-200 print:border-none print:shadow-none print:p-0">
        <!-- Kop Surat Resmi AutoRent -->
        <div class="flex items-center justify-between pb-6 border-b-2 border-slate-900 mb-6">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-2xl font-black">
                    <i class="fa-solid fa-car-side"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900">AUTORENT INDONESIA</h1>
                    <p class="text-xs text-slate-500 font-medium">PT AutoRent Mitra Transportasi Prima • Layanan Door-to-Door Delivery</p>
                    <p class="text-[11px] text-slate-500">Jl. Merdeka No. 1, Gambir, Jakarta Pusat • Telp: (021) 555-7890 / 0812-3456-7890</p>
                </div>
            </div>
            <div class="text-right">
                <div class="inline-block px-3 py-1 bg-slate-100 rounded-lg text-xs font-mono font-bold text-slate-700">
                    DOKUMEN RESMI REKAPITULASI
                </div>
                <div class="text-[11px] text-slate-500 mt-1">Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
            </div>
        </div>

        <!-- Judul Laporan & Metadata Periode -->
        <div class="text-center my-6">
            <h2 class="text-lg font-black uppercase tracking-wide text-slate-900">LAPORAN REKAPITULASI TRANSAKSI KEUANGAN</h2>
            <p class="text-xs text-slate-600 mt-1">
                Periode Pelaporan: <strong class="text-slate-900">{{ $dateFilter['label'] }}</strong>
            </p>
        </div>

        <!-- Financial Summary Mini Grid -->
        <div class="grid grid-cols-4 gap-3 mb-6 text-xs">
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                <span class="text-slate-500 block text-[10px] font-bold uppercase">Sewa Mobil</span>
                <span class="font-black text-slate-900 text-sm">Rp {{ number_format($totalRentalFee, 0, ',', '.') }}</span>
            </div>
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                <span class="text-slate-500 block text-[10px] font-bold uppercase">Jasa Driver</span>
                <span class="font-black text-slate-900 text-sm">Rp {{ number_format($totalDriverFee, 0, ',', '.') }}</span>
            </div>
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                <span class="text-slate-500 block text-[10px] font-bold uppercase">Denda Keterlambatan</span>
                <span class="font-black text-amber-700 text-sm">Rp {{ number_format($totalLatePenalty, 0, ',', '.') }}</span>
            </div>
            <div class="p-3 bg-blue-50 rounded-xl border border-blue-200">
                <span class="text-blue-700 block text-[10px] font-bold uppercase">Total Keseluruhan</span>
                <span class="font-black text-blue-900 text-sm">Rp {{ number_format($totalOverall, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Tabel Transaksi -->
        <div class="overflow-x-auto mb-8">
            <table class="w-full text-left border-collapse text-[11px]">
                <thead>
                    <tr class="bg-slate-100 border-y border-slate-300 font-bold text-slate-700 uppercase">
                        <th class="py-2.5 px-2">No</th>
                        <th class="py-2.5 px-2">Kode</th>
                        <th class="py-2.5 px-3">Tanggal Booking</th>
                        <th class="py-2.5 px-3">Customer</th>
                        <th class="py-2.5 px-3">Armada Mobil</th>
                        <th class="py-2.5 px-2">Layanan</th>
                        <th class="py-2.5 px-2 text-right">Sewa Unit</th>
                        <th class="py-2.5 px-2 text-right">Denda</th>
                        <th class="py-2.5 px-3 text-right">Total Biaya</th>
                        <th class="py-2.5 px-2">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($bookings as $index => $booking)
                        <tr>
                            <td class="py-2 px-2 text-slate-400 font-mono">{{ $index + 1 }}</td>
                            <td class="py-2 px-2 font-mono font-bold text-slate-900">{{ $booking->booking_code }}</td>
                            <td class="py-2 px-3 text-slate-600">{{ $booking->created_at ? $booking->created_at->format('d/m/Y H:i') : '-' }}</td>
                            <td class="py-2 px-3 font-semibold text-slate-900">
                                {{ $booking->user?->name ?: '-' }}
                                <div class="text-[10px] text-slate-400">{{ $booking->user?->phone_number ?: '-' }}</div>
                            </td>
                            <td class="py-2 px-3 text-slate-800">
                                {{ $booking->car?->brand }} {{ $booking->car?->model }}
                                <span class="font-mono text-slate-500 text-[10px]">({{ $booking->car?->license_plate }})</span>
                            </td>
                            <td class="py-2 px-2">
                                {{ $booking->driver_service ? '+ Driver' : 'Lepas Kunci' }}
                            </td>
                            <td class="py-2 px-2 text-right text-slate-700">
                                Rp {{ number_format($booking->car_rental_fee, 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-2 text-right text-amber-700">
                                {{ $booking->late_penalty_fee > 0 ? 'Rp ' . number_format($booking->late_penalty_fee, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-2 px-3 text-right font-bold text-slate-900">
                                Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-2">
                                <span class="text-[10px] font-bold uppercase">{{ $booking->status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-6 text-center text-slate-400">
                                Tidak ada data transaksi untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-slate-100 border-t-2 border-slate-300 font-bold text-slate-900">
                        <td colspan="6" class="py-2.5 px-3 uppercase text-right">TOTAL KESELURUHAN:</td>
                        <td class="py-2.5 px-2 text-right">Rp {{ number_format($totalRentalFee, 0, ',', '.') }}</td>
                        <td class="py-2.5 px-2 text-right text-amber-700">Rp {{ number_format($totalLatePenalty, 0, ',', '.') }}</td>
                        <td class="py-2.5 px-3 text-right text-blue-700 text-xs">Rp {{ number_format($totalOverall, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Kolom Tanda Tangan / Otorisasi -->
        <div class="mt-12 pt-6 border-t border-slate-200 grid grid-cols-2 text-xs text-center">
            <div>
                <p class="text-slate-500 mb-16">Dibuat Oleh,<br><strong class="text-slate-900">Petugas Keuangan / Admin</strong></p>
                <div class="w-48 mx-auto border-b border-slate-400"></div>
                <p class="font-bold text-slate-800 mt-1">{{ auth()->user()->name ?? 'Administrator' }}</p>
            </div>
            <div>
                <p class="text-slate-500 mb-16">Mengetahui & Menyetujui,<br><strong class="text-slate-900">Pimpinan / Operational Manager</strong></p>
                <div class="w-48 mx-auto border-b border-slate-400"></div>
                <p class="font-bold text-slate-800 mt-1">Management AutoRent Indonesia</p>
            </div>
        </div>
    </div>

    <script>
        // Trigger print automatically after load if not prevented
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 600);
        });
    </script>
</body>
</html>
