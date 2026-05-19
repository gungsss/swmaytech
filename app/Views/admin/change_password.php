<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<nav class="breadcrumb">
    <a href="/admin/dashboard"><i class="fas fa-home"></i> Dashboard</a>
    <span class="separator">/</span>
    <a href="/admin/users">Kelola User</a>
    <span class="separator">/</span>
    <span class="text-slate-500">Ganti Password</span>
</nav>

<div class="mb-6 sm:mb-8">
    <a href="/admin/users" class="back-btn">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
    <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 mt-1">Ganti Password User</h1>
    <p class="text-slate-500 mt-1">Reset password untuk user: <span class="font-semibold text-slate-700"><?= $user['username'] ?></span></p>
</div>

<div class="max-w-md mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="/admin/users/update-password/<?= $user['id'] ?>" method="POST">
            <?= csrf_field() ?>
            
            <div class="mb-5">
                <label for="password_baru" class="block text-sm font-medium text-slate-700 mb-2">
                    Password Baru <span class="text-red-500">*</span>
                </label>
                <input type="password" id="password_baru" name="password_baru" required minlength="6"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="Masukkan password baru (min. 6 karakter)"
                    value="<?= old('password_baru') ?>">
                <p class="mt-1 text-xs text-slate-500">Minimal 6 karakter</p>
            </div>

            <div class="mb-6">
                <label for="konfirmasi_password" class="block text-sm font-medium text-slate-700 mb-2">
                    Konfirmasi Password <span class="text-red-500">*</span>
                </label>
                <input type="password" id="konfirmasi_password" name="konfirmasi_password" required
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="Masukkan ulang password baru"
                    value="<?= old('konfirmasi_password') ?>">
            </div>

            <div class="flex gap-3">
                <a href="/admin/users" class="flex-1 px-4 py-2.5 text-center border border-slate-300 text-slate-600 rounded-lg hover:bg-slate-50 transition font-medium">
                    Batal
                </a>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    <i class="fas fa-save mr-2"></i>Simpan Password
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>