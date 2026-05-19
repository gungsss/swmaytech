<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<nav class="breadcrumb">
    <a href="/user/kategori"><i class="fas fa-home"></i> Kategori</a>
    <span class="separator">/</span>
    <a href="/user/dashboard/<?= $kategori['id'] ?>"><?= $kategori['nama_kategori'] ?></a>
    <span class="separator">/</span>
    <span class="text-slate-500">Soal Tersedia</span>
</nav>

<div class="mb-6 sm:mb-8 flex flex-wrap justify-between items-center gap-3">
    <div>
        <a href="/user/kategori" class="back-btn">
            <i class="fas fa-arrow-left"></i> Kembali ke Kategori
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800"><?= $kategori['nama_kategori'] ?></h1>
        <p class="text-slate-500 mt-1">Pilih soal untuk dikerjakan</p>
    </div>
    <div class="flex items-center gap-2">
        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
            <i class="<?= $kategori['icon'] ?? 'fas fa-folder' ?> text-blue-500 text-xl"></i>
        </div>
    </div>
</div>

<?php if (empty($soal)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
        <i class="fas fa-inbox text-slate-300 text-5xl mb-4"></i>
        <h3 class="text-lg font-semibold text-slate-600">Belum ada soal di kategori ini</h3>
        <p class="text-slate-400 mt-2">Tunggu admin menambahkan soal baru.</p>
        <a href="/user/kategori" class="inline-block mt-4 bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium transition">
            Kembali ke Kategori
        </a>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <?php foreach ($soal as $s): ?>
            <?php
            $isDone = isset($progress[$s['id']]);
            $skor = $isDone ? $progress[$s['id']]['skor'] : 0;
            $total = $isDone ? $progress[$s['id']]['total'] : 0;
            $persen = $total > 0 ? round(($skor / $total) * 100) : 0;
            ?>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition">
                <div class="relative">
                    <img src="/uploads/soal/<?= $s['gambar'] ?>" alt="<?= $s['nama_soal'] ?>"
                        class="w-full h-40 sm:h-48 object-cover">
                    <div class="absolute top-3 right-3">
                        <?php if ($isDone): ?>
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">
                                <i class="fas fa-check mr-1"></i>Sudah
                            </span>
                        <?php else: ?>
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                                Baru
                            </span>
                        <?php endif; ?>
                    </div>
                    <?php if ($isDone): ?>
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-3">
                            <div class="flex items-center justify-between text-white">
                                <span class="text-sm font-medium"><?= $skor ?>/<?= $total ?></span>
                                <span class="text-sm font-bold"><?= $persen ?>%</span>
                            </div>
                            <div class="mt-1 w-full bg-white/30 rounded-full h-2">
                                <div class="bg-green-400 h-2 rounded-full transition-all" style="width: <?= $persen ?>%"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-slate-800 mb-2"><?= $s['nama_soal'] ?></h3>
                    <p class="text-sm text-slate-500 mb-4 line-clamp-2"><?= $s['deskripsi'] ?? 'Tidak ada deskripsi' ?></p>

                    <?php if ($isDone): ?>
                        <button disabled
                            class="block w-full bg-slate-300 text-slate-600 text-center font-semibold py-3 rounded-lg text-sm cursor-not-allowed">
                            <i class="fas fa-lock mr-2"></i>Sudah Dikerjakan
                        </button>
                    <?php else: ?>
                        <a href="/user/soal/kerjakan/<?= $s['id'] ?>"
                            class="block w-full bg-green-500 hover:bg-green-600 text-white text-center font-semibold py-3 rounded-lg transition text-sm">
                            <i class="fas fa-play mr-2"></i>Kerjakan Soal
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="mt-6 sm:mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 sm:p-6">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-question-circle text-blue-500 text-lg sm:text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-slate-500">Soal di Kategori Ini</p>
                <p class="text-xl sm:text-2xl font-bold text-slate-800"><?= count($soal) ?></p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 sm:p-6">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-green-500 text-lg sm:text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-slate-500">Sudah Dikerjakan</p>
                <p class="text-xl sm:text-2xl font-bold text-slate-800"><?= count($progress) ?></p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 sm:p-6">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-trophy text-purple-500 text-lg sm:text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-slate-500">Akurasi di Kategori Ini</p>
                <p class="text-xl sm:text-2xl font-bold text-slate-800">
                    <?php
                    $totalPersen = 0;
                    foreach ($progress as $p) {
                        $totalPersen += $p['total'] > 0 ? ($p['skor'] / $p['total']) * 100 : 0;
                    }
                    echo count($progress) > 0 ? round($totalPersen / count($progress)) . '%' : '0%';
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