<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctCreditsCollectibility_model extends CI_Model {
		var $table = "acct_savings_account";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getCreditsAccount(){
			$this->db->select('acct_credits_account.credits_account_id, acct_credits_account.credits_account_serial, acct_credits_account.member_id, core_member.member_name, core_member.member_address, acct_credits_account.credits_account_amount, acct_credits_account.credits_account_principal_amount, acct_credits_account.credits_account_interest_amount, acct_credits_account.credits_account_last_balance, acct_credits_account.credits_account_last_payment_date, acct_credits_account.credits_account_payment_date, acct_credits_account.credits_account_payment_to, acct_credits_account.credits_account_period');
			$this->db->from('acct_credits_account');
			$this->db->join('core_member', 'acct_credits_account.member_id = core_member.member_id');
			$this->db->where('acct_credits_account.data_state ', 0);
			$this->db->where('acct_credits_account.credits_account_last_balance >=', 1);
			$this->db->where('acct_credits_account.credits_approve_status', 1);
			$this->db->order_by('acct_credits_account.credits_account_serial', 'ASC');	
			$result = $this->db->get()->result_array();
			// print_r($result);exit;
			return $result;
		}

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			return $this->db->get()->row_array();
		}

		public function getPreferenceCollectibility(){
			$this->db->select('*');
			$this->db->from('preference_collectibility');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getPreferenceCollectibilityID($tunggakan){
			$this->db->select('collectibility_id');
			$this->db->from('preference_collectibility');
			$this->db->where('collectibility_bottom >= ', $tunggakan);
			$this->db->where('collectibility_top <= ', $tunggakan);
			$result = $this->db->get()->result();
			
			return $result['collectibility_id'];
		}

		public function getAcctCreditsPayment($credits_account_id){
			$this->db->select('acct_credits_payment.credits_payment_date, acct_credits_payment.credits_principal_last_balance');
			$this->db->from('acct_credits_payment');
			$this->db->where('acct_credits_payment.data_state ', 0);
			$this->db->where('acct_credits_payment.credits_account_id', $credits_account_id);
			$this->db->order_by('acct_credits_payment.credits_payment_date', 'DESC');	
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			// print_r($result);exit;
			return $result;
		}

		public function getAcctCredits(){
			$this->db->select('acct_credits.credits_id, acct_credits.credits_name');
			$this->db->from('acct_credits');
			$this->db->where('acct_credits.data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctSourceFund(){
			$this->db->select('acct_source_fund.source_fund_id, acct_source_fund.source_fund_name');
			$this->db->from('acct_source_fund');
			$this->db->where('acct_source_fund.data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getUsername($user_id){
			$this->db->select('username');
			$this->db->from('system_user');
			$this->db->where('user_id', $user_id);
			$result = $this->db->get()->row_array();
			return $result['username'];
		}
	}
?>