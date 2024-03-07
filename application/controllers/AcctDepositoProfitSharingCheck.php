<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctDepositoProfitSharingCheck extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctDepositoProfitSharingCheck_model');
			$this->load->model('AcctSavingsTransferMutation_model');
			$this->load->model('AcctSavingsAccount_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}

		public function index(){
			$auth 	=	$this->session->userdata('auth');
			$sesi	= 	$this->session->userdata('filter-acctdepositoprofitsharingcheck');
			if(!is_array($sesi)){
				$sesi['start_date']					= date('Y-m-d');
				$sesi['end_date']					= date('Y-m-d');
				$sesi['branch_id']					= $auth['branch_id'];
			}

			$data['main_view']['corebranch']		= create_double($this->AcctDepositoProfitSharingCheck_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['acctdepositoprofitsharingcheck']		= $this->AcctDepositoProfitSharingCheck_model->getAcctDepositoProfitSharingCheck($sesi['start_date'], $sesi['end_date'], $sesi['branch_id']);
			$data['main_view']['preferencecompany']		= $this->AcctDepositoProfitSharingCheck_model->getPreferenceCompany();

			$data['main_view']['content']			= 'AcctDepositoProfitSharingCheck/ListAcctDepositoProfitSharingCheck_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date" 	=> tgltodb($this->input->post('start_date',true)),
				"end_date" 		=> tgltodb($this->input->post('end_date',true)),
				"branch_id"		=> $this->input->post('branch_id',true),
			);

			$this->session->set_userdata('filter-acctdepositoprofitsharingcheck',$data);
			redirect('deposito-profit-sharing-check');
		}
		public function reset_search(){
			$this->session->unset_userdata('filter-acctdepositoprofitsharingcheck');
			redirect('deposito-profit-sharing-check');
		}
		public function addAcctDepositoProfitSharingCheck(){
			$deposito_profit_sharing_id 	= $this->uri->segment(3);
			$savings_account_id			 	= $this->uri->segment(4);
			$unique = $this->session->userdata('unique');
			$token 	= $this->session->userdata('acctdepositoprofitsharingcheck-'.$unique['unique']);

			if(empty($token)){
				$token = md5(date('Y-m-d H:i:s'));
				$this->session->set_userdata('acctdepositoprofitsharingcheck-'.$unique['unique'], $token);
			}

			$depositoaccount 		= $this->AcctDepositoProfitSharingCheck_model->getAcctDepositoAccountDetail($deposito_profit_sharing_id);
			$deposito 				= $this->AcctDepositoProfitSharingCheck_model->getAcctDepositoDetail($depositoaccount['deposito_id']);
			$depositoaccrual_last 	= $this->AcctDepositoProfitSharingCheck_model->getAcctDepositoAccrualLast($depositoaccount['deposito_account_id']);

			$date_on 	= new DateTime($depositoaccrual_last['created_on']);
			$date_next 	= new DateTime(date('Y-m-d'));
			$date_diff 	= $date_on->diff($date_next);
			$days 		= $date_diff->days;
			// print_r($days);exit();
			// $deposito_profit_sharing_amount = ($days *$depositoaccount['deposito_account_amount'] * $deposito['deposito_interest_rate']) / 365;

			$data['main_view']['acctdepositoprofitsharing']	= $this->AcctDepositoProfitSharingCheck_model->getAcctDepositoProfitSharing_Detail($deposito_profit_sharing_id);
			$data['main_view']['acctsavingsaccount']		= $this->AcctDepositoProfitSharingCheck_model->getAcctSavingsAccount_Detail($savings_account_id);
			$data['main_view']['preferencecompany']		= $this->AcctDepositoProfitSharingCheck_model->getPreferenceCompany();
			// $data['main_view']['deposito_profit_sharing_amount']	= $deposito_profit_sharing_amount;

			$data['main_view']['content'] = 'AcctDepositoProfitSharingCheck/FormAddAcctDepositoProfitSharingCheck_view';
			$this->load->view('MainPage_view', $data);
		}

		public function getAcctSavingsAccountList(){
			$deposito_profit_sharing_id = $this->uri->segment(3);
			$auth = $this->session->userdata('auth');
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
	            $row[] = '<a href="'.base_url().'deposito-profit-sharing-check/add/'.$deposito_profit_sharing_id.'/'.$savingsaccount->savings_account_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
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

		public function processUpdateAcctDepositoProfitSharing(){
			$auth = $this->session->userdata('auth');
			$unique = $this->session->userdata('unique');
			$date = date('Y-m-d');

			$data = array (
				'deposito_id'						=> $this->input->post('deposito_id', true),
				'deposito_profit_sharing_id'		=> $this->input->post('deposito_profit_sharing_id', true),
				'deposito_profit_sharing_date'		=> date('Y-m-d'),
				'deposito_index_amount'				=> $this->input->post('deposito_index_amount', true),
				'deposito_profit_sharing_amount'	=> $this->input->post('deposito_profit_sharing_amount', true),
				'deposito_profit_sharing_period'	=> $this->input->post('deposito_profit_sharing_period', true),
				'savings_account_id'				=> $this->input->post('savings_account_id', true),
				'deposito_profit_sharing_token'		=> $this->input->post('deposito_profit_sharing_token', true),
				'deposito_profit_sharing_status'	=> 1,
			);

			$data_savings = array (
				'savings_id'						=> $this->input->post('savings_id', true),
				'member_id'							=> $this->input->post('member_id_savings', true),
				'savings_account_opening_balance'	=> $this->input->post('savings_account_last_balance', true),
				'savings_account_last_balance'		=> $this->input->post('savings_account_last_balance', true) + $data['deposito_profit_sharing_amount'],
			);

			$this->form_validation->set_rules('deposito_profit_sharing_amount', 'Basil', 'required');

			$transaction_module_code = "BSDEP";

			$transaction_module_id 		= $this->AcctDepositoProfitSharingCheck_model->getTransactionModuleID($transaction_module_code);

			$preferencecompany 	= $this->AcctDepositoProfitSharingCheck_model->getPreferenceCompany();
			$depositoaccount	= $this->AcctDepositoProfitSharingCheck_model->getAcctDepositoAccountDetail($data['deposito_profit_sharing_id']);

			$deposito_profit_sharing_token 	= $this->AcctDepositoProfitSharingCheck_model->getAcctDepositoProfitSharingToken($data['deposito_profit_sharing_token']);

			if($this->form_validation->run()==true){
				if($deposito_profit_sharing_token->num_rows()==0){
					if($this->AcctDepositoProfitSharingCheck_model->updateAcctDepositoProfitSharing($data)){
						$data_depositoaccount = array (
							'deposito_account_id'			=> $depositoaccount['deposito_account_id'],
							'deposito_account_nisbah'		=> $depositoaccount['deposito_account_nisbah']+$data['deposito_profit_sharing_amount'],
							'deposito_process_last_date'	=> $data['deposito_profit_sharing_date'],
						);
						$this->AcctDepositoProfitSharingCheck_model->updateAcctDepositoAccount($data_depositoaccount);

						$total_amount	= $data['deposito_profit_sharing_amount'];
						$tax_amount		= 0;
						if($total_amount > $preferencecompany['tax_minimum_amount']){
							$tax_amount = $total_amount * $preferencecompany['tax_percentage'] / 100;
						}
						$total_amount_min_tax	= $total_amount - $tax_amount;

						$data_transfer = array (
							'branch_id'							=> $auth['branch_id'],
							'savings_transfer_mutation_date'	=> date('Y-m-d'),
							'savings_transfer_mutation_amount'	=> $total_amount_min_tax,
							'operated_name'						=> 'SYS',
							'created_id'						=> $auth['user_id'],
							'created_on'						=> date('Y-m-d H:i:s'),
						);
	
						if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutation($data_transfer)){
							$savings_transfer_mutation_id = $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data_transfer['created_on']);
	
							$data_transfer_to = array (
								'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
								'savings_account_id'						=> $data['savings_account_id'],
								'savings_id'								=> $data_savings['savings_id'],
								'member_id'									=> $data_savings['member_id'],
								'branch_id'									=> $auth['branch_id'],
								'mutation_id'								=> $preferencecompany['deposito_basil_id'],
								'savings_account_opening_balance'			=> $data_savings['savings_account_opening_balance'],
								'savings_transfer_mutation_to_amount'		=> $total_amount_min_tax,
								'savings_account_last_balance'				=> $data_savings['savings_account_last_balance'],
							);
	
							if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationTo($data_transfer_to)){

								$acctdepositoprofitsharing_last 	= $this->AcctDepositoProfitSharingCheck_model->getAcctDepositoProfitSharing_Last($data['deposito_profit_sharing_id']);
		
							
								$journal_voucher_period = date("Ym", strtotime($data['deposito_profit_sharing_date']));
								
								$data_journal = array(
									'branch_id'						=> $auth['branch_id'],
									'journal_voucher_period' 		=> $journal_voucher_period,
									'journal_voucher_date'			=> date('Y-m-d'),
									'journal_voucher_title'			=> 'JASA SIMP BERJANGKA '.$acctdepositoprofitsharing_last['member_name'],
									'journal_voucher_description'	=> 'JASA SIMP BERJANGKA '.$acctdepositoprofitsharing_last['member_name'],
									'transaction_module_id'			=> $transaction_module_id,
									'transaction_module_code'		=> $transaction_module_code,
									'transaction_journal_id' 		=> $acctdepositoprofitsharing_last['deposito_profit_sharing_id'],
									'transaction_journal_no' 		=> $acctdepositoprofitsharing_last['deposito_account_no'],
									'journal_voucher_token'			=> $data['deposito_profit_sharing_token'],	
									'created_id' 					=> $auth['user_id'],
									'created_on' 					=> date('Y-m-d H:i:s'),
								);
								
								$this->AcctDepositoProfitSharingCheck_model->insertAcctJournalVoucher($data_journal);
		
								$journal_voucher_id = $this->AcctDepositoProfitSharingCheck_model->getJournalVoucherID($data_journal['created_id']);
		
								$account_basil_id 	= $this->AcctDepositoProfitSharingCheck_model->getAccountBasilID($data['deposito_id']);
		
								$account_id_default_status = $this->AcctDepositoProfitSharingCheck_model->getAccountIDDefaultStatus($account_basil_id);
		
								$data_debet = array (
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_basil_id,
									'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
									'journal_voucher_amount'		=> ABS($total_amount),
									'journal_voucher_debit_amount'	=> ABS($total_amount),
									'account_id_default_status'		=> $account_id_default_status,
									'journal_voucher_item_token'	=> $data['deposito_profit_sharing_token'].$account_basil_id,	
									'account_id_status'				=> 0,
									'created_id' 					=> $auth['user_id'],
								);
		
								$this->AcctDepositoProfitSharingCheck_model->insertAcctJournalVoucherItem($data_debet);
		
								$account_id = $this->AcctDepositoProfitSharingCheck_model->getAccountID($data_savings['savings_id']);
		
								$account_id_default_status = $this->AcctDepositoProfitSharingCheck_model->getAccountIDDefaultStatus($account_id);
		
								$data_credit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
									'journal_voucher_amount'		=> ABS($total_amount_min_tax),
									'journal_voucher_credit_amount'	=> ABS($total_amount_min_tax),
									'account_id_default_status'		=> $account_id_default_status,
									'journal_voucher_item_token'	=> $data['deposito_profit_sharing_token'].$account_id,
									'account_id_status'				=> 1,
									'created_id' 					=> $auth['user_id'],
									
								);
		
								$this->AcctDepositoProfitSharingCheck_model->insertAcctJournalVoucherItem($data_credit);
								
								if($tax_amount > 0){
									$account_savings_tax_id 	= $preferencecompany['account_savings_tax_id'];

									$account_id_default_status = $this->AcctDepositoProfitSharingCheck_model->getAccountIDDefaultStatus($account_savings_tax_id);

									$data_credit = array (
										'journal_voucher_id'			=> $journal_voucher_id,
										'account_id'					=> $account_savings_tax_id,
										'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
										'journal_voucher_amount'		=> ABS($tax_amount),
										'journal_voucher_credit_amount'	=> ABS($tax_amount),
										'account_id_default_status'		=> $account_id_default_status,
										'journal_voucher_item_token'	=> $data['deposito_profit_sharing_token'].$account_savings_tax_id,	
										'account_id_status'				=> 0,
										'created_id' 					=> $auth['user_id'],
									);

									$this->AcctDepositoProfitSharingCheck_model->insertAcctJournalVoucherItem($data_credit);
								}
							}
						}
						
						$auth = $this->session->userdata('auth');
						// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Input Bunga Berjangka Sukses
								</div> ";

						$this->session->unset_userdata('acctdepositoprofitsharingcheck-'.$unique['unique']);
						$this->session->set_userdata('message',$msg);
						redirect('deposito-profit-sharing-check');
					} else {
						$this->session->set_userdata('addacctsavingscashmutation',$data);
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Input Bunga Berjangka Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('deposito-profit-sharing-check');
					}
				}else{
					$data_depositoaccount = array (
						'deposito_account_id'			=> $depositoaccount['deposito_account_id'],
						'deposito_account_nisbah'		=> $depositoaccount['deposito_account_nisbah']+$data['deposito_profit_sharing_amount'],
						'deposito_process_last_date'	=> $data['deposito_profit_sharing_date'],
					);
					$this->AcctDepositoProfitSharingCheck_model->updateAcctDepositoAccount($data_depositoaccount);

					$total_amount	= $data['deposito_profit_sharing_amount'];
					$tax_amount		= 0;
					if($total_amount > $preferencecompany['tax_minimum_amount']){
						$tax_amount = $total_amount * $preferencecompany['tax_percentage'] / 100;
					}
					$total_amount_min_tax	= $total_amount - $tax_amount;

					$data_transfer = array (
						'branch_id'							=> $auth['branch_id'],
						'savings_transfer_mutation_date'	=> date('Y-m-d'),
						'savings_transfer_mutation_amount'	=> $total_amount_min_tax,
						'operated_name'						=> 'SYS',
						'created_id'						=> $auth['user_id'],
						'created_on'						=> date('Y-m-d H:i:s'),
					);

					if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutation($data_transfer)){
						$savings_transfer_mutation_id = $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data_transfer['created_on']);

						$data_transfer_to = array (
							'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
							'savings_account_id'						=> $data['savings_account_id'],
							'savings_id'								=> $data_savings['savings_id'],
							'member_id'									=> $data_savings['member_id'],
							'branch_id'									=> $auth['branch_id'],
							'mutation_id'								=> $preferencecompany['deposito_basil_id'],
							'savings_account_opening_balance'			=> $data_savings['savings_account_opening_balance'],
							'savings_transfer_mutation_to_amount'		=> $total_amount_min_tax,
							'savings_account_last_balance'				=> $data_savings['savings_account_last_balance'],
						);

						if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationTo($data_transfer_to)){
							$acctdepositoprofitsharing_last 	= $this->AcctDepositoProfitSharingCheck_model->getAcctDepositoProfitSharing_Last($data['deposito_profit_sharing_id']);

							$journal_voucher_period = date("Ym", strtotime($data['deposito_profit_sharing_date']));
							
							$data_journal = array(
								'branch_id'						=> $auth['branch_id'],
								'journal_voucher_period' 		=> $journal_voucher_period,
								'journal_voucher_date'			=> date('Y-m-d'),
								'journal_voucher_title'			=> 'JASA SIMP BERJANGKA '.$acctdepositoprofitsharing_last['member_name'],
								'journal_voucher_description'	=> 'JASA SIMP BERJANGKA '.$acctdepositoprofitsharing_last['member_name'],
								'transaction_module_id'			=> $transaction_module_id,
								'transaction_module_code'		=> $transaction_module_code,
								'transaction_journal_id' 		=> $acctdepositoprofitsharing_last['deposito_profit_sharing_id'],
								'transaction_journal_no' 		=> $acctdepositoprofitsharing_last['deposito_account_no'],
								'journal_voucher_token'			=> $data['deposito_profit_sharing_token'],	
								'created_id' 					=> $auth['user_id'],
								'created_on' 					=> date('Y-m-d H:i:s'),
							);
							
							$this->AcctDepositoProfitSharingCheck_model->insertAcctJournalVoucher($data_journal);

							$journal_voucher_id = $this->AcctDepositoProfitSharingCheck_model->getJournalVoucherID($data_journal['created_id']);

							$account_basil_id 	= $this->AcctDepositoProfitSharingCheck_model->getAccountBasilID($data['deposito_id']);

							$account_id_default_status = $this->AcctDepositoProfitSharingCheck_model->getAccountIDDefaultStatus($account_basil_id);

							$data_debet = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_basil_id,
								'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
								'journal_voucher_amount'		=> ABS($total_amount),
								'journal_voucher_debit_amount'	=> ABS($total_amount),
								'account_id_default_status'		=> $account_id_default_status,
								'journal_voucher_item_token'	=> $data['deposito_profit_sharing_token'].$account_basil_id,	
								'account_id_status'				=> 0,
								'created_id' 					=> $auth['user_id'],
							);

							// print_r($data_debet['account_id']);exit;

							$this->AcctDepositoProfitSharingCheck_model->insertAcctJournalVoucherItem($data_debet);

							$account_id = $this->AcctDepositoProfitSharingCheck_model->getAccountID($data_savings['savings_id']);

							$account_id_default_status = $this->AcctDepositoProfitSharingCheck_model->getAccountIDDefaultStatus($account_id);

							$data_credit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_id,
								'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
								'journal_voucher_amount'		=> ABS($total_amount_min_tax),
								'journal_voucher_credit_amount'	=> ABS($total_amount_min_tax),
								'account_id_default_status'		=> $account_id_default_status,
								'journal_voucher_item_token'	=> $data['deposito_profit_sharing_token'].$account_id,
								'account_id_status'				=> 1,
								'created_id' 					=> $auth['user_id'],
								
							);

							$this->AcctDepositoProfitSharingCheck_model->insertAcctJournalVoucherItem($data_credit);
								
							if($tax_amount > 0){
								$account_savings_tax_id 	= $preferencecompany['account_savings_tax_id'];

								$account_id_default_status = $this->AcctDepositoProfitSharingCheck_model->getAccountIDDefaultStatus($account_savings_tax_id);

								$data_credit = array (
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_savings_tax_id,
									'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
									'journal_voucher_amount'		=> ABS($tax_amount),
									'journal_voucher_credit_amount'	=> ABS($tax_amount),
									'account_id_default_status'		=> $account_id_default_status,
									'journal_voucher_item_token'	=> $data['deposito_profit_sharing_token'].$account_savings_tax_id,	
									'account_id_status'				=> 0,
									'created_id' 					=> $auth['user_id'],
								);

								$this->AcctDepositoProfitSharingCheck_model->insertAcctJournalVoucherItem($data_credit);
							}

							$auth = $this->session->userdata('auth');
							// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
							$msg = "<div class='alert alert-success alert-dismissable'>  
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
										Input Bunga Berjangka Sukses
									</div> ";

							$this->session->unset_userdata('acctdepositoprofitsharingcheck-'.$unique['unique']);
							$this->session->set_userdata('message',$msg);
							redirect('deposito-profit-sharing-check');
						}
					}

					
				}
			} else {
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('deposito-profit-sharing-check');
			}
		}

	}
?>