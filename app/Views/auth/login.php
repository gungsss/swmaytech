<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="min-h-[80vh] flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <i class="fas fa-bolt text-blue-500 text-4xl mb-4"></i>
            <h1 class="text-2xl font-bold text-slate-800">Login</h1>
            <p class="text-slate-500 mt-2">Masuk ke akun Anda</p>
        </div>

        <form action="/login" method="POST">
            <?= csrf_field() ?>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Username</label>
                <input type="text" name="username" required 
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                    placeholder="Masukkan username">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                <input type="password" name="password" required 
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                    placeholder="Masukkan password">
            </div>

            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-lg transition">
                <i class="fas fa-sign-in-alt mr-2"></i>Login
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-slate-500">Belum punya akun? 
                <a href="/register" class="text-blue-500 hover:text-blue-700 font-medium">Daftar sekarang</a>
            </p>
        </div>

        <div class="mt-4 p-3 bg-slate-50 rounded-lg text-xs text-slate-500">
            <p class="font-semibold mb-1">Demo Account:</p>
            <p>Admin: admin / admin123</p>
            <p>User: user1 / user123</p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
