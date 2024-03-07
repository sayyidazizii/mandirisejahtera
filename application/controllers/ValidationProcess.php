<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class ValidationProcess extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('MainPage_model');
			$this->load->model('ValidationProcess_model');
			$this->load->model('AcctSavingsAccount_model');
			$this->load->model('AcctCreditAccount_model');
			$this->load->model('AcctCashPayment_model');
			$this->load->model('AcctSavingsCashMutation_model');
			$this->load->model('SystemEndOfDays_model');
			$this->load->model('CoreMemberTransferMutation_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->library('fungsi');
			/*$this->load->library('session');*/
			$this->load->library('configuration');
			$this->load->database('default');

		}
		
		public function index(){
			$posisition = str_replace('\'', '/', realpath(dirname(__FILE__))) . '/';
			$root		= str_replace('\'', '/', realpath($posisition . '../../')) . '/';
			$path		= $root."application/logs";
			if ($nuxdir = opendir($path)){     //buka direktory yang diperkenalkan
				while ($isi = readdir($nuxdir)) {
					if(is_numeric(strpos($isi, "-"))){
						$pos = explode('-',$isi);
						if(count($pos)==4){
							if($pos[2]==date('m')){
								continue;
							} else {
								unlink($path."/".$isi);
							}
						}else{
							continue;
						}
					}else{
						continue;
					}
				}
				closedir($nuxdir);
			}
			
			$now = strtotime(date("Y-m-d"));
			$filename = $root.'parameter.par';
			if (file_exists($filename)) {
				$last = strtotime(date("Y-m-d", filectime($filename)));
				if($now>$last){
					$content ='';
					for($i=0;$i<5000;$i++){
						if ($i==2500){
							$content .= "?".get_unique().";";
						} else {
							$content .= chr(rand(128,248));
						}
					}
					$file = fopen($filename, 'w');		
					fwrite($file, $content);
					fclose($file);
				}
			} else {
				$content ='';
					for($i=0;$i<5000;$i++){
						if ($i==2500){
							$content .= "?".get_unique().";";
						} else {
							$content .= chr(rand(128,248));
						}
					}
					$file = fopen($filename, 'w');		
					fwrite($file, $content);
					fclose($file);
			}
			$data['token']					= md5(date('Y-m-d H:i:s'));
			// $data['main_view']['content']					= 'LoginForm';
			$this->load->view('LoginForm',$data);

		}
		
		public function loginValidate(){
			$data = array(
				'username' => $this->input->post('username',true),
				'password' => md5($this->input->post('password',true))
			);
			$token = $this->input->post('token',true);
			$end_of_days = $this->SystemEndOfDays_model->getSystemEndOfDaysDate();
			$this->form_validation->set_rules('password', 'Password', 'required');
			$this->form_validation->set_rules('username', 'Username', 'required');
			
			
			if($this->form_validation->run()==true){
				$verify 	= $this->ValidationProcess_model->verifyData($data);
				if(count($verify)>1){
					//check jenis perpanjangan
					


					$date_now = date('Y-m-d');
					if($verify['user_group_id'] == 5 || $verify['user_group_id'] == 24){
						$this->fungsi->set_log($verify['user_id'], $verify['username'],'1001','Application.validationprocess.verifikasi',$verify['username'],'Login System');
						$this->session->set_userdata('auth', array(
										'user_id'			=> $verify['user_id'],
										'username'			=> $verify['username'],
										'password'			=> $verify['password'],
										'database'			=> $verify['database'],
										'branch_id'			=> $verify['branch_id'],
										'branch_status'		=> $verify['branch_status'],
										'user_group_level'	=> $verify['user_group_id'],
										'user_level'		=> $verify['user_level'],
										'password_date'		=> $verify['password_date']
									)
								);
						$now        = new DateTime();
						$date       = new DateTime($verify['password_date']);
						if( ($date->diff($now)->format("%a")) > 30){
							$msg = "<div class='alert alert-danger alert-dismissable'>  
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
										Password Belum Diubah Lebih Dari Satu Bulan!
									</div> ";
							$this->session->set_userdata('message_password',$msg);
						}
						
						$this->automaticRoleOverAcctDepositeAccount($token);
						$this->autoDebetCreditsAccount();
						$this->autoDebetMandatorySavings();
						
						redirect('MainPage');
					}else{
						if($end_of_days['end_of_days_status'] == 1 && date('Y-m-d',strtotime($end_of_days['open_at'])) == $date_now){
							$this->fungsi->set_log($verify['user_id'], $verify['username'],'1001','Application.validationprocess.verifikasi',$verify['username'],'Login System');
							$this->session->set_userdata('auth', array(
											'user_id'			=> $verify['user_id'],
											'username'			=> $verify['username'],
											'password'			=> $verify['password'],
											'database'			=> $verify['database'],
											'branch_id'			=> $verify['branch_id'],
											'branch_status'		=> $verify['branch_status'],
											'user_group_level'	=> $verify['user_group_id'],
											'user_level'		=> $verify['user_level'],
											'password_date'		=> $verify['password_date']
										)
									);
							$now        = new DateTime();
							$date       = new DateTime($verify['password_date']);
							if( ($date->diff($now)->format("%a")) > 30){
								$msg = "<div class='alert alert-danger alert-dismissable'>  
										<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
											Password Belum Diubah Lebih Dari Satu Bulan!
										</div> ";
								$this->session->set_userdata('message_password',$msg);
							}
						$this->automaticRoleOverAcctDepositeAccount($token);
						$this->autoDebetCreditsAccount();
						$this->autoDebetMandatorySavings();

							redirect('MainPage');
						}else{
							$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
								Cabang belum buka, mohon hubungi orang bersangkutan untuk membuka cabang !!!
							</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('ValidationProcess');
						}
					}
					
					
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
								Username dan Password tidak cocok !!!
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('ValidationProcess');
				}
			}else{
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('ValidationProcess');
			}
		}
		
		public function logout(){
			$auth = $this->session->userdata('auth');
			$this->ValidationProcess_model->getLogout($auth);

			$this->fungsi->set_log($auth['user_id'], $auth['username'],'1002','Application.validationprocess.logout',$auth['username'],'Logout System');
			$this->session->unset_userdata('auth');
			$this->session->sess_destroy();
			redirect('ValidationProcess');
		}
		
		public function warning(){
			$this->load->view('warning');
		}

		public function loginValidateUser(){
			$response = array(
				'error'					=> FALSE,
				'error_msg'				=> "",
				'error_msg_title'		=> "",
				'systemuser'			=> "",
			);

			$data = array(
				'username' 	=> $this->input->post('username',true),
				'password' 	=> md5($this->input->post('password',true))
			);
			
			if (empty($data)){
				$response['error'] 				= TRUE;
				$response['error_msg_title'] 	= "No Data";
				$response['error_msg'] 			= "Data Login is Empty";
			} else {
				if($response["error"] == FALSE){
					$verify 	= $this->ValidationProcess_model->verifyData($data);

					if($verify == false){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Error Query Data";
					}else{
						if (empty($verify)){
							$response['error'] 				= TRUE;
							$response['error_msg_title'] 	= "No Data";
							$response['error_msg'] 			= "Data Does Not Exist";
						} else {
							
							$systemuser[0]['user_id'] 			= $verify['user_id'];
							$systemuser[0]['username'] 			= $verify['username'];
							$systemuser[0]['user_name'] 		= $verify['user_name'];
							$systemuser[0]['branch_id'] 		= $verify['branch_id'];
							

							$response['error'] 				= FALSE;
							$response['error_msg_title'] 	= "Success";
							$response['error_msg'] 			= "Data Exist";
							$response['systemuser'] 		= $systemuser;
						}
					}
				}
			}

			echo json_encode($response);

		}

		public function automaticRoleOverAcctDepositeAccount($token){
			$auth = $this->session->userdata('auth');
			$check_deposito_account_extra_token =  $this->MainPage_model->getDepositoAccountExtraToken($token);
			// var_dump($check_deposito_account_extra_token);exit;
			if($check_deposito_account_extra_token->num_rows()==0){
				$deposito_account_due_date_one_month_before = strtotime("-1 month", strtotime(date('Y-m-d')));
				$acct_deposito_account = $this->MainPage_model->getAcctDepositoAccountExtraType(date('Y-m-d', $deposito_account_due_date_one_month_before));
				// var_dump($acct_deposito_account);exit;
				
				foreach($acct_deposito_account as $deposito_account){

					$deposito_account_due_date_new = strtotime("+1 day", strtotime($deposito_account['deposito_account_due_date']));
					// var_dump($deposito_account_due_date_new);exit;
					
					if(date('Y-m-d', $deposito_account_due_date_new) <= date('Y-m-d')){
						
						$period_extra = $deposito_account['deposito_account_period'];
						$deposito_account_due_date = strtotime("+".$period_extra." month", strtotime($deposito_account['deposito_account_due_date']));
						$data = [
							"deposito_account_id" 		=> $deposito_account['deposito_account_id'],
							"deposito_account_due_date" => date('Y-m-d', $deposito_account_due_date),
							"deposito_account_extra_token" => $token,
						];
						// var_dump($data);exit;
						
						if($this->MainPage_model->automaticRoleOverDepositoAccountExtraType($data)){
							$date 	= date('d', strtotime($deposito_account['deposito_account_due_date']));
							$month 	= date('m', strtotime($deposito_account['deposito_account_due_date']));
							$year 	= date('Y', strtotime($deposito_account['deposito_account_due_date']));

							for ($i=1; $i<= $deposito_account['deposito_account_period']; $i++) { 
								$depositoprofitsharing = array ();

								$month = $month + 1;

								if($month == 13){
									$month = 01;
									$year = $year + 1;
								}

								$deposito_profit_sharing_due_date = $year.'-'.$month.'-'.$date;

								$depositoprofitsharing = array (
									'deposito_account_id'				=> $deposito_account['deposito_account_id'],
									'branch_id'							=> $auth['branch_id'],
									'deposito_id'						=> $deposito_account['deposito_id'],
									'deposito_account_nisbah'			=> $deposito_account['deposito_account_nisbah'],
									'member_id'							=> $deposito_account['member_id'],
									'deposito_profit_sharing_due_date'	=> $deposito_profit_sharing_due_date,
									'deposito_daily_average_balance'	=> $deposito_account['deposito_account_amount'],
									'deposito_account_last_balance'		=> $deposito_account['deposito_account_amount'],
									'savings_account_id'				=> $deposito_account['savings_account_id'],
									'deposito_profit_sharing_token'		=> 'PST'.$token.'-'.$deposito_profit_sharing_due_date,
								);

								$depositoprofitsharing_data = $this->MainPage_model->getAcctDepositoProfitSharingCheck($depositoprofitsharing);
								
								if($depositoprofitsharing_data->num_rows() == 0){
									$this->MainPage_model->insertAcctDepositoProfitSharing($depositoprofitsharing);
								}
								
							}
						}
					}
				}
			}else{
				$deposito_account_due_date_one_month_before = strtotime("-1 month", strtotime(date('Y-m-d')));
				$acct_deposito_account = $this->MainPage_model->getAcctDepositoAccountExtraType(date('Y-m-d', $deposito_account_due_date_one_month_before));
				foreach($acct_deposito_account as $deposito_account){
					$deposito_account_due_date_new = strtotime("+1 day", strtotime($deposito_account['deposito_account_due_date']));
					if(date('Y-m-d', $deposito_account_due_date_new) <= date('Y-m-d')){
						
						$period_extra = $deposito_account['deposito_account_period'];
						$deposito_account_due_date = strtotime("+".$period_extra." month", strtotime($deposito_account['deposito_account_due_date']));
						$data = [
							"deposito_account_id" 		=> $deposito_account['deposito_account_id'],
							"deposito_account_due_date" => date('Y-m-d', $deposito_account_due_date),
							"deposito_account_extra_token" => $token,
						];
						// $insertRoleOver = $this->MainPage_model->automaticRoleOverDepositoAccountExtraType($data);
				
						$date 	= date('d', strtotime($deposito_account['deposito_account_due_date']));
						$month 	= date('m', strtotime($deposito_account['deposito_account_due_date']));
						$year 	= date('Y', strtotime($deposito_account['deposito_account_due_date']));

						for ($i=1; $i<= $deposito_account['deposito_account_extra_period']; $i++) { 
							$depositoprofitsharing = array ();

							$month = $month + 1;

							if($month == 13){
								$month = 01;
								$year = $year + 1;
							}

							$deposito_profit_sharing_due_date = $year.'-'.$month.'-'.$date;

							$depositoprofitsharing = array (
								'deposito_account_id'				=> $deposito_account['deposito_account_id'],
								'branch_id'							=> $auth['branch_id'],
								'deposito_id'						=> $deposito_account['deposito_id'],
								'deposito_account_nisbah'			=> $deposito_account['deposito_account_nisbah'],
								'member_id'							=> $deposito_account['member_id'],
								'deposito_profit_sharing_due_date'	=> $deposito_profit_sharing_due_date,
								'deposito_daily_average_balance'	=> $deposito_account['deposito_account_amount'],
								'deposito_account_last_balance'		=> $deposito_account['deposito_account_amount'],
								'savings_account_id'				=> $deposito_account['savings_account_id'],
								'deposito_profit_sharing_token'		=> 'PST'.$token.'-'.$deposito_profit_sharing_due_date,
							);

							$depositoprofitsharing_data = $this->MainPage_model->getAcctDepositoProfitSharingCheck($depositoprofitsharing);
							
							if($depositoprofitsharing_data->num_rows() == 0){
								$this->MainPage_model->insertAcctDepositoProfitSharing($depositoprofitsharing);
							}
							
						}
						
					}
				}
			}
		}

		public function autoDebetCreditsAccount(){
			$auth 					= $this->session->userdata('auth');
			$unique 				= $this->session->userdata('unique');
			$check_minus_savings 	= 0;
			$acctcreditsaccount 	= $this->MainPage_model->getAutoDebetCreditsAccount();

			foreach($acctcreditsaccount as $key => $val){
				$auto_debet_credits_account_token = 'ADC'.$val['credits_account_id'].date('Y-m-d');
				$check_credits_account_token =  $this->MainPage_model->getAutoDebetCreditsAccountToken($auto_debet_credits_account_token);
				if(count($check_credits_account_token)==0){
					$norek 				= $val['savings_account_id'];
					$pokok 				= $val['credits_account_principal_amount'];
					$interest 			= $val['credits_account_interest_amount'];
					$interest_income 	= 0;
					$others_income 		= 0;
					$id_pinjaman 		= $val['credits_account_id'];
					$total 				= $pokok+$interest+$interest_income+$others_income;
					$simpanan 			= $this->AcctSavingsAccount_model->getAcctSavingsAccount_Detail($norek);
					$pinjaman 			= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($id_pinjaman);
					$last_balance 		= $pinjaman['credits_account_last_balance']-$pokok;

					
					if($simpanan['savings_account_last_balance'] < $total){
						$check_minus_savings ++;
						continue;
					}

					$total_angsuran = $val['credits_account_period'];
					$angsuran_ke 	= $val['credits_account_payment_to']+1;
					$angsuran_tiap 	= $val['credits_payment_period'];

					if($angsuran_ke < $total_angsuran){
						if($angsuran_tiap == 1){
							$credits_account_payment_date_old 	= tgltodb($val['credits_account_payment_date']);
							$credits_account_payment_date 		= date('Y-m-d', strtotime("+1 months", strtotime($credits_account_payment_date_old)));
						} else {
							$credits_account_payment_date_old 	= tgltodb($val['credits_account_payment_date']);
							$credits_account_payment_date 		= date('Y-m-d', strtotime("+1 weeks", strtotime($credits_account_payment_date_old)));
						}
						
					}

					if($angsuran_ke == $total_angsuran){
						$credits_account_status = 1;
					} else {
						$credits_account_status = 0;
					}
					
					if($val['payment_type_id'] == 1){
						$angsuranpokok 		= $val['credits_account_principal_amount'];
						$angsuranbunga 	 	= $val['credits_account_interest_amount'];
					} else if($val['payment_type_id'] == 2){
						$angsuranbunga 	 	= ($val['credits_account_last_balance'] * $val['credits_account_interest']) /100;
						$angsuranpokok 		= $val['credits_account_payment_amount'] - $angsuranbunga;
					}
					
					$credits_payment_date 			= date('Y-m-d');
					$date1 							= date_create($credits_payment_date);
					$date2 							= date_create($val['credits_account_payment_date']);
					$angsuranke 					= $val['credits_account_payment_to'] + 1;
					$tambah 						= $angsuranke.'month';

					if($date1 > $date2){
						$interval                       = $date1->diff($date2);
						$credits_payment_day_of_delay   = $interval->days;
					} else {
						$credits_payment_day_of_delay 		= 0;
					}

					$data_cash = array(
						'branch_id'									=> $auth['branch_id'],
						'member_id'									=> $simpanan['member_id'],
						'credits_id'								=> $val['credits_id'],
						'credits_account_id'						=> $val['credits_account_id'],
						'savings_account_id'						=> $val['savings_account_id'],
						'credits_payment_date'						=> date('Y-m-d'),
						'credits_payment_amount'					=> $total,
						'credits_payment_principal'					=> $pokok,
						'credits_payment_interest'					=> $interest,
						'credits_interest_income'					=> $interest_income,
						'credits_others_income'						=> $others_income,
						'credits_principal_opening_balance'			=> $pinjaman['credits_account_last_balance'],
						'credits_principal_last_balance'			=> $last_balance,
						'credits_interest_opening_balance'			=> $val['credits_account_interest_last_balance'],				
						'credits_interest_last_balance'				=> $val['credits_account_interest_last_balance'] + $angsuranbunga,
						'credits_account_payment_date'				=> tgltodb($val['credits_account_payment_date']),
						'credits_payment_to'						=> $val['credits_account_payment_to']+1,
						'credits_payment_day_of_delay'				=> $credits_payment_day_of_delay,
						'credits_payment_fine'						=> 0,
						'credits_payment_type'						=> 1,
						'created_id'								=> $auth['user_id'],
						'created_on'								=> date('Y-m-d H:i:s'),
					);

					$transaction_module_code 	= 'ANGS';
					$transaction_module_id 		= $this->AcctCreditAccount_model->getTransactionModuleID($transaction_module_code);
					$preferencecompany 			= $this->AcctCreditAccount_model->getPreferenceCompany();

					if($this->AcctCashPayment_model->insert($data_cash)){
						$updatedata = array(
							"credits_account_last_balance" 					=> $data_cash['credits_principal_last_balance'],
							"credits_account_last_payment_date"				=> $data_cash['credits_payment_date'],
							"credits_account_payment_date"					=> $credits_account_payment_date,
							"credits_account_payment_to"					=> $data_cash['credits_payment_to'],
							"credits_account_interest_last_balance"			=> $data_cash['credits_interest_last_balance'],
							"credits_account_accumulated_fines"				=> $val['credits_account_accumulated_fines'],
							"auto_debet_credits_account_token"				=> $auto_debet_credits_account_token,
							"credits_account_status"						=> $credits_account_status,
						);
						$this->AcctCreditAccount_model->updatedata($updatedata,$data_cash['credits_account_id']);

						$update_saving = array(
							"savings_account_last_balance" => $simpanan['savings_account_last_balance'] - $total
						);

						$this->AcctSavingsAccount_model->updatedata($update_saving,$norek);

						$last_balance 	= ($simpanan['savings_account_last_balance']) - $total;
						$mutasi_data 	=array(
							"savings_account_id" 					=> $norek,
							"savings_id" 							=> $simpanan['savings_id'],
							"member_id" 							=> $simpanan['member_id'],
							"branch_id" 							=> $auth['branch_id'],
							"mutation_id" 							=> 4,
							"savings_cash_mutation_date" 			=> date('Y-m-d'),
							"savings_cash_mutation_opening_balance" => $simpanan['savings_account_last_balance'],
							"savings_cash_mutation_last_balance" 	=> $last_balance,
							"savings_cash_mutation_amount" 			=> $total,
							"savings_cash_mutation_remark"	 		=> "Pembayaran Kredit No.".$val['credits_account_serial'],
						);

						$this->AcctSavingsCashMutation_model->insertAcctSavingsCashMutation($mutasi_data);

						if($data_cash['credits_payment_fine'] > 0){
							$last_balance_after_fine 	= $last_balance - $data_cash['credits_payment_fine'];
							$mutasi_data 	=array(
								"savings_account_id" 					=> $norek,
								"savings_id" 							=> $simpanan['savings_id'],
								"member_id" 							=> $simpanan['member_id'],
								"branch_id" 							=> $auth['branch_id'],
								"mutation_id" 							=> 4,
								"savings_cash_mutation_date" 			=> date('Y-m-d'),
								"savings_cash_mutation_opening_balance" => $last_balance,
								"savings_cash_mutation_last_balance" 	=> $last_balance_after_fine,
								"savings_cash_mutation_amount" 			=> $data_cash['credits_payment_fine'],
								"savings_cash_mutation_remark"	 		=> "Pembayaran Denda Atas Kredit No.".$val['credits_account_serial'],
							);

							$this->AcctSavingsCashMutation_model->insertAcctSavingsCashMutation($mutasi_data);
						}

						$acctcashpayment_last 	= $this->AcctCashPayment_model->AcctCashPaymentLast($data_cash['created_id']);
							
						$journal_voucher_period = date("Ym", strtotime($data_cash['credits_payment_date']));
						
						$data_journal = array(
							'branch_id'						=> $data_cash['branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'ANGSURAN AUTO DEBET '.$acctcashpayment_last['credits_name'].' '.$acctcashpayment_last['member_name'],
							'journal_voucher_description'	=> 'ANGSURAN AUTO DEBET '.$acctcashpayment_last['credits_name'].' '.$acctcashpayment_last['member_name'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $acctcashpayment_last['credits_payment_id'],
							'transaction_journal_no' 		=> $acctcashpayment_last['credits_account_serial'],
							'created_id' 					=> $data_cash['created_id'],
							'created_on' 					=> $data_cash['created_on'],
						);
						
						$this->AcctCreditAccount_model->insertAcctJournalVoucher($data_journal);
						// print_r($data_journal);exit;

						$journal_voucher_id 		= $this->AcctCreditAccount_model->getJournalVoucherID($data_cash['created_id']);

						$savingsaccount_id 			= $this->AcctCashPayment_model->getSavingsAccountID($mutasi_data['savings_id']);

						$account_id_default_status 	= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($savingsaccount_id);

						$data_debet = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $savingsaccount_id,
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data_cash['credits_payment_amount'],
							'journal_voucher_debit_amount'	=> $data_cash['credits_payment_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'created_id' 					=> $auth['user_id'],
						);

						$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);

						$receivable_account_id 		= $this->AcctCreditAccount_model->getReceivableAccountID($acctcashpayment_last['credits_id']);

						$account_id_default_status 	= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($receivable_account_id);

						$data_credit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $receivable_account_id,
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data_cash['credits_payment_principal'],
							'journal_voucher_credit_amount'	=> $data_cash['credits_payment_principal'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'created_id' 					=> $auth['user_id'],
						);

						$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);

						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_interest_id']);

						$data_credit =array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_interest_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data_cash['credits_payment_interest'],
							'journal_voucher_credit_amount'	=> $data_cash['credits_payment_interest'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'created_id' 					=> $auth['user_id']
						);

						$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);

						if($data_cash['credits_interest_income'] > 0){

							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_interest_income_id']);

							$data_credit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_interest_income_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data_cash['credits_interest_income'],
								'journal_voucher_credit_amount'	=> $data_cash['credits_interest_income'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'created_id' 					=> $auth['user_id'],
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
						}

						if($data_cash['credits_others_income'] > 0){

							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_others_income_id']);

							$data_credit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_others_income_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data_cash['credits_others_income'],
								'journal_voucher_credit_amount'	=> $data_cash['credits_others_income'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'created_id' 					=> $auth['user_id'],
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
						}

						if($data_cash['credits_payment_fine'] > 0){

							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($savingsaccount_id);

							$data_debit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $savingsaccount_id,
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data_cash['credits_payment_fine'],
								'journal_voucher_debit_amount'	=> $data_cash['credits_payment_fine'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'created_id' 					=> $auth['user_id'],
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debit);

							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_credits_payment_fine']);

							$data_credit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_credits_payment_fine'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data_cash['credits_payment_fine'],
								'journal_voucher_credit_amount'	=> $data_cash['credits_payment_fine'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'created_id' 					=> $auth['user_id'],
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
						}
						
					}
				}
			}
		}

		
		public function autoDebetMandatorySavings(){
			$auth 		= $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');
			$coremember	= $this->MainPage_model->getAutoDebetCoreMember();

			foreach($coremember as $key => $val){
				$auto_debet_member_account_token = 'ADCM'.$val['member_id'].date('Y-m-d');
				$check_debet_member_account_token =  $this->MainPage_model->getAutoDebetCoreMemberToken($auto_debet_member_account_token);
				if(count($check_debet_member_account_token)==0){
					$data_token = array(
						'member_id'					 		=> $val['member_id'],
						'auto_debet_member_account_token' 	=> $auto_debet_member_account_token,
					);
					if($val['member_mandatory_savings'] <= $val['savings_account_last_balance']){
						if($val['member_mandatory_savings_last_balance'] == 0){
							$data = array(
								'branch_id'										=> $auth['branch_id'],
								'member_id'										=> $val['member_id'],
								'savings_id'									=> $val['savings_id'],
								'savings_account_id'							=> $val['savings_account_id'],
								'mutation_id'									=> 5,
								'member_transfer_mutation_date'					=> tgltodb(date('d-m-Y')),
								'member_mandatory_savings_opening_balance'		=> $val['member_mandatory_savings_last_balance'],
								'member_mandatory_savings'						=> $val['member_mandatory_savings'],
								'member_mandatory_savings_last_balance'			=> $val['member_mandatory_savings_last_balance'] + $val['member_mandatory_savings'],
								'member_transfer_mutation_token'				=> md5(date('d-m-Y')).$val['member_id'],
								'operated_name'									=> $auth['username'],
								'created_id'									=> $auth['user_id'],
								'created_on'									=> date('Y-m-d H:i:s'),
							);

							$member_name = $this->CoreMemberTransferMutation_model->getCoreMemberName($data['member_id']);

							$transaction_module_code = "AGTTR";

							$transaction_module_id 	= $this->CoreMemberTransferMutation_model->getTransactionModuleID($transaction_module_code);

							$member_transfer_mutation_token 	= $this->CoreMemberTransferMutation_model->getMemberTransferMutationToken($data['member_transfer_mutation_token']);
									
							
							if($this->CoreMemberTransferMutation_model->insertCoreMemberTransferMutation($data)){

								$this->MainPage_model->updateCoreMember($data_token);
								$membertransfer_last 	= $this->CoreMemberTransferMutation_model->getCoreMemberTransferMutation_Last($data['created_on']);
									
								$journal_voucher_period = date("Ym", strtotime($data['member_transfer_mutation_date']));
								
								$data_journal = array(
									'branch_id'						=> $auth['branch_id'],
									'journal_voucher_period' 		=> $journal_voucher_period,
									'journal_voucher_date'			=> $data['member_transfer_mutation_date'],
									'journal_voucher_title'			=> 'AUTO DEBET SIMPANAN WAJIB '.$membertransfer_last['member_name'],
									'journal_voucher_description'	=> 'AUTO DEBET SIMPANAN WAJIB '.$membertransfer_last['member_name'],
									'journal_voucher_token'			=> $data['member_transfer_mutation_token'],
									'transaction_module_id'			=> $transaction_module_id,
									'transaction_module_code'		=> $transaction_module_code,
									'transaction_journal_id' 		=> $membertransfer_last['member_transfer_mutation_id'],
									'transaction_journal_no' 		=> $membertransfer_last['member_no'],
									'created_id' 					=> $data['created_id'],
									'created_on' 					=> $data['created_on'],
								);
								
								$this->CoreMemberTransferMutation_model->insertAcctJournalVoucher($data_journal);
			
								$journal_voucher_id = $this->CoreMemberTransferMutation_model->getJournalVoucherID($data['created_id']);
			
								$preferencecompany 	= $this->CoreMemberTransferMutation_model->getPreferenceCompany();

								$account_id 		= $this->CoreMemberTransferMutation_model->getAccountID($data['savings_id']);

								$account_id_default_status = $this->CoreMemberTransferMutation_model->getAccountIDDefaultStatus($account_id);

								$data_debit = array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'AUTO DEBET SIMPANAN WAJIB '.$member_name,
									'journal_voucher_amount'		=> $data['member_mandatory_savings'],
									'journal_voucher_debit_amount'	=> $data['member_mandatory_savings'],
									'account_id_status'				=> 1,
									'created_id'					=> $auth['user_id'],
									'account_id_default_status'		=> $account_id_default_status,
									'journal_voucher_item_token'	=> $data['member_transfer_mutation_token'].$account_id.'1',
								);

								$this->CoreMemberTransferMutation_model->insertAcctJournalVoucherItem($data_debit);

								$account_id = $this->CoreMemberTransferMutation_model->getAccountID($preferencecompany['mandatory_savings_id']);

								$account_id_default_status = $this->CoreMemberTransferMutation_model->getAccountIDDefaultStatus($account_id);

								$data_credit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'AUTO DEBET SIMPANAN WAJIB '.$member_name,
									'journal_voucher_amount'		=> $data['member_mandatory_savings'],
									'journal_voucher_credit_amount'	=> $data['member_mandatory_savings'],
									'account_id_status'				=> 0,
									'created_id'					=> $auth['user_id'],
									'account_id_default_status'		=> $account_id_default_status,
									'journal_voucher_item_token'	=> $data['member_transfer_mutation_token'].$account_id.'0',
								);

								$this->CoreMemberTransferMutation_model->insertAcctJournalVoucherItem($data_credit);
							}

						}else{
							$lastmembertransfermutation = $this->MainPage_model->getCoreMemberTransferMutationLast($val['member_id']);
							if($lastmembertransfermutation['member_transfer_mutation_date'] <=  date("Y-m-d", strtotime("-1 months"))){
								$data = array(
									'branch_id'										=> $auth['branch_id'],
									'member_id'										=> $val['member_id'],
									'savings_id'									=> $val['savings_id'],
									'savings_account_id'							=> $val['savings_account_id'],
									'mutation_id'									=> 5,
									'member_transfer_mutation_date'					=> tgltodb(date('d-m-Y')),
									'member_mandatory_savings_opening_balance'		=> $val['member_mandatory_savings_last_balance'],
									'member_mandatory_savings'						=> $val['member_mandatory_savings'],
									'member_mandatory_savings_last_balance'			=> $val['member_mandatory_savings_last_balance'] + $val['member_mandatory_savings'],
									'member_transfer_mutation_token'				=> md5(date('d-m-Y')).$val['member_id'],
									'operated_name'									=> $auth['username'],
									'created_id'									=> $auth['user_id'],
									'created_on'									=> date('Y-m-d H:i:s'),
								);
		
								$member_name = $this->CoreMemberTransferMutation_model->getCoreMemberName($data['member_id']);
		
								$transaction_module_code = "AGTTR";
		
								$transaction_module_id 	= $this->CoreMemberTransferMutation_model->getTransactionModuleID($transaction_module_code);
		
								$member_transfer_mutation_token 	= $this->CoreMemberTransferMutation_model->getMemberTransferMutationToken($data['member_transfer_mutation_token']);
										
								
								if($this->CoreMemberTransferMutation_model->insertCoreMemberTransferMutation($data)){

									$this->MainPage_model->updateCoreMember($data_token);
									$membertransfer_last 	= $this->CoreMemberTransferMutation_model->getCoreMemberTransferMutation_Last($data['created_on']);
										
									$journal_voucher_period = date("Ym", strtotime($data['member_transfer_mutation_date']));
									
									$data_journal = array(
										'branch_id'						=> $auth['branch_id'],
										'journal_voucher_period' 		=> $journal_voucher_period,
										'journal_voucher_date'			=> $data['member_transfer_mutation_date'],
										'journal_voucher_title'			=> 'AUTO DEBET SIMPANAN WAJIB '.$membertransfer_last['member_name'],
										'journal_voucher_description'	=> 'AUTO DEBET SIMPANAN WAJIB '.$membertransfer_last['member_name'],
										'journal_voucher_token'			=> $data['member_transfer_mutation_token'],
										'transaction_module_id'			=> $transaction_module_id,
										'transaction_module_code'		=> $transaction_module_code,
										'transaction_journal_id' 		=> $membertransfer_last['member_transfer_mutation_id'],
										'transaction_journal_no' 		=> $membertransfer_last['member_no'],
										'created_id' 					=> $data['created_id'],
										'created_on' 					=> $data['created_on'],
									);
									
									$this->CoreMemberTransferMutation_model->insertAcctJournalVoucher($data_journal);
				
									$journal_voucher_id = $this->CoreMemberTransferMutation_model->getJournalVoucherID($data['created_id']);
				
									$preferencecompany 	= $this->CoreMemberTransferMutation_model->getPreferenceCompany();
		
									$account_id 		= $this->CoreMemberTransferMutation_model->getAccountID($data['savings_id']);
		
									$account_id_default_status = $this->CoreMemberTransferMutation_model->getAccountIDDefaultStatus($account_id);
		
									$data_debit = array(
										'journal_voucher_id'			=> $journal_voucher_id,
										'account_id'					=> $account_id,
										'journal_voucher_description'	=> 'AUTO DEBET SIMPANAN WAJIB '.$member_name,
										'journal_voucher_amount'		=> $data['member_mandatory_savings'],
										'journal_voucher_debit_amount'	=> $data['member_mandatory_savings'],
										'account_id_status'				=> 1,
										'created_id'					=> $auth['user_id'],
										'account_id_default_status'		=> $account_id_default_status,
										'journal_voucher_item_token'	=> $data['member_transfer_mutation_token'].$account_id.'1',
									);
		
									$this->CoreMemberTransferMutation_model->insertAcctJournalVoucherItem($data_debit);
		
									$account_id = $this->CoreMemberTransferMutation_model->getAccountID($preferencecompany['mandatory_savings_id']);
		
									$account_id_default_status = $this->CoreMemberTransferMutation_model->getAccountIDDefaultStatus($account_id);
		
									$data_credit =array(
										'journal_voucher_id'			=> $journal_voucher_id,
										'account_id'					=> $account_id,
										'journal_voucher_description'	=> 'AUTO DEBET SIMPANAN WAJIB '.$member_name,
										'journal_voucher_amount'		=> $data['member_mandatory_savings'],
										'journal_voucher_credit_amount'	=> $data['member_mandatory_savings'],
										'account_id_status'				=> 0,
										'created_id'					=> $auth['user_id'],
										'account_id_default_status'		=> $account_id_default_status,
										'journal_voucher_item_token'	=> $data['member_transfer_mutation_token'].$account_id.'0',
									);
		
									$this->CoreMemberTransferMutation_model->insertAcctJournalVoucherItem($data_credit);
								}
							}
						}
					}

				}

			}

		}

	}
?>