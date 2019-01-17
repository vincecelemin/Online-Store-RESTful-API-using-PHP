<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../classes/DatabaseHandler.php';

$dbHandler = new DatabaseHandler();
$result = $dbHandler->getLoadTransactions(filter_input(INPUT_POST, 'profile_id', FILTER_SANITIZE_NUMBER_INT));

echo json_encode($result);