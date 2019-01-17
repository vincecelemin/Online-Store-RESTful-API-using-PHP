<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../classes/DatabaseHandler.php';

$dbHandler = new DatabaseHandler();
$result = $dbHandler->finalizeCustomerOrder(filter_input(INPUT_POST, 'profile_id', FILTER_SANITIZE_NUMBER_INT),
        filter_input(INPUT_POST, 'delivery_person', FILTER_SANITIZE_STRING),
        filter_input(INPUT_POST, 'delivery_address', FILTER_SANITIZE_STRING),
        filter_input(INPUT_POST, 'delivery_contact', FILTER_SANITIZE_STRING),
        filter_input(INPUT_POST, 'payment_type', FILTER_SANITIZE_NUMBER_INT),
        filter_input(INPUT_POST, 'current_bal', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
        filter_input(INPUT_POST, 'total_amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));

echo json_encode($result);