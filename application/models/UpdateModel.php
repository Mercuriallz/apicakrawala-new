<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UpdateModel extends CI_Model {
 
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
 

	
	// Function to update record in table
	public function update_pkwt($data, $id){
		$this->db->where('contract_id', $id);
		if( $this->db->update('xin_employee_contract',$data)) {
			return true;
		} else {
			return false;
		}		
	}
}
?>