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
function convertCsvToArray ($file) {
    if (!file_exists($file) || !is_readable($file))
        return [false, 'File không tồn tại.'];

    $file_to_read = fopen(($file), 'r');

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

//
//    $header = null;
//    $data = array();
//    if (($handle = fopen($file, 'r')) !== false)
//    {
//
//        $i = 1;
//        while (($row = fgetcsv($handle, 1000, ',')) !== false)
//        {
//            if (!$header)
//                $header = $row;
//
//            if(
//                count($header) !== 4
//            ){
//                return [false, 'Header phải là (customer_name,email,tel_num,address)'];
//            }
//            if(count($row) > 4) {
//                return [false, 'Lỗi dữ liệu ở dòng '.$i];
//            }
//                $data[] = array(
//                    $header[0] => isset($row[0]) ? $row[0] : '',
//                    $header[1] => isset($row[1]) ? $row[1] : '',
//                    $header[2] => isset($row[2]) ? $row[2] : '',
//                    $header[3] => isset($row[3]) ? $row[3] : ''
//                );
////            $data[] = array_combine($header, $row);
//            $i++;
//        }
//        fclose($handle);
//    }
//    else{
//        return [false, 'Không đọc được file.'];
//    }
//
//    return [true, $data];
}
