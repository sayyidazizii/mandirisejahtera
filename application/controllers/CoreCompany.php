<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class CoreCompany extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('CoreCompany_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['corememberclass']		= $this->CoreCompany_model->getDataCoreCompany();
			$data['main_view']['content']				= 'CoreCompany/ListCoreCompany_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function addCoreCompany(){	
			$data['main_view']['content']			= 'CoreCompany/FormAddCoreCompany_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function getBranchName(){
			$branch_parent_id		= $this->input->post('branch_parent_id',true);
			$branch_name = $this->CoreCompany_model->getBranchName($branch_parent_id);
			
			$result = array();
			$result = array("status" => "true", "branch_parent_id"=>trim($branch_parent_id,' '), "branch_name" => $branch_name);
			
			echo json_encode($result);
		}
		
		public function processAddCoreCompany(){
			$data = array(
				'company_code'					=> $this->input->post('company_code', true),
				'company_name'					=> $this->input->post('company_name', true),
				'company_mandatory_savings'		=> $this->input->post('company_mandatory_savings', true),
				'company_address'				=> $this->input->post('company_address', true),
			);
			
			$this->form_validation->set_rules('company_code', 'Code', 'required');
			$this->form_validation->set_rules('company_name', 'Name', 'required');
			$this->form_validation->set_rules('company_mandatory_savings', 'Mandatory Savings', 'required');
			// $this->form_validation->set_rules('company_address', 'Address', 'required');
			
			if($this->form_validation->run()==true){
				if($this->CoreCompany_model->insertCoreCompany($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Perusahaan Sukses
							</div> ";
					$this->session->unset_userdata('addcorebranch');
					$this->session->set_userdata('message',$msg);
					redirect('company/add');
				}else{
					$this->session->set_userdata('addcorebranch',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Perusahaan Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('company/add');
				}
			}else{
				$this->session->set_userdata('addcorebranch',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('company/add');
			}
		}
		
		public function editCoreCompany(){
			$data['main_view']['corecompany']		= $this->CoreCompany_model->getCoreCompany_Detail($this->uri->segment(3));
			$data['main_view']['content']			= 'CoreCompany/FormEditCoreCompany_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processEditCoreCompany(){
			$data = array(
				'company_id'					=> $this->input->post('company_id', true),
				'company_code'					=> $this->input->post('company_code', true),
				'company_name'					=> $this->input->post('company_name', true),
				'company_mandatory_savings'		=> $this->input->post('company_mandatory_savings', true),
				'company_address'				=> $this->input->post('company_address', true),
			);
			
			$this->form_validation->set_rules('company_code', 'Code', 'required');
			$this->form_validation->set_rules('company_name', 'Name', 'required');
			$this->form_validation->set_rules('company_mandatory_savings', 'Mandatory Savings', 'required');
			// $this->form_validation->set_rules('company_address', 'Address', 'required');
			
			if($this->form_validation->run()==true){
				if($this->CoreCompany_model->updateCoreCompany($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processMachinesupplier',$auth['username'],'edit machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Perusahaan Sukses
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('company/edit/'.$data['company_id']);
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Perusahaan Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('company/edit/'.$data['company_id']);
				}
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('company/edit/'.$data['company_id']);
			}				
		}
		
		public function deleteCoreCompany(){
			if($this->CoreCompany_model->deleteCoreCompany($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				// $this->fungsi->set_log($auth['suppliername'],'1005','Application.machine.delete',$auth['suppliername'],'Delete machine');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Hapus Data Perusahaan Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('company');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Hapus Data Perusahaan Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('company');
			}
		}
	}
?>