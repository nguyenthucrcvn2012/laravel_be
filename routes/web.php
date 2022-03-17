<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    function convertArray() {
        $file_to_read = fopen(public_path('uploads/files/example.csv'), 'r');

        $datas = [];
        $i = 1;
        while (!feof($file_to_read) ) {
            $line = fgetcsv($file_to_read, 1000, ',');
            if( $i == 1 ) {
                if(
                    count($line)!== 4||
                    $line[0]!=='customer_name'||
                    $line[1]!=='email'||
                    $line[2]!=='tel_num'||
                    $line[3]!=='address'
                ) {
                    return [false, 'Header phải là (customer_name,email,tel_num,address)'];
                }
            }
            else {
                $datas[] = $line;
            }
            $i += 1;
        }
        fclose($file_to_read);

        if(count($datas) < 2) {
            return [false, 'Không có dữ liệu'];
        }
        return [true, $datas];
    }

    dd(convertArray());
});
