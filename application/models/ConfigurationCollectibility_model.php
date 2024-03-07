<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class ConfigurationCollectibility_model extends CI_Model {
		var $table = "preference_collectibility";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getConfigurationCollectibility(){
			$this->db->select('*');
			$this->db->from('preference_collectibility');
			$result = $this->db->get()->result_array();
			// print_r($result);exit;
			return $result;
		}
		
		public function updateConfigurationCollectibility($data){
			$this->db->where("collectibility_id",$data['collectibility_id']);
			$query = $this->db->update('preference_collectibility', $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
	}
?>