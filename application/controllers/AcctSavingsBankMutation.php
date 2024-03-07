<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctSavingsBankMutation extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctSavingsAccount_model');
			$this->load->model('AcctSavingsBankMutation_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		} 
		
		public function index(){
			$sesi	= 	$this->session->userdata('filter-acctsavingsbankmutation');
			
			$unique = $this->session->userdata('unique');
			$this->session->unset_userdata('acctsavingsbankmutationtoken-'.$unique['unique']);

			if(!is_array($sesi)){
				$sesi['start_date']				= date('Y-m-d');
				$sesi['end_date']				= date('Y-m-d');
				$sesi['savings_account_id']		= '';
			}

			$data['main_view']['acctsavingsaccount']			= create_double($this->AcctSavingsBankMutation_model->getAcctSavingsAccount(),'savings_account_id', 'savings_account_no');
			$data['main_view']['acctsavingsbankmutation']		= $this->AcctSavingsBankMutation_model->getAcctSavingsBankMutation($sesi['start_date'], $sesi['end_date'], $sesi['savings_account_id']);
			$data['main_view']['content']						= 'AcctSavingsBankMutation/ListAcctSavingsBankMutation_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date" 				=> tgltodb($this->input->post('start_date',true)),
				"end_date" 					=> tgltodb($this->input->post('end_date',true)),
				"savings_account_id"		=> $this->input->post('savings_account_id',true),
			);

			$this->session->set_userdata('filter-acctsavingsbankmutation',$data);
			redirect('savings-bank-mutation');
		}
		public function getListAcctSavingsAccount(){
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
	            $row[] = '<a href="'.base_url().'savings-bank-mutation/add/'.$savingsaccount->savings_account_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Pilih</a>';
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctSavingsAccount_model->count_all($auth['branch_id']),
	                        "recordsFiltered" => $this->AcctSavingsAccount_model->count_filtered($auth['branch_id']),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}
		
		public function addAcctSavingsBankMutation(){
			$savings_account_id = $this->uri->segment(3);
			$unique = $this->session->userdata('unique');
			$token 	= $this->session->userdata('acctsavingsbankmutationtoken-'.$unique['unique']);
		
			if(empty($token)){
				$token = md5(date('Y-m-d H:i:s'));
				$this->session->set_userdata('acctsavingsbankmutationtoken-'.$unique['unique'], $token);
			}


			$data['main_view']['acctsavingsaccount']		= $this->AcctSavingsBankMutation_model->getAcctSavingsAccount_Detail($savings_account_id);
			
			$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();

			// $data['main_view']['acctsavingsaccount']		= create_double($this->AcctSavingsBankMutation_model->getAcctSavingsAccount(),'savings_account_id', 'savings_account_no');	

			$data['main_view']['acctbankaccount']			= create_double($this->AcctSavingsBankMutation_model->getAcctBankAccount(),'bank_account_id', 'bank_account_code');	

			$data['main_view']['acctmutation']				= create_double($this->AcctSavingsBankMutation_model->getAcctMutation(),'mutation_id', 'mutation_name');


			$data['main_view']['content']					= 'AcctSavingsBankMutation/FormAddAcctSavingsBankMutation_view';
			$this->load->view('MainPage_view',$data);
		}

		// public function getAcctSavingsAccount_Detail(){
		// 	$savings_account_id 	= $this->input->post('savings_account_id');

		// 	// $savings_account_id = 4;
			
		// 	$data 			= $this->AcctSavingsBankMutation_model->getAcctSavingsAccount_Detail($savings_account_id);

		// 	$result = array();
		// 	$result = array(
		// 		"savings_name" 					=> $data['savings_name'], 
		// 		"member_name"					=> $data['member_name'],
		// 		"member_address"				=> $data['member_address'],
		// 		"city_name"						=> $data['city_name'],
		// 		"kecamatan_name"				=> $data['kecamatan_name'],
		// 		"identity_name"					=> $data['identity_name'],
		// 		"member_identity_no"			=> $data['member_identity_no'],
		// 		"savings_account_last_balance"	=> $data['savings_account_last_balance'],
		// 	);
		// 	echo json_encode($result);		
		// }

		public function getMutationFunction(){
			$mutation_id 	= $this->input->post('mutation_id');

			// $mutation_id = 2;
			
			$mutation_function 			= $this->AcctSavingsBankMutation_model->getMutationFunction($mutation_id);
			echo json_encode($mutation_function);		
		}
		
		
		public function processAddAcctSavingsBankMutation(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'savings_account_id'					=> $this->input->post('savings_account_id', true),
				'mutation_id'							=> $this->input->post('mutation_id', true),
				'member_id'								=> $this->input->post('member_id', true),
				'savings_id'							=> $this->input->post('savings_id', true),
				'branch_id'								=> $auth['branch_id'],
				'bank_account_id'						=> $this->input->post('bank_account_id', true),
				'savings_bank_mutation_date'			=> date('Y-m-d'),
				'savings_bank_mutation_opening_balance'	=> $this->input->post('savings_bank_mutation_opening_balance', true),
				'savings_bank_mutation_last_balance'	=> $this->input->post('savings_bank_mutation_last_balance', true),
				'savings_bank_mutation_amount'			=> $this->input->post('savings_bank_mutation_amount', true),
				'savings_bank_mutation_amount_adm'		=> $this->input->post('savings_bank_mutation_amount_adm', true),
				'savings_bank_mutation_token'			=> $this->input->post('savings_bank_mutation_token', true),
				'savings_bank_mutation_remark'			=> $this->input->post('savings_bank_mutation_remark', true),
				'operated_name'							=> $auth['username'],
				'created_id'							=> $auth['user_id'],
				'created_on'							=> date('Y-m-d H:i:s'),
			);

			//print_r($data);exit;
			
			$this->form_validation->set_rules('savings_account_id', 'No. Mutasi', 'required');
			$this->form_validation->set_rules('bank_account_id', 'Transfer Bank', 'required');
			$this->form_validation->set_rules('savings_bank_mutation_amount', 'Jumlah Transaksi', 'required');
			
			$savings_bank_mutation_token 	= $this->AcctSavingsBankMutation_model->getSavingsBankMutationToken($data['savings_bank_mutation_token']);

			$transaction_module_code 		= "TTAB";
			$transaction_module_id 			= $this->AcctSavingsBankMutation_model->getTransactionModuleID($transaction_module_code);
			
			$journal_voucher_period 		= date("Ym", strtotime($data['savings_bank_mutation_date']));

			if($this->form_validation->run()==true){
				if($savings_bank_mutation_token->num_rows()==0){
					if($this->AcctSavingsBankMutation_model->insertAcctSavingsBankMutation($data)){
						$acctsavingsbank_last 			= $this->AcctSavingsBankMutation_model->getAcctSavingsBankMutation_Last($data['created_id']);

						$data_journal = array(
							'branch_id'							=> $auth['branch_id'],
							'journal_voucher_period' 			=> $journal_voucher_period,
							'journal_voucher_date'				=> date('Y-m-d'),
							'journal_voucher_title'				=> 'MUTASI BANK '.$acctsavingsbank_last['member_name'],
							'journal_voucher_description'		=> 'MUTASI BANK '.$acctsavingsbank_last['member_name'],
							'journal_voucher_token'				=> $data['savings_bank_mutation_token'],
							'transaction_module_id'				=> $transaction_module_id,
							'transaction_module_code'			=> $transaction_module_code,
							'transaction_journal_id' 			=> $acctsavingsbank_last['savings_bank_mutation_id'],
							'transaction_journal_no' 			=> $acctsavingsbank_last['savings_account_no'],
							'created_id' 						=> $data['created_id'],
							'created_on' 						=> $data['created_on'],
						);
						
						$this->AcctSavingsBankMutation_model->insertAcctJournalVoucher($data_journal);

						$journal_voucher_id 					= $this->AcctSavingsBankMutation_model->getJournalVoucherID($data['created_id']);

						$preferencecompany 						= $this->AcctSavingsBankMutation_model->getPreferenceCompany();

						if($data['mutation_id'] == 7){
							$account_bank_id					= $this->AcctSavingsBankMutation_model->getAccountBankID($data['bank_account_id']);

							$account_id_default_status 			= $this->AcctSavingsBankMutation_model->getAccountIDDefaultStatus($account_bank_id);

							$data_debet = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_bank_id,
								'journal_voucher_description'	=> 'SETORAN VIA BANK '.$acctsavingsbank_last['member_name'],
								'journal_voucher_amount'		=> $data['savings_bank_mutation_amount'],
								'journal_voucher_debit_amount'	=> $data['savings_bank_mutation_amount'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token'	=> $data['savings_bank_mutation_token'].$account_bank_id,
								'created_id'					=> $auth['user_id'],
							);

							$this->AcctSavingsBankMutation_model->insertAcctJournalVoucherItem($data_debet);

							$account_id 						= $this->AcctSavingsBankMutation_model->getAccountID($data['savings_id']);

							$account_id_default_status 			= $this->AcctSavingsBankMutation_model->getAccountIDDefaultStatus($account_id);

							$data_credit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_id,
								'journal_voucher_description'	=> 'SETORAN VIA BANK '.$acctsavingsbank_last['member_name'],
								'journal_voucher_amount'		=> $data['savings_bank_mutation_amount'],
								'journal_voucher_credit_amount'	=> $data['savings_bank_mutation_amount'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $data['savings_bank_mutation_token'].$account_id,
								'created_id'					=> $auth['user_id'],
							);

						$this->AcctSavingsBankMutation_model->insertAcctJournalVoucherItem($data_credit);

						if($data['savings_bank_mutation_amount_adm'] > 0){
							$data_debet = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['savings_bank_mutation_amount_adm'],
								'journal_voucher_debit_amount'	=> $data['savings_bank_mutation_amount_adm'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token'	=> $data['savings_bank_mutation_token'].$data['savings_bank_mutation_amount_adm'],
								'created_id'					=> $auth['user_id'],
							);

							$this->AcctSavingsBankMutation_model->insertAcctJournalVoucherItem($data_debet);
						}

						$preferencecompany = $this->AcctSavingsBankMutation_model->getPreferenceCompany();

						$account_id_default_status = $this->AcctSavingsBankMutation_model->getAccountIDDefaultStatus($preferencecompany['account_mutation_adm_id']);

						$data_credit =array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_mutation_adm_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['savings_bank_mutation_amount_adm'],
							'journal_voucher_credit_amount'	=> $data['savings_bank_mutation_amount_adm'],
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> 'Str'.$data['savings_bank_mutation_token'].$preferencecompany['account_mutation_adm_id'],
							'created_id'					=> $auth['user_id'],
						);

						$this->AcctSavingsBankMutation_model->insertAcctJournalVoucherItem($data_credit);

						} else if($data['mutation_id'] == 8){
							$account_id 						= $this->AcctSavingsBankMutation_model->getAccountID($data['savings_id']);

							$account_id_default_status 			= $this->AcctSavingsBankMutation_model->getAccountIDDefaultStatus($account_id);

							$data_debit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_id,
								'journal_voucher_description'	=> 'PENARIKAN VIA BANK '.$acctsavingsbank_last['member_name'],
								'journal_voucher_amount'		=> $data['savings_bank_mutation_amount'],
								'journal_voucher_debit_amount'	=> $data['savings_bank_mutation_amount'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token'	=> $data['savings_bank_mutation_token'].$account_id,
								'created_id'					=> $auth['user_id'],
							);

							$this->AcctSavingsBankMutation_model->insertAcctJournalVoucherItem($data_debit);

							$account_bank_id					= $this->AcctSavingsBankMutation_model->getAccountBankID($data['bank_account_id']);

							$account_id_default_status 			= $this->AcctSavingsBankMutation_model->getAccountIDDefaultStatus($account_bank_id);

							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_bank_id,
								'journal_voucher_description'	=> 'PENARIKAN VIA BANK '.$acctsavingsbank_last['member_name'],
								'journal_voucher_amount'		=> $data['savings_bank_mutation_amount'],
								'journal_voucher_credit_amount'	=> $data['savings_bank_mutation_amount'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $data['savings_bank_mutation_token'].$account_bank_id,
								'created_id'					=> $auth['user_id'],
							);

							$this->AcctSavingsBankMutation_model->insertAcctJournalVoucherItem($data_credit);

							
						if($data['savings_bank_mutation_amount_adm'] > 0){
							$data_debet = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['savings_bank_mutation_amount_adm'],
								'journal_voucher_debit_amount'	=> $data['savings_bank_mutation_amount_adm'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token'	=> 'PNR1'.$data['savings_bank_mutation_token'].$data['savings_bank_mutation_amount_adm'],
								'created_id'					=> $auth['user_id'],
							);

							$this->AcctSavingsBankMutation_model->insertAcctJournalVoucherItem($data_debet);
						}

						$preferencecompany = $this->AcctSavingsBankMutation_model->getPreferenceCompany();

						$account_id_default_status = $this->AcctSavingsBankMutation_model->getAccountIDDefaultStatus($preferencecompany['account_mutation_adm_id']);

						$data_credit =array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_mutation_adm_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['savings_bank_mutation_amount_adm'],
							'journal_voucher_credit_amount'	=> $data['savings_bank_mutation_amount_adm'],
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> 'PNR2'.$data['savings_bank_mutation_token'].$preferencecompany['account_mutation_adm_id'],
							'created_id'					=> $auth['user_id'],
						);

						$this->AcctSavingsBankMutation_model->insertAcctJournalVoucherItem($data_credit);
						} else if($data['mutation_id'] == 3){
							$account_bank_id					= $this->AcctSavingsBankMutation_model->getAccountBankID($data['bank_account_id']);

							$account_id_default_status 			= $this->AcctSavingsBankMutation_model->getAccountIDDefaultStatus($account_bank_id);

							$data_debet = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_bank_id,
								'journal_voucher_description'	=> 'KOREKSI KREDIT '.$acctsavingsbank_last['member_name'],
								'journal_voucher_amount'		=> $data['savings_bank_mutation_amount'],
								'journal_voucher_debit_amount'	=> $data['savings_bank_mutation_amount'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token'	=> $data['savings_bank_mutation_token'].$account_bank_id,
								'created_id'					=> $auth['user_id'],
							);

							$this->AcctSavingsBankMutation_model->insertAcctJournalVoucherItem($data_debet);

							$account_id 						= $this->AcctSavingsBankMutation_model->getAccountID($data['savings_id']);

							$account_id_default_status 			= $this->AcctSavingsBankMutation_model->getAccountIDDefaultStatus($account_id);

							$data_credit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_id,
								'journal_voucher_description'	=> 'KOREKSI KREDIT '.$acctsavingsbank_last['member_name'],
								'journal_voucher_amount'		=> $data['savings_bank_mutation_amount'],
								'journal_voucher_credit_amount'	=> $data['savings_bank_mutation_amount'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $data['savings_bank_mutation_token'].$account_id,
								'created_id'					=> $auth['user_id'],
							);

							$this->AcctSavingsBankMutation_model->insertAcctJournalVoucherItem($data_credit);

						} else if($data['mutation_id'] == 4){
							$account_id 						= $this->AcctSavingsBankMutation_model->getAccountID($data['savings_id']);

							$account_id_default_status 			= $this->AcctSavingsBankMutation_model->getAccountIDDefaultStatus($account_id);

							$data_debit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_id,
								'journal_voucher_description'	=> 'KOREKSI DEBET '.$acctsavingsbank_last['member_name'],
								'journal_voucher_amount'		=> $data['savings_bank_mutation_amount'],
								'journal_voucher_debit_amount'	=> $data['savings_bank_mutation_amount'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token'	=> $data['savings_bank_mutation_token'].$account_id,
								'created_id'					=> $auth['user_id'],
							);

							$this->AcctSavingsBankMutation_model->insertAcctJournalVoucherItem($data_debit);

							$account_bank_id					= $this->AcctSavingsBankMutation_model->getAccountBankID($data['bank_account_id']);

							$account_id_default_status 			= $this->AcctSavingsBankMutation_model->getAccountIDDefaultStatus($account_bank_id);

							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_bank_id,
								'journal_voucher_description'	=> 'KOREKSI DEBET '.$acctsavingsbank_last['member_name'],
								'journal_voucher_amount'		=> $data['savings_bank_mutation_amount'],
								'journal_voucher_credit_amount'	=> $data['savings_bank_mutation_amount'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $data['savings_bank_mutation_token'].$account_bank_id,
								'created_id'					=> $auth['user_id'],
							);

							$this->AcctSavingsBankMutation_model->insertAcctJournalVoucherItem($data_credit);
						}// else {
						// 	if($this->AcctSavingsBankMutation_model->closedAcctSavingsAccount($data['savings_account_id'])){
						// 		$account_id 						= $this->AcctSavingsBankMutation_model->getAccountID($data['savings_id']);

						// 		$account_id_default_status 			= $this->AcctSavingsBankMutation_model->getAccountIDDefaultStatus($account_id);

						// 		$data_debit =array(
						// 			'journal_voucher_id'			=> $journal_voucher_id,
						// 			'account_id'					=> $account_id,
						// 			'journal_voucher_description'	=> 'TUTUP REKENING '.$acctsavingsbank_last['member_name'],
						// 			'journal_voucher_amount'		=> $data['savings_bank_mutation_amount'],
						// 			'journal_voucher_debit_amount'	=> $data['savings_bank_mutation_amount'],
						// 			'account_id_default_status'		=> $account_id_default_status,
						// 			'account_id_status'				=> 0,
						// 			'journal_voucher_item_token'	=> $data['savings_bank_mutation_token'].$account_id,
						// 			'created_id'					=> $auth['user_id'],
						// 		);

						// 		$this->AcctSavingsBankMutation_model->insertAcctJournalVoucherItem($data_debit);

						// 		$account_id_default_status 			= $this->AcctSavingsBankMutation_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

						// 		$data_credit = array (
						// 			'journal_voucher_id'			=> $journal_voucher_id,
						// 			'account_id'					=> $preferencecompany['account_cash_id'],
						// 			'journal_voucher_description'	=> 'TUTUP REKENING '.$acctsavingsbank_last['member_name'],
						// 			'journal_voucher_amount'		=> $data['savings_bank_mutation_amount'],
						// 			'journal_voucher_credit_amount'	=> $data['savings_bank_mutation_amount'],
						// 			'account_id_default_status'		=> $account_id_default_status,
						// 			'account_id_status'				=> 1,
						// 			'journal_voucher_item_token'	=> $data['savings_bank_mutation_token'].$preferencecompany['account_cash_id'],
						//			'created_id'					=> $auth['user_id'],
						// 		);

						// 		$this->AcctSavingsBankMutation_model->insertAcctJournalVoucherItem($data_credit);
						// 	}
							
						// }
						$auth = $this->session->userdata('auth');
						// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Simpanan Bank Sukses
								</div> ";
						$sesi = $this->session->userdata('unique');
						$this->session->unset_userdata('addacctsavingsbankmutation-'.$sesi['unique']);
						$this->session->unset_userdata('acctsavingsbankmutationtoken-'.$sesi['unique']);
						$this->session->set_userdata('message',$msg);
						redirect('savings-bank-mutation/add');
					}else{
						$this->session->set_userdata('addacctsavingsbankmutation',$data);
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Simpanan Bank Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('savings-bank-mutation/add');
					}
				}else{
					$acctsavingsbank_last 			= $this->AcctSavingsBankMutation_model->getAcctSavingsBankMutation_Last($data['created_id']);
					
					$data_journal = array(
						'branch_id'							=> $auth['branch_id'],
						'journal_voucher_period' 			=> $journal_voucher_period,
						'journal_voucher_date'				=> date('Y-m-d'),
						'journal_voucher_title'				=> 'MUTASI BANK '.$acctsavingsbank_last['member_name'],
						'journal_voucher_description'		=> 'MUTASI BANK '.$acctsavingsbank_last['member_name'],
						'journal_voucher_token'				=> $data['savings_bank_mutation_token'],
						'transaction_module_id'				=> $transaction_module_id,
						'transaction_module_code'			=> $transaction_module_code,
						'transaction_journal_id' 			=> $acctsavingsbank_last['savings_bank_mutation_id'],
						'transaction_journal_no' 			=> $acctsavingsbank_last['savings_account_no'],
						'created_id' 						=> $data['created_id'],
						'created_on' 						=> $data['created_on'],
					);

					$journal_voucher_token 	= $this->AcctSavingsBankMutation_model->getJournalVoucherToken($data_journal['journal_voucher_token']);

					if($journal_voucher_token->num_rows()== 0){
						$this->AcctSavingsBankMutation_model->insertAcctJournalVoucher($data_journal);
					}

					$journal_voucher_id 	= $this->AcctSavingsBankMutation_model->getJournalVoucherID($data['created_id']);

					$preferencecompany 		= $this->AcctSavingsBankMutation_model->getPreferenceCompany();

					if($data['mutation_id'] == 7){
						$account_bank_id					= $this->AcctSavingsBankMutation_model->getAccountBankID($data['bank_account_id']);

						$account_id_default_status 			= $this->AcctSavingsBankMutation_model->getAccountIDDefaultStatus($account_bank_id);

						$data_debet = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_bank_id,
							'journal_voucher_description'	=> 'SETORAN VIA BANK '.$acctsavingsbank_last['member_name'],
							'journal_voucher_amount'		=> $data['savings_bank_mutation_amount'],
							'journal_voucher_debit_amount'	=> $data['savings_bank_mutation_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token'	=> $data['savings_bank_mutation_token'].$account_bank_id,
							'created_id'					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctSavingsBankMutation_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctSavingsBankMutation_model->insertAcctJournalVoucherItem($data_debet);
						}

						$account_id 						= $this->AcctSavingsBankMutation_model->getAccountID($data['savings_id']);

						$account_id_default_status 			= $this->AcctSavingsBankMutation_model->getAccountIDDefaultStatus($account_id);

						$data_credit = array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_id,
							'journal_voucher_description'	=> 'SETORAN VIA BANK '.$acctsavingsbank_last['member_name'],
							'journal_voucher_amount'		=> $data['savings_bank_mutation_amount'],
							'journal_voucher_credit_amount'	=> $data['savings_bank_mutation_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $data['savings_bank_mutation_token'].$account_id,
						);

						$journal_voucher_item_token 		= $this->AcctSavingsBankMutation_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctSavingsBankMutation_model->insertAcctJournalVoucherItem($data_credit);
						}

						$data_debet = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_cash_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['savings_bank_mutation_amount_adm'],
							'journal_voucher_debit_amount'	=> $data['savings_bank_mutation_amount_adm'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token'	=> 'STR1'.$data['savings_bank_mutation_token'].$data['savings_bank_mutation_amount_adm'],
							'created_id'					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctSavingsBankMutation_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctSavingsBankMutation_model->insertAcctJournalVoucherItem($data_debet);
						}

						$preferencecompany = $this->AcctSavingsBankMutation_model->getPreferenceCompany();

						$account_id_default_status = $this->AcctSavingsBankMutation_model->getAccountIDDefaultStatus($preferencecompany['account_mutation_adm_id']);

						$data_credit =array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_mutation_adm_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['savings_bank_mutation_amount_adm'],
							'journal_voucher_credit_amount'	=> $data['savings_bank_mutation_amount_adm'],
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> 'Str'.$data['savings_bank_mutation_token'].$preferencecompany['account_mutation_adm_id'],
							'created_id'					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctSavingsBankMutation_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctSavingsBankMutation_model->insertAcctJournalVoucherItem($data_credit);
						}

					} else {	
						$account_id 						= $this->AcctSavingsBankMutation_model->getAccountID($data['savings_id']);

						$account_id_default_status 			= $this->AcctSavingsBankMutation_model->getAccountIDDefaultStatus($account_id);

						$data_debit =array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_id,
							'journal_voucher_description'	=> 'PENARIKAN VIA BANK '.$acctsavingsbank_last['member_name'],
							'journal_voucher_amount'		=> $data['savings_bank_mutation_amount'],
							'journal_voucher_debit_amount'	=> $data['savings_bank_mutation_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token'	=> $data['savings_bank_mutation_token'].$account_id,
							'created_id'					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctSavingsBankMutation_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctSavingsBankMutation_model->insertAcctJournalVoucherItem($data_debit);
						}

						$account_bank_id					= $this->AcctSavingsBankMutation_model->getAccountBankID($data['bank_account_id']);

						$account_id_default_status 			= $this->AcctSavingsBankMutation_model->getAccountIDDefaultStatus($account_bank_id);

						$data_credit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_bank_id,
							'journal_voucher_description'	=> 'PENARIKAN VIA BANK '.$acctsavingsbank_last['member_name'],
							'journal_voucher_amount'		=> $data['savings_bank_mutation_amount'],
							'journal_voucher_credit_amount'	=> $data['savings_bank_mutation_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $data['savings_bank_mutation_token'].$account_bank_id,
							'created_id'					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctSavingsBankMutation_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctSavingsBankMutation_model->insertAcctJournalVoucherItem($data_credit);
						}
					}

					$data_debet = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_cash_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['savings_bank_mutation_amount_adm'],
							'journal_voucher_debit_amount'	=> $data['savings_bank_mutation_amount_adm'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token'	=> 'PNR1'.$data['savings_bank_mutation_token'].$data['savings_bank_mutation_amount_adm'],
							'created_id'					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctSavingsBankMutation_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctSavingsBankMutation_model->insertAcctJournalVoucherItem($data_debet);
						}

						$preferencecompany = $this->AcctSavingsBankMutation_model->getPreferenceCompany();

						$account_id_default_status = $this->AcctSavingsBankMutation_model->getAccountIDDefaultStatus($preferencecompany['account_mutation_adm_id']);

						$data_credit =array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_mutation_adm_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['savings_bank_mutation_amount_adm'],
							'journal_voucher_credit_amount'	=> $data['savings_bank_mutation_amount_adm'],
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> 'PNR2'.$data['savings_bank_mutation_token'].$preferencecompany['account_mutation_adm_id'],
							'created_id'					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctSavingsBankMutation_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctSavingsBankMutation_model->insertAcctJournalVoucherItem($data_credit);
						}

					

					
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Mutasi Simpanan Sukses
							</div> ";
					$sesi = $this->session->userdata('unique');
					$this->session->unset_userdata('addacctsavingsbankmutation-'.$sesi['unique']);
					$this->session->unset_userdata('acctsavingsbankmutationtoken-'.$sesi['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('savings-bank-mutation/add');
					
				
				}
			}else{
				$this->session->set_userdata('addacctsavingsbankmutation',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('savings-bank-mutation/add');
			}
		}
		
		public function voidAcctSavingsBankMutation(){
			$data['main_view']['acctsavingsbankmutation']	= $this->AcctSavingsBankMutation_model->getAcctSavingsBankMutation_Detail($this->uri->segment(3));
			$data['main_view']['content']					= 'AcctSavingsBankMutation/FormVoidAcctSavingsBankMutation_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processVoidAcctSavingsBankMutation(){
			$auth	= $this->session->userdata('auth');

			$newdata = array (
				"savings_bank_mutation_id"	=> $this->input->post('savings_bank_mutation_id',true),
				"voided_on"					=> date('Y-m-d H:i:s'),
				'data_state'				=> 2,
				"voided_remark" 			=> $this->input->post('voided_remark',true),
				"voided_id"					=> $auth['user_id']
			);
			
			$this->form_validation->set_rules('voided_remark', 'Keterangan', 'required');

			if($this->form_validation->run()==true){
				if($this->AcctSavingsBankMutation_model->voidAcctSavingsBankMutation($newdata)){
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Pembatalan Mutasi Setoran Simpanan Non Tunai Sukses
							</div>";
					$this->session->set_userdata('message',$msg);
					redirect('savings-bank-mutation');
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Pembatalan Mutasi Setoran Simpanan Non Tunai Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('savings-bank-mutation');
				}
					
			}else{
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('savings-bank-mutation');
			}
		}
		

		public function function_state_add(){
			$unique 	= $this->session->userdata('unique');
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctsavingsbankmutation-'.$unique['unique']);
			$sessions['active_tab'] = $value;
			$this->session->set_userdata('addacctsavingsbankmutation-'.$unique['unique'],$sessions);
		}
		
		public function function_elements_add(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctsavingsbankmutation-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('addacctsavingsbankmutation-'.$unique['unique'],$sessions);
		}

		public function reset_data(){
			$unique 	= $this->session->userdata('unique');
			$sessions	= $this->session->unset_userdata('addacctsavingsbankmutation-'.$unique['unique']);
			redirect('savings-bank-mutation/add');
		}
		public function reset_search(){
			$this->session->unset_userdata('filter-acctsavingsbankmutation');
			redirect('savings-bank-mutation');
		}
		
	}
?>