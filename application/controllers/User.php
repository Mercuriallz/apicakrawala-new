<?php

class User extends CI_Controller {

    protected $CI;

    public function __construct() {
        parent:: __construct();
        $this->CI = & get_instance();
        $this->CI->load->model('UserModel');
        $this->CI->load->model('DownloadModel');
        $this->CI->load->helper('shared');
        $this->CI->load->library('rest');
        $this->CI->load->library('auth');
    }
 
    private function getDateOnly($dt) {
        return (string) date('d-m-Y', strtotime($dt));
    }
    
    private function compareDate($phonedt, $serverdt) {
        $timeServer = strtotime($serverdt);
        $dateServer = date('d-m-Y', strtotime($serverdt));
        $strDateServer = (string) $dateServer;
        $newDateServer = date_create($strDateServer);

        $timeInput = strtotime($phonedt);
        $dateInput = date('d-m-Y', $timeInput);
        $strDateInput = (string) $dateInput;
        $newDateInput = date_create($strDateInput);

        $dateDiff = date_diff($newDateServer, $newDateInput);
        if ($dateDiff->d) {
            return true;
        } else {
            return false;
        }
    }

    private function isNullField($data) {
        if (is_null($data) || trim($data) == '') {
            return true;
        } else {
            return false;
        }
    }
	
    public function login() {
        $status = 1; $message = ''; $data = null;
        $apiName = "apicakrawala/user/login";
        $nik 		= $this->CI->rest->input('nik'); 
        $deviceID 	= $this->CI->rest->input('deviceID');
        $logindt 	= $this->CI->rest->input('logindt');
        $apkVersion = $this->CI->rest->input('apk_version');
        $isnik 		= $this->isNullField($nik);
        $isdeviceID = $this->isNullField($deviceID);
        $isApkNull 	= $this->isNullField($apkVersion);
        $presencedt = $this->getDateOnly($logindt);
		
		// $myObj->name = $apiName;
		// $myObj->age = $nik;
		// $myObj->did = $deviceID;
		// $myObj->logindt = $logindt;
		// $myObj->apk = $apkVersion;
		// $myJSON = json_encode($myObj);
		// echo $myJSON;

        if ($isnik || $isdeviceID || $isApkNull) {

		// $myObj->name = $apiName;
		// $myObj->age = $nik;
		// $myObj->did = $deviceID;
		// $myObj->logindt = $logindt;
		// $myObj->apk = $apkVersion;
		// $myJSON = json_encode($myObj);
		// echo $myJSON;

	            // $this->insertError('U000', 'Field null, ' . " NIK : ".$nik ." Device ID : ".$deviceID ." APK : ".$apkVersion, $apiName);
	            restOutput(0, "Field nya ada yang kosong ". "NIK : " .$nik . " Device ID : ".$deviceID ." APK : ".$apkVersion, new stdClass());

	        }else{

	        	if($nik == '000'){

		        	$isNIKExists = $this->isNIKExists($apiName, $nik, '123456789');
					$token = null;

						$data = array(
							"employee_id" => $isNIKExists->EMPLOYEE_ID,
							"fullname" => $isNIKExists->FULLNAME,
							"server_env" => $isNIKExists->SERVER_ENV,
							"type_id" => $isNIKExists->TYPE_ID,
							"project_id" => $isNIKExists->PROJECT_ID,
							"area_id" => $isNIKExists->AREA_ID,
							"area_id_extra1" => $isNIKExists->AREA_ID_EXTRA1,
							"area_id_extra2" => $isNIKExists->AREA_ID_EXTRA2

						);

						restOutput($isNIKExists->STATUS, $isNIKExists->MESSAGE, $data);

		        	} else {

			        	$isNIKExists = $this->isNIKExists($apiName, $nik, $deviceID);
						$token = null;
						if($isNIKExists->STATUS){

							$data = array(
								"employee_id" => $isNIKExists->EMPLOYEE_ID,
								"fullname" => $isNIKExists->FULLNAME,
								"server_env" => $isNIKExists->SERVER_ENV,
								"type_id" => $isNIKExists->TYPE_ID,
								"project_id" => $isNIKExists->PROJECT_ID,
								"area_id" => $isNIKExists->AREA_ID,
								"area_id_extra1" => $isNIKExists->AREA_ID_EXTRA1,
								"area_id_extra2" => $isNIKExists->AREA_ID_EXTRA2

							);

							restOutput($isNIKExists->STATUS, $isNIKExists->MESSAGE, $data);

						} else {

							restOutput($isNIKExists->STATUS, $isNIKExists->MESSAGE, new stdClass());
							// $myObj->namex = $isNIKExists->FULLNAME;
							// $myObj->age = $isNIKExists->STATUS;
							// $myObj->did = $deviceID;
							// $myObj->logindt = $logindt;
							// $myObj->apk = $apkVersion;
							// $myJSON = json_encode($myObj);
							// echo $myJSON;

						}

		        	}

	    		}
    		}



    public function employeebynip() {
        $status = 1; $message = ''; $data = null;
        $apiName = "apicakrawala/user/employeebynip";
        $employee_id 		= $this->CI->rest->input('employee_id'); 
        $isemployee_id		= $this->isNullField($employee_id);


        if ($isemployee_id) {

	            restOutput(0, "Field nya ada yang kosong ". "NIP : " .$employee_id, new stdClass());

	        }else{

	        	// $isOutletExists = $this->isOutletExists($employee_id);
	        	$isEmployeeExists = $this->isEmployeeExists($employee_id);
				// $token = null;
				if($isEmployeeExists->STATUS){

					$data = array(

			            // 'STATUS' 		=> $status,
			            // 'MESSAGE' 		=> $message,

			            'USERID' 		=> $isEmployeeExists->USERID,
			            'EMPLOYEE_ID' 	=> $isEmployeeExists->EMPLOYEE_ID,
			            'FULLNAME' 		=> $isEmployeeExists->FULLNAME,
			            'DOB' 			=> $isEmployeeExists->DOB,
						'GENDER' 		=> $isEmployeeExists->GENDER,
						'MARITAL' 		=> $isEmployeeExists->MARITAL,
						'AGAMA' 		=> $isEmployeeExists->AGAMA,
						'NO_KONTAK' 	=> $isEmployeeExists->NO_KONTAK,
						'EMAIL' 		=> $isEmployeeExists->EMAIL,
						'ALAMAT' 		=> $isEmployeeExists->ALAMAT,
						'KOTA' 			=> $isEmployeeExists->KOTA,
						'NO_KTP' 		=> $isEmployeeExists->NO_KTP,
						'NO_KK' 		=> $isEmployeeExists->NO_KK,
						'NO_BPJSTK' 	=> $isEmployeeExists->NO_BPJSTK,
						'NO_BPJSKS' 	=> $isEmployeeExists->NO_BPJSKS,
						'COMPANY' 		=> $isEmployeeExists->COMPANY,
						'PROJECT' 		=> $isEmployeeExists->PROJECT,
						'PROJECT_SUB' 	=> $isEmployeeExists->PROJECT_SUB,
						'DEPT' 			=> $isEmployeeExists->DEPT,
						'JABATAN' 		=> $isEmployeeExists->JABATAN,
						'EMPLOYEE_STATUS' => $isEmployeeExists->EMPLOYEE_STATUS,
						'IBU_KANDUNG' 	=> $isEmployeeExists->IBU_KANDUNG

					);

					restOutput($isEmployeeExists->STATUS, $isEmployeeExists->MESSAGE, $data);

				} else {

					restOutput($isEmployeeExists->STATUS, $isEmployeeExists->MESSAGE, new stdClass());


				}

	    }
    }

    public function orderresume() {
        $status = 1; $message = ''; $data = null;
        $apiName = "apicakrawala/user/orderresume";
        $employee_id 		= $this->CI->rest->input('employee_id'); 
        $date_now 			= $this->CI->rest->input('date_now'); 
        $isemployee_id		= $this->isNullField($employee_id);


        if ($isemployee_id) {

	            restOutput(0, "Field nya ada yang kosong ". "NIP : " .$employee_id, new stdClass());

	        }else{

	        	// $isOutletExists = $this->isOutletExists($employee_id);
	        	$isOrderResume = $this->isOrderResume($employee_id, $date_now);
				// $token = null;
				if($isOrderResume->STATUS){

					$data = array(

			            'TOTAL_CALL' 		=> $isOrderResume->TOTAL_CALL,
			            'TOTAL_EC' 			=> $isOrderResume->TOTAL_EC,
			            'TOTAL_PRODUCT' 	=> $isOrderResume->TOTAL_PRODUCT,
			            'TOTAL_VALUE' 		=> $isOrderResume->TOTAL_VALUE,
			            'TARGET_CUSTOMER' 	=> $isOrderResume->TARGET_CUSTOMER,
						'TARGET_VALUE' 		=> $isOrderResume->TARGET_VALUE

					);

					restOutput($isOrderResume->STATUS, $isOrderResume->MESSAGE, $data);

				} else {

					restOutput($isOrderResume->STATUS, $isOrderResume->MESSAGE, new stdClass());

				}

	    }
    }


