<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username'     => 'admin',
                'email'        => 'admin@quiz.com',
                'password'     => password_hash('admin123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Administrator',
                'role'         => 'admin',
                'status'       => 'aktif',
                'created_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'username'     => 'user1',
                'email'        => 'user1@quiz.com',
                'password'     => password_hash('user123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'User Satu',
                'role'         => 'user',
                'status'       => 'aktif',
                'created_at'   => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
