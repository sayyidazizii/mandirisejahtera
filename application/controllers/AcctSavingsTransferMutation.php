<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctSavingsTransferMutation extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
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
			$sesi	= 	$this->session->userdata('filter-acctsavingstransfermutation');
			$unique = $this->session->userdata('unique');

			if(!is_array($sesi)){
				$sesi['start_date']					= date('Y-m-d');
				$sesi['end_date']					= date('Y-m-d');
				$sesi['savings_account_from_id']	= '';
				$sesi['savings_account_to_id']		= '';
			}

			$this->session->unset_userdata('savings_account_from_id');
			$this->session->unset_userdata('acctsavingstransfermutationtoken-'.$unique['unique']);

			$data['main_view']['acctsavingsaccount']		= create_double($this->AcctSavingsTransferMutation_model->getAcctSavingsAccount(),'savings_account_id', 'savings_account_no');
			$data['main_view']['acctsavingstransfermutation']		= $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutation($sesi['start_date'], $sesi['end_date'], $sesi['savings_account_from_id'], $sesi['savings_account_to_id']);
			$data['main_view']['content']			= 'AcctSavingsTransferMutation/ListAcctSavingsTransferMutation_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date" 				=> tgltodb($this->input->post('start_date',true)),
				"end_date" 					=> tgltodb($this->input->post('end_date',true)),
				"savings_account_from_id"	=> $this->input->post('savings_account_from_id',true),
				"savings_account_to_id"		=> $this->input->post('savings_account_to_id',true),
			);

			$this->session->set_userdata('filter-acctsavingstransfermutation',$data);
			redirect('savings-transfer-mutation');
		}

		public function reset_data(){
			$unique 	= $this->session->userdata('unique');
			$sessions	= $this->session->unset_userdata('addacctsavingstransfermutation-'.$unique['unique']);
			$this->session->unset_userdata('savings_account_from_id');
			redirect('savings-transfer-mutation/add');
		}
		public function reset_search(){
			$this->session->unset_userdata('filter-acctsavingstransfermutation');
			redirect('savings-transfer-mutation');
		}

		public function addAcctSavingsAccountFrom(){
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
	            $row[] = '<a href="'.base_url().'savings-transfer-mutation/add/'.$savingsaccount->savings_account_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
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

		public function addAcctSavingsAccountTo(){
			$savings_account_from_id = $this->uri->segment(3);
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
	            $row[] = '<a href="'.base_url().'savings-transfer-mutation/add/'.$savings_account_from_id.'/'.$savingsaccount->savings_account_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
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
		
		public function addAcctSavingsTransferMutation(){
			$savings_account_from_id 	= $this->uri->segment(3);
			$savings_account_to_id 		= $this->uri->segment(4);

			$unique = $this->session->userdata('unique');
			$token 	= $this->session->userdata('acctsavingstransfermutationtoken-'.$unique['unique']);

			if(empty($token)){
				$token = md5(date('Y-m-d H:i:s'));
				$this->session->set_userdata('acctsavingstransfermutationtoken-'.$unique['unique'], $token);
			}

			
			$data['main_view']['acctsavingsaccountfrom']	= $this->AcctSavingsTransferMutation_model->getAcctSavingsAccount_Detail($savings_account_from_id);	
			$data['main_view']['acctsavingsaccountto']		= $this->AcctSavingsTransferMutation_model->getAcctSavingsAccount_Detail($savings_account_to_id);	
			$data['main_view']['acctmutation']				= $this->AcctSavingsTransferMutation_model->getAcctMutation();	
			$data['main_view']['content']					= 'AcctSavingsTransferMutation/FormAddAcctSavingsTransferMutation_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processAddAcctSavingsTransferMutation(){
			$auth = $this->session->userdata('auth');

			$username = $this->AcctSavingsTransferMutation_model->getUsername($auth['user_id']);

			$data = array(
				'branch_id'								=> $auth['branch_id'],
				'savings_transfer_mutation_date'		=> date('Y-m-d'),
				'savings_transfer_mutation_amount'		=> $this->input->post('savings_transfer_mutation_amount', true),
				'savings_transfer_mutation_token'		=> $this->input->post('savings_transfer_mutation_token', true),
				'operated_name'							=> $username,
				'created_id'							=> $auth['user_id'],
				'created_on'							=> date('Y-m-d H:i:s'),
			);

			$this->form_validation->set_rules('savings_transfer_mutation_amount', 'Jumlah', 'required');

			$savings_transfer_mutation_token 	= $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationToken($data['savings_transfer_mutation_token']);
			
			if($this->form_validation->run()==true){
				if($savings_transfer_mutation_token->num_rows()==0){
					if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutation($data)){
						$transaction_module_code = "TRTAB";

						$transaction_module_id 	= $this->AcctSavingsTransferMutation_model->getTransactionModuleID($transaction_module_code);
						$acctsavingstr_last 	= $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutation_Last($data['created_on']);

							
						$journal_voucher_period = date("Ym", strtotime($data['savings_transfer_mutation_date']));
						
						$data_journal = array(
							'branch_id'						=> $auth['branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'TRANSFER ANTAR REKENING '.$acctsavingstr_last['member_name'],
							'journal_voucher_description'	=> 'TRANSFER ANTAR REKENING '.$acctsavingstr_last['member_name'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $acctsavingstr_last['savings_transfer_mutation_id'],
							'transaction_journal_no' 		=> $acctsavingstr_last['savings_account_no'],
							'journal_voucher_token' 		=> $data['savings_transfer_mutation_token'],
							'created_id' 					=> $data['created_id'],
							'created_on' 					=> $data['created_on'],
						);
						
						$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucher($data_journal);

						$journal_voucher_id = $this->AcctSavingsTransferMutation_model->getJournalVoucherID($data['created_id']);

						$savings_transfer_mutation_id = $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data['created_on']);

						$preferencecompany = $this->AcctSavingsTransferMutation_model->getPreferenceCompany();

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
							'savings_transfer_mutation_from_token'		=> $data['savings_transfer_mutation_token'].$savings_transfer_mutation_id,
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
								'journal_voucher_item_token'	=> $data['savings_transfer_mutation_token'].$account_id,
								'created_id'					=> $auth['user_id'],
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
							'savings_transfer_mutation_to_token'		=> $data['savings_transfer_mutation_token'].$savings_transfer_mutation_id,
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
								'journal_voucher_item_token'	=> $data['savings_transfer_mutation_token'].$account_id,
								'created_id'					=> $auth['user_id'],
							);

							$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucherItem($data_credit);
						}


						$auth = $this->session->userdata('auth');
						// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Transfer Antar Rekening Sukses
								</div> ";
						$sesi = $this->session->userdata('unique');
						$this->session->unset_userdata('acctsavingstransfermutationtoken-'.$sesi['unique']);
						$this->session->unset_userdata('addacctsavingstransfermutation-'.$sesi['unique']);
						$this->session->unset_userdata('savings_account_from_id');
						$this->session->set_userdata('message',$msg);
						redirect('savings-transfer-mutation');
					}else{
						$this->session->set_userdata('addacctsavingstransfermutation',$data);
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah DataTransfer Antar Rekening Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('savings-transfer-mutation');
					}
				}else{
					$transaction_module_code = "TRTAB";

					$transaction_module_id 	= $this->AcctSavingsTransferMutation_model->getTransactionModuleID($transaction_module_code);
					$acctsavingstr_last 	= $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutation_Last($data['created_on']);

						
					$journal_voucher_period = date("Ym", strtotime($data['savings_transfer_mutation_date']));
					
					$data_journal = array(
						'branch_id'						=> $auth['branch_id'],
						'journal_voucher_period' 		=> $journal_voucher_period,
						'journal_voucher_date'			=> date('Y-m-d'),
						'journal_voucher_title'			=> 'TRANSFER ANTAR REKENING '.$acctsavingstr_last['member_name'],
						'journal_voucher_description'	=> 'TRANSFER ANTAR REKENING '.$acctsavingstr_last['member_name'],
						'transaction_module_id'			=> $transaction_module_id,
						'transaction_module_code'		=> $transaction_module_code,
						'transaction_journal_id' 		=> $acctsavingstr_last['savings_transfer_mutation_id'],
						'transaction_journal_no' 		=> $acctsavingstr_last['savings_account_no'],
						'journal_voucher_token' 		=> $data['savings_transfer_mutation_token'],
						'created_id' 					=> $data['created_id'],
						'created_on' 					=> $data['created_on'],
					);

					$journal_voucher_token 	= $this->AcctSavingsTransferMutation_model->getJournalVoucherToken($data_journal['journal_voucher_token']);

					if($journal_voucher_token->num_rows()== 0){					
						$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucher($data_journal);
					}

					$journal_voucher_id = $this->AcctSavingsTransferMutation_model->getJournalVoucherID($data['created_id']);

					$savings_transfer_mutation_id = $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data['created_on']);

					$preferencecompany = $this->AcctSavingsTransferMutation_model->getPreferenceCompany();

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
						'savings_transfer_mutation_from_token'		=> $data['savings_transfer_mutation_token'].$savings_transfer_mutation_id,
					);

					$savings_transfer_mutation_from_token 	= $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationFromToken($datafrom['savings_transfer_mutation_from_token']);

					if($savings_transfer_mutation_from_token->num_rows()== 0){
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
								'journal_voucher_item_token'	=> $data['savings_transfer_mutation_token'].$account_id,
								'created_id'					=> $auth['user_id'],
							);

							$journal_voucher_item_token 		= $this->AcctSavingsTransferMutation_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

							if($journal_voucher_item_token->num_rows()==0){
								$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucherItem($data_debit);
							}
						}
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
						'savings_transfer_mutation_to_token'		=> $data['savings_transfer_mutation_token'].$savings_transfer_mutation_id,
					);

					$savings_transfer_mutation_to_token 	= $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationToToken($datato['savings_transfer_mutation_to_token']);

					if($savings_transfer_mutation_to_token->num_rows()== 0){
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
								'journal_voucher_item_token'	=> $data['savings_transfer_mutation_token'].$account_id,
								'created_id'					=> $auth['user_id'],
							);

							$journal_voucher_item_token 		= $this->AcctSavingsTransferMutation_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

							if($journal_voucher_item_token->num_rows()==0){
							$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucherItem($data_credit);
							}
						}
					}


					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Transfer Antar Rekening Sukses
							</div> ";
					$sesi = $this->session->userdata('unique');
					$this->session->unset_userdata('acctsavingstransfermutationtoken-'.$sesi['unique']);
					$this->session->unset_userdata('addacctsavingstransfermutation-'.$sesi['unique']);
					$this->session->unset_userdata('savings_account_from_id');
					$this->session->set_userdata('message',$msg);
					redirect('savings-transfer-mutation');
				}
			}else{
				$this->session->set_userdata('addacctsavingstransfermutation',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('savings-transfer-mutation');
			}
		}

		public function validationAcctSavingsTransferMutation(){
			$auth = $this->session->userdata('auth');
			$savings_transfer_mutation_id = $this->uri->segment(3);

			$data = array (
				'savings_transfer_mutation_id'  => $savings_transfer_mutation_id,
				'validation'					=> 1,
				'validation_id'					=> $auth['user_id'],
				'validation_on'					=> date('Y-m-d H:i:s'),
			);

			if($this->AcctSavingsTransferMutation_model->validationAcctSavingsTransferMutation($data)){
				$msg = "<div class='alert alert-success alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
							Validasi Transfer Antar Rekening Sukses
						</div>";
				$this->session->set_userdata('message',$msg);
				redirect('savings-transfer-mutation/print-validation/'.$savings_transfer_mutation_id);
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'> 
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
							Validasi Transfer Antar Rekening Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('savings-transfer-mutation');
			}
		}

		public function printValidationAcctSavingsTransferMutation(){
			$savings_transfer_mutation_id 	= $this->uri->segment(3);

			$acctsavingstransfermutation	= $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutation_Detail($savings_transfer_mutation_id);

			$acctsavingstransfermutationfrom = $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutationFrom($savings_transfer_mutation_id);

			$preferencecompany				= $this->AcctSavingsTransferMutation_model->getPreferenceCompany();


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new tcpdf('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(7, 7, 7, 7); // put space of 10 on top

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
			        <td width=\"55%\"><div style=\"text-align: right; font-size:14px\">".$this->AcctSavingsTransferMutation_model->getSavingsAccountNo($acctsavingstransfermutationfrom['savings_account_id'])."</div></td>
			        <td width=\"45%\"><div style=\"text-align: right; font-size:14px\">".$this->AcctSavingsTransferMutation_model->getMemberName($acctsavingstransfermutationfrom['member_id'])."</div></td>
			    </tr>
			    <tr>
			        <td width=\"52%\"><div style=\"text-align: right; font-size:14px\">".$acctsavingstransfermutation['validation_on']."</div></td>
			        <td width=\"18%\"><div style=\"text-align: right; font-size:14px\">".$this->AcctSavingsTransferMutation_model->getUsername($acctsavingstransfermutation['validation_id'])."</div></td>
			        <td width=\"30%\"><div style=\"text-align: right; font-size:14px\"> IDR &nbsp; ".number_format($acctsavingstransfermutation['savings_transfer_mutation_amount'], 2)."</div></td>
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
		
		public function voidAcctSavingsTransferMutation(){
			$data['main_view']['acctsavingstransfermutation']	= $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutation_Detail($this->uri->segment(3));
			$data['main_view']['content']					= 'AcctSavingsTransferMutation/FormVoidAcctSavingsTransferMutation_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processVoidAcctSavingsTransferMutation(){
			$auth	= $this->session->userdata('auth');

			$newdata = array (
				"savings_transfer_mutation_id"	=> $this->input->post('savings_transfer_mutation_id',true),
				"voided_on"					=> date('Y-m-d H:i:s'),
				'data_state'				=> 2,
				"voided_remark" 			=> $this->input->post('voided_remark',true),
				"voided_id"					=> $auth['user_id']
			);
			
			$this->form_validation->set_rules('voided_remark', 'Keterangan', 'required');

			if($this->form_validation->run()==true){
				if($this->AcctSavingsTransferMutation_model->voidAcctSavingsTransferMutation($newdata)){
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Pembatalan Mutasi Setoran Simpanan Non Tunai Sukses
							</div>";
					$this->session->set_userdata('message',$msg);
					redirect('savings-transfer-mutation');
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Pembatalan Mutasi Setoran Simpanan Non Tunai Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('savings-transfer-mutation');
				}
					
			}else{
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('savings-transfer-mutation');
			}
		}

		public function function_state_add(){
			$unique 	= $this->session->userdata('unique');
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctsavingstransfermutation-'.$unique['unique']);
			$sessions['active_tab'] = $value;
			$this->session->set_userdata('addacctsavingstransfermutation-'.$unique['unique'],$sessions);
		}
		
		public function function_elements_add(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctsavingstransfermutation-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('addacctsavingstransfermutation-'.$unique['unique'],$sessions);
		}

		// public function reset_data(){
		// 	$unique 	= $this->session->userdata('unique');
		// 	$sessions	= $this->session->unset_userdata('addacctsavingstransfermutation-'.$unique['unique']);
		// 	redirect('savings-transfer-mutation/add');
		// }
		
		
	}
?>