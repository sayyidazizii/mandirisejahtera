<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctDepositoDailyCashMutation_model extends CI_Model {
		var $table = "acct_deposito_account";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 

		public function getCoreBranch(){
			$this->db->select('core_branch.branch_id, core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctDeposito(){
			$this->db->select('acct_deposito.deposito_id, acct_deposito.deposito_name');
			$this->db->from('acct_deposito');
			$this->db->where('acct_deposito.data_state', 0);
			return $this->db->get()->result_array();
		}
		
		public function getAcctDeposito_CashDeposit($start_date, $end_date, $deposito_id, $branch_id){
			$this->db->select('acct_deposito_account.deposito_account_id, acct_deposito_account.deposito_account_no, acct_deposito_account.member_id, core_member.member_name, acct_deposito_account.deposito_account_date, acct_deposito_account.deposito_account_due_date, acct_deposito_account.deposito_account_amount, acct_deposito_account.savings_account_id, acct_deposito_account.deposito_account_period');
			$this->db->from('acct_deposito_account');
			$this->db->join('core_member', 'acct_deposito_account.member_id = core_member.member_id');
			$this->db->where('acct_deposito_account.deposito_account_date >=', $start_date);
			$this->db->where('acct_deposito_account.deposito_account_date <=', $end_date);
			$this->db->where('acct_deposito_account.deposito_id', $deposito_id);
			if(!empty($branch_id)){
				$this->db->where('acct_deposito_account.branch_id', $branch_id);
			}
			
			$this->db->where('acct_deposito_account.deposito_account_status', 0);
			$this->db->where('acct_deposito_account.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctDeposito_CashWithdrawal($start_date, $end_date, $deposito_id, $branch_id){
			$this->db->select('acct_deposito_account.deposito_account_id, acct_deposito_account.deposito_account_no, acct_deposito_account.member_id, core_member.member_name, acct_deposito_account.deposito_account_date, acct_deposito_account.deposito_account_due_date, acct_deposito_account.deposito_account_amount, acct_deposito_account.savings_account_id, acct_deposito_account.deposito_account_period');
			$this->db->from('acct_deposito_account');
			$this->db->join('core_member', 'acct_deposito_account.member_id = core_member.member_id');
			$this->db->where('acct_deposito_account.deposito_account_closed_date >=', $start_date);
			$this->db->where('acct_deposito_account.deposito_account_closed_date <=', $end_date);
			$this->db->where('acct_deposito_account.deposito_id', $deposito_id);
			if(!empty($branch_id)){
				$this->db->where('acct_deposito_account.branch_id', $branch_id);
			}
			$this->db->where('acct_deposito_account.deposito_account_status', 1);
			$this->db->where('acct_deposito_account.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function getUsername($user_id){
			$this->db->select('username');
			$this->db->from('system_user');
			$this->db->where('user_id', $user_id);
			$result = $this->db->get()->row_array();
			return $result['username'];
		}

		public function getMutationCode($mutation_id){
			$this->db->select('mutation_code');
			$this->db->from('acct_mutation');
			$this->db->where('mutation_id', $mutation_id);
			$result = $this->db->get()->row_array();
			return $result['mutation_code'];
		}

		public function getMemberName($member_id){
			$this->db->select('member_name');
			$this->db->from('core_member');
			$this->db->where('member_id', $member_id);
			$result = $this->db->get()->row_array();
			return $result['member_name'];
		}


		public function getBranchCity($branch_id){
			$this->db->select('branch_city');
			$this->db->from('core_branch');
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['branch_city'];
		}
	}
?>