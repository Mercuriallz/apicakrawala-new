<?php
defined('BASEPATH') or exit('No direct script access allowed');
class UserModel extends CI_Model {

    protected $CI;

    public function __construct() {
        parent:: __construct();
        $this->CI = & get_instance();
        $this->CI->load->model('ErrorModel');
        $this->CI->load->library('ora');
    }

    public function getSpvBySalesOffice($salesofficeid) {
        $q = $this->db->query("SELECT sid
            FROM soa_salesman WHERE soffice = '$salesofficeid' AND isactive = 'Y' AND stype = 'SAS'
        ");
        $rows = $q->row();
        $q->free_result();
        return $rows;
    }
 
    public function getUserById($nik) {
        $q = $this->db->query("SELECT EMPLOYEE_ID, IS_ACTIVE, SERVER_ENVIRONMENT, DEVICE1, DEVICE2
            FROM ATD_MT_USERMOBILE WHERE EMPLOYEE_ID = '$nik'
        ");
        $rows = $q->row();
        $q->free_result();
        return $rows;
    }

    public function insertPresence($api, $sid, $presencedt, $logindt, $logoutdt, $statsid, $lat, $lng) {
        $status = 1; $message = ''; $data = new stdClass();
        $errLogin = null; $errLogout = null;
        if ($statsid == 1) { //Login
            $stmt = oci_parse($this->db->conn_id, "INSERT INTO soa_presence(sid, logindt, presencedt, loginlat, loginlng)
                VALUES(:pi_sid, systimestamp, to_date(:pi_presencedt, 'DD-MM-YYYY'), :pi_lat, :pi_lng)
            ");
            oci_bind_by_name($stmt, ':pi_sid', $sid, 8 ,SQLT_CHR);
            oci_bind_by_name($stmt, ':pi_presencedt', $presencedt, 10,SQLT_CHR);
            oci_bind_by_name($stmt, ':pi_lat', $lat, 18,SQLT_LNG);
            oci_bind_by_name($stmt, ':pi_lng', $lng, 18,SQLT_LNG);
            $r = oci_execute($stmt, OCI_NO_AUTO_COMMIT);
            if (!$r) {
                $err = oci_error($stmt);
                if ($err["code"] != '') {
                    $isConstraint = $this->CI->ora->isConstraint($err['message']);
                    if (!$isConstraint) {
                        $errLogin = $err["message"];
                        $status = 0; $message = 'Login Gagal-> '. $err["message"];
                        $this->CI->ErrorModel->insertError('UM1',  $err["message"], $api, $sid);
                    }
                }
            }
            if (is_null($errLogout)) {
                oci_commit($this->db->conn_id);
            } else {
                oci_rollback($this->db->conn_id);
            }
        } else { //Logout
            $stmt = oci_parse($this->db->conn_id, "UPDATE soa_presence SET logoutdt = systimestamp, logoutlat = :pi_lat, logoutlng = :pi_lng
                WHERE sid = :pi_sid AND presencedt = to_date(:pi_presencedt, 'DD-MM-YYYY')
            ");
            oci_bind_by_name($stmt, ':pi_sid', $sid, 8 ,SQLT_CHR);
            oci_bind_by_name($stmt, ':pi_presencedt', $presencedt, 10,SQLT_CHR);
            oci_bind_by_name($stmt, ':pi_lat', $lat, 18,SQLT_LNG);
            oci_bind_by_name($stmt, ':pi_lng', $lng, 18,SQLT_LNG);
            $r = oci_execute($stmt, OCI_NO_AUTO_COMMIT);
            if (!$r) {
                $err = oci_error($stmt);
                if ($err["code"] != '') {
                    $errLogout = $err["message"];
                    $status = 0; $message = 'Logout Gagal-> '. $err["message"];
                    $this->CI->ErrorModel->insertError('UM1',  $err["message"], $api, $sid);
                }
            }
            if (is_null($errLogout)) {
                oci_commit($this->db->conn_id);
            } else {
                oci_rollback($this->db->conn_id);
            }
        }

        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }

    public function updateRegsID($api, $sid, $regsid) {
        $status = 1; $message = 'Sukses update regsid'; $data = new stdClass();
        $errRegsid = null;
        $stmt = oci_parse($this->db->conn_id, "UPDATE soa_salesman set regsid = :pi_regsid where sid = :pi_sid");
        oci_bind_by_name($stmt, ':pi_sid', $sid, 8 ,SQLT_CHR);
        oci_bind_by_name($stmt, ':pi_regsid', $regsid, 152 ,SQLT_CHR);
        $r = oci_execute($stmt, OCI_NO_AUTO_COMMIT);
        if (!$r) {
            $err = oci_error($stmt);
            if ($err["code"] != '') {
                $errRegsid = $err["message"];
                $status = 0; $message = 'Gagal Update Regsid -> '. $err["message"];
                $this->CI->ErrorModel->insertError('UM1',  $err["message"], $api, $sid);
            }
        }
        if (is_null($errRegsid)) {
            oci_commit($this->db->conn_id);
        } else {
            oci_rollback($this->db->conn_id);
        }
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message, 
            'DATA' => $data
        ];
    }


    public function distance($lat1, $lon1, $lat2, $lon2) {

    $theta = $lon1 - $lon2;
    $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
    $miles = acos($miles);
    $miles = rad2deg($miles);
    $miles = $miles * 60 * 1.1515;
    $feet  = $miles * 5280;
    $yards = $feet / 3;
    $kilometers = $miles * 1.609344;
    $meters = $kilometers * 1000;
    $message = compact('kilometers','meters');


        return (object) [
            'STATUS' => 1,
            'MESSAGE' => $meters
        ];

    }
    public function distances($lat1, $lon1, $lat2, $lon2) {

    $theta = $lon1 - $lon2;
    $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
    $miles = acos($miles);
    $miles = rad2deg($miles);
    $miles = $miles * 60 * 1.1515;
    $feet  = $miles * 5280;
    $yards = $feet / 3;
    $kilometers = $miles * 1.609344;
    $meters = $kilometers * 1000;
    // $message = compact('kilometers','meters');


        return $meters;

    }

}