<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DatabaseHandler
 *
 * @author Power
 */
require_once '../classes/Config.php';
require_once '../classes/CustomerAccount.php';
require_once '../classes/CartItem.php';

class DatabaseHandler {

    private $config;

    function __construct() {
        $this->config = new Config();
    }

    private function checkEmail($email) {
        if (strpos($email, '@') !== false) {
            $split = explode('@', $email);
            return (strpos($split['1'], '.') !== false ? true : false);
        } else {
            return false;
        }
    }

    public function emailExists($email) {
        $query = "CALL checkEmailAvailability('" . $email . "')" or die(mysqli_error($this->config->getLink()));
        return (mysqli_num_rows(mysqli_query($this->config->getLink(), $query)) > 0 ? true : false);
    }

    public function registerCustomerAccount($customer) {
        if (empty($customer->getFirst_name()) ||
                empty($customer->getLast_name()) ||
                empty($customer->getGender()) ||
                empty($customer->getAddress()) ||
                empty($customer->getEmail()) ||
                empty($customer->getPassword()) ||
                empty($customer->getContact_number())) {
            return array('status' => 'failed',
                'message' => 'Incomplete Fields');
        }

        if (!$this->checkEmail($customer->getEmail())) {
            return array('status' => 'failed',
                'message' => 'Enter a valid email address');
        }

        if ($this->emailExists($customer->getEmail())) {
            return array('status' => 'failed',
                'message' => 'This email is already taken');
        } else {
            mysqli_next_result($this->config->getLink());

            $query = "CALL registerCustomerAccount('" . $customer->getFirst_name() . "', " .
                    "'" . $customer->getLast_name() . "', " .
                    "'" . $customer->getGender() . "', " .
                    "'" . $customer->getAddress() . "', " .
                    "'" . $customer->getContact_number() . "', " .
                    "'" . $customer->getEmail() . "', " .
                    "'" . hash("sha256", $customer->getPassword()) . "')" or die(mysqli_error($this->config->getLink()));

            $result = mysqli_query($this->config->getLink(), $query);
            $customer_id = mysqli_fetch_assoc($result);

            if ($result) {
                return array('status' => 'success',
                    'customer_id' => $customer_id['customer_id']);
            } else {
                return array('status' => 'failed',
                    'message' => 'Failed creating account');
            }
        }
    }

