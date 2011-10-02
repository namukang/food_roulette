<?php

/* Order API */
class Order extends Ordrin {
    function __construct() {
        // Placeholder
    }

    function submit($rID, $tray, $tip, $dT, $em, $fName, $lName, $addr, $card_name, $card_number, $card_cvc, $card_expiry, $ccAddr) {
        $addr->validate();
        $ccAddr->validate();

        // Validations
        if (!is_numeric($rID)) {
            parent::$_errors[] = 'Order submit - validation - restaurant ID (invalid, must be numeric) ' . "($rID)";
        }

        if (!preg_match($this->_cc_re, $card_number)) {
            parent::$_errors[] = 'Order submit - validation - credit card number (invalid) ' . "($card_number)";
        }

        if (!is_numeric($card_cvc)) {
            parent::$_errors[] = 'Order submit - validation - credit card security code (invalid, must be numeric) ' . "($card_cvc)";
        }

        if (filter_var($em, FILTER_VALIDATE_EMAIL) === false) {
            parent::$_errors[] = 'Order submit - validation - email (invalid) ' . "($em)";
        }

        // Validations are done

        if ($dT->_asap) {
            $date = "ASAP";
            $time = "";
        } else {
            $date = $dT->_strAPI('month') . '-' . $dT->_strAPI('day');
            $time = $dT->_strAPI('hour') . ':' . $dT->_strAPI('minute');
        }

        return $this->_request(array(
                             'type' => 'POST',
                             'method' => 'o',
                            'url_params' => array(
                                $rID,

                            ),
                            'data_params' => array(
                                'tray' => $tray,
                                'tip' => $tip->_convertForAPI(),
                                'delivery_date' => $date,
                                'delivery_time' => $time,
                                'first_name' => $fName,
                                'last_name' => $lName,
                                'addr' => $addr->street,
                                'city' => $addr->city,
                                'state' => $addr->state,
                                'zip' => $addr->zip,
                                'phone' => $addr->phone,
                                'em' => $em,
                                'card_name' => $card_name,
                                'card_number' => $card_number,
                                'card_expiry' => $card_expiry,
                                'card_cvc' => $card_cvc,
                                'card_bill_addr' => $ccAddr->street,
                                'card_bill_addr2' => $ccAddr->street2,
                                'card_bill_city' => $ccAddr->city,
                                'card_bill_state' => $ccAddr->state,
                                'card_bill_zip' => $ccAddr->zip,
                                'type' => 'RES'
                            )
                        ));
    }
}
