<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctSavingsAccountBlockir extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctSavingsAccountBlockir_model');
			$this->load->model('AcctSavingsAccount_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$auth = $this->session->userdata('auth');
			$data['main_view']['content']			= 'AcctSavingsAccountBlockir/ListAcctSavingsAccountBlockir_view';
			$this->load->view('MainPage_view',$data);
		}

		public function getAcctSavingsAccountBlockirList(){
			$list = $this->AcctSavingsAccountBlockir_model->get_datatables();

			// print_r($list);
			$blockirtype		= $this->configuration->BlockirType();	
			$blockirstatus		= $this->configuration->BlockirStatus();	
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $savingsblockir) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $savingsblockir->savings_account_no;
	            $row[] = $savingsblockir->member_name;
	            $row[] = $savingsblockir->member_address;
	            $row[] = $blockirtype[$savingsblockir->savings_account_blockir_type];
	            $row[] = $blockirstatus[$savingsblockir->savings_account_blockir_status];
	            $row[] = tgltoview($savingsblockir->savings_account_blockir_date);
	            $row[] = tgltoview($savingsblockir->savings_account_unblockir_date);
	            $row[] = number_format($savingsblockir->savings_account_blockir_amount, 2);
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctSavingsAccountBlockir_model->count_all(),
	                        "recordsFiltered" => $this->AcctSavingsAccountBlockir_model->count_filtered(),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}

		public function getListAcctSavingsAccount(){
			$auth 	= $this->session->userdata('auth');
			$list = $this->AcctSavingsAccount_model->get_datatables($auth['branch_id']);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $savingsaccount) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $savingsaccount->savings_account_no;
	            $row[] = $savingsaccount->member_name;
	            $row[] = $savingsaccount->member_address;
	            $row[] = '<a href="'.base_url().'AcctSavingsAccountBlockir/addAcctSavingsAccountBlockir/'.$savingsaccount->savings_account_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	            $data[] = $row;
	        }

	        // print_r($list);exit;
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctSavingsAccount_model->count_all($auth['branch_id']),
	                        "recordsFiltered" => $this->AcctSavingsAccount_model->count_filtered($auth['branch_id']),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);

		}
		
		public function addAcctSavingsAccountBlockir(){
			$auth 	= $this->session->userdata('auth');
			$savings_account_id = $this->uri->segment(3);
	
			$data['main_view']['acctsavingsaccount']	= $this->AcctSavingsAccountBlockir_model->getAcctSavingsAccount_Detail($savings_account_id);
			$data['main_view']['memberidentity']		= $this->configuration->MemberIdentity();
			$data['main_view']['blockirtype']			= $this->configuration->BlockirType();		
			$data['main_view']['content']				= 'AcctSavingsAccountBlockir/FormAddAcctSavingsAccountBlockir_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processAddAcctSavingsAccountBlockir(){
			$auth 		= $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');

			$data = array(
				'savings_account_id'				=> $this->input->post('savings_account_id', true),
				'member_id'							=> $this->input->post('member_id', true),
				'savings_account_blockir_type'		=> $this->input->post('savings_account_blockir_type', true),
				'savings_account_blockir_date'		=> date('Y-m-d'),
				'savings_account_blockir_amount'	=> $this->input->post('savings_account_blockir_amount', true),
				'savings_account_blockir_status'	=> 1,
			);

			
			$this->form_validation->set_rules('savings_account_blockir_type', 'Sifat Blokis=r', 'required');

			$savings_account_blockir_status = $this->AcctSavingsAccountBlockir_model->getSavingsAccountBlockirStatus($data['savings_account_id']);

			// print_r($data);exit;

			if($this->form_validation->run()==true){
				if($savings_account_blockir_status <> $data['savings_account_blockir_status']){
					if($this->AcctSavingsAccountBlockir_model->insertAcctSavingsAccountBlockir($data)){

						$dataupdate = array (
							'savings_account_id'				=> $this->input->post('savings_account_id', true),
							'savings_account_blockir_type'		=> $this->input->post('savings_account_blockir_type', true),
							'savings_account_blockir_amount'	=> $this->input->post('savings_account_blockir_amount', true),
							'savings_account_blockir_status'	=> 1,
						);

						$this->AcctSavingsAccountBlockir_model->updateAcctSavingsAccount($dataupdate);

						$auth = $this->session->userdata('auth');
						$this->fungsi->set_log($auth['user_id'], $auth['username'],'1003','Application.AcctSavingsAccountBlockir.processAddAcctSavingsAccountBlockir',$auth['user_id'],'Add New Savings Account Blockir');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Blockir Berhasil
								</div> ";

						$unique 	= $this->session->userdata('unique');
						$this->session->unset_userdata('addAcctSavingsAccountBlockir-'.$unique['unique']);
						$this->session->set_userdata('message',$msg);
						redirect('savings-account-blockir');
					}else{
						$this->session->set_userdata('addcoremember',$data);
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Blockir Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('savings-account-blockir');
					}
				}else{
					$this->session->set_userdata('addcoremember',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Rekening Sedang Diblokir
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('savings-account-blockir');
				}
				
			}else{
				$this->session->set_userdata('addcoremember',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('savings-account-blockir');
			}
		}

		public function unBlockirAcctSavingsAccount(){
			$auth = $this->session->userdata('auth');

			$data['main_view']['acctsavingsaccountblockir']	= $this->AcctSavingsAccountBlockir_model->getAcctSavingsAccountBlockir();
			$data['main_view']['blockirtype']				= $this->configuration->BlockirType();
			$data['main_view']['blockirstatus']				= $this->configuration->BlockirStatus();
			$data['main_view']['content']					= 'AcctSavingsAccountBlockir/ListAcctSavingsAccountUnBlockir_view';
			$this->load->view('MainPage_view',$data);
		}

		// public function getAcctSavingsAccountUnBlockirList(){
		// 	$list = $this->AcctSavingsAccountBlockir_model->get_datatables();

		// 	// print_r($list);exit;
		// 	$blockirtype		= $this->configuration->BlockirType();	
		// 	$blockirstatus		= $this->configuration->BlockirStatus();	
	 //        $data = array();
	 //        $no = $_POST['start'];
	 //        foreach ($list as $savingsblockir) {
	 //            $no++;
	 //            $row = array();
	 //            $row[] = $no;
	 //            $row[] = $savingsblockir->savings_account_no;
	 //            $row[] = $savingsblockir->member_name;
	 //            $row[] = $savingsblockir->member_address;
	 //            $row[] = $blockirtype[$savingsblockir->savings_account_blockir_type];
	 //            $row[] = $blockirstatus[$savingsblockir->savings_account_blockir_status];
	 //            $row[] = tgltoview($savingsblockir->savings_account_blockir_date);
	 //            $row[] = tgltoview($savingsblockir->savings_account_unblockir_date);
	 //            $row[] = number_format($savingsblockir->savings_account_blockir_amount, 2);
	 //            if($savingsblockir->savings_account_blockir_status == 1){
	 //            	$row[] = '<a href="'.base_url().'AcctSavingsAccountBlockir/addAcctSavingsAccountUnBlockir/'.$savingsblockir->savings_account_blockir_id.'" class="btn default btn-xs red"><i class="fa fa-trash"></i> UnBlockir</a>';
	 //            } else {
	 //            	$row[] == '';
	 //            }
	            
	 //            $data[] = $row;
	 //        }
	 
	 //        $output = array(
	 //                        "draw" => $_POST['draw'],
	 //                        "recordsTotal" => $this->AcctSavingsAccountBlockir_model->count_all(),
	 //                        "recordsFiltered" => $this->AcctSavingsAccountBlockir_model->count_filtered(),
	 //                        "data" => $data,
	 //                );
	 //        //output to json format
	 //        echo json_encode($output);
		// }

		public function addAcctSavingsAccountUnBlockir(){
			$auth 	= $this->session->userdata('auth');
			$savings_account_blockir_id = $this->uri->segment(3);
	
			$data['main_view']['acctsavingsaccountblocker']	= $this->AcctSavingsAccountBlockir_model->getAcctSavingsAccountBlockir_Detail($savings_account_blockir_id);
			$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();
			$data['main_view']['blockirtype']				= $this->configuration->BlockirType();	
			$data['main_view']['blockirstatus']				= $this->configuration->BlockirStatus();	
			$data['main_view']['content']					= 'AcctSavingsAccountBlockir/FormAddAcctSavingsAccountUnBlockir_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processAddAcctSavingsAccountUnBlockir(){
			$auth 		= $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');

			$data = array(
				'savings_account_blockir_id'		=> $this->input->post('savings_account_blockir_id', true),
				'savings_account_unblockir_date'	=> date('Y-m-d'),
				'savings_account_blockir_status'	=> 0,
			);

			if($this->AcctSavingsAccountBlockir_model->updateAcctSavingsAccountBlockir($data)){

				$dataupdate = array (
					'savings_account_id'				=> $this->input->post('savings_account_id', true),
					'savings_account_blockir_type'		=> 9,
					'savings_account_blockir_amount'	=> 0,
					'savings_account_blockir_status'	=> 0,
				);

				$this->AcctSavingsAccountBlockir_model->updateAcctSavingsAccount($dataupdate);

				$auth = $this->session->userdata('auth');
				$this->fungsi->set_log($auth['user_id'], $auth['username'],'1003','Application.AcctSavingsAccountBlockir.processAddAcctSavingsAccountBlockir',$auth['user_id'],'Add Savings Account UnBlockir');
				$msg = "<div class='alert alert-success alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							UnBlockir Berhasil
						</div> ";

				$unique 	= $this->session->userdata('unique');
				$this->session->unset_userdata('addAcctSavingsAccountBlockir-'.$unique['unique']);
				$this->session->set_userdata('message',$msg);
				redirect('savings-account-blockir/unblockir');
			}else{
				$this->session->set_userdata('addcoremember',$data);
				$msg = "<div class='alert alert-danger alert-dismissable'>
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							UnBlockir Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('savings-account-blockir');
			}
		}

		// public function function_elements_add(){
		// 	$unique 	= $this->session->userdata('unique');
		// 	$name 		= $this->input->post('name',true);
		// 	$value 		= $this->input->post('value',true);
		// 	$sessions	= $this->session->userdata('addAcctSavingsAccountBlockir-'.$unique['unique']);
		// 	$sessions[$name] = $value;
		// 	$this->session->set_userdata('addAcctSavingsAccountBlockir-'.$unique['unique'],$sessions);
		// }

		// public function reset_add(){
		// 	$unique 	= $this->session->userdata('unique');

		// 	$this->session->unset_userdata('addAcctSavingsAccountBlockir-'.$unique['unique']);
		// 	redirect('AcctSavingsAccountBlockir/addAcctSavingsAccountBlockir');
		// }
	}
?>