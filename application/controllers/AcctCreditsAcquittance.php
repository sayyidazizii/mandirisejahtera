<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctCreditsAcquittance extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('MainPage_model');
			$this->load->model('AcctCreditsAcquittance_model');
			$this->load->model('AcctCreditAccount_model');
			$this->load->model('AcctSavingsAccount_model');
			$this->load->model('AcctSavingsCashMutation_model');
			$this->load->model('CoreMember_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi'); 
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}

		public function index(){
			$auth 	= $this->session->userdata('auth');
			$unique = $this->session->userdata('unique');
			$this->session->unset_userdata('acctcreditsacquittancetoken-'.$unique['unique']);

			$data['main_view']['acctcredits']	= create_double($this->AcctCreditAccount_model->getAcctCredits(),'credits_id', 'credits_name');
			$data['main_view']['corebranch']	= create_double($this->AcctCreditAccount_model->getCoreBranch(),'branch_id', 'branch_name');
			$data['main_view']['content']		= 'AcctCreditsAcquittance/ListAcctCreditsAcquittance_view';
			$this->load->view('MainPage_view', $data);
		}

		public function filter(){
			$data = array (
				'start_date'	=> tgltodb($this->input->post('start_date', true)),
				'end_date'		=> tgltodb($this->input->post('end_date', true)),
				'credits_id'	=> $this->input->post('credits_id', true),
			);

			$this->session->set_userdata('filter-acctcreditsacquittance', $data);
			redirect('credits-acquittance');
		}
		public function reset(){
			$this->session->unset_userdata('filter-acctcreditsacquittance');
			redirect('credits-acquittance');
		}

		public function getAcctCreditsAcquittance(){ 
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-acctcreditsacquittance');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['credits_id']		= '';
			}

			$list = $this->AcctCreditsAcquittance_model->get_datatables($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id']);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $cashacquittance) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $cashacquittance->credits_account_serial;
	            $row[] = $cashacquittance->member_name;
	            $row[] = $cashacquittance->credits_name;
	            $row[] = tgltoview($cashacquittance->credits_acquittance_date);
	            $row[] = number_format($cashacquittance->credits_acquittance_principal, 2);
	            $row[] = number_format($cashacquittance->credits_acquittance_interest, 2);
	            $row[] = number_format($cashacquittance->credits_acquittance_fine, 2);
	            $row[] = number_format($cashacquittance->credits_acquittance_penalty, 2);
	            $row[] = number_format($cashacquittance->credits_acquittance_amount, 2);
			    $row[] = '<a href="'.base_url().'credits-acquittance/print-note/'.$cashacquittance->credits_acquittance_id.'" class="btn btn-xs blue" role="button"><i class="fa fa-print"></i> Kwitansi</a>';
	            $data[] = $row;
	        }

	        $output = array(
				"draw" 				=> $_POST['draw'],
				"recordsTotal" 		=> $this->AcctCreditsAcquittance_model->count_all($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id']),
				"recordsFiltered" 	=> $this->AcctCreditsAcquittance_model->count_filtered($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id']),
				"data" 				=> $data,
			);
	        echo json_encode($output);
		}

		public function addAcctCreditsAcquittance(){
			$credits_account_id 	= $this->uri->segment(3);

			$unique = $this->session->userdata('unique');
			$token 	= $this->session->userdata('acctcreditsacquittancetoken-'.$unique['unique']);

			if(empty($token)){
				$token = md5(date('Y-m-d H:i:s'));
				$this->session->set_userdata('acctcreditsacquittancetoken-'.$unique['unique'], $token);
			}

			$data['main_view']['penaltytype']			= $this->configuration->PenaltyType();
			$data['main_view']['accountcredit']			= $this->AcctCreditAccount_model->getDetailByID($credits_account_id);
			$data['main_view']['detailpayment']			= $this->AcctCreditsAcquittance_model->getDataByIDCredit($credits_account_id);
			$data['main_view']['content']				= 'AcctCreditsAcquittance/FormAddAcctCreditsAcquittance_view';
			$this->load->view('MainPage_view',$data);
		}

		public function akadlisttunai(){
			$auth 	= $this->session->userdata('auth');
			$list 	= $this->AcctCreditAccount_model->get_datatables($auth['branch_id']);
	        $data 	= array();
	        $no 	= $_POST['start'];
	        foreach ($list as $customers) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $customers->credits_account_serial;
	            $row[] = $customers->member_name;
	            $row[] = $customers->member_no;
	            $row[] = $customers->credits_account_date;
	            $row[] = $customers->credits_account_due_date;
	             $row[] = '<a href="'.base_url().'credits-acquittance/add/'.$customers->credits_account_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	    
	            $data[] = $row;
	        }
	 
	        $output = array(
				"draw" 				=> $_POST['draw'],
				"recordsTotal" 		=> 1,
				"recordsFiltered" 	=> 5,
				"data" 				=> $data,
			);
	        echo json_encode($output);
		}

		public function processAddAcctCreditsAcquittance(){
			$auth	= $this->session->userdata('auth');

			$data = array(
				'branch_id'									=> $auth['branch_id'],
				'member_id'									=> $this->input->post('member_id', true),
				'credits_id'								=> $this->input->post('credits_id', true),
				'credits_account_id'						=> $this->input->post('credits_account_id', true),
				'credits_acquittance_date'					=> date('Y-m-d'),
				'credits_acquittance_penalty_type'			=> $this->input->post('penalty_type_id', true),
				'credits_account_last_balance'				=> $this->input->post('credits_account_last_balance', true),
				'credits_account_interest_last_balance'		=> $this->input->post('credits_account_interest_last_balance', true),
				'credits_account_accumulated_fines'			=> $this->input->post('credits_account_accumulated_fines', true),
				'credits_acquittance_amount'				=> $this->input->post('credits_acquittance_amount', true),
				'credits_acquittance_principal'				=> $this->input->post('credits_acquittance_principal', true),
				'credits_acquittance_interest'				=> $this->input->post('credits_acquittance_interest', true),
				'credits_acquittance_fine'					=> $this->input->post('credits_acquittance_fine', true),
				'credits_acquittance_penalty'				=> $this->input->post('penalty', true),
				'credits_acquittance_penalty_amount'		=> $this->input->post('credits_acquittance_penalty', true),
				'credits_acquittance_token'					=> $this->input->post('credits_acquittance_token', true),
				'created_id'								=> $auth['user_id'],
				'created_on'								=> date('Y-m-d H:i:s'),
			);

			$kerugian_pelunasan_peminjaman = $data['credits_account_last_balance'] - $data['credits_acquittance_principal'];

			$this->form_validation->set_rules('credits_account_id', 'No. Perjanjian Kredit', 'required');
			$this->form_validation->set_rules('credits_acquittance_principal', 'Pelunasan Sisa Pokok', 'required');

			$transaction_module_code 	= 'PP';
			$transaction_module_id 		= $this->AcctCreditAccount_model->getTransactionModuleID($transaction_module_code);
			$preferencecompany 			= $this->AcctCreditAccount_model->getPreferenceCompany();

			$journal_voucher_period 	= date("Ym", strtotime($data['credits_acquittance_date']));
			
			$credits_acquittance_token 	= $this->AcctCreditsAcquittance_model->getCreditsPaymentToken($data['credits_acquittance_token']);


			if($this->form_validation->run()==true){
				if($credits_acquittance_token->num_rows() == 0){
					if($this->AcctCreditsAcquittance_model->insert($data)){
						$updatedata = array(
							"credits_account_last_balance"			=> $data['credits_account_last_balance'] - $data['credits_acquittance_principal'],
							"credits_account_accumulated_fines"		=> $data['credits_account_accumulated_fines'] - $data['credits_acquittance_fine'], 
							"credits_account_status" 				=> 2,
						);

						$this->AcctCreditAccount_model->updatedata($updatedata,$data['credits_account_id']);

						$acctcashacquittance_last 							= $this->AcctCreditsAcquittance_model->AcctCreditsAcquittanceLast($data['created_id']);
						
						$data_journal = array(
							'branch_id'						=> $auth['branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'PELUNASAN PEMINJAMAN '.$acctcashacquittance_last['credits_name'].' '.$acctcashacquittance_last['member_name'],
							'journal_voucher_description'	=> 'PELUNASAN PEMINJAMAN '.$acctcashacquittance_last['credits_name'].' '.$acctcashacquittance_last['member_name'],
							'journal_voucher_token'			=> $data['credits_acquittance_token'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $acctcashacquittance_last['credits_acquittance_id'],
							'transaction_journal_no' 		=> $acctcashacquittance_last['credits_account_serial'],
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
							'journal_voucher_amount'		=> $data['credits_acquittance_amount'],
							'journal_voucher_debit_amount'	=> $data['credits_acquittance_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token'	=> $data['credits_acquittance_token'].$preferencecompany['account_cash_id'],
							'created_id' 					=> $auth['user_id']
						);

						$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);

						// if(!empty($kerugian_pelunasan_peminjaman) || $kerugian_pelunasan_peminjaman > 0){
						// 	$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_acquittance_loss_id']);

						// 	$data_debet = array (
						// 		'journal_voucher_id'			=> $journal_voucher_id,
						// 		'account_id'					=> $preferencecompany['account_acquittance_loss_id'],
						// 		'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
						// 		'journal_voucher_amount'		=> $kerugian_pelunasan_peminjaman,
						// 		'journal_voucher_debit_amount'	=> $kerugian_pelunasan_peminjaman,
						// 		'account_id_default_status'		=> $account_id_default_status,
						// 		'account_id_status'				=> 0,
						// 		'journal_voucher_item_token'	=> $data['credits_acquittance_token'].$preferencecompany['account_acquittance_loss_id'],
						//		'created_id' 					=> $auth['user_id']
						// 	);

						// 	$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);
						// }

						$receivable_account_id 				= $this->AcctCreditAccount_model->getReceivableAccountID($data['credits_id']);

						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($receivable_account_id);

						$data_credit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $receivable_account_id,
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_acquittance_principal'],
							'journal_voucher_credit_amount'	=> $data['credits_acquittance_principal'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $data['credits_acquittance_token'].$receivable_account_id,
							'created_id' 					=> $auth['user_id']
						);

						$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);

						if($data['credits_acquittance_interest'] > 0){

							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_interest_id']);

							$data_credit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_interest_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_acquittance_interest'],
								'journal_voucher_credit_amount'	=> $data['credits_acquittance_interest'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $data['credits_acquittance_token'].$preferencecompany['account_interest_id'],
								'created_id' 					=> $auth['user_id'],
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
						}

						if($data['credits_acquittance_fine'] > 0){
							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_credits_payment_fine']);

							$data_credit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_credits_payment_fine'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_acquittance_fine'],
								'journal_voucher_credit_amount'	=> $data['credits_acquittance_fine'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $data['credits_acquittance_token'].$preferencecompany['account_credits_payment_fine'],
								'created_id' 					=> $auth['user_id'],
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
						}

						if(!empty($data['credits_acquittance_penalty_amount']) || $data['credits_acquittance_penalty_amount'] > 0 || $data['credits_acquittance_penalty_amount'] != ''){

							$account_id_default_status = $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_penalty_id']);

							$data_credit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_penalty_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_acquittance_penalty_amount'],
								'journal_voucher_credit_amount'	=> $data['credits_acquittance_penalty_amount'],
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $data['credits_acquittance_token'].$preferencecompany['account_penalty_id'],
								'created_id' 					=> $auth['user_id'],
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
						}
						
						$auth = $this->session->userdata('auth');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Pembayaran Pinjaman Sukses
								</div> ";
						$sesi = $this->session->userdata('unique');
						$this->session->unset_userdata('addacctcreditsacquittance-'.$sesi['unique']);
						$this->session->unset_userdata('acctcreditsacquittancetoken-'.$sesi['unique']);
						$this->session->set_userdata('message',$msg);
						redirect('credits-acquittance/print-note/'.$acctcashacquittance_last['credits_acquittance_id']);
					}else{
						$this->session->set_userdata('addacctcashacquittance-'.$unique['unique'],$data);
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Pembayaran Pinjaman Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('credits-acquittance/add');
					}
				} else {
					$acctcashacquittance_last			= $this->AcctCreditsAcquittance_model->AcctCreditsAcquittanceLast($data['created_id']);

					$data_journal = array(
						'branch_id'						=> $auth['branch_id'],
						'journal_voucher_period' 		=> $journal_voucher_period,
						'journal_voucher_date'			=> date('Y-m-d'),
						'journal_voucher_title'			=> 'PELUNASAN PEMINJAMAN '.$acctcashacquittance_last['credits_name'].' '.$acctcashacquittance_last['member_name'],
						'journal_voucher_description'	=> 'PELUNASAN PEMINJAMAN '.$acctcashacquittance_last['credits_name'].' '.$acctcashacquittance_last['member_name'],
						'journal_voucher_token'			=> $data['credits_acquittance_token'],
						'transaction_module_id'			=> $transaction_module_id,
						'transaction_module_code'		=> $transaction_module_code,
						'transaction_journal_id' 		=> $acctcashacquittance_last['credits_acquittance_id'],
						'transaction_journal_no' 		=> $acctcashacquittance_last['credits_account_serial'],
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
						'journal_voucher_amount'		=> $data['credits_acquittance_amount'],
						'journal_voucher_debit_amount'	=> $data['credits_acquittance_amount'],
						'account_id_default_status'		=> $account_id_default_status,
						'account_id_status'				=> 0,
						'journal_voucher_item_token'	=> $data['credits_acquittance_token'].$preferencecompany['account_cash_id'],
						'created_id' 					=> $auth['user_id'],
					);

					$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

					if($journal_voucher_item_token->num_rows()==0){
						$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);
					}

					// if(!empty($kerugian_pelunasan_peminjaman) || $kerugian_pelunasan_peminjaman > 0){
					// 		$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_acquittance_loss_id']);

					// 		$data_debet = array (
					// 			'journal_voucher_id'			=> $journal_voucher_id,
					// 			'account_id'					=> $preferencecompany['account_acquittance_loss_id'],
					// 			'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
					// 			'journal_voucher_amount'		=> $kerugian_pelunasan_peminjaman,
					// 			'journal_voucher_debit_amount'	=> $kerugian_pelunasan_peminjaman,
					// 			'account_id_default_status'		=> $account_id_default_status,
					// 			'account_id_status'				=> 0,
					// 			'journal_voucher_item_token'	=> $data['credits_acquittance_token'].$preferencecompany['account_acquittance_loss_id'],
					//			'created_id' 					=> $auth['user_id'],
					// 		);

					// 		$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

					// 		if($journal_voucher_item_token->num_rows()==0){
					// 			$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);
					// 		}
					// 	}

					$receivable_account_id 				= $this->AcctCreditAccount_model->getReceivableAccountID($data['credits_id']);

					$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($receivable_account_id);

					$data_credit = array (
						'journal_voucher_id'			=> $journal_voucher_id,
						'account_id'					=> $receivable_account_id,
						'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
						'journal_voucher_amount'		=> $data['credits_acquittance_principal'],
						'journal_voucher_credit_amount'	=> $data['credits_acquittance_principal'],
						'account_id_default_status'		=> $account_id_default_status,
						'account_id_status'				=> 1,
						'journal_voucher_item_token'	=> $data['credits_acquittance_token'].$receivable_account_id,
						'created_id' 					=> $auth['user_id'],
					);

					$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

					if($journal_voucher_item_token->num_rows()==0){
						$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
					}

					if($data['credits_acquittance_interest'] > 0){
						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_interest_id']);

						$data_credit =array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_interest_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_acquittance_interest'],
							'journal_voucher_credit_amount'	=> $data['credits_acquittance_interest'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $data['credits_acquittance_token'].$preferencecompany['account_interest_id'],
							'created_id' 					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
						}
					}

					if($data['credits_acquittance_fine'] > 0){
						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_credits_payment_fine']);

						$data_credit =array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_credits_payment_fine'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_acquittance_fine'],
							'journal_voucher_credit_amount'	=> $data['credits_acquittance_fine'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $data['credits_acquittance_token'].$preferencecompany['account_credits_payment_fine'],
							'created_id' 					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
						}
					}

					if(!empty($data['credits_acquittance_penalty_amount']) || $data['credits_acquittance_penalty_amount'] > 0 || $data['credits_acquittance_penalty_amount'] != ''){

						$account_id_default_status = $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_penalty_id']);

						$data_credit =array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_penalty_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_acquittance_penalty_amount'],
							'journal_voucher_credit_amount'	=> $data['credits_acquittance_penalty_amount'],
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $data['credits_acquittance_token'].$preferencecompany['account_penalty_id'],
							'created_id' 					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
						}
					}

					$auth = $this->session->userdata('auth');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Pembayaran Pinjaman Sukses
							</div> ";
					$sesi = $this->session->userdata('unique');
					$this->session->unset_userdata('addacctcreditsacquittance-'.$sesi['unique']);
					$this->session->unset_userdata('acctcreditsacquittancetoken-'.$sesi['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('credits-acquittance/print-note/'.$acctcashacquittance_last['credits_acquittance_id']);
				}
			}else{
				$this->session->set_userdata('addacctcashacquittance-'.$unique['unique'],$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('credits-acquittance/add');
			}
		}

		public function printNote(){
			$auth = $this->session->userdata('auth');
			$credits_acquittance_id 	= $this->uri->segment(3);
			$preferencecompany 			= $this->AcctCreditAccount_model->getPreferenceCompany();
			$acctcreditsacquittance	 	= $this->AcctCreditsAcquittance_model->getAcctCreditsacquittance_Detail($credits_acquittance_id);

			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');

			$pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);
			$pdf->SetMargins(7, 7, 7, 7);
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

			if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			    require_once(dirname(__FILE__).'/lang/eng.php');
			    $pdf->setLanguageArray($l);
			}

			// ---------------------------------------------------------

			$pdf->SetFont('helvetica', 'B', 20);
			$pdf->AddPage();
			$pdf->SetFont('helvetica', '', 10);

			// -----------------------------------------------------------------------------
			$base_url = base_url();
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preferencecompany['logo_koperasi']."\" alt=\"\" width=\"700%\" height=\"300%\"/>";

			$tbl = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
			    <tr>
			    	<td rowspan=\"2\" width=\"20%\">".$img."</td>
			        <td width=\"50%\"><div style=\"text-align: left; font-size:14px\">BUKTI PELUNASAN PEMINJAMAN</div></td>
			    </tr>
			    <tr>
			        <td width=\"40%\"><div style=\"text-align: left; font-size:14px\">Jam : ".date('H:i:s')."</div></td>
			    </tr>
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');
			

			$tbl1 = "
			Telah diterima dari :
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Nama</div></td>
			        <td width=\"50%\"><div style=\"text-align: left;\">: ".$acctcreditsacquittance['member_name']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">No. Akad</div></td>
			        <td width=\"50%\"><div style=\"text-align: left;\">: ".$acctcreditsacquittance['credits_account_serial']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Alamat</div></td>
			        <td width=\"50%\"><div style=\"text-align: left;\">: ".$acctcreditsacquittance['member_address']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Terbilang</div></td>
			        <td width=\"50%\"><div style=\"text-align: left;\">: ".numtotxt($acctcreditsacquittance['credits_acquittance_amount'])."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Keperluan</div></td>
			        <td width=\"50%\"><div style=\"text-align: left;\">: PELUNASAN PEMINJAMAN</div></td>
			    </tr>
			     <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Jumlah</div></td>
			        <td width=\"50%\"><div style=\"text-align: left;\">: Rp. &nbsp;".number_format($acctcreditsacquittance['credits_acquittance_amount'], 2)."</div></td>
			    </tr>				
			</table>";

			$tbl2 = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			    	<td width=\"30%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"10%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"30%\"><div style=\"text-align: center;\">".$this->AcctCreditAccount_model->getBranchCity($auth['branch_id']).", ".date('d-m-Y')."</div></td>
			    </tr>
			    <tr>
			        <td width=\"30%\"><div style=\"text-align: center;\">Penyetor</div></td>
			        <td width=\"10%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"30%\"><div style=\"text-align: center;\">Teller/Kasir</div></td>
			    </tr>				
			</table>";

			$pdf->writeHTML($tbl1.$tbl2, true, false, false, false, '');

			ob_clean();

			// -----------------------------------------------------------------------------
			
			$filename = 'Kwitansi.pdf';
			$pdf->Output($filename, 'I');
		}
	}
?>