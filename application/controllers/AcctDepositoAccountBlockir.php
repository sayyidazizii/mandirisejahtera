<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctDepositoAccountBlockir extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctDepositoAccountBlockir_model');
			$this->load->model('AcctDepositoAccount_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$auth = $this->session->userdata('auth');
			$data['main_view']['content']			= 'AcctDepositoAccountBlockir/ListAcctDepositoAccountBlockir_view';
			$this->load->view('MainPage_view',$data);
		}

		public function getAcctDepositoAccountBlockirList(){
			$list = $this->AcctDepositoAccountBlockir_model->get_datatables();

			// print_r($list);
			$blockirtype		= $this->configuration->BlockirType();	
			$blockirstatus		= $this->configuration->BlockirStatus();	
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $depositoblockir) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $depositoblockir->deposito_account_no;
	            $row[] = $depositoblockir->member_name;
	            $row[] = $depositoblockir->member_address;
	            $row[] = $blockirtype[$depositoblockir->deposito_account_blockir_type];
	            $row[] = $blockirstatus[$depositoblockir->deposito_account_blockir_status];
	            $row[] = tgltoview($depositoblockir->deposito_account_blockir_date);
	            $row[] = tgltoview($depositoblockir->deposito_account_unblockir_date);
	            $row[] = number_format($depositoblockir->deposito_account_blockir_amount, 2);
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctDepositoAccountBlockir_model->count_all(),
	                        "recordsFiltered" => $this->AcctDepositoAccountBlockir_model->count_filtered(),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}

		public function getListAcctDepositoAccount(){
			$list = $this->AcctDepositoAccount_model->get_datatables();
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $depositoaccount) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $depositoaccount->deposito_account_no;
	            $row[] = $depositoaccount->member_name;
	            $row[] = $depositoaccount->member_address;
	            $row[] = '<a href="'.base_url().'AcctDepositoAccountBlockir/addAcctDepositoAccountBlockir/'.$depositoaccount->deposito_account_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	            $data[] = $row;
	        }

	        // print_r($list);exit;
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctDepositoAccount_model->count_all(),
	                        "recordsFiltered" => $this->AcctDepositoAccount_model->count_filtered(),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);

		}
		
		public function addAcctDepositoAccountBlockir(){
			$auth 	= $this->session->userdata('auth');
			$deposito_account_id = $this->uri->segment(3);
	
			$data['main_view']['acctdepositoaccount']	= $this->AcctDepositoAccountBlockir_model->getAcctDepositoAccount_Detail($deposito_account_id);
			$data['main_view']['memberidentity']		= $this->configuration->MemberIdentity();
			$data['main_view']['blockirtype']			= $this->configuration->BlockirType();		
			$data['main_view']['content']				= 'AcctDepositoAccountBlockir/FormAddAcctDepositoAccountBlockir_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processAddAcctDepositoAccountBlockir(){
			$auth 		= $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');

			$data = array(
				'deposito_account_id'				=> $this->input->post('deposito_account_id', true),
				'member_id'							=> $this->input->post('member_id', true),
				'deposito_account_blockir_type'		=> $this->input->post('deposito_account_blockir_type', true),
				'deposito_account_blockir_date'		=> date('Y-m-d'),
				'deposito_account_blockir_amount'	=> $this->input->post('deposito_account_blockir_amount', true),
				'deposito_account_blockir_status'	=> 1,
			);

			
			$this->form_validation->set_rules('deposito_account_blockir_type', 'Sifat Blokis=r', 'required');

			$deposito_account_blockir_status = $this->AcctDepositoAccountBlockir_model->getDepositoAccountBlockirStatus($data['deposito_account_id']);

			// print_r($data);exit;

			if($this->form_validation->run()==true){
				if($deposito_account_blockir_status <> $data['deposito_account_blockir_status']){
					if($this->AcctDepositoAccountBlockir_model->insertAcctDepositoAccountBlockir($data)){

						$dataupdate = array (
							'deposito_account_id'				=> $this->input->post('deposito_account_id', true),
							'deposito_account_blockir_type'		=> $this->input->post('deposito_account_blockir_type', true),
							'deposito_account_blockir_amount'	=> $this->input->post('deposito_account_blockir_amount', true),
							'deposito_account_blockir_status'	=> 1,
						);

						$this->AcctDepositoAccountBlockir_model->updateAcctDepositoAccount($dataupdate);

						$auth = $this->session->userdata('auth');
						$this->fungsi->set_log($auth['user_id'], $auth['username'],'1003','Application.AcctDepositoAccountBlockir.processAddAcctDepositoAccountBlockir',$auth['user_id'],'Add New Simpanan Berjangka Account Blockir');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Blockir Berhasil
								</div> ";

						$unique 	= $this->session->userdata('unique');
						$this->session->unset_userdata('addAcctDepositoAccountBlockir-'.$unique['unique']);
						$this->session->set_userdata('message',$msg);
						redirect('deposito-account-blockir');
					}else{
						$this->session->set_userdata('addcoremember',$data);
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Blockir Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('deposito-account-blockir');
					}
				}else{
					$this->session->set_userdata('addcoremember',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Rekening Sedang Diblokir
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('deposito-account-blockir');
				}
				
			}else{
				$this->session->set_userdata('addcoremember',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('deposito-account-blockir');
			}
		}

		public function unBlockirAcctDepositoAccount(){
			$auth = $this->session->userdata('auth');

			$data['main_view']['acctdepositoaccountblockir']	= $this->AcctDepositoAccountBlockir_model->getAcctDepositoAccountBlockir();
			$data['main_view']['blockirtype']				= $this->configuration->BlockirType();
			$data['main_view']['blockirstatus']				= $this->configuration->BlockirStatus();
			$data['main_view']['content']					= 'AcctDepositoAccountBlockir/ListAcctDepositoAccountUnBlockir_view';
			$this->load->view('MainPage_view',$data);
		}

		// public function getAcctDepositoAccountUnBlockirList(){
		// 	$list = $this->AcctDepositoAccountBlockir_model->get_datatables();

		// 	// print_r($list);exit;
		// 	$blockirtype		= $this->configuration->BlockirType();	
		// 	$blockirstatus		= $this->configuration->BlockirStatus();	
	 //        $data = array();
	 //        $no = $_POST['start'];
	 //        foreach ($list as $depositoblockir) {
	 //            $no++;
	 //            $row = array();
	 //            $row[] = $no;
	 //            $row[] = $depositoblockir->deposito_account_no;
	 //            $row[] = $depositoblockir->member_name;
	 //            $row[] = $depositoblockir->member_address;
	 //            $row[] = $blockirtype[$depositoblockir->deposito_account_blockir_type];
	 //            $row[] = $blockirstatus[$depositoblockir->deposito_account_blockir_status];
	 //            $row[] = tgltoview($depositoblockir->deposito_account_blockir_date);
	 //            $row[] = tgltoview($depositoblockir->deposito_account_unblockir_date);
	 //            $row[] = number_format($depositoblockir->deposito_account_blockir_amount, 2);
	 //            if($depositoblockir->deposito_account_blockir_status == 1){
	 //            	$row[] = '<a href="'.base_url().'AcctDepositoAccountBlockir/addAcctDepositoAccountUnBlockir/'.$depositoblockir->deposito_account_blockir_id.'" class="btn default btn-xs red"><i class="fa fa-trash"></i> UnBlockir</a>';
	 //            } else {
	 //            	$row[] == '';
	 //            }
	            
	 //            $data[] = $row;
	 //        }
	 
	 //        $output = array(
	 //                        "draw" => $_POST['draw'],
	 //                        "recordsTotal" => $this->AcctDepositoAccountBlockir_model->count_all(),
	 //                        "recordsFiltered" => $this->AcctDepositoAccountBlockir_model->count_filtered(),
	 //                        "data" => $data,
	 //                );
	 //        //output to json format
	 //        echo json_encode($output);
		// }

		public function addAcctDepositoAccountUnBlockir(){
			$auth 	= $this->session->userdata('auth');
			$deposito_account_blockir_id = $this->uri->segment(3);
	
			$data['main_view']['acctdepositoaccountblocker']	= $this->AcctDepositoAccountBlockir_model->getAcctDepositoAccountBlockir_Detail($deposito_account_blockir_id);
			$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();
			$data['main_view']['blockirtype']				= $this->configuration->BlockirType();	
			$data['main_view']['blockirstatus']				= $this->configuration->BlockirStatus();	
			$data['main_view']['content']					= 'AcctDepositoAccountBlockir/FormAddAcctDepositoAccountUnBlockir_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processAddAcctDepositoAccountUnBlockir(){
			$auth 		= $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');

			$data = array(
				'deposito_account_blockir_id'		=> $this->input->post('deposito_account_blockir_id', true),
				'deposito_account_unblockir_date'	=> date('Y-m-d'),
				'deposito_account_blockir_status'	=> 0,
			);

			if($this->AcctDepositoAccountBlockir_model->updateAcctDepositoAccountBlockir($data)){

				$dataupdate = array (
					'deposito_account_id'				=> $this->input->post('deposito_account_id', true),
					'deposito_account_blockir_type'		=> 9,
					'deposito_account_blockir_amount'	=> 0,
					'deposito_account_blockir_status'	=> 0,
				);

				$this->AcctDepositoAccountBlockir_model->updateAcctDepositoAccount($dataupdate);

				$auth = $this->session->userdata('auth');
				$this->fungsi->set_log($auth['user_id'], $auth['username'],'1003','Application.AcctDepositoAccountBlockir.processAddAcctDepositoAccountBlockir',$auth['user_id'],'Add Simpanan Berjangka Account UnBlockir');
				$msg = "<div class='alert alert-success alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							UnBlockir Berhasil
						</div> ";

				$unique 	= $this->session->userdata('unique');
				$this->session->unset_userdata('addAcctDepositoAccountBlockir-'.$unique['unique']);
				$this->session->set_userdata('message',$msg);
				redirect('deposito-account-blockir/unblockir');
			}else{
				$this->session->set_userdata('addcoremember',$data);
				$msg = "<div class='alert alert-danger alert-dismissable'>
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							UnBlockir Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('deposito-account-blockir');
			}
		}

		// public function function_elements_add(){
		// 	$unique 	= $this->session->userdata('unique');
		// 	$name 		= $this->input->post('name',true);
		// 	$value 		= $this->input->post('value',true);
		// 	$sessions	= $this->session->userdata('addAcctDepositoAccountBlockir-'.$unique['unique']);
		// 	$sessions[$name] = $value;
		// 	$this->session->set_userdata('addAcctDepositoAccountBlockir-'.$unique['unique'],$sessions);
		// }

		// public function reset_add(){
		// 	$unique 	= $this->session->userdata('unique');

		// 	$this->session->unset_userdata('addAcctDepositoAccountBlockir-'.$unique['unique']);
		// 	redirect('deposito-account-blockir/add');
		// }
	}
?>