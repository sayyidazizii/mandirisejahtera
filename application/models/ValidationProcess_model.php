<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class ValidationProcess_model extends CI_Model{
		var $table = "system_user";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
		}
		
		public function verifyData($data){
			$this->db->select('user_id, username, password, user_group_id, log_stat, user_level, database, branch_id, branch_status, user_name, password_date');
			$this->db->from('system_user');
			$this->db->where('username', $data['username']);
			$this->db->where('password', $data['password']);
			return $this->db->get()->row_array();
		}
		
		function getLogin($data){
			$hasil = $this->db->query("UPDATE system_user SET log_stat='on' WHERE username='$data[username]' AND password='$data[password]'");
			if($hasil){
				return true;
			}else{
				return false;
			}
		}
		
		function getLogout($data){
			$hasil = $this->db->query("UPDATE system_user SET log_stat='off' WHERE username='$data[username]' AND password='$data[password]'");
			if($hasil){
				return true;
			}else{
				return false;
			}
		}
		
		function getName($id){
		
		}
	}
?>