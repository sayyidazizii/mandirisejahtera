<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctLedgerReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctLedgerReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['acctaccount']		= create_double($this->AcctLedgerReport_model->getAcctAccount(),'account_id','account_code');
			$data['main_view']['corebranch']		= create_double($this->AcctLedgerReport_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']			= 'AcctLedgerReport/ListAcctLedgerReport_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processPrinting(){
			$auth 	=	$this->session->userdata('auth'); 
			$sesi = array (
				"start_date" 		=> tgltodb($this->input->post('start_date',true)),
				"end_date" 			=> tgltodb($this->input->post('end_date',true)),
				"account_id" 		=> $this->input->post('account_id',true),
				"branch_id"			=> $this->input->post('branch_id',true),
			);

			if(empty($sesi['branch_id'])){
				$branch_id = $auth['branch_id'];
			} else {
				$branch_id = $sesi['branch_id'];
			}


			$accountbalancedetail	= $this->AcctLedgerReport_model->getAcctAccountBalanceDetail($sesi['account_id'], $sesi['start_date'], $sesi['end_date'], $branch_id);

			$opening_date = $this->AcctLedgerReport_model->getOpeningDate($sesi['account_id'], $sesi['start_date'], $sesi['end_date'], $branch_id);

			$opening_balance = $this->AcctLedgerReport_model->getOpeningBalance($opening_date, $sesi['account_id'], $branch_id);

			if(empty($opening_balance)){
				$opening_date = $this->AcctLedgerReport_model->getLastDate($sesi['account_id'], $sesi['start_date'], $sesi['end_date'], $branch_id);

				$opening_balance = $this->AcctLedgerReport_model->getLastBalance($opening_date, $sesi['account_id'], $branch_id);
			}

			$account_id_status = $this->AcctLedgerReport_model->getAccountIDDefaultStatus($sesi['account_id']);

			// print_r($accountbalancedetail);exit;


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new tcpdf('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(7, 7, 7, 7); // put space of 10 on top

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

			$tbl = "
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
				    <tr>
				        <td><div style=\"text-align: center; font-size:14px\">LAPORAN BUKU BESAR (LEDGER)</div></td>
				    </tr>
				    <tr>
				        <td><div style=\"text-align: center; font-size:10px\">Periode ".tgltoview($sesi['start_date'])." S.D. ".tgltoview($sesi['end_date'])."</div></td>
				    </tr>
				</table>";

			$tbl1 = "
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
				    <tr>
				        <td width=\"10%\"><div style=\"text-align: left; font-size:12px\">No. Rek</div></td>
				        <td width=\"25%\"><div style=\"text-align: left; font-size:12px\">".$this->AcctLedgerReport_model->getAccountCode($sesi['account_id'])."</div></td>
				        <td width=\"10%\"><div style=\"text-align: left; font-size:12px\">Saldo</div></td>
				        <td><div style=\"text-align: left; font-size:12px\">".number_format($opening_balance, 2)."</div></td>
				    </tr>
				    <tr>
				        <td width=\"10%\"><div style=\"text-align: left; font-size:12px\">Nama</div></td>
				        <td><div style=\"text-align: left; font-size:12px\">".$this->AcctLedgerReport_model->getAccountName($sesi['account_id'])."</div></td>
				    </tr>
				</table>";

			$pdf->writeHTML($tbl.$tbl1, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Tanggal</div></td>
			        <td width=\"40%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Uraian</div></td>
			        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Debit (Rp)</div></td>
			        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Kredit (Rp)</div></td>
			       
			    </tr>				
			</table>";

			$no = 1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
		
			foreach ($accountbalancedetail as $key => $val) {
				$description = $this->AcctLedgerReport_model->getJournalVoucherDescription($val['transaction_id'], $val['account_id']);
				// print_r($acctcreditspayment);exit;

				if($account_id_status == 1){
					$tbl3 .= "
						<tr>
					    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
					        <td width=\"10%\"><div style=\"text-align: left;\">".tgltoview($val['transaction_date'])."</div></td>
					        <td width=\"40%\"><div style=\"text-align: left;\">".$description."</div></td>
					        <td width=\"20%\"><div style=\"text-align: right;\">".number_format($val['account_in'], 2)."</div></td>
					        <td width=\"20%\"><div style=\"text-align: right;\">".number_format($val['account_out'], 2)."</div></td>
					    </tr>
					";

					$totaldebit += $val['account_in'];
					$totalkredit += $val['account_out'];
					$sisasaldo = ($opening_balance + $totaldebit) - $totalkredit;
					$no++;

					$tbl4 = "
						<tr>
							<td colspan =\"3\"><div style=\"font-size:10;text-align:right;font-style:italic\">Jumlah Mutasi</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totaldebit, 2)."</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalkredit, 2)."</div></td>
							
						</tr>
						<tr>
							<td colspan =\"3\"><div style=\"font-size:10;text-align:right;font-style:italic\">Saldo Akhir</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($sisasaldo, 2)."</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\"></div></td>
							
						</tr>
									
					</table>";
				} else {
					$tbl3 .= "
						<tr>
					    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
					        <td width=\"10%\"><div style=\"text-align: left;\">".tgltoview($val['transaction_date'])."</div></td>
					        <td width=\"40%\"><div style=\"text-align: left;\">".$description."</div></td>
					        <td width=\"20%\"><div style=\"text-align: right;\">".number_format($val['account_in'], 2)."</div></td>
					        <td width=\"20%\"><div style=\"text-align: right;\">".number_format($val['account_out'], 2)."</div></td>
					    </tr>
					";

					$totaldebit += $val['account_in'];
					$totalkredit += $val['account_out'];
					$sisasaldo = ($opening_balance + $totaldebit) - $totalkredit;
					$no++;

					$tbl4 = "
						<tr>
							<td colspan =\"3\"><div style=\"font-size:10;text-align:right;font-style:italic\">Jumlah Mutasi</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totaldebit, 2)."</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalkredit, 2)."</div></td>
							
						</tr>
						<tr>
							<td colspan =\"3\"><div style=\"font-size:10;text-align:right;font-style:italic\">Saldo Akhir</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($sisasaldo, 2)."</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\"></div></td>
							
						</tr>
									
					</table>";
				}

				
				
			}

			
			


			

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

		public function cashTellerReport(){
			$data['main_view']['corebranch']		= create_double($this->AcctLedgerReport_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']			= 'AcctLedgerReport/ListAcctLedgerReportTeller_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processPrintingCashTellerReport(){
			$auth 	=	$this->session->userdata('auth'); 
			$sesi = array (
				"start_date" 		=> tgltodb($this->input->post('start_date',true)),
				"branch_id"			=> $this->input->post('branch_id',true),
			);

			if(empty($sesi['branch_id'])){
				$branch_id = $auth['branch_id'];
			} else {
				$branch_id = $sesi['branch_id'];
			}

			$preferencecompany = $this->AcctLedgerReport_model->getPreferenceCompany();


			$accountbalancedetail	= $this->AcctLedgerReport_model->getAcctAccountBalanceDetailTeller($preferencecompany['account_cash_id'], $sesi['start_date'], $branch_id);

			$opening_date = $this->AcctLedgerReport_model->getOpeningDateTeller($preferencecompany['account_cash_id'], $sesi['start_date'], $branch_id);

			$opening_balance = $this->AcctLedgerReport_model->getOpeningBalanceTeller($opening_date, $preferencecompany['account_cash_id'], $branch_id);

			if(!is_array($opening_balance)){
				$opening_date = $this->AcctLedgerReport_model->getLastDateTeller($preferencecompany['account_cash_id'], $sesi['start_date'], $branch_id);

				$opening_balance = $this->AcctLedgerReport_model->getLastBalanceTeller($opening_date, $preferencecompany['account_cash_id'], $branch_id);
			}

			$account_id_status = $this->AcctLedgerReport_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

			// print_r($accountbalancedetail);exit;


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new tcpdf('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

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
				        <td><div style=\"text-align: center; font-size:14px\">LAPORAN ARUS KAS HARIAN</div></td>
				    </tr>
				    <tr>
				        <td><div style=\"text-align: center; font-size:10px\">Per ".tgltoview($sesi['start_date'])."</div></td>
				    </tr>
				</table>";

			$tbl1 = "
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
				    <tr>
				        <td width=\"10%\"><div style=\"text-align: left; font-size:12px\">No. Rek</div></td>
				        <td width=\"25%\"><div style=\"text-align: left; font-size:12px\">".$this->AcctLedgerReport_model->getAccountCode($preferencecompany['account_cash_id'])."</div></td>
				        <td width=\"10%\"><div style=\"text-align: left; font-size:12px\">Saldo</div></td>
				        <td><div style=\"text-align: left; font-size:12px\">".number_format($opening_balance, 2)."</div></td>
				    </tr>
				    <tr>
				        <td width=\"10%\"><div style=\"text-align: left; font-size:12px\">Nama</div></td>
				        <td><div style=\"text-align: left; font-size:12px\">".$this->AcctLedgerReport_model->getAccountName($preferencecompany['account_cash_id'])."</div></td>
				    </tr>
				</table>";

			$pdf->writeHTML($tbl.$tbl1, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Tanggal</div></td>
			        <td width=\"40%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Uraian</div></td>
			        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Debit (Rp)</div></td>
			        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Kredit (Rp)</div></td>
			       
			    </tr>				
			</table>";

			$no = 1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
		
			foreach ($accountbalancedetail as $key => $val) {
				$description = $this->AcctLedgerReport_model->getJournalVoucherDescription($val['transaction_id'], $preferencecompany['account_cash_id']);
				// print_r($acctcreditspayment);exit;

				if($account_id_status == 1){
					$tbl3 .= "
						<tr>
					    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
					        <td width=\"10%\"><div style=\"text-align: left;\">".tgltoview($val['transaction_date'])."</div></td>
					        <td width=\"40%\"><div style=\"text-align: left;\">".$description."</div></td>
					        <td width=\"20%\"><div style=\"text-align: right;\">".number_format($val['account_out'], 2)."</div></td>
					        <td width=\"20%\"><div style=\"text-align: right;\">".number_format($val['account_in'], 2)."</div></td>
					    </tr>
					";

					$totaldebit += $val['account_out'];
					$totalkredit += $val['account_in'];
					$sisasaldo = ($opening_balance + $totaldebit) - $totalkredit;
					$no++;

					$tbl4 = "
						<tr>
							<td colspan =\"3\"><div style=\"font-size:10;text-align:right;font-style:italic\">Jumlah Mutasi</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totaldebit, 2)."</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalkredit, 2)."</div></td>
							
						</tr>
						<tr>
							<td colspan =\"3\"><div style=\"font-size:10;text-align:right;font-style:italic\">Saldo Akhir</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($sisasaldo, 2)."</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\"></div></td>
							
						</tr>
									
					</table>";
				} else {
					$tbl3 .= "
						<tr>
					    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
					        <td width=\"10%\"><div style=\"text-align: left;\">".tgltoview($val['transaction_date'])."</div></td>
					        <td width=\"40%\"><div style=\"text-align: left;\">".$description."</div></td>
					        <td width=\"20%\"><div style=\"text-align: right;\">".number_format($val['account_in'], 2)."</div></td>
					        <td width=\"20%\"><div style=\"text-align: right;\">".number_format($val['account_out'], 2)."</div></td>
					    </tr>
					";

					$totaldebit += $val['account_in'];
					$totalkredit += $val['account_out'];
					$sisasaldo = ($opening_balance + $totaldebit) - $totalkredit;
					$no++;

					$tbl4 = "
						<tr>
							<td colspan =\"3\"><div style=\"font-size:10;text-align:right;font-style:italic\">Jumlah Mutasi</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totaldebit, 2)."</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalkredit, 2)."</div></td>
							
						</tr>
						<tr>
							<td colspan =\"3\"><div style=\"font-size:10;text-align:right;font-style:italic\">Saldo Akhir</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($sisasaldo, 2)."</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\"></div></td>
							
						</tr>
									
					</table>";
				}

				
				
			}

			
			


			

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

	}
?>