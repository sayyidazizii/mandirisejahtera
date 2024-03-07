<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctSavings_model extends CI_Model {
		var $table = "acct_savings";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getDataAcctSavings(){
			$this->db->select('acct_savings.savings_id, acct_savings.savings_code, acct_savings.savings_name, acct_savings.account_id, acct_account.account_code, acct_account.account_name, acct_savings.savings_profit_sharing, acct_savings.savings_interest_rate, acct_savings.savings_basil');
			$this->db->from('acct_savings');
			$this->db->join('acct_account', 'acct_savings.account_id = acct_account.account_id');
			$this->db->where('acct_savings.data_state', 0);
			$this->db->where('acct_savings.savings_status', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctAccount(){
			$hasil = $this->db->query("
							SELECT acct_account.account_id, 
							CONCAT(acct_account.account_code,' - ', acct_account.account_name) as account_code 
							FROM acct_account
							WHERE acct_account.data_state='0'");
			return $hasil->result_array();
		}

		public function insertAcctSavings($data){
			return $query = $this->db->insert('acct_savings',$data);
		}
		
		public function insertAcctAccount($data){
			return $query = $this->db->insert('acct_account',$data);
		}
	
		public function getAcctSavings_Detail($savings_id){
			$this->db->select('acct_savings.savings_id, acct_savings.savings_code, acct_savings.savings_name, acct_savings.account_id, acct_account.account_code, acct_account.account_name, acct_savings.savings_profit_sharing, acct_savings.savings_interest_rate, acct_savings.savings_basil, acct_savings.account_basil_id');
			$this->db->from('acct_savings');
			$this->db->join('acct_account', 'acct_savings.account_id = acct_account.account_id');
			$this->db->where('acct_savings.data_state', 0);
			$this->db->where('acct_savings.savings_id', $savings_id);
			return $this->db->get()->row_array();
		}
		
		public function updateAcctSavings($data){
			$this->db->where("savings_id",$data['savings_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function deleteAcctSavings($savings_id){
			$this->db->where("savings_id",$savings_id);
			$query = $this->db->update($this->table, array('data_state'=>1));
			if($query){
				return true;
			}else{
				return false;
			}
		}




		
		// public function cekacctassettypecode($id){
		// 	$this->db->select('member_code')->from('acct_savings');
		// 	$this->db->where('member_code',$id);
		// 	$this->db->where('data_state', '0');
		// 	$result = $this->db->get()->row_array();
		// 	if(!isset($result['member_code'])){
		// 		return '0';
		// 	}else{
		// 		return '1';
		// 	}
		// }
		
		// public function getNewCode(){
		// 	$query = $this->db->query("SELECT getNewCodeBranch() as member_code")->row_array();
		// 	return $query['member_code'];
		// }
		
		// public function getexport(){
		// 	$this->db->select('member_id, member_code, member_name, member_description');
		// 	$this->db->from('acct_savings');
		// 	$this->db->where('data_state', '0');
		// 	$result = $this->db->get();
		// 	return $result;
		// }
	}
?>