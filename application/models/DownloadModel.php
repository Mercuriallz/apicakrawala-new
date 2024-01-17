<?php
defined('BASEPATH') or exit('No direct script access allowed');
class DownloadModel extends CI_Model
{

    protected $CI;

    public function __construct()
    {
        parent::__construct();
        $this->CI = &get_instance();
        $this->CI->load->model('ErrorModel');
        $this->CI->load->library('ora');
    }


    public function getCustomerArea($areaid)
    {
        $status = 1;
        $message = 'Data Customer Area';
        $data = [];


        $q = $this->db->query("
				 
				SELECT cust.customer_id, cust.customer_name, cust.address, cust.latitude, cust.longitude, cust.village_id, vil.name AS vil_name, cust.district_id, dist.name AS dist_name, cust.city_id, city.name AS city_name
				FROM xin_customer cust
				LEFT JOIN mt_villages vil ON vil.id = cust.village_id
				LEFT JOIN mt_districts dist ON dist.id = cust.district_id
				LEFT JOIN mt_regencies city ON city.id = cust.city_id
				WHERE cust.district_id = '$areaid'
				
			");


        if ($q->num_rows()) {
            $data = $q->result();
        } else {
            $status = 1;
            $message = 'Customer Tidak Tersedia';
        }
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }


    public function getVillageSearch($keys) {
        $status = 1; $message = 'Data Village All'; $data = [];
	
			$q = $this->db->query("
				SELECT village.id AS vill_id, dist.id AS dist_id, city.id AS city_id, CONCAT(village.name,', Kec. ', dist.name, ' - ', city.name) AS village_name
FROM mt_villages village
LEFT JOIN mt_districts dist ON dist.id = village.district_id
LEFT JOIN mt_regencies city ON city.id = dist.regency_id
WHERE CONCAT(village.name,', Kec. ', dist.name, ' - ', city.name) LIKE '%$keys%'
			");
		

        if ($q->num_rows()) {
            $data = $q->result();
        } else {
            $status = 1; $message = 'Customer Tidak Tersedia';
        } 
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }


    public function getVillages($area1, $area2, $area3)
    {
        $status = 1;
        $message = 'Data Desa/Kelurahan';
        $data = [];


        if ($area2 == null) {
            $q = $this->db->query("
				SELECT vill.id, vill.name, dist.id AS dist_id, rege.id AS city_id, rege.province_id AS prov_id
				FROM mt_villages vill
				LEFT JOIN mt_districts dist ON dist.id = vill.district_id
				LEFT JOIN mt_regencies rege ON rege.id = dist.regency_id
				WHERE rege.province_id IN (
					SELECT province_id
					FROM mt_regencies WHERE id IN('$area1'))
				ORDER BY vill.name ASC;

			");
        } else if ($area3 == null) {
            $q = $this->db->query("
				SELECT vill.id, vill.name, dist.id AS dist_id, rege.id AS city_id, rege.province_id AS prov_id
				FROM mt_villages vill
				LEFT JOIN mt_districts dist ON dist.id = vill.district_id
				LEFT JOIN mt_regencies rege ON rege.id = dist.regency_id
				WHERE rege.province_id IN (
					SELECT province_id
					FROM mt_regencies WHERE id IN('$area1','$area2'))
				ORDER BY vill.name ASC;

			");
        } else {
            $q = $this->db->query("
				SELECT vill.id, vill.name, dist.id AS dist_id, rege.id AS city_id, rege.province_id AS prov_id
				FROM mt_villages vill
				LEFT JOIN mt_districts dist ON dist.id = vill.district_id
				LEFT JOIN mt_regencies rege ON rege.id = dist.regency_id
				WHERE rege.province_id IN (
					SELECT province_id
					FROM mt_regencies WHERE id IN('$area1','$area2','$area3'))
				ORDER BY vill.name ASC;
				
			");
        }


        if ($q->num_rows()) {
            $data = $q->result();
        } else {
            $status = 1;
            $message = 'Desa/Kelurahan Tidak Tersedia';
        }
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }

    public function getCallPlan($employee_id, $date_callplan) {
        $status = 1;
        $message = 'Call Plan Data';
        $data = [];

        $q = $this->db->query("
        SELECT cp.secid, cp.customer_id, cus.customer_name, cus.owner_name, cus.no_contact, cus.address, cus.village_id, cus.district_id, cus.city_id, cus.photo, cus.latitude, cus.longitude
        FROM `tx_callplan` cp
        LEFT JOIN xin_user_mobile mo on mo.employee_id = cp.employee_id
        LEFT join xin_customer cus on cus.customer_id = cp.customer_id
        WHERE cp.employee_id = '$employee_id'
        AND cp.date_callplan = '$date_callplan'
        ORDER BY cus.customer_name;");

if ($q->num_rows()) {
    $data = $q->result();
} else {
    $status = 0;
    $message = 'Data call plan tidak tersedia';
} 
return (object) [
    'STATUS' => $status,
    'MESSAGE' => $message,
    'DATA' => $data
];
    }

    public function getPriceTag($customer_id, $employee_id) {
        $status = 1;
        $message = 'Price Tag data';
        $data = [];

        $q = $this->db->query("
        SELECT tagcus.secid, tagcus.material_id, tagcus.price, tagcus.foto 
        FROM tx_price_tag tagcus
        LEFT JOIN xin_sku_material sku ON sku.kode_sku = tagcus.material_id
        LEFT JOIN xin_customer cus ON cus.customer_id = tagcus.customer_id
        WHERE tagcus.customer_id = '$customer_id' AND tagcus.employee_id = '$employee_id';");

        if ($q->num_rows()) {
            $data = $q->result();
        } else {
            $status = 0;
            $message = 'Price tag tidak tersedia';
        } 
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
        }

    public function getStockQuery($customer_id)
    {
        $status = 1;
        $message = 'stock data';
        $data = [];

        $q = $this->db->query("
        SELECT skucus.secid, skucus.material_id, sku.nama_material, skucus.customer_id, cus.customer_name, skucus.stock_date, skucus.stock_qty  
        FROM mp_sku_customer skucus
        LEFT JOIN xin_sku_material sku ON sku.kode_sku = skucus.material_id
        LEFT JOIN xin_customer cus ON cus.customer_id = skucus.customer_id
        WHERE skucus.customer_id = '$customer_id';");

        if ($q->num_rows()) {
            $data = $q->result();
        } else {
            $status = 0;
            $message = 'Stock tidak tersedia';
        } 
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }

    public function getSKUPro()
    {
        $status = 1;
        $message = 'Data SKU Material Project';
        $data = [];

        $q = $this->db->query("
                 
                SELECT * FROM xin_sku_material WHERE project = '40' ORDER BY nama_material ASC
                
            ");


        if ($q->num_rows()) {
            $data = $q->result();
        } else {
            $status = 1;
            $message = 'SKU Material Tidak Tersedia';
        }
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }


    public function getDistrict($area1, $area2, $area3)
    {
        $status = 1;
        $message = 'Data Kecamatan';
        $data = [];

        if ($area2 == null) {
            $q = $this->db->query("
				SELECT dist.id, dist.name, rege.id AS city_id, rege.province_id
				FROM mt_districts dist
				LEFT JOIN mt_regencies rege ON rege.id = dist.regency_id
				WHERE rege.province_id IN (
					SELECT province_id
					FROM mt_regencies WHERE id IN('$area1'))
				ORDER BY dist.name ASC;

			");
        } else if ($area3 == null) {
            $q = $this->db->query("
				SELECT dist.id, dist.name, rege.id AS city_id, rege.province_id
				FROM mt_districts dist
				LEFT JOIN mt_regencies rege ON rege.id = dist.regency_id
				WHERE rege.province_id IN (
					SELECT province_id
					FROM mt_regencies WHERE id IN('$area1','$area2'))
				ORDER BY dist.name ASC;
			");
        } else {
            $q = $this->db->query("
				SELECT dist.id, dist.name, rege.id AS city_id, rege.province_id
				FROM mt_districts dist
				LEFT JOIN mt_regencies rege ON rege.id = dist.regency_id
				WHERE rege.province_id IN (
					SELECT province_id
					FROM mt_regencies WHERE id IN('$area1','$area2', '$area3'))
				ORDER BY dist.name ASC;

			");
        }


        if ($q->num_rows()) {
            $data = $q->result();
        } else {
            $status = 1;
            $message = 'Kecamatan Tidak Tersedia';
        }
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }

    public function getRegency($area1, $area2, $area3)
    {
        $status = 1;
        $message = 'Data Kota/Kabupaten';
        $data = [];

        if ($area2 == null) {
            $q = $this->db->query("
				SELECT * FROM mt_regencies
				WHERE province_id IN (
				SELECT province_id
				FROM mt_regencies WHERE id IN('$area1'));
			");
        } else if ($area3 == null) {
            $q = $this->db->query("
				SELECT * FROM mt_regencies
				WHERE province_id IN (
				SELECT province_id
				FROM mt_regencies WHERE id IN('$area1','$area2'));
			");
        } else {
            $q = $this->db->query("
				SELECT * FROM mt_regencies
				WHERE province_id IN (
				SELECT province_id
				FROM mt_regencies WHERE id IN('$area1','$area2', '$area3'));
			");
        }




        if ($q->num_rows()) {
            $data = $q->result();
        } else {
            $status = 1;
            $message = 'Kota/Kabupaten Tidak Tersedia';
        }
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }

    // get employees list> reports
    public function getUserFoto()
    {
        return $query = $this->db->query("SELECT * FROM xin_trx_cio WHERE secid = '15';");
        // 15
        // 186104
    }

    // public function getSku($projectid, $subprojectid) {
    //     $status = 1; $message = 'Data SKU/Material'; $data = [];


    // 		$q = $this->db->query("
    // 			SELECT * FROM xin_sku_material
    // 		");




    //     if ($q->num_rows()) {
    //         $data = $q->result();
    //     } else {
    //         $status = 1; $message = 'SKU/Material Tidak Tersedia';
    //     } 
    //     return (object) [
    //         'STATUS' => $status,
    //         'MESSAGE' => $message,
    //         'DATA' => $data
    //     ];
    // }

    public function getHistoryCio($nip)
    {
        $status = 1;
        $message = 'Data History CIO';
        $data = [];

        $q = $this->db->query("
        SELECT cio.employee_id, cio.customer_id, cio.date_cio, a.customer_name, cio.datetimephone_in, cio.datetimephone_out FROM `tx_cio` cio
        LEFT JOIN xin_customer a ON a.customer_id = cio.customer_id 
        WHERE `employee_id` = '$nip' 
			");


        if ($q->num_rows()) {
            $data = $q->result();
        } else {
            $status = 1;
            $message = 'Data History CIO Tidak Tersedia';
        }
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }

    public function getSKU()
    {
        $status = 1;
        $message = 'Data SKU Material';
        $data = [];

        $q = $this->db->query("
				 
				SELECT * FROM xin_sku_material ORDER BY nama_material ASC
				
			");


        if ($q->num_rows()) {
            $data = $q->result();
        } else {
            $status = 1;
            $message = 'SKU Material Tidak Tersedia';
        }
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }

    public function getProvincy()
    {
        $status = 1;
        $message = 'Data Provinsi';
        $data = [];

        $q = $this->db->query("
				 
				SELECT * FROM mt_provinces ORDER BY NAME ASC
				
			");


        if ($q->num_rows()) {
            $data = $q->result();
        } else {
            $status = 1;
            $message = 'Provinsi Tidak Tersedia';
        }
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }

    public function getCustomerAreaDist($area1, $area2, $area3, $lat1, $lon1)
    {
        $status = 1;
        $message = 'Data Customer Area';
        $data = [];


        if ($area2 == null) {
            $q = $this->db->query("
				SELECT cust.customer_id, cust.customer_name, cust.address, cust.latitude, cust.longitude, cust.village_id, vil.name AS vil_name, cust.district_id, dist.name AS dist_name, cust.city_id, city.name AS city_name, ROUND(6353 * 2 * ASIN(SQRT( POWER(SIN(('$lat1' - (cust.latitude)) * pi()/180 / 2),2) + COS('$lat1' * pi()/180 ) * COS( (cust.latitude) *  pi()/180) * POWER(SIN(('$lon1' - cust.longitude) *  pi()/180 / 2), 2) )) * 1000, 2) as dista
				FROM xin_customer cust
				LEFT JOIN mt_villages vil ON vil.id = cust.village_id
				LEFT JOIN mt_districts dist ON dist.id = cust.district_id
				LEFT JOIN mt_regencies city ON city.id = cust.city_id
				WHERE city.id IN ('$area1')
				ORDER BY dista ASC
			");
        } else if ($area3 == null) {
            $q = $this->db->query("

				SELECT cust.customer_id, cust.customer_name, cust.address, cust.latitude, cust.longitude, cust.village_id, vil.name AS vil_name, cust.district_id, dist.name AS dist_name, cust.city_id, city.name AS city_name, ROUND(6353 * 2 * ASIN(SQRT( POWER(SIN(('$lat1' - (cust.latitude)) * pi()/180 / 2),2) + COS('$lat1' * pi()/180 ) * COS( (cust.latitude) *  pi()/180) * POWER(SIN(('$lon1' - cust.longitude) *  pi()/180 / 2), 2) )) * 1000, 2) as dista
				FROM xin_customer cust
				LEFT JOIN mt_villages vil ON vil.id = cust.village_id
				LEFT JOIN mt_districts dist ON dist.id = cust.district_id
				LEFT JOIN mt_regencies city ON city.id = cust.city_id
				WHERE city.id IN ('$area1','$area2')
				ORDER BY dista ASC

			");
        } else {
            $q = $this->db->query("

				SELECT cust.customer_id, cust.customer_name, cust.address, cust.latitude, cust.longitude, cust.village_id, vil.name AS vil_name, cust.district_id, dist.name AS dist_name, cust.city_id, city.name AS city_name, ROUND(6353 * 2 * ASIN(SQRT( POWER(SIN(('$lat1' - (cust.latitude)) * pi()/180 / 2),2) + COS('$lat1' * pi()/180 ) * COS( (cust.latitude) *  pi()/180) * POWER(SIN(('$lon1' - cust.longitude) *  pi()/180 / 2), 2) )) * 1000, 2) as dista
				FROM xin_customer cust
				LEFT JOIN mt_villages vil ON vil.id = cust.village_id
				LEFT JOIN mt_districts dist ON dist.id = cust.district_id
				LEFT JOIN mt_regencies city ON city.id = cust.city_id
				WHERE city.id IN ('$area1','$area2', '$area3')
				ORDER BY dista ASC

			");
        }


        if ($q->num_rows()) {
            $data = $q->result();
        } else {
            $status = 1;
            $message = 'Customer Tidak Tersedia';
        }
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }


    public function getCustomerByNIP($empid, $area1, $area2, $area3, $lat1, $lon1)
    {
        $status = 1;
        $message = 'Data Customer Area';
        $data = [];

        $q = $this->db->query("SELECT cust.customer_id, cust.customer_name, cust.address, cust.latitude, cust.longitude, cust.village_id, vil.name AS vil_name, cust.district_id, dist.name AS dist_name, cust.city_id, city.name AS city_name, ROUND(6353 * 2 * ASIN(SQRT( POWER(SIN(('$lat1' - (cust.latitude)) * pi()/180 / 2),2) + COS('$lat1' * pi()/180 ) * COS( (cust.latitude) *  pi()/180) * POWER(SIN(('$lon1' - cust.longitude) *  pi()/180 / 2), 2) )) * 1000, 2) as dista
				FROM xin_customer cust
				LEFT JOIN mt_villages vil ON vil.id = cust.village_id
				LEFT JOIN mt_districts dist ON dist.id = cust.district_id
				LEFT JOIN mt_regencies city ON city.id = cust.city_id
				WHERE cust.customer_id IN (SELECT DISTINCT(customer_id)
			FROM tx_cio WHERE employee_id = '$empid'
			AND datediff(DATE_ADD(date_cio, INTERVAL 30 DAY), CURDATE()) > 0)
							ORDER BY dista ASC");

        if ($q->num_rows()) {
            $data = $q->result();
        } else {
            $status = 1;
            $message = 'Customer Tidak Tersedia';
        }

        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }


    public function getCustomerSearch($keys)
    {
        $status = 1;
        $message = 'Data Customer Area';
        $data = [];

        $q = $this->db->query("
				SELECT * FROM xin_customer WHERE customer_name LIKE '%$keys%'
			");


        if ($q->num_rows()) {
            $data = $q->result();
        } else {
            $status = 1;
            $message = 'Customer Tidak Tersedia';
        }
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }

    public function getLastAttend($nik, $phoneDate, $shift)
    {
        $status = 1;
        $message = 'Data Last Attend';
        $data = [];
        $yesterday = date('Y-m-d', strtotime("-1 days"));



        if ($shift == 1) {

            $q = $this->db->query("
				SELECT 
				TO_CHAR(SHIFT_DATE,'YYYY-MM-DD') AS SHIFT_DATEFORMAT,
				TO_CHAR(SHIFT_DATE,'DD-MM-YYYY') AS SHIFT_DATE, 
				CONCAT(CONCAT(CONCAT(CONCAT(TO_CHAR(PHONE_DATETIME,'HH24'),':'),TO_CHAR(PHONE_DATETIME,'MI')),':'),TO_CHAR(PHONE_DATETIME,'SS'))  AS TIMES, 
				VKBUR,
				STATUS_ID
				FROM ATD_TR_ATTEND
				WHERE TO_CHAR(SHIFT_DATE,'YYYY-MM-DD')  = '$phoneDate' 
				AND EMPLOYEE_ID = '$nik' 
				AND SHIFT_ID = '$shift'
			");
        } else {

            $q1 = $this->db->query("
				SELECT SHIFT_DATE 
				FROM ATD_TR_ATTEND 
				WHERE TO_CHAR(SHIFT_DATE,'YYYY-MM-DD')  = '$yesterday' 
				AND EMPLOYEE_ID = '$nik'
				AND SHIFT_ID = '$shift'
			");


            if ($q1->num_rows() == 1) {

                $q = $this->db->query("
					SELECT 
					TO_CHAR(SHIFT_DATE,'YYYY-MM-DD') AS SHIFT_DATEFORMAT,
					TO_CHAR(SHIFT_DATE,'DD-MM-YYYY') AS SHIFT_DATE, 
					CONCAT(CONCAT(CONCAT(CONCAT(TO_CHAR(PHONE_DATETIME,'HH24'),':'),TO_CHAR(PHONE_DATETIME,'MI')),':'),TO_CHAR(PHONE_DATETIME,'SS'))  AS TIMES, 
					VKBUR,
					STATUS_ID
					FROM ATD_TR_ATTEND
					WHERE TO_CHAR(SHIFT_DATE,'YYYY-MM-DD')  = '$yesterday' 
					AND EMPLOYEE_ID = '$nik' 
					AND SHIFT_ID = '$shift'
				");
            } else {

                $q = $this->db->query("
					SELECT 
					TO_CHAR(SHIFT_DATE,'YYYY-MM-DD') AS SHIFT_DATEFORMAT,
					TO_CHAR(SHIFT_DATE,'DD-MM-YYYY') AS SHIFT_DATE, 
					CONCAT(CONCAT(CONCAT(CONCAT(TO_CHAR(PHONE_DATETIME,'HH24'),':'),TO_CHAR(PHONE_DATETIME,'MI')),':'),TO_CHAR(PHONE_DATETIME,'SS'))  AS TIMES, 
					VKBUR,
					STATUS_ID
					FROM ATD_TR_ATTEND
					WHERE TO_CHAR(SHIFT_DATE,'YYYY-MM-DD')  = '$phoneDate' 
					AND EMPLOYEE_ID = '$nik' 
					AND SHIFT_ID = '$shift'
				");
            }
        }

        if ($q->num_rows()) {
            $data = $q->result();
        } else {
            $status = 1;
            $message = 'Hari ini belum ada absensi';
        }
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }

    public function getHistory($nik)
    {
        $status = 1;
        $message = 'Data History Attend';
        $data = [];


        $q = $this->db->query("
				 
				 SELECT TO_CHAR(trdepo.SHIFT_DATE, 'DD-MM-YYYY') AS TANGGAL,
					CASE WHEN muser.VKBUR IN ('90L0','90T1','90T3','90S2','90S3')
					THEN  
						CONCAT(CONCAT(CONCAT(CONCAT(TO_CHAR((trdepo.CREATEDON+INTERVAL '1' HOUR),'HH24'),':'),TO_CHAR(trdepo.CREATEDON,'MI')),':'),TO_CHAR(trdepo.CREATEDON,'SS'))    
					ELSE 
						CONCAT(CONCAT(CONCAT(CONCAT(TO_CHAR((trdepo.CREATEDON+INTERVAL '0' HOUR),'HH24'),':'),TO_CHAR(trdepo.CREATEDON,'MI')),':'),TO_CHAR(trdepo.CREATEDON,'SS'))
					END 
					AS TIMES,
				STATUS_ID,
				SHIFT_ID
				FROM (
					  SELECT * FROM ATD_TR_ATTEND
					  WHERE EMPLOYEE_ID = '$nik' ORDER BY PHONE_DATETIME DESC
					  ) trdepo
				LEFT JOIN ATD_MT_USERMOBILE muser ON muser.EMPLOYEE_ID = trdepo.EMPLOYEE_ID
				WHERE ROWNUM <= 14
				
			");


        if ($q->num_rows()) {
            $data = $q->result();
        } else {
            $status = 1;
            $message = 'Hari ini belum ada absensi';
        }
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }


    public function isAlreadyCin($date_cio, $employee_id, $customer_id)
    {
        $status = 1;
        $message = 'Data History Check IN';
        $data = [];

        $q = $this->db->query("

				SELECT aa.employee_id, aa.first_name, aa.c_io, aa.radius, aa.distance, aa.dateat, aa.timeat, aa.latitude, aa.longitude, aa.foto FROM (
					SELECT 
					trx.employee_id, 
					emp.first_name,
					trx.customer_id, 
					trx.c_io, 
					trx.radius, 
					trx.distance, 
					date_format(trx.datetime_phone, '%d-%m-%Y') AS dateat, 
					date_format(trx.datetime_phone, '%H:%i') AS timeat, 
					trx.latitude, 
					trx.longitude,
					'-' as foto
					FROM xin_trx_cio trx
					LEFT JOIN xin_employees emp ON emp.employee_id = trx.employee_id
					WHERE trx.employee_id = '$employee_id'
					AND trx.customer_id = '$customer_id'
					AND trx.c_io = '1'
					AND date_format(trx.datetime_phone, '%Y-%m-%d') = '$date_cio'
					ORDER BY trx.createdon
					LIMIT 1) aa

				UNION

					SELECT bb.employee_id, bb.first_name, bb.c_io, bb.radius, bb.distance, bb.dateat, bb.timeat, bb.latitude, bb.longitude, bb.foto FROM (
					SELECT 
					trx.employee_id, 
					emp.first_name,
					trx.customer_id, 
					trx.c_io, 
					trx.radius, 
					trx.distance, 
					date_format(trx.datetime_phone, '%d-%m-%Y') AS dateat, 
					date_format(trx.datetime_phone, '%H:%i') AS timeat, 
					trx.latitude, 
					trx.longitude,
					'-' as foto
					FROM xin_trx_cio trx
					LEFT JOIN xin_employees emp ON emp.employee_id = trx.employee_id
					WHERE trx.employee_id = '$employee_id'
					AND trx.customer_id = '$customer_id'
					AND trx.c_io = '2'
					AND date_format(trx.datetime_phone, '%Y-%m-%d') = '$date_cio'
					ORDER BY trx.createdon
					LIMIT 1) bb

			");

        // $q->num_rows();
        if ($q->num_rows()) {
            $data = $q->result();
        } else {
            $status = 0;
            $message = 'Hari ini belum ada absensi';
        }
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }


    public function isAlreadyOrder($date_cio, $employee_id, $customer_id)
    {
        $status = 1;
        $message = 'Data History Order';
        $data = [];

        $q = $this->db->query("

				SELECT morder.secid, morder.customer_id, morder.employee_id, morder.material_id, sku.nama_material, 
morder.order_date, morder.qty, morder.price, (morder.qty * morder.price) as total
FROM xin_mobile_order morder
LEFT JOIN xin_sku_material sku ON sku.kode_sku = morder.material_id
WHERE morder.customer_id = '$customer_id'
AND morder.employee_id = '$employee_id'
AND date_format(morder.order_date, '%Y-%m-%d') = '$date_cio'
			");

        // $q->num_rows();
        if ($q->num_rows()) {
            $data = $q->result();
        } else {
            $status = 0;
            $message = 'Hari ini belum ada absensi';
        }
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }

    public function isAlreadyCout($date_cio, $employee_id, $customer_id)
    {
        $status = 1;
        $message = 'Data History Check OUT';
        $data = [];

        $q = $this->db->query("

				SELECT employee_id,customer_id, c_io, radius, distance, 
				date_format(datetime_phone, '%d-%m-%Y') AS dateat, 
				date_format(datetime_phone, '%H:%i') AS timeat, latitude, longitude
				FROM xin_trx_cio
				WHERE employee_id = '$employee_id'
				AND customer_id = '$customer_id'
				AND c_io = '2'
				AND date_format(datetime_phone, '%d-%m-%Y') = '$date_cio'
				ORDER BY createdon
                LIMIT 1
				
			");

        // $q->num_rows();
        if ($q->num_rows()) {
            $data = $q->result();
        } else {
            $status = 0;
            $message = 'Hari ini belum ada absensi';
        }
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }

    public function insertNewCustomerLocation($api, $sid, $customerno, $lat, $lng)
    {
        $status = 1;
        $message = 'Sukses request new location';
        $data = new stdClass();
        $errHeader = null;
        $isAlreadyEntry = 0;
        $reqno = 0;

        $q = $this->db->query("SELECT 1
            FROM GL_TR_CUSTOMER_LOCATION_REQ
            WHERE isapproved IS NULL AND custno = '$customerno'
        ");

        if ($q->num_rows()) {
        } else {
            $q2 = $this->db->query("SELECT NVL(MAX(reqno)+1,0) AS REQNO 
                FROM GL_TR_CUSTOMER_LOCATION_REQ
                WHERE custno = '$customerno'
            ");

            if ($q2->num_rows()) {
                $rows = $q2->row();
                $reqno = $rows->REQNO;
                $q2->free_result();
            }

            $stmt = oci_parse($this->db->conn_id, "INSERT INTO GL_TR_CUSTOMER_LOCATION_REQ(SID,CUSTNO,LAT,LNG,REQNO) 
                VALUES(:pi_sid,:pi_custno,:pi_lat,:pi_lng, :pi_reqno)
            ");

            oci_bind_by_name($stmt, ':pi_sid', $sid, 8, SQLT_CHR);
            oci_bind_by_name($stmt, ':pi_custno', $customerno, 10, SQLT_CHR);
            oci_bind_by_name($stmt, ':pi_lat', $lat, 18, SQLT_LNG);
            oci_bind_by_name($stmt, ':pi_lng', $lng, 18, SQLT_LNG);
            oci_bind_by_name($stmt, ':pi_reqno', $reqno, 5, SQLT_INT);

            $r = oci_execute($stmt, OCI_NO_AUTO_COMMIT);
            if (!$r) {
                $err = oci_error($stmt);
                $isConstraint = $this->CI->ora->isConstraint($err['message']);
                if (!$isConstraint) {
                    $errHeader = $err["message"];
                } else {
                    $isAlreadyEntry = 1;
                }
            }
            oci_free_statement($stmt);


            if (is_null($errHeader)) {
                if (!$isAlreadyEntry) {
                    oci_commit($this->db->conn_id);
                } else {
                    oci_rollback($this->db->conn_id);
                }
            } else {
                oci_rollback($this->db->conn_id);
                $status = 0;
                $message = 'Error insert request location : ' . $errHeader;
                $this->CI->ErrorModel->insertError('ADR_INSERT_STOCK',  $message, $sid);
            }
        }



        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }

    public function getOffice($nik)
    {
        $status = 1;
        $message = 'Data History Attend';
        $data = [];


        $q = $this->db->query("
			
SELECT a.vkbur AS OFFICE_CODE, b.BEZEI AS OFFICE_NAME, nvl(c.LAT,0) AS LATITUDE, nvl(c.LNG,0) AS LONGITUDE
from ATD_MT_USERMOBILE a
LEFT JOIN 	
	(	
		SELECT VKBUR, BEZEI from SAP_TVKBT
	) b ON b.vkbur = a.vkbur
LEFT JOIN GL_MT_SALESOFFICE_LOCATION c ON c.SALESOFFICEID = a.VKBUR
WHERE a.EMPLOYEE_ID = '$nik'
UNION
SELECT x.CUSTOMERID AS OFFICE_CODE, y.NAME1 AS OFFICE_NAME, nvl(z.LAT,0) AS LATITUDE, nvl(z.LNG,0) AS LONGITUDE
FROM ATD_MT_EXTRA_LOCATION x
LEFT JOIN SAP_KNA1 y ON y.KUNNR = x.CUSTOMERID
LEFT JOIN GL_MT_CUSTOMER_LOCATION z ON z.CUSTNO = x.CUSTOMERID
WHERE x.EMPLOYEEID = '$nik'
AND TYPE = '0'
UNION
SELECT x.CUSTOMERID AS OFFICE_CODE, b.BEZEI AS OFFICE_NAME, nvl(c.LAT,0) AS LATITUDE, nvl(c.LNG,0) AS LONGITUDE
FROM ATD_MT_EXTRA_LOCATION x
LEFT JOIN SAP_TVKBT b ON b.vkbur = x.CUSTOMERID
LEFT JOIN GL_MT_SALESOFFICE_LOCATION c ON c.SALESOFFICEID = x.CUSTOMERID
WHERE x.EMPLOYEEID = '$nik'
AND TYPE = '1'
UNION
SELECT x.CUSTOMERID AS OFFICE_CODE, b.NAME1 AS OFFICE_NAME, nvl(c.LAT,0) AS LATITUDE, nvl(c.LNG,0) AS LONGITUDE
FROM ATD_MT_EXTRA_LOCATION x
LEFT JOIN SAP_LFA1 b ON b.lifnr = x.CUSTOMERID
LEFT JOIN GL_MT_VENDOR_LOCATION c ON c.VENDORID = x.CUSTOMERID
WHERE x.EMPLOYEEID = '$nik'
AND TYPE = '2'
UNION

SELECT x.CUSTOMERID AS OFFICE_CODE, b.NAME AS OFFICE_NAME, nvl(c.LAT,0) AS LATITUDE, nvl(c.LNG,0) AS LONGITUDE
FROM ATD_MT_EXTRA_LOCATION x
LEFT JOIN GL_MT_CUSTOMER b ON b.CUSTNO = x.CUSTOMERID
LEFT JOIN GL_MT_CUSTOMER_LOCATION c ON c.CUSTNO = x.CUSTOMERID
WHERE x.EMPLOYEEID = '$nik'
AND TYPE = '3'

ORDER BY OFFICE_CODE DESC
				
			");


        if ($q->num_rows()) {
            $data = $q->result();
        } else {
            $status = 1;
            $message = 'Hari ini belum ada absensi';
        }
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }


    public function getHistoryPeriode($nik, $dateFrom, $dateUntil)
    {
        $status = 1;
        $message = 'Data History Attend';
        $data = [];


        $q = $this->db->query("
				 
				SELECT TO_CHAR(trdepo.SHIFT_DATE, 'DD-MM-YYYY') AS TANGGAL,
					CASE WHEN muser.VKBUR IN ('90L0','90T1','90T3','90S2','90S3')
					THEN  
						CONCAT(CONCAT(CONCAT(CONCAT(TO_CHAR((trdepo.CREATEDON+INTERVAL '1' HOUR),'HH24'),':'),TO_CHAR(trdepo.CREATEDON,'MI')),':'),TO_CHAR(trdepo.CREATEDON,'SS'))    
					ELSE 
						CONCAT(CONCAT(CONCAT(CONCAT(TO_CHAR((trdepo.CREATEDON+INTERVAL '0' HOUR),'HH24'),':'),TO_CHAR(trdepo.CREATEDON,'MI')),':'),TO_CHAR(trdepo.CREATEDON,'SS'))
					END 
					AS TIMES,
				STATUS_ID,
				SHIFT_ID
				FROM (
                    SELECT * FROM ATD_TR_ATTEND WHERE EMPLOYEE_ID = '$nik' ORDER BY PHONE_DATETIME DESC
                    ) trdepo
				LEFT JOIN ATD_MT_USERMOBILE muser ON muser.EMPLOYEE_ID = trdepo.EMPLOYEE_ID
				WHERE TRUNC(PHONE_DATETIME) 
                BETWEEN TO_DATE('$dateFrom','dd-mm-YYYY') 
                AND TO_DATE('$dateUntil','dd-mm-YYYY')
				
			");


        if ($q->num_rows()) {
            $data = $q->result();
        } else {
            $status = 1;
            $message = 'Hari ini belum ada absensi';
        }
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }




    public function getReason()
    {
        $status = 1;
        $message = 'Data Reason';
        $data = [];
        $q = $this->db->query("
				SELECT REASON_ID, REASON_NAME
				FROM ATD_MT_REASON ORDER BY REASON_ID ASC
        ");
        if ($q->num_rows()) {
            $data = $q->result();
        } else {
            $status = 0;
            $message = 'Data Reason tidak ditemukan';
        }
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }

    function insertCio($data)
    {

        $result = $this->db->insert('xin_trx_cio', $data);
        $insert_id = $this->db->insert_id();
        $error = $this->db->_error_message();
        return $error;
    }

    public function getUserDesktop()
    {
        $status = 1;
        $message = 'Data User';
        $data = [];
        //SELECT USERNAME, PASSWORD, FULLNAME, PLANTID FROM TBG_MT_USER_DESKTOP
        /*$q = $this->db->query("SELECT USERNAME, PASSWORD, FULLNAME, PLANTID FROM TBG_MT_USER_DESKTOP");
			
        if ($q->num_rows()) {
             $data = $q->result();
        } else {
             $status = 1; $message = 'Tidak ada daftar user';
        } 
		 */
        return (object) [
            'STATUS' => $status,
            'MESSAGE' => $message,
            'DATA' => $data
        ];
    }
}
