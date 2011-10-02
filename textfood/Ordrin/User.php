<?php
class User extends Ordrin {

    function __construct() {
        // Placeholder
    }
    
    function makeAcct($email, $password, $fName, $lName) {
        return $this->_request(array(
                             'type' => 'POST',
                             'method' => 'uN',
                             'url_params' => array(
                                 $email,
                             ),
                             'data_params' => array(
                                 'password' => $password,
                                 'first_name' => $fName,
                                 'last_name' => $lName
                             ),
                        ));
    }

    function getAcct() {
        return $this->_request(array(
                             'type' => 'GET',
                             'method' => 'u',
                             'url_params' => array(
                                 $this->_email
                             ),
                             'data_params' => array(),
                        ));
    }

    function getAddress($addrNick = '') {
        if (!empty($addrNick)) {
            return $this->_request(array(
                                 'type' => 'GET',
                                 'method' => 'u',
                                 'url_params' => array(
                                     $this->_email,
                                     'addrs',
                                     $addrNick,
                                 ),
                                 'data_params' => array(),
                            ));
        } else {
            return $this->_request(array(
                                 'type' => 'GET',
                                 'method' => 'u',
                                 'url_params' => array(
                                     $this->_email,
                                     'addrs',
                                 ),
                                 'data_params' => array(),
                            ));

        }
    }

    function updateAddress($addr) {
        $addr->validate();

        return $this->_request(array(
                             'type' => 'PUT',
                             'method' => 'u',
                             'url_params' => array(
                                 $this->_email,
                                 'addrs',
                                 $addr->nick
                             ),
                             'data_params' => array(
                                 'addr' => $addr->street,
                                 'addr2' => $addr->street2,
                                 'city' => $addr->city,
                                 'state' => $addr->state,
                                 'zip' => $addr->zip,
                                 'phone' => $addr->phone,
                             )
                        ));
    }

    function deleteAddress($addrNick) {
        return $this->_request(array(
                             'type' => 'DELETE',
                             'method' => 'u',
                             'url_params' => array(
                                 'email',
                                 'addrs',
                                 $addrNick
                             ),
                             'data_params' => array()
                        ));
    }

    function getCard($cardNick = '') {
        if (!empty($cardNick)) {
            return $this->_request(array(
                                 'type' => 'GET',
                                 'method' => 'u',
                                 'url_params' => array(
                                     $this->_email,
                                     "ccs",
                                     $cardNick
                                 ),
                                 'data_params' => array()
                            ));
        } else {
            return $this->_request(array(
                                 'type' => 'GET',
                                 'method' => 'u',
                                 'url_params' => array(
                                     $this->_email,
                                     "ccs",
                                 ),
                                 'data_params' => array()
                            ));
        }
    }

    function updateCard($cardNick, $name, $number, $cvv, $expiryMonth, $expiryYear, $addr) {
        $addr->validate();

        return $this->_request(array(
                             'type' => 'PUT',
                             'method' => 'u',
                             'url_params' => array(
                                 $this->_email,
                                 'ccs',
                                 $cardNick,
                             ),
                             'data_params' => array(
                                 'name' => $name,
                                 'number' => $number,
                                 'cvc' => $cvv,
                                 'expiry_month' => $expiryMonth,
                                 'expiry_year' => $expiryYear,
                                 'bill_addr' => $addr->street,
                                 'bill_addr2' => $addr->street2,
                                 'bill_city' => $addr->city,
                                 'bill_state' => $addr->state,
                                 'bill_zip' => $addr->zip,
                             ),
                        ));
    }

    function deleteCard($cardNick) {
        return $this->_request(array(
                            'type' => 'DELETE',
                            'method' => 'u',
                            'url_params' => array(
                                $this->_email,
                                'ccs',
                                $cardNick
                            ),
                            'data_params' => array(),
                        ));
    }

    function orderHistory($orderID='') {
        if (!empty($orderID)) return $this->_request(array(
                                                   'type' => 'GET',
                                                   'method' => 'u',
                                                   'url_params' => array(
                                                       $this->_email,
                                                       'order',
                                                       $orderID
                                                   ),
                                                   'data_params' => array()
                                              ));
        else return $this->_request(array(
                                  'type' => 'GET',
                                  'method' => 'u',
                                  'url_params' => array(
                                      $this->_email,
                                      'orders' => true
                                  ),
                                  'data_params' => array()
                             ));
    }

    function updatePassword($password) {
        return $this->_request(array(
                             'type' => 'PUT',
                             'method' => 'u',
                             'url_params' => array(
                                 $this->_email,
                                 'password'
                             ),
                             'data_params' => array(
                                 'password' => hash('sha256', $password)
                             )
                        ));
    }
}

