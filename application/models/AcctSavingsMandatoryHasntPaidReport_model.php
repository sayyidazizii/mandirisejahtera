<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctSavingsMandatoryHasntPaidReport_model extends CI_Model {
		var $table = "acct_savings_account";
		
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
		
		public function getCoreMemberDetail( $start_date, $end_date ,$branch_id ){
			$this->db->select('core_member.member_id, core_member.member_name, core_member.member_address,core_member.member_no, core_member.member_register_date');
			$this->db->from('core_member');
			//$this->db->join('core_member', 'acct_savings_member_detail.member_id = core_member.member_id');
			$this->db->where('core_member.data_state ', 0);
			$this->db->where('core_member.member_register_date >=', $start_date);
			$this->db->where('core_member.member_register_date <=', $end_date);
		//	$this->db->where('MONTH(acct_savings_member_detail.transaction_date) <>', $month_period);
			// $this->db->where('YEAR(acct_savings_member_detail.transaction_date)', $year_period);
			if(!empty($branch_id)){
				$this->db->where('core_member.branch_id', $branch_id);
			}
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getSavingsMandatoryDetail($member_id, $month_period, $year_period){
			$this->db->select('acct_savings_member_detail.transaction_date, core_member.member_no');
			$this->db->from('acct_savings_member_detail');
			$this->db->join('core_member', 'acct_savings_member_detail.member_id = core_member.member_id');
			// $this->db->where('core_member.data_state ', 0);
			$this->db->where('acct_savings_member_detail.mutation_id',1);	
			$this->db->where('acct_savings_member_detail.member_id', $member_id);	
		 	$this->db->where('MONTH(acct_savings_member_detail.transaction_date)', $month_period);
			$this->db->where('YEAR(acct_savings_member_detail.transaction_date)', $year_period);		
			//$this->db->group_by('acct_savings_member_detail.member_id');
			//$this->db->group_by('core_member.member_id');
			// $this->db->order_by('acct_savings_member_detail.transaction_date', 'DESC');	
			// $this->db->limit(1);
			$result = $this->db->get();
			//print_r($result);exit;
			return $result;
		}

		public function getLastDate($member_id){
			$this->db->select('acct_savings_member_detail.transaction_date');
			$this->db->from('acct_savings_member_detail');
			$this->db->join('core_member', 'acct_savings_member_detail.member_id = core_member.member_id');
			$this->db->where('acct_savings_member_detail.mutation_id',1);	
			$this->db->where('acct_savings_member_detail.member_id', $member_id);			
			$this->db->order_by('acct_savings_member_detail.transaction_date', 'DESC');	
			// $this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['transaction_date'];
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