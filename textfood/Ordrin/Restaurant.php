<?php

/* Restuarant API */
class Restaurant extends Ordrin {

    function __construct() {
        // reserved
    }

    function deliveryList($dt, $addr) {
        $addr->validate();

        return $this->_request(array(
                             'type' => 'GET',
                             'method' => 'dl',
                             'url_params' => array(
                                 $dt->_convertForAPI(),
                                 $addr->zip,
                                 $addr->city,
                                 $addr->street

                             ),
                             'data_params' => array()
                        ));
    }

    function deliveryCheck($rID, $dT, $addr) {
        if (!is_numeric($rID)) {
            parent::$_errors[] = "Restaurant DeliveryCheck - Validation - restaurant ID (invalid, must be numeric) we got ($rID)";
        }

        $addr->validate();
        return $this->_request(array(
                             'type' => 'GET',
                             'method' => 'dc',
                             'url_params' => array(
                                 $rID, $dT->_convertForAPI(),
                                 $addr->zip,
                                 $addr->city,
                                 $addr->street
                             ),
                            'data_params' => array()
                        ));
    }

    function deliveryFee($rID, $subtotal, $tip, $dT, $addr) {
        if (!is_numeric($rID)) {
            parent::$_errors[] = "Restaurant DeliveryCheck - Validation - restaurant ID (invalid, must be numeric) we got ($rID)";
        }

        $addr->validate();
        return $this->_request(array(
                             'type' => 'GET',
                             'method' => 'fee',
                            'url_params' => array(
                                $rID,
                                $subtotal->_convertForAPI(),
                                $tip->_convertForAPI(),
                                $dT->_convertForAPI(),
                                $addr->zip,
                                $addr->city,
                                $addr->street
                            ),
                            'data_params' => array()
                        ));
    }

    function details($rID) {
        if (!is_numeric($rID)) {
            parent::$_errors[] = "Restaurant DeliveryCheck - Validation - restaurant ID (invalid, must be numeric) we got ($rID)";
        }

        return $this->_request(array(
                             'type' => 'GET',
                             'method' => 'rd',
                             'url_params' => array($rID),
                             'data_params' => array()
                        ));
    }
}
