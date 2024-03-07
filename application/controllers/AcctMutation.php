<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctMutation extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctMutation_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['accountstatus']		= $this->configuration->AccountStatus();
			$data['main_view']['acctmutation']		= $this->AcctMutation_model->getDataAcctMutation();
			// exit;
			$data['main_view']['content']			= 'AcctMutation/ListAcctMutation_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function addAcctMutation(){
			$data['main_view']['accountstatus']		= $this->configuration->AccountStatus();
			$data['main_view']['content']			= 'AcctMutation/FormAddAcctMutation_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processAddAcctMutation(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'mutation_code'				=> $this->input->post('mutation_code', true),
				'mutation_name'				=> $this->input->post('mutation_name', true),
				'mutation_function'			=> $this->input->post('mutation_function', true),
				'mutation_status'			=> $this->input->post('mutation_status', true),
			);

			// print_r($data);exit;
			
			$this->form_validation->set_rules('mutation_name', 'Nama', 'required');
			$this->form_validation->set_rules('mutation_code', 'Kode', 'required');
			$this->form_validation->set_rules('mutation_function', 'Fungsi', 'required');
			$this->form_validation->set_rules('mutation_status', 'Status', 'required');

			
			if($this->form_validation->run()==true){
				// print_r($data);
				if($this->AcctMutation_model->insertAcctMutation($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Mutasi Sukses
							</div> ";
					$this->session->unset_userdata('addacctmutation');
					$this->session->set_userdata('message',$msg);
					redirect('mutation/add');
				}else{
					$this->session->set_userdata('addacctmutation',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Mutasi Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('mutation/add');
				}
			}else{
				$this->session->set_userdata('addacctmutation',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('mutation/add');
			}
		}
		
		public function editAcctMutation(){
			$this->uri->segment(3);


			$data['main_view']['accountstatus']		= $this->configuration->AccountStatus();
			$data['main_view']['acctmutation']		= $this->AcctMutation_model->getAcctMutation_Detail($this->uri->segment(3));
			$data['main_view']['content']			= 'AcctMutation/FormEditAcctMutation_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processEditAcctMutation(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'mutation_id'				=> $this->input->post('mutation_id', true),
				'mutation_name'				=> $this->input->post('mutation_name', true),
				'mutation_code'				=> $this->input->post('mutation_code', true),
				'mutation_function'			=> $this->input->post('mutation_function', true),
				'mutation_status'			=> $this->input->post('mutation_status', true),
			);
			
			$this->form_validation->set_rules('mutation_name', 'Nama', 'required');
			$this->form_validation->set_rules('mutation_code', 'Kode', 'required');
			$this->form_validation->set_rules('mutation_function', 'Fungsi', 'required');
			$this->form_validation->set_rules('mutation_status', 'Status', 'required');
			
			if($this->form_validation->run()==true){
				if($this->AcctMutation_model->updateAcctMutation($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processMachinesupplier',$auth['username'],'edit machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Mutasi Sukses
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('mutation/edit/'.$data['mutation_id']);
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Mutasi Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('mutation/edit/'.$data['mutation_id']);
				}
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('mutation/edit/'.$data['mutation_id']);
			}				
		}
		
		public function deleteAcctMutation(){
			if($this->AcctMutation_model->deleteAcctMutation($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				// $this->fungsi->set_log($auth['suppliername'],'1005','Application.machine.delete',$auth['suppliername'],'Delete machine');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Hapus Data Mutasi Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('mutation');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Hapus Data Mutasi Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('mutation');
			}
		}
	}
?>