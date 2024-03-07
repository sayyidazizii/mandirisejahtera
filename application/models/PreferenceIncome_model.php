<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class PreferenceIncome_model extends CI_Model {
		var $table = "preference_income";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getPreferenceIncome(){
			$this->db->select('preference_income.income_id, preference_income.income_name, preference_income.income_percentage, preference_income.income_group, preference_income.account_id, acct_account.account_code, acct_account.account_name, preference_income.income_status');
			$this->db->from('preference_income');
			$this->db->join('acct_account', 'preference_income.account_id = acct_account.account_id');
			$this->db->where('preference_income.data_state', 0);
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
		
		public function insertPreferenceIncome($data){
			$query = $this->db->insert('preference_income',$data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function getAccountName($account_id){
			$this->db->select('CONCAT(account_code, " - " ,account_name) AS account_name');
			$this->db->from('acct_account');
			$this->db->where('account_id', $account_id);
			$result = $this->db->get()->row_array();
			return $result['account_name'];
		}
		
		public function getPreferenceIncome_Detail($account_id){
			$this->db->select('acct_account.account_id, acct_account.account_type_id, acct_account.account_code, acct_account.account_name, acct_account.account_group, acct_account.account_status');
			$this->db->from('acct_account');
			$this->db->where('acct_account.data_state', 0);
			$this->db->where('acct_account.account_id', $account_id);
			return $this->db->get()->row_array();
		}
		
		public function updatePreferenceIncome($data){
			$this->db->where("income_id",$data['income_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function deletePreferenceIncome($income_id){
			$this->db->where("income_id",$income_id);
			$query = $this->db->update($this->table, array('data_state'=>1));
			if($query){
				return true;
			}else{
				return false;
			}
		}
	}
?>