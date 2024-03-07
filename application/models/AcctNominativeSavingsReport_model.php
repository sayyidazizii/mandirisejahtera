<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctNominativeSavingsReport_model extends CI_Model {
		var $table = "acct_savings_account";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getAcctNomintiveSavingsReport(){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.member_id, core_member.member_name, core_member.member_address, acct_savings_account.savings_account_date, acct_savings_account.savings_account_last_balance, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings.savings_status, acct_savings.savings_interest_rate');
			$this->db->from('acct_savings_account');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->where('acct_savings_account.data_state ', 0);
			$this->db->where('acct_savings.savings_status ', 0);
			$this->db->order_by('acct_savings_account.savings_account_no', 'ASC');
			$this->db->order_by('acct_savings_account.savings_account_id', 'ASC');
			$this->db->order_by('acct_savings_account.member_id', 'ASC');
			$this->db->order_by('core_member.member_name', 'ASC');
			$this->db->order_by('core_member.member_address', 'ASC');
			$this->db->order_by('acct_savings_account.savings_account_date', 'ASC');
			$this->db->order_by('acct_savings_account.savings_account_last_balance', 'ASC');
			$this->db->order_by('acct_savings_account.savings_id', 'ASC');			
			$result = $this->db->get()->result_array();
			return $result;
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

		public function getAcctSavings(){
			$this->db->select('acct_savings.savings_id, acct_savings.savings_name');
			$this->db->from('acct_savings');
			$this->db->where('acct_savings.data_state', 0);
			$this->db->where('acct_savings.savings_status', 0);
			return $this->db->get()->result_array();
		}

		public function getUsername($user_id){
			$this->db->select('username');
			$this->db->from('system_user');
			$this->db->where('user_id', $user_id);
			$result = $this->db->get()->row_array();
			return $result['username'];
		}

		public function getSavingsName($savings_id){
			$this->db->select('savings_name');
			$this->db->from('acct_savings');
			$this->db->where('savings_id', $savings_id);
			$result = $this->db->get()->row_array();
			return $result['savings_name'];
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

		public function getAcctSavingsProfitSharing($savings_account_id, $period, $branch_id){
			$this->db->select('acct_savings_profit_sharing.savings_account_id, acct_savings_account.savings_account_no, acct_savings_profit_sharing.member_id, core_member.member_name, core_member.member_address, acct_savings_profit_sharing.savings_profit_sharing_amount, acct_savings_profit_sharing.savings_daily_average_balance, acct_savings_profit_sharing.savings_account_last_balance');
			$this->db->from('acct_savings_profit_sharing');
			$this->db->join('acct_savings_account', 'acct_savings_profit_sharing.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_member', 'acct_savings_profit_sharing.member_id = core_member.member_id');
			$this->db->where('acct_savings_profit_sharing.savings_profit_sharing_period', $period);
			if(empty($savings_account_id)){
			$this->db->where('acct_savings_profit_sharing.savings_account_id', $savings_account_id);
			}
			if($branch_id != ''){
				$this->db->where('acct_savings_profit_sharing.branch_id', $branch_id);
			}
			$result = $this->db->get()->row_array();
			return $result;
		}

	}
?>