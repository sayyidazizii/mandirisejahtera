<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctNominativeRecapReport_model extends CI_Model {
		var $table = "acct_deposito_account";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		public function getAcctNomintiveCreditsReport($start_date, $end_date){
			$this->db->select('acct_credits_account.credits_account_serial, acct_credits_account.member_id, core_member.member_name, core_member.member_address, acct_credits_account.credits_account_amount, acct_credits_account.credits_account_last_balance, acct_credits_account.credits_account_date, acct_credits_account.credits_account_due_date, acct_credits_account.credits_account_period, acct_credits_account.credits_account_interest_amount, acct_credits_account.credits_account_interest_last_balance,
				acct_credits_account.credits_account_payment_to, acct_credits_account.credits_account_payment_amount, acct_credits_account.credits_account_interest');
			$this->db->from('acct_credits_account');
			$this->db->join('core_member', 'acct_credits_account.member_id = core_member.member_id');
			$this->db->where('acct_credits_account.data_state ', 0);
			$this->db->where('acct_credits_account.credits_approve_status', 1);
			$this->db->where('acct_credits_account.credits_account_last_balance >', 0);
			$this->db->where('acct_credits_account.credits_account_date >=', $start_date);
			$this->db->where('acct_credits_account.credits_account_date <=', $end_date);
			$this->db->order_by('acct_credits_account.credits_account_serial', 'ASC');
			$this->db->order_by('acct_credits_account.member_id', 'ASC');
			$this->db->order_by('core_member.member_name', 'ASC');
			$this->db->order_by('core_member.member_id', 'ASC');
			$this->db->order_by('core_member.member_address', 'ASC');
			$this->db->order_by('acct_credits_account.credits_account_amount', 'ASC');
			$this->db->order_by('acct_credits_account.credits_account_last_balance', 'ASC');
			$this->db->order_by('acct_credits_account.credits_account_date', 'ASC');
			$this->db->order_by('acct_credits_account.credits_account_due_date', 'ASC');
			$this->db->order_by('acct_credits_account.credits_account_period', 'ASC');			
			$result = $this->db->get()->result_array();
			// print_r($result);exit;
			return $result;
		}


		public function getAcctNomintiveDepositoReport($start_date){
			$this->db->select('acct_deposito_account.deposito_account_id, acct_deposito_account.deposito_id, acct_deposito_account.member_id, core_member.member_name, core_member.member_address, acct_deposito_account.deposito_account_date, acct_deposito_account.deposito_account_due_date, acct_deposito_account.deposito_account_amount, acct_deposito_account.deposito_account_no, acct_deposito_account.deposito_account_period, acct_deposito_account.deposito_account_status, acct_deposito.deposito_interest_rate');
			$this->db->from('acct_deposito_account');
			$this->db->join('acct_deposito', 'acct_deposito_account.deposito_id = acct_deposito.deposito_id');
			$this->db->join('core_member', 'acct_deposito_account.member_id = core_member.member_id');
			$this->db->where('acct_deposito_account.deposito_account_date <= ', $start_date);
			$this->db->where('acct_deposito_account.data_state', 0);
			$this->db->where('acct_deposito_account.deposito_account_status', 0);
			$this->db->order_by('acct_deposito_account.deposito_account_id', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_id', 'ASC');
			$this->db->order_by('acct_deposito_account.member_id', 'ASC');
			$this->db->order_by('core_member.member_name', 'ASC');
			$this->db->order_by('core_member.member_address', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_account_date', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_account_due_date', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_account_amount', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_account_no', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_account_period', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_account_status', 'ASC');
			$result = $this->db->get()->result_array();
			// print_r($this->db->last_query());exit;
			return $result;
		}
		public function getAcctNomintiveSavingsReport_Savings($savings_id){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.member_id, core_member.member_name, core_member.member_address, acct_savings_account.savings_account_date, acct_savings_account.savings_account_last_balance, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings.savings_status');
			$this->db->from('acct_savings_account');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->where('acct_savings_account.data_state ', 0);
			$this->db->where('acct_savings_account.savings_id ', $savings_id);
			$this->db->order_by('acct_savings_account.savings_account_id', 'ASC');
			$this->db->order_by('acct_savings_account.savings_account_no', 'ASC');
			$this->db->order_by('acct_savings_account.member_id', 'ASC');
			$this->db->order_by('core_member.member_name', 'ASC');
			$this->db->order_by('core_member.member_address', 'ASC');
			$this->db->order_by('acct_savings_account.savings_account_date', 'ASC');
			$this->db->order_by('acct_savings_account.savings_account_last_balance', 'ASC');
			$this->db->order_by('acct_savings_account.savings_id', 'ASC');
			$this->db->order_by('acct_savings.savings_name', 'ASC');
			$this->db->order_by('acct_savings.savings_status', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}
		public function getAcctSavingsProfitSharing($savings_account_id, $start_date, $end_date){
			$this->db->select('acct_savings_profit_sharing.savings_account_id, acct_savings_account.savings_account_no, acct_savings_profit_sharing.member_id, core_member.member_name, core_member.member_address, acct_savings_profit_sharing.savings_profit_sharing_amount, acct_savings_profit_sharing.savings_daily_average_balance, acct_savings_profit_sharing.savings_account_last_balance');
			$this->db->from('acct_savings_profit_sharing');
			$this->db->join('acct_savings_account', 'acct_savings_profit_sharing.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_member', 'acct_savings_profit_sharing.member_id = core_member.member_id');
			$this->db->where('acct_savings_profit_sharing.savings_profit_sharing_date >=', $start_date);
			$this->db->where('acct_savings_profit_sharing.savings_profit_sharing_date <=', $end_date);
		
			$this->db->where('acct_savings_profit_sharing.savings_account_id', $savings_account_id);
			
		
			$result = $this->db->get()->row_array();
			return $result;
		}
		public function getAcctNomintiveCreditsReport_Credits($start_date, $end_date, $credits_id){
			$this->db->select('acct_credits_account.credits_account_serial, acct_credits_account.member_id, acct_credits_account.credits_account_last_balance, acct_credits_account.credits_account_date, acct_credits_account.credits_account_due_date, acct_credits_account.credits_account_amount, acct_credits_account.credits_account_interest_last_balance ,acct_credits_account.credits_account_interest, acct_credits_account.credits_account_period, acct_credits_account.credits_account_interest_amount, acct_credits_account.credits_account_interest_last_balance,
			acct_credits_account.credits_account_payment_to, acct_credits_account.credits_account_payment_amount ');
		$this->db->from('acct_credits_account');
		$this->db->where('acct_credits_account.credits_approve_status', 1);
		$this->db->where('acct_credits_account.data_state ', 0);
		$this->db->where('acct_credits_account.credits_account_last_balance >', 0);
		$this->db->where('acct_credits_account.credits_account_date >=', $start_date);
		$this->db->where('acct_credits_account.credits_account_date <=', $end_date);
		if(!empty($credits_id)){
		$this->db->where('acct_credits_account.credits_id', $credits_id);
		}
		$this->db->order_by('acct_credits_account.credits_account_serial', 'ASC');
		$this->db->order_by('acct_credits_account.member_id', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_last_balance', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_date', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_due_date', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_amount', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_interest_last_balance', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_interest', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_period', 'ASC');
		$result = $this->db->get()->result_array();
		return $result;
		}
		public function getAcctNomintiveCreditsReport_SourceFund($start_date, $end_date, $source_fund_id){
			$this->db->select('acct_credits_account.credits_account_serial, acct_credits_account.member_id, core_member.member_name, core_member.member_address, acct_credits_account.credits_account_date, acct_credits_account.credits_account_due_date, acct_credits_account.credits_account_interest, acct_credits_account.credits_account_last_balance, acct_credits_account.credits_account_amount, acct_credits_account.credits_account_interest_last_balance, acct_credits_account.credits_account_period, acct_credits_account.credits_account_interest_amount, acct_credits_account.credits_account_interest_last_balance,
				acct_credits_account.credits_account_payment_to, acct_credits_account.credits_account_payment_amount');
			$this->db->from('acct_credits_account');
			$this->db->join('core_member', 'acct_credits_account.member_id = core_member.member_id');
			$this->db->where('acct_credits_account.credits_approve_status', 1);
			$this->db->where('acct_credits_account.data_state ', 0);
			$this->db->where('acct_credits_account.credits_account_last_balance >', 0);
			$this->db->where('acct_credits_account.credits_account_date >=', $start_date);
			$this->db->where('acct_credits_account.credits_account_date <=', $end_date);
			$this->db->where('acct_credits_account.source_fund_id', $source_fund_id);
			$this->db->order_by('acct_credits_account.credits_account_serial', 'ASC');
			$this->db->order_by('acct_credits_account.member_id', 'ASC');
			$this->db->order_by('core_member.member_name', 'ASC');
			$this->db->order_by('core_member.member_address', 'ASC');
			$this->db->order_by('acct_credits_account.credits_account_date', 'ASC');
			$this->db->order_by('acct_credits_account.credits_account_due_date', 'ASC');
			$this->db->order_by('acct_credits_account.credits_account_interest', 'ASC');
			$this->db->order_by('acct_credits_account.credits_account_last_balance', 'ASC');
			$this->db->order_by('acct_credits_account.credits_account_amount', 'ASC');
			$this->db->order_by('acct_credits_account.credits_account_interest_last_balance', 'ASC');
			$this->db->order_by('acct_credits_account.credits_account_period', 'ASC');
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
		public function getAcctSavings(){
			$this->db->select('acct_savings.savings_id, acct_savings.savings_name');
			$this->db->from('acct_savings');
			$this->db->where('acct_savings.data_state', 0);
			$this->db->where('acct_savings.savings_status', 0);
			return $this->db->get()->result_array();
		}

		public function getCoreBranch(){
			$this->db->select('core_branch.branch_id, core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getAcctDeposito(){
			$this->db->select('acct_deposito.deposito_id, acct_deposito.deposito_name');
			$this->db->from('acct_deposito');
			$this->db->where('acct_deposito.data_state', 0);
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

		public function getAcctNomintiveDepositoReport_Deposito($start_date, $end_date, $deposito_id){
			$this->db->select('acct_deposito_account.deposito_account_id, acct_deposito_account.deposito_id, acct_deposito_account.member_id, core_member.member_name, core_member.member_address, acct_deposito_account.deposito_account_date, acct_deposito_account.deposito_account_due_date, acct_deposito_account.deposito_account_amount, acct_deposito_account.deposito_account_no, acct_deposito_account.deposito_account_period, acct_deposito_account.deposito_account_status, acct_deposito.deposito_interest_rate');
			$this->db->from('acct_deposito_account');
			$this->db->join('acct_deposito', 'acct_deposito_account.deposito_id = acct_deposito.deposito_id');
			$this->db->join('core_member', 'acct_deposito_account.member_id = core_member.member_id');
			$this->db->where('acct_deposito_account.deposito_account_date >= ', $start_date);
			$this->db->where('acct_deposito_account.deposito_account_date <= ', $end_date);
			$this->db->where('acct_deposito_account.deposito_id ', $deposito_id);
			// if(!empty($branch_id)){
			// 	$this->db->where('acct_deposito_account.branch_id', $branch_id);
			// }
			$this->db->where('acct_deposito_account.data_state', 0);
			$this->db->where('acct_deposito_account.deposito_account_status', 0);
			$this->db->order_by('acct_deposito_account.deposito_account_id', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_id', 'ASC');
			$this->db->order_by('acct_deposito_account.member_id', 'ASC');
			$this->db->order_by('core_member.member_name', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_account_date', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_account_due_date', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_account_amount', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_account_no', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_account_period', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_account_status', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

	}
?>