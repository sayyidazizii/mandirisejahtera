<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctMemorialJournal extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctMemorialJournal_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= 	$this->session->userdata('filter-acctmemorialjournal');
			/*print_r("sesi atas ");
			print_r($sesi);
			print_r("<BR> ");

			print_r("auth ");
			print_r($auth);
			print_r("<BR> ");*/
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['branch_id']		= $auth['branch_id'];
				// // if($auth['branch_status'] == 1){
				// // 	$sesi['branch_id']	= $auth['branch_id'];
				// // }
				// if($auth['branch_status'] == 0){
				// 	$sesi['branch_id']	= $auth['branch_id'];
				// }
				// /*print_r("Not Sesi");*/
			 } else {
				if(!$sesi['branch_id']){
					$sesi['branch_id']	= $auth['branch_id'];
				}
				

			}
			// print_r($this->AcctMemorialJournal_model->getAcctMemorialJournal($sesi['start_date'], $sesi['end_date'], $sesi['branch_id']));exit;
			// print_r($this->AcctMemorialJournal_model->getAcctMemorialJournal($sesi['start_date'], $sesi['end_date'], $sesi['branch_id']));exit;


			$data['main_view']['corebranch']			= create_double($this->AcctMemorialJournal_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['acctmemorialjournal']	= $this->AcctMemorialJournal_model->getAcctMemorialJournal($sesi['start_date'], $sesi['end_date'], $sesi['branch_id']);	

			$data['main_view']['content']				= 'AcctMemorialJournal/ListMemorialJournalNew_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date" 	=> tgltodb($this->input->post('start_date',true)),
				"end_date" 		=> tgltodb($this->input->post('end_date',true)),
				"branch_id" 	=> $this->input->post('branch_id',true),
			);

			$this->session->set_userdata('filter-acctmemorialjournal',$data);
			redirect('memorial-journal');
		}
		public function reset_search(){
			$this->session->unset_userdata('filter-acctmemorialjournal');
			redirect('memorial-journal');
		}

		public function printNoteAcctMemorialJournal(){
			$auth = $this->session->userdata('auth');
			$savings_account_id = $this->uri->segment(3);
			$acctsavingsaccount	= $this->AcctMemorialJournal_model->getAcctMemorialJournal_Detail($savings_account_id);


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

			$tbl = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
			    <tr>
			        <td width=\"80%\"><div style=\"text-align: center; font-size:14px\">BUKTI SETORAN AWAL SIMPANAN</div></td>
			    </tr>
			    <tr>
			        <td width=\"80%\"><div style=\"text-align: center; font-size:14px\">Jam : ".date('H:i:s')."</div></td>
			    </tr>
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');
			

			$tbl1 = "
			Telah diterima uang dari :
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Nama</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$acctsavingsaccount['member_name']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">No. Rekening</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$acctsavingsaccount['savings_account_no']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Alamat</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$acctsavingsaccount['member_address']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Terbilang</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".numtotxt($acctsavingsaccount['savings_account_first_deposit_amount'])."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Keperluan</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: SETORAN AWAL SIMPANAN</div></td>
			    </tr>
			     <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Jumlah</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: Rp. &nbsp;".number_format($acctsavingsaccount['savings_account_first_deposit_amount'], 2)."</div></td>
			    </tr>				
			</table>";

			$tbl2 = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			    	<td width=\"30%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"20%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"30%\"><div style=\"text-align: center;\">".$this->AcctMemorialJournal_model->getBranchCity($auth['branch_id']).", ".date('d-m-Y')."</div></td>
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
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function validationAcctMemorialJournal(){
			$auth = $this->session->userdata('auth');
			$savings_account_id = $this->uri->segment(3);

			$data = array (
				'savings_account_id'  	=> $savings_account_id,
				'validation'			=> 1,
				'validation_id'			=> $auth['user_id'],
				'validation_on'			=> date('Y-m-d H:i:s'),
			);

			if($this->AcctMemorialJournal_model->validationAcctMemorialJournal($data)){
				$msg = "<div class='alert alert-success alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
							Validasi Rekening Sukses
						</div>";
				$this->session->set_userdata('message',$msg);
				redirect('memorial-journal/print-validation/'.$savings_account_id);
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'> 
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
							Validasi Rekening Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('memorial-journal');
			}
		}

		public function printValidationAcctMemorialJournal(){
			$savings_account_id = $this->uri->segment(3);
			$acctsavingsaccount	= $this->AcctMemorialJournal_model->getAcctMemorialJournal_Detail($savings_account_id);


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

			$pdf->SetFont('helveticaI', '', 7);

			// -----------------------------------------------------------------------------

			$tbl = "
			<br><br><br><br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
			    <tr>
			        <td width=\"55%\"><div style=\"text-align: right; font-size:14px\">".$acctsavingsaccount['savings_account_no']."</div></td>
			        <td width=\"40%\"><div style=\"text-align: right; font-size:14px\">".$acctsavingsaccount['member_name']."</div></td>
			        <td width=\"5%\"><div style=\"text-align: right; font-size:14px\">".$acctsavingsaccount['office_id']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"52%\"><div style=\"text-align: right; font-size:14px\">".$acctsavingsaccount['validation_on']."</div></td>
			        <td width=\"18%\"><div style=\"text-align: right; font-size:14px\">".$this->AcctMemorialJournal_model->getUsername($acctsavingsaccount['validation_id'])."</div></td>
			        <td width=\"30%\"><div style=\"text-align: right; font-size:14px\"> IDR &nbsp; ".number_format($acctsavingsaccount['savings_account_first_deposit_amount'], 2)."</div></td>
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

		public function voidAcctMemorialJournal(){
			$data['main_view']['membergender']				= $this->configuration->MemberGender();
			$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();
			$data['main_view']['acctsavingsaccount']		= $this->AcctMemorialJournal_model->getAcctMemorialJournal_Detail($this->uri->segment(3));
			$data['main_view']['content']			= 'AcctMemorialJournal/FormVoidAcctMemorialJournal_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processVoidAcctMemorialJournal(){
			$auth	= $this->session->userdata('auth');

			$newdata = array (
				"savings_account_id"	=> $this->input->post('savings_account_id',true),
				"voided_on"				=> date('Y-m-d H:i:s'),
				'data_state'			=> 2,
				"voided_remark" 		=> $this->input->post('voided_remark',true),
				"voided_id"				=> $auth['user_id']
			);
			
			$this->form_validation->set_rules('voided_remark', 'Keterangan', 'required');

			if($this->form_validation->run()==true){
				if($this->AcctMemorialJournal_model->voidAcctMemorialJournal($newdata)){
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Pembatalan Rekening Sukses
							</div>";
					$this->session->set_userdata('message',$msg);
					redirect('memorial-journal');
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Pembatalan Rekening Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('memorial-journal');
				}
					
			}else{
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('memorial-journal');
			}
		}		
	}
?>