<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<nav class="breadcrumb">
    <a href="/admin/dashboard"><i class="fas fa-home"></i> Dashboard</a>
    <span class="separator">/</span>
    <span class="text-slate-500">Riwayat Pengerjaan</span>
</nav>

<div class="mb-6">
    <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">Riwayat Pengerjaan</h1>
    <p class="text-slate-500 mt-1">Lihat semua pengerjaan user</p>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 sm:p-6 mb-6">
    <form method="get" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Kategori</label>
            <select name="kategori" id="filterKategori" onchange="updateSoalOptions()" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                <option value="">Semua Kategori</option>
                <?php foreach ($kategori as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= ($filters['kategori'] ?? '') == $k['id'] ? 'selected' : '' ?>>
                        <?= $k['nama_kategori'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Soal</label>
            <select name="soal" id="filterSoal" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                <option value="">Semua Soal</option>
                <?php foreach ($soalOptions ?? [] as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= ($filters['soal'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                        <?= $s['nama_soal'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Cari</label>
            <div class="relative">
                <input type="text" name="search" value="<?= $filters['search'] ?? '' ?>" placeholder="Username, nama, atau soal..."
                    class="w-full px-3 py-2 pl-10 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
            </div>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
            <a href="/admin/riwayat" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fas fa-times mr-1"></i> Reset
            </a>
        </div>
    </form>
</div>

<!-- Results Table -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">User</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase hidden sm:table-cell">Kategori</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Soal</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Skor</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase hidden sm:table-cell">Waktu</th>
                    <!-- <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Aksi</th> -->
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php
                // Group by user+soal combination
                $grouped = [];
                foreach ($riwayat as $r) {
                    $key = $r['id_user'] . '-' . $r['id_soal'];
                    if (!isset($grouped[$key])) {
                        $grouped[$key] = $r;
                        $grouped[$key]['total_jawaban'] = 0;
                        foreach ($riwayat as $r2) {
                            if ($r2['id_user'] == $r['id_user'] && $r2['id_soal'] == $r['id_soal']) {
                                $grouped[$key]['total_jawaban']++;
                            }
                        }
                    }
                }
                ?>
                <?php if (empty($grouped)): ?>
                    <tr>
                        <td colspan="6" class="px-4 sm:px-6 py-8 text-center text-slate-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>Belum ada riwayat pengerjaan</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($grouped as $r): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 sm:px-6 py-4">
                                <div class="font-medium text-slate-800"><?= $r['nama_lengkap'] ?? 'Unknown' ?></div>
                                <div class="text-xs text-slate-500">@<?= $r['username'] ?? 'unknown' ?></div>
                            </td>
                            <td class="px-4 sm:px-6 py-4 text-sm text-slate-600 hidden sm:table-cell"><?= $r['nama_kategori'] ?? 'Tanpa Kategori' ?></td>
                            <td class="px-4 sm:px-6 py-4">
                                <div class="font-medium text-slate-800"><?= $r['nama_soal'] ?? 'Soal Dihapus' ?></div>
                            </td>
                            <td class="px-4 sm:px-6 py-4">
                                <?php
                                $skor = $r['total'] > 0 ? round(($r['benar'] / $r['total']) * 100) : 0;
                                $color = $skor >= 80 ? 'green' : ($skor >= 60 ? 'yellow' : 'red');
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-<?= $color ?>-100 text-<?= $color ?>-700">
                                    <?= $r['benar'] ?>/<?= $r['total'] ?> (<?= $skor ?>%)
                                </span>
                            </td>
                            <td class="px-4 sm:px-6 py-4 text-sm text-slate-500 hidden sm:table-cell">
                                <?= date('d/m/Y H:i', strtotime($r['created_at'])) ?>
                            </td>
                            <!-- <td class="px-4 sm:px-6 py-4">
                                <a href="/admin/riwayat/detail/<?= $r['id_user'] ?>/<?= $r['id_soal'] ?>" class="text-blue-500 hover:text-blue-700">
                                    <i class="fas fa-eye"></i> Lihat
                                </a>
                            </td> -->
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function updateSoalOptions() {
        const kategoriId = document.getElementById('filterKategori').value;
        const soalSelect = document.getElementById('filterSoal');

        // Clear current options
        soalSelect.innerHTML = '<option value="">Semua Soal</option>';

        if (!kategoriId) {
            // Load all soal
            fetch('/admin/soal/api/list')
                .then(res => res.json())
                .then(data => {
                    data.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s.id;
                        opt.textContent = s.nama_soal;
                        soalSelect.appendChild(opt);
                    });
                });
        } else {
            // Load soal by kategori
            fetch('/admin/soal/api/list?kategori=' + kategoriId)
                .then(res => res.json())
                .then(data => {
                    data.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s.id;
                        opt.textContent = s.nama_soal;
                        soalSelect.appendChild(opt);
                    });
                });
        }
    }
</script>

<?= $this->endSection() ?>