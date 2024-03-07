<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctCreditsAccountPaidOff extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctCreditsAccountPaidOff_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}

		public function index(){
			
			$auth 	=	$this->session->userdata('auth'); 

			$start_date	= tgltodb($this->input->post('start_date', true));

			$preferencecompany = $this->AcctCreditsAccountPaidOff_model->getPreferenceCompany();


			$acctcreditspayment	= $this->AcctCreditsAccountPaidOff_model->getAcctCreditsAccount($start_date);


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new tcpdf('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

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
			/*$pdf->SetMargins(PDF_BUNGA_LEFT, PDF_BUNGA_TOP, PDF_BUNGA_RIGHT);*/

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(7, 7, 7, 7); // put space of 10 on top
			/*$pdf->SetHeaderMargin(PDF_BUNGA_HEADER);*/
			/*$pdf->SetFooterMargin(PDF_BUNGA_FOOTER);*/

			// set auto page breaks
			/*$pdf->SetAutoPageBreak(TRUE, PDF_BUNGA_BOTTOM);*/

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
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">

			    <tr>
			        <td><div style=\"text-align: left;font-size:12;font-weight:bold\">DAFTAR NASABAH PINJAMAN YANG SUDAH LUNAS</div></td>		
			       	       
			    </tr>					
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">NO.</div></td>
			        <td width=\"12%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">NO. AKAD</div></td>
			        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">NAMA</div></td>
			        <td width=\"18%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">ALAMAT</div></td>
			        <td width=\"13%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">POKOK</div></td>
			        <td width=\"13%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">BUNGA</div></td>
			        <td width=\"13%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">SALDO POKOK</div></td>			       
			    </tr>				
			</table>";

			$no = 1;
			$totalpokok = 0;
			$totalmargin = 0;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";

			foreach ($acctcreditspayment as $key => $val) {
				$tbl3 .= "
					<tr>
				    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
				        <td width=\"12%\"><div style=\"text-align: left;\">".$val['credits_account_serial']."</div></td>
				        <td width=\"15%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
				        <td width=\"18%\"><div style=\"text-align: left;\">".$val['member_address']."</div></td>
				        <td width=\"13%\"><div style=\"text-align: right;\">".number_format($val['credits_account_principal_amount'])."</div></td>
				        <td width=\"13%\"><div style=\"text-align: right;\">".number_format($val['credits_account_interest_amount'], 2)."</div></td>
				        <td width=\"13%\"><div style=\"text-align: right;\">".number_format($val['credits_account_last_balance'])."</div></td>
				       
				    </tr>
				";

				$totalpokok 	+= $val['credits_account_principal_amount'];
				$totalmargin 	+= $val['credits_account_interest_amount'];

				$no++;
			}
			

			$tbl4 = "
					<tr>
						<td colspan =\"3\" style=\"border-top: 1px solid black;\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctCreditsAccountPaidOff_model->getUserName($auth['user_id'])."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Jumlah </div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalpokok, 2)."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalmargin, 2)."</div></td>
						<td style=\"border-top: 1px solid black\"></td>
					</tr>
							
			</table>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'PINJAMAN LUNAS.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

	}
?>