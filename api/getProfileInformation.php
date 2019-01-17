<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include '../classes/DatabaseHandler.php';

$profile_id = filter_input(INPUT_POST, 'profile_id', FILTER_SANITIZE_NUMBER_INT);

$dbHandler = new DatabaseHandler();
$result = $dbHandler->getProfileInformation($profile_id);

echo json_encode($result);

