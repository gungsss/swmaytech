<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<nav class="breadcrumb">
    <a href="/admin/dashboard"><i class="fas fa-home"></i> Dashboard</a>
    <span class="separator">/</span>
    <a href="/admin/riwayat"><i class="fas fa-history"></i> Riwayat</a>
    <span class="separator">/</span>
    <span class="text-slate-500">Detail</span>
</nav>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">Detail Pengerjaan</h1>
        <p class="text-slate-500 mt-1"><?= $user['nama_lengkap'] ?? 'Unknown' ?> - <?= $soal['nama_soal'] ?? 'Soal Dihapus' ?></p>
    </div>
    <a href="/admin/riwayat" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
</div>

<!-- Score Summary -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm font-medium text-slate-500">Skor</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800 mt-1"><?= $skor ?>%</p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-chart-pie text-blue-500 text-lg sm:text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm font-medium text-slate-500">Benar</p>
                <p class="text-2xl sm:text-3xl font-bold text-green-600 mt-1"><?= $benar ?></p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-check text-green-500 text-lg sm:text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm font-medium text-slate-500">Salah</p>
                <p class="text-2xl sm:text-3xl font-bold text-red-600 mt-1"><?= $salah ?></p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-red-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-times text-red-500 text-lg sm:text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm font-medium text-slate-500">Total Jalur</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800 mt-1"><?= $total ?></p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-road text-purple-500 text-lg sm:text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- User's Answers -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-slate-200 bg-red-50">
            <h2 class="text-lg font-semibold text-red-700">
                <i class="fas fa-user-edit mr-2"></i>Jawaban User
            </h2>
        </div>
        <div class="p-4 sm:p-6">
            <?php if (empty($jawabanUser)): ?>
                <p class="text-slate-500 text-center py-4">User tidak menjawab apapun</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($jawabanUser as $index => $jawaban): 
                        $isCorrect = false;
                        foreach ($jalurBenar as $jb) {
                            if ((strval($jawaban['titik_a_id']) == strval($jb['titik_a_id']) && strval($jawaban['titik_b_id']) == strval($jb['titik_b_id'])) ||
                                (strval($jawaban['titik_a_id']) == strval($jb['titik_b_id']) && strval($jawaban['titik_b_id']) == strval($jb['titik_a_id']))) {
                                $isCorrect = true;
                                break;
                            }
                        }
                    ?>
                        <div class="flex items-center justify-between p-3 rounded-lg <?= $isCorrect ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' ?>">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold <?= $isCorrect ? 'bg-green-500 text-white' : 'bg-red-500 text-white' ?>">
                                    <?= $index + 1 ?>
                                </span>
                                <span class="text-sm text-slate-700">
                                    Titik <?= $jawaban['titik_a_id'] ?> → <?= $jawaban['titik_b_id'] ?>
                                </span>
                            </div>
                            <span class="<?= $isCorrect ? 'text-green-600' : 'text-red-600' ?>">
                                <i class="fas <?= $isCorrect ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Correct Answers -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-slate-200 bg-green-50">
            <h2 class="text-lg font-semibold text-green-700">
                <i class="fas fa-check-double mr-2"></i>Jawaban Benar
            </h2>
        </div>
        <div class="p-4 sm:p-6">
            <?php if (empty($jalurBenar)): ?>
                <p class="text-slate-500 text-center py-4">Soal tidak memiliki jawaban benar</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($jalurBenar as $index => $jalur): ?>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-green-50 border border-green-200">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-green-500 text-white flex items-center justify-center text-xs font-bold">
                                    <?= $index + 1 ?>
                                </span>
                                <span class="text-sm text-slate-700">
                                    Titik <?= $jalur['titik_a_id'] ?> → <?= $jalur['titik_b_id'] ?>
                                </span>
                            </div>
                            <span class="text-green-600">
                                <i class="fas fa-check"></i>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- User Info Card -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mt-6">
    <h3 class="text-lg font-semibold text-slate-800 mb-4">Informasi User</h3>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <p class="text-xs text-slate-500">Nama Lengkap</p>
            <p class="font-medium text-slate-800"><?= $user['nama_lengkap'] ?? 'Unknown' ?></p>
        </div>
        <div>
            <p class="text-xs text-slate-500">Username</p>
            <p class="font-medium text-slate-800">@<?= $user['username'] ?? 'unknown' ?></p>
        </div>
        <div>
            <p class="text-xs text-slate-500">Email</p>
            <p class="font-medium text-slate-800"><?= $user['email'] ?? '-' ?></p>
        </div>
    </div>
</div>

<?= $this->endSection() ?>