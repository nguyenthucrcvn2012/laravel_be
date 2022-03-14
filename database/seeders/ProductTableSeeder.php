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

        $limit = 100;

        for ($i = 0; $i < $limit; $i++) {
            $name = $faker->name;
            if($i > 9){
                $id = 'S0000000'.$i;
            }
            else{
                $id = 'S00000000'.$i;
            }

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
