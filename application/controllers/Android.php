<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class Android extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('Android_model');
			$this->load->model('AcctSavingsCashMutation_model');
			$this->load->model('AcctCashPayment_model');
			$this->load->model('AcctCreditAccount_model');
			$this->load->model('AcctCashPayment_model');
			$this->load->model('AcctCreditAccount_model');
			$this->load->model('AcctSavingsTransferMutation_model');
			$this->load->model('CoreMember_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			
		}

		public function getCoreMember(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'				=> FALSE,
				'error_msg'			=> "",
				'error_msg_title'	=> "",
				'coremember'		=> "",
			);

			$data = array(
				'branch_id'		=> $this->input->post('branch_id',true),
				'user_id'		=> $this->input->post('user_id',true),
			);

			$preferencecompany 		= $this->Android_model->getPreferenceCompany();

			$user_time_limit 		= strtotime(date("Y-m-d")." ".$preferencecompany['user_time_limit']);

			$now 					= strtotime(date("Y-m-d H:i:s"));

			if ($now <= $user_time_limit){

				if($response["error"] == FALSE){
					$corememberlist 	= $this->Android_model->getCoreMember($data['branch_id']);

					if(!$corememberlist){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Error Query Data";
					}else{
						if (empty($corememberlist)){
							$response['error'] 				= TRUE;
							$response['error_msg_title'] 	= "No Data";
							$response['error_msg'] 			= "Data Does Not Exist";
						} else {
							foreach ($corememberlist as $key => $val) {
								$memberidentity = $this->configuration->MemberIdentity();

								$member_address 	= $val['member_address'].' '.$val['province_name'].' '.$val['city_name'].' '.$val['kecamatan_name'];

								$coremember[$key]['branch_id']				= $val['branch_id'];
								$coremember[$key]['branch_name']			= $val['branch_name'];
								$coremember[$key]['member_id']				= $val['member_id'];
								$coremember[$key]['member_no']				= $val['member_no'];
								$coremember[$key]['member_name']			= $val['member_name'];
								$coremember[$key]['member_address']			= $member_address;
								$coremember[$key]['member_identity_no']		= $val['member_identity_no'];
							}
							
							$response['error'] 					= FALSE;
							$response['error_msg_title'] 		= "Success";
							$response['error_msg'] 				= "Data Exist";
							$response['coremember'] 			= $coremember;
						}
					}
				}
			} else {
				$response['error'] 				= TRUE;
				$response['error_msg_title'] 	= "No Data";
				$response['error_msg'] 			= "Waktu Transaksi Sudah Habis";
			}
			echo json_encode($response);
		}
		
		public function getAcctSavingsAccount(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'						=> FALSE,
				'error_msg'					=> "",
				'error_msg_title'			=> "",
				'acctsavingsaccount'		=> "",
			);

			$data = array(
				'branch_id'		=> $this->input->post('branch_id',true),
				'member_id'		=> $this->input->post('member_id',true),
			);

			$preferencecompany 		= $this->Android_model->getPreferenceCompany();

			$user_time_limit 		= strtotime(date("Y-m-d")." ".$preferencecompany['user_time_limit']);

			$now 					= strtotime(date("Y-m-d H:i:s"));

			/*print_r("data ");
			print_r($data);
			print_r("<BR>");*/

			/*print_r("user_time_limit ");
			print_r($user_time_limit);
			print_r("<BR>");*/

			if ($now <= $user_time_limit){
				if($response["error"] == FALSE){
					$acctsavingsaccountlist = $this->Android_model->getAcctSavingsAccount($data['member_id'], $data['branch_id']);

					if(!$acctsavingsaccountlist){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Error Query Data";
					}else{
						if (empty($acctsavingsaccountlist)){
							$response['error'] 				= TRUE;
							$response['error_msg_title'] 	= "No Data";
							$response['error_msg'] 			= $data;
						} else {
							foreach ($acctsavingsaccountlist as $key => $val) {
								$acctsavingsaccount[$key]['savings_account_id'] 					= $val['savings_account_id'];
								$acctsavingsaccount[$key]['savings_id']								= $val['savings_id'];
								$acctsavingsaccount[$key]['savings_code']							= $val['savings_code'];
								$acctsavingsaccount[$key]['savings_name']							= $val['savings_name'];
								$acctsavingsaccount[$key]['savings_account_no']						= $val['savings_account_no'];
								$acctsavingsaccount[$key]['savings_account_first_deposit_amount']	= $val['savings_account_first_deposit_amount'];
								$acctsavingsaccount[$key]['savings_account_last_balance']			= $val['savings_account_last_balance'];
							}
							
							$response['error'] 					= FALSE;
							$response['error_msg_title'] 		= "Success";
							$response['error_msg'] 				= "Data Exist";
							$response['acctsavingsaccount'] 	= $acctsavingsaccount;
						}
					}
				}
			} else {
				$response['error'] 				= TRUE;
				$response['error_msg_title'] 	= "No Data";
				$response['error_msg'] 			= "Waktu Transaksi Sudah Habis";
			}

			echo json_encode($response);
		}

		public function getAcctSavingsAccountMBayar(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'						=> FALSE,
				'error_msg'					=> "",
				'error_msg_title'			=> "",
				'acctsavingsaccount'		=> "",
			);

			$data = array(
				'branch_id'		=> $this->input->post('branch_id',true),
				'member_id'		=> $this->input->post('member_id',true),
			);

			
			/*print_r("now ");
			print_r($now);
			print_r("<BR>");

			print_r("user_time_limit ");
			print_r($user_time_limit);
			print_r("<BR>");*/

			
			if($response["error"] == FALSE){
				$acctsavingsaccountlist = $this->Android_model->getAcctSavingsAccount($data['member_id'], $data['branch_id']);

				if(!$acctsavingsaccountlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingsaccountlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {
						foreach ($acctsavingsaccountlist as $key => $val) {
							$acctsavingsaccount[$key]['savings_account_id'] 					= $val['savings_account_id'];
							$acctsavingsaccount[$key]['savings_id']								= $val['savings_id'];
							$acctsavingsaccount[$key]['savings_code']							= $val['savings_code'];
							$acctsavingsaccount[$key]['savings_name']							= $val['savings_name'];
							$acctsavingsaccount[$key]['savings_account_no']						= $val['savings_account_no'];
							$acctsavingsaccount[$key]['savings_account_first_deposit_amount']	= $val['savings_account_first_deposit_amount'];
							$acctsavingsaccount[$key]['savings_account_last_balance']			= $val['savings_account_last_balance'];
						}
						
						$response['error'] 					= FALSE;
						$response['error_msg_title'] 		= "Success";
						$response['error_msg'] 				= "Data Exist";
						$response['acctsavingsaccount'] 	= $acctsavingsaccount;
					}
				}
			}
			

			echo json_encode($response);
		}

		public function processAddAcctSavingsCashMutation(){
			$status = $this->input->post('status',true);

			/*print_r("status ");
			print_r("$status");*/

			$preferencecompany = $this->Android_model->getPreferenceCompany();

			if ($status == 1){
				$mutation_id = $preferencecompany['cash_deposit_id'];
			} else if ($status == 2){
				$mutation_id = $preferencecompany['cash_withdrawal_id'];
			}

			$password										= md5($this->input->post('password', true));

			$data = array(
				'savings_account_id'						=> $this->input->post('savings_account_id', true),
				'mutation_id'								=> $mutation_id,
				'member_id'									=> $this->input->post('member_id', true),
				'branch_id'									=> $this->input->post('branch_id', true),
				'savings_id'								=> $this->input->post('savings_id', true),
				'savings_cash_mutation_date'				=> date('Y-m-d'),
				'savings_cash_mutation_opening_balance'		=> $this->input->post('savings_cash_mutation_opening_balance', true),
				'savings_cash_mutation_last_balance'		=> $this->input->post('savings_cash_mutation_last_balance', true),
				'savings_cash_mutation_amount'				=> $this->input->post('savings_cash_mutation_amount', true),
				'savings_cash_mutation_remark'				=> $this->input->post('savings_cash_mutation_remark', true),
				'savings_cash_mutation_status'				=> 1,
				'operated_name'								=> $this->input->post('username', true),
				'created_id'								=> $this->input->post('user_id', true),
				'created_on'								=> date('Y-m-d H:i:s'),
			);
			
			$response = array(
				'error'										=> FALSE,
				'error_acctsavingscashmutation'				=> FALSE,
				'error_msg_title_acctsavingscashmutation'	=> "",
				'error_msg_acctsavingscashmutation'			=> "",
			);

			if($response["error_acctsavingscashmutation"] == FALSE){
				if(!empty($data)){					
					if($this->Android_model->getSystemUser($data['created_id'], $password)){
						if ($this->Android_model->insertAcctSavingsCashMutation($data)){

							$transaction_module_code = "TTAB";

							$transaction_module_id 	= $this->AcctSavingsCashMutation_model->getTransactionModuleID($transaction_module_code);
							$acctsavingscash_last 	= $this->AcctSavingsCashMutation_model->getAcctSavingsCashMutation_Last($data['created_id']);

							$savings_cash_mutation_id = $acctsavingscash_last['savings_cash_mutation_id'];

								
							$journal_voucher_period = date("Ym", strtotime($data['savings_cash_mutation_date']));
							
							$data_journal = array(
								'branch_id'						=> $data['branch_id'],
								'journal_voucher_period' 		=> $journal_voucher_period,
								'journal_voucher_date'			=> date('Y-m-d'),
								'journal_voucher_title'			=> 'MUTASI TUNAI '.$acctsavingscash_last['member_name'],
								'journal_voucher_description'	=> 'MUTASI TUNAI '.$acctsavingscash_last['member_name'],
								'transaction_module_id'			=> $transaction_module_id,
								'transaction_module_code'		=> $transaction_module_code,
								'transaction_journal_id' 		=> $acctsavingscash_last['savings_cash_mutation_id'],
								'transaction_journal_no' 		=> $acctsavingscash_last['savings_account_no'],
								'created_id' 					=> $data['created_id'],
								'created_on' 					=> $data['created_on'],
							);
							
							$this->AcctSavingsCashMutation_model->insertAcctJournalVoucher($data_journal);

							$journal_voucher_id = $this->AcctSavingsCashMutation_model->getJournalVoucherID($data['created_id']);

							$preferencecompany = $this->AcctSavingsCashMutation_model->getPreferenceCompany();


							if($data['mutation_id'] == $preferencecompany['cash_deposit_id']){
								$account_id_default_status = $this->AcctSavingsCashMutation_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

								$data_debet = array (
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $preferencecompany['account_cash_id'],
									'journal_voucher_description'	=> 'SETORAN TUNAI '.$acctsavingscash_last['member_name'],
									'journal_voucher_amount'		=> $data['savings_cash_mutation_amount'],
									'journal_voucher_debit_amount'	=> $data['savings_cash_mutation_amount'],
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 0,
								);

								$this->AcctSavingsCashMutation_model->insertAcctJournalVoucherItem($data_debet);

								$account_id = $this->AcctSavingsCashMutation_model->getAccountID($data['savings_id']);

								$account_id_default_status = $this->AcctSavingsCashMutation_model->getAccountIDDefaultStatus($account_id);

								$data_credit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'SETORAN TUNAI '.$acctsavingscash_last['member_name'],
									'journal_voucher_amount'		=> $data['savings_cash_mutation_amount'],
									'journal_voucher_credit_amount'	=> $data['savings_cash_mutation_amount'],
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 1,
								);

								$this->AcctSavingsCashMutation_model->insertAcctJournalVoucherItem($data_credit);
							} else {
								$account_id_default_status = $this->AcctSavingsCashMutation_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);
					
								$account_id = $this->AcctSavingsCashMutation_model->getAccountID($data['savings_id']);

								$account_id_default_status = $this->AcctSavingsCashMutation_model->getAccountIDDefaultStatus($account_id);

								$data_debit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$acctsavingscash_last['member_name'],
									'journal_voucher_amount'		=> $data['savings_cash_mutation_amount'],
									'journal_voucher_debit_amount'	=> $data['savings_cash_mutation_amount'],
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 0,
								);

								$this->AcctSavingsCashMutation_model->insertAcctJournalVoucherItem($data_debit);

								$data_credit = array (
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $preferencecompany['account_cash_id'],
									'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$acctsavingscash_last['member_name'],
									'journal_voucher_amount'		=> $data['savings_cash_mutation_amount'],
									'journal_voucher_credit_amount'	=> $data['savings_cash_mutation_amount'],
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 1,
								);

								$this->AcctSavingsCashMutation_model->insertAcctJournalVoucherItem($data_credit);
							}

							$response['error_acctsavingscashmutation'] 	= FALSE;
							$response['error_msg_title'] 				= "Success";
							$response['error_msg'] 						= "Data Exist";
							$response['savings_cash_mutation_id'] 		= $savings_cash_mutation_id;
						} else {
							$response['error_acctsavingscashmutation'] 	= TRUE;
							$response['error_msg_title'] 				= "Success";
							$response['error_msg'] 						= "Data Exist";
							$response['savings_cash_mutation_id'] 		= "";
						}
					} else {
						$response['error_acctsavingscashmutation'] 	= TRUE;
						$response['error_msg_title'] 				= "Gagal";
						$response['error_msg'] 						= "Password Salah";
						$response['savings_cash_mutation_id'] 		= "";
					}
				}

			} 
			
			echo json_encode($response);
		}

		public function getAcctCreditsAccount(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'						=> FALSE,
				'error_msg'					=> "",
				'error_msg_title'			=> "",
				'acctcreditsaccount'		=> "",
			);

			$data = array(
				'branch_id'		=> $this->input->post('branch_id',true),
				'member_id'		=> $this->input->post('member_id',true),
			);

			$preferencecompany 		= $this->Android_model->getPreferenceCompany();

			$user_time_limit 		= strtotime(date("Y-m-d")." ".$preferencecompany['user_time_limit']);

			$now 					= strtotime(date("Y-m-d H:i:s"));

			if ($now <= $user_time_limit){

				if($response["error"] == FALSE){
					$acctcreditsaccountlist = $this->Android_model->getAcctCreditsAccount($data['member_id'], $data['branch_id']);

					if(!$acctcreditsaccountlist){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Error Query Data";
					}else{
						if (empty($acctcreditsaccountlist)){
							$response['error'] 				= TRUE;
							$response['error_msg_title'] 	= "No Data";
							$response['error_msg'] 			= "Data Does Not Exist";
						} else {
							foreach ($acctcreditsaccountlist as $key => $val) {
								$credits_account_id 	= $val['credits_account_id'];

								$credits_payment_date 	= date('Y-m-d');
								$date1 = date_create($credits_payment_date);
								$date2 = date_create($val['credits_account_payment_date']);

								if($date1 > $date2){
									$interval                       = $date1->diff($date2);
							    	$credits_payment_day_of_delay   = $interval->days;
								} else {
									$credits_payment_day_of_delay 	= 0;
								}
								

								// print_r($credits_payment_day_of_delay);

								$credits_account_payment_to 		= $val['credits_account_payment_to'] + 1;

								$credits_payment_fine_amount 		= (($val['credits_account_payment_amount'] * $val['credits_fine']) / 100 ) * $credits_payment_day_of_delay;

								$credits_account_accumulated_fines 	= $val['credits_account_accumulated_fines'] + $credits_payment_fine_amount;

								if($val['payment_type_id'] == 1){
									$angsuranpokok 		= $val['credits_account_principal_amount'];

									$angsuranbunga 	 	= $val['credits_account_interest_amount'];

								} else if($val['payment_type_id'] == 2){
									$angsuranbunga 	 	= ($val['credits_account_last_balance'] * $val['credits_account_interest']) /100;

									$angsuranpokok 		= $val['credits_account_payment_amount'] - $angsuranbunga;
								}


								$acctcreditsaccount[$key]['credits_account_id'] 					= $val['credits_account_id'];
								$acctcreditsaccount[$key]['credits_id']								= $val['credits_id'];
								$acctcreditsaccount[$key]['credits_code']							= $val['credits_code'];
								$acctcreditsaccount[$key]['credits_name']							= $val['credits_name'];
								$acctcreditsaccount[$key]['credits_account_serial']					= $val['credits_account_serial'];
								$acctcreditsaccount[$key]['credits_account_amount']					= $val['credits_account_amount'];
								$acctcreditsaccount[$key]['credits_account_last_balance']			= $val['credits_account_last_balance'];

								$acctcreditsaccount[$key]['credits_payment_day_of_delay']			= $credits_payment_day_of_delay;
								$acctcreditsaccount[$key]['credits_account_payment_to']				= $credits_account_payment_to;
								$acctcreditsaccount[$key]['credits_payment_fine_amount']			= $credits_payment_fine_amount;
								$acctcreditsaccount[$key]['credits_account_accumulated_fines']		= $credits_account_accumulated_fines;

								$acctcreditsaccount[$key]['credits_principal_installments']			= $angsuranpokok;
								$acctcreditsaccount[$key]['credits_interest_installments']			= $angsuranbunga;

								$acctcreditsaccount[$key]['credits_account_payment_amount']			= $val['credits_account_payment_amount'];
							}
							
							$response['error'] 					= FALSE;
							$response['error_msg_title'] 		= "Success";
							$response['error_msg'] 				= "Data Exist";
							$response['acctcreditsaccount'] 	= $acctcreditsaccount;
						}
					}
				}
			} else {
				$response['error'] 				= TRUE;
				$response['error_msg_title'] 	= "No Data";
				$response['error_msg'] 			= "Waktu Transaksi Sudah Habis";
			}
			echo json_encode($response);
		}

		public function processAddAcctCreditsPayment(){
			$response = array(
				'error'										=> FALSE,
				'error_acctcreditspayment'					=> FALSE,
				'error_msg_title_acctcreditspayment'		=> "",
				'error_msg_acctcreditspayment'				=> "",
			);

			$auth 									= $this->session->userdata('auth');
			$total_angsuran 						= $this->input->post('credits_account_period', true);
			$angsuran_ke 							= $this->input->post('credits_payment_to', true);
			$angsuran_tiap							= $this->input->post('credits_payment_period', true);

			$password 								= md5($this->input->post('password', true));

			/* print_r("total_angsuran ");
			print_r($total_angsuran);
			print_r("<BR> ");

			print_r("angsuran_ke ");
			print_r($angsuran_ke);
			print_r("<BR> ");

			print_r("angsuran_tiap ");
			print_r($angsuran_tiap);
			print_r("<BR> ");

			print_r("password ");
			print_r($password);
			print_r("<BR> "); */

			$credits_account_payment_date_old = date("Y-m-d");

			if($angsuran_ke <= $total_angsuran){
				if($angsuran_tiap == 1){
					$credits_account_payment_date 		= date('Y-m-d', strtotime("+1 months", strtotime($credits_account_payment_date_old)));
				} else {
					$credits_account_payment_date 		= date('Y-m-d', strtotime("+1 weeks", strtotime($credits_account_payment_date_old)));
				}
			}

			if($angsuran_ke == $total_angsuran){
				$credits_account_status = 1;
			} else {
				$credits_account_status = 0;
			}

			// print_r($credits_account_payment_date);exit;
			$data = array(
				'branch_id'									=> $this->input->post('branch_id', true),
				'member_id'									=> $this->input->post('member_id', true),
				'credits_id'								=> $this->input->post('credits_id', true),
				'credits_account_id'						=> $this->input->post('credits_account_id', true),
				'credits_payment_date'						=> date('Y-m-d'),
				'credits_payment_amount'					=> $this->input->post('credits_payment_amount', true),
				'credits_payment_principal'					=> $this->input->post('credits_payment_principal', true),
				'credits_payment_interest'					=> $this->input->post('credits_payment_interest', true),
				'credits_principal_opening_balance'			=> $this->input->post('credits_principal_opening_balance', true),
				'credits_principal_last_balance'			=> $this->input->post('credits_principal_opening_balance', true) - $this->input->post('credits_payment_principal', true),

				'credits_interest_opening_balance'			=> $this->input->post('credits_interest_opening_balance', true),
				'credits_interest_last_balance'				=> $this->input->post('credits_interest_opening_balance', true) + $this->input->post('credits_payment_interest', true),		

				'credits_payment_fine'						=> $this->input->post('credits_payment_fine', true),
				'credits_account_payment_date'				=> date("Y-m-d"),
				'credits_payment_to'						=> $this->input->post('credits_payment_to', true),
				'credits_payment_day_of_delay'				=> $this->input->post('credits_payment_day_of_delay', true),
				'credits_payment_token'						=> $this->input->post('credits_payment_token', true),
				'credits_payment_status'					=> 1,
				'created_id'								=> $this->input->post('user_id', true),
				'created_on'								=> date('Y-m-d H:i:s'),
			);

			/*print_r("data ");
			print_r($data);*/
		

			$member_mandatory_savings 						= $this->input->post('member_mandatory_savings', true);
			
			
			// $this->form_validation->set_rules('jumlah_angsuran', 'Jumlah Pembayaran', 'required');

			$transaction_module_code 						= 'ANGS';
			$transaction_module_id 							= $this->AcctCreditAccount_model->getTransactionModuleID($transaction_module_code);
			$preferencecompany 								= $this->AcctCreditAccount_model->getPreferenceCompany();

			$journal_voucher_period 						= date("Ym", strtotime($data['credits_payment_date']));
			
			$credits_payment_token 							= $this->AcctCashPayment_model->getCreditsPaymentToken($data['credits_payment_token']);


			if($response["error_acctcreditspayment"] == FALSE){
				if(!empty($data)){					
					if ($this->Android_model->getSystemUser($data['created_id'], $password)){
						if($credits_payment_token->num_rows() == 0){
							if($this->AcctCashPayment_model->insert($data)){

								$acctcashpayment_last 	= $this->AcctCashPayment_model->AcctCashPaymentLast($data['created_id']);

								$credits_payment_id 	= $acctcashpayment_last['credits_payment_id'];


								$updatedata = array(
									"credits_account_last_balance" 					=> $data['credits_principal_last_balance'],
									"credits_account_last_payment_date"				=> $data['credits_payment_date'],
									"credits_account_interest_last_balance"			=> $data['credits_interest_last_balance'],
									"credits_account_payment_date"					=> $credits_account_payment_date,
									"credits_account_payment_to"					=> $data['credits_payment_to'],
									"credits_account_accumulated_fines"				=> $this->input->post('credits_account_accumulated_fines', true),
									'credits_account_status'						=> $credits_account_status,
								);

								$this->AcctCreditAccount_model->updatedata($updatedata, $data['credits_account_id']);

								if($member_mandatory_savings <> 0 || $member_mandatory_savings <> ''){

									$data_detail = array (
										'branch_id'						=> $data['branch_id'],
										'member_id'						=> $data['member_id'],
										'mutation_id'					=> 1,
										'transaction_date'				=> date('Y-m-d'),
										'mandatory_savings_amount'		=> $member_mandatory_savings,
										'operated_name'					=> $auth['username'],
										'savings_member_detail_token'	=> $data['credits_payment_token'],
									);

									$this->AcctCashPayment_model->insertAcctSavingsMemberDetail($data_detail);
								}

								$acctcashpayment_last 							= $this->AcctCashPayment_model->AcctCashPaymentLast($data['created_id']);
								
								$data_journal = array(
									'branch_id'						=> $data['branch_id'],
									'journal_voucher_period' 		=> $journal_voucher_period,
									'journal_voucher_date'			=> date('Y-m-d'),
									'journal_voucher_title'			=> 'ANGSURAN TUNAI '.$acctcashpayment_last['credits_name'].' '.$acctcashpayment_last['member_name'],
									'journal_voucher_description'	=> 'ANGSURAN TUNAI '.$acctcashpayment_last['credits_name'].' '.$acctcashpayment_last['member_name'],
									'journal_voucher_token'			=> $data['credits_payment_token'],
									'transaction_module_id'			=> $transaction_module_id,
									'transaction_module_code'		=> $transaction_module_code,
									'transaction_journal_id' 		=> $acctcashpayment_last['credits_payment_id'],
									'transaction_journal_no' 		=> $acctcashpayment_last['credits_account_serial'],
									'created_id' 					=> $data['created_id'],
									'created_on' 					=> $data['created_on'],
								);
								
								$this->AcctCreditAccount_model->insertAcctJournalVoucher($data_journal);

								$journal_voucher_id 				= $this->AcctCreditAccount_model->getJournalVoucherID($data['created_id']);

								$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

								$data_debet = array (
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $preferencecompany['account_cash_id'],
									'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
									'journal_voucher_amount'		=> $data['credits_payment_amount'],
									'journal_voucher_debit_amount'	=> $data['credits_payment_amount'],
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 0,
									'journal_voucher_item_token'	=> $data['credits_payment_token'].$preferencecompany['account_cash_id'],
								);

								$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);

								$receivable_account_id 				= $this->AcctCreditAccount_model->getReceivableAccountID($data['credits_id']);

								$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($receivable_account_id);

								$data_credit = array (
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $receivable_account_id,
									'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
									'journal_voucher_amount'		=> $data['credits_payment_principal'],
									'journal_voucher_credit_amount'	=> $data['credits_payment_principal'],
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 1,
									'journal_voucher_item_token'	=> $data['credits_payment_token'].$receivable_account_id,
								);

								$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);

								$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_interest_id']);

								$data_credit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $preferencecompany['account_interest_id'],
									'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
									'journal_voucher_amount'		=> $data['credits_payment_interest'],
									'journal_voucher_credit_amount'	=> $data['credits_payment_interest'],
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 1,
									'journal_voucher_item_token'	=> $data['credits_payment_token'].$preferencecompany['account_interest_id'],
								);

								$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);

								if($data['credits_payment_fine'] > 0){
									$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

									$data_debit =array(
										'journal_voucher_id'			=> $journal_voucher_id,
										'account_id'					=> $preferencecompany['account_cash_id'],
										'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
										'journal_voucher_amount'		=> $data['credits_payment_fine'],
										'journal_voucher_debit_amount'	=> $data['credits_payment_fine'],
										'account_id_default_status'		=> $account_id_default_status,
										'account_id_status'				=> 0,
										'journal_voucher_item_token'	=> $data['credits_payment_token'].'D'.$preferencecompany['account_cash_id'],
									);

									$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debit);

									$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_credits_payment_fine']);

									$data_credit =array(
										'journal_voucher_id'			=> $journal_voucher_id,
										'account_id'					=> $preferencecompany['account_credits_payment_fine'],
										'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
										'journal_voucher_amount'		=> $data['credits_payment_fine'],
										'journal_voucher_credit_amount'	=> $data['credits_payment_fine'],
										'account_id_default_status'		=> $account_id_default_status,
										'account_id_status'				=> 1,
										'journal_voucher_item_token'	=> $data['credits_payment_token'].$preferencecompany['account_credits_payment_fine'],
									);

									$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
								}

								if(!empty($member_mandatory_savings) || $member_mandatory_savings > 0 || $member_mandatory_savings != ''){
									$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

									$data_debit =array(
										'journal_voucher_id'			=> $journal_voucher_id,
										'account_id'					=> $preferencecompany['account_cash_id'],
										'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
										'journal_voucher_amount'		=> $member_mandatory_savings,
										'journal_voucher_debit_amount'	=> $member_mandatory_savings,
										'account_id_default_status'		=> $account_id_default_status,
										'account_id_status'				=> 0,
										'journal_voucher_item_token'	=> $data['credits_payment_token'].'SW'.$preferencecompany['account_cash_id'],
									);

									$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debit);

									$account_id = $this->CoreMember_model->getAccountID($preferencecompany['mandatory_savings_id']);

									$account_id_default_status = $this->AcctCreditAccount_model->getAccountIDDefaultStatus($account_id);

									$data_credit =array(
										'journal_voucher_id'			=> $journal_voucher_id,
										'account_id'					=> $account_id,
										'journal_voucher_description'	=> 'SETORAN TUNAI '.$acctcashpayment_last['member_name'],
										'journal_voucher_amount'		=> $member_mandatory_savings,
										'journal_voucher_credit_amount'	=> $member_mandatory_savings,
										'account_id_status'				=> 1,
										'journal_voucher_item_token'	=> $data['credits_payment_token'].$account_id,
									);

									$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
								}

								$response['error_acctcreditspayment'] 		= FALSE;
								$response['error_msg_title'] 				= "Success";
								$response['error_msg'] 						= "Data Exist";
								$response['credits_payment_id']				= $credits_payment_id;
							}else{
								$response['error_acctcreditspayment'] 		= TRUE;
								$response['error_msg_title'] 				= "Error";
								$response['error_msg'] 						= "Data Exist";
								$response['credits_payment_id']				= $credits_payment_id;
							}
						} else {
							$acctcashpayment_last 				= $this->AcctCashPayment_model->AcctCashPaymentLast($data['created_id']);

							$credits_payment_id 				= $acctcashpayment_last['credits_payment_id'];

							$data_journal = array(
								'branch_id'						=> $auth['branch_id'],
								'journal_voucher_period' 		=> $journal_voucher_period,
								'journal_voucher_date'			=> date('Y-m-d'),
								'journal_voucher_title'			=> 'ANGSURAN TUNAI '.$acctcashpayment_last['credits_name'].' '.$acctcashpayment_last['member_name'],
								'journal_voucher_description'	=> 'ANGSURAN TUNAI '.$acctcashpayment_last['credits_name'].' '.$acctcashpayment_last['member_name'],
								'journal_voucher_token'			=> $data['credits_payment_token'],
								'transaction_module_id'			=> $transaction_module_id,
								'transaction_module_code'		=> $transaction_module_code,
								'transaction_journal_id' 		=> $acctcashpayment_last['credits_payment_id'],
								'transaction_journal_no' 		=> $acctcashpayment_last['credits_account_serial'],
								'created_id' 					=> $data['created_id'],
								'created_on' 					=> $data['created_on'],
							);
							
							$journal_voucher_token 				= $this->AcctCreditAccount_model->getJournalVoucherToken($data_journal['journal_voucher_token']);

							if($journal_voucher_token->num_rows()==0){
								$this->AcctCreditAccount_model->insertAcctJournalVoucher($data_journal);
							}

							$journal_voucher_id 				= $this->AcctCreditAccount_model->getJournalVoucherID($data['created_id']);

							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

							$data_debet = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_payment_amount'],
								'journal_voucher_debit_amount'	=> $data['credits_payment_amount'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token'	=> $data['credits_payment_token'].$preferencecompany['account_cash_id'],
							);

							$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

							if($journal_voucher_item_token->num_rows()==0){
								$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);
							}

							$receivable_account_id 				= $this->AcctCreditAccount_model->getReceivableAccountID($data['credits_id']);

							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($receivable_account_id);

							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $receivable_account_id,
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_payment_amount'],
								'journal_voucher_credit_amount'	=> $data['credits_payment_amount'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $data['credits_payment_token'].$receivable_account_id,
							);

							$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

							if($journal_voucher_item_token->num_rows()==0){
								$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
							}

							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_interest_id']);

							$data_credit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_interest_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_payment_interest'],
								'journal_voucher_credit_amount'	=> $data['credits_payment_interest'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $data['credits_payment_token'].$preferencecompany['account_interest_id'],
							);

							$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

							if($journal_voucher_item_token->num_rows()==0){
								$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
							}

							if($data['credits_payment_fine'] > 0){
								$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

								$data_debit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $preferencecompany['account_cash_id'],
									'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
									'journal_voucher_amount'		=> $data['credits_payment_fine'],
									'journal_voucher_debit_amount'	=> $data['credits_payment_fine'],
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 0,
									'journal_voucher_item_token'	=> $data['credits_payment_token'].'D'.$preferencecompany['account_cash_id'],
								);

								$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows()==0){
									$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debit);
								}

								$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_credits_payment_fine']);

								$data_credit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $preferencecompany['account_credits_payment_fine'],
									'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
									'journal_voucher_amount'		=> $data['credits_payment_fine'],
									'journal_voucher_credit_amount'	=> $data['credits_payment_fine'],
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 1,
									'journal_voucher_item_token'	=> $data['credits_payment_token'].$preferencecompany['account_credits_payment_fine'],
								);

								$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows()==0){
									$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
								}
							}

							if(!empty($member_mandatory_savings) || $member_mandatory_savings > 0 || $member_mandatory_savings != ''){
								$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

								$data_debit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $preferencecompany['account_cash_id'],
									'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
									'journal_voucher_amount'		=> $member_mandatory_savings,
									'journal_voucher_debit_amount'	=> $member_mandatory_savings,
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 0,
									'journal_voucher_item_token'	=> $data['credits_payment_token'].'SW'.$preferencecompany['account_cash_id'],
								);

								$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows()==0){
									$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debit);
								}

								$account_id = $this->CoreMember_model->getAccountID($preferencecompany['mandatory_savings_id']);

								$account_id_default_status = $this->AcctCreditAccount_model->getAccountIDDefaultStatus($account_id);

								$data_credit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'SETORAN TUNAI '.$acctcashpayment_last['member_name'],
									'journal_voucher_amount'		=> $member_mandatory_savings,
									'journal_voucher_credit_amount'	=> $member_mandatory_savings,
									'account_id_status'				=> 1,
									'journal_voucher_item_token'	=> $data['credits_payment_token'].$account_id,
								);

								$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows()==0){
									$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
								}
							}


							$response['error_acctcreditspayment'] 		= FALSE;
							$response['error_msg_title'] 				= "Success";
							$response['error_msg'] 						= "Data Exist";
							$response['credits_payment_id']				= $credits_payment_id;
						}
					} else{
						$response['error_acctcreditspayment'] 		= TRUE;
						$response['error_msg_title'] 				= "Password Salah";
						$response['error_msg'] 						= "Data Exist";
						$response['credits_payment_id']				= $credits_payment_id;
					}
				}
			} else {
				$response['error_acctcreditspayment'] 		= TRUE;
				$response['error_msg_title'] 				= "Error";
				$response['error_msg'] 						= "Data Exist";
				$response['credits_payment_id']				= $credits_payment_id;
			}

			echo json_encode($response);
		}

		public function processAddAcctCreditsPayment2(){
			$password 							= md5($this->input->post('password',true));
			$credits_principal_opening_balance 	= $this->input->post('credits_principal_opening_balance',true);
			$credits_margin_opening_balance 	= $this->input->post('credits_margin_opening_balance',true);
			$credits_payment_principal 			= $this->input->post('credits_payment_principal',true);
			$credits_payment_margin 			= $this->input->post('credits_payment_margin',true);

			$credits_principal_last_balance		= $credits_principal_opening_balance - $credits_payment_principal;
			$credits_margin_last_balance		= $credits_margin_opening_balance - $credits_payment_margin;
			
			$response = array(
				'error'										=> FALSE,
				'error_acctcreditspayment'					=> FALSE,
				'error_msg_title_acctcreditspayment'		=> "",
				'error_msg_acctcreditspayment'				=> "",
			);



			$total_angsuran = $this->input->post('credits_account_period', true);
			$angsuran_ke 	= $this->input->post('credits_payment_to', true);

			/*print_r("angsuran_ke ");
			print_r($angsuran_ke);*/


			$credits_account_id 					= $this->input->post('credits_account_id', true);

			$credits_account_payment_date 			= $this->Android_model->getCreditsAccountPaymentDate($credits_account_id);

			if($angsuran_ke < $total_angsuran){
				$credits_account_payment_date_old 	= $credits_account_payment_date;
				$credits_account_payment_date 		= date('Y-m-d', strtotime("+1 months", strtotime($credits_account_payment_date_old)));
			}

			$credits_payment_date 					= date('Y-m-d');

			$date1 									= date_create($credits_payment_date);
			$date2 									= date_create($credits_account_payment_date);

			$credits_payment_day_of_delay 			= date_diff($date1, $date2)->format('%d');

			$data = array(
				'branch_id'							=> $this->input->post('branch_id', true),
				'member_id'							=> $this->input->post('member_id', true),
				'credits_id'						=> $this->input->post('credits_id', true),
				'credits_account_id'				=> $this->input->post('credits_account_id', true),
				'credits_payment_date'				=> date('Y-m-d'),
				'credits_principal_opening_balance'	=> $this->input->post('credits_principal_opening_balance',true),
				'credits_margin_opening_balance'	=> $this->input->post('credits_margin_opening_balance',true),
				'credits_payment_principal'			=> $this->input->post('credits_payment_principal',true),
				'credits_payment_margin'			=> $this->input->post('credits_payment_margin',true),
				'credits_payment_amount'			=> $this->input->post('credits_payment_amount',true),
				'credits_principal_last_balance'	=> $credits_principal_last_balance,
				'credits_margin_last_balance'		=> $credits_margin_last_balance,
				'credits_account_payment_date'		=> $credits_account_payment_date,
				'credits_payment_to'				=> $this->input->post('credits_payment_to', true),
				'credits_payment_day_of_delay'		=> $credits_payment_day_of_delay,
				'credits_payment_status'			=> 1,
				'created_id'						=> $this->input->post('user_id', true),
				'created_on'						=> date('Y-m-d H:i:s'),
			);
			

			$transaction_module_code 	= 'ANGS';
			$transaction_module_id 		= $this->AcctCreditAccount_model->getTransactionModuleID($transaction_module_code);
			$preferencecompany 			= $this->AcctCreditAccount_model->getPreferenceCompany();





			if($response["error_acctcreditspayment"] == FALSE){
				if(!empty($data)){					
					if ($this->Android_model->getSystemUser($data['created_id'], $password)){
						if($this->AcctCashPayment_model->insert($data)){
							$updatedata=array(
								"credits_account_last_balance_principal" 	=>$data['credits_principal_last_balance'],
								"credits_account_last_balance_margin" 		=>$data['credits_margin_last_balance'],
								"credits_account_last_payment_date"			=>$data['credits_payment_date'],
								"credits_account_payment_date"				=>$credits_account_payment_date,
								"credits_account_payment_to"				=>$data['credits_payment_to'],
							);
							$this->AcctCreditAccount_model->updatedata($updatedata,$data['credits_account_id']);

							$acctcashpayment_last 	= $this->AcctCashPayment_model->AcctCashPaymentLast($data['created_id']);

							$credits_payment_id 	= $acctcashpayment_last['credits_payment_id'];
								
							$journal_voucher_period = date("Ym", strtotime($data['credits_payment_date']));
							
							$data_journal = array(
								'branch_id'						=> $data['branch_id'],
								'journal_voucher_period' 		=> $journal_voucher_period,
								'journal_voucher_date'			=> date('Y-m-d'),
								'journal_voucher_title'			=> 'ANGSURAN TUNAI '.$acctcashpayment_last['credits_name'].' '.$acctcashpayment_last['member_name'],
								'journal_voucher_description'	=> 'ANGSURAN TUNAI '.$acctcashpayment_last['credits_name'].' '.$acctcashpayment_last['member_name'],
								'transaction_module_id'			=> $transaction_module_id,
								'transaction_module_code'		=> $transaction_module_code,
								'transaction_journal_id' 		=> $acctcashpayment_last['credits_payment_id'],
								'transaction_journal_no' 		=> $acctcashpayment_last['credits_account_serial'],
								'created_id' 					=> $data['created_id'],
								'created_on' 					=> $data['created_on'],
							);

							// print_r($acctcashpayment_last);exit;
							
							$this->AcctCreditAccount_model->insertAcctJournalVoucher($data_journal);

							$journal_voucher_id = $this->AcctCreditAccount_model->getJournalVoucherID($data['created_id']);

							$account_id_default_status = $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

							$data_debet = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_payment_principal'],
								'journal_voucher_debit_amount'	=> $data['credits_payment_principal'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);

							$receivable_account_id = $this->AcctCreditAccount_model->getReceivableAccountID($data['credits_id']);

							$account_id_default_status = $this->AcctCreditAccount_model->getAccountIDDefaultStatus($receivable_account_id);

							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $receivable_account_id,
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_payment_principal'],
								'journal_voucher_credit_amount'	=> $data['credits_payment_principal'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);

							if($data['credits_id'] == $preferencecompany['deferred_margin_income']){

								$account_id_default_status = $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_deferred_margin_income']);

								$data_debet =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $preferencecompany['account_deferred_margin_income'],
									'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
									'journal_voucher_amount'		=> $data['credits_payment_margin'],
									'journal_voucher_debit_amount'	=> $data['credits_payment_margin'],
									'account_id_status'				=> 0,
								);

								$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);
							} else {
								$account_id_default_status = $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

								$data_debet =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $preferencecompany['account_cash_id'],
									'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
									'journal_voucher_amount'		=> $data['credits_payment_margin'],
									'journal_voucher_debit_amount'	=> $data['credits_payment_margin'],
									'account_id_status'				=> 0,
								);

								$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);
							}

							$income_account_id 			= $this->AcctCreditAccount_model->getIncomeAccountID($data['credits_id']);

							$account_id_default_status 	= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($income_account_id);

							$data_credit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $income_account_id,
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_payment_margin'],
								'journal_voucher_credit_amount'	=> $data['credits_payment_margin'],
								'account_id_status'				=> 1,
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);

							$response['error_acctcreditspayment'] 		= FALSE;
							$response['error_msg_title'] 				= "Success";
							$response['error_msg'] 						= "Data Exist";
							$response['credits_payment_id']				= $credits_payment_id;
						} else {
							$response['error_acctcreditspayment'] 		= TRUE;
							$response['error_msg_title'] 				= "Success";
							$response['error_msg'] 						= "Data Exist";
							$response['credits_payment_id']				= $credits_payment_id;
						}
					} else {
						$response['error_acctcreditspayment'] 		= TRUE;
						$response['error_msg_title'] 				= "Gagal";
						$response['error_msg'] 						= "Password Salah";
						$response['credits_payment_id']				= $credits_payment_id;
					}


				}

			} 
			
			echo json_encode($response);
		}

		public function printNoteAcctSavingsCashMutation(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'acctsavingscashmutation'		=> "",
			);

			$data = array(
				'savings_cash_mutation_id'		=> $this->input->post('savings_cash_mutation_id',true),
			);

			$preferencecompany = $this->Android_model->getPreferenceCompany();

			if($response["error"] == FALSE){
				$acctsavingscashmutationlist	= $this->AcctSavingsCashMutation_model->getAcctSavingsCashMutation_Detail($data['savings_cash_mutation_id']);

				/*print_r("acctsavingscashmutationlist ");
				print_r($acctsavingscashmutationlist);*/

				if(!$acctsavingscashmutationlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingscashmutationlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {						
						$acctsavingscashmutation[0]['company_name'] 					= $preferencecompany['company_name'];
						$acctsavingscashmutation[0]['member_name'] 						= $acctsavingscashmutationlist['member_name'];
						$acctsavingscashmutation[0]['savings_account_no']				= $acctsavingscashmutationlist['savings_account_no'];
						$acctsavingscashmutation[0]['member_address']					= $acctsavingscashmutationlist['member_address'];
						$acctsavingscashmutation[0]['savings_cash_mutation_amount']		= "Rp. ".number_format($acctsavingscashmutationlist['savings_cash_mutation_amount'], 2);
						$acctsavingscashmutation[0]['savings_cash_mutation_amount_str']	= numtotxt($acctsavingscashmutationlist['savings_cash_mutation_amount']);
						$acctsavingscashmutation[0]['branch_city']						= $acctsavingscashmutationlist['branch_city'];
						
						$response['error'] 						= FALSE;
						$response['error_msg_title'] 			= "Success";
						$response['error_msg'] 					= "Data Exist";
						$response['acctsavingscashmutation'] 	= $acctsavingscashmutation;
					}
				}
			}
			echo json_encode($response);
		}


		public function printNoteAcctCreditsPayment(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'						=> FALSE,
				'error_msg'					=> "",
				'error_msg_title'			=> "",
				'acctcreditspayment'		=> "",
			);

			$data = array(
				'credits_payment_id'		=> $this->input->post('credits_payment_id',true),
			);

			$preferencecompany = $this->Android_model->getPreferenceCompany();

			if($response["error"] == FALSE){
				$acctcreditspaymentlist	= $this->Android_model->getAcctCreditsPayment_Detail($data['credits_payment_id']);

				/*print_r("acctsavingscashmutationlist ");
				print_r($acctsavingscashmutationlist);*/

				if(!$acctcreditspaymentlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctcreditspaymentlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {						
						$acctcreditspayment[0]['company_name'] 						= $preferencecompany['company_name'];
						$acctcreditspayment[0]['member_name'] 						= $acctcreditspaymentlist['member_name'];
						$acctcreditspayment[0]['credits_account_serial']			= $acctcreditspaymentlist['credits_account_serial'];
						$acctcreditspayment[0]['member_address']					= $acctcreditspaymentlist['member_address'];
						$acctcreditspayment[0]['credits_payment_amount']			= "Rp. ".number_format($acctcreditspaymentlist['credits_payment_amount'], 2);
						$acctcreditspayment[0]['credits_payment_amount_str']		= numtotxt($acctcreditspaymentlist['credits_payment_amount']);
						$acctcreditspayment[0]['branch_city']						= $acctcreditspaymentlist['branch_city'];
						
						$response['error'] 						= FALSE;
						$response['error_msg_title'] 			= "Success";
						$response['error_msg'] 					= "Data Exist";
						$response['acctcreditspayment'] 		= $acctcreditspayment;
					}
				}
			}
			echo json_encode($response);
		}

		public function getDailyDashboard(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'						=> FALSE,
				'error_msg'					=> "",
				'error_msg_title'			=> "",
				'dailydashboard'			=> "",
			);

			$data = array(
				'user_id'					=> $this->input->post('user_id',true),
				'daily_dashboard_date'		=> date("Y-m-d"),
			);


			if($response["error"] == FALSE){

				$preferencecompany 		= $this->Android_model->getPreferenceCompany();
				$cash_deposit_id 		= $preferencecompany['cash_deposit_id'];
				$cash_withdrawal_id 	= $preferencecompany['cash_withdrawal_id'];

				$savings_cash_deposit_amount 		= $this->Android_model->getSavingsCashDepositAmount($data['user_id'], $data['daily_dashboard_date'], $cash_deposit_id);

				$savings_cash_withdrawal_amount 	= $this->Android_model->getSavingsCashDepositAmount($data['user_id'], $data['daily_dashboard_date'], $cash_withdrawal_id);

				$creditspaymentamount 				= $this->Android_model->getCreditsPaymentAmount($data['user_id'], $data['daily_dashboard_date']);

				$credits_payment_principal 			= $creditspaymentamount['credits_payment_principal'];

				$credits_payment_margin 			= $creditspaymentamount['credits_payment_margin'];

				if (empty($savings_cash_deposit_amount)){
					$savings_cash_deposit_amount = 0;
				}

				if (empty($savings_cash_withdrawal_amount)){
					$savings_cash_withdrawal_amount = 0;
				}

				if (empty($credits_payment_principal)){
					$credits_payment_principal = 0;
				}

				if (empty($credits_payment_margin)){
					$credits_payment_margin = 0;
				}
				
				$credits_payment_amount 			= $credits_payment_principal + $credits_payment_margin;

						
				$dailydashboard[0]['dashboard_setor_tunai'] 	= number_format($savings_cash_deposit_amount, 2);
				$dailydashboard[0]['dashboard_tarik_tunai']		= number_format($savings_cash_withdrawal_amount, 2);
				$dailydashboard[0]['dashboard_angsuran_tunai']	= number_format($credits_payment_amount, 2);
						
				$response['error'] 					= FALSE;
				$response['error_msg_title'] 		= "Success";
				$response['error_msg'] 				= "Data Exist";
				$response['dailydashboard'] 		= $dailydashboard;
				
			}
			echo json_encode($response);
		}

		public function getCoreMember_Detail(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'					=> FALSE,
				'error_msg'				=> "",
				'error_msg_title'		=> "",
				'corememberdetail'		=> "",
			);

			$data = array(
				'member_id'		=> $this->input->post('member_id',true),
				'user_id'		=> $this->input->post('user_id',true),
				'branch_id'		=> $this->input->post('branch_id',true),
			);

			$preferencecompany 		= $this->Android_model->getPreferenceCompany();

			$user_time_limit 		= strtotime(date("Y-m-d")." ".$preferencecompany['user_time_limit']);

			$now 					= strtotime(date("Y-m-d H:i:s"));

			if ($now <= $user_time_limit){
				if($response["error"] == FALSE){
					$systemuserdusun	= $this->Android_model->getSystemUserDusun($data['user_id']);

					$corememberdetaillist = $this->Android_model->getCoreMember_Detail($data['member_id'], $data['branch_id'], $systemuserdusun);
					

					if(!$corememberdetaillist){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Error Query Data";
					}else{
						if (empty($corememberdetaillist)){
							$response['error'] 				= TRUE;
							$response['error_msg_title'] 	= "No Data";
							$response['error_msg'] 			= "Data Does Not Exist";
						} else {
							$memberidentity = $this->configuration->MemberIdentity();

							/*print_r("memberidentity ");
							print_r($memberidentity);*/

							$member_address 	= $corememberdetaillist['member_address'].' '.$corememberdetaillist['province_name'].' '.$corememberdetaillist['city_name'].' '.$corememberdetaillist['kecamatan_name'];

							$corememberdetail[0]['branch_id']				= $corememberdetaillist['branch_id'];
							$corememberdetail[0]['branch_name']				= $corememberdetaillist['branch_name'];
							$corememberdetail[0]['member_id']				= $corememberdetaillist['member_id'];
							$corememberdetail[0]['member_no']				= $corememberdetaillist['member_no'];
							$corememberdetail[0]['member_name']				= $corememberdetaillist['member_name'];
							$corememberdetail[0]['member_address']			= $member_address;
							$corememberdetail[0]['member_identity_no']		= $corememberdetaillist['member_identity_no'];
								
							$response['error'] 					= FALSE;
							$response['error_msg_title'] 		= "Success";
							$response['error_msg'] 				= "Data Exist";
							$response['corememberdetail'] 		= $corememberdetail;
						}
					}
				}
			} else {
				$response['error'] 				= TRUE;
				$response['error_msg_title'] 	= "No Data";
				$response['error_msg'] 			= "Waktu Transaksi Sudah Habis";
			}

			echo json_encode($response);
		}

		public function getCoreMember_Login(){
			$response = array(
				'error'					=> FALSE,
				'error_msg'				=> "",
				'error_msg_title'		=> "",
				'corememberlogin'			=> "",
			);

			$data = array(
				'member_no' 		=> $this->input->post('member_no',true),
				'password' 			=> $this->input->post('password',true),
				'member_password' 	=> md5($this->input->post('password',true))
			);

			/* $data = array(
				'member_no' 		=> '01000034',
				'password' 			=> $this->input->post('password',true),
				'member_password' 	=> md5(2063625795)
			); */

			/*print_r("data ");
			print_r($data);*/
			
			if (empty($data)){
				$response['error'] 				= TRUE;
				$response['error_msg_title'] 	= "No Data";
				$response['error_msg'] 			= "Data Login is Empty";
			} else {
				if($response["error"] == FALSE){
					$verify 	= $this->Android_model->getCoreMember_Login($data['member_no'], $data['member_password']);

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
							$savings_id = $this->Android_model->getSavingsID(); 

							$corememberlogin[0]['member_id'] 			= $verify['member_id'];
							$corememberlogin[0]['member_no'] 			= $verify['member_no'];
							$corememberlogin[0]['member_name'] 			= $verify['member_name'];
							$corememberlogin[0]['branch_id'] 			= $verify['branch_id'];
							$corememberlogin[0]['savings_account_id'] 	= $verify['savings_account_id'];
							$corememberlogin[0]['savings_id'] 			= $savings_id;
							

							$response['error'] 				= FALSE;
							$response['error_msg_title'] 	= "Success";
							$response['error_msg'] 			= "Data Exist";
							$response['corememberlogin'] 	= $corememberlogin;
						}
					}
				}
			}

			echo json_encode($response);

		}

		public function getAcctSavingsCashMutation(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'acctsavingscashmutation'		=> "",
			);

			$data = array(
				'member_id'						=> $this->input->post('member_id',true),
				'cash_savings_mutation_date'	=> date("Y-m-d"),
			);


			if($response["error"] == FALSE){
				$preferencecompany = $this->Android_model->getPreferenceCompany();

				$data_mutation = array ($preferencecompany['cash_deposit_id'], $preferencecompany['cash_withdrawal_id']);

				$acctsavingscashmutationlist	= $this->AcctSavingsCashMutation_model->getAcctSavingsCashMutation_Member($data['member_id'], $data['cash_savings_mutation_date'], $data_mutation);


				if(!$acctsavingscashmutationlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingscashmutationlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {				
						foreach ($acctsavingscashmutationlist as $key => $val) {
							$acctsavingscashmutation[$key]['savings_code']					= $val['savings_code'];
							$acctsavingscashmutation[$key]['savings_name']					= $val['savings_name'];
							$acctsavingscashmutation[$key]['mutation_name']					= $val['mutation_name'];
							$acctsavingscashmutation[$key]['savings_account_no']			= $val['savings_account_no'];
							$acctsavingscashmutation[$key]['savings_cash_mutation_date']	= tgltoview($val['savings_cash_mutation_date']);
							$acctsavingscashmutation[$key]['savings_cash_mutation_amount']	= "Rp. ".number_format($val['savings_cash_mutation_amount'], 2);
							$acctsavingscashmutation[$key]['savings_account_last_balance']	= "Rp. ".number_format($val['savings_account_last_balance'], 2);
						}
						
						$response['error'] 						= FALSE;
						$response['error_msg_title'] 			= "Success";
						$response['error_msg'] 					= "Data Exist";
						$response['acctsavingscashmutation'] 	= $acctsavingscashmutation;
					}
				}
			}
			echo json_encode($response);
		}

		public function getAcctCreditsPayment(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'						=> FALSE,
				'error_msg'					=> "",
				'error_msg_title'			=> "",
				'acctcreditspayment'		=> "",
			);

			$data = array(
				'member_id'					=> $this->input->post('member_id',true),
				'credits_payment_date'		=> date("Y-m-d"),
			);



			if($response["error"] == FALSE){
				$acctcreditspaymentlist	= $this->Android_model->getAcctCreditsPayment_Member($data['member_id'], $data['credits_payment_date']);

				if(!$acctcreditspaymentlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctcreditspaymentlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {	
						foreach ($acctcreditspaymentlist as $key => $val) {
							$credits_payment_amount = $val['credits_payment_principal'] + $val['credits_payment_interest'];

							$acctcreditspayment[$key]['credits_id']					= $val['credits_id'];
							$acctcreditspayment[$key]['credits_code']				= $val['credits_code'];
							$acctcreditspayment[$key]['credits_name']				= $val['credits_name'];
							$acctcreditspayment[$key]['credits_account_id']			= $val['credits_account_id'];
							$acctcreditspayment[$key]['credits_account_serial']		= $val['credits_account_serial'];
							$acctcreditspayment[$key]['credits_payment_amount']		= $credits_payment_amount;
							$acctcreditspayment[$key]['credits_payment_date']		= tgltoview($val['credits_payment_date']);
						}					

						
						$response['error'] 						= FALSE;
						$response['error_msg_title'] 			= "Success";
						$response['error_msg'] 					= "Data Exist";
						$response['acctcreditspayment'] 		= $acctcreditspayment;
					}
				}
			}
			echo json_encode($response);
		}

		public function getAcctSavingsAccountFromDetail(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'acctsavingsaccountfrom'		=> "",
			);

			$data = array(
				'member_id'			=> $this->input->post('member_id',true),
				'savings_id'		=> $this->input->post('savings_id',true),
			);


			if($response["error"] == FALSE){
				$acctsavingsaccountlist = $this->Android_model->getAcctSavingsAccount_Detail($data['member_id'], $data['savings_id']);

				if(!$acctsavingsaccountlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingsaccountlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {
						$acctsavingsaccountfrom[0]['savings_account_id'] 			= $acctsavingsaccountlist['savings_account_id'];
						$acctsavingsaccountfrom[0]['savings_id']					= $acctsavingsaccountlist['savings_id'];
						$acctsavingsaccountfrom[0]['savings_code']					= $acctsavingsaccountlist['savings_code'];
						$acctsavingsaccountfrom[0]['savings_name']					= $acctsavingsaccountlist['savings_name'];
						$acctsavingsaccountfrom[0]['branch_id']						= $acctsavingsaccountlist['branch_id'];
						$acctsavingsaccountfrom[0]['savings_account_no']			= $acctsavingsaccountlist['savings_account_no'];
						$acctsavingsaccountfrom[0]['savings_account_last_balance']	= $acctsavingsaccountlist['savings_account_last_balance'];
						
						$response['error'] 						= FALSE;
						$response['error_msg_title'] 			= "Success";
						$response['error_msg'] 					= "Data Exist";
						$response['acctsavingsaccountfrom'] 	= $acctsavingsaccountfrom;
					}
				}
			}
			echo json_encode($response);
		}


		public function getAcctSavingsAccountToDetail(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'						=> FALSE,
				'error_msg'					=> "",
				'error_msg_title'			=> "",
				'acctsavingsaccountto'		=> "",
			);

			$data = array(
				'savings_account_id'		=> $this->input->post('savings_account_id',true),
			);

			if($response["error"] == FALSE){
				$acctsavingsaccountlist = $this->Android_model->getAcctSavingsAccount_DetailAccount($data['savings_account_id']);

				
				if(!$acctsavingsaccountlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingsaccountlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {
						$acctsavingsaccountto[0]['savings_account_id'] 				= $acctsavingsaccountlist['savings_account_id'];
						$acctsavingsaccountto[0]['savings_id']						= $acctsavingsaccountlist['savings_id'];
						$acctsavingsaccountto[0]['savings_code']					= $acctsavingsaccountlist['savings_code'];
						$acctsavingsaccountto[0]['savings_name']					= $acctsavingsaccountlist['savings_name'];
						$acctsavingsaccountto[0]['branch_id']						= $acctsavingsaccountlist['branch_id'];
						$acctsavingsaccountto[0]['member_id']						= $acctsavingsaccountlist['member_id'];
						$acctsavingsaccountto[0]['member_no']						= $acctsavingsaccountlist['member_no'];
						$acctsavingsaccountto[0]['member_name']						= $acctsavingsaccountlist['member_name'];
						$acctsavingsaccountto[0]['savings_account_no']				= $acctsavingsaccountlist['savings_account_no'];
						$acctsavingsaccountto[0]['savings_account_last_balance']	= $acctsavingsaccountlist['savings_account_last_balance'];
						
						$response['error'] 					= FALSE;
						$response['error_msg_title'] 		= "Success";
						$response['error_msg'] 				= "Data Exist";
						$response['acctsavingsaccountto'] 	= $acctsavingsaccountto;
					}
				}
			}
			echo json_encode($response);
		}

		public function processAddAcctSavingsTransferMutation(){
			$auth = $this->session->userdata('auth');

			$member_id			= $this->input->post('member_from_id', true);
			$member_password	= md5($this->input->post('member_password', true));

			$data = array(
				'branch_id'								=> $this->input->post('branch_from_id', true),
				'savings_transfer_mutation_date'		=> date('Y-m-d'),
				'savings_transfer_mutation_amount'		=> $this->input->post('savings_transfer_mutation_amount', true),
				'savings_transfer_mutation_status'		=> $this->input->post('savings_transfer_mutation_status', true),
				'operated_name'							=> $this->input->post('username', true),
				'created_id'							=> $this->input->post('user_id', true),
				'created_on'							=> date('Y-m-d H:i:s'),
			);

			$response = array(
				'error'											=> FALSE,
				'error_acctsavingstransfermutation'				=> FALSE,
				'error_msg_title_acctsavingstransfermutation'	=> "",
				'error_msg_acctsavingstransfermutation'			=> "",
			);

			if($response["error_acctsavingstransfermutation"] == FALSE){
				if(!empty($data)){	
					if ($this->Android_model->getMemberPassword($member_id, $member_password)){
						if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutation($data)){
							$transaction_module_code 	= "MbAYAR";

							$transaction_module_id 		= $this->AcctSavingsTransferMutation_model->getTransactionModuleID($transaction_module_code);

							$acctsavingstr_last 		= $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutation_Last($data['created_id']);
								
							$journal_voucher_period 	= date("Ym", strtotime($data['savings_transfer_mutation_date']));
							
							$data_journal = array(
								'branch_id'						=> $data['branch_id'],
								'journal_voucher_period' 		=> $journal_voucher_period,
								'journal_voucher_date'			=> date('Y-m-d'),
								'journal_voucher_title'			=> 'TRANSFER ANTAR REKENING '.$acctsavingstr_last['member_name'],
								'journal_voucher_description'	=> 'TRANSFER ANTAR REKENING '.$acctsavingstr_last['member_name'],
								'transaction_module_id'			=> $transaction_module_id,
								'transaction_module_code'		=> $transaction_module_code,
								'transaction_journal_id' 		=> $acctsavingstr_last['savings_transfer_mutation_id'],
								'transaction_journal_no' 		=> $acctsavingstr_last['savings_account_no'],
								'created_id' 					=> $data['created_id'],
								'created_on' 					=> $data['created_on'],
							);
							
							$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucher($data_journal);

							$journal_voucher_id 			= $this->AcctSavingsTransferMutation_model->getJournalVoucherID($data['created_id']);

							$savings_transfer_mutation_id 	= $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data['created_on']);

							$preferencecompany 				= $this->AcctSavingsTransferMutation_model->getPreferenceCompany();

							$datafrom = array (
								'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
								'savings_account_id'						=> $this->input->post('savings_account_from_id', true),
								'savings_id'								=> $this->input->post('savings_from_id', true),
								'member_id'									=> $this->input->post('member_from_id', true),
								'branch_id'									=> $this->input->post('branch_from_id', true),
								'mutation_id'								=> $preferencecompany['account_savings_transfer_from_id'],
								'savings_account_opening_balance'			=> $this->input->post('savings_account_from_opening_balance', true),
								'savings_transfer_mutation_from_amount'		=> $this->input->post('savings_transfer_mutation_amount', true),
								'savings_account_last_balance'				=> $this->input->post('savings_account_from_last_balance', true),
							);

							$member_name = $this->AcctSavingsTransferMutation_model->getMemberName($datafrom['member_id']);

							if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationFrom($datafrom)){
								$account_id = $this->AcctSavingsTransferMutation_model->getAccountID($datafrom['savings_id']);

								$account_id_default_status = $this->AcctSavingsTransferMutation_model->getAccountIDDefaultStatus($account_id);

								$data_debit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'NOTA DEBET '.$member_name,
									'journal_voucher_amount'		=> $data['savings_transfer_mutation_amount'],
									'journal_voucher_debit_amount'	=> $data['savings_transfer_mutation_amount'],
									'account_id_status'				=> 1,
								);

								$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucherItem($data_debit);
							}

							$datato = array (
								'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
								'savings_account_id'						=> $this->input->post('savings_account_to_id', true),
								'savings_id'								=> $this->input->post('savings_to_id', true),
								'member_id'									=> $this->input->post('member_to_id', true),
								'branch_id'									=> $this->input->post('branch_to_id', true),
								'mutation_id'								=> $preferencecompany['account_savings_transfer_to_id'],
								'savings_account_opening_balance'			=> $this->input->post('savings_account_to_opening_balance', true),
								'savings_transfer_mutation_to_amount'		=> $this->input->post('savings_transfer_mutation_amount', true),
								'savings_account_last_balance'				=> $this->input->post('savings_account_to_last_balance', true),
							);

							$member_name = $this->AcctSavingsTransferMutation_model->getMemberName($datato['member_id']);

							if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationTo($datato)){
								$account_id = $this->AcctSavingsTransferMutation_model->getAccountID($datato['savings_id']);

								$account_id_default_status = $this->AcctSavingsTransferMutation_model->getAccountIDDefaultStatus($account_id);

								$data_credit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'NOTA KREDIT '.$member_name,
									'journal_voucher_amount'		=> $data['savings_transfer_mutation_amount'],
									'journal_voucher_credit_amount'	=> $data['savings_transfer_mutation_amount'],
									'account_id_status'				=> 0,
								);

								$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucherItem($data_credit);
							}

							$response['error_acctsavingstransfermutation'] 	= FALSE;
							$response['error_msg_title'] 					= "Success";
							$response['error_msg'] 							= "Data Exist";
							$response['savings_transfer_mutation_id'] 		= $savings_transfer_mutation_id;
						}else{
							$response['error_acctsavingstransfermutation'] 	= TRUE;
							$response['error_msg_title'] 					= "Fail";
							$response['error_msg'] 							= "Data Exist";
							$response['savings_transfer_mutation_id'] 		= $savings_transfer_mutation_id;
						}
					} else {
						$response['error_acctsavingstransfermutation'] 	= TRUE;
						$response['error_msg_title'] 					= "Fail";
						$response['error_msg'] 							= "Password Salah";
						$response['savings_transfer_mutation_id'] 		= 0;		
					}
				}
			}

			echo json_encode($response);

		}

		public function printAcctSavingsTransferMutationFrom(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'									=> FALSE,
				'error_msg'								=> "",
				'error_msg_title'						=> "",
				'acctsavingstransfermutationfrom'		=> "",
			);

			$data = array(
				'savings_transfer_mutation_id'		=> $this->input->post('savings_transfer_mutation_id',true),
			);

			$preferencecompany = $this->Android_model->getPreferenceCompany();

			if($response["error"] == FALSE){
				$acctsavingstransfermutationlist	= $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutationFrom_DetailPrint($data['savings_transfer_mutation_id']);

				/*print_r("acctsavingscashmutationlist ");
				print_r($acctsavingscashmutationlist);*/

				if(!$acctsavingstransfermutationlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingstransfermutationlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {						
						$acctsavingstransfermutationfrom[0]['company_name'] 							= $preferencecompany['company_name'];
						$acctsavingstransfermutationfrom[0]['member_name'] 								= $acctsavingstransfermutationlist['member_name'];
						$acctsavingstransfermutationfrom[0]['savings_account_no']						= $acctsavingstransfermutationlist['savings_account_no'];
						$acctsavingstransfermutationfrom[0]['member_address']							= $acctsavingstransfermutationlist['member_address'];
						$acctsavingstransfermutationfrom[0]['savings_transfer_mutation_amount']			= "Rp. ".number_format($acctsavingstransfermutationlist['savings_transfer_mutation_from_amount'], 2);
						$acctsavingstransfermutationfrom[0]['savings_transfer_mutation_amount_str']		= numtotxt($acctsavingstransfermutationlist['savings_transfer_mutation_from_amount']);

						
						$response['error'] 								= FALSE;
						$response['error_msg_title'] 					= "Success";
						$response['error_msg'] 							= "Data Exist";
						$response['acctsavingstransfermutationfrom'] 	= $acctsavingstransfermutationfrom;
					}
				}
			}
			echo json_encode($response);
		}


		public function printAcctSavingsTransferMutationTo(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'								=> FALSE,
				'error_msg'							=> "",
				'error_msg_title'					=> "",
				'acctsavingstransfermutationto'		=> "",
			);

			$data = array(
				'savings_transfer_mutation_id'		=> $this->input->post('savings_transfer_mutation_id',true),
			);

			$preferencecompany = $this->Android_model->getPreferenceCompany();

			if($response["error"] == FALSE){
				$acctsavingstransfermutationlist	= $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutationTo_DetailPrint($data['savings_transfer_mutation_id']);

				/*print_r("acctsavingscashmutationlist ");
				print_r($acctsavingscashmutationlist);*/

				if(!$acctsavingstransfermutationlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingstransfermutationlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {						
						$acctsavingstransfermutationto[0]['company_name'] 								= $preferencecompany['company_name'];
						$acctsavingstransfermutationto[0]['member_name'] 								= $acctsavingstransfermutationlist['member_name'];
						$acctsavingstransfermutationto[0]['savings_account_no']							= $acctsavingstransfermutationlist['savings_account_no'];
						$acctsavingstransfermutationto[0]['member_address']								= $acctsavingstransfermutationlist['member_address'];
						$acctsavingstransfermutationto[0]['savings_transfer_mutation_amount']			= "Rp. ".number_format($acctsavingstransfermutationlist['savings_transfer_mutation_to_amount'], 2);
						$acctsavingstransfermutationto[0]['savings_transfer_mutation_amount_str']		= numtotxt($acctsavingstransfermutationlist['savings_transfer_mutation_to_amount']);

						
						$response['error'] 							= FALSE;
						$response['error_msg_title'] 				= "Success";
						$response['error_msg'] 						= "Data Exist";
						$response['acctsavingstransfermutationto'] 	= $acctsavingstransfermutationto;
					}
				}
			}
			echo json_encode($response);
		}


		public function getAcctSavingsTransferMutationFrom(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'								=> FALSE,
				'error_msg'							=> "",
				'error_msg_title'					=> "",
				'acctsavingstransfermutationfrom'	=> "",
			);

			$data = array(
				'member_id'							=> $this->input->post('member_id',true),
				'savings_transfer_mutation_status'	=> $this->input->post('savings_transfer_mutation_status',true),
				'savings_transfer_mutation_date'	=> date("Y-m-d"),
			);


			if($response["error"] == FALSE){
				$preferencecompany = $this->Android_model->getPreferenceCompany();

				$data_mutation = array ($preferencecompany['account_savings_transfer_from_id'], $preferencecompany['account_savings_transfer_to_id']);

				$acctsavingstransfermutationlist	= $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutationFrom_Member($data['member_id'], $data['savings_transfer_mutation_date'], $data_mutation, $data['savings_transfer_mutation_status']);


				if(!$acctsavingstransfermutationlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingstransfermutationlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {				
						foreach ($acctsavingstransfermutationlist as $key => $val) {
							$acctsavingstransfermutationfrom[$key]['savings_code']						= $val['savings_code'];
							$acctsavingstransfermutationfrom[$key]['savings_name']						= $val['savings_name'];
							$acctsavingstransfermutationfrom[$key]['mutation_name']						= $val['mutation_name'];
							$acctsavingstransfermutationfrom[$key]['savings_account_no']				= $val['savings_account_no'];
							$acctsavingstransfermutationfrom[$key]['savings_transfer_mutation_date']	= tgltoview($val['savings_transfer_mutation_date']);
							$acctsavingstransfermutationfrom[$key]['savings_transfer_mutation_amount']	= $val['savings_transfer_mutation_from_amount'];
						}
						
						$response['error'] 								= FALSE;
						$response['error_msg_title'] 					= "Success";
						$response['error_msg'] 							= "Data Exist";
						$response['acctsavingstransfermutationfrom'] 	= $acctsavingstransfermutationfrom;
					}
				}
			}
			echo json_encode($response);
		}


		public function getAcctSavingsTransferMutationTo(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'								=> FALSE,
				'error_msg'							=> "",
				'error_msg_title'					=> "",
				'acctsavingstransfermutationto'		=> "",
			);

			$data = array(
				'member_id'							=> $this->input->post('member_id',true),
				'savings_transfer_mutation_date'	=> date("Y-m-d"),
			);


			if($response["error"] == FALSE){
				$preferencecompany = $this->Android_model->getPreferenceCompany();

				$data_mutation = array ($preferencecompany['account_savings_transfer_from_id'], $preferencecompany['account_savings_transfer_to_id']);

				$acctsavingstransfermutationlist	= $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutationTo_Member($data['member_id'], $data['savings_transfer_mutation_date'], $data_mutation);


				if(!$acctsavingstransfermutationlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingstransfermutationlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {				
						foreach ($acctsavingstransfermutationlist as $key => $val) {
							$acctsavingstransfermutationto[$key]['savings_code']						= $val['savings_code'];
							$acctsavingstransfermutationto[$key]['savings_name']						= $val['savings_name'];
							$acctsavingstransfermutationto[$key]['mutation_name']						= $val['mutation_name'];
							$acctsavingstransfermutationto[$key]['savings_account_no']					= $val['savings_account_no'];
							$acctsavingstransfermutationto[$key]['savings_transfer_mutation_date']		= tgltoview($val['savings_transfer_mutation_date']);	
							$acctsavingstransfermutationto[$key]['savings_transfer_mutation_amount']	= $val['savings_transfer_mutation_to_amount'];
						}
						
						$response['error'] 							= FALSE;
						$response['error_msg_title'] 				= "Success";
						$response['error_msg'] 						= "Data Exist";
						$response['acctsavingstransfermutationto'] 	= $acctsavingstransfermutationto;
					}
				}
			}
			echo json_encode($response);
		}

		public function processEditSystemUserPassword(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'user_id'			=> $this->input->post('user_id', true),
				'password'			=> md5($this->input->post('password', true)),
				'new_password'		=> md5($this->input->post('new_password', true)),
			);


			$response = array(
				'error'									=> FALSE,
				'error_systemuserpassword'				=> FALSE,
				'error_msg_title_systemuserpassword'	=> "",
				'error_msg_systemuserpassword'			=> "",
			);

			if($response["error_systemuserpassword"] == FALSE){
				if ($data_systemuser = $this->Android_model->getSystemUserPassword($data['user_id'], $data['password'])){
					$dataupdate = array(
						'user_id'			=>	$data['user_id'],
						'password'			=>	$data['new_password'],
					);

					if ($this->Android_model->updateSystemUser($dataupdate)){

						$response['error'] 				= FALSE;
						$response['error_msg_title'] 	= "Ganti Password Berhasil";
						$response['error_msg'] 			= "Data Berhasil";		
					} else {
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "Ganti Password Gagal";
						$response['error_msg'] 			= "Data Gagal";		
					}
				} else {
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "Ganti Password Gagal";
					$response['error_msg'] 			= "Password User Salah";		
				}
				

			} 

			echo json_encode($response);

		}


		public function processEditCoreMemberPassword(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'member_id'					=> $this->input->post('member_id', true),
				'member_password'			=> md5($this->input->post('member_password', true)),
				'member_new_password'		=> md5($this->input->post('member_new_password', true)),
			);

			/*print_r("data ");
			print_r($data);*/

			$response = array(
				'error'									=> FALSE,
				'error_corememberpassword'				=> FALSE,
				'error_msg_title_corememberpassword'	=> "",
				'error_msg_corememberpassword'			=> "",
			);

			if($response["error_corememberpassword"] == FALSE){
				if ($data_coremember = $this->Android_model->getCoreMemberPassword($data['member_id'], $data['member_password'])){
					$dataupdate = array(
						'member_id'				=>	$data['member_id'],
						'member_password'		=>	$data['member_new_password'],
					);

					if ($this->Android_model->updateCoreMember($dataupdate)){

						$response['error'] 				= FALSE;
						$response['error_msg_title'] 	= "Ganti Password Berhasil";
						$response['error_msg'] 			= "Data Berhasil";		
					} else {
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "Ganti Password Gagal";
						$response['error_msg'] 			= "Data Gagal";		
					}
				} else {
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "Ganti Password Gagal";
					$response['error_msg'] 			= "Password Member Salah";		
				}
				

			} 

			echo json_encode($response);

		}

		public function getCoreMemberNo_Detail(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'					=> FALSE,
				'error_msg'				=> "",
				'error_msg_title'		=> "",
				'corememberdetail'		=> "",
			);

			$data = array(
				'member_no'		=> $this->input->post('member_no',true),
				'user_id'		=> $this->input->post('user_id',true),
				'branch_id'		=> $this->input->post('branch_id',true),
			);

			$preferencecompany 		= $this->Android_model->getPreferenceCompany();

			$user_time_limit 		= strtotime(date("Y-m-d")." ".$preferencecompany['user_time_limit']);

			$now 					= strtotime(date("Y-m-d H:i:s"));

			if ($now <= $user_time_limit){
				if($response["error"] == FALSE){
					$systemuserdusun		= $this->Android_model->getSystemUserDusun($data['user_id']);

					$corememberdetaillist 	= $this->Android_model->getCoreMemberNo_Detail($data['member_no'], $data['branch_id'], $systemuserdusun);

					if(!$corememberdetaillist){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Error Query Data";
					}else{
						if (empty($corememberdetaillist)){
							$response['error'] 				= TRUE;
							$response['error_msg_title'] 	= "No Data";
							$response['error_msg'] 			= "Data Does Not Exist";
						} else {
							$memberidentity = $this->configuration->MemberIdentity();

							/*print_r("memberidentity ");
							print_r($memberidentity);*/

							$member_address 	= $corememberdetaillist['member_address'].' '.$corememberdetaillist['province_name'].' '.$corememberdetaillist['city_name'].' '.$corememberdetaillist['kecamatan_name'];

							$corememberdetail[0]['branch_id']				= $corememberdetaillist['branch_id'];
							$corememberdetail[0]['branch_name']				= $corememberdetaillist['branch_name'];
							$corememberdetail[0]['member_id']				= $corememberdetaillist['member_id'];
							$corememberdetail[0]['member_no']				= $corememberdetaillist['member_no'];
							$corememberdetail[0]['member_name']				= $corememberdetaillist['member_name'];
							$corememberdetail[0]['member_address']			= $member_address;
							$corememberdetail[0]['member_identity_no']		= $corememberdetaillist['member_identity_no'];
								
							$response['error'] 					= FALSE;
							$response['error_msg_title'] 		= "Success";
							$response['error_msg'] 				= "Data Exist";
							$response['corememberdetailno'] 	= $corememberdetail;
						}
					}
				}
			} else {
				$response['error'] 				= TRUE;
				$response['error_msg_title'] 	= "No Data";
				$response['error_msg'] 			= "Waktu Transaksi Sudah Habis";
			}
			echo json_encode($response);
		}


		public function getAcctSavingsAccountDetailNo(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'								=> FALSE,
				'error_msg'							=> "",
				'error_msg_title'					=> "",
				'acctsavingsaccountdetailno'		=> "",
			);

			$data = array(
				'branch_id'				=> $this->input->post('branch_id',true),
				'member_id'				=> $this->input->post('member_id',true),
				'savings_account_no'	=> $this->input->post('savings_account_no',true),
			);

			$preferencecompany 		= $this->Android_model->getPreferenceCompany();

			$user_time_limit 		= strtotime(date("Y-m-d")." ".$preferencecompany['user_time_limit']);

			$now 					= strtotime(date("Y-m-d H:i:s"));

			/*print_r("now ");
			print_r($now);
			print_r("<BR>");

			print_r("user_time_limit ");
			print_r($user_time_limit);
			print_r("<BR>");*/

			if ($now <= $user_time_limit){
				if($response["error"] == FALSE){
					$acctsavingsaccountdetailnolist = $this->Android_model->getAcctSavingsAccountDetailNo($data['savings_account_no']);

					if(!$acctsavingsaccountdetailnolist){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Error Query Data";
					}else{
						if (empty($acctsavingsaccountdetailnolist)){
							$response['error'] 				= TRUE;
							$response['error_msg_title'] 	= "No Data";
							$response['error_msg'] 			= "Data Does Not Exist";
						} else {
							
                            $memberidentity 	= $this->configuration->MemberIdentity();

							$member_address 	= $acctsavingsaccountdetailnolist['member_address'].' '.$acctsavingsaccountdetailnolist['province_name'].' '.$acctsavingsaccountdetailnolist['city_name'].' '.$acctsavingsaccountdetailnolist['kecamatan_name'];

							$acctsavingsaccountdetailno[0]['member_id'] 							= $acctsavingsaccountdetailnolist['member_id'];
							$acctsavingsaccountdetailno[0]['member_no'] 							= $acctsavingsaccountdetailnolist['member_no'];
							$acctsavingsaccountdetailno[0]['member_name'] 							= $acctsavingsaccountdetailnolist['member_name'];
							$acctsavingsaccountdetailno[0]['member_address'] 						= $member_address;
							$acctsavingsaccountdetailno[0]['member_identity_no'] 					= $acctsavingsaccountdetailnolist['member_identity_no'];
							$acctsavingsaccountdetailno[0]['savings_account_id'] 					= $acctsavingsaccountdetailnolist['savings_account_id'];
							$acctsavingsaccountdetailno[0]['savings_id']							= $acctsavingsaccountdetailnolist['savings_id'];
							$acctsavingsaccountdetailno[0]['savings_code']							= $acctsavingsaccountdetailnolist['savings_code'];
							$acctsavingsaccountdetailno[0]['savings_name']							= $acctsavingsaccountdetailnolist['savings_name'];
							$acctsavingsaccountdetailno[0]['savings_account_no']					= $acctsavingsaccountdetailnolist['savings_account_no'];
							$acctsavingsaccountdetailno[0]['savings_account_first_deposit_amount']	= $acctsavingsaccountdetailnolist['savings_account_first_deposit_amount'];
							$acctsavingsaccountdetailno[0]['savings_account_last_balance']			= $acctsavingsaccountdetailnolist['savings_account_last_balance'];
							
							
							$response['error'] 							= FALSE;
							$response['error_msg_title'] 				= "Success";
							$response['error_msg'] 						= "Data Exist";
							$response['acctsavingsaccountdetailno'] 	= $acctsavingsaccountdetailno;
						}
					}
				}
			} else {
				$response['error'] 				= TRUE;
				$response['error_msg_title'] 	= "No Data";
				$response['error_msg'] 			= "Waktu Transaksi Sudah Habis";
			}

			echo json_encode($response);
		}


		public function getAcctCreditsAccountDetailNo(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(	
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'acctcreditsaccountdetailno'	=> "",
			);

			$data = array(
				'branch_id'					=> $this->input->post('branch_id',true),
				'member_id'					=> $this->input->post('member_id',true),
				'credits_account_serial'	=> $this->input->post('credits_account_no',true),
			);

			/* $data = array(
				'credits_account_serial' 	=> '0105700002'
			); */

			$preferencecompany 		= $this->Android_model->getPreferenceCompany();

			$user_time_limit 		= strtotime(date("Y-m-d")." ".$preferencecompany['user_time_limit']);

			$now 					= strtotime(date("Y-m-d H:i:s"));

			if ($now <= $user_time_limit){

				if($response["error"] == FALSE){
					$acctcreditsaccountseriallist = $this->Android_model->getAcctCreditsAccountDetailSerial($data['credits_account_serial']);

					/*print_r("acctcreditsaccountseriallist ");
					print_r($acctcreditsaccountseriallist);*/

					if(!$acctcreditsaccountseriallist){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Error Query Data";
					}else{
						if (empty($acctcreditsaccountseriallist)){
							$response['error'] 				= TRUE;
							$response['error_msg_title'] 	= "No Data";
							$response['error_msg'] 			= "Data Does Not Exist";
						} else {
							$memberidentity 						= $this->configuration->MemberIdentity();

							$member_address 						= $acctcreditsaccountseriallist['member_address'].' '.$acctcreditsaccountseriallist['province_name'].' '.$acctcreditsaccountseriallist['city_name'].' '.$acctcreditsaccountseriallist['kecamatan_name'];

							$credits_account_id 	= $acctcreditsaccountseriallist['credits_account_id'];

							$credits_payment_date 	= date('Y-m-d');
							$date1 = date_create($credits_payment_date);
							$date2 = date_create($acctcreditsaccountseriallist['credits_account_payment_date']);

							if($date1 > $date2){
								$interval                       = $date1->diff($date2);
						    	$credits_payment_day_of_delay   = $interval->days;
							} else {
								$credits_payment_day_of_delay 	= 0;
							}
							

							// print_r($credits_payment_day_of_delay);

							$credits_account_payment_to 		= $acctcreditsaccountseriallist['credits_account_payment_to'] + 1;

							$credits_payment_fine_amount 		= (($acctcreditsaccountseriallist['credits_account_payment_amount'] * $acctcreditsaccountseriallist['credits_fine']) / 100 ) * $credits_payment_day_of_delay;

							$credits_account_accumulated_fines 	= $acctcreditsaccountseriallist['credits_account_accumulated_fines'] + $credits_payment_fine_amount;

							if($acctcreditsaccountseriallist['payment_type_id'] == 1){
								$angsuranpokok 		= $acctcreditsaccountseriallist['credits_account_principal_amount'];

								$angsuranbunga 	 	= $acctcreditsaccountseriallist['credits_account_interest_amount'];

							} else if($acctcreditsaccountseriallist['payment_type_id'] == 2){
								$angsuranbunga 	 	= ($acctcreditsaccountseriallist['credits_account_last_balance'] * $acctcreditsaccountseriallist['credits_account_interest']) /100;

								$angsuranpokok 		= $acctcreditsaccountseriallist['credits_account_payment_amount'] - $angsuranbunga;
							}

							

							$acctcreditsaccountdetailno[0]['member_id'] 								= $acctcreditsaccountseriallist['member_id'];
							$acctcreditsaccountdetailno[0]['member_no'] 								= $acctcreditsaccountseriallist['member_no'];
							$acctcreditsaccountdetailno[0]['member_name'] 								= $acctcreditsaccountseriallist['member_name'];
							$acctcreditsaccountdetailno[0]['member_address'] 							= $member_address;
							$acctcreditsaccountdetailno[0]['member_identity_no'] 						= $acctcreditsaccountseriallist['member_identity_no'];
							$acctcreditsaccountdetailno[0]['credits_account_id'] 						= $acctcreditsaccountseriallist['credits_account_id'];
							$acctcreditsaccountdetailno[0]['credits_id']								= $acctcreditsaccountseriallist['credits_id'];
							$acctcreditsaccountdetailno[0]['credits_code']								= $acctcreditsaccountseriallist['credits_code'];
							$acctcreditsaccountdetailno[0]['credits_name']								= $acctcreditsaccountseriallist['credits_name'];
							$acctcreditsaccountdetailno[0]['credits_account_serial']					= $acctcreditsaccountseriallist['credits_account_serial'];
							$acctcreditsaccountdetailno[0]['credits_installment_date']					= date("d-m-Y");
							$acctcreditsaccountdetailno[0]['credits_account_payment_date']				= tgltoview($acctcreditsaccountseriallist['credits_account_payment_date']);
							$acctcreditsaccountdetailno[0]['credits_account_amount']					= $acctcreditsaccountseriallist['credits_account_amount'];
							$acctcreditsaccountdetailno[0]['credits_account_last_balance']				= $acctcreditsaccountseriallist['credits_account_last_balance'];
							$acctcreditsaccountdetailno[0]['credits_account_interest_last_balance']		= $acctcreditsaccountseriallist['credits_account_interest_last_balance'];
							$acctcreditsaccountdetailno[0]['credits_account_period']					= $acctcreditsaccountseriallist['credits_account_period'];
							$acctcreditsaccountdetailno[0]['credits_payment_day_of_delay']				= $credits_payment_day_of_delay;
							$acctcreditsaccountdetailno[0]['credits_account_payment_to']				= $credits_account_payment_to;
							$acctcreditsaccountdetailno[0]['credits_payment_fine_amount']				= $credits_payment_fine_amount;
							$acctcreditsaccountdetailno[0]['credits_account_accumulated_fines']			= $credits_account_accumulated_fines;

							$acctcreditsaccountdetailno[0]['credits_principal_installments']			= $angsuranpokok;
							$acctcreditsaccountdetailno[0]['credits_interest_installments']				= $angsuranbunga;

							$acctcreditsaccountdetailno[0]['credits_account_payment_amount']			= $acctcreditsaccountseriallist['credits_account_payment_amount'];
							$acctcreditsaccountdetailno[0]['credits_payment_period']					= $acctcreditsaccountseriallist['credits_payment_period'];
							$acctcreditsaccountdetailno[0]['credits_payment_token']						= md5(date('Y-m-d H:i:s'));
							
							
							$response['error'] 							= FALSE;
							$response['error_msg_title'] 				= "Success";
							$response['error_msg'] 						= "Data Exist";
							$response['acctcreditsaccountdetailno'] 	= $acctcreditsaccountdetailno;
						}
					}
				}
			} else {
				$response['error'] 				= TRUE;
				$response['error_msg_title'] 	= "No Data";
				$response['error_msg'] 			= "Waktu Transaksi Sudah Habis";
			}
			echo json_encode($response);
		}


		public function getAcctSavingsCashMutationDeposit(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'								=> FALSE,
				'error_msg'							=> "",
				'error_msg_title'					=> "",
				'acctsavingscashmutationdeposit'	=> "",
			);

			$data = array(
				'user_id'						=> $this->input->post('user_id',true),
				'savings_cash_mutation_date'	=> date("Y-m-d"),
			);

			if($response["error"] == FALSE){

				$preferencecompany 		= $this->Android_model->getPreferenceCompany();
				$cash_deposit_id 		= $preferencecompany['cash_deposit_id'];
				$cash_withdrawal_id 	= $preferencecompany['cash_withdrawal_id'];

				$acctsavingscashmutationdepositlist	= $this->Android_model->getAcctSavingsCashMutation($data['user_id'], $data['savings_cash_mutation_date'], $cash_deposit_id);

				if(!$acctsavingscashmutationdepositlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingscashmutationdepositlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {				
						foreach ($acctsavingscashmutationdepositlist as $key => $val) {
							$acctsavingscashmutationdeposit[$key]['member_id']								= $val['member_id'];
							$acctsavingscashmutationdeposit[$key]['member_no']								= $val['member_no'];
							$acctsavingscashmutationdeposit[$key]['member_name']							= $val['member_name'];
							$acctsavingscashmutationdeposit[$key]['savings_account_id']						= $val['savings_account_id'];
							$acctsavingscashmutationdeposit[$key]['savings_account_no']						= $val['savings_account_no'];
							$acctsavingscashmutationdeposit[$key]['savings_id']								= $val['savings_id'];
							$acctsavingscashmutationdeposit[$key]['savings_code']							= $val['savings_code'];
							$acctsavingscashmutationdeposit[$key]['savings_name']							= $val['savings_name'];
							$acctsavingscashmutationdeposit[$key]['savings_cash_mutation_date']				= tgltoview($val['savings_cash_mutation_date']);	
							$acctsavingscashmutationdeposit[$key]['savings_cash_mutation_opening_balance']	= $val['savings_cash_mutation_opening_balance'];
							$acctsavingscashmutationdeposit[$key]['savings_cash_mutation_amount']			= $val['savings_cash_mutation_amount'];
							$acctsavingscashmutationdeposit[$key]['savings_cash_mutation_last_balance']		= $val['savings_cash_mutation_last_balance'];
						}
						
						$response['error'] 								= FALSE;
						$response['error_msg_title'] 					= "Success";
						$response['error_msg'] 							= "Data Exist";
						$response['acctsavingscashmutationdeposit'] 	= $acctsavingscashmutationdeposit;
					}
				}
				
			}
			echo json_encode($response);
		}


		public function getAcctSavingsCashMutationWithdraw(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'								=> FALSE,
				'error_msg'							=> "",
				'error_msg_title'					=> "",
				'acctsavingscashmutationwithdraw'	=> "",
			);

			$data = array(
				'user_id'						=> $this->input->post('user_id',true),
				'savings_cash_mutation_date'	=> date("Y-m-d"),
			);

			if($response["error"] == FALSE){

				$preferencecompany 		= $this->Android_model->getPreferenceCompany();
				$cash_deposit_id 		= $preferencecompany['cash_deposit_id'];
				$cash_withdrawal_id 	= $preferencecompany['cash_withdrawal_id'];

				$acctsavingscashmutationwithdrawlist	= $this->Android_model->getAcctSavingsCashMutation($data['user_id'], $data['savings_cash_mutation_date'], $cash_withdrawal_id);

				if(!$acctsavingscashmutationwithdrawlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingscashmutationwithdrawlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {				
						foreach ($acctsavingscashmutationwithdrawlist as $key => $val) {
							$acctsavingscashmutationwithdraw[$key]['member_id']								= $val['member_id'];
							$acctsavingscashmutationwithdraw[$key]['member_no']								= $val['member_no'];
							$acctsavingscashmutationwithdraw[$key]['member_name']							= $val['member_name'];
							$acctsavingscashmutationwithdraw[$key]['savings_account_id']						= $val['savings_account_id'];
							$acctsavingscashmutationwithdraw[$key]['savings_account_no']						= $val['savings_account_no'];
							$acctsavingscashmutationwithdraw[$key]['savings_id']								= $val['savings_id'];
							$acctsavingscashmutationwithdraw[$key]['savings_code']							= $val['savings_code'];
							$acctsavingscashmutationwithdraw[$key]['savings_name']							= $val['savings_name'];
							$acctsavingscashmutationwithdraw[$key]['savings_cash_mutation_date']				= tgltoview($val['savings_cash_mutation_date']);	
							$acctsavingscashmutationwithdraw[$key]['savings_cash_mutation_opening_balance']	= $val['savings_cash_mutation_opening_balance'];
							$acctsavingscashmutationwithdraw[$key]['savings_cash_mutation_amount']			= $val['savings_cash_mutation_amount'];
							$acctsavingscashmutationwithdraw[$key]['savings_cash_mutation_last_balance']		= $val['savings_cash_mutation_last_balance'];
						}
						
						$response['error'] 								= FALSE;
						$response['error_msg_title'] 					= "Success";
						$response['error_msg'] 							= "Data Exist";
						$response['acctsavingscashmutationwithdraw'] 	= $acctsavingscashmutationwithdraw;
					}
				}
				
			}
			echo json_encode($response);
		}


		public function getAcctCreditsPaymentDashboard(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'								=> FALSE,
				'error_msg'							=> "",
				'error_msg_title'					=> "",
				'acctcreditspaymentdashboard'		=> "",
			);

			$data = array(
				'user_id'					=> $this->input->post('user_id',true),
				'credits_payment_date'		=> date("Y-m-d"),
			);



			if($response["error"] == FALSE){

				$acctcreditspaymentdashboardlist	= $this->Android_model->getAcctCreditsPayment($data['user_id'], $data['credits_payment_date']);

				if(!$acctcreditspaymentdashboardlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctcreditspaymentdashboardlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {				
						foreach ($acctcreditspaymentdashboardlist as $key => $val) {
							$credits_principal_opening_balance 	= $val['credits_principal_opening_balance'];
							$credits_interest_opening_balance 	= $val['credits_interest_opening_balance'];

							$credits_payment_opening_balance 	= $credits_principal_opening_balance + $credits_interest_opening_balance;

							$credits_principal_last_balance 	= $val['credits_principal_last_balance'];
							$credits_interest_last_balance 		= $val['credits_interest_last_balance'];

							$credits_payment_last_balance 		= $credits_principal_last_balance + $credits_interest_last_balance;


							$acctcreditspaymentdashboard[$key]['member_id']							= $val['member_id'];
							$acctcreditspaymentdashboard[$key]['member_no']							= $val['member_no'];
							$acctcreditspaymentdashboard[$key]['member_name']						= $val['member_name'];
							$acctcreditspaymentdashboard[$key]['credits_account_id']				= $val['credits_account_id'];
							$acctcreditspaymentdashboard[$key]['credits_account_serial']			= $val['credits_account_serial'];
							$acctcreditspaymentdashboard[$key]['credits_id']						= $val['credits_id'];
							$acctcreditspaymentdashboard[$key]['credits_name']						= $val['credits_name'];
							$acctcreditspaymentdashboard[$key]['credits_payment_date']				= tgltoview($val['credits_payment_date']);	
							$acctcreditspaymentdashboard[$key]['credits_payment_opening_balance']	= $credits_payment_opening_balance;
							$acctcreditspaymentdashboard[$key]['credits_payment_amount']			= $val['credits_payment_amount'];
							$acctcreditspaymentdashboard[$key]['credits_payment_last_balance']		= $credits_payment_last_balance;
						}
						
						$response['error'] 							= FALSE;
						$response['error_msg_title'] 				= "Success";
						$response['error_msg'] 						= "Data Exist";
						$response['acctcreditspaymentdashboard'] 	= $acctcreditspaymentdashboard;
					}
				}
				
			}
			echo json_encode($response);
		}

		public function getAcctSavingsAccountMember(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'						=> FALSE,
				'error_msg'					=> "",
				'error_msg_title'			=> "",
				'acctsavingsaccount'		=> "",
			);

			$data = array(
				'branch_id'		=> $this->input->post('branch_id',true),
				'member_id'		=> $this->input->post('member_id',true),
			);

			
			if($response["error"] == FALSE){
				$acctsavingsaccountlist = $this->Android_model->getAcctSavingsAccount($data['member_id']);

				if(!$acctsavingsaccountlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingsaccountlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {
						foreach ($acctsavingsaccountlist as $key => $val) {
							$acctsavingsaccount[$key]['savings_account_id'] 					= $val['savings_account_id'];
							$acctsavingsaccount[$key]['savings_id']								= $val['savings_id'];
							$acctsavingsaccount[$key]['savings_code']							= $val['savings_code'];
							$acctsavingsaccount[$key]['savings_name']							= $val['savings_name'];
							$acctsavingsaccount[$key]['savings_account_no']						= $val['savings_account_no'];
							$acctsavingsaccount[$key]['savings_account_last_balance']			= $val['savings_account_last_balance'];
						}
						
						$response['error'] 					= FALSE;
						$response['error_msg_title'] 		= "Success";
						$response['error_msg'] 				= "Data Exist";
						$response['acctsavingsaccount'] 	= $acctsavingsaccount;
					}
				}
			}
			

			echo json_encode($response);
		}


		public function getAcctSavingsAccountDetail(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(	
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'acctsavingsaccountdetail'		=> "",
			);

			$data = array(
				'savings_account_id'		=> $this->input->post('savings_account_id',true),
			);

			
			if($response["error"] == FALSE){
				$acctsavingsaccountdetaillist = $this->Android_model->getAcctSavingsAccountDetail($data['savings_account_id']);

				if(!$acctsavingsaccountdetaillist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingsaccountdetaillist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {
						/*krsort($acctsavingsaccountdetaillist);*/

						/*print_r("acctsavingsaccountdetaillist ");
						print_r($acctsavingsaccountdetaillist);*/

						foreach ($acctsavingsaccountdetaillist as $key => $val) {
							$acctsavingsaccountdetail[$key]['savings_account_id'] 		= $val['savings_account_id'];
							$acctsavingsaccountdetail[$key]['mutation_id']				= $val['mutation_id'];
							$acctsavingsaccountdetail[$key]['mutation_code']			= $val['mutation_code'];
							$acctsavingsaccountdetail[$key]['mutation_name']			= $val['mutation_name'];
							$acctsavingsaccountdetail[$key]['today_transaction_date']	= tgltoview($val['today_transaction_date']);
							$acctsavingsaccountdetail[$key]['mutation_in']				= $val['mutation_in'];
							$acctsavingsaccountdetail[$key]['mutation_out']				= $val['mutation_out'];
							$acctsavingsaccountdetail[$key]['last_balance']				= $val['last_balance'];
						}

						/*print_r("acctsavingsaccountdetail ");
						print_r($acctsavingsaccountdetail);*/
						
						$response['error'] 						= FALSE;
						$response['error_msg_title'] 			= "Success";
						$response['error_msg'] 					= "Data Exist";
						$response['acctsavingsaccountdetail'] 	= $acctsavingsaccountdetail;
					}
				}
			}
			

			echo json_encode($response);
		}


		public function getAcctSavingsMandatoryDetail(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(	
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'acctsavingsmandatorydetail'	=> "",
			);

			$data = array(
				'member_id'		=> $this->input->post('member_id',true),
			);

			if($response["error"] == FALSE){
				$acctsavingsmandatorydetaillist = $this->Android_model->getAcctSavingsMandatoryDetail($data['member_id']);

				if(!$acctsavingsmandatorydetaillist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingsmandatorydetaillist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {
						foreach ($acctsavingsmandatorydetaillist as $key => $val) {
							$acctsavingsmandatorydetail[$key]['member_id'] 								= $val['member_id'];
							$acctsavingsmandatorydetail[$key]['member_no']	 							= $val['member_no'];
							$acctsavingsmandatorydetail[$key]['member_name'] 							= $val['member_name'];
							$acctsavingsmandatorydetail[$key]['transaction_date'] 						= tgltoview($val['transaction_date']);
							$acctsavingsmandatorydetail[$key]['mandatory_savings_amount']				= $val['mandatory_savings_amount'];
							$acctsavingsmandatorydetail[$key]['member_mandatory_savings_last_balance']	= $val['member_mandatory_savings_last_balance'];
						}
						
						$response['error']	= FALSE;
						$response['error_msg_title'] 				= "Success";
						$response['error_msg'] 						= "Data Exist";
						$response['acctsavingsmandatorydetail'] 	= $acctsavingsmandatorydetail;
					}
				}
			}
			

			echo json_encode($response);
		}

		public function processAddAcctSavingsMandatory(){
			$auth = $this->session->userdata('auth');

			$response = array(
				'error'										=> FALSE,
				'error_acctsavingsmandatory'				=> FALSE,
				'error_msg_title_acctsavingsmandatory'		=> "",
				'error_msg_acctsavingsmandatory'			=> "",
			);

			$password									= md5($this->input->post('password', true));

			$member_mandatory_savings 					= $this->input->post('member_mandatory_savings', true);

			$member_mandatory_savings_last_balance_old	= $this->input->post('member_mandatory_savings_last_balance', true);

			$member_mandatory_savings_last_balance 		= $member_mandatory_savings_last_balance_old + $member_mandatory_savings;

			$data = array(
				'member_id'								=> $this->input->post('member_id', true),
				'branch_id'								=> $this->input->post('branch_id', true),
				'member_mandatory_savings'				=> $this->input->post('member_mandatory_savings', true),
				'member_mandatory_savings_last_balance'	=> $member_mandatory_savings_last_balance,				
			);

			$data_update = array (
				'username' 								=> $this->input->post('username', true),
				'member_name'							=> $this->input->post('member_name', true),
				'member_no'								=> $this->input->post('member_no', true),
				'user_id'								=> $this->input->post('user_id', true),
			);

			$data_mandatory = array (
				'member_id'								=> $this->input->post('member_id', true),
				'branch_id'								=> $this->input->post('branch_id', true),
				'savings_mandatory_log_date'			=> date("Y-m-d"),
				'savings_mandatory_log_amount'			=> $this->input->post('member_mandatory_savings', true),
				'savings_mandatory_log_remark'			=> $this->input->post('savings_mandatory_log_remark', true),
				'created_id'							=> $this->input->post('user_id', true),
				'created_on'							=> date("Y-m-d H:i;s"),
			);
			
			/* $password									= md5('123456');

			$member_mandatory_savings 					= 50000;

			$member_mandatory_savings_last_balance_old	= 70000;

			$member_mandatory_savings_last_balance 		= $member_mandatory_savings_last_balance_old + $member_mandatory_savings;

			$data = array(
				'member_id'								=> 1,
				'branch_id'								=> 2,
				'member_mandatory_savings'				=> $member_mandatory_savings,
				'member_mandatory_savings_last_balance'	=> $member_mandatory_savings_last_balance,				
			);

			$data_update = array (
				'username' 								=> 'Administrator',
				'member_name'							=> 'budi',
				'member_no'								=> '01000001',
				'user_id'								=> 37,
			);

			$data_mandatory = array (
				'member_id'								=> 1,
				'branch_id'								=> 2,
				'savings_mandatory_log_date'			=> date("Y-m-d"),
				'savings_mandatory_log_amount'			=> 70000,
				'savings_mandatory_log_remark'			=> 'test',
				'created_id'							=> 37,
				'created_on'							=> date("Y-m-d H:i;s"),
			); */






			if($response["error_acctsavingsmandatory"] == FALSE){
				if(!empty($data)){					
					if($this->Android_model->getSystemUser($data_update['user_id'], $password)){
						if($data['member_mandatory_savings'] <> 0 || $data['member_mandatory_savings'] <> ''){
							if ($this->Android_model->insertAcctSavingsMandatoryLog($data_mandatory)){
								$savings_mandatory_log_id 	=  $this->Android_model->getSavingsMandatoryLogOD($data_mandatory['created_id']); 

								if($this->CoreMember_model->updateCoreMember($data)){
									$data_detail = array (
										'branch_id'						=> $data['branch_id'],
										'member_id'						=> $data['member_id'],
										'mutation_id'					=> 1,
										'transaction_date'				=> date('Y-m-d'),
										'mandatory_savings_amount'		=> $data['member_mandatory_savings'],
										'operated_name'					=> $data_update['username'],
										'created_id'					=> $data_update['user_id'],
										'created_on'					=> date("Y-m-d H:i:s"),
									);



									if($this->CoreMember_model->insertAcctSavingsMemberDetail($data_detail)){

										$transaction_module_code 	= "AGT";

										$transaction_module_id 		= $this->CoreMember_model->getTransactionModuleID($transaction_module_code);
										$preferencecompany 			= $this->CoreMember_model->getPreferenceCompany();
											
										$journal_voucher_period 	= date("Ym", strtotime($data_detail['transaction_date']));

										//-------------------------Jurnal Cabang----------------------------------------------------
										
										$data_journal_cabang = array(
											'branch_id'						=> $data['branch_id'],
											'journal_voucher_period' 		=> $journal_voucher_period,
											'journal_voucher_date'			=> date('Y-m-d'),
											'journal_voucher_title'			=> 'MUTASI ANGGOTA TUNAI '.$data_update['member_name'],
											'journal_voucher_description'	=> 'MUTASI ANGGOTA TUNAI '.$data_update['member_name'],
											'transaction_module_id'			=> $transaction_module_id,
											'transaction_module_code'		=> $transaction_module_code,
											'transaction_journal_id' 		=> $data['member_id'],
											'transaction_journal_no' 		=> $data_update['member_no'],
											'created_id' 					=> $data_update['user_id'],
											'created_on' 					=> date('Y-m-d H:i:s'),
										);
										
										$this->CoreMember_model->insertAcctJournalVoucher($data_journal_cabang);

										$journal_voucher_id			= $this->CoreMember_model->getJournalVoucherID($data_journal_cabang['created_id']);

										$preferencecompany 						= $this->CoreMember_model->getPreferenceCompany();

										$account_id_default_status 	= $this->CoreMember_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

										$data_debet = array (
											'journal_voucher_id'			=> $journal_voucher_id,
											'account_id'					=> $preferencecompany['account_cash_id'],
											'journal_voucher_description'	=> 'SETORAN TUNAI '.$data_update['member_name'],
											'journal_voucher_amount'		=> $data['member_mandatory_savings'],
											'journal_voucher_debit_amount'	=> $data['member_mandatory_savings'],
											'account_id_default_status'		=> $account_id_default_status,
											'account_id_status'				=> 0,
										);

										$this->CoreMember_model->insertAcctJournalVoucherItem($data_debet);

										if($data['member_mandatory_savings'] <> 0 || $data['member_mandatory_savings'] <> ''){
											$account_id = $this->CoreMember_model->getAccountID($preferencecompany['mandatory_savings_id']);

											$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

											$data_credit =array(
												'journal_voucher_id'			=> $journal_voucher_id,
												'account_id'					=> $account_id,
												'journal_voucher_description'	=> 'SETORAN TUNAI '.$data_update['member_name'],
												'journal_voucher_amount'		=> $data['member_mandatory_savings'],
												'journal_voucher_credit_amount'	=> $data['member_mandatory_savings'],
												'account_id_status'				=> 1,
											);

											$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);	
										}

										$response['error_acctsavingsmandatory'] 	= FALSE;
										$response['error_msg_title'] 				= "Success";
										$response['error_msg'] 						= "Data Exist";
										$response['savings_mandatory_log_id']		= $savings_mandatory_log_id;
									} else {
										$response['error_acctsavingsmandatory'] 	= TRUE;
										$response['error_msg_title'] 				= "Insert Failed";
										$response['error_msg'] 						= "Insert Gagal";
										$response['savings_mandatory_log_id']		= 0;
									}
								} else {
									$response['error_acctsavingsmandatory'] 	= TRUE;
									$response['error_msg_title'] 				= "Update Member Failed";
									$response['error_msg'] 						= "Update Gagal";
									$response['savings_mandatory_log_id']		= 0;		
								}
							} else {
								$response['error_acctsavingsmandatory'] 	= TRUE;
								$response['error_msg_title'] 				= "Insert Log Failed";
								$response['error_msg'] 						= "Update Gagal";
								$response['savings_mandatory_log_id']		= 0;		
							}
						} else {
							$response['error_acctsavingsmandatory'] 	= TRUE;
							$response['error_msg_title'] 				= "Mandatory Savings Empty";
							$response['error_msg'] 						= "Simpanan Wajib Kosong";
							$response['savings_mandatory_log_id']		= 0;
						}
					} else {
						$response['error_acctsavingsmandatory'] 	= TRUE;
						$response['error_msg_title'] 				= "Password Salah";
						$response['error_msg'] 						= "Password Salah";
						$response['savings_mandatory_log_id']		= 0;
					} 
				} else {
					$response['error_acctsavingsmandatory'] 	= TRUE;
					$response['error_msg_title'] 				= "Data Empty";
					$response['error_msg'] 						= "Data Empty";
					$response['savings_mandatory_log_id']		= 0;
				}
			}else{
				$response['error_acctsavingsmandatory'] 	= TRUE;
				$response['error_msg_title'] 				= "Error";
				$response['error_msg'] 						= "Data Exist";
				$response['savings_mandatory_log_id']		= 0;
			}	
			
			echo json_encode($response);
		}

		public function getAcctSavingsMandatoryLog(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'								=> FALSE,
				'error_msg'							=> "",
				'error_msg_title'					=> "",
				'acctsavingsmandatorylog'			=> "",
			);

			$data = array(
				'user_id'						=> $this->input->post('user_id',true),
				'savings_mandatory_log_date'	=> date("Y-m-d"),
			);

			$data = array(
				'user_id'						=> 37,
				'savings_mandatory_log_date'	=> date("Y-m-d"),
			);

			if($response["error"] == FALSE){

				$acctsavingsmandatoryloglist	= $this->Android_model->getAcctSavingsMandatoryLog($data['user_id'], $data['savings_mandatory_log_date']);

				if(!$acctsavingsmandatoryloglist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingsmandatoryloglist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {				
						foreach ($acctsavingsmandatoryloglist as $key => $val) {
							$acctsavingsmandatorylog[$key]['member_id']						= $val['member_id'];
							$acctsavingsmandatorylog[$key]['member_no']						= $val['member_no'];
							$acctsavingsmandatorylog[$key]['member_name']					= $val['member_name'];
							$acctsavingsmandatorylog[$key]['savings_mandatory_log_date']	= tgltoview($val['savings_mandatory_log_date']);
							$acctsavingsmandatorylog[$key]['savings_mandatory_log_amount']	= $val['savings_mandatory_log_amount'];
						}
						
						$response['error'] 								= FALSE;
						$response['error_msg_title'] 					= "Success";
						$response['error_msg'] 							= "Data Exist";
						$response['acctsavingsmandatorylog'] 			= $acctsavingsmandatorylog;
					}
				}
				
			}
			echo json_encode($response);
		}

		public function printNoteAcctSavingsMandatoryLog(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'acctsavingsmandatorylog'		=> "",
			);

			$data = array(
				'savings_mandatory_log_id'		=> $this->input->post('savings_mandatory_log_id',true),
			);

			$preferencecompany = $this->Android_model->getPreferenceCompany();

			if($response["error"] == FALSE){
				$acctsavingsmandatoryloglist	= $this->Android_model->getAcctSavingsMandatoryLog_Detail($data['savings_mandatory_log_id']);

				/*print_r("acctsavingscashmutationlist ");
				print_r($acctsavingscashmutationlist);*/

				if(!$acctsavingsmandatoryloglist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingsmandatoryloglist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {						
						$acctsavingsmandatorylog[0]['company_name'] 					= $preferencecompany['company_name'];
						$acctsavingsmandatorylog[0]['member_name'] 						= $acctsavingsmandatoryloglist['member_name'];
						$acctsavingsmandatorylog[0]['member_address']					= $acctsavingsmandatoryloglist['member_address'];
						$acctsavingsmandatorylog[0]['savings_mandatory_log_amount']		= "Rp. ".number_format($acctsavingsmandatoryloglist['savings_mandatory_log_amount'], 2);
						$acctsavingsmandatorylog[0]['savings_mandatory_log_amount_str']	= numtotxt($acctsavingsmandatoryloglist['savings_mandatory_log_amount']);
						$acctsavingsmandatorylog[0]['branch_city']						= $acctsavingsmandatoryloglist['branch_city'];
						
						$response['error'] 						= FALSE;
						$response['error_msg_title'] 			= "Success";
						$response['error_msg'] 					= "Data Exist";
						$response['acctsavingsmandatorylog'] 	= $acctsavingsmandatorylog;
					}
				}
			}
			echo json_encode($response);
		}

		public function getDailyRecapitulation(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(	
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'dailyrecapitulation'			=> "",
			);

			$data = array(
				'user_id'						=> $this->input->post('user_id',true),
				'daily_recapitulation_date'		=> date("Y-m-d"),
			);

			if($response["error"] == FALSE){
				$preferencecompany 				= $this->Android_model->getPreferenceCompany();
				$cash_deposit_id 				= $preferencecompany['cash_deposit_id'];
				$cash_withdrawal_id 			= $preferencecompany['cash_withdrawal_id'];

				$cash_mutation_deposit_amount 	= $this->Android_model->getAcctSavingsCashMutation_Total($data['user_id'], $data['daily_recapitulation_date'], $cash_deposit_id);

				$cash_mutation_withdraw_amount 	= $this->Android_model->getAcctSavingsCashMutation_Total($data['user_id'], $data['daily_recapitulation_date'], $cash_withdrawal_id);

				$credits_payment_amount			= $this->Android_model->getAcctCreditsPayment_Total($data['user_id'], $data['daily_recapitulation_date']);	

				$savings_mandatory_amount		= $this->Android_model->getAcctSavingsMandatoryLog_Total($data['user_id'], $data['daily_recapitulation_date']);


				if (empty($cash_mutation_deposit_amount)){
					$cash_mutation_deposit_amount = 0;
				}

				if (empty($cash_mutation_withdraw_amount)){
					$cash_mutation_withdraw_amount = 0;
				}

				if (empty($credits_payment_amount)){
					$credits_payment_amount = 0;
				}

				if (empty($savings_mandatory_amount)){
					$savings_mandatory_amount = 0;
				}

				$dailyrecapitulation[0]['cash_mutation_deposit_amount'] 	= $cash_mutation_deposit_amount;
				$dailyrecapitulation[0]['cash_mutation_withdraw_amount']	= $cash_mutation_withdraw_amount;
				$dailyrecapitulation[0]['credits_payment_amount'] 			= $credits_payment_amount;
				$dailyrecapitulation[0]['savings_mandatory_amount'] 		= $savings_mandatory_amount;
				
				$response['error']	= FALSE;
				$response['error_msg_title'] 				= "Success";
				$response['error_msg'] 						= "Data Exist";
				$response['dailyrecapitulation'] 			= $dailyrecapitulation;
			}
			

			echo json_encode($response);
		}

		public function getAcctSavingsAccountNoTo(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(	
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'acctsavingsaccountnoto'		=> "",
			);

			$data = array(
				'savings_id'				=> $this->input->post('savings_id',true),
				'savings_account_no'		=> $this->input->post('savings_account_no',true),
			);

			if($response["error"] == FALSE){
				$acctsavingsaccount = $this->Android_model->getAcctSavingsAccount_NoTo($data['savings_id'], $data['savings_account_no']);

				if (!empty($acctsavingsaccount)){
					$acctsavingsaccountnoto[0]['savings_account_no'] 			= $acctsavingsaccount['savings_account_no'];
					$acctsavingsaccountnoto[0]['savings_account_id'] 			= $acctsavingsaccount['savings_account_id'];
					$acctsavingsaccountnoto[0]['savings_id']					= $acctsavingsaccount['savings_id'];
					$acctsavingsaccountnoto[0]['member_id']						= $acctsavingsaccount['member_id'];
					$acctsavingsaccountnoto[0]['member_no']						= $acctsavingsaccount['member_no'];
					$acctsavingsaccountnoto[0]['member_name']					= $acctsavingsaccount['member_name'];
					$acctsavingsaccountnoto[0]['branch_id'] 					= $acctsavingsaccount['branch_id'];
					$acctsavingsaccountnoto[0]['savings_account_last_balance'] 	= $acctsavingsaccount['savings_account_last_balance'];

					$response['error']							= FALSE;
					$response['error_msg_title'] 				= "Success";
					$response['error_msg'] 						= "Data Exist";
					$response['acctsavingsaccountnoto'] 		= $acctsavingsaccountnoto;
				} else {
					$response['error']							= TRUE;
					$response['error_msg_title'] 				= "Fail";
					$response['error_msg'] 						= "Data Empty";
				}

			}
			

			echo json_encode($response);
		}
	}
?>