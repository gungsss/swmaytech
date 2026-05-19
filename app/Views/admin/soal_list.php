<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<nav class="breadcrumb">
    <a href="/admin/dashboard"><i class="fas fa-home"></i> Dashboard</a>
    <span class="separator">/</span>
    <span class="text-slate-500">Daftar Soal</span>
</nav>

<div class="mb-6 sm:mb-8 flex flex-wrap justify-between items-center gap-3">
    <div>
        <a href="/admin/dashboard" class="back-btn">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 mt-1">Daftar Soal</h1>
        <p class="text-slate-500 mt-1">Kelola soal elektronika per kategori</p>
    </div>
    <a href="/admin/soal/tambah" class="bg-blue-500 hover:bg-blue-600 text-white px-4 sm:px-6 py-3 rounded-lg font-medium transition text-sm sm:text-base">
        <i class="fas fa-plus mr-2"></i>Tambah Soal
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Gambar</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nama Soal</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Kategori</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase hidden sm:table-cell">Deskripsi</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php foreach ($soal as $s): ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 sm:px-6 py-4">
                            <img src="/uploads/soal/<?= $s['gambar'] ?>" alt="<?= $s['nama_soal'] ?>"
                                class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-lg">
                        </td>
                        <td class="px-4 sm:px-6 py-4 text-sm font-medium text-slate-800"><?= $s['nama_soal'] ?></td>
                        <td class="px-4 sm:px-6 py-4">
                            <span class="px-2 py-1 bg-blue-50 text-blue-600 text-xs font-medium rounded-full">
                                <?= $s['nama_kategori'] ?? 'Tanpa Kategori' ?>
                            </span>
                        </td>
                        <td class="px-4 sm:px-6 py-4 text-sm text-slate-500 hidden sm:table-cell"><?= substr($s['deskripsi'] ?? '-', 0, 50) ?>...</td>
                        <td class="px-4 sm:px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full <?= $s['status'] === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                <?= $s['status'] ?>
                            </span>
                        </td>
                        <td class="px-4 sm:px-6 py-4">
                            <div class="flex gap-2">
                                <a href="/admin/soal/edit/<?= $s['id'] ?>" class="text-blue-500 hover:text-blue-700 p-2 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="/admin/soal/hapus/<?= $s['id'] ?>" class="text-red-500 hover:text-red-700 p-2 hover:bg-red-50 rounded-lg transition"
                                    onclick="return confirm('Yakin hapus soal ini?')" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (empty($soal)): ?>
        <div class="p-12 text-center">
            <i class="fas fa-inbox text-slate-300 text-5xl mb-4"></i>
            <h3 class="text-lg font-semibold text-slate-600">Belum ada soal</h3>
            <p class="text-slate-400 mt-2">Klik "Tambah Soal" untuk membuat soal pertama.</p>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>