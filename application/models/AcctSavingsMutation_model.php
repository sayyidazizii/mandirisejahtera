<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctSavingsMutation_model extends CI_Model {
		var $table = "acct_savings_mutation";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getAcctSavingsMutation($start_date, $end_date){
			$this->db->select('acct_savings_mutation.savings_mutation_id, acct_savings_mutation.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.member_id, core_member.member_name, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings_mutation.savings_mutation_date, acct_savings_mutation.savings_mutation_amount, acct_savings_mutation.mutation_id, acct_mutation.mutation_name');
			$this->db->from('acct_savings_mutation');
			$this->db->join('acct_mutation', 'acct_savings_mutation.mutation_id = acct_mutation.mutation_id');
			$this->db->join('acct_savings_account', 'acct_savings_mutation.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_mutation.savings_mutation_date >=', $start_date);
			$this->db->where('acct_savings_mutation.savings_mutation_date <=', $end_date);
			$this->db->where('acct_savings_mutation.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctSavingsAccount(){
			$this->db->select('savings_account_id, savings_account_no');
			$this->db->from('acct_savings_account');
			$this->db->where('data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctMutation(){
			$this->db->select('mutation_id, mutation_name');
			$this->db->from('acct_mutation');
			$this->db->where('data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctSavingsAccount_Detail($savings_account_id){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings_account.savings_account_last_balance, acct_savings_account.member_id, core_member.member_name, core_member.member_address, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.identity_id, core_identity.identity_name, core_member.member_identity_no');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->join('core_identity', 'core_member.identity_id = core_identity.identity_id');
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings_account.savings_account_id', $savings_account_id);
			return $this->db->get()->row_array();
		}

		public function getMutationFunction($mutation_id){
			$this->db->select('mutation_function');
			$this->db->from('acct_mutation');
			$this->db->where('mutation_id', $mutation_id);
			$result = $this->db->get()->row_array();
			return $result['mutation_function'];
		}
		
		public function insertAcctSavingsMutation($data){
			return $query = $this->db->insert('acct_savings_mutation',$data);
		}
		
		public function getAcctSavingsMutation_Detail($savings_mutation_id){
			$this->db->select('acct_savings_mutation.savings_mutation_id, acct_savings_mutation.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.member_id, core_member.member_name, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings_mutation.savings_mutation_date, acct_savings_mutation.savings_mutation_amount, acct_savings_mutation.mutation_id, acct_mutation.mutation_name');
			$this->db->from('acct_savings_mutation');
			$this->db->join('acct_mutation', 'acct_savings_mutation.mutation_id = acct_mutation.mutation_id');
			$this->db->join('acct_savings_account', 'acct_savings_mutation.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_mutation.data_state', 0);
			$this->db->where('acct_savings_mutation.savings_mutation_id', $savings_mutation_id);
			return $this->db->get()->row_array();
		}
		
		public function updateAcctSavingsMutation($data){
			$this->db->where("savings_id",$data['savings_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function deleteAcctSavingsMutation($savings_id){
			$this->db->where("savings_id",$savings_id);
			$query = $this->db->update($this->table, array('data_state'=>1));
			if($query){
				return true;
			}else{
				return false;
			}
		}
	}
?>