    public function customerbysearch() {

        $apiName = "apicakrawala/user/customerbysearch";

        $keys = $this->CI->rest->input('keys');

        // $getCustomerArea = $this->CI->DownloadModel->getCustomerAreaDist($keys);
        $getCustomerSearch = $this->CI->DownloadModel->getCustomerSearch($keys);
        // $this->CI->rest->output($getCustomerArea->STATUS, $getCustomerArea->MESSAGE, $getCustomerArea->DATA);
        $this->CI->rest->output($getCustomerSearch->STATUS, $getCustomerSearch->MESSAGE, $getCustomerSearch->DATA);

    }


    public function villagebysearch() {

        $apiName = "apicakrawala/user/villagebysearch";

        $keys = $this->CI->rest->input('keys');

        // $getCustomerArea = $this->CI->DownloadModel->getCustomerAreaDist($keys);
        $getVillageSearch = $this->CI->DownloadModel->getVillageSearch($keys);
        // $this->CI->rest->output($getCustomerArea->STATUS, $getCustomerArea->MESSAGE, $getCustomerArea->DATA);
        $this->CI->rest->output($getVillageSearch->STATUS, $getVillageSearch->MESSAGE, $getVillageSearch->DATA);

    }

    public function loginextra() {
        $status = 1; $message = ''; $data = null;
        $apiName = "sgfattend/user/loginextra";
        $nik 		= $this->CI->rest->input('nik'); 
        $deviceID 	= $this->CI->rest->input('deviceID');
        $logindt 	= $this->CI->rest->input('logindt');
        $apkVersion = $this->CI->rest->input('apk_version');
        $isnik 		= $this->isNullField($nik);
        $isdeviceID = $this->isNullField($deviceID);
        $isApkNull 	= $this->isNullField($apkVersion);
		
        $presencedt = $this->getDateOnly($logindt);
		

        if ($isnik || $isdeviceID || $isApkNull) {
			
            $this->ErrorModel->insertError('U000', 'Field null, ' . " NIK : ".$nik ." Device ID : ".$deviceID ." APK : ".$apkVersion, $apiName);
            restOutput(0, "Field nya ada yang kosong ". "NIK : " .$nik . " Device ID : ".$deviceID ." APK : ".$apkVersion, new stdClass());
			
		}else{
			
            $isNIKExists = $this->isNIKExists($apiName, $nik, $deviceID);
			$token = null;
			if ($isNIKExists->STATUS) {
				
                $isDeviceIDExists = $this->isDeviceIDExists($apiName, $isNIKExists->EMPLOYEE_ID, $deviceID, $apkVersion);
				$isDateExists = $this->isDateExistsAttend($isNIKExists->EMPLOYEE_ID, $isNIKExists->OFFICELOC);
				
                if ($isDeviceIDExists->CODE == 200 || $isDeviceIDExists->CODE == 305 || $isDeviceIDExists->CODE == 302) {
                    /**
                     * ALWAYS GENERATE TOKEN WHEN IMEI IS REGISTERED OR IMEI NOT FOUND OR IMEI HAVE BEEN DIFFERENT THAN REGISTERED
                     * CHECK CODE AGAIN ON MOBILE IF CODE = 305 TO REPLACE AND UPDATE NEW IMEI
                     */
                    $token = $this->CI->auth->generateToken($deviceID, $isNIKExists->EMPLOYEE_ID, $isNIKExists->SERVER_ENV, $isNIKExists->TYPE_ID, $isNIKExists->SHIFT_KERJA);
                }
				
				
							$getOffice = $this->CI->DownloadModel->getOffice($nik);
							
                    header("Token: " .$token);
                    $data = array(
                        "code" => $isDeviceIDExists->CODE,
                        "fullname" => $isNIKExists->FULLNAME,
                        "employee_id" => $isNIKExists->EMPLOYEE_ID,
                        "type_id" => $isNIKExists->TYPE_ID,
                        "server_env" => $isNIKExists->SERVER_ENV,
						"office" => $getOffice->DATA,
			
						"shif_kerja" => $isNIKExists->SHIFT_NAME,
						"shift_name" => $isNIKExists->SHIFT_NAME,
						"last_date" => $isDateExists->LASTDATE,
						"status_attend" => $isDateExists->STATUSID
                    );
                    
				
					
                    restOutput($isDeviceIDExists->STATUS, $isDeviceIDExists->MESSAGE, $data);
				
			}else{
				restOutput($isNIKExists->STATUS, $isNIKExists->MESSAGE, new stdClass());
			}
		}
    }
	
    private function isNIKExists($apiName, $nik, $deviceID) {
        $q = $this->db->query("
        	SELECT users.employee_id, emp.first_name, emp.last_name, users.is_active, pos.designation_name AS usertype_id, users.server_inv, users.project_id, users.areaid, users.areaid_extra1, users.areaid_extra2, users.device_id_one, users.device_id_two
			FROM xin_user_mobile users
			LEFT JOIN xin_employees emp ON emp.employee_id = users.employee_id
			LEFT JOIN xin_designations pos ON pos.designation_id = emp.designation_id
			WHERE users.employee_id = '$nik'");

        $status = 1;
        $employeeID = $fullname  = $serverEnv = $message = $typeId = null;

        if ($q->num_rows() == 1) {
            $rows = $q->row();

            $message = "success";
            $serverEnv = $rows->server_inv;
            $isActive = $rows->is_active;
			$employeeID = $rows->employee_id;
            $fullname = $rows->first_name.' '.$rows->last_name;
            $typeId = $rows->usertype_id;
			$projectId = $rows->project_id;
			$areaId = $rows->areaid;
			$deviceid1 = $rows->device_id_one;
			$deviceid2 = $rows->device_id_two;
			$areaId_extra1 = $rows->areaid_extra1;
			$areaId_extra2 = $rows->areaid_extra2;
			// $officeLng = $rows->LNG;
			// $shift = $rows->SHIFT_KERJA;
			// $shiftName = $rows->SHIFT_NAME;
			

            if ($isActive != 1) {
                $status = 0;
                $message = "NIK anda tidak aktif, Hubungi admin untuk informasi lengkapnya.\n\nDevice ID: $deviceID";
				$serverEnv = "";
				$isActive = "";
				$employeeID = "";
				$fullname = "";
				$typeId = "";
				$projectId = "";
				$areaId = "";
				$areaId_extra1 = "";
				$areaId_extra2 = "";
				// $officeLoc = "";
				// $officeLat = "";
				// $officeLng = "";
				// $shift = "";
				// $shiftName = "";
			
            } 

            if($deviceID != $deviceid1 && $deviceID != $deviceid2){

                $status = 0;
                $message = "Device ID anda tidak terdaftar, Hubungi admin untuk informasi lengkapnya.\n\nDevice ID: $deviceID";
				$serverEnv = "";
				$isActive = "";
				$employeeID = "";
				$fullname = "";
				$typeId = "";
				$projectId = "";
				$areaId = "";
				$areaId_extra1 = "";
				$areaId_extra2 = "";
				// $officeLoc = "";
				// $officeLat = "";
				// $officeLng = "";
				// $shift = "";
				// $shiftName = "";


	            $sqllog = "INSERT INTO xin_login_log (nik, device_id) 
	                    VALUES ('$nik', '$deviceID')";
	            $result = $this->db->query($sqllog);

            }


	            if($rows->device_id_one==0 || $rows->device_id_one=="0"){

		            $sqlupdevice = "UPDATE xin_user_mobile SET device_id_one = '$deviceID' WHERE employee_id = '$nik'";
		            $result = $this->db->query($sqlupdevice);

					$status = 9;
		            $message = "success";
		            $serverEnv = $rows->server_inv;
		            $isActive = $rows->is_active;
					$employeeID = $rows->employee_id;
		            $fullname = $rows->first_name.' '.$rows->last_name;
		            $typeId = $rows->usertype_id;
					$projectId = $rows->project_id;
					$areaId = $rows->areaid;
					$deviceid1 = $rows->device_id_one;
					$deviceid2 = $rows->device_id_two;
					$areaId_extra1 = $rows->areaid_extra1;
					$areaId_extra2 = $rows->areaid_extra2;

		            $sqllog = "UPDATE xin_user_mobile SET device_id_one = '$deviceID' WHERE employee_id = '$nik'";
		            $result = $this->db->query($sqllog);
	            } 



        } else if ($q->num_rows() > 1) {
            $status = 0;
            $message = "NIK anda terdaftar lebih dari 2 kali, Hubungi admin untuk informasi lengkapnya.\n\nDevice ID: $deviceID";
			
				$serverEnv = "";
				$isActive = "";
				$employeeID = "";
				$fullname = "";
				$typeId = "";
				$projectId = "";
				$areaId = "";
				$areaId_extra1 = "";
				$areaId_extra2 = "";
				
        } else {
            $status = 0;
            $message = "NIK anda tidak terdaftar, Hubungi admin untuk informasi lengkapnya.\n\nDevice ID: $deviceID";
			
				$serverEnv = "";
				$isActive = "";
				$employeeID = "";
				$fullname = "";
				$typeId = "";
				$projectId = "";
				$areaId = "";
				$areaId_extra1 = "";
				$areaId_extra2 = "";
				
        }
      
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'SERVER_ENV' => $serverEnv,
            'EMPLOYEE_ID' => $employeeID,
            'FULLNAME' => $fullname,
            'TYPE_ID' => $typeId,
            'PROJECT_ID' => $projectId,
			'AREA_ID' => $areaId,
			'AREA_ID_EXTRA1' => $areaId_extra1,
			'AREA_ID_EXTRA2' => $areaId_extra2
			// 'OFFICELNG' => $officeLng,
			// 'SHIFT_KERJA' => $shift,
			// 'SHIFT_NAME' => $shiftName
        ];
    }

