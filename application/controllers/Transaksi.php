<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 *  @property ErrorModel $insertError;
 */

class Transaksi extends CI_Controller
{

    protected $CI;

    public function __construct()
    {
        parent::__construct();
        $this->CI = &get_instance();
        $this->CI->load->model('UserModel');
        $this->CI->load->model('DownloadModel');
        $this->CI->load->model('ErrorModel');
        $this->CI->load->helper('shared');
        $this->CI->load->library('rest');
        $this->CI->load->library('auth');
        $this->CI->load->library('sgf');
    }

    private function getDateOnly($dt)
    {
        return (string) date('d-m-Y', strtotime($dt));
    }

    private function compareDate($phonedt, $serverdt)
    {
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

    // private function getValidDate($datediff, $phonedt, $serverdt, $salesid) {

    //     if ($datediff->d) {
    //         $phonedt = date('Y-m-d 23:59:59 P', $timeDb);
    //         $phonedt = date_format(date_create($phonedt), 'd-m-Y');
    //         if ($phonedt != $serverdt) {
    //             $serverdt = date('d-m-Y', $timeDb);
    //         }
    //     }

    //     $serverdt = date("d-m-Y");
    //     $obj = (object) [
    //         'STATUS' => FALSE,
    //         'PHONE_DATE' => $phonedt,
    //         'SERVER_DATE' => $serverdt
    //     ];
    //     $q = $this->db->query("SELECT TO_CHAR(MAX(PHONE_DATE), 'YYYY-MM-DD') PHONE_DATE FROM SOA_PRECENSE
    //     WHERE SALESID = '$salesid' AND (STATUS_ID = 3 OR STATUS_ID = 5) ");

    //     if ($q->num_rows()) {
    //         $row = $q->row();
    //         $dateDiff = $this->compareDate($phonedt, $row->PHONE_DATE);
    //         if ($dateDiff) {
    //             $phonedt = date('Y-m-d 23:59:59 P', $timeDb);
    //             $phonedt = date_format(date_create($phonedt), 'd-m-Y');
    //             if ($phonedt != $serverdt) {
    //                 $serverdt = date('d-m-Y', $timeDb);
    //             }
    //         }
    //         $obj = (object) [
    //             'STATUS' => TRUE,
    //             'PHONE_DATE' => $phonedt,
    //             'SERVER_DATE' => $serverdt
    //         ];
    //     }
    //     return $obj;
    // }

    private function isNullField($data)
    {
        if (is_null($data) || trim($data) == '') {
            return true;
        } else {
            return false;
        }
    }


    public function pushNoo()
    {
        $apiName = "apicakrawala/transaksi/pushNoo";

        $customer_id = $this->CI->rest->input('customer_id');
        $customer_name = $this->CI->rest->input('customer_name');
        $owner_name = $this->CI->rest->input('owner_name');
        $no_contact = $this->CI->rest->input('no_contact');
        $address = $this->CI->rest->input('address');
        $village_id = $this->CI->rest->input('village_id');
        $district_id = $this->CI->rest->input('district_id');
        $city_id = $this->CI->rest->input('city_id');
        $latitude = $this->CI->rest->input('latitude');
        $longitude = $this->CI->rest->input('longitude');
        $photo = $this->CI->rest->input('photo');
        $createdby = $this->CI->rest->input('createdby');

        if ($customer_id == null) {
            $status = 0;
            $message = "Customer ID Kosong";
        } else {

            $sql = "INSERT INTO xin_customer (customer_id,customer_name,owner_name,no_contact,address,village_id,district_id,city_id,latitude,longitude,photo,createdby) 
                    VALUES ('$customer_id', '$customer_name','$owner_name','$no_contact','$address','$village_id','$district_id','$city_id','$latitude','$longitude','$photo','$createdby')";
            $result = $this->db->query($sql);
            $error = $this->db->error($sql);
            if ($result) {
                $status = 1;
                $message = "Success Insert";
            } else {
                $status = 0;
                $message = $error;
            }
        }
        $this->CI->rest->output($status, $message, new stdClass());
    }

    public function pushOrder()
    {
        $apiName = "apicakrawala/transaksi/pushOrder";

        $customer_id    = $this->CI->rest->input('customer_id');
        $employee_id    = $this->CI->rest->input('employee_id');
        $material_id    = $this->CI->rest->input('material_id');
        $order_date     = $this->CI->rest->input('order_date');
        $qty            = $this->CI->rest->input('qty');
        $price          = $this->CI->rest->input('total') / $this->CI->rest->input('qty');
        $total          = $this->CI->rest->input('total');
        $createdby      = $this->CI->rest->input('createdby');

        if ($customer_id == null) {
            $status = 0;
            $message = "Customer ID Kosong";
        } else {

            $sql = "INSERT INTO xin_mobile_order (customer_id,employee_id,material_id,order_date,qty,price,total,createdby) 
                    VALUES ('$customer_id', '$employee_id','$material_id','$order_date','$qty','$price','$total','$createdby')";
            $result = $this->db->query($sql);
            $error = $this->db->error($sql);
            if ($result) {
                $status = 1;
                $message = "Success Insert";
            } else {
                $status = 0;
                $message = $error;
            }
        }
        $this->CI->rest->output($status, $message, new stdClass());
    }

    // TIMBANGAN
    public function planning()
    {
        $apiName = "sgfattend/transaksi/planning";

        //$phoneDate = $this->CI->rest->input('do_date');
        $phoneDate = date("Y-m-d");

        //$datePh = date_create($phoneDate);

        $getHistory = $this->CI->DownloadModel->getPlanning($phoneDate);
        $this->CI->rest->output($getHistory->STATUS, $getHistory->MESSAGE, $getHistory->DATA);
    }


    public function sendDisplay()
{
    $apiName = "apicakrawala/transaksi/senddisplay";

    $display  = $this->CI->rest->input('display');
    $display2 = $this->CI->rest->input('display2');
    $display3 = $this->CI->rest->input('display3');
    $createdat  = $this->CI->rest->input('created_at');
    $createdby = $this->CI->rest->input('created_by');
    $nik = $this->CI->rest->input('nik');
    $id_toko = $this->CI->rest->input('id_toko');

    define('UPLOAD_DIR_DISPLAY', 'upload/display/');

    $img = str_replace('data:image/png;base64,', '', $display);
    $img2 = str_replace('data:image/png;base64,', '', $display2);
    $img3 = str_replace('data:image/png;base64,', '', $display3);

    $file1 = $this->saveImage($nik, $img);
    $file2 = $this->saveImage($nik, $img2);
    $file3 = $this->saveImage($nik, $img3);

    if (!$file1) {
        $this->CI->rest->output(0, "Wajib memasukkan 1 gambar", new stdClass());
    } else {
        $sql = "INSERT INTO tx_display (nik, display, display2, display3, created_at, created_by, id_toko)
        VALUES ('$nik', '$file1', '$file2', '$file3', '$createdat', '$createdby', '$id_toko')";

        $result = $this->db->query($sql);
        $error = $this->db->error($sql);

        if ($result) {
            $status = 1;
            $message = "Success insert display";
        } else {
            $status = 0;
            $message = $error;
        }
    }

    $this->CI->rest->output($status, $message, new stdClass());
}

private function saveImage($nik, $img)
{
    $image = str_replace(' ', '+', $img);
    $data = base64_decode($image);
    $file = UPLOAD_DIR_DISPLAY . $nik . '_' . uniqid() . '.png';
    if (file_put_contents($file, $data)) {
        return $file;
    } else {
        return false;
    }
}

public function sendCompetitor() {
    $employeeId = $this->CI->rest->input('employee_id');
    $customerId = $this->CI->rest->input('customer_id');
    $namaMaterial = $this->CI->rest->input('nama_material');
    $qty = $this->CI->rest->input('qty');
    $hargaNormal = $this->CI->rest->input('harga_normal');
    $hargaPromo = $this->CI->rest->input('harga_promo');
    $tanggalPromo = $this->CI->rest->input('tanggal_promo');
    $akhirPromo = $this->CI->rest->input('akhir_promo');
    $keteranganPromo = $this->CI->rest->input('keterangan_promo');
    $foto1 = $this->CI->rest->input('foto_1');
    $foto2 = $this->CI->rest->input('foto_2');

    define('UPLOAD_DIR_COMPETITOR', 'upload/competitor/');

    $imgcomp = str_replace('data:image/png;base64,', '', $foto1);
    $imgcomp2 = str_replace('data:image/png;base64,', '', $foto2);

    $file1  = $this->saveImageCompetitor($employeeId, $imgcomp);
    $file2 = $this->saveImageCompetitor($employeeId, $imgcomp2);

    if($customerId == null) {
        $this->CI->rest->output(0, "customer id tidak boleh kosong", new stdClass());

    } else {
        $sql = "INSERT INTO tx_competitor (employee_id, customer_id, nama_material, qty, harga_normal, harga_promo, tanggal_promo, akhir_promo, keterangan_promo, foto_1, foto_2)
        VALUES ('$employeeId', '$customerId', '$namaMaterial', '$qty', '$hargaNormal', '$hargaPromo', '$tanggalPromo', '$akhirPromo', '$keteranganPromo', '$file1', '$file2')
        ";

        $result = $this->db->query($sql);
        $error = $this->db->error($sql);

        if($result) {
            $status = 1;
            $message = "success insert competitor";
        } else {
            $status = 0;
            $message = $error;
        }
    }
    $this->CI->rest->output($status, $message, new stdClass());
}

private function saveImageCompetitor($employee_id, $img)
{
    $image = str_replace(' ', '+', $img);
    $data = base64_decode($image);
    $file = UPLOAD_DIR_COMPETITOR . $employee_id . '_' . uniqid() . '.png';
    if (file_put_contents($file, $data)) {
        return $file;
    } else {
        return false;
    }
}

    

public function sendPlanogram()
{
    $apiName = "apicakrawala/transaksi/sendplanogram";

    $planogram  = $this->CI->rest->input('planogram');
    $planogram2 = $this->CI->rest->input('planogram2');
    $planogram3 = $this->CI->rest->input('planogram3');
    $createdat  = $this->CI->rest->input('createdat');
    $createdby = $this->CI->rest->input('createdby');
    $nik = $this->CI->rest->input('nik');
    $id_toko = $this->CI->rest->input('id_toko');

    define('UPLOAD_DIR_PLANOGRAM', 'upload/planogram/');

    $img = str_replace('data:image/png;base64,', '', $planogram);
    $img2 = str_replace('data:image/png;base64,', '', $planogram2);
    $img3 = str_replace('data:image/png;base64,', '', $planogram3);

    $file1 = $this->saveImagePlanogram($nik, $img);
    $file2 = $this->saveImagePlanogram($nik, $img2);
    $file3 = $this->saveImagePlanogram($nik, $img3);

    if (!$file1) {
        $this->CI->rest->output(0, "Wajib memasukkan 1 gambar", new stdClass());
    } else {
        $sql = "INSERT INTO tx_planogram (nik, planogram, planogram2, planogram3, createdat, createdby, id_toko)
        VALUES ('$nik', '$file1', '$file2', '$file3', '$createdat', '$createdby', '$id_toko')";

        $result = $this->db->query($sql);
        $error = $this->db->error($sql);

        if ($result) {
            $status = 1;
            $message = "Success insert planogram";
        } else {
            $status = 0;
            $message = $error;
        }
    }

    $this->CI->rest->output($status, $message, new stdClass());
}

private function saveImagePlanogram($nik, $img)
{
    $image = str_replace(' ', '+', $img);
    $data = base64_decode($image);
    $file = UPLOAD_DIR_PLANOGRAM . $nik . '_' . uniqid() . '.png';
    if (file_put_contents($file, $data)) {
        return $file;
    } else {
        return false;
    }
}


    public function pushCheckIn() {
        $apiName = "apicakrawala/transaksi/pushCheckIn";


        $nik = $this->CI->rest->input('nik');
        $employee_id = $this->CI->rest->input('employee_id');
        $customerid = $this->CI->rest->input('customer_id');
        $jabatanid = $this->CI->rest->input('jabatan_id');
        $date_cio = $this->CI->rest->input('date_cio');
        $status_emp = $this->CI->rest->input('status_emp');
        $datetimephone_in = $this->CI->rest->input('datetimephone_in');
        $project_id = $this->CI->rest->input('project_id');
        $latitude_in = $this->CI->rest->input('latitude_in');
        $longitude_in = $this->CI->rest->input('longitude_in');
        $radius_in = $this->CI->rest->input('radius_in');
        $distance_in = $this->CI->rest->input('distance_in');
        $img = $this->CI->rest->input('foto_in');
        $keterangan = $this->CI->rest->input('keterangan');
        $apk = $this->CI->rest->input('apk');
        // $foto = '0';


        define('UPLOAD_DIR_IN', 'upload/cio/');
        // $img = $_POST['img'];
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $file = UPLOAD_DIR_IN . $employee_id . '_' . uniqid() . '.png';
        $success = file_put_contents($file, $data);

        if ($employee_id == null) {
            $status = 0;
            $message = "NIK Null";
            print_r($employee_id);
        } else {

            $sql = "INSERT INTO tx_cio (employee_id,customer_id,project_id,date_cio,jabatan_id,datetimephone_in,latitude_in,longitude_in,radius_in,distance_in, foto_in, status_emp, keterangan, apk) 
                    VALUES ('$employee_id', '$customerid','$project_id','$date_cio','$jabatanid','$datetimephone_in','$latitude_in','$longitude_in','$radius_in','$distance_in','$file', '$status_emp', '$keterangan', '$apk')";
            $result = $this->db->query($sql);
            $error = $this->db->error($sql);
            if ($result) {
                $status = 1;
                $message = "Success Insert";
            } else {
                $status = 0;
                $message = $error;
            }
        }
        $this->CI->rest->output($status, $message, new stdClass());
    }

    public function pushCheckOut() {
        $apiName = "apicakrawala/transaksi/pushCheckOut";

        $date_cio = $this->CI->rest->input('date_cio');
        $customer_id = $this->CI->rest->input('customer_id');
        $employee_id = $this->CI->rest->input('employee_id');
        $datetimephone_out = $this->CI->rest->input('datetimephone_out');
        $latitude_out = $this->CI->rest->input('latitude_out');
        $longitude_out = $this->CI->rest->input('longitude_out');
        $radius_out = $this->CI->rest->input('radius_out');
        $distance_out = $this->CI->rest->input('distance_out');
        $img = $this->CI->rest->input('foto_out');
        // $foto = '0';


        define('UPLOAD_DIR_OUT', 'upload/cio/');
        // $img = $_POST['img'];
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $file = UPLOAD_DIR_OUT . $employee_id . '_' . uniqid() . '.png';
        $success = file_put_contents($file, $data);

        if ($datetimephone_out == null) {
            $status = 0;
            $message = "Datetime Null";
            print_r($datetimephone_out);
        } else {

            $sql = "UPDATE tx_cio SET datetimephone_out = '$datetimephone_out', latitude_out = $latitude_out, longitude_out = $longitude_out, radius_out = '$radius_out', distance_out = '$distance_out', foto_out = '$file' WHERE employee_id = '$employee_id' AND date_cio = '$date_cio' AND customer_id = '$customer_id'";
            $result = $this->db->query($sql);
            $error = $this->db->error($sql);
            if ($result) {
                $status = 1;
                $message = "Success Insert";
            } else {
                $status = 0;
                $message = $error;
            }
        }
        $this->CI->rest->output($status, $message, new stdClass());
    }

    public function sendPriceTag() {
        $apiName = "apicakrawala/transaksi/sendPriceTag";
    
        $customerid = $this->CI->rest->input('customer_id');
        $employeeid = $this->CI->rest->input('employee_id');
        $materialid = $this->CI->rest->input('material_id');
        $price = $this->CI->rest->input('price');
        $created_at = $this->CI->rest->input('created_at');
        $nik = $this->CI->rest->input('nik');
        $img = $this->CI->rest->input('foto');

    
        define('UPLOAD_DIR_PRICETAG', 'upload/pricetag/');
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $file = UPLOAD_DIR_PRICETAG . $employeeid . '_' . uniqid() . '.png';
        $success = file_put_contents($file, $data);

        if($customerid == null) {
            $status = 0;
            $message = "Customer ID nya kosong";
            print_r($customerid);
        } else {
            $sql = "INSERT INTO tx_price_tag (material_id,employee_id,customer_id,price,foto,created_at)
            VALUES ('$materialid','$employeeid','$customerid','$price','$file','$created_at')";
            $result = $this->db->query($sql);
            $error = $this->db->error($sql);
            
            if($result) {
                $status = 1;
                $message = "Success insert price tag";
            } else {
                $status = 0;
                $message = $error;
            }
        }
        $this->CI->rest->output($status, $message, new stdClass());
    }

    public function insertStock () {
        $apiName = "api.traxes.id/transaksi/insertStock";

        $materialid = $this->CI->rest->input('material_id');
        $customerid = $this->CI->rest->input('customer_id');
        $stockdate = $this->CI->rest->input('stock_date');
        $stockqty = $this->CI->rest->input('stock_qty');

        if ($customerid == null) {
            $status = 0;
            $message = "Customer ID nya kosong";
            print_r($customerid);
        } else {
            $sql = "INSERT INTO mp_sku_customer (material_id,customer_id,stock_date,stock_qty) 
            VALUES ('$materialid','$customerid','$stockdate','$stockqty')";
            $result = $this->db->query($sql);
            $error= $this->db->error($sql);
            if($result) {
                $status = 1;
                $message = "Success insert";
            } else {
                $status = 0;
                $message = $error;
            }
        }
        $this->CI->rest->output($status, $message, new stdClass());
    }

    public function updateStock() {
        $apiName = "api.traxes.id/transaksi/updatestock";
        $materialid = $this->CI->rest->input('material_id');
        $customerid = $this->CI->rest->input('customer_id');
        $stockdate = $this->CI->rest->input('stock_date');
        $stockqty = $this->CI->rest->input('stock_qty');
        $createdat = $this->CI->rest->input('created_at');

        if($customerid == null) {
            $status = 0;
            $message = "Customer id kosong";
            print_r($customerid);
        } else {
            $sql = "UPDATE mp_sku_customer set stock_qty = '$stockqty', created_at = '$createdat', stock_date = '$stockdate' WHERE material_id = '$materialid' AND customer_id = '$customerid'";
            $result = $this->db->query($sql);
            $error = $this->db->error($sql);
            if ($result) {
                $status = 1;
                $message = "Success update stock";
            } else {
                $status = 0;
                $message = $error;
            }
        }
        $this->CI->rest->output($status, $message, new stdClass());


    }

    // public function getStock() {
    //     $customer_id = $this->CI->rest->input('customer_id');

    //     if ($customer_id == null) {
    //         $status = 0;
    //         $message = "Toko nya kosong";
    //         print_r($customer_id);
    //     } else {
    //         $sql = "SELECT skucus.secid, skucus.material_id, sku.nama_material, skucus.customer_id, cus.customer_name, skucus.stock_date, skucus.stock_qty  
    //         FROM `mp_sku_customer` skucus
    //         LEFT JOIN xin_sku_material sku ON sku.kode_sku = skucus.material_id
    //         LEFT JOIN xin_customer cus ON cus.customer_id = skucus.customer_id
    //         WHERE skucus.customer_id = '$customer_id'";
    //         $result = $this->db->query($sql);
    //         $error = $this->db->error($sql);
    //         if ($result) {
    //             $status = 1;
    //             $message = "Success Insert";
    //         } else {
    //             $status = 0;
    //             $message = $error;
    //         }
    //     }
    //     $this->CI->rest->output($status, $message, new stdClass());
    // }

    public function pushCio()
    {
        $apiName = "apicakrawala/transaksi/pushCio";


        $nik = $this->CI->rest->input('nik');
        $customerid = $this->CI->rest->input('customerid');
        $cio = $this->CI->rest->input('cio');
        $cio_date = $this->CI->rest->input('cio_date');
        $datetime_phone = $this->CI->rest->input('datetime_phone');
        $project_id = $this->CI->rest->input('project_id');
        $latitude = $this->CI->rest->input('latitude');
        $longitude = $this->CI->rest->input('longitude');
        $distance = $this->CI->rest->input('distance');
        $img = $this->CI->rest->input('foto');
        // $foto = '0';


        define('UPLOAD_DIR', 'upload/cio/');
        // $img = $_POST['img'];
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $file = UPLOAD_DIR . $nik . '_' . uniqid() . '.png';
        $success = file_put_contents($file, $data);

        if ($nik == null || $customerid == null) {
            $status = 0;
            $message = "NIK Null";
        } else {

            $sql = "INSERT INTO xin_trx_cio (employee_id,customer_id,c_io,cio_date,datetime_phone,project_id,latitude,longitude,distance, foto) 
                    VALUES ('$nik', '$customerid','$cio','$cio_date','$datetime_phone','$project_id','$latitude','$longitude','$distance','$file')";
            $result = $this->db->query($sql);
            $error = $this->db->error($sql);
            if ($result) {
                $status = 1;
                $message = "Success Insert";
            } else {
                $status = 0;
                $message = $error;
            }
        }
        $this->CI->rest->output($status, $message, new stdClass());
    }



    public function userdesktop()
    {
        $apiName = "sgfattend/transaksi/userdesktop";

        $getUserDesktop = $this->CI->DownloadModel->getUserDesktop();
        $this->CI->rest->output($getUserDesktop->STATUS, $getUserDesktop->MESSAGE, $getUserDesktop->DATA);
    }

    public function insertPhoto()
    {
        $apiName = "apicakrawala/transaksi/insertPhoto";


        $usermobile = $this->CI->DownloadModel->getUserFoto();

        foreach ($usermobile->result() as $r) {

            $secid = $r->secid;
            $img = $r->foto;
            $nik = $r->employee_id;

            define(UPLOAD_DIR, 'upload/cio/');
            // $img = $_POST['img'];
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            $file = UPLOAD_DIR . $nik . '_' . uniqid() . '.png';
            $success = file_put_contents($file, $data);



            $sql = "UPDATE xin_trx_cio SET foto = '$file' WHERE secid = '$secid';";
            $result = $this->db->query($sql);
            $error = $this->db->error($sql);
            if ($result) {
                $status = 1;
                $message = "Success Insert";
            } else {
                $status = 0;
                $message = $error;
            }

            // $data = array(
            // 'foto' => $file
            // );

            // // $data = $this->security->xss_clean($data);
            // $result = $this->Usersmobile_model->update_record($data,$secid);

        }


        $this->CI->rest->output($success, $message, new stdClass());
    }
}
