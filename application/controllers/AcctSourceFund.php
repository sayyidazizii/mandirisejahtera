<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctSourceFund extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctSourceFund_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['acctsourcefund']		= $this->AcctSourceFund_model->getDataAcctSourceFund();
			$data['main_view']['content']			= 'AcctSourceFund/ListAcctSourceFund_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function addAcctSourceFund(){
			$data['main_view']['content']			= 'AcctSourceFund/FormAddAcctSourceFund_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processAddAcctSourceFund(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'source_fund_code'				=> $this->input->post('source_fund_code', true),
				'source_fund_name'				=> $this->input->post('source_fund_name', true),
			);

			// print_r($data);exit;
			
			$this->form_validation->set_rules('source_fund_name', 'Nama', 'required');
			$this->form_validation->set_rules('source_fund_code', 'Kode', 'required');

			
			if($this->form_validation->run()==true){
				// print_r($data);
				if($this->AcctSourceFund_model->insertAcctSourceFund($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Sumber Dana Sukses
							</div> ";
					$this->session->unset_userdata('addacctsourcefund');
					$this->session->set_userdata('message',$msg);
					redirect('source-fund/add');
				}else{
					$this->session->set_userdata('addacctsourcefund',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Sumber Dana Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('source-fund/add');
				}
			}else{
				$this->session->set_userdata('addacctsourcefund',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('source-fund/add');
			}
		}
		
		public function editAcctSourceFund(){
			$this->uri->segment(3);

			$data['main_view']['acctsourcefund']		= $this->AcctSourceFund_model->getAcctSourceFund_Detail($this->uri->segment(3));
			$data['main_view']['content']			= 'AcctSourceFund/FormEditAcctSourceFund_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processEditAcctSourceFund(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'source_fund_id'				=> $this->input->post('source_fund_id', true),
				'source_fund_name'				=> $this->input->post('source_fund_name', true),
				'source_fund_code'				=> $this->input->post('source_fund_code', true),
			);
			
			$this->form_validation->set_rules('source_fund_name', 'Nama', 'required');
			$this->form_validation->set_rules('source_fund_code', 'Kode', 'required');
			
			if($this->form_validation->run()==true){
				if($this->AcctSourceFund_model->updateAcctSourceFund($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processMachinesupplier',$auth['username'],'edit machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Sumber Dana Sukses
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('source-fund/edit/'.$data['source_fund_id']);
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Sumber Dana Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('source-fund/edit/'.$data['source_fund_id']);
				}
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('source-fund/edit/'.$data['source_fund_id']);
			}				
		}
		
		public function deleteAcctSourceFund(){
			if($this->AcctSourceFund_model->deleteAcctSourceFund($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				// $this->fungsi->set_log($auth['suppliername'],'1005','Application.machine.delete',$auth['suppliername'],'Delete machine');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Hapus Data Sumber Dana Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('source-fund');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Hapus Data Sumber Dana Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('source-fund');
			}
		}
	}
?>