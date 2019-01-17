<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../classes/DatabaseHandler.php';

$dbHandler = new DatabaseHandler();
$result = $dbHandler->getCartCount($_POST['profile_id']);

echo json_encode($result);