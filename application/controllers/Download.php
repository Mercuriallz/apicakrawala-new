<?php
class Download extends CI_Controller {

    

    protected $CI;

    public function __construct() {
        parent:: __construct();
        $this->CI = & get_instance();
        $this->CI->load->library('rest');
        $this->CI->load->library('auth');
        $this->CI->load->helper('shared');
        $this->CI->load->model('DownloadModel');
    }


    public function reason() {
        $api = "apicakrawala/download/reason";
        $jwt = $this->CI->auth->checkToken();
        if ($jwt->STATUS == 1) {
            $getReason = $this->CI->DownloadModel->getReason();
            $this->CI->rest->output($getReason->STATUS, $getReason->MESSAGE, $getReason->DATA);
        } else {
            $this->CI->rest->output($jwt->STATUS, $jwt->MESSAGE, $jwt->DATA);
        }
    }
    

    public function villagelist() {
        $apiName = "apicakrawala/download/villagelist";
        $apiName = "apicakrawala/download/districtlist";
        $areaid      = $this->CI->rest->input('areaid');
        $areaid2      = $this->CI->rest->input('areaid2');
        $areaid3      = $this->CI->rest->input('areaid3');
        $isareaid   = $this->isNullField($areaid);
        $isareaid2   = $this->isNullField($areaid2);
        $isareaid3   = $this->isNullField($areaid3);

        if ($isareaid) {
            restOutput(0, "Field nya ada yang kosong ". "Area ID : " .$areaid, new stdClass());
        } else {
        $getVillages = $this->CI->DownloadModel->getVillages($areaid, $areaid2, $areaid3);
        $this->CI->rest->output($getVillages->STATUS, $getVillages->MEESSAGE, $getVillages->DATA);
        }


    }

    public function stockList() {
        $customer_id = $this->CI->rest->input('customer_id');
        $is_customer_id   = $this->isNullField($customer_id);

        if ($is_customer_id) {
            restOutput(0, "Customer ID nya kosong " , new stdClass());
        } else {
            $getStocks = $this->CI->DownloadModel->getStockQuery($customer_id);
            $this->CI->rest->output($getStocks->STATUS, $getStocks ->MESSAGE, $getStocks->DATA);
        }
    }

    public function callPlanList() {
        $employee_id = $this->CI->rest->input('employee_id');
        $is_employee_id = $this->isNullField($employee_id);
        $date_callplan = $this->CI->rest->input('date_callplan');
        $is_date_callplan = $this->isNullField($date_callplan);

        if ($is_employee_id && $is_date_callplan) {
            restOutput(0, "customer ID atau Employee ID kosong ", new stdClass());
        } else {
            $getCallPlan = $this->CI->DownloadModel->getCallPlan($employee_id, $date_callplan);
            $this->CI->rest->output($getCallPlan->STATUS, $getCallPlan -> MESSAGE, $getCallPlan->DATA);
        }
    }

    public function skupro() {
        $apiName = "apicakrawala/download/skupro";

        $projectid          = $this->CI->rest->input('projectid');
        $subprojectid       = $this->CI->rest->input('subprojectid');
        $isprojectid        = $this->isNullField($projectid);
        $issubprojectid     = $this->isNullField($subprojectid);
        
        $getProvincy = $this->CI->DownloadModel->getSKUPro();
        $this->CI->rest->output($getProvincy->STATUS, $getProvincy->MESSAGE, $getProvincy->DATA);
 }

    public function priceTagList() {
        $customer_id = $this->CI->rest->input('customer_id');
        $is_customer_id = $this->isNullField($customer_id);
        $employee_id = $this->CI->rest->input('employee_id');
        $is_employee_id = $this->isNullField($employee_id);

        if ($is_customer_id && $is_employee_id) {
            restOutput(0, "Customer ID anda kosong atau employee id anda kosong" , new stdClass());
        } else {
            $getPriceTag = $this->CI->DownloadModel->getPriceTag($customer_id, $employee_id);
            $this->CI->rest->output($getPriceTag->STATUS, $getPriceTag -> MESSAGE, $getPriceTag->DATA);
        }
    }

