<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctBankAccount_model extends CI_Model {
		var $table = "acct_bank_account";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getDataAcctBankAccount(){
			$this->db->select('acct_bank_account.bank_account_id, acct_bank_account.bank_account_code, acct_bank_account.bank_account_name, acct_bank_account.account_id, acct_account.account_code, acct_account.account_name, acct_account.account_status, acct_bank_account.bank_account_no, acct_bank_account.bank_account_remark');
			$this->db->from('acct_bank_account');
			$this->db->join('acct_account', 'acct_bank_account.account_id = acct_account.account_id');
			$this->db->where('acct_bank_account.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctAccount(){
			$hasil = $this->db->query("
							SELECT acct_account.account_id, 
							CONCAT(acct_account.account_code,' - ', acct_account.account_name) as account_code 
							FROM acct_account
							WHERE acct_account.data_state='0'");
			return $hasil->result_array();
		}
		
		public function insertAcctBankAccount($data){
			$query = $this->db->insert('acct_bank_account',$data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function getAcctBankAccount_Detail($bank_account_id){
			$this->db->select('acct_bank_account.bank_account_id, acct_bank_account.bank_account_code, acct_bank_account.bank_account_name, acct_bank_account.account_id, acct_account.account_code, acct_account.account_name, acct_account.account_status, acct_bank_account.bank_account_no, acct_bank_account.bank_account_remark');
			$this->db->from('acct_bank_account');
			$this->db->join('acct_account', 'acct_bank_account.account_id = acct_account.account_id');
			$this->db->where('acct_bank_account.data_state', 0);
			$this->db->where('acct_bank_account.bank_account_id', $bank_account_id);
			return $this->db->get()->row_array();
		}
		
		public function updateAcctBankAccount($data){
			$this->db->where("bank_account_id",$data['bank_account_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function deleteAcctBankAccount($bank_account_id){
			$this->db->where("bank_account_id",$bank_account_id);
			$query = $this->db->update($this->table, array('data_state'=>1));
			if($query){
				return true;
			}else{
				return false;
			}
		}
	}
?>