<?php
// define('API_ACCESS_KEY', 'AAAAYe_VCVw:APA91bEehIszT6FTqjLlMqrU0056Uner131ObjdtiLsivafk-OfcHPiKbr91BI4f6fhbxXt--p08imloa2VNHgzsArskMtldJMDzTISsf-r0mOsi0MFdYVDmLl850wqGN5zijJ3_D0W_');
define('API_ACCESS_KEY', 'AAAAF7ncjrE:APA91bHiRxvBZJjE1dU6r_T3T0WxHTzDykAjjOfivJL_cszbzQdj5fdFW7TN9t-e0LskoFtQS_j2M5T-aWjQUCqPjr4HZtxZQW7lPMsoVS3sjBwj0E_dMnY_IIPiM2g_rRexQXIqDf6W');
class Customer extends CI_Controller {

    protected $CI;

    public function __construct() {
        parent:: __construct();
        $this->CI = & get_instance();
        $this->CI->load->model('UserModel');
        $this->CI->load->model('DownloadModel');
        $this->CI->load->library('rest');
        $this->CI->load->library('auth');
    }

    public function request_new_customer_location(){

        $api = "sgfattend/customer/request_new_customer_location";
        $customerno = $this->CI->rest->input('customerno');
        $lat = $this->CI->rest->input('lat');
        $lng = $this->CI->rest->input('lng');
        $jwt = $this->CI->auth->checkToken();
		
        if ($jwt->STATUS == 1) {
            $newLocationResult = $this->CI->DownloadModel->insertNewCustomerLocation($api,$jwt->DATA->nik,$customerno,$lat,$lng);
            $this->CI->rest->output($newLocationResult->STATUS, $newLocationResult->MESSAGE, new stdClass());
        } else {
            $this->CI->rest->output($jwt->STATUS, $jwt->MESSAGE, $jwt->DATA);
        }
    }


}
