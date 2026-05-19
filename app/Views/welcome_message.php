<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="min-h-[80vh] flex items-center justify-center">
    <div class="text-center max-w-2xl">
        <div class="mb-8">
            <i class="fas fa-bolt text-blue-500 text-6xl mb-6"></i>
            <h1 class="text-4xl font-bold text-slate-800 mb-4">Aset Simulator</h1>
            <p class="text-xl text-slate-500 leading-relaxed">
                Platform interaktif untuk belajar dan menguji pemahaman rangkaian elektronika.
                Upload gambar rangkaian, tentukan titik koneksi, dan uji kemampuan Anda!
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <i class="fas fa-upload text-blue-500 text-3xl mb-4"></i>
                <h3 class="font-semibold text-slate-800 mb-2">Upload Gambar</h3>
                <p class="text-sm text-slate-500">Upload gambar rangkaian elektronika lengkap</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <i class="fas fa-dot-circle text-green-500 text-3xl mb-4"></i>
                <h3 class="font-semibold text-slate-800 mb-2">Tentukan Titik</h3>
                <p class="text-sm text-slate-500">Tandai titik-titik koneksi pada gambar</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <i class="fas fa-project-diagram text-purple-500 text-3xl mb-4"></i>
                <h3 class="font-semibold text-slate-800 mb-2">Sambungkan Jalur</h3>
                <p class="text-sm text-slate-500">Uji kemampuan dengan menyambungkan jalur</p>
            </div>
        </div>

        <div class="flex justify-center gap-4">
            <?php if (!session()->get('logged_in')): ?>
                <a href="/login" class="bg-blue-500 hover:bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold transition">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </a>
                <a href="/register" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-8 py-3 rounded-lg font-semibold transition">
                    <i class="fas fa-user-plus mr-2"></i>Daftar
                </a>
            <?php else: ?>
                <a href="<?= session()->get('role') === 'admin' ? '/admin/dashboard' : '/user/dashboard' ?>"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold transition">
                    <i class="fas fa-home mr-2"></i>Dashboard
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>