<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<nav class="breadcrumb">
    <a href="/admin/dashboard"><i class="fas fa-home"></i> Dashboard</a>
    <span class="separator">/</span>
    <span class="text-slate-500">Kelola Kategori</span>
</nav>

<div class="mb-6 sm:mb-8 flex flex-wrap justify-between items-center gap-3">
    <div>
        <a href="/admin/dashboard" class="back-btn">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 mt-1">Kelola Kategori</h1>
        <p class="text-slate-500 mt-1">Atur kategori soal (Motor, Mobil, AC, dll)</p>
    </div>
    <a href="/admin/kategori/tambah" class="bg-blue-500 hover:bg-blue-600 text-white px-4 sm:px-6 py-3 rounded-lg font-medium transition text-sm sm:text-base">
        <i class="fas fa-plus mr-2"></i>Tambah Kategori
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
    <?php foreach ($kategori as $k): ?>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition">
            <div class="p-5 sm:p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="<?= $k['icon'] ?? 'fas fa-folder' ?> text-blue-500 text-xl"></i>
                    </div>
                    <span class="px-2 py-1 text-xs font-medium rounded-full <?= $k['status'] === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                        <?= $k['status'] ?>
                    </span>
                </div>

                <h3 class="text-lg font-semibold text-slate-800 mb-1"><?= $k['nama_kategori'] ?></h3>
                <p class="text-sm text-slate-500 mb-4 line-clamp-2"><?= $k['deskripsi'] ?: 'Tidak ada deskripsi' ?></p>

                <div class="flex items-center gap-4 text-sm text-slate-600 mb-4">
                    <div class="flex items-center gap-1">
                        <i class="fas fa-question-circle text-blue-400"></i>
                        <span><?= $k['total_soal'] ?? 0 ?> soal</span>
                    </div>
                </div>

                <div class="flex gap-2">
                    <a href="/admin/kategori/edit/<?= $k['id'] ?>"
                        class="flex-1 text-center bg-blue-50 hover:bg-blue-100 text-blue-600 font-medium py-2 rounded-lg transition text-sm">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                    <a href="/admin/kategori/hapus/<?= $k['id'] ?>"
                        class="flex-1 text-center bg-red-50 hover:bg-red-100 text-red-600 font-medium py-2 rounded-lg transition text-sm"
                        onclick="return confirm('Yakin hapus kategori ini? Pastikan tidak ada soal di dalamnya.')">
                        <i class="fas fa-trash mr-1"></i>Hapus
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if (empty($kategori)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
        <i class="fas fa-folder-open text-slate-300 text-5xl mb-4"></i>
        <h3 class="text-lg font-semibold text-slate-600">Belum ada kategori</h3>
        <p class="text-slate-400 mt-2">Tambah kategori pertama untuk mulai mengorganisir soal.</p>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>