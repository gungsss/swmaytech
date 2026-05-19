<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="min-h-[80vh] flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <i class="fas fa-user-plus text-blue-500 text-4xl mb-4"></i>
            <h1 class="text-2xl font-bold text-slate-800">Register</h1>
            <p class="text-slate-500 mt-2">Buat akun baru</p>
        </div>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <p class="text-sm"><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="/register" method="POST">
            <?= csrf_field() ?>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" required 
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                    placeholder="Masukkan nama lengkap" value="<?= old('nama_lengkap') ?>">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Username</label>
                <input type="text" name="username" required 
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                    placeholder="Masukkan username" value="<?= old('username') ?>">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                <input type="email" name="email" required 
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                    placeholder="Masukkan email" value="<?= old('email') ?>">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                <input type="password" name="password" required 
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                    placeholder="Minimal 6 karakter">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-2">Konfirmasi Password</label>
                <input type="password" name="confirm_password" required 
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                    placeholder="Ulangi password">
            </div>

            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-lg transition">
                <i class="fas fa-user-plus mr-2"></i>Daftar
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-slate-500">Sudah punya akun? 
                <a href="/login" class="text-blue-500 hover:text-blue-700 font-medium">Login</a>
            </p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