    private function isNIKExists_next($apiName, $nik, $deviceID) {
        $q = $this->db->query("
        	SELECT employee_id, first_name, last_name, status_employee AS is_active, user_mobile_type AS usertype_id,server_inv,project_id,
		areaid1 AS areaid, areaid2 AS areaid_extra1,areaid3 AS areaid_extra2,device_id_one,device_id_two
		FROM xin_employees
		WHERE employee_id = '$nik'");

        $status = 1;
        $employeeID = $fullname  = $serverEnv = $message = $typeId = null;

        if ($q->num_rows() == 1) {
            $rows = $q->row();

            $message = "success";
            $serverEnv = $rows->server_inv;
            $isActive = $rows->is_active;
			$employeeID = $rows->employee_id;
            $fullname = $rows->first_name.' '.$rows->last_name;
            $typeId = $rows->usertype_id;
			$projectId = $rows->project_id;
			$areaId = $rows->areaid;
			$deviceid1 = $rows->device_id_one;
			$deviceid2 = $rows->device_id_two;
			$areaId_extra1 = $rows->areaid_extra1;
			$areaId_extra2 = $rows->areaid_extra2;
			// $officeLng = $rows->LNG;
			// $shift = $rows->SHIFT_KERJA;
			// $shiftName = $rows->SHIFT_NAME;
			

            if ($isActive != 1) {
                $status = 0;
                $message = "NIK anda tidak aktif, Hubungi admin untuk informasi lengkapnya.\n\nDevice ID: $deviceID";
				$serverEnv = "";
				$isActive = "";
				$employeeID = "";
				$fullname = "";
				$typeId = "";
				$projectId = "";
				$areaId = "";
				$areaId_extra1 = "";
				$areaId_extra2 = "";
				// $officeLoc = "";
				// $officeLat = "";
				// $officeLng = "";
				// $shift = "";
				// $shiftName = "";
			
            } 

            if($deviceID != $deviceid1 && $deviceID != $deviceid2){

                $status = 0;
                $message = "Device ID anda tidak terdaftar, Hubungi admin untuk informasi lengkapnya.\n\nDevice ID: $deviceID";
				$serverEnv = "";
				$isActive = "";
				$employeeID = "";
				$fullname = "";
				$typeId = "";
				$projectId = "";
				$areaId = "";
				$areaId_extra1 = "";
				$areaId_extra2 = "";
				// $officeLoc = "";
				// $officeLat = "";
				// $officeLng = "";
				// $shift = "";
				// $shiftName = "";


	            $sqllog = "INSERT INTO xin_login_log (nik, device_id) 
	                    VALUES ('$nik', '$deviceID')";
	            $result = $this->db->query($sqllog);

            }


	            if($rows->device_id_one==0 || $rows->device_id_one=="0"){

		            $sqlupdevice = "UPDATE xin_user_mobile SET device_id_one = '$deviceID' WHERE employee_id = '$nik'";
		            $result = $this->db->query($sqlupdevice);

					$status = 9;
		            $message = "success";
		            $serverEnv = $rows->server_inv;
		            $isActive = $rows->is_active;
					$employeeID = $rows->employee_id;
		            $fullname = $rows->first_name.' '.$rows->last_name;
		            $typeId = $rows->usertype_id;
					$projectId = $rows->project_id;
					$areaId = $rows->areaid;
					$deviceid1 = $rows->device_id_one;
					$deviceid2 = $rows->device_id_two;
					$areaId_extra1 = $rows->areaid_extra1;
					$areaId_extra2 = $rows->areaid_extra2;

		            $sqllog = "UPDATE xin_user_mobile SET device_id_one = '$deviceID' WHERE employee_id = '$nik'";
		            $result = $this->db->query($sqllog);
	            } 



        } else if ($q->num_rows() > 1) {
            $status = 0;
            $message = "NIK anda terdaftar lebih dari 2 kali, Hubungi admin untuk informasi lengkapnya.\n\nDevice ID: $deviceID";
			
				$serverEnv = "";
				$isActive = "";
				$employeeID = "";
				$fullname = "";
				$typeId = "";
				$projectId = "";
				$areaId = "";
				$areaId_extra1 = "";
				$areaId_extra2 = "";
				
        } else {
            $status = 0;
            $message = "NIK anda tidak terdaftar, Hubungi admin untuk informasi lengkapnya.\n\nDevice ID: $deviceID";
			
				$serverEnv = "";
				$isActive = "";
				$employeeID = "";
				$fullname = "";
				$typeId = "";
				$projectId = "";
				$areaId = "";
				$areaId_extra1 = "";
				$areaId_extra2 = "";
				
        }
      
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'SERVER_ENV' => $serverEnv,
            'EMPLOYEE_ID' => $employeeID,
            'FULLNAME' => $fullname,
            'TYPE_ID' => $typeId,
            'PROJECT_ID' => $projectId,
			'AREA_ID' => $areaId,
			'AREA_ID_EXTRA1' => $areaId_extra1,
			'AREA_ID_EXTRA2' => $areaId_extra2
			// 'OFFICELNG' => $officeLng,
			// 'SHIFT_KERJA' => $shift,
			// 'SHIFT_NAME' => $shiftName
        ];
    }

    private function isDeviceIDExists($apiName, $employeeID, $deviceID, $apkVersion) {

        $q = $this->db->query("SELECT device_id_one, device_id_two 
			FROM xin_user_mobile 
			WHERE EMPLOYEE_ID = '$employeeID'"
		);
        $code = 200; //Success
        $status = 1;
        $message = "Welcome";
        
        if ($q->num_rows() == 1) {
            $rows = $q->row();
            $rowDevice1 = $rows->device_id_one;
            $rowDevice2 = $rows->device_id_two;
            if ($rowDevice1 != $deviceID && $rowDevice2 != $deviceID) {
                $code = 305; //The data compared is not the same
                $message = "Device ID anda berbeda dari sebelumnya, Hubungi admin untuk informasi lengkapnya.\n\nDevice ID: $deviceID";
            } else {
				$sql = "UPDATE xin_user_mobile SET last_login = CURRENT_TIMESTAMP WHERE employee_id = ?";
				$this->db->query($sql, array($employeeID));
			}
				
			
        } else if ($q->num_rows() > 1) {
            $status = 0;
            $code = 303; //Return more than 1 row
            $message = "ID anda mempunyai banyak Perangkat yang terdaftar";
        } else {
            $code = 302; //Data not found
            $message = "Perangkat anda tidak terdaftar";
            $this->db->query("UPDATE xin_user_mobile SET device_id_one = '$deviceID' WHERE employee_id = '$employeeID'");
            $err = $this->db->error();
            if ($err["code"] != "") {
                $logErrCode = "U002";
                $this->ErrorModel->insertError($logErrCode, $err["message"], $apiName, $employeeID);
            }

				$sql = "UPDATE ATD_MT_USERMOBILE SET LAST_LOGIN = SYSTIMESTAMP WHERE EMPLOYEE_ID = ?";
				$this->db->query($sql, array($employeeID));
        }

        return (object) [
            'STATUS' => $status,
            'CODE' => $code,
            'MESSAGE' => $message
        ];
    }
	
