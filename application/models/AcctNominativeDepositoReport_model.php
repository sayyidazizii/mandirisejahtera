<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctNominativeDepositoReport_model extends CI_Model {
		var $table = "acct_deposito_account";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getAcctNomintiveDepositoReport($start_date, $branch_id){
			$this->db->select('acct_deposito_account.deposito_account_id, acct_deposito_account.deposito_id, acct_deposito_account.member_id, core_member.member_name, core_member.member_address, acct_deposito_account.deposito_account_date, acct_deposito_account.deposito_account_due_date, acct_deposito_account.deposito_account_amount, acct_deposito_account.deposito_account_no, acct_deposito_account.deposito_account_period, acct_deposito_account.deposito_account_status, acct_deposito.deposito_interest_rate');
			$this->db->from('acct_deposito_account');
			$this->db->join('acct_deposito', 'acct_deposito_account.deposito_id = acct_deposito.deposito_id');
			$this->db->join('core_member', 'acct_deposito_account.member_id = core_member.member_id');
			$this->db->where('acct_deposito_account.deposito_account_date <= ', $start_date);
			if(!empty($branch_id)){
				$this->db->where('acct_deposito_account.branch_id', $branch_id);
			}
			$this->db->where('acct_deposito_account.data_state', 0);
			$this->db->where('acct_deposito_account.deposito_account_status', 0);
			$this->db->order_by('acct_deposito_account.deposito_account_id', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_id', 'ASC');
			$this->db->order_by('acct_deposito_account.member_id', 'ASC');
			$this->db->order_by('core_member.member_name', 'ASC');
			$this->db->order_by('core_member.member_address', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_account_date', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_account_due_date', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_account_amount', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_account_no', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_account_period', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_account_status', 'ASC');
			$result = $this->db->get()->result_array();
			// print_r($this->db->last_query());exit;
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

		public function getAcctDeposito(){
			$this->db->select('acct_deposito.deposito_id, acct_deposito.deposito_name');
			$this->db->from('acct_deposito');
			$this->db->where('acct_deposito.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getUsername($user_id){
			$this->db->select('username');
			$this->db->from('system_user');
			$this->db->where('user_id', $user_id);
			$result = $this->db->get()->row_array();
			return $result['username'];
		}

		public function getAcctNomintiveDepositoReport_Deposito($start_date, $deposito_id){
			$this->db->select('acct_deposito_account.deposito_account_id, acct_deposito_account.deposito_id, acct_deposito_account.member_id, core_member.member_name, core_member.member_address, acct_deposito_account.deposito_account_date, acct_deposito_account.deposito_account_due_date, acct_deposito_account.deposito_account_amount, acct_deposito_account.deposito_account_no, acct_deposito_account.deposito_account_period, acct_deposito_account.deposito_account_status, acct_deposito.deposito_interest_rate');
			$this->db->from('acct_deposito_account');
			$this->db->join('acct_deposito', 'acct_deposito_account.deposito_id = acct_deposito.deposito_id');
			$this->db->join('core_member', 'acct_deposito_account.member_id = core_member.member_id');
			$this->db->where('acct_deposito_account.deposito_account_date <= ', $start_date);
			$this->db->where('acct_deposito_account.deposito_id ', $deposito_id);
			// if(!empty($branch_id)){
			// 	$this->db->where('acct_deposito_account.branch_id', $branch_id);
			// }
			$this->db->where('acct_deposito_account.data_state', 0);
			$this->db->where('acct_deposito_account.deposito_account_status', 0);
			$this->db->order_by('acct_deposito_account.deposito_account_id', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_id', 'ASC');
			$this->db->order_by('acct_deposito_account.member_id', 'ASC');
			$this->db->order_by('core_member.member_name', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_account_date', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_account_due_date', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_account_amount', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_account_no', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_account_period', 'ASC');
			$this->db->order_by('acct_deposito_account.deposito_account_status', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

	}
?>