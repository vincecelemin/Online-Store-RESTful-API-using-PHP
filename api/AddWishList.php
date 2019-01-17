<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../classes/CartItem.php';
include '../classes/DatabaseHandler.php';

$cart_item = new CartItem();
$cart_item->setCustomer_profile_id($_POST['profile_id']);
$cart_item->setProduct_id($_POST['product_id']);


$dbHandler = new DatabaseHandler();

$result = $dbHandler->Addtowish($cart_item);

echo json_encode($result);