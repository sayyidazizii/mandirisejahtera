<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class User_model extends CI_Model {
		var $table = "system_user";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
		}
		
		public function get_list($branch_status, $branch_id)
		{
			//Select table name
			$table_name = "system_user";

			$this->db->select('u.username, u.password, u.user_group_id')->from($table_name." as u");
			$this->db->where('u.data_state', '0');
			$this->db->where('u.user_level', 0);
			if($branch_status == 0){
				$this->db->where('u.branch_id', $branch_id);
			}
			$result = $this->db->get();
			return $result;
		}

		public function getCoreBranch(){
			$this->db->select('branch_id,branch_name');
			$this->db->from('core_branch');
			$this->db->where('data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}
		
		public function getGroupName($id){
			$this->db->select('user_group_name')->from('system_user_group');
			$this->db->where('user_group_id',$id);
			$result = $this->db->get()->row_array();
			if(!isset($result['user_group_name'])){
				return 'Not Set';
			}else{
				return $result['user_group_name'];
			}
		}
		
		public function getGroup($branch_status){
			$this->db->select('user_group_level,user_group_name')->from('system_user_group');
			$this->db->where('user_group_level !=','1');
			if($branch_status == 0){
				$this->db->where('user_group_status',0);
			}
			
			$result = $this->db->get()->result_array();
			return $result;
		}
		
		public function saveNewuser($data){
			return $this->db->insert('system_user',$data);
		}
		
		public function getFactory(){
			$this->db->select('factory_id,factory_name')->from('core_factory');
			$this->db->where('data_state','0');
			$result = $this->db->get()->result_array();
			return $result;
		}
		
		public function getDetail($id){
			$this->db->select('username, password, user_group_id, log_stat')->from($this->table);
			$this->db->where('username',$id);
			return $this->db->get()->row_array();
		}
		
		function cekuserNameExist($username){
			$this->db->select('username, password, user_group_id, log_stat')->from($this->table);
			$this->db->where('username',$username);
			$hasil = $this->db->get()->row_array();
			if(count($hasil)>0){
				return false;
			}else{
				return true;
			}
		}
		
		public function saveEdituser($data,$id){
			$this->db->where("username",$id);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function getPassword(){
			$this->db->select('default_password')->from('preference_company');
			// $this->db->where('company_id','2');
			$return = $this->db->get()->row_array();
			return $return['default_password'];
		}
		
		public function delete($id){
			// $this->db->where("username",$id);
			// $query = $this->db->update($this->table, array('data_state'=>'1', 'log_stat'=>'off'));
			// if($query){
				// return true;
			// }else{
				// return false;
			// }
			return $this->db->delete('system_user',array('username'=>$id));
		}
	}
?>