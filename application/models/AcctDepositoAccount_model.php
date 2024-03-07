<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctDepositoAccount_model extends CI_Model {
		var $table = "acct_deposito_account";
		var $column_order = array(null, 'acct_deposito_account.deposito_account_no','core_member.member_name','core_member.member_address',); //field yang ada di table user
		var $column_search = array('acct_deposito_account.deposito_account_no','core_member.member_name','core_member.member_address'); //field yang diizin untuk pencarian 
		var $order = array('acct_deposito_account.deposito_account_id' => 'asc');
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getDataAcctDepositoAccount($deposito_id, $branch_id){
			$this->db->select('acct_deposito_account.deposito_account_id, acct_deposito_account.member_id, core_member.member_name, acct_deposito_account.deposito_id, acct_deposito.deposito_code, acct_deposito.deposito_name, acct_deposito_account.deposito_account_no, acct_deposito_account.deposito_account_date, acct_deposito_account.deposito_account_amount, acct_deposito_account.deposito_account_due_date, acct_deposito_account.deposito_account_serial_no, acct_deposito_account.deposito_account_nisbah, acct_deposito_account.validation, acct_deposito_account.validation_id, acct_deposito_account.validation_on, acct_deposito_account.deposito_account_blockir_type, acct_deposito_account.deposito_account_blockir_status');
			$this->db->from('acct_deposito_account');
			$this->db->join('core_member', 'acct_deposito_account.member_id = core_member.member_id');
			$this->db->join('acct_deposito', 'acct_deposito_account.deposito_id = acct_deposito.deposito_id');
			// $this->db->where('acct_deposito_account.deposito_account_date >=', $start_date);
			// $this->db->where('acct_deposito_account.deposito_account_date <=', $end_date);
			$this->db->where('acct_deposito_account.branch_id', $branch_id);
			if(!empty($deposito_id)){
				$this->db->where('acct_deposito_account.deposito_id', $deposito_id);
			}
			$this->db->where('acct_deposito_account.deposito_account_status', 0);
			$this->db->where('acct_deposito_account.data_state', 0);
			$this->db->order_by('acct_deposito_account.deposito_account_no', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreMember($city_id, $kecamatan_id){
			$this->db->select('core_member.member_id, core_member.member_name, core_member.member_address, core_member.member_no, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.member_mother');
			$this->db->from('core_member');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			if(!empty($city_id)){
				$this->db->where('core_member.city_id', $city_id);
			}

			if(!empty($kecamatan_id)){
				$this->db->where('core_member.kecamatan_id', $kecamatan_id);
			}
			$this->db->where('core_member.data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getCoreBranch(){
			$this->db->select('core_branch.branch_id, core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreCity(){
			$this->db->select('city_id, city_name');
			$this->db->from('core_city');
			$this->db->where('data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getCoreKecamatan($city_id){
			$this->db->select('core_kecamatan.kecamatan_id, core_kecamatan.kecamatan_name');
			$this->db->from('core_kecamatan');
			$this->db->where('core_kecamatan.city_id', $city_id);
			$this->db->where('core_kecamatan.data_state', '0');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctDeposito(){
			$this->db->select('acct_deposito.deposito_id, acct_deposito.deposito_name');
			$this->db->from('acct_deposito');
			$this->db->join('acct_account', 'acct_deposito.account_id = acct_account.account_id');
			$this->db->where('acct_deposito.data_state', 0);
			$this->db->order_by('acct_deposito.deposito_number','ASC');
			return $this->db->get()->result_array();
		}

		public function getAcctSavings(){
			$this->db->select('savings_id, savings_name');
			$this->db->from('acct_savings');
			$this->db->where('data_state', 0);
			$this->db->where('savings_status', 0);
			return $this->db->get()->result_array();
		}

		public function getCoreOffice(){
			$this->db->select('office_id, office_name');
			$this->db->from('core_office');
			$this->db->where('data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctDepositoAccountDetail($deposito_account_id){
			$this->db->select('*');
			$this->db->from('acct_deposito_account');
			$this->db->where('data_state', 0);
			$this->db->where('deposito_account_id', $deposito_account_id);
			return $this->db->get()->row_array();
		}

		public function getAcctDepositoAccrualLastBalance($deposito_account_id){
			$this->db->select('deposito_accrual_last_balance');
			$this->db->from('acct_deposito_accrual');
			$this->db->where('data_state', 0);
			$this->db->where('deposito_account_id', $deposito_account_id);
			$this->db->order_by('created_on', 'DESC');
			$result = $this->db->get()->row_array();
			return $result['deposito_accrual_last_balance'];
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

		public function getAcctSavingsAccount_Detail($savings_account_id){
			$this->db->select('acct_savings_account.savings_account_id, CONCAT(acct_savings_account.savings_account_no," - ",core_member.member_name) AS savings_account_no, acct_savings_account.savings_id, acct_savings_account.member_id, acct_savings_account.savings_account_last_balance');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings.savings_status', 0);
			$this->db->where('acct_savings_account.savings_account_id', $savings_account_id);
			return $this->db->get()->row_array();
		}

		public function getAcctSavingsAccount($branch_id){
			$this->db->select('acct_savings_account.savings_account_id, CONCAT(acct_savings_account.savings_account_no," - ",core_member.member_name) AS savings_account_no');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			// $this->db->where('acct_savings_account.branch_id', $branch_id);
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings.savings_status', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctDepositoAccount(){
			$this->db->select('acct_deposito_account.deposito_account_id, CONCAT(acct_deposito_account.deposito_account_no," - ",core_member.member_name) AS deposito_account_no');
			$this->db->from('acct_deposito_account');
			$this->db->join('core_member', 'acct_deposito_account.member_id = core_member.member_id');
			$this->db->where('acct_deposito_account.deposito_account_status', 0);
			$this->db->where('acct_deposito_account.data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getCoreMember_Detail($member_id){
			$this->db->select('core_member.member_id, core_member.branch_id, core_branch.branch_name, core_member.member_no, core_member.member_name, core_member.member_gender, core_member.member_place_of_birth, core_member.member_date_of_birth, core_member.member_address, core_member.province_id, core_province.province_name, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.member_phone, core_member.member_job, core_member.member_identity, core_member.member_identity_no, core_member.member_postal_code, core_member.member_mother, core_member.member_heir, core_member.member_family_relationship, core_member.member_status, core_member.member_register_date, core_member.member_marital_status');
			$this->db->from('core_member');
			$this->db->join('core_province', 'core_member.province_id = core_province.province_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->join('core_branch', 'core_member.branch_id = core_branch.branch_id');
			$this->db->where('core_member.data_state', 0);
			$this->db->where('core_member.member_id', $member_id);
			return $this->db->get()->row_array();
		}

		public function getBranchCode($branch_id){
			$this->db->select('RIGHT(core_branch.branch_code,2) as branch_code');
			//$this->db->select('branch_code');
			$this->db->from('core_branch');
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['branch_code'];
		}

		public function getDepositoCode($deposito_id){
			$this->db->select('deposito_code');
			$this->db->from('acct_deposito');
			$this->db->where('deposito_id', $deposito_id);
			$result = $this->db->get()->row_array();
			return $result['deposito_code'];
		}

		public function getDepositoPeriod($deposito_id){
			$this->db->select('deposito_period');
			$this->db->from('acct_deposito');
			$this->db->where('deposito_id', $deposito_id);
			$result = $this->db->get()->row_array();
			return $result['deposito_period'];
		}

		public function getDepositoNisbah($deposito_id){
			$this->db->select('deposito_interest_rate');
			$this->db->from('acct_deposito');
			$this->db->where('deposito_id', $deposito_id);
			$result = $this->db->get()->row_array();
			return $result['deposito_interest_rate'];
		}

		public function getOfficeName($office_id){
			$this->db->select('office_name');
			$this->db->from('core_office');
			$this->db->where('office_id', $office_id);
			$result = $this->db->get()->row_array();
			return $result['office_name'];
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

		public function getLastAccountDepositoNo($branch_id, $deposito_id){
			$this->db->select('RIGHT(acct_deposito_account.deposito_account_no,5) as last_deposito_account_no');
			$this->db->from('acct_deposito_account');
			$this->db->where('acct_deposito_account.branch_id', $branch_id);
			$this->db->where('acct_deposito_account.deposito_id', $deposito_id);
			$this->db->limit(1);
			$this->db->order_by('acct_deposito_account.deposito_account_id', 'DESC');
			$result = $this->db->get();
			return $result;
		}

		public function getDepositoAccountToken($deposito_account_token){
			$this->db->select('deposito_account_token');
			$this->db->from('acct_deposito_account');
			$this->db->where('deposito_account_token', $deposito_account_token);
			return $this->db->get();
		}
		public function getDepositoAccountExtraToken($deposito_account_extra_token){
			$this->db->select('deposito_account_extra_token');
			$this->db->from('acct_deposito_account');
			$this->db->where('deposito_account_extra_token', $deposito_account_extra_token);
			return $this->db->get();
		}
		public function getAcctDepositoProfitSharingToken($deposito_profit_sharing_token){
			$this->db->select('deposito_profit_sharing_token');
			$this->db->from('acct_deposito_profit_sharing');
			$this->db->where('deposito_profit_sharing_token', $deposito_profit_sharing_token);
			return $this->db->get();
		}
		public function getDepositoAccountClosedToken($deposito_account_closed_token){
			$this->db->select('deposito_account_closed_token');
			$this->db->from('acct_deposito_account');
			$this->db->where('deposito_account_closed_token', $deposito_account_closed_token);
			return $this->db->get();
		}

		public function getDepositoProfitSharingToken($deposito_profit_sharing_token){
			$this->db->select('deposito_profit_sharing_token');
			$this->db->from('acct_deposito_profit_sharing');
			$this->db->where('deposito_profit_sharing_token', $deposito_profit_sharing_token);
			return $this->db->get();
		}

		public function getAcctSavingsTransferMutationToken($savings_transfer_mutation_token){
			$this->db->select('savings_transfer_mutation_token');
			$this->db->from('acct_savings_transfer_mutation');
			$this->db->where('savings_transfer_mutation_token', $savings_transfer_mutation_token);
			return $this->db->get();
		}

		public function getAcctSavingsTransferMutationToToken($savings_transfer_mutation_to_token){
			$this->db->select('savings_transfer_mutation_to_token');
			$this->db->from('acct_savings_transfer_mutation');
			$this->db->where('savings_transfer_mutation_to_token', $savings_transfer_mutation_to_token);
			return $this->db->get();
		}

		public function getAcctJournalVoucherToken($journal_voucher_token){
			$this->db->select('journal_voucher_token');
			$this->db->from('acct_journal_voucher');
			$this->db->where('journal_voucher_token', $journal_voucher_token);
			return $this->db->get();
		}

		public function getAcctJournalVoucherItemToken($journal_voucher_item_token){
			$this->db->select('journal_voucher_item_token');
			$this->db->from('acct_journal_voucher_item');
			$this->db->where('journal_voucher_item_token', $journal_voucher_item_token);
			return $this->db->get();
		}
		
		public function insertAcctDepositoAccount($data){
			return $query = $this->db->insert('acct_deposito_account',$data);
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

		public function getAcctDepositoAccount_Last($created_on){
			$this->db->select('acct_deposito_account.deposito_account_id, acct_deposito_account.deposito_account_no, acct_deposito_account.member_id, core_member.member_name');
			$this->db->from('acct_deposito_account');
			$this->db->join('core_member','acct_deposito_account.member_id = core_member.member_id');
			$this->db->where('acct_deposito_account.created_on', $created_on);
			$this->db->limit(1);
			$this->db->order_by('acct_deposito_account.created_on','DESC');
			$result = $this->db->get()->row_array();
			return $result;
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

		public function getAccountID($deposito_id){
			$this->db->select('acct_deposito.account_id');
			$this->db->from('acct_deposito');
			$this->db->where('acct_deposito.deposito_id', $deposito_id);
			$result = $this->db->get()->row_array();
			return $result['account_id'];
		}

		public function getAccountSavingsID($savings_id){
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
		
		public function insertAcctJournalVoucherItem($data){
			if($this->db->insert('acct_journal_voucher_item', $data)){
				return true;
			}else{
				return false;
			}
		}

		public function getDepositoAccountID($created_on){
			$this->db->select('deposito_account_id');
			$this->db->from('acct_deposito_account');
			$this->db->where('last_update', $created_on);
			$this->db->limit(1);
			$this->db->order_by('deposito_account_id','DESC');
			$result = $this->db->get()->row_array();
			return $result['deposito_account_id'];
		}

		public function insertAcctDepositoProfitSharing($data){
			return $query = $this->db->insert('acct_deposito_profit_sharing',$data);
		}
		
		public function getAcctDepositoAccount_Detail($deposito_account_id){
			$this->db->select('acct_deposito_account.deposito_account_id, acct_deposito_account.member_id, core_member.member_name, core_member.member_no, core_member.member_gender, core_member.member_address, core_member.member_phone, core_member.member_date_of_birth, core_member.member_identity_no, core_member.city_id, core_member.kecamatan_id, core_member.identity_id, core_member.member_job, acct_deposito_account.deposito_id, acct_deposito.deposito_code, acct_deposito.deposito_name, acct_deposito.deposito_interest_rate, acct_deposito_account.deposito_account_no, acct_deposito_account.deposito_account_serial_no, acct_deposito_account.deposito_account_date, acct_deposito_account.deposito_account_amount, acct_deposito_account.deposito_account_period, acct_deposito_account.deposito_account_due_date, acct_deposito_account.voided_remark, acct_deposito_account.savings_account_id, acct_deposito_account.office_id, acct_deposito_account.deposito_account_nisbah, acct_deposito_account.validation, acct_deposito_account.validation_id, acct_deposito_account.validation_on, acct_deposito_account.office_id, acct_deposito_account.deposito_account_blockir_type, acct_deposito_account.deposito_account_blockir_status');
			$this->db->from('acct_deposito_account');
			$this->db->join('core_member', 'acct_deposito_account.member_id = core_member.member_id');
			// $this->db->join('acct_savings_account', 'acct_deposito_account.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('acct_deposito', 'acct_deposito_account.deposito_id = acct_deposito.deposito_id');
			$this->db->where('acct_deposito_account.data_state', 0);
			$this->db->where('acct_deposito_account.deposito_account_id', $deposito_account_id);
			return $this->db->get()->row_array();
		}

		public function validationAcctDepositoAccount($data){
			$this->db->where("deposito_account_id",$data['deposito_account_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function voidAcctDepositoAccount($data){
			$this->db->where("deposito_account_id",$data['deposito_account_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function getAcctDepositoAccountDueDate($deposito_id, $branch_id){
			$this->db->select('acct_deposito_account.deposito_account_id, acct_deposito_account.member_id, core_member.member_name, core_member.member_gender, acct_deposito_account.deposito_id, acct_deposito.deposito_code, acct_deposito.deposito_name, acct_deposito_account.deposito_account_no, acct_deposito_account.deposito_account_date, acct_deposito_account.deposito_account_amount, acct_deposito_account.deposito_account_due_date, acct_deposito_account.deposito_account_serial_no,acct_deposito_account.
			deposito_account_extra_type, acct_deposito_account.deposito_account_nisbah');
			$this->db->from('acct_deposito_account');
			$this->db->join('core_member', 'acct_deposito_account.member_id = core_member.member_id');
			$this->db->join('acct_deposito', 'acct_deposito_account.deposito_id = acct_deposito.deposito_id');
			// $this->db->where('acct_deposito_account.deposito_account_date >=', $start_date);
			// $this->db->where('acct_deposito_account.deposito_account_date <=', $end_date);
			// $this->db->where('CURDATE() >= acct_deposito_account.deposito_account_due_date ');
			$this->db->where('acct_deposito_account.branch_id ', $branch_id);
			if(!empty($deposito_id)){
				$this->db->where('acct_deposito_account.deposito_id ', $deposito_id);
			}
			$this->db->where('acct_deposito_account.deposito_account_status', 0);
			$this->db->where('acct_deposito_account.data_state', 0);
			$this->db->order_by('acct_deposito_account.deposito_account_no', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctDepositoProfitSharing($data){
			$this->db->select('acct_deposito_profit_sharing.deposito_profit_sharing_id, acct_deposito_profit_sharing.deposito_account_id, acct_deposito_account.deposito_account_no, acct_deposito_account.member_id, core_member.member_name');
			$this->db->from('acct_deposito_profit_sharing');
			$this->db->join('core_member','acct_deposito_profit_sharing.member_id = core_member.member_id');
			$this->db->join('acct_deposito_account','acct_deposito_profit_sharing.deposito_account_id = acct_deposito_account.deposito_account_id');
			$this->db->where('acct_deposito_profit_sharing.deposito_account_id', $data['deposito_account_id']);
			$this->db->where('acct_deposito_profit_sharing.deposito_profit_sharing_due_date', $data['deposito_profit_sharing_due_date']);
			$result = $this->db->get();
			return $result;
		}


		public function insertAcctDepositoAccountExtra($data, $data_update){
			$query = $this->db->insert('acct_deposito_account_extra',$data);
			if($query){
				$this->db->where('deposito_account_id', $data['deposito_account_id']);
				$this->db->update('acct_deposito_account', $data_update);
				return true;
			} else {
				return false;
			}
		}

		public function getClosedAcctDepositoAccount($deposito_id, $branch_id){
			$this->db->select('acct_deposito_account.deposito_account_id, acct_deposito_account.member_id, core_member.member_name, acct_deposito_account.deposito_id, acct_deposito.deposito_code, acct_deposito.deposito_name, acct_deposito_account.deposito_account_no, acct_deposito_account.deposito_account_date, acct_deposito_account.deposito_account_amount, acct_deposito_account.deposito_account_due_date, acct_deposito_account.deposito_account_serial_no, acct_deposito_account.deposito_account_nisbah, acct_deposito_account.deposito_account_blockir_type, acct_deposito_account.deposito_account_blockir_status');
			$this->db->from('acct_deposito_account');
			$this->db->join('core_member', 'acct_deposito_account.member_id = core_member.member_id');
			$this->db->join('acct_deposito', 'acct_deposito_account.deposito_id = acct_deposito.deposito_id');
			// $this->db->where('acct_deposito_account.deposito_account_date >=', $start_date);
			// $this->db->where('acct_deposito_account.deposito_account_date <=', $end_date);
			if(!empty($branch_id)){
			$this->db->where('acct_deposito_account.branch_id ', $branch_id);
			}
			
			if(!empty($deposito_id)){
				$this->db->where('acct_deposito_account.deposito_id ', $deposito_id);
			}
			$this->db->where('acct_deposito_account.deposito_account_status', 0);
			$this->db->where('acct_deposito_account.data_state', 0);
			$this->db->order_by('acct_deposito_account.deposito_account_no', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function closedAcctDepositoAccountExtra($data){
			$this->db->where('deposito_account_id', $data['deposito_account_id']);
			if($this->db->update('acct_deposito_account', $data)){
				return true;
			} else {
				return false;
			}
		}

		public function getExport(){
			$this->db->select('acct_deposito_account.deposito_account_id, acct_deposito_account.member_id, core_member.member_name, acct_deposito_account.deposito_id, acct_deposito.deposito_code, acct_deposito.deposito_name, acct_deposito_account.deposito_account_extra_type, acct_deposito_account.deposito_account_no, acct_deposito_account.deposito_account_date, acct_deposito_account.deposito_account_amount, acct_deposito_account.deposito_account_due_date, acct_deposito_account.deposito_account_serial_no, acct_deposito_account.deposito_account_nisbah, acct_deposito_account.validation, acct_deposito_account.validation_id, acct_deposito_account.validation_on');
			$this->db->from('acct_deposito_account');
			$this->db->join('core_member', 'acct_deposito_account.member_id = core_member.member_id');
			$this->db->join('acct_deposito', 'acct_deposito_account.deposito_id = acct_deposito.deposito_id');
			$this->db->where('acct_deposito_account.deposito_account_status', 0);
			$this->db->where('acct_deposito_account.data_state', 0);
			$this->db->order_by('acct_deposito_account.deposito_account_no', 'ASC');
			$result = $this->db->get();
			return $result;
		}

			private function _get_datatables_query()
	    {
	    	// $this->db->select('acct_savings_account.savings_account_no','core_member.member_name','core_member.member_address');
	       	$this->db->from('acct_deposito_account');
			$this->db->join('core_member', 'acct_deposito_account.member_id = core_member.member_id');
			$this->db->join('acct_deposito', 'acct_deposito_account.deposito_id = acct_deposito.deposito_id');
			$this->db->where('acct_deposito_account.deposito_account_status', 0);
			$this->db->where('acct_deposito_account.data_state', 0);
			$this->db->order_by('acct_deposito_account.deposito_account_no', 'ASC');
	        $i = 0;
	     
	        foreach ($this->column_search as $item) // looping awal
	        {
	            if($_POST['search']['value']) // jika datatable mengirimkan pencarian dengan metode POST
	            {
	                 
	                if($i===0) // looping awal
	                {
	                    $this->db->group_start(); 
	                    $this->db->like($item, $_POST['search']['value']);
	                }
	                else
	                {
	                    $this->db->or_like($item, $_POST['search']['value']);
	                }
	 
	                if(count($this->column_search) - 1 == $i) 
	                    $this->db->group_end(); 
	            }
	            $i++;
	        }
	         
	        if(isset($_POST['order'])) 
	        {
	            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
	        } 
	        else if(isset($this->order))
	        {
	            $order = $this->order;
	            $this->db->order_by(key($order), $order[key($order)]);
	        }
	    }
	 
	    function get_datatables()
	    {
	        $this->_get_datatables_query();
	        if($_POST['length'] != -1)
	        $this->db->limit($_POST['length'], $_POST['start']);
	        $query = $this->db->get();
	        return $query->result();
	    }
	 
	    function count_filtered()
	    {
	        $this->_get_datatables_query();
	        $query = $this->db->get();
	        return $query->num_rows();
	    }
	 
	    public function count_all()
	    {
	        $this->db->from($this->table);
	        return $this->db->count_all_results();
	    }

	    private function _get_datatables_query_master($deposito_id, $branch_id)
	    {
	    	// $this->db->select('acct_savings_account.savings_account_no','core_member.member_name','core_member.member_address');
	       	$this->db->from('acct_deposito_account');
			$this->db->join('core_member', 'acct_deposito_account.member_id = core_member.member_id');
			$this->db->join('acct_deposito', 'acct_deposito_account.deposito_id = acct_deposito.deposito_id');
			// $this->db->where('acct_deposito_account.deposito_account_date >=', $start_date);
			// $this->db->where('acct_deposito_account.deposito_account_date <=', $end_date);
			if(!empty($branch_id)){
				$this->db->where('acct_deposito_account.branch_id', $branch_id);
			}
			
			if(!empty($deposito_id)){
				$this->db->where('acct_deposito_account.deposito_id', $deposito_id);
			}
			$this->db->where('acct_deposito_account.deposito_account_status', 0);
			$this->db->where('acct_deposito_account.data_state', 0);
			$this->db->order_by('acct_deposito_account.deposito_account_no', 'ASC');
	        $i = 0;
	     
	        foreach ($this->column_search as $item) // looping awal
	        {
	            if($_POST['search']['value']) // jika datatable mengirimkan pencarian dengan metode POST
	            {
	                 
	                if($i===0) // looping awal
	                {
	                    $this->db->group_start(); 
	                    $this->db->like($item, $_POST['search']['value']);
	                }
	                else
	                {
	                    $this->db->or_like($item, $_POST['search']['value']);
	                }
	 
	                if(count($this->column_search) - 1 == $i) 
	                    $this->db->group_end(); 
	            }
	            $i++;
	        }
	         
	        if(isset($_POST['order'])) 
	        {
	            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
	        } 
	        else if(isset($this->order))
	        {
	            $order = $this->order;
	            $this->db->order_by(key($order), $order[key($order)]);
	        }
	    }
	 
	    function get_datatables_master($deposito_id, $branch_id)
	    {
	        $this->_get_datatables_query_master($deposito_id, $branch_id);
	        if($_POST['length'] != -1)
	        $this->db->limit($_POST['length'], $_POST['start']);
	        $query = $this->db->get();
	        return $query->result();
	    }
	 
	    function count_filtered_master($deposito_id, $branch_id)
	    {
	        $this->_get_datatables_query_master($deposito_id, $branch_id);
	        $query = $this->db->get();
	        return $query->num_rows();
	    }
	 
	    public function count_all_master($deposito_id, $branch_id)
	    {
	        $this->db->from($this->table);
	        return $this->db->count_all_results();
	    }
	}
?>