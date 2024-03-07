<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctRecalculate extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctRecalculate_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['acctaccount']		= create_double($this->AcctRecalculate_model->getAcctAccount(),'account_id','account_code');
			$data['main_view']['corebranch']		= create_double($this->AcctRecalculate_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']			= 'AcctRecalculate/ListAcctRecalculate_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processRecalculate(){
			$data = array (
				'start_date'			=> tgltodb($this->input->post('start_date', true)),
				'end_date'				=> tgltodb($this->input->post('end_date', true)),
				'account_id'			=> $this->input->post('account_id', true),
				'branch_id'				=> $this->input->post('branch_id', true),
			);

			
			$this->form_validation->set_rules('start_date', 'Month Period', 'required');
			$this->form_validation->set_rules('end_date', 'Year Period', 'required');
			if($this->form_validation->run()==true){
				$opening_date 		= $this->AcctRecalculate_model->getOpeningDate($data);

				$opening_balance 	= $this->AcctRecalculate_model->getOpeningBalance($opening_date, $data['account_id'], $data['branch_id']);

				if(empty($opening_date)){
					$opening_date 	= $this->AcctRecalculate_model->getLastDate($data);

					$opening_balance = $this->AcctRecalculate_model->getLastBalance($opening_date, $data['account_id'], $data['branch_id']);
				}


				$accountbalancedetail = $this->AcctRecalculate_model->getAcctAccountBalanceDetail($data);

				if(!empty($accountbalancedetail)){
					foreach ($accountbalancedetail as $key => $val) {
						$last_balance = ($opening_balance + $val['account_in']) - $val['account_out'];

						$newdata = array (
							'account_balance_detail_id'		=> $val['account_balance_detail_id'],
							'opening_balance'				=> $opening_balance,
							'last_balance'					=> $last_balance,
						);

						// print_r($newdata);
						// print_r("<BR>");
						
						// print_r("<BR>");

						$opening_balance = $last_balance;

						if($this->AcctRecalculate_model->updateAcctAccountBalanceDetail($newdata)){
							$msg = "<div class='alert alert-success alert-dismissable'>  
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>	               
											Recalculate Account Balance Successfully
										</div> ";
							$this->session->set_userdata('message',$msg);
							continue;
						} else {
							$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>	               
									Recalculate Account Balance Fail
								</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('AcctRecalculate');
							break;
						}
					}
					// exit;
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>	               
									Recalculate Account Balance Successfully
								</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('AcctRecalculate');
				} else {
					$msg = "<div class='alert alert-danger alert-dismissable'>
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>	               
							Data Kosong
						</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('AcctRecalculate');
				}
				
			}else{
				$msg = validation_errors("<div class='alert alert-danger'>", "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button></div>");
				$this->session->set_userdata('message',$msg);
				redirect('AcctRecalculate');
			}
		}

		public function replace(){
			$data['main_view']['acctaccount']		= create_double($this->AcctRecalculate_model->getAcctAccount(),'account_id','account_code');
			$data['main_view']['corebranch']		= create_double($this->AcctRecalculate_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']			= 'AcctRecalculate/ListAcctReplace_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processReplace(){
			$data = array (
				'start_date'			=> tgltodb($this->input->post('start_date', true)),
				'end_date'				=> tgltodb($this->input->post('end_date', true)),
				'account_id'			=> $this->input->post('account_id', true),
				'branch_id'				=> $this->input->post('branch_id', true),
			);

			
			$this->form_validation->set_rules('start_date', 'Month Period', 'required');
			$this->form_validation->set_rules('end_date', 'Year Period', 'required');
			if($this->form_validation->run()==true){
				$accountbalancedetail = $this->AcctRecalculate_model->getAcctAccountBalanceDetail($data);

				if(!empty($accountbalancedetail)){
					foreach ($accountbalancedetail as $key => $val) {
						$newdata = array (
							'account_balance_detail_id'		=> $val['account_balance_detail_id'],
							'account_in'					=> $val['account_out'],
							'account_out'					=> $val['account_in'],
						);

						// print_r($newdata);

						if($this->AcctRecalculate_model->updateAcctAccountBalanceDetail($newdata)){
							$msg = "<div class='alert alert-success alert-dismissable'>  
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>	               
											Replace Mutation Account Successfully
										</div> ";
							$this->session->set_userdata('message',$msg);
							continue;
						} else {
							$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>	               
									Replace Mutation Account Fail
								</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('AcctRecalculate/replace');
							break;
						}
					}
					// exit;
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>	               
							Replace Mutation Account Successfully
								</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('AcctRecalculate/replace');
				} else {
					$msg = "<div class='alert alert-danger alert-dismissable'>
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>	               
							Data Kosong
						</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('AcctRecalculate/replace');
				}
				
			}else{
				$msg = validation_errors("<div class='alert alert-danger'>", "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button></div>");
				$this->session->set_userdata('message',$msg);
				redirect('AcctRecalculate/replace');
			}
		}

	}
?>