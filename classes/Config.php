<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Config
 *
 * @author Power
 */
class Config {

    private $host;
    private $user;
    private $password;
    private $database;
    private $link;

    function __construct() {
        $this->host = "localhost";
        $this->user = "root";
        $this->password = "";
        $this->database = "concept_db";
        $this->link = "";

        $this->connect();
    }

    public function connect() {
        $this->link = mysqli_connect($this->host, $this->user, $this->password, $this->database);

        if (!$this->link) {
            die('Failed to connect to server: (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
        }
    }

    public function getLink() {
        return $this->link;
    }

}
