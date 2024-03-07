<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctCredits extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctCredits_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['acctcredits']		= $this->AcctCredits_model->getDataAcctCredits();
			$data['main_view']['content']			= 'AcctCredits/ListAcctCredits_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function addAcctCredits(){
			$data['main_view']['accountstatus']				= $this->configuration->AccountStatus();
			$data['main_view']['kelompokperkiraan']			= $this->configuration->KelompokPerkiraan();
			$data['main_view']['acctaccount']				= create_double($this->AcctCredits_model->getAcctAccount(),'account_id', 'account_code');
			$data['main_view']['content']			= 'AcctCredits/FormAddAcctCredits_view';
			$this->load->view('MainPage_view',$data);
		}

		public function function_state_add(){
			$unique 	= $this->session->userdata('unique');
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctcredits-'.$unique['unique']);
			$sessions['active_tab'] = $value;
			$this->session->set_userdata('addacctcredits-'.$unique['unique'],$sessions);
		}
		
		public function function_elements_add(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctcredits-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('addacctcredits-'.$unique['unique'],$sessions);
		}

		public function reset_data(){
			$unique 	= $this->session->userdata('unique');
			$sessions	= $this->session->unset_userdata('addacctcredits-'.$unique['unique']);
			redirect('credits/add');
		}

		public function processAddAcctAccount(){
			$auth = $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');

			$data = array(
				'account_code'		=> $this->input->post('account_code', true),
				'account_name'		=> $this->input->post('account_name', true),
				'account_type_id'	=> $this->input->post('account_type_id', true),
				'account_group'		=> $this->input->post('account_group', true),
				'created_id'		=> $auth['user_id'],
				'created_on'		=> date('Y-m-d H:i:s'),
			);
			
			if($this->AcctCredits_model->insertAcctAccount($data)){
				$auth = $this->session->userdata('auth');
				// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
				$msg = "<div class='alert alert-success alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Tambah Data Perkiraan Sukses
						</div> ";

				$this->session->unset_userdata('addacctcredits-'.$unique['unique']);
				$this->session->set_userdata('message',$msg);
				$this->session->set_userdata('message',$msg);
				redirect('credits/add');
			}else{
				$this->session->set_userdata('addacctcredits',$data);
				$msg = "<div class='alert alert-danger alert-dismissable'>
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Tambah Data Perkiraan Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('credits/add');
			}
		}
		
		public function processAddAcctCredits(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'credits_code'				=> $this->input->post('credits_code', true),
				'credits_name'				=> $this->input->post('credits_name', true),
				'receivable_account_id'		=> $this->input->post('receivable_account_id', true),
				'income_account_id'			=> $this->input->post('income_account_id', true),
				'credits_fine'				=> $this->input->post('credits_fine', true),
				'created_id'				=> $auth['user_id'],
				'created_on'				=> date('Y-m-d H:i:s'),
			);
			
			$this->form_validation->set_rules('credits_code', 'Kode Pinjaman', 'required');
			$this->form_validation->set_rules('credits_name', 'Nama Pinjaman', 'required');
			$this->form_validation->set_rules('receivable_account_id', 'Nomor Perkiraan', 'required');
			$this->form_validation->set_rules('income_account_id', 'Nomor Perkiraan Margin', 'required');
			$this->form_validation->set_rules('credits_fine', 'Prosentase Denda', 'required');
			
			if($this->form_validation->run()==true){
				if($this->AcctCredits_model->insertAcctCredits($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Kode Pinjaman Sukses
							</div> ";
					$sesi = $this->session->userdata('unique');
					$this->session->unset_userdata('addacctcredits-'.$sesi['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('credits/add');
				}else{
					$this->session->set_userdata('addacctcredits',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Kode Pinjaman Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('credits/add');
				}
			}else{
				$this->session->set_userdata('addacctcredits',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('credits/add');
			}
		}
		
		public function editAcctCredits(){
			$data['main_view']['accountstatus']				= $this->configuration->AccountStatus();
			$data['main_view']['kelompokperkiraan']			= $this->configuration->KelompokPerkiraan();
			$data['main_view']['acctaccount']				= create_double($this->AcctCredits_model->getAcctAccount(),'account_id', 'account_code');
			$data['main_view']['acctcredits']				= $this->AcctCredits_model->getAcctCredits_Detail($this->uri->segment(3));
			$data['main_view']['content']					= 'AcctCredits/FormEditAcctCredits_view';
			$this->load->view('MainPage_view',$data);
		}
		 
		public function processEditAcctCredits(){
			$data = array(
				'credits_id'				=> $this->input->post('credits_id', true),
				'credits_code'				=> $this->input->post('credits_code', true),
				'credits_name'				=> $this->input->post('credits_name', true),
				'receivable_account_id'		=> $this->input->post('receivable_account_id', true),
				'income_account_id'			=> $this->input->post('income_account_id', true),
				'credits_fine'				=> $this->input->post('credits_fine', true),
			);
			
			$this->form_validation->set_rules('credits_code', 'Kode Pinjaman', 'required');
			$this->form_validation->set_rules('credits_name', 'Nama Pinjaman', 'required');
			$this->form_validation->set_rules('receivable_account_id', 'Nomor Perkiraan', 'required');
			$this->form_validation->set_rules('income_account_id', 'Nomor Perkiraan Margin', 'required');
			$this->form_validation->set_rules('credits_fine', 'Prosentase Denda', 'required');
			
			if($this->form_validation->run()==true){
				if($this->AcctCredits_model->updateAcctCredits($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processMachinesupplier',$auth['username'],'edit machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Kode Pinjaman Sukses
							</div> ";

					$this->session->set_userdata('message',$msg);
					redirect('credits/edit/'.$data['credits_id']);
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Kode Pinjaman Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('credits/edit/'.$data['credits_id']);
				}
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('credits/edit/'.$data['credits_id']);
			}				
		}
		
		public function deleteAcctCredits(){
			if($this->AcctCredits_model->deleteAcctCredits($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				// $this->fungsi->set_log($auth['suppliername'],'1005','Application.machine.delete',$auth['suppliername'],'Delete machine');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Hapus Data Kode Pinjaman Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('credits');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Hapus Data Kode Pinjaman Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('credits');
			}
		}
		
	}
?>