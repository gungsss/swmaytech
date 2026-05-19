<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<nav class="breadcrumb">
    <a href="/admin/dashboard"><i class="fas fa-home"></i> Dashboard</a>
    <span class="separator">/</span>
    <span class="text-slate-500">Kelola User</span>
</nav>

<div class="mb-6 sm:mb-8">
    <a href="/admin/dashboard" class="back-btn">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
    <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 mt-1">Kelola User</h1>
    <p class="text-slate-500 mt-1">Aktifkan atau nonaktifkan pengguna</p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">No</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Username</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nama Lengkap</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Email</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php $no = 1;
                foreach ($users as $u): ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 sm:px-6 py-4 text-sm text-slate-500"><?= $no++ ?></td>
                        <td class="px-4 sm:px-6 py-4 text-sm font-medium text-slate-800"><?= $u['username'] ?></td>
                        <td class="px-4 sm:px-6 py-4 text-sm text-slate-600"><?= $u['nama_lengkap'] ?></td>
                        <td class="px-4 sm:px-6 py-4 text-sm text-slate-500 hidden sm:table-cell"><?= $u['email'] ?></td>
                        <td class="px-4 sm:px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full <?= $u['status'] === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                <?= $u['status'] ?>
                            </span>
                        </td>
                        <td class="px-4 sm:px-6 py-4">
                            <div class="flex flex-wrap gap-2">
                                <a href="/admin/users/toggle/<?= $u['id'] ?>"
                                    class="text-sm font-medium px-3 py-1 rounded-lg transition <?= $u['status'] === 'aktif' ? 'text-red-600 hover:bg-red-50' : 'text-green-600 hover:bg-green-50' ?>">
                                    <i class="fas <?= $u['status'] === 'aktif' ? 'fa-ban' : 'fa-check' ?> mr-1"></i>
                                    <?= $u['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>
                                </a>
                                <a href="/admin/users/change-password/<?= $u['id'] ?>"
                                    class="text-sm font-medium px-3 py-1 rounded-lg transition text-orange-600 hover:bg-orange-50">
                                    <i class="fas fa-key mr-1"></i>Ganti Password
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (empty($users)): ?>
        <div class="p-12 text-center">
            <i class="fas fa-users text-slate-300 text-5xl mb-4"></i>
            <h3 class="text-lg font-semibold text-slate-600">Belum ada user</h3>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>