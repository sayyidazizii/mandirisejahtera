<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class Core_account_Officer_model extends CI_Model {
		var $table = "core_account_officer";
		
		public function CoreOffice_model(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getDataCoreAccountOfficer(){
			$this->db->select('*');
			$this->db->from('core_account_officer');
			$this->db->where('data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}
		
	}
?>