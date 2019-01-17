<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../classes/CustomerAccount.php';
include '../classes/DatabaseHandler.php';

$customer = new CustomerAccount();
$customer->setFirst_name(filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING));
$customer->setLast_name(filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING));
$customer->setGender(filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING));
$customer->setAddress(filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING));
$customer->setEmail(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
$customer->setPassword(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));
$customer->setContact_number(filter_input(INPUT_POST, 'contact_number', FILTER_SANITIZE_NUMBER_INT));

$dbHandler = new DatabaseHandler();
$result = $dbHandler->registerCustomerAccount($customer);

echo json_encode($result);