    public function districtlist() {
        $apiName = "apicakrawala/download/districtlist";
        $areaid      = $this->CI->rest->input('areaid');
        $areaid2      = $this->CI->rest->input('areaid2');
        $areaid3      = $this->CI->rest->input('areaid3');
        $isareaid   = $this->isNullField($areaid);
        $isareaid2   = $this->isNullField($areaid2);
        $isareaid3   = $this->isNullField($areaid3);

        if ($isareaid) {
            restOutput(0, "Field nya ada yang kosong ". "Area ID : " .$areaid, new stdClass());
        } else {
        $getDistrict = $this->CI->DownloadModel->getDistrict($areaid, $areaid2, $areaid3);
        $this->CI->rest->output($getDistrict->STATUS, $getDistrict->MESSAGE, $getDistrict->DATA);
        }


    }


    public function regencylist() {
        $apiName = "apicakrawala/download/regencylist";
        $areaid      = $this->CI->rest->input('areaid');
        $areaid2      = $this->CI->rest->input('areaid2');
        $areaid3      = $this->CI->rest->input('areaid3');
        $isareaid   = $this->isNullField($areaid);
        $isareaid2   = $this->isNullField($areaid2);
        $isareaid3   = $this->isNullField($areaid3);

        if ($isareaid) {
            restOutput(0, "Field nya ada yang kosong ". "Aarea Utama : " .$isareaid, new stdClass());
        } else {
            $getRegency = $this->CI->DownloadModel->getRegency($areaid, $areaid2, $areaid3);
            $this->CI->rest->output($getRegency->STATUS, $getRegency->MESSAGE, $getRegency->DATA);
        }
    }

    public function skulist() {
        $apiName = "apicakrawala/download/skulist";
        // $projectid          = $this->CI->rest->input('projectid');
        // $subprojectid       = $this->CI->rest->input('subprojectid');
        // $isprojectid        = $this->isNullField($projectid);
        // $issubprojectid     = $this->isNullField($subprojectid);

        // if ($isprojectid) {
        //     restOutput(0, "Field nya ada yang kosong ". "Project ID : " .$isprojectid, new stdClass());
        // } else {

            $getProvincy = $this->CI->DownloadModel->getSKU();
            $this->CI->rest->output($getProvincy->STATUS, $getProvincy->MESSAGE, $getProvincy->DATA);

            // $getSKU = $this->CI->DownloadModel->getSKU($projectid, $subprojectid);
            // $this->CI->rest->output($getSKU->STATUS, $getSKU->MESSAGE, $getSKU->DATA);
        // }
    }

    public function historycio() {
        $apiName = "apicakrawala/download/historycio";
        $nip      = $this->CI->rest->input('nip');
        $isnip   = $this->isNullField($nip);

        if ($isnip) {
            restOutput(0, "Permintaan History NIP Kosong ". "NIP : " .$isnip, new stdClass());
        } else {
            // $getRegency = $this->CI->DownloadModel->getRegency($areaid, $areaid2, $areaid3);
            $getHistoryCio = $this->CI->DownloadModel->getHistoryCio($nip);
            $this->CI->rest->output($getHistoryCio->STATUS, $getHistoryCio->MESSAGE, $getHistoryCio->DATA);
        }
    }


    public function provincylist() {
        $apiName = "apicakrawala/download/provincylist";

        $getProvincy = $this->CI->DownloadModel->getProvincy();
        $this->CI->rest->output($getProvincy->STATUS, $getProvincy->MESSAGE, $getProvincy->DATA);

    }

    public function sku() {
        $apiName = "apicakrawala/download/sku";

        $projectid          = $this->CI->rest->input('projectid');
        $subprojectid       = $this->CI->rest->input('subprojectid');
        $isprojectid        = $this->isNullField($projectid);
        $issubprojectid     = $this->isNullField($subprojectid);
        
        $getProvincy = $this->CI->DownloadModel->getSKU();
        $this->CI->rest->output($getProvincy->STATUS, $getProvincy->MESSAGE, $getProvincy->DATA);

    }

    public function cityzen(){
    
        $myObj = new stdClass();
        $myObj->name = "John";
        $myObj->age = 30;
        $myObj->city = "New York";

        $myJSON = json_encode($myObj);

        echo $myJSON;

    }


    private function isNullField($data) {
        if (is_null($data) || trim($data) == '') {
            return true;
        } else {
            return false;
        }
    }

}
