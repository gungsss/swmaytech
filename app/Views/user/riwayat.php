<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<nav class="breadcrumb">
    <a href="/user/kategori"><i class="fas fa-home"></i> Kategori</a>
    <span class="separator">/</span>
    <span class="text-slate-500">Riwayat</span>
</nav>

<div class="mb-6 sm:mb-8">
    <a href="/user/kategori" class="back-btn">
        <i class="fas fa-arrow-left"></i> Kembali ke Kategori
    </a>
    <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 mt-2">Riwayat Pengerjaan</h1>
    <p class="text-slate-500 mt-1">Statistik dan hasil pengerjaan soal</p>
</div>

<?php if (empty($riwayat)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
        <i class="fas fa-history text-slate-300 text-5xl mb-4"></i>
        <h3 class="text-lg font-semibold text-slate-600">Belum ada riwayat</h3>
        <p class="text-slate-400 mt-2">Anda belum mengerjakan soal apapun.</p>
        <a href="/user/kategori" class="inline-block mt-4 bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium transition">
            Pilih Kategori
        </a>
    </div>
<?php else: ?>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6 sm:mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 sm:p-6">
            <p class="text-xs sm:text-sm text-slate-500">Total Soal Dikerjakan</p>
            <p class="text-2xl sm:text-3xl font-bold text-slate-800 mt-1"><?= count($riwayat) ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 sm:p-6">
            <p class="text-xs sm:text-sm text-slate-500">Total Jawaban Benar</p>
            <p class="text-2xl sm:text-3xl font-bold text-green-600 mt-1"><?= array_sum(array_column($riwayat, 'benar')) ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 sm:p-6">
            <p class="text-xs sm:text-sm text-slate-500">Total Jawaban Salah</p>
            <p class="text-2xl sm:text-3xl font-bold text-red-600 mt-1"><?= array_sum(array_column($riwayat, 'salah')) ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 sm:p-6">
            <p class="text-xs sm:text-sm text-slate-500">Akurasi Rata-rata</p>
            <p class="text-2xl sm:text-3xl font-bold text-blue-600 mt-1">
                <?php
                $totalSoal = count($riwayat);
                $totalBenar = array_sum(array_column($riwayat, 'benar'));
                $totalSemua = array_sum(array_column($riwayat, 'total'));
                echo $totalSemua > 0 ? round(($totalBenar / $totalSemua) * 100) . '%' : '0%';
                ?>
            </p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Gambar</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nama Soal</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Kategori</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Benar</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase hidden sm:table-cell">Salah</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Akurasi</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase hidden md:table-cell">Waktu</th>
                        <!-- <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Aksi</th> -->
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($riwayat as $r): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 sm:px-6 py-4">
                                <img src="/uploads/soal/<?= $r['gambar'] ?>" alt="<?= $r['nama_soal'] ?>"
                                    class="w-14 h-14 sm:w-16 sm:h-16 object-cover rounded-lg">
                            </td>
                            <td class="px-4 sm:px-6 py-4 text-sm font-medium text-slate-800"><?= $r['nama_soal'] ?></td>
                            <td class="px-4 sm:px-6 py-4">
                                <span class="px-2 py-1 bg-blue-50 text-blue-600 text-xs font-medium rounded-full">
                                    <?= $r['nama_kategori'] ?? 'Tanpa Kategori' ?>
                                </span>
                            </td>
                            <td class="px-4 sm:px-6 py-4">
                                <span class="text-green-600 font-semibold"><?= $r['benar'] ?></span>
                                <span class="text-slate-400 text-xs">/<?= $r['total'] ?></span>
                            </td>
                            <td class="px-4 sm:px-6 py-4 hidden sm:table-cell">
                                <span class="text-red-600 font-semibold"><?= $r['salah'] ?></span>
                            </td>
                            <td class="px-4 sm:px-6 py-4">
                                <?php $persen = $r['total'] > 0 ? round(($r['benar'] / $r['total']) * 100) : 0; ?>
                                <div class="flex items-center gap-2">
                                    <div class="w-12 sm:w-16 bg-slate-200 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full transition-all" style="width: <?= $persen ?>%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-slate-700"><?= $persen ?>%</span>
                                </div>
                            </td>
                            <td class="px-4 sm:px-6 py-4 text-sm text-slate-500 hidden md:table-cell">
                                <?php
                                $waktu = $r['waktu'] ?? null;
                                if ($waktu && $waktu !== '0000-00-00 00:00:00' && strtotime($waktu) > 0) {
                                    echo date('d/m/Y H:i', strtotime($waktu));
                                } else {
                                    echo '<span class="text-slate-400">-</span>';
                                }
                                ?>
                            </td>
                            <!-- <td class="px-4 sm:px-6 py-4">
                                <a href="/user/soal/kerjakan/<?= $r['id_soal'] ?>" class="text-blue-500 hover:text-blue-700 text-sm font-medium whitespace-nowrap">
                                    Kerjakan Lagi →
                                </a>
                            </td> -->
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>