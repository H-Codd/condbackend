<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('units')->insert([
            'name' => 'APT 100',
            'id_owner' => '1'
        ]);
        DB::table('units')->insert([
            'name' => 'APT 101',
            'id_owner' => '1'
        ]);
        DB::table('units')->insert([
            'name' => 'APT 110',
            'id_owner' => '0'
        ]);
        DB::table('units')->insert([
            'name' => 'APT 200',
            'id_owner' => '0'
        ]);
        DB::table('areas')->insert([
            'allowed' => '1',
            'cover' => 'pool.png',
            'title' => 'Piscina',
            'days' => '1,2,3,4,5',
            'start_time' => '07:00:00',
            'end_time' => '23:00:00'
        ]);
        DB::table('areas')->insert([
            'allowed' => '1',
            'title' => 'Academia',
            'cover' => 'gym.jpg',
            'days' => '1,2,3,4,5',
            'start_time' => '07:00:00',
            'end_time' => '11:00:00'
        ]);
        DB::table('areas')->insert([
            'allowed' => '1',
            'title' => 'Churrasqueira',
            'cover' => 'barbecue.jpg',
            'days' => '3,5,6',
            'start_time' => '09:00:00',
            'end_time' => '23:00:00'
        ]);

        DB::table('walls')->insert([
            'title' => 'Alerta Geral para Todos',
            'body' => 'Badadadadadada daasdsad asdasd',
            'datecreated' => '2020-12-20 15:00:00'
        ]);

        DB::table('walls')->insert([
            'title' => 'Aviso Público',
            'body' => 'Badadadadadada asd daasdsad asdasd',
            'datecreated' => '2020-12-20 12:00:00'
        ]);

    }
}
