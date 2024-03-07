<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctNominativeSavingsPickup extends CI_Controller{
		public function __construct(){
			parent::__construct();

			// $menu = 'company';

			// $this->cekLogin();
			// $this->accessMenu($menu);

			$this->load->model('MainPage_model');
			$this->load->model('Connection_model');
			$this->load->model('AcctNominativeSavingsPickup_model');
			$this->load->helper('sistem');
			$this->load->library('fungsi');
			$this->load->library('configuration');
			// $this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
			$this->load->database('default');
		}
		
		public function index(){
			$this->load->model('AcctNominativeSavingsPickup_model');

			$auth = $this->session->userdata('auth');

			$sesi	= 	$this->session->userdata('filter-AcctNominativeSavingsPickup');

			if(!is_array($sesi)){
				$sesi['start_date']					= date('d-m-Y');
			}

			$start_date = tgltodb($sesi['start_date']);

			$data['main_view']['kelompoklaporansimpanan']	= $this->configuration->KelompokLaporanSimpanan1();

			$data['main_view']['savingspickup']				= $this->AcctNominativeSavingsPickup_model->getAcctNominativeSavingsPickup($start_date);


			$data['main_view']['content']		='AcctNominativeSavingsPickup/ListAcctNominativeSavingsPickup_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date" 		 => $this->input->post('start_date',true),
			);

			$this->session->set_userdata('filter-AcctNominativeSavingsPickup',$data);
			redirect('nominative-savings-pickup');
		}
		
		// public function addAcctNominativeSavingsPickup(){
		// 	$unique 	= $this->session->userdata('unique');

		// 	$this->session->unset_userdata('addcorecompany-'.$unique['unique']);
		// 	$data['main_view']['content']			= 'AcctNominativeSavingsPickup/FormAddAcctNominativeSavingsPickup_view';
		// 	$this->load->view('MainPage_view',$data);
		// }

		public function function_state_add(){
			$unique 	= $this->session->userdata('unique');
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addcorecompany-'.$unique['unique']);
			$sessions['active_tab'] = $value;
			$this->session->set_userdata('addcorecompany-'.$unique['unique'],$sessions);
		}
		
		public function function_elements_add(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addcorecompany-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('addcorecompany-'.$unique['unique'],$sessions);
		}

		public function reset_add(){
			$unique 	= $this->session->userdata('unique');

			$this->session->unset_userdata('addcorecompany-'.$unique['unique']);
			redirect('company/add');
		}

		public function processAddAcctNominativeSavingsPickup(){
			$this->load->model('AcctNominativeSavingsPickup_model');
			$auth = $this->session->userdata('auth');

			$data = array(
					'company_id' 					=> $this->input->post('company_id',true),
					'company_name' 					=> $this->input->post('company_name',true),
					'company_email' 				=> $this->input->post('company_email',true),
					'company_address'				=> $this->input->post('company_address',true),
					'company_phone_number' 			=> $this->input->post('company_phone_number',true),
					'company_mobile_number' 		=> $this->input->post('company_mobile_number',true),
					'company_contact_person'		=> $this->input->post('company_contact_person',true),
					'created_id'						=> $auth['user_id'],
					'created_on'						=> date('Y-m-d H:i:s'),
					'data_state'						=> 0
			);
			
			
			$this->form_validation->set_rules('company_name', 'Name Company', 'required');
			$this->form_validation->set_rules('company_email', 'Email', 'required');
			$this->form_validation->set_rules('company_address', 'Address', 'required');
			$this->form_validation->set_rules('company_phone_number', 'Phone Number', 'required');
			$this->form_validation->set_rules('company_mobile_number', 'Mobile Number', 'required');
			$this->form_validation->set_rules('company_contact_person', 'Contact Person', 'required');

			
			if($this->form_validation->run()==true){
				if($this->AcctNominativeSavingsPickup_model->insertAcctNominativeSavingsPickup($data)){
					$auth = $this->session->userdata('auth');

					// $company_id = $this->AcctNominativeSavingsPickup_model->getItemCategoryID();

					// $this->fungsi->set_log($auth['user_id'], $auth['username'], '3122', 'Application.invtItemCategory.processAddInvtItemUnit', $item_unit_id, 'Add New Invt Item Unit');

					$msg = "<div class='alert alert-success alert-dismissable'>                  
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
								Tambah Data Perusahaan Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					$unique 	= $this->session->userdata('unique');

					$this->session->unset_userdata('addcorecompany-'.$unique['unique']);
					redirect('company/add');
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'>    
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
								Tambah Data Perusahaan Gagal
							</div> ";
					$this->session->set_userdata('message',$msg);
					$this->session->set_userdata('addcorecompany',$data);
					redirect('company/add');
				}
			}else{
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>    
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				$this->session->set_userdata('addcorecompany',$data);
				redirect('company/add');
			}
		}

		public function reset_edit(){
			$company_id = $this->uri->segment(3);
			$unique 	= $this->session->userdata('unique');

			$this->session->unset_userdata('addcorecompany-'.$unique['unique']);
			redirect('company/edit/'.$company_id);
		}
		
		public function editAcctNominativeSavingsPickup(){
			$this->load->model('AcctNominativeSavingsPickup_model');

			$company_id = $this->uri->segment(3);

			$data['main_view']['corecompany']			= $this->AcctNominativeSavingsPickup_model->getAcctNominativeSavingsPickup_Detail($company_id);
			$data['main_view']['content']				= 'AcctNominativeSavingsPickup/FormEditAcctNominativeSavingsPickup_view';		
			$this->load->view('MainPage_view',$data);
		}
		
		public function processEditAcctNominativeSavingsPickup(){
			$this->load->model('AcctNominativeSavingsPickup_model');

			$data = array(
					'company_id' 					=> $this->input->post('company_id',true),
					'company_name' 					=> $this->input->post('company_name',true),
					'company_email' 				=> $this->input->post('company_email',true),
					'company_address'				=> $this->input->post('company_address',true),
					'company_phone_number' 			=> $this->input->post('company_phone_number',true),
					'company_mobile_number' 		=> $this->input->post('company_mobile_number',true),
					'company_contact_person'		=> $this->input->post('company_contact_person',true),
					'data_state'						=> 0
			);
			
			
			$this->form_validation->set_rules('company_name', 'Name Company', 'required');
			$this->form_validation->set_rules('company_email', 'Email', 'required');
			$this->form_validation->set_rules('company_address', 'Address', 'required');
			$this->form_validation->set_rules('company_phone_number', 'Phone Number', 'required');
			$this->form_validation->set_rules('company_mobile_number', 'Mobile Number', 'required');
			$this->form_validation->set_rules('company_contact_person', 'Contact Person', 'required');
			
			$old_data = $this->AcctNominativeSavingsPickup_model->getAcctNominativeSavingsPickup_Detail($data['company_id']);
			
			if($this->form_validation->run()==true){
				if($this->AcctNominativeSavingsPickup_model->updateAcctNominativeSavingsPickup($data)==true){
					$auth 	= $this->session->userdata('auth');

					// $this->fungsi->set_log($auth['user_id'], $auth['username'], '3123','Application.invtItemCategory.processEditAcctNominativeSavingsPickup', $data['company_id'],'Edit Invt Company');

					// $this->fungsi->set_change_log($old_data, $data, $auth['user_id'], $data['company_id']);

					$msg = "<div class='alert alert-success alert-dismissable'>                  
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
								Ubah Perusahaan Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('company/edit/'.$data['company_id']);
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'>    
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
								Ubah Perusahaan Gagal
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('company/edit/'.$data['company_id']);
				}
			}else{
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>    
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('company/edit/'.$data['company_id']);
			}
		}
		
		public function deleteAcctNominativeSavingsPickup(){
			$this->load->model('AcctNominativeSavingsPickup_model');
			
			$company_id = $this->uri->segment(3);
			if($this->AcctNominativeSavingsPickup_model->deleteAcctNominativeSavingsPickup($company_id)){
				$auth = $this->session->userdata('auth');

				// $this->fungsi->set_log($auth['user_id'], $auth['username'], '3124','Application.invtItemCategory.deleteAcctNominativeSavingsPickup', $data['company_id'],'Delete Invt Company');

				$msg = "<div class='alert alert-success alert-dismissable'>                  
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
							Hapus Data Perusahaan Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('company');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>    
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
							Hapus Data Perusahaan Gagal
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('company');
			}
		}

		public function showdetail(){
			$savings_cash_mutation_id = $this->uri->segment(3);

			$data['main_view']['savingspickup']				= $this->AcctNominativeSavingsPickup_model->getAcctNominativeSavingsPickup_detail($savings_cash_mutation_id);

			$data['main_view']['content']					= 'AcctNominativeSavingsPickup/DetailNominativeSavingsPickup_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processValAcctNominativeSavingsPickup(){
			$this->load->model('AcctNominativeSavingsPickup_model');

			$data = array(
					'savings_cash_mutation_id' 		=> $this->input->post('savings_cash_mutation_id',true),
					'pickup_status' 				=> 1,
					'created_on' 					=> date('Y-m-d H:i:s'),
					'pickup_remark' 				=> $this->input->post('pickup_remark',true),
			);

			//print_r($data);exit;
			
			
			$this->form_validation->set_rules('pickup_remark', 'remark', 'required');
						
			if($this->form_validation->run()==true){
				if($this->AcctNominativeSavingsPickup_model->updateAcctNominativeSavingsPickup($data)==true){
					$auth 	= $this->session->userdata('auth');

					$msg = "<div class='alert alert-success alert-dismissable'>                  
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
								Update Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('nominative-savings-pickup');
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'>    
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
								Update Gagal
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('nominative-savings-pickup/show-detail/'.$data['savings_cash_mutation_id']);
				}
			}else{
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>    
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('nominative-savings-pickup/show-detail/'.$data['savings_cash_mutation_id']);
			}
		}
	}
?>