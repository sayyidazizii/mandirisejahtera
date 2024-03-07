<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctSavingsProfitSharingReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctSavingsProfitSharingReport_model');
			$this->load->model('AcctSavingsAccount_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		// public function index(){
		// 	$data['main_view']['acctsavingsprofitsharing']	= $this->AcctSavingsProfitSharingReport_model->getAcctSavingsProfitSharing();	
		// 	$data['main_view']['preference']				= $this->AcctSavingsProfitSharingReport_model->getPreferenceCompany();
		// 	$data['main_view']['content']					= 'AcctSavingsProfitSharingReport/ListAcctSavingsProfitSharingReport_view';
		// 	$this->load->view('MainPage_view',$data);
		// }

		public function index(){
			$auth 	= $this->session->userdata('auth');

			$date 	= date('Y-m-d');
			$month 	= date('m', strtotime($date));
			$year 	= date('Y', strtotime($date));

			if($month == 1){
				$month 	= 12;
				$year 	= $year - 1;
			} else {
				$month 	= $month - 1;
				$year 	= $year;
			}

			$period = $month.$year;

			$acctsavingsprofitsharing 	= $this->AcctSavingsProfitSharingReport_model->getAcctSavingsProfitSharing($period);
			$preference					= $this->AcctSavingsProfitSharingReport_model->getPreferenceCompany();


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

			$pdf->SetFont('helvetica', '', 9);

			// -----------------------------------------------------------------------------
			$base_url = base_url();
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preference['logo_koperasi']."\" alt=\"\" width=\"700%\" height=\"300%\"/>";

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
			        <td width=\"100%\"><div style=\"text-align: left; font-size:14px; font-weight:bold\">DAFTAR BUNGA TABUNGAN SIMPANAN BULAN INI</div></td>
			    </tr>
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			    	<td width=\"3%\"><div style=\"text-align: center;border-bottom: 1px solid black;border-top: 1px solid black\">No</div></td>
			        <td width=\"15%\"><div style=\"text-align: center;border-bottom: 1px solid black;border-top: 1px solid black\">No. Rekening</div></td>
			        <td width=\"25%\"><div style=\"text-align: center;border-bottom: 1px solid black;border-top: 1px solid black\">Nama</div></td>
			        <td width=\"7%\"><div style=\"text-align: center;border-bottom: 1px solid black;border-top: 1px solid black\">Sandi</div></td>
			        <td width=\"20%\"><div style=\"text-align: center;border-bottom: 1px solid black;border-top: 1px solid black\">Nominal</div></td>
			        <td width=\"20%\"><div style=\"text-align: center;border-bottom: 1px solid black;border-top: 1px solid black\">Saldo</div></td>
			    </tr>			
			</table>";

			$no = 1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
			foreach ($acctsavingsprofitsharing as $key => $val) {
				$tbl3 .= "
					<tr>
				    	<td width=\"3%\"><div style=\"text-align: left;\">$no</div></td>
				        <td width=\"15%\"><div style=\"text-align: left;\">".$val['savings_account_no']."</div></td>
				        <td width=\"25%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
				        <td width=\"7%\"><div style=\"text-align: center;\">".$this->AcctSavingsProfitSharingReport_model->getMutationCode($preference['savings_profit_sharing_id'])."</div></td>
				        <td width=\"20%\"><div style=\"text-align: right;\">".number_format($val['savings_profit_sharing_amount'], 2)."</div></td>
				        <td width=\"20%\"><div style=\"text-align: right;\">".number_format($val['savings_account_last_balance'], 2)."</div></td>
				    </tr>
				";

				$total += $val['savings_profit_sharing_amount'];

				$no++;
			}
			
			
			$tbl4 = "
					<tr>
						<td colspan =\"3\" style=\"border-top: 1px solid black;\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctSavingsProfitSharingReport_model->getUserName($auth['user_id'])."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Jumlah </div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($total, 2)."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\"></div></td>
						
					</tr>
							
			</table>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Kwitansi.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function function_state_add(){
			$unique 	= $this->session->userdata('unique');
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctsavingsaccount-'.$unique['unique']);
			$sessions['active_tab'] = $value;
			$this->session->set_userdata('addacctsavingsaccount-'.$unique['unique'],$sessions);
		}	
		
	}
?>