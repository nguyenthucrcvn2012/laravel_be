<?php
use App\Models\Product;

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
