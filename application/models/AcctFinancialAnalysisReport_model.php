<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctFinancialAnalysisReport_model extends CI_Model {
		var $table = "acct_mutation";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
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

		public function getAcctFinancialAnalysisLCR(){
			$this->db->select('acct_financial_analysis.financial_analysis_id, acct_financial_analysis.report_no, acct_financial_analysis.account_id1, acct_financial_analysis.account_code1, acct_financial_analysis.account_name1, acct_financial_analysis.report_formula1, acct_financial_analysis.report_operator1, acct_financial_analysis.report_type1, acct_financial_analysis.account_id2, acct_financial_analysis.account_code2, acct_financial_analysis.account_name2, acct_financial_analysis.report_formula2, acct_financial_analysis.report_operator2, acct_financial_analysis.report_type2');
			$this->db->from('acct_financial_analysis');
			$this->db->where('acct_financial_analysis.financial_analysis_type', 1);
			$this->db->order_by('acct_financial_analysis.report_no', 'ASC');
			return $this->db->get()->result_array();
		}

		public function getAcctFinancialAnalysisCAR(){
			$this->db->select('acct_financial_analysis.financial_analysis_id, acct_financial_analysis.report_no, acct_financial_analysis.account_id1, acct_financial_analysis.account_code1, acct_financial_analysis.account_name1, acct_financial_analysis.report_formula1, acct_financial_analysis.report_operator1, acct_financial_analysis.report_type1, acct_financial_analysis.account_id2, acct_financial_analysis.account_code2, acct_financial_analysis.account_name2, acct_financial_analysis.report_formula2, acct_financial_analysis.report_operator2, acct_financial_analysis.report_type2');
			$this->db->from('acct_financial_analysis');
			$this->db->where('acct_financial_analysis.financial_analysis_type', 2);
			$this->db->order_by('acct_financial_analysis.report_no', 'ASC');
			return $this->db->get()->result_array();
		}

		public function getAcctFinancialAnalysisFDR(){
			$this->db->select('acct_financial_analysis.financial_analysis_id, acct_financial_analysis.report_no, acct_financial_analysis.account_id1, acct_financial_analysis.account_code1, acct_financial_analysis.account_name1, acct_financial_analysis.report_formula1, acct_financial_analysis.report_operator1, acct_financial_analysis.report_type1, acct_financial_analysis.account_id2, acct_financial_analysis.account_code2, acct_financial_analysis.account_name2, acct_financial_analysis.report_formula2, acct_financial_analysis.report_operator2, acct_financial_analysis.report_type2');
			$this->db->from('acct_financial_analysis');
			$this->db->where('acct_financial_analysis.financial_analysis_type', 3);
			$this->db->order_by('acct_financial_analysis.report_no', 'ASC');
			return $this->db->get()->result_array();
		}

		public function getAcctFinancialAnalysisBOPO(){
			$this->db->select('acct_financial_analysis.financial_analysis_id, acct_financial_analysis.report_no, acct_financial_analysis.account_id1, acct_financial_analysis.account_code1, acct_financial_analysis.account_name1, acct_financial_analysis.report_formula1, acct_financial_analysis.report_operator1, acct_financial_analysis.report_type1, acct_financial_analysis.account_id2, acct_financial_analysis.account_code2, acct_financial_analysis.account_name2, acct_financial_analysis.report_formula2, acct_financial_analysis.report_operator2, acct_financial_analysis.report_type2');
			$this->db->from('acct_financial_analysis');
			$this->db->where('acct_financial_analysis.financial_analysis_type', 4);
			$this->db->order_by('acct_financial_analysis.report_no', 'ASC');
			return $this->db->get()->result_array();
		}

		public function getLastBalance($branch_id, $account_id){
			$this->db->select('acct_account_balance_detail.last_balance');
			$this->db->from('acct_account_balance_detail');
			
			$this->db->where('acct_account_balance_detail.branch_id', $branch_id);
			$this->db->where('acct_account_balance_detail.account_id', $account_id);
			$this->db->limit(1);
			$this->db->order_by('acct_account_balance_detail.account_balance_detail_id', 'DESC');
			$result = $this->db->get()->row_array();
			// print_r($this->db->last_query());
			// exit;
			return $result['last_balance'];
		}

		public function getAcctProfitLossReport_Bottom(){
			$this->db->select('acct_profit_loss_report.profit_loss_report_id, acct_profit_loss_report.report_no, acct_profit_loss_report.account_id, acct_profit_loss_report.account_code, acct_profit_loss_report.account_name, acct_profit_loss_report.report_formula, acct_profit_loss_report.report_operator, acct_profit_loss_report.report_type, acct_profit_loss_report.report_tab, acct_profit_loss_report.report_bold');
			$this->db->from('acct_profit_loss_report');
			$this->db->where('acct_profit_loss_report.account_name <> " " ');
			$this->db->where('acct_profit_loss_report.account_type_id', 3);
			$this->db->order_by('acct_profit_loss_report.report_no', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
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

		public function getAcctBalanceSheetReport_Left(){
			$this->db->select('acct_balance_sheet_report.balance_sheet_report_id, acct_balance_sheet_report.report_no, acct_balance_sheet_report.account_id1, acct_balance_sheet_report.account_code1, acct_balance_sheet_report.account_name1, acct_balance_sheet_report.report_formula1, acct_balance_sheet_report.report_operator1, acct_balance_sheet_report.report_type1, acct_balance_sheet_report.report_tab1, acct_balance_sheet_report.report_bold1, acct_balance_sheet_report.account_id2, acct_balance_sheet_report.report_formula2, acct_balance_sheet_report.report_operator2, acct_balance_sheet_report.report_type2, acct_balance_sheet_report.report_tab2, acct_balance_sheet_report.report_bold2, acct_balance_sheet_report.report_formula3, acct_balance_sheet_report.report_operator3');
			$this->db->from('acct_balance_sheet_report');
			$this->db->where('acct_balance_sheet_report.data_state', 0);
			$this->db->where('acct_balance_sheet_report.balance_report_type', 1);
			$this->db->where('acct_balance_sheet_report.account_name1 <> " " ');
			$this->db->order_by('acct_balance_sheet_report.report_no', 'ASC');
			return $this->db->get()->result_array();
		}

		public function getAcctBalanceSheetReport_LeftLDR(){
			$this->db->select('acct_balance_sheet_report.balance_sheet_report_id, acct_balance_sheet_report.report_no, acct_balance_sheet_report.account_id1, acct_balance_sheet_report.account_code1, acct_balance_sheet_report.account_name1, acct_balance_sheet_report.report_formula1, acct_balance_sheet_report.report_operator1, acct_balance_sheet_report.report_type1, acct_balance_sheet_report.report_tab1, acct_balance_sheet_report.report_bold1, acct_balance_sheet_report.account_id2, acct_balance_sheet_report.report_formula2, acct_balance_sheet_report.report_operator2, acct_balance_sheet_report.report_type2, acct_balance_sheet_report.report_tab2, acct_balance_sheet_report.report_bold2, acct_balance_sheet_report.report_formula3, acct_balance_sheet_report.report_operator3, acct_balance_sheet_report.balance_report_type');
			$this->db->from('acct_balance_sheet_report');
			$this->db->where('acct_balance_sheet_report.data_state', 0);
			$this->db->where('acct_balance_sheet_report.balance_report_type1', 1);
			$this->db->where('acct_balance_sheet_report.account_name1 <> " " ');
			$this->db->order_by('acct_balance_sheet_report.report_no', 'ASC');
			// print_r($this->db->last_query());
			// exit;
			return $this->db->get()->result_array();
		}

		public function getAcctProfitLossReport_Top(){
			$this->db->select('acct_profit_loss_report.profit_loss_report_id, acct_profit_loss_report.report_no, acct_profit_loss_report.account_id, acct_profit_loss_report.account_code, acct_profit_loss_report.account_name, acct_profit_loss_report.report_formula, acct_profit_loss_report.report_operator, acct_profit_loss_report.report_type, acct_profit_loss_report.report_tab, acct_profit_loss_report.report_bold');
			$this->db->from('acct_profit_loss_report');
			$this->db->where('acct_profit_loss_report.account_name <> " " ');
			$this->db->where('acct_profit_loss_report.account_type_id', 2);
			$this->db->order_by('acct_profit_loss_report.report_no', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAccountAmount($account_id, $month, $year, $profit_loss_report_type, $branch_id){
			$this->db->select('SUM(acct_account_balance_detail.account_in) AS account_in_amount, SUM(acct_account_balance_detail.account_out) AS account_out_amount');
			$this->db->from('acct_account_balance_detail');
			$this->db->where('acct_account_balance_detail.account_id', $account_id);
			
			if ($auth['branch_status']==1) {
				$this->db->where('acct_account_balance_detail.branch_id', $branch_id);
			}

			if ($profit_loss_report_type == 1){
				$this->db->where('MONTH(acct_account_balance_detail.transaction_date)', $month);
				$this->db->where('YEAR(acct_account_balance_detail.transaction_date)', $year);
			}

			if ($profit_loss_report_type == 2){
				$this->db->where('YEAR(acct_account_balance_detail.transaction_date)', $year);
			}
			$result = $this->db->get()->row_array();
			return $result;
		}
		
		
		
	}
?>