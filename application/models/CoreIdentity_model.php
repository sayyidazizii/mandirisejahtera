<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class CoreIdentity_model extends CI_Model {
		var $table = "core_identity";
		
		public function CoreIdentity_model(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getDataCoreIdentity(){
			$this->db->select('identity_id, identity_code, identity_name');
			$this->db->from('core_identity');
			$this->db->where('data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}
		
		public function insertCoreIdentity($data){
			$query = $this->db->insert('core_identity',$data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function getCoreIdentity_Detail($identity_id){
			$this->db->select('identity_id, identity_code, identity_name');
			$this->db->from('core_identity');
			$this->db->where('identity_id', $identity_id);
			return $this->db->get()->row_array();
		}
		
		public function updateCoreIdentity($data){
			$this->db->where("identity_id",$data['identity_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function deleteCoreIdentity($identity_id){
			$this->db->where("identity_id",$identity_id);
			$query = $this->db->update($this->table, array('data_state'=>1));
			if($query){
				return true;
			}else{
				return false;
			}
		}




	
	}
?>