<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class CoreBranch extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('CoreBranch_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['branchstatus']		= $this->configuration->BranchStatus();
			$data['main_view']['corebranch']		= $this->CoreBranch_model->getDataCoreBranch();
			$data['main_view']['content']			= 'CoreBranch/ListCoreBranch_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function addCoreBranch(){
			$data['main_view']['branchstatus']		= $this->configuration->BranchStatus();
			$data['main_view']['corebranch']		= create_double($this->CoreBranch_model->getCoreBranch(),'branch_id','branch_code');
			$data['main_view']['acctaccount']		= create_double($this->CoreBranch_model->getAcctAccount(),'account_id','account_code');	
			$data['main_view']['content']			= 'CoreBranch/FormAddCoreBranch_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function getBranchName(){
			$branch_parent_id		= $this->input->post('branch_parent_id',true);
			$branch_name = $this->CoreBranch_model->getBranchName($branch_parent_id);
			
			$result = array();
			$result = array("status" => "true", "branch_parent_id"=>trim($branch_parent_id,' '), "branch_name" => $branch_name);
			
			echo json_encode($result);
		}
		
		public function processAddCoreBranch(){
			$data = array(
				'branch_code'				=> $this->input->post('branch_code', true),
				'branch_name'				=> $this->input->post('branch_name', true),
				'branch_city'				=> $this->input->post('branch_city', true),
				'branch_address'			=> $this->input->post('branch_address', true),
				'branch_contact_person'		=> $this->input->post('branch_contact_person', true),
				'branch_phone1'				=> $this->input->post('branch_phone1', true),
				'branch_email'				=> $this->input->post('branch_email', true),
				'account_rak_id'			=> $this->input->post('account_rak_id', true),
				'account_aka_id'			=> $this->input->post('account_aka_id', true),
				'branch_manager'			=> $this->input->post('branch_manager', true),
			);
			
			$this->form_validation->set_rules('branch_code', 'Code', 'required');
			$this->form_validation->set_rules('branch_name', 'Name', 'required');
			$this->form_validation->set_rules('branch_city', 'Kota', 'required');
			$this->form_validation->set_rules('branch_address', 'Address', 'required');
			$this->form_validation->set_rules('branch_contact_person', 'Contact Person', 'required');
			$this->form_validation->set_rules('branch_phone1', 'Phone', 'required');
			$this->form_validation->set_rules('branch_email', 'Email', 'required');
			$this->form_validation->set_rules('account_rak_id', 'Perkiraan RAK', 'required');
			$this->form_validation->set_rules('account_aka_id', 'Perkiraan AKA', 'required');
			$this->form_validation->set_rules('branch_manager', 'Kepala Manager', 'required');
			
			if($this->form_validation->run()==true){
				if($this->CoreBranch_model->insertCoreBranch($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Cabang Sukses
							</div> ";
					$this->session->unset_userdata('addcorebranch');
					$this->session->set_userdata('message',$msg);
					redirect('branch/add');
				}else{
					$this->session->set_userdata('addcorebranch',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Cabang Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('branch/add');
				}
			}else{
				$this->session->set_userdata('addcorebranch',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('branch/add');
			}
		}
		
		public function editCoreBranch(){
			$data['main_view']['corebranchparent']	= create_double($this->CoreBranch_model->getCoreBranch(),'branch_id','branch_code');
			$data['main_view']['acctaccount']		= create_double($this->CoreBranch_model->getAcctAccount(),'account_id','account_code');
			$data['main_view']['branchstatus']		= $this->configuration->BranchStatus();
			$data['main_view']['corebranch']		= $this->CoreBranch_model->getCoreBranch_Detail($this->uri->segment(3));
			$data['main_view']['content']			= 'CoreBranch/FormEditCoreBranch_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processEditCoreBranch(){
			$data = array(
				'branch_id'					=> $this->input->post('branch_id', true),
				'branch_code'				=> $this->input->post('branch_code', true),
				'branch_name'				=> $this->input->post('branch_name', true),
				'branch_city'				=> $this->input->post('branch_city', true),
				'branch_address'			=> $this->input->post('branch_address', true),
				'branch_contact_person'		=> $this->input->post('branch_contact_person', true),
				'branch_phone1'				=> $this->input->post('branch_phone1', true),
				'branch_email'				=> $this->input->post('branch_email', true),
				'account_rak_id'			=> $this->input->post('account_rak_id', true),
				'account_aka_id'			=> $this->input->post('account_aka_id', true),
				'branch_manager'			=> $this->input->post('branch_manager', true),
			);
			
			$this->form_validation->set_rules('branch_code', 'Code', 'required');
			$this->form_validation->set_rules('branch_name', 'Name', 'required');
			$this->form_validation->set_rules('branch_city', 'Kota', 'required');
			$this->form_validation->set_rules('branch_address', 'Address', 'required');
			$this->form_validation->set_rules('branch_contact_person', 'Contact Person', 'required');
			$this->form_validation->set_rules('branch_phone1', 'Phone', 'required');
			$this->form_validation->set_rules('branch_email', 'Email', 'required');
			$this->form_validation->set_rules('account_rak_id', 'Perkiraan RAK', 'required');
			$this->form_validation->set_rules('account_aka_id', 'Perkiraan AKA', 'required');
			$this->form_validation->set_rules('branch_manager', 'Kepala Manager', 'required');
			
			if($this->form_validation->run()==true){
				if($this->CoreBranch_model->updateCoreBranch($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processMachinesupplier',$auth['username'],'edit machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Cabang Sukses
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('branch/edit/'.$data['branch_id']);
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Cabang Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('branch/edit/'.$data['branch_id']);
				}
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('branch/edit/'.$data['branch_id']);
			}				
		}
		
		public function deleteCoreBranch(){
			if($this->CoreBranch_model->deleteCoreBranch($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				// $this->fungsi->set_log($auth['suppliername'],'1005','Application.machine.delete',$auth['suppliername'],'Delete machine');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Hapus Data Cabang Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('branch');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Hapus Data Cabang Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('branch');
			}
		}
	}
?>