    public function authenticateCustomer($customer) {
        if (empty($customer->getEmail()) ||
                empty($customer->getPassword())) {
            return array('status' => 'failed',
                'message' => 'Incomplete Fields');
        }

        if ($this->emailExists($customer->getEmail())) {
            mysqli_next_result($this->config->getLink());

            $query = "CALL authenticateCustomerProfile('" . $customer->getEmail() . "', '" . hash("sha256", $customer->getPassword()) . "')" or die(mysqli_error($this->config->getLink()));
            $result = mysqli_query($this->config->getLink(), $query);

            if (mysqli_num_rows($result) > 0) {
                $data = mysqli_fetch_assoc($result);
                $profile_data = array('profile_id' => $data['profile_id'],
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name']);

                return array('status' => 'success',
                    'profile_data' => $profile_data);
            } else {
                return array('status' => 'failed',
                    'message' => 'Incorrect Email/Password');
            }
        } else {
            return array('status' => 'failed',
                'message' => 'Incorrect Email/Password');
        }
    }

    private function inputFilter($string) {
        return strpos($string, '?') === false;
    }

    private function filterProducts($product_list, $filters) {
//        if (!empty($filters['product_name'])) {
//            $filter = $filters['product_name'];
//            
//            for($i = 0; $i < count($product_list); $i++) {
//                if(strpos(strtolower($product_list[$i]['name']), strtolower($filter)) !== true) {
//                    unset($product_list[$i]);
//                }
//            }
//        }
//
        if (!empty($filters['low_range'])) {
            $low = $filters['low_range'];
            if (!empty($filters['high_range'])) {
                $high = $filters['high_range'];

                for ($i = 0; $i < count($product_list); $i++) {
                    $price = $product_list[$i]['price'];

                    if ($price < $low || $price > $high) {
                        unset($product_list[$i]);
                    }
                }
            } else {
                for ($i = 0; $i < count($product_list); $i++) {
                    $price = $product_list[$i]['price'];

                    if ($price < $low) {
                        unset($product_list[$i]);
                    }
                }
            }
        }

        if ($filters['category'] != '0') {
            for ($i = 0; $i < count($product_list); $i++) {
                if ($product_list[$i][$category] != $filters['category']) {
                    array_splice($product_list, $i, 1);
                }
            }
        }

        $final_list = array();
        foreach ($product_list as $product) {
            $final_list[] = $product;
        }
        return $final_list;
    }

    public function getAllProducts() {
        $query = "CALL  getAllProducts()" or die(mysqli_error($this->config->getLink()));
        $result = mysqli_query($this->config->getLink(), $query);

        if (mysqli_num_rows($result) > 0) {
            $product_list = array();

            while ($product = mysqli_fetch_assoc($result)) {
                $product_list[] = $product;
            }

            return array("status" => "success",
                "products" => $product_list);
        } else {
            return array("status" => "failed",
                "message" => "No products available.");
        }
    }

    public function getProfileInformation($profile_id) {
        $query = "CALL getProfileInformation(" . $profile_id . ")" or die(mysqli_error($this->config->getLink()));
        $result = mysqli_query($this->config->getLink(), $query);

        if ($result) {
            return array("status" => "success",
                "profile_data" => mysqli_fetch_assoc($result));
        } else {
            return array("status" => "failed",
                "message" => "Error Retrieving Profile");
        }
    }

    public function updateCustomerProfile($new_account_info, $profile_id) {
        if (empty($new_account_info->getFirst_name()) ||
                empty($new_account_info->getLast_name()) ||
                empty($new_account_info->getAddress()) ||
                empty($new_account_info->getEmail()) ||
                empty($new_account_info->getPassword()) ||
                empty($new_account_info->getContact_number())) {
            return array('status' => 'failed',
                'message' => 'Incomplete Fields');
        }

        if (!$this->checkEmail($new_account_info->getEmail())) {
            return array('status' => 'failed',
                'message' => 'Enter a valid email address');
        }

        $authenticated = $this->authenticateCustomer($new_account_info);

        if ($authenticated['status'] === 'success') {
            mysqli_next_result($this->config->getLink());

            $query = "CALL updateCustomerProfile('" . $new_account_info->getFirst_name() . "', '"
                    . $new_account_info->getLast_name() . "', '"
                    . $new_account_info->getEmail() . "', '"
                    . $new_account_info->getAddress() . "', '"
                    . $new_account_info->getContact_number() . "',"
                    . $profile_id . ")" or die(mysqli_error($this->config->getLink()));

            $result = mysqli_query($this->config->getLink(), $query);

            if ($result) {
                return array('status' => 'success');
            } else {
                return array('status' => 'failed',
                    'message' => 'Error updating account');
            }
        } else {
            return array('status' => 'failed',
                'message' => 'Incorrect Password');
        }
    }

    public function updateCustomerPassword($account, $new_password) {
        if (empty($account->getEmail()) ||
                empty($account->getPassword()) ||
                empty($new_password)) {
            return array('status' => 'failed',
                'message' => 'Incomplete Fields');
        }

        $authenticated = $this->authenticateCustomer($account);


        if ($authenticated['status'] === 'success') {
            mysqli_next_result($this->config->getLink());

            $query = "CALL updateCustomerPassword('" . $account->getEmail() . "', '" .
                    hash("sha256", $new_password) . "')"
                    or die(mysqli_error($this->config->getLink()));
            $result = mysqli_query($this->config->getLink(), $query);

            if ($result) {
                return array('status' => 'success');
            } else {
                return array('status' => 'failed',
                    'message' => 'Error udpating password');
            }
        } else {
            return array('status' => 'failed',
                'message' => 'Incorrect password');
        }
    }

    public function getProductInformation($product_id) {
        $query = "CALL getProductInformation(" . $product_id . ")" or die(mysqli_error($this->config->getLink()));
        $result = mysqli_query($this->config->getLink(), $query);

        if ($result) {
            $product_data = mysqli_fetch_assoc($result);
            mysqli_next_result($this->config->getLink());

            $query = "CALL getProductImageAddresses(" . $product_id . ")" or die(mysqli_error($this->config->getLink()));
            $result = mysqli_query($this->config->getLink(), $query);

            $product_images = array();

            while ($pic = mysqli_fetch_assoc($result)) {
                $product_images[] = $pic;
            }

            return array('status' => 'success',
                'product_data' => $product_data,
                'product_images' => $product_images);
        } else {
            return array('status' => 'failed',
                'message' => 'Error retrieving product');
        }
    }

    private function checkItemInCart($cart_product_id, $customer_profile_id) {
        $query = "CALL checkItemInCart(" . $cart_product_id . ", " . $customer_profile_id . ")" or die(mysqli_error($this->config->getLink()));
        $result = mysqli_query($this->config->getLink(), $query);

        if (mysqli_num_rows($result) > 0) {
            $info = mysqli_fetch_assoc($result);
            return array('exists' => 'true',
                'quantity' => $info['quantity'],
                'stock' => $info['stock'],
                'id' => $info['id']);
        } else {
            return array('exists' => 'false');
        }
    }

    public function addItemToCart($cart_item) {
        $item = $this->checkItemInCart($cart_item->getProduct_id(), $cart_item->getCustomer_profile_id());
        if ($item['exists'] === 'true') {
            if (($cart_item->getQuantity() + $item['quantity']) > $item['stock']) {
                return array('status' => 'failed',
                    'message' => "You will exceed the product's stock");
            } else {
                mysqli_next_result($this->config->getLink());
                return $this->updateCartQuantity($item['id'], $cart_item->getQuantity() + $item['quantity']);
            }
        } else {
            mysqli_next_result($this->config->getLink());
            $query = "CALL addItemToCart(" . $cart_item->getCustomer_profile_id() . ","
                    . $cart_item->getProduct_id() . ", "
                    . $cart_item->getQuantity() . ")" or die(mysqli_error($this->config->getLink()));

            $result = mysqli_query($this->config->getLink(), $query);

            if ($result) {
                return array('status' => 'success');
            } else {
                return array('status' => 'failed',
                    'message' => 'Error pushing to cart');
            }
        }
    }

    public function getCartItemsInformation($profile_id) {
        $query = "CALL getCartItemsInformation(" . $profile_id . ")" or die(mysqli_error($this->config->getLink()));
        $result = mysqli_query($this->config->getLink(), $query);

        if ($result) {
            $cart_list = array();

            while ($cart_item = mysqli_fetch_assoc($result)) {
                $cart_list[] = $cart_item;
            }

            return array('status' => 'success',
                'cart_list' => $cart_list);
        } else {
            return array('status' => 'failed',
                'message' => 'Error retrieving cart');
        }
    }

    public function deleteFromCart($cart_id) {
        $query = "CALL  deleteFromCart(" . $cart_id . ")" or die(mysqli_error($this->config->getLink()));
        $result = mysqli_query($this->config->getLink(), $query);

        if ($result) {
            return array('status' => 'success');
        } else {
            return array('status' => 'failed',
                'message' => 'Error removing from cart');
        }
    }

    public function updateCartQuantity($cart_id, $new_quantity) {
        $query = "CALL  updateCartQuantity(" . $cart_id . ", " . $new_quantity . ")" or die(mysqli_error($this->config->getLink()));
        $result = mysqli_query($this->config->getLink(), $query);

        if ($result) {
            return array('status' => 'success');
        } else {
            return array('status' => 'failed',
                'message' => 'Error updating cart');
        }
    }

    public function getCartCount($customer_profile_id) {
        $query = "CALL  getCartCount(" . $customer_profile_id . ")" or die(mysqli_error($this->config->getLink()));
        $result = mysqli_query($this->config->getLink(), $query);

        if ($result) {
            $cart_count = mysqli_fetch_assoc($result);
            return array('status' => 'success',
                'count' => $cart_count['cart_items']);
        } else {
            return array('status' => 'failed',
                'message' => 'Error retrieving cart');
        }
    }

    public function getCurrentBalance($customer_profile_id) {
        $query = "CALL  getCurrentBalance(" . $customer_profile_id . ")" or die(mysqli_error($this->config->getLink()));
        $result = mysqli_query($this->config->getLink(), $query);

        if ($result) {
            $current_balance = mysqli_fetch_assoc($result);
            return array('status' => 'success',
                'balance' => $current_balance['balance']);
        } else {
            return array('status' => 'failed',
                'message' => 'Error retrieving cart');
        }
    }

    public function addCustomerLoad($customer, $prev_balance, $added_balance) {
        $authenticate = $this->authenticateCustomer($customer);

        if ($authenticate['status'] === 'success') {
            mysqli_next_result($this->config->getLink());

            $query = "CALL addCustomerLoad(" . $authenticate['profile_data']['profile_id'] . ","
                    . ($prev_balance + $added_balance) . ","
                    . $added_balance . ")" or die(mysqli_error($this->config->getLink()));
            $result = mysqli_query($this->config->getLink(), $query);

            if ($result) {
                return array('status' => 'success',
                    'prev' => $prev_balance,
                    'added' => $added_balance);
            } else {
                return array('status' => 'failed',
                    'message' => 'Error adding load');
            }
        } else {
            return $authenticate;
        }
    }

    public function finalizeCustomerOrder($customer_profile_id, $delivery_person, $delivery_address, $delivery_contact, $payment_type, $current_bal, $total_amount) {
        $date_now = new DateTime('Asia/Manila');
        $r_days = rand(3, 4);
        if ($r_days == 3) {
            $r_hours = rand(8, 18);
            $r_mins = rand(0, 59);
            $delivery_date = $date_now->add(new DateInterval('P0Y0M3DT' . $r_hours . 'H' . $r_mins . 'M0S'));
        } else {
            $delivery_date = $date_now->add(new DateInterval('P0Y0M4DT0H0M0S'));
        }

        $date_now = new DateTime('Asia/Manila');

        $query = "CALL createDeliveryReference(" . $customer_profile_id . ", '"
                . $delivery_person . "', '"
                . $delivery_address . "', '"
                . $delivery_contact . "', '"
                . $payment_type . "', "
                . "'" . $date_now->format('Y-m-d H:i:s') . "', "
                . "'" . $delivery_date->format('Y-m-d H:i:s') . "')" or die(mysqli_error($this->config->getLink()));
        $result = mysqli_query($this->config->getLink(), $query);

        if ($result) {
            $delivery = mysqli_fetch_assoc($result);
            mysqli_next_result($this->config->getLink());

            $cart_data = $this->getCartItemsInformation($customer_profile_id);
            $cart_list = $cart_data['cart_list'];

            $ctr = 0;
            foreach ($cart_list as $cart_item) {
                mysqli_next_result($this->config->getLink());
                $query = "CALL addToDeliveryItems(" . $delivery['delivery_id'] . ","
                        . $cart_item['product_id'] . ","
                        . $cart_item['quantity'] . ", "
                        . ($cart_item['quantity'] * $cart_item['price']) . ")" or die(mysqli_error($this->config->getLink()));
                $result = mysqli_query($this->config->getLink(), $query);

                if (!$result) {
                    return array('status' => 'failed',
                        'message' => 'An error has occured');
                } else {
                    mysqli_next_result($this->config->getLink());
                    $result = $this->deleteFromCart($cart_item['cart_id']);

                    if (!$result['status'] === 'success') {
                        return array('status' => 'failed',
                            'message' => 'An error has occured');
                    } else {
                        mysqli_next_result($this->config->getLink());
                        $result = $this->updateProductQuantity($cart_item['product_id'], ($cart_item['stock'] - $cart_item['quantity']));

                        if (!$result['status'] === 'success') {
                            return array('status' => 'failed',
                                'message' => 'An error has occured');
                        } else {
                            mysqli_next_result($this->config->getLink());
                            $result = $this->updateCartProcessedInfo($cart_item['cart_id'], $date_now->format('Y-m-d H:i:s'));

                            if (!$result['status'] === 'success') {
                                return array('status' => 'failed',
                                    'message' => 'An error has occured');
                            }
                        }
                    }
                }

                $ctr ++;
            }

            if ($ctr == count($cart_list)) {
                if ($payment_type == '0') {
                    mysqli_next_result($this->config->getLink());

                    $result = $this->updateCustomerLoad($customer_profile_id, $current_bal, $total_amount, ($current_bal - $total_amount));
                    if ($result['status'] === 'success') {
                        return array('status' => 'success',
                            'message' => 'Your order has been finalized');
                    } else {
                        return array('status' => 'failed',
                            'message' => 'An error has occurred');
                    }
                } else {
                    return array('status' => 'success',
                        'message' => 'Your order has been finalized');
                }
            }
        } else {
            return array('status' => 'failed',
                'message' => 'Error processing data');
        }
    }

    private function updateCustomerLoad($customer_profile_id, $prev_balance, $added_balance, $new_balance) {
        $query = "CALL updateCustomerLoad(" . $customer_profile_id . ", " . $prev_balance . ", " . $added_balance . ", " . $new_balance . ")" or die(mysqli_error($this->config->getLink()));
        $result = mysqli_query($this->config->getLink(), $query);

        if ($result) {
            return array('status' => 'success');
        } else {
            return array('status' => 'failed');
        }
    }

    private function updateProductQuantity($product_id, $new_quantity) {
        $query = "CALL  updateProductQuantity(" . $product_id . ", " . $new_quantity . ")" or die(mysqli_error($this->config->getLink()));
        $result = mysqli_query($this->config->getLink(), $query);

        if ($result) {
            return array('status' => 'success');
        } else {
            return array('status' => 'failed',
                'message' => 'Error updating cart');
        }
    }

    private function updateCartProcessedInfo($cart_item_id, $process_date) {
        $query = "CALL updateCartProcessedInfo(" . $cart_item_id . ", '" . $process_date . "')" or die(mysqli_error($this->config->getLink()));
        $result = mysqli_query($this->config->getLink(), $query);

        if ($result) {
            return array('status' => 'success');
        } else {
            return array('status' => 'failed');
        }
    }

    public function getLoadTransactions($customer_profile_id) {
        $query = "CALL getLoadTransactions(" . $customer_profile_id . ")" or die(mysqli_error($this->config->getLink()));
        $result = mysqli_query($this->config->getLink(), $query);

        if ($result) {
            $transaction_list = array();

            while ($transaction = mysqli_fetch_assoc($result)) {
                $transaction_list[] = $transaction;
            }

            return array('status' => 'success',
                'transaction_list' => $transaction_list);
        } else {
            return array('status' => 'failed',
                'message' => 'Error retrieving transaction list');
        }
    }

    public function getDeliveryItems($customer_profile_id, $timestamp) {
        $query = "CALL getDeliveryItems(" . $customer_profile_id . ")" or die(mysqli_error($this->config->getLink()));
        $result = mysqli_query($this->config->getLink(), $query);

        if ($result) {
            $delivery_items = array();
            $delivered = array();

            while ($item = mysqli_fetch_assoc($result)) {
                $d_item = new DateTime($item['arrival_date']);
                $d_now = new DateTime($timestamp);

                if ($d_now > $d_item && $item['status'] == '0') {
                    $item['status'] = '1';
                    $delivered[] = $item['id'];
                }

                $delivery_items[] = $item;
            }

            if (count($delivered) > 0) {
                foreach ($delivered as $delivery_id) {
                    mysqli_next_result($this->config->getLink());
                    $result = $this->deliverItem($delivery_id);
                }
            }

            return array('status' => 'success',
                'delivery_items' => $delivery_items);
        } else {
            return array('status' => 'failed',
                'message' => 'Error retrieving transaction list');
        }
    }

    private function deliverItem($delivery_item_id) {
        $query = "CALL updateOrderStatus(" . $delivery_item_id . ", '1')" or die(mysqli_error($this->config->getLink()));
        $result = mysqli_query($this->config->getLink(), $query);

        if ($result) {
            return array('status' => 'success');
        } else {
            return array('status' => 'failed');
        }
    }

    public function cancelOrder($delivery_item_id) {
        $query = "CALL updateOrderStatus(" . $delivery_item_id . ", '2')" or die(mysqli_error($this->config->getLink()));
        $result = mysqli_query($this->config->getLink(), $query);

        if ($result) {
            return array('status' => 'success');
        } else {
            return array('status' => 'failed');
        }
    }

    public function Addtowish($cart_item) {
        $query = "CALL AddToWishlist(" . $cart_item->getCustomer_profile_id() . ","
                . $cart_item->getProduct_id() . ")" or die(mysqli_error($this->config->getLink()));

        $result = mysqli_query($this->config->getLink(), $query);

        if ($result) {
            return array('status' => 'success');
        } else {
            return array('status' => 'failed',
                'message' => 'Error Adding to Wishlist');
        }
}

}
