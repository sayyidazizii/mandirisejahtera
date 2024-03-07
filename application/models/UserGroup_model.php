<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class UserGroup_model extends CI_Model {
		var $table = "system_user_group";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
		}
		
		public function get_list() 
		{
			//Select table name
			$table_name = "system_user_group";
			
			//Build contents query
			$this->db->select('user_group_id, user_group_level, user_group_name')->from($table_name);
			$this->db->where('data_state', '0');
			$this->db->where('user_group_status', 0);
			$result = $this->db->get()->result_array();
	
			return $result;
		}
		
		public function getMenuList($char){
			$hasil = $this->db->query("SELECT id_menu,text,type FROM system_menu Where id_menu like '$char' ORDER BY id_menu ASC ");
			$hasil = $hasil->result_array();
			return $hasil;
		}
		
		public function saveNewGroup($data){
			$this->db->set('user_group_id', 'getNewUserGroupID()', FALSE);
			$this->db->set('user_group_level', 'getNewUserGroupID()', FALSE);
			if($this->db->insert($this->table, $data)){
				return true;
			}else{
				return false;
			}
		}
		
		function getMenuID($name){
			$this->db->select('user_group_level')->from($this->table);
			$this->db->where('user_group_name',$name);
			$hasil = $this->db->get()->row_array();
			return $hasil['user_group_level'];
		}
		
		function saveMapping($data){
			return $this->db->insert("system_menu_mapping",$data);
		}
		
		function deleteMapping($level){
			$this->db->delete('system_menu_mapping', array('user_group_level' => $level)); 
		}
		
		function getDetail($level){
			$this->db->select('user_group_id,user_group_name');
			$this->db->where('user_group_level',$level);
			$result = $this->db->get('system_user_group')->row_array();
			return $result;
		}
		
		function isThisMenuInGroup($level, $id_menu){
			$hasil = $this->db->query("SELECT * FROM system_menu_mapping WHERE user_group_level='$level' AND id_menu='$id_menu'");
			$hasil = $hasil->row_array();
			if(count($hasil)>0){
				return true;
			}else{
				return false;
			}
		}
		
		function delete($id){
			$this->deleteMapping($id);
			if($this->db->delete('system_user_group',array('user_group_id' => $id))){
				return true;
			}else{
				return false;
			}
		}
		
		function cekGroupName($name){
			$this->db->select('user_group_id,user_group_name')->from('system_user_group');
			$this->db->where('user_group_name',$name);
			$hasil = $this->db->get()->row_array();
			if(count($hasil)>0){
				return false;
			}else{
				return true;
			}
		}
		
		function UpdateGroup($data){
			$this->db->where('user_group_id',$data['user_group_id']);
			$query = $this->db->update('system_user_group', $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
	}
?>