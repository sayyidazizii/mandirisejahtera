<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctCreditsPaymentSuspend extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('MainPage_model');
			$this->load->model('AcctCreditsPaymentSuspend_model');
			$this->load->model('AcctCreditAccount_model');
			$this->load->model('AcctSavingsAccount_model');
			$this->load->model('AcctSavingsCashMutation_model');
			$this->load->model('CoreMember_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi'); 
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}

		public function index(){
			$auth 	= $this->session->userdata('auth');
			$unique = $this->session->userdata('unique');

			$data['main_view']['acctcredits']	= create_double($this->AcctCreditAccount_model->getAcctCredits(),'credits_id', 'credits_name');
			$data['main_view']['corebranch']	= create_double($this->AcctCreditAccount_model->getCoreBranch(),'branch_id', 'branch_name');
			$data['main_view']['content']		= 'AcctCreditsPaymentSuspend/ListAcctCreditsPaymentSuspend_view';
			$this->load->view('MainPage_view', $data);
		}

		public function filter(){
			$data = array (  
				'start_date'	=> tgltodb($this->input->post('start_date', true)),
				'end_date'		=> tgltodb($this->input->post('end_date', true)),
				'credits_id'	=> $this->input->post('credits_id', true),
				'branch_id'		=> $this->input->post('branch_id', true),
			);

			$this->session->set_userdata('filter-AcctCreditsPaymentSuspend', $data);
			redirect('AcctCreditsPaymentSuspend');
		}
		public function reset(){
			$this->session->unset_userdata('filter-AcctCreditsPaymentSuspend');
			redirect('AcctCreditsPaymentSuspend');
		}

		public function getAcctCreditsPaymentSuspend(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-AcctCreditsPaymentSuspend');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['credits_id']		='';
				if($auth['branch_status'] == 1){
					$sesi['branch_id']	= '';
				}
				if($auth['branch_status'] == 0){
					$sesi['branch_id']	= $auth['branch_id'];
				}
			} else {
				if($auth['branch_status'] == 1){
					$sesi['branch_id']	= '';
				}
				if($auth['branch_status'] == 0){
					$sesi['branch_id']	= $auth['branch_id'];
				}

				/*print_r(" Sesi");*/
			}

			$creditspaymentperiod = $this->configuration->CreditsPaymentPeriod();

			$list = $this->AcctCreditsPaymentSuspend_model->get_datatables($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $cashpayment) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $cashpayment->credits_account_serial;
	            $row[] = $cashpayment->member_name;
	            $row[] = $cashpayment->credits_name;
	            $row[] = $creditspaymentperiod[$cashpayment->credits_payment_period];
	            $row[] = $cashpayment->credits_grace_period;
	            $row[] = tgltoview($cashpayment->credits_payment_date_old);
	            $row[] = tgltoview($cashpayment->credits_payment_date_new);
	            $data[] = $row;
	        }



	        // print_r($list);exit;
	 
	        $output = array(
	                        "draw" 				=> $_POST['draw'],
	                        "recordsTotal" 		=> $this->AcctCreditsPaymentSuspend_model->count_all($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']),
	                        "recordsFiltered" 	=> $this->AcctCreditsPaymentSuspend_model->count_filtered($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']),
	                        "data" 				=> $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}

		public function addAcctCreditsPaymentSuspend(){	
			$credits_account_id 	= $this->uri->segment(3);

			$data['main_view']['creditspaymentperiod']	= $this->configuration->CreditsPaymentPeriod();
			$data['main_view']['accountcredit']			= $this->AcctCreditAccount_model->getDetailByID($credits_account_id);
			$data['main_view']['content']				= 'AcctCreditsPaymentSuspend/FormAddAcctCreditsPaymentSuspend_view';
			$this->load->view('MainPage_view',$data);
		} 

		public function akadlisttunai(){
			$auth 	= $this->session->userdata('auth');
			$list 	= $this->AcctCreditAccount_model->get_datatables($auth['branch_id']);
	        $data 	= array();
	        $no 	= $_POST['start'];
	        foreach ($list as $customers) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $customers->credits_account_serial;
	            $row[] = $customers->member_name;
	            $row[] = $customers->member_no;
	            $row[] = tgltoview($customers->credits_account_date);
	            $row[] = tgltoview($customers->credits_account_due_date);
	             $row[] = '<a href="'.base_url().'credits-payment-suspend/add/'.$customers->credits_account_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	    
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" 				=> $_POST['draw'],
	                        "recordsTotal" 		=> $this->AcctCreditAccount_model->count_all($auth['branch_id']),
	                        "recordsFiltered" 	=> $this->AcctCreditAccount_model->count_filtered($auth['branch_id']),
	                        "data" 				=> $data,
	                );
	        echo json_encode($output);
			
		}

		public function processAddAcctCreditsPaymentSuspend(){
			$auth 									= $this->session->userdata('auth');

			$data = array(
				'branch_id'							=> $auth['branch_id'],
				'member_id'							=> $this->input->post('member_id', true),
				'credits_id'						=> $this->input->post('credits_id', true),
				'credits_account_id'				=> $this->input->post('credits_account_id', true),
				'credits_payment_suspend_date'		=> date('Y-m-d'),
				'credits_payment_period'			=> $this->input->post('credits_payment_period', true),
				'credits_grace_period'				=> $this->input->post('credits_grace_period', true),
				'credits_payment_date_old'			=> tgltodb($this->input->post('credits_payment_date_old', true)),
				'credits_payment_date_new'			=> tgltodb($this->input->post('credits_payment_date_new', true)),
				'created_id'						=> $auth['user_id'],
				'created_on'						=> date('Y-m-d H:i:s'),
			);

		
			$this->form_validation->set_rules('credits_payment_period', 'Periode Penundaan Angsuran', 'required');

			if($this->form_validation->run()==true){
				if($this->AcctCreditsPaymentSuspend_model->insert($data)){
					$updatedata = array(
						"credits_account_id" 					=> $data['credits_account_id'],
						"credits_account_payment_date"			=> $data['credits_payment_date_new'],
					);
					// print_r($updatedata);exit;

					$this->AcctCreditsPaymentSuspend_model->updateAcctCreditsAccount($updatedata);

					


					$auth = $this->session->userdata('auth');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Pembayaran Pinjaman Sukses
							</div> ";
					$sesi = $this->session->userdata('unique');
					$this->session->unset_userdata('addAcctCreditsPaymentSuspend-'.$sesi['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('AcctCreditsPaymentSuspend');
				}else{
					$this->session->set_userdata('addAcctCreditsPaymentSuspend-'.$unique['unique'],$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Pembayaran Pinjaman Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('credits-payment-suspend/add/');
				}
				
			}else{
				$this->session->set_userdata('addAcctCreditsPaymentSuspend-'.$unique['unique'],$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('credits-payment-suspend/add/');
			}
		}
		

	}
?>