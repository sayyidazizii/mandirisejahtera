<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctCreditsCollectibility extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctCreditsCollectibility_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}

		public function index(){
			$auth 	= $this->session->userdata('auth'); 

			$preferencecollectibility 	= $this->AcctCreditsCollectibility_model->getPreferenceCollectibility();

			$acctcreditsaccount			= $this->AcctCreditsCollectibility_model->getCreditsAccount();

			$preferencecompany			= $this->AcctCreditsCollectibility_model->getPreferenceCompany();

			//$acctcreditsaccountdetail			= $this->AcctCreditsCollectibility_model->getCreditsAccount_Detail($);


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new tcpdf('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(7, 7, 7, 7); 

			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

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
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
				    <tr>
				        <td><div style=\"text-align: center; font-size:14px\">KOLEKTIBILITAS PINJAMAN</div></td>
				    </tr>
				</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"3%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No. Akad</div></td>
			        <td width=\"18%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nama</div></td>
			        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Alamat</div></td>
			        <td width=\"16%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Outstanding</div></td>
			        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Tenor</div></td>
			       
			        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Kolektibilitas</div></td>
			       
			    </tr>				
			</table>";

			$no = 1;
			$total1 = 0;
			$total2 = 0;
			$total3 = 0;
			$total4 = 0;
			$total5 = 0;
			$totaloutstanding=0;
			$date 	= date('Y-m-d');

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
				
			foreach ($acctcreditsaccount as $key => $val) {
				$date1 = new DateTime($date);
				$date2 = new DateTime($val['credits_account_payment_date']);

				$interval    = $date1->diff($date2);
    			$tunggakan   = $interval->days;
				if($date2 >= $date1){
					$tunggakan2 = 0;
				}else{
					$tunggakan2 = $tunggakan;
				}
    			
				
    			
				foreach ($preferencecollectibility as $k => $v) {
					if($tunggakan2 >= $v['collectibility_bottom'] && $tunggakan2 <= $v['collectibility_top']){
						$collectibility = $v['collectibility_id'];
						$collectibility_name = $v['collectibility_name'];
					}
				}


				$credits_account_payment_to = ($val['credits_account_payment_to'] + 1); 
				
				
				$tbl3 .= "
					<tr>
				    	<td width=\"3%\"><div style=\"text-align: left;\">".$no."</div></td>
				        <td width=\"10%\"><div style=\"text-align: left;\">".$val['credits_account_serial']."</div></td>
				        <td width=\"18%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
				        <td width=\"20%\"><div style=\"text-align: left;\">".$val['member_address']."</div></td>
				        <td width=\"12%\"><div style=\"text-align: right;\">".number_format($val['credits_account_last_balance'], 2)."</div></td>
				        <td width=\"10%\"><div style=\"text-align: right;\">".$credits_account_payment_to." / ".$val['credits_account_period']."</div></td>
				        
				       	<td width=\"15%\"><div style=\"text-align: right;\">".$collectibility_name."</div></td>

				    </tr>
				";

				$no++;

				if($collectibility == 1){
					$total1 = $total1 + $val['credits_account_last_balance'];
				} else if($collectibility == 2){
					$total2 = $total2 + $val['credits_account_last_balance'];
				} else if($collectibility == 3){
					$total3 = $total3 + $val['credits_account_last_balance'];
				} else if($collectibility == 4){
					$total4 = $total4 + $val['credits_account_last_balance'];
				} else if($collectibility == 5){
					$total5 = $total5 + $val['credits_account_last_balance'];
				}

				$totaloutstanding += $val['credits_account_last_balance'];
				// $totalmargin += $val['credits_account_last_balance_margin'];
			}			
			//exit; 

			$tbl4 = "
				<tr>
					<td colspan =\"3\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctCreditsCollectibility_model->getUserName($auth['user_id'])."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;font-weight:bold;text-align:center\">Total </div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totaloutstanding, 2)."</div></td>
					
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\"></div></td>
				</tr>
							
			</table>";
			
			

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');

			$tbl5 = "
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
				    <tr>
				        <td width=\"15%\"><div style=\"text-align: left; font-size:12px; font-weight:bold\">REKAPITULASI :</div></td>
				        <td width=\"20%\"><div style=\"text-align: left; font-size:12px; font-weight:bold\"></div></td>
				        <td width=\"20%\"><div style=\"text-align: left; font-size:12px; font-weight:bold\"></div></td>
				    </tr>
				";

			foreach ($preferencecollectibility as $k => $v) {
				if($v['collectibility_id'] == 1){
					$persent1 	= ($total1 / $totaloutstanding) * 100;
					$ppap1 		= ($total1 * $v['collectibility_ppap']) / 100;
					$tbl6 .= "
						<tr>
					        <td width=\"15%\"><div style=\"text-align: left; font-size:12px;\">JUMLAH KOLEKT ".$v['collectibility_id']."</div></td>
					        <td width=\"20%\"><div style=\"text-align: right; font-size:12px;\"> ".number_format($total1, 2)." ( ".number_format($persent1, 2)." ) % &nbsp;&nbsp;</div></td>
					        <td width=\"15%\"><div style=\"text-align: left; font-size:12px;\">".$v['collectibility_name']."</div></td>
					        <td width=\"10%\"><div style=\"text-align: left; font-size:12px;\">PPAP (".$v['collectibility_ppap']." %)</div></td>
					        <td width=\"15%\"><div style=\"text-align: right; font-size:12px;\"> ".number_format($ppap1, 2)."</div></td>
					    </tr>
					";
				} else if($v['collectibility_id'] == 2){
					$persent2 	= ($total2 / $totaloutstanding) * 100;
					$ppap2 		= ($total2 * $v['collectibility_ppap']) / 100;
					$tbl6 .= "
						<tr>
					        <td width=\"15%\"><div style=\"text-align: left; font-size:12px;\">JUMLAH KOLEKT ".$v['collectibility_id']."</div></td>
					        <td width=\"20%\"><div style=\"text-align: right; font-size:12px;\"> ".number_format($total2, 2)." ( ".number_format($persent2, 2)." ) % &nbsp;&nbsp;</div></td>
					        <td width=\"15%\"><div style=\"text-align: left; font-size:12px;\">".$v['collectibility_name']."</div></td>
					        <td width=\"10%\"><div style=\"text-align: left; font-size:12px;\">PPAP (".$v['collectibility_ppap']." %)</div></td>
					        <td width=\"15%\"><div style=\"text-align: right; font-size:12px;\"> ".number_format($ppap2, 2)."</div></td>
					    </tr>
					";
				} else if($v['collectibility_id'] == 3){
					$persent3 	= ($total3 / $totaloutstanding) * 100;
					$ppap3 		= ($total3 * $v['collectibility_ppap']) / 100;
					$tbl6 .= "
						<tr>
					        <td width=\"15%\"><div style=\"text-align: left; font-size:12px;\">JUMLAH KOLEKT ".$v['collectibility_id']."</div></td>
					        <td width=\"20%\"><div style=\"text-align: right; font-size:12px;\"> ".number_format($total3, 2)." ( ".number_format($persent3, 2)." ) % &nbsp;&nbsp;</div></td>
					        <td width=\"15%\"><div style=\"text-align: left; font-size:12px;\">".$v['collectibility_name']."</div></td>
					        <td width=\"10%\"><div style=\"text-align: left; font-size:12px;\">PPAP (".$v['collectibility_ppap']." %)</div></td>
					        <td width=\"15%\"><div style=\"text-align: right; font-size:12px;\"> ".number_format($ppap3, 2)."</div></td>
					    </tr>
					";
				} else if($v['collectibility_id'] == 4){
					$persent4 	= ($total4 / $totaloutstanding) * 100;
					$ppap4 		= ($total4 * $v['collectibility_ppap']) / 100;
					$tbl6 .= "
						<tr>
					        <td width=\"15%\"><div style=\"text-align: left; font-size:12px;\">JUMLAH KOLEKT ".$v['collectibility_id']."</div></td>
					        <td width=\"20%\"><div style=\"text-align: right; font-size:12px;\"> ".number_format($total4, 2)." ( ".number_format($persent4, 2)." ) % &nbsp;&nbsp;</div></td>
					        <td width=\"15%\"><div style=\"text-align: left; font-size:12px;\">".$v['collectibility_name']."</div></td>
					        <td width=\"10%\"><div style=\"text-align: left; font-size:12px;\">PPAP (".$v['collectibility_ppap']." %)</div></td>
					        <td width=\"15%\"><div style=\"text-align: right; font-size:12px;\"> ".number_format($ppap4, 2)."</div></td>
					    </tr>
					";
				} else if($v['collectibility_id'] == 5){
					$persent5 	= ($total5 / $totaloutstanding) * 100;
					$ppap5 		= ($total5 * $v['collectibility_ppap']) / 100;
					$tbl6 .= "
						<tr>
					        <td width=\"15%\"><div style=\"text-align: left; font-size:12px;\">JUMLAH KOLEKT ".$v['collectibility_id']."</div></td>
					        <td width=\"20%\"><div style=\"text-align: right; font-size:12px;\"> ".number_format($total5, 2)." ( ".number_format($persent5, 2)." ) % &nbsp;&nbsp;</div></td>
					        <td width=\"15%\"><div style=\"text-align: left; font-size:12px;\">".$v['collectibility_name']."</div></td>
					        <td width=\"10%\"><div style=\"text-align: left; font-size:12px;\">PPAP (".$v['collectibility_ppap']." %)</div></td>
					        <td width=\"15%\"><div style=\"text-align: right; font-size:12px;\"> ".number_format($ppap5, 2)."</div></td>
					    </tr>
					";
				}
				
			}

			$tbl7 = "
				</table>
			";

			$pdf->writeHTML($tbl5.$tbl6.$tbl7, true, false, false, false, '');


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