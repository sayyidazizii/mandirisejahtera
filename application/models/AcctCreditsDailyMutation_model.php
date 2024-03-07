<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctCreditsDailyMutation_model extends CI_Model {
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

		public function getCoreOffice(){
			$this->db->select('core_office.office_id, core_office.office_name');
			$this->db->from('core_office');
			$this->db->where('core_office.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}
		
		public function getAcctCreditsAccount($start_date, $end_date, $branch_id){
			$this->db->select('acct_credits_account.credits_account_serial, acct_credits_account.member_id, core_member.member_name, core_member.member_address, acct_credits_account.credits_account_amount, acct_credits_account.credits_account_period, acct_credits_account.credits_account_due_date');
			$this->db->from('acct_credits_account');
			$this->db->join('core_member', 'acct_credits_account.member_id = core_member.member_id');
			$this->db->where('acct_credits_account.credits_account_date >=', $start_date);
			$this->db->where('acct_credits_account.credits_account_date <=', $end_date);
			if(!empty($branch_id)){
			$this->db->where('acct_credits_account.branch_id', $branch_id);
			}
			$this->db->where('acct_credits_account.credits_approve_status', 1);
			$this->db->where('acct_credits_account.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctCreditsPayment($start_date, $end_date, $branch_id, $office_id){
			$this->db->select('acct_credits_payment.credits_account_id, acct_credits_account.credits_account_serial, acct_credits_payment.member_id, core_member.member_name, core_member.member_address, acct_credits_payment.credits_payment_principal, acct_credits_payment.credits_payment_interest, acct_credits_payment.credits_principal_last_balance, acct_credits_account.credits_account_payment_amount,acct_credits_account.credits_account_accumulated_fines');
			$this->db->from('acct_credits_payment');
			$this->db->join('core_member', 'acct_credits_payment.member_id = core_member.member_id');
			$this->db->join('acct_credits_account', 'acct_credits_payment.credits_account_id = acct_credits_account.credits_account_id');
			$this->db->where('acct_credits_payment.credits_payment_date >=', $start_date);
			$this->db->where('acct_credits_payment.credits_payment_date <=', $end_date);
			if(!empty($branch_id)){
			$this->db->where('acct_credits_payment.branch_id', $branch_id);
			}

			if(!empty($office_id)){
			$this->db->where('acct_credits_account.office_id', $office_id);
			}
			$this->db->where('acct_credits_account.credits_approve_status', 1);
			$this->db->where('acct_credits_payment.data_state', 0);
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

		public function getofficeName($office_id){
			$this->db->select('office_name');
			$this->db->from('core_office');
			$this->db->where('office_id', $office_id);
			$result = $this->db->get()->row_array();
			return $result['office_name'];
		}
	}
?>