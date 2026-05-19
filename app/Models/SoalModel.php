<?php

namespace App\Models;

use CodeIgniter\Model;

class SoalModel extends Model
{
    protected $table            = 'soal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_kategori', 'nama_soal', 'deskripsi', 'gambar', 'img_width', 'img_height', 'status', 'created_at'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'id_kategori' => 'required|integer',
        'nama_soal'   => 'required|min_length[1]|max_length[255]',
        'gambar'      => 'permit_empty|max_length[255]',
        'status'      => 'permit_empty|in_list[aktif,nonaktif]',
    ];

    public function getSoalAktif()
    {
        return $this->where('status', 'aktif')->orderBy('created_at', 'DESC')->findAll();
    }

    public function getSoalAktifByKategori(int $idKategori)
    {
        return $this->where(['status' => 'aktif', 'id_kategori' => $idKategori])
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getSoalWithDetails($id)
    {
        $soal = $this->find($id);
        if (!$soal) return null;

        $db = \Config\Database::connect();

        $soal['kategori'] = $db->table('kategori')
            ->where('id', $soal['id_kategori'])
            ->get()
            ->getRowArray();

        $soal['titik'] = $db->table('titik')
            ->where('id_soal', $id)
            ->get()
            ->getResultArray();

        $jalurRows = $db->table('jalur_jawaban')
            ->where('id_soal', $id)
            ->get()
            ->getResultArray();

        foreach ($jalurRows as &$j) {
            $titikA = $db->table('titik')->where('id', $j['titik_a_id'])->get()->getRowArray();
            $titikB = $db->table('titik')->where('id', $j['titik_b_id'])->get()->getRowArray();
            if ($titikA) {
                $j['titik_a_x'] = $titikA['x'];
                $j['titik_a_y'] = $titikA['y'];
            }
            if ($titikB) {
                $j['titik_b_x'] = $titikB['x'];
                $j['titik_b_y'] = $titikB['y'];
            }
        }
        $soal['jalur_jawaban'] = $jalurRows;

        return $soal;
    }
}
