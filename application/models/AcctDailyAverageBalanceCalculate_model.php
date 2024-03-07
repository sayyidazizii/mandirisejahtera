<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctDailyAverageBalanceCalculate_model extends CI_Model {
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
			$this->db->select('savings_id, savings_name');
			$this->db->from('acct_savings');
			$this->db->where('data_state', 0);
			$this->db->where('savings_status', 0);
			return $this->db->get()->result_array();
		}
		
		public function getAcctSavingsAccount($savings_id, $branch_id){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.member_id, core_member.member_name, core_member.member_address, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings_account.savings_account_last_balance, acct_savings_account.savings_account_daily_average_balance');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_account.branch_id', $branch_id);
			if(!empty($savings_id)){
				$this->db->where('acct_savings_account.savings_id', $savings_id);
			}
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings.savings_status', 0);
			return $this->db->get()->result_array();
		}

		public function getDailyAverageBalanceTotal($savings_account_id, $month, $year){
			$this->db->select('SUM(daily_average_balance) AS daily_average_balance_total');
			$this->db->from('acct_savings_account_detail');
			$this->db->where('savings_account_id', $savings_account_id);
			$this->db->where('MONTH(today_transaction_date)', $month);
			$this->db->where('YEAR(today_transaction_date)', $year);
			$this->db->limit(1);
			$this->db->order_by('today_transaction_date', 'ASC');
			$result = $this->db->get()->row_array();
			return $result['daily_average_balance_total'];
		}

		public function getYesterdayTransactionDate($savings_account_id){
			$this->db->select('today_transaction_date');
			$this->db->from('acct_savings_account_detail');
			$this->db->where('savings_account_id', $savings_account_id);
			$this->db->limit(1);
			$this->db->order_by('today_transaction_date', 'DESC');
			$result = $this->db->get()->row_array();
			return $result['today_transaction_date'];
		}
		
		public function getLastBalanceSRH($savings_account_id){
			$this->db->select('last_balance');
			$this->db->from('acct_savings_account_detail');
			$this->db->where('savings_account_id', $savings_account_id);
			$this->db->limit(1);
			$this->db->order_by('today_transaction_date', 'DESC');
			$result = $this->db->get()->row_array();
			return $result['last_balance'];
		}
		
		public function insertAcctSavingsAccountDetail($data){
			return $query = $this->db->insert('acct_savings_account_detail',$data);
		}

		public function updateAcctSavingsAccount($data){
			$this->db->where('savings_account_id', $data['savings_account_id']);
			if($this->db->update('acct_savings_account', $data)){
				return true;
			} else {
				return false;
			}
		}
		
		
	}
?>