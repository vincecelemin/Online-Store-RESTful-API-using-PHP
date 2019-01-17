<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../classes/DatabaseHandler.php';

$dbHandler = new DatabaseHandler();

//$filter = array(
//    'product_name' => filter_input(INPUT_POST, 'productName_f', FILTER_SANITIZE_STRING),
//    'shop_name' => filter_input(INPUT_POST, 'shopName_f', FILTER_SANITIZE_STRING),
//    'low_range' => filter_input(INPUT_POST, 'lowPrice_f', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
//    'high_range' => filter_input(INPUT_POST, 'highPrice_f', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
//    'category' => filter_input(INPUT_POST, 'category_f', FILTER_SANITIZE_STRING),
//    'gender' => filter_input(INPUT_POST, 'gender_f', FILTER_SANITIZE_STRING),
//);

$result = $dbHandler->getAllProducts();

echo json_encode($result);