<?php

/**
 * Created by : robin.kurniawan
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Sgf {

    protected $CI;

    public function __construct() {
        $this->CI = & get_instance();
        $this->CI->load->library('jwt');
    }

    function generateToken($nik, $device_id, $server_env, $type_id, $salesOffice, $distributor) {

        $issuedAt = time();
        /*
            $notBeforeAt = $issuedAt + 1;
           $expireAt = $notBeforeAt + 360;
        */
        $secretKey = 'sogoodfood2018';
        $token = array(
            "nik" => $nik,
            "device_id" => $deviceid,
            "server_env" => $server_env,
            "type_id" => $type_id,
            "sOffice" => $salesOffice,
            "distributor" => $distributor,
            "iat" => $issuedAt, //When token was generated
            "nbf" => null, //$notBeforeAt, //When token is active after generated
            "exp" => null//$expireAt              //When token being expired after generated
        );
        return $this->CI->jwt->encode($token, $secretKey);
    }

    function validationToken() {
        $headers = apache_request_headers();
        $secretKey = 'sogoodfood2018';

        if (isset($headers["Authorization"])) {
            $bearerAuth = $headers["Authorization"];
            $splitAuth = explode("Bearer ", $bearerAuth);
            $token = $splitAuth[1];
            $resToken = $this->CI->jwt->decode($token, $secretKey);
            return $resToken;
        }else if (isset($headers["authorization"])) {
            $bearerAuth = $headers["authorization"];
            $splitAuth = explode("Bearer ", $bearerAuth);
            $token = $splitAuth[1];
            $resToken = $this->CI->jwt->decode($token, $secretKey);
            return $resToken;
        } else {
            return arrResponse(401, "Not authorized", new stdClass());
        }
    }

    /**
     * https://www.apptha.com/blog/how-to-reduce-image-file-size-while-uploading-using-php-code/
     */
    function compress($source, $destination, $quality) {

        $info = getimagesize($source);

        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($source);
        } elseif ($info['mime'] == 'image/gif') {
            $image = imagecreatefromgif($source);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($source);
        }
        imagejpeg($image, $destination, $quality);

        return $destination;
    }

}
