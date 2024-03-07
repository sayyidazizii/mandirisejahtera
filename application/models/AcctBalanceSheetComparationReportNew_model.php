<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctBalanceSheetComparationReportNew_model extends CI_Model {
		var $table = "acct_mutation";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
		} 

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			return $this->db->get()->row_array();
		}

		public function getCoreBranch(){
			$this->db->select('core_branch.branch_id, core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctBalanceSheetComparationReportNew_Left(){
			$this->db->select('acct_balance_sheet_report.balance_sheet_report_id, acct_balance_sheet_report.report_no, acct_balance_sheet_report.account_id1, acct_balance_sheet_report.account_code1, acct_balance_sheet_report.account_name1, acct_balance_sheet_report.report_formula1, acct_balance_sheet_report.report_operator1, acct_balance_sheet_report.report_type1, acct_balance_sheet_report.report_tab1, acct_balance_sheet_report.report_bold1, acct_balance_sheet_report.report_formula3, acct_balance_sheet_report.report_operator3');
			$this->db->from('acct_balance_sheet_report');
			$this->db->where('acct_balance_sheet_report.account_name1 <> " " ');
			$this->db->order_by('acct_balance_sheet_report.report_no', 'ASC');
			return $this->db->get()->result_array();
		}

		public function getAcctBalanceSheetComparationReportNew_Right(){
			$this->db->select('acct_balance_sheet_report.balance_sheet_report_id, acct_balance_sheet_report.report_no, acct_balance_sheet_report.account_id2, acct_balance_sheet_report.account_code2, acct_balance_sheet_report.account_name2, acct_balance_sheet_report.report_formula2, acct_balance_sheet_report.report_operator2, acct_balance_sheet_report.report_type2, acct_balance_sheet_report.report_tab2, acct_balance_sheet_report.report_bold2, acct_balance_sheet_report.report_formula3, acct_balance_sheet_report.report_operator3');
			$this->db->from('acct_balance_sheet_report');
			$this->db->where('acct_balance_sheet_report.account_name2 <> " " ');
			$this->db->order_by('acct_balance_sheet_report.report_no', 'ASC');
			return $this->db->get()->result_array();
		}

		public function getLastBalance($account_id, $month_period, $year_period, $account_comparation_report_type, $branch_id){
			$this->db->select('acct_account_balance_detail.last_balance');
			$this->db->from('acct_account_balance_detail');
			$this->db->where('acct_account_balance_detail.account_id', $account_id);
			$this->db->where('acct_account_balance_detail.branch_id', $branch_id);

			if ($account_comparation_report_type == 1){
				$this->db->where('MONTH(acct_account_balance_detail.transaction_date)', $month_period);
				$this->db->where('YEAR(acct_account_balance_detail.transaction_date)', $year_period);
			}

			if ($account_comparation_report_type == 2){
				$this->db->where('YEAR(acct_account_balance_detail.transaction_date)', $year_period);
			}

			$this->db->limit(1);
			$this->db->order_by('acct_account_balance_detail.account_balance_detail_id', 'DESC');
			$result = $this->db->get()->row_array();
			return $result['last_balance'];
		}

		public function getLastBalance2($account_id, $month_period, $year_period, $account_comparation_report_type, $branch_id){
			$this->db->select('acct_account_balance_detail.last_balance');
			$this->db->from('acct_account_balance_detail');
			$this->db->where('acct_account_balance_detail.account_id', $account_id);
			$this->db->where('acct_account_balance_detail.branch_id', $branch_id);
			$this->db->where('YEAR(acct_account_balance_detail.transaction_date)', $year_period);
			$this->db->limit(1);
			$this->db->order_by('acct_account_balance_detail.account_balance_detail_id', 'DESC');
			$result = $this->db->get()->row_array();
			return $result['last_balance'];
		}

		public function getOpeningBalance($account_id, $month, $year,$account_comparation_report_type, $branch_id){
			$this->db->select('acct_account_opening_balance.opening_balance');
			$this->db->from('acct_account_opening_balance');
			$this->db->where('acct_account_opening_balance.account_id', $account_id);
			$this->db->where('MONTH(acct_account_opening_balance.month_period)', $month);
			$this->db->where('YEAR(acct_account_opening_balance.year_period)', $year);
			if ($account_comparation_report_type == 1){
				$this->db->where('MONTH(acct_account_opening_balance.month_period)', $month_period);
				$this->db->where('YEAR(acct_account_opening_balance.year_period)', $year_period);
			}

			if ($account_comparation_report_type == 2){
				$this->db->where('YEAR(acct_account_opening_balance.year_period)', $year_period);
			}
			$this->db->where('acct_account_opening_balance.branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['opening_balance'];
		}

		public function getOpeningBalance2($account_id, $month, $year,$account_comparation_report_type, $branch_id){
			$this->db->select('acct_account_opening_balance.opening_balance');
			$this->db->from('acct_account_opening_balance');
			$this->db->where('acct_account_opening_balance.account_id', $account_id);
			$this->db->where('acct_account_opening_balance.month_period', $month);
			$this->db->where('acct_account_opening_balance.year_period', $year);
			if ($account_comparation_report_type == 1){
				$this->db->where('MONTH(acct_account_opening_balance.month_period)', $month_period);
				$this->db->where('YEAR(acct_account_opening_balance.year_period)', $year_period);
			}

			if ($account_comparation_report_type == 2){
				$this->db->where('YEAR(acct_account_opening_balance.year_period)', $year_period);
			}
			$this->db->where('acct_account_opening_balance.branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['opening_balance'];
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