<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class CoreCustomer_model extends CI_Model {
		var $table = "core_coremaintenance";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			$this->CI->load->model('Connection_model');
		}

		public function getCoreCustomer(){
			$this->db->select('core_customer.customer_id, core_customer.customer_name, core_customer.customer_address, core_customer.customer_phone_number, core_customer.customer_contact_person, core_customer.customer_email, core_customer.customer_company_code');
			$this->db->from('core_customer');
			$this->db->where('core_customer.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function insertCoreCustomer($data){
			$query = $this->db->insert('core_customer',$data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function createDatabaseCustomer($customer, $dbcustomer, $data){
			// $this->dbforge->create_database($dbcustomer);
			// exit;

			$database = $this->Connection_model->define_database($customer, $dbcustomer);
			$this->database = $this->load->database($database, true);

			$query = $this->database->query($data);
			exit;
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function getCustomerID(){
			$this->db->select('core_customer.customer_id');
			$this->db->from('core_customer');
			$this->db->order_by('core_customer.customer_id', 'DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['customer_id'];
		}
		
		public function getCoreCustomer_Detail($customer_id){
			$this->db->select('core_customer.customer_id, core_customer.customer_name, core_customer.customer_address, core_customer.customer_phone_number, core_customer.customer_contact_person, core_customer.customer_email, core_customer.customer_company_code');
			$this->db->from('core_customer');
			$this->db->where('core_customer.data_state', 0);
			$this->db->where('core_customer.customer_id', $customer_id);
			return $this->db->get()->row_array();
		}

		
		public function updateCoreCustomer($data){
			$this->db->where('core_customer.customer_id', $data['customer_id']);
			$query = $this->db->update('core_customer', $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function deleteCoreCustomer($customer_id){
			$this->db->where("core_customer.customer_id", $customer_id);
			$query = $this->db->update('core_customer', array("data_state"=>1));
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function getNewCode(){
			$query = $this->db->query("SELECT getNewCodeMaintenance() as maintenance_code")->row_array();
			return $query['maintenance_code'];
		}
	}
?>