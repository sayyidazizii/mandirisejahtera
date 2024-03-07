<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class CoreCompany_model extends CI_Model {
		var $table = "core_company";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
		} 
		
		public function getDataCoreCompany(){
			$this->db->select('company_id, company_code, company_name, company_address, company_mandatory_savings');
			$this->db->from('core_company');
			$this->db->where('data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreCompany(){
			$this->db->select('company_id,company_code');
			$this->db->from('core_company');
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

		public function getBranchName($company_parent){
			$this->db->select('core_company.company_name');
			$this->db->from('core_company');
			$this->db->where('core_company.company_id', $company_parent);
			$this->db->where('core_company.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['company_name'];
		}
		
		public function insertCoreCompany($data){
			$query = $this->db->insert('core_company',$data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function getCoreCompany_Detail($company_id){
			$this->db->select('company_id, company_code, company_name, company_address, company_mandatory_savings');
			$this->db->from('core_company');
			$this->db->where('company_id', $company_id);
			return $this->db->get()->row_array();
		}
		
		public function updateCoreCompany($data){
			$this->db->where("company_id",$data['company_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function deleteCoreCompany($company_id){
			$this->db->where("company_id",$company_id);
			$query = $this->db->update($this->table, array('data_state'=>1));
			if($query){
				return true;
			}else{
				return false;
			}
		}
	}
?>