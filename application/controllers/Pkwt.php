<?php

class Pkwt extends CI_Controller {

    protected $CI;

    public function __construct() {
        parent:: __construct();
        $this->CI = & get_instance();
        $this->CI->load->model('UserModel');
        $this->CI->load->model('DownloadModel');
        $this->CI->load->model('UpdateModel');
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
	


    public function inactivepkwt() {

        $status = 1; $message = 'Tidak ada pkwt yg expired'; $data = null;
        $q = $this->db->query("
        	SELECT contract_id, to_date, current_date() as tgl_sekarang, datediff(current_date(), to_date) as selisih
			FROM xin_employee_contract WHERE datediff(current_date(), to_date) >= 1 AND status_pkwt != 0 AND status_approve = 1
        	"); 
        $i = 0;
        $totalup = 0;

        while ($i < $q->num_rows()) {

        	$rows = $q->row();
        	$x = $this->db->query("
        		UPDATE xin_employee_contract SET status_pkwt = 0 WHERE datediff(current_date(), to_date) >= 1 AND status_pkwt != 0 AND status_approve = 1
        	"); 
        	$i++;
        	$message='success';
        	$data = array(
			"total_pkwt_inactive" => $i
			);

    	}		
		restOutput($status, $message, $data);
    }



    public function inacitve() {
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

	        	$isNIKExists = $this->isNIKExists($apiName, $nik, $deviceID);
				$token = null;
				if($isNIKExists->STATUS){


					$data = array(
						"employee_id" => $isNIKExists->EMPLOYEE_ID,
						"fullname" => $isNIKExists->FULLNAME,
						"server_env" => $isNIKExists->SERVER_ENV,
						"type_id" => $isNIKExists->TYPE_ID

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







			// 		if ($isNIKExists->STATUS) {

			// 			// $isDeviceIDExists = $this->isDeviceIDExists($apiName, $isNIKExists->EMPLOYEE_ID, $deviceID, $apkVersion);

		 //                    // header("Token: " .$token);


		      //               $data = array(
		      //                   "code" => $isDeviceIDExists->CODE,
		      //                   "fullname" => $isNIKExists->FULLNAME,
		      //                   "employee_id" => $isNIKExists->EMPLOYEE_ID,
		      //                   "type_id" => $isNIKExists->TYPE_ID,
		      //                   "server_env" => $isNIKExists->SERVER_ENV,
		      //                   // "office_code" => $isNIKExists->OFFICECODE,
		      //                   // "office_loc" => $isNIKExists->OFFICELOC,
								
		      //                   // "office_lat" => $isNIKExists->OFFICELAT,
		      //                   // "office_lng" => $isNIKExists->OFFICELNG,
					
								// // "shif_kerja" => $isNIKExists->SHIFT_KERJA,
								// // "last_date" => $isDateExists->LASTDATE,
								// // "status_attend" => $isDateExists->STATUSID
		      //               );
		                    
		 //      //               restOutput($isDeviceIDExists->STATUS, $isDeviceIDExists->MESSAGE, $data);

			// 		} else {
			// 			// restOutput($isNIKExists->STATUS, $isNIKExists->MESSAGE, new stdClass());


			// 		}
	    }
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
			
            $this->insertError('U000', 'Field null, ' . " NIK : ".$nik ." Device ID : ".$deviceID ." APK : ".$apkVersion, $apiName);
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
        	SELECT users.employee_id, emp.first_name, emp.last_name, users.is_active, users.posisi_id, users.server_inv
			FROM xin_user_mobile users
			LEFT JOIN xin_employees emp ON emp.employee_id = users.employee_id
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
            $typeId = $rows->posisi_id;
			// $officeCode = $rows->VKBUR;
			// $officeLoc = $rows->BEZEI;
			// $officeLat = $rows->LAT;
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
				// $officeCode = "";
				// $officeLoc = "";
				// $officeLat = "";
				// $officeLng = "";
				// $shift = "";
				// $shiftName = "";
			
            }

        } else if ($q->num_rows() > 1) {
            $status = 0;
            $message = "NIK anda terdaftar lebih dari 2 kali, Hubungi admin untuk informasi lengkapnya.\n\nDevice ID: $deviceID";
			
				$serverEnv = "";
				$isActive = "";
				$employeeID = "";
				$fullname = "";
				$typeId = "";
				// $officeCode = "";
				// $officeLoc = "";
				// $officeLat = "";
				// $officeLng = "";
				// $shift = "";
				// $shiftName = "";
				
        } else {
            $status = 0;
            $message = "NIK anda tidak terdaftar, Hubungi admin untuk informasi lengkapnya.\n\nDevice ID: $deviceID";
			
				$serverEnv = "";
				$isActive = "";
				$employeeID = "";
				$fullname = "";
				$typeId = "";
				// $officeCode = "";
				// $officeLoc = "";
				// $officeLat = "";
				// $officeLng = "";
				// $shift = "";
				// $shiftName = "";
				
        }
      
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'SERVER_ENV' => $serverEnv,
            'EMPLOYEE_ID' => $employeeID,
            'FULLNAME' => $fullname,
            'TYPE_ID' => $typeId
			// 'OFFICECODE' => $officeCode,
			// 'OFFICELOC' => $officeLoc,
			// 'OFFICELAT' => $officeLat,
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
                $this->insertError($logErrCode, $err["message"], $apiName, $employeeID);
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
                $this->insertError($logErrCode, $message, $apiName, $nik);
                $status = 401; $message = "Environment Id anda berubah, session dan data aplikasi anda dihapus";
            }
            
            if ($rows->TYPE_ID != $typeId) {
                $logErrCode = "IU01"; $message = "Token typeId: ".$typeId . " DB typeId: ".$rows->TYPE_ID;
                $this->insertError($logErrCode, $message, $apiName, $nik);
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
                $this->insertError($logErrCode, $message, $apiName, $nik);
                $status = 401; $message = "DeviceID anda sudah berubah, session dan data aplikasi anda dihapus";
            }
        } else {
            $logErrCode = "IU03"; $message = "Token NIK: ".$nik . " tidak aktif ";
            $this->insertError($logErrCode, $message, $apiName, $nik);
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

	
        if ($apkVersion == "0.2.2") {
			
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
	

	

	
}
