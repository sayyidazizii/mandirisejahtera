<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctDepositoProfitSharingReport_model extends CI_Model {
		var $table = "acct_deposito_profit_sharing";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getAcctDepositoProfitSharing($start_date, $end_date){
			$this->db->select('acct_deposito_profit_sharing.deposito_account_id, acct_deposito_account.deposito_account_no, acct_deposito_profit_sharing.savings_account_id, acct_savings_account.savings_account_no, acct_deposito_profit_sharing.member_id, core_member.member_name, acct_deposito_profit_sharing.deposito_profit_sharing_amount, acct_deposito_profit_sharing.deposito_account_last_balance, acct_deposito_profit_sharing.deposito_profit_sharing_due_date, acct_deposito_profit_sharing.deposito_profit_sharing_date');
			$this->db->from('acct_deposito_profit_sharing');
			$this->db->join('core_member', 'acct_deposito_profit_sharing.member_id = core_member.member_id');
			$this->db->join('acct_savings_account', 'acct_deposito_profit_sharing.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('acct_deposito_account', 'acct_deposito_profit_sharing.deposito_account_id = acct_deposito_account.deposito_account_id');
			$this->db->where('acct_deposito_profit_sharing.deposito_profit_sharing_due_date >=', $start_date);
			$this->db->where('acct_deposito_profit_sharing.deposito_profit_sharing_due_date <=', $end_date);
			$this->db->where('acct_deposito_account.data_state', 0);
			$this->db->order_by('acct_deposito_account.deposito_account_no', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}
		public function getCoreBranch(){
			$this->db->select('core_branch.branch_id, core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}
		public function getUsername($user_id){
			$this->db->select('username');
			$this->db->from('system_user');
			$this->db->where('user_id', $user_id);
			$result = $this->db->get()->row_array();
			return $result['username'];
		}
		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}
		
	}
?>