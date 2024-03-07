<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctDepositoAccountClosedReport_model extends CI_Model {
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

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			$result = $this->db->get()->row_array();
			return $result;
		}
		
		public function getAcctDepositoAccountClosed($start_date, $end_date, $branch_id){
			$this->db->select('acct_deposito_account.deposito_account_id, acct_deposito_account.deposito_id, acct_deposito_account.member_id, core_member.member_name, core_member.member_address, acct_deposito_account.deposito_account_date, acct_deposito_account.deposito_account_due_date, acct_deposito_account.deposito_account_amount, acct_deposito_account.deposito_account_no, acct_deposito_account.deposito_account_period, acct_deposito_account.deposito_account_status, acct_deposito_account.deposito_account_closed_date, acct_deposito_account.office_id');
			$this->db->from('acct_deposito_account');
			$this->db->join('acct_deposito', 'acct_deposito_account.deposito_id = acct_deposito.deposito_id');
			$this->db->join('core_member', 'acct_deposito_account.member_id = core_member.member_id');
			$this->db->where('acct_deposito_account.deposito_account_closed_date >= ', $start_date);
			$this->db->where('acct_deposito_account.deposito_account_closed_date <= ', $end_date);
			$this->db->where('acct_deposito_account.branch_id', $branch_id);
			$this->db->where('acct_deposito_account.data_state', 0);
			$this->db->where('acct_deposito_account.deposito_account_status', 1);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctDeposito(){
			$this->db->select('acct_deposito.deposito_id, acct_deposito.deposito_name');
			$this->db->from('acct_deposito');
			$this->db->where('acct_deposito.data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getUsername($user_id){
			$this->db->select('username');
			$this->db->from('system_user');
			$this->db->where('user_id', $user_id);
			$result = $this->db->get()->row_array();
			return $result['username'];
		}

		public function getOfficeCode($office_id){
			$this->db->select('office_code');
			$this->db->from('core_office');
			$this->db->where('office_id', $office_id);
			$result = $this->db->get()->row_array();
			return $result['office_code'];
		}

		public function getAcctDepositoAccountClosed_Deposito($start_date, $end_date, $deposito_id, $branch_id){
			$this->db->select('acct_deposito_account.deposito_account_id, acct_deposito_account.deposito_id, acct_deposito_account.member_id, core_member.member_name, core_member.member_address, acct_deposito_account.deposito_account_date, acct_deposito_account.deposito_account_due_date, acct_deposito_account.deposito_account_amount, acct_deposito_account.deposito_account_no, acct_deposito_account.deposito_account_period, acct_deposito_account.deposito_account_status, acct_deposito_account.deposito_account_closed_date, acct_deposito_account.office_id');
			$this->db->from('acct_deposito_account');
			$this->db->join('acct_deposito', 'acct_deposito_account.deposito_id = acct_deposito.deposito_id');
			$this->db->join('core_member', 'acct_deposito_account.member_id = core_member.member_id');
			$this->db->where('acct_deposito_account.deposito_account_closed_date >= ', $start_date);
			$this->db->where('acct_deposito_account.deposito_account_closed_date <= ', $end_date);
			$this->db->where('acct_deposito_account.deposito_id ', $deposito_id);
			$this->db->where('acct_deposito_account.branch_id ', $branch_id);
			$this->db->where('acct_deposito_account.data_state', 0);
			$this->db->where('acct_deposito_account.deposito_account_status', 1);
			$result = $this->db->get()->result_array();
			return $result;
		}

	}
?>