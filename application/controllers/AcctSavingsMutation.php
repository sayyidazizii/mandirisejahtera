<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctSavingsMutation extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctSavingsMutation_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$sesi	= 	$this->session->userdata('filter-acctsavingsmutation');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['member_id']		= '';
				$sesi['savings_id']		='';
			}

			$data['main_view']['acctsavingsmutation']		= $this->AcctSavingsMutation_model->getAcctSavingsMutation($sesi['start_date'], $sesi['end_date']);
			$data['main_view']['content']			= 'AcctSavingsMutation/ListAcctSavingsMutation_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date" 	=> tgltodb($this->input->post('start_date',true)),
				"end_date" 		=> tgltodb($this->input->post('end_date',true)),
				"member_id"		=> $this->input->post('member_id',true),
				"savings_id"	=> $this->input->post('savings_id',true),
			);

			$this->session->set_userdata('filter-acctsavingsmutation',$data);
			redirect('AcctSavingsMutation');
		}
		
		public function addAcctSavingsMutation(){
			$data['main_view']['acctsavingsaccount']		= create_double($this->AcctSavingsMutation_model->getAcctSavingsAccount(),'savings_account_id', 'savings_account_no');	
			$data['main_view']['acctmutation']				= create_double($this->AcctSavingsMutation_model->getAcctMutation(),'mutation_id', 'mutation_name');	
			$data['main_view']['content']					= 'AcctSavingsMutation/FormAddAcctSavingsMutation_view';
			$this->load->view('MainPage_view',$data);
		}

		public function getAcctSavingsAccount_Detail(){
			$savings_account_id 	= $this->input->post('savings_account_id');

			// $savings_account_id = 4;
			
			$data 			= $this->AcctSavingsMutation_model->getAcctSavingsAccount_Detail($savings_account_id);

			$result = array();
			$result = array(
				"savings_name" 					=> $data['savings_name'], 
				"member_name"					=> $data['member_name'],
				"member_address"				=> $data['member_address'],
				"city_name"						=> $data['city_name'],
				"kecamatan_name"				=> $data['kecamatan_name'],
				"identity_name"					=> $data['identity_name'],
				"member_identity_no"			=> $data['member_identity_no'],
				"savings_account_last_balance"	=> $data['savings_account_last_balance'],
			);
			echo json_encode($result);		
		}

		public function getMutationFunction(){
			$mutation_id 	= $this->input->post('mutation_id');

			// $mutation_id = 2;
			
			$mutation_function 			= $this->AcctSavingsMutation_model->getMutationFunction($mutation_id);
			echo json_encode($mutation_function);		
		}
		
		public function processAddAcctSavingsMutation(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'savings_account_id'				=> $this->input->post('savings_account_id', true),
				'mutation_id'						=> $this->input->post('mutation_id', true),
				'savings_mutation_date'				=> date('Y-m-d'),
				'savings_mutation_opening_balance'	=> $this->input->post('savings_mutation_opening_balance', true),
				'savings_mutation_last_balance'		=> $this->input->post('savings_mutation_last_balance', true),
				'savings_mutation_amount'			=> $this->input->post('savings_mutation_amount', true),
				'created_id'						=> $auth['user_id'],
				'created_on'						=> date('Y-m-d H:i:s'),
			);
			
			$this->form_validation->set_rules('savings_account_id', 'No. Mutasi', 'required');
			$this->form_validation->set_rules('mutation_id', 'Sandi', 'required');
			$this->form_validation->set_rules('savings_mutation_amount', 'Jumlah Transaksi', 'required');
			
			if($this->form_validation->run()==true){
				if($this->AcctSavingsMutation_model->insertAcctSavingsMutation($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Mutasi Simpanan Sukses
							</div> ";
					$sesi = $this->session->userdata('unique');
					$this->session->unset_userdata('addacctsavingsmutation-'.$sesi['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('AcctSavingsMutation/addAcctSavingsMutation');
				}else{
					$this->session->set_userdata('addacctsavingsmutation',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Mutasi Simpanan Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('AcctSavingsMutation/addAcctSavingsMutation');
				}
			}else{
				$this->session->set_userdata('addacctsavingsmutation',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('AcctSavingsMutation/addAcctSavingsMutation');
			}
		}
		
		public function editAcctSavingsMutation(){
			$data['main_view']['savingsprofitsharing']		= $this->configuration->SavingsProfitSharing();
			$data['main_view']['accountstatus']				= $this->configuration->AccountStatus();
			$data['main_view']['kelompokperkiraan']			= $this->configuration->KelompokPerkiraan();
			$data['main_view']['acctaccount']				= create_double($this->AcctSavingsMutation_model->getAcctAccount(),'account_id', 'account_code');
			$data['main_view']['acctsavingsmutation']		= $this->AcctSavingsMutation_model->getAcctSavingsMutation_Detail($this->uri->segment(3));
			$data['main_view']['content']			= 'AcctSavingsMutation/FormEditAcctSavingsMutation_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processEditAcctSavingsMutation(){
			$data = array(
				'savings_id'				=> $this->input->post('savings_id', true),
				'savings_code'				=> $this->input->post('savings_code', true),
				'savings_name'				=> $this->input->post('savings_name', true),
				'account_id'				=> $this->input->post('account_id', true),
				'savings_profit_sharing'	=> $this->input->post('savings_profit_sharing', true),
				'savings_nisbah'			=> $this->input->post('savings_nisbah', true),
			);
			
			$this->form_validation->set_rules('savings_code', 'Mutasi Simpanan', 'required');
			$this->form_validation->set_rules('savings_name', 'Nama Simpanan', 'required');
			$this->form_validation->set_rules('account_id', 'Nomor Perkiraan', 'required');
			
			if($this->form_validation->run()==true){
				if($this->AcctSavingsMutation_model->updateAcctSavingsMutation($data)){
					$auth = $this->session->userdata('auth');
					$this->fungsi->set_log($auth['username'],'1003','Application.machine.processMachinesupplier',$auth['username'],'edit machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Mutasi Simpanan Sukses
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('AcctSavingsMutation/editAcctSavingsMutation/'.$data['savings_id']);
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Mutasi Simpanan Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('AcctSavingsMutation/editAcctSavingsMutation/'.$data['savings_id']);
				}
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('AcctSavingsMutation/editAcctSavingsMutation/'.$data['savings_id']);
			}				
		}
		
		public function deleteAcctSavingsMutation(){
			if($this->AcctSavingsMutation_model->deleteAcctSavingsMutation($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				$this->fungsi->set_log($auth['suppliername'],'1005','Application.machine.delete',$auth['suppliername'],'Delete machine');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Hapus Data Mutasi Simpanan Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('AcctSavingsMutation');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Hapus Data Mutasi Simpanan Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('AcctSavingsMutation');
			}
		}

		public function function_state_add(){
			$unique 	= $this->session->userdata('unique');
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctsavingsmutation-'.$unique['unique']);
			$sessions['active_tab'] = $value;
			$this->session->set_userdata('addacctsavingsmutation-'.$unique['unique'],$sessions);
		}
		
		public function function_elements_add(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctsavingsmutation-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('addacctsavingsmutation-'.$unique['unique'],$sessions);
		}

		public function reset_data(){
			$unique 	= $this->session->userdata('unique');
			$sessions	= $this->session->unset_userdata('addacctsavingsmutation-'.$unique['unique']);
			redirect('AcctSavingsMutation/addAcctSavingsMutation');
		}
		
		
	}
?>