    private function isDateExistsAttend($employeeID, $outlet) {
        $qq = $this->db->query("SELECT SHIFT_DATE, STATUS_ID
        FROM ATD_TR_ATTEND
        WHERE PHONE_DATE = 
        (
            SELECT MAX(PHONE_DATE) AS PHONE_DATE 
            FROM ATD_TR_ATTEND 
            WHERE EMPLOYEE_ID ='$employeeID'
        ) AND EMPLOYEE_ID = '$employeeID' ORDER BY STATUS_ID DESC
		");
		
		//$qq = $this->db->query("SELECT PHONE_DATE, STATUS_ID
		//		FROM SGF_TRABSENT_DEPO
		//		WHERE PHONE_DATE = (SELECT MAX(PHONE_DATE) AS PHONE_DATE FROM SGF_TRABSENT_DEPO WHERE USER_ID ='00000000')
		//		");
	
	

		if($qq->num_rows()==0){
			
			$statusId = "2";
			$lastDate = "1970-01-01";
			
		}else{
			
				$rows = $qq->row();
				$phone_date = $rows->SHIFT_DATE;
				$dateDb = date('Y-m-d', strtotime($phone_date));
				$strDateDb = (string) $dateDb;
				$statusId = $rows->STATUS_ID;
				$lastDate = $strDateDb;
				
			//if($rows->STATUS_ID == "1"){
			//	$statusId = "1";
			//	$lastDate = $strDateDb;
			//} else {
			//	$statusId = "2";
			//	$lastDate = $strDateDb;
			//}
			
			//if ($qq->num_rows()==1) {
			//	$statusId = "1";
			//	$lastDate = $strDateDb;
			//} else {
			//	$statusId = "2";
			//	$lastDate = $strDateDb;
			//}
		}
			
		
     
        return (object) [
            'STATUSID' => $statusId,
            'LASTDATE' => $lastDate
        ];
    }

    private function isOutletExists($customerID) {
        $q = $this->db->query("
        	SELECT cust.customer_id, cust.customer_name, cust.owner_name, cust.no_contact, cust.address, cust.district_id, dist.name AS district_name, cust.city_id, city.name AS city_name, cust.latitude, cust.longitude, cust.photo
        		FROM xin_customer cust
				LEFT JOIN mt_districts dist ON dist.id = cust.district_id
				LEFT JOIN mt_regencies city ON city.id = cust.city_id
				WHERE cust.customer_id = '$customerID'");

        $status = 1;
  		// $rows = $q->row();
		// $message = "success";
		// $customerID = $rows->customer_id;
		// $customerName = $rows->customer_name;

        // /$customerID = $customerName  = $ownerName = $address = $district = $city = $latitude = $longitude = $url = null;

        if ($q->num_rows() == 1) {
            $rows = $q->row();

            $message = "success";
            $customerID = $rows->customer_id;
            $customerName = $rows->customer_name;
            $address = $rows->address;
            $district = $rows->district_name;
			$city = $rows->city_name;
			$ownerName = $rows->owner_name;
			$latitude = $rows->latitude;
			$longitude = $rows->longitude;
			$url_foto = $rows->photo;

        } else if ($q->num_rows() > 1) {
            $status = 0;
            $message = "Toko/Outlet terdaftar lebih dari 2 kali, Hubungi admin untuk informasi lengkapnya.";

				$customerID = "";
				$customerName = "";
				$address = "";
				$district = "";
				$city = "";
				$ownerName = "";
				$latitude = "";
				$longitude = "";
				$url_foto = "";

        } else {
            $status = 0;
            $message = "Toko/Outlet anda tidak terdaftar, Hubungi admin untuk informasi lengkapnya.";
			
				$customerID = "";
				$customerName = "";
				$address = "";
				$district = "";
				$city = "";
				$ownerName = "";
				$latitude = "";
				$longitude = "";
				$url_foto = "";		
        }
      
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'CUSTOMER_ID' => $customerID,	
            'CUSTOMER_NAME' => $customerName,
            'ALAMAT' => $address,
            'DISTRICT' => $district,
			'CITY' => $city,
			'OWNER' =>$ownerName,
			'LATITUDE' => $latitude,
			'LONGITUDE' => $longitude,
			'URL' => $url_foto
        ];
    }


    private function isEmployeeExists($employee_id) {
        $q = $this->db->query("
        	SELECT emp.user_id,emp.employee_id,emp.first_name, emp.date_of_birth, 
			emp.gender, emp.marital_status, emp.ethnicity_type, agama.type,
			emp.contact_no, emp.email, emp.address, emp.city,
			emp.ktp_no, emp.ktp_status, emp.kk_no, emp.kk_status,
			emp.bpjs_tk_no, emp.bpjs_tk_status, emp.bpjs_ks_no, emp.bpjs_ks_status,
			emp.company_id, comp.name, emp.department_id, dept.department_name,
			emp.project_id, proj.title, emp.sub_project_id, prosub.sub_project_name,
			emp.designation_id, jabatan.designation_name, emp.status_employee, emp.ibu_kandung
			FROM xin_employees emp
			LEFT JOIN xin_companies comp ON comp.company_id = emp.company_id
			LEFT JOIN xin_projects proj ON proj.project_id = emp.project_id
			LEFT JOIN xin_departments dept ON dept.department_id = emp.department_id
			LEFT JOIN xin_projects_sub prosub ON prosub.secid = emp.sub_project_id
			LEFT JOIN xin_designations jabatan ON jabatan.designation_id = emp.designation_id
			LEFT JOIN xin_ethnicity_type agama ON agama.ethnicity_type_id = emp.ethnicity_type
			WHERE emp.employee_id  = '$employee_id'");

        $status = 1;

        if ($q->num_rows() == 1) {
            $rows = $q->row();

            $message = "success";
            $userid = $rows->user_id;
            $employeeid = $rows->employee_id;
            $first_name = $rows->first_name;
            $dob = $rows->date_of_birth;
			$gender = $rows->gender;
			$marital_status = $rows->marital_status;
			$agama = $rows->type;
			$no_contact = $rows->contact_no;
			$email = $rows->email;
			$address = $rows->address;
			$city = $rows->city;
			$no_ktp = $rows->ktp_no;
			$no_kk = $rows->kk_no;
			$no_bpjstk = $rows->bpjs_tk_no;
			$no_bpjsks = $rows->bpjs_ks_no;
			$company = $rows->pt_name;
			$prject = $rows->title;
			$project_sub = $rows->sub_project_name;
			$depth = $rows->department_name;
			$jabatan = $rows->designation_name;
			$status_employee = $rows->status_employee;
			$ibu_kandung = $rows->ibu_kandung;

        } else if ($q->num_rows() > 1) {
            $status = 0;
            $message = "Data Diri anda terdaftar lebih dari 2, segera hubungi IT Care untuk Verifikasi.";

            $userid = "";
            $employeeid = "";
            $first_name = "";
            $dob = "";
			$gender = "";
			$marital_status = "";
			$agama = "";
			$no_contact = "";
			$email = "";
			$address = "";
			$city = "";
			$no_ktp = "";
			$no_kk = "";
			$no_bpjstk = "";
			$no_bpjsks = "";
			$company = "";
			$prject = "";
			$project_sub = "";
			$depth = "";
			$jabatan = "";
			$status_employee = "";
			$ibu_kandung = "";

        } else {
            $status = 0;
            $message = "Data Diri anda tidak terdaftar, segera hubungi IT Care untuk Verifikasi.";
			
            $userid = "";
            $employeeid = "";
            $first_name = "";
            $dob = "";
			$gender = "";
			$marital_status = "";
			$agama = "";
			$no_contact = "";
			$email = "";
			$address = "";
			$city = "";
			$no_ktp = "";
			$no_kk = "";
			$no_bpjstk = "";
			$no_bpjsks = "";
			$company = "";
			$prject = "";
			$project_sub = "";
			$depth = "";
			$jabatan = "";
			$status_employee = "";
			$ibu_kandung = "";	
        }
      
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,

            'USERID' => $userid,
            'EMPLOYEE_ID' => $employeeid,
            'FULLNAME' => $first_name,
            'DOB' => $dob,
			'GENDER' => $gender,
			'MARITAL' => $marital_status,
			'AGAMA' => $agama,
			'NO_KONTAK' => $no_contact,
			'EMAIL' => $email,
			'ALAMAT' => $address,
			'KOTA' => $city,
			'NO_KTP' => $no_ktp,
			'NO_KK' => $no_kk,
			'NO_BPJSTK' => $no_bpjstk,
			'NO_BPJSKS' => $no_bpjsks,
			'COMPANY' => $company,
			'PROJECT' => $prject,
			'PROJECT_SUB' => $project_sub,
			'DEPT' => $depth,
			'JABATAN' => $jabatan,
			'EMPLOYEE_STATUS' => $status_employee,
			'IBU_KANDUNG' => $ibu_kandung
        ];
    }

    private function isOrderResume($employee_id,$datenow) {
        $q = $this->db->query("
        	SELECT 
			(SELECT COUNT(*) FROM xin_trx_cio WHERE employee_id = '$employee_id' AND c_io = 1 AND date_format(cio_date, '%Y-%m-%d') = '$datenow') as total_call, 
			COUNT(DISTINCT customer_id) AS total_ec, 
			SUM(qty) AS total_product, 
			SUM(total) AS total_value, 
			40 AS target_customer, 
			200000 AS target_value 
			FROM xin_mobile_order
			WHERE employee_id = '$employee_id'
			AND date_format(order_date, '%Y-%m-%d') = '$datenow'");

        $status = 1;

        if ($q->num_rows() > 0) {
            $rows = $q->row();

            $message 			= "success";
            $total_call 		= $rows->total_call;
            $total_ec 			= $rows->total_ec;

            if($rows->total_product==null){
	            $total_product 	= 0;
            } else {
	            $total_product 	= $rows->total_product;
            }

            if($rows->total_value==null){
            	$total_value 		= "Rp. 0;";
            } else {
            	$total_value 		= "Rp " . number_format($rows->total_value,0,',','.') . ";";
            }

            $target_value 		= "Rp " . number_format($rows->target_value,0,',','.') . ";";
			// $target_value 		= "Rp. "$rows->target_value;

        } else {
            $status = 0;
            $message = "Resume Order Kosong.";

            $total_call			= "0";
            $total_ec 			= "0";
            $total_product 		= "0";
            $total_value 		= "0";
            $target_customer 	= "0";
			$target_value 		= "0";
        }
      
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,

            'TOTAL_CALL' 		=> $total_call,
            'TOTAL_EC' 			=> $total_ec,
            'TOTAL_PRODUCT' 	=> $total_product,
            'TOTAL_VALUE' 		=> $total_value,
            'TARGET_CUSTOMER' 	=> $target_customer,
			'TARGET_VALUE' 		=> $target_value
        ];
    }
 
 // CUSTOMER AREA

    public function customerarea() {
        $apiName = "apicakrawala/user/customerarea";
        $area = $this->CI->rest->input('area');
        $area2 = $this->CI->rest->input('area2');
        $area3 = $this->CI->rest->input('area3');
        $latitude = $this->CI->rest->input('latitude');
        $longitude = $this->CI->rest->input('longitude');

        $getCustomerArea = $this->CI->DownloadModel->getCustomerAreaDist($area, $area2, $area3, $latitude,$longitude);
        $this->CI->rest->output($getCustomerArea->STATUS, $getCustomerArea->MESSAGE, $getCustomerArea->DATA);

    }


    public function customerbyid() {
        $apiName = "apicakrawala/user/customerbyid";
        $empid = $this->CI->rest->input('empid');
        $area = $this->CI->rest->input('area');
        $area2 = $this->CI->rest->input('area2');
        $area3 = $this->CI->rest->input('area3');
        $latitude = $this->CI->rest->input('latitude');
        $longitude = $this->CI->rest->input('longitude');

        $getCustomerByNIP = $this->CI->DownloadModel->getCustomerByNIP($empid, $area, $area2, $area3, $latitude,$longitude);
        $this->CI->rest->output($getCustomerByNIP->STATUS, $getCustomerByNIP->MESSAGE, $getCustomerByNIP->DATA);
    }

    // public function customerbyid() {
    //     $status = 1; $message = ''; $data = null;
    //     $apiName = "apicakrawala/user/customerbyid";
    //     $customerID 		= $this->CI->rest->input('customer_id'); 
    //     $iscustomerID 		= $this->isNullField($customerID);

    //     if ($iscustomerID) {

	//             restOutput(0, "Field nya ada yang kosong ". "Customer ID : " .$customerID, new stdClass());

	//         }else{

	// 			// restOutput(0, "Field nya ada yang kosong ". "Customer ID : " .$customerID, new stdClass());

	//         	$isOutletExists = $this->isOutletExists($customerID);
	// 			// $token = null;
	// 			if($isOutletExists->STATUS){

	// 				$data = array(
	// 					"customer_id" => $isOutletExists->CUSTOMER_ID,
	// 					"customer_name" => $isOutletExists->CUSTOMER_NAME,
	// 					"alamat" => $isOutletExists->ALAMAT,
	// 					"district" => $isOutletExists->DISTRICT,
	// 					"city" => $isOutletExists->CITY,
	// 					"owner" => $isOutletExists->OWNER,
	// 					"latitude" => $isOutletExists->LATITUDE,
	// 					"longitude" => $isOutletExists->LONGITUDE,
	// 					"url" => $isOutletExists->URL

	// 				);

	// 				restOutput($isOutletExists->STATUS, $isOutletExists->MESSAGE, $data);

	// 			} else {

	// 				restOutput($isOutletExists->STATUS, $isOutletExists->MESSAGE, new stdClass());


	// 			}

	//     }
    // }

    public function attend() {
        $apiName = "sgfattend/user/attend";
		
        $shiftDate = $this->CI->rest->input('shift_date');
        $phoneDate = $this->CI->rest->input('phone_date');
        $phoneDateTime = $this->CI->rest->input('phone_datetime');
        $statusClock = $this->CI->rest->input('status_clock');
        $latitude = $this->CI->rest->input('latitude');
        $longitude = $this->CI->rest->input('longitude');
        $latOutlet = $this->CI->rest->input('lat_outlet');
        $lonOutlet = $this->CI->rest->input('lon_outlet');
        $distance = $this->CI->rest->input('distance');
        $depoid = $this->CI->rest->input('depoid');
        $maxRadius = $this->CI->rest->input('max_radius');
        $statusRange = $this->CI->rest->input('status_range');
        $reasonID = $this->CI->rest->input('reason_id');
        $reasonName = $this->CI->rest->input('reason_desc');
		
		$jwt = $this->CI->auth->checkToken();
		
        //$resToken = $this->sgf->validationToken();
        //$statusToken = $resToken["status"];
        //$msgToken = $resToken["message"];
        // TOKEN = [ DEVICE_ID, EMPLOYE_ID, SERVER_ENV, TYPE_ID

        if ($jwt->STATUS == 1) {
			
            $device_id = $jwt->DATA->deviceID;
            $nik = $jwt->DATA->nik;
            $type_Id = $jwt->DATA->typeID;
            $server_env = $jwt->DATA->env;
			$shift = $jwt->DATA->shift;
			
            $isUserActive = $this->isUserActiveAttend($apiName, $nik, $device_id, $type_Id, $server_env);
            if ($isUserActive->STATUS == 1) {
				
				$datePh = date_create($phoneDate);
				if(date_format($datePh,"Y-m-d") > date("Y-m-d"))
				{
					
					restOutput(0, "Tanggal absensi tidak diterima,\nCek pengaturan Tanggal dan Waktu di smartphone anda.", new stdClass());
					
				} else {
					
						if($shift == 1){
							
							
							$isValidateClockOut = $this->isValidateClockOut($apiName, $nik, $shiftDate, $statusClock, $shift);
							if($isValidateClockOut->STATUS == 1){
								
								$sendNoo = $this->insertAttend(
								$apiName, $nik, $shiftDate, 
								$phoneDate, $phoneDateTime, $statusClock, 
								$latitude, $longitude, $latOutlet, $lonOutlet,
								$depoid, $shift, $distance, $maxRadius, $statusRange,
								$reasonID, $reasonName
								);
								
								restOutput($sendNoo->STATUS, $sendNoo->MESSAGE, new stdClass());
								
							} else if ($isValidateClockOut->STATUS == 2){
								
								restOutput(1, $isValidateClockOut->MESSAGE, new stdClass());
								
							} else {
								restOutput($isValidateClockOut->STATUS, $isValidateClockOut->MESSAGE, new stdClass());
							}
						}else{
							
							$isExistAttend = $this->isExistDayAttend($apiName, $nik, $shiftDate, $statusClock);
							
							if($isExistAttend->STATUS == 1){
				
								
								$isValidateClockOut = $this->isValidateClockOut($apiName, $nik, $shiftDate, $statusClock, $shift);
								if($isValidateClockOut->STATUS == 1){
									
									$sendNoo = $this->insertAttend(
									$apiName, $nik, $shiftDate, 
									$phoneDate, $phoneDateTime, $statusClock, 
									$latitude, $longitude, $latOutlet, $lonOutlet,
									$depoid, $shift, $distance, $maxRadius, $statusRange,
									$reasonID, $reasonName
									);
									
									restOutput($sendNoo->STATUS, $sendNoo->MESSAGE, new stdClass());
								
								} else {
									restOutput($isValidateClockOut->STATUS, $isValidateClockOut->MESSAGE, new stdClass());
								}
								
							} else { 

								restOutput($isExistAttend->STATUS, $isExistAttend->MESSAGE, new stdClass());
							 
							}
					
						}
				}
				
			
              
            } else {
                restOutput($isUserActive->STATUS, $isUserActive->MESSAGE, new stdClass());
            }
        } else {
			$this->CI->rest->output($jwt->STATUS, $jwt->MESSAGE, $jwt->DATA);
            //$this->insertError("401", $msgToken, $apiName, "undefined");
            //restOutput(401, $this->lang->line("failed_tokenexpired") . " " . $msgToken, new stdClass());
        }
    }
	
	
    public function shift() {
        $apiName = "sgfattend/user/shift";
			
        $phoneDate = $this->CI->rest->input('phone_date');
		$jwt = $this->CI->auth->checkToken();
		
					
        if ($jwt->STATUS == 1) {
			
            $device_id = $jwt->DATA->deviceID;
            $nik = $jwt->DATA->nik;
            $type_Id = $jwt->DATA->typeID;
            $server_env = $jwt->DATA->env;
			$shift = $jwt->DATA->shift;
			
			$isDateNow = $this->checkDatefromDB();
            $isUserActive = $this->isUserActiveAttend($apiName, $nik, $device_id, $type_Id, $server_env);
            if ($isUserActive->STATUS == 1) {
				
				
				$datePh = date_create($phoneDate);
				if(date_format($datePh,"Y-m-d") == $isDateNow->DATEDB)
				//date("Y-m-d")
				//date('Y-m-d',strtotime("-1 days"))
				{
					 
							$getLastAttend = $this->CI->DownloadModel->getLastAttend($nik,date_format($datePh,"Y-m-d"),$shift);
							$this->CI->rest->output($getLastAttend->STATUS, $getLastAttend->MESSAGE, $getLastAttend->DATA);
					
				} else {
					
					//restOutput(0, "Tanggal absensi tidak diterima,\nCek pengaturan Tanggal dan Waktu di smartphone anda.", new stdClass());
					restOutput(0, $phoneDate."=".date("Y-m-d h:i:s")."->".$isDateNow->DATEDB, new stdClass());
				}
				
						
              
            } else {
                restOutput($isUserActive->STATUS, $isUserActive->MESSAGE, new stdClass());
            }
        } else {
			$this->CI->rest->output($jwt->STATUS, $jwt->MESSAGE, $jwt->DATA);
        }
    }
	
	
    public function history() {
        $apiName = "sgfattend/user/history";
			
        $phoneDate = $this->CI->rest->input('phone_date');
		$jwt = $this->CI->auth->checkToken();
		
					
        if ($jwt->STATUS == 1) {
			
            $device_id = $jwt->DATA->deviceID;
            $nik = $jwt->DATA->nik;
            $type_Id = $jwt->DATA->typeID;
            $server_env = $jwt->DATA->env;
			$shift = $jwt->DATA->shift;
			
			$isDateNow = $this->checkDatefromDB();
            $isUserActive = $this->isUserActiveAttend($apiName, $nik, $device_id, $type_Id, $server_env);
            if ($isUserActive->STATUS == 1) {
				
				$datePh = date_create($phoneDate);  
				if(date_format($datePh,"Y-m-d") == $isDateNow->DATEDB)
				{
					
							$getHistory = $this->CI->DownloadModel->getHistory($nik);
							$this->CI->rest->output($getHistory->STATUS, $getHistory->MESSAGE, $getHistory->DATA);
					
				} else {
					
					//restOutput(0, "Tanggal absensi tidak diterima,\nCek pengaturan Tanggal dan Waktu di smartphone anda.", new stdClass());
					restOutput(0, date("Y-m-d h:i:s")."->".$isDateNow->DATEDB, new stdClass());
				}
				
						
              
            } else {
                restOutput($isUserActive->STATUS, $isUserActive->MESSAGE, new stdClass());
            }
        } else {
			$this->CI->rest->output($jwt->STATUS, $jwt->MESSAGE, $jwt->DATA);
        }
    }
	

    public function lookcin() {
        $apiName = "apicakrawala/user/lookcin";
			
        $date_cio = $this->CI->rest->input('date_cio');
        $employee_id = $this->CI->rest->input('employee_id');
        $customer_id = $this->CI->rest->input('customer_id');
       		
		// $isDateNow = $this->checkDatefromDB();
        // $isAlreadyCio = $this->isAlreadyCio($date_cio, $employee_id, $customer_id);
				
				// $datePh = date_create($phoneDate);  

				$isAlreadyCin = $this->CI->DownloadModel->isAlreadyCin($date_cio, $employee_id, $customer_id);
				$this->CI->rest->output($isAlreadyCin->STATUS, $isAlreadyCin->MESSAGE, $isAlreadyCin->DATA);

    }
	
    public function lookcout() {
        $apiName = "apicakrawala/user/lookcout";
			
        $date_cio = $this->CI->rest->input('date_cio');
        $employee_id = $this->CI->rest->input('employee_id');
        $customer_id = $this->CI->rest->input('customer_id');
       		
		// $isDateNow = $this->checkDatefromDB();
        // $isAlreadyCio = $this->isAlreadyCio($date_cio, $employee_id, $customer_id);
				
				// $datePh = date_create($phoneDate);  

				$isAlreadyCout = $this->CI->DownloadModel->isAlreadyCout($date_cio, $employee_id, $customer_id);
				$this->CI->rest->output($isAlreadyCout->STATUS, $isAlreadyCout->MESSAGE, $isAlreadyCout->DATA);

    }
	

    public function lookcio() {
        $apiName = "apicakrawala/user/lookcio";
			
        $date_cio = $this->CI->rest->input('date_cio');
        $employee_id = $this->CI->rest->input('employee_id');
        $customer_id = $this->CI->rest->input('customer_id');
       		
		// $isDateNow = $this->checkDatefromDB();
        // $isAlreadyCio = $this->isAlreadyCio($date_cio, $employee_id, $customer_id);
				
				// $datePh = date_create($phoneDate);  

				$isAlreadyCin = $this->CI->DownloadModel->isAlreadyCin($date_cio, $employee_id, $customer_id);
				$this->CI->rest->output($isAlreadyCin->STATUS, $isAlreadyCin->MESSAGE, $isAlreadyCin->DATA);

    }


    public function lookorder() {
        $apiName = "apicakrawala/user/lookorder";
		
        $date_cio = $this->CI->rest->input('date_cio');
        $employee_id = $this->CI->rest->input('employee_id');
        $customer_id = $this->CI->rest->input('customer_id');
       		
		// $isDateNow = $this->checkDatefromDB();
        // $isAlreadyCio = $this->isAlreadyCio($date_cio, $employee_id, $customer_id);
				
				// $datePh = date_create($phoneDate);  

				$isAlreadyOrder = $this->CI->DownloadModel->isAlreadyOrder($date_cio, $employee_id, $customer_id);
				$this->CI->rest->output($isAlreadyOrder->STATUS, $isAlreadyOrder->MESSAGE, $isAlreadyOrder->DATA);

    }

    public function historyPeriode() {
        $apiName = "sgfattend/user/historyperiode";
			
		$phoneDate = $this->CI->rest->input('phone_date');
        $phoneDateFrom = $this->CI->rest->input('phone_date_from');
        $phoneDateUntil = $this->CI->rest->input('phone_date_until');
		$jwt = $this->CI->auth->checkToken();
		
					
        if ($jwt->STATUS == 1) {
			
            $device_id = $jwt->DATA->deviceID;
            $nik = $jwt->DATA->nik;
            $type_Id = $jwt->DATA->typeID;
            $server_env = $jwt->DATA->env;
			$shift = $jwt->DATA->shift;
			
			$isDateNow = $this->checkDatefromDB();
            $isUserActive = $this->isUserActiveAttend($apiName, $nik, $device_id, $type_Id, $server_env);
            if ($isUserActive->STATUS == 1) {
				
				$datePh = date_create($phoneDate);
				if(date_format($datePh,"Y-m-d") == $isDateNow->DATEDB)
				{
					
							$getHistory = $this->CI->DownloadModel->getHistoryPeriode($nik, $phoneDateFrom, $phoneDateUntil);
							$this->CI->rest->output($getHistory->STATUS, $getHistory->MESSAGE, $getHistory->DATA);
					
				} else {
					
					restOutput(0, "Tanggal absensi tidak diterima,\nCek pengaturan Tanggal dan Waktu di smartphone anda.", new stdClass());
				}
				
						
              
            } else {
                restOutput($isUserActive->STATUS, $isUserActive->MESSAGE, new stdClass());
            }
        } else {
			$this->CI->rest->output($jwt->STATUS, $jwt->MESSAGE, $jwt->DATA);
        }
    }
	
    private function isUserActiveAttend($apiName, $nik, $deviceID, $typeId, $serverEnv) {
        $q = $this->db->query("SELECT TYPE_ID, SERVER_ENVIRONMENT
            FROM ATD_MT_USERMOBILE 
            WHERE EMPLOYEE_ID = '$nik' AND IS_ACTIVE = 'Y'
        "); 
        $status = 1;
        $message = null;


        if ($q->num_rows()) {
            $rows = $q->row();
            if ($rows->SERVER_ENVIRONMENT != $serverEnv) {
                $logErrCode = "IU00"; $message = "Token serverEnv: ".$serverEnv . " DB serverEnv: ".$rows->SERVER_ENVIRONMENT;
                $this->ErrorModel->insertError($logErrCode, $message, $apiName, $nik);
                $status = 401; $message = "Environment Id anda berubah, session dan data aplikasi anda dihapus";
            }
            
            if ($rows->TYPE_ID != $typeId) {
                $logErrCode = "IU01"; $message = "Token typeId: ".$typeId . " DB typeId: ".$rows->TYPE_ID;
                $this->ErrorModel->insertError($logErrCode, $message, $apiName, $nik);
                $status = 401; $message = "XType Id anda berubah, session dan data aplikasi anda dihapus";
            }

            $q2 = $this->db->query("SELECT COUNT(1) AS IS_EXISTS 
                FROM ATD_MT_USERMOBILE 
                WHERE EMPLOYEE_ID = '$nik' AND DEVICE1 = '$deviceID' OR DEVICE2 = '$deviceID'
            ");
			
            $rows2 = $q2->row();
            $isImeiExists = $rows2->IS_EXISTS;

            if (!$isImeiExists) {
                $logErrCode = "IU02"; $message = "Token DeviceID: ".$deviceID . " DB Device ID: cek sendiri yaaa";
                $this->ErrorModel->insertError($logErrCode, $message, $apiName, $nik);
                $status = 401; $message = "DeviceID anda sudah berubah, session dan data aplikasi anda dihapus";
            }
        } else {
            $logErrCode = "IU03"; $message = "Token NIK: ".$nik . " tidak aktif ";
            $this->ErrorModel->insertError($logErrCode, $message, $apiName, $nik);
            $status = 401; $message = "XNIK anda sudah tidak aktif, session dan data aplikasi anda dihapus";
        }
        
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message
        ];
    }
	
	
    private function isExistDayAttend($apiName, $nik, $shiftDate, $statusClock) {
        $q = $this->db->query("SELECT EMPLOYEE_ID, 
		CONCAT(CONCAT(CONCAT(CONCAT(TO_CHAR(PHONE_DATETIME,'HH24'),':'),TO_CHAR(PHONE_DATETIME,'MM')),':'),TO_CHAR(PHONE_DATETIME,'SS'))
		AS TIMES
            FROM ATD_TR_ATTEND 
            WHERE EMPLOYEE_ID = '$nik' AND TO_CHAR(SHIFT_DATE,'YYYY-MM-DD') = '$shiftDate' AND STATUS_ID = '$statusClock' AND SHIFT_ID = '2'
        "); 
        $status = 1;
        $message = null;

 
        if ($q->num_rows() == 1) {
            $rows = $q->row();
			
            $logErrCode = "IU03"; $message = "Employee NIK: ".$nik . " Sudah Melakukan Absensi ";
            //$this->insertError($logErrCode, $message, $apiName, $nik);
            $status = 401; $message = "Anda sudah melakukan absensi di Jam ".$rows->TIMES;
			
        } else {
			$status = 1;
			$message = "GO TO INSERT";
		}			
       
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message
        ];
    }
	
	
    private function isValidateClockOut($apiName, $nik, $shiftDate, $statusClock, $shift) {
        $q = $this->db->query("SELECT *
								FROM ATD_TR_ATTEND
								WHERE TO_CHAR(SHIFT_DATE,'YYYY-MM-DD') = '$shiftDate'
								AND EMPLOYEE_ID = '$nik'
								AND SHIFT_ID = '$shift'
								AND STATUS_ID = 1
        "); 
        $status = 1;
        $message = null;

 
        if ($q->num_rows() == 1) {
			
			
			if($statusClock == 1) {
					
				$status = 2;
				$message = "SKEEP";
			} else {
				
				$status = 1;
				$message = "GO TO INSERT";
			
			}
			
        } else {
			
			if($statusClock == 1) {
				
				$status = 1;
				$message = "GO TO INSERT";
			
			} else {
				
				$status = 401; 
				$message = "Mohon disiplin dalam penggunaan Absensi Online.!, \n\nLakukan Absen Masuk terlebih dahulu. \n\nTerima Kasih.";
			
			}
				
			
		}			
       
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message
        ];
    }
	

	
	
    private function insertAttend(
								$apiName, $nik, $shiftDate, $phoneDate, $phoneDateTime, 
								$statusClock, $latitude, $longitude, $latOutlet, $lonOutlet, 
								$depoid, $shift, $distance, $maxRadius, $statusRange,
								$reasonID, $reasonName) {
									
        $errTransaction = $errMaster = null;
        $status = 1;
        $isExistsRetail = $isExistsTransaction = 0;
        $message = 'Sukses kirim data absensi';

   
                $stmt = oci_parse($this->db->conn_id, "INSERT INTO ATD_TR_ATTEND
				(EMPLOYEE_ID,STATUS_ID, SHIFT_DATE, PHONE_DATE, PHONE_DATETIME, LATITUDE, LONGITUDE, LAT_OUTLET, LON_OUTLET, VKBUR, 
				SHIFT_ID, DISTANCE, LIMIT_RADIUS, STATUS_RANGE, REASON_ID, REASON_DESC)
				VALUES
				('$nik',
				'$statusClock',
				TO_DATE('$shiftDate','YYYY-MM-DD'),
				TO_DATE('$phoneDate','YYYY-MM-DD'),
				TIMESTAMP '$phoneDateTime',
				'$latitude',
				'$longitude',
				'$latOutlet',
				'$lonOutlet',
				'$depoid',
				'$shift',
				'$distance',
				'$maxRadius',
				'$statusRange',
				'$reasonID',
				'$reasonName'
				)
				"); 
                $r = oci_execute($stmt, OCI_NO_AUTO_COMMIT);
                
                if (!$r) {
                    $err = oci_error($stmt);
                    $isConstraint = $this->checkConstraint($err["message"]);
                    if (!$isConstraint) {
                        $errTransaction = $err["message"];
                    } else {
                        $isExistsTransaction = 1;
                    }
                    oci_free_statement($stmt);
                }
                
                if (is_null($errTransaction)) {
                    oci_commit($this->db->conn_id);
                } else {
                    oci_rollback($this->db->conn_id);
                    $logErrCode = "O011";
                    $status = 0;
                    $message = 'Err insert trabsentdepo: '.$errTransaction;
                    $this->ErrorModel->insertError($logErrCode, $message, $apiName, $nik);
                }

        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message
        ];
    	}
	
	
    public function radius() {
        $status 	= 1; $message = ''; $data = null;
        $apiName 	= "sgfattend/user/radius";
        $officeID 	= $this->CI->rest->input('office_id'); 
        $distance 	= $this->CI->rest->input('distance');
		
		
            $isRadius = $this->isRadius($officeID, $distance);
	
                    $data = array(
					
                        "max_radius" => $isRadius->MAXRADIUS,
                        "status_range" => $isRadius->MESSAGE
                    );
					
			if ($isRadius->STATUS) {
                    
                    restOutput($isRadius->STATUS, $isRadius->MESSAGE, $data);
				
			}else{
				
					restOutput($isRadius->STATUS, $isRadius->MESSAGE, $data);
			}
		
    }


    private function isRadius($officeID, $distance) {

        $q = $this->db->query("SELECT rd.SALESOFFICE_ID, rd.MAX_RADIUS, rd.IS_ACTIVE from (
							SELECT SALESOFFICE_ID, MAX_RADIUS, IS_ACTIVE FROM ATD_MT_RADIUS
							UNION
							select CUSTOMERID AS SALESOFFICE_ID, MAXRADIUS AS MAX_RADIUS, IS_ACTIVE from ATD_MT_EXTRA_LOCATION
							) rd
							WHERE rd.SALESOFFICE_ID = '$officeID'"
		);
        $code = 200; //Success
        $status = 1;
        $message = "Welcome";
		$rowRadius = 0;
        
        if ($q->num_rows() >= 1) {
            $rows = $q->row(); 
            $rowRadius = $rows->MAX_RADIUS;
			$isActiveRadius = $rows->IS_ACTIVE;

			if($isActiveRadius == 'Y') {
				
				if ($rowRadius == null || $rowRadius == 0) {
					
					$code = 305; //The data compared is not the same
					$message = "Out Of Range";
					$rowRadius = 0;
					
				} else if ($distance <= $rowRadius ){
					
					$code = 305; //The data compared is not the same
					$message = "In Radius";
					
				} else {
					
					$status = 0;
					$code = 303; //Return more than 1 row
					$message = "Out Of Range";
					
				}
			
			} else {
				
					$code = 305; //The data compared is not the same
					$message = "In Radius";
			}

			
        } else {
			
                $code = 305; //The data compared is not the same
                $message = "Out Of Range";
        }

					
        return (object) [
            'STATUS' => $status,
            'CODE' => $code,
            'MESSAGE' => $message,
			'MAXRADIUS' => $rowRadius
        ];
    }
	
    public function refresh_regsid() {
        $status = 1; $message = ''; $data = null;
        $api = "soagent/user/refresh_regsid";
        $regsid = $this->CI->rest->input('regsid');
        $jwt = $this->CI->auth->checkToken();
        if ($jwt->STATUS == 1) {
            $updateRegsID = $this->CI->UserModel->updateRegsID($api, $jwt->DATA->sid, $regsid);
            $this->CI->rest->output($updateRegsID->STATUS, $updateRegsID->MESSAGE, $updateRegsID->DATA);
        } else {
            $this->CI->rest->output($jwt->STATUS, $jwt->MESSAGE, $jwt->DATA);
        }
    }

    public function token() {
        $status = 1; $message = ''; $data = null;
        $api = "soagent/user/token";
        $jwt = $this->CI->auth->validationToken();
        if ($jwt) {
            if ($jwt->STATUS == 1) {
                $act = $this->CI->auth->validationActivity($api, $jwt->DATA->sid, $jwt->DATA->env, $jwt->DATA->logindate);
                $this->CI->rest->output($act->STATUS, $act->MESSAGE, new stdClass());
            } else {
                $this->CI->rest->output(401, $jwt->MESSAGE, new stdClass());
            }
        } else {
            $this->CI->rest->output(401, 'Token not provided ', new stdClass());
        }
    }


    public function reason() {
       // $apiName = "apicakrawala/user/reason";
					
					
							$getReason = $this->CI->DownloadModel->getReason();
							$this->CI->rest->output($getReason->STATUS, $getReason->MESSAGE, $getReason->DATA);
				
 
    }

    public function version() {
        $status = 1; $message = ''; $data = null;
        $apiName = "apicakrawala/user/version";
        $apkVersion = $this->CI->rest->input('version');
		
		$isValidateVersion = $this->isValidateVersion($apiName, $apkVersion);
		if($isValidateVersion->STATUS == 1){
			restOutput($isValidateVersion->STATUS, $isValidateVersion->MESSAGE, new stdClass());
		} else {
			restOutput($isValidateVersion->STATUS, $isValidateVersion->MESSAGE, new stdClass());
		}
    }
	
	
    private function isValidateVersion($apiName, $apkVersion) {

        $status = 1;
        $message = null;

	
        if ($apkVersion == "0.0.1") {
			
			$status = 1;
			$message = "Updated";
			
        } else {
			
			$status = 0; 
			$message = "Old Version";

		}			
       
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message
        ];
    }
	
	public function getextralocation() {
		
        $status = 1; $message = ''; $data = null;
        $apiName = "sgfattend/user/getextralocation";		
        $nik 		= $this->CI->rest->input('nik'); 
		
					$getOffice = $this->CI->DownloadModel->getOffice($nik);
					//$getExLocation = $this->CI->DownloadModel->getReason();
							
					$this->CI->rest->output($getOffice->STATUS, $getOffice->MESSAGE, $getOffice->DATA);
				
 
    }
	
	
    private function checkDatefromDB() {
        $q = $this->db->query("SELECT DATENOW FROM ATD_MT_DATENOW"); 
        $dateDB = null;

 
        if ($q->num_rows() == 1) {
            $rows = $q->row();
            $dateDB = $rows->DATENOW;
        } else {
			$dateDB = date("Y-m-d");
		}			
       
        return (object) [
            'DATEDB' => $dateDB
        ];
    }
	

    public function getdistance() {
        $status 	= 1; $message = ''; $data = null;
        $apiName 	= "apicakrawala/user/getdistance";
        $lat1 		= $this->CI->rest->input('lat1'); 
        $lon1 		= $this->CI->rest->input('lon1');
        $lat2 		= $this->CI->rest->input('lat2');
        $lon2 		= $this->CI->rest->input('lon2');
        // $unit 		= $this->CI->rest->input('unit');
        // $outlet 		= $this->isNullField($nik);
        // $isdeviceID = $this->isNullField($deviceID);
        // $isApkNull 	= $this->isNullField($apkVersion);
        // $presencedt = $this->getDateOnly($logindt);

		$getOffice = $this->CI->UserModel->distance($lat1,$lon1,$lat2,$lon2);

		// $this->CI->rest->output($getOffice->STATUS, $getOffice->MESSAGE, $getOffice->DATA);
		// restOutput($isNIKExists->STATUS, $isNIKExists->MESSAGE, $data);
		echo $getOffice->MESSAGE;
		// restOutput("1", $getOffice->MESSAGE, new stdClass());

    }

    public function upcio(){


    	if($_SERVER['REQUEST_METHOD']=='POST'){

    		if(isset($_POST['name']) and isset($_FILES['image']['name'])){

    			$name = $_POST['name'];
    			$fileinfo = pathinfo($_FILES['image']['name']);
    			$extension = $fileinfo['extension'];
    			$file_url = $upload_url . '12343' . '.' . $extension;
    			$file_path = $upload_path . '12343' . '.'. $extension;
    			try{
					 //saving the file
					 move_uploaded_file($_FILES['image']['tmp_name'],$file_path);
					 $sql = "INSERT INTO `db_images`.`images` (`id`, `url`, `name`) VALUES (NULL, '$file_url', '$name');";

					 //adding the path and name to database
					 if(mysqli_query($con,$sql)){
					 	//filling response array with values
						 $response['error'] = false;
						 $response['url'] = $file_url;
						 $response['name'] = $name;
 					}
				} catch(Exception $e){
					$response['error']=true;
				 $response['message']=$e->getMessage();
				 }
				 echo json_encode($response);
					mysqli_close($con);
    		} else {
    			$response['error']=true;
 				$response['message']='Please choose a file';
    		}

    	}
    }

}
