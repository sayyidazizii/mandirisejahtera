<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class CoreMember_model extends CI_Model {
		var $table = "core_member";
		var $column_order = array(null, 'member_no','member_name','member_address','member_status','member_phone','member_principal_savings_last_balance','member_special_savings_last_balance','member_mandatory_savings_last_balance'); //field yang ada di table user
		var $column_search = array('member_id','member_name','member_no','member_address'); //field yang diizin untuk pencarian 
		var $order = array('member_id' => 'asc');
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getDataCoreMember($branch_id){
			$this->db->select('core_member.member_id, core_member.branch_id, core_branch.branch_name, core_member.member_no, core_member.member_name, core_member.member_gender, core_member.member_place_of_birth, core_member.member_date_of_birth, core_member.member_address, core_member.province_id, core_province.province_name, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.member_phone, core_member.member_job, core_member.member_identity, core_member.member_identity_no, core_member.member_postal_code, core_member.member_mother, core_member.member_heir, core_member.member_family_relationship, core_member.member_status, core_member.member_register_date, core_member.member_principal_savings, core_member.member_special_savings, core_member.member_mandatory_savings, core_member.member_character, core_member.member_token, core_member.member_principal_savings_last_balance, core_member.member_special_savings_last_balance, core_member.member_mandatory_savings_last_balance');
			$this->db->from('core_member');
			$this->db->join('core_province', 'core_member.province_id = core_province.province_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->join('core_branch', 'core_member.branch_id = core_branch.branch_id');
			// $this->db->where('core_member.branch_id', $branch_id);
			$this->db->where('core_member.data_state', 0);
			$this->db->order_by('core_member.member_no', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}
		public function getCoreMemberNameandPlaceOfBirth($name,$dateofbirth,$placeofbirth){
			$this->db->select('core_member.member_name, core_member.member_place_of_birth, core_member.member_date_of_birth');
			$this->db->from('core_member');
			$this->db->where('core_member.data_state', 0);
			$this->db->where("(member_name = '".$name."' OR member_place_of_birth = '".$placeofbirth."' AND member_date_of_birth = '".$dateofbirth."' )");
			$this->db->order_by('core_member.member_no', 'ASC');
			$result = $this->db->get()->row_array();
			return $result;
		}
		public function getCoreBranch(){
			$this->db->select('core_branch.branch_id, core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctMutation(){
			$this->db->select('mutation_id, mutation_name');
			$this->db->from('acct_mutation');
			$this->db->where('data_state', 0);
			$this->db->where('mutation_module', 'TAB');
			return $this->db->get()->result_array();
		}

		public function getMutationFunction($mutation_id){
			$this->db->select('mutation_function');
			$this->db->from('acct_mutation');
			$this->db->where('mutation_id', $mutation_id);
			$result = $this->db->get()->row_array();
			return $result['mutation_function'];
		}

		public function getCoreProvince(){
			$this->db->select('core_province.province_id, core_province.province_name');
			$this->db->from('core_province');
			$this->db->where('core_province.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreCity($province_id){
			$this->db->select('core_city.city_id, core_city.city_name');
			$this->db->from('core_city');
			$this->db->where('core_city.province_id', $province_id);
			$this->db->where('core_city.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreKecamatan($city_id){
			$this->db->select('core_kecamatan.kecamatan_id, core_kecamatan.kecamatan_name');
			$this->db->from('core_kecamatan');
			$this->db->where('core_kecamatan.city_id', $city_id);
			$this->db->where('core_kecamatan.data_state', '0');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreKelurahan($kecamatan_id){
			$this->db->select('core_kelurahan.kelurahan_id, core_kelurahan.kelurahan_name');
			$this->db->from('core_kelurahan');
			$this->db->where('core_kelurahan.kecamatan_id', $kecamatan_id);
			$this->db->where('core_kelurahan.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreDusun($kelurahan_id){
			$this->db->select('core_dusun.dusun_id, core_dusun.dusun_name');
			$this->db->from('core_dusun');
			$this->db->where('core_dusun.kelurahan_id', $kelurahan_id);
			$this->db->where('core_dusun.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreJob(){
			$this->db->select('job_id, job_name');
			$this->db->from('core_job');
			$this->db->where('data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getCoreIdentity(){
			$this->db->select('identity_id, identity_name');
			$this->db->from('core_identity');
			$this->db->where('data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getCoreMemberClass(){
			$this->db->select('member_class_id, member_class_name');
			$this->db->from('core_member_class');
			$this->db->where('data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getCoreCompany(){
			$this->db->select('company_id, company_name');
			$this->db->from('core_company');
			$this->db->where('data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getCoreCompanyNoDateState(){
			$this->db->select('company_id, company_name');
			$this->db->from('core_company');
			return $this->db->get()->result_array();
		}

		public function getCoreMemberClassMandatorySavings($member_class_id){
			$this->db->select('member_class_mandatory_savings');
			$this->db->from('core_member_class');
			$this->db->where('data_state', 0);
			$this->db->where('member_class_id', $member_class_id);
			$result = $this->db->get()->row_array();
			return $result['member_class_mandatory_savings'];
		}

		public function getCoreCompanyMandatorySavings($company_id){
			$this->db->select('company_mandatory_savings');
			$this->db->from('core_company');
			$this->db->where('data_state', 0);
			$this->db->where('company_id', $company_id);
			$result = $this->db->get()->row_array();
			return $result['company_mandatory_savings'];
		}

		public function getUsername($user_id){
			$this->db->select('username');
			$this->db->from('system_user');
			$this->db->where('user_id', $user_id);
			$result = $this->db->get()->row_array();
			return $result['username'];
		}

		public function getBranchCode($branch_id){
			$this->db->select('RIGHT(core_branch.branch_code,2) as branch_code');			
			// $this->db->select('branch_code');
			$this->db->from('core_branch');
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['branch_code'];
		}

		public function getBranchCity($branch_id){
			$this->db->select('branch_city');
			$this->db->from('core_branch');
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['branch_city'];
		}

		public function getLastMemberNo($branch_id){
			$this->db->select('RIGHT(core_member.member_no,6) as last_member_no');
			$this->db->from('core_member');
			// $this->db->where('core_member.branch_id', $branch_id);
			$this->db->limit(1);
			$this->db->order_by('core_member.member_id', 'DESC');
			$result = $this->db->get();
			// print_r($result);exit;
			return $result;
		}

		public function getMemberToken($member_token){
			$this->db->select('member_token');
			$this->db->from('core_member');
			$this->db->where('member_token', $member_token);
			return $this->db->get();
		}
		
		public function insertCoreMember($data){
			$query = $this->db->insert('core_member',$data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function getMemberID($created_id){
			$this->db->select('member_id');
			$this->db->from('core_member');
			$this->db->where('created_id', $created_id);
			$this->db->order_by('created_on', 'DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['member_id'];
		}

		public function insertCoreMemberWorking($data){
			$query = $this->db->insert('core_member_working',$data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function getCoreMember_Detail($member_id){
			$this->db->select('core_member.*, core_member.branch_id, core_branch.branch_name, core_province.province_name, core_city.city_name, core_kecamatan.kecamatan_name, core_member_working.member_company_job_title, core_member_working.member_company_name');
			$this->db->from('core_member');
			$this->db->join('core_member_working','core_member.member_id = core_member_working.member_id');
			$this->db->join('core_province', 'core_member.province_id = core_province.province_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->join('core_branch', 'core_member.branch_id = core_branch.branch_id');
			$this->db->where('core_member.data_state', 0);
			$this->db->where('core_member.member_id', $member_id);
			return $this->db->get()->row_array();
		}

		public function getCoreMemberWorking_Detail($member_id){
			$this->db->select('core_member_working.*');
			$this->db->from('core_member_working');
			$this->db->where('core_member_working.member_id', $member_id);
			return $this->db->get()->row_array();
		}

		public function getMemberTokenEdit($member_token_edit){
			$this->db->select('member_token_edit');
			$this->db->from('core_member');
			$this->db->where('member_token_edit', $member_token_edit);
			return $this->db->get();
		}
		
		public function updateCoreMember($data){
			$this->db->where("member_id",$data['member_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function updateCoreMemberWorking($data){
			$corememberworking = $this->getCoreMemberWorking_Detail($data['member_id']);

			if(empty($corememberworking)){
				return $this->db->insert('core_member_working', $data);
			} else {
				$this->db->where("member_id",$data['member_id']);
				$query = $this->db->update('core_member_working', $data);
				if($query){
					return true;
				}else{
					return false;
				}
			}
			
		}

		public function getSavingsMemberDetailToken($savings_member_detail_token){
			$this->db->select('savings_member_detail_token');
			$this->db->from('acct_savings_member_detail');
			$this->db->where('savings_member_detail_token', $savings_member_detail_token);
			return $this->db->get();
		}

		public function insertAcctSavingsMemberDetail($data){
			$query = $this->db->insert('acct_savings_member_detail',$data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function getLastAcctSavingsMemberDetail($member_id){
			$this->db->select('acct_savings_member_detail.savings_member_detail_id, acct_savings_member_detail.member_id, core_member.member_no, core_member.member_name, core_member.member_address, acct_savings_member_detail.branch_id, acct_savings_member_detail.mutation_id, acct_mutation.mutation_code, acct_savings_member_detail.transaction_date, acct_savings_member_detail.principal_savings_amount, acct_savings_member_detail.special_savings_amount, acct_savings_member_detail.mandatory_savings_amount, acct_savings_member_detail.last_balance, acct_savings_member_detail.operated_name');
			$this->db->from('acct_savings_member_detail');
			$this->db->join('core_member', 'acct_savings_member_detail.member_id = core_member.member_id');
			$this->db->join('acct_mutation', 'acct_savings_member_detail.mutation_id = acct_mutation.mutation_id');
			$this->db->where('acct_savings_member_detail.member_id', $member_id);

			$this->db->order_by('acct_savings_member_detail.savings_member_detail_id', 'DESC');
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

		public function getAccoutCapitalID($savings_id){
			$this->db->select('core_branch.account_capital_id');
			$this->db->from('core_branch');
			$this->db->where('core_branch.branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['account_capital_id'];
		}

		public function getAccountRAKID($branch_id){
			$this->db->select('core_branch.account_rak_id');
			$this->db->from('core_branch');
			$this->db->where('core_branch.branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['account_rak_id'];
		}

		public function getAcctAccountSetting($account_setting_code){
			$this->db->select('acct_account_setting.account_id, acct_account_setting.account_setting_status, acct_account_setting.account_setting_name, acct_account_setting.section_id');
			$this->db->from('acct_account_setting');
			$this->db->where('acct_account_setting.account_setting_code', $account_setting_code);
			$this->db->where('acct_account_setting.data_state', 0);
			$result = $this->db->get()->result_array();
			
			return $result;
		}

		public function getAccountIDDefaultStatus($account_id){
			$this->db->select('acct_account.account_default_status');
			$this->db->from('acct_account');
			$this->db->where('acct_account.account_id', $account_id);
			$this->db->where('acct_account.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['account_default_status'];
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

		public function getAcctSavingsMemberDetail($member_id, $start_date, $end_date){
			$this->db->select('acct_savings_member_detail.savings_member_detail_id, acct_savings_member_detail.member_id, core_member.member_no, acct_savings_member_detail.branch_id, acct_savings_member_detail.mutation_id, acct_mutation.mutation_code, acct_savings_member_detail.transaction_date, acct_savings_member_detail.principal_savings_amount, acct_savings_member_detail.special_savings_amount, acct_savings_member_detail.mandatory_savings_amount, acct_savings_member_detail.last_balance, acct_savings_member_detail.operated_name, core_member.member_identity_no');
			$this->db->from('acct_savings_member_detail');
			$this->db->join('core_member', 'acct_savings_member_detail.member_id = core_member.member_id');
			$this->db->join('acct_mutation', 'acct_savings_member_detail.mutation_id = acct_mutation.mutation_id');
			$this->db->where('acct_savings_member_detail.transaction_date >=', $start_date);
			$this->db->where('acct_savings_member_detail.transaction_date <=', $end_date);
			$this->db->where('acct_savings_member_detail.member_id', $member_id);
			$this->db->where('acct_savings_member_detail.savings_print_status', 0);
			return $this->db->get()->result_array();
		}

		public function getMemberLastNumber($member_id){
			$this->db->select('member_last_number');
			$this->db->from('core_member');
			$this->db->where('member_id', $member_id);
			$result = $this->db->get()->row_array();
			return $result['member_last_number'];
		}

		public function updatePrintMutationStatus($data){
			$this->db->set('acct_savings_member_detail.savings_print_status', $data['savings_print_status']);
			$this->db->where('acct_savings_member_detail.savings_member_detail_id', $data['savings_member_detail_id']);
			if($this->db->update('acct_savings_member_detail')){
				$this->db->set('core_member.member_last_number', $data['member_last_number']);
				$this->db->where('core_member.member_id', $data['member_id']);
				if($this->db->update('core_member')){
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		public function getSavingsAccountToken($savings_account_token){
			$this->db->select('savings_account_token');
			$this->db->from('acct_savings_account');
			$this->db->where('savings_account_token', $savings_account_token);
			return $this->db->get();
		}


		public function insertAcctSavingsAccount($data){
			return $this->db->insert('acct_savings_account', $data);
		}

		public function getAcctSavingsAccount_Member($member_id){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.savings_account_no, acct_savings.savings_name');
			$this->db->from('acct_savings_account');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_account.member_id', $member_id);
			$this->db->where('acct_savings.savings_status', 0);
			$this->db->where('acct_savings_account.data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctCreditsAccount_Member($member_id){
			$this->db->select('acct_credits_account.credits_account_id, acct_credits_account.credits_account_serial, acct_credits.credits_name');
			$this->db->from('acct_credits_account');
			$this->db->join('acct_credits', 'acct_credits_account.credits_id = acct_credits.credits_id');
			$this->db->where('acct_credits_account.member_id', $member_id);
			//$this->db->where('acct_credits_account.credits_account_last_balance_principal <> 0');
			$this->db->where('acct_credits_account.data_state', 0);
			return $this->db->get()->result_array();
		}
		
		public function deleteCoreMember($member_id){
			$this->db->where("member_id",$member_id);
			$query = $this->db->update($this->table, array('data_state'=>1));
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function activateCoreMember($member_id){
			$this->db->where("member_id",$member_id);
			$query = $this->db->update($this->table, array('member_active_status'=>0));
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function nonActivateCoreMember($member_id){
			$this->db->where("member_id",$member_id);
			$query = $this->db->update($this->table, array('member_active_status'=>1, 'company_id'=>0));
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function getCoreMemberStatus(){
			$this->db->select('core_member.member_id, core_member.branch_id, core_branch.branch_name, core_member.member_no, core_member.member_name, core_member.member_gender, core_member.member_place_of_birth, core_member.member_date_of_birth, core_member.member_address, core_member.province_id, core_province.province_name, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.member_phone, core_member.member_job, core_member.member_identity, core_member.member_identity_no, core_member.member_postal_code, core_member.member_mother, core_member.member_heir, core_member.member_family_relationship, core_member.member_status, core_member.member_register_date, core_member.member_principal_savings, core_member.member_special_savings, core_member.member_mandatory_savings, core_member.member_character, core_member.member_principal_savings_last_balance, core_member.member_special_savings_last_balance, core_member.member_mandatory_savings_last_balance');
			$this->db->from('core_member');
			$this->db->join('core_province', 'core_member.province_id = core_province.province_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->join('core_branch', 'core_member.branch_id = core_branch.branch_id');
			$this->db->where('core_member.data_state', 0);
			$this->db->where('core_member.member_status', 0);
			$this->db->order_by('core_member.member_no', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function getCompanyName($company_id){
			$this->db->select('company_name');
			$this->db->from('core_company');
			$this->db->where('company_id', $company_id);
			$result = $this->db->get()->row_array();
			return $result['company_name'];
		}

		public function getAcctSavingsAccount($principal_savings_id, $member_id){
			$this->db->select('*');
			$this->db->from('acct_savings_member_detail');
			// $this->db->where('savings_id', $principal_savings_id);
			$this->db->where('member_id', $member_id);
			// $this->db->where('data_state', 0);
			return $this->db->get();
		}

		public function getAcctSavingsAccount2($member_id){
			$this->db->select('*');
			$this->db->from('acct_savings_account');
			$this->db->where('member_id', $member_id);
			$this->db->where('data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctDepositoAccount($member_id){
			$this->db->select('*');
			$this->db->from('acct_deposito_account');
			$this->db->where('member_id', $member_id);
			$this->db->where('data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctCreditsAccount($member_id){
			$this->db->select('*');
			$this->db->from('acct_credits_account');
			$this->db->where('member_id', $member_id);
			$this->db->where('data_state', 0);
			return $this->db->get()->result_array();
		}

		public function updateCoreMemberStatus($member_id){
			$this->db->where("member_id",$member_id);
			$query = $this->db->update($this->table, array('member_status'=>1));
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function getExport($branch_id){
			$this->db->select('core_member.member_id, core_member.branch_id, core_branch.branch_name, core_member.member_no, core_member.member_name, core_member.member_active_status, core_member.member_gender, core_member.member_place_of_birth, core_member.member_date_of_birth, core_member.member_address, core_member.province_id, core_province.province_name, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.member_phone, core_member.member_job, core_member.member_identity, core_member.member_identity_no, core_member.member_postal_code, core_member.member_mother, core_member.member_heir, core_member.member_family_relationship, core_member.member_status, core_member.member_register_date, core_member.member_principal_savings, core_member.member_special_savings, core_member.member_mandatory_savings, core_member.member_character, core_member.member_token, core_member.member_principal_savings_last_balance, core_member.member_special_savings_last_balance, core_member.member_mandatory_savings_last_balance, core_member.company_id, core_member_working.partner_working_type, core_member_working.member_company_name');
			$this->db->from('core_member');
			$this->db->join('core_member_working', 'core_member.member_id = core_member_working.member_id');
			// $this->db->join('core_company', 'core_member.company_id = core_company.company_id');
			$this->db->join('core_province', 'core_member.province_id = core_province.province_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->join('core_branch', 'core_member.branch_id = core_branch.branch_id');
			$this->db->where('core_member.data_state', 0);
			$this->db->where('core_member.branch_id', $branch_id);
			$this->db->order_by('core_member.member_no', 'ASC');
			$result = $this->db->get();
			return $result;
		}

		private function _get_datatables_query($branch_id)
    	{
		$column_order = array(null, 'member_no','member_name','member_address','member_status','member_phone','member_principal_savings_last_balance', 'member_special_savings_last_balance' ,'member_mandatory_savings_last_balance');
        $this->db->from($this->table);
 		$this->db->where('data_state', 0);
 		// if(!empty($company_id)){
 		// 	$this->db->where('company_id', $company_id);
 		// }
 		
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
            $this->db->order_by($column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
		$this->db->order_by('member_no', 'ASC');
		
		
    }
 
    function get_datatables($company_id)
    {
        $this->_get_datatables_query($company_id);
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
	
	private function _get_datatables_status_query($branch_id)
    {
         
        $this->db->from($this->table);
 		$this->db->where('data_state', 0);
//  		$this->db->where('member_status', 1);
 		// if(!empty($branch_id)){
 		// 	$this->db->where('branch_id', $branch_id);
 		// }
 		
 		$this->db->order_by('member_no', 'ASC');
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
	function get_datatables_status($branch_id)
    {
        $this->_get_datatables_status_query($branch_id);
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
	

	function count_filtered_status($branch_id)
    {
        $this->_get_datatables_status_query($branch_id);
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all_status($branch_id)
    {
        $this->db->from($this->table);
   //      if(!empty($branch_id)){
 		// 	$this->db->where('branch_id', $branch_id);
 		// }
        return $this->db->count_all_results();
    }


	
    function count_filtered($branch_id)
    {
        $this->_get_datatables_query($branch_id);
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all($branch_id)
    {
        $this->db->from($this->table);
   //      if(!empty($branch_id)){
 		// 	$this->db->where('branch_id', $branch_id);
 		// }
        return $this->db->count_all_results();
    }
	}
