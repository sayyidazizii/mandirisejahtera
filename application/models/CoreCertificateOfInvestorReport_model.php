<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class CoreCertificateOfInvestorReport_model extends CI_Model {
		var $table = "acct_deposito_account";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getCoreMember(){
			$this->db->select('core_member.member_id, core_member.branch_id, core_branch.branch_name, core_member.member_no, core_member.member_name, core_member.member_gender, core_member.member_place_of_birth, core_member.member_date_of_birth, core_member.member_address, core_member.province_id, core_province.province_name, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.member_phone, core_member.member_job, core_member.member_identity, core_member.member_identity_no, core_member.member_postal_code, core_member.member_mother, core_member.member_heir, core_member.member_family_relationship, core_member.member_status, core_member.member_register_date, core_member.member_principal_savings, core_member.member_special_savings, core_member.member_mandatory_savings, core_member.member_character');
			$this->db->from('core_member');
			$this->db->join('core_province', 'core_member.province_id = core_province.province_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->join('core_branch', 'core_member.branch_id = core_branch.branch_id');
			$this->db->where('core_member.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getMemberName($member_id){
			$this->db->select('member_name');
			$this->db->from('core_member');
			$this->db->where('member_id', $member_id);
			$result = $this->db->get()->row_array();
			return $result['member_name'];
		}

		public function getBranchCity($branch_id){
			$this->db->select('branch_city');
			$this->db->from('core_branch');
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['branch_city'];
		}

		public function getCoreMember_Detail($member_id){
			$this->db->select('core_member.member_id, core_member.branch_id, core_branch.branch_name, core_member.member_no, core_member.member_name, core_member.member_gender, core_member.member_place_of_birth, core_member.member_date_of_birth, core_member.member_address, core_member.province_id, core_province.province_name, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.member_phone, core_member.member_job, core_member.member_identity, core_member.member_identity_no, core_member.member_postal_code, core_member.member_mother, core_member.member_heir, core_member.member_family_relationship, core_member.member_status, core_member.member_register_date, core_member.member_principal_savings, core_member.member_special_savings, core_member.member_mandatory_savings, core_member.member_character');
			$this->db->from('core_member');
			$this->db->join('core_province', 'core_member.province_id = core_province.province_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->join('core_branch', 'core_member.branch_id = core_branch.branch_id');
			$this->db->where('core_member.data_state', 0);
			$this->db->where('core_member.member_id', $member_id);
			return $this->db->get()->row_array();
		}

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

	}
?>