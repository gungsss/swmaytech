<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<nav class="breadcrumb">
    <a href="/admin/dashboard"><i class="fas fa-home"></i> Dashboard</a>
    <span class="separator">/</span>
    <span class="text-slate-500">Overview</span>
</nav>

<div class="mb-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">Dashboard Admin</h1>
    <p class="text-slate-500 mt-1">Kelola soal, kategori, dan pengguna</p>
</div>

<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-5 sm:p-6 border border-slate-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm font-medium text-slate-500">Total Kategori</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800 mt-1"><?= $totalKategori ?></p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-folder text-indigo-500 text-lg sm:text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-5 sm:p-6 border border-slate-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm font-medium text-slate-500">Total Soal</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800 mt-1"><?= $totalSoal ?></p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-question-circle text-blue-500 text-lg sm:text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-5 sm:p-6 border border-slate-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm font-medium text-slate-500">Total User</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800 mt-1"><?= $totalUser ?></p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-green-500 text-lg sm:text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-5 sm:p-6 border border-slate-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm font-medium text-slate-500">Total Pengerjaan</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800 mt-1"><?= $totalPengerjaan ?? 0 ?></p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-clipboard-check text-purple-500 text-lg sm:text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-5 sm:p-6 border border-slate-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm font-medium text-slate-500">Rata-rata Skor</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800 mt-1"><?= $rataRataSkor ?? '0%' ?></p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-chart-line text-orange-500 text-lg sm:text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-slate-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-slate-800">Soal Terbaru</h2>
            <a href="/admin/soal/tambah" class="bg-blue-500 hover:bg-blue-600 text-white px-3 sm:px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fas fa-plus mr-1"></i><span class="hidden sm:inline">Tambah</span>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nama</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase hidden sm:table-cell">Dikerjakan</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($soalTerbaru as $s): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 sm:px-6 py-4 text-sm font-medium text-slate-800"><?= $s['nama_soal'] ?></td>
                            <td class="px-4 sm:px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full <?= $s['status'] === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                    <?= $s['status'] ?>
                                </span>
                            </td>
                            <td class="px-4 sm:px-6 py-4 text-sm text-slate-500 hidden sm:table-cell"><?= $s['total_pengerjaan'] ?? 0 ?> kali</td>
                            <td class="px-4 sm:px-6 py-4">
                                <a href="/admin/soal/edit/<?= $s['id'] ?>" class="text-blue-500 hover:text-blue-700 mr-3">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="px-4 sm:px-6 py-3 border-t border-slate-200">
            <a href="/admin/soal" class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                Lihat Semua Soal →
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-800">Top Performers</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Rank</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">User</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase hidden sm:table-cell">Soal</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Akurasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php $rank = 1;
                    foreach ($topPerformers ?? [] as $tp): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 sm:px-6 py-4">
                                <?php if ($rank <= 3): ?>
                                    <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-yellow-100 text-yellow-700 flex items-center justify-center font-bold text-sm">
                                        <?= $rank ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-slate-500 font-medium pl-2"><?= $rank ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 sm:px-6 py-4 text-sm font-medium text-slate-800"><?= $tp['nama_lengkap'] ?></td>
                            <td class="px-4 sm:px-6 py-4 text-sm text-slate-500 hidden sm:table-cell"><?= $tp['total_soal'] ?> soal</td>
                            <td class="px-4 sm:px-6 py-4">
                                <span class="text-green-600 font-semibold"><?= $tp['akurasi'] ?>%</span>
                            </td>
                        </tr>
                    <?php $rank++;
                    endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <a href="/admin/kategori" class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition flex items-center gap-4">
        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-folder text-indigo-500 text-xl"></i>
        </div>
        <div>
            <p class="font-semibold text-slate-800">Kelola Kategori</p>
            <p class="text-sm text-slate-500">Tambah/edit kategori soal</p>
        </div>
    </a>
    <a href="/admin/soal" class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition flex items-center gap-4">
        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-question-circle text-blue-500 text-xl"></i>
        </div>
        <div>
            <p class="font-semibold text-slate-800">Kelola Soal</p>
            <p class="text-sm text-slate-500">Tambah, edit, hapus soal</p>
        </div>
    </a>
    <a href="/admin/users" class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition flex items-center gap-4">
        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-users text-green-500 text-xl"></i>
        </div>
        <div>
            <p class="font-semibold text-slate-800">Kelola User</p>
            <p class="text-sm text-slate-500">Aktifkan/nonaktifkan user</p>
        </div>
    </a>
    <a href="/admin/riwayat" class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition flex items-center gap-4">
        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-history text-purple-500 text-xl"></i>
        </div>
        <div>
            <p class="font-semibold text-slate-800">Riwayat Pengerjaan</p>
            <p class="text-sm text-slate-500">Lihat semua pengerjaan user</p>
        </div>
    </a>
</div>
<?= $this->endSection() ?>