<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctSavingsIndex_model extends CI_Model {
		var $table = "acct_savings_cash_mutation";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 

		public function getAcctSavings(){
			$this->db->select('savings_id, savings_name, savings_nisbah');
			$this->db->from('acct_savings');
			$this->db->where('data_state', 0);
			$this->db->where('savings_status', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctDeposito(){
			$this->db->select('deposito_id, deposito_name, deposito_interest_rate');
			$this->db->from('acct_deposito');
			$this->db->where('data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}


		public function getDailyAverageBalance_Savings($savings_id, $branch_id){
			$this->db->select('SUM(acct_savings_account.savings_account_daily_average_balance) AS daily_average_balance_accumulation');
			$this->db->from('acct_savings_account');
			$this->db->where('acct_savings_account.savings_id', $savings_id);
			$this->db->where('acct_savings_account.branch_id', $branch_id);
			$this->db->where('acct_savings_account.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['daily_average_balance_accumulation'];
		}

		public function getDailyAverageBalanceAccumulation($branch_id){
			$this->db->select('SUM(acct_savings_account.savings_account_daily_average_balance) AS savings_account_daily_average_balance');
			$this->db->from('acct_savings_account');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_account.branch_id', $branch_id);
			$this->db->where('acct_savings.savings_status', 0);
			$this->db->where('acct_savings_account.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['savings_account_daily_average_balance'];
		}

		public function getSavingsLastBalance($month, $year, $branch_id){
			$this->db->select('acct_savings_account_detail.last_balance');
			$this->db->from('acct_savings_account_detail');
			$this->db->join('acct_savings', 'acct_savings_account_detail.savings_id = acct_savings.savings_id');
			$this->db->where('MONTH(acct_savings_account_detail.today_transaction_date)', $month);
			$this->db->where('YEAR(acct_savings_account_detail.today_transaction_date)', $year);
			$this->db->where('acct_savings_account_detail.branch_id', $branch_id);
			$this->db->where('acct_savings.savings_status', 0);
			$this->db->group_by('acct_savings_account_detail.savings_account_id');
			$this->db->order_by('acct_savings_account_detail.savings_account_id', 'DESC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getSavingsLastBalanceAccumulation($month, $year, $branch_id){
			$savings_last_balance = $this->getSavingsLastBalance($month, $year, $branch_id);

			foreach ($savings_last_balance as $key => $val) {
				$savings_account_last_balance_accumulation += $val['last_balance'];
			}

			return $savings_account_last_balance_accumulation;
		}

		public function getDepositoLastBalance_Deposito($date, $deposito_id, $branch_id){
			$this->db->select('SUM(deposito_account_amount) AS deposito_last_balance_accumulation');
			$this->db->from('acct_deposito_account');
			// $this->db->where('deposito_account_date >=', $date);
			$this->db->where('deposito_account_due_date >=', $date);
			$this->db->where('acct_deposito_account.deposito_id', $deposito_id);
			$this->db->where('acct_deposito_account.branch_id', $branch_id);
			$this->db->where('deposito_account_status', 0);
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['deposito_last_balance_accumulation'];
		}

		public function getDepositoLastBalanceAccumulation($date, $branch_id){
			$this->db->select('SUM(deposito_account_amount) AS deposito_last_balance_accumulation');
			$this->db->from('acct_deposito_account');
			// $this->db->where('deposito_account_date >=', $date);
			$this->db->where('deposito_account_due_date >=', $date);
			$this->db->where('acct_deposito_account.branch_id', $branch_id);
			$this->db->where('deposito_account_status', 0);
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['deposito_last_balance_accumulation'];
		}
		
		public function insertAcctSavingsIndex($data){
			return $query = $this->db->insert('acct_savings_index',$data);
		}
		
		public function insertAcctDepositoIndex($data){
			return $query = $this->db->insert('acct_deposito_index',$data);
		}

		public function getAcctSavingsIndexMAX($branch_id, $period){
			$this->db->select('max(acct_savings_index.savings_index_id) AS savings_index_id');
			$this->db->from('acct_savings_index');
			$this->db->join('acct_savings','acct_savings_index.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_index.branch_id', $branch_id);
			$this->db->where('acct_savings_index.savings_index_period', $period);
			$this->db->group_by('acct_savings_index.savings_id');
			$this->db->order_by('acct_savings_index.savings_index_id', 'DESC');
			return $this->db->get()->result_array();
		}

		public function getAcctSavingsIndex($savings_index_id){
			$this->db->select('acct_savings_index.savings_id, acct_savings.savings_name, acct_savings_index.savings_nisbah, acct_savings_index.savings_index_amount, acct_savings_index.savings_member_portion, acct_savings_index.savings_bmt_portion');
			$this->db->from('acct_savings_index');
			$this->db->join('acct_savings','acct_savings_index.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_index.savings_index_id', $savings_index_id);
			return $this->db->get()->row_array();
		}

		public function getAcctDepositoIndexMAX($branch_id, $period){
			$this->db->select('max(acct_deposito_index.deposito_index_id) AS deposito_index_id');
			$this->db->from('acct_deposito_index');
			$this->db->join('acct_deposito','acct_deposito_index.deposito_id = acct_deposito.deposito_id');
			$this->db->where('acct_deposito_index.branch_id', $branch_id);
			$this->db->where('acct_deposito_index.deposito_index_period', $period);
			$this->db->group_by('acct_deposito_index.deposito_id');
			$this->db->order_by('acct_deposito_index.deposito_index_id', 'DESC');
			return $this->db->get()->result_array();
		}

		public function getAcctDepositoIndex($deposito_index_id){
				$this->db->select('acct_deposito_index.deposito_id, acct_deposito.deposito_name, acct_deposito_index.deposito_nisbah, acct_deposito_index.deposito_index_amount, acct_deposito_index.deposito_member_portion, acct_deposito_index.deposito_bmt_portion');
				$this->db->from('acct_deposito_index');
				$this->db->join('acct_deposito','acct_deposito_index.deposito_id = acct_deposito.deposito_id');
				$this->db->where('acct_deposito_index.deposito_index_id', $deposito_index_id);
				return $this->db->get()->row_array();
			
		}

		public function getTotalSRH($branch_id, $period){
			$this->db->select('daily_average_balance_accumulation');
			$this->db->from('acct_savings_index');
			$this->db->where('branch_id', $branch_id);
			$this->db->where('savings_index_period', $period);
			$this->db->order_by('savings_index_id', 'DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['daily_average_balance_accumulation'];
		}

		public function getTotalIncome($branch_id, $period){
			$this->db->select('income_amount');
			$this->db->from('acct_savings_index');
			$this->db->where('branch_id', $branch_id);
			$this->db->where('savings_index_period', $period);
			$this->db->order_by('savings_index_id', 'DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['income_amount'];
		}

		public function getTotalSavings($branch_id, $period){
			$this->db->select('savings_account_last_balance_accumulation');
			$this->db->from('acct_savings_index');
			$this->db->where('branch_id', $branch_id);
			$this->db->where('savings_index_period', $period);
			$this->db->order_by('savings_index_id', 'DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['savings_account_last_balance_accumulation'];
		}

		public function getTotalDeposito($branch_id, $period){
			$this->db->select('deposito_account_last_balance_accumulation');
			$this->db->from('acct_deposito_index');
			$this->db->where('branch_id', $branch_id);
			$this->db->where('deposito_index_period', $period);
			$this->db->order_by('deposito_index_id', 'DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['deposito_account_last_balance_accumulation'];
		}

		public function getTotal($branch_id, $period){
			$totalSRH 		= $this->getTotalSRH($branch_id, $period);
			$totalIncome 	= $this->getTotalIncome($branch_id, $period);
			$totalSavings 	= $this->getTotalSavings($branch_id, $period);
			$totalDeposito 	= $this->getTotalDeposito($branch_id, $period);

			$data = array (
				'total_srh'					=> $totalSRH,
				'total_income'				=> $totalIncome,
				'total_savings_deposito'	=> $totalSavings + $totalDeposito,
			);

			return $data;
		}


		// public function getDailyAverageBalance_Savings($month, $year, $savings_id, $branch_id){
		// 	$this->db->select('SUM(acct_savings_account_detail.daily_average_balance) AS daily_average_balance_accumulation');
		// 	$this->db->from('acct_savings_account_detail');
		// 	$this->db->where('MONTH(acct_savings_account_detail.today_transaction_date)', $month);
		// 	$this->db->where('YEAR(acct_savings_account_detail.today_transaction_date)', $year);
		// 	$this->db->where('acct_savings_account_detail.savings_id', $savings_id);
		// 	$this->db->where('acct_savings_account_detail.branch_id', $branch_id);
		// 	$this->db->limit(1);
		// 	$this->db->order_by('today_transaction_date', 'ASC');
		// 	$result = $this->db->get()->row_array();
		// 	return $result['daily_average_balance_accumulation'];
		// }

		// public function getDailyAverageBalanceAccumulation($month, $year, $branch_id){
		// 	$this->db->select('SUM(acct_savings_account_detail.daily_average_balance) AS daily_average_balance_accumulation');
		// 	$this->db->from('acct_savings_account_detail');
		// 	$this->db->join('acct_savings', 'acct_savings_account_detail.savings_id = acct_savings.savings_id');
		// 	$this->db->where('MONTH(acct_savings_account_detail.today_transaction_date)', $month);
		// 	$this->db->where('YEAR(acct_savings_account_detail.today_transaction_date)', $year);
		// 	$this->db->where('acct_savings_account_detail.branch_id', $branch_id);
		// 	$this->db->where('acct_savings.savings_status', 0);
		// 	$this->db->order_by('today_transaction_date', 'DESC');
		// 	$this->db->limit(1);
		// 	$result = $this->db->get()->row_array();
		// 	return $result['daily_average_balance_accumulation'];
		// }
	}
?>