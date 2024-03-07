<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class CoreJob_model extends CI_Model {
		var $table = "core_job";
		
		public function CoreJob_model(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getDataCoreJob(){
			$this->db->select('job_id, job_code, job_name');
			$this->db->from('core_job');
			$this->db->where('data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}
		
		public function insertCoreJob($data){
			$query = $this->db->insert('core_job',$data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function getCoreJob_Detail($job_id){
			$this->db->select('job_id, job_code, job_name');
			$this->db->from('core_job');
			$this->db->where('job_id', $job_id);
			return $this->db->get()->row_array();
		}
		
		public function updateCoreJob($data){
			$this->db->where("job_id",$data['job_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function deleteCoreJob($job_id){
			$this->db->where("job_id",$job_id);
			$query = $this->db->update($this->table, array('data_state'=>1));
			if($query){
				return true;
			}else{
				return false;
			}
		}




	
	}
?>