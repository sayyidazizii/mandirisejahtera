<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class CoreDusun_model extends CI_Model {
		var $table = "core_dusun";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getDataCoreDusun($kelurahan_id){
			$this->db->select('core_dusun.kelurahan_id, core_kelurahan.kelurahan_name, core_dusun.dusun_id, core_dusun.dusun_name');
			$this->db->from('core_dusun');
			$this->db->join('core_kelurahan','core_dusun.kelurahan_id = core_kelurahan.kelurahan_id');
			$this->db->where('core_dusun.data_state', 0);
			if(!empty($kelurahan_id)){
				$this->db->where('core_dusun.kelurahan_id', $kelurahan_id);
			}
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreCity(){
			$this->db->select('city_id,city_name');
			$this->db->from('core_city');
			$this->db->where('data_state', 0);
			$this->db->where('province_id', 72);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreKecamatan($city_id){
			$this->db->select('kecamatan_id,kecamatan_name');
			$this->db->from('core_kecamatan');
			$this->db->where('data_state', 0);
			$this->db->where('city_id', $city_id);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreKelurahan($kecamatan_id){
			$this->db->select('kelurahan_id,kelurahan_name');
			$this->db->from('core_kelurahan');
			$this->db->where('data_state', 0);
			$this->db->where('kecamatan_id', $kecamatan_id);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreKelurahan2(){
			$this->db->select('kelurahan_id,kelurahan_name');
			$this->db->from('core_kelurahan');
			$this->db->where('data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		
		public function insertCoreDusun($data){
			$query = $this->db->insert('core_dusun',$data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function getCoreDusun_Detail($dusun_id){
			$this->db->select('core_dusun.kelurahan_id, core_kelurahan.kelurahan_name, core_dusun.dusun_id, core_dusun.dusun_name');
			$this->db->from('core_dusun');
			$this->db->join('core_kelurahan','core_dusun.kelurahan_id = core_kelurahan.kelurahan_id');
			$this->db->where('core_dusun.dusun_id', $dusun_id);
			return $this->db->get()->row_array();
		}
		
		public function updateCoreDusun($data){
			$this->db->where("dusun_id",$data['dusun_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function deleteCoreDusun($dusun_id){
			$this->db->where("dusun_id",$dusun_id);
			$query = $this->db->update($this->table, array('data_state'=>1));
			if($query){
				return true;
			}else{
				return false;
			}
		}




		
		// public function cekacctassettypecode($id){
		// 	$this->db->select('dusun_code')->from('core_dusun');
		// 	$this->db->where('dusun_code',$id);
		// 	$this->db->where('data_state', '0');
		// 	$result = $this->db->get()->row_array();
		// 	if(!isset($result['dusun_code'])){
		// 		return '0';
		// 	}else{
		// 		return '1';
		// 	}
		// }
		
		// public function getNewCode(){
		// 	$query = $this->db->query("SELECT getNewCodeBranch() as dusun_code")->row_array();
		// 	return $query['dusun_code'];
		// }
		
		// public function getexport(){
		// 	$this->db->select('dusun_id, dusun_code, dusun_name, dusun_description');
		// 	$this->db->from('core_dusun');
		// 	$this->db->where('data_state', '0');
		// 	$result = $this->db->get();
		// 	return $result;
		// }
	}
?>