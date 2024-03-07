<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	ini_set('memory_limit', '256M');

	Class AcctDailyAverageBalanceCalculate extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctDailyAverageBalanceCalculate_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['acctsavings']		= create_double($this->AcctDailyAverageBalanceCalculate_model->getAcctSavings(), 'savings_id', 'savings_name');
			$data['main_view']['content']			= 'AcctDailyAverageBalanceCalculate/ListAcctDailyAverageBalanceCalculate_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processAddAcctDailyAverageBalanceCalculate(){
			$auth = $this->session->userdata('auth');

			$acctsavings = $this->AcctDailyAverageBalanceCalculate_model->getAcctSavings();



			$data = array(
				'daily_average_balance_calculate_date'		=> tgltodb($this->input->post('daily_average_balance_calculate_date', true)),
				'savings_id'								=> $this->input->post('savings_id', true),
			);

			$this->form_validation->set_rules('savings_id', 'Jenis Simpanan', 'required');

			if($this->form_validation->run()==true){
				$acctsavingsaccount = $this->AcctDailyAverageBalanceCalculate_model->getAcctSavingsAccount($data['savings_id'], $auth['branch_id']);

				if(!empty($acctsavingsaccount)){
					foreach ($acctsavingsaccount as $key => $val) {
						if($val['savings_account_daily_average_balance'] == 0){
							$yesterday_transaction_date = $this->AcctDailyAverageBalanceCalculate_model->getYesterdayTransactionDate($val['savings_account_id']);

							$last_balance_SRH = $this->AcctDailyAverageBalanceCalculate_model->getLastBalanceSRH($val['savings_account_id']);

							if(empty($last_balance_SRH)){
								$last_balance_SRH = 0;
							}

							$last_date = date('t', strtotime($data['daily_average_balance_calculate_date']));

							$date1 = date_create($data['daily_average_balance_calculate_date']);
							$date2 = date_create($yesterday_transaction_date);

							$range_date = date_diff($date1, $date2)->format('%d');

							if($range_date == 0){
								$range_date = 1;
							}

							$month 	= date('m', strtotime($data['daily_average_balance_calculate_date']));
							$year 	= date('Y', strtotime($data['daily_average_balance_calculate_date']));



							$daily_average_balance = ($last_balance_SRH * $range_date) / $last_date;
							// $daily_average_balance = ($val['savings_account_last_balance'] * 80) / 100;

							$dataacctsavingsaccountdetail = array (
								'savings_account_id'				=> $val['savings_account_id'],
								'branch_id'							=> $auth['branch_id'],
								'savings_id'						=> $val['savings_id'],
								'member_id'							=> $val['member_id'],
								'today_transaction_date'			=> $data['daily_average_balance_calculate_date'],
								'yesterday_transaction_date'		=> $yesterday_transaction_date,
								'transaction_code'					=> 'Penutupan Akhir Bulan',
								'opening_balance'					=> $last_balance_SRH,
								'last_balance'						=> $last_balance_SRH,
								'daily_average_balance'				=> $daily_average_balance,
								'operated_name'						=> 'SYSTEM',
							);

							// print_r($daily_average_balance);exit;

							$this->AcctDailyAverageBalanceCalculate_model->insertAcctSavingsAccountDetail($dataacctsavingsaccountdetail);

							$daily_average_balance_total = $this->AcctDailyAverageBalanceCalculate_model->getDailyAverageBalanceTotal($val['savings_account_id'], $month, $year);

							$data_savings = array (
								'savings_account_id'					=> $val['savings_account_id'],
								'savings_account_daily_average_balance' => $daily_average_balance_total,
							);

							$this->AcctDailyAverageBalanceCalculate_model->updateAcctSavingsAccount($data_savings);
						}
						
					}
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Hitung SRH Sukses
							</div> ";
					$sesi = $this->session->userdata('unique');
					$this->session->unset_userdata('addacctsavingscashmutation-'.$sesi['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('AcctDailyAverageBalanceCalculate');
				} else {
					$this->session->set_userdata('addacctsavingscashmutation',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Rekening Kosong
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('AcctDailyAverageBalanceCalculate');
				}
			} else {
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('AcctDailyAverageBalanceCalculate');
			}			
		}
	}
?>