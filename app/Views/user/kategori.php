<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<nav class="breadcrumb">
    <a href="/user/kategori"><i class="fas fa-home"></i> Kategori</a>
    <span class="separator">/</span>
    <span class="text-slate-500">Pilih Kategori</span>
</nav>

<div class="mb-6 sm:mb-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">Pilih Kategori</h1>
    <p class="text-slate-500 mt-1">Pilih kategori soal yang ingin dikerjakan</p>
</div>

<?php if (empty($kategori)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
        <i class="fas fa-folder-open text-slate-300 text-5xl mb-4"></i>
        <h3 class="text-lg font-semibold text-slate-600">Belum ada kategori</h3>
        <p class="text-slate-400 mt-2">Tunggu admin menambahkan kategori baru.</p>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <?php foreach ($kategori as $k): ?>
            <?php
            $isDone = ($k['soal_dikerjakan'] ?? 0) > 0 && ($k['soal_dikerjakan'] ?? 0) >= ($k['total_soal_aktif'] ?? 0);
            $persen = ($k['total_soal_aktif'] ?? 0) > 0 ? round((($k['soal_dikerjakan'] ?? 0) / ($k['total_soal_aktif'] ?? 1)) * 100) : 0;
            ?>
            <a href="/user/dashboard/<?= $k['id'] ?>" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition block">
                <div class="p-5 sm:p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="<?= $k['icon'] ?? 'fas fa-folder' ?> text-blue-500 text-2xl"></i>
                        </div>
                        <?php if ($isDone): ?>
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                                <i class="fas fa-check mr-1"></i>Selesai
                            </span>
                        <?php elseif (($k['soal_dikerjakan'] ?? 0) > 0): ?>
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">
                                <?= $k['soal_dikerjakan'] ?>/<?= $k['total_soal_aktif'] ?>
                            </span>
                        <?php else: ?>
                            <span class="px-2 py-1 bg-slate-100 text-slate-600 text-xs font-medium rounded-full">
                                <?= $k['total_soal_aktif'] ?? 0 ?> soal
                            </span>
                        <?php endif; ?>
                    </div>

                    <h3 class="text-lg font-semibold text-slate-800 mb-2"><?= $k['nama_kategori'] ?></h3>
                    <p class="text-sm text-slate-500 mb-4 line-clamp-2"><?= $k['deskripsi'] ?: 'Tidak ada deskripsi' ?></p>

                    <?php if (($k['soal_dikerjakan'] ?? 0) > 0): ?>
                        <div class="mb-3">
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-slate-500">Progress</span>
                                <span class="font-medium text-slate-700"><?= $persen ?>%</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full transition-all" style="width: <?= $persen ?>%"></div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <span class="text-green-600 font-medium"><i class="fas fa-bullseye mr-1"></i>Akurasi: <?= $k['akurasi'] ?>%</span>
                        </div>
                    <?php endif; ?>

                    <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-sm text-slate-500">
                            <?= ($k['total_soal_aktif'] ?? 0) ?> soal tersedia
                        </span>
                        <span class="text-blue-500 text-sm font-medium">
                            Lihat Soal →
                        </span>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="mt-6 sm:mt-8 grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 sm:p-6">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-folder text-indigo-500 text-lg sm:text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-slate-500">Total Kategori</p>
                <p class="text-xl sm:text-2xl font-bold text-slate-800"><?= count($kategori) ?></p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 sm:p-6">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-question-circle text-blue-500 text-lg sm:text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-slate-500">Total Soal</p>
                <p class="text-xl sm:text-2xl font-bold text-slate-800">
                    <?= array_sum(array_column($kategori, 'total_soal_aktif')) ?>
                </p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 sm:p-6">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-green-500 text-lg sm:text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-slate-500">Kategori Dikerjakan</p>
                <p class="text-xl sm:text-2xl font-bold text-slate-800">
                    <?= count(array_filter($kategori, fn($k) => ($k['soal_dikerjakan'] ?? 0) > 0)) ?>
                </p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 sm:p-6">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-trophy text-purple-500 text-lg sm:text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-slate-500">Akurasi Rata-rata</p>
                <p class="text-xl sm:text-2xl font-bold text-slate-800">
                    <?php
                    $totalAkurasi = 0;
                    $count = 0;
                    foreach ($kategori as $k) {
                        if (($k['soal_dikerjakan'] ?? 0) > 0) {
                            $totalAkurasi += $k['akurasi'] ?? 0;
                            $count++;
                        }
                    }
                    echo $count > 0 ? round($totalAkurasi / $count) . '%' : '0%';
                    ?>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="mt-6">
    <a href="/user/riwayat" class="text-blue-500 hover:text-blue-700 font-medium text-sm sm:text-base">
        <i class="fas fa-history mr-2"></i>Lihat Riwayat Pengerjaan Lengkap →
    </a>
</div>
<?= $this->endSection() ?>