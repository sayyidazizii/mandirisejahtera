<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class CoreJob extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('CoreJob_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['corejob']		= $this->CoreJob_model->getDataCoreJob();
			$data['main_view']['content']			= 'CoreJob/ListCoreJob_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function addCoreJob(){
			$data['main_view']['content']			= 'CoreJob/FormAddCoreJob_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processAddCoreJob(){
			$data = array(
				'job_code'				=> $this->input->post('job_code', true),
				'job_name'				=> $this->input->post('job_name', true),
			);
			
			$this->form_validation->set_rules('job_code', 'Code', 'required');
			$this->form_validation->set_rules('job_name', 'Name', 'required');
			
			if($this->form_validation->run()==true){
				if($this->CoreJob_model->insertCoreJob($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Pekerjaan Sukses
							</div> ";
					$this->session->unset_userdata('addcorejob');
					$this->session->set_userdata('message',$msg);
					redirect('CoreJob/addCoreJob');
				}else{
					$this->session->set_userdata('addcorejob',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Pekerjaan Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('CoreJob/addCoreJob');
				}
			}else{
				$this->session->set_userdata('addcorejob',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('CoreJob/addCoreJob');
			}
		}
		
		public function editCoreJob(){
			$data['main_view']['corejob']		= $this->CoreJob_model->getCoreJob_Detail($this->uri->segment(3));
			$data['main_view']['content']			= 'CoreJob/FormEditCoreJob_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processEditCoreJob(){
			$data = array(
				'job_id'				=> $this->input->post('job_id', true),
				'job_code'				=> $this->input->post('job_code', true),
				'job_name'				=> $this->input->post('job_name', true),
			);
			
			$this->form_validation->set_rules('job_code', 'Code', 'required');
			$this->form_validation->set_rules('job_name', 'Name', 'required');
			
			
			if($this->form_validation->run()==true){
				if($this->CoreJob_model->updateCoreJob($data)){
					$auth = $this->session->userdata('auth');
					$this->fungsi->set_log($auth['username'],'1003','Application.machine.processMachinesupplier',$auth['username'],'edit machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Pekerjaan Sukses
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('CoreJob/editCoreJob/'.$data['job_id']);
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Pekerjaan Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('CoreJob/editCoreJob/'.$data['job_id']);
				}
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('CoreJob/editCoreJob/'.$data['job_id']);
			}				
		}
		
		public function deleteCoreJob(){
			if($this->CoreJob_model->deleteCoreJob($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				$this->fungsi->set_log($auth['suppliername'],'1005','Application.machine.delete',$auth['suppliername'],'Delete machine');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Hapus Data Pekerjaan Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('CoreJob');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Hapus Data Pekerjaan Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('CoreJob');
			}
		}

	}
?>