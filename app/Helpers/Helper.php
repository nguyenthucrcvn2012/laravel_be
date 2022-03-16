<?php
use App\Models\Product;

function checkEmail($email) {
    if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
    }
    else {
        return false;
    }
}

/**
 * @param $name
 * @return string //newId Product
 */
function getIdProduct ($name) {
    $firstCharacter = strtoupper($name[0]);
    $newId = $firstCharacter.'000000001';
    $newProduct = Product::orderBy('created_at', 'DESC')->first();
    if($newProduct) {
        $indexProduct = substr($newProduct->product_id, 1)+1;
        switch ($indexProduct){
            case $indexProduct < 10:
                $newId = $firstCharacter.'00000000'.$indexProduct;
                break;
            case $indexProduct < 100:
                $newId = $firstCharacter.'0000000'.$indexProduct;
                break;
            case $indexProduct < 1000:
                $newId = $firstCharacter.'000000'.$indexProduct;
                break;
            case $indexProduct < 10000:
                $newId = $firstCharacter.'00000'.$indexProduct;
                break;
            case $indexProduct < 100000:
                $newId = $firstCharacter.'0000'.$indexProduct;
                break;
            case $indexProduct < 1000000:
                $newId = $firstCharacter.'000'.$indexProduct;
                break;
            case $indexProduct < 10000000:
                $newId = $firstCharacter.'00'.$indexProduct;
                break;
            case $indexProduct < 100000000:
                $newId = $firstCharacter.'0'.$indexProduct;
                break;
            default:
                $newId = $firstCharacter.$indexProduct;
        }

    }
    return $newId;

}

/**
 * @param $file
 * @param $delimiter
 * @return array|false //chuyển file csv thành mảng
 */
function convertCsvToArray ($file, $delimiter = ',') {
    if (!file_exists($file) || !is_readable($file))
        return false;



//    $allData = count(array_map('str_getcsv', file($file)));
//    return count($allData[0]);
//    $lenLineFirstData = count($allData[0]);
//    $lenData = count($allData[0]);
//
//    if($lenData < 2 || $lenLineFirstData !== 4)
//        return [false, 'Không có dữ liệu'];
//
//    if(
//        $allData[0]['customer_name'] !== 'customer_name' ||
//        $allData[1]['email'] !== 'email' ||
//        $allData[2]['tel_num'] !== 'tel_num' ||
//        $allData[3]['address'] !== 'address'
//    ){
//        return [false, 'Sai định dạng các trường (customer_name,email,tel_num,address)'];
//
//    }

//Bỏ index 0
    $header = null;
    $data = array();
    if (($handle = fopen($file, 'r')) !== false)
    {

        $i = 1;
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
        {
            if (!$header)
                $header = $row;
            if(
                count($header) !== 4 ||
                $header[0] !== 'customer_name' ||
                $header[1] !== 'email' ||
                $header[2] !== 'tel_num' ||
                $header[3] !== 'address'
            ){
                return [false, 'Header phải là (customer_name,email,tel_num,address)'];
            }
            if(count($row) > 4) {
                return [false, 'Lỗi dữ liệu ở dòng '.$i];
            }
                $data[] = array(
                        $header[0] => isset($row[0]) ? $row[0] : '',
                        $header[1] => isset($row[1]) ? $row[1] : '',
                        $header[2] => isset($row[2]) ? $row[2] : '',
                        $header[3] => isset($row[3]) ? $row[3] : ''
                );
//                );
//            $data[] = array_combine($header, $row);
            $i++;
        }
        fclose($handle);
    }

    return [true, $data];
}
