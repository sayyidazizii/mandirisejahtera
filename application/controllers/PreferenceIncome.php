<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class PreferenceIncome extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('PreferenceIncome_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$unique 									= $this->session->userdata('unique');

			$data['main_view']['preferenceincome']		= $this->PreferenceIncome_model->getPreferenceIncome();
			$data['main_view']['kelompokperkiraan']		= $this->configuration->KelompokPerkiraan();
			$data['main_view']['acctaccount']			= create_double($this->PreferenceIncome_model->getAcctAccount(),'account_id', 'account_code');
			
			$datapreferenceincome						= $this->session->userdata('addpreferenceincomeawal-'.$unique['unique']);

			if (empty($datapreferenceincome)){
				$preferenceincome						= $this->PreferenceIncome_model->getPreferenceIncome();

				foreach ($preferenceincome  as $key => $val) {
					$dataincome = array(
						'income_id'						=> $val['income_id'],
						'income_name' 					=> $val['income_name'],
						'income_percentage'				=> $val['income_percentage'],
						'income_group'					=> $val['income_group'],
						'account_id'					=> $val['account_id'],
						'income_status'					=> $val['income_status'],
					);

					$unique 			= $this->session->userdata('unique');
					$session_name 		= $this->input->post('session_name',true);
					$dataArrayHeader	= $this->session->userdata('addpreferenceincome-'.$unique['unique']);

					$dataArrayHeader[$dataincome['income_id']] = $dataincome;
					
					$this->session->set_userdata('addpreferenceincome-'.$unique['unique'],$dataArrayHeader);
					$this->session->set_userdata('addpreferenceincomeawal-'.$unique['unique'],$dataArrayHeader);
				}
			}
			$data['main_view']['content']				= 'PreferenceIncome/FormAddPreferenceIncome_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processAddPreferenceIncome(){
			$auth 			= $this->session->userdata('auth');
			$unique 		= $this->session->userdata('unique');

			$data = array(
				'income_name'			=> $this->input->post('income_name', true),
				'income_group'			=> $this->input->post('income_group', true),
				'income_percentage'		=> $this->input->post('income_percentage', true),
				'account_id'			=> $this->input->post('account_id', true),
				'created_id'			=> $auth['user_id'],
				'created_on'			=> date('Y-m-d H:i:s'),
			);

			$this->form_validation->set_rules('income_name', 'Nama Pendapatan', 'required');
			$this->form_validation->set_rules('income_group', 'Golongan Perkiraan', 'required');
			$this->form_validation->set_rules('income_percentage', 'Prosentase', 'required');
			$this->form_validation->set_rules('account_id', 'Nomor Perkiraan', 'required');
			
			if($this->form_validation->run()==true){
				if($this->PreferenceIncome_model->insertPreferenceIncome($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Konfirgurasi Pendapatan Sukses
							</div> ";

					$this->session->unset_userdata('addpreferenceincome-'.$unique['unique']);
					$this->session->unset_userdata('addpreferenceincomeawal-'.$unique['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('preference-income');
				}else{
					$this->session->set_userdata('addacctsavings',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Konfirgurasi Pendapatan Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('preference-income');
				}
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('preference-income');
			}
		}
		
		public function processEditPreferenceIncome(){
			$auth 					= $this->session->userdata('auth');
			$unique 				= $this->session->userdata('unique');

			foreach($_POST as $key=>$val){
						
				if(is_numeric($key)){
						$data_item[$key] = array(
							'income_id' 			=> $_POST["income_id_".$key],
							'income_group' 			=> $_POST["income_group_".$key],
							'income_percentage' 	=> $_POST["income_percentage_".$key],
							'account_id' 			=> $_POST["account_id_".$key],
						);
					}
			}

			// print_r($data_item);exit;

			foreach ($data_item as $key => $val) {
				$dataitem = array(
					'income_id'						=> $val['income_id'],
					'income_group'					=> $val['income_group'],
					'income_percentage'				=> $val['income_percentage'],
					'account_id'					=> $val['account_id'],

				);

				if($this->PreferenceIncome_model->updatePreferenceIncome($dataitem)){
					$msg = "<div class='alert alert-success alert-dismissable'>  
					<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>	               
							Edit Konfirgurasi Pendapatan Sukses
						</div> ";
					$this->session->set_userdata('message',$msg);
					continue;
				} else {
					$msg = "<div class='alert alert-danger alert-dismissable'>
					<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>	               
						Edit Konfirgurasi Pendapatan Gagal
					</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('preference-income');
					break;
				}
			}

			$this->session->unset_userdata('addpreferenceincome-'.$unique['unique']);
			$this->session->unset_userdata('addpreferenceincomeawal-'.$unique['unique']);
			redirect('preference-income');		
		}
		
		public function deletePreferenceIncome(){
			if($this->PreferenceIncome_model->deletePreferenceIncome($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				// $this->fungsi->set_log($auth['suppliername'],'1005','Application.machine.delete',$auth['suppliername'],'Delete machine');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Hapus Data Perkiraan Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				$unique 				= $this->session->userdata('unique');

				$this->session->unset_userdata('addpreferenceincome-'.$unique['unique']);
				$this->session->unset_userdata('addpreferenceincomeawal-'.$unique['unique']);
				redirect('preference-income');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Hapus Data Perkiraan Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('preference-income');
			}
		}
	}
?>