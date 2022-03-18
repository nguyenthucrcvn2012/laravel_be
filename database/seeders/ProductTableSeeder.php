<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 98; $i++) {
            $name = $faker->name;
            $id = getIdProduct($name);

            DB::table('products')->insert([
                'product_id' => $id,
                'product_name' => $name,
                'product_price' => (int) '1200000'.$i,
                'description' => $faker->paragraph(1),
                'created_at' =>  \Carbon\Carbon::now()
            ]);
        }
    }
}
