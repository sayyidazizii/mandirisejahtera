<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class Library_model extends CI_Model {
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
		}

		public function getIDMenu($id){
			$this->db->select('system_menu.id_menu');
			$this->db->from('system_menu');
			$this->db->where('system_menu.id', $id);
			$result = $this->db->get()->row_array();
			return $result['id_menu'];
		}
		
		public function getUserGroupLevel($id){
			$this->db->select('system_user_group.user_group_level');
			$this->db->from('system_user_group');
			$this->db->where('system_user_group.user_group_id', $id);
			$result = $this->db->get()->row_array();
			return $result['user_group_level'];
		}
		
		public function getIDMenuOnSystemMapping($id){
			$auth 	= $this->session->userdata('auth');
			$level 	= $this->getUserGroupLevel($auth['user_group_level']);
			
			$this->db->select('system_menu_mapping.id_menu');
			$this->db->from('system_menu_mapping');
			$this->db->where('system_menu_mapping.id_menu', $id);
			$this->db->where('system_menu_mapping.user_group_level', $level);
			$result = $this->db->get()->row_array();
			if($result['id_menu']!=''){
				$return = '1';
			}else{
				$return = '0';
			}
			return $return;
		}

	}
?>