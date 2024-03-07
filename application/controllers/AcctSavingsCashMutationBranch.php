<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctSavingsCashMutationBranch extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctSavingsCashMutationBranch_model');
			$this->load->model('AcctSavingsAccount_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$auth 	= $this->session->userdata('auth');

			$data['main_view']['content']			= 'AcctSavingsCashMutationBranch/ListAcctSavingsCashMutationBranch_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date" 				=> tgltodb($this->input->post('start_date',true)),
				"end_date" 					=> tgltodb($this->input->post('end_date',true)),
				
			);

			$this->session->set_userdata('filter-acctsavingsmutationbranch',$data);
			redirect('savings-cash-mutation-branch');
		}
		public function reset_search(){
			$this->session->unset_userdata('filter-acctsavingsmutationbranch');
			redirect('savings-cash-mutation-branch');
			
		}

		public function getAcctSavingsCashMutationBranch(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-acctsavingsmutationbranch');
			if(!is_array($sesi)){
				$sesi['start_date']				= date('Y-m-d');
				$sesi['end_date']				= date('Y-m-d');
				
			}

			$savingscashstatus = $this->configuration->SavingsCashMutationStatus();

			$list = $this->AcctSavingsCashMutationBranch_model->get_datatables($sesi['start_date'], $sesi['end_date'], $auth['branch_id']);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $savingsaccount) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $savingsaccount->member_name;
	            $row[] = $savingsaccount->savings_name;
	            $row[] = $savingsaccount->savings_account_no;
	            $row[] = tgltoview($savingsaccount->savings_cash_mutation_date);
	            $row[] = $savingsaccount->mutation_name;
	            $row[] = number_format($savingsaccount->savings_cash_mutation_amount, 2);
	            $row[] = $savingscashstatus[$savingsaccount->savings_cash_mutation_status];
	            if($savingsaccount->validation_status == 0){
	            	$row[] = '<a href="'.base_url().'savings-cash-mutation-branch/print-note/'.$savingsaccount->savings_cash_mutation_id.'" class="btn btn-info btn-xs" role="button"><span class="glyphicon glyphicon-print"></span> Kwitansi</a> 

	            		<a href="'.base_url().'savings-cash-mutation-branch/validation/'.$savingsaccount->savings_cash_mutation_id.'" class="btn btn-success btn-xs" role="button"><span class="glyphicon glyphicon-check"></span> Validasi</a>';
	            } else {
	            	 $row[] = '<a href="'.base_url().'savings-cash-mutation-branch/print-note/'.$savingsaccount->savings_cash_mutation_id.'" class="btn btn-info btn-xs" role="button"><span class="glyphicon glyphicon-print"></span> Kwitansi</a>';
	            }
	            $data[] = $row;
	        }

	        // print_r($list);exit;
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctSavingsCashMutationBranch_model->count_all($sesi['start_date'], $sesi['end_date'], $auth['branch_id']),
	                        "recordsFiltered" => $this->AcctSavingsCashMutationBranch_model->count_filtered($sesi['start_date'], $sesi['end_date'], $auth['branch_id']),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);

		}

		public function function_elements_add(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctsavingscashmutation-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('addacctsavingscashmutation-'.$unique['unique'],$sessions);
		}

		public function reset_data(){
			$unique 	= $this->session->userdata('unique');
			$sessions	= $this->session->unset_userdata('addacctsavingscashmutation-'.$unique['unique']);
			redirect('savings-cash-mutation-branch/add');
		}

		public function getListAcctSavingsAccount(){
			$auth 		= $this->session->userdata('auth');
			$branch_id 	= '';
			$list 		= $this->AcctSavingsAccount_model->get_datatables($branch_id);
	        $data 		= array();
	        $no 		= $_POST['start'];
	        
	        foreach ($list as $savingsaccount) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $savingsaccount->savings_account_no;
	            $row[] = $savingsaccount->member_name;
	            $row[] = $savingsaccount->member_address;
	            $row[] = '<a href="'.base_url().'savings-cash-mutation-branch/add/'.$savingsaccount->savings_account_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctSavingsAccount_model->count_all($branch_id),
	                        "recordsFiltered" => $this->AcctSavingsAccount_model->count_filtered($branch_id),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);

		}
		
		public function addAcctSavingsCashMutationBranch(){
			$savings_account_id = $this->uri->segment(3);
			$unique 	= $this->session->userdata('unique');
			$token 	= $this->session->userdata('acctsavingscashmutationbranchtoken-'.$unique['unique']);

			if(empty($token)){
				$token = md5(date('Y-m-d H:i:s'));
				$this->session->set_userdata('acctsavingscashmutationbranchtoken-'.$unique['unique'], $token);
			}


			$data['main_view']['acctsavingsaccount']		= $this->AcctSavingsCashMutationBranch_model->getAcctSavingsAccount_Detail($savings_account_id);	
			$data['main_view']['acctmutation']				= create_double($this->AcctSavingsCashMutationBranch_model->getAcctMutation(),'mutation_id', 'mutation_name');
			$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();
			$data['main_view']['content']					= 'AcctSavingsCashMutationBranch/FormAddAcctSavingsCashMutationBranch_view';
			$this->load->view('MainPage_view',$data);
		}

		public function getMutationFunction(){
			$mutation_id 	= $this->input->post('mutation_id');

			// $mutation_id = 2;
			
			$mutation_function 			= $this->AcctSavingsCashMutationBranch_model->getMutationFunction($mutation_id);
			echo json_encode($mutation_function);		
		}
		
		public function processAddAcctSavingsCashMutationBranch(){
			$auth = $this->session->userdata('auth');

			$branch_asal = $this->input->post('branch_asal_id', true);

			$data = array(
				'savings_account_id'						=> $this->input->post('savings_account_id', true),
				'mutation_id'								=> $this->input->post('mutation_id', true),
				'member_id'									=> $this->input->post('member_id', true),
				'savings_id'								=> $this->input->post('savings_id', true),
				'branch_id'									=> $auth['branch_id'],
				'savings_cash_mutation_date'				=> date('Y-m-d'),
				'savings_cash_mutation_opening_balance'		=> $this->input->post('savings_cash_mutation_opening_balance', true),
				'savings_cash_mutation_last_balance'		=> $this->input->post('savings_cash_mutation_last_balance', true),
				'savings_cash_mutation_amount'				=> $this->input->post('savings_cash_mutation_amount', true),
				'savings_cash_mutation_remark'				=> $this->input->post('savings_cash_mutation_remark', true),
				'savings_cash_mutation_token'				=> $this->input->post('savings_cash_mutation_token', true),
				'savings_cash_mutation_branch'				=> 1,
				'operated_name'								=> $auth['username'],
				'created_id'								=> $auth['user_id'],
				'created_on'								=> date('Y-m-d H:i:s'),
			);
			
			$this->form_validation->set_rules('savings_account_id', 'No. Mutasi', 'required');
			$this->form_validation->set_rules('mutation_id', 'Sandi', 'required');
			$this->form_validation->set_rules('savings_cash_mutation_amount', 'Jumlah Transaksi', 'required');

			$savings_cash_mutation_token 					= $this->AcctSavingsCashMutationBranch_model->getAcctSavingsCashMutationToken($data['savings_cash_mutation_token']);
			
			if($this->form_validation->run()==true){
				if($savings_cash_mutation_token->num_rows()==0){
					if($this->AcctSavingsCashMutationBranch_model->insertAcctSavingsCashMutationBranch($data)){
						$transaction_module_code = "TTAB";

						$transaction_module_id 	= $this->AcctSavingsCashMutationBranch_model->getTransactionModuleID($transaction_module_code);
						$acctsavingscash_last 	= $this->AcctSavingsCashMutationBranch_model->getAcctSavingsCashMutationBranch_Last($data['created_id']);

							
						$journal_voucher_period = date("Ym", strtotime($data['savings_cash_mutation_date']));

						//-----------------------------Jurnal Cabang yang Menerima----------------------------------------
						
						$data_journal_terima = array(
							'branch_id'						=> $auth['branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'MUTASI TUNAI '.$acctsavingscash_last['member_name'],
							'journal_voucher_description'	=> 'MUTASI TUNAI '.$acctsavingscash_last['member_name'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $acctsavingscash_last['savings_cash_mutation_id'],
							'transaction_journal_no' 		=> $acctsavingscash_last['savings_account_no'],
							'journal_voucher_token' 		=> $data['savings_cash_mutation_token'],
							'created_id' 					=> $data['created_id'],
							'created_on' 					=> $data['created_on'],
						);
						
						$this->AcctSavingsCashMutationBranch_model->insertAcctJournalVoucher($data_journal_terima);

						$journal_voucher_id = $this->AcctSavingsCashMutationBranch_model->getJournalVoucherID($data['created_id']);

						$preferencecompany = $this->AcctSavingsCashMutationBranch_model->getPreferenceCompany();

						if($data['mutation_id'] == $preferencecompany['cash_deposit_id']){
							$account_id_default_status = $this->AcctSavingsCashMutationBranch_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

							$data_debet = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> 'SETORAN TUNAI '.$acctsavingscash_last['member_name'],
								'journal_voucher_amount'		=> $data['savings_cash_mutation_amount'],
								'journal_voucher_debit_amount'	=> $data['savings_cash_mutation_amount'],
								'account_id_default_status'		=> $account_id_default_status,
								'journal_voucher_item_token' 	=> $data['savings_cash_mutation_token'].$preferencecompany['account_cash_id'],
								'account_id_status'				=> 0,
								'created_id'					=> $auth['user_id'],
							);

							$this->AcctSavingsCashMutationBranch_model->insertAcctJournalVoucherItem($data_debet);

							$account_rak_id 			= $this->AcctSavingsCashMutationBranch_model->getAccountRAKID($branch_asal);

							$account_id_default_status 	= $this->AcctSavingsCashMutationBranch_model->getAccountIDDefaultStatus($account_rak_id);

							$data_credit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_rak_id,
								'journal_voucher_description'	=> 'SETORAN TUNAI '.$acctsavingscash_last['member_name'],
								'journal_voucher_amount'		=> $data['savings_cash_mutation_amount'],
								'journal_voucher_credit_amount'	=> $data['savings_cash_mutation_amount'],
								'account_id_default_status'		=> $account_id_default_status,
								'journal_voucher_item_token' 	=> $data['savings_cash_mutation_token'].$account_rak_id,
								'account_id_status'				=> 1,
								'created_id'					=> $auth['user_id'],
							);

							$this->AcctSavingsCashMutationBranch_model->insertAcctJournalVoucherItem($data_credit);

						} else {
							$mutation_type = '';
							if($data['mutation_id'] == 2){
								$mutation_type = 'PENARIKAN TUNAI';
							}else if($data['mutation_id'] == 3){
								$mutation_type = 'KOREKSI KREDIT';
							}else if($data['mutation_id'] == 4){
								$mutation_type = 'KOREKSI DEBET';
							}else{
								$mutation_type = 'TUTUP REKENING'; //masuk else
							}
							$account_rak_id 			= $this->AcctSavingsCashMutationBranch_model->getAccountRAKID($branch_asal);

							$account_id_default_status 	= $this->AcctSavingsCashMutationBranch_model->getAccountIDDefaultStatus($account_rak_id);

							$data_debit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_rak_id,
								'journal_voucher_description'	=> $mutation_type.' '.$acctsavingscash_last['member_name'],
								'journal_voucher_amount'		=> $data['savings_cash_mutation_amount'],
								'journal_voucher_debit_amount'	=> $data['savings_cash_mutation_amount'],
								'account_id_default_status'		=> $account_id_default_status,
								'journal_voucher_item_token' 	=> $data['savings_cash_mutation_token'].$account_rak_id,
								'account_id_status'				=> 0,
								'created_id'					=> $auth['user_id'],
							);

							$this->AcctSavingsCashMutationBranch_model->insertAcctJournalVoucherItem($data_debit);

							$account_id_default_status = $this->AcctSavingsCashMutationBranch_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$acctsavingscash_last['member_name'],
								'journal_voucher_amount'		=> $data['savings_cash_mutation_amount'],
								'journal_voucher_credit_amount'	=> $data['savings_cash_mutation_amount'],
								'account_id_default_status'		=> $account_id_default_status,
								'journal_voucher_item_token' 	=> $data['savings_cash_mutation_token'].$preferencecompany['account_cash_id'],
								'account_id_status'				=> 1,
								'created_id'					=> $auth['user_id'],
							);

							$this->AcctSavingsCashMutationBranch_model->insertAcctJournalVoucherItem($data_credit);
						}

						//---------------------------Jurnal Cabang Asal-------------------------------------------

						$data_journal_asal = array(
							'branch_id'						=> $branch_asal,
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'MUTASI TUNAI '.$acctsavingscash_last['member_name'],
							'journal_voucher_description'	=> 'MUTASI TUNAI '.$acctsavingscash_last['member_name'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $acctsavingscash_last['savings_cash_mutation_id'],
							'transaction_journal_no' 		=> $acctsavingscash_last['savings_account_no'],
							'journal_voucher_token' 		=> $data['savings_cash_mutation_token'].'asal',
							'created_id' 					=> $data['created_id'],
							'created_on' 					=> $data['created_on'],
						);
						
						$this->AcctSavingsCashMutationBranch_model->insertAcctJournalVoucher($data_journal_asal);

						$journal_voucher_id = $this->AcctSavingsCashMutationBranch_model->getJournalVoucherID($data['created_id']);

						$preferencecompany = $this->AcctSavingsCashMutationBranch_model->getPreferenceCompany();

						if($data['mutation_id'] == $preferencecompany['cash_deposit_id']){
							$account_aka_id 			= $this->AcctSavingsCashMutationBranch_model->getAccountAKAID($auth['branch_id']);

							$account_id_default_status 	= $this->AcctSavingsCashMutationBranch_model->getAccountIDDefaultStatus($account_aka_id);

							$data_debet = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_aka_id,
								'journal_voucher_description'	=> 'SETORAN TUNAI '.$acctsavingscash_last['member_name'],
								'journal_voucher_amount'		=> $data['savings_cash_mutation_amount'],
								'journal_voucher_debit_amount'	=> $data['savings_cash_mutation_amount'],
								'account_id_default_status'		=> $account_id_default_status,
								'journal_voucher_item_token' 	=> $data['savings_cash_mutation_token'].$account_aka_id.'asal',
								'account_id_status'				=> 0,
								'created_id'					=> $auth['user_id'],
							);

							$this->AcctSavingsCashMutationBranch_model->insertAcctJournalVoucherItem($data_debet);

							$account_id = $this->AcctSavingsCashMutationBranch_model->getAccountID($data['savings_id']);

							$account_id_default_status = $this->AcctSavingsCashMutationBranch_model->getAccountIDDefaultStatus($account_id);

							$data_credit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_id,
								'journal_voucher_description'	=> 'SETORAN TUNAI '.$acctsavingscash_last['member_name'],
								'journal_voucher_amount'		=> $data['savings_cash_mutation_amount'],
								'journal_voucher_credit_amount'	=> $data['savings_cash_mutation_amount'],
								'account_id_default_status'		=> $account_id_default_status,
								'journal_voucher_item_token' 	=> $data['savings_cash_mutation_token'].$account_id.'asal',
								'account_id_status'				=> 1,
								'created_id'					=> $auth['user_id'],
							);

							$this->AcctSavingsCashMutationBranch_model->insertAcctJournalVoucherItem($data_credit);

						} else {			
							$account_id = $this->AcctSavingsCashMutationBranch_model->getAccountID($data['savings_id']);

							$account_id_default_status = $this->AcctSavingsCashMutationBranch_model->getAccountIDDefaultStatus($account_id);

							$data_debit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_id,
								'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$acctsavingscash_last['member_name'],
								'journal_voucher_amount'		=> $data['savings_cash_mutation_amount'],
								'journal_voucher_debit_amount'	=> $data['savings_cash_mutation_amount'],
								'account_id_default_status'		=> $account_id_default_status,
								'journal_voucher_item_token' 	=> $data['savings_cash_mutation_token'].$account_id.'asal',
								'account_id_status'				=> 0,
								'created_id'					=> $auth['user_id'],
							);

							$this->AcctSavingsCashMutationBranch_model->insertAcctJournalVoucherItem($data_debit);

							$account_aka_id 			= $this->AcctSavingsCashMutationBranch_model->getAccountAKAID($auth['branch_id']);

							$account_id_default_status 	= $this->AcctSavingsCashMutationBranch_model->getAccountIDDefaultStatus($account_aka_id);

							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_aka_id,
								'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$acctsavingscash_last['member_name'],
								'journal_voucher_amount'		=> $data['savings_cash_mutation_amount'],
								'journal_voucher_credit_amount'	=> $data['savings_cash_mutation_amount'],
								'account_id_default_status'		=> $account_id_default_status,
								'journal_voucher_item_token' 	=> $data['savings_cash_mutation_token'].$account_aka_id.'asal',
								'account_id_status'				=> 1,
								'created_id'					=> $auth['user_id'],
							);

							$this->AcctSavingsCashMutationBranch_model->insertAcctJournalVoucherItem($data_credit);
						}

						

						
						$auth = $this->session->userdata('auth');
						// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Mutasi Simpanan Sukses
								</div> ";
						$sesi = $this->session->userdata('unique');
						$this->session->unset_userdata('addacctsavingscashmutation-'.$sesi['unique']);
						$this->session->unset_userdata('acctsavingscashmutationbranchtoken-'.$sesi['unique']);
						$this->session->set_userdata('message',$msg);
						redirect('savings-cash-mutation-branch/print-note/'.$acctsavingscash_last['savings_cash_mutation_id']);
					}else{
						$this->session->set_userdata('addacctsavingscashmutation',$data);
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Mutasi Simpanan Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('savings-cash-mutation-branch');
					}
				}else{
					$transaction_module_code = "TTAB";

					$transaction_module_id 	= $this->AcctSavingsCashMutationBranch_model->getTransactionModuleID($transaction_module_code);
					$acctsavingscash_last 	= $this->AcctSavingsCashMutationBranch_model->getAcctSavingsCashMutationBranch_Last($data['created_id']);

						
					$journal_voucher_period = date("Ym", strtotime($data['savings_cash_mutation_date']));

					//-----------------------------Jurnal Cabang yang Menerima----------------------------------------
					
					$data_journal_terima = array(
						'branch_id'						=> $auth['branch_id'],
						'journal_voucher_period' 		=> $journal_voucher_period,
						'journal_voucher_date'			=> date('Y-m-d'),
						'journal_voucher_title'			=> 'MUTASI TUNAI '.$acctsavingscash_last['member_name'],
						'journal_voucher_description'	=> 'MUTASI TUNAI '.$acctsavingscash_last['member_name'],
						'transaction_module_id'			=> $transaction_module_id,
						'transaction_module_code'		=> $transaction_module_code,
						'transaction_journal_id' 		=> $acctsavingscash_last['savings_cash_mutation_id'],
						'transaction_journal_no' 		=> $acctsavingscash_last['savings_account_no'],
						'journal_voucher_token' 		=> $data['savings_cash_mutation_token'],
						'created_id' 					=> $data['created_id'],
						'created_on' 					=> $data['created_on'],
					);
					
					$journal_voucher_token 		= $this->AcctSavingsCashMutationBranch_model->getAcctJournalVoucherToken($data_journal_terima['journal_voucher_token']);
						
					if($journal_voucher_token->num_rows()==0){
						$this->AcctSavingsCashMutationBranch_model->insertAcctJournalVoucher($data_journal_terima);
					}

					$journal_voucher_id = $this->AcctSavingsCashMutationBranch_model->getJournalVoucherID($data['created_id']);

					$preferencecompany = $this->AcctSavingsCashMutationBranch_model->getPreferenceCompany();

					if($data['mutation_id'] == $preferencecompany['cash_deposit_id']){
						$account_id_default_status = $this->AcctSavingsCashMutationBranch_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

						$data_debet = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_cash_id'],
							'journal_voucher_description'	=> 'SETORAN TUNAI '.$acctsavingscash_last['member_name'],
							'journal_voucher_amount'		=> $data['savings_cash_mutation_amount'],
							'journal_voucher_debit_amount'	=> $data['savings_cash_mutation_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'journal_voucher_item_token' 	=> $data['savings_cash_mutation_token'].$preferencecompany['account_cash_id'],
							'account_id_status'				=> 0,
							'created_id'					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctSavingsCashMutationBranch_model->getAcctJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctSavingsCashMutationBranch_model->insertAcctJournalVoucherItem($data_debet);
						}

						$account_rak_id 			= $this->AcctSavingsCashMutationBranch_model->getAccountRAKID($branch_asal);

						$account_id_default_status 	= $this->AcctSavingsCashMutationBranch_model->getAccountIDDefaultStatus($account_rak_id);

						$data_credit =array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_rak_id,
							'journal_voucher_description'	=> 'SETORAN TUNAI '.$acctsavingscash_last['member_name'],
							'journal_voucher_amount'		=> $data['savings_cash_mutation_amount'],
							'journal_voucher_credit_amount'	=> $data['savings_cash_mutation_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'journal_voucher_item_token' 	=> $data['savings_cash_mutation_token'].$account_rak_id,
							'account_id_status'				=> 1,
							'created_id'					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctSavingsCashMutationBranch_model->getAcctJournalVoucherItemToken($data_credit['journal_voucher_item_token']);
						
						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctSavingsCashMutationBranch_model->insertAcctJournalVoucherItem($data_credit);
						}

					} else {
						$account_rak_id 			= $this->AcctSavingsCashMutationBranch_model->getAccountRAKID($branch_asal);

						$account_id_default_status 	= $this->AcctSavingsCashMutationBranch_model->getAccountIDDefaultStatus($account_rak_id);

						$data_debit =array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_rak_id,
							'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$acctsavingscash_last['member_name'],
							'journal_voucher_amount'		=> $data['savings_cash_mutation_amount'],
							'journal_voucher_debit_amount'	=> $data['savings_cash_mutation_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'journal_voucher_item_token' 	=> $data['savings_cash_mutation_token'].$account_rak_id,
							'account_id_status'				=> 0,
							'created_id'					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctSavingsCashMutationBranch_model->getAcctJournalVoucherItemToken($data_debit['journal_voucher_item_token']);
						
						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctSavingsCashMutationBranch_model->insertAcctJournalVoucherItem($data_debit);
						}

						$account_id_default_status = $this->AcctSavingsCashMutationBranch_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

						$data_credit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_cash_id'],
							'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$acctsavingscash_last['member_name'],
							'journal_voucher_amount'		=> $data['savings_cash_mutation_amount'],
							'journal_voucher_credit_amount'	=> $data['savings_cash_mutation_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'journal_voucher_item_token' 	=> $data['savings_cash_mutation_token'].$preferencecompany['account_cash_id'],
							'account_id_status'				=> 1,
							'created_id'					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctSavingsCashMutationBranch_model->getAcctJournalVoucherItemToken($data_credit['journal_voucher_item_token']);
						
						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctSavingsCashMutationBranch_model->insertAcctJournalVoucherItem($data_credit);
						}
					}

					//---------------------------Jurnal Cabang Asal-------------------------------------------

					$data_journal_asal = array(
						'branch_id'						=> $branch_asal,
						'journal_voucher_period' 		=> $journal_voucher_period,
						'journal_voucher_date'			=> date('Y-m-d'),
						'journal_voucher_title'			=> 'MUTASI TUNAI '.$acctsavingscash_last['member_name'],
						'journal_voucher_description'	=> 'MUTASI TUNAI '.$acctsavingscash_last['member_name'],
						'transaction_module_id'			=> $transaction_module_id,
						'transaction_module_code'		=> $transaction_module_code,
						'transaction_journal_id' 		=> $acctsavingscash_last['savings_cash_mutation_id'],
						'transaction_journal_no' 		=> $acctsavingscash_last['savings_account_no'],
						'journal_voucher_token' 		=> $data['savings_cash_mutation_token'].'asal',
						'created_id' 					=> $data['created_id'],
						'created_on' 					=> $data['created_on'],
					);
					
					$journal_voucher_token 		= $this->AcctSavingsCashMutationBranch_model->getAcctJournalVoucherToken($data_journal_asal['journal_voucher_token']);
						
					if($journal_voucher_token->num_rows()==0){
						$this->AcctSavingsCashMutationBranch_model->insertAcctJournalVoucher($data_journal_asal);
					}

					$journal_voucher_id = $this->AcctSavingsCashMutationBranch_model->getJournalVoucherID($data['created_id']);

					$preferencecompany = $this->AcctSavingsCashMutationBranch_model->getPreferenceCompany();

					if($data['mutation_id'] == $preferencecompany['cash_deposit_id']){
						$account_aka_id 			= $this->AcctSavingsCashMutationBranch_model->getAccountAKAID($auth['branch_id']);

						$account_id_default_status 	= $this->AcctSavingsCashMutationBranch_model->getAccountIDDefaultStatus($account_aka_id);

						$data_debet = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_aka_id,
							'journal_voucher_description'	=> 'SETORAN TUNAI '.$acctsavingscash_last['member_name'],
							'journal_voucher_amount'		=> $data['savings_cash_mutation_amount'],
							'journal_voucher_debit_amount'	=> $data['savings_cash_mutation_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'journal_voucher_item_token' 	=> $data['savings_cash_mutation_token'].$account_aka_id.'asal',
							'account_id_status'				=> 0,
							'created_id'					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctSavingsCashMutationBranch_model->getAcctJournalVoucherItemToken($data_debet['journal_voucher_item_token']);
						
						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctSavingsCashMutationBranch_model->insertAcctJournalVoucherItem($data_debet);
						}

						$account_id = $this->AcctSavingsCashMutationBranch_model->getAccountID($data['savings_id']);

						$account_id_default_status = $this->AcctSavingsCashMutationBranch_model->getAccountIDDefaultStatus($account_id);

						$data_credit =array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_id,
							'journal_voucher_description'	=> 'SETORAN TUNAI '.$acctsavingscash_last['member_name'],
							'journal_voucher_amount'		=> $data['savings_cash_mutation_amount'],
							'journal_voucher_credit_amount'	=> $data['savings_cash_mutation_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'journal_voucher_item_token' 	=> $data['savings_cash_mutation_token'].$account_id.'asal',
							'account_id_status'				=> 1,
							'created_id'					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctSavingsCashMutationBranch_model->getAcctJournalVoucherItemToken($data_credit['journal_voucher_item_token']);
						
						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctSavingsCashMutationBranch_model->insertAcctJournalVoucherItem($data_credit);
						}

					} else {			
						$account_id = $this->AcctSavingsCashMutationBranch_model->getAccountID($data['savings_id']);

						$account_id_default_status = $this->AcctSavingsCashMutationBranch_model->getAccountIDDefaultStatus($account_id);

						$data_debit =array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_id,
							'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$acctsavingscash_last['member_name'],
							'journal_voucher_amount'		=> $data['savings_cash_mutation_amount'],
							'journal_voucher_debit_amount'	=> $data['savings_cash_mutation_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'journal_voucher_item_token' 	=> $data['savings_cash_mutation_token'].$account_id.'asal',
							'account_id_status'				=> 0,
							'created_id'					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctSavingsCashMutationBranch_model->getAcctJournalVoucherItemToken($data_debit['journal_voucher_item_token']);
						
						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctSavingsCashMutationBranch_model->insertAcctJournalVoucherItem($data_debit);
						}

						$account_aka_id 			= $this->AcctSavingsCashMutationBranch_model->getAccountAKAID($auth['branch_id']);

						$account_id_default_status 	= $this->AcctSavingsCashMutationBranch_model->getAccountIDDefaultStatus($account_aka_id);

						$data_credit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_aka_id,
							'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$acctsavingscash_last['member_name'],
							'journal_voucher_amount'		=> $data['savings_cash_mutation_amount'],
							'journal_voucher_credit_amount'	=> $data['savings_cash_mutation_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'journal_voucher_item_token' 	=> $data['savings_cash_mutation_token'].$account_aka_id.'asal',
							'account_id_status'				=> 1,
							'created_id'					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctSavingsCashMutationBranch_model->getAcctJournalVoucherItemToken($data_credit['journal_voucher_item_token']);
						
						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctSavingsCashMutationBranch_model->insertAcctJournalVoucherItem($data_credit);
						}
					}

					

					
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Mutasi Simpanan Sukses
							</div> ";
					$sesi = $this->session->userdata('unique');
					$this->session->unset_userdata('addacctsavingscashmutation-'.$sesi['unique']);
					$this->session->unset_userdata('acctsavingscashmutationbranchtoken-'.$sesi['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('savings-cash-mutation-branch/print-note/'.$acctsavingscash_last['savings_cash_mutation_id']);
				}
			}else{
				$this->session->set_userdata('addacctsavingscashmutation',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('savings-cash-mutation-branch');
			}
		}

		public function printNoteAcctSavingsCashMutationBranch(){
			$auth = $this->session->userdata('auth');
			$savings_cash_mutation_id 	= $this->uri->segment(3);
			$acctsavingscashmutation	= $this->AcctSavingsCashMutationBranch_model->getAcctSavingsCashMutationBranch_Detail($savings_cash_mutation_id);
			$preferencecompany 			= $this->AcctSavingsCashMutationBranch_model->getPreferenceCompany();

			if($acctsavingscashmutation['mutation_id'] == $preferencecompany['cash_deposit_id']){
				$keterangan = 'SETORAN TUNAI';
			} else if($acctsavingscashmutation['mutation_id'] == $preferencecompany['cash_withdrawal_id']){
				$keterangan = 'PENARIKAN TUNAI';
			}


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new tcpdf('P', PDF_UNIT, 'F4', true, 'UTF-8', false);


			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(7, 7, 7, 7); 
			
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

			// set some language-dependent strings (optional)
			if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			    require_once(dirname(__FILE__).'/lang/eng.php');
			    $pdf->setLanguageArray($l);
			}

			// ---------------------------------------------------------

			// set font
			$pdf->SetFont('helvetica', 'B', 20);

			// add a page
			$pdf->AddPage();

			/*$pdf->Write(0, 'Example of HTML tables', '', 0, 'L', true, 0, false, false, 0);*/

			$pdf->SetFont('helvetica', '', 12);

			// -----------------------------------------------------------------------------
			$base_url = base_url();
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preferencecompany['logo_koperasi']."\" alt=\"\" width=\"590%\" height=\"250%\"/>";

			$tbl = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
			    <tr>
				<td rowspan=\"2\" width=\"20%\">" .$img."</td>
			        <td width=\"40%\"><div style=\"text-align: left; font-size:14px\">BUKTI SETORAN TUNAI</div></td>
			    </tr>
			    <tr>
			        <td width=\"40%\"><div style=\"text-align: left; font-size:14px\">Jam : ".date('H:i:s')."</div></td>
			    </tr>
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');
			

			$tbl1 = "
			Telah diterima uang dari :
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Nama</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$acctsavingscashmutation['member_name']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">No. Rekening</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$acctsavingscashmutation['savings_account_no']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Alamat</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$acctsavingscashmutation['member_address']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Terbilang</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".numtotxt($acctsavingscashmutation['savings_cash_mutation_amount'])."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Keperluan</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$keterangan."</div></td>
			    </tr>
			     <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Jumlah</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: Rp. &nbsp;".number_format($acctsavingscashmutation['savings_cash_mutation_amount'], 2)."</div></td>
			    </tr>				
			</table>";

			$tbl2 = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			    	<td width=\"30%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"20%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"30%\"><div style=\"text-align: center;\">".$this->AcctSavingsCashMutationBranch_model->getBranchCity($auth['branch_id']).", ".date('d-m-Y')."</div></td>
			    </tr>
			    <tr>
			        <td width=\"30%\"><div style=\"text-align: center;\">Penyetor</div></td>
			        <td width=\"20%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"30%\"><div style=\"text-align: center;\">Teller/Kasir</div></td>
			    </tr>				
			</table>";

			$pdf->writeHTML($tbl1.$tbl2, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Kwitansi.pdf';

			// force print dialog
			$js .= 'print(true);';

			// set javascript
			$pdf->IncludeJS($js);
			
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function validationAcctSavingsCashMutationBranch(){
			$auth = $this->session->userdata('auth');
			$savings_cash_mutation_id = $this->uri->segment(3);

			$data = array (
				'savings_cash_mutation_id'  	=> $savings_cash_mutation_id,
				'validation'					=> 1,
				'validation_id'					=> $auth['user_id'],
				'validation_on'					=> date('Y-m-d H:i:s'),
			);

			if($this->AcctSavingsCashMutationBranch_model->validationAcctSavingsCashMutationBranch($data)){
				$msg = "<div class='alert alert-success alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
							Validasi Setoran Tunai Sukses
						</div>";
				$this->session->set_userdata('message',$msg);
				redirect('savings-cash-mutation-branch/print-validation/'.$savings_cash_mutation_id);
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'> 
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
							Validasi Setoran Tunai Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('savings-cash-mutation-branch');
			}
		}

		public function printValidationAcctSavingsCashMutationBranch(){
			$savings_cash_mutation_id 	= $this->uri->segment(3);
			$acctsavingscashmutation	= $this->AcctSavingsCashMutationBranch_model->getAcctSavingsCashMutationBranch_Detail($savings_cash_mutation_id);
			$preferencecompany			= $this->AcctSavingsCashMutationBranch_model->getPreferenceCompany();


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new tcpdf('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(7, 7, 7, 7);

			// set image scale factor
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

			// set some language-dependent strings (optional)
			if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			    require_once(dirname(__FILE__).'/lang/eng.php');
			    $pdf->setLanguageArray($l);
			}

			// ---------------------------------------------------------

			// set font
			$pdf->SetFont('helvetica', 'B', 20);

			// add a page
			$pdf->AddPage();

			/*$pdf->Write(0, 'Example of HTML tables', '', 0, 'L', true, 0, false, false, 0);*/

			$pdf->SetFont('helveticaI', '', 7);

			// -----------------------------------------------------------------------------
			$base_url = base_url();
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preferencecompany['logo_koperasi']."\" alt=\"\" width=\"700%\" height=\"300%\"/>";

			$tbl = "
			<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
			    <tr>
			    	<td rowspan=\"2\" width=\"10%\">" .$img."</td>
			    </tr>
			    <tr>
			    </tr>
			</table>
			<br/>
			<br/>
			<br/>
			<br/>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
			    <tr>
			        <td width=\"55%\"><div style=\"text-align: right; font-size:14px\">".$acctsavingscashmutation['savings_account_no']."</div></td>
			        <td width=\"45%\"><div style=\"text-align: right; font-size:14px\">".$acctsavingscashmutation['member_name']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"52%\"><div style=\"text-align: right; font-size:14px\">".$acctsavingscashmutation['validation_on']."</div></td>
			        <td width=\"18%\"><div style=\"text-align: right; font-size:14px\">".$this->AcctSavingsCashMutationBranch_model->getUsername($acctsavingscashmutation['validation_id'])."</div></td>
			        <td width=\"30%\"><div style=\"text-align: right; font-size:14px\"> IDR &nbsp; ".number_format($acctsavingscashmutation['savings_cash_mutation_amount'], 2)."</div></td>
			    </tr>
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');
			

			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Validasi.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}
		
		public function voidAcctSavingsCashMutationBranch(){
			$data['main_view']['acctsavingscashmutation']	= $this->AcctSavingsCashMutationBranch_model->getAcctSavingsCashMutationBranch_Detail($this->uri->segment(3));
			$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();
			$data['main_view']['content']					= 'AcctSavingsCashMutationBranch/FormVoidAcctSavingsCashMutationBranch_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processVoidAcctSavingsCashMutationBranch(){
			$auth	= $this->session->userdata('auth');

			$newdata = array (
				"savings_cash_mutation_id"	=> $this->input->post('savings_cash_mutation_id',true),
				"voided_on"					=> date('Y-m-d H:i:s'),
				'data_state'				=> 2,
				"voided_remark" 			=> $this->input->post('voided_remark',true),
				"voided_id"					=> $auth['user_id']
			);
			
			$this->form_validation->set_rules('voided_remark', 'Keterangan', 'required');

			if($this->form_validation->run()==true){
				if($this->AcctSavingsCashMutationBranch_model->voidAcctSavingsCashMutationBranch($newdata)){
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Pembatalan Mutasi Simpanan Sukses
							</div>";
					$this->session->set_userdata('message',$msg);
					redirect('savings-cash-mutation-branch');
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Pembatalan Mutasi Simpanan Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('savings-cash-mutation-branch');
				}
					
			}else{
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('savings-cash-mutation-branch');
			}
		}


		public function function_state_add(){
			$unique 	= $this->session->userdata('unique');
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctsavingscashmutation-'.$unique['unique']);
			$sessions['active_tab'] = $value;
			$this->session->set_userdata('addacctsavingscashmutation-'.$unique['unique'],$sessions);
		}
	}
?>