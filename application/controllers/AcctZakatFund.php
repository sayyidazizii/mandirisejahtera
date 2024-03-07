<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctZakatFund extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctZakatFund_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			
		}

		public function getAcctZakatFundReceived(){
			$data['main_view']['corebranch']		= create_double($this->AcctZakatFund_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']			= 'AcctZakatFund/ListAcctZakatFundReceived_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filterreceived(){
			$data = array (
				"branch_id" 	=> $this->input->post('branch_id',true),
			);

			$this->session->set_userdata('filter-acctzakatfundreceived',$data);
			redirect('AcctZakatFund/getAcctZakatFundReceived');
		}

		public function reset_data(){
			$this->session->unset_userdata('filter-acctzakatfundreceived');
			redirect('AcctZakatFund/getAcctZakatFundReceived');

		}

		public function getAcctZakatFundReceivedList(){
			$auth = $this->session->userdata('auth');

			if($auth['branch_status'] == 1){
				$sesi	= 	$this->session->userdata('filter-acctzakatfundreceived');
				if(!is_array($sesi)){
					$sesi['branch_id']		= '';
				}
			} else {
				$sesi['branch_id']	= $auth['branch_id'];
			}

			$list = $this->AcctZakatFund_model->get_datatables_zakat_received($sesi['branch_id']);

			// print_r($list);exit;
			$sourcefund		= $this->configuration->SourceFundZakat();	
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $zakat) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = tgltoview($zakat->zakat_fund_received_date);
	            $row[] = $sourcefund[$zakat->zakat_fund_source_fund];
	            $row[] = number_format($zakat->zakat_fund_received_amount, 2);
	            $row[] = $zakat->zakat_fund_description;
	            $data[] = $row;
	        }

	        // print_r($data);exit;
	 	
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctZakatFund_model->count_all_zakat_received($sesi['branch_id']),
	                        "recordsFiltered" => $this->AcctZakatFund_model->count_filtered_zakat_received($sesi['branch_id']),
	                        "data" => $data,
	                );

	        //output to json format
	        echo json_encode($output);
		}

		public function addAcctZakatFundReceived(){
			$auth 	= $this->session->userdata('auth');


			$data['main_view']['zakat_fund_opening_balance'] 	= $this->AcctZakatFund_model->getZakatFundLastBalance();
			$data['main_view']['sourcefund']					= $this->configuration->SourceFundZakat();	
			$data['main_view']['content']						= 'AcctZakatFund/FormAddAcctZakatFundReceived_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processAddAcctZakatFundReceived(){
			$auth 		= $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');

			$data = array(
				'branch_id'						=> $auth['branch_id'],
				'zakat_fund_type'				=> 0,
				'zakat_fund_received_date'		=> tgltodb($this->input->post('zakat_fund_received_date', true)),
				'zakat_fund_opening_balance'	=> $this->input->post('zakat_fund_opening_balance', true),
				'zakat_fund_received_amount'	=> $this->input->post('zakat_fund_received_amount', true),
				'zakat_fund_last_balance'		=> $this->input->post('zakat_fund_last_balance', true),
				'zakat_fund_description'		=> $this->input->post('zakat_fund_description', true),
				'zakat_fund_source_fund'		=> $this->input->post('zakat_fund_source_fund', true),
				'created_id'					=> $auth['user_id'],
				'created_on'					=> date('Y-m-d H:i:s'),
			);

			// print_r($data);exit;
			
			$this->form_validation->set_rules('zakat_fund_source_fund', 'Sumber Dana', 'required');
			$this->form_validation->set_rules('zakat_fund_received_amount', 'Jumlah Penerimaan Zakat', 'required');

			$transaction_module_code = "ZISR";

			$transaction_module_id = $this->AcctZakatFund_model->getTransactionModuleID($transaction_module_code);

			if($this->form_validation->run()==true){
				if($this->AcctZakatFund_model->insertAcctZakatFund($data)){
					$acctzakatfund = $this->AcctZakatFund_model->getLastAcctZakatFundReceived($data['created_id']);
						
					$journal_voucher_period = date("Ym", strtotime($acctzakatfund['zakat_fund_received_date']));
					
					$data_journal = array(
						'branch_id'						=> $auth['branch_id'],
						'journal_voucher_period' 		=> $journal_voucher_period,
						'journal_voucher_date'			=> date('Y-m-d'),
						'journal_voucher_title'			=> $acctzakatfund['zakat_fund_description'],
						'journal_voucher_description'	=> $acctzakatfund['zakat_fund_description'],
						'transaction_module_id'			=> $transaction_module_id,
						'transaction_module_code'		=> $transaction_module_code,
						'transaction_journal_id' 		=> $acctzakatfund['zakat_fund_id'],
						'created_id' 					=> $auth['user_id'],
						'created_on' 					=> date('Y-m-d H:i:s'),
					);
					
					$this->AcctZakatFund_model->insertAcctJournalVoucher($data_journal);

					$journal_voucher_id = $this->AcctZakatFund_model->getJournalVoucherID($data_journal['created_id']);

					$preferencecompany = $this->AcctZakatFund_model->getPreferenceCompany();

					$account_id_default_status = $this->AcctZakatFund_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

					$data_debet = array (
						'journal_voucher_id'			=> $journal_voucher_id,
						'account_id'					=> $preferencecompany['account_cash_id'],
						'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
						'journal_voucher_amount'		=> $data['zakat_fund_received_amount'],
						'journal_voucher_debit_amount'	=> $data['zakat_fund_received_amount'],
						'account_id_default_status'		=> $account_id_default_status,
						'account_id_status'				=> 0,
					);

					$this->AcctZakatFund_model->insertAcctJournalVoucherItem($data_debet);

					$account_id_default_status = $this->AcctZakatFund_model->getAccountIDDefaultStatus($preferencecompany['account_zakat_id']);

					$data_credit = array (
						'journal_voucher_id'			=> $journal_voucher_id,
						'account_id'					=> $preferencecompany['account_zakat_id'],
						'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
						'journal_voucher_amount'		=> $data['zakat_fund_received_amount'],
						'journal_voucher_credit_amount'	=> $data['zakat_fund_received_amount'],
						'account_id_default_status'		=> $account_id_default_status,
						'account_id_status'				=> 1,
					);

					$this->AcctZakatFund_model->insertAcctJournalVoucherItem($data_credit);

					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['user_id'], $auth['username'],'1003','Application.AcctZakatFund.processAddAcctZakatFund',$auth['user_id'],'Add New Member');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Penerimaan Dana Zakat Sukses
							</div> ";

					$this->session->set_userdata('message',$msg);
					redirect('AcctZakatFund/getAcctZakatFundReceived');
				}else{
					$this->session->set_userdata('addcoremember',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Penerimaan Dana Zakat Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('AcctZakatFund/getAcctZakatFundReceived');
				}
			}else{
				$this->session->set_userdata('addcoremember',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('AcctZakatFund/getAcctZakatFundReceived');
			}
		}

		public function getAcctZakatFundDistribution(){
			$auth = $this->session->userdata('auth');
			$data['main_view']['corebranch']		= create_double($this->AcctZakatFund_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']			= 'AcctZakatFund/ListAcctZakatFundDistribution_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filterdistribution(){
			$data = array (
				"branch_id" 	=> $this->input->post('branch_id',true),
			);

			$this->session->set_userdata('filter-acctzakatfunddistribution',$data);
			redirect('AcctZakatFund/getAcctZakatFundDistribution');
		}
		public function reset_search(){
			$this->session->unset_userdata('filter-acctzakatfunddistribution');
			redirect('AcctZakatFund/getAcctZakatFundDistribution');
		}

		public function getAcctZakatFundDistributionList(){
			$auth = $this->session->userdata('auth');

			if($auth['branch_status'] == 1){
				$sesi	= 	$this->session->userdata('filter-acctzakatfunddistribution');
				if(!is_array($sesi)){
					$sesi['branch_id']		= '';
				}
			} else {
				$sesi['branch_id']	= $auth['branch_id'];
			}

			$list = $this->AcctZakatFund_model->get_datatables_zakat_distribution($sesi['branch_id']);

			// print_r($list);exit;
			$distribution		= $this->configuration->DistributionZakat();	
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $zakat) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = tgltoview($zakat->zakat_fund_distribution_date);
	            $row[] = $distribution[$zakat->zakat_fund_distribution_to];
	            $row[] = number_format($zakat->zakat_fund_distribution_amount, 2);
	            $row[] = $zakat->zakat_fund_description;
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctZakatFund_model->count_all_zakat_distribution($sesi['branch_id']),
	                        "recordsFiltered" => $this->AcctZakatFund_model->count_filtered_zakat_distribution($sesi['branch_id']),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}

		public function addAcctZakatFundDistribution(){
			$auth 	= $this->session->userdata('auth');

			$data['main_view']['zakat_fund_opening_balance'] 	= $this->AcctZakatFund_model->getZakatFundLastBalance();
			$data['main_view']['distribution']					= $this->configuration->DistributionZakat();	
			$data['main_view']['content']						= 'AcctZakatFund/FormAddAcctZakatFundDistribution_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processAddAcctZakatFundDistribution(){
			$auth 		= $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');

			$data = array(
				'branch_id'							=> $auth['branch_id'],
				'zakat_fund_type'					=> 1,
				'zakat_fund_distribution_date'		=> tgltodb($this->input->post('zakat_fund_distribution_date', true)),
				'zakat_fund_opening_balance'		=> $this->input->post('zakat_fund_opening_balance', true),
				'zakat_fund_distribution_amount'	=> $this->input->post('zakat_fund_distribution_amount', true),
				'zakat_fund_last_balance'			=> $this->input->post('zakat_fund_last_balance', true),
				'zakat_fund_description'			=> $this->input->post('zakat_fund_description', true),
				'zakat_fund_distribution_to'		=> $this->input->post('zakat_fund_distribution_to', true),
				'created_id'						=> $auth['user_id'],
				'created_on'						=> date('Y-m-d H:i:s'),
			);

			// print_r($data);exit;
			
			$this->form_validation->set_rules('zakat_fund_distribution_to', 'Disaluran ke', 'required');
			$this->form_validation->set_rules('zakat_fund_distribution_amount', 'Jumlah Penyaluran Zakat', 'required');

			$transaction_module_code = "ZISD";

			$transaction_module_id = $this->AcctZakatFund_model->getTransactionModuleID($transaction_module_code);

			if($this->form_validation->run()==true){
				if($this->AcctZakatFund_model->insertAcctZakatFund($data)){
					$acctzakatfund = $this->AcctZakatFund_model->getLastAcctZakatFundDistribution($data['created_id']);
						
					$journal_voucher_period = date("Ym", strtotime($acctzakatfund['zakat_fund_distribution_date']));
					
					$data_journal = array(
						'branch_id'						=> $auth['branch_id'],
						'journal_voucher_period' 		=> $journal_voucher_period,
						'journal_voucher_date'			=> date('Y-m-d'),
						'journal_voucher_title'			=> $acctzakatfund['zakat_fund_description'],
						'journal_voucher_description'	=> $acctzakatfund['zakat_fund_description'],
						'transaction_module_id'			=> $transaction_module_id,
						'transaction_module_code'		=> $transaction_module_code,
						'transaction_journal_id' 		=> $acctzakatfund['zakat_fund_id'],
						'created_id' 					=> $auth['user_id'],
						'created_on' 					=> date('Y-m-d H:i:s'),
					);
					
					$this->AcctZakatFund_model->insertAcctJournalVoucher($data_journal);

					$journal_voucher_id = $this->AcctZakatFund_model->getJournalVoucherID($data_journal['created_id']);

					$preferencecompany = $this->AcctZakatFund_model->getPreferenceCompany();

					$account_id_default_status = $this->AcctZakatFund_model->getAccountIDDefaultStatus($preferencecompany['account_zakat_id']);

					$data_debet = array (
						'journal_voucher_id'			=> $journal_voucher_id,
						'account_id'					=> $preferencecompany['account_zakat_id'],
						'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
						'journal_voucher_amount'		=> $data['zakat_fund_distribution_amount'],
						'journal_voucher_debit_amount'	=> $data['zakat_fund_distribution_amount'],
						'account_id_default_status'		=> $account_id_default_status,
						'account_id_status'				=> 0,
					);

					$this->AcctZakatFund_model->insertAcctJournalVoucherItem($data_debet);

					$account_id_default_status = $this->AcctZakatFund_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

					$data_credit = array (
						'journal_voucher_id'			=> $journal_voucher_id,
						'account_id'					=> $preferencecompany['account_cash_id'],
						'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
						'journal_voucher_amount'		=> $data['zakat_fund_distribution_amount'],
						'journal_voucher_credit_amount'	=> $data['zakat_fund_distribution_amount'],
						'account_id_default_status'		=> $account_id_default_status,
						'account_id_status'				=> 1,
					);

					$this->AcctZakatFund_model->insertAcctJournalVoucherItem($data_credit);

					

					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['user_id'], $auth['username'],'1003','Application.AcctZakatFund.processAddAcctZakatFund',$auth['user_id'],'Add New Member');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Penyaluran Dana Zakat Sukses
							</div> ";

					$this->session->set_userdata('message',$msg);
					redirect('AcctZakatFund/getAcctZakatFundDistribution');
				}else{
					$this->session->set_userdata('addcoremember',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Penyaluran Dana Zakat Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('AcctZakatFund/getAcctZakatFundDistribution');
				}
			}else{
				$this->session->set_userdata('addcoremember',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('AcctZakatFund/getAcctZakatFundDistribution');
			}
		}
	}
?>