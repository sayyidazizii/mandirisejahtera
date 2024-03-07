<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class CoreBranch_model extends CI_Model {
		var $table = "core_branch";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getDataCoreBranch(){
			$this->db->select('branch_id, branch_code, branch_name, branch_city, branch_address, branch_contact_person, branch_email, branch_phone1, branch_phone2, branch_manager');
			$this->db->from('core_branch');
			$this->db->where('data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreBranch(){
			$this->db->select('branch_id,branch_code');
			$this->db->from('core_branch');
			$this->db->where('data_state', 0);
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

		// public function getCoreBranchParent($branch_id){
		// 	$this->db->select('branch_id,branch_code');
		// 	$this->db->from('core_branch');
		// 	$this->db->where('branch_id', $branch_id)
		// 	$this->db->where('data_state', 0);
		// 	$result = $this->db->get()->result_array();
		// 	return $result;
		// }

		public function getBranchName($branch_parent){
			$this->db->select('core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.branch_id', $branch_parent);
			$this->db->where('core_branch.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['branch_name'];
		}
		
		public function insertCoreBranch($data){
			$query = $this->db->insert('core_branch',$data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function getCoreBranch_Detail($branch_id){
			$this->db->select('branch_id, branch_code, branch_name, branch_city, branch_address, branch_contact_person, branch_email, branch_phone1, branch_phone2, account_rak_id, account_aka_id, branch_manager');
			$this->db->from('core_branch');
			$this->db->where('branch_id', $branch_id);
			return $this->db->get()->row_array();
		}
		
		public function updateCoreBranch($data){
			$this->db->where("branch_id",$data['branch_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function deleteCoreBranch($branch_id){
			$this->db->where("branch_id",$branch_id);
			$query = $this->db->update($this->table, array('data_state'=>1));
			if($query){
				return true;
			}else{
				return false;
			}
		}




		
		// public function cekacctassettypecode($id){
		// 	$this->db->select('branch_code')->from('core_branch');
		// 	$this->db->where('branch_code',$id);
		// 	$this->db->where('data_state', '0');
		// 	$result = $this->db->get()->row_array();
		// 	if(!isset($result['branch_code'])){
		// 		return '0';
		// 	}else{
		// 		return '1';
		// 	}
		// }
		
		// public function getNewCode(){
		// 	$query = $this->db->query("SELECT getNewCodeBranch() as branch_code")->row_array();
		// 	return $query['branch_code'];
		// }
		
		// public function getexport(){
		// 	$this->db->select('branch_id, branch_code, branch_name, branch_description');
		// 	$this->db->from('core_branch');
		// 	$this->db->where('data_state', '0');
		// 	$result = $this->db->get();
		// 	return $result;
		// }
	}
?>