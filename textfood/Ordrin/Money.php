<?php

/* Money Class */
class Money extends Ordrin {
    private $amount = 0;

    function __construct($amt) {
//        echo "DEBUG: in constructor\n";
        if (!is_numeric($amt)) {
//            echo "DEBUG: not numeric\n";
            parent::$_errors[]= "Money - Validation - must be a number, we got ($amt)";
        } else {
            $this->amount = $amt;
        }
    }

    function _convertForAPI() {
        return $this->amount;
    }
}

