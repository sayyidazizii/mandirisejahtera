<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class CoreMemberTransferMutation_model extends CI_Model {
		var $table = "core_member_transfer_mutation";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getCoreMemberTransferMutation($start_date, $end_date, $member_id){
			$this->db->select('core_member_transfer_mutation.member_transfer_mutation_id, core_member_transfer_mutation.member_transfer_mutation_date, core_member_transfer_mutation.member_mandatory_savings, core_member_transfer_mutation.validation, core_member_transfer_mutation.validation_id, core_member_transfer_mutation.validation_on, core_member_transfer_mutation.member_id, core_member.member_name, core_member.member_no, core_member_transfer_mutation.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.member_id as member_id_savings');
			$this->db->from('core_member_transfer_mutation');

			$this->db->join('acct_savings_account', 'core_member_transfer_mutation.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_member', 'core_member_transfer_mutation.member_id = core_member.member_id');

			$this->db->where('core_member_transfer_mutation.member_transfer_mutation_date >=', $start_date);
			$this->db->where('core_member_transfer_mutation.member_transfer_mutation_date <=', $end_date);
			if(!empty($member_id)){
				$this->db->where('core_member_transfer_mutation.member_id ', $member_id);
			}
			$this->db->where('core_member_transfer_mutation.data_state', 0);
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

		public function getAcctSavingsAccountData($savings_id){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.savings_account_no, core_member.member_name, acct_savings.savings_name, acct_savings_account.savings_account_date, acct_savings_account.savings_account_last_balance');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings.savings_status', 0);
			if(!empty($savings_id)){
				$this->db->where('acct_savings_account.savings_id', $savings_id);
			}
			return $this->db->get()->result_array();
		}

		public function getCoreMember(){
			$this->db->select('core_member.member_id, CONCAT(core_member.member_no," - ",core_member.member_name) AS member_name');
			$this->db->from('core_member');
			$this->db->where('core_member.data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctMutation(){
			$this->db->select('acct_mutation.mutation_id, CONCAT(acct_mutation.mutation_code, " - " ,acct_mutation.mutation_name) AS mutation_name');
			$this->db->from('acct_mutation');
			$this->db->where('acct_mutation.data_state', 0);
			$this->db->where('mutation_module', 'WJB');
			return $this->db->get()->row_array();
		}

		public function getMemberName($member_id){
			$this->db->select('member_name');
			$this->db->from('core_member');
			$this->db->where('member_id', $member_id);
			$result = $this->db->get()->row_array();
			return $result['member_name'];
		}

		public function getMemberAddress($member_id){
			$this->db->select('member_address');
			$this->db->from('core_member');
			$this->db->where('member_id', $member_id);
			$result = $this->db->get()->row_array();
			return $result['member_address'];
		}

		public function getCoreMemberName($member_id){
			$this->db->select('member_name');
			$this->db->from('core_member');
			$this->db->where('member_id', $member_id);
			$result = $this->db->get()->row_array();
			return $result['member_name'];
		}

		public function getUsername($user_id){
			$this->db->select('username');
			$this->db->from('system_user');
			$this->db->where('user_id', $user_id);
			$result = $this->db->get()->row_array();
			return $result['username'];
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

		public function getBranchCity($branch_id){
			$this->db->select('branch_city');
			$this->db->from('core_branch');
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['branch_city'];
		}

		public function getSavingsAccountNo($savings_account_id){
			$this->db->select('savings_account_no');
			$this->db->from('acct_savings_account');
			$this->db->where('savings_account_id', $savings_account_id);
			$result = $this->db->get()->row_array();
			return $result['savings_account_no'];
		}

		public function getAcctSavingsAccount_Detail($savings_account_id){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.branch_id, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings_account.savings_account_last_balance, acct_savings_account.member_id, core_member.member_name, core_member.member_address, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.identity_id, core_member.member_identity_no');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings_account.savings_account_id', $savings_account_id);
			return $this->db->get()->row_array();
		}
		
		public function insertCoreMemberTransferMutation($data){
			return $query = $this->db->insert('core_member_transfer_mutation',$data);
		}

		public function getMemberTransferMutationToken($member_transfer_mutation_token){
			$this->db->select('member_transfer_mutation_token');
			$this->db->from('core_member_transfer_mutation');
			$this->db->where('member_transfer_mutation_token', $member_transfer_mutation_token);
			return $this->db->get();
		}

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
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

		public function getCoreMemberTransferMutation_Last($created_id){
			$this->db->select('core_member_transfer_mutation.member_transfer_mutation_id, core_member_transfer_mutation.member_id, core_member.member_name');
			$this->db->from('core_member_transfer_mutation');
			$this->db->join('core_member','core_member_transfer_mutation.member_id = core_member.member_id');
			$this->db->where('core_member_transfer_mutation.created_id', $created_id);
			$this->db->order_by('core_member_transfer_mutation.member_transfer_mutation_id','DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getMutationJournalDesc($mutation_id){
			$this->db->select('mutation_journal_desc');
			$this->db->from('acct_mutation');
			$this->db->where('mutation_id', $mutation_id);
			$result = $this->db->get()->row_array();
			return $result['mutation_journal_desc'];
		}

		public function insertAcctJournalVoucher($data){
			if ($this->db->insert('acct_journal_voucher', $data)){
				return true;
			}else{
				return false;
			}
		}

		public function getJournalVoucherToken($journal_voucher_token){
			$this->db->select('journal_voucher_token');
			$this->db->from('acct_journal_voucher');
			$this->db->where('journal_voucher_token', $journal_voucher_token);
			return $this->db->get();
		}
		
		public function getJournalVoucherItemToken($journal_voucher_item_token){
			$this->db->select('journal_voucher_item_token');
			$this->db->from('acct_journal_voucher_item');
			$this->db->where('journal_voucher_item_token', $journal_voucher_item_token);
			return $this->db->get();
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

		public function getSavingsTransferMutationID($created_on){
			$this->db->select('savings_transfer_mutation_id');
			$this->db->from('acct_savings_transfer_mutation');
			$this->db->where('created_on', $created_on);
			$this->db->limit(1);
			$this->db->order_by('savings_transfer_mutation_id','DESC');
			$result = $this->db->get()->row_array();
			return $result['savings_transfer_mutation_id'];
		}
		
		public function getCoreMemberTransferMutation_Detail($member_transfer_mutation_id){
			$this->db->select('core_member_transfer_mutation.member_transfer_mutation_id, core_member_transfer_mutation.member_transfer_mutation_date, core_member_transfer_mutation.member_mandatory_savings, core_member_transfer_mutation.validation, core_member_transfer_mutation.validation_id, core_member_transfer_mutation.validation_on, core_member_transfer_mutation.member_id, core_member.member_name, core_member.member_no, core_member_transfer_mutation.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.member_id as member_id_savings');
			$this->db->from('core_member_transfer_mutation');

			$this->db->join('acct_savings_account', 'core_member_transfer_mutation.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_member', 'core_member_transfer_mutation.member_id = core_member.member_id');
			$this->db->where('core_member_transfer_mutation.member_transfer_mutation_id', $member_transfer_mutation_id);
			return $this->db->get()->row_array();
		}

		public function validationCoreMemberTransferMutation($data){
			$this->db->where("member_transfer_mutation_id",$data['member_transfer_mutation_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function getCoreMemberTransferMutationFrom_Member($member_id, $savings_transfer_mutation_date, $data_mutation){
			$this->db->select('acct_savings_transfer_mutation.savings_transfer_mutation_date, acct_savings_transfer_mutation_from.savings_transfer_mutation_id, acct_savings_transfer_mutation_from.savings_account_id, acct_savings_account.savings_account_no, acct_savings_transfer_mutation_from.savings_id, acct_savings.savings_code, acct_savings.savings_name, acct_savings_transfer_mutation_from.mutation_id, acct_mutation.mutation_name, acct_savings_transfer_mutation_from.member_id, core_member.member_name, core_member.member_address, acct_savings_transfer_mutation_from.savings_transfer_mutation_from_amount');
			$this->db->from('acct_savings_transfer_mutation');
			$this->db->join('acct_savings_transfer_mutation_from', 'acct_savings_transfer_mutation.savings_transfer_mutation_id = acct_savings_transfer_mutation_from.savings_transfer_mutation_id');
			$this->db->join('acct_mutation', 'acct_savings_transfer_mutation_from.mutation_id = acct_mutation.mutation_id');
			$this->db->join('acct_savings_account', 'acct_savings_transfer_mutation_from.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('acct_savings', 'acct_savings_transfer_mutation_from.savings_id = acct_savings.savings_id');
			$this->db->join('core_member', 'acct_savings_transfer_mutation_from.member_id = core_member.member_id');
			$this->db->where('acct_savings_transfer_mutation.data_state', 0);
			$this->db->where('acct_savings_transfer_mutation_from.member_id', $member_id);
			$this->db->where('acct_savings_transfer_mutation.savings_transfer_mutation_date', $savings_transfer_mutation_date);
			$this->db->where_in('acct_savings_transfer_mutation_from.mutation_id', $data_mutation);
			$this->db->where('acct_savings_transfer_mutation.savings_transfer_mutation_status', 1);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreMemberTransferMutationTo_Member($member_id, $savings_transfer_mutation_date, $data_mutation){
			$this->db->select('acct_savings_transfer_mutation.savings_transfer_mutation_date, acct_savings_transfer_mutation_to.savings_transfer_mutation_id, acct_savings_transfer_mutation_to.savings_account_id, acct_savings_account.savings_account_no, acct_savings_transfer_mutation_to.savings_id, acct_savings.savings_code, acct_savings.savings_name, acct_savings_transfer_mutation_to.mutation_id, acct_mutation.mutation_name, acct_savings_transfer_mutation_to.member_id, core_member.member_name, core_member.member_address, acct_savings_transfer_mutation_to.savings_transfer_mutation_to_amount');
			$this->db->from('acct_savings_transfer_mutation');
			$this->db->join('acct_savings_transfer_mutation_to', 'acct_savings_transfer_mutation.savings_transfer_mutation_id = acct_savings_transfer_mutation_to.savings_transfer_mutation_id');
			$this->db->join('acct_mutation', 'acct_savings_transfer_mutation_to.mutation_id = acct_mutation.mutation_id');
			$this->db->join('acct_savings_account', 'acct_savings_transfer_mutation_to.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('acct_savings', 'acct_savings_transfer_mutation_to.savings_id = acct_savings.savings_id');
			$this->db->join('core_member', 'acct_savings_transfer_mutation_to.member_id = core_member.member_id');
			$this->db->where('acct_savings_transfer_mutation.data_state', 0);
			$this->db->where('acct_savings_transfer_mutation_to.member_id', $member_id);
			$this->db->where('acct_savings_transfer_mutation.savings_transfer_mutation_date', $savings_transfer_mutation_date);
			$this->db->where_in('acct_savings_transfer_mutation_to.mutation_id', $data_mutation);
			$this->db->where('acct_savings_transfer_mutation.savings_transfer_mutation_status', 1);
			$result = $this->db->get()->result_array();
			return $result;
		}
	}
?>