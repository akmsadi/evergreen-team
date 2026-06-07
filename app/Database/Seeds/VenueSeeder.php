<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class VenueSeeder extends Seeder
{
    public function run()
    {
        $builder = $this->db->table('venues');
        $timestamp = date('Y-m-d H:i:s');

        $venues = [
            ['name' => 'MA Aziz Stadium, Chattogram'],
            ['name' => 'Zahur Ahmed Chowdhury Stadium, Chattogram'],
            ['name' => 'Chattogram Abahani Cricket Ground, Chattogram'],
            ['name' => 'Police Lines Ground, Chattogram'],
            ['name' => 'Port Trust Ground, Chattogram'],
        ];

        foreach ($venues as $venue) {
            $exists = $builder->where('name', $venue['name'])->get()->getRowArray();

            if ($exists !== null) {
                continue;
            }

            $builder->insert($venue + [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }
    }
}
