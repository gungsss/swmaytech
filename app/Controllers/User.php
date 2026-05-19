<?php

namespace App\Controllers;

use App\Models\SoalModel;
use App\Models\TitikModel;
use App\Models\JalurJawabanModel;
use App\Models\JawabanUserModel;
use App\Models\KategoriModel;

class User extends BaseController
{
    protected $soalModel;
    protected $titikModel;
    protected $jalurModel;
    protected $jawabanModel;
    protected $kategoriModel;

    public function __construct()
    {
        $this->soalModel     = new SoalModel();
        $this->titikModel    = new TitikModel();
        $this->jalurModel    = new JalurJawabanModel();
        $this->jawabanModel  = new JawabanUserModel();
        $this->kategoriModel = new KategoriModel();
    }

    public function kategori()
    {
        $idUser = session()->get('user_id');
        $kategori = $this->kategoriModel->getKategoriWithSoalCount();

        $db = \Config\Database::connect();
        foreach ($kategori as &$k) {
            $soalIds = $db->table('soal')
                ->where('id_kategori', $k['id'])
                ->where('status', 'aktif')
                ->get()
                ->getResultArray();

            $k['total_soal_aktif'] = count($soalIds);

            $soalDikerjakan = 0;
            $totalBenar = 0;
            $totalSoal = 0;

            foreach ($soalIds as $s) {
                $builder = $db->table('jawaban_user');
                $builder->select('COUNT(*) as total_jawaban');
                $builder->where(['id_user' => $idUser, 'id_soal' => $s['id']]);
                $result = $builder->get()->getRow();

                if ($result && $result->total_jawaban > 0) {
                    $soalDikerjakan++;
                    $jalurBenar = $this->jalurModel->getJalurBySoal($s['id']);
                    $hasil = $this->jawabanModel->hitungSkor($idUser, $s['id'], $jalurBenar);
                    $totalBenar += $hasil['benar'];
                    $totalSoal += $hasil['total'];
                }
            }

            $k['soal_dikerjakan'] = $soalDikerjakan;
            $k['akurasi'] = $totalSoal > 0 ? round(($totalBenar / $totalSoal) * 100) : 0;
        }

        $data['kategori'] = $kategori;
        return view('user/kategori', $data);
    }

    public function dashboard($idKategori = null)
    {
        $idUser = session()->get('user_id');

        if (!$idKategori) {
            return redirect()->to('/user/kategori');
        }

        $kategori = $this->kategoriModel->find($idKategori);
        if (!$kategori || $kategori['status'] !== 'aktif') {
            return redirect()->to('/user/kategori')->with('error', 'Kategori tidak ditemukan atau tidak aktif.');
        }

        $soal = $this->soalModel->getSoalAktifByKategori($idKategori);

        $db = \Config\Database::connect();
        $progress = [];

        foreach ($soal as $s) {
            $builder = $db->table('jawaban_user');
            $builder->select('COUNT(*) as total_jawaban');
            $builder->where(['id_user' => $idUser, 'id_soal' => $s['id']]);
            $result = $builder->get()->getRow();

            if ($result && $result->total_jawaban > 0) {
                $jalurBenar = $this->jalurModel->getJalurBySoal($s['id']);
                $hasil = $this->jawabanModel->hitungSkor($idUser, $s['id'], $jalurBenar);
                $progress[$s['id']] = [
                    'skor' => $hasil['benar'],
                    'total' => $hasil['total'],
                    'sudah_dikerjakan' => true
                ];
            }
        }

        $data['soal'] = $soal;
        $data['progress'] = $progress;
        $data['kategori'] = $kategori;
        return view('user/dashboard', $data);
    }

    public function kerjakan($id)
    {
        $soal = $this->soalModel->getSoalWithDetails($id);
        if (!$soal || $soal['status'] !== 'aktif') {
            return redirect()->to('/user/kategori')->with('error', 'Soal tidak ditemukan atau tidak aktif.');
        }

        // CHECK: Jika user sudah pernah mengerjakan soal ini, TIDAK BOLEH mengerjakan lagi
        $idUser = session()->get('user_id');
        $jawabanExisting = $this->jawabanModel->getJawabanByUserSoal($idUser, $id);
        
        if (!empty($jawabanExisting)) {
            // Hitung skor untuk ditampilkan
            $jalurBenar = $this->jalurModel->getJalurBySoal($id);
            $hasil = $this->jawabanModel->hitungSkor($idUser, $id, $jalurBenar);
            $persen = $hasil['total'] > 0 ? round(($hasil['benar'] / $hasil['total']) * 100) : 0;
            
            return redirect()->to('/user/dashboard/' . $soal['id_kategori'])
                ->with('error', 'Soal ini sudah pernah dikerjakan. Skor Anda: ' . $hasil['benar'] . '/' . $hasil['total'] . ' (' . $persen . '%)');
        }

        $data['soal'] = $soal;
        return view('user/kerjakan', $data);
    }

    public function simpanJawaban()
    {
        $idUser = session()->get('user_id');
        $idSoal = $this->request->getPost('id_soal');
        $jawabanJson = $this->request->getPost('jawaban');

        $jawaban = json_decode($jawabanJson, true);
        if (!$jawaban || !is_array($jawaban)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Jawaban tidak valid.']);
        }

        // PROTECTION: Cek apakah user sudah pernah mengerjakan soal ini
        $jawabanExisting = $this->jawabanModel->getJawabanByUserSoal($idUser, $idSoal);
        if (!empty($jawabanExisting)) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Soal ini sudah pernah dikerjakan. Tidak dapat submit ulang.'
            ]);
        }

        $db = \Config\Database::connect();
        foreach ($jawaban as $j) {
            $fromId = $j['fromId'] ?? null;
            $toId = $j['toId'] ?? null;
            if (!$fromId || !$toId) continue;

            $db->table('jawaban_user')->insert([
                'id_user'    => $idUser,
                'id_soal'    => $idSoal,
                'titik_a_id' => (string)$fromId,
                'titik_b_id' => (string)$toId,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $jalurBenar = $this->jalurModel->getJalurBySoal($idSoal);
        $hasil = $this->jawabanModel->hitungSkor($idUser, $idSoal, $jalurBenar);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Jawaban berhasil disimpan!',
            'skor'    => $hasil,
        ]);
    }

    public function riwayat()
    {
        $idUser = session()->get('user_id');
        $db = \Config\Database::connect();

        $builder = $db->table('jawaban_user ju');
        $builder->select('ju.id_soal, s.nama_soal, s.gambar, k.nama_kategori, COUNT(ju.id) as total_jawaban, MAX(ju.created_at) as waktu');
        $builder->join('soal s', 's.id = ju.id_soal');
        $builder->join('kategori k', 'k.id = s.id_kategori', 'left');
        $builder->where('ju.id_user', $idUser);
        $builder->groupBy('ju.id_soal');
        $builder->orderBy('MAX(ju.created_at)', 'DESC');

        $riwayat = $builder->get()->getResultArray();

        foreach ($riwayat as &$r) {
            $jalurBenar = $this->jalurModel->getJalurBySoal($r['id_soal']);
            $hasil = $this->jawabanModel->hitungSkor($idUser, $r['id_soal'], $jalurBenar);
            $r['benar'] = $hasil['benar'];
            $r['total'] = $hasil['total'];
            $r['salah'] = $r['total_jawaban'] - $hasil['benar'];
        }

        $data['riwayat'] = $riwayat;
        return view('user/riwayat', $data);
    }
}
