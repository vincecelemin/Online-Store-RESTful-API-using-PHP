<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../classes/DatabaseHandler.php';

$dbHandler = new DatabaseHandler();
$result = $dbHandler->updateCartQuantity($_POST['cart_id'], filter_input(INPUT_POST, "new_quantity", FILTER_SANITIZE_NUMBER_INT));

echo json_encode($result);