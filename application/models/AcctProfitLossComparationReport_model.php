<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctProfitLossComparationReport_model extends CI_Model {
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

		public function getAcctProfitLossComparationReport_Top($format_id){
			$this->db->select('acct_profit_loss_report.profit_loss_report_id, acct_profit_loss_report.report_no, acct_profit_loss_report.account_id, acct_profit_loss_report.account_code, acct_profit_loss_report.account_name, acct_profit_loss_report.report_formula, acct_profit_loss_report.report_operator, acct_profit_loss_report.report_type, acct_profit_loss_report.report_tab, acct_profit_loss_report.report_bold');
			$this->db->from('acct_profit_loss_report');
			$this->db->where('acct_profit_loss_report.account_name <> " " ');
			$this->db->where('acct_profit_loss_report.account_type_id', 2);
			$this->db->where('acct_profit_loss_report.format_id', $format_id);
			$this->db->order_by('acct_profit_loss_report.report_no', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctProfitLossComparationReport_Bottom(){
			$this->db->select('acct_profit_loss_report.profit_loss_report_id, acct_profit_loss_report.report_no, acct_profit_loss_report.account_id, acct_profit_loss_report.account_code, acct_profit_loss_report.account_name, acct_profit_loss_report.report_formula, acct_profit_loss_report.report_operator, acct_profit_loss_report.report_type, acct_profit_loss_report.report_tab, acct_profit_loss_report.report_bold');
			$this->db->from('acct_profit_loss_report');
			$this->db->where('acct_profit_loss_report.account_name <> " " ');
			$this->db->where('acct_profit_loss_report.account_type_id', 3);
			$this->db->order_by('acct_profit_loss_report.report_no', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getLastBalance($account_id){
			$this->db->select('acct_account_balance_detail.last_balance');
			$this->db->from('acct_account_balance_detail');
			$this->db->where('acct_account_balance_detail.account_id', $account_id);
			$this->db->limit(1);
			$this->db->order_by('acct_account_balance_detail.account_balance_detail_id', 'DESC');
			$result = $this->db->get()->row_array();
			return $result['last_balance'];
		}

		public function getAccountListParent($length, $account_id){
			$this->db->select('account_id, account_code, account_name');
			$this->db->from('acct_account');
			$this->db->where('LEFT(account_code,'.$length.')', $account_id);
			$this->db->where('account_status', 1);
			$this->db->where('data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
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

		public function getAccountAmount($account_id, $month, $year, $profit_loss_report_type, $branch_id){
			$this->db->select('SUM(acct_account_balance_detail.account_in) AS account_in_amount, SUM(acct_account_balance_detail.account_out) AS account_out_amount');
			$this->db->from('acct_account_balance_detail');
			$this->db->where('acct_account_balance_detail.account_id', $account_id);
			$this->db->where('acct_account_balance_detail.branch_id', $branch_id);


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