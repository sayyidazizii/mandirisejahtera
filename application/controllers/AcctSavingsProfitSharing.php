<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	ini_set('memory_limit', '256M');

	Class AcctSavingsProfitSharing extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctSavingsProfitSharing_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['acctsavings']		= create_double($this->AcctSavingsProfitSharing_model->getAcctSavings(), 'savings_id', 'savings_name');
			$data['main_view']['content']			= 'AcctSavingsProfitSharing/ListAcctSavingsProfitSharing_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processAddAcctSavingsProfitSharing(){
			$auth = $this->session->userdata('auth');

			$data = array (
				'savings_daily_average_balance_minimum' 	=> $this->input->post('savings_daily_average_balance_minimum', true),
				'last_date'									=> tgltodb($this->input->post('last_date', true)),
				'savings_id'								=> $this->input->post('savings_id', true),
			);

			$this->form_validation->set_rules('savings_daily_average_balance_minimum', 'SRH Minimal', 'required');
			$this->form_validation->set_rules('savings_id', 'Jenis Simpanan', 'required');

			if($this->form_validation->run()==true){
					$month 							= date('m', strtotime($data['last_date']));
					$year 							= date('Y', strtotime($data['last_date']));
					$savings_profit_sharing_period 	= $month.$year;

					$acctsavingsaccount 			= $this->AcctSavingsProfitSharing_model->getAcctSavingsAccount($data['savings_id'], $auth['branch_id'], $data['savings_daily_average_balance_minimum']);

					$totalsavingsprofitsharing 		= $this->AcctSavingsProfitSharing_model->getSavingsProfitSharingTotalAmount($data['savings_id'], $auth['branch_id'], $data['savings_daily_average_balance_minimum'], $savings_profit_sharing_period);

					$data_log = array(
						'savings_id'							=> $data['savings_id'],
						'branch_id'								=> $auth['branch_id'],
						'savings_profit_sharing_date'			=> $data['last_date'],
						'savings_profit_sharing_period'			=> $savings_profit_sharing_period,
						'savings_profit_sharing_total_amount'	=> $totalsavingsprofitsharing,
						'savings_profit_sharing_total_savings' 	=> count($acctsavingsaccount),
						'created_id'							=> $auth['user_id'],
						'created_on'							=> date('Y-m-d H:i:s'),
					);

					// print_r($data_log);exit;

					$preferencecompany 			= $this->AcctSavingsProfitSharing_model->getPreferenceCompany();

					$acctsavingsprofitsharing 	= $this->AcctSavingsProfitSharing_model->getAcctSavingsProfitSharingLog_Detail($data_log['created_id'], $data_log['savings_id'], $savings_profit_sharing_period);

					if(empty($acctsavingsprofitsharing)){
						if($this->AcctSavingsProfitSharing_model->insertAcctSavingsProfitSharingLog($data_log)){
							$acctsavingsprofitsharing 	= $this->AcctSavingsProfitSharing_model->getAcctSavingsProfitSharingLog_Detail($data_log['created_id'], $data_log['savings_id'], $savings_profit_sharing_period);

							//-------------------------------------Jurnal--------------------------------------------------------//
						
							$transaction_module_code 	= "BS";

							$transaction_module_id 		= $this->AcctSavingsProfitSharing_model->getTransactionModuleID($transaction_module_code);

							
								
							$journal_voucher_period 	= date("Ym", strtotime($acctsavingsprofitsharing['savings_profit_sharing_date']));
							
							$data_journal 				= array(
								'branch_id'						=> $auth['branch_id'],
								'journal_voucher_period' 		=> $journal_voucher_period,
								'journal_voucher_date'			=> $data['last_date'],
								'journal_voucher_title'			=> 'BAGI HASIL SIMPANAN '.$acctsavingsprofitsharing['savings_name'],
								'journal_voucher_description'	=> 'BAGI HASIL SIMPANAN '.$acctsavingsprofitsharing['savings_name'],
								'transaction_module_id'			=> $transaction_module_id,
								'transaction_module_code'		=> $transaction_module_code,
								'transaction_journal_id' 		=> $acctsavingsprofitsharing['savings_profit_sharing_log_id'],
								'transaction_journal_no' 		=> $acctsavingsprofitsharing['savings_profit_sharing_period'],
								'created_id' 					=> $auth['user_id'],
								'created_on' 					=> date('Y-m-d H:i:s'),
							);
							
							$this->AcctSavingsProfitSharing_model->insertAcctJournalVoucher($data_journal);

							$journal_voucher_id 			= $this->AcctSavingsProfitSharing_model->getJournalVoucherID($data_journal['created_id']);

							$account_basil_id 				= $this->AcctSavingsProfitSharing_model->getAccountBasilID($data_log['savings_id']);

							$account_id_default_status 		= $this->AcctSavingsProfitSharing_model->getAccountIDDefaultStatus($account_basil_id);

							$data_debet = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_basil_id,
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $totalsavingsprofitsharing,
								'journal_voucher_debit_amount'	=> $totalsavingsprofitsharing,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
							);

							$this->AcctSavingsProfitSharing_model->insertAcctJournalVoucherItem($data_debet);

							$account_id 					= $this->AcctSavingsProfitSharing_model->getAccountID($data_log['savings_id']);

							$account_id_default_status 		= $this->AcctSavingsProfitSharing_model->getAccountIDDefaultStatus($account_id);

							$data_credit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_id,
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $totalsavingsprofitsharing,
								'journal_voucher_credit_amount'	=> $totalsavingsprofitsharing,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
							);

							$this->AcctSavingsProfitSharing_model->insertAcctJournalVoucherItem($data_credit);

							foreach ($acctsavingsaccount as $k => $v) {
								// if($v['savings_account_daily_average_balance'] >= $data['savings_daily_average_balance_minimum']){
									$savings_interest_rate 				= $this->AcctSavingsProfitSharing_model->getSavingsInterest($data['savings_id']);

									$savings_profit_sharing_amount 	= ($v['savings_account_daily_average_balance'] * $savings_interest_rate)/100;

									$savings_account_last_balance 	= $v['savings_account_last_balance'] + $savings_profit_sharing_amount;

									$dataacctsavingsprofitsharing 	= array (
										'savings_profit_sharing_log_id'				=> $acctsavingsprofitsharing['savings_profit_sharing_log_id'],
										'savings_account_id'						=> $v['savings_account_id'],
										'branch_id'									=> $auth['branch_id'],
										'savings_id'								=> $v['savings_id'],
										'member_id'									=> $v['member_id'],
										'savings_profit_sharing_date'				=> $data['last_date'],
										'savings_interest_rate'						=> $savings_interest_rate,
										'savings_daily_average_balance_minimum'		=> $data['savings_daily_average_balance_minimum'],
										'savings_daily_average_balance'				=> $v['savings_account_daily_average_balance'],
										'savings_profit_sharing_amount'				=> $savings_profit_sharing_amount,
										'savings_profit_sharing_period'				=> $savings_profit_sharing_period,
										'savings_account_last_balance'				=> $savings_account_last_balance,
										'savings_profit_sharing_token'				=> $savings_profit_sharing_period.$v['savings_account_id'],
										'operated_name'								=> 'SYSTEM',
										'created_id'								=> $auth['user_id'],
										'created_on'								=> date('Y-m-d H:i:s'),
									);

									$data_transfermutation 			= array (
										'branch_id'									=> $auth['branch_id'],
										'savings_transfer_mutation_date'			=> $data['last_date'],
										'savings_transfer_mutation_amount'			=> $savings_profit_sharing_amount,
										'operated_name'								=> 'SYSTEM',
										'created_id'								=> $auth['user_id'],
										'created_on'								=> date('Y-m-d H:i:s'),
									);

									$savings_account_id = $this->AcctSavingsProfitSharing_model->getSavingsAccountID($dataacctsavingsprofitsharing);

									if($savings_account_id->num_rows() == 0){
										if($this->AcctSavingsProfitSharing_model->insertAcctSavingsProfitSharing($dataacctsavingsprofitsharing)){
											if($this->AcctSavingsProfitSharing_model->updateAcctSavingsProfitSharingLog($dataacctsavingsprofitsharing)){
												if($this->AcctSavingsProfitSharing_model->insertAcctSavingsTransferMutation($data_transfermutation)){

													$savings_transfer_mutation_id 	= $this->AcctSavingsProfitSharing_model->getSavingsTranferMutationID($data_transfermutation['created_id']);

													$data_transfermutationto 		= array (
														'savings_transfer_mutation_id'			=> $savings_transfer_mutation_id,
														'savings_account_id'					=> $v['savings_account_id'],
														'savings_id'							=> $v['savings_id'],
														'branch_id'								=> $auth['branch_id'],
														'member_id'								=> $v['member_id'],
														'mutation_id'							=> $preferencecompany['savings_profit_sharing_id'],
														'savings_account_opening_balance'		=> $v['savings_account_last_balance'],
														'savings_transfer_mutation_to_amount'	=> $savings_profit_sharing_amount,
														'savings_account_last_balance'			=> $savings_account_last_balance,	
													);

													$this->AcctSavingsProfitSharing_model->insertAcctSavingsTransferMutationTo($data_transfermutationto);
												}
											}
										}
									}								
								}
							}

						$auth = $this->session->userdata('auth');
						// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Perhitungan Basil Simpanan Sukses
								</div> ";

						$this->session->set_userdata('message',$msg);
						redirect('AcctSavingsProfitSharing');

					} else if($acctsavingsprofitsharing['savings_profit_sharing_total_savings'] <> $acctsavingsprofitsharing['savings_profit_sharing_total_savings_process']){
						foreach ($acctsavingsaccount as $k => $v) {
								$savings_interest_rate 				= $this->AcctSavingsProfitSharing_model->getSavingsInterest($data['savings_id']);

								$savings_profit_sharing_amount 	= ($v['savings_account_daily_average_balance'] * $savings_interest_rate)/100;

								$savings_account_last_balance 	= $v['savings_account_last_balance'] + $savings_profit_sharing_amount;

								$dataacctsavingsprofitsharing = array (
									'savings_profit_sharing_log_id'				=> $acctsavingsprofitsharing['savings_profit_sharing_log_id'],
									'savings_account_id'						=> $v['savings_account_id'],
									'branch_id'									=> $auth['branch_id'],
									'savings_id'								=> $v['savings_id'],
									'member_id'									=> $v['member_id'],
									'savings_profit_sharing_date'				=> $data['last_date'],
									'savings_interest_rate'							=> $savings_interest_rate,
									'savings_daily_average_balance_minimum'		=> $data['savings_daily_average_balance_minimum'],
									'savings_daily_average_balance'				=> $v['savings_account_daily_average_balance'],
									'savings_profit_sharing_amount'				=> $savings_profit_sharing_amount,
									'savings_profit_sharing_period'				=> $savings_profit_sharing_period,
									'savings_account_last_balance'				=> $savings_account_last_balance,
									'savings_profit_sharing_token'				=> $savings_profit_sharing_period.$v['savings_account_id'],
									'operated_name'								=> 'SYSTEM',
									'created_id'								=> $auth['user_id'],
									'created_on'								=> date('Y-m-d H:i:s'),
								);

								$data_transfermutation 			= array (
									'branch_id'									=> $auth['branch_id'],
									'savings_transfer_mutation_date'			=> $data['last_date'],
									'savings_transfer_mutation_amount'			=> $savings_profit_sharing_amount,
									'operated_name'								=> 'SYSTEM',
									'created_id'								=> $auth['user_id'],
									'created_on'								=> date('Y-m-d H:i:s'),
								);

								$savings_account_id = $this->AcctSavingsProfitSharing_model->getSavingsAccountID($dataacctsavingsprofitsharing);
									if($savings_account_id->num_rows() == 0){
										if($this->AcctSavingsProfitSharing_model->insertAcctSavingsProfitSharing($dataacctsavingsprofitsharing)){
											if($this->AcctSavingsProfitSharing_model->updateAcctSavingsProfitSharingLog($dataacctsavingsprofitsharing)){
												if($this->AcctSavingsProfitSharing_model->insertAcctSavingsTransferMutation($data_transfermutation)){

													$savings_transfer_mutation_id 	= $this->AcctSavingsProfitSharing_model->getSavingsTranferMutationID($data_transfermutation['created_id']);

													$data_transfermutationto 		= array (
														'savings_transfer_mutation_id'			=> $savings_transfer_mutation_id,
														'savings_account_id'					=> $v['savings_account_id'],
														'savings_id'							=> $v['savings_id'],
														'branch_id'								=> $auth['branch_id'],
														'member_id'								=> $v['member_id'],
														'mutation_id'							=> $preferencecompany['savings_profit_sharing_id'],
														'savings_account_opening_balance'		=> $v['savings_account_last_balance'],
														'savings_transfer_mutation_to_amount'	=> $savings_profit_sharing_amount,
														'savings_account_last_balance'			=> $savings_account_last_balance,	
													);

													$this->AcctSavingsProfitSharing_model->insertAcctSavingsTransferMutationTo($data_transfermutationto);
												}
											}
										}
									}	
						}

						$auth = $this->session->userdata('auth');
						// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Perhitungan Basil Simpanan Sukses
								</div> ";

						$this->session->set_userdata('message',$msg);
						redirect('AcctSavingsProfitSharing');
					} else if($acctsavingsprofitsharing['savings_profit_sharing_total_savings'] == $acctsavingsprofitsharing['savings_profit_sharing_total_savings_process']){

						$auth = $this->session->userdata('auth');
						// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
						$msg = "<div class='alert alert-danger alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Perhitungan Basil Sudah Selesai DiProses
								</div> ";

						$this->session->set_userdata('message',$msg);
						redirect('AcctSavingsProfitSharing');
					}
			} else {
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('AcctSavingsProfitSharing');
			}			
		}
	}
?>