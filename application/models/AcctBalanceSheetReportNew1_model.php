<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctBalanceSheetReportNew1_model extends CI_Model {
		var $table = "acct_mutation";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			$this->CI->load->model('Connection_model');
			$this->CI->load->dbforge();

			// $auth 			= $this->session->userdata('auth');
			// $db_user 		= $this->Connection_model->define_database($auth['database']);
			// $this->db_user 	= $this->load->database($db_user, true);
		} 

		public function getPreferenceCompany(){
			$this->db->select('preference_company.company_name');
			$this->db->from('preference_company');
			return $this->db->get()->row_array();
		}

		public function getCoreBranch(){
			
			$this->db->select('core_branch.branch_id, core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctJournalVoucherItem(){
			$this->db->select('acct_journal_voucher_item.*');
			$this->db->from('acct_journal_voucher_item');
			$this->db->where('acct_journal_voucher_item.data_state', 0);
			$this->db->order_by('acct_journal_voucher_item.journal_voucher_id', 'ASC');
			return $this->db->get()->result_array();
		}

		public function getAcctBalanceSheetReportNew1_Left(){
			$this->db->select('acct_balance_sheet_report.balance_sheet_report_id, acct_balance_sheet_report.report_no, acct_balance_sheet_report.account_id1, acct_balance_sheet_report.account_code1, acct_balance_sheet_report.account_name1, acct_balance_sheet_report.report_formula1, acct_balance_sheet_report.report_operator1, acct_balance_sheet_report.report_type1, acct_balance_sheet_report.report_tab1, acct_balance_sheet_report.report_bold1, acct_balance_sheet_report.report_formula3, acct_balance_sheet_report.report_operator3');
			$this->db->from('acct_balance_sheet_report');
			$this->db->where('acct_balance_sheet_report.account_name1 <> " " ');
			$this->db->order_by('acct_balance_sheet_report.report_no', 'ASC');
			return $this->db->get()->result_array();
		}

		public function getAcctBalanceSheetReportNew1_Right(){
			$this->db->select('acct_balance_sheet_report.balance_sheet_report_id, acct_balance_sheet_report.report_no, acct_balance_sheet_report.account_id2, acct_balance_sheet_report.account_code2, acct_balance_sheet_report.account_name2, acct_balance_sheet_report.report_formula2, acct_balance_sheet_report.report_operator2, acct_balance_sheet_report.report_type2, acct_balance_sheet_report.report_tab2, acct_balance_sheet_report.report_bold2, acct_balance_sheet_report.report_formula3, acct_balance_sheet_report.report_operator3');
			$this->db->from('acct_balance_sheet_report');
			$this->db->where('acct_balance_sheet_report.account_name2 <> " " ');
			$this->db->order_by('acct_balance_sheet_report.report_no', 'ASC');
			return $this->db->get()->result_array();
		}

		public function getAcctAccount_List($length, $account_code){
			$this->db->select('acct_account.account_id');
			$this->db->from('acct_account');
			$this->db->where('data_state', 0);
			$this->db->where('LEFT(account_code,'.$length.')', $account_code);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getLastBalance($account_id, $branch_id, $month, $year){
			$this->db->select('acct_account_opening_balance.opening_balance');
			$this->db->from('acct_account_opening_balance');
			$this->db->where('acct_account_opening_balance.account_id', $account_id);
			$this->db->where('acct_account_opening_balance.branch_id', $branch_id);
			$this->db->where('acct_account_opening_balance.month_period', $month);
			$this->db->where('acct_account_opening_balance.year_period', $year);
			$result = $this->db->get()->row_array();
			return $result['opening_balance'];
		}

		public function getSHUTahunBerjalan($account_id, $branch_id, $month, $year){
			$this->db->select('acct_account_mutation.mutation_in_amount, acct_account_mutation.mutation_out_amount');
			$this->db->from('acct_account_mutation');
			$this->db->where('acct_account_mutation.account_id', $account_id);
			$this->db->where('acct_account_mutation.branch_id', $branch_id);
			$this->db->where('acct_account_mutation.month_period <=', $month);
			$this->db->where('acct_account_mutation.year_period', $year);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getProfitLossAmount($branch_id, $month, $year){
			$this->db->select('SUM(acct_profit_loss.profit_loss_amount) AS profit_loss_amount');
			$this->db->from('acct_profit_loss');
			$this->db->where('acct_profit_loss.branch_id', $branch_id);
			$this->db->where('acct_profit_loss.month_period <=', $month);
			$this->db->where('acct_profit_loss.year_period', $year);
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['profit_loss_amount'];
		}

		public function getSHUTahunLalu($branch_id, $month, $year){
			$this->db->select('SUM(acct_profit_loss.profit_loss_amount) AS shu_tahun_lalu');
			$this->db->from('acct_profit_loss');
			$this->db->where('acct_profit_loss.branch_id', $branch_id);
			// $this->db->where('acct_profit_loss.month_period <=', $month);
			$this->db->where('acct_profit_loss.year_period <', $year);
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['shu_tahun_lalu'];
		}

		public function getBranchName($branch_id){
			$this->db->select('branch_name');
			$this->db->from('core_branch');
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['branch_name'];
		}







		public function getAccountListParent($length, $account_id){
			$this->db->select('account_id, account_code, account_name');
			$this->db->from('acct_account');
			$this->db->where('LEFT(account_code,'.$length.')', $account_id);
			$this->db->where('account_status', 1);
			$this->db->where('data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getSaldoAccountChild($account_id, $month, $year){
			$this->db->select('acct_account_balance_detail.opening_balance');
			$this->db->from('acct_account_balance_detail');
			$this->db->join('acct_account','acct_account_balance_detail.account_id = acct_account.account_id');
			$this->db->where('acct_account.parent_account_id', $account_id);
			$this->db->where('MONTH(acct_account_balance_detail.transaction_date)', $month);
			$this->db->where('YEAR(acct_account_balance_detail.transaction_date)', $year);
			$this->db->limit(1);
			$this->db->order_by('acct_account_balance_detail.transaction_date', 'ASC');
			$result = $this->db->get()->row_array();
			return $result['opening_balance'];
		}

		public function getSaldoAccountParent($account_id, $month, $year){
			$this->db->select('acct_account_balance_detail.opening_balance');
			$this->db->from('acct_account_balance_detail');
			$this->db->join('acct_account','acct_account_balance_detail.account_id = acct_account.account_id');
			$this->db->where('acct_account.account_id', $account_id);
			$this->db->where('MONTH(acct_account_balance_detail.transaction_date)', $month);
			$this->db->where('YEAR(acct_account_balance_detail.transaction_date)', $year);
			$this->db->limit(1);
			$this->db->order_by('acct_account_balance_detail.transaction_date', 'ASC');
			$result = $this->db->get()->row_array();
			return $result['opening_balance'];
		}


		public function getAccountChildAmount($account_id, $month, $year){
			$this->db->select('SUM(acct_account_balance_detail.account_in) AS account_in_amount, SUM(acct_account_balance_detail.account_out) AS account_out_amount');
			$this->db->from('acct_account_balance_detail');
			$this->db->join('acct_account','acct_account_balance_detail.account_id = acct_account.account_id');
			$this->db->where('acct_account.parent_account_id', $account_id);
			$this->db->where('MONTH(acct_account_balance_detail.transaction_date)', $month);
			$this->db->where('YEAR(acct_account_balance_detail.transaction_date)', $year);
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getAccountParentAmount($account_id, $month, $year){
			$this->db->select('SUM(acct_account_balance_detail.account_in) AS account_in_amount, SUM(acct_account_balance_detail.account_out) AS account_out_amount');
			$this->db->from('acct_account_balance_detail');
			$this->db->join('acct_account','acct_account_balance_detail.account_id = acct_account.account_id');
			$this->db->where('acct_account.account_id', $account_id);
			$this->db->where('MONTH(acct_account_balance_detail.transaction_date)', $month);
			$this->db->where('YEAR(acct_account_balance_detail.transaction_date)', $year);
			$result = $this->db->get()->row_array();
			return $result;
		}
		
		
	}
?>