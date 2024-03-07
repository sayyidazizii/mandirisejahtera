<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctBankAccount extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctBankAccount_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['acctbankaccount']			= $this->AcctBankAccount_model->getDataAcctBankAccount();	
			$data['main_view']['accountstatus']			= $this->configuration->AccountStatus();
			$data['main_view']['content']				= 'AcctBankAccount/ListAcctBankAccount_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function addAcctBankAccount(){
			$data['main_view']['acctaccount']			= create_double($this->AcctBankAccount_model->getAcctAccount(),'account_id', 'account_code');
			$data['main_view']['content']				= 'AcctBankAccount/FormAddAcctBankAccount_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processAddAcctBankAccount(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'bank_account_code'		=> $this->input->post('bank_account_code', true),
				'bank_account_name'		=> $this->input->post('bank_account_name', true),
				'bank_account_no'		=> $this->input->post('bank_account_no', true),
				'account_id'			=> $this->input->post('account_id', true),
				// 'created_id'			=> $auth['user_id'],
				// 'created_on'			=> date('Y-m-d H:i:s'),
			);

			$this->form_validation->set_rules('bank_account_code', 'Kode Bank', 'required');
			$this->form_validation->set_rules('bank_account_name', 'Nama Bank', 'required');
			$this->form_validation->set_rules('account_id', 'Nomor Perkiraan', 'required');
			
			if($this->form_validation->run()==true){
				if($this->AcctBankAccount_model->insertAcctBankAccount($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Bank Sukses
							</div> ";
					$this->session->unset_userdata('addacctsavings');
					$this->session->set_userdata('message',$msg);
					redirect('bank-account/add');
				}else{
					$this->session->set_userdata('addacctsavings',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Bank Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('bank-account/add');
				}
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('bank-account/add');
			}
		}
		
		public function editAcctBankAccount(){
			$data['main_view']['acctbankaccount']		= $this->AcctBankAccount_model->getAcctBankAccount_Detail($this->uri->segment(3));
			$data['main_view']['acctaccount']			= create_double($this->AcctBankAccount_model->getAcctAccount(),'account_id', 'account_code');
			$data['main_view']['content']				= 'AcctBankAccount/FormEditAcctBankAccount_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processEditAcctBankAccount(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'bank_account_id'		=> $this->input->post('bank_account_id', true),
				'bank_account_code'		=> $this->input->post('bank_account_code', true),
				'bank_account_name'		=> $this->input->post('bank_account_name', true),
				'bank_account_no'		=> $this->input->post('bank_account_no', true),
				'account_id'			=> $this->input->post('account_id', true),
			);
			
			$this->form_validation->set_rules('bank_account_code', 'Kode Bank', 'required');
			$this->form_validation->set_rules('bank_account_name', 'Nama Bank', 'required');
			$this->form_validation->set_rules('account_id', 'Nomor Perkiraan', 'required');
			
			if($this->form_validation->run()==true){
				if($this->AcctBankAccount_model->updateAcctBankAccount($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processMachinesupplier',$auth['username'],'edit machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Data Bank Sukses
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('bank-account/edit/'.$data['bank_account_id']);
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Data Bank Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('bank-account/edit/'.$data['bank_account_id']);
				}
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('bank-account/edit/'.$data['bank_account_id']);
			}				
		}
		
		public function deleteAcctBankAccount(){
			if($this->AcctBankAccount_model->deleteAcctBankAccount($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				$this->fungsi->set_log($auth['suppliername'],'1005','Application.machine.delete',$auth['suppliername'],'Delete machine');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Hapus Data Bank Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('bank-account');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Hapus Data Bank Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('bank-account');
			}
		}
	}
?>