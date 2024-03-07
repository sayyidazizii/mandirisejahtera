<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctDeposito extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctDeposito_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['acctdeposito']		= $this->AcctDeposito_model->getDataAcctDeposito();
			$data['main_view']['content']			= 'AcctDeposito/ListAcctDeposito_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function addAcctDeposito(){ 
			$data['main_view']['savingsprofitsharing']		= $this->configuration->SavingsProfitSharing();
			$data['main_view']['accountstatus']				= $this->configuration->AccountStatus();
			$data['main_view']['kelompokperkiraan']			= $this->configuration->KelompokPerkiraan();
			$data['main_view']['acctaccount']				= create_double($this->AcctDeposito_model->getAcctAccount(),'account_id', 'account_code');
			$data['main_view']['content']					= 'AcctDeposito/FormAddAcctDeposito_view';
			$this->load->view('MainPage_view',$data);
		}

		public function function_state_add(){
			$unique 	= $this->session->userdata('unique');
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctdeposito-'.$unique['unique']);
			$sessions['active_tab'] = $value;
			$this->session->set_userdata('addacctdeposito-'.$unique['unique'],$sessions);
		}
		
		public function function_elements_add(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctdeposito-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('addacctdeposito-'.$unique['unique'],$sessions);
		}

		public function reset_data(){
			$unique 	= $this->session->userdata('unique');
			$sessions	= $this->session->unset_userdata('addacctdeposito-'.$unique['unique']);
			redirect('deposito/add');
		}

		public function processAddAcctAccount(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'account_code'		=> $this->input->post('account_code', true),
				'account_name'		=> $this->input->post('account_name', true),
				'account_type_id'	=> $this->input->post('account_type_id', true),
				'account_group'		=> $this->input->post('account_group', true),
				'created_id'		=> $auth['user_id'],
				'created_on'		=> date('Y-m-d H:i:s'),
			);
			
			if($this->AcctDeposito_model->insertAcctAccount($data)){
				$auth = $this->session->userdata('auth');
				// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
				$msg = "<div class='alert alert-success alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Tambah Data Perkiraan Sukses
						</div> ";
						
				$unique 	= $this->session->userdata('unique');
				$this->session->unset_userdata('addacctdeposito-'.$unique['unique']);
				$this->session->set_userdata('message',$msg);
				redirect('deposito/add');
			}else{
				$this->session->set_userdata('addacctdeposito-',$data);
				$msg = "<div class='alert alert-danger alert-dismissable'>
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Tambah Data Perkiraan Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('deposito/add');
			}
		}
		
		public function processAddAcctDeposito(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'deposito_code'				=> $this->input->post('deposito_code', true),
				'deposito_name'				=> $this->input->post('deposito_name', true),
				'account_id'				=> $this->input->post('account_id', true),
				// 'account_basil_id'			=> $this->input->post('account_basil_id', true),
				'deposito_period'			=> $this->input->post('deposito_period', true),
				'deposito_interest_rate'	=> $this->input->post('deposito_interest_rate', true),
				'created_id'				=> $auth['user_id'],
				'created_on'				=> date('Y-m-d H:i:s'),
			);
			
			$this->form_validation->set_rules('deposito_code', 'Kode Simpanan Berjangka', 'required');
			$this->form_validation->set_rules('deposito_name', 'Nama Simpanan Berjangka', 'required');
			$this->form_validation->set_rules('account_id', 'Nomor Perkiraan', 'required');
			// $this->form_validation->set_rules('account_basil_id', 'Nomor Perkiraan Basil', 'required');

			
			if($this->form_validation->run()==true){
				if($this->AcctDeposito_model->insertAcctDeposito($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Kode Simpanan Berjangka Sukses
							</div> ";

					$unique 	= $this->session->userdata('unique');
					$this->session->unset_userdata('addacctdeposito-'.$unique['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('deposito/add');
				}else{
					$this->session->set_userdata('addacctdeposito',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Kode Simpanan Berjangka Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('deposito/add');
				}
			}else{
				$this->session->set_userdata('addacctdeposito',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('deposito/add');
			}
		}
		
		public function editAcctDeposito(){
			$data['main_view']['savingsprofitsharing']	= $this->configuration->SavingsProfitSharing();
			$data['main_view']['accountstatus']			= $this->configuration->AccountStatus();
			$data['main_view']['kelompokperkiraan']		= $this->configuration->KelompokPerkiraan();
			$data['main_view']['acctaccount']			= create_double($this->AcctDeposito_model->getAcctAccount(),'account_id', 'account_code');
			$data['main_view']['acctdeposito']			= $this->AcctDeposito_model->getAcctDeposito_Detail($this->uri->segment(3));
			$data['main_view']['content']				= 'AcctDeposito/FormEditAcctDeposito_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processEditAcctDeposito(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'deposito_id'				=> $this->input->post('deposito_id', true),
				'deposito_code'				=> $this->input->post('deposito_code', true),
				'deposito_name'				=> $this->input->post('deposito_name', true),
				'account_id'				=> $this->input->post('account_id', true),
				'account_basil_id'			=> $this->input->post('account_basil_id', true),
				'deposito_period'			=> $this->input->post('deposito_period', true),
				'deposito_interest_rate'	=> $this->input->post('deposito_interest_rate', true),
			);
			
			$this->form_validation->set_rules('deposito_code', 'Kode Simpanan Berjangka', 'required');
			$this->form_validation->set_rules('deposito_name', 'Nama Simpanan Berjangka', 'required');
			$this->form_validation->set_rules('account_id', 'Nomor Perkiraan', 'required');
			// $this->form_validation->set_rules('account_basil_id', 'Nomor Perkiraan Basil', 'required');
			
			if($this->form_validation->run()==true){
				if($this->AcctDeposito_model->updateAcctDeposito($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processMachinesupplier',$auth['username'],'edit machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Kode Simpanan Berjangka Sukses
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('deposito/edit/'.$data['deposito_id']);
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Kode Simpanan Berjangka Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('deposito/edit/'.$data['deposito_id']);
				}
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('deposito/edit/'.$data['deposito_id']);
			}				
		}
		
		public function deleteAcctDeposito(){
			if($this->AcctDeposito_model->deleteAcctDeposito($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				// $this->fungsi->set_log($auth['suppliername'],'1005','Application.machine.delete',$auth['suppliername'],'Delete machine');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Hapus Data Kode Simpanan Berjangka Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('deposito');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Hapus Data Kode Simpanan Berjangka Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('deposito');
			}
		}
	}
?>