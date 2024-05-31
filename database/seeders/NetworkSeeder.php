<?php

namespace Database\Seeders;

use App\Models\Network;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class NetworkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Network::create([
            'network_name' => 'MTN',
            'visibility' => '1',
        ]);

        Network::create([
            'network_name' => 'GLO',
            'visibility' => '1',
        ]);

        Network::create([
            'network_name' => 'AIRTEL',
            'visibility' => '1',
        ]);

        Network::create([
            'network_name' => '9MOBILE',
            'visibility' => '1',
        ]);
    }
}
