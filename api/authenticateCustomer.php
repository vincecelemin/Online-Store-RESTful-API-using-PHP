<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../classes/CustomerAccount.php';
include '../classes/DatabaseHandler.php';

$customer = new CustomerAccount();

$customer->setEmail(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
$customer->setPassword(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));

$dbHandler = new DatabaseHandler();
$result = $dbHandler->authenticateCustomer($customer);

echo json_encode($result);