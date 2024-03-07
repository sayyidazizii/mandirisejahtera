<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctNominativeSavingsPickup_model extends CI_Model {
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			$this->CI->load->model('Connection_model');
			$this->CI->load->dbforge();
		}
		
		public function getAcctNominativeSavingsPickup($savings_cash_mutation_date){
			$this->db->select('acct_savings_cash_mutation.savings_cash_mutation_id, acct_savings_cash_mutation.savings_account_id, acct_savings_cash_mutation.member_id, core_member.member_name, acct_savings_cash_mutation.savings_id, acct_savings_cash_mutation.branch_id, acct_savings_cash_mutation.mutation_id, acct_savings_cash_mutation.savings_cash_mutation_date, acct_savings_cash_mutation.savings_cash_mutation_amount, acct_savings_cash_mutation.savings_cash_mutation_remark, acct_savings_cash_mutation.savings_cash_mutation_status, acct_savings_cash_mutation.operated_name, acct_mutation.mutation_name, acct_savings_cash_mutation.pickup_status');
			$this->db->from('acct_savings_cash_mutation');
			$this->db->join('acct_mutation', 'acct_savings_cash_mutation.mutation_id = acct_mutation.mutation_id');
			$this->db->join('core_member', 'acct_savings_cash_mutation.member_id = core_member.member_id');
			$this->db->where('acct_savings_cash_mutation.data_state ', 0);
			$this->db->where('acct_savings_cash_mutation.savings_cash_mutation_status ', 1);
			$this->db->order_by('acct_savings_cash_mutation.savings_cash_mutation_date', 'ASC');
			$this->db->where('acct_savings_cash_mutation.savings_cash_mutation_date ', $savings_cash_mutation_date);	
			return $this->db->get()->result_array();
		}

		public function getAcctNominativeSavingsPickup_detail($savings_cash_mutation_id){
			$this->db->select('acct_savings_cash_mutation.savings_cash_mutation_id, acct_savings_cash_mutation.savings_account_id, acct_savings_cash_mutation.member_id, acct_savings_cash_mutation.savings_id, acct_savings_cash_mutation.branch_id, acct_savings_cash_mutation.mutation_id, acct_savings_cash_mutation.savings_cash_mutation_date, acct_savings_cash_mutation.savings_cash_mutation_amount, acct_savings_cash_mutation.savings_cash_mutation_remark, acct_savings_cash_mutation.savings_cash_mutation_status, acct_savings_cash_mutation.pickup_remark, acct_savings_cash_mutation.operated_name, acct_mutation.mutation_name, core_member.member_name');
			$this->db->from('acct_savings_cash_mutation');
			$this->db->join('acct_mutation', 'acct_savings_cash_mutation.mutation_id = acct_mutation.mutation_id');
			$this->db->join('core_member', 'acct_savings_cash_mutation.member_id = core_member.member_id');
			$this->db->where('acct_savings_cash_mutation.savings_cash_mutation_id ', $savings_cash_mutation_id);	
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function insertAcctNominativeSavingsPickup($data){
			return $this->db->insert('acct_savings_cash_mutation',$data);
		}
		

		// public function getCompanyID($created_id){
		// 	$this->db->select('acct_savings_cash_mutation.savings_cash_mutation_id');
		// 	$this->db->from('acct_savings_cash_mutation');
		// 	$this->db->where('acct_savings_cash_mutation.created_id', $created_id);
		// 	$this->db->order_by('acct_savings_cash_mutation.savings_cash_mutation_id', 'DESC');
		// 	$this->db->limit(1);
		// 	$result = $this->db->get()->row_array();
		// 	return $result['savings_cash_mutation_id'];
		// }

		public function getUserDetail($data){
			$this->db->select('system_user.*');
			$this->db->from('system_user');
			$this->db->where('system_user.username', $data['username']);
			$this->db->where('system_user.password', $data['password']);
			return $this->db->get()->row_array();
		}

		public function insertUser($data){
			return $this->db->insert('system_user', $data);
		}

		// public function getCompanyID(){
		// 	$this->db->select('acct_savings_cash_mutation.savings_cash_mutation_id');
		// 	$this->db->from('acct_savings_cash_mutation');
		// 	$this->db->order_by('acct_savings_cash_mutation.savings_cash_mutation_id', 'DESC');
		// 	$this->db->limit(1);
		// 	$result = $this->db->get()->row_array();
		// 	return $result['savings_cash_mutation_id'];
		// }
		
		// public function getAcctNominativeSavingsPickup_Detail($savings_cash_mutation_id){
		// 	$this->db->select('acct_savings_cash_mutation.savings_cash_mutation_id, acct_savings_cash_mutation.company_name, acct_savings_cash_mutation.company_email, acct_savings_cash_mutation.company_address, acct_savings_cash_mutation.company_phone_number, acct_savings_cash_mutation.company_mobile_number, acct_savings_cash_mutation.company_contact_person');
		// 	$this->db->from('acct_savings_cash_mutation');
		// 	$this->db->where('acct_savings_cash_mutation.savings_cash_mutation_id', $savings_cash_mutation_id);
		// 	return $this->db->get()->row_array();
		// }
		
		public function updateAcctNominativeSavingsPickup($data){
			$this->db->where('acct_savings_cash_mutation.savings_cash_mutation_id', $data['savings_cash_mutation_id']);
			$query = $this->db->update('acct_savings_cash_mutation', $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function deleteAcctNominativeSavingsPickup($savings_cash_mutation_id){
			$this->db->where("acct_savings_cash_mutation.savings_cash_mutation_id", $savings_cash_mutation_id);
			$query = $this->db->update('acct_savings_cash_mutation', array("data_state"=>1));
			if($query){
				return true;
			}else{
				return false;
			}
		}

		// public function getNewCode(){
		// 	$query = $this->db->query("SELECT getNewCodeCompany() as company_code")->row_array();
		// 	return $query['company_code'];
		// }
	}
?>