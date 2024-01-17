<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rest {

    protected $CI;

    public function __construct() {
        $this->CI = & get_instance();
    }

    public function input($input) {
        $body = json_decode(file_get_contents('php://input'), true);
        return $body[$input];
    }

    public function output($status, $message, $data) {
        $body = get_instance();
        $body->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => $status,
                    'message' => $message,
                    'data' => $data
        )));
    }

}
