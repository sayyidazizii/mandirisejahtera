<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class NewMemberReport_model extends CI_Model {
		var $table = "acct_savings_cash_mutation";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
		} 
		
		public function getNewMemberReport($start_date, $end_date){
			$this->db->select('core_member.*');
			$this->db->from('core_member');
			$this->db->where('core_member.data_state ', 0);
			$this->db->where('core_member.member_register_date >=', $start_date);	
			$this->db->where('core_member.member_register_date <=', $end_date);	
			return $this->db->get()->result_array();
		}

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			return $this->db->get()->row_array();
		}

		public function getCoreBranch(){
			$this->db->select('core_branch.branch_id, core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctSavings(){
			$this->db->select('acct_savings.savings_id, acct_savings.savings_name');
			$this->db->from('acct_savings');
			$this->db->where('acct_savings.data_state', 0);
			$this->db->where('acct_savings.savings_status', 0);
			return $this->db->get()->result_array();
		}

		public function getUsername($user_id){
			$this->db->select('username');
			$this->db->from('system_user');
			$this->db->where('user_id', $user_id);
			$result = $this->db->get()->row_array();
			return $result['username'];
		}

		public function getSavingsName($savings_id){
			$this->db->select('savings_name');
			$this->db->from('acct_savings');
			$this->db->where('savings_id', $savings_id);
			$result = $this->db->get()->row_array();
			return $result['savings_name'];
		}
	}
?>