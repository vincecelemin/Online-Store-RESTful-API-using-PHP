<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CustomerAccount
 *
 * @author Power
 */
class CustomerAccount implements JsonSerializable{
    //put your code here
    private $first_name;
    private $last_name;
    private $gender;
    private $address;
    private $contact_number;
    
    private $email;
    private $password;
    
    function getContact_number() {
        return $this->contact_number;
    }

    function setContact_number($contact_number) {
        $this->contact_number = $contact_number;
    }
        
    function getFirst_name() {
        return $this->first_name;
    }

    function getLast_name() {
        return $this->last_name;
    }

    function getGender() {
        return $this->gender;
    }

    function getAddress() {
        return $this->address;
    }

    function getEmail() {
        return $this->email;
    }

    function getPassword() {
        return $this->password;
    }

    function setFirst_name($first_name) {
        $this->first_name = $first_name;
    }

    function setLast_name($last_name) {
        $this->last_name = $last_name;
    }

    function setGender($gender) {
        $this->gender = $gender;
    }

    function setAddress($address) {
        $this->address = $address;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    public function jsonSerialize() {
        
    }

}
