<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use DB;

class CustomerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $faker = \Faker\Factory::create();

        $limit = 40;

        for ($i = 0; $i < $limit; $i++) {
            DB::table('customers')->insert([
                'customer_name' => $faker->name,
                'email' => $faker->unique()->email,
                'tel_num' => '036'.$faker->numerify('#######'),
                'address' => $faker->unique()->address,
                'created_at' =>  \Carbon\Carbon::now()
            ]);
        }
    }
}
