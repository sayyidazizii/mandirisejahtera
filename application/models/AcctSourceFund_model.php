<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctSourceFund_model extends CI_Model {
		var $table = "acct_source_fund";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getDataAcctSourceFund(){
			$this->db->select('acct_source_fund.source_fund_id, acct_source_fund.source_fund_code, acct_source_fund.source_fund_name');
			$this->db->from('acct_source_fund');
			$this->db->where('acct_source_fund.data_state', 0);
			$result = $this->db->get()->result_array();
			// print_r($result);exit;
			return $result;
		}
		
		public function insertAcctSourceFund($data){
			// print_r($data);exit;
			// $query = $this->db->insert('acct_source_fund',$data);
			if($this->db->insert('acct_source_fund',$data)){
				return true;
			}else{
				return false;
			}
		}
		
		public function getAcctSourceFund_Detail($source_fund_id){
			$this->db->select('acct_source_fund.source_fund_id, acct_source_fund.source_fund_code, acct_source_fund.source_fund_name');
			$this->db->from('acct_source_fund');
			$this->db->where('acct_source_fund.source_fund_id', $source_fund_id);
			return $this->db->get()->row_array();
		}
		
		public function updateAcctSourceFund($data){
			$this->db->where("source_fund_id",$data['source_fund_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function deleteAcctSourceFund($source_fund_id){
			$this->db->where("source_fund_id",$source_fund_id);
			$query = $this->db->update($this->table, array('data_state'=>1));
			if($query){
				return true;
			}else{
				return false;
			}
		}
	}
?>