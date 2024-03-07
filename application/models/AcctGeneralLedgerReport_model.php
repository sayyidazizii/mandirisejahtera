<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctGeneralLedgerReport_model extends CI_Model {
		var $table = "acct_journal_voucher";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $this->CI->load->model('Connection_model');
			// $this->CI->load->dbforge();

			// $auth 			= $this->session->userdata('auth');
			// $db 		= $this->Connection_model->define_database($auth['database']);
			// $this->db 	= $this->load->database($db, true);
			
		}

		public function getAcctGeneralLedgerReport($start_date, $end_date, $account_id, $branch_id){
			$this->db->select('acct_journal_voucher_item.journal_voucher_item_id, acct_journal_voucher_item.journal_voucher_description, acct_journal_voucher_item.journal_voucher_debit_amount, acct_journal_voucher_item.journal_voucher_credit_amount,acct_journal_voucher_item.account_id, acct_account.account_code, acct_account.account_name, acct_journal_voucher.journal_voucher_date, acct_journal_voucher.journal_voucher_id');
			$this->db->from('acct_journal_voucher_item');
			$this->db->join('acct_account','acct_journal_voucher_item.account_id = acct_account.account_id');
			$this->db->join('acct_journal_voucher','acct_journal_voucher_item.journal_voucher_id = acct_journal_voucher.journal_voucher_id');
			$this->db->where('MONTH(acct_journal_voucher.journal_voucher_date >=)',$start_date);
			$this->db->where('MONTH(acct_journal_voucher.journal_voucher_date <=)',$end_date);	
			$this->db->where('acct_journal_voucher.data_state', 0);		
			$this->db->where('acct_journal_voucher_item.journal_voucher_amount <>', 0);		
			$this->db->order_by('acct_journal_voucher.created_on','desc');
			$this->db->order_by('acct_journal_voucher.journal_voucher_date','desc');
			
			if($account_id != ''){
				$this->db->where('acct_journal_voucher_item.account_id',$account_id);
			}

			if($branch_id != ''){
				$this->db->where('acct_journal_voucher.branch_id', $branch_id);
			}
			$result = $this->db->get()->result_array();
			return $result;
		}

		// public function getAcctAccountBalanceDetail($account_id, $start_date, $end_date, $branch_id){
		// 	$this->db->select('acct_account_balance_detail.account_balance_detail_id, acct_account_balance_detail.transaction_type, acct_account_balance_detail.transaction_code, acct_account_balance_detail.transaction_date, acct_account_balance_detail.transaction_id, acct_account_balance_detail.account_id, acct_account.account_code, acct_account.account_name, acct_account_balance_detail.opening_balance, acct_account_balance_detail.account_in, acct_account_balance_detail.account_out, acct_account_balance_detail.last_balance');
		// 	$this->db->from('acct_account_balance_detail');
		// 	$this->db->join('acct_account', 'acct_account_balance_detail.account_id = acct_account.account_id');
		// 	$this->db->where('acct_account_balance_detail.account_id', $account_id);
		// 	$this->db->where('acct_account_balance_detail.transaction_date >=', $start_date);
		// 	$this->db->where('acct_account_balance_detail.transaction_date <=', $end_date);
		// 	$this->db->where('acct_account_balance_detail.branch_id', $branch_id);
		// 	$this->db->order_by('acct_account_balance_detail.transaction_date', 'ASC');	
		// 	$this->db->order_by('acct_account_balance_detail.account_balance_detail_id', 'ASC');	
		// 	$result = $this->db->get()->result_array();
		// 	// print_r($result);exit;
		// 	return $result;
		// }

		public function getAcctAccountBalanceDetail($account_id, $month_period_start, $month_period_end, $year, $branch_id){
			$this->db->select('acct_account_balance_detail.account_balance_detail_id, acct_account_balance_detail.transaction_type, acct_account_balance_detail.transaction_code, acct_account_balance_detail.transaction_date, acct_account_balance_detail.transaction_id, acct_account_balance_detail.account_id, acct_account.account_code, acct_account.account_name, acct_account_balance_detail.opening_balance, acct_account_balance_detail.account_in, acct_account_balance_detail.account_out, acct_account_balance_detail.last_balance');
			$this->db->from('acct_account_balance_detail');
			$this->db->join('acct_account', 'acct_account_balance_detail.account_id = acct_account.account_id');
			$this->db->where('acct_account_balance_detail.account_id', $account_id);
			$this->db->where('MONTH(acct_account_balance_detail.transaction_date) >=', $month_period_start);
			$this->db->where('MONTH(acct_account_balance_detail.transaction_date) <=', $month_period_end);
			$this->db->where('YEAR(acct_account_balance_detail.transaction_date)', $year);
			$this->db->where('acct_account_balance_detail.branch_id', $branch_id);
			$this->db->order_by('acct_account_balance_detail.transaction_date', 'ASC');	
			$this->db->order_by('acct_account_balance_detail.account_balance_detail_id', 'ASC');	
			$result = $this->db->get()->result_array();
			//print_r($result);exit;
			return $result;
		}

		public function getAccountIn($account_id, $month_start, $year, $branch_id){
			$this->db->select('SUM(acct_account_balance_detail.account_in) AS account_in_amount');
			$this->db->from('acct_account_balance_detail');
			$this->db->where('acct_account_balance_detail.account_id', $account_id);
			$this->db->where('MONTH(acct_account_balance_detail.transaction_date)', $month_start);
			$this->db->where('YEAR(acct_account_balance_detail.transaction_date)', $year);
			$this->db->where('acct_account_balance_detail.branch_id', $branch_id);
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['account_in_amount'];
		}

		public function getAccountOut($account_id, $month_start, $year, $branch_id){
			$this->db->select('SUM(acct_account_balance_detail.account_out) AS account_out_amount');
			$this->db->from('acct_account_balance_detail');
			$this->db->where('acct_account_balance_detail.account_id', $account_id);
			$this->db->where('MONTH(acct_account_balance_detail.transaction_date)', $month_start);
			$this->db->where('YEAR(acct_account_balance_detail.transaction_date)', $year);
			$this->db->where('acct_account_balance_detail.branch_id', $branch_id);
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['account_out_amount'];
		}


		public function getCoreBranch(){
			$this->db->select('core_branch.branch_id, core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state',0);
			$result = $this->db->get()->result_array();
			return $result;
		}


		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getMinID($journal_voucher_id){
			$this->db->select_min('journal_voucher_item_id');
			$this->db->from('acct_journal_voucher_item');
			$this->db->where('journal_voucher_id', $journal_voucher_id);
			$result = $this->db->get()->row_array();
			return $result['journal_voucher_item_id'];
		}

		public function getAcctAccount(){
			$this->db->select('acct_account.account_id, acct_account.account_name');
			$this->db->from('acct_account');
			$this->db->where('acct_account.data_state',0);
			// $this->db->where('acct_account.parent_account_status',0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAccountName($account_id){
			$this->db->select('CONCAT(acct_account.account_code," - ", acct_account.account_name) AS account_name');
			$this->db->from('acct_account');
			$this->db->where('acct_account.account_id', $account_id);
			$result = $this->db->get()->row_array();
			return $result['account_name'];
		}

		public function getOpeningDate($account_id, $start_date, $end_date, $branch_id){
			$this->db->select_min('transaction_date');
			$this->db->from('acct_account_balance_detail');
			$this->db->where('account_id', $account_id);
			$this->db->where('transaction_date >=', $start_date);
			$this->db->where('transaction_date <=', $end_date);
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['transaction_date'];
		}

		public function getLastDate($account_id, $start_date, $end_date, $branch_id){
			$this->db->select_max('transaction_date');
			$this->db->from('acct_account_balance_detail');
			$this->db->where('account_id', $account_id);
			$this->db->where('branch_id', $branch_id);
			$this->db->where('transaction_date <', $start_date);
			$result = $this->db->get()->row_array();
			return $result['transaction_date'];
		}

		public function getOpeningBalance($account_id, $month, $year, $branch_id){

			//print_r($month);
			$last_year = $year -1;
			$this->db->select('acct_account_opening_balance.opening_balance');
			$this->db->from('acct_account_opening_balance');
			$this->db->where('acct_account_opening_balance.account_id', $account_id);
			if(!empty($month)){
				$this->db->where('acct_account_opening_balance.month_period', $month);
				$this->db->where('acct_account_opening_balance.year_period', $year);
				$this->db->order_by('acct_account_opening_balance.account_opening_balance_id', 'DESC');
			} else {
				// print_r("aaaa");exit;
				$this->db->where('acct_account_opening_balance.year_period', $year);
				$this->db->order_by('acct_account_opening_balance.month_period', 'ASC');
			}
			$this->db->where('acct_account_opening_balance.branch_id', $branch_id);
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			//print_r($this->db->last_query());
			return $result['opening_balance'];
		}

		public function getLastBalance($account_id, $month, $year, $branch_id){
			$this->db->select('acct_account_opening_balance.opening_balance');
			$this->db->from('acct_account_opening_balance');
			$this->db->where('acct_account_opening_balance.account_id', $account_id);
			$this->db->where('acct_account_opening_balance.branch_id', $branch_id);
			$this->db->order_by('acct_account_opening_balance.month_period', 'ASC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['opening_balance'];
		}

		public function getAccountIDDefaultStatus($account_id){
			$this->db->select('acct_account.account_default_status');
			$this->db->from('acct_account');
			$this->db->where('acct_account.account_id', $account_id);
			$this->db->where('acct_account.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['account_default_status'];
		}

		public function getJournalVoucherDescription($journal_voucher_id, $account_id){
			$this->db->select('journal_voucher_description');
			$this->db->from('acct_journal_voucher_item');
			$this->db->where('journal_voucher_id', $journal_voucher_id);
			$this->db->where('account_id', $account_id);
			$result = $this->db->get()->row_array();
			return $result['journal_voucher_description'];
		}

		public function getJournalVoucherNo($journal_voucher_id){
			$this->db->select('journal_voucher_no');
			$this->db->from('acct_journal_voucher');
			$this->db->where('journal_voucher_id', $journal_voucher_id);
			$result = $this->db->get()->row_array();
			return $result['journal_voucher_no'];
		}

	}
?>