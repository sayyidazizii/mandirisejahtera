<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctMemorialJournal_model extends CI_Model {
		var $table = "acct_savings_account";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getAcctMemorialJournal($start_date, $end_date, $branch_id){
			$this->db->select('acct_journal_voucher_item.journal_voucher_item_id, acct_journal_voucher_item.journal_voucher_description, acct_journal_voucher_item.journal_voucher_debit_amount, acct_journal_voucher_item.journal_voucher_credit_amount, acct_journal_voucher_item.account_id, acct_account.account_code, acct_account.account_name, acct_journal_voucher_item.account_id_status, acct_journal_voucher.transaction_module_code, acct_journal_voucher.journal_voucher_date, acct_journal_voucher.journal_voucher_id');
			$this->db->from('acct_journal_voucher_item');
			$this->db->join('acct_journal_voucher','acct_journal_voucher_item.journal_voucher_id = acct_journal_voucher.journal_voucher_id');
			$this->db->join('acct_account','acct_journal_voucher_item.account_id = acct_account.account_id');
			$this->db->where('acct_journal_voucher.journal_voucher_date >=', $start_date);
			$this->db->where('acct_journal_voucher.journal_voucher_date <=', $end_date);
			$this->db->where('acct_journal_voucher.branch_id', $branch_id);
			$this->db->where('acct_journal_voucher.data_state', 0);
			$this->db->where('acct_journal_voucher_item.data_state', 0);
			$this->db->where('acct_journal_voucher.posted', 0);
			$this->db->where('acct_journal_voucher_item.journal_voucher_amount <>', 0);	
			$this->db->where('acct_journal_voucher.transaction_module_id !=', 10);	
			$this->db->order_by('acct_journal_voucher_item.journal_voucher_item_id', 'ASC');
			$result = $this->db->get()->result_array();
			// print_r($this->db->last_query());
			return $result;
		}

		public function getCoreBranch(){
			$this->db->select('core_branch.branch_id, core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctSavings(){
			$this->db->select('savings_id, savings_name');
			$this->db->from('acct_savings');
			$this->db->where('data_state', 0);
			$this->db->where('savings_status', 0);
			return $this->db->get()->result_array();
		}

		public function getMinID($journal_voucher_id){
			$this->db->select('journal_voucher_item_id');
			$this->db->from('acct_journal_voucher_item');
			$this->db->where('journal_voucher_id', $journal_voucher_id);
			$result = $this->db->get()->row_array();
			return $result['journal_voucher_item_id'];
		}

		public function getAcctSavingsAccount_Detail($savings_account_id){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.member_id, core_member.member_name, core_member.member_no, core_member.member_gender, core_member.member_address, core_member.member_phone, core_member.member_date_of_birth, core_member.member_identity_no, core_member.city_id, core_member.kecamatan_id, core_member.identity_id, core_member.member_job, acct_savings_account.savings_id, acct_savings.savings_code, acct_savings.savings_name, acct_savings_account.savings_account_no, acct_savings_account.savings_account_date, acct_savings_account.savings_account_first_deposit_amount, acct_savings_account.savings_account_last_balance, acct_savings_account.voided_remark, acct_savings_account.validation, acct_savings_account.validation_on, acct_savings_account.validation_id, acct_savings_account.office_id');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings_account.savings_account_id', $savings_account_id);
			return $this->db->get()->row_array();
		}


		public function getAcctSavingsAccountDetail($savings_account_id, $start_date, $end_date){
			$this->db->select('acct_savings_account_detail.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account_detail.member_id, core_member.member_name, core_member.member_address, acct_savings_account_detail.mutation_id, acct_mutation.mutation_status, acct_mutation.mutation_code, acct_savings_account_detail.mutation_in, acct_savings_account_detail.mutation_out, acct_savings_account_detail.opening_balance, acct_savings_account_detail.last_balance, acct_savings_account_detail.savings_id, acct_savings.savings_name, acct_savings_account_detail.today_transaction_date');
			$this->db->from('acct_savings_account_detail');
			$this->db->join('core_member', 'acct_savings_account_detail.member_id = core_member.member_id');
			$this->db->join('acct_savings_account', 'acct_savings_account_detail.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('acct_savings', 'acct_savings_account_detail.savings_id = acct_savings.savings_id');
			$this->db->join('acct_mutation', 'acct_savings_account_detail.mutation_id = acct_mutation.mutation_id');
			$this->db->where('acct_savings_account_detail.today_transaction_date >=', $start_date);
			$this->db->where('acct_savings_account_detail.today_transaction_date <=', $end_date);
			$this->db->where('acct_savings_account_detail.savings_account_id', $savings_account_id);
			$result = $this->db->get()->result_array();
			return $result;
		}


		public function getBranchCode($branch_id){
			$this->db->select('branch_code');
			$this->db->from('core_branch');
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['branch_code'];
		}

		public function getSavingsCode($savings_id){
			$this->db->select('savings_code');
			$this->db->from('acct_savings');
			$this->db->where('savings_id', $savings_id);
			$result = $this->db->get()->row_array();
			return $result['savings_code'];
		}

		public function getSavingsNisbah($savings_id){
			$this->db->select('savings_nisbah');
			$this->db->from('acct_savings');
			$this->db->where('savings_id', $savings_id);
			$result = $this->db->get()->row_array();
			return $result['savings_nisbah'];
		}

		public function getCityName($city_id){
			$this->db->select('city_name');
			$this->db->from('core_city');
			$this->db->where('city_id', $city_id);
			$result = $this->db->get()->row_array();
			return $result['city_name'];
		}

		public function getKecamatanName($kecamatan_id){
			$this->db->select('kecamatan_name');
			$this->db->from('core_kecamatan');
			$this->db->where('kecamatan_id', $kecamatan_id);
			$result = $this->db->get()->row_array();
			return $result['kecamatan_name'];
		}

		public function getUsername($user_id){
			$this->db->select('username');
			$this->db->from('system_user');
			$this->db->where('user_id', $user_id);
			$result = $this->db->get()->row_array();
			return $result['username'];
		}

		public function getBranchCity($branch_id){
			$this->db->select('branch_city');
			$this->db->from('core_branch');
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['branch_city'];
		}

		public function getLastAccountSavingsNo($savings_id){
			$this->db->select('savings_account_no');
			$this->db->from('acct_savings_account');
			$this->db->where('savings_id', $savings_id);
			$this->db->limit(1);
			$this->db->order_by('savings_account_id','DESC');
			$result = $this->db->get()->row_array();
			return $result['savings_account_no'];
		}
		
		
		public function getCoreMember_Detail($member_id){
			$this->db->select('core_member.member_id, core_member.branch_id, core_branch.branch_name, core_member.member_no, core_member.member_name, core_member.member_gender, core_member.member_place_of_birth, core_member.member_date_of_birth, core_member.member_address, core_member.province_id, core_province.province_name, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.member_phone, core_member.member_job, core_member.identity_id, core_member.member_identity_no, core_member.member_postal_code, core_member.member_mother, core_member.member_heir, core_member.member_family_relationship, core_member.member_status, core_member.member_register_date, core_member.member_principal_savings, core_member.member_special_savings, core_member.member_mandatory_savings');
			$this->db->from('core_member');
			$this->db->join('core_province', 'core_member.province_id = core_province.province_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->join('core_branch', 'core_member.branch_id = core_branch.branch_id');
			$this->db->where('core_member.data_state', 0);
			$this->db->where('core_member.member_id', $member_id);
			return $this->db->get()->row_array();
		}
	}
?>