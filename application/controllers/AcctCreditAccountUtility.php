<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctCreditAccountUtility extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('CoreMember_model');
			$this->load->model('Core_account_Officer_model');
			$this->load->model('Core_source_fund_model');
			$this->load->model('AcctDepositoAccount_model');
			$this->load->model('AcctCredit_model');
			$this->load->model('AcctCreditAccountUtility_model');
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

			$data['main_view']['acctcredits']	= create_double($this->AcctCreditAccountUtility_model->getAcctCredits(),'credits_id', 'credits_name');
			$data['main_view']['corebranch']	= create_double($this->AcctCreditAccountUtility_model->getCoreBranch(),'branch_id', 'branch_name');
			$data['main_view']['content']		= 'AcctCreditAccountUtility/ListAcctCreditsAccount_view';
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
			redirect('AcctCreditAccountUtility');
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

			$list = $this->AcctCreditAccountUtility_model->get_datatables_master($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']);
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
	            $row[] = number_format($creditsaccount->credits_account_financing, 2);
	      //     
			    	$row[] = '
			    		<a href="'.base_url().'AcctCreditAccountUtility/printNoteAcctCreditAccountUtility/'.$creditsaccount->credits_account_id.'" class="btn btn-xs blue" role="button"><i class="fa fa-print"></i> Kwitansi</a> &nbsp;
			    		<a href="'.base_url().'AcctCreditAccountUtility/processPrintingAkad/'.$creditsaccount->credits_account_id.'" class="btn btn-xs blue" role="button"><i class="fa fa-print"></i> Akad</a>';
			    
	            $data[] = $row;
	        }



	        // print_r($list);exit;
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctCreditAccountUtility_model->count_all_master($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']),
	                        "recordsFiltered" => $this->AcctCreditAccountUtility_model->count_filtered_master($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']),
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
			redirect('AcctCreditAccountUtility/addform');
		}

		public function addform(){
			$auth 	= $this->session->userdata('auth');
			$unique = $this->session->userdata('unique');
			$token 	= $this->session->userdata('acctcreditsaccounttoken-'.$unique['unique']);

			if(empty($token)){
				$token = md5(date('Y-m-d H:i:s'));
				$this->session->set_userdata('acctcreditsaccounttoken-'.$unique['unique'], $token);
			}

			$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();
			$data['main_view']['membergender']				= $this->configuration->MemberGender();
			$data['main_view']['coreoffice']				= create_double($this->AcctCreditAccountUtility_model->getCoreOffice(),'office_id', 'office_name');
			$data['main_view']['sumberdana']				= create_double($this->Core_source_fund_model->getData(),'source_fund_id', 'source_fund_name');
			$data['main_view']['coremember']				= $this->CoreMember_model->getCoreMember_Detail($this->uri->segment(3));
			$data['main_view']['acctsavingsaccount']		= create_double($this->AcctDepositoAccount_model->getAcctSavingsAccount($auth['branch_id']),'savings_account_id', 'savings_account_no');
			$data['main_view']['creditid']					= create_double($this->AcctCreditAccountUtility_model->getAcctCredits(),'credits_id', 'credits_name');
			$data['main_view']['content']					= 'AcctCreditAccountUtility/FormAddAcctCreditAccountUtility_view';
			$this->load->view('MainPage_view',$data);
		}

		public function memberlist(){
		$auth = $this->session->userdata('auth');

		$list = $this->CoreMember_model->get_datatables($auth['branch_id']);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $customers) {
            $no++;
            if($customers->member_status == 1){
            	$row = array();
	            $row[] = $no;
	            $row[] = $customers->member_no;
	            $row[] = $customers->member_name;
	            $row[] = $customers->member_address;
	            $row[] = '<a href="'.base_url().'AcctCreditAccountUtility/addform/'.$customers->member_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	            $data[] = $row;
            }
        }
 
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->CoreMember_model->count_all($auth['branch_id']),
                        "recordsFiltered" => $this->CoreMember_model->count_filtered($auth['branch_id']),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
		}

		public function processAddArrayAgunan(){
			$date = date('Ymdhis');
			$credits_agunan_type 			= $this->input->post('tipe', true);


				$data_agunan = array(
					'record_id' 						=> $credits_agunan_type.$date,
					'credits_agunan_type' 				=> $this->input->post('tipe', true),
					'credits_agunan_bpkb_nomor' 		=> $this->input->post('bpkb_nomor', true),
					'credits_agunan_bpkb_nopol' 		=> $this->input->post('bpkb_nopol', true),
					'credits_agunan_bpkb_nama' 			=> $this->input->post('bpkb_nama', true),
					'credits_agunan_bpkb_no_mesin' 		=> $this->input->post('bpkb_no_mesin', true),
					'credits_agunan_bpkb_no_rangka'		=> $this->input->post('bpkb_no_rangka', true),
					'credits_agunan_bpkb_taksiran' 		=> $this->input->post('bpkb_taksiran', true),
					'credits_agunan_bpkb_keterangan'	=> $this->input->post('bpkb_keterangan', true),
					'credits_agunan_shm_no_sertifikat' 	=> $this->input->post('shm_no_sertifikat', true),
					'credits_agunan_shm_luas' 			=> $this->input->post('shm_luas', true),
					'credits_agunan_shm_atas_nama' 		=> $this->input->post('shm_atas_nama', true),
					'credits_agunan_shm_kedudukan' 		=> $this->input->post('shm_kedudukan', true),
					'credits_agunan_shm_taksiran' 		=> $this->input->post('shm_taksiran', true),
					'credits_agunan_shm_keterangan'		=> $this->input->post('shm_keterangan', true)
				);


			$unique 			= $this->session->userdata('unique');
			$session_name 		= $this->input->post('session_name',true);
			$dataArrayHeader	= $this->session->userdata('addarrayacctcreditsagunan-'.$unique['unique']);
			
			$dataArrayHeader[$data_agunan['record_id']] = $data_agunan;
			
			$this->session->set_userdata('addarrayacctcreditsagunan-'.$unique['unique'],$dataArrayHeader);
			// $sesi 	= $this->session->userdata('unique');
			// $data_agunan = $this->session->userdata('addacctcreditsagunan-'.$sesi['unique']);
			
			$data_agunan['record_id'] 								= '';
			$data_agunan['credits_agunan_bpkb_nomor'] 				= '';
			$data_agunan['credits_agunan_type'] 					= '';
			$data_agunan['credits_agunan_bpkb_nama'] 				= '';
			$data_agunan['credits_agunan_bpkb_nopol'] 				= '';
			$data_agunan['credits_agunan_bpkb_no_mesin'] 			= '';
			$data_agunan['credits_agunan_bpkb_no_rangka'] 			= '';
			$data_agunan['credits_agunan_bpkb_taksiran'] 			= '';
			$data_agunan['credits_agunan_bpkb_keterangan'] 			= '';
			$data_agunan['credits_agunan_shm_no_sertifikat'] 		= '';
			$data_agunan['credits_agunan_shm_luas'] 				= '';
			$data_agunan['credits_agunan_shm_atas_nama'] 			= '';
			$data_agunan['credits_agunan_shm_kedudukan'] 			= '';
			$data_agunan['credits_agunan_shm_taksiran'] 			= '';
			$data_agunan['credits_agunan_shm_keterangan'] 			= '';

			
			// $this->session->set_userdata('addacctcreditsagunan-'.$sesi['unique'],$data_agunan);
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

			$credits_account_net_price = $this->input->post('credit_account_net_price',true);

			if(empty($credits_account_net_price) || $credits_account_net_price == 0){
				$credits_account_last_balance_principal 	= $this->input->post('credits_account_last_balance_principal',true);
			} else {
				$credits_account_last_balance_principal 	= $credits_account_net_price;
			}

			

			$credits_account_date 							= tgltodb($this->input->post('credit_account_date',true));

			$credits_account_payment_date 					= date('Y-m-d', strtotime("+1 months", strtotime($credits_account_date)));

			$data = array (
				"credits_account_date" 						=> tgltodb($this->input->post('credit_account_date',true)),
				"member_id"									=> $this->input->post('member_id',true),
				"office_id"									=> $this->input->post('office_id',true),
				"source_fund_id"							=> $this->input->post('sumberdana',true),
				"credits_id"								=> $this->input->post('credit_id',true),
				"branch_id"									=> $auth['branch_id'],
				"credits_account_period"					=> $this->input->post('credit_account_period',true),
				"credits_account_due_date"					=>tgltodb($this->input->post('credit_account_due_date',true)),
				"credits_account_materai"					=> $this->input->post('credit_account_materai',true),
				"credits_account_serial"					=> $this->input->post('credit_account_serial',true),
				"credits_account_adm_cost"					=> $this->input->post('credit_account_adm_cost',true),
				"credits_account_net_price"					=> $this->input->post('credit_account_net_price',true),
				"credits_account_sell_price"				=> $this->input->post('credit_account_sell_price',true),
				"credits_account_um"						=> $this->input->post('credit_account_um',true),
				"credits_account_margin"					=> $this->input->post('credit_account_margin',true),
				"credits_account_financing"					=> $this->input->post('credits_account_last_balance_principal',true),
				"credits_account_nisbah_bmt"				=> $this->input->post('credit_account_nisbah_bmt',true),
				"credits_account_nisbah_agt"				=> $this->input->post('credit_account_nisbah_agt',true),
				"credits_account_notaris"					=> $this->input->post('credit_account_notaris',true),
				"credits_account_insurance"					=> $this->input->post('credit_account_insurance',true),
				"credits_account_principal_amount"			=> $this->input->post('credits_account_principal_amount',true),
				"credits_account_margin_amount"				=> $this->input->post('credits_account_margin_amount',true),
				"credits_account_payment_amount"			=> $this->input->post('credit_account_payment_amount',true),
				"credits_account_last_balance_principal"	=> $credits_account_last_balance_principal,
				"credits_account_last_balance_margin"		=> $this->input->post('credit_account_margin',true),
				"credits_account_payment_date"				=> $credits_account_payment_date,
				"savings_account_id"						=> $this->input->post('savings_account_id',true),
				"credits_account_token" 					=> $this->input->post('credits_account_token',true),
				"created_id"								=> $auth['user_id'],
				"created_on"								=> date('Y-m-d H:i:s'),
			);

			// print_r($data);exit;

			$credits_account_token 					= $this->AcctCreditAccountUtility_model->getCreditsAccountToken($data['credits_account_token']);

			if($credits_account_token->num_rows()==0){
				if($this->AcctCreditAccountUtility_model->insertAcctCreditAccountUtility($data)){
					if(!empty($daftaragunan)){
						foreach ($daftaragunan as $key => $val) {
							if($val['credits_agunan_type'] == 'BPKB'){
								$credits_agunan_type	= 1;
							}else {
								$credits_agunan_type 	= 2;
							}
							$dataagunan = array (
								'credits_account_id'				=> $acctcreditsaccount_last['credits_account_id'],
								'credits_agunan_type'				=> $credits_agunan_type,
								'credits_agunan_shm_no_sertifikat'	=> $val['credits_agunan_shm_no_sertifikat'],
								'credits_agunan_shm_atas_nama'		=> $val['credits_agunan_shm_atas_nama'],
								'credits_agunan_shm_luas'			=> $val['credits_agunan_shm_luas'],
								'credits_agunan_shm_kedudukan'		=> $val['credits_agunan_shm_kedudukan'],
								'credits_agunan_shm_taksiran'		=> $val['credits_agunan_shm_taksiran'],
								'credits_agunan_shm_keterangan'		=> $val['credits_agunan_shm_keterangan'],
								'credits_agunan_bpkb_nomor'			=> $val['credits_agunan_bpkb_nomor'],
								'credits_agunan_bpkb_nama'			=> $val['credits_agunan_bpkb_nama'],
								'credits_agunan_bpkb_nopol'			=> $val['credits_agunan_bpkb_nopol'],
								'credits_agunan_bpkb_no_rangka'		=> $val['credits_agunan_bpkb_no_rangka'],
								'credits_agunan_bpkb_no_mesin'		=> $val['credits_agunan_bpkb_no_mesin'],
								'credits_agunan_bpkb_taksiran'		=> $val['credits_agunan_bpkb_taksiran'],
								'credits_agunan_bpkb_keterangan'	=> $val['credits_agunan_bpkb_keterangan'],

							);

							$this->AcctCreditAccountUtility_model->insertAcctCreditsAgunan($dataagunan);
							// print_r($dataagunan);
						}
					}

					$acctcreditsaccount_last 				= $this->AcctCreditAccountUtility_model->getAcctCreditsAccount_Last($data['created_on']);


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
					$url='AcctCreditAccountUtility/showdetaildata/'.$acctcreditsaccount_last['credits_account_id'];
					redirect($url);
				}else{
					$this->session->set_userdata('addacctdepositoaccount',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Credit Berjangka Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					$url='AcctCreditAccountUtility/addform/'.$member_id;
					redirect($url);
				}
			}
		}

		public function showdetaildata(){
			$auth 					= $this->session->userdata('auth'); 
			$credits_account_id 	= $this->uri->segment(3);
			$type 					= $this->uri->segment(4);
			if($type== '' || $type==0){
				$datapola 			= $this->flat($credits_account_id);
			}else{
				$datapola 			= $this->slidingrate($credits_account_id);
			}


			$detaildata 			= $this->AcctCreditAccountUtility_model->getAcctCreditsAccount_Detail($credits_account_id);

			$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();
			$data['main_view']['membergender']				= $this->configuration->MemberGender();
			$data['main_view']['acctcreditsaccount']		= $this->AcctCreditAccountUtility_model->getAcctCreditsAccount_Detail($credits_account_id);
			$data['main_view']['acctcreditsagunan']			= $this->AcctCreditAccountUtility_model->getAcctCreditsAgunan_Detail($credits_account_id);
			$data['main_view']['coreoffice']				= create_double($this->AcctCreditAccountUtility_model->getCoreOffice(),'office_id', 'office_name');
			$data['main_view']['sumberdana']				= create_double($this->Core_source_fund_model->getData(),'source_fund_id', 'source_fund_name');
			$data['main_view']['coremember']				= $this->CoreMember_model->getCoreMember_Detail($detaildata['member_id']);
			$data['main_view']['acctsavingsaccount']		= create_double($this->AcctDepositoAccount_model->getAcctSavingsAccount($auth['branch_id']),'savings_account_id', 'savings_account_no');
			$data['main_view']['creditid']					= create_double($this->AcctCredit_model->getData(),'credits_id', 'credits_name');

			$data['main_view']['creditaccount']				= $this->AcctCreditAccountUtility_model->getAcctCreditsAccount_Detail($this->uri->segment(3));
			$data['main_view']['datapola']					= $datapola;

			$data['main_view']['content']					= 'AcctCreditAccountUtility/FormSaveSuccessAcctCreditAccountUtility_view';
			$this->load->view('MainPage_view',$data);
		}

		public function printNoteAcctCreditAccountUtility(){
			$auth = $this->session->userdata('auth');
			$credits_account_id 	= $this->uri->segment(3);
			$preferencecompany 		= $this->AcctCreditAccountUtility_model->getPreferenceCompany();
			$acctcreditsaccount	 	= $this->AcctCreditAccountUtility_model->getAcctCreditsAccount_Detail($credits_account_id);



			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

			// set document information
			/*$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('');
			$pdf->SetTitle('');
			$pdf->SetSubject('');
			$pdf->SetKeywords('TCPDF, PDF, example, test, guide');*/

			// set default header data
			/*$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE);
			$pdf->SetSubHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_STRING);*/

			// set header and footer fonts
			/*$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
			$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));*/

			// set default monospaced font
			/*$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);*/

			// set margins
			/*$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);*/

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(7, 7, 7, 7); // put space of 10 on top
			/*$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);*/
			/*$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);*/

			// set auto page breaks
			/*$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);*/

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
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".numtotxt($acctcreditsaccount['credits_account_financing'])."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Keperluan</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: PENCAIRAN PEMBIAYAAN</div></td>
			    </tr>
			     <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Jumlah</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: Rp. &nbsp;".number_format($acctcreditsaccount['credits_account_financing'], 2)."</div></td>
			    </tr>				
			</table>";

			$tbl2 = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			    	<td width=\"30%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"20%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"30%\"><div style=\"text-align: center;\">".$this->AcctCreditAccountUtility_model->getBranchCity($auth['branch_id']).", ".date('d-m-Y')."</div></td>
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
			
			//Close and output PDF document
			$filename = 'Kwitansi.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function AcctCreditAccountUtilityBook(){
			$auth = $this->session->userdata('auth');

			$data['main_view']['acctcredits']	= create_double($this->AcctCreditAccountUtility_model->getAcctCredits(),'credits_id', 'credits_name');
			$data['main_view']['corebranch']	= create_double($this->AcctCreditAccountUtility_model->getCoreBranch(),'branch_id', 'branch_name');
			$data['main_view']['content']		= 'AcctCreditAccountUtility/ListBookAcctCreditsAccount_view';
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
			redirect('AcctCreditAccountUtility/AcctCreditAccountUtilityBook');
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

				/*print_r(" Sesi");*/
			}

			$list = $this->AcctCreditAccountUtility_model->get_datatables_master($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']);
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
	            $row[] = number_format($creditsaccount->credits_account_financing, 2);
	    
			    	$row[] = '<a href="'.base_url().'AcctCreditAccountUtility/printBookAcctCreditAccountUtility/'.$creditsaccount->credits_account_id.'" class="btn btn-xs blue" role="button"><i class="fa fa-print"></i> Cetak Cover</a>';
			    
	            $data[] = $row;
	        }



	        
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctCreditAccountUtility_model->count_all_master($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']),
	                        "recordsFiltered" => $this->AcctCreditAccountUtility_model->count_filtered_master($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}

		public function printBookAcctCreditAccountUtility(){
			$auth = $this->session->userdata('auth');
			$credits_account_id 	= $this->uri->segment(3);
			$acctcreditsaccount	 	= $this->AcctCreditAccountUtility_model->getAcctCreditsAccount_Detail($credits_account_id);

			$credits_account_payment_date = date('Y-m-d', strtotime("+1 months", strtotime($acctcreditsaccount['credits_account_date'])));



			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

			// set document information
			/*$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('');
			$pdf->SetTitle('');
			$pdf->SetSubject('');
			$pdf->SetKeywords('TCPDF, PDF, example, test, guide');*/

			// set default header data
			/*$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE);
			$pdf->SetSubHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_STRING);*/

			// set header and footer fonts
			/*$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
			$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));*/

			// set default monospaced font
			/*$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);*/

			// set margins
			/*$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);*/

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(5, 30, 7, 7); // put space of 10 on top
			/*$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);*/
			/*$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);*/

			// set auto page breaks
			/*$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);*/

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
			$resolution= array(200, 200);
			
			$page = $pdf->AddPage('P', $resolution);

			/*$pdf->Write(0, 'Example of HTML tables', '', 0, 'L', true, 0, false, false, 0);*/

			$pdf->SetFont('helvetica', '', 8);

			// -----------------------------------------------------------------------------

			

			$tbl1 = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">NOMOR KONTRAK</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$acctcreditsaccount['credits_account_serial']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">JUMLAH PEMBIAYAAN</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".number_format($acctcreditsaccount['credits_account_financing'], 2)."</div></td>
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
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$acctcreditsaccount['branch_id']."</div></td>
			    </tr>	
			</table>";


			$pdf->writeHTML($tbl1, true, false, false, false, '');

			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Kwitansi.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function detailAcctCreditsAccount(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-AcctCreditsAccount');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['member_id']		= '';
				$sesi['credits_id']		= '';
			}

			$start_date = tgltodb($sesi['start_date']);
			$end_date 	= tgltodb($sesi['end_date']);

			$data['main_view']['coremember']				= create_double($this->AcctCreditAccountUtility_model->getCoreMember($auth['branch_id']), 'member_id', 'member_name');

			$data['main_view']['acctcredits']				= create_double($this->AcctCreditAccountUtility_model->getAcctCredits(), 'credits_id', 'credits_name');

			$data['main_view']['acctcreditsaccount']		= $this->AcctCreditAccountUtility_model->getAcctCreditsAccount($start_date, $end_date, $auth['branch_id'], $sesi['member_id'], $sesi['credits_id']);

			$data['main_view']['content']					= 'AcctCreditAccountUtility/ListDetailAcctCreditsAccount_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				'start_date'			=> $this->input->post('start_date',true),
				'end_date'				=> $this->input->post('end_date',true),
				'member_id'				=> $this->input->post('member_id',true),
				'credits_id'			=> $this->input->post('credits_id',true),
			);
			$this->session->set_userdata('filter-AcctCreditsAccount', $data);
			redirect('AcctCreditAccountUtility/detailAcctCreditsAccount');
		}
		
		public function reset_search(){
			$sesi= $this->session->userdata('filter-AcctCreditsAccount');
			$this->session->unset_userdata('filter-AcctCreditsAccount');
			redirect('AcctCreditAccountUtility/detailAcctCreditsAccount');
		}

		public function showdetail(){
			$credits_account_id 	= $this->uri->segment(3);

			$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();

			$data['main_view']['acctcreditsaccount']		= $this->AcctCreditAccountUtility_model->getAcctCreditsAccount_Detail($credits_account_id);

			$data['main_view']['acctcreditspayment']		= $this->AcctCreditAccountUtility_model->getAcctCreditsPayment_Detail($credits_account_id);

			$data['main_view']['content']					= 'AcctCreditAccountUtility/FormDetailAcctCreditsAccount_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processPrinting(){
			$credits_account_id			= $this->input->post('credits_account_id',true);

			$memberidentity				= $this->configuration->MemberIdentity();

			$acctcreditsaccount			= $this->AcctCreditAccountUtility_model->getAcctCreditsAccount_Detail($credits_account_id);

			$acctcreditspayment			= $this->AcctCreditAccountUtility_model->getAcctCreditsPayment_Detail($credits_account_id);

			// print_r($acctcreditsaccount);exit;


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			
			$pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
			// Check the example n. 29 for viewer preferences

			// set document information
			/*$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('');
			$pdf->SetTitle('');
			$pdf->SetSubject('');
			$pdf->SetKeywords('TCPDF, PDF, example, test, guide');*/

			// set default header data
			/*$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE);
			$pdf->SetSubHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_STRING);*/

			// set header and footer fonts
			/*$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
			$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));*/

			// set default monospaced font
			/*$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);*/

			// set margins
			/*$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);*/

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(10, 10, 10, 10); // put space of 10 on top
			/*$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);*/
			/*$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);*/

			// set auto page breaks
			/*$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);*/

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

			$pdf->SetFont('helvetica', '', 10);

			// -----------------------------------------------------------------------------

			/*print_r($preference_company);*/
			
			$tblheader = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:14px\";><b>HISTORI ANGSURAN PINJAMAN</b></div>
						</td>			
	 				</tr>
	 				<tr>
	 					<td style=\"text-align:right;\" width=\"40%\">
							
						</td>
						<td style=\"text-align:left;\" width=\"10%\">
							<div style=\"font-size:12px\";><b>No. Akad</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"50%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['credits_account_serial']."</b></div>
						</td>			
	 				</tr>
	 				<tr>
	 					<td style=\"text-align:right;\" width=\"40%\">
							
						</td>
						<td style=\"text-align:left;\" width=\"10%\">
							<div style=\"font-size:12px\";><b>Nama</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"50%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['member_name']."</b></div>
						</td>			
	 				</tr>
	 			</table>
	 			<br><br>
			";
				
			$pdf->writeHTML($tblheader, true, false, false, false, '');

			$tblmember = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\">

	 				<tr>
						<td style=\"text-align:left;\" width=\"17%\">
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

	 				<tr>
						<td style=\"text-align:left;\" width=\"17%\">
							<div style=\"font-size:12px;font-weight:bold\">
								Tanggal Realisasi
							</div>
						</td>

						<td style=\"text-align:left; \" width=\"15%\">
							<div style=\"font-size:12px;font-weight:bold\">
								: ".tgltoview($acctcreditsaccount['credits_account_date'])."
							</div>
	 					</td>

						<td style=\"text-align:left;\" width=\"17%\">
							<div style=\"font-size:12px;font-weight:bold\">
								Jangka Waktu
							</div>
						</td>
						
						<td style=\"text-align:left; \" width=\"10%\">
							<div style=\"font-size:12px;font-weight:bold\">
								: ".$acctcreditsaccount['credits_account_period']."
							</div>
						</td>
						<td style=\"text-align:left; \" width=\"17%\">
							<div style=\"font-size:12px;font-weight:bold\">
								Pinjaman
							</div>
						</td>

						<td style=\"text-align:left; \" width=\"33%\">
							<div style=\"font-size:12px;font-weight:bold\">
								: ".nominal($acctcreditsaccount['credits_account_sell_price'] - $acctcreditsaccount['credits_account_um'])."
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
					
						<td style=\"text-align:center;\" width=\"15%\">
							<div style=\"font-size:10px\">
								<b>Tanggal Angsuran</b>
							</div>
						</td>
					
						<td style=\"text-align:center;\" width=\"20%\">
							<div style=\"font-size:10px\">
								<b>Angsuran Pokok</b>
							</div>
						</td>

						<td style=\"text-align:center;\" width=\"20%\">
							<div style=\"font-size:10px\">
								<b>Angsuran Margin</b>
							</div>
						</td>

						<td style=\"text-align:center;\" width=\"20%\">
							<div style=\"font-size:10px\">
								<b>Saldo Pokok</b>
							</div>
						</td>

						<td style=\"text-align:center;\" width=\"20%\">
							<div style=\"font-size:10px\">
								<b>Saldo Margin</b>
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

						<td style=\"text-align:left;\" width=\"15%\">
							<div style=\"font-size:10px\">
								".tgltoview($val['credits_payment_date'])."
							</div>
						</td>
					
						<td style=\"text-align:right;\" width=\"20%\">
							<div style=\"font-size:10px\">
								".nominal($val['credits_payment_principal'])."
							</div>
						</td>
					
						<td style=\"text-align:right;\" width=\"20%\">
							<div style=\"font-size:10px\">
								".nominal($val['credits_payment_margin'])."
							</div>
						</td>

						<td style=\"text-align:right;\" width=\"20%\">
							<div style=\"font-size:10px\">
								".nominal($val['credits_principal_last_balance'])."
							</div>
						</td>

						<td style=\"text-align:right;\" width=\"20%\">
							<div style=\"font-size:10px\">
								".nominal($val['credits_margin_last_balance'])."
							</div>
						</td>
					</tr>";

				$no++;
			}

			$tblpaymentfooter = "
				</table>
			";

			

			$pdf->writeHTML($tblpaymentheader.$tblpaymentlist.$tblpaymentfooter, true, false, false, false, '');

			ob_clean();

			$filename = 'Histori_Angsuran_Pinjaman_'.$acctcreditsaccount['credits_account_serial'].'.pdf';
			$pdf->Output($filename, 'I');

			// exit;
			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			// $filename = 'IST Test '.$testingParticipantData['participant_name'].'.pdf';
			// $pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}


		public function creditlist(){
			$data['main_view']['content']			= 'AcctCreditAccountUtility/Creditlist_view';
			$this->load->view('MainPage_view',$data);
			
		}
		public function creditajax(){
			$list = $this->AcctCreditAccountUtility_model->get_datatables();
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
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->CoreMember_model->count_all(),
	                        "recordsFiltered" => $this->CoreMember_model->count_filtered(),
	                        "data" => $data,
	                );
	        echo json_encode($output);
		}

		public function agunanadd(){
				// $this->session->unset_userdata('agunan_data');
				// $this->session->unset_userdata('agunan_key');
				// exit;
			$data = $this->session->userdata('agunan_data');
			$agunan = $this->session->userdata('agunan_key');
			// echo "<pre>";
			// $a=json_encode($data);
			// print_r($a);
			// exit;
			if(!isset($agunan)){
				$agunan=1;
			}
			$new_key=$agunan+1;
			if($this->uri->segment(3)=="save"){
				$type=$this->input->post('tipe',true);
				if($type == 'Sertifikat'){
					$data[$new_key]=array (
							"shm_no_sertifikat"	=> $this->input->post('shm_no_sertifikat',true),
							"shm_luas"	=> $this->input->post('shm_luas',true),
							"shm_atas_nama"	=> $this->input->post('shm_atas_nama',true),
							"shm_kedudukan"	=> $this->input->post('shm_kedudukan',true),
							"shm_taksiran"	=> $this->input->post('shm_taksiran',true),
							"tipe"	=> $this->input->post('tipe',true),
							"shm_keterangan"	=> $this->input->post('shm_keterangan',true),
							);
				}else{
					$data[$new_key]=array (
							"bpkb_nomor"	=> $this->input->post('bpkb_nomor',true),
							"bpkb_nama"	=> $this->input->post('bpkb_nama',true),
							"bpkb_nopol"	=> $this->input->post('bpkb_nopol',true),
							"bpkb_no_mesin"	=> $this->input->post('bpkb_no_mesin',true),
							"bpkb_no_rangka"	=> $this->input->post('bpkb_no_rangka',true),
							"taksiran"	=> $this->input->post('taksiran',true),
							"tipe"	=> $this->input->post('tipe',true),
							"bpkb_keterangan"	=> $this->input->post('bpkb_keterangan',true),
							);
				}
				
				$this->session->set_userdata('agunan_data',$data);
				$this->session->set_userdata('agunan_key',$new_key);
			}
			$kirim['data']=$data;

			
			$this->load->view('AcctCreditAccountUtility/FormAddAcctCreditAgunan',$kirim);
		}
		
		public function agunanview(){
			$credits_account_id 	= $this->uri->segment(3);
			$detaildata=$this->AcctCreditAccountUtility_model->getAcctCreditsAccount_Detail($credits_account_id);
			// print_r($detaildata['credits_account_agunan']); exit;
			$this->load->view('AcctCreditAccountUtility/FormShowCreditAgunan',$detaildata);
		}
		
		public function polaangsuran(){
			$id=$this->uri->segment(3);
			$type=$this->uri->segment(4);
			if($type== '' || $type==0){
				$datapola=$this->flat($id);
			}else{
				$datapola=$this->slidingrate($id);
			}
			$data['main_view']['creditaccount']		= $this->AcctCreditAccountUtility_model->getAcctCreditsAccount_Detail($this->uri->segment(3));
			$data['main_view']['datapola']		= $datapola;
			$data['main_view']['content']			= 'AcctCreditAccountUtility/FormPolaAngsuran_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function angsuran(){
			$id=$this->uri->segment(3);
			$type=$this->uri->segment(4);
			if($type== '' || $type==0){
				$datapola=$this->flat($id);
			}else{
				$datapola=$this->slidingrate($id);
			}
			
			$creditaccount		= $this->AcctCreditAccountUtility_model->getAcctCreditsAccount_Detail($this->uri->segment(3));
			redirect('AcctCreditAccountUtility/showdetaildata/'.$id.'/'.$type,compact('datapola'));
		}
		
		public function cekPolaAngsuran(){
			$id=$this->input->post('id_credit',true);
			$pola=$this->input->post('pola_angsuran',true);
			$url='AcctCreditAccountUtility/angsuran/'.$id.'/'.$pola;
			redirect($url);
		}

		public function flat($id){
			$credistaccount					= $this->AcctCreditAccountUtility_model->getCreditsAccount_Detail($id);

			/*print_r("credistaccount ");
			print_r($credistaccount);
			exit;*/

			$credits_account_um 			= $credistaccount['credits_account_um'];

			if($credistaccount['credits_account_sell_price'] == '' || $credistaccount['credits_account_sell_price'] == 0){
				$credits_account_net_price 		= $credistaccount['credits_account_financing'];
				$total_credits_account 			= $credits_account_net_price;
			} else {
				$credits_account_net_price 		= $credistaccount['credits_account_net_price']; - $creditsaccount['credits_account_um'];
				$total_credits_account 			= $credits_account_net_price;
			}

			$credits_account_margin 		= $credistaccount['credits_account_margin'];
			$credits_account_period 		= $credistaccount['credits_account_period'];

/*			$jangkawaktuth 					= $jangkawaktu/12;
			$percentageth = ($margin*100)/$pinjaman;
			$percentagebl=round($percentageth/$jangkawaktu,2);
			
			$angsuranpokok=round($pinjaman/$jangkawaktuth/12,2);
			$angsuranmargin=round($pinjaman*$percentageth/100/12,2);
			$totangsuran=$angsuranpokok+$angsuranmargin;*/
			$installment_pattern			= array();
			$opening_balance				= $total_credits_account;

			for($i=1; $i<=$credits_account_period; $i++){
				/*$totpokok=$totpokok+$angsuranpokok;
				$sisapokok=$pinjaman-$totpokok;*/

				$angsuran_pokok									= $total_credits_account / $credits_account_period;				

				$angsuran_margin								= $credits_account_margin / $credits_account_period;				

				$angsuran 										= $angsuran_pokok + $angsuran_margin;

				$last_balance 									= $opening_balance - $angsuran_pokok;

				$installment_pattern[$i]['opening_balance']		= $opening_balance;
				$installment_pattern[$i]['ke'] 					= $i;
				$installment_pattern[$i]['angsuran'] 			= $angsuran;
				$installment_pattern[$i]['angsuran_pokok']		= $angsuran_pokok;
				$installment_pattern[$i]['angsuran_margin'] 	= $angsuran_margin;
				$installment_pattern[$i]['akumulasi_pokok'] 	= $totpokok;
				$installment_pattern[$i]['last_balance'] 		= $last_balance;
				
				$opening_balance 								= $last_balance;
			}
			
			return $installment_pattern;
			
		}
		
		public function slidingrate($id){
			$creditsaccount 	= $this->AcctCreditAccountUtility_model->getCreditsAccount_Detail($id);

			/*print_r("detailpinjaman ");
			print_r($detailpinjaman);
			exit;*/
			$credits_account_net_price 		= $creditsaccount['credits_account_net_price'];
			$credits_account_um 			= $creditsaccount['credits_account_um'];
			$credits_account_margin 		= $creditsaccount['credits_account_margin'];
			$credits_account_period 		= $creditsaccount['credits_account_period'];			

			$total_credits_account 			= $credits_account_net_price - $credits_account_um;




			
			$jangkawaktuth 		= $jangkawaktu/12;
			$percentageth 		= ($margin*100)/$pinjaman;
			$percentagebl 		= round($percentageth/$jangkawaktu,2);
			
			$angsuranpokok 		= round($pinjaman/$jangkawaktuth/12,2);
			
			$pola 				= array();
			$totpinjaman 		= $pinjaman;
			$totpokok 			= 0;
			for($i=1; $i<=$jangkawaktu; $i++){
				$angsuranmargin 				= round(($totpinjaman * $percentageth/100)/$jangkawaktu,2);
				$totangsuran 					= $angsuranpokok + $angsuranmargin;
				$totpokok						= $totpokok + $angsuranpokok;
				$sisapokok 						= $pinjaman - $totpokok;
				$pola[$i]['ke']					= $i;
				$pola[$i]['angsuran']			= $totangsuran;
				$pola[$i]['angsuran_pokok']		= $angsuranpokok;
				$pola[$i]['angsuran_margin']	= $angsuranmargin;
				$pola[$i]['akumulasi_pokok']	= $totpokok;
				$pola[$i]['sisa_pokok']			= $sisapokok;
				$totpinjaman					= $totpinjaman - $angsuranpokok;
			}
			
			return $pola;
			
		}
		
		public function anuitas($id){
			
		}
		
		function RATE($nper, $pmt, $pv, $fv = 0.0, $type = 0, $guess = 0.1) {
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
			if($type== '' || $type==0){
				$datapola=$this->flat($credits_account_id);
			}else{
				$datapola=$this->slidingrate($credits_account_id);
			}

			$acctcreditsaccount		= $this->AcctCreditAccountUtility_model->getAcctCreditsAccount_Detail($credits_account_id);
			// print_r($acctcreditsaccount);exit;


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			
			$pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
			// Check the example n. 29 for viewer preferences

			// set document information
			/*$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('');
			$pdf->SetTitle('');
			$pdf->SetSubject('');
			$pdf->SetKeywords('TCPDF, PDF, example, test, guide');*/

			// set default header data
			/*$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE);
			$pdf->SetSubHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_STRING);*/

			// set header and footer fonts
			/*$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
			$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));*/

			// set default monospaced font
			/*$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);*/

			// set margins
			/*$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);*/

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(10, 10, 10, 10); // put space of 10 on top
			/*$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);*/
			/*$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);*/

			// set auto page breaks
			/*$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);*/

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

			$pdf->SetFont('helvetica', '', 10);

			// -----------------------------------------------------------------------------

			/*print_r($preference_company);*/
			
			$tblheader = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:14px\";><b>Pola Angsuran</b></div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"10%\">
							<div style=\"font-size:12px\";><b>No. Akad</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"50%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['credits_account_serial']."</b></div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"10%\">
							<div style=\"font-size:12px\";><b>Nama</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"50%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['member_name']."</b></div>
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
			        <td width=\"5%\"><div style=\"text-align: center;font-size:10;\">Ke</div></td>
			        <td width=\"18%\"><div style=\"text-align: center;font-size:10;\">Saldo Pokok</div></td>
			        <td width=\"18%\"><div style=\"text-align: center;font-size:10;\">Angsuran Pokok</div></td>
			        <td width=\"18%\"><div style=\"text-align: center;font-size:10;\">Angsuran Margin</div></td>
			        <td width=\"18%\"><div style=\"text-align: center;font-size:10;\">Total Angsuran</div></td>
			        <td width=\"18%\"><div style=\"text-align: center;font-size:10;\">Sisa Pokok</div></td>

			       
			    </tr>				
			</table>";

			$no = 1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">";
		
			foreach ($datapola as $key => $val) {
				// print_r($acctcreditspayment);exit;

				$tbl3 .= "
					<tr>
				    	<td width=\"5%\"><div style=\"text-align: left;\">&nbsp; ".$val['ke']."</div></td>
				        <td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['opening_balance'], 2)." &nbsp; </div></td>
				        <td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['angsuran_pokok'], 2)." &nbsp; </div></td>
				        <td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['angsuran_margin'], 2)." &nbsp; </div></td>
				        <td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['angsuran'], 2)." &nbsp; </div></td>
				        <td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['last_balance'], 2)." &nbsp; </div></td>
				       	
				    </tr>
				";

				$no++;
			}

			$tbl4 = "							
			</table>";
			


			

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');

			

			ob_clean();

			$filename = 'Pola_Angsuran_'.$acctcreditsaccount['credits_account_serial'].'.pdf';
			$pdf->Output($filename, 'I');

			// exit;
			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			// $filename = 'IST Test '.$testingParticipantData['participant_name'].'.pdf';
			// $pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function processPrintingAkad(){
			$credits_account_id			= $this->uri->segment(3);

			$memberidentity				= $this->configuration->MemberIdentity();
			$dayname 					= $this->configuration->DayName();
			$monthname 					= $this->configuration->Month();

			$acctcreditsaccount			= $this->AcctCreditAccountUtility_model->getAcctCreditsAccount_Detail($credits_account_id);

			$acctcreditsagunan			= $this->AcctCreditAccountUtility_model->getAcctCreditsAgunan_Detail($credits_account_id);

			if($acctcreditsaccount['credits_id'] == 5 || $acctcreditsaccount['credits_id'] == 6){
				$credits_name = 'MURABAHAH';
			} else {
				$credits_name = '';
			}

			$date 	= date('d', (strtotime($acctcreditsaccount['credits_account_date'])));
			$day 	= date('D', (strtotime($acctcreditsaccount['credits_account_date'])));
			$month 	= date('m', (strtotime($acctcreditsaccount['credits_account_date'])));
			$year 	= date('Y', (strtotime($acctcreditsaccount['credits_account_date'])));

			// print_r($acctcreditsaccount);exit;


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			
			$pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
			// Check the example n. 29 for viewer preferences

			// set document information
			/*$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('');
			$pdf->SetTitle('');
			$pdf->SetSubject('');
			$pdf->SetKeywords('TCPDF, PDF, example, test, guide');*/

			// set default header data
			/*$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE);
			$pdf->SetSubHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_STRING);*/

			// set header and footer fonts
			/*$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
			$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));*/

			// set default monospaced font
			/*$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);*/

			// set margins
			/*$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);*/

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(20, 20, 20, 20); // put space of 10 on top
			/*$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);*/
			/*$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);*/

			// set auto page breaks
			/*$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);*/

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

			$pdf->SetFont('helvetica', '', 10);

			// -----------------------------------------------------------------------------

			/*print_r($preference_company);*/
			
			$tblheader = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:10px\"><i>“Hai orang-orang yang beriman, penuhilah akad-akad (akad) itu……....”</i></div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:10px\";><i>(Terjemahan QS : Al-Maidah 1)</i></div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:10px\"><i>”Hai orang-orang yang beriman, janganlah kamu saling memakan harta sesamamu dengan jalan bathil, kecuali dengan jalan perniagaan yang berlaku suka sama suka diantaramu......”</i></div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:10px\"><i>(Terjemahan QS : An-Nisa’ 29)</i></div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:10px\"><i>Roh seorang mukmin masih terkatung-katung (sesudah wafatnya ) sampai utangnya di dunia dilunasi ..... (HR. Ahmad )</i></div>
						</td>			
	 				</tr>
	 				
	 			</table>
	 			<br><br>
	 			<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:14px; font-weight:bold\"><u>AKAD PEMBIAYAAN ".$credits_name."</u></div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:14px\">No. : ".$acctcreditsaccount['credits_account_serial']."</div>
						</td>			
	 				</tr>
	 				
	 			</table>
			";
				
			$pdf->writeHTML($tblheader, true, false, false, false, '');

			$tblket = "
	 			<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:left;\" width=\"100%\">
							<div style=\"font-size:12px;\">Pada hari ini <b>".$dayname[$day]."</br> tanggal <b>".$date."</b> bulan <b>".$monthname[$month]."</br>  tahun <b>".$year."</br> oleh dan antara pihak-pihak:</div>
						</td>			
	 				</tr>
	 				<br>
	 				<tr>
						<td style=\"text-align:left;\" width=\"100%\">
							<div style=\"font-size:12px;\">Yang bertanda tangan dibawah ini,</div>
						</td>			
	 				</tr>
	 				<br>
	 			</table>
	 			<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
	 				<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">1.</div>
						</td>	
						<td style=\"text-align:left;\" width=\"15%\">
							<div style=\"font-size:12px;\">Nama</div>
						</td>
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px;\">:</div>
						</td>	
						<td style=\"text-align:left;\" width=\"80%\">
							<div style=\"font-size:12px;\">".$acctcreditsaccount['member_name']."</div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\"></div>
						</td>	
						<td style=\"text-align:left;\" width=\"15%\">
							<div style=\"font-size:12px;\">Jabatan</div>
						</td>
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px;\">:</div>
						</td>	
						<td style=\"text-align:left;\" width=\"80%\">
							<div style=\"font-size:12px;\">".$acctcreditsaccount['member_name']."</div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"15%\">
							<div style=\"font-size:12px;\">No. Identitas</div>
						</td>
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px;\">:</div>
						</td>	
						<td style=\"text-align:left;\" width=\"80%\">
							<div style=\"font-size:12px;\">".$acctcreditsaccount['member_identity_no']."</div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"15%\">
							<div style=\"font-size:12px;\">Alamat</div>
						</td>
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px;\">:</div>
						</td>	
						<td style=\"text-align:left;\" width=\"80%\">
							<div style=\"font-size:12px;\">".$acctcreditsaccount['member_address']."</div>
						</td>			
	 				</tr>
	 				<tr>
	 					<td style=\"text-align:left;\" width=\"5%\"></td>
						<td style=\"text-align:justify;\" colspan=\"3\">
							<div style=\"font-size:12px;\"><br>
								Dalam hal ini bertindak dalam jabatannya dan berdasarkan Surat Kuasa Pengurus No : 006/SK/KP.RAJA/JATIM/2017 dengan sah mewakili Koperasi Syariah “Rizky Amanah Jaya” Jawa Timur yang berkedudukan di Dsn Sukabumi 001/004 Kelurahan Siman Kecamatan Kepung Kabupaten Kediri, untuk selanjutnya disebut sebagai  PIHAK I <br></div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">2.</div>
						</td>	
						<td style=\"text-align:left;\" width=\"15%\">
							<div style=\"font-size:12px;\">Nama</div>
						</td>
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px;\">:</div>
						</td>	
						<td style=\"text-align:left;\" width=\"80%\">
							<div style=\"font-size:12px;\">".$acctcreditsaccount['member_name']."</div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\"></div>
						</td>	
						<td style=\"text-align:left;\" width=\"15%\">
							<div style=\"font-size:12px;\">Jabatan</div>
						</td>
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px;\">:</div>
						</td>	
						<td style=\"text-align:left;\" width=\"80%\">
							<div style=\"font-size:12px;\">".$acctcreditsaccount['member_name']."</div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"15%\">
							<div style=\"font-size:12px;\">No. Identitas</div>
						</td>
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px;\">:</div>
						</td>	
						<td style=\"text-align:left;\" width=\"80%\">
							<div style=\"font-size:12px;\">".$acctcreditsaccount['member_identity_no']."</div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"15%\">
							<div style=\"font-size:12px;\">Alamat</div>
						</td>
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px;\">:</div>
						</td>	
						<td style=\"text-align:left;\" width=\"80%\">
							<div style=\"font-size:12px;\">".$acctcreditsaccount['member_address']."</div>
						</td>			
	 				</tr>
	 				<tr>
	 					<td style=\"text-align:left;\" width=\"5%\"></td>
						<td style=\"text-align:justify;\" colspan=\"3\">
							<div style=\"font-size:12px;\">Bertindak  untuk  dan  atas  nama  diri   sendiri, untuk selanjutnya disebut  sebagai PIHAK  II <br></div>
						</td>			
	 				</tr>
	 			</table>
	 			<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:justify;\" colspan=\"4\" width=\"90%\">
							<div style=\"font-size:12px;\">Para pihak terlebih dahulu menerangkan hal-hal berikut ini.</div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:justify;\" colspan=\"4\" width=\"90%\">
							<div style=\"font-size:12px;\">PIHAK I dan PIHAK II, yang secara bersama-sama untuk selanjutnya disebut Para Pihak, bertindak dalam kedudukannya masing-masing sebagaimana tersebut di atas, terlebih dahulu menerangkan bahwa:</div>
						</td>			
	 				</tr>
	 				<tr>
	 					<td style=\"text-align:left;\" width=\"5%\">-</td>
						<td style=\"text-align:justify;\" colspan=\"3\" width=\"95%\">
							<div style=\"font-size:12px;\">Berdasarkan formulir permohonan pembiayaan konsumtif tanggal 26 Desember 2016 PIHAK II telah mengajukan permohonan pembiayaan (..............................................).</div>
						</td>		
	 				</tr>
	 				<tr>
	 					<td style=\"text-align:left;\" width=\"5%\">-</td>
						<td style=\"text-align:justify;\" colspan=\"3\" width=\"95%\">
							<div style=\"font-size:12px;\">Berdasarkan Surat Keputusan Pembiayaan Nomor tanggal 13 JANUARI 2017 yang  merupakan  bagian  yang  tidak  terpisahkan  dari Akad ini,  PIHAK I  telah menyetujui penyaluran pembiayaan sesuai dengan syarat-syarat dan ketentuan yang diatur dalam Akad ini.</div>
						</td>		
	 				</tr>
	 				<br>
	 				<tr>
						<td style=\"text-align:justify;\" colspan=\"4\" width=\"100%\">
							<div style=\"font-size:12px;\">Berdasarkan hal-hal tersebut di atas, Para Pihak dengan ini sepakat mengadakan Akad Pembiayaan Murabahah (untuk selanjutnya disebut Akad) dengan ketentuan-ketentuan dan syarat-syarat berikut ini.</div>
						</td>			
	 				</tr>
	 			</table>
			";
				
			$pdf->writeHTML($tblket, true, false, false, false, '');

			ob_clean();

			$filename = 'Akad_'.$credits_name.'_'.$acctcreditsaccount['member_name'].'.pdf';
			$pdf->Output($filename, 'I');

			// exit;
			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			// $filename = 'IST Test '.$testingParticipantData['participant_name'].'.pdf';
			// $pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}
		
	}
?>