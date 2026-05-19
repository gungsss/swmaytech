<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table            = 'kategori';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nama_kategori', 'deskripsi', 'icon', 'status'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $validationRules = [
        'nama_kategori' => 'required|min_length[1]|max_length[100]',
        'status'        => 'permit_empty|in_list[aktif,nonaktif]',
    ];

    public function getKategoriAktif()
    {
        return $this->where('status', 'aktif')->orderBy('nama_kategori', 'ASC')->findAll();
    }

    public function getKategoriWithSoalCount()
    {
        $db = \Config\Database::connect();
        $kategori = $this->orderBy('nama_kategori', 'ASC')->findAll();

        foreach ($kategori as &$k) {
            $k['total_soal'] = $db->table('soal')
                ->where('id_kategori', $k['id'])
                ->where('status', 'aktif')
                ->countAllResults();
        }

        return $kategori;
    }
}
