<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class vendor_model extends CI_Model {
		var $table = "core_vendor";
		
		public function vendor_model(){
			parent::__construct();
			$this->CI = get_instance();
		}
		
		public function get_list(){
			$this->db->select('*')->from('core_vendor');
			// $this->db->where('data_state', '0');
			$this->db->where('vendor_id != 0');
			$result = $this->db->get()->result_array();
			return $result;
		}
		
		public function insertvendor($data){
			return $this->db->insert('core_vendor',$data);
		}
		
		public function getdetail($id){
			$this->db->select('*')->from($this->table);
			$this->db->where('vendor_id',$id);
			return $this->db->get()->row_array();
		}
		
		public function updatevendor($data){
			$this->db->where('vendor_id',$data['vendor_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		// public function deletelocation($id){
			// $this->db->where("vendor_id",$id);
			// $query = $this->db->update($this->table, array("data_state"=>'1'));
			// if($query){
			// return true;
			// }else{
			// return false;
			// }
		// }
		
		public function deletelocation($id){
			$query = $this->db->query("DELETE FROM `core_vendor` WHERE (`vendor_id`='".$id."')");
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		// public function getNewCode(){
			// $query = $this->db->query("SELECT getNewCodeVendor() as vendor_code")->row_array();
			// return $query['vendor_code'];
		// }
		
		// public function getreferencelast(){
				// $this->db->select('reference_last_digit')->from('preference_reference');
				// $this->db->where('data_state','0');
				// $this->db->where('reference_id','4');
				// $result = $this->db->get()->row_array();
				// return $result['reference_last_digit'];
			// }
		
		// public function UpdateReference(){
				// $data= array (
					// 'reference_last_digit'				=> $this->getreferencelast()+1,
				// );
				// $this->db->where("reference_id",'4');
				// $query = $this->db->update('preference_reference', $data);
				// if($query){
					// return true;
				// }else{
					// return false;
				// }
		// }
	}
?>