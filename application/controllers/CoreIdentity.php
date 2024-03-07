<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class CoreIdentity extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('CoreIdentity_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['coreidentity']		= $this->CoreIdentity_model->getDataCoreIdentity();
			$data['main_view']['content']			= 'CoreIdentity/ListCoreIdentity_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function addCoreIdentity(){
			$data['main_view']['content']			= 'CoreIdentity/FormAddCoreIdentity_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processAddCoreIdentity(){
			$data = array(
				'identity_code'				=> $this->input->post('identity_code', true),
				'identity_name'				=> $this->input->post('identity_name', true),
			);
			
			$this->form_validation->set_rules('identity_code', 'Code', 'required');
			$this->form_validation->set_rules('identity_name', 'Name', 'required');
			
			if($this->form_validation->run()==true){
				if($this->CoreIdentity_model->insertCoreIdentity($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Identitas Sukses
							</div> ";
					$this->session->unset_userdata('addcoreidentity');
					$this->session->set_userdata('message',$msg);
					redirect('CoreIdentity/addCoreIdentity');
				}else{
					$this->session->set_userdata('addcoreidentity',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Identitas Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('CoreIdentity/addCoreIdentity');
				}
			}else{
				$this->session->set_userdata('addcoreidentity',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('CoreIdentity/addCoreIdentity');
			}
		}
		
		public function editCoreIdentity(){
			$data['main_view']['coreidentity']		= $this->CoreIdentity_model->getCoreIdentity_Detail($this->uri->segment(3));
			$data['main_view']['content']			= 'CoreIdentity/FormEditCoreIdentity_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processEditCoreIdentity(){
			$data = array(
				'identity_id'				=> $this->input->post('identity_id', true),
				'identity_code'				=> $this->input->post('identity_code', true),
				'identity_name'				=> $this->input->post('identity_name', true),
			);
			
			$this->form_validation->set_rules('identity_code', 'Code', 'required');
			$this->form_validation->set_rules('identity_name', 'Name', 'required');
			
			
			if($this->form_validation->run()==true){
				if($this->CoreIdentity_model->updateCoreIdentity($data)){
					$auth = $this->session->userdata('auth');
					$this->fungsi->set_log($auth['username'],'1003','Application.machine.processMachinesupplier',$auth['username'],'edit machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Identitas Sukses
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('CoreIdentity/editCoreIdentity/'.$data['identity_id']);
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Identitas Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('CoreIdentity/editCoreIdentity/'.$data['identity_id']);
				}
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('CoreIdentity/editCoreIdentity/'.$data['identity_id']);
			}				
		}
		
		public function deleteCoreIdentity(){
			if($this->CoreIdentity_model->deleteCoreIdentity($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				$this->fungsi->set_log($auth['suppliername'],'1005','Application.machine.delete',$auth['suppliername'],'Delete machine');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Hapus Data Identitas Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('CoreIdentity');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Hapus Data Identitas Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('CoreIdentity');
			}
		}

	}
?>