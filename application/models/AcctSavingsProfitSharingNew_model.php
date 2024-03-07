<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctSavingsProfitSharingNew_model extends CI_Model {
		var $table = "acct_savings_cash_mutation";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			$this->CI->load->dbforge();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 

		public function getPeriodLog(){
			$this->db->select('*');
			$this->db->from('system_period_log');
			$this->db->order_by('period_log_id', 'DESC');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function getAcctSavings(){
			$this->db->select('savings_id, savings_name');
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

		public function getAcctDepositoDetail($deposito_id){
			$this->db->select('deposito_id, deposito_name, deposito_interest_rate');
			$this->db->from('acct_deposito');
			$this->db->where('deposito_id', $deposito_id);
			$this->db->where('data_state', 0);
			return $this->db->get()->row_array();
		}

		public function getAcctDepositoAccount(){
			$this->db->select('*');
			$this->db->from('acct_deposito_account');
			$this->db->where('data_state', 0);
			$this->db->where('deposito_account_blockir_status', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctDepositoAccrualCheck(){
			$this->db->select('*');
			$this->db->from('acct_deposito_accrual');
			$this->db->where('data_state', 0);
			return $this->db->get()->num_rows();
		}

		public function getAcctDepositoAccrualLast($deposito_account_id){
			$this->db->select('*');
			$this->db->from('acct_deposito_accrual');
			$this->db->where('data_state', 0);
			$this->db->where('deposito_account_id', $deposito_account_id);
			$this->db->order_by('created_on', 'DESC');
			return $this->db->get()->row_array();
		}

		public function insertDataLogStep($data){
			return $query = $this->db->insert('savings_profit_sharing_log', $data);
			
		}

		public function deleteDataLog($data){
			$this->db->where('created_id', $data['created_id']);
			$this->db->where('branch_id', $data['branch_id']);
			$this->db->where('periode', $data['periode']);
			if($this->db->delete('savings_profit_sharing_log')){
				return true;
			} else {
				return false;
			}
		}

		//--------------------Step 1 Create Table-----------------------------------//

		public function insertDataLogStep1($data, $file){
			if($this->dbforge->drop_table('acct_savings_account_detail_temp', 'acct_savings_profit_sharing_temp','acct_savings_account_temp')){
					$sql_contents = explode(";", $file);

					$no = 0;
					foreach($sql_contents as $key => $query){
						$result = $this->db->query($query);
					}

					if($this->db->insert('savings_profit_sharing_log', $data)){
						$data_log_step1 = $this->getDataLogStep1($data);

						$this->db->set('status_process', 1);
						$this->db->where('savings_profit_sharing_log_id', $data_log_step1['savings_profit_sharing_log_id']);
						if($this->db->update('savings_profit_sharing_log')){
							return true;
						} else {
							return false;
						}
					} else {
						return false;
					}
			} else {
				return false;
			}
			
		}

		public function getDataLogStep1($data){
			$this->db->select('savings_profit_sharing_log_id, branch_id, created_id, created_on, periode, step, total_account, total_process, status_process');
			$this->db->from('savings_profit_sharing_log');
			$this->db->where('step', 1);
			$this->db->where('created_id', $data['created_id']);
			$this->db->where('branch_id', $data['branch_id']);
			$this->db->where('periode', $data['periode']);
			$this->db->order_by('savings_profit_sharing_log_id', 'DESC');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function createTable($data, $file){
			if($this->dbforge->drop_table('acct_savings_account_detail_temp', 'acct_savings_profit_sharing_temp','acct_savings_account_temp')){
				$sql_contents = explode(";", $file);

				$no = 0;
				foreach($sql_contents as $key => $query){
					$result = $this->db->query($query);
				}

				$data_log_step1 = $this->getDataLogStep1($data);

				$this->db->set('status_process', 1);
				$this->db->where('savings_profit_sharing_log_id', $data_log_step1['savings_profit_sharing_log_id']);
				if($this->db->update('savings_profit_sharing_log')){
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		//--------------------Step 2 SRH--------------------------------------//

		public function getDataLogStep2($data){
			$this->db->select('savings_profit_sharing_log_id, branch_id, created_id, created_on, periode, step, total_account, total_process, status_process');
			$this->db->from('savings_profit_sharing_log');
			$this->db->where('step', 2);
			$this->db->where('created_id', $data['created_id']);
			$this->db->where('branch_id', $data['branch_id']);
			$this->db->where('periode', $data['periode']);
			$this->db->where('status_process', 1);
			$this->db->order_by('savings_profit_sharing_log_id', 'DESC');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function getAcctSavingsAccountfoSRH($branch_id){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.member_id, core_member.member_name, core_member.member_address, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings_account.savings_account_last_balance, acct_savings_account.savings_account_daily_average_balance, acct_savings_account.branch_id');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			// $this->db->where('acct_savings_account.branch_id', $branch_id);
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings.savings_status', 0);
			return $this->db->get()->result_array();
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

		public function insertAcctSavingsProfitSharingLog($data){
			return $query = $this->db->insert('savings_profit_sharing_log',$data);
		}

		public function insertAcctDepositoAccrual($data){
			return $query = $this->db->insert('acct_deposito_accrual',$data);
		}

		public function insertAcctSavingsAccountDetail($data, $log){
			if($this->db->insert_batch('acct_savings_account_detail_temp',$data)){
				$this->db->set('status_process', 1);
				$this->db->where('created_id', $log['created_id']);
				$this->db->where('periode', $log['periode']);
				$this->db->where('branch_id', $log['branch_id']);
				if($this->db->update('savings_profit_sharing_log')){
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}

		}

		public function insertAcctSavingsAccountTemp($data){
			if($this->db->insert_batch('acct_savings_account_temp',$data)){
				return true;
			} else {
				return false;
			}

		}

		//--------------------STEP 3 INDEX--------------------------------------//

		public function getDataLogStep3($data){
			$this->db->select('savings_profit_sharing_log_id, branch_id, created_id, created_on, periode, step, total_account, total_process, status_process');
			$this->db->from('savings_profit_sharing_log');
			$this->db->where('step', 3);
			$this->db->where('created_id', $data['created_id']);
			$this->db->where('branch_id', $data['branch_id']);
			$this->db->where('periode', $data['periode']);
			$this->db->where('status_process', 1);
			$this->db->order_by('savings_profit_sharing_log_id', 'DESC');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function getDailyAverageBalanceAccumulation($branch_id){
			$this->db->select('SUM(acct_savings_account_temp.savings_account_daily_average_balance) AS savings_account_daily_average_balance');
			$this->db->from('acct_savings_account_temp');
			$this->db->where('acct_savings_account_temp.branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['savings_account_daily_average_balance'];
		}

		public function getSavingsLastBalance($month, $year, $branch_id){
			$this->db->select('acct_savings_account_detail_temp.last_balance');
			$this->db->from('acct_savings_account_detail_temp');
			$this->db->join('acct_savings', 'acct_savings_account_detail_temp.savings_id = acct_savings.savings_id');
			$this->db->where('MONTH(acct_savings_account_detail_temp.today_transaction_date)', $month);
			$this->db->where('YEAR(acct_savings_account_detail_temp.today_transaction_date)', $year);
			$this->db->where('acct_savings_account_detail_temp.branch_id', $branch_id);
			$this->db->where('acct_savings.savings_status', 0);	
			$this->db->group_by('acct_savings_account_detail_temp.savings_account_id');
			$this->db->order_by('acct_savings_account_detail_temp.savings_account_detail_temp_id', 'DESC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getSavingsLastBalanceAccumulation($month, $year, $branch_id){
			$savings_last_balance = $this->getSavingsLastBalance($month, $year, $branch_id);

			$savings_account_last_balance_accumulation = 0;
			foreach ($savings_last_balance as $key => $val) {
				$savings_account_last_balance_accumulation += $val['last_balance'];
			}

			return $savings_account_last_balance_accumulation;
		}

		public function getDepositoLastBalanceAccumulation($branch_id){
			$this->db->select('SUM(deposito_account_amount) AS deposito_last_balance_accumulation');
			$this->db->from('acct_deposito_account');
			// $this->db->where('deposito_account_date >=', $date);
			// $this->db->where('deposito_account_due_date', $date);
			$this->db->where('acct_deposito_account.branch_id', $branch_id);
			$this->db->where('deposito_account_status', 0);
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['deposito_last_balance_accumulation'];
		}

		public function getDailyAverageBalance_Savings($savings_id, $branch_id){
			$this->db->select('SUM(acct_savings_account_temp.savings_account_daily_average_balance) AS daily_average_balance_accumulation');
			$this->db->from('acct_savings_account_temp');
			$this->db->where('acct_savings_account_temp.savings_id', $savings_id);
			$this->db->where('acct_savings_account_temp.branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['daily_average_balance_accumulation'];
		}

		public function getDepositoLastBalance_Deposito($deposito_id, $branch_id){
			$this->db->select('SUM(deposito_account_amount) AS deposito_last_balance_accumulation');
			$this->db->from('acct_deposito_account');
			// $this->db->where('deposito_account_date >=', $date);
			// $this->db->where('deposito_account_due_date >=', $date);
			$this->db->where('acct_deposito_account.deposito_id', $deposito_id);
			$this->db->where('acct_deposito_account.branch_id', $branch_id);
			$this->db->where('deposito_account_status', 0);
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['deposito_last_balance_accumulation'];
		}

		//--------------------STEP 4 BASIL--------------------------------------//

		public function getDataLogStep4($data){
			$this->db->select('savings_profit_sharing_log_id, branch_id, created_id, created_on, periode, step, total_account, total_process, status_process');
			$this->db->from('savings_profit_sharing_log');
			$this->db->where('step', 4);
			$this->db->where('created_id', $data['created_id']);
			$this->db->where('branch_id', $data['branch_id']);
			$this->db->where('periode', $data['periode']);
			$this->db->where('status_process', 1);
			$this->db->order_by('savings_profit_sharing_log_id', 'DESC');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function getPreferenceProfitSharing(){
			$this->db->select('*');
			$this->db->from('preference_profit_sharing');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function getSavingsInterestRate($savings_id){
			$this->db->select('savings_interest_rate');
			$this->db->from('acct_savings');
			$this->db->where('savings_id', $savings_id);
			$result = $this->db->get()->row_array();
			return $result['savings_interest_rate'];
		}

		// public function getAcctSavingsAccountforBasil($branch_id, $savings_daily_average_balance_minimum){
		// 	$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings_account.savings_account_last_balance, acct_savings_account.member_id, core_member.member_name, core_member.member_address, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.identity_id, core_member.member_identity_no, acct_savings_account.savings_account_daily_average_balance, acct_savings_account.branch_id');
		// 	$this->db->from('acct_savings_account');
		// 	$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
		// 	$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
		// 	$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
		// 	$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
		// 	$this->db->where('acct_savings_account.data_state', 0);
		// 	// $this->db->where('acct_savings_account.branch_id', $branch_id);
		// 	$this->db->where('acct_savings_account.savings_account_daily_average_balance >=', $savings_daily_average_balance_minimum);
		// 	return $this->db->get()->result_array();
		// }

		public function getAcctSavingsAccountforBasil($branch_id, $savings_daily_average_balance_minimum){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings_account.savings_account_last_balance, acct_savings_account.member_id, core_member.member_name, core_member.member_address, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.identity_id, core_member.member_identity_no, acct_savings_account.savings_account_daily_average_balance, acct_savings_account.branch_id');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->join('acct_savings_account_temp', 'acct_savings_account.savings_account_id = acct_savings_account_temp.savings_account_id');
			// $this->db->where('acct_savings_account.branch_id', $branch_id);
			$this->db->where('acct_savings_account.savings_account_last_balance >=', $savings_daily_average_balance_minimum);
			return $this->db->get()->result_array();
		}

		public function getSavingsProfitSharingTotalAmount($branch_id, $savings_daily_average_balance_minimum, $period){
			$savings_index_amount = $this->getSavingsIndexAmount($savings_id, $period);

			$this->db->select('SUM(savings_account_daily_average_balance * '.$savings_index_amount.') AS savings_profit_sharing_amount ');
			$this->db->from('acct_savings_account');
			$this->db->where('acct_savings_account.data_state', 0);
			// $this->db->where('acct_savings_account.savings_id', $savings_id);
			$this->db->where('acct_savings_account.branch_id', $branch_id);
			$this->db->where('acct_savings_account.savings_account_daily_average_balance >=', $savings_daily_average_balance_minimum);
			$result = $this->db->get()->row_array();
			return $result['savings_profit_sharing_amount'];
		}

		public function getSavingsAccountDailyAverage($savings_account_id){
			$this->db->select('savings_account_daily_average_balance');
			$this->db->from('acct_savings_account_temp');
			$this->db->where('acct_savings_account_temp.savings_account_id', $savings_account_id);
			$result = $this->db->get()->row_array();
			return $result['savings_account_daily_average_balance'];
		}

		public function getSavingsAccountLastBalance($savings_account_id){
			$this->db->select('savings_account_last_balance');
			$this->db->from('acct_savings_account');
			$this->db->where('acct_savings_account.savings_account_id', $savings_account_id);
			$result = $this->db->get()->row_array();
			return $result['savings_account_last_balance'];
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

		public function insertAcctSavingsProfitSharingTemp($data, $log){
			if($this->db->insert_batch('acct_savings_profit_sharing_temp',$data)){
				$this->db->set('status_process', 1);
				$this->db->where('created_id', $log['created_id']);
				$this->db->where('periode', $log['periode']);
				$this->db->where('branch_id', $log['branch_id']);
				if($this->db->update('savings_profit_sharing_log')){
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		//---------------------------STEP 5 UPDATE--------------------------//

		public function getPeriode($data){
			$this->db->select('periode');
			$this->db->from('savings_profit_sharing_log');
			$this->db->where('created_id', $data['user_id']);
			$this->db->where('branch_id', $data['branch_id']);
			$this->db->order_by('savings_profit_sharing_log_id', 'DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['periode'];
		}

		public function insertSystemPeriodLog($data){
			return $this->db->insert('system_period_log', $data);
		}

		public function getDataLogStep5($data){
			$this->db->select('savings_profit_sharing_log_id, branch_id, created_id, created_on, periode, step, total_account, total_process, status_process');
			$this->db->from('savings_profit_sharing_log');
			$this->db->where('step', 5);
			$this->db->where('created_id', $data['created_id']);
			$this->db->where('branch_id', $data['branch_id']);
			$this->db->where('periode', $data['periode']);
			$this->db->order_by('savings_profit_sharing_log_id', 'DESC');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function getCoreBranch(){
			$this->db->select('*');
			$this->db->from('core_branch');
			// $this->db->where('branch_id != 1');
			$this->db->where('data_state', 0);
			return $this->db->get()->result_array();
		}

		public function insertAcctSavingsProfitSharingFix(){
			$query = $this->db->query("INSERT INTO acct_savings_profit_sharing (branch_id, savings_account_id,
			savings_id, member_id, savings_profit_sharing_date, savings_daily_average_balance_minimum, savings_daily_average_balance, savings_profit_sharing_amount, savings_account_last_balance, savings_profit_sharing_period, savings_profit_sharing_token, operated_name, created_id, created_on, savings_tax_amount, savings_interest_amount) SELECT branch_id, savings_account_id,
			savings_id, member_id, savings_profit_sharing_temp_date, savings_daily_average_balance_minimum, savings_daily_average_balance, savings_profit_sharing_temp_amount, savings_account_last_balance, savings_profit_sharing_temp_period, savings_profit_sharing_temp_token, operated_name, created_id, created_on, savings_tax_temp_amount, savings_interest_temp_amount FROM acct_savings_profit_sharing_temp");
			if($query){
				return true;
			} else {
				return false;
			}
		}

		public function getTotalSavingsProfitSharing($branch_id){
			$this->db->select('SUM(savings_profit_sharing_temp_amount) AS savings_profit_sharing_amount');
			$this->db->from('acct_savings_profit_sharing_temp');
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['savings_profit_sharing_amount'];
		}

		public function getTotalSavingsProfitSharingTax($branch_id){
			$this->db->select('SUM(savings_tax_temp_amount) AS savings_tax_amount');
			$this->db->from('acct_savings_profit_sharing_temp');
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['savings_tax_amount'];
		}

		public function insertAcctSavingsTransferMutation($data){
			return $query = $this->db->insert('acct_savings_transfer_mutation',$data);
		}

		public function getSavingsTranferMutationID($created_id){
			$this->db->select('acct_savings_transfer_mutation.savings_transfer_mutation_id');
			$this->db->from('acct_savings_transfer_mutation');
			$this->db->where('acct_savings_transfer_mutation.created_id', $created_id);
			$this->db->order_by('acct_savings_transfer_mutation.created_on', 'DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['savings_transfer_mutation_id'];
		}

		public function insertAcctSavingsTransferMutationTo($savings_transfer_mutation_id, $branch_id){
			$preference_company = $this->getPreferenceCompany();

			$query = $this->db->query("INSERT INTO acct_savings_transfer_mutation_to (savings_transfer_mutation_id, savings_account_id, savings_id, branch_id, member_id, mutation_id, savings_transfer_mutation_to_amount, savings_account_last_balance) SELECT ".$savings_transfer_mutation_id.", savings_account_id, savings_id, branch_id, member_id, ".$preference_company['savings_profit_sharing_id'].", (savings_profit_sharing_temp_amount - savings_tax_temp_amount), savings_account_last_balance FROM acct_savings_profit_sharing_temp where branch_id = ".$branch_id."");
			if($query){
				return true;
			} else {
				return false;
			}
		}

		public function getSubTotalSavingsProfitSharing($savings_id, $branch_id){
			$this->db->select('SUM(savings_profit_sharing_temp_amount) AS savings_profit_sharing_amount');
			$this->db->from('acct_savings_profit_sharing_temp');
			$this->db->where('savings_id', $savings_id);
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['savings_profit_sharing_amount'];
		}

		public function getSubTotalSavingsTax($savings_id, $branch_id){
			$this->db->select('SUM(savings_tax_temp_amount) AS savings_tax_amount');
			$this->db->from('acct_savings_profit_sharing_temp');
			$this->db->where('savings_id', $savings_id);
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['savings_tax_amount'];
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

		public function getDepositoAccountID($deposito_id){
			$this->db->select('acct_deposito.account_id');
			$this->db->from('acct_deposito');
			$this->db->where('acct_deposito.deposito_id', $deposito_id);
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

		public function getAcctSavingsProfitSharingTemp($branch_id){
			$this->db->select('acct_savings_profit_sharing_temp.savings_account_id, acct_savings_account.savings_account_no, acct_savings_profit_sharing_temp.member_id, core_member.member_name, core_member.member_address, acct_savings_profit_sharing_temp.savings_profit_sharing_temp_amount, acct_savings_profit_sharing_temp.savings_account_last_balance, acct_savings_profit_sharing_temp.savings_profit_sharing_temp_period, acct_savings_profit_sharing_temp.savings_interest_temp_amount, acct_savings_profit_sharing_temp.savings_tax_temp_amount');
			$this->db->from('acct_savings_profit_sharing_temp');
			$this->db->join('acct_savings_account','acct_savings_profit_sharing_temp.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_member', 'acct_savings_profit_sharing_temp.member_id = core_member.member_id');
			// $this->db->where('acct_savings_profit_sharing_temp.branch_id', $branch_id);
			return $this->db->get()->result_array();
		}


		//----------Test SRH---------//

		public function getAcctSavingsAccountDetail($period, $savings_account_id){
			$this->db->select('*');
			$this->db->from('acct_savings_account_detail');
			$this->db->where('savings_account_id', $savings_account_id);
			$this->db->where('MONTH(today_transaction_date)', $period);
			return $this->db->get()->result_array();
		}

		public function getAcctSavingsAccountDetailForInterest($period, $year, $savings_account_id){
			$this->db->select('*');
			$this->db->from('acct_savings_account_detail');
			$this->db->where('savings_account_id', $savings_account_id);
			$this->db->where('MONTH(today_transaction_date)', $period);
			$this->db->where('YEAR(today_transaction_date)', $year);
			$this->db->order_by('today_transaction_date', 'ASC');
			return $this->db->get()->result_array();
		}
		
		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}
		
	
	}
?>