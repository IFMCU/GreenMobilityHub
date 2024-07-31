<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ParkingLotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Taman Cerdas',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.307237483654572,
                'longitude' => 110.48909967144758,
                'phone_number' => '0298312222',
                'available_spots' => 25,
            ],
            [
                'name' => 'UKSW FTI',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.296136701546459, 
                'longitude' => 110.49178964951761,
                'phone_number' => '08362666533',
                'available_spots' => 100,
            ],
            [
                'name' => 'Grand Wahid Hotel',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.325865048038265,
                'longitude' => 110.50451384633617,
                'phone_number' => '08362666535',
                'available_spots' => 50,
            ],
            [
                'name' => 'Ramayana',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.324997609973325,
                'longitude' => 110.50521335592335,
                'phone_number' => '0298328500',
                'available_spots' => 50,
            ],
            [
                'name' => 'Satlantas Polres',
                'country' => 'Indonesia',
                'city' => 'Salatiga',
                'latitude' => -7.316768998380225,
                'longitude' => 110.49567759080703,
                'phone_number' => '08362666537',
                'available_spots' => 1050,
            ],
        ];

        foreach ($locations as $location) {
            DB::table('parking_lots')->insert([
                'guid' => Str::uuid()->toString(),
                'name' => $location['name'],
                'country' => $location['country'],
                'city' => $location['city'],
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                'phone_number' => $location['phone_number'],
                'available_spots' => $location['available_spots'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    }






