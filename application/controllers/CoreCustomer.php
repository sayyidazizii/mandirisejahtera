<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class CoreCustomer extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('CoreCustomer_model');
			$this->load->helper('sistem');
			$this->load->library('fungsi');
			$this->load->library('configuration');
			$this->load->helper('url');
			$this->load->database('default');
		}
		
		public function index(){
			$auth = $this->session->userdata('auth');
			$this->fungsi->set_log($auth['user_id'], $auth['username'], '9111', 'Application.CoreCustomer.indexCoreCustomer', $auth['user_id'], 'Index Customer');

			$data['main_view']['corecustomer']	= $this->CoreCustomer_model->getCoreCustomer();
			$data['main_view']['content']		='CoreCustomer/ListCoreCustomer_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function function_state_add(){
			$unique 	= $this->session->userdata('unique');
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addcorecustomer-'.$unique['unique']);
			$sessions['active_tab'] = $value;
			$this->session->set_userdata('addcorecustomer-'.$unique['unique'],$sessions);
		}
		
		public function function_elements_add(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addcorecustomer-'.$unique['unique']);
			$sessions2	= $this->session->userdata('editcorecustomer-'.$unique['unique']);
			$sessions[$name] = $value;
			$sessions2[$name] = $value;
			$this->session->set_userdata('addcorecustomer-'.$unique['unique'],$sessions);
			$this->session->set_userdata('editcorecustomer-'.$unique['unique'],$sessions2);
		}

		public function reset_data(){
			$unique = $this->session->userdata('unique');
			$this->session->unset_userdata('addcorecustomer-'.$unique['unique']);
			$this->session->unset_userdata('addarraycorecustomer-'.$unique['unique']);
			redirect('CoreCustomer/addCoreCustomer');
		}

		public function reset_data_edit(){
			$customer_id = $this->uri->segment(3);

			$unique = $this->session->userdata('unique');
			$this->session->unset_userdata('editcorecustomer-'.$unique['unique']);
			redirect('CoreCustomer/editCoreCustomer/'.$customer_id);
		}
		
		public function addCoreCustomer(){
			$data['main_view']['content']			= 'CoreCustomer/FormAddCoreCustomer_view';			
			$this->load->view('MainPage_view',$data);
		}

		public function processAddCoreCustomer(){
			$auth 		= $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');

			$data = array(
				'customer_name'				=> $this->input->post('customer_name', true),
				'customer_company_code'		=> $this->input->post('customer_company_code', true),
				'customer_email'			=> $this->input->post('customer_email', true),
				'customer_contact_person'	=> $this->input->post('customer_contact_person', true),
				'customer_phone_number'		=> $this->input->post('customer_phone_number', true),
				'customer_address'			=> $this->input->post('customer_address', true),
			);

			$this->form_validation->set_rules('customer_name', 'Customer Name', 'required');
			$this->form_validation->set_rules('customer_contact_person', 'Contact Person Name', 'required');
			$this->form_validation->set_rules('customer_phone_number', 'Customer Phone', 'required');
			$this->form_validation->set_rules('customer_address', 'Customer Address', 'required');
			$this->form_validation->set_rules('customer_company_code', 'Company Code', 'required');

			
			if($this->form_validation->run()==true){
				// if($this->CoreCustomer_model->insertCoreCustomer($data)){
					$query = file_get_contents(base_url().'cst_assetmanagement081118.sql');
					
					$dbcustomer = 'cst_assetmanagement_'.$data['customer_company_code'];

					// print_r($dbcustomer); exit;
					$this->CoreCustomer_model->createDatabaseCustomer($data['customer_company_code'], $dbcustomer, $query);
				
				$auth = $this->session->userdata('auth');

				$customer_id = $this->CoreCustomer_model->getCustomerID();

				$this->fungsi->set_log($auth['user_id'], $auth['username'], '9112', 'Application.coreCustomer.processAddCoreCustomer', $customer_id, 'Add New Customer');

					$msg = "<div class='alert alert-success'>                
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Add Data Customer Success
							</div> ";
					$this->session->set_userdata('message',$msg);
					$this->session->unset_userdata('addcorecustomer-'.$unique['unique']);
					redirect('CoreCustomer/addCoreCustomer');
				// }else{
				// 	$msg = "<div class='alert alert-danger'>                
				// 				<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
				// 				Add Data Customer Fail
				// 			</div> ";
				// 	$this->session->set_userdata('message',$msg);
				// 	redirect('CoreCustomer/addCoreCustomer');			
				// }
			}else{
				$msg = validation_errors("<div class='alert alert-danger'>", "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button></div>");
				$this->session->set_userdata('message',$msg);
					redirect('CoreCustomer/addCoreCustomer');
			}
		}
		
		public function editCoreCustomer(){
			$customer_id = $this->uri->segment(3);
			$data['main_view']['corecustomer']		= $this->CoreCustomer_model->getCoreCustomer_Detail($customer_id);

			$data['main_view']['content']			= 'CoreCustomer/FormEditCoreCustomer_view';		
			$this->load->view('MainPage_view',$data);
		}

		public function processEditCoreCustomer(){
			$data = array(
				'customer_id'				=> $this->input->post('customer_id', true),
				'customer_name'				=> $this->input->post('customer_name', true),
				'customer_company_code'		=> $this->input->post('customer_company_code', true),
				'customer_email'			=> $this->input->post('customer_email', true),
				'customer_contact_person'	=> $this->input->post('customer_contact_person', true),
				'customer_phone_number'		=> $this->input->post('customer_phone_number', true),
				'customer_address'			=> $this->input->post('customer_address', true),
			);

			$this->form_validation->set_rules('customer_name', 'Customer Name', 'required');
			$this->form_validation->set_rules('customer_contact_person', 'Contact Person Name', 'required');
			$this->form_validation->set_rules('customer_phone_number', 'Customer Phone', 'required');
			$this->form_validation->set_rules('customer_address', 'Customer Address', 'required');
			$this->form_validation->set_rules('customer_company_code', 'Company Code', 'required');

			
			if($this->form_validation->run()==true){
				if($this->CoreCustomer_model->updateCoreCustomer($data)==true){
					$auth 	= $this->session->userdata('auth');

					$this->fungsi->set_log($auth['user_id'], $auth['username'], '9113','Application.coreCustomer.processEditCoreCustomer', $data['customer_id'],'Customer');

					$this->fungsi->set_change_log($old_data, $data, $auth['user_id'], $data['customer_id']);

					$msg = "<div class='alert alert-success'>                
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Customer Success
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('CoreCustomer/editCoreCustomer/'.$data['customer_id']);
				}else{
					$msg = "<div class='alert alert-danger'>                
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Customer Fail
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('CoreCustomer/editCoreCustomer/'.$data['customer_id']);
				}
				
			}else{
				$msg = validation_errors("<div class='alert alert-danger'>", "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button></div>");
				$this->session->set_userdata('message',$msg);
			}
		}
		
		public function deleteCoreCustomer(){
			$customer_id = $this->uri->segment(3);
			if($this->CoreCustomer_model->deleteCoreCustomer($customer_id)){
				$auth = $this->session->userdata('auth');

				$this->fungsi->set_log($auth['user_id'], $auth['username'], '9114','Application.coreCustomer.deleteCoreCustomer', $customer_id,'Delete Core Customer');

				$msg = "<div class='alert alert-success'>                
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
							Delete Customer Success
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('CoreCustomer');
			}else{
				$msg = "<div class='alert alert-danger'>                
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
							Delete Customer Fail
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('CoreCustomer');
			}
		}
	}
?>