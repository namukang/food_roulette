<?php
/**
 * Ordr.In PHP Wrapper for the API
 * This is a self-encompasing
 */

/* This is the lazy loading portion */
function __autoload($name) {
    require_once($name . '.php');
}

class Ordrin {
    private
        $_staticVars = array('_email','_password', '_key', '_url'),
        $_api_data;

    protected
        $_cc_re = "/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11})$/";

    static
        $_email,
        $_password,
        $_url,
        $_key,
        $_errors;

    function __construct($key, $url) {
        $this->_url = $url;
        $this->_key = $key;
    }

    function setCurrAcct($email, $pass) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            // Not Found
            self::$_errors[] = "Ordrin.php setCurrAcct - validation - email invalid ($email)";
        } else {
            $this->_email = $email;
            $this->_password = $pass;
        }
    }

    
    /** -- Magic Functions -- **/
    function __set($name, $value) {

        if (in_array($name, $this->_staticVars)) {
            switch ($name) {
                case '_email':
                    self::$_email = $value;
                    break;
                case '_password':
                    self::$_password = $value;
                    break;
                case '_url':
                    self::$_url = $value;
                    break;
                case '_key':
                    self::$_key = $value;
                    break;
            }
        } else
            $this->_api_data[$name] = $value;
    }

    function __get($name) {
        if (in_array($name, $this->_staticVars)) {
            switch ($name) {
                case '_email':
                    return self::$_email;
                    break;
                case '_password':
                    return self::$_password;
                    break;
                case '_url':
                    return self::$_url;
                    break;
                case '_key':
                    return self::$_key;
                    break;
            }
        } else
            return $this->_api_data[$name];
    }


    /* Protected Functions */
    protected  function _request($data) {
        /*
        echo "DEBUG :: Request started.. \n";
        echo "---------------------------------------------\n";
        echo print_r($data,true);
        echo "---------------------------------------------\n";
        */

        if (!$this->_key) self::$_errors[] = 'initialization - must initialize with developer key for api';
        elseif (!$this->_url) self::$_errors[] = 'initialization - must initialize with site at which API is running';

        //Grab Method and Type
        $type = $data['type'];
        $method = $data['method'];
        $headers = array();
        
        $headers[] = 'X-NAAMA-CLIENT-AUTHENTICATION: id="' . $this->_key . '", version="1"';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';

        $_data = '';
        $postData = array();
        
//        foreach ($data['data_params'] as $key => $val) {
//
//            if (!empty($_data)) $_data .= '&';
//            $_data .= $key . '=' . $val;
//            if ($type == "POST") {
//                $postData[$key] = $val;
//            }
//        }
        $_data = http_build_query($data['data_params'], '', '&');
        $headers[] = 'Content-length: ' . strlen($_data);

        $origMethod = $method;
        if ($method == 'uN') $method = 'u';
        $url_params = '/' . $method . '/' . join('/', $data['url_params']);

        if ($origMethod == 'u') {
            if (!self::$_email || !self::$_password) {
                self::$_errors[] = 'user API - valid email and password required to access user API';
            }

            // Add header for authentication
            $headers[] = 'X-NAAMA-AUTHENTICATION: username="' . $this->_email . '", response="' . hash('sha256', $this->_password . $this->_email . $url_params) . '", version="1"';
        }

        unset($origMethod);
        
        /*
        echo "URL Append: " . $url_params . "\n";
        echo "DATA: $_data\n";

        echo "DO: " . $this->_url . $url_params . "\n";

        echo "\n\n";
        
        echo 'Headers: ' . print_r($headers,true);
        */
        
        if (!empty(self::$_errors)) {
            throw new Exception('Errors encountered: ' . implode('\n', self::$_errors));
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_URL, $this->_url . $url_params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        // For Debug
        curl_setopt($ch, CURLOPT_VERBOSE, 1);


        if ($type == 'GET') {
            $respBody = curl_exec($ch);
            $respInfo = curl_getinfo($ch);
        }

        if ($type == 'PUT') {
            $reqLen = strlen($_data);
            $fh = fopen('php://memory', 'rw');
            fwrite($fh, $_data);
            rewind($fh);

            curl_setopt($ch, CURLOPT_INFILE, $fh);
            curl_setopt($ch, CURLOPT_INFILESIZE, $reqLen);
            curl_setopt($ch, CURLOPT_PUT, true);

            $respBody = curl_exec($ch);
            $respInfo = curl_getinfo($ch);
        }

        if ($type == "POST") {
            $_data = http_build_query($postData, '', '&');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $_data);
            curl_setopt($ch, CURLOPT_POST, 1);

            $respBody = curl_exec($ch);
            $respInfo = curl_getinfo($ch);
        }

        if ($type == "DELETE") {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

            $respBody = curl_exec($ch);
            $respInfo = curl_getinfo($ch);

        }
        
        curl_close($ch);
        return json_decode($respBody);
        
        // echo "\nRespInfo : " . print_r($respInfo,true);
        
    }


    function _headerFunc($ch, $header) {
        echo "HEADER: " . $header;
    }
    /* Private Functions */
}





