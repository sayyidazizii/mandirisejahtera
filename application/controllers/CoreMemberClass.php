<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class CoreMemberClass extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('CoreMemberClass_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['corememberclass']		= $this->CoreMemberClass_model->getDataCoreMemberClass();
			$data['main_view']['content']				= 'CoreMemberClass/ListCoreMemberClass_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function addCoreMemberClass(){	
			$data['main_view']['content']			= 'CoreMemberClass/FormAddCoreMemberClass_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function getBranchName(){
			$branch_parent_id		= $this->input->post('branch_parent_id',true);
			$branch_name = $this->CoreMemberClass_model->getBranchName($branch_parent_id);
			
			$result = array();
			$result = array("status" => "true", "branch_parent_id"=>trim($branch_parent_id,' '), "branch_name" => $branch_name);
			
			echo json_encode($result);
		}
		
		public function processAddCoreMemberClass(){
			$data = array(
				'member_class_code'	=> $this->input->post('member_class_code', true),
				'member_class_name'	=> $this->input->post('member_class_name', true),
				'member_class_mandatory_savings'	=> $this->input->post('member_class_mandatory_savings', true),
			);
			
			$this->form_validation->set_rules('member_class_code', 'Code', 'required');
			$this->form_validation->set_rules('member_class_name', 'Name', 'required');
			$this->form_validation->set_rules('member_class_mandatory_savings', 'Amount', 'required');
			
			if($this->form_validation->run()==true){
				if($this->CoreMemberClass_model->insertCoreMemberClass($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Keanggotaan Member Sukses
							</div> ";
					$this->session->unset_userdata('addcorebranch');
					$this->session->set_userdata('message',$msg);
					redirect('member-class/add');
				}else{
					$this->session->set_userdata('addcorebranch',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Keanggotaan Member Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('member-class/add');
				}
			}else{
				$this->session->set_userdata('addcorebranch',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('member-class/add');
			}
		}
		
		public function editCoreMemberClass(){
			$data['main_view']['corememberclass']	= $this->CoreMemberClass_model->getCoreMemberClass_Detail($this->uri->segment(3));
			$data['main_view']['content']			= 'CoreMemberClass/FormEditCoreMemberClass_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processEditCoreMemberClass(){
			$data = array(
				'member_class_id'		=> $this->input->post('member_class_id', true),
				'member_class_code'		=> $this->input->post('member_class_code', true),
				'member_class_name'		=> $this->input->post('member_class_name', true),
				'member_class_mandatory_savings'	=> $this->input->post('member_class_mandatory_savings', true),
			);
			
			$this->form_validation->set_rules('member_class_code', 'Code', 'required');
			$this->form_validation->set_rules('member_class_name', 'Name', 'required');
			$this->form_validation->set_rules('member_class_mandatory_savings', 'Amount', 'required');
			
			if($this->form_validation->run()==true){
				if($this->CoreMemberClass_model->updateCoreMemberClass($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processMachinesupplier',$auth['username'],'edit machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Keanggotaan Member Sukses
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('member-class/edit/'.$data['member_class_id']);
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Keanggotaan Member Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('member-class/edit/'.$data['member_class_id']);
				}
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('member-class/edit/'.$data['member_class_id']);
			}				
		}
		
		public function deleteCoreMemberClass(){
			if($this->CoreMemberClass_model->deleteCoreMemberClass($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				// $this->fungsi->set_log($auth['suppliername'],'1005','Application.machine.delete',$auth['suppliername'],'Delete machine');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Hapus Data Keanggotaan Member Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('member-class');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Hapus Data Keanggotaan Member Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('member-class');
			}
		}
	}
?>