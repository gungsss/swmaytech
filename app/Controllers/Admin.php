<?php

namespace App\Controllers;

use App\Models\SoalModel;
use App\Models\TitikModel;
use App\Models\JalurJawabanModel;
use App\Models\UserModel;
use App\Models\JawabanUserModel;
use App\Models\KategoriModel;

class Admin extends BaseController
{
    protected $soalModel;
    protected $titikModel;
    protected $jalurModel;
    protected $userModel;
    protected $jawabanModel;
    protected $kategoriModel;

    public function __construct()
    {
        $this->soalModel     = new SoalModel();
        $this->titikModel    = new TitikModel();
        $this->jalurModel    = new JalurJawabanModel();
        $this->userModel     = new UserModel();
        $this->jawabanModel  = new JawabanUserModel();
        $this->kategoriModel = new KategoriModel();
    }

    public function dashboard()
    {
        $db = \Config\Database::connect();

        $totalSoal     = $this->soalModel->countAll();
        $totalUser     = $this->userModel->where('role', 'user')->countAllResults();
        $totalKategori = $this->kategoriModel->countAll();

        $result = $db->query(
            'SELECT COUNT(DISTINCT CONCAT(id_user, "-", id_soal)) as total FROM jawaban_user'
        )->getRow();
        $totalPengerjaan = $result->total ?? 0;

        $allJawaban = $db->table('jawaban_user')->get()->getResultArray();
        $rataRataSkor = '0%';
        if (count($allJawaban) > 0) {
            $totalBenar = 0;
            $totalSoalDikerjakan = 0;
            foreach ($allJawaban as $j) {
                $jalurBenar = $this->jalurModel->getJalurBySoal($j['id_soal']);
                $hasil = $this->jawabanModel->hitungSkor($j['id_user'], $j['id_soal'], $jalurBenar);
                $totalBenar += $hasil['benar'];
                $totalSoalDikerjakan += $hasil['total'];
            }
            $rataRataSkor = $totalSoalDikerjakan > 0 ? round(($totalBenar / $totalSoalDikerjakan) * 100) . '%' : '0%';
        }

        $soalTerbaru = $this->soalModel->orderBy('created_at', 'DESC')->limit(5)->find();
        foreach ($soalTerbaru as &$s) {
            $result = $db->query(
                'SELECT COUNT(DISTINCT id_user) as total FROM jawaban_user WHERE id_soal = ?',
                [$s['id']]
            )->getRow();
            $s['total_pengerjaan'] = $result->total ?? 0;
        }

        $topPerformers = [];
        $users = $this->userModel->where('role', 'user')->findAll();
        foreach ($users as $u) {
            $totalBenar = 0;
            $totalSoalUser = 0;
            $userSoal = $db->query(
                'SELECT DISTINCT id_soal FROM jawaban_user WHERE id_user = ?',
                [$u['id']]
            )->getResultArray();

            foreach ($userSoal as $us) {
                $jalurBenar = $this->jalurModel->getJalurBySoal($us['id_soal']);
                $hasil = $this->jawabanModel->hitungSkor($u['id'], $us['id_soal'], $jalurBenar);
                $totalBenar += $hasil['benar'];
                $totalSoalUser += $hasil['total'];
            }

            if ($totalSoalUser > 0) {
                $topPerformers[] = [
                    'nama_lengkap' => $u['nama_lengkap'],
                    'total_soal' => count($userSoal),
                    'akurasi' => round(($totalBenar / $totalSoalUser) * 100)
                ];
            }
        }

        usort($topPerformers, function ($a, $b) {
            return $b['akurasi'] - $a['akurasi'];
        });
        $topPerformers = array_slice($topPerformers, 0, 10);

        $data = [
            'totalSoal'       => $totalSoal,
            'totalUser'       => $totalUser,
            'totalKategori'   => $totalKategori,
            'totalPengerjaan' => $totalPengerjaan,
            'rataRataSkor'    => $rataRataSkor,
            'soalTerbaru'     => $soalTerbaru,
            'topPerformers'   => $topPerformers,
        ];
        return view('admin/dashboard', $data);
    }

    // ==================== KATEGORI ====================
    public function kategori()
    {
        $data['kategori'] = $this->kategoriModel->getKategoriWithSoalCount();
        return view('admin/kategori_list', $data);
    }

    public function tambahKategori()
    {
        return view('admin/kategori_form');
    }

    public function simpanKategori()
    {
        $data = [
            'nama_kategori' => trim($this->request->getPost('nama_kategori') ?? ''),
            'deskripsi'     => trim($this->request->getPost('deskripsi') ?? ''),
            'icon'          => trim($this->request->getPost('icon') ?? 'fas fa-folder'),
            'status'        => 'aktif',
        ];

        if (empty($data['nama_kategori'])) {
            return redirect()->back()->withInput()->with('error', 'Nama kategori harus diisi.');
        }

        $this->kategoriModel->insert($data);
        return redirect()->to('/admin/kategori')->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function editKategori($id)
    {
        $data['kategori'] = $this->kategoriModel->find($id);
        if (!$data['kategori']) {
            return redirect()->to('/admin/kategori')->with('error', 'Kategori tidak ditemukan.');
        }
        return view('admin/kategori_form', $data);
    }

    public function updateKategori($id)
    {
        $kategori = $this->kategoriModel->find($id);
        if (!$kategori) {
            return redirect()->to('/admin/kategori')->with('error', 'Kategori tidak ditemukan.');
        }

        $data = [
            'nama_kategori' => trim($this->request->getPost('nama_kategori') ?? ''),
            'deskripsi'     => trim($this->request->getPost('deskripsi') ?? ''),
            'icon'          => trim($this->request->getPost('icon') ?? 'fas fa-folder'),
            'status'        => $this->request->getPost('status') ?? 'aktif',
        ];

        if (empty($data['nama_kategori'])) {
            return redirect()->back()->withInput()->with('error', 'Nama kategori harus diisi.');
        }

        $this->kategoriModel->update($id, $data);
        return redirect()->to('/admin/kategori')->with('success', 'Kategori berhasil diupdate!');
    }

    public function hapusKategori($id)
    {
        $kategori = $this->kategoriModel->find($id);
        if (!$kategori) {
            return redirect()->to('/admin/kategori')->with('error', 'Kategori tidak ditemukan.');
        }

        $totalSoal = $this->soalModel->where('id_kategori', $id)->countAllResults();
        if ($totalSoal > 0) {
            return redirect()->to('/admin/kategori')->with('error', 'Kategori masih memiliki ' . $totalSoal . ' soal. Pindahkan atau hapus soal terlebih dahulu.');
        }

        $this->kategoriModel->delete($id);
        return redirect()->to('/admin/kategori')->with('success', 'Kategori berhasil dihapus.');
    }

    // ==================== SOAL ====================
    public function soal()
    {
        $db = \Config\Database::connect();
        $soal = $this->soalModel->orderBy('created_at', 'DESC')->findAll();

        foreach ($soal as &$s) {
            $kategori = $db->table('kategori')->where('id', $s['id_kategori'])->get()->getRowArray();
            $s['nama_kategori'] = $kategori['nama_kategori'] ?? 'Tanpa Kategori';
        }

        $data['soal'] = $soal;
        return view('admin/soal_list', $data);
    }

    public function tambahSoal()
    {
        $data['kategori'] = $this->kategoriModel->getKategoriAktif();
        return view('admin/soal_form', $data);
    }

    public function simpanSoal()
    {
        $idKategori = $this->request->getPost('id_kategori');
        if (!$idKategori) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pilih kategori soal terlebih dahulu.']);
        }

        $gambar = $this->request->getFile('gambar');
        if (!$gambar || !$gambar->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gambar harus diupload.']);
        }

        $newName = $gambar->getRandomName();
        $uploadPath = FCPATH . 'uploads/soal';
        if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);
        if (!$gambar->move($uploadPath, $newName)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan gambar.']);
        }

        $imgPath = $uploadPath . '/' . $newName;
        $imgSize = @getimagesize($imgPath);
        $width = $imgSize[0] ?? 800;
        $height = $imgSize[1] ?? 600;

        $namaSoal = trim($this->request->getPost('nama_soal') ?? '');
        if (empty($namaSoal)) {
            if (file_exists($imgPath)) unlink($imgPath);
            return $this->response->setJSON(['success' => false, 'message' => 'Nama soal tidak boleh kosong.']);
        }

        $soalData = [
            'id_kategori' => $idKategori,
            'nama_soal'   => $namaSoal,
            'deskripsi'   => trim($this->request->getPost('deskripsi') ?? ''),
            'gambar'      => $newName,
            'img_width'   => $width,
            'img_height'  => $height,
            'status'      => 'aktif',
        ];

        $idSoal = $this->soalModel->insert($soalData);
        if (!$idSoal) {
            if (file_exists($imgPath)) unlink($imgPath);
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan soal.']);
        }

        $titikData = json_decode($this->request->getPost('titik'), true);
        $titikIdMap = [];
        if ($titikData && is_array($titikData)) {
            $db = \Config\Database::connect();
            foreach ($titikData as $index => $t) {
                $db->table('titik')->insert([
                    'id_soal'    => $idSoal,
                    'x'          => $t['x'],
                    'y'          => $t['y'],
                    'label'      => $t['label'] ?? '',
                    'ukuran'     => $t['ukuran'] ?? 24,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $titikIdMap[$index] = $db->insertID();
            }
        }

        $jalurData = json_decode($this->request->getPost('jalur'), true);
        if ($jalurData && is_array($jalurData) && count($titikIdMap) > 0) {
            $db = \Config\Database::connect();
            foreach ($jalurData as $j) {
                if (isset($titikIdMap[$j['fromIndex']]) && isset($titikIdMap[$j['toIndex']])) {
                    $insertData = [
                        'id_soal'    => $idSoal,
                        'titik_a_id' => $titikIdMap[$j['fromIndex']],
                        'titik_b_id' => $titikIdMap[$j['toIndex']],
                        'style'      => $j['style'] ?? 'straight',
                        'created_at' => date('Y-m-d H:i:s'),
                    ];
                    if (!empty($j['controlPoints'])) {
                        $insertData['control_points'] = json_encode($j['controlPoints']);
                    }
                    $db->table('jalur_jawaban')->insert($insertData);
                }
            }
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Soal berhasil disimpan!', 'id' => $idSoal]);
    }

    public function editSoal($id)
    {
        $data['soal'] = $this->soalModel->getSoalWithDetails($id);
        if (!$data['soal']) {
            return redirect()->to('/admin/soal')->with('error', 'Soal tidak ditemukan.');
        }
        $data['kategori'] = $this->kategoriModel->getKategoriAktif();
        return view('admin/soal_form', $data);
    }

    public function updateSoal($id)
    {
        $soal = $this->soalModel->find($id);
        if (!$soal) {
            return $this->response->setJSON(['success' => false, 'message' => 'Soal tidak ditemukan.']);
        }

        $updateData = [
            'id_kategori' => $this->request->getPost('id_kategori') ?? $soal['id_kategori'],
            'nama_soal'   => trim($this->request->getPost('nama_soal') ?? ''),
            'deskripsi'   => trim($this->request->getPost('deskripsi') ?? ''),
            'status'      => $this->request->getPost('status') ?? 'aktif',
        ];

        $gambar = $this->request->getFile('gambar');
        if ($gambar && $gambar->isValid()) {
            $oldPath = FCPATH . 'uploads/soal/' . $soal['gambar'];
            if (file_exists($oldPath)) unlink($oldPath);
            $newName = $gambar->getRandomName();
            $gambar->move(FCPATH . 'uploads/soal', $newName);
            $updateData['gambar'] = $newName;
            list($width, $height) = getimagesize(FCPATH . 'uploads/soal/' . $newName);
            $updateData['img_width'] = $width;
            $updateData['img_height'] = $height;
        }

        $this->soalModel->update($id, $updateData);

        $titikData = json_decode($this->request->getPost('titik'), true);
        if ($titikData && is_array($titikData)) {
            $db = \Config\Database::connect();
            $db->table('titik')->where('id_soal', $id)->delete();
            $db->table('jalur_jawaban')->where('id_soal', $id)->delete();

            $titikIdMap = [];
            foreach ($titikData as $index => $t) {
                $db->table('titik')->insert([
                    'id_soal' => $id,
                    'x'       => $t['x'],
                    'y'       => $t['y'],
                    'label'   => $t['label'] ?? '',
                    'ukuran'  => $t['ukuran'] ?? 24,
                ]);
                $titikIdMap[$index] = $db->insertID();
            }

            $jalurData = json_decode($this->request->getPost('jalur'), true);
            if ($jalurData && is_array($jalurData)) {
                foreach ($jalurData as $j) {
                    if (isset($titikIdMap[$j['fromIndex']]) && isset($titikIdMap[$j['toIndex']])) {
                        $insertData = [
                            'id_soal'    => $id,
                            'titik_a_id' => $titikIdMap[$j['fromIndex']],
                            'titik_b_id' => $titikIdMap[$j['toIndex']],
                            'style'      => $j['style'] ?? 'straight',
                            'created_at' => date('Y-m-d H:i:s'),
                        ];
                        if (!empty($j['controlPoints'])) {
                            $insertData['control_points'] = json_encode($j['controlPoints']);
                        }
                        $db->table('jalur_jawaban')->insert($insertData);
                    }
                }
            }
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Soal berhasil diupdate!']);
    }

    public function hapusSoal($id)
    {
        $soal = $this->soalModel->find($id);
        if (!$soal) {
            return redirect()->to('/admin/soal')->with('error', 'Soal tidak ditemukan.');
        }
        $imgPath = FCPATH . 'uploads/soal/' . $soal['gambar'];
        if (file_exists($imgPath)) unlink($imgPath);
        $this->soalModel->delete($id);
        return redirect()->to('/admin/soal')->with('success', 'Soal berhasil dihapus.');
    }

    public function users()
    {
        $data['users'] = $this->userModel->where('role', 'user')->findAll();
        return view('admin/users', $data);
    }

    public function toggleUserStatus($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }
        $newStatus = $user['status'] === 'aktif' ? 'nonaktif' : 'aktif';
        $this->userModel->update($id, ['status' => $newStatus]);
        return redirect()->back()->with('success', 'Status user berhasil diubah.');
    }

    public function changePassword($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        $data['user'] = $user;
        return view('admin/change_password', $data);
    }

    public function updatePassword($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        $passwordBaru = $this->request->getPost('password_baru');
        $konfirmasiPassword = $this->request->getPost('konfirmasi_password');

        if (empty($passwordBaru)) {
            return redirect()->back()->withInput()->with('error', 'Password baru harus diisi.');
        }

        if (strlen($passwordBaru) < 6) {
            return redirect()->back()->withInput()->with('error', 'Password minimal 6 karakter.');
        }

        if ($passwordBaru !== $konfirmasiPassword) {
            return redirect()->back()->withInput()->with('error', 'Konfirmasi password tidak cocok.');
        }

        $hashedPassword = password_hash($passwordBaru, PASSWORD_DEFAULT);
        $this->userModel->update($id, ['password' => $hashedPassword]);

        return redirect()->to('/admin/users')->with('success', 'Password user ' . $user['username'] . ' berhasil diubah.');
    }

    // ==================== RIWAYAT PENGERJAAN ====================
    public function riwayat()
    {
        $db = \Config\Database::connect();

        // Get all categories for filter
        $data['kategori'] = $this->kategoriModel->getKategoriAktif();
        
        // Get filter parameters
        $idKategori = $this->request->getGet('kategori');
        $idSoal = $this->request->getGet('soal');
        $search = $this->request->getGet('search');

        // Build query
        $builder = $db->table('jawaban_user ju');
        $builder->select('ju.*, u.username, u.nama_lengkap, s.nama_soal, s.id as soal_id, s.id_kategori, k.nama_kategori');
        $builder->join('users u', 'u.id = ju.id_user', 'left');
        $builder->join('soal s', 's.id = ju.id_soal', 'left');
        $builder->join('kategori k', 'k.id = s.id_kategori', 'left');

        // Apply filters
        if ($idKategori) {
            $builder->where('s.id_kategori', $idKategori);
        }
        if ($idSoal) {
            $builder->where('ju.id_soal', $idSoal);
        }
        if ($search) {
            $builder->groupStart();
            $builder->like('u.username', $search);
            $builder->orLike('u.nama_lengkap', $search);
            $builder->orLike('s.nama_soal', $search);
            $builder->groupEnd();
        }

        $builder->orderBy('ju.created_at', 'DESC');
        $data['riwayat'] = $builder->get()->getResultArray();

        // Get all soal for dropdown filter
        if ($idKategori) {
            $data['soalOptions'] = $this->soalModel->where('id_kategori', $idKategori)->where('status', 'aktif')->findAll();
        } else {
            $data['soalOptions'] = $this->soalModel->where('status', 'aktif')->findAll();
        }

        // Calculate scores for each entry
        foreach ($data['riwayat'] as &$r) {
            $jalurBenar = $this->jalurModel->getJalurBySoal($r['id_soal']);
            $userJawaban = $this->jawabanModel->getJawabanByUserSoal($r['id_user'], $r['id_soal']);
            
            $benar = 0;
            foreach ($userJawaban as $uj) {
                $found = false;
                foreach ($jalurBenar as $jb) {
                    if ((strval($uj['titik_a_id']) == strval($jb['titik_a_id']) && strval($uj['titik_b_id']) == strval($jb['titik_b_id'])) ||
                        (strval($uj['titik_a_id']) == strval($jb['titik_b_id']) && strval($uj['titik_b_id']) == strval($jb['titik_a_id']))) {
                        $found = true;
                        break;
                    }
                }
                if ($found) $benar++;
            }
            
            $r['benar'] = $benar;
            $r['total'] = count($jalurBenar);
            $r['salah'] = count($userJawaban) - $benar;
        }

        $data['filters'] = [
            'kategori' => $idKategori,
            'soal' => $idSoal,
            'search' => $search
        ];

        return view('admin/riwayat', $data);
    }

    public function detailRiwayat($idUser, $idSoal)
    {
        $user = $this->userModel->find($idUser);
        $soal = $this->soalModel->getSoalWithDetails($idSoal);
        
        if (!$user || !$soal) {
            return redirect()->to('/admin/riwayat')->with('error', 'Data tidak ditemukan.');
        }

        $jawabanUser = $this->jawabanModel->getJawabanByUserSoal($idUser, $idSoal);
        $jalurBenar = $this->jalurModel->getJalurBySoal($idSoal);

        $benar = 0;
        $salah = 0;
        foreach ($jawabanUser as $uj) {
            $found = false;
            foreach ($jalurBenar as $jb) {
                if ((strval($uj['titik_a_id']) == strval($jb['titik_a_id']) && strval($uj['titik_b_id']) == strval($jb['titik_b_id'])) ||
                    (strval($uj['titik_a_id']) == strval($jb['titik_b_id']) && strval($uj['titik_b_id']) == strval($jb['titik_a_id']))) {
                    $found = true;
                    break;
                }
            }
            if ($found) $benar++;
            else $salah++;
        }

        $data = [
            'user' => $user,
            'soal' => $soal,
            'jawabanUser' => $jawabanUser,
            'jalurBenar' => $jalurBenar,
            'benar' => $benar,
            'salah' => $salah,
            'total' => count($jalurBenar),
            'skor' => count($jalurBenar) > 0 ? round(($benar / count($jalurBenar)) * 100) : 0
        ];

        return view('admin/riwayat_detail', $data);
    }
}
