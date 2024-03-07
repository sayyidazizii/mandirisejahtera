<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctSavingsProfitSharing_model extends CI_Model {
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
			$this->db->select('savings_id, savings_name, savings_interest_rate');
			$this->db->from('acct_savings');
			$this->db->where('data_state', 0);
			$this->db->where('savings_status', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctSavingsAccount($savings_id, $branch_id, $savings_daily_average_balance_minimum){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings_account.savings_account_last_balance, acct_savings_account.member_id, core_member.member_name, core_member.member_address, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.identity_id, core_member.member_identity_no, acct_savings_account.savings_account_daily_average_balance');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings_account.savings_id', $savings_id);
			$this->db->where('acct_savings_account.branch_id', $branch_id);
			$this->db->where('acct_savings_account.savings_account_daily_average_balance >=', $savings_daily_average_balance_minimum);
			return $this->db->get()->result_array();
		}

		public function getSavingsInterest($savings_id){
			$this->db->select('savings_interest_rate');
			$this->db->from('acct_savings');
			$this->db->where('savings_id', $savings_id);
			$result = $this->db->get()->row_array();
			return $result['savings_interest_rate'];
		}

		public function getSavingsProfitSharingTotalAmount($savings_id, $branch_id, $savings_daily_average_balance_minimum, $period){
			$savings_interest_rate = $this->getSavingsInterest($savings_id);

			$this->db->select('SUM((savings_account_daily_average_balance * '.$savings_interest_rate.')/100) AS savings_profit_sharing_amount ');
			$this->db->from('acct_savings_account');
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings_account.savings_id', $savings_id);
			$this->db->where('acct_savings_account.branch_id', $branch_id);
			$this->db->where('acct_savings_account.savings_account_daily_average_balance >=', $savings_daily_average_balance_minimum);
			$result = $this->db->get()->row_array();
			return $result['savings_profit_sharing_amount'];
		}

		public function getSavingsAccountDailyAverageBalance($savings_id, $branch_id, $savings_daily_average_balance_minimum){
			$this->db->select('savings_account_daily_average_balance');
			$this->db->from('acct_savings_account');
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings_account.savings_id', $savings_id);
			$this->db->where('acct_savings_account.branch_id', $branch_id);
			$this->db->where('acct_savings_account.savings_account_daily_average_balance >=', $savings_daily_average_balance_minimum);
			$result = $this->db->get()->row_array();
			return $result['savings_account_daily_average_balance'];
		}

		public function getSavingsIndexAmount($savings_id, $period){
			$this->db->select('savings_index_amount');
			$this->db->from('acct_savings_index');
			$this->db->where('savings_id', $savings_id);
			$this->db->where('savings_index_period', $period);
			$this->db->limit(1);
			$this->db->order_by('last_update', 'DESC');
			$result = $this->db->get()->row_array();
			return $result['savings_index_amount'];
		}

		public function getSavingsAccountID($data){
			$this->db->select('savings_account_id');
			$this->db->from('acct_savings_profit_sharing');
			$this->db->where('savings_profit_sharing_period', $data['savings_profit_sharing_period']);
			$this->db->where('savings_account_id', $data['savings_account_id']);
			return $this->db->get();
		}

		public function updateAcctSavingsProfitSharingLog($data){
			$savings_profit_sharing_total_savings_process = 1;
			$this->db->set('acct_savings_profit_sharing_log.savings_profit_sharing_total_savings_process','acct_savings_profit_sharing_log.savings_profit_sharing_total_savings_process + '. (int)$savings_profit_sharing_total_savings_process, FALSE);
			$this->db->where('acct_savings_profit_sharing_log.savings_profit_sharing_log_id', $data['savings_profit_sharing_log_id']);
			if($this->db->update('acct_savings_profit_sharing_log')){
				return true;
			} else {
				return false;
			}
		}
		
		public function insertAcctSavingsProfitSharing($data){
			return $query = $this->db->insert('acct_savings_profit_sharing',$data);			
		}

		public function getTotalSavingsProfitSharing($savings_id, $period, $date){
			$this->db->select('SUM(savings_profit_sharing_amount) AS savings_profit_sharing_amount');
			$this->db->from('acct_savings_profit_sharing');
			$this->db->where('savings_id', $savings_id);
			$this->db->where('savings_profit_sharing_period', $period);
			$this->db->where('savings_profit_sharing_date', $date);
			$result = $this->db->get()->row_array();
			return $result['savings_profit_sharing_amount'];
		}

		public function insertAcctSavingsProfitSharingLog($data){
			return $query = $this->db->insert('acct_savings_profit_sharing_log',$data);
		}

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function insertAcctSavingsTransferMutation($data){
			return $query = $this->db->insert('acct_savings_transfer_mutation',$data);
		}

		public function getSavingsTranferMutationID($created_id){
			$this->db->select('acct_savings_transfer_mutation.savings_transfer_mutation_id');
			$this->db->from('acct_savings_transfer_mutation');
			$this->db->where('acct_savings_transfer_mutation.created_id', $created_id);
			$this->db->order_by('acct_savings_transfer_mutation.created_id', 'DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['savings_transfer_mutation_id'];
		}

		public function insertAcctSavingsTransferMutationTo($data){
			return $query = $this->db->insert('acct_savings_transfer_mutation_to',$data);
		}

		public function getAcctSavingsProfitSharingLog_Detail($created_id, $savings_id, $period){
			$this->db->select('acct_savings_profit_sharing_log.savings_profit_sharing_log_id, acct_savings_profit_sharing_log.savings_profit_sharing_period, acct_savings_profit_sharing_log.savings_profit_sharing_date, acct_savings_profit_sharing_log.savings_id, acct_savings.savings_name, acct_savings_profit_sharing_log.branch_id, acct_savings_profit_sharing_log.savings_profit_sharing_total_amount, acct_savings_profit_sharing_log.savings_profit_sharing_total_savings, acct_savings_profit_sharing_log.savings_profit_sharing_total_savings_process');
			$this->db->from('acct_savings_profit_sharing_log');
			$this->db->join('acct_savings', 'acct_savings_profit_sharing_log.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_profit_sharing_log.savings_id', $savings_id);
			$this->db->where('acct_savings_profit_sharing_log.created_id', $created_id);
			$this->db->where('acct_savings_profit_sharing_log.savings_profit_sharing_period', $period);
			$this->db->order_by('acct_savings_profit_sharing_log.created_id', 'DESC');
			$this->db->limit(1);
			return $this->db->get()->row_array();

		}
		
		public function getTransactionModuleID($transaction_module_code){
			$this->db->select('preference_transaction_module.transaction_module_id');
			$this->db->from('preference_transaction_module');
			$this->db->where('preference_transaction_module.transaction_module_code', $transaction_module_code);
			$result = $this->db->get()->row_array();
			return $result['transaction_module_id'];
		}

		public function getJournalVoucherToken($journal_voucher_token){
			$this->db->select('journal_voucher_token');
			$this->db->from('acct_journal_voucher');
			$this->db->where('journal_voucher_token', $journal_voucher_token);
			return $this->db->get();
		}

		public function insertAcctJournalVoucher($data){
			if ($this->db->insert('acct_journal_voucher', $data)){
				return true;
			}else{
				return false;
			}
		}

		public function getJournalVoucherID($created_id){
			$this->db->select('acct_journal_voucher.journal_voucher_id');
			$this->db->from('acct_journal_voucher');
			$this->db->where('acct_journal_voucher.created_id', $created_id);
			$this->db->order_by('acct_journal_voucher.journal_voucher_id', 'DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['journal_voucher_id'];
		}

		public function getAccountID($savings_id){
			$this->db->select('acct_savings.account_id');
			$this->db->from('acct_savings');
			$this->db->where('acct_savings.savings_id', $savings_id);
			$result = $this->db->get()->row_array();
			return $result['account_id'];
		}

		public function getAccountBasilID($savings_id){
			$this->db->select('acct_savings.account_basil_id');
			$this->db->from('acct_savings');
			$this->db->where('acct_savings.savings_id', $savings_id);
			$result = $this->db->get()->row_array();
			return $result['account_basil_id'];
		}
	
		public function getAccountIDDefaultStatus($account_id){
			$this->db->select('acct_account.account_default_status');
			$this->db->from('acct_account');
			$this->db->where('acct_account.account_id', $account_id);
			$this->db->where('acct_account.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['account_default_status'];
		}

		public function insertAcctJournalVoucherItem($data){
			if($this->db->insert('acct_journal_voucher_item', $data)){
				return true;
			}else{
				return false;
			}
		}
	
	}
?>