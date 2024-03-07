<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctCreditsPaymentDuePaidReport_model extends CI_Model {
		var $table = "acct_savings_account";
	

		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 

		public function getCoreBranch(){
			$this->db->select('core_branch.branch_id, core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}
		
		public function getCreditsAccount($end_date,$branch_id ){

			$this->db->select('acct_credits_account.credits_account_id, acct_credits_account.credits_account_serial, acct_credits_account.member_id, core_member.member_name, core_member.member_address, acct_credits_account.credits_account_amount, acct_credits_account.credits_account_principal_amount, acct_credits_account.credits_account_interest_amount, acct_credits_account.credits_account_last_balance, acct_credits_account.credits_account_payment_date,acct_credits_account.credits_account_last_payment_date, acct_credits_account.credits_account_payment_amount,acct_credits_account.credits_account_accumulated_fines, acct_credits_account.credits_account_period, acct_credits_account.credits_account_payment_to, acct_credits_account.credits_account_status');
			$this->db->from('acct_credits_account');
			$this->db->join('core_member', 'acct_credits_account.member_id = core_member.member_id');
			$this->db->where('acct_credits_account.data_state ', 0);
			$this->db->where('acct_credits_account.credits_account_status !=', 2);
			$this->db->where('acct_credits_account.credits_approve_status', 1);	
			$this->db->where('acct_credits_account.credits_account_payment_date', $end_date);
// 			$this->db->where('CURDATE() >= acct_credits_account.credits_account_payment_date');

			if(!empty($branch_id)){
				$this->db->where('acct_credits_account.branch_id', $branch_id);
			}
			$this->db->order_by('acct_credits_account.credits_account_serial', 'ASC');	
			$result = $this->db->get()->result_array();
			//print_r($result);exit;
			return $result;
		}

		public function AcctCreditsPaymentSuspend($start_date, $end_date, $branch_id){
			$this->db->select('acct_credits_payment_suspend.branch_id, acct_credits_payment_suspend.credits_account_id, acct_credits_payment_suspend.member_id, core_member.member_name,  acct_credits_payment_suspend.credits_payment_suspend_date, acct_credits_payment_suspend.credits_payment_period, acct_credits_payment_suspend.credits_id, acct_credits_payment_suspend.credits_grace_period, acct_credits_payment_suspend.credits_payment_date_old, acct_credits_payment_suspend.credits_payment_date_new, acct_credits.credits_name, acct_credits_account.credits_account_serial, acct_credits_account.credits_payment_period');
			$this->db->from('acct_credits_payment_suspend');
			$this->db->join('core_member', 'acct_credits_payment_suspend.member_id = core_member.member_id');
			$this->db->join('acct_credits','acct_credits_payment_suspend.credits_id = acct_credits.credits_id');
			$this->db->join('acct_credits_account','acct_credits_payment_suspend.credits_account_id = acct_credits_account.credits_account_id');
			$this->db->where('acct_credits_payment_suspend.data_state ', 0);
			$this->db->where('acct_credits_payment_suspend.credits_payment_date_new >=', $start_date);
			$this->db->where('acct_credits_payment_suspend.credits_payment_date_new <=', $end_date);
			if(!empty($branch_id)){
				$this->db->where('acct_credits_payment_suspend.branch_id', $branch_id);
			}
			$this->db->order_by('acct_credits_account.credits_account_serial','ASC');
			$result = $this->db->get()->result_array();

			//print_r($this->db->last_query());exit;
			return $result;
		}

		public function getCoreMemberDetail( $start_date, $end_date ,$branch_id ){
			$this->db->select('core_member.member_id, core_member.member_name, core_member.member_address,core_member.member_no, core_member.member_register_date');
			$this->db->from('core_member');
			$this->db->where('core_member.data_state ', 0);
			$this->db->where('core_member.member_register_date >=', $start_date);
			$this->db->where('core_member.member_register_date <=', $end_date);
			if(!empty($branch_id)){
				$this->db->where('core_member.branch_id', $branch_id);
			}
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctCredits(){
			$this->db->select('acct_credits.credits_id, acct_credits.credits_name');
			$this->db->from('acct_credits');
			$this->db->where('acct_credits.data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctSourceFund(){
			$this->db->select('acct_source_fund.source_fund_id, acct_source_fund.source_fund_name');
			$this->db->from('acct_source_fund');
			$this->db->where('acct_source_fund.data_state', 0);
			return $this->db->get()->result_array();
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