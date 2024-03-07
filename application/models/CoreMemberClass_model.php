<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class CoreMemberClass_model extends CI_Model {
		var $table = "core_member_class";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
		} 
		
		public function getDataCoreMemberClass(){
			$this->db->select('member_class_id, member_class_code, member_class_name, member_class_mandatory_savings');
			$this->db->from('core_member_class');
			$this->db->where('data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreMemberClass(){
			$this->db->select('member_class_id,member_class_code');
			$this->db->from('core_member_class');
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

		public function getBranchName($member_class_parent){
			$this->db->select('core_member_class.member_class_name');
			$this->db->from('core_member_class');
			$this->db->where('core_member_class.member_class_id', $member_class_parent);
			$this->db->where('core_member_class.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['member_class_name'];
		}
		
		public function insertCoreMemberClass($data){
			$query = $this->db->insert('core_member_class',$data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function getCoreMemberClass_Detail($member_class_id){
			$this->db->select('member_class_id, member_class_code, member_class_name, member_class_mandatory_savings');
			$this->db->from('core_member_class');
			$this->db->where('member_class_id', $member_class_id);
			return $this->db->get()->row_array();
		}
		
		public function updateCoreMemberClass($data){
			$this->db->where("member_class_id",$data['member_class_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function deleteCoreMemberClass($member_class_id){
			$this->db->where("member_class_id",$member_class_id);
			$query = $this->db->update($this->table, array('data_state'=>1));
			if($query){
				return true;
			}else{
				return false;
			}
		}
	}
?>