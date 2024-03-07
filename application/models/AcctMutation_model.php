<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctMutation_model extends CI_Model {
		var $table = "acct_mutation";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getDataAcctMutation(){
			$this->db->select('acct_mutation.mutation_id, acct_mutation.mutation_code, acct_mutation.mutation_name, acct_mutation.mutation_function, acct_mutation.mutation_status, acct_mutation.mutation_module');
			$this->db->from('acct_mutation');
			$this->db->where('acct_mutation.data_state', 0);
			$result = $this->db->get()->result_array();
			// print_r($result);exit;
			return $result;
		}
		
		public function insertAcctMutation($data){
			// print_r($data);exit;
			// $query = $this->db->insert('acct_mutation',$data);
			if($this->db->insert('acct_mutation',$data)){
				return true;
			}else{
				return false;
			}
		}
		
		public function getAcctMutation_Detail($mutation_id){
			$this->db->select('acct_mutation.mutation_id, acct_mutation.mutation_code, acct_mutation.mutation_name, acct_mutation.mutation_function, acct_mutation.mutation_status');
			$this->db->from('acct_mutation');
			$this->db->where('acct_mutation.mutation_id', $mutation_id);
			return $this->db->get()->row_array();
		}
		
		public function updateAcctMutation($data){
			$this->db->where("mutation_id",$data['mutation_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function deleteAcctMutation($mutation_id){
			$this->db->where("mutation_id",$mutation_id);
			$query = $this->db->update($this->table, array('data_state'=>1));
			if($query){
				return true;
			}else{
				return false;
			}
		}
	}
?>