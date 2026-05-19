<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<nav class="breadcrumb">
    <a href="/admin/dashboard"><i class="fas fa-home"></i> Dashboard</a>
    <span class="separator">/</span>
    <a href="/admin/kategori">Kategori</a>
    <span class="separator">/</span>
    <span class="text-slate-500"><?= isset($kategori) ? 'Edit Kategori' : 'Tambah Kategori' ?></span>
</nav>

<div class="mb-6">
    <a href="/admin/kategori" class="back-btn">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
    <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 mt-2">
        <?= isset($kategori) ? 'Edit Kategori' : 'Tambah Kategori Baru' ?>
    </h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 sm:p-8 max-w-2xl">
    <form action="<?= isset($kategori) ? '/admin/kategori/update/' . $kategori['id'] : '/admin/kategori/simpan' ?>" method="POST">
        <?= csrf_field() ?>

        <div class="mb-5">
            <label class="block text-sm font-medium text-slate-700 mb-2">Nama Kategori <span class="text-red-500">*</span></label>
            <input type="text" name="nama_kategori" required
                value="<?= isset($kategori) ? esc($kategori['nama_kategori']) : old('nama_kategori') ?>"
                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                placeholder="Contoh: Motor, Mobil, AC, Kulkas">
        </div>

        <div class="mb-5">
            <label class="block text-sm font-medium text-slate-700 mb-2">Deskripsi</label>
            <textarea name="deskripsi" rows="3"
                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                placeholder="Deskripsi singkat tentang kategori ini..."><?= isset($kategori) ? esc($kategori['deskripsi']) : old('deskripsi') ?></textarea>
        </div>

        <div class="mb-5">
            <label class="block text-sm font-medium text-slate-700 mb-2">Icon (FontAwesome)</label>
            <div class="flex gap-2">
                <input type="text" name="icon"
                    value="<?= isset($kategori) ? esc($kategori['icon']) : (old('icon') ?: 'fas fa-car') ?>"
                    class="flex-1 px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                    placeholder="fas fa-car">
                <div class="w-14 h-12 bg-slate-100 rounded-lg flex items-center justify-center">
                    <i id="iconPreview" class="<?= isset($kategori) ? esc($kategori['icon']) : 'fas fa-car' ?> text-slate-600 text-lg"></i>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-1">Gunakan class FontAwesome, contoh: fas fa-car, fas fa-motorcycle, fas fa-snowflake</p>
        </div>

        <?php if (isset($kategori)): ?>
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="aktif" <?= $kategori['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                    <option value="nonaktif" <?= $kategori['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                </select>
            </div>
        <?php endif; ?>

        <div class="flex gap-3 pt-4">
            <a href="/admin/kategori" class="flex-1 text-center bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold py-3 rounded-lg transition">
                Batal
            </a>
            <button type="submit" class="flex-[2] bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-lg transition">
                <i class="fas fa-save mr-2"></i><?= isset($kategori) ? 'Update Kategori' : 'Simpan Kategori' ?>
            </button>
        </div>
    </form>
</div>

<script>
    document.querySelector('input[name="icon"]').addEventListener('input', function() {
        document.getElementById('iconPreview').className = this.value + ' text-slate-600 text-lg';
    });
</script>

<?= $this->endSection() ?>