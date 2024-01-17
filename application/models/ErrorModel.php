<?php
defined('BASEPATH') or exit('No direct script access allowed');
class ErrorModel extends CI_Model {

    public function __construct() {
        parent:: __construct();
    }

    public function insertError($id, $msg, $api, $user) {
        $this->db->query("INSERT INTO SOA_API_LOG(ID, MSG, API, CREATEDBY) 
                VALUES('$id','$msg', '$api', '$user')");
        return;
    }

}