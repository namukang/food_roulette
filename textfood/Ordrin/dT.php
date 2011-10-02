<?php

/* Date and Time Class */

class dT extends Ordrin {
    private
        $_date;

    function __construct($date) {
        if (!$date) {
            $this->_date = getdate();
        } else {
            $this->_date = $date; 
        }
    }

    function asap () {
        $this->_asap = true;
    }

    function _strAPI($element) {
        switch ($element) {
            case 'month':
                if ($this->_date['mon'] < 10) return "0" . $this->_date['mon'];
                else return $this->_date['mon'];
                break;
            case 'day':
                if ($this->_date['mday'] < 10) return "0" . $this->_date['mday'];
                else return $this->_date['mday'];
                break;
            case 'hour':
                if ($this->_date['hours'] < 10) return "0" . $this->_date['hours'];
                else return $this->_date['hours'];
                break;
            case 'minute':
                if ($this->_date['minutes'] < 10) return "0" . $this->_date['minutes'];
                else return $this->_date['minutes'];
                break;
            default:
                return 0;
        }

        return 0; // Secondary Default
    }

    function _convertForAPI() {
        if ($this->_asap) return "ASAP";

        return $this->_date['mon'] . "-" . $this->_date['mday'] . "+" . $this->_date['hours'] . ':' . $this->_date['minutes'];
    }

    function _asap() {
//        echo "DEBUG :: Returning asap as: " . $this->_asap;
        return $this->_asap;
    }
}