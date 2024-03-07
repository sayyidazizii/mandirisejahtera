<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctDeposito_model extends CI_Model {
		var $table = "acct_deposito";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getDataAcctDeposito(){
			$this->db->select('acct_deposito.deposito_id, acct_deposito.deposito_code, acct_deposito.deposito_name, acct_deposito.account_id, acct_account.account_code, acct_account.account_name, acct_deposito.account_basil_id, acct_deposito.deposito_period, acct_deposito.deposito_interest_rate');
			$this->db->from('acct_deposito');
			$this->db->join('acct_account', 'acct_deposito.account_id = acct_account.account_id');
			$this->db->where('acct_deposito.data_state', 0);
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

		public function getAccountCode($account_id){
			$this->db->select('account_code');
			$this->db->from('acct_account');
			$this->db->where('account_id', $account_id);
			$result = $this->db->get()->row_array();
			return $result['account_code'];
		}

		public function getAccountName($account_id){
			$this->db->select('account_name');
			$this->db->from('acct_account');
			$this->db->where('account_id', $account_id);
			$result = $this->db->get()->row_array();
			return $result['account_name'];
		}

		public function insertAcctAccount($data){
			return $query = $this->db->insert('acct_account',$data);
		}
		
		public function insertAcctDeposito($data){
			$query = $this->db->insert('acct_deposito',$data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function getAcctDeposito_Detail($deposito_id){
			$this->db->select('acct_deposito.deposito_id, acct_deposito.deposito_code, acct_deposito.deposito_name, acct_deposito.account_id, acct_account.account_code, acct_account.account_name, acct_deposito.account_basil_id, acct_deposito.deposito_period, acct_deposito.deposito_interest_rate');
			$this->db->from('acct_deposito');
			$this->db->join('acct_account', 'acct_deposito.account_id = acct_account.account_id');
			$this->db->where('acct_deposito.deposito_id', $deposito_id);
			return $this->db->get()->row_array();
		}
		
		public function updateAcctDeposito($data){
			$this->db->where("deposito_id",$data['deposito_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function deleteAcctDeposito($deposito_id){
			$this->db->where("deposito_id",$deposito_id);
			$query = $this->db->update($this->table, array('data_state'=>1));
			if($query){
				return true;
			}else{
				return false;
			}
		}
	}
?>