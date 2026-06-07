<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PlayersSeeder extends Seeder
{
    public function run()
    {
        $builder = $this->db->table('players');
        $timestamp = date('Y-m-d H:i:s');

        $players = [
            ['name' => 'Reaz',         'organization' => 'VF Asia',         'total_contribution' => 5380, 'total_expense' => 4857, 'email' => 'reaz@evergreenteam.com',          'phone' => '+8801711000101', 'address' => 'Agrabad, Chattogram, Bangladesh',          'status' => 'approved'],
            ['name' => 'Shalauddin',   'organization' => 'VF Asia',         'total_contribution' => 2500, 'total_expense' => 2420, 'email' => 'shalauddin@evergreenteam.com',    'phone' => '+8801711000102', 'address' => 'Kotwali, Chattogram, Bangladesh',          'status' => 'approved'],
            ['name' => 'Mehedi',       'organization' => 'VF Asia',         'total_contribution' => 2000, 'total_expense' => 1905, 'email' => 'mehedi@evergreenteam.com',        'phone' => '+8801711000103', 'address' => 'Panchlaish, Chattogram, Bangladesh',      'status' => 'approved'],
            ['name' => 'Faruk',        'organization' => 'VF Asia',         'total_contribution' => 3000, 'total_expense' => 2693, 'email' => 'faruk@evergreenteam.com',         'phone' => '+8801711000104', 'address' => 'Halishahar, Chattogram, Bangladesh',      'status' => 'approved'],
            ['name' => 'Masud',        'organization' => 'Avanta',          'total_contribution' => 5000, 'total_expense' => 4786, 'email' => 'masud@evergreenteam.com',         'phone' => '+8801711000105', 'address' => 'Nasirabad, Chattogram, Bangladesh',       'status' => 'approved'],
            ['name' => 'Bulbul',       'organization' => 'Avanta',          'total_contribution' => 5210, 'total_expense' => 4743, 'email' => 'bulbul@evergreenteam.com',        'phone' => '+8801711000106', 'address' => 'Khulshi, Chattogram, Bangladesh',         'status' => 'approved'],
            ['name' => 'Nasir',        'organization' => 'Deko',            'total_contribution' => 5536, 'total_expense' => 4933, 'email' => 'nasir@evergreenteam.com',         'phone' => '+8801711000107', 'address' => 'GEC Circle, Chattogram, Bangladesh',      'status' => 'approved'],
            ['name' => 'Bilash',       'organization' => 'Marks',           'total_contribution' => 5000, 'total_expense' => 4193, 'email' => 'bilash@evergreenteam.com',        'phone' => '+8801711000108', 'address' => 'Chawkbazar, Chattogram, Bangladesh',      'status' => 'approved'],
            ['name' => 'Sumon',        'organization' => 'City Bank',       'total_contribution' => 2500, 'total_expense' => 1980, 'email' => 'sumon@evergreenteam.com',         'phone' => '+8801711000109', 'address' => 'Muradpur, Chattogram, Bangladesh',        'status' => 'approved'],
            ['name' => 'Jaffar',       'organization' => 'Wolverine',       'total_contribution' => 3000, 'total_expense' => 2978, 'email' => 'jaffar@evergreenteam.com',        'phone' => '+8801711000110', 'address' => 'Lalkhan Bazar, Chattogram, Bangladesh',   'status' => 'approved'],
            ['name' => 'Rajib',        'organization' => 'Wolverine',       'total_contribution' => 3000, 'total_expense' => 3038, 'email' => 'rajib@evergreenteam.com',         'phone' => '+8801711000111', 'address' => 'Oxygen, Chattogram, Bangladesh',          'status' => 'approved'],
            ['name' => 'Shamim',       'organization' => 'Wolverine',       'total_contribution' => 5000, 'total_expense' => 5242, 'email' => 'shamim@evergreenteam.com',        'phone' => '+8801711000112', 'address' => 'Bakalia, Chattogram, Bangladesh',         'status' => 'approved'],
            ['name' => 'Chinmoy',      'organization' => 'Wolverine',       'total_contribution' => 2000, 'total_expense' => 2569, 'email' => 'chinmoy@evergreenteam.com',       'phone' => '+8801711000113', 'address' => 'Pahartali, Chattogram, Bangladesh',       'status' => 'approved'],
            ['name' => 'Kawsar',       'organization' => 'Wolverine',       'total_contribution' => 3000, 'total_expense' => 3923, 'email' => 'kawsar@evergreenteam.com',        'phone' => '+8801711000114', 'address' => 'Bayazid, Chattogram, Bangladesh',         'status' => 'approved'],
            ['name' => 'Iqbal',        'organization' => 'Wolverine',       'total_contribution' => 8671, 'total_expense' => 7523, 'email' => 'iqbal@evergreenteam.com',         'phone' => '+8801711000115', 'address' => 'Dampara, Chattogram, Bangladesh',         'status' => 'approved'],
            ['name' => 'Avijit',       'organization' => 'Wolverine',       'total_contribution' => 6360, 'total_expense' => 6044, 'email' => 'avijit@evergreenteam.com',        'phone' => '+8801711000116', 'address' => 'Chandgaon, Chattogram, Bangladesh',       'status' => 'approved'],
            ['name' => 'Sajidul Islam', 'organization' => 'Ching Tai Cloth', 'total_contribution' => 1000, 'total_expense' =>  530, 'email' => 'sajidul@evergreenteam.com',       'phone' => '+8801711000117', 'address' => 'Sholoshahar, Chattogram, Bangladesh',     'status' => 'approved'],
            ['name' => 'Anis',         'organization' => 'Aliza',           'total_contribution' => 5700, 'total_expense' => 5595, 'email' => 'anis@evergreenteam.com',          'phone' => '+8801711000118', 'address' => 'Double Mooring, Chattogram, Bangladesh',  'status' => 'approved'],
            ['name' => 'Shakil',       'organization' => 'VF Asia',         'total_contribution' => 1000, 'total_expense' =>  895, 'email' => 'shakil@evergreenteam.com',        'phone' => '+8801711000119', 'address' => 'Sadarghat, Chattogram, Bangladesh',       'status' => 'approved'],
            ['name' => 'Tonmoy',       'organization' => 'AUW',             'total_contribution' => 1000, 'total_expense' =>  671, 'email' => 'tonmoy@evergreenteam.com',        'phone' => '+8801711000120', 'address' => 'Enayet Bazar, Chattogram, Bangladesh',    'status' => 'approved'],
            ['name' => 'Noor-e-Alam',  'organization' => 'Aliza',           'total_contribution' => 6000, 'total_expense' => 5329, 'email' => 'noor.e.alam@evergreenteam.com',  'phone' => '+8801711000121', 'address' => 'Firingee Bazar, Chattogram, Bangladesh',  'status' => 'approved'],
            ['name' => 'Mehdi Hasan',  'organization' => 'Aliza',           'total_contribution' => 4500, 'total_expense' => 4370, 'email' => 'mehdi.hasan@evergreenteam.com',  'phone' => '+8801711000122', 'address' => 'Anderkilla, Chattogram, Bangladesh',      'status' => 'approved'],
            ['name' => 'Bablu',        'organization' => 'Employee',                 'total_contribution' => 1000, 'total_expense' =>  962, 'email' => 'bablu@evergreenteam.com',         'phone' => '+8801711000123', 'address' => 'Boxirhat, Chattogram, Bangladesh',        'status' => 'approved'],
            ['name' => 'Sarup',        'organization' => 'Employee',                 'total_contribution' =>  500, 'total_expense' =>  468, 'email' => 'sarup@evergreenteam.com',         'phone' => '+8801711000124', 'address' => 'Patharghata, Chattogram, Bangladesh',     'status' => 'approved'],
            ['name' => 'Ashraful',     'organization' => 'VF Asia',         'total_contribution' =>    0, 'total_expense' =>  819, 'email' => 'ashraful@evergreenteam.com',      'phone' => '+8801711000125', 'address' => 'Jamal Khan, Chattogram, Bangladesh',      'status' => 'approved'],
            ['name' => 'Fahad',        'organization' => 'VF Asia',         'total_contribution' =>  580, 'total_expense' =>  776, 'email' => 'fahad@evergreenteam.com',         'phone' => '+8801711000126', 'address' => 'Kazir Dewri, Chattogram, Bangladesh',     'status' => 'approved'],
            ['name' => 'Sadi',         'organization' => 'Einstoffen',                 'total_contribution' => 1500, 'total_expense' => 1908, 'email' => 'sadi@evergreenteam.com',          'phone' => '+8801711000127', 'address' => 'Dewanhat, Chattogram, Bangladesh',        'status' => 'approved'],
            ['name' => 'Miraz',        'organization' => 'Employee',                 'total_contribution' => 1000, 'total_expense' =>  791, 'email' => 'miraz@evergreenteam.com',         'phone' => '+8801711000128', 'address' => 'Shulkbahar, Chattogram, Bangladesh',      'status' => 'approved'],
            ['name' => 'Himel',        'organization' => 'Employee',                 'total_contribution' =>    0, 'total_expense' =>  368, 'email' => 'himel@evergreenteam.com',         'phone' => '+8801711000129', 'address' => 'Akbar Shah, Chattogram, Bangladesh',      'status' => 'approved'],
            ['name' => 'Shezan',       'organization' => 'ROBI',                 'total_contribution' =>    0, 'total_expense' =>  368, 'email' => 'shezan@evergreenteam.com',        'phone' => '+8801711000130', 'address' => 'Kalurghat, Chattogram, Bangladesh',       'status' => 'approved'],
        ];

        foreach ($players as $player) {
            $exists = $builder->where('email', $player['email'])->get()->getRowArray();

            if ($exists !== null) {
                $builder->where('email', $player['email'])->update($player + [
                    'updated_at' => $timestamp,
                ]);

                continue;
            }

            $builder->insert($player + [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }
    }
}
