<?php

namespace App\Models;

use CodeIgniter\Model;

class TitikModel extends Model
{
    protected $table            = 'titik';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_soal', 'x', 'y', 'label', 'ukuran'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';

    // PERBAIKAN: x dan y numeric (boleh float), id_soal integer
    protected $validationRules = [
        'id_soal' => 'required|integer',
        'x'       => 'required|numeric',   // ✅ numeric = int atau float
        'y'       => 'required|numeric',     // ✅ numeric = int atau float
    ];

    public function getTitikBySoal(int $idSoal)
    {
        return $this->where('id_soal', $idSoal)->findAll();
    }

    public function deleteBySoal(int $idSoal)
    {
        return $this->where('id_soal', $idSoal)->delete();
    }
}
