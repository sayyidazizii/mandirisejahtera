<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctSavingsCloseBook_model extends CI_Model {
		var $table = "acct_savings_account";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 

		public function insertAcctSavingsCloseBook($data){
			return $query = $this->db->insert('acct_savings_close_book_log',$data);
		}

		public function updateAcctSavingsAccount($branch_id){
			$query = $this->db->query("update acct_savings_account set savings_account_opening_balance = savings_account_last_balance where branch_id = '".$branch_id."'");
			// $this->db->set('acct_savings_account.savings_account_opening_balance','acct_savings_account.savings_account_last_balance');
			// $this->db->where('acct_savings_account.branch_id', $branch_id);
			if($query){
				// print_r($this->db->last_query());exit;
				return true;
			} else {
				return false;
			}

		}

		public function getAcctSavingsAccount($branch_id){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.member_id, core_member.member_name, acct_savings_account.savings_id, acct_savings.savings_code, acct_savings.savings_name, acct_savings_account.savings_account_no, acct_savings_account.savings_account_date, acct_savings_account.savings_account_first_deposit_amount, acct_savings_account.savings_account_last_balance, acct_savings_account.validation, acct_savings_account.validation_on');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_account.branch_id', $branch_id);
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings.savings_status', 0);
			$this->db->order_by('acct_savings_account.savings_account_no', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function insertAcctSavingsAccountDetail($data){
			return $query = $this->db->insert('acct_savings_account_detail',$data);
		}
		
		

	}
?>