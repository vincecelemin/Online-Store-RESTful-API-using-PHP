<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../classes/CustomerAccount.php';
include '../classes/DatabaseHandler.php';

$new_account_info = new CustomerAccount();
$new_account_info->setFirst_name(trim(filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING)));
$new_account_info->setLast_name(trim(filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING)));
$new_account_info->setAddress(trim(filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING)));
$new_account_info->setEmail(trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)));
$new_account_info->setPassword(trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING)));
$new_account_info->setContact_number(trim(filter_input(INPUT_POST, 'contact_number', FILTER_SANITIZE_STRING)));

$dbHandler= new DatabaseHandler();
$result = $dbHandler->updateCustomerProfile($new_account_info, filter_input(INPUT_POST, 'profile_id', FILTER_SANITIZE_NUMBER_INT));
echo json_encode($result);
