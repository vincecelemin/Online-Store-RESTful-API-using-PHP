<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CartItem
 *
 * @author Power
 */
class CartItem implements JsonSerializable{
    //put your code here
    private $product_id;
    private $customer_profile_id;
    private $quantity;
    
    function getProduct_id() {
        return $this->product_id;
    }

    function getCustomer_profile_id() {
        return $this->customer_profile_id;
    }

    function getQuantity() {
        return $this->quantity;
    }

    function setProduct_id($product_id) {
        $this->product_id = $product_id;
    }

    function setCustomer_profile_id($customer_profile_id) {
        $this->customer_profile_id = $customer_profile_id;
    }

    function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

        
    public function jsonSerialize() {
        
    }

}
