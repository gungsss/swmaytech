<?php

namespace App\Models;

use CodeIgniter\Model;

class JawabanUserModel extends Model
{
    protected $table            = 'jawaban_user';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_user', 'id_soal', 'titik_a_id', 'titik_b_id', 'created_at'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = ''; // No updated_at field

    public function getJawabanByUserSoal(int $idUser, int $idSoal)
    {
        return $this->where(['id_user' => $idUser, 'id_soal' => $idSoal])->findAll();
    }

    /**
     * Check if user has already answered a specific soal
     * Returns true if already answered (used for no-reattempt protection)
     */
    public function sudahDikerjakan(int $idUser, int $idSoal): bool
    {
        $result = $this->where(['id_user' => $idUser, 'id_soal' => $idSoal])->countAllResults();
        return $result > 0;
    }

    public function deleteJawabanUser(int $idUser, int $idSoal)
    {
        return $this->where(['id_user' => $idUser, 'id_soal' => $idSoal])->delete();
    }

    public function hitungSkor(int $idUser, int $idSoal, array $jalurBenar)
    {
        $jawabanUser = $this->getJawabanByUserSoal($idUser, $idSoal);

        $benar = 0;
        foreach ($jalurBenar as $jalur) {
            $found = false;
            foreach ($jawabanUser as $jawaban) {
                if (
                    ($jawaban['titik_a_id'] == $jalur['titik_a_id'] && $jawaban['titik_b_id'] == $jalur['titik_b_id']) ||
                    ($jawaban['titik_a_id'] == $jalur['titik_b_id'] && $jawaban['titik_b_id'] == $jalur['titik_a_id'])
                ) {
                    $found = true;
                    break;
                }
            }
            if ($found) $benar++;
        }

        return [
            'benar' => $benar,
            'total' => count($jalurBenar),
            'skor'  => $benar,
        ];
    }
}
