<?php
	class AcctPaymentPrintMutation_model extends CI_Model {
		var $table = "acct_savings_account";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getAcctSavings(){
			$this->db->select('savings_id, savings_name');
			$this->db->from('acct_savings');
			$this->db->where('data_state', 0);
			$this->db->where('savings_status', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctCreditAccountDetail($credits_account_id){
			$this->db->select('acct_credits_account.credits_account_id, acct_credits_account.credits_account_serial, acct_credits_account.member_id, core_member.member_name, core_member.member_address');
			$this->db->from('acct_credits_account');
			$this->db->join('core_member', 'acct_credits_account.member_id = core_member.member_id');
			$this->db->where('acct_credits_account.credits_account_id', $credits_account_id);
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getAcctCreditPaymentDetail($credits_account_id, $start_date, $end_date){
			$this->db->select('acct_credits_payment.credits_account_id, acct_credits_account.credits_account_serial, acct_credits_payment.credits_id, acct_credits.credits_name, acct_credits_payment.credits_payment_id, acct_credits_payment.credits_payment_date, acct_credits_payment.credits_payment_amount, acct_credits_payment.credits_payment_principal, acct_credits_payment.credits_payment_margin, acct_credits_payment.credits_principal_last_balance, acct_credits_payment.credits_margin_last_balance, acct_credits_payment.operated_name');
			$this->db->from('acct_credits_payment');
			$this->db->join('acct_credits_account', 'acct_credits_payment.credits_account_id = acct_credits_account.credits_account_id');
			$this->db->join('acct_credits', 'acct_credits_payment.credits_id = acct_credits.credits_id');
			$this->db->where('acct_credits_payment.credits_payment_date >=', $start_date);
			$this->db->where('acct_credits_payment.credits_payment_date <=', $end_date);
			$this->db->where('acct_credits_payment.credits_account_id', $credits_account_id);
			$this->db->where('acct_credits_payment.credits_print_status', 0);
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
			$this->db->order_by('credits_account_id','DESC');
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

		public function updatePrintMutationStatus($data){
			$this->db->set('acct_credits_payment.credits_print_status', $data['credits_print_status']);
			$this->db->where('acct_credits_payment.credits_payment_id', $data['credits_payment_id']);
			if($this->db->update('acct_credits_payment')){
				$this->db->set('acct_credits_account.credits_account_last_number', $data['credits_account_last_number']);
				$this->db->where('acct_credits_account.credits_account_id', $data['credits_account_id']);
				if($this->db->update('acct_credits_account')){
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		public function getCreditsAcountLastNumber($credits_account_id){
			$this->db->select('credits_account_last_number');
			$this->db->from('acct_credits_account');
			$this->db->where('credits_account_id', $credits_account_id);
			$result = $this->db->get()->row_array();
			return $result['credits_account_last_number'];
		}
		
	}
?>