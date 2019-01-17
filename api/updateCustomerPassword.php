<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../classes/CustomerAccount.php';
include '../classes/DatabaseHandler.php';

$account = new CustomerAccount();
$account->setEmail(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
$account->setPassword(filter_input(INPUT_POST, 'old_password', FILTER_SANITIZE_STRING));

$dbHandler = new DatabaseHandler();
$result = $dbHandler->updateCustomerPassword($account, 
                                            trim(filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_STRING)));

echo json_encode($result);