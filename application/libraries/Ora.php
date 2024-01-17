<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ora {

    protected $CI;

    public function __construct() {
        $this->CI = & get_instance();
    }
    
    public function isConstraint($msg) {
        $substrErr = substr($msg, 0, 9);
        if ($substrErr == 'ORA-00001') {
            return true;
        } else {
            return false;
        }
    }

    public function isValueTooLarge($msg) {
        $substrErr = substr($msg, 0, 9);
        if ($substrErr == 'ORA-12899') {
            return true;
        } else {
            return false;
        }
    }
    
}