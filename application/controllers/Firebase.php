<?php
define('API_ACCESS_KEY', 'AAAAYe_VCVw:APA91bEehIszT6FTqjLlMqrU0056Uner131ObjdtiLsivafk-OfcHPiKbr91BI4f6fhbxXt--p08imloa2VNHgzsArskMtldJMDzTISsf-r0mOsi0MFdYVDmLl850wqGN5zijJ3_D0W_');

class Firebase extends CI_Controller {

    protected $CI;

    public function __construct() {
        parent:: __construct();
        $this->CI = & get_instance();
    }

    public function send_message() {
        $regIds = array();
        
        array_push($regIds, 'frRj0bVxg2w:APA91bE0T2TWvpVpWuiZPcaLjvHmpLHKwBd3FQWbyEAK52cy6YekRA8w1ypvaSHRndoi6NzLjMzPqzuG_3TMNOQ9rfOP7BVzVKZ8zc4y9lPkgeq_LG-G7KqVo8ZPBTq_gdhjkUdnxJdB');
        $name = 'Coeg Name';
        

        $msg = array
            (
            "body" => $name . " meminta persetujuan untuk tukar guling",
            "title" => "Permintaan Untuk Tukar Guling"
        );
        $data = array
            (
            "click_action" => '0122',
            'anything_else' => '01'
        );
        $fields = array
            (
            'registration_ids' => $regIds,
            "notification" => $msg,
            'data' => $data
        );
        $headers = array
            (
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_exec($ch);
        curl_close($ch);
        return;
    }
}