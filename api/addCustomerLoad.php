<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../classes/CustomerAccount.php';
include '../classes/DatabaseHandler.php';

$customer_account = new CustomerAccount();
$customer_account->setEmail(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING));
$customer_account->setPassword(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));

$dbHandler = new DatabaseHandler();
$result = $dbHandler->addCustomerLoad(
        $customer_account, 
        filter_input(INPUT_POST, 'prev_balance', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION), 
        filter_input(INPUT_POST, 'added_balance', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));

echo json_encode($result);
