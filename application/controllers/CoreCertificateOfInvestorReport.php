<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class CoreCertificateOfInvestorReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('CoreCertificateOfInvestorReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			// $sesi	= 	$this->session->userdata('filter-corecertificatecfinvestorreport');
			// if(!is_array($sesi)){
			// 	$sesi['start_date']					= date('Y-m-d');
			// 	$sesi['end_date']					= date('Y-m-d');
			// 	$sesi['kelompok_laporan_simpanan']	= 0;
			// }

			$data['main_view']['coremember']		= $this->CoreCertificateOfInvestorReport_model->getCoreMember();

			$data['main_view']['content']			= 'CoreCertificateOfInvestorReport/ListCoreMember_view';
			$this->load->view('MainPage_view',$data);
		}

		// public function filter(){
		// 	$data = array (
		// 		"start_date" 				=> tgltodb($this->input->post('start_date',true)),
		// 		"end_date" 					=> tgltodb($this->input->post('end_date',true)),
		// 		"kelompok_laporan_simpanan"	=> $this->input->post('kelompok_laporan_simpanan',true),
		// 	);

		// 	$this->session->set_userdata('filter-corecertificatecfinvestorreport',$data);
		// 	redirect('CoreCertificateOfInvestorReport');
		// }

		public function addCertificatOfInvestorReport(){
			$member_id = $this->uri->segment(3);

			$data['main_view']['coremember']		= $this->CoreCertificateOfInvestorReport_model->getCoreMember_Detail($member_id);

			$data['main_view']['content']			= 'CoreCertificateOfInvestorReport/FormAddCoreCertificateOfInvestorReport_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processPrinting(){
			$auth 	=	$this->session->userdata('auth'); 

			$member_id 	= $this->input->post('member_id', true);
			$pengurus	= $this->input->post('pengurus', true);
			$pengelola	= $this->input->post('pengelola', true);


			$coremember	= $this->CoreCertificateOfInvestorReport_model->getCoreMember_Detail($member_id);
			$preference	= $this->CoreCertificateOfInvestorReport_model->getPreferenceCompany();


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new tcpdf('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

			// set document information
			/*$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('');
			$pdf->SetTitle('');
			$pdf->SetSubject('');
			$pdf->SetKeywords('tcpdf, PDF, example, test, guide');*/

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

			$pdf->SetMargins(20, 40, 20, 20); // put space of 10 on top
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

			$pdf->SetFont('helvetica', '', 9);

			// -----------------------------------------------------------------------------
			

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td><div style=\"text-align: right;font-size:10;\">Sertifikat No. : 0001/SPM/01/2016</div></td>			       
			    </tr>	
			    <tr>
			        <td><div style=\"text-align: center;\"><img src=\"".base_url()."img/".$preference['logo_koperasi']."\" style=\"width:92px;height:90px\"></div></td>			       
			    </tr>

			    <br><br>

			    <tr>
			        <td><div style=\"text-align: center;font-size:12;\">Koperasi Konsumen</div></td>		
			       	       
			    </tr>
			     <br>
			    <tr>
			        <td><div style=\"text-align: center;font-size:10;\">Yang Berkedudukan di :</div></td>		       
			    </tr>
			     <tr>
			        <td><div style=\"text-align: center;font-size:12;\">BMT INDONESIA</div></td>		       
			    </tr>					
			</table>
			<br><br><br>";

			$tbl2 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" >
			    <tr>
			    	<td width=\"25%\"></td>
			        <td width=\"50%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black;border-left: 1px solid black;border-right: 1px solid black\"><div style=\"text-align: center;font-size:12;font-weight:bold\">SERTIFIKAT MODAL PENYERTAAN </div>
			        </td>
			        <td width=\"25%\"></td>
			    </tr>					
			</table>
			<br><br><br><br><br>";

			$tbl3 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td><div style=\"text-align: center;font-size:10;\">Dengan Setoran Modal Penyertaan :</div></td>		       
			    </tr>
			     <tr>
			        <td><div style=\"text-align: center;font-size:12;font-weight:bold\">Rp. ".number_format($coremember['member_special_savings'], 2)."</div></td>		       
			    </tr>
			    <br>
			    <tr>
			        <td><div style=\"text-align: center;font-size:12;font-weight:bold\">( ".numtotxt($coremember['member_special_savings'])." )</div></td>		       
			    </tr>
			    <br>
			    <tr>
			        <td><div style=\"text-align: center;font-size:10;\">Sebagaimana telah tercatat dalam Buku Daftar Anggota</div></td>		       
			    </tr>					
			</table>
			<br><br>";

			$tbl4 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" >
			    <tr>
			    	<td width=\"15%\"></td>
			    	<td width=\"15%\"><div style=\"text-align: left;font-size:10;\">Atas Nama</div></td>
			    	<td width=\"5%\"><div style=\"text-align: left;font-size:10;\">:</div></td>
			        <td width=\"55%\"><div style=\"text-align: left;font-size:10;\">".$coremember['member_name']." </div></td>
			    </tr>
			    <tr>
			    	<td width=\"15%\"></td>
			    	<td width=\"15%\"><div style=\"text-align: left;font-size:10;\">Alamat</div></td>
			    	<td width=\"5%\"><div style=\"text-align: left;font-size:10;\">:</div></td>
			        <td width=\"55%\"><div style=\"text-align: left;font-size:10;\">".$coremember['member_address']." </div></td>
			    </tr>
			    <tr>
			    	<td width=\"15%\"></td>
			    	<td width=\"15%\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
			    	<td width=\"5%\"><div style=\"text-align: left;font-size:10;\">:</div></td>
			        <td width=\"55%\"><div style=\"text-align: left;font-size:10;\">".$coremember['member_no']." </div></td>
			    </tr>					
			</table>
			<br><br><br><br>";

			$tbl5 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" >
			    <tr>
			    	<td width=\"15%\"><div style=\"text-align: center;font-size:10;\"></div></td>
			    	<td width=\"25%\"><div style=\"text-align: center;font-size:10;\"></div></td>
			    	<td width=\"20%\"><div style=\"text-align: center;font-size:10;\"></div></td>
			        <td width=\"25%\"><div style=\"text-align: center;font-size:10;\">".$this->CoreCertificateOfInvestorReport_model->getBranchCity($auth['branch_id']).", ".date('d-m-Y')."</div></td>
			        <td width=\"5%\"><div style=\"text-align: center;font-size:10;\"></div></td>
			    </tr>
			    <tr>
			    	<td width=\"15%\"><div style=\"text-align: center;font-size:10;\"></div></td>
			    	<td width=\"25%\" height=\"90px\"><div style=\"text-align: center;font-size:10;font-weight:bold\">PENGURUS </div></td>
			    	<td width=\"20%\"><div style=\"text-align: center;font-size:10;\"></div></td>
			        <td width=\"25%\" height=\"90px\"><div style=\"text-align: center;font-size:10;font-weight:bold\">PENGELOLA</div></td>
			        <td width=\"5%\"><div style=\"text-align: center;font-size:10;\"></div></td>
			    </tr>
			    <tr>
			    	<td width=\"15%\"><div style=\"text-align: center;font-size:10;\"></div></td>
			    	<td width=\"25%\" style=\"border-bottom: 1px solid black\"><div style=\"text-align: center;font-size:10;font-weight:bold\">".$pengurus."</div></td>
			    	<td width=\"20%\"><div style=\"text-align: center;font-size:10;\"></div></td>
			        <td width=\"25%\" style=\"border-bottom: 1px solid black\"><div style=\"text-align: center;font-size:10;font-weight:bold\">".$pengelola."</div></td>
			        <td width=\"5%\"><div style=\"text-align: center;font-size:10;\"></div></td>
			    </tr>
			    <tr>
			    	<td width=\"15%\"><div style=\"text-align: center;font-size:10;\"></div></td>
			    	<td width=\"25%\"><div style=\"text-align: center;font-size:10;\">KETUA </div></td>
			    	<td width=\"20%\"><div style=\"text-align: center;font-size:10;\"></div></td>
			        <td width=\"25%\"><div style=\"text-align: center;font-size:10;\">MANAGER</div></td>
			        <td width=\"5%\"><div style=\"text-align: center;font-size:10;\"></div></td>
			    </tr>
			    				
			</table>
			<br><br><br><br>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4.$tbl5, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Kwitansi.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

	}
?>