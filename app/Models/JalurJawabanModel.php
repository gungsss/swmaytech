<?php

namespace App\Models;

use CodeIgniter\Model;

class JalurJawabanModel extends Model
{
    protected $table            = 'jalur_jawaban';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_soal', 'titik_a_id', 'titik_b_id', 'style', 'control_points'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';

    // PERBAIKAN: titik_a_id dan titik_b_id boleh string (karena dari JS bisa float)
    protected $validationRules = [
        'id_soal'    => 'required|integer',
        'titik_a_id' => 'required',   // tidak dibatasi integer, bisa string/float
        'titik_b_id' => 'required',   // tidak dibatasi integer, bisa string/float
        'style'      => 'permit_empty|in_list[straight,elbow,bezier]',
    ];

    public function getJalurBySoal(int $idSoal)
    {
        return $this->where('id_soal', $idSoal)->findAll();
    }

    public function deleteBySoal(int $idSoal)
    {
        return $this->where('id_soal', $idSoal)->delete();
    }
}
