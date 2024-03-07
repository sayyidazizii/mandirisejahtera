<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	define('FINANCIAL_MAX_ITERATIONS', 128);
	define('FINANCIAL_PRECISION', 1.0e-08);
	require_once('tcpdf/config/tcpdf_config.php');
	require_once('tcpdf/tcpdf.php');
	
	// Extend the TCPDF class to create custom Header and Footer
	class MYPDF extends TCPDF {
		public function Header() {
			$this->SetY(1500);
			$image_file = K_PATH_IMAGES.'kop2.png';
			$this->Image($image_file, 30, 13, 165, '', 'PNG', '', 'C', false, 400, 'C', false, false, 0, false, false, false);
			$this->SetFont('helvetica', 'B', 20);
			$style = array('width' => 0.5, 'color' => "black");
			$this->Line(20, 45, 190, 45, $style);
		}
	}
	Class AcctCreditAccount extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('CoreMember_model');
			$this->load->model('Core_account_Officer_model');
			$this->load->model('Core_source_fund_model');
			$this->load->model('AcctDepositoAccount_model');
			$this->load->model('AcctCredit_model');
			$this->load->model('AcctCreditAccount_model');
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
			$this->session->unset_userdata('acctcreditsaccounttoken-'.$unique['unique']);
			
			$data['main_view']['acctcredits']	= create_double($this->AcctCreditAccount_model->getAcctCredits(),'credits_id', 'credits_name');
			$data['main_view']['corebranch']	= create_double($this->AcctCreditAccount_model->getCoreBranch(),'branch_id', 'branch_name');
			$data['main_view']['content']		= 'AcctCreditAccount/ListAcctCreditsAccount_view';
			$this->load->view('MainPage_view', $data);
		}

		public function filteracctcreditsaccount(){
			$data = array (
				'start_date'	=> tgltodb($this->input->post('start_date', true)),
				'end_date'		=> tgltodb($this->input->post('end_date', true)),
				'credits_id'	=> $this->input->post('credits_id', true),
				'branch_id'		=> $this->input->post('branch_id', true),
			);

			$this->session->set_userdata('filter-acctcreditsaccountlist', $data);
			redirect('credit-account');
		}

		public function reset(){
			$this->session->unset_userdata('filter-acctcreditsaccountlist');
			redirect('credit-account');
		}

		public function getAcctCreditsAccountList(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-acctcreditsaccountlist');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['credits_id']		='';
				if($auth['branch_status'] == 1){
					$sesi['branch_id']	= '';
				}
				if($auth['branch_status'] == 0){
					$sesi['branch_id']	= $auth['branch_id'];
				}
			} else {
				if($auth['branch_status'] == 1){
					$sesi['branch_id']	= '';
				}
				if($auth['branch_status'] == 0){
					$sesi['branch_id']	= $auth['branch_id'];
				}

				/*print_r(" Sesi");*/
			}

			$creditsapprovestatus 	= $this->configuration->CreditsApproveStatus();
			$creditsaccountstatus 	= $this->configuration->CreditsAccountStatus();
			$creditsaccountpayment	= $this->configuration->PaymentType();

			$list = $this->AcctCreditAccount_model->get_datatables_master($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $creditsaccount) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $creditsaccount->credits_account_serial;
	            $row[] = $creditsaccount->member_name;
	            $row[] = $creditsaccount->credits_name;
	            $row[] = $creditsaccountpayment[$creditsaccount->payment_type_id];
	            $row[] = $creditsaccount->source_fund_name;
	            $row[] = tgltoview($creditsaccount->credits_account_date);
	            $row[] = number_format($creditsaccount->credits_account_amount, 2);
	            $row[] = $creditsaccountstatus[$creditsaccount->credits_account_status];


				if($creditsaccount->credits_approve_status == 0 && $auth['user_group_level'] == 5){
					$row[] = '			    		
						<a href="'.base_url().'credit-account/approving/'.$creditsaccount->credits_account_id.'" class="btn btn-xs purple" role="button"><i class="fa fa-check"></i> Proses</a>
						<a href="'.base_url().'credit-account/reject/'.$creditsaccount->credits_account_id.'" class="btn btn-xs red" onClick="javascript:return confirm(\'Apakah Anda yakin akan pembatalkan perjanjian kredit ini ?\')"  role="button"><i class="fa fa-times"></i> Reject</a>';
				}else{
					$row[] = $creditsapprovestatus[$creditsaccount->credits_approve_status];
				}

				if($creditsaccount->credits_approve_status == 0){
					$row[] = '
						<a href="'.base_url().'credit-account/print-note/'.$creditsaccount->credits_account_id.'" class="btn btn-xs blue" role="button"><i class="fa fa-print"></i> Kwitansi</a> &nbsp;
						<a href="'.base_url().'credit-account/process-print-akad/'.$creditsaccount->credits_account_id.'" class="btn btn-xs green" role="button"><i class="fa fa-print"></i> Akad</a>
						<a href="'.base_url().'credit-account/edit-date/'.$creditsaccount->credits_account_id.'" class="btn btn-xs green-jungle" role="button"><i class="fa fa-print"></i> Edit Tanggal</a>
						<a href="'.base_url().'credit-account/print-schedule-credits-payment/'.$creditsaccount->credits_account_id.'" class="btn btn-xs yellow-lemon" role="button"><i class="fa fa-print"></i> Jadwal Angsuran</a>
						<a href="'.base_url().'credit-account/print-schedule-credits-payment-member/'.$creditsaccount->credits_account_id.'" class="btn btn-xs purple" role="button"><i class="fa fa-print"></i> Jadwal Angsuran Untuk Anggota</a>
						<a href="'.base_url().'credit-account/print-agunan-receipt/'.$creditsaccount->credits_account_id.'" class="btn btn-xs purple-medium" role="button"><i class="fa fa-print"></i> Tanda Terima Jaminan</a>';
				}else if($creditsaccount->credits_approve_status == 1){
					$row[] = '
						<a href="'.base_url().'credit-account/print-note/'.$creditsaccount->credits_account_id.'" class="btn btn-xs blue" role="button"><i class="fa fa-print"></i> Kwitansi</a> &nbsp;
						<a href="'.base_url().'credit-account/process-print-akad/'.$creditsaccount->credits_account_id.'" class="btn btn-xs green" role="button"><i class="fa fa-print"></i> Akad</a>
						<a href="'.base_url().'credit-account/print-schedule-credits-payment/'.$creditsaccount->credits_account_id.'" class="btn btn-xs yellow-lemon" role="button"><i class="fa fa-print"></i> Jadwal Angsuran</a>
						<a href="'.base_url().'credit-account/print-schedule-credits-payment-member/'.$creditsaccount->credits_account_id.'" class="btn btn-xs purple" role="button"><i class="fa fa-print"></i> Jadwal Angsuran Untuk Anggota</a>
						<a href="'.base_url().'credit-account/print-agunan-receipt/'.$creditsaccount->credits_account_id.'" class="btn btn-xs purple-medium" role="button"><i class="fa fa-print"></i> Tanda Terima Jaminan</a>';
				}else{
					$row[] = '
						<a href="'.base_url().'credit-account/print-note/'.$creditsaccount->credits_account_id.'" class="btn btn-xs blue" role="button"><i class="fa fa-print"></i> Kwitansi</a> &nbsp;
						<a href="'.base_url().'credit-account/process-print-akad/'.$creditsaccount->credits_account_id.'" class="btn btn-xs green" role="button"><i class="fa fa-print"></i> Akad</a>
						<a href="'.base_url().'credit-account/print-schedule-credits-payment/'.$creditsaccount->credits_account_id.'" class="btn btn-xs yellow-lemon" role="button"><i class="fa fa-print"></i> Jadwal Angsuran</a>
						<a href="'.base_url().'credit-account/print-schedule-credits-payment-member/'.$creditsaccount->credits_account_id.'" class="btn btn-xs purple" role="button"><i class="fa fa-print"></i> Jadwal Angsuran Untuk Anggota</a>
						<a href="'.base_url().'credit-account/print-agunan-receipt/'.$creditsaccount->credits_account_id.'" class="btn btn-xs purple-medium" role="button"><i class="fa fa-print"></i> Tanda Terima Jaminan</a>
						<a href="'.base_url().'credit-account/delete/'.$creditsaccount->credits_account_id.'" class="btn btn-xs red" role="button"><i class="fa fa-trash"></i> Hapus</a>';
				}
			    // }
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctCreditAccount_model->count_all_master($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']),
	                        "recordsFiltered" => $this->AcctCreditAccount_model->count_filtered_master($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}

		public function function_elements_add(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addcreditaccount-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('addcreditaccount-'.$unique['unique'],$sessions);
		}

		public function reset_data(){
			$unique 	= $this->session->userdata('unique');
			$sessions	= $this->session->unset_userdata('addcreditaccount-'.$unique['unique']);
			$this->session->unset_userdata('addarrayacctcreditsagunan-'.$unique['unique']);
			redirect('credit-account/add-form');
		}

		public function addform(){
			$auth 	= $this->session->userdata('auth');
			$unique = $this->session->userdata('unique');
			$token 	= $this->session->userdata('acctcreditsaccounttoken-'.$unique['unique']);

			if(empty($token)){
				$token = md5(date('Y-m-d H:i:s'));
				$this->session->set_userdata('acctcreditsaccounttoken-'.$unique['unique'], $token);
				$this->session->unset_userdata('addcreditaccount-' . $unique['unique']);
			}

			$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();
			$data['main_view']['membergender']				= $this->configuration->MemberGender();
			$data['main_view']['paymentperiod']				= $this->configuration->CreditsPaymentPeriod();
			$data['main_view']['coreoffice']				= create_double($this->AcctCreditAccount_model->getCoreOffice(),'office_id', 'office_name');
			$data['main_view']['sumberdana']				= create_double($this->Core_source_fund_model->getData(),'source_fund_id', 'source_fund_name');
			$data['main_view']['coremember']				= $this->CoreMember_model->getCoreMember_Detail($this->uri->segment(3));
			$data['main_view']['acctsavingsaccount']		= create_double($this->AcctDepositoAccount_model->getAcctSavingsAccount($auth['branch_id']),'savings_account_id', 'savings_account_no');
			$data['main_view']['creditid']					= create_double($this->AcctCreditAccount_model->getAcctCredits(),'credits_id', 'credits_name');
			$data['main_view']['paymenttype']				= $this->configuration->PaymentType();
			$data['main_view']['paymentpreference']			= $this->configuration->PaymentPreference();
			$data['main_view']['content']					= 'AcctCreditAccount/FormAddAcctCreditAccount_view';
			$this->load->view('MainPage_view',$data);
		}

		public function editDateAcctCreditAccount(){
			$auth 				= $this->session->userdata('auth');
			$unique 			= $this->session->userdata('unique');
			$credits_account_id	= $this->uri->segment(3);

			$data['main_view']['acctcreditsaccount']			= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);
			$data['main_view']['content']					= 'AcctCreditAccount/FormEditDateAcctCreditsAccount_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processEditDateAcctCreditAccount(){
			$data = array(
				'credits_account_id'			=> $this->input->post('credits_account_id', true),
				'credits_account_date' 			=> tgltodb($this->input->post('credits_account_date', true)),
				'credits_account_due_date' 		=> tgltodb($this->input->post('credits_account_due_date', true)),
				'credits_account_payment_date' 	=> tgltodb($this->input->post('credits_account_payment_date', true)),
			);

			if($this->AcctCreditAccount_model->updatedata($data, $data['credits_account_id'])){
				$msg = "<div class='alert alert-success alert-dismissable'>
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Edit Tanggal Pinjaman Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				$url='credit-account';
				redirect($url);
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Edit Tanggal Pinjaman Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				$url='credit-account/edit-date/'.$data['credits_account_id'];
				redirect($url);
			}
		}

		public function deleteAcctCreditAccount(){
			$credits_account_id	= $this->uri->segment(3);

			$data = array(
				'credits_account_id'		=> $credits_account_id,
				'data_state'			 	=> 1,
			);

			if($this->AcctCreditAccount_model->updatedata($data, $data['credits_account_id'])){
				$msg = "<div class='alert alert-success alert-dismissable'>
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Hapus Pinjaman Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				$url='credit-account';
				redirect($url);
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Hapus Pinjaman Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				$url='credit-account';
				redirect($url);
			}
		}

		public function getCreditsAccountSerial(){
			$auth = $this->session->userdata('auth'); 

			$credits_id 		= $this->input->post('credits_id');

			//$credits_id = 4;
			
			$branchcode 			= $this->AcctCreditAccount_model->getBranchCode($auth['branch_id']);
			$credits_code 			= $this->AcctCreditAccount_model->getCreditsCode($credits_id);
			$lastcreditsaccountno 	= $this->AcctCreditAccount_model->getLastAccountCreditsNo($auth['branch_id'], $credits_id);

			if($lastcreditsaccountno->num_rows() <> 0){      
			   //jika kode ternyata sudah ada.      
			   $data = $lastcreditsaccountno->row_array();    
			   $kode = intval($data['last_credits_account_serial']) + 1;
			   
			 } else {      
			   //jika kode belum ada      
			   $kode = 1;    
			}
			
			$kodemax 					= str_pad($kode, 5, "0", STR_PAD_LEFT);
			$new_credits_account_serial = $branchcode.$credits_code.$kodemax;

			$result = array ();
			$result = array (
				'credit_account_serial'		=> $new_credits_account_serial,
			);

			echo json_encode($result);		
		}

		public function memberlist(){
			$auth = $this->session->userdata('auth');

			$list = $this->CoreMember_model->get_datatables_status($auth['branch_id']);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $customers) {
					$no++;
					$row = array();
					$row[] = $no;
					$row[] = $customers->member_no;
					$row[] = $customers->member_name;
					$row[] = $customers->member_address;
					$row[] = '<a href="'.base_url().'credit-account/add-form/'.$customers->member_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
					$data[] = $row;
			}
	
			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->CoreMember_model->count_all_status($auth['branch_id']),
							"recordsFiltered" => $this->CoreMember_model->count_filtered_status($auth['branch_id']),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}

		public function processAddArrayAgunan(){
			$date = date('Ymdhis');
			$credits_agunan_type 			= $this->input->post('tipe', true);


				$data_agunan = array(
					'record_id' 								=> $credits_agunan_type.$date,
					'credits_agunan_type' 						=> $this->input->post('tipe', true),
					'credits_agunan_bpkb_nomor' 				=> $this->input->post('bpkb_nomor', true),
					'credits_agunan_bpkb_type' 					=> $this->input->post('bpkb_type', true),
					'credits_agunan_bpkb_nopol' 				=> $this->input->post('bpkb_nopol', true),
					'credits_agunan_bpkb_nama' 					=> $this->input->post('bpkb_nama', true),
					'credits_agunan_bpkb_address' 				=> $this->input->post('bpkb_address', true),
					'credits_agunan_bpkb_no_mesin' 				=> $this->input->post('bpkb_no_mesin', true),
					'credits_agunan_bpkb_no_rangka'				=> $this->input->post('bpkb_no_rangka', true),
					'credits_agunan_bpkb_dealer_name'			=> $this->input->post('bpkb_dealer_name', true),
					'credits_agunan_bpkb_dealer_address'		=> $this->input->post('bpkb_dealer_address', true),
					'credits_agunan_bpkb_taksiran' 				=> $this->input->post('bpkb_taksiran', true),
					'credits_agunan_bpkb_gross' 				=> $this->input->post('bpkb_gross', true),
					'credits_agunan_bpkb_keterangan'			=> $this->input->post('bpkb_keterangan', true),
					'credits_agunan_shm_no_sertifikat' 			=> $this->input->post('shm_no_sertifikat', true),
					'credits_agunan_shm_luas' 					=> $this->input->post('shm_luas', true),
					'credits_agunan_shm_no_gs' 					=> $this->input->post('shm_no_gs', true),
					'credits_agunan_shm_gambar_gs' 				=> $this->input->post('shm_tanggal_gs', true),
					'credits_agunan_shm_atas_nama' 				=> $this->input->post('shm_atas_nama', true),
					'credits_agunan_shm_kedudukan' 				=> $this->input->post('shm_kedudukan', true),
					'credits_agunan_shm_taksiran' 				=> $this->input->post('shm_taksiran', true),
					'credits_agunan_shm_keterangan'				=> $this->input->post('shm_keterangan', true),
					'credits_agunan_atmjamsostek_nomor'			=> $this->input->post('atmjamsostek_nomor', true),
					'credits_agunan_atmjamsostek_nama'			=> $this->input->post('atmjamsostek_nama', true),
					'credits_agunan_atmjamsostek_bank'			=> $this->input->post('atmjamsostek_bank', true),
					'credits_agunan_atmjamsostek_taksiran'		=> $this->input->post('atmjamsostek_taksiran', true),
					'credits_agunan_atmjamsostek_keterangan'	=> $this->input->post('atmjamsostek_keterangan', true),
					'credits_agunan_other_keterangan'			=> $this->input->post('other_keterangan', true)
				);


			$unique 			= $this->session->userdata('unique');
			$session_name 		= $this->input->post('session_name',true);
			$dataArrayHeader	= $this->session->userdata('addarrayacctcreditsagunan-'.$unique['unique']);
			
			$dataArrayHeader[$data_agunan['record_id']] = $data_agunan;
			
			$this->session->set_userdata('addarrayacctcreditsagunan-'.$unique['unique'],$dataArrayHeader);
			// $sesi 	= $this->session->userdata('unique');
			// $data_agunan = $this->session->userdata('addacctcreditsagunan-'.$sesi['unique']);
			
			$data_agunan['record_id'] 								= '';
			$data_agunan['credits_agunan_type'] 					= '';
			$data_agunan['credits_agunan_bpkb_nomor'] 				= '';
			$data_agunan['credits_agunan_bpkb_type'] 				= '';
			$data_agunan['credits_agunan_bpkb_nama'] 				= '';
			$data_agunan['credits_agunan_bpkb_address']				= '';
			$data_agunan['credits_agunan_bpkb_nopol'] 				= '';
			$data_agunan['credits_agunan_bpkb_no_mesin'] 			= '';
			$data_agunan['credits_agunan_bpkb_no_rangka'] 			= '';
			$data_agunan['credits_agunan_bpkb_dealer_name']			= '';
			$data_agunan['credits_agunan_bpkb_dealer_no'] 			= '';
			$data_agunan['credits_agunan_bpkb_taksiran'] 			= '';
			$data_agunan['credits_agunan_bpkb_gross'] 				= '';
			$data_agunan['credits_agunan_bpkb_keterangan'] 			= '';
			$data_agunan['credits_agunan_shm_no_sertifikat'] 		= '';
			$data_agunan['credits_agunan_shm_luas'] 				= '';
			$data_agunan['credits_agunan_shm_no_gs'] 				= '';
			$data_agunan['credits_agunan_shm_gambar_gs'] 			= '';
			$data_agunan['credits_agunan_shm_atas_nama'] 			= '';
			$data_agunan['credits_agunan_shm_kedudukan'] 			= '';
			$data_agunan['credits_agunan_shm_taksiran'] 			= '';
			$data_agunan['credits_agunan_shm_keterangan'] 			= '';
			$data_agunan['credits_agunan_atmjamsostek_nomor'] 		= '';
			$data_agunan['credits_agunan_atmjamsostek_nama'] 		= '';
			$data_agunan['credits_agunan_atmjamsostek_bank'] 		= '';
			$data_agunan['credits_agunan_atmjamsostek_taksiran'] 	= '';
			$data_agunan['credits_agunan_atmjamsostek_keterangan'] 	= '';
			$data_agunan['credits_agunan_other_keterangan'] 		= '';

		}

		public function addcreditaccount(){
			$auth 			= $this->session->userdata('auth');
			$sesi 			= $this->session->userdata('unique');
			$daftaragunan 	= $this->session->userdata('addarrayacctcreditsagunan-'.$sesi['unique']);

			$agunan_data 	= $this->session->userdata('agunan_data');
			$agunan 		= $this->session->userdata('agunan_key');
			$a 				= json_encode($agunan_data);
			// print_r($this->session->userdata('agunan_data'));exit;
			$this->session->unset_userdata('agunan_data');
			$this->session->unset_userdata('agunan_key');

			$member_id 		= $this->input->post('member_id',true);
			if(empty($member_id)){
				$member_id 	= $this->uri->segment(3);
			}
		

			$data = array (
				"credits_account_date" 						=> tgltodb($this->input->post('credit_account_date',true)),
				"member_id"									=> $this->input->post('member_id',true),
				"office_id"									=> $this->input->post('office_id',true),
				"source_fund_id"							=> $this->input->post('sumberdana',true),
				"credits_id"								=> $this->input->post('credit_id',true),
				"branch_id"									=> $auth['branch_id'],
				"payment_preference_id"						=> $this->input->post('payment_preference_id',true),
				"payment_type_id"							=> $this->input->post('payment_type_id',true),
				"credits_payment_period"					=> $this->input->post('payment_period',true),
				"credits_account_period"					=> $this->input->post('credit_account_period',true),
				"credits_account_due_date"					=> tgltodb($this->input->post('credit_account_due_date',true)),
				"credits_account_amount"					=> $this->input->post('credits_account_last_balance_principal',true),
				"credits_account_sales_name"				=> $this->input->post('credit_account_sales_name',true),
				"credits_account_interest"					=> $this->input->post('credit_account_interest',true),
				"credits_account_provisi"					=> $this->input->post('credit_account_provisi',true),
				"credits_account_komisi"					=> $this->input->post('credit_account_komisi',true),
				"credits_account_adm_cost"					=> $this->input->post('credit_account_adm_cost',true),
				"credits_account_insurance"					=> $this->input->post('credit_account_insurance',true),
				"credits_account_materai"					=> $this->input->post('credit_account_materai',true),
				"credits_account_risk_reserve"				=> $this->input->post('credit_account_risk_reserve',true),
				"credits_account_stash"						=> $this->input->post('credit_account_stash',true),
				"credits_account_principal"					=> $this->input->post('credit_account_principal',true),
				"credits_account_amount_received"			=> $this->input->post('credit_account_amount_received',true),
				"credits_account_principal_amount"			=> $this->input->post('credits_account_principal_amount',true),
				"credits_account_interest_amount"			=> $this->input->post('credits_account_interest_amount',true),
				"credits_account_payment_amount"			=> $this->input->post('credit_account_payment_amount',true),
				"credits_account_last_balance"				=> $this->input->post('credits_account_last_balance_principal',true),
				"credits_account_payment_date"				=> tgltodb($this->input->post('credit_account_payment_to',true)),
				"savings_account_id"						=> $this->input->post('savings_account_id',true),
				"credits_account_token" 					=> $this->input->post('credits_account_token',true),
				"created_id"								=> $auth['user_id'],
				"created_on"								=> date('Y-m-d H:i:s'),
			);

			$this->form_validation->set_rules('credit_id', 'jenis Pinjaman', 'required');
			$this->form_validation->set_rules('credits_account_last_balance_principal', 'Pinjaman', 'required');
			$this->form_validation->set_rules('credit_account_interest', 'Bunga Per Bulan', 'required');
			$this->form_validation->set_rules('payment_type_id', 'Jenis Angsuran', 'required');
			$this->form_validation->set_rules('payment_period', 'Angsuran Tiap', 'required');
			$this->form_validation->set_rules('credit_account_period', 'Jangka Waktu', 'required');
			$this->form_validation->set_rules('office_id', 'Business Officer (BO)', 'required');
			$this->form_validation->set_rules('sumberdana', 'Sumber Dana', 'required');
			// $this->form_validation->set_rules('savings_account_id', 'No Simpanan', 'required');

			$credits_account_token 					= $this->AcctCreditAccount_model->getCreditsAccountToken($data['credits_account_token']);

			if($this->form_validation->run()==true){

				if($credits_account_token->num_rows()==0){
					if($this->AcctCreditAccount_model->insertAcctCreditAccount($data)){
						$acctcreditsaccount_last 				= $this->AcctCreditAccount_model->getAcctCreditsAccount_Last($data['created_on']);
						
						if(!empty($daftaragunan)){
							foreach ($daftaragunan as $key => $val) {
								if($val['credits_agunan_type'] == 'BPKB'){
									$credits_agunan_type	= 1;
								}else if($val['credits_agunan_type'] == 'Sertifikat') {
									$credits_agunan_type 	= 2;
								}else if($val['credits_agunan_type'] == 'Bilyet Simpanan Berjangka'){
									$credits_agunan_type 	= 3;
								}else if($val['credits_agunan_type'] == 'Elektro'){
									$credits_agunan_type 	= 4;
								}else if($val['credits_agunan_type'] == 'Dana Keanggotaan'){
									$credits_agunan_type 	= 5;
								}else if($val['credits_agunan_type'] == 'Tabungan'){
									$credits_agunan_type 	= 6;
								}else if($val['credits_agunan_type'] == 'ATM / Jamsostek'){
									$credits_agunan_type 	= 7;
								}
								$dataagunan = array (
									'credits_account_id'						=> $acctcreditsaccount_last['credits_account_id'],
									'credits_agunan_type'						=> $credits_agunan_type,
									'credits_agunan_shm_no_sertifikat'			=> $val['credits_agunan_shm_no_sertifikat'],
									'credits_agunan_shm_atas_nama'				=> $val['credits_agunan_shm_atas_nama'],
									'credits_agunan_shm_luas'					=> $val['credits_agunan_shm_luas'],
									'credits_agunan_shm_no_gs'					=> $val['credits_agunan_shm_no_gs'],
									'credits_agunan_shm_gambar_gs'				=> $val['credits_agunan_shm_gambar_gs'],
									'credits_agunan_shm_kedudukan'				=> $val['credits_agunan_shm_kedudukan'],
									'credits_agunan_shm_taksiran'				=> $val['credits_agunan_shm_taksiran'],
									'credits_agunan_shm_keterangan'				=> $val['credits_agunan_shm_keterangan'],
									'credits_agunan_bpkb_nomor'					=> $val['credits_agunan_bpkb_nomor'],
									'credits_agunan_bpkb_type'					=> $val['credits_agunan_bpkb_type'],
									'credits_agunan_bpkb_nama'					=> $val['credits_agunan_bpkb_nama'],
									'credits_agunan_bpkb_address'				=> $val['credits_agunan_bpkb_address'],
									'credits_agunan_bpkb_nopol'					=> $val['credits_agunan_bpkb_nopol'],
									'credits_agunan_bpkb_no_rangka'				=> $val['credits_agunan_bpkb_no_rangka'],
									'credits_agunan_bpkb_no_mesin'				=> $val['credits_agunan_bpkb_no_mesin'],
									'credits_agunan_bpkb_dealer_name'			=> $val['credits_agunan_bpkb_dealer_name'],
									'credits_agunan_bpkb_dealer_address'		=> $val['credits_agunan_bpkb_dealer_address'],
									'credits_agunan_bpkb_taksiran'				=> $val['credits_agunan_bpkb_taksiran'],
									'credits_agunan_bpkb_gross'					=> $val['credits_agunan_bpkb_gross'],
									'credits_agunan_bpkb_keterangan'			=> $val['credits_agunan_bpkb_keterangan'],
									'credits_agunan_atmjamsostek_nomor'			=> $val['credits_agunan_atmjamsostek_nomor'],
									'credits_agunan_atmjamsostek_nama'			=> $val['credits_agunan_atmjamsostek_nama'],
									'credits_agunan_atmjamsostek_bank'			=> $val['credits_agunan_atmjamsostek_bank'],
									'credits_agunan_atmjamsostek_taksiran'		=> $val['credits_agunan_atmjamsostek_taksiran'],
									'credits_agunan_atmjamsostek_keterangan'	=> $val['credits_agunan_atmjamsostek_keterangan'],
									'credits_agunan_other_keterangan'			=> $val['credits_agunan_other_keterangan'],
									
								);

								$this->AcctCreditAccount_model->insertAcctCreditsAgunan($dataagunan);
								// print_r($dataagunan);
							}
						}

						$auth = $this->session->userdata('auth');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Credit Berjangka Sukses
								</div> ";
						$sesi = $this->session->userdata('unique');

						$this->session->unset_userdata('addarrayacctcreditsagunan-'.$sesi['unique']);
						$this->session->unset_userdata('addacctcreditaccount-'.$sesi['unique']);
						$this->session->unset_userdata('addcreditaccount-'.$sesi['unique']);
						$this->session->unset_userdata('acctcreditsaccounttoken-'.$sesi['unique']);
						$this->session->set_userdata('message',$msg);
						$url='credit-account/show-detail-data/'.$acctcreditsaccount_last['credits_account_id'].'/'.$data['payment_type_id'];
						redirect($url);
					}else{
						$this->session->set_userdata('addacctdepositoaccount',$data);
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Credit Berjangka Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message',$msg);
						$url='credit-account/add-form/'.$member_id;
						redirect($url);
					}
				} else {
					$this->session->set_userdata('addcreditaccount',$data);
					$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
					$this->session->set_userdata('message',$msg);
					redirect('credit-account/add-form/'.$data['member_id']);
				}
			}else{
				$this->session->set_userdata('addcreditaccount',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('credit-account/add-form/'.$data['member_id']);
			}
			
		}

		public function Approving(){
			$credits_account_id 	= $this->uri->segment(3);
			$unique = $this->session->userdata('unique');
			$token 	= $this->session->userdata('acctcreditsaccounttoken-'.$unique['unique']);

			if(empty($token)){
				$token = md5(date('Y-m-d H:i:s'));
				$this->session->set_userdata('acctcreditsaccounttoken-'.$unique['unique'], $token);
			}

			$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();
			
			$data['main_view']['approvalstatus']			= $this->configuration->ApprovalStatus();

			$data['main_view']['paymenttype']				= $this->configuration->PaymentType();

			$data['main_view']['acctcreditsaccount']		= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);

			$data['main_view']['content']					= 'AcctCreditAccount/FormApproveAcctCreditsAccount_view';
			
			$this->load->view('MainPage_view',$data);
		}

		public function processApprove(){
			$auth 			= $this->session->userdata('auth');

			$dataApprove = array (
				'credits_account_id'		=> $this->input->post('credits_account_id', true),
				'credits_account_token'		=> $this->input->post('credits_account_token', true),
				'credits_approve_status'	=> 1,
			);

			$data = array(
				'credits_account_amount'			=> $this->input->post('credits_account_amount', true),
				'credits_account_adm_cost'			=> $this->input->post('credits_account_adm_cost', true),
				'credits_account_provisi'			=> $this->input->post('credits_account_provisi', true),
				'credits_account_komisi'			=> $this->input->post('credits_account_komisi', true),
				'credits_account_insurance'			=> $this->input->post('credits_account_insurance', true),
				'credits_account_materai'			=> $this->input->post('credits_account_materai', true),
				'credits_account_risk_reserve'		=> $this->input->post('credits_account_risk_reserve', true),
				'credits_account_stash'				=> $this->input->post('credits_account_stash', true),
				'credits_account_principal'			=> $this->input->post('credits_account_principal', true),
				'credits_account_amount_received'	=> $this->input->post('credits_account_amount_received', true),
				'credits_account_date'				=> $this->input->post('credits_account_date', true),
				'credits_account_notaris'			=> $this->input->post('credits_account_notaris', true),
				'credits_id'						=> $this->input->post('credits_id',true),
			);
			
			if($data['credits_account_provisi'] != '' && $data['credits_account_provisi'] > 0){
				$provisi = $data['credits_account_provisi'];
			}else{
				$provisi = 0;
			}

			if($data['credits_account_komisi'] != '' && $data['credits_account_komisi'] > 0){
				$komisi = $data['credits_account_komisi'];
			}else{
				$komisi = 0;
			}

			/*print_r($data); exit;*/
			$this->form_validation->set_rules('credits_account_id','No. Perjanjian Kredit', 'required');
			
			$transaction_module_code 				= 'PYB';

			$transaction_module_id 					= $this->AcctCreditAccount_model->getTransactionModuleID($transaction_module_code);

			$preferencecompany 						= $this->AcctCreditAccount_model->getPreferenceCompany();

			$preferenceinventory 					= $this->AcctCreditAccount_model->getPreferenceInventory();			

			$credits_account_token 					= $this->AcctCreditAccount_model->getCreditsAccountToken($dataApprove['credits_account_token']);
			
			$journal_voucher_period 				= date("Ym", strtotime($data['credits_account_date']));

			if($this->form_validation->run()==true){
				if($credits_account_token->num_rows()==0){
					if($this->AcctCreditAccount_model->updateApprove($dataApprove)){
						$acctcreditsaccount_last = $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($dataApprove['credits_account_id']);	
						$auth = $this->session->userdata('auth');
						
						$data_journal = array(
							'branch_id'						=> $auth['branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'PEMBIAYAAN '.$acctcreditsaccount_last['credits_name'].' '.$acctcreditsaccount_last['member_name'],
							'journal_voucher_description'	=> 'PEMBIAYAAN '.$acctcreditsaccount_last['credits_name'].' '.$acctcreditsaccount_last['member_name'],
							'journal_voucher_token'			=> $dataApprove['credits_account_token'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $acctcreditsaccount_last['credits_account_id'],
							'transaction_journal_no' 		=> $acctcreditsaccount_last['credits_account_serial'],
							'created_id'					=> $auth['user_id'],								
							'created_on' 					=> date('Y-m-d H:i:s'),
						);
						$this->AcctCreditAccount_model->insertAcctJournalVoucher($data_journal);

						$journal_voucher_id 				= $this->AcctCreditAccount_model->getJournalVoucherID($data_journal['created_id']);


						$receivable_account_id				= $this->AcctCreditAccount_model->getReceivableAccountID($data['credits_id']);

						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($receivable_account_id);

						$data_debet = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $receivable_account_id,
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_account_amount'],
							'journal_voucher_debit_amount'	=> $data['credits_account_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token' 	=> $dataApprove['credits_account_token'].$receivable_account_id,
							'created_id' 					=> $auth['user_id'],
						);
						$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);

						$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();
						$preferenceinventory 				= $this->AcctCreditAccount_model->getPreferenceInventory();			


						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

						$data_credit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_cash_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_account_amount'],
							'journal_voucher_credit_amount'	=> $data['credits_account_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $dataApprove['credits_account_token'].$preferencecompany['account_cash_id'],
							'created_id' 					=> $auth['user_id'],
						);
						$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);		

						if($provisi != '' && $provisi > 0){

							$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();
							$preferenceinventory 				= $this->AcctCreditAccount_model->getPreferenceInventory();	
							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

							$data_debet = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $provisi,
								'journal_voucher_debit_amount'	=> $provisi,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token' 	=> $dataApprove['credits_account_token'].'PR'.$preferencecompany['account_cash_id'],
								'created_id' 					=> $auth['user_id'],
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);

							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferenceinventory['inventory_stamp_duty_id']);

							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferenceinventory['inventory_stamp_duty_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $provisi,
								'journal_voucher_credit_amount'	=> $provisi,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $dataApprove['credits_account_token'].'PR'.$preferenceinventory['inventory_stamp_duty_id'],
								'created_id' 					=> $auth['user_id'],
							); 
							
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
							
						}	

						if($komisi != '' && $komisi > 0){

							$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();
							$preferenceinventory 				= $this->AcctCreditAccount_model->getPreferenceInventory();	
							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

							$data_debet = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $komisi,
								'journal_voucher_debit_amount'	=> $komisi,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token' 	=> $dataApprove['credits_account_token'].'KM'.$preferencecompany['account_cash_id'],
								'created_id' 					=> $auth['user_id'],
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);

							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_commission_id']);

							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_commission_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $komisi,
								'journal_voucher_credit_amount'	=> $komisi,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $dataApprove['credits_account_token'].'KM'.$preferencecompany['inventory_stamp_duty_id'],
								'created_id' 					=> $auth['user_id'],
							); 
							
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
							
						}

						if($data['credits_account_adm_cost'] != '' && $data['credits_account_adm_cost'] > 0){
							$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();
							$preferenceinventory 				= $this->AcctCreditAccount_model->getPreferenceInventory();	
							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

							$data_debet = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_account_adm_cost'],
								'journal_voucher_debit_amount'	=> $data['credits_account_adm_cost'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token' 	=> $dataApprove['credits_account_token'].'ADM'.$preferencecompany['account_cash_id'],
								'created_id' 					=> $auth['user_id'],
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);

							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferenceinventory['inventory_adm_id']);

							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferenceinventory['inventory_adm_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_account_adm_cost'],
								'journal_voucher_credit_amount'	=> $data['credits_account_adm_cost'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $dataApprove['credits_account_token'].'ADM'.$preferenceinventory['inventory_adm_id'],
								'created_id' 					=> $auth['user_id'],
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
							
						}

						if($data['credits_account_materai'] != '' && $data['credits_account_materai'] > 0){
							$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();
							$preferenceinventory 				= $this->AcctCreditAccount_model->getPreferenceInventory();	
							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

							$data_debet = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_account_materai'],
								'journal_voucher_debit_amount'	=> $data['credits_account_materai'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token' 	=> $dataApprove['credits_account_token'].'MT'.$preferencecompany['account_cash_id'],
								'created_id' 					=> $auth['user_id'],
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);

							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_materai_id']);

							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_materai_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_account_materai'],
								'journal_voucher_credit_amount'	=> $data['credits_account_materai'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $dataApprove['credits_account_token'].'MT'.$preferencecompany['account_materai_id'],
								'created_id' 					=> $auth['user_id'],
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
							
						}

						if($data['credits_account_risk_reserve'] != '' && $data['credits_account_risk_reserve'] > 0){
							$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();
							$preferenceinventory 				= $this->AcctCreditAccount_model->getPreferenceInventory();	
							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

							$data_debet = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_account_risk_reserve'],
								'journal_voucher_debit_amount'	=> $data['credits_account_risk_reserve'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token' 	=> $dataApprove['credits_account_token'].'RR'.$preferencecompany['account_cash_id'],
								'created_id' 					=> $auth['user_id'],
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);

							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_risk_reserve_id']);

							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_risk_reserve_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_account_risk_reserve'],
								'journal_voucher_credit_amount'	=> $data['credits_account_risk_reserve'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $dataApprove['credits_account_token'].'RR'.$preferencecompany['account_risk_reserve_id'],
								'created_id' 					=> $auth['user_id'],
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
							
						}

						if($data['credits_account_stash'] != '' && $data['credits_account_stash'] > 0){
							$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();
							$preferenceinventory 				= $this->AcctCreditAccount_model->getPreferenceInventory();	
							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

							$data_debet = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_account_stash'],
								'journal_voucher_debit_amount'	=> $data['credits_account_stash'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token' 	=> $dataApprove['credits_account_token'].'ST'.$preferencecompany['account_cash_id'],
								'created_id' 					=> $auth['user_id'],
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);

							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_stash_id']);

							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_stash_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_account_stash'],
								'journal_voucher_credit_amount'	=> $data['credits_account_stash'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $dataApprove['credits_account_token'].'ST'.$preferencecompany['account_stash_id'],
								'created_id' 					=> $auth['user_id'],
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
							
							$data_detail = array (
								'branch_id'						=> $auth['branch_id'],
								'member_id'						=> $this->input->post('member_id', true),
								'mutation_id'					=> $preferencecompany['cash_deposit_id'],
								'transaction_date'				=> date('Y-m-d'),
								'principal_savings_amount'		=> 0,
								'special_savings_amount'		=> 0,
								'mandatory_savings_amount'		=> $data['credits_account_stash'],
								'operated_name'					=> $auth['username'],
								'savings_member_detail_token'	=> $dataApprove['credits_account_token'].'ST',
							);

							$this->CoreMember_model->insertAcctSavingsMemberDetail($data_detail);

							$data_member = array (
								'member_id'								=> $this->input->post('member_id', true),
								'member_mandatory_savings_last_balance'	=> $this->input->post('member_mandatory_savings_last_balance', true) + $data['credits_account_stash'],
							);

							$this->CoreMember_model->updateCoreMember($data_member);
							
						}

						if($data['credits_account_principal'] != '' && $data['credits_account_principal'] > 0){
							$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();
							$preferenceinventory 				= $this->AcctCreditAccount_model->getPreferenceInventory();	
							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

							$data_debet = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_account_principal'],
								'journal_voucher_debit_amount'	=> $data['credits_account_principal'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token' 	=> $dataApprove['credits_account_token'].'ST'.$preferencecompany['account_cash_id'],
								'created_id' 					=> $auth['user_id'],
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);

							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_principal_id']);

							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_principal_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_account_principal'],
								'journal_voucher_credit_amount'	=> $data['credits_account_principal'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $dataApprove['credits_account_token'].'ST'.$preferencecompany['account_principal_id'],
								'created_id' 					=> $auth['user_id'],
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
							
							$data_detail = array (
								'branch_id'						=> $auth['branch_id'],
								'member_id'						=> $this->input->post('member_id', true),
								'mutation_id'					=> $preferencecompany['cash_deposit_id'],
								'transaction_date'				=> date('Y-m-d'),
								'principal_savings_amount'		=> 0,
								'special_savings_amount'		=> 0,
								'principal_savings_amount'		=> $data['credits_account_principal'],
								'operated_name'					=> $auth['username'],
								'savings_member_detail_token'	=> $dataApprove['credits_account_token'].'ST',
							);

							$this->CoreMember_model->insertAcctSavingsMemberDetail($data_detail);

							$data_member = array (
								'member_id'								=> $this->input->post('member_id', true),
								'member_principal_savings_last_balance'	=> $this->input->post('member_principal_savings_last_balance', true) + $data['credits_account_principal'],
							);

							$this->CoreMember_model->updateCoreMember($data_member);
							
						}

						if($data['credits_account_insurance'] !='' && $data['credits_account_insurance'] > 0){
							$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();
							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

							$data_debet = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'			   		=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_account_insurance'],
								'journal_voucher_debit_amount'	=> $data['credits_account_insurance'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token' 	=> $dataApprove['credits_account_token'].'INS'.$preferencecompany['account_cash_id'],
								'created_id' 					=> $auth['user_id'],
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);

							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_insurance_cost_id']);

							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_insurance_cost_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_account_insurance'],
								'journal_voucher_credit_amount'	=> $data['credits_account_insurance'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $dataApprove['credits_account_token'].'INS'.$preferencecompany['account_insurance_cost_id'],
								'created_id' 					=> $auth['user_id'],
							);
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
						}

						$auth = $this->session->userdata('auth');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Proses Persetujuan Berhasil
								</div> ";
						$sesi = $this->session->userdata('unique');
						
						$this->session->unset_userdata('addacctcreditaccount-'.$sesi['unique']);
						$this->session->unset_userdata('addcreditaccount-'.$sesi['unique']);
						$this->session->unset_userdata('acctcreditsaccounttoken-'.$sesi['unique']);
						$this->session->set_userdata('message',$msg);
						$url='credit-account';
						redirect($url);
					}else{
						$this->session->set_userdata('addacctdepositoaccount',$data);
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>				
									Proses Persetujuan Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message',$msg);
						$url='credit-account';
						redirect($url);
					}
				}else{
					$acctcreditsaccount_last = $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($dataApprove['credits_account_id']);	
					$auth = $this->session->userdata('auth');

					$data_journal = array(
						'branch_id'						=> $auth['branch_id'],
						'journal_voucher_period' 		=> $journal_voucher_period,
						'journal_voucher_date'			=> date('Y-m-d'),
						'journal_voucher_title'			=> 'PEMBIAYAAN '.$acctcreditsaccount_last['credits_name'].' '.$acctcreditsaccount_last['member_name'],
						'journal_voucher_description'	=> 'PEMBIAYAAN '.$acctcreditsaccount_last['credits_name'].' '.$acctcreditsaccount_last['member_name'],
						'journal_voucher_token'			=> $dataApprove['credits_account_token'],
						'transaction_module_id'			=> $transaction_module_id,
						'transaction_module_code'		=> $transaction_module_code,
						'transaction_journal_id' 		=> $acctcreditsaccount_last['credits_account_id'],
						'transaction_journal_no' 		=> $acctcreditsaccount_last['credits_account_serial'],
						'created_id'					=> $auth['user_id'],								
						'created_on' 					=> date('Y-m-d H:i:s'),
					);

					$journal_voucher_id 				= $this->AcctCreditAccount_model->getJournalVoucherID($data_journal['created_id']);

					$receivable_account_id				= $this->AcctCreditAccount_model->getReceivableAccountID($data['credits_id']);

					$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($receivable_account_id);


					$data_debet = array (
						'journal_voucher_id'			=> $journal_voucher_id,
						'account_id'					=> $receivable_account_id,
						'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
						'journal_voucher_amount'		=> $data['credits_account_amount'],
						'journal_voucher_debit_amount'	=> $data['credits_account_amount'],
						'account_id_default_status'		=> $account_id_default_status,
						'account_id_status'				=> 0,
						'journal_voucher_item_token' 	=> $data_journal['journal_voucher_token'].$receivable_account_id,
						'created_id' 					=> $auth['user_id'],
					);

					
					
					$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

					if($journal_voucher_item_token->num_rows()==0){
						$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);
					}

					
					$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();

					$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

					$data_credit = array (
						'journal_voucher_id'			=> $journal_voucher_id,
						'account_id'					=> $preferencecompany['account_cash_id'],
						'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
						'journal_voucher_amount'		=> $data['credits_account_amount_received'],
						'journal_voucher_credit_amount'	=> $data['credits_account_amount_received'],
						'account_id_default_status'		=> $account_id_default_status,
						'account_id_status'				=> 1,
						'journal_voucher_item_token'	=> $data_journal['journal_voucher_token'].$preferencecompany['account_cash_id'],
						'created_id' 					=> $auth['user_id'],
					);

					
					$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

					if($journal_voucher_item_token->num_rows()==0){
						$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
					}			

					if($provisi !=''  && $provisi>0){
						$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();
						$preferenceinventory 				= $this->AcctCreditAccount_model->getPreferenceInventory();	
						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

						$data_debet = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_cash_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $provisi,
							'journal_voucher_debit_amount'	=> $provisi,
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token' 	=> $dataApprove['credits_account_token'].'PR'.$preferencecompany['account_cash_id'],
							'created_id' 					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);
						}

						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferenceinventory['inventory_stamp_duty_id']);

						$data_credit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferenceinventory['inventory_stamp_duty_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $provisi,
							'journal_voucher_credit_amount'	=> $provisi,
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $dataApprove['credits_account_token'].'PR'.$preferenceinventory['inventory_stamp_duty_id'],
							'created_id' 					=> $auth['user_id'],
						);  
						
						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
						}
					
					}	

					if($komisi !=''  && $komisi>0){
						$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();
						$preferenceinventory 				= $this->AcctCreditAccount_model->getPreferenceInventory();	
						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

						$data_debet = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_cash_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $komisi,
							'journal_voucher_debit_amount'	=> $komisi,
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token' 	=> $dataApprove['credits_account_token'].'KM'.$preferencecompany['account_cash_id'],
							'created_id' 					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);
						}

						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_commission_id']);

						$data_credit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_commission_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $komisi,
							'journal_voucher_credit_amount'	=> $komisi,
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $dataApprove['credits_account_token'].'KM'.$preferencecompany['account_commission_id'],
							'created_id' 					=> $auth['user_id'],
						);  
						
						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
						}
					
					}

					if($data['credits_account_adm_cost'] != '' &&  $data['credits_account_adm_cost'] > 0){
						$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();
						$preferenceinventory 					= $this->AcctCreditAccount_model->getPreferenceInventory();			


						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

						$data_debit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_cash_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_account_adm_cost'],
							'journal_voucher_debit_amount'	=> $data['credits_account_adm_cost'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token'	=> $data_journal['journal_voucher_token'].'ADM'.$preferencecompany['account_cash_id'],
							'created_id' 					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debit);
						}

						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferenceinventory['inventory_adm_id']);

						$data_credit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferenceinventory['inventory_adm_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_account_adm_cost'],
							'journal_voucher_credit_amount'	=> $data['credits_account_adm_cost'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $data_journal['journal_voucher_token'].'ADM'.$preferenceinventory['inventory_adm_id'],
							'created_id' 					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
						}
						
					}

					if($data['credits_account_materai'] != '' && $data['credits_account_materai'] >0 ){
						$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();

						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

						$data_debit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_cash_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_account_materai'],
							'journal_voucher_debit_amount'	=> $data['credits_account_materai'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token'	=> $data_journal['journal_voucher_token'].'MT'.$preferencecompany['account_cash_id'],
							'created_id' 					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debit);
						}

						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_materai_id']);

						$data_credit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_materai_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_account_materai'],
							'journal_voucher_credit_amount'	=> $data['credits_account_materai'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $data_journal['journal_voucher_token'].'MT'.$preferencecompany['account_materai_id'],
							'created_id' 					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
						}
						
					}

					if($data['credits_account_risk_reserve'] != '' && $data['credits_account_risk_reserve'] >0 ){
						$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();

						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

						$data_debit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_cash_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_account_risk_reserve'],
							'journal_voucher_debit_amount'	=> $data['credits_account_risk_reserve'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token'	=> $data_journal['journal_voucher_token'].'RR'.$preferencecompany['account_cash_id'],
							'created_id' 					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debit);
						}

						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_risk_reserve_id']);

						$data_credit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_risk_reserve_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_account_risk_reserve'],
							'journal_voucher_credit_amount'	=> $data['credits_account_risk_reserve'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $data_journal['journal_voucher_token'].'RR'.$preferencecompany['account_risk_reserve_id'],
							'created_id' 					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
						}
						
					}

					if($data['credits_account_stash'] != '' && $data['credits_account_stash'] >0 ){
						$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();

						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

						$data_debit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_cash_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_account_stash'],
							'journal_voucher_debit_amount'	=> $data['credits_account_stash'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token'	=> $data_journal['journal_voucher_token'].'ST'.$preferencecompany['account_cash_id'],
							'created_id' 					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debit);
						}

						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_stash_id']);

						$data_credit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_stash_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_account_stash'],
							'journal_voucher_credit_amount'	=> $data['credits_account_stash'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $data_journal['journal_voucher_token'].'ST'.$preferencecompany['account_stash_id'],
							'created_id' 					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
						}
						
					}

					if($data['credits_account_insurance'] != '' && $data['credits_account_insurance'] >0 ){
						$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();

						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

						$data_debit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_cash_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_account_insurance'],
							'journal_voucher_debit_amount'	=> $data['credits_account_insurance'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token'	=> $data_journal['journal_voucher_token'].'INS'.$preferencecompany['account_cash_id'],
							'created_id' 					=> $auth['user_id'],
						);

						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debit);
						}

						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_insurance_cost_id']);

						$data_credit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_insurance_cost_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_account_insurance'],
							'journal_voucher_credit_amount'	=> $data['credits_account_insurance'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $data_journal['journal_voucher_token'].'INS'.$preferencecompany['account_insurance_cost_id'],
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
								Proses Persetujuan Berhasil
							</div> ";
					$sesi = $this->session->userdata('unique');

					$this->session->unset_userdata('addarrayacctcreditsagunan-'.$sesi['unique']);
					$this->session->unset_userdata('addacctcreditaccount-'.$sesi['unique']);
					$this->session->unset_userdata('addcreditaccount-'.$sesi['unique']);
					$this->session->unset_userdata('acctcreditsaccounttoken-'.$sesi['unique']);
					$this->session->set_userdata('message',$msg);
					$url='credit-account';
					redirect($url);
				}							
			}else{
				$this->session->set_userdata('addcreditaccount',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('credit-account');				
			}

		}

		public function rejectAcctCreditsAccount(){
			$credits_account_id = $this->uri->segment(3);
			$data = array (
				'credits_account_id'		=> $credits_account_id,
				'credits_approve_status'	=> 9,
			);

			if($this->AcctCreditAccount_model->updateAcctCreditAccount($data)){
				$this->session->set_userdata('addacctdepositoaccount',$data);
				$msg = "<div class='alert alert-success alert-dismissable'>
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Proses Pembatalan Perjanjian Kredit Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				$url='credit-account';
				redirect($url);
			} else {
				$this->session->set_userdata('addacctdepositoaccount',$data);
				$msg = "<div class='alert alert-danger alert-dismissable'>
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Proses Pembatalan Perjanjian Kredit Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				$url='credit-account';
				redirect($url);
			}
		}

		public function showdetaildata(){
			$auth 					= $this->session->userdata('auth'); 
			$credits_account_id 	= $this->uri->segment(3);
			$type 					= $this->uri->segment(4);
			if($type== '' && $type==1){
				$datapola 			= $this->flat($credits_account_id);
			} else if($type == 2){
				$datapola 			= $this->anuitas($credits_account_id);
			} else{
				$datapola 			= $this->slidingrate($credits_account_id);
			}

			$detaildata 			= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);

			$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();
			$data['main_view']['membergender']				= $this->configuration->MemberGender();
			$data['main_view']['acctcreditsaccount']		= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);
			$data['main_view']['acctcreditsagunan']			= $this->AcctCreditAccount_model->getAcctCreditsAgunan_Detail($credits_account_id);
			$data['main_view']['coreoffice']				= create_double($this->AcctCreditAccount_model->getCoreOffice(),'office_id', 'office_name');
			$data['main_view']['sumberdana']				= create_double($this->Core_source_fund_model->getData(),'source_fund_id', 'source_fund_name');
			$data['main_view']['coremember']				= $this->CoreMember_model->getCoreMember_Detail($detaildata['member_id']);
			$data['main_view']['acctsavingsaccount']		= create_double($this->AcctDepositoAccount_model->getAcctSavingsAccount($auth['branch_id']),'savings_account_id', 'savings_account_no');
			$data['main_view']['creditid']					= create_double($this->AcctCredit_model->getData(),'credits_id', 'credits_name');
			$data['main_view']['creditaccount']				= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($this->uri->segment(3));
			$data['main_view']['datapola']					= $datapola;
			$data['main_view']['paymenttype']				= $this->configuration->PaymentType();
			$data['main_view']['paymentpreference']			= $this->configuration->PaymentPreference();

			$data['main_view']['content']					= 'AcctCreditAccount/FormSaveSuccessAcctCreditAccount_view';
			$this->load->view('MainPage_view',$data);
		}

		public function printNoteAcctCreditAccount(){
			$auth = $this->session->userdata('auth');
			$credits_account_id 	= $this->uri->segment(3);
			$preferencecompany 		= $this->AcctCreditAccount_model->getPreferenceCompany();
			$acctcreditsaccount	 	= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);

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
			$pdf->SetFont('helvetica', '', 12);

			// -----------------------------------------------------------------------------
			$base_url = base_url();
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preferencecompany['logo_koperasi']."\" alt=\"\" width=\"700%\" height=\"300%\"/>";

			$tbl = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
			    <tr>
			    	<td rowspan=\"2\" width=\"20%\">".$img."</td>
			        <td width=\"50%\"><div style=\"text-align: left; font-size:14px\">BUKTI PENCAIRAN PEMBIAYAAN</div></td>
			    </tr>
			    <tr>
			        <td width=\"40%\"><div style=\"text-align: left; font-size:14px\">Jam : ".date('H:i:s')."</div></td>
			    </tr>
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl1 = "
			Telah dibayarkan kepada :
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Nama</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$acctcreditsaccount['member_name']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">No. Akad</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$acctcreditsaccount['credits_account_serial']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Alamat</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$acctcreditsaccount['member_address']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Terbilang</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".numtotxt($acctcreditsaccount['credits_account_amount'])."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Keperluan</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: PENCAIRAN PEMBIAYAAN</div></td>
			    </tr>
			     <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Jumlah</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: Rp. &nbsp;".number_format($acctcreditsaccount['credits_account_amount'], 2)."</div></td>
			    </tr>				
			</table>";

			$tbl2 = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			    	<td width=\"30%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"20%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"30%\"><div style=\"text-align: center;\">".$this->AcctCreditAccount_model->getBranchCity($auth['branch_id']).", ".date('d-m-Y')."</div></td>
			    </tr>
			    <tr>
			        <td width=\"30%\"><div style=\"text-align: center;\">Penerima</div></td>
			        <td width=\"20%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"30%\"><div style=\"text-align: center;\">Teller/Kasir</div></td>
			    </tr>				
			</table>";

			$pdf->writeHTML($tbl1.$tbl2, true, false, false, false, '');

			ob_clean();

			// -----------------------------------------------------------------------------
			
			$filename = 'Kwitansi.pdf';
			$pdf->Output($filename, 'I');
		}

		public function AcctCreditAccountBook(){
			$auth = $this->session->userdata('auth');

			$data['main_view']['acctcredits']	= create_double($this->AcctCreditAccount_model->getAcctCredits(),'credits_id', 'credits_name');
			$data['main_view']['corebranch']	= create_double($this->AcctCreditAccount_model->getCoreBranch(),'branch_id', 'branch_name');
			$data['main_view']['content']		= 'AcctCreditAccount/ListBookAcctCreditsAccount_view';
			$this->load->view('MainPage_view', $data);
		}

		public function filteracctcreditsaccountbook(){
			$data = array (
				'start_date'	=> tgltodb($this->input->post('start_date', true)),
				'end_date'		=> tgltodb($this->input->post('end_date', true)),
				'credits_id'	=> $this->input->post('credits_id', true),
				'branch_id'		=> $this->input->post('branch_id', true),
			);

			$this->session->set_userdata('filter-acctcreditsaccountbooklist', $data);
			redirect('credit-account/book');
		}

		public function getAcctCreditsAccountBookList(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-acctcreditsaccountbooklist');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['credits_id']		='';
				if($auth['branch_status'] == 1){
					$sesi['branch_id']	= '';
				}
				if($auth['branch_status'] == 0){
					$sesi['branch_id']	= $auth['branch_id'];
				}
			} else {
				if($auth['branch_status'] == 1){
					$sesi['branch_id']	= '';
				}
				if($auth['branch_status'] == 0){
					$sesi['branch_id']	= $auth['branch_id'];
				}
			}
			
			$creditsapprovestatus = $this->configuration->CreditsApproveStatus();

			$list 	= $this->AcctCreditAccount_model->get_datatables_master($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']);
	        $data 	= array();
	        $no 	= $_POST['start'];
	        foreach ($list as $creditsaccount) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $creditsaccount->credits_account_serial;
	            $row[] = $creditsaccount->member_name;
	            $row[] = $creditsaccount->credits_name;
	            $row[] = $creditsaccount->source_fund_name;
	            $row[] = tgltoview($creditsaccount->credits_account_date);
	            $row[] = number_format($creditsaccount->credits_account_amount, 2);
	            $row[] = $creditsapprovestatus[$creditsaccount->credits_approve_status];
	     
	            if ($creditsaccount->credits_approve_status == 1){
			    	$row[] = '<a href="'.base_url().'credit-account/print-book//'.$creditsaccount->credits_account_id.'" class="btn btn-xs blue" role="button"><i class="fa fa-print"></i> Cetak Cover</a>';
	            }else{
	            	$row[] ='';
	            }
	            $data[] = $row;
	        }

	        $output = array(
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->AcctCreditAccount_model->count_all_master($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']),
				"recordsFiltered" => $this->AcctCreditAccount_model->count_filtered_master($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']),
				"data" => $data,
			);
	        echo json_encode($output);
		}

		public function printBookAcctCreditAccount(){
			$auth 					= $this->session->userdata('auth');
			$credits_account_id 	= $this->uri->segment(3);
			$acctcreditsaccount	 	= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);
			$preferencecompany 		= $this->AcctCreditAccount_model->getPreferenceCompany();

			$credits_account_payment_date = date('Y-m-d', strtotime("+1 months", strtotime($acctcreditsaccount['credits_account_date'])));

			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');

			$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

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

			$resolution	= array(200, 200);
			$page 		= $pdf->AddPage('P', $resolution);

			$pdf->SetFont('helvetica', '', 8);

			// -----------------------------------------------------------------------------
			$base_url = base_url();
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preferencecompany['logo_koperasi']."\" alt=\"\" width=\"700%\" height=\"300%\"/>";
			$tbl1 .= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
			<tr>
				<td rowspan=\"2\" width=\"10%\">" .$img."</td>
					</tr>
					<tr>
					</tr>
				</table>
				<br/>
				<br/>
				<br/>
				<br/>";

			$tbl1 .= "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">NOMOR KONTRAK</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$acctcreditsaccount['credits_account_serial']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">JUMLAH PEMBIAYAAN</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".number_format($acctcreditsaccount['credits_account_amount'], 2)."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">TENOR</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$acctcreditsaccount['credits_account_period']." Bulan</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">ANGSURAN</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".number_format($acctcreditsaccount['credits_account_payment_amount'], 2)."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">TGL AKTIVASI</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".tgltoview($acctcreditsaccount['credits_account_date'])."</div></td>
			    </tr>
			     <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">JATUH TEMPO PERTAMA</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".tgltoview($credits_account_payment_date)."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">JATUH TEMPO TERAKHIR</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".tgltoview($acctcreditsaccount['credits_account_due_date'])."</div></td>
			    </tr>			
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">CABANG PENGAJUAN</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$acctcreditsaccount['branch_name']."</div></td>
			    </tr>	
			</table>";

			$pdf->writeHTML($tbl1, true, false, false, false, '');

			ob_clean();

			// -----------------------------------------------------------------------------
			
			$filename = 'Kwitansi.pdf';
			$pdf->Output($filename, 'I');
		}

		public function detailAcctCreditsAccount(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-AcctCreditsAccount');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['branch_id']		= '';
				$sesi['credits_id']		= '';
			}

			$start_date = tgltodb($sesi['start_date']);
			$end_date 	= tgltodb($sesi['end_date']);

			$data['main_view']['corebranch']				= create_double($this->AcctCreditAccount_model->getCoreBranch(), 'branch_id', 'branch_name');
			$data['main_view']['acctcredits']				= create_double($this->AcctCreditAccount_model->getAcctCredits(), 'credits_id', 'credits_name');

			$data['main_view']['content']					= 'AcctCreditAccount/ListDetailAcctCreditsAccount_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filterdetail(){
			$data = array (
				'start_date'			=> tgltodb($this->input->post('start_date',true)),
				'end_date'				=> tgltodb($this->input->post('end_date',true)),
				'branch_id'				=> $this->input->post('branch_id',true),
				'credits_id'			=> $this->input->post('credits_id',true),
			);
			$this->session->set_userdata('filter-AcctCreditsAccount', $data);
			redirect('credit-account/detail');
		}

		public function getAcctCreditsAccountDetailList(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-AcctCreditsAccount');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['credits_id']		='';
				if($auth['branch_status'] == 1){
					$sesi['branch_id']	= '';
				}
				if($auth['branch_status'] == 0){
					$sesi['branch_id']	= $auth['branch_id'];
				}
			} else {
				if($auth['branch_status'] == 1){
					$sesi['branch_id']	= '';
				}
				if($auth['branch_status'] == 0){
					$sesi['branch_id']	= $auth['branch_id'];
				}
			}
			$creditsapprovestatus = $this->configuration->CreditsApproveStatus();

			$list = $this->AcctCreditAccount_model->get_datatables_master($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']);

	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $creditsaccount) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $creditsaccount->credits_account_serial;
	            $row[] = $creditsaccount->member_name;
	            $row[] = $creditsaccount->credits_name;
	            $row[] = $creditsaccount->source_fund_name;
	            $row[] = tgltoview($creditsaccount->credits_account_date);
	            $row[] = number_format($creditsaccount->credits_account_amount, 2);
	            $row[] = $creditsapprovestatus[$creditsaccount->credits_approve_status];

	    		if($creditsaccount->credits_approve_status == 1){
			   		$row[] = '
			    		<a href="'.base_url().'credit-account/show-detail/'.$creditsaccount->credits_account_id.'" class="btn btn-xs yellow-lemon" role="button"><i class="fa fa-bars"></i> Detail</a>
			    		
			    		<a href="'.base_url().'credit-account/print-pola-angsuran-credits/'.$creditsaccount->credits_account_id.'" class="btn btn-xs blue" role="button"><i class="fa fa-print"></i>Pola Angsuran</a>
			    		';
			    }else{
			    	$row[]='';
			    }
	            $data[] = $row;
	        }
	 
	        $output = array(
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->AcctCreditAccount_model->count_all_master($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']),
				"recordsFiltered" => $this->AcctCreditAccount_model->count_filtered_master($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']),
				"data" => $data,
			);
	        echo json_encode($output);
		}
		
		public function reset_search(){
			$sesi= $this->session->userdata('filter-AcctCreditsAccount');
			$this->session->unset_userdata('filter-AcctCreditsAccount');
			redirect('credit-account/detail');
		}

		public function showdetail(){
			$credits_account_id 	= $this->uri->segment(3);

			$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();
			$data['main_view']['paymenttype']				= $this->configuration->PaymentType();

			$data['main_view']['acctcreditsaccount']		= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);
			$data['main_view']['acctcreditspayment']		= $this->AcctCreditAccount_model->getAcctCreditsPayment_Detail($credits_account_id);
			$data['main_view']['acctcreditsacquittance']	= $this->AcctCreditAccount_model->getAcctCreditsAcquittance($credits_account_id);

			$data['main_view']['content']					= 'AcctCreditAccount/FormDetailAcctCreditsAccount_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processPrinting(){
			$credits_account_id			= $this->input->post('credits_account_id',true);
			$memberidentity				= $this->configuration->MemberIdentity();
			$acctcreditsaccount			= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);
			$acctcreditspayment			= $this->AcctCreditAccount_model->getAcctCreditsPayment_Detail($credits_account_id);
			$acctcreditsacquittance		= $this->AcctCreditAccount_model->getAcctCreditsAcquittance($credits_account_id);
			$preferencecompany 			= $this->AcctCreditAccount_model->getPreferenceCompany();

			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			
			$pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);
			$pdf->SetMargins(10, 10, 10, 10); 
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

			$tblheader = "
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
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:14px\";><b>HISTORI ANGSURAN PINJAMAN</b></div>
						</td>			
	 				</tr>
	 			</table>
	 			<br><br>
			";
				
			$pdf->writeHTML($tblheader, true, false, false, false, '');

			$tblmember = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
					<tr>
	 					<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Nama</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"80%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['member_name']."</b></div>
						</td>
									
	 				</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>No. Perjanjian Kredit</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"30%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['credits_account_serial']."</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px;font-weight:bold\">
								Jangka Waktu
							</div>
						</td>
						
						<td style=\"text-align:left; \" width=\"30%\">
							<div style=\"font-size:12px;font-weight:bold\">
								: ".$acctcreditsaccount['credits_account_period']."
							</div>
						</td>			
	 				</tr>
	 				<tr>
	 					<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px;font-weight:bold\">
								Tanggal Realisasi
							</div>
						</td>

						<td style=\"text-align:left; \" width=\"30%\">
							<div style=\"font-size:12px;font-weight:bold\">
								: ".tgltoview($acctcreditsaccount['credits_account_date'])."
							</div>
	 					</td>
	 					<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px;font-weight:bold\">
								Pinjaman
							</div>
						</td>
						<td style=\"text-align:left; \" width=\"30%\">
							<div style=\"font-size:12px;font-weight:bold\">
								: ".nominal($acctcreditsaccount['credits_account_amount'])."
							</div>
	 					</td>
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px;font-weight:bold\">
								Alamat
							</div>
						</td>

						<td style=\"text-align:left; \" width=\"83%\">
							<div style=\"font-size:12px;font-weight:bold\">
								: ".$acctcreditsaccount['member_address']."
							</div>
	 					</td>
	 				</tr>
	 			</table>
	 			<br><br>
			";

			$pdf->writeHTML($tblmember, true, false, false, false, '');

			$tblpaymentheader = "
				<table id=\"items\" width=\"100%\" cellpadding=\"3\" cellspacing=\"0\" border=\"1\">
					<tr>
						<td style=\"text-align:center;\" width=\"5%\">
							<div style=\"font-size:10px\">
								<b>No</b>
							</div>
						</td>
					
						<td style=\"text-align:center;\" width=\"10%\">
							<div style=\"font-size:10px\">
								<b>Tanggal Angsuran</b>
							</div>
						</td>
					
						<td style=\"text-align:center;\" width=\"15%\">
							<div style=\"font-size:10px\">
								<b>Angsuran Pokok</b>
							</div>
						</td>

						<td style=\"text-align:center;\" width=\"15%\">
							<div style=\"font-size:10px\">
								<b>Angsuran Bunga</b>
							</div>
						</td>

						<td style=\"text-align:center;\" width=\"15%\">
							<div style=\"font-size:10px\">
								<b>Saldo Pokok</b>
							</div>
						</td>

						<td style=\"text-align:center;\" width=\"15%\">
							<div style=\"font-size:10px\">
								<b>Saldo Bunga</b>
							</div>
						</td>

						<td style=\"text-align:center;\" width=\"10%\">
							<div style=\"font-size:10px\">
								<b>Sanksi Dibayarkan</b>
							</div>
						</td>
						<td style=\"text-align:center;\" width=\"15%\">
							<div style=\"font-size:10px\">
								<b>Akumulasi Sanksi</b>
							</div>
						</td>
					</tr>";

			$tblpaymentlist = "";
			$no = 1;
			foreach($acctcreditspayment as $key=>$val){
				$tblpaymentlist .= "
					<tr>
						<td style=\"text-align:center;\" width=\"5%\">
							<div style=\"font-size:10px\">
								".$no."
							</div>
						</td>

						<td style=\"text-align:left;\" width=\"10%\">
							<div style=\"font-size:10px\">
								".tgltoview($val['credits_payment_date'])."
							</div>
						</td>
					
						<td style=\"text-align:right;\" width=\"15%\">
							<div style=\"font-size:10px\">
								".nominal($val['credits_payment_principal'])."
							</div>
						</td>
					
						<td style=\"text-align:right;\" width=\"15%\">
							<div style=\"font-size:10px\">
								".nominal($val['credits_payment_interest'])."
							</div>
						</td>

						<td style=\"text-align:right;\" width=\"15%\">
							<div style=\"font-size:10px\">
								".nominal($val['credits_principal_last_balance'])."
							</div>
						</td>

						<td style=\"text-align:right;\" width=\"15%\">
							<div style=\"font-size:10px\">
								".nominal($val['credits_interest_last_balance'])."
							</div>
						</td>

						<td style=\"text-align:right;\" width=\"10%\">
							<div style=\"font-size:10px\">
								".nominal($val['credits_payment_fine'])."
							</div>
						</td>
						<td style=\"text-align:right;\" width=\"15%\">
							<div style=\"font-size:10px\">
								".nominal($val['credits_payment_fine_last_balance'])."
							</div>
						</td>
					</tr>";
				$no++;
			}
			$tblacquittance = "";
			if($acctcreditsacquittance){
				$tblacquittance .= "
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:10px\">
								<b>Pelunasan</b>
							</div>
						</td>
					</tr>
					<tr>
						<td style=\"text-align:center;\" width=\"15%\">
							<div style=\"font-size:10px\">
								<b>Tanggal Pelunasan</b>
							</div>
						</td>
					
						<td style=\"text-align:center;\" width=\"15%\">
							<div style=\"font-size:10px\">
								<b>Pelunasan Pokok</b>
							</div>
						</td>

						<td style=\"text-align:center;\" width=\"15%\">
							<div style=\"font-size:10px\">
								<b>Pelunasan Bunga</b>
							</div>
						</td>

						<td style=\"text-align:center;\" width=\"15%\">
							<div style=\"font-size:10px\">
								<b>Pelunasan Sanksi</b>
							</div>
						</td>

						<td style=\"text-align:center;\" width=\"15%\">
							<div style=\"font-size:10px\">
								<b>Pinalti</b>
							</div>
						</td>

						<td style=\"text-align:center;\" width=\"25%\">
							<div style=\"font-size:10px\">
								<b>Jumlah Pelunasan</b>
							</div>
						</td>
					</tr>";
				foreach($acctcreditsacquittance as $key => $val){
					$tblacquittance .= "
						<tr>
							<td style=\"text-align:left;\" width=\"15%\">
								<div style=\"font-size:10px\">
									".tgltoview($val['credits_acquittance_date'])."
								</div>
							</td>
						
							<td style=\"text-align:right;\" width=\"15%\">
								<div style=\"font-size:10px\">
									".nominal($val['credits_acquittance_principal'])."
								</div>
							</td>
						
							<td style=\"text-align:right;\" width=\"15%\">
								<div style=\"font-size:10px\">
									".nominal($val['credits_acquittance_interest'])."
								</div>
							</td>
	
							<td style=\"text-align:right;\" width=\"15%\">
								<div style=\"font-size:10px\">
									".nominal($val['credits_acquittance_fine'])."
								</div>
							</td>
	
							<td style=\"text-align:right;\" width=\"15%\">
								<div style=\"font-size:10px\">
									".nominal($val['credits_acquittance_penalty_amount'])."
								</div>
							</td>
	
							<td style=\"text-align:right;\" width=\"25%\">
								<div style=\"font-size:10px\">
									".nominal($val['credits_acquittance_amount'])."
								</div>
							</td>
						</tr>";
				}
			}

			$tblpaymentfooter = "
				</table>
			";

			$pdf->writeHTML($tblpaymentheader.$tblpaymentlist.$tblacquittance.$tblpaymentfooter, true, false, false, false, '');

			ob_clean();

			$filename = 'Histori_Angsuran_Pinjaman_'.$acctcreditsaccount['credits_account_serial'].'.pdf';
			$pdf->Output($filename, 'I');
		}

		public function creditlist(){
			$data['main_view']['content']	= 'AcctCreditAccount/Creditlist_view';
			$this->load->view('MainPage_view',$data);
		}

		public function creditajax(){
			$list = $this->AcctCreditAccount_model->get_datatables();
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $customers) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $customers->credits_account_serial;
	            $row[] = $customers->member_name;
	            $row[] = $customers->member_no;
	            $row[] = $customers->credits_account_date;
	            $row[] = $customers->credits_account_due_date;
	            $row[] = $customers->credits_account_period;
	            $row[] = $customers->credits_account_net_price;
	            $row[] = $customers->credits_account_sell_price;
	            $row[] = $customers->credits_account_margin;
	            $data[] = $row;
	        }
	 
	        $output = array(
				"draw" 				=> $_POST['draw'],
				"recordsTotal" 		=> $this->CoreMember_model->count_all(),
				"recordsFiltered" 	=> $this->CoreMember_model->count_filtered(),
				"data" 				=> $data,
			);
	        echo json_encode($output);
		}

		public function agunanadd(){
			$data 	= $this->session->userdata('agunan_data');
			$agunan = $this->session->userdata('agunan_key');
			if(!isset($agunan)){
				$agunan=1;
			}
			$new_key = $agunan+1;
			if($this->uri->segment(3)=="save"){
				$type = $this->input->post('tipe',true);
				if($type == 'Sertifikat'){
					$data[$new_key]=array (
						"shm_no_sertifikat"	=> $this->input->post('shm_no_sertifikat',true),
						"shm_luas"			=> $this->input->post('shm_luas',true),
						"shm_atas_nama"		=> $this->input->post('shm_atas_nama',true),
						"shm_kedudukan"		=> $this->input->post('shm_kedudukan',true),
						"shm_taksiran"		=> $this->input->post('shm_taksiran',true),
						"tipe"				=> $this->input->post('tipe',true),
						"shm_keterangan"	=> $this->input->post('shm_keterangan',true),
					);
				}else{
					$data[$new_key]=array (
						"bpkb_nomor"		=> $this->input->post('bpkb_nomor',true),
						"bpkb_nama"			=> $this->input->post('bpkb_nama',true),
						"bpkb_nopol"		=> $this->input->post('bpkb_nopol',true),
						"bpkb_no_mesin"		=> $this->input->post('bpkb_no_mesin',true),
						"bpkb_no_rangka"	=> $this->input->post('bpkb_no_rangka',true),
						"taksiran"			=> $this->input->post('taksiran',true),
						"tipe"				=> $this->input->post('tipe',true),
						"bpkb_keterangan"	=> $this->input->post('bpkb_keterangan',true),
					);
				}
				$this->session->set_userdata('agunan_data',$data);
				$this->session->set_userdata('agunan_key',$new_key);
			}
			$kirim['data'] = $data;
			
			$this->load->view('AcctCreditAccount/FormAddAcctCreditAgunan',$kirim);
		}
		
		public function agunanview(){
			$credits_account_id 	= $this->uri->segment(3);
			$detaildata=$this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);
			$this->load->view('AcctCreditAccount/FormShowCreditAgunan',$detaildata);
		}
		
		public function polaangsuran(){
			$id=$this->uri->segment(3);
			$type=$this->uri->segment(4);
			if($type == '' && $type == 0){
				$datapola=$this->flat($id);
			}else{
				$datapola=$this->slidingrate($id);
			}
			$data['main_view']['creditaccount']		= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($this->uri->segment(3));
			$data['main_view']['datapola']			= $datapola;
			$data['main_view']['content']			= 'AcctCreditAccount/FormPolaAngsuran_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function angsuran(){
			$id=$this->uri->segment(3);
			$type=$this->uri->segment(4);
			if($type== '' && $type==0){
				$datapola=$this->flat($id);
			}else{
				$datapola=$this->slidingrate($id);
			}
			
			$creditaccount		= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($this->uri->segment(3));
			redirect('credit-account/show-detail-data/'.$id.'/'.$type,compact('datapola'));
		}
		
		public function cekPolaAngsuran(){
			$id=$this->input->post('id_credit',true);
			$pola=$this->input->post('pola_angsuran',true);
			$url='credit-account/angsuran/'.$id.'/'.$pola;
			redirect($url);
		}
		
		public const EPSILON = 1e-6;

		private static function checkZero(float $value, float $epsilon = self::EPSILON): float
		{
			return \abs($value) < $epsilon ? 0.0 : $value;
		}
		
		public static function fv(float $rate, int $periods, float $payment, float $present_value, bool $beginning = false): float
		{
			$when = $beginning ? 1 : 0;
	
			if ($rate == 0) {
				$fv = -($present_value + ($payment * $periods));
				return self::checkZero($fv);
			}
	
			$initial  = 1 + ($rate * $when);
			$compound = \pow(1 + $rate, $periods);
			$fv       = - (($present_value * $compound) + (($payment * $initial * ($compound - 1)) / $rate));
	
			return self::checkZero($fv);
		}
		
		public static function pmt(float $rate, int $periods, float $present_value, float $future_value = 0.0, bool $beginning = false): float
		{
			$when = $beginning ? 1 : 0;
	
			if ($rate == 0) {
				return - ($future_value + $present_value) / $periods;
			}
	
			return - ($future_value + ($present_value * \pow(1 + $rate, $periods)))
				/
				((1 + $rate * $when) / $rate * (\pow(1 + $rate, $periods) - 1));
		}
		
		public static function ipmt(float $rate, int $period, int $periods, float $present_value, float $future_value = 0.0, bool $beginning = false): float
		{
			if ($period < 1 || $period > $periods) {
				return \NAN;
			}
	
			if ($rate == 0) {
				return 0;
			}
	
			if ($beginning && $period == 1) {
				return 0.0;
			}
	
			$payment = self::pmt($rate, $periods, $present_value, $future_value, $beginning);
			if ($beginning) {
				$interest = (self::fv($rate, $period - 2, $payment, $present_value, $beginning) - $payment) * $rate;
			} else {
				$interest = self::fv($rate, $period - 1, $payment, $present_value, $beginning) * $rate;
			}
	
			return self::checkZero($interest);
		}

		
		public static function ppmt(float $rate, int $period, int $periods, float $present_value, float $future_value = 0.0, bool $beginning = false): float
		{
			$payment = self::pmt($rate, $periods, $present_value, $future_value, $beginning);
			$ipmt    = self::ipmt($rate, $period, $periods, $present_value, $future_value, $beginning);
	
			return $payment - $ipmt;
		}

		public function flat($id){
			$credistaccount					= $this->AcctCreditAccount_model->getCreditsAccount_Detail($id);

			$total_credits_account 			= $credistaccount['credits_account_amount'];
			$credits_account_interest 		= $credistaccount['credits_account_interest'];
			$credits_account_period 		= $credistaccount['credits_account_period'];

			$installment_pattern			= array();
			$opening_balance				= $total_credits_account;

			for($i=1; $i<=$credits_account_period; $i++){
				/*$totpokok=$totpokok+$angsuranpokok;
				$sisapokok=$pinjaman-$totpokok;*/
				
				if($credistaccount['credits_payment_period'] == 2){
					$a = $i * 7;

					$tanggal_angsuran 								= date('d-m-Y', strtotime("+".$a." days", strtotime($credistaccount['credits_account_date'])));

				} else {

					$tanggal_angsuran 								= date('d-m-Y', strtotime("+".$i." months", strtotime($credistaccount['credits_account_date'])));
				}
				
				$angsuran_pokok									= $credistaccount['credits_account_principal_amount'];				

				$angsuran_margin								= $credistaccount['credits_account_interest_amount'];				

				$angsuran 										= $angsuran_pokok + $angsuran_margin;

				$last_balance 									= $opening_balance - $angsuran_pokok;

				$installment_pattern[$i]['opening_balance']		= $opening_balance;
				$installment_pattern[$i]['ke'] 					= $i;
				$installment_pattern[$i]['tanggal_angsuran'] 	= $tanggal_angsuran;
				$installment_pattern[$i]['angsuran'] 			= $angsuran;
				$installment_pattern[$i]['angsuran_pokok']		= $angsuran_pokok;
				$installment_pattern[$i]['angsuran_bunga'] 		= $angsuran_margin;
				/*$installment_pattern[$i]['akumulasi_pokok'] 	= $totpokok;*/
				$installment_pattern[$i]['last_balance'] 		= $last_balance;
				
				$opening_balance 								= $last_balance;
			}
			
			return $installment_pattern;
		}

		public function slidingrate($id){
			$credistaccount					= $this->AcctCreditAccount_model->getCreditsAccount_Detail($id);

			$total_credits_account 			= $credistaccount['credits_account_amount'];
			$credits_account_interest 		= $credistaccount['credits_account_interest'];
			$credits_account_period 		= $credistaccount['credits_account_period'];

			$installment_pattern			= array();
			$opening_balance				= $total_credits_account;

			for($i=1; $i<=$credits_account_period; $i++){
				
				if($credistaccount['credits_payment_period'] == 2){
					$a = $i * 7;

					$tanggal_angsuran 								= date('d-m-Y', strtotime("+".$a." days", strtotime($credistaccount['credits_account_date'])));

				} else {

					$tanggal_angsuran 								= date('d-m-Y', strtotime("+".$i." months", strtotime($credistaccount['credits_account_date'])));
				}
				
				$angsuran_pokok									= $credistaccount['credits_account_amount']/$credits_account_period;				

				$angsuran_margin								= $opening_balance*$credits_account_interest/100;				

				$angsuran 										= $angsuran_pokok + $angsuran_margin;

				$last_balance 									= $opening_balance - $angsuran_pokok;

				$installment_pattern[$i]['opening_balance']		= $opening_balance;
				$installment_pattern[$i]['ke'] 					= $i;
				$installment_pattern[$i]['tanggal_angsuran'] 	= $tanggal_angsuran;
				$installment_pattern[$i]['angsuran'] 			= $angsuran;
				$installment_pattern[$i]['angsuran_pokok']		= $angsuran_pokok;
				$installment_pattern[$i]['angsuran_bunga'] 		= $angsuran_margin;
				$installment_pattern[$i]['last_balance'] 		= $last_balance;
				
				$opening_balance 								= $last_balance;
			}
			
			return $installment_pattern;
		}

		public function menurunharian($id){
			$credistaccount					= $this->AcctCreditAccount_model->getCreditsAccount_Detail($id);

			$total_credits_account 			= $credistaccount['credits_account_amount'];
			$credits_account_interest 		= $credistaccount['credits_account_interest'];
			$credits_account_period 		= $credistaccount['credits_account_period'];

			$installment_pattern			= array();
			$opening_balance				= $total_credits_account;
			
			return $installment_pattern;
		}
		
		// public function slidingrate2($id){
		// 	$creditsaccount 	= $this->AcctCreditAccount_model->getCreditsAccount_Detail($id);
			

		// 	/*print_r("detailpinjaman ");
		// 	print_r($detailpinjaman);
		// 	exit;*/
		// 	$credits_account_net_price 		= $creditsaccount['credits_account_net_price'];
		// 	$credits_account_um 			= $creditsaccount['credits_account_um'];
		// 	$credits_account_margin 		= $creditsaccount['credits_account_margin'];
		// 	$credits_account_period 		= $creditsaccount['credits_account_period'];			

		// 	$total_credits_account 			= $credits_account_net_price - $credits_account_um;

		// 	$jangkawaktuth 		= $jangkawaktu/12;
		// 	$percentageth 		= ($margin*100)/$pinjaman;
		// 	$percentagebl 		= round($percentageth/$jangkawaktu,2);
			
		// 	$angsuranpokok 		= round($pinjaman/$jangkawaktuth/12,2);
			
		// 	$pola 				= array();
		// 	$totpinjaman 		= $pinjaman;
		// 	$totpokok 			= 0;
		// 	for($i=1; $i<=$jangkawaktu; $i++){
		// 		if($creditsaccount['credits_payment_period'] == 1){
		// 			$tanggal_angsuran 	= date('d-m-Y', strtotime("+".$i." months", strtotime($creditsaccount['credits_account_date']))); 
		// 		} else {
		// 			$a = $i * 7;

		// 			$tanggal_angsuran 	= date('d-m-Y', strtotime("+".$a." days", strtotime($creditsaccount['credits_account_date']))); 
		// 		}

		// 		$angsuranmargin 				= round(($totpinjaman * $percentageth/100)/$jangkawaktu,2);
		// 		$totangsuran 					= $angsuranpokok + $angsuranmargin;
		// 		$totpokok						= $totpokok + $angsuranpokok;
		// 		$sisapokok 						= $pinjaman - $totpokok;
		// 		$pola[$i]['ke']					= $i;
		// 		$pola[$i]['angsuran']			= $totangsuran;
		// 		$pola[$i]['tanggal_angsuran']	= $tanggal_angsuran;
		// 		$pola[$i]['angsuran_pokok']		= $angsuranpokok;
		// 		$pola[$i]['angsuran_margin']	= $angsuranmargin;
		// 		$pola[$i]['akumulasi_pokok']	= $totpokok;
		// 		$pola[$i]['sisa_pokok']			= $sisapokok;
		// 		$totpinjaman					= $totpinjaman - $angsuranpokok;
		// 	}
			
		// 	return $pola;
			
		// }
		
		public function rate1($nper, $pmt, $pv, $fv = 0.0, $type = 0, $guess = 0.1) {
			$rate = $guess;
			if (abs($rate) < FINANCIAL_PRECISION) {
				$y = $pv * (1 + $nper * $rate) + $pmt * (1 + $rate * $type) * $nper + $fv;
			} else {
				$f = exp($nper * log(1 + $rate));
				$y = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;
			}
			$y0 = $pv + $pmt * $nper + $fv;
			$y1 = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;
			$i = $x0 = 0.0;
			$x1 = $rate;
			while ((abs($y0 - $y1) > FINANCIAL_PRECISION) && ($i < FINANCIAL_MAX_ITERATIONS)) {
				$rate = ($y1 * $x0 - $y0 * $x1) / ($y1 - $y0);
				$x0 = $x1;
				$x1 = $rate;
				if (abs($rate) < FINANCIAL_PRECISION) {
					$y = $pv * (1 + $nper * $rate) + $pmt * (1 + $rate * $type) * $nper + $fv;
				} else {
					$f = exp($nper * log(1 + $rate));
					$y = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;
				}
				$y0 = $y1;
				$y1 = $y;
				++$i;
			}
			return $rate;
		}

		public function rate4() {
			$nprest 	= $this->input->post('nprest', true);
			$vlrparc 	= $this->input->post('vlrparc', true);
			$vp 		= $this->input->post('vp', true);
			$guess 		= 0.25;
			$maxit 		= 100;
			$precision 	= 14;
			$check 		= 1;
			$guess 		= round($guess,$precision);
			for ($i=0 ; $i<$maxit ; $i++) {
				$divdnd = $vlrparc - ( $vlrparc * (pow(1 + $guess , -$nprest)) ) - ($vp * $guess);
				$divisor = $nprest * $vlrparc * pow(1 + $guess , (-$nprest - 1)) - $vp;
				$newguess = $guess - ( $divdnd / $divisor );
				$newguess = round($newguess, $precision);
				if ($newguess == $guess) {
					if($check == 1){
					echo $newguess;
					$check++;
					}
				} else {
					$guess = $newguess;
				}
			}
			echo null;
		}

		function rate3($nprest, $vlrparc, $vp, $guess = 0.25) {
			$maxit = 100;
			$precision = 14;
			$guess = round($guess,$precision);
			for ($i=0 ; $i<$maxit ; $i++) {
				$divdnd = $vlrparc - ( $vlrparc * (pow(1 + $guess , -$nprest)) ) - ($vp * $guess);
				$divisor = $nprest * $vlrparc * pow(1 + $guess , (-$nprest - 1)) - $vp;
				$newguess = $guess - ( $divdnd / $divisor );
				$newguess = round($newguess, $precision);
				if ($newguess == $guess) {
					return $newguess;
				} else {
					$guess = $newguess;
				}
			}
			return null;
		}
		
		public function anuitas($id){
			$creditsaccount 	= $this->AcctCreditAccount_model->getCreditsAccount_Detail($id);

			$pinjaman 	= $creditsaccount['credits_account_amount'];
			$bunga 		= $creditsaccount['credits_account_interest'] / 100;
			$period 	= $creditsaccount['credits_account_period'];

			$bungaA 		= pow((1 + $bunga), $period);
			$bungaB 		= pow((1 + $bunga), $period) - 1;
			$bAnuitas 		= ($bungaA / $bungaB);
			// $totangsuran 	= $pinjaman * $bunga * $bAnuitas;
			$totangsuran 	= round(($pinjaman*($bunga))+$pinjaman/$period);
			$rate			= $this->rate3($period, $totangsuran, $pinjaman);

			$sisapinjaman = $pinjaman;
			for ($i=1; $i <= $period ; $i++) {

				if($creditsaccount['credits_payment_period'] == 1){
					$tanggal_angsuran 	= date('d-m-Y', strtotime("+".$i." months", strtotime($creditsaccount['credits_account_date']))); 
				} else {
					$a = $i * 7;

					$tanggal_angsuran 	= date('d-m-Y', strtotime("+".$a." days", strtotime($creditsaccount['credits_account_date']))); 
				}
				
				$angsuranbunga 		= $sisapinjaman * $rate;
				$angsuranpokok 		= $totangsuran - $angsuranbunga;
				// $angsuranpokok		= $pinjaman * (($bunga)/(1-(1+$bunga)-$i));
				$sisapokok 			= $sisapinjaman - $angsuranpokok;

				$pola[$i]['ke']					= $i;
				$pola[$i]['tanggal_angsuran']	= $tanggal_angsuran;
				$pola[$i]['opening_balance']	= $sisapinjaman;
				$pola[$i]['angsuran']			= $totangsuran;
				$pola[$i]['angsuran_pokok']		= $angsuranpokok;
				$pola[$i]['angsuran_bunga']		= $angsuranbunga;
				$pola[$i]['last_balance']		= $sisapokok;

				$sisapinjaman = $sisapinjaman - $angsuranpokok;
			}
			return $pola;
		}
		
		function rate2($nper, $pmt, $pv, $fv = 0.0, $type = 0, $guess = 0.1) {
			$rate = $guess;
			if (abs($rate) < $this->FINANCIAL_PRECISION) {
				$y = $pv * (1 + $nper * $rate) + $pmt * (1 + $rate * $type) * $nper + $fv;
			} else {
				$f = exp($nper * log(1 + $rate));
				$y = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;
			}
			$y0 = $pv + $pmt * $nper + $fv;
			$y1 = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;

			// find root by secant method
			$i  = $x0 = 0.0;
			$x1 = $rate;
			while ((abs($y0 - $y1) > $this->FINANCIAL_PRECISION) && ($i < $this->FINANCIAL_MAX_ITERATIONS)) {
				$rate = ($y1 * $x0 - $y0 * $x1) / ($y1 - $y0);
				$x0 = $x1;
				$x1 = $rate;

				if (abs($rate) < $this->FINANCIAL_PRECISION) {
					$y = $pv * (1 + $nper * $rate) + $pmt * (1 + $rate * $type) * $nper + $fv;
				} else {
					$f = exp($nper * log(1 + $rate));
					$y = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;
				}

				$y0 = $y1;
				$y1 = $y;
				++$i;
			}
			return $rate;
		}  
		
		public function printPolaAngsuran(){
			$credits_account_id 	= $this->input->post('id_credit', true);
			$type					= $this->input->post('pola', true);
			if($type== '' && $type==1){
				$datapola=$this->flat($credits_account_id);
			}else if ($type==2){
				$datapola=$this->anuitas($credits_account_id);
			}else {
				$datapola=$this->slidingrate($credits_account_id);
			}

			$acctcreditsaccount		= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);
			$paymenttype 			= $this->configuration->PaymentType();
			$paymentperiod 			= $this->configuration->CreditsPaymentPeriod();

			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			
			$pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(10, 10, 10, 10); 
			
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

			if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			    require_once(dirname(__FILE__).'/lang/eng.php');
			    $pdf->setLanguageArray($l);
			}

			// ---------------------------------------------------------

			$pdf->SetFont('helvetica', 'B', 20);
			$pdf->AddPage();
			$pdf->SetFont('helvetica', '', 9);

			// -----------------------------------------------------------------------------
			
			$tblheader = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:14px\";><b>Pola Angsuran</b></div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>No. Pinjaman</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"40%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['credits_account_serial']."</b></div>
						</td>	
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Alamat</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"40%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['member_address']."</b></div>
						</td>		
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Nama</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"40%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['member_name']."</b></div>
						</td>	
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Plafon</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"40%\">
							<div style=\"font-size:12px\";><b>: ".number_format($acctcreditsaccount['credits_account_amount'],2)."</b></div>
						</td>		
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Tipe Angsuran</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"40%\">
							<div style=\"font-size:12px\";><b>: ".$paymenttype[$acctcreditsaccount['payment_type_id']]."</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Jangka Waktu</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"40%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['credits_account_period']." ".$paymentperiod[$acctcreditsaccount['credits_payment_period']]."</b></div>
						</td>			
	 				</tr>
	 			</table>
	 			<br><br>
			";
				
			$pdf->writeHTML($tblheader, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
			    <tr>
			        <td width=\"5%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Ke</div></td>
			        <td width=\"12%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Tanggal Angsuran</div></td>
			        <td width=\"18%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Saldo Pokok</div></td>
			        <td width=\"15%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Angsuran Pokok</div></td>
			        <td width=\"15%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Angsuran Bunga</div></td>
			        <td width=\"18%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Total Angsuran</div></td>
			        <td width=\"18%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Sisa Pokok</div></td>
			    </tr>				
			</table>";

			$no = 1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">";
		
			foreach ($datapola as $key => $val) {
				$tbl3 .= "
					<tr>
				    	<td width=\"5%\"><div style=\"text-align: left;\">&nbsp; ".$val['ke']."</div></td>
				    	<td width=\"12%\"><div style=\"text-align: right;\">".tgltoview($val['tanggal_angsuran'], 2)." &nbsp; </div></td>
				        <td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['opening_balance'], 2)." &nbsp; </div></td>
				        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['angsuran_pokok'], 2)." &nbsp; </div></td>
				        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['angsuran_bunga'], 2)." &nbsp; </div></td>
				        <td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['angsuran'], 2)." &nbsp; </div></td>
				        <td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['last_balance'], 2)." &nbsp; </div></td>
				    </tr>
				";

				$no++;
				$totalpokok += $val['angsuran_pokok'];
				$totalmargin += $val['angsuran_bunga'];
				$total += $val['angsuran'];
			}

			$tbl4 = "
				<tr>
					<td colspan=\"3\"><div style=\"text-align: right;font-weight:bold\">Total</div></td>
					<td><div style=\"text-align: right;font-weight:bold\">".number_format($totalpokok, 2)."</div></td>
					<td><div style=\"text-align: right;font-weight:bold\">".number_format($totalmargin, 2)."</div></td>
					<td><div style=\"text-align: right;font-weight:bold\">".number_format($total, 2)."</div></td>
				</tr>							
			</table>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');

			ob_clean();

			$filename = 'Pola_Angsuran_'.$acctcreditsaccount['credits_account_serial'].'.pdf';
			$pdf->Output($filename, 'I');
		}

		public function printScheduleCreditsPayment(){
			$credits_account_id 	= $this->uri->segment(3);

			$acctcreditsaccount		= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);
			$paymenttype 			= $this->configuration->PaymentType();
			$paymentperiod 			= $this->configuration->CreditsPaymentPeriod();
			$preferencecompany 		= $this->AcctCreditAccount_model->getPreferenceCompany();			

			if($acctcreditsaccount['payment_type_id'] == '' || $acctcreditsaccount['payment_type_id'] == 1){
				$datapola=$this->flat($credits_account_id);
			}else if ($acctcreditsaccount['payment_type_id'] == 2){
				$datapola=$this->anuitas($credits_account_id);
			}else if($acctcreditsaccount['payment_type_id'] == 3){
				$datapola=$this->slidingrate($credits_account_id);
			}else if($acctcreditsaccount['payment_type_id'] == 4){
				$datapola=$this->menurunharian($credits_account_id);
			}
			
			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			
			$pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(10, 10, 10, 10); 
			
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

			if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			    require_once(dirname(__FILE__).'/lang/eng.php');
			    $pdf->setLanguageArray($l);
			}

			// ---------------------------------------------------------

			$pdf->SetFont('helvetica', 'B', 20);
			$pdf->AddPage();
			$pdf->SetFont('helvetica', '', 9);
			
			$base_url = base_url();
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preferencecompany['logo_koperasi']."\" alt=\"\" width=\"700%\" height=\"300%\"/>";

			$tblheader = "
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
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:14px\";><b>Jadwal Angsuran</b></div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>No. Pinjaman</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"45%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['credits_account_serial']."</b></div>
						</td>

						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Jenis Pinjaman</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"50%\">
							<div style=\"font-size:12px\";><b>: ".$this->AcctCreditAccount_model->getAcctCreditsName($acctcreditsaccount['credits_id'])."</b></div>
						</td>		
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Nama</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"45%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['member_name']."</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Jangka Waktu</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"50%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['credits_account_period']." ".$paymentperiod[$acctcreditsaccount['credits_payment_period']]."</b></div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Tipe Angsuran</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"45%\">
							<div style=\"font-size:12px\";><b>: ".$paymenttype[$acctcreditsaccount['payment_type_id']]."</b></div>
						</td>	
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Plafon</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"50%\">
							<div style=\"font-size:12px\";><b>: Rp.".number_format($acctcreditsaccount['credits_account_amount'])."</b></div>
						</td>			
	 				</tr>
	 			</table>
	 			<br><br>
			";
			
			$pdf->writeHTML($tblheader, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
			    <tr>
			        <td width=\"5%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Ke</div></td>
			        <td width=\"12%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Tanggal Angsuran</div></td>
			        <td width=\"18%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Saldo Pokok</div></td>
			        <td width=\"15%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Angsuran Pokok</div></td>
			        <td width=\"15%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Angsuran Bunga</div></td>
			        <td width=\"18%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Total Angsuran</div></td>
			        <td width=\"18%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Sisa Pokok</div></td>
			    </tr>				
			</table>";

			$no = 1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">";
		
			foreach ($datapola as $key => $val) {
				
				$roundAngsuran=round($val['angsuran'],-3);
				$sisaRoundAngsuran = $val['angsuran'] - $roundAngsuran;
				$sumAngsuranBunga = $val['angsuran_bunga'] + $sisaRoundAngsuran;

				$tbl3 .= "
					<tr>
				    	<td width=\"5%\"><div style=\"text-align: left;\">&nbsp; ".$val['ke']."</div></td>
				    	<td width=\"12%\"><div style=\"text-align: right;\">".tgltoview($val['tanggal_angsuran'])." &nbsp; </div></td>
				        <td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['opening_balance'], 2)." &nbsp; </div></td>
				        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['angsuran_pokok'], 2)." &nbsp; </div></td>
				        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($sumAngsuranBunga,2)." &nbsp; </div></td>
				        <td width=\"18%\"><div style=\"text-align: right;\">".number_format($roundAngsuran,2)." &nbsp; </div></td>
				        <td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['last_balance'], 2)." &nbsp; </div></td>
				    </tr>	
				";

				$no++;
				$totalpokok += $val['angsuran_pokok'];
				$totalmargin += $sumAngsuranBunga;
				$total += $roundAngsuran;
			}

			$tbl4 = "
				<tr>
					<td colspan=\"3\"><div style=\"text-align: right;font-weight:bold\">Total</div></td>
					<td><div style=\"text-align: right;font-weight:bold\">".number_format($totalpokok, 2)."</div></td>
					<td><div style=\"text-align: right;font-weight:bold\">".number_format($totalmargin, 2)."</div></td>
					<td><div style=\"text-align: right;font-weight:bold\">".number_format($total, 2)."</div></td>
				</tr>							
			</table>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');

			ob_clean();

			$filename = 'Jadwal_Angsuran_'.$acctcreditsaccount['credits_account_serial'].'.pdf';
			$pdf->Output($filename, 'I');
		}

		public function printAgunanReceipt(){
			$credits_account_id 	= $this->uri->segment(3);
			$acctcreditsaccount		= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);
			$acctcreditsagunan 		= $this->AcctCreditAccount_model->getAcctCreditsAgunan_Detail($credits_account_id);
			
			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			
			$pdf = new MYPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
			
			$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
			
			$pdf->SetPrintHeader(true);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(20, 50, 20, 10); 
			
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

			if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			    require_once(dirname(__FILE__).'/lang/eng.php');
			    $pdf->setLanguageArray($l);
			}

			// ---------------------------------------------------------

			$pdf->SetFont('helvetica', 'B', 20);
			$pdf->AddPage();
			$pdf->SetFont('helvetica', '', 9);

			//---------------------------------------------------------------------------------------------------------------------------------

			$base_url = base_url();
			$img1 = "<img src=\"".$base_url."assets/layouts/layout/img/logo/logomandirisejahteranoname.png"."\" alt=\"\" width=\"900%\" height=\"900%\"/>";
			$img2 = "<img src=\"".$base_url."assets/layouts/layout/img/logo/logokoperasiindonesia.png"."\" alt=\"\" width=\"900%\" height=\"900%\"/>";

			$tbl = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:14px\";><b>TANDA TERIMA JAMINAN</b></div>
						</td>			
	 				</tr>
					<br>
					<br>
					<tr>
					   <td style=\"text-align:left;\" width=\"100%\">
						   <div style=\"font-size:12px\";>Telah diterima barang jaminan dari :</div>
					   </td>	
					</tr>
					<tr>
					   <td style=\"text-align:left;\" width=\"5%\">
					   </td>
					   <td style=\"text-align:left;\" width=\"28%\">
						   <div style=\"font-size:12px\";>Nama</div>
					   </td>
					   <td style=\"text-align:left;\" width=\"2%\">
						   <div style=\"font-size:12px\";>:</div>
					   </td>
					   <td style=\"text-align:left;\" width=\"65%\">
						   <div style=\"font-size:12px\";>".$acctcreditsaccount['member_name']."</div>
					   </td>		
					</tr>
					<tr>
					   <td style=\"text-align:left;\" width=\"5%\">
					   </td>
					   <td style=\"text-align:left;\" width=\"28%\">
						   <div style=\"font-size:12px\";>No. KTP</div>
					   </td>
					   <td style=\"text-align:left;\" width=\"2%\">
						   <div style=\"font-size:12px\";>:</div>
					   </td>
					   <td style=\"text-align:left;\" width=\"65%\">
						   <div style=\"font-size:12px\";>".$acctcreditsaccount['member_identity_no']."</div>
					   </td>		
					</tr>
					<tr>
					   <td style=\"text-align:left;\" width=\"5%\">
					   </td>
					   <td style=\"text-align:left;\" width=\"28%\">
						   <div style=\"font-size:12px\";>Pekerjaan</div>
					   </td>
					   <td style=\"text-align:left;\" width=\"2%\">
						   <div style=\"font-size:12px\";>:</div>
					   </td>
					   <td style=\"text-align:left;\" width=\"65%\">
						   <div style=\"font-size:12px\";>".$acctcreditsaccount['member_company_job_title']."</div>
					   </td>		
					</tr>
					<tr>
					   <td style=\"text-align:left;\" width=\"5%\">
					   </td>
					   <td style=\"text-align:left;\" width=\"28%\">
						   <div style=\"font-size:12px\";>Alamat</div>
					   </td>
					   <td style=\"text-align:left;\" width=\"2%\">
						   <div style=\"font-size:12px\";>:</div>
					   </td>
					   <td style=\"text-align:left;\" width=\"65%\">
						   <div style=\"font-size:12px\";>".$acctcreditsaccount['member_address']."</div>
					   </td>		
					</tr>
					<tr>
					   <td style=\"text-align:left;\" width=\"5%\">
					   </td>
					   <td style=\"text-align:left;\" width=\"28%\">
						   <div style=\"font-size:12px\";>Telepon</div>
					   </td>
					   <td style=\"text-align:left;\" width=\"2%\">
						   <div style=\"font-size:12px\";>:</div>
					   </td>
					   <td style=\"text-align:left;\" width=\"65%\">
						   <div style=\"font-size:12px\";>".$acctcreditsaccount['member_phone']."</div>
					   </td>		
					</tr>";
					foreach($acctcreditsagunan as $key => $val){
						if($val['credits_agunan_type'] == 1){
						$tbl .= "
						<tr>
						   <td style=\"text-align:left;\" width=\"100%\">
							   <div style=\"font-size:12px\";>Jaminan BPKB dengan data sebagai berikut :</div>
						   </td>	
						</tr>
						<br>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">-
							</td>
							<td style=\"text-align:left;\" width=\"28%\">
								<div style=\"font-size:12px\";>No. BPKB</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px\";>:</div>
							</td>
							<td style=\"text-align:left;\" width=\"65%\">
								<div style=\"font-size:12px\";>".$val['credits_agunan_bpkb_nomor']."</div>
							</td>		
						</tr>
						<br>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">-
							</td>
							<td style=\"text-align:left;\" width=\"28%\">
								<div style=\"font-size:12px\";>No. Polisi</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px\";>:</div>
							</td>
							<td style=\"text-align:left;\" width=\"65%\">
								<div style=\"font-size:12px\";>".$val['credits_agunan_bpkb_nopol']."</div>
							</td>		
						</tr>
						<br>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">-
							</td>
							<td style=\"text-align:left;\" width=\"28%\">
								<div style=\"font-size:12px\";>No. Rangka</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px\";>:</div>
							</td>
							<td style=\"text-align:left;\" width=\"65%\">
								<div style=\"font-size:12px\";>".$val['credits_agunan_bpkb_no_rangka']."</div>
							</td>		
						</tr>
						<br>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">-
							</td>
							<td style=\"text-align:left;\" width=\"28%\">
								<div style=\"font-size:12px\";>No. Mesin</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px\";>:</div>
							</td>
							<td style=\"text-align:left;\" width=\"65%\">
								<div style=\"font-size:12px\";>".$val['credits_agunan_bpkb_no_mesin']."</div>
							</td>		
						</tr>
						<br>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">-
							</td>
							<td style=\"text-align:left;\" width=\"28%\">
								<div style=\"font-size:12px\";>Merk/Type/Thn/Warna</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px\";>:</div>
							</td>
							<td style=\"text-align:left;\" width=\"65%\">
								<div style=\"font-size:12px\";>".$val['credits_agunan_bpkb_keterangan']."</div>
							</td>		
						</tr>
						<br>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">-
							</td>
							<td style=\"text-align:left;\" width=\"28%\">
								<div style=\"font-size:12px\";>A/N Nama</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px\";>:</div>
							</td>
							<td style=\"text-align:left;\" width=\"65%\">
								<div style=\"font-size:12px\";>".$val['credits_agunan_bpkb_nama']."</div>
							</td>		
						</tr>
						<br>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">-
							</td>
							<td style=\"text-align:left;\" width=\"28%\">
								<div style=\"font-size:12px\";>Alamat</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px\";>:</div>
							</td>
							<td style=\"text-align:left;\" width=\"65%\">
								<div style=\"font-size:12px\";>".$val['credits_agunan_bpkb_address']."</div>
							</td>		
						</tr>
						<br>";
							if($acctcreditsaccount['credits_id'] == 13){
								$tbl .= 
								"<tr>
									<td style=\"text-align:left;\" width=\"5%\">-
									</td>
									<td style=\"text-align:left;\" width=\"95%\">
										<div style=\"font-size:12px\";><b>BPKB Baru dalam Proses Pembuatan Dealer ".$val['credits_agunan_bpkb_dealer_name'].", dan setelah selesai akan diberikan ke pihak KSU “Mandiri Sejahtera”</b></div>
									</td>		
								</tr>
								";
							}
						}else if($val['credits_agunan_type'] == 2){
							$tbl .= "
							<tr>
							   <td style=\"text-align:left;\" width=\"100%\">
								   <div style=\"font-size:12px\";>Jaminan Sertifikat dengan data sebagai berikut :</div>
							   </td>	
							</tr>
							<br>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\">-
								</td>
								<td style=\"text-align:left;\" width=\"28%\">
									<div style=\"font-size:12px\";>No Sertifikat</div>
								</td>
								<td style=\"text-align:left;\" width=\"2%\">
									<div style=\"font-size:12px\";>:</div>
								</td>
								<td style=\"text-align:left;\" width=\"65%\">
									<div style=\"font-size:12px\";>".$val['credits_agunan_shm_no_sertifikat']."</div>
								</td>		
							</tr>
							<br>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\">-
								</td>
								<td style=\"text-align:left;\" width=\"28%\">
									<div style=\"font-size:12px\";>Luas</div>
								</td>
								<td style=\"text-align:left;\" width=\"2%\">
									<div style=\"font-size:12px\";>:</div>
								</td>
								<td style=\"text-align:left;\" width=\"65%\">
									<div style=\"font-size:12px\";>".$val['credits_agunan_shm_luas']."</div>
								</td>		
							</tr>
							<br>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\">-
								</td>
								<td style=\"text-align:left;\" width=\"28%\">
									<div style=\"font-size:12px\";>A/N Nama</div>
								</td>
								<td style=\"text-align:left;\" width=\"2%\">
									<div style=\"font-size:12px\";>:</div>
								</td>
								<td style=\"text-align:left;\" width=\"65%\">
									<div style=\"font-size:12px\";>".$val['credits_agunan_shm_atas_nama']."</div>
								</td>		
							</tr>
							<br>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\">-
								</td>
								<td style=\"text-align:left;\" width=\"28%\">
									<div style=\"font-size:12px\";>Kedudukan</div>
								</td>
								<td style=\"text-align:left;\" width=\"2%\">
									<div style=\"font-size:12px\";>:</div>
								</td>
								<td style=\"text-align:left;\" width=\"65%\">
									<div style=\"font-size:12px\";>".$val['credits_agunan_shm_kedudukan']."</div>
								</td>		
							</tr>
							<br>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\">-
								</td>
								<td style=\"text-align:left;\" width=\"28%\">
									<div style=\"font-size:12px\";>Keterangan</div>
								</td>
								<td style=\"text-align:left;\" width=\"2%\">
									<div style=\"font-size:12px\";>:</div>
								</td>
								<td style=\"text-align:left;\" width=\"65%\">
									<div style=\"font-size:12px\";>".$val['credits_agunan_shm_keterangan']."</div>
								</td>		
							</tr>
							";
						}else if($val['credits_agunan_type'] == 7){
							$tbl .= "
							<tr>
							   <td style=\"text-align:left;\" width=\"100%\">
								   <div style=\"font-size:12px\";>Jaminan ATM/Jamsostek dengan data sebagai berikut :</div>
							   </td>	
							</tr>
							<br>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\">-
								</td>
								<td style=\"text-align:left;\" width=\"28%\">
									<div style=\"font-size:12px\";>No ATM</div>
								</td>
								<td style=\"text-align:left;\" width=\"2%\">
									<div style=\"font-size:12px\";>:</div>
								</td>
								<td style=\"text-align:left;\" width=\"65%\">
									<div style=\"font-size:12px\";>".$val['credits_agunan_atmjamsostek_nomor']."</div>
								</td>		
							</tr>
							<br>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\">-
								</td>
								<td style=\"text-align:left;\" width=\"28%\">
									<div style=\"font-size:12px\";>Nama Bank</div>
								</td>
								<td style=\"text-align:left;\" width=\"2%\">
									<div style=\"font-size:12px\";>:</div>
								</td>
								<td style=\"text-align:left;\" width=\"65%\">
									<div style=\"font-size:12px\";>".$val['credits_agunan_atmjamsostek_bank']."</div>
								</td>		
							</tr>
							<br>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\">-
								</td>
								<td style=\"text-align:left;\" width=\"28%\">
									<div style=\"font-size:12px\";>A/N Nama</div>
								</td>
								<td style=\"text-align:left;\" width=\"2%\">
									<div style=\"font-size:12px\";>:</div>
								</td>
								<td style=\"text-align:left;\" width=\"65%\">
									<div style=\"font-size:12px\";>".$val['credits_agunan_atmjamsostek_nama']."</div>
								</td>		
							</tr>
							<br>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\">-
								</td>
								<td style=\"text-align:left;\" width=\"28%\">
									<div style=\"font-size:12px\";>Rek Tbgn / No. BPJS</div>
								</td>
								<td style=\"text-align:left;\" width=\"2%\">
									<div style=\"font-size:12px\";>:</div>
								</td>
								<td style=\"text-align:left;\" width=\"65%\">
									<div style=\"font-size:12px\";>".$val['credits_agunan_atmjamsostek_keterangan']."</div>
								</td>		
							</tr>
							";
						}
						setlocale(LC_ALL, 'IND');
						$tbl .= "
						<br>
						<tr>
							<td style=\"text-align:left;font-size:12px;\" width=\"100%\"><b>Dan akan diterimakan kembali saat pinjaman lunas.</b></td>	
						</tr>
						<br>
						<tr>
							<td style=\"text-align:left;font-size:12px;\" width=\"100%\">Karanganyar, ".strftime("%d %B %Y", strtotime($acctcreditsaccount['credits_account_date']))."</td>	
						</tr>
						<br>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
							</td>
							<td style=\"text-align:center;\" width=\"20%\">
								<div style=\"font-size:12px\";>Yang Menyerahkan</div>
							</td>
							<td style=\"text-align:left;\" width=\"50%\">
								<div style=\"font-size:12px\";></div>
							</td>
							<td style=\"text-align:center;\" width=\"20%\">
								<div style=\"font-size:12px\";>Yang Menerima</div>
							</td>	
							<td style=\"text-align:left;\" width=\"5%\">
							</td>	
						</tr>
						<br>
						<br>
						<br>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
							</td>
							<td style=\"text-align:center;\" width=\"20%\">
								<div style=\"font-size:12px\";>(".$acctcreditsaccount['member_name'].")</div>
							</td>
							<td style=\"text-align:left;\" width=\"50%\">
								<div style=\"font-size:12px\";></div>
							</td>
							<td style=\"text-align:center;\" width=\"20%\">
								<div style=\"font-size:12px\";>(Melyda Nur Malita)</div>
							</td>	
							<td style=\"text-align:left;\" width=\"5%\">
							</td>	
						</tr>
						<br>
						<br>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
							</td>
							<td style=\"text-align:left;\" width=\"20%\">
							</td>
							<td style=\"text-align:center;\" width=\"50%\">
								<div style=\"font-size:12px\";>Mengetahui</div>
							</td>
							<td style=\"text-align:left;\" width=\"20%\">
							</td>	
							<td style=\"text-align:left;\" width=\"5%\">
							</td>	
						</tr>
						<br>
						<br>
						<br>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
							</td>
							<td style=\"text-align:left;\" width=\"20%\">
							</td>
							<td style=\"text-align:center;\" width=\"50%\">
								<div style=\"font-size:12px;text-decoration: underline;\";>Herry Warsilo</div>
							</td>
							<td style=\"text-align:left;\" width=\"20%\">
							</td>	
							<td style=\"text-align:left;\" width=\"5%\">
							</td>	
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
							</td>
							<td style=\"text-align:left;\" width=\"20%\">
							</td>
							<td style=\"text-align:center;\" width=\"50%\">
								<div style=\"font-size:12px\";>Pimpinan Cabang</div>
							</td>
							<td style=\"text-align:left;\" width=\"20%\">
							</td>	
							<td style=\"text-align:left;\" width=\"5%\">
							</td>	
						</tr>
						";
					}
	 			$tbl .= "</table>
	 			<br><br>
			";
			
			$pdf->writeHTML($tbl, true, false, false, false, '');

			ob_clean();

			$filename = 'Tanda_Terima_Agunan_'.$acctcreditsaccount['credits_account_serial'].'.pdf';
			$pdf->Output($filename, 'I');
		}

		public function printScheduleCreditsPaymentMember(){
			$credits_account_id 	= $this->uri->segment(3);

			$acctcreditsaccount		= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);
			$paymenttype 			= $this->configuration->PaymentType();
			$paymentperiod 			= $this->configuration->CreditsPaymentPeriod();
			$preferencecompany 		= $this->AcctCreditAccount_model->getPreferenceCompany();			

			if($acctcreditsaccount['payment_type_id'] == '' || $acctcreditsaccount['payment_type_id'] == 1){
				$datapola=$this->flat($credits_account_id);
			}else if ($acctcreditsaccount['payment_type_id'] == 2){
				$datapola=$this->anuitas($credits_account_id);
			}else if($acctcreditsaccount['payment_type_id'] == 3){
				$datapola=$this->slidingrate($credits_account_id);
			}else if($acctcreditsaccount['payment_type_id'] == 4){
				$datapola=$this->menurunharian($credits_account_id);
			}
			
			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			
			$pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(10, 10, 10, 10); 
			
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

			if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			    require_once(dirname(__FILE__).'/lang/eng.php');
			    $pdf->setLanguageArray($l);
			}

			// ---------------------------------------------------------

			$pdf->SetFont('helvetica', 'B', 20);
			$pdf->AddPage();
			$pdf->SetFont('helvetica', '', 9);
			
			$base_url = base_url();
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preferencecompany['logo_koperasi']."\" alt=\"\" width=\"700%\" height=\"300%\"/>";

			$tblheader = "
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
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:14px\";><b>Jadwal Angsuran</b></div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>No. Pinjaman</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"45%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['credits_account_serial']."</b></div>
						</td>

						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Jenis Pinjaman</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"50%\">
							<div style=\"font-size:12px\";><b>: ".$this->AcctCreditAccount_model->getAcctCreditsName($acctcreditsaccount['credits_id'])."</b></div>
						</td>		
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Nama</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"45%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['member_name']."</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Jangka Waktu</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"50%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['credits_account_period']." ".$paymentperiod[$acctcreditsaccount['credits_payment_period']]."</b></div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Tipe Angsuran</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"45%\">
							<div style=\"font-size:12px\";><b>: ".$paymenttype[$acctcreditsaccount['payment_type_id']]."</b></div>
						</td>	
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Plafon</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"50%\">
							<div style=\"font-size:12px\";><b>: Rp.".number_format($acctcreditsaccount['credits_account_amount'])."</b></div>
						</td>			
	 				</tr>
	 			</table>
	 			<br><br>
			";
			
			$pdf->writeHTML($tblheader, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
			    <tr>
			        <td width=\"5%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Ke</div></td>
			        <td width=\"12%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Tanggal Angsuran</div></td>
			        <td width=\"18%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Saldo Pokok</div></td>
			    </tr>				
			</table>";

			$no = 1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">";
		
			foreach ($datapola as $key => $val) {
				$tbl3 .= "
					<tr>
				    	<td width=\"5%\"><div style=\"text-align: left;\">&nbsp; ".$val['ke']."</div></td>
				    	<td width=\"12%\"><div style=\"text-align: right;\">".tgltoview($val['tanggal_angsuran'])." &nbsp; </div></td>
				        <td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['opening_balance'], 2)." &nbsp; </div></td>
				    </tr>
				";

				$no++;
				$totalpokok += $val['angsuran_pokok'];
				$totalmargin += $val['angsuran_bunga'];
				$total += $val['angsuran'];
			}

			$tbl4 = "						
			</table>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');

			ob_clean();

			$filename = 'Jadwal_Angsuran_'.$acctcreditsaccount['credits_account_serial'].'.pdf';
			$pdf->Output($filename, 'I');
		}

		public function printPolaAngsuranCredits(){
			$credits_account_id 	= $this->uri->segment(3);

			$acctcreditsaccount		= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);
			$paymenttype 			= $this->configuration->PaymentType();
			$preferencecompany 		= $this->AcctCreditAccount_model->getPreferenceCompany();		
			$paymentperiod 			= $this->configuration->CreditsPaymentPeriod();	

			if($acctcreditsaccount['payment_type_id'] == '' || $acctcreditsaccount['payment_type_id'] == 1){
				$datapola=$this->flat($credits_account_id);
			}else if ($acctcreditsaccount['payment_type_id'] == 2){
				$datapola=$this->anuitas($credits_account_id);
			}

			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			
			$pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(10, 10, 10, 10);

			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

			// set some language-dependent strings (optional)
			if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			    require_once(dirname(__FILE__).'/lang/eng.php');
			    $pdf->setLanguageArray($l);
			}

			// ---------------------------------------------------------

			$pdf->SetFont('helvetica', 'B', 20);
			$pdf->AddPage();
			$pdf->SetFont('helvetica', '', 9);

			// -----------------------------------------------------------------------------
			
			$base_url = base_url();
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preferencecompany['logo_koperasi']."\" alt=\"\" width=\"700%\" height=\"300%\"/>";

			$tblheader = "
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
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:14px\";><b>Pola Angsuran</b></div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>No. Pinjaman</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"45%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['credits_account_serial']."</b></div>
						</td>

						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Jenis Pinjaman</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"50%\">
							<div style=\"font-size:12px\";><b>: ".$this->AcctCreditAccount_model->getAcctCreditsName($acctcreditsaccount['credits_id'])."</b></div>
						</td>		
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Nama</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"45%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['member_name']."</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Jangka Waktu</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"50%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['credits_account_period']." ".$paymentperiod[$acctcreditsaccount['credits_payment_period']]."</b></div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Tipe Angsuran</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"45%\">
							<div style=\"font-size:12px\";><b>: ".$paymenttype[$acctcreditsaccount['payment_type_id']]."</b></div>
						</td>	
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Plafon</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"50%\">
							<div style=\"font-size:12px\";><b>: Rp.".number_format($acctcreditsaccount['credits_account_amount'])."</b></div>
						</td>			
	 				</tr>
	 			</table>
	 			<br><br>
			";
				
			$pdf->writeHTML($tblheader, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
			    <tr>
			        <td width=\"5%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Ke</div></td>
			        <td width=\"12%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Tanggal Angsuran</div></td>
			        <td width=\"18%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Saldo Pokok</div></td>
			        <td width=\"15%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Angsuran Pokok</div></td>
			        <td width=\"15%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Angsuran Bunga</div></td>
			        <td width=\"18%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Total Angsuran</div></td>
			        <td width=\"18%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Sisa Pokok</div></td>

			       
			    </tr>				
			</table>";

			$no = 1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">";
		
			foreach ($datapola as $key => $val) {
				$tbl3 .= "
					<tr>
				    	<td width=\"5%\"><div style=\"text-align: left;\">&nbsp; ".$val['ke']."</div></td>
				    	<td width=\"12%\"><div style=\"text-align: right;\">".tgltoview($val['tanggal_angsuran'])." &nbsp; </div></td>
				        <td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['opening_balance'], 2)." &nbsp; </div></td>
				        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['angsuran_pokok'], 2)." &nbsp; </div></td>
				        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['angsuran_bunga'], 2)." &nbsp; </div></td>
				        <td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['angsuran'], 2)." &nbsp; </div></td>
				        <td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['last_balance'], 2)." &nbsp; </div></td>
				       	
				    </tr>
				";

				$no++;
				$totalpokok += $val['angsuran_pokok'];
				$totalmargin += $val['angsuran_bunga'];
				$total += $val['angsuran'];
			}

			$tbl4 = "
				<tr>
					<td colspan=\"3\"><div style=\"text-align: right;font-weight:bold\">Total</div></td>
					<td><div style=\"text-align: right;font-weight:bold\">".number_format($totalpokok, 2)."</div></td>
					<td><div style=\"text-align: right;font-weight:bold\">".number_format($totalmargin, 2)."</div></td>
					<td><div style=\"text-align: right;font-weight:bold\">".number_format($total, 2)."</div></td>
				</tr>							
			</table>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');

			ob_clean();

			$filename = 'Pola_Angsuran_'.$acctcreditsaccount['credits_account_serial'].'.pdf';
			$pdf->Output($filename, 'I');
		}

		public function processPrintingAkad(){
			$credits_account_id		= $this->uri->segment(3);

			$memberidentity			= $this->configuration->MemberIdentity();
			$dayname 				= $this->configuration->DayName();
			$monthname 				= $this->configuration->Month();

			$acctcreditsaccount		= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);
			$acctcreditsagunan		= $this->AcctCreditAccount_model->getAcctCreditsAgunan_Detail($credits_account_id);

			if($acctcreditsaccount['credits_id'] == 5 && $acctcreditsaccount['credits_id'] == 6){
				$credits_name = 'MURABAHAH';
			} else {
				$credits_name = '';
			}

			$date 	= date('d', (strtotime($acctcreditsaccount['credits_account_date'])));
			$day 	= date('D', (strtotime($acctcreditsaccount['credits_account_date'])));
			$month 	= date('m', (strtotime($acctcreditsaccount['credits_account_date'])));
			$year 	= date('Y', (strtotime($acctcreditsaccount['credits_account_date'])));

			$acctcreditsagunan	= $this->AcctCreditAccount_model->getAcctCreditsAgunan_Detail($credits_account_id);

			$total_agunan = 0;
			foreach ($acctcreditsagunan as $key => $val) {
				if($val['credits_agunan_type'] == 1){
					$agunanbpkb[] = array (
						'credits_agunan_bpkb_nama'				=> $val['credits_agunan_bpkb_nama'],
						'credits_agunan_bpkb_nomor'				=> $val['credits_agunan_bpkb_nomor'],
						'credits_agunan_bpkb_no_mesin'			=> $val['credits_agunan_bpkb_no_mesin'],
						'credits_agunan_bpkb_no_rangka'			=> $val['credits_agunan_bpkb_no_rangka'],		
					);
				} else if($val['credits_agunan_type'] == 2){
					$agunansertifikat[] = array (
						'credits_agunan_shm_no_sertifikat'		=> $val['credits_agunan_shm_no_sertifikat'],
						'credits_agunan_shm_luas'				=> $val['credits_agunan_shm_luas'],
						'credits_agunan_shm_atas_nama'			=> $val['credits_agunan_shm_atas_nama'],
		
					);
				}else if($val['credits_agunan_type'] == 7){
					$agunanatmjamsostek[] = array (
						'credits_agunan_atmjamsostek_nomor'			=> $val['credits_agunan_atmjamsostek_nomor'],
						'credits_agunan_atmjamsostek_nama'			=> $val['credits_agunan_atmjamsostek_nama'],
						'credits_agunan_atmjamsostek_bank'			=> $val['credits_agunan_atmjamsostek_bank'],
						'credits_agunan_atmjamsostek_keterangan'	=> $val['credits_agunan_atmjamsostek_keterangan'],
					);
				}

				$total_agunan = $total_agunan + $val['credits_agunan_bpkb_taksiran'] + $val['credits_agunan_shm_taksiran'] + $val['credits_agunan_atmjamsostek_taksiran'];
			}

			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			
			$pdf = new MYPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

			$pdf->SetPrintHeader(true);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(20, 50, 20, 0); 
			
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

			if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			    require_once(dirname(__FILE__).'/lang/eng.php');
			    $pdf->setLanguageArray($l);
			}

			// ---------------------------------------------------------

			$pdf->SetFont('helvetica', 'B', 20);
			$pdf->AddPage();
			$pdf->SetFont('helvetica', '', 12);

			// -----------------------------------------------------------------------------

			$akad_payment_period 	= $this->configuration->CreditsPaymentPeriodAkad();
			$monthname				= $this->configuration->Month();
			$month 					= date('m', (strtotime($acctcreditsaccount['credits_account_date'])));
			$day 					= date('d', (strtotime($acctcreditsaccount['credits_account_date'])));
			$year 					= date('Y', (strtotime($acctcreditsaccount['credits_account_date'])));
			$month_due 				= date('m', (strtotime($acctcreditsaccount['credits_account_due_date'])));
			$day_due 				= date('d', (strtotime($acctcreditsaccount['credits_account_due_date'])));
			$year_due				= date('Y', (strtotime($acctcreditsaccount['credits_account_due_date'])));
			$total_administration	= $acctcreditsaccount['credits_account_provisi'] + $acctcreditsaccount['credits_account_komisi'] + $acctcreditsaccount['credits_account_insurance'] + $acctcreditsaccount['credits_account_materai'] + $acctcreditsaccount['credits_account_risk_reserve'] + $acctcreditsaccount['credits_account_stash'] + $acctcreditsaccount['credits_account_adm_cost'] + $acctcreditsaccount['credits_account_principal'];
			$total_administration_elektro	= $acctcreditsaccount['credits_account_provisi'] + $acctcreditsaccount['credits_account_komisi'] + $acctcreditsaccount['credits_account_insurance'] + $acctcreditsaccount['credits_account_materai'] + $acctcreditsaccount['credits_account_risk_reserve'] + $acctcreditsaccount['credits_account_stash'] + $acctcreditsaccount['credits_account_adm_cost'] + $acctcreditsaccount['credits_account_principal'];
			$pencairan				= $acctcreditsaccount['credits_account_amount'] - $total_administration;
			$pencairan_elektro		= $acctcreditsaccount['credits_account_amount'] - $total_administration_elektro - $acctcreditsaccount['credits_account_payment_amount'];
			
			$base_url = base_url();
			$img1 = "<img src=\"".$base_url."assets/layouts/layout/img/logo/logomandirisejahteranoname.png"."\" alt=\"\" width=\"900%\" height=\"900%\"/>";
			$img2 = "<img src=\"".$base_url."assets/layouts/layout/img/logo/logokoperasiindonesia.png"."\" alt=\"\" width=\"900%\" height=\"900%\"/>";

			$tblkop = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:14px\";><b>KSU</b></div>
						</td>	
					</tr>
					<tr>
						<td rowspan=\"4\" width=\"10%\">" .$img1."</td>
						<td style=\"text-align:center;\" width=\"80%\">
							<a style=\"font-size:20px; color:#141a70; text-decoration: none;\";><b>mandiri</b></a> <a style=\"font-size:18px; color:black;text-decoration: none;\";>Sejahtera</a>
						</td>	
						<td rowspan=\"4\" width=\"10%\">" .$img2."</td>
					</tr>
					<tr>
						<td style=\"text-align:center;\" width=\"80%\">
							<div style=\"font-size:14px; color:#141a70;\";><i>'Solusi Kebutuhan Anda'</i></div>
						</td>	
					</tr>
					<tr>
						<td style=\"text-align:center;\" width=\"80%\">
							<div style=\"font-size:12px\";>Gedangan RT. 2 RW. 2 Kemiri, Kebakkramat, Karanganyar</div>
						</td>	
					</tr>
					<tr style=\"border-bottom-style: solid;\">
						<td style=\"text-align:center;\" width=\"80%\">
							<div style=\"font-size:12px\";>(0271) 646990 | 0896 8667 5079, Email : mandirisejahtera.ms@gmail.com</div>
						</td>	
					</tr>
				</table>
				<div>
				<hr/>
				</div>
			";
			
			if($acctcreditsaccount['credits_id'] == 16 || $acctcreditsaccount['credits_id'] == 17 || $acctcreditsaccount['credits_id'] == 18 || $acctcreditsaccount['credits_id'] == 19){
			
			$tblheader = "
	 			<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:14px; font-weight:bold\"><u>SURAT PERJANJIAN HUTANG - PIUTANG ".$credits_name."</u></div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:14px; font-weight:bold\">No. : ".$acctcreditsaccount['credits_account_serial']."</div>
						</td>			
	 				</tr>
	 				
	 			</table>
	 			<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:left;\" width=\"100%\">
							<div style=\"font-size:12px; font-weight:bold;\">Yang bertanda tangan dibawah ini : </div>
						</td>			
	 				</tr>
	 				<br>
	 			</table>
	 			<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
	 				<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px; font-weight:bold;\">1.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\">
								<b>Nyonya Liany Widjaja</b>, Ketua<b> Koperasi Serba Usaha MANDIRI SEJAHTERA</b> yang berkedudukan di Pawisman Gedangan Rt 002 Rw 002 Kelurahan Kemiri, Kecamatan Kebakkramat, Kabupaten Karanganyar, dalam hal ini bertindak dalam jabatannya tersebut di atas, oleh karena itu sah mewakili untuk dan atas nama Koperasi,
								<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								Selaku Pemberi Hutang selanjutnya disebut <b>PIHAK PERTAMA</b>.
							</div>
						</td>		
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px; font-weight:bold;\">2.</div>
						</td>	
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px; font-weight:bold;\">Nama</div>
						</td>
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px; font-weight:bold;\">:</div>
						</td>	
						<td style=\"text-align:left;\" width=\"80%\">
							<div style=\"font-size:12px;\">".$acctcreditsaccount['member_name']."</div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px; font-weight:bold;\"></div>
						</td>	
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px; font-weight:bold;\">No. KTP</div>
						</td>
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px; font-weight:bold;\">:</div>
						</td>	
						<td style=\"text-align:left;\" width=\"80%\">
							<div style=\"font-size:12px;\">".$acctcreditsaccount['member_identity_no']."</div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px; font-weight:bold;\">Pekerjaan</div>
						</td>
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px; font-weight:bold;\">:</div>
						</td>	
						<td style=\"text-align:left;\" width=\"80%\">
							<div style=\"font-size:12px;\">".$acctcreditsaccount['member_company_job_title']."</div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px; font-weight:bold;\">Alamat</div>
						</td>
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px; font-weight:bold;\">:</div>
						</td>	
						<td style=\"text-align:left;\" width=\"80%\">
							<div style=\"font-size:12px;\">".$acctcreditsaccount['member_address']."</div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px; font-weight:bold;\">No. Telpon</div>
						</td>
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px; font-weight:bold;\">:</div>
						</td>	
						<td style=\"text-align:left;\" width=\"80%\">
							<div style=\"font-size:12px;\">".$acctcreditsaccount['member_phone']."</div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px; font-weight:bold;\">Perusahaan</div>
						</td>
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px; font-weight:bold;\">:</div>
						</td>	
						<td style=\"text-align:left;\" width=\"80%\">
							<div style=\"font-size:12px;\">".$acctcreditsaccount['member_company_name']."</div>
						</td>			
	 				</tr>
	 				<tr>
	 					<td style=\"text-align:left;\" width=\"5%\"></td>
						<td style=\"text-align:justify;\" colspan=\"3\">
							<div style=\"font-size:12px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Selaku yang berhutang, selanjutnya disebut 
							<b>PIHAK KEDUA</b></div>
						</td>			
	 				</tr>
	 			</table>
	 			<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:justify;\" colspan=\"4\" width=\"90%\">
							<div style=\"font-size:12px;\">PIHAK PERTAMA dan PIHAK KEDUA telah bersepakat bahwa perjanjian hutang piutang ini dilakukan dan diterima dengan syarat - syarat dan ketentuan sebagai berikut :</div>
						</td>			
	 				</tr>
	 			</table>
				<br/>
				<br/>
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Pasal 1</b></div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Jenis Kredit, Nilai Pinjaman, Jangka Waktu, Jatuh Tempo, Biaya</b></div>
						</td>			
	 				</tr>
	 			</table>";
				if($acctcreditsaccount['credits_id'] == 19){
					$tblheader .= "
					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:left;\" width=\"100%\">
								<div style=\"font-size:12px;\">
								Dengan ini Pihak kedua menerima fasilitas kredit dari Pihak pertama dengan sistem angsuran : <b>Installment</b> : Angsuran Pokok dan Bunga dibayar tiap ".$akad_payment_period[$acctcreditsaccount['credits_payment_period']]." hingga saat jatuh tempo.
								</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"100%\">
								<div style=\"font-size:12px;\">
									<b>Tipe barang yang akan dibeli : ";
									foreach ($acctcreditsagunan as $key => $val) {
										$tblheader .= $val['credits_agunan_other_keterangan'].' ';
									}
									$tblheader .= "
									</b>
								</div>
							</td>
						</tr>
					</table>
					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">Pokok Hutang</div>
							</td>	
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"70%\">
								<div style=\"font-size:12px;\"><b>Rp. ".nominal($acctcreditsaccount['credits_account_amount'])."</b></div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">Administrasi Total</div>
							</td>	
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"70%\">
								<div style=\"font-size:12px;\"><b>Rp. ".nominal($total_administration_elektro)."</b></div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">Angsuran 1 (DP)</div>
							</td>	
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"23%\">
								<div style=\"font-size:12px;\"><b>Rp. ".nominal($acctcreditsaccount['credits_account_payment_amount'])."</b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"3%\">
								<div style=\"font-size:12px;\"><b>-</b></div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"50%\">
								<hr>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">Pencairan Pinjaman</div>
							</td>	
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"70%\">
								<div style=\"font-size:12px;\"><b>Rp. ".nominal($pencairan_elektro)."</b></div>
							</td>			
						</tr>";
				}else{
					$tblheader .= "
					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:left;\" width=\"100%\">
								<div style=\"font-size:12px;\">
								Dengan ini Pihak kedua menerima fasilitas kredit dari Pihak pertama dengan sistem angsuran : <b>Installment</b> : Angsuran Pokok dan Bunga dibayar tiap ".$akad_payment_period[$acctcreditsaccount['credits_payment_period']]." hingga saat jatuh tempo.
								<br>
								Pinjaman yang disetujui kepada Pihak kedua adalah sebesar
								<b>Rp.".nominal($acctcreditsaccount['credits_account_amount'])." ( Rupiah ).</b>
								</div>
							</td>
						 </tr>
					</table>
					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">Administrasi Total</div>
							</td>	
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>
							<td style=\"text-align:justify;\" width=\"70%\">
								<div style=\"font-size:12px;\"><b>Rp. ".nominal($total_administration)."</b></div>
							</td>
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">Pencairan Pinjaman</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>
							<td style=\"text-align:justify;\" width=\"70%\">
								<div style=\"font-size:12px;\"><b>Rp. ".nominal($pencairan)."</b></div>
							</td>
						</tr>";
				}
					$tblheader .= "
					<tr>
						<td style=\"text-align:left;\" width=\"25%\">
							<div style=\"font-size:12px;\">Angsuran /".$akad_payment_period[$acctcreditsaccount['credits_payment_period']]."</div>
						</td>	
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px;\"><b>: </b></div>
						</td>	
						<td style=\"text-align:justify;\" width=\"70%\">
							<div style=\"font-size:12px;\"><b>Rp. ".nominal($acctcreditsaccount['credits_account_payment_amount'])."</b></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"25%\">
							<div style=\"font-size:12px;\">Jangka Waktu</div>
						</td>	
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px;\"><b>: </b></div>
						</td>	
						<td style=\"text-align:justify;\" width=\"70%\">
							<div style=\"font-size:12px;\"><b>".$acctcreditsaccount['credits_account_period'].' '.$akad_payment_period[$acctcreditsaccount['credits_payment_period']]."</b></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"25%\">
							<div style=\"font-size:12px;\">Periode Pinjaman</div>
						</td>	
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px;\"><b>:</b></div>
						</td>	
						<td style=\"text-align:justify;\" width=\"70%\">
							<div style=\"font-size:12px;\"><b>".$day.' '.$monthname[$month].' '.$year." s/d ".$day_due.' '.$monthname[$month_due].' '.$year_due."</b></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"25%\">
							<div style=\"font-size:12px;\">Jatuh Tempo</div>
						</td>	
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px;\"><b>:</b></div>
						</td>	
						<td style=\"text-align:justify;\" width=\"70%\">
							<div style=\"font-size:12px;\"><b>Tanggal ".$day." setiap ".$akad_payment_period[$acctcreditsaccount['credits_payment_period']]."nya</b></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"25%\">
							<div style=\"font-size:12px;\"><b>Denda</b></div>
						</td>	
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px;\"><b>:</b></div>
						</td>	
						<td style=\"text-align:justify;\" width=\"70%\">
							<div style=\"font-size:12px;\"><b>0,5% Per hari dari angsuran ditambah biaya tagih Rp. 15.000 (Lima Belas Ribu) Per kedatangan.</b></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"25%\">
							<div style=\"font-size:12px;\"><b>Pelunasan Di Percepat</b></div>
						</td>	
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px;\"><b>:</b></div>
						</td>	
						<td style=\"text-align:justify;\" width=\"70%\">
							<div style=\"font-size:12px;\"><b>Membayar Seluruh Sisa Angsuran. Apabila ingin memperpanjang Pinjaman, syaratnya Angsuran kurang 2 (dua) kali</b></div>
						</td>			
					</tr>
	 			</table>
				 ";

				if($acctcreditsaccount['credits_id'] != 18){
					$tblheader .="
					<br/>
					<br/><table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>Pasal 2</b></div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>Jaminan</b></div>
							</td>			
						</tr>
					</table>
					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>	
							<td style=\"text-align:justify;\" width=\"100%\">
								<div style=\"font-size:12px;\">
								Untuk menjamin pembayaran kembali dan sebagaimana mestinya dari hutang Pihak Kedua kepada Pihak Pertama berikut bunganya dan jumlah lainnya yang karena sebab apapun wajib dibayar oleh Pihak Kedua,
								<br>";
					$no = 1;
					foreach ($acctcreditsagunan as $key => $val) {
						if($val['credits_agunan_type'] == 2){
							$tblheader .= "<b>".$no.". No. Sertifikat : ".$val['credits_agunan_shm_no_sertifikat']."</b><br>";
							$no++; 
						}
						if($val['credits_agunan_type'] == 7){
							$tblheader .= "
							<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
								<tr>
									<td style=\"text-align:left;\" width=\"5%\">
										<div style=\"font-size:12px;\">".$no.'. '."</div>
									</td>
									<td style=\"text-align:left;\" width=\"25%\">
										<div style=\"font-size:12px;\"><b>No. ATM Asli</b></div>
									</td>	
									<td style=\"text-align:left;\" width=\"2%\">
										<div style=\"font-size:12px;\"><b>: </b></div>
									</td>	
									<td style=\"text-align:justify;\" width=\"70%\">
										<div style=\"font-size:12px;\"><b>".$val['credits_agunan_atmjamsostek_nomor']."</b></div>
									</td>			
								</tr>
								<tr>
									<td style=\"text-align:left;\" width=\"5%\">
										<div style=\"font-size:12px;\"></div>
									</td>
									<td style=\"text-align:left;\" width=\"25%\">
										<div style=\"font-size:12px;\"><b>Rek. Tabungan/No. BPJS</b></div>
									</td>	
									<td style=\"text-align:left;\" width=\"2%\">
										<div style=\"font-size:12px;\"><b>: </b></div>
									</td>	
									<td style=\"text-align:justify;\" width=\"70%\">
										<div style=\"font-size:12px;\"><b>".$val['credits_agunan_atmjamsostek_keterangan']."</b></div>
									</td>			
								</tr>
							</table>";
							$no++;
						}

					}
				}
				if($acctcreditsaccount['credits_id'] == 18){
					$tblheader .= "
								</div>
							</td>			
						</tr>
					</table>
					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>Pasal 2</b></div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>Penyelesaian Hutang</b></div>
							</td>			
						</tr>
					</table>";
				}else{
					$tblheader .= "
								</div>
							</td>			
						</tr>
					</table>
					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>Pasal 3</b></div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>Penyelesaian Hutang</b></div>
							</td>			
						</tr>
					</table>";
				}
				$tblheader .= "<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
	 				<tr>	
						<td style=\"text-align:justify;\" width=\"100%\">
							<div style=\"font-size:12px;\">Bilamana Pihak Kedua lalai dalam melakukan kewajibannya terhadap Koperasi dan telah pula disampaikan kepadanya peringatan - peringatan dan Pihak Kedua tetap melakukan wanprestasi, maka dengan perjanjian ini pula Pihak Kedua memberikan <b>KUASA</b> penuh kepada Koperasi untuk dan atas nama Pihak Kedua guna :</div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">1.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\">Mengambil alih barang yang sesuai, dan pihak Pertama akan menyita barang - barang yang senilai dengan jumlah Pinjaman + Bunga serta Denda untuk menutup kerugian pinjaman.</div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">2.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\">Menjual baik secara lelang maupun bawah tangan barang yang disita dengan harga yang dianggap layak oleh pihak Koperasi dan mengkonpensir hasil penualan barang jaminan tersebut dengan hutang Pihak kedua dan biaya - biaya lain serta denda yang harus dipikul oleh Pihak Kedua.</div>
						</td>			
	 				</tr>
					 <br/>";

					if($acctcreditsaccount['credits_id'] == 18){
						$tblheader .= "<br/>
						<br/>
						<br/>
						<br/>
						<br/>";
					}

	 				$tblheader .= "<tr>	
						<td style=\"text-align:justify;\" width=\"100%\">
							<div style=\"font-size:12px;\">Demikian Surat Perjanjian Hutang Piutang ini ditandatangani di Kantor KSU \"MANDIRI SEJAHTERA\" di kabupaten Karanganyar, Kecamatan Kebakkrmat, Desa Kemiri, <b>".$day.' '.$monthname[$month].' '.$year."</b></div>
						</td>			
	 				</tr>
	 			</table>
	 			<br><br>
			";
				
			$pdf->writeHTML($tblheader, true, false, false, false, '');

			$tblket = "			
	 			<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
	 				<tr>	
						<td style=\"text-align:center;\" width=\"50%\" height=\"80px\">
							<div style=\"font-size:12px;font-weight:bold;\">
								PIHAK PERTAMA</div>
						</td>
						<td style=\"text-align:center;\" width=\"50%\" height=\"80px\">
							<div style=\"font-size:12px;font-weight:bold;\">
								PIHAK KEDUA</div>
						</td>
	 				</tr>
					<br>
					<br>
					<br>
	 				<tr>	
						<td style=\"text-align:center;\" width=\"50%\">
							<div style=\"font-size:12px;font-weight:bold\">Liany Widjaja</div>
						</td>
						<td style=\"text-align:center;\" width=\"50%\">
							<div style=\"font-size:12px;font-weight:bold\">
								".$acctcreditsaccount['member_name']."</div>
						</td>			
	 				</tr>
	 			</table>
			";
				
			$pdf->writeHTML($tblket, true, false, false, false, '');
			
			}else if($acctcreditsaccount['credits_id'] == 13){
				$tblheader = "
					 <table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:14px; font-weight:bold\"><u>PERJANJIAN PEMBIAYAAN KONSUMEN ".$credits_name."</u></div>
							</td>			
						 </tr>
						 <tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:14px; font-weight:bold\">No. : ".$acctcreditsaccount['credits_account_serial']."</div>
							</td>			
						 </tr>
					 </table>
					 <table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:left;\" width=\"100%\">
								<div style=\"font-size:12px; font-weight:bold;\">Yang bertanda tangan dibawah ini : </div>
							</td>			
						 </tr>
						 <br>
					 </table>
					 <table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						 <tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px; font-weight:bold;\">1.</div>
							</td>	
							<td style=\"text-align:justify;\" width=\"95%\">
								<div style=\"font-size:12px;\">
									<b>Nyonya Liany Widjaja</b>, Ketua<b> Koperasi Serba Usaha MANDIRI SEJAHTERA</b> yang berkedudukan di Pawisman Gedangan Rt 002 Rw 002 Kelurahan Kemiri, Kecamatan Kebakkramat, Kabupaten Karanganyar, dalam hal ini bertindak dalam jabatannya tersebut di atas, oleh karena itu sah mewakili untuk dan atas nama Koperasi,
									<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									Selaku \"Pemberi Fasilitas\", selanjutnya disebut <b>PIHAK PERTAMA</b>.
								</div>
							</td>		
						 </tr>
						 <tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px; font-weight:bold;\">2.</div>
							</td>	
							<td style=\"text-align:left;\" width=\"20%\">
								<div style=\"font-size:12px; font-weight:bold;\">Nama</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px; font-weight:bold;\">:</div>
							</td>	
							<td style=\"text-align:left;\" width=\"80%\">
								<div style=\"font-size:12px;\">".$acctcreditsaccount['member_name']."</div>
							</td>			
						 </tr>
						 <tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px; font-weight:bold;\"></div>
							</td>	
							<td style=\"text-align:left;\" width=\"20%\">
								<div style=\"font-size:12px; font-weight:bold;\">No. KTP</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px; font-weight:bold;\">:</div>
							</td>	
							<td style=\"text-align:left;\" width=\"80%\">
								<div style=\"font-size:12px;\">".$acctcreditsaccount['member_identity_no']."</div>
							</td>			
						 </tr>
						 <tr>
							<td style=\"text-align:left;\" width=\"5%\"></td>	
							<td style=\"text-align:left;\" width=\"20%\">
								<div style=\"font-size:12px; font-weight:bold;\">Pekerjaan</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px; font-weight:bold;\">:</div>
							</td>	
							<td style=\"text-align:left;\" width=\"80%\">
								<div style=\"font-size:12px;\">".$acctcreditsaccount['member_company_job_title']."</div>
							</td>			
						 </tr>
						 <tr>
							<td style=\"text-align:left;\" width=\"5%\"></td>	
							<td style=\"text-align:left;\" width=\"20%\">
								<div style=\"font-size:12px; font-weight:bold;\">Alamat</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px; font-weight:bold;\">:</div>
							</td>	
							<td style=\"text-align:left;\" width=\"80%\">
								<div style=\"font-size:12px;\">".$acctcreditsaccount['member_address']."</div>
							</td>			
						 </tr>
						 <tr>
							<td style=\"text-align:left;\" width=\"5%\"></td>	
							<td style=\"text-align:left;\" width=\"20%\">
								<div style=\"font-size:12px; font-weight:bold;\">No. Telpon</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px; font-weight:bold;\">:</div>
							</td>	
							<td style=\"text-align:left;\" width=\"80%\">
								<div style=\"font-size:12px;\">".$acctcreditsaccount['member_phone']."</div>
							</td>			
						 </tr>
						 <tr>
							 <td style=\"text-align:left;\" width=\"5%\"></td>
							<td style=\"text-align:justify;\" colspan=\"3\">
								<div style=\"font-size:12px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Selaku \"Penerima Fasilitas\", selanjutnya disebut 
								<b>PIHAK KEDUA</b></div>
							</td>			
						 </tr>
					 </table>
					 <table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:justify;\" colspan=\"4\" width=\"90%\">
								<div style=\"font-size:12px;\">PIHAK PERTAMA dan PIHAK KEDUA, secara bersama - sama selanjutnya disebut <b>\"Para Pihak\"</b>, sepakat dan saling mengikatkan diri dalam Perjanjian Pembiayaan dengan terlebih dahulu menerangkan hal - hal yang menjadi dasar dari Perjanjian Pembiayaan ini, yaitu :</div>
							</td>			
						 </tr>
					 </table>
					 <br>
					 <br>
					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>Pasal 1</b></div>
							</td>			
						 </tr>
						 <tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>OBJEK PEMBIAYAAN KONSUMEN</b></div>
							</td>			
						 </tr>
					 </table>
					 <table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">
								1. 
								</div>
							</td>
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">
								Pihak Pertama sepakat untuk memberikan fasilitas pembiayaan konsumen kepada Pihak Kedua guna pembelian barang berupa kendaraan bermotor (“kendaraan“) dengan spesifikasi sebagai berikut :
								</div>
							</td>			
						 </tr>
					 </table>
					 <table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					 ";
					 
		foreach ($acctcreditsagunan as $key => $val) {
			$tblheader .= "
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
							</td>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">Jenis / Jumlah</div>
							</td>	
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\">".$val['credits_agunan_bpkb_type']." / Satu</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
							</td>	
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">Merk / Tipe / Tahun</div>
							</td>	
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\">".$val['credits_agunan_bpkb_keterangan']."</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
							</td>	
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">Nomor Rangka</div>
							</td>	
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\">".$val['credits_agunan_bpkb_no_rangka']."</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
							</td>	
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">Nomor Mesin</div>
							</td>	
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\">".$val['credits_agunan_bpkb_no_mesin']."</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
							</td>	
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">Nomor BPKB</div>
							</td>	
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\">".$val['credits_agunan_bpkb_nomor']."</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
							</td>	
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">Atas Nama STNK</div>
							</td>	
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\">".$val['credits_agunan_bpkb_nama']."</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">
								2.
								</div>
							</td>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Harga Barang
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\">Rp. ".nominal($val['credits_agunan_bpkb_taksiran'])."</div>
							</td>	
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
							</td>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Uang Muka Gross
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\">Rp. ".nominal($val['credits_agunan_bpkb_gross'])."</div>
							</td>	
						</tr>
						<br>";
		}
		$tblheader .= "
					 </table>
					 <table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
							</td>	
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">Selanjutnya disebut <b>\"Barang Jaminan\"</b></div>
							</td>	
							</td>			
						</tr>
					 </table>
					<br>
					<br>
					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">
								3. 
								</div>
							</td>
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">
								Untuk Kepentingan pembelian barang tersebut, Pihak Pertama membayarkan langsung kepada dealer / Penyedia Barang, yaitu:
								</div>
							</td>		
						</tr>";
		foreach ($acctcreditsagunan as $key => $val) {
			$tblheader .= "<tr>
							<td style=\"text-align:left;\" width=\"5%\">
							</td>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Nama Dealer
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\">".$val['credits_agunan_bpkb_dealer_name']."</div>
							</td>	
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
							</td>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Alamat
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\">".$val['credits_agunan_bpkb_dealer_address']."</div>
							</td>	
						</tr>
						";
		}
			$tblheader .= "
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
							</td>
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">
								Selanjutnya disebut <b>\"Dealer\"</b>
								</div>
							</td>		
						</tr>
						<br>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
							4.
							</td>
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">
								Pihak Kedua Memberikan kuasa kepada Pihak Pertama untuk dapat mengambil BPKB ( Barang Jaminan ) di dealer.
								</div>
							</td>		
						</tr>
					 </table>
					 <br><br>
	
					 <table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>Pasal 2</b></div>
							</td>			
						 </tr>
						 <tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>STRUKTUR PEMBIAYAAN KONSUMEN</b></div>
							</td>			
						 </tr>
					 </table>
					 <table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:left;\" width=\"100%\">
								<div style=\"font-size:12px;\">
								Fasilitas Pembiayaan Konsumen diberikan kepada Pihak Kedua oleh Pihak Pertama dengan struktur pembiayaan konsumen yang disepakati sebagai berikut :
								</div>
							</td>		
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Pokok Pembiayaan
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\">Rp. ".nominal($acctcreditsaccount['credits_account_amount'])."</div>
							</td>	
						</tr>";
						
					if($acctcreditsaccount['payment_type_id'] == '' || $acctcreditsaccount['payment_type_id'] == 1){
        				$datapola=$this->flat($credits_account_id);
        			}else if ($acctcreditsaccount['payment_type_id'] == 2){
        				$datapola=$this->anuitas($credits_account_id);
        			}else if($acctcreditsaccount['payment_type_id'] == 3){
        				$datapola=$this->slidingrate($credits_account_id);
        			}else if($acctcreditsaccount['payment_type_id'] == 4){
        				$datapola=$this->menurunharian($credits_account_id);
        			}
        			
        			$sumPembiayaan = 0;
        			foreach ($datapola as $key => $val) {
        			    $sumPembiayaan += round($val['angsuran'],-3);
        			}
        			
					$hutangpembiayaan = ($acctcreditsaccount['credits_account_amount']*$acctcreditsaccount['credits_account_interest']/100*$acctcreditsaccount['credits_account_period'])+$acctcreditsaccount['credits_account_amount'];
					$roundPembiayaan=round($hutangpembiayaan,-3);
					$sisaRoundPembiayaan = $roundPembiayaan - $hutangpembiayaan;
					
					if($acctcreditsaccount['payment_type_id'] == 3){
						$tblheader .= "
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Bunga
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\">".($acctcreditsaccount['credits_account_interest']+0)."% menurun</div>
							</td>	
						</tr>";
					}else{
						$tblheader .= "
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Bunga
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\">Rp. ".nominal(($acctcreditsaccount['credits_account_amount']*$acctcreditsaccount['credits_account_interest']/100*$acctcreditsaccount['credits_account_period'])+$sisaRoundPembiayaan)."</div>
							</td>	
						</tr>";
					}
					$tblheader .= "
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Hutang Pembiayaan
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\">Rp. ".nominal($sumPembiayaan)."</div>
							</td>	
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Periode Pembiayaan
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\">".$day.' '.$monthname[$month].' '.$year." s/d ".$day_due.' '.$monthname[$month_due].' '.$year_due."</div>
							</td>	
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Waktu Pembayaran
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\">".$acctcreditsaccount['credits_account_period']." Kali</div>
							</td>	
						</tr>";
						
					if($acctcreditsaccount['payment_type_id'] == 3){
						$tblheader .="
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Angsuran
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\"><b>Pokok + Bunga ".($acctcreditsaccount['credits_account_interest']+0)."% setiap ".$akad_payment_period[$acctcreditsaccount['credits_payment_period']]."nya</b></div>
							</td>	
						</tr>";
					}else{
						$tblheader .="
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Angsuran
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\">Rp. ".nominal(round($acctcreditsaccount['credits_account_payment_amount'],-3))." per ".$akad_payment_period[$acctcreditsaccount['credits_payment_period']]."</div>
							</td>	
						</tr>";
					}
					$tblheader .="
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Tanggal Jatuh Tempo
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\">".$day_due.' '.$monthname[$month_due].' '.$year_due." yang merupakan batas terakhir pembayaran (terlampir)</div>
							</td>	
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Denda Keterlambatan
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\"><b>0.5% per hari</b></div>
							</td>	
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Biaya Tagih
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\">Rp. 15.000 (Lima Belas Ribu Rupiah) per Kwitansi</div>
							</td>	
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Pelunasan Di Percepat
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\">Dapat dilakukan setelah angsuran ke - 6 ( enam ), serta bersedia membayar Administrasi pelunasan dipercepat sebesar 10 % ( sepuluh persen )  dari Sisa Pokok Hutang , ditambah bunga berjalan dan denda keterlambatan yang belum terbayar.</div>
							</td>	
						</tr>
					 </table>
					 <br><br>";
					 $no_pasal = 2;
			if($acctcreditsaccount['credits_account_insurance'] > 0){
				$no_pasal += 1;
				$tblheader .="
					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>Pasal ".$no_pasal."</b></div>
							</td>			
						 </tr>
						 <tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>ASURANSI</b></div>
							</td>			
						 </tr>
					 </table>
					 <table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						 <tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">1.</div>
							</td>	
							<td style=\"text-align:justify;\" width=\"95%\">
								<div style=\"font-size:12px;\">Segala resiko rusak, hilang, atau musnahnya Barang karena sebab apapun juga sepenuhnya menjadi tanggung jawab Pihak Kedua, sehingga dengan rusak, hilang, atau musnahnya Barang tidak meniadakan, mengurangi, atau menunda pemenuhan kewajiban Pihak Kedua terhadap Pihak Pertama.</div>
							</td>			
						 </tr>
						 <tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">2.</div>
							</td>	
							<td style=\"text-align:justify;\" width=\"95%\">
								<div style=\"font-size:12px;\">Pihak Kedua wajib untuk mengasuransikan Barang termasuk membayar biaya premi yang dibayarkannya melalui Pihak Pertama.</div>
							</td>			
						 </tr>
						 <tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">3.</div>
							</td>	
							<td style=\"text-align:justify;\" width=\"95%\">
								<div style=\"font-size:12px;\">Pihak Pertama akan mengasuransikan Barang Jaminan Tersebut secara TLO ( Total Loss Only ), yang artinya apabila ada kehilangan atau kerusakan diatas 85 % baru dapat di Klaim ke Perusahaan Asuransi.</div>
							</td>			
						 </tr>
						 <tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">4.</div>
							</td>	
							<td style=\"text-align:justify;\" width=\"95%\">
								<div style=\"font-size:12px;\">Jika Barang yang berada di bawah penguasaan Pihak Kedua hilang atau rusak, apabila klaim/tuntutan penggantian asuransi dapat dicairkan, maka Pihak Pertama berhak sebagaimana Pihak Kedua setuju untuk menerima penggantian asuransi dan memperhitungkannya dengan seluruh / sisa Hutang Pembiayaan yang masih ada setelah dikurangi dengan biaya dan/atau ongkos-ongkos yang dikeluarkan oleh Pihak Pertama untuk mengajukan, mengurus, atau menyelesaikan klaim/tuntutan penggantian asuransi.</div>
							</td>			
						 </tr>
						 <tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">5.</div>
							</td>	
							<td style=\"text-align:justify;\" width=\"95%\">
								<div style=\"font-size:12px;\">Apabila Penggantian asuransi tidak mencukupi untuk pelunasan seluruh / sisa Hutang Pembiayaan, maka Pihak kedua berjanji dan mengikatkan diri untuk melunasinya.</div>
							</td>			
						 </tr>
						 <tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\"><b>6.</b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"95%\">
								<div style=\"font-size:12px;\"><b>Apabila pihak kedua melakukan pelunasan dimuka / sudah lunas, maka perlindungan Asuransi akan berakhir pula.</b></div>
							</td>			
						 </tr>
						 ";
					// if($acctcreditsaccount['payment_type_id'] == 3){
					// 	$tblheader .="
					// 	 <tr>
					// 		<td style=\"text-align:left;\" width=\"5%\">
					// 			<div style=\"font-size:12px;\">6.</div>
					// 		</td>	
					// 		<td style=\"text-align:justify;\" width=\"95%\">
					// 			<div style=\"font-size:12px;\">Apabila pihak kedua melakukan pelunasan dimuka / sudah lunas, maka perlindungan Asuransi akan berakhir pula.</div>
					// 		</td>			
					// 	 </tr>
					// 	 ";
					// }

				$tblheader .="
					 </table>
					 <br><br>";
			}
			$tblheader .="
					 <table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>Pasal ".($no_pasal+1)."</b></div>
							</td>			
						 </tr>
						 <tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>JAMINAN</b></div>
							</td>			
						 </tr>
						 <tr>
							<td style=\"text-align:justify;\" width=\"100%\">
								<div style=\"font-size:12px;\">Pihak Kedua menjamin bahwa surat dan fisik barang yang dijaminkan ini tidak dijaminkan kepada pihak lain, tidak dalam keadaan sengketa, bebas dari sitaan, tidak dalam keadaan disewakan serta tidak terikat dengan perjanjian apapun. Pihak Kedua menjamin tidak akan merubah fisik barang yang dijaminkan, merawat dengan baik serta menjaga fisik barang tetap dalam keadaan sama pada saat perjanjian ini disepakati.</div>
							</td>			
						 </tr>
					 </table>
					 <br><br>
	
					 <table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>Pasal ".($no_pasal+2)."</b></div>
							</td>			
						 </tr>
						 <tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>PENYELESAIAN HUTANG</b></div>
							</td>			
						 </tr>
						 <tr>
							<td style=\"text-align:justify;\" width=\"100%\">
								<div style=\"font-size:12px;\">Bilamana Pihak Kedua lalai dalam melakukan kewajibannya terhadap Koperasi dan telah pula disampaikan kepadanya peringatan-peringatan dan Pihak Kedua tetap melakukan wanprestasi, maka dengan perjanjian ini pula Pihak Kedua memberikan KUASA penuh kepada Koperasi untuk dan atas nama Pihak Kedua guna :</div>
							</td>			
						 </tr>
					 </table>
					 <table>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">1.</div>
							</td>
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">Mengambil/menarik barang jaminan Pihak Kedua secara langsung dan seketika dari tangan Pihak Kedua atau pihak lain siapapun , bilamana dan di mana saja barang jaminan tersebut berada dan membawanya ke tempat yang ditentukan oleh Pihak Pertama, jika Koperasi karena suatu hal memerlukan barang jaminan tersebut.</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">2.</div>
							</td>
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">Menjual baik secara lelang maupun bawah tangan barang yang dijaminkan dengan harga yang dianggap layak oleh pihak Koperasi dan mengkonpensir hasil penjualan barang jaminan tersebut dengan hutang Pihak Kedua dan biaya-biaya lain serta denda yang harus dipikul oleh Pihak Kedua.</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">3.</div>
							</td>
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">Menandatangani surat-surat yang diperlukan, menerima pembayaran dan memberikan bukti penerimaan pembayaran dari penjualan barang jaminan tersebut.</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">4.</div>
							</td>
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">Menghadap kepada pejabat sipil/militer dan melakukan tindakan hukum lain yang diperlukan untuk itu.</div>
							</td>			
						</tr>
					 </table>
					 <br><br>
	
					 <table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>Pasal ".($no_pasal+3)."</b></div>
							</td>			
						 </tr>
						 <tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>LAIN - LAIN</b></div>
							</td>			
						 </tr>
						 <tr>
							<td style=\"text-align:justify;\" width=\"100%\">
								<div style=\"font-size:12px;\">Pihak Kedua wajib membayar hutangnya kepada Pihak Pertama seketika dan sekaligus bila :</div>
							</td>			
						 </tr>
					 </table>
					 <table>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">a.</div>
							</td>
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">Pihak Kedua lalai dan kelalaian ini sudah cukup dibuktikan dengan lewatnya waktu 7 (tujuh) hari sejak hari pembayaran tersebut, atau pihak kedua tidak/kurang menepati janjinya menurut perjanjian ini.</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">b.</div>
							</td>
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">Pihak Kedua meninggal dunia sebelum melunasi hutangnya,maka semua hutang dan kewajiban Pihak Kedua yang timbul berdasarkan Surat Perjanjian ini  menjadi tanggung jawab ahli waris Pihak Kedua.</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">c.</div>
							</td>
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">Harta benda/kekayaan Pihak Kedua baik seluruhnya maupun sebagian secara apapun dikenakan penyitaan.</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">d.</div>
							</td>
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">Barang yang masih berstatus barang yang dijaminkan Pihak Kedua, berdasarkan perjanjian ini dipindahtangankan secara apapun kepada pihak lain tanpa persetujuan dari Pihak Pertama.</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">e.</div>
							</td>
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">Barang yang masih berstatus barang yang dijaminkan Pihak Kedua, dinyatakan hilang dikarenakan tindak kriminal ataupun rusak dikarenakan apapun.</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">f.</div>
							</td>
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">Biaya penitipan Jaminan sebesar Rp. 1.000,- Perhari akan dikenakan apabila pihak kedua tidak mengambil jaminan lebih dari 30 hari setelah masa kontrak berakhir dan atau lunas.</div>
							</td>			
						</tr>
						";
						
					// if($acctcreditsaccount['payment_type_id'] == 3){
					// 	$tblheader .="
					// 	 <tr>
					// 		<td style=\"text-align:left;\" width=\"5%\">
					// 			<div style=\"font-size:12px;\">f.</div>
					// 		</td>	
					// 		<td style=\"text-align:justify;\" width=\"95%\">
					// 			<div style=\"font-size:12px;\">f.Biaya penitipan Jaminan sebesar Rp. 1.000,- Perhari akan dikenakan apabila pihak kedua tidak mengambil jaminan lebih dari 30 hari setelah masa kontrak berakhir dan atau lunas.</div>
					// 		</td>			
					// 	 </tr>
					// 	 ";
					// }

					$tblheader .="
					 </table>
					 <br><br>
	
					 <table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>Pasal ".($no_pasal+4)."</b></div>
							</td>			
						 </tr>
						 <tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>DOMISILI</b></div>
							</td>			
						 </tr>
						 <tr>
							<td style=\"text-align:justify;\" width=\"100%\">
								<div style=\"font-size:12px;\">Perjanjian Pembiayaan ini dibuat 2 ( dua )  Rangkap dengan aslinya, masing masing mempunyai kekuatan hukum yang sama.
								<br>
								Perjanjian pembiayaan ini dan segala akibat hukumnya, para pihak sepakat memilih domisili yang tetap dan umum di Kantor Panitera Pengadilan Negeri Kabupaten Karanganyar.
								<br>
								<b>Para Pihak Telah Mengerti dan menyetujui setiap dan seluruh isi perjanjian Pembiayaan ini.</b>
								<br>
								Demikian Surat Perjanjian Pembiayaan Konsumen ini ditandatangani pada hari ini, <b>".$day.' '.$monthname[$month].' '.$year."</b>
								</div>
							</td>			
						 </tr>
					 </table>
					 <br>
					 <br>
					 <br>

				";
					
				$pdf->writeHTML($tblheader, true, false, false, false, '');
	
				$tblket = "			
	
					 <table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						 <tr>	
							<td style=\"text-align:center;\" width=\"50%\" height=\"100px\">
								<div style=\"font-size:12px;font-weight:bold;\">
									PIHAK PERTAMA</div>
							</td>
							<td style=\"text-align:center;\" width=\"50%\" height=\"100px\">
								<div style=\"font-size:12px;font-weight:bold;\">
									PIHAK KEDUA</div>
							</td>			
						 </tr>
						 <br>
						 <br>
						 <br>
						 <tr>	
							<td style=\"text-align:center;\" width=\"50%\">
								<div style=\"font-size:12px;font-weight:bold\">Liany Widjaja</div>
							</td>
							<td style=\"text-align:center;\" width=\"50%\">
								<div style=\"font-size:12px;font-weight:bold\">
									".$acctcreditsaccount['member_name']."</div>
							</td>			
						 </tr>
					 </table>
	
				";
				
				$pdf->writeHTML($tblket, true, false, false, false, '');
			
			}else if($acctcreditsaccount['credits_id'] == 14 || $acctcreditsaccount['credits_id'] == 15){
			
				$tblheader = "
					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:14px; font-weight:bold\"><u>SURAT PERJANJIAN HUTANG PIUTANG</u></div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:14px; font-weight:bold\">No. : ".$acctcreditsaccount['credits_account_serial']."</div>
							</td>			
						</tr>
						
					</table>
					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:left;\" width=\"100%\">
								<div style=\"font-size:12px; font-weight:bold;\">Yang bertanda tangan dibawah ini : </div>
							</td>			
						</tr>
						<br>
					</table>
					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px; font-weight:bold;\">1.</div>
							</td>	
							<td style=\"text-align:justify;\" width=\"95%\">
								<div style=\"font-size:12px;\">
									<b>Nyonya Liany Widjaja</b>, Ketua<b> Koperasi Serba Usaha MANDIRI SEJAHTERA</b> yang berkedudukan di Pawisman Gedangan Rt 002 Rw 002 Kelurahan Kemiri, Kecamatan Kebakkramat, Kabupaten Karanganyar, dalam hal ini bertindak dalam jabatannya tersebut di atas, oleh karena itu sah mewakili untuk dan atas nama Koperasi,
									<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									Selaku Pemberi Hutang, selanjutnya disebut <b>PIHAK PERTAMA</b>.
								</div>
							</td>		
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px; font-weight:bold;\">2.</div>
							</td>	
							<td style=\"text-align:left;\" width=\"20%\">
								<div style=\"font-size:12px; font-weight:bold;\">Nama</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px; font-weight:bold;\">:</div>
							</td>	
							<td style=\"text-align:left;\" width=\"80%\">
								<div style=\"font-size:12px;\">".$acctcreditsaccount['member_name']."</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px; font-weight:bold;\"></div>
							</td>	
							<td style=\"text-align:left;\" width=\"20%\">
								<div style=\"font-size:12px; font-weight:bold;\">No. KTP</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px; font-weight:bold;\">:</div>
							</td>	
							<td style=\"text-align:left;\" width=\"80%\">
								<div style=\"font-size:12px;\">".$acctcreditsaccount['member_identity_no']."</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\"></td>	
							<td style=\"text-align:left;\" width=\"20%\">
								<div style=\"font-size:12px; font-weight:bold;\">Pekerjaan</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px; font-weight:bold;\">:</div>
							</td>	
							<td style=\"text-align:left;\" width=\"80%\">
								<div style=\"font-size:12px;\">".$acctcreditsaccount['member_company_job_title']."</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\"></td>	
							<td style=\"text-align:left;\" width=\"20%\">
								<div style=\"font-size:12px; font-weight:bold;\">Alamat</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px; font-weight:bold;\">:</div>
							</td>	
							<td style=\"text-align:left;\" width=\"80%\">
								<div style=\"font-size:12px;\">".$acctcreditsaccount['member_address']."</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\"></td>	
							<td style=\"text-align:left;\" width=\"20%\">
								<div style=\"font-size:12px; font-weight:bold;\">No. Telpon</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px; font-weight:bold;\">:</div>
							</td>	
							<td style=\"text-align:left;\" width=\"80%\">
								<div style=\"font-size:12px;\">".$acctcreditsaccount['member_phone']."</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\"></td>
							<td style=\"text-align:justify;\" colspan=\"3\">
								<div style=\"font-size:12px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Selaku yang berhutang, selanjutnya disebut 
								<b>PIHAK KEDUA</b></div>
							</td>			
						</tr>
					</table>
					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:justify;\" colspan=\"4\" width=\"90%\">
								<div style=\"font-size:12px;\">PIHAK PERTAMA dan PIHAK KEDUA telah bersepakat bahwa perjanjian hutang piutang ini dilakukan dan diterima dengan syarat-syarat dan ketentuan sebagai berikut :</div>
							</td>			
						</tr>
					</table>
					<br>
					<br>
					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>Pasal 1</b></div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>JENIS KREDIT</b></div>
							</td>			
						</tr>
					</table>
					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:left;\" width=\"100%\">
								<div style=\"font-size:12px;\">
								Dengan ini Pihak Kedua menerima fasilitas kredit dari Pihak Pertama dengan sistem angsuran : 
								</div>
							</td>		
						</tr>
						<tr>	
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">
								</div>
							</td>	
							<td style=\"text-align:left;\" width=\"100%\">
								<div style=\"font-size:12px;\">
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Installment : Angsuran Pokok dan Bunga dibayar tiap bulan hingga saat jatuh tempo.
								</div>
							</td>			
						</tr>
					</table>
					<br><br>

					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>Pasal 2</b></div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>NILAI PINJAMAN, JANGKA WAKTU, JATUH TEMPO</b></div>
							</td>			
						</tr>
					</table>
					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:left;\" width=\"100%\">
								<div style=\"font-size:12px;\">
								Pinjaman yang disetujui kepada Pihak Kedua adalah sebesar <b> Rp. ".nominal($acctcreditsaccount['credits_account_amount'])."</b>
								</div>
							</td>		
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Administrasi Total
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\"><b>Rp. ".nominal($acctcreditsaccount['credits_account_amount']-$acctcreditsaccount['credits_account_amount_received'])."</b></div>
							</td>	
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Pencairan Pinjaman
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\"><b>Rp. ".nominal($acctcreditsaccount['credits_account_amount_received'])."</b></div>
							</td>	
						</tr>";
					if($acctcreditsaccount['payment_type_id'] == 3){
					$tblheader .= "
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">Bunga</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\"><b>".($acctcreditsaccount['credits_account_interest']+0)."% menurun per".$akad_payment_period[$acctcreditsaccount['credits_payment_period']]."</b></div>
							</td>	
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Periode Pembayaran
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\"><b>".$day.' '.$monthname[$month].' '.$year." s/d ".$day_due.' '.$monthname[$month_due].' '.$year_due."</b></div>
							</td>	
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Waktu Pembayaran
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\"><b>".$acctcreditsaccount['credits_account_period']." Kali Jangka waktu kredit</b></div>
							</td>	
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Angsuran
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\"><b>Pokok + Bunga ".($acctcreditsaccount['credits_account_interest']+0)."% setiap ".$akad_payment_period[$acctcreditsaccount['credits_payment_period']]."nya</b></div>
							</td>	
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Tanggal Jatuh Tempo
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\"><b>".$day_due.' '.$monthname[$month_due].' '.$year_due." yang merupakan batas terakhir pembayaran (terlampir)</b></div>
							</td>	
						</tr>";
					}else{
					$tblheader .="
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Angsuran /".$akad_payment_period[$acctcreditsaccount['credits_payment_period']]."
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\"><b>Rp. ".nominal($acctcreditsaccount['credits_account_payment_amount'])."</b></div>
							</td>	
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Jangka Waktu
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\"><b>".$acctcreditsaccount['credits_account_period'].' '.$akad_payment_period[$acctcreditsaccount['credits_payment_period']]."</b></div>
							</td>	
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Periode Pinjaman
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\"><b>".$day.' '.$monthname[$month].' '.$year." s/d ".$day_due.' '.$monthname[$month_due].' '.$year_due."</b></div>
							</td>	
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">
								Jatuh Tempo
								</div>
							</td>		
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\"><b>: </b></div>
							</td>	
							<td style=\"text-align:justify;\" width=\"68%\">
								<div style=\"font-size:12px;\"><b>Tanggal ".$day." setiap ".$akad_payment_period[$acctcreditsaccount['credits_payment_period']]."nya</b></div>
							</td>	
						</tr>";
					}
					$tblheader .="
					</table>
					<br><br>

					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>Pasal 3</b></div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>PELUNASAN DIPERCEPAT</b></div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:justify;\" width=\"100%\">
								<div style=\"font-size:12px;\">Pihak Kedua diwajibkan membayar Angsuran tiap bulan sesuai dengan Jadwal yang sudah disepakati bersama, dan jika hutang dilunasi sebelum jatuh tempo Pihak Kedua wajib Membayar seluruh sisa pokok  dan bunga sampai akhir periode. </div>
							</td>			
						</tr>
					</table>
					<br><br>

					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>Pasal 4</b></div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>JAMINAN</b></div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:justify;\" width=\"100%\">
								<div style=\"font-size:12px;\">Untuk menjamin pembayaran kembali dan sebagaimana mestinya dari hutang Pihak Kedua kepada Pihak Pertama berikut bunganya dan jumlah lainnya yang karena sebab apapun wajib dibayar oleh Pihak Kedua,</div>
							</td>			
						</tr>
					</table>
					<table>";
					
		foreach ($acctcreditsagunan as $key => $val) {
			$tblheader .= "<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">No. BPKB</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\">:</div>
							</td>
							<td style=\"text-align:left;\" width=\"68%\">
								<div style=\"font-size:12px;\">".$val['credits_agunan_bpkb_nomor']."</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">No. POLISI</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\">:</div>
							</td>
							<td style=\"text-align:left;\" width=\"68%\">
								<div style=\"font-size:12px;\">".$val['credits_agunan_bpkb_nopol']."</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">No. Mesin</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\">:</div>
							</td>
							<td style=\"text-align:left;\" width=\"68%\">
								<div style=\"font-size:12px;\">".$val['credits_agunan_bpkb_no_mesin']."</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">No. Rangka</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\">:</div>
							</td>
							<td style=\"text-align:left;\" width=\"68%\">
								<div style=\"font-size:12px;\">".$val['credits_agunan_bpkb_no_rangka']."</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">Merk / Type / Tahun</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\">:</div>
							</td>
							<td style=\"text-align:left;\" width=\"68%\">
								<div style=\"font-size:12px;\">".$val['credits_agunan_bpkb_keterangan']."</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">Nama</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\">:</div>
							</td>
							<td style=\"text-align:left;\" width=\"68%\">
								<div style=\"font-size:12px;\">".$val['credits_agunan_bpkb_nama']."</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"25%\">
								<div style=\"font-size:12px;\">Alamat</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\">:</div>
							</td>
							<td style=\"text-align:left;\" width=\"68%\">
								<div style=\"font-size:12px;\">".$val['credits_agunan_bpkb_address']."</div>
							</td>			
						</tr>
						<br>";
		}
			$tblheader	.=	"<tr>
							<td style=\"text-align:left;\" width=\"100%\">
								<div style=\"font-size:12px;\">Pihak Kedua menjamin bahwa surat dan fisik barang yang dijaminkan ini tidak dijaminkan kepada pihak lain, tidak dalam keadaan sengketa, bebas dari sitaan, tidak dalam keadaan disewakan serta tidak terikat dengan perjanjian apapun. Pihak Kedua menjamin tidak akan merubah fisik barang yang dijaminkan, merawat dengan baik serta menjaga fisik barang tetap dalam keadaan sama pada saat perjanjian ini disepakati. </div>
							</td>	
						</tr>
					</table>
					<br>

					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>Pasal 5</b></div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>DENDA DAN BIAYA</b></div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:justify;\" width=\"5%\">
								<div style=\"font-size:12px;\">1.</div>
							</td>	
							<td style=\"text-align:justify;\" width=\"95%\">
								<div style=\"font-size:12px;\">Dalam hal Pihak Kedua lalai terhadap kewajibannya kepada Koperasi, yang cukup dibuktikan dengan lewatnya tanggal pembayaran/pelunasan, sehingga tidak diperlukan pemberitahuan terlebih dahulu kepada Pihak Kedua, dengan ini diwajibkan membayar denda kepada Koperasi sebesar <b>0,5% dari total angsuran untuk tiap hari keterlambatan dan biaya tagih sebesar Rp. 15.000 ( Lima Belas Ribu Rupiah )  Per Kedatangan.</b></div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:justify;\" width=\"5%\">
								<div style=\"font-size:12px;\">2.</div>
							</td>	
							<td style=\"text-align:justify;\" width=\"95%\">
								<div style=\"font-size:12px;\">Biaya penagihan yang menurut perjanjian antara lain biaya teguran/peringatan akibat kelalaian membayar dari Pihak Kedua termasuk pula biaya-biaya lain yang mungkin timbul sehubungan dengan pengakuan hutang Pihak Kedua menurut perjanjian ini harus dipikul dan dibayar Pihak Kedua. Besaran Biaya Tagih <b>sebesar Rp. 15.000 ( Lima Belas Ribu Rupiah ) Per Kedatangan.</b></div>
							</td>			
						</tr>
					</table>
					<br><br>

					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>Pasal 6</b></div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>PENYELESAIAN HUTANG</b></div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:justify;\" width=\"100%\">
								<div style=\"font-size:12px;\">Bilamana Pihak Kedua lalai dalam melakukan kewajibannya terhadap Koperasi dan telah pula disampaikan kepadanya peringatan-peringatan dan Pihak Kedua tetap melakukan wanprestasi, maka dengan perjanjian ini pula Pihak Kedua memberikan KUASA penuh kepada Koperasi untuk dan atas nama Pihak Kedua guna :
								</div>
							</td>			
						</tr>
					</table>
					<table>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">1.</div>
							</td>
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">Mengambil/menarik barang jaminan Pihak Kedua secara langsung dan seketika dari tangan Pihak Kedua atau pihak lain siapapun , bilamana dan di mana saja barang jaminan tersebut berada dan membawanya ke tempat yang ditentukan oleh Pihak Pertama, jika Koperasi karena suatu hal memerlukan barang jaminan tersebut.</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">2.</div>
							</td>
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">Menjual baik secara lelang maupun bawah tangan barang yang dijaminkan dengan harga yang dianggap layak oleh pihak Koperasi dan mengkonpensir hasil penjualan barang jaminan tersebut dengan hutang Pihak Kedua dan biaya-biaya lain serta denda yang harus dipikul oleh Pihak Kedua.</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">3.</div>
							</td>
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">Menandatangani surat-surat yang diperlukan, menerima pembayaran dan memberikan bukti penerimaan pembayaran dari penjualan barang jaminan tersebut.</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">4.</div>
							</td>
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">Menghadap kepada pejabat sipil/militer dan melakukan tindakan hukum lain yang diperlukan untuk itu.</div>
							</td>			
						</tr>
					</table>
					<br><br>

					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>Pasal 7</b></div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>LAIN - LAIN</b></div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:justify;\" width=\"100%\">
								<div style=\"font-size:12px;\">Pihak Kedua wajib membayar hutangnya kepada Pihak Pertama seketika dan sekaligus bila :
								</div>
							</td>			
						</tr>
					</table>
					<table>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">a.</div>
							</td>
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">Pihak Kedua lalai dan kelalaian ini sudah cukup dibuktikan dengan lewatnya waktu 7 (tujuh) hari sejak hari pembayaran tersebut, atau pihak kedua tidak/kurang menepati janjinya menurut perjanjian ini.</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">b.</div>
							</td>
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">Pihak Kedua meninggal dunia sebelum melunasi hutangnya,maka semua hutang dan kewajiban Pihak Kedua yang timbul berdasarkan Surat Perjanjian Hutang Piutang ini berikut semua perubahan/perpanjangan merupakan satu kesatuan hutang dan penyelesaiannya menjadi tanggung jawab ahli waris Pihak Kedua.</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">c.</div>
							</td>
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">Pihak Kedua ditaruh di bawah pengampuan (curatele) atau karena/dengan cara apapun kehilangan hak untuk mengurus harta benda/kekayaannya.</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">d.</div>
							</td>
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">Menurut pertimbangan Pihak Pertama, bahwa harta kekayaan Pihak Kedua menyusut atau berkurang.</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">e.</div>
							</td>
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">Harta benda/kekayaan Pihak Kedua baik seluruhnya maupun sebagian secara apapun dikenakan penyitaan.</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">f.</div>
							</td>
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">Barang yang masih berstatus barang yang dijaminkan Pihak Kedua, berdasarkan perjanjian ini akan dipindahtangankan secara apapun kepada pihak lain tanpa persetujuan dari Pihak Pertama.</div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:left;\" width=\"5%\">
								<div style=\"font-size:12px;\">g.</div>
							</td>
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\">Barang yang masih berstatus barang yang dijaminkan Pihak Kedua, dinyatakan hilang dikarenakan tindak kriminal ataupun rusak dikarenakan apapun.</div>
							</td>			
						</tr>
					</table>
					<br><br>
					<br><br>

					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>Pasal 8</b></div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>ATURAN TAMBAHAN</b></div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:justify;\" width=\"100%\">
								<div style=\"font-size:12px;\">Apabila dikarenakan suatu hal Pihak Kedua terpaksa untuk mengganti barang jaminan, dengan pertimbangan Pihak Koperasi maka perubahan barang yang dijaminkan tersebut tidak terpisahkan dari keseluruhan isi perjanjian dan merupakan satu kesatuan perjanjian ini.
								</div>
							</td>			
						</tr>
					</table>
					<br><br>

					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>Pasal 9</b></div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:center;\" width=\"100%\">
								<div style=\"font-size:12px\"><b>DOMISILI</b></div>
							</td>			
						</tr>
						<tr>
							<td style=\"text-align:justify;\" width=\"100%\">
								<div style=\"font-size:12px;\">Mengenai surat perjanjian hutang-piutang ini dan segala akibat hukumnya, keduabelah pihak sepakat memilih domisili yang tetap dan umum di Kantor Panitera Pengadilan Negeri Kabupaten Karanganyar.
								Demikian Surat Perjanjian Hutang Piutang ini ditandatangani di Kantor KSU “MANDIRI SEJAHTERA” di Kabupaten Karanganyar, Kecamatan Kebakkrmat, Desa Kemiri pada hari ini, <b>".$day.' '.$monthname[$month].' '.$year."</b>
								</div>
							</td>			
						</tr>
					</table>
					<br>
					<br>
					<br>
				";
					
				$pdf->writeHTML($tblheader, true, false, false, false, '');

				$tblket = "			
					<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
						<tr>	
							<td style=\"text-align:center;\" width=\"50%\" height=\"100px\">
								<div style=\"font-size:12px;font-weight:bold;\">
									PIHAK PERTAMA</div>
							</td>
							<td style=\"text-align:center;\" width=\"50%\" height=\"100px\">
								<div style=\"font-size:12px;font-weight:bold;\">
									PIHAK KEDUA</div>
							</td>			
						</tr>
						<br>
						<br>
						<br>
						<tr>	
							<td style=\"text-align:center;\" width=\"50%\">
								<div style=\"font-size:12px;font-weight:bold\">Liany Widjaja</div>
							</td>
							<td style=\"text-align:center;\" width=\"50%\">
								<div style=\"font-size:12px;font-weight:bold\">
									".$acctcreditsaccount['member_name']."</div>
							</td>			
						</tr>
					</table>
				";
				$pdf->writeHTML($tblket, true, false, false, false, '');
			}

			ob_clean();

			$filename = 'Akad_'.$credits_name.'_'.$acctcreditsaccount['member_name'].'.pdf';
			$pdf->Output($filename, 'I');
		}
	}
?>