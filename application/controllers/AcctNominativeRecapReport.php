<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctNominativeRecapReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctNominativeRecapReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('Fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$corebranch 									= create_double_branch($this->AcctNominativeRecapReport_model->getCoreBranch(),'branch_id','branch_name');
			$corebranch[0] 									= 'Semua Cabang';
			ksort($corebranch);
			$data['main_view']['corebranch']				= $corebranch;
			$data['main_view']['kelompoklaporansimpanan']	= $this->configuration->KelompokLaporanPembiayaan();
			$data['main_view']['content']					= 'AcctNominativeRecapReport/ListAcctNominativeRecapReport_View';
			$this->load->view('MainPage_view',$data);
		}
		
		public function viewreport(){
			$auth 	=	$this->session->userdata('auth'); 
			$sesi = array (
				"start_date" 					=> tgltodb($this->input->post('start_date',true)),
				"end_date" 						=> tgltodb($this->input->post('end_date',true)),
				"view"							=> $this->input->post('view',true),
			);

			
			if($sesi['view'] == 'pdf'){
				$this->processPrinting($sesi);
			}
		}

		public function processPrinting($sesi){
			$auth 						=	$this->session->userdata('auth'); 
			$branch_id 					= '';
			//savingsReport
			$preferencecompany 			= $this->AcctNominativeRecapReport_model->getPreferenceCompany();
			$kelompoklaporansimpanan	= $this->configuration->KelompokLaporanSimpanan();	
			$acctsavings 				= $this->AcctNominativeRecapReport_model->getAcctSavings();
			$period 					= date('mY', strtotime($sesi['start_date']));
			//depositeReport
			$acctdeposito 				= $this->AcctNominativeRecapReport_model->getAcctDeposito();
			//creditsReport
			$acctcredits 				= $this->AcctNominativeRecapReport_model->getAcctCredits();
			$acctsourcefund 			= $this->AcctNominativeRecapReport_model->getAcctSourceFund();

		

			foreach ($acctsavings as $key => $vS) {

				$acctsavingsaccount 		= $this->AcctNominativeRecapReport_model->getAcctNomintiveSavingsReport_Savings($vS['savings_id']);
				foreach ($acctsavingsaccount as $key => $val) {

					$acctsavingsprofitsharing 	 		= $this->AcctNominativeRecapReport_model->getAcctSavingsProfitSharing($val['savings_account_id'], $sesi['start_date'], $sesi['end_date']);

					// print_r($val);exit;

					if(empty($acctsavingsprofitsharing)){
						$savings_daily_average_balance 	= 0;
						$savings_profit_sharing_amount 	= 0;
						$savings_account_last_balance 	= $val['savings_account_last_balance'];
					} else {
						$savings_daily_average_balance 	= $acctsavingsprofitsharing['savings_daily_average_balance'];
						$savings_profit_sharing_amount 	= $acctsavingsprofitsharing['savings_profit_sharing_amount'];
						$savings_account_last_balance 	= $acctsavingsprofitsharing['savings_account_last_balance'];
					}

					$data_acctsavingsaccount[$vS['savings_id']][] = array (
						'savings_account_no'			=> $val['savings_account_no'],
						'member_name'					=> $val['member_name'],
						'member_address'				=> $val['member_address'],
						'savings_interest_rate'			=> $savingsinterestrate,
						'savings_daily_average_balance'	=> $savings_daily_average_balance,
						'savings_profit_sharing_amount'	=> $savings_profit_sharing_amount,
						'savings_account_last_balance'	=> $savings_account_last_balance,
					);
				}
			}

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

			//KOP
			$tbl0 = "
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
			<br/>";

			//Per Jenis Simpanan
			$tbl = "
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
					<tr>
						<td><div style=\"text-align: center; font-size:14px;font-weight:bold\">DAFTAR NOMINATIF SIMPANAN </div></td>
					</tr>
					<tr>
						<td><div style=\"text-align: center; font-size:10px\">Periode ".tgltoview($sesi['start_date'])." S.D. ".tgltoview($sesi['end_date'])."</div></td>
					</tr>
				</table>";
		

			$pdf->writeHTML($tbl0.$tbl, true, false, false, false, '');
			
			
			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
			        <td width=\"35%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">Jenis Simpanan</div></td>
					<td width=\"30%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Sub Total Bagi Hasil</div></td>
			        <td width=\"30%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Sub Total Saldo Akhir</div></td>     
			    </tr>				
			</table>";

			$no=1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
			
			$totalsaldo = 0;
			$subtotalbasil = 0;
			$subtotalsaldo = 0;
			$no = 1;
			foreach ($acctsavings as $key => $vS) {
				
				if(!empty($data_acctsavingsaccount[$vS['savings_id']])){
					// var_dump($data_acctsavingsaccount[$vS['savings_id']]);exit;
					
					foreach ($data_acctsavingsaccount[$vS['savings_id']] as $v) {

						$subtotalbasil += $v['savings_profit_sharing_amount'];
						$subtotalsaldo += $v['savings_account_last_balance'];
					}
					// var_dump($subtotalsaldo);exit;

					
				
					$tbl3 .= "
						
						<tr>
							<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
							<td width=\"35%\" style=\"font-weight:bold\"><div style=\"font-size:10\">".$vS['savings_name']."</div></td>
							<td width=\"30%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($subtotalbasil, 2)."</div></td>
							<td width=\"30%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($subtotalsaldo, 2)."</div></td>
						</tr>
						
					";
					$totalglobal 	+= $subtotalbasil;
					$totalsaldo 	+= $subtotalsaldo;	
				}
				$no=$no+1;
			}
			$tbl5 = "<br>
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
						<tr>
							<td width=\"30%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\"></div></td>
							<td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\"></div></td>
							<td width=\"30%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;font-weight:bold\">Saldo Akhir</div></td>
							<td width=\"30%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;font-weight:bold\">Bagi Hasil</div></td>     
						</tr>
						<tr>
							<td width=\"35%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\"></div></td>
							<td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;font-weight:bold\">Total</div></td>
							<td width=\"25%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">".number_format($totalglobal,2)."</div></td>
							<td width=\"30%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">".number_format($totalsaldo,2)."</div></td>     
						</tr>
								
					</table>";

	

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4.$tbl5, true, false, false, false, false, '');


			//depositoNormative
			$tbldeposito0 = "
			<br><br><br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
				<tr>
					<td><div style=\"text-align: center; font-size:14px;font-weight:bold\">DAFTAR NOMINATIF SIMPANAN BERJANGKA </div></td>
				</tr>
				<tr>
					<td><div style=\"text-align: center; font-size:10px\">Periode ".tgltoview($sesi['start_date'])." S.D. ".tgltoview($sesi['end_date'])."</div></td>
				</tr>
			</table>";

			$tbldeposito1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
			        <td width=\"50%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">Jenis Simpanan Berjangka</div></td>
			        <td width=\"45%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Sub Total Saldo Akhir</div></td>     
			    </tr>				
			</table>";

			foreach ($acctdeposito as $kSavings => $vSavings) {					
				$acctdepositoaccount_deposito = $this->AcctNominativeRecapReport_model->getAcctNomintiveDepositoReport_Deposito($sesi['start_date'], $sesi['end_date'], $vSavings['deposito_id']);
				
				if(!empty($acctdepositoaccount_deposito)){
					
					$subtotalperjenis = 0;
					foreach ($acctdepositoaccount_deposito as $v) {
						$subtotalperjenis += $v['deposito_account_amount'];
					}
					$tbldeposito2 .= "
					
						<tr>
							<td width=\"5%\"><div style=\"text-align: left;\">".$kSavings++."</div></td>
							<td width=\"50%\" style=\"font-weight:bold\"><div style=\"font-size:10\">".$vSavings['deposito_name']."</div></td>
							<td width=\"45%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($subtotalperjenis, 2)."</div></td>
						</tr>
						
					";
					
					$totalperjenis += $subtotalperjenis;
					
				}
			}
			$tbldeposito3 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"40%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\"></div></td>
			        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\"></div></td>
			        <td width=\"45%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;font-weight:bold\">Saldo Akhir</div></td>     
			    </tr>		
				<tr>
				<td width=\"40%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\"></div></td>
				<td width=\"35%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;font-weight:bold\">Total</div></td>
				<td width=\"25%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">".number_format($totalperjenis,2)."</div></td>     
			</tr>		
			</table>";
			$pdf->writeHTML($tbldeposito0.$tbldeposito1.$tbldeposito2.$tbldeposito3, true, false, false, false, '');

			//creditsNominative
				//creditsNormative jenis pinjaman
				$tblcredit0 = "
				<br><br><br>
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
					<tr>
						<td><div style=\"text-align: center; font-size:14px;font-weight:bold\">DAFTAR NOMINATIF PINJAMAN</div></td>
					</tr>
					<tr>
					<td><div style=\"text-align: center; font-size:10px\">Periode ".tgltoview($sesi['start_date'])." S.D. ".tgltoview($sesi['end_date'])."</div></td>
					</tr>
				</table>";
	
				$tblcredit1 = "
				<br>
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
					<tr>
						<td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
						<td width=\"30%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">Jenis Pinjaman</div></td>
						<td width=\"25%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Sub Total Saldo Pokok</div></td>     
						<td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Sub Total Sisa Bagi Hasil</div></td>     
						<td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Sub Total Sisa Pokok</div></td>     
					</tr>				
				</table>";
		
			$subtotalpokok 		= 0;
			$subtotalsisapokok 	= 0;
			$subtotalsisamargin 	= 0;
			$tblcredit2 = '';
			// print_r($acctcredits);exit;

			foreach ($acctcredits as $kCredits => $vCredits) {

				$acctcreditsaccount_credits = $this->AcctNominativeRecapReport_model->getAcctNomintiveCreditsReport_Credits($sesi['start_date'], $sesi['end_date'], $vCredits['credits_id']);
					
				// print_r($acctcreditsaccount_credits);exit;

				foreach ($acctcreditsaccount_credits as $v) {
					$credits_account_interest_last_balance = ($v['credits_account_interest_amount']*$v['credits_account_period'])-($v['credits_account_payment_to']*$v['credits_account_interest_amount']);

					$subtotalpokok += $v['credits_account_amount'];
					$subtotalsisapokok += $v['credits_account_last_balance'];
					$subtotalsisamargin += $credits_account_interest_last_balance;
	
				}
				$tblcredit2 .= "
					
						<tr>
							<td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">".($kCredits+1)."</div></td>
							<td width=\"30%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">".$vCredits['credits_name']."</div></td>
							<td width=\"25%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">".number_format($subtotalpokok, 2)."</div></td>     
							<td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">".number_format($subtotalsisamargin, 2)."</div></td>     
							<td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">".number_format($subtotalsisapokok, 2)."</div></td>     
						</tr>";
				$totalpokok += $subtotalpokok;
				$totalsisapokok += $subtotalsisapokok;
				$totalsisamargin += $subtotalsisamargin;
						
				$subtotalpokok = 0;
				$subtotalsisapokok = 0;
				$subtotalsisamargin = 0;
			}
			$tblcredit3 = "
				<br>
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
					<tr>
						<td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\"></div></td>
						<td width=\"30%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\"></div></td>
						<td width=\"25%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;font-weight:bold;\">Saldo Pokok</div></td>     
						<td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;font-weight:bold;\">Sisa Bagi Hasil</div></td>     
						<td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;font-weight:bold;\">Sisa Pokok</div></td>     
					</tr>	
					<tr>
						<td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\"></div></td>
						<td width=\"30%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;font-weight:bold;\">Total</div></td>
						<td width=\"25%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">".number_format($totalpokok, 2)."</div></td>     
						<td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">".number_format($totalsisamargin, 2)."</div></td>     
						<td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">".number_format($totalsisapokok, 2)."</div></td>     
					</tr>		
				</table>";
			$pdf->writeHTML($tblcredit0.$tblcredit1.$tblcredit2.$tblcredit3, true, false, false, '');
				
				//creditsNominative
				//creditsNormative jenis pinjaman
				$tblsourcefund0 = "
				<br><br><br>
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
					<tr>
						<td><div style=\"text-align: center; font-size:14px;font-weight:bold\">DAFTAR NOMINATIF SUMBER DANA</div></td>
					</tr>
					<tr>
					<td><div style=\"text-align: center; font-size:10px\">Periode ".tgltoview($sesi['start_date'])." S.D. ".tgltoview($sesi['end_date'])."</div></td>
					</tr>
				</table>";
	
				$tblsourcefund1 = "
				<br>
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
					<tr>
						<td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
						<td width=\"30%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: Left;font-size:10;\">Jenis Pinjaman</div></td>
						<td width=\"25%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Sub Total Saldo Pokok</div></td>     
						<td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Sub Total Sisa Bagi Hasil</div></td>     
						<td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Sub Total Sisa Pokok</div></td>     
					</tr>				
				</table>
				
				";
		
			$totalpokok 		= 0;
			$totalsisapokok 	= 0;
			$totalsisamargin 	= 0;
			$branch_id = '';
			$tblsourcefund2 = '';
			// print_r($acctcredits);exit;

			foreach ($acctsourcefund as $kCredits => $vCredits) {

				$acctcreditsaccount_sourcefund = $this->AcctNominativeRecapReport_model->getAcctNomintiveCreditsReport_SourceFund($sesi['start_date'], $sesi['end_date'], $vCredits['source_fund_id']);
						
				// print_r($sesi['start_date']);exit;

				foreach ($acctcreditsaccount_sourcefund as $v) {
					$credits_account_interest_last_balance = ($v['credits_account_interest_amount']*$v['credits_account_period'])-($v['credits_account_payment_to']*$v['credits_account_interest_amount']);

					$subtotalpokok += $v['credits_account_amount'];
					$subtotalsisapokok += $v['credits_account_last_balance'];
					$subtotalsisamargin += $credits_account_interest_last_balance;
	
				}
				$tblsourcefund2 .= "
					
						<tr>
							<td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">".($kCredits+1)."</div></td>
							<td width=\"30%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">".$vCredits['source_fund_name']."</div></td>
							<td width=\"25%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">".number_format($subtotalpokok, 2)."</div></td>     
							<td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">".number_format($subtotalsisamargin, 2)."</div></td>     
							<td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">".number_format($subtotalsisapokok, 2)."</div></td>     
						</tr>
						
					";
				$totalpokok += $subtotalpokok;
				$totalsisapokok += $subtotalsisapokok;
				$totalsisamargin += $subtotalsisamargin;
						
				$subtotalpokok = 0;
				$subtotalsisapokok = 0;
				$subtotalsisamargin = 0;
			}
			$tblsourcefund3 = "
				<br>
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
					<tr>
						<td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\"></div></td>
						<td width=\"30%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\"></div></td>
						<td width=\"25%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;font-weight:bold;\">Saldo Pokok</div></td>     
						<td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;font-weight:bold;\">Sisa Bagi Hasil</div></td>     
						<td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;font-weight:bold;\">Sisa Pokok</div></td>     
					</tr>	
					<tr>
						<td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
						<td width=\"30%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;font-weight:bold;\">Total</div></td>
						<td width=\"25%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">".number_format($totalpokok, 2)."</div></td>     
						<td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">".number_format($totalsisamargin, 2)."</div></td>     
						<td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">".number_format($totalsisapokok, 2)."</div></td>     
					</tr>			
				</table>
				<tr>
				<br><br><br><br>
				<td colspan =\"3\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."<br>".$this->AcctNominativeRecapReport_model->getUserName($auth['user_id'])."</div></td>
				
			</tr>
				";
			$pdf->writeHTML($tblsourcefund0.$tblsourcefund1.$tblsourcefund2.$tblsourcefund3, true, false, false, '');
				
			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Laporan Nominatif Rekap.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}
		
		public function export($sesi){
			$auth 	=	$this->session->userdata('auth'); 
			// $sesi = array (
			// 	"start_date" 							=> tgltodb($this->input->post('start_date',true)),
			// 	"kelompok_laporan_simpanan_berjangka"	=> $this->input->post('kelompok_laporan_simpanan_berjangka',true),
			// 	"branch_id"								=> $this->input->post('branch_id',true),
			// );

			if($auth['branch_status'] == 1){
				if($sesi['branch_id'] == '' || $sesi['branch_id'] == 0){
					$branch_id = '';
				} else {
					$branch_id = $sesi['branch_id'];
				}
			} else {
				$branch_id = $auth['branch_id'];
			}
			$acctcreditsaccount	= $this->AcctNominativeCreditsReport_model->getAcctNomintiveCreditsReport($sesi['start_date'], $sesi['end_date'], $branch_id);
			$acctcredits 		= $this->AcctNominativeCreditsReport_model->getAcctCredits();
			$acctsourcefund 	= $this->AcctNominativeCreditsReport_model->getAcctSourceFund();

			// $acctdepositoaccount	= $this->AcctNominativeDepositoReport_model->getAcctNomintiveDepositoReport($sesi['start_date']);
			// $acctdeposito 			= $this->AcctNominativeDepositoReport_model->getAcctDeposito();

			if(count($acctcreditsaccount) !=0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("CST FISRT")
									 ->setLastModifiedBy("CST FISRT")
									 ->setTitle("Laporan Nominatif Pembiayaan")
									 ->setSubject("")
									 ->setDescription("Laporan Nominatif Pembiayaan")
									 ->setKeywords("Laporan, Nominatif, Pembiayaan")
									 ->setCategory("Laporan Nominatif Pembiayaan");
									 
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);		
				$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);		
				$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);		
				$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(20);		
				$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(20);		
				$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(20);		

				
				$this->excel->getActiveSheet()->mergeCells("B1:J1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B3:J3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B3:J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3:J3')->getFont()->setBold(true);
			
				if($sesi['kelompok_laporan_pembiayaan'] == 0){
					$this->excel->getActiveSheet()->setCellValue('B3',"No");
					$this->excel->getActiveSheet()->setCellValue('C3',"No. Kredit");
					$this->excel->getActiveSheet()->setCellValue('D3',"Nama");
					$this->excel->getActiveSheet()->setCellValue('E3',"Alamat");
					$this->excel->getActiveSheet()->setCellValue('F3',"Plafon");
					$this->excel->getActiveSheet()->setCellValue('G3',"Sisa Pokok");
					$this->excel->getActiveSheet()->setCellValue('H3',"Tanggal Pinjam");
					$this->excel->getActiveSheet()->setCellValue('I3',"Jangka Waktu");
					$this->excel->getActiveSheet()->setCellValue('J3',"Tanggal Jatuh Tempo");

					$this->excel->getActiveSheet()->setCellValue('B1',"DAFTAR NOMINATIF PEMBIAYAAN GLOBAL");

				}else if($sesi['kelompok_laporan_pembiayaan'] ==1 ){
					$this->excel->getActiveSheet()->setCellValue('B3',"No");
					$this->excel->getActiveSheet()->setCellValue('C3',"No. Kredit");
					$this->excel->getActiveSheet()->setCellValue('D3',"Nama");
					$this->excel->getActiveSheet()->setCellValue('E3',"Alamat");
					$this->excel->getActiveSheet()->setCellValue('F3',"Pokok");
					$this->excel->getActiveSheet()->setCellValue('G3',"Bunga");
					$this->excel->getActiveSheet()->setCellValue('H3',"Sisa Pokok");
					$this->excel->getActiveSheet()->setCellValue('I3',"Sisa Bunga");
					$this->excel->getActiveSheet()->setCellValue('J3',"Tanggal Realisasi");
					$this->excel->getActiveSheet()->setCellValue('K3',"Jangka Waktu");
					$this->excel->getActiveSheet()->setCellValue('L3',"Jatuh Tempo");

					$this->excel->getActiveSheet()->setCellValue('B1',"DAFTAR NOMINATIF PEMBIAYAAN PER JENIS KREDIT");

				}else{
					$this->excel->getActiveSheet()->setCellValue('B3',"No");
					$this->excel->getActiveSheet()->setCellValue('C3',"No. Kredit");
					$this->excel->getActiveSheet()->setCellValue('D3',"Nama");
					$this->excel->getActiveSheet()->setCellValue('E3',"Alamat");
					$this->excel->getActiveSheet()->setCellValue('F3',"Pokok");
					$this->excel->getActiveSheet()->setCellValue('G3',"Bunga");
					$this->excel->getActiveSheet()->setCellValue('H3',"Sisa Pokok");
					$this->excel->getActiveSheet()->setCellValue('I3',"Sisa Bunga");
					$this->excel->getActiveSheet()->setCellValue('J3',"Tanggal Realisasi");
					$this->excel->getActiveSheet()->setCellValue('K3',"Jangka Waktu");
					$this->excel->getActiveSheet()->setCellValue('L3',"Jatuh Tempo");

					$this->excel->getActiveSheet()->setCellValue('B1',"DAFTAR NOMINATIF PEMBIAYAAN PER JENIS SUMBER DANA");

				}
					$this->excel->getActiveSheet()->setCellValue('B2',"Periode : ".tgltoview($sesi['start_date'])." S.D ".tgltoview($sesi['end_date']));

				$j=4;
				$no=0;
				$totalplafon	= 0;
				$totalsisapokok = 0;
				if($sesi['kelompok_laporan_pembiayaan'] == 0){
					foreach($acctcreditsaccount as $key=>$val){
						if(is_numeric($key)){
							$no++;
							$this->excel->setActiveSheetIndex(0);
							$this->excel->getActiveSheet()->getStyle('B'.$j.':J'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							


							$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
							$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j, $val['credits_account_serial'],PHPExcel_Cell_DataType::TYPE_STRING);
							$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
							$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['member_address']);
							$this->excel->getActiveSheet()->setCellValue('F'.$j,number_format($val['credits_account_amount'],2));
							$this->excel->getActiveSheet()->setCellValue('G'.$j, number_format($val['credits_account_last_balance'],2));
							$this->excel->getActiveSheet()->setCellValue('H'.$j, tgltoview($val['credits_account_date']));
							$this->excel->getActiveSheet()->setCellValue('I'.$j, $val['credits_account_period']);
							$this->excel->getActiveSheet()->setCellValue('J'.$j, tgltoview($val['credits_account_due_date']));
						
						$totalplafon	+= $val['credits_account_amount'];
						$totalsisapokok += $val['credits_account_last_balance'];
							
						}else{
							continue;
						}
						$j++;
					}
				
				} else if($sesi['kelompok_laporan_pembiayaan'] == 1) {
					$i=4;
					
					$jumlahpokok 	 = 0;
					$jumlahsisapokok = 0;
					$jumlahsisabunga = 0;


					foreach ($acctcredits as $k => $v) {
						$acctcreditsaccount_credits = $this->AcctNominativeCreditsReport_model->getAcctNomintiveCreditsReport_Credits($sesi['start_date'], $sesi['end_date'], $v['credits_id'], $branch_id);

						if(!empty($acctcreditsaccount_credits)){
							$this->excel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true)->setSize(14);
							$this->excel->getActiveSheet()->getStyle('B'.$i.':L'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$this->excel->getActiveSheet()->mergeCells('B'.$i.':L'.$i);
							$this->excel->getActiveSheet()->setCellValue('B'.$i, $v['credits_name']);

							$nov= 0;
							$j=$i+1;
						
								$subtotalpokok 		= 0;
								$subtotalsisapokok	= 0;
								$subtotalsisabunga	= 0;

							foreach($acctcreditsaccount_credits as $key=>$val){
								if(is_numeric($key)){
									$no++;
									$this->excel->setActiveSheetIndex(0);
									$this->excel->getActiveSheet()->getStyle('B'.$j.':L'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
									$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
									$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
									$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
									$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
									$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
									$this->excel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('K'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('L'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									

									$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
									$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j, $val['credits_account_serial'],PHPExcel_Cell_DataType::TYPE_STRING);
									$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
									$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['member_address']);
									$this->excel->getActiveSheet()->setCellValue('F'.$j, number_format($val['credits_account_amount'],2));
									$this->excel->getActiveSheet()->setCellValue('G'.$j, number_format($val['credits_account_interest'],2));
									$this->excel->getActiveSheet()->setCellValue('H'.$j, number_format($val['credits_account_last_balance'],2));
									$this->excel->getActiveSheet()->setCellValue('I'.$j, number_format($val['credits_account_interest_last_balance'],2));
									$this->excel->getActiveSheet()->setCellValue('J'.$j, tgltoview($val['credits_account_date']));
									$this->excel->getActiveSheet()->setCellValue('K'.$j, $val['credits_account_period']);
									$this->excel->getActiveSheet()->setCellValue('L'.$j, tgltoview($val['credits_account_due_date']));

								}else{
									continue;
								}
								$j++;
							
								$subtotalpokok 		+= $val['credits_account_amount'];
								$subtotalsisapokok	+= $val['credits_account_last_balance'];
								$subtotalsisabunga	+= $val['credits_account_interest_last_balance'];

								$i = $j;
							}
							$m =$j;

						$this->excel->getActiveSheet()->getStyle('B'.$m.':L'.$m)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
						$this->excel->getActiveSheet()->getStyle('B'.$m.':L'.$m)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->mergeCells('B'.$m.':E'.$m);
						
						$this->excel->getActiveSheet()->setCellValue('B'.$m, 'SubTotal');

						$this->excel->getActiveSheet()->setCellValue('F'.$m, number_format($subtotalpokok,2));
						$this->excel->getActiveSheet()->setCellValue('H'.$m, number_format($subtotalsisapokok,2));
						$this->excel->getActiveSheet()->setCellValue('I'.$m, number_format($subtotalsisabunga,2));
						
						$i = $m+1;
						$jumlahpokok 	 += $subtotalpokok;
						$jumlahsisapokok += $subtotalsisapokok;
						$jumlahsisabunga += $subtotalsisabunga;	
						}
					}

					$j = $i;
					
				 }  else if($sesi['kelompok_laporan_pembiayaan'] == 2) {
					$i=4;
					
					foreach ($acctsourcefund as $k => $v) {
					$acctcreditsaccount_sourcefund = $this->AcctNominativeCreditsReport_model->getAcctNomintiveCreditsReport_SourceFund($sesi['start_date'], $sesi['end_date'], $v['source_fund_id'],$branch_id);

						if(!empty($acctcreditsaccount_sourcefund)){
							$this->excel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true)->setSize(14);
							$this->excel->getActiveSheet()->getStyle('B'.$i.':L'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$this->excel->getActiveSheet()->mergeCells('B'.$i.':L'.$i);
							$this->excel->getActiveSheet()->setCellValue('B'.$i, $v['source_fund_name']);

							$nov= 0;
							$j=$i+1;
						
							$subtotalpokok 		= 0;
							$subtotalsisapokok	= 0;
							$subtotalsisabunga	= 0;
							
							foreach($acctcreditsaccount_sourcefund as $key=>$val){
								if(is_numeric($key)){
									$no++;
									$this->excel->setActiveSheetIndex(0);
									$this->excel->getActiveSheet()->getStyle('B'.$j.':L'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
									$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
									$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
									$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
									$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
									$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
									$this->excel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('K'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('L'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									
									$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
									$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j, $val['credits_account_serial'],PHPExcel_Cell_DataType::TYPE_STRING);
									$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
									$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['member_address']);
									$this->excel->getActiveSheet()->setCellValue('F'.$j, number_format($val['credits_account_amount'],2));
									$this->excel->getActiveSheet()->setCellValue('G'.$j, number_format($val['credits_account_interest'],2));
									$this->excel->getActiveSheet()->setCellValue('H'.$j, number_format($val['credits_account_last_balance'],2));
									$this->excel->getActiveSheet()->setCellValue('I'.$j, number_format($val['credits_account_interest_last_balance'],2));
									$this->excel->getActiveSheet()->setCellValue('J'.$j, tgltoview($val['credits_account_date']));
									$this->excel->getActiveSheet()->setCellValue('K'.$j, $val['credits_account_period']);
									$this->excel->getActiveSheet()->setCellValue('L'.$j, tgltoview($val['credits_account_due_date']));

								}else{
									continue;
								}
								$j++;
							
								$subtotalpokok 		+= $val['credits_account_amount'];
								$subtotalsisapokok	+= $val['credits_account_last_balance'];
								$subtotalsisabunga	+= $val['credits_account_interest_last_balance'];

								$i = $j;
							}

							$m =$j;

							$this->excel->getActiveSheet()->getStyle('B'.$m.':L'.$m)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
							$this->excel->getActiveSheet()->getStyle('B'.$m.':L'.$m)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$this->excel->getActiveSheet()->mergeCells('B'.$m.':E'.$m);
							$this->excel->getActiveSheet()->setCellValue('B'.$m, 'SubTotal');

							$this->excel->getActiveSheet()->setCellValue('F'.$m, number_format($subtotalpokok,2));
							$this->excel->getActiveSheet()->setCellValue('H'.$m, number_format($subtotalsisapokok,2));
							$this->excel->getActiveSheet()->setCellValue('I'.$m, number_format($subtotalsisabunga,2));
							$i = $m+1;

							$jumlahpokok 	 += $subtotalpokok;
							$jumlahsisapokok += $subtotalsisapokok;
							$jumlahsisabunga += $subtotalsisabunga;

						}
						
						
					}

					$j = $i;
					
				 }

				$n = $j;
				//$grandtotal += $subtotalnominal;
				if($sesi['kelompok_laporan_pembiayaan']== 0){
					$this->excel->getActiveSheet()->getStyle('B'.$n.':I'.$n)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
					$this->excel->getActiveSheet()->getStyle('B'.$n.':I'.$n)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$this->excel->getActiveSheet()->mergeCells('B'.$n.':E'.$n);
					$this->excel->getActiveSheet()->setCellValue('B'.$n, 'Total');

					$this->excel->getActiveSheet()->setCellValue('F'.$n, number_format($totalplafon,2));
					$this->excel->getActiveSheet()->setCellValue('G'.$n, number_format($totalsisapokok,2));
					
				}else{
					$this->excel->getActiveSheet()->getStyle('B'.$n.':L'.$n)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
					$this->excel->getActiveSheet()->getStyle('B'.$n.':L'.$n)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$this->excel->getActiveSheet()->mergeCells('B'.$n.':E'.$n);
					$this->excel->getActiveSheet()->setCellValue('B'.$n, 'Total');

					$this->excel->getActiveSheet()->setCellValue('F'.$n, number_format($jumlahpokok,2));
					$this->excel->getActiveSheet()->setCellValue('H'.$n, number_format($jumlahsisapokok,2));
					$this->excel->getActiveSheet()->setCellValue('I'.$n, number_format($jumlahsisabunga,2));
					//$this->excel->getActiveSheet()->setCellValue('H'.$j, $totalsaldo);
				}
				$filename='Laporan_Nominatif_Pembiayaan.xls';
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="'.$filename.'"');
				header('Cache-Control: max-age=0');
							 
				$objWriter = IOFactory::createWriter($this->excel, 'Excel5');  
				ob_end_clean();
				$objWriter->save('php://output');
			}else{
				echo "Maaf data yang di eksport tidak ada !";
			}
		 }

	}
?>