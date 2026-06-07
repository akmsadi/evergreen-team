<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $builder = $this->db->table('admins');
        $now = date('Y-m-d H:i:s');

        $admins = [
            [
                'username' => 'admin',
                'email' => 'admin@mypersonalwork.com',
                'password' => 'admin102030',
            ],
        ];

        foreach ($admins as $admin) {
            $existingAdmin = $builder
                ->groupStart()
                ->where('username', $admin['username'])
                ->orWhere('email', $admin['email'])
                ->groupEnd()
                ->get()
                ->getRowArray();

            if ($existingAdmin !== null) {
                continue;
            }

            $builder->insert([
                'username'   => $admin['username'],
                'email'      => $admin['email'],
                'password'   => password_hash($admin['password'], PASSWORD_DEFAULT),
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
