<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	ini_set('memory_limit', '256M');
	
	Class AcctSavingsCloseBook extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctSavingsCloseBook_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['content']			= 'AcctSavingsCloseBook/ListAcctSavingsCloseBook_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processAddAcctSavingsCloseBook(){
			$auth = $this->session->userdata('auth');

			$data = array (
				'last_date'				=> tgltodb($this->input->post('last_date', true)),
			);


			$month 	= date('m', strtotime($data['last_date']));
			$year 	= date('Y', strtotime($data['last_date']));
			$savings_close_book_period = $month.$year;

			$data_log 	= array (
				'savings_close_book_date'		=> $data['last_date'],
				'savings_close_book_period'		=> $savings_close_book_period,
				'branch_id'						=> $auth['branch_id'],
				'created_id'					=> $auth['user_id'],
				'created_on'					=> date('Y-m-d H:i:s'),
			);

			if($this->AcctSavingsCloseBook_model->insertAcctSavingsCloseBook($data_log)){
				if($this->AcctSavingsCloseBook_model->updateAcctSavingsAccount($auth['branch_id'])){
					$acctsavingsaccount = $this->AcctSavingsCloseBook_model->getAcctSavingsAccount($auth['branch_id']);

					// print_r(count($acctsavingsaccount));exit;

					// print_r($acctsavingsaccount);exit;

					foreach ($acctsavingsaccount as $key => $val) {
						$data_detail = array (
							'branch_id'						=> $auth['branch_id'],
							'member_id'						=> $val['member_id'],
							'savings_id'					=> $val['savings_id'],
							'savings_account_id'			=> $val['savings_account_id'],
							'transaction_code'				=> 'Tutup Buku/Saldo Awal',
							'today_transaction_date'		=> $data['last_date'],
							'yesterday_transaction_date'	=> $data['last_date'],
							'opening_balance'				=> $val['savings_account_last_balance'],
							'last_balance'					=> $val['savings_account_last_balance'],
							'operated_name'					=> 'SYSTEM',
							'created_id'					=> $auth['user_id'],
						);

						if($this->AcctSavingsCloseBook_model->insertAcctSavingsAccountDetail($data_detail)){
							$auth = $this->session->userdata('auth');
							// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
							$msg = "<div class='alert alert-success alert-dismissable'>  
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
										Proses Tutup Buku Simpanan Sukses
									</div> ";

							$this->session->set_userdata('message',$msg);
							continue;
						} else {
							$this->session->set_userdata('addacctsavingscashmutation',$data);
							$msg = "<div class='alert alert-danger alert-dismissable'>
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
										Proses Tutup Buku Simpanan Gagal
									</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('savings-close-book');
							break;
						}
					}
				}
				
				redirect('savings-close-book');
			} else {
				$this->session->set_userdata('addacctsavingscashmutation',$data);
				$msg = "<div class='alert alert-danger alert-dismissable'>
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Proses Tutup Buku Simpanan Gagal
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('savings-close-book');			
			}
		}
	}
?>