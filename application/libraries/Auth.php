<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Auth {

    protected $CI;
    
    public function __construct() {
        $this->CI = & get_instance();
        $this->CI->load->library('jwt');
        $this->CI->load->model('UserModel');
    }

    public function generateToken($deviceID, $nik, $env, $typeID) {
        /***************************************************************************************************
            * 1. Setup time, secret key and array data
            * 2. Encode token (jwt)
        ****************************************************************************************************/             
        // $issuedAt = time();
        $secretKey = 's0g00d';

        $token = array(
            "nik" => $nik,
            "deviceID" => $deviceID,
            "env" => $env,
            "typeID" => $typeID
			
        );
        return $this->CI->jwt->encode($token, $secretKey);
    }

    public function validationToken() {
        /***************************************************************************************************
            * 1. Get Request header
            * 2. Split auth from Bearer
            * 3. Decode token (jwt)
        ****************************************************************************************************/            
        $headers = apache_request_headers();
        $secretKey = 's0g00d';
        if (isset($headers["Authorization"])) {
            $bearerAuth = $headers["Authorization"];
            $splitAuth = explode("Bearer ", $bearerAuth);
            $token = $splitAuth[1];
            $resToken = $this->CI->jwt->decode($token, $secretKey);
            return $resToken;
        } else if (isset($headers["authorization"])) {
            $bearerAuth = $headers["authorization"];
            $splitAuth = explode("Bearer ", $bearerAuth);
            $token = $splitAuth[1];
            $resToken = $this->CI->jwt->decode($token, $secretKey);
            return $resToken;
        }else {
            return false;
        }
    }

    //validation userimei
    public function validationLogin($api, $sid, $imei) {
        /***************************************************************************************************
            * 1. Check user exists
            * 2. Check user active
            * 3. Check imei1, imei2, imei3
        ****************************************************************************************************/        
        $status = 1; $message = ''; $data = null;
        $sm = $this->CI->UserModel->getUserById($sid);
        if ($sm) {
            if ($sm->ISACTIVE == 'Y') {
                if ($imei == $sm->IMEI1 || $imei == $sm->IMEI2 || $imei == $sm->IMEI3) {
                    $data = $sm;
                } else {
                    $status = 0;
                    $message =  'Sales : '. $sid . ' dengan imei : '. $imei .' tidak terdaftar di system ';
                }
            } else {
                $status = 0;
                $message = 'Kode sales ' . $sid. ' tidak aktif';
            }
        } else {
            $status = 0;
            $message = 'Kode sales '.$sid.' tidak ditemukan';
        }
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }

    //validationUser
    public function validationActivity($api, $nik, $jwtenv) {
        /***************************************************************************************************
            * 1. Check user exists
            * 2. Check user active
            * 3. Check environment
            * 4. Check login date
        ****************************************************************************************************/        
        $status = 1; $message = ''; $data = null;
        $sm = $this->CI->UserModel->getUserById($nik);
        if ($sm) {
             if ($jwtenv != $sm->SERVER_ENVIRONMENT) {
                 $status = 401; $message = 'Env berubah';
             } else {

             }
        } else {
            $status = 401; $message = 'ID ' . $nik . 'tidak aktif';
        }
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }

    public function checkToken() {
        $status = 1; $message = ''; $data = new stdClass();
        $api = '';
        $jwt = $this->validationToken();
        if ($jwt) {
            if ($jwt->STATUS == 1) {
                $act = $this->validationActivity($api, $jwt->DATA->nik, $jwt->DATA->env);
                if ($act->STATUS == 1) {
                    $status = 1; $message = $act->MESSAGE; $data = $jwt->DATA;
                } else {
                    $status = 401; $message = $act->MESSAGE;
                }
            } else {
                $status = 401; $message = $jwt->MESSAGE;
            }
        } else {
            $status = 401; $message = 'Token not provided';
        }

        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }

    
}