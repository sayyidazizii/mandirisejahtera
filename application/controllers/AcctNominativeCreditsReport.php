<?php
defined('BASEPATH') or exit('No direct script access allowed');
class AcctNominativeCreditsReport extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Connection_model');
		$this->load->model('MainPage_model');
		$this->load->model('AcctNominativeCreditsReport_model');
		$this->load->helper('sistem');
		$this->load->helper('url');
		$this->load->database('default');
		$this->load->library('configuration');
		$this->load->library('Fungsi');
		$this->load->library(array('PHPExcel', 'PHPExcel/IOFactory'));
	}

	public function index()
	{
		$corebranch 									= create_double_branch($this->AcctNominativeCreditsReport_model->getCoreBranch(), 'branch_id', 'branch_name');
		$corebranch[0] 									= 'Semua Cabang';
		ksort($corebranch);
		$data['main_view']['corebranch']				= $corebranch;
		$data['main_view']['kelompoklaporansimpanan']	= $this->configuration->KelompokLaporanPembiayaan();
		$data['main_view']['content']					= 'AcctNominativeCreditsReport/ListAcctNominativeCreditsReport_View';
		$this->load->view('MainPage_view', $data);
	}

	public function viewreport()
	{
		$auth 	=	$this->session->userdata('auth');
		$sesi = array(
			"start_date" 					=> tgltodb($this->input->post('start_date', true)),
			"end_date" 						=> tgltodb($this->input->post('end_date', true)),
			"kelompok_laporan_pembiayaan"	=> $this->input->post('kelompok_laporan_pembiayaan', true),
			"branch_id"						=> $this->input->post('branch_id', true),
			"view"							=> $this->input->post('view', true),

		);


		if ($sesi['view'] == 'pdf') {
			$this->processPrinting($sesi);
		} else {
			$this->export($sesi);
		}
	}

	public function processPrinting($sesi)
	{
		$auth 	=	$this->session->userdata('auth');
		$preferencecompany = $this->AcctNominativeCreditsReport_model->getPreferenceCompany();
		if ($auth['branch_status'] == 1) {
			if ($sesi['branch_id'] == '' || $sesi['branch_id'] == 0) {
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

		// print_r($acctsavingsprofitsharing);exit;


		require_once('tcpdf/config/tcpdf_config.php');
		require_once('tcpdf/tcpdf.php');
		// create new PDF document
		$pdf =  new tcpdf('P', 'mm', array(610, 630), true, 'UTF-8', false);

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
		if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
			require_once(dirname(__FILE__) . '/lang/eng.php');
			$pdf->setLanguageArray($l);
		}

		// ---------------------------------------------------------

		// set font
		$pdf->SetFont('helvetica', 'B', 25);

		// add a page
		$pdf->AddPage();

		/*$pdf->Write(0, 'Example of HTML tables', '', 0, 'L', true, 0, false, false, 0);*/

		$pdf->SetFont('helvetica', '', 9);

		// -----------------------------------------------------------------------------
		$base_url = base_url();
		$img = "<img src=\"" . $base_url . "assets/layouts/layout/img/" . $preferencecompany['logo_koperasi'] . "\" alt=\"\" width=\"700%\" height=\"300%\"/>";

		$tbl0 = "
			<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
			    <tr>
			    	<td rowspan=\"2\" width=\"10%\">" . $img . "</td>
			    </tr>
			    <tr>
			    </tr>
			</table>
			<br/>
			<br/>
			<br/>
			<br/>";

		if ($sesi['kelompok_laporan_pembiayaan'] == 0) {
			$tbl = "
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
					    <tr>
					        <td><div style=\"text-align: center; font-size:14px\">DAFTAR NOMINATIF PINJAMAN GLOBAL</div></td>
					    </tr>
					    <tr>
					        <td><div style=\"text-align: center; font-size:10px\">Periode " . tgltoview($sesi['start_date']) . " S.D. " . tgltoview($sesi['end_date']) . "</div></td>
					    </tr>
					</table>";
		} else if ($sesi['kelompok_laporan_pembiayaan'] == 1) {
			$tbl = "
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
					    <tr>
					        <td><div style=\"text-align: center; font-size:14px\">DAFTAR NOMINATIF PINJAMAN PER JENIS KREDIT</div></td>
					    </tr>
					    <tr>
					        <td><div style=\"text-align: center; font-size:10px\">Periode " . tgltoview($sesi['start_date']) . " S.D. " . tgltoview($sesi['end_date']) . "</div></td>
					    </tr>
					</table>";
		} else if ($sesi['kelompok_laporan_pembiayaan'] == 2) {
			$tbl = "
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
					    <tr>
					        <td><div style=\"text-align: center; font-size:14px\">DAFTAR NOMINATIF PINJAMAN PER JENIS SUMBER DANA</div></td>
					    </tr>
					    <tr>
					        <td><div style=\"text-align: center; font-size:10px\">Periode " . tgltoview($sesi['start_date']) . " S.D. " . tgltoview($sesi['end_date']) . "</div></td>
					    </tr>
					</table>";
		}


		$pdf->writeHTML($tbl0 . $tbl, true, false, false, false, '');

		if ($sesi['kelompok_laporan_pembiayaan'] == 0) {
			$tbl1 = "
				<br>
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
				    <tr>
				        <td width=\"3%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
				        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No. Kredit</div></td>
				        <td width=\"8%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nama</div></td>
				        <td width=\"6%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Alamat</div></td>
				        <td width=\"6%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Plafon</div></td>
				        <td width=\"6%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Bunga</div></td>
				        <td width=\"6%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Sisa Pokok</div></td>
				        <td width=\"6%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Sisa Bunga</div></td>
				        <td width=\"6%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Tgl Pinjam</div></td>
				        <td width=\"5%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Jangka Waktu</div></td>
				        <td width=\"5%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Tgl JT Tempo</div></td>
						<td width=\"4%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Provisi</div></td>
						<td width=\"5%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Survei</div></td>
						<td width=\"5%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Asuransi</div></td>
						<td width=\"5%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Simpanan Wajib</div></td>
						<td width=\"4%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Administrasi</div></td>
						<td width=\"4%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Materai</div></td>
						<td width=\"6%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Cadangan Resiko</div></td>
						<td width=\"5%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Simpanan Pokok</div></td>
						
				    </tr>				
				</table>";

			$no = 1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
			$totalbasilglobal = 0;
			$totalsaldoglobal = 0;
			$totalsaldobunga  = 0;
			$totalProvisiGbl	= 0;
			$totalSurveiGbl 	= 0;
			$totalAsuransiGbl	= 0;
			$totalSimpananWjbGbl	= 0;
			$totalAdministrasiGbl	= 0;
			$totalMateraiGbl	= 0;
			$totalCadanganResGlb	= 0;
			$totalSimpananPokokGlb	= 0;

			if (!empty($acctcreditsaccount)) {
				foreach ($acctcreditsaccount as $key => $val) {
					$month 	= date('m', strtotime($sesi['end_date']));
					$year	= date('Y', strtotime($sesi['end_date']));
					$period = $month . $year;

					$credits_account_interest_last_balance = ($val['credits_account_interest_amount'] * $val['credits_account_period']) - ($val['credits_account_payment_to'] * $val['credits_account_interest_amount']);


					$tbl3 .= "
							<tr>
						    	<td width=\"4%\"><div style=\"text-align: left;\">" . $no . "</div></td>
						        <td width=\"6%\"><div style=\"text-align: left;\">" . $val['credits_account_serial'] . "</div></td>
						        <td width=\"7%\"><div style=\"text-align: left;\">" . $val['member_name'] . "</div></td>
						        <td width=\"6%\"><div style=\"text-align: left;\">" . $val['member_address'] . "</div></td>
						        <td width=\"6%\"><div style=\"text-align: right;\">" . number_format($val['credits_account_amount'], 2) . "</div></td>
						         <td width=\"5%\"><div style=\"text-align: right;\">" . number_format($val['credits_account_interest'], 2) . "</div></td>
						        <td width=\"6%\"><div style=\"text-align: right;\">" . number_format($val['credits_account_last_balance'], 2) . "</div></td>
						         <td width=\"6%\"><div style=\"text-align: right;\">" . number_format($credits_account_interest_last_balance, 2) . "</div></td>
						        <td width=\"6%\"><div style=\"text-align: right;\">" . tgltoview($val['credits_account_date']) . "</div></td>
						         <td width=\"3%\"><div style=\"text-align: right;\">" . $val['credits_account_period'] . "</div></td>
						         <td width=\"6%\"><div style=\"text-align: right;\">" . tgltoview($val['credits_account_due_date']) . "</div></td>
								 <td width=\"5%\"><div style=\"text-align: right;\">" . number_format($val['credits_account_provisi'], 2) . "</div></td>
								 <td width=\"5%\"><div style=\"text-align: right;\">" . number_format($val['credits_account_komisi'], 2) . "</div></td>
								 <td width=\"5%\"><div style=\"text-align: right;\">" . number_format($val['credits_account_insurance'], 2) . "</div></td>
								 <td width=\"5%\"><div style=\"text-align: right;\">" . number_format($val['credits_account_stash'], 2) . "</div></td>
								 <td width=\"4%\"><div style=\"text-align: right;\">" . number_format($val['credits_account_adm_cost'], 2) . "</div></td>
								 <td width=\"4%\"><div style=\"text-align: right;\">" . number_format($val['credits_account_materai'], 2) . "</div></td>
								 <td width=\"5%\"><div style=\"text-align: right;\">" . number_format($val['credits_account_risk_reserve'], 2) . "</div></td>
								 <td width=\"4%\"><div style=\"text-align: right;\">" . number_format($val['credits_account_principal'], 2) . "</div></td>
						    </tr>
						";

					$totalbasilglobal += $val['credits_account_amount'];
					$totalsaldoglobal += $val['credits_account_last_balance'];
					$totalsaldobunga  += $credits_account_interest_last_balance;
					$totalProvisiGbl	+= $val['credits_account_provisi'];
					$totalSurveiGbl 	+= $val['credits_account_komisi'];
					$totalAsuransiGbl	+= $val['credits_account_insurance'];
					$totalSimpananWjbGbl	+= $val['credits_account_stash'];
					$totalAdministrasiGbl	+= $val['credits_account_adm_cost'];
					$totalMateraiGbl	+= $val['credits_account_materai'];
					$totalCadanganResGlb	+= $val['credits_account_risk_reserve'];
					$totalSimpananPokokGlb	+= $val['credits_account_principal'];

					$no++;
				}

				$tbl3 .= "
							<br>
							
							<tr>
								<td colspan =\"3\"><div style=\"font-size:
								10;text-align:left;font-style:italic\"></div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Subtotal </div></td>
								<td  style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalbasilglobal, 2) . "</div></td>
								
								<td colspan =\"2\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalsaldoglobal, 2) . "</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalsaldobunga, 2) . "</div></td>
								<td colspan =\"4\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalProvisiGbl, 2) . "</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalSurveiGbl, 2) . "</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalAsuransiGbl, 2) . "</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalSimpananWjbGbl, 2) . "</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalAdministrasiGbl, 2) . "</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalMateraiGbl, 2) . "</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalCadanganResGlb, 2) . "</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalSimpananPokokGlb, 2) . "</div></td>
							</tr>
							<br>
						";

				$totalpokokglobal += $totalbasilglobal;
				$totalsisapokokglobal += $totalsaldoglobal;
				$totalsisamarginglobal += $totalsaldobunga;
			} else {
				$tbl3 = "";
			}


			$tbl4 = "

					<br>
						<tr>
								<td colspan =\"3\"><div style=\"font-size:10;text-align:left;font-style:italic\"></div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\"> </div></td>
								<td  style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\"><b>PLAFON</b></div></td>
								<td colspan =\"2\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\"><b>SISA POKOK</b></div></td>
								<td  style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\"><b>SISA BUNGA</b></div></td>
							</tr>
					<tr>
						<td colspan =\"3\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : " . date('d-m-Y H:i:s') . "  " . $this->AcctNominativeCreditsReport_model->getUserName($auth['user_id']) . "</div></td>
						<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Total </div></td>
						<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalbasilglobal, 2) . "</div></td>
						<td colspan =\"2\"  style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalsaldoglobal, 2) . "</div></td>
						<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalsaldobunga, 2) . "</div></td>
					</tr>
							
			</table>";
		} else if ($sesi['kelompok_laporan_pembiayaan'] == 1) {
			$tbl1 = "
				<br>
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
				    <tr>
				        <td width=\"3%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
				        <td width=\"8%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No. Kredit</div></td>
				        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nama</div></td>
				        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Alamat</div></td>
				        <td width=\"12%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Pokok</div></td>
				       <td width=\"7%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Bunga</div></td>
				        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Sisa Pokok</div></td>
				        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Sisa Bunga</div></td>
				        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Tgl Realisasi</div></td>	
				         <td width=\"5%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Jangka Waktu</div></td>			       
				        <td width=\"7%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Tgl JT Tempo</div></td>
				       
				    </tr>				
				</table>";
			$totalpokokglobal 		= 0;
			$totalsisapokokglobal	= 0;
			$totalsisamarginglobal 	= 0;


			foreach ($acctcredits as $kCredits => $vCredits) {
				$acctcreditsaccount_credits = $this->AcctNominativeCreditsReport_model->getAcctNomintiveCreditsReport_Credits($sesi['start_date'], $sesi['end_date'], $vCredits['credits_id'], $branch_id);

				if (!empty($acctcreditsaccount_credits)) {
					$tbl3 .= "
							<br>
							<tr>
								<td colspan =\"7\" width=\"100%\" style=\"border-bottom: 1px solid black;\"><div style=\"font-size:10\">" . $vCredits['credits_name'] . "</div></td>
							</tr>
							<br>
						";
					$nov = 1;

					$totalpokok 		= 0;
					$totalsisapokok 	= 0;
					$totalsisamargin 	= 0;
					foreach ($acctcreditsaccount_credits as $k => $v) {
						$month 	= date('m', strtotime($sesi['end_date']));
						$year	= date('Y', strtotime($sesi['end_date']));
						$period = $month . $year;

						$credits_account_interest_last_balance = ($v['credits_account_interest_amount'] * $v['credits_account_period']) - ($v['credits_account_payment_to'] * $v['credits_account_interest_amount']);


						$tbl3 .= "
								<tr>
							    	<td width=\"3%\"><div style=\"text-align: left;\">" . $nov . "</div></td>
							        <td width=\"8%\"><div style=\"text-align: left;\">" . $v['credits_account_serial'] . "</div></td>
							        <td width=\"10%\"><div style=\"text-align: left;\">" . $v['member_name'] . "</div></td>
							        <td width=\"12%\"><div style=\"text-align: left;\">" . $v['member_address'] . "</div></td>
							        <td width=\"10%\"><div style=\"text-align: right;\">" . number_format($v['credits_account_amount'], 2) . "</div></td>
							        <td width=\"7%\"><div style=\"text-align: right;\">" . number_format($v['credits_account_interest'], 2) . "</div></td>
							        <td width=\"10%\"><div style=\"text-align: right;\">" . number_format($v['credits_account_last_balance'], 2) . "</div></td>
					         		<td width=\"10%\"><div style=\"text-align: right;\">" . number_format($credits_account_interest_last_balance, 2) . "</div></td>
					         		<td width=\"10%\"><div style=\"text-align: right;\">" . tgltoview($v['credits_account_date']) . "</div></td>			
					         		<td width=\"5%\"><div style=\"text-align: right;\">" . $v['credits_account_period'] . "</div></td>
							        <td width=\"7%\"><div style=\"text-align: right;\">" . tgltoview($v['credits_account_due_date']) . "</div></td>
							    </tr>

							";

						$totalpokok += $v['credits_account_amount'];
						$totalsisapokok += $v['credits_account_last_balance'];
						$totalsisamargin += $credits_account_interest_last_balance;

						$nov++;
					}

					$tbl3 .= "
							<br>
							
							<tr>
								<td colspan =\"3\"><div style=\"font-size:
								10;text-align:left;font-style:italic\"></div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Subtotal </div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalpokok, 2) . "</div></td>
								
								<td colspan =\"2\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalsisapokok, 2) . "</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalsisamargin, 2) . "</div></td>
							</tr>
							<br>
						";

					$totalpokokglobal += $totalpokok;
					$totalmarginglobal += $totalmargin;
					$totalsisapokokglobal += $totalsisapokok;
					$totalsisamarginglobal += $totalsisamargin;
				}
			}

			$tbl4 = "
						<tr>
								<td colspan =\"3\"><div style=\"font-size:10;text-align:left;font-style:italic\"></div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\"> </div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\"><b>POKOK</b></div></td>
								
								<td colspan =\"2\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\"><b>SISA POKOK</b></div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\"><b>SISA BUNGA</b></div></td>
							</tr>
						<tr>
							<td colspan =\"3\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : " . date('d-m-Y H:i:s') . "  " . $this->AcctNominativeCreditsReport_model->getUserName($auth['user_id']) . "</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Total </div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalpokokglobal, 2) . "</div></td>
							
							<td colspan =\"3\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalsisapokokglobal, 2) . "</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalsisamarginglobal, 2) . "</div></td>
						</tr>
								
				</table>";
		} else if ($sesi['kelompok_laporan_pembiayaan'] == 2) {
			$tbl1 = "
				<br>
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
				    <tr>
				        <td width=\"3%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
				        <td width=\"8%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No. Kredit</div></td>
				        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nama</div></td>
				        <td width=\"12%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Alamat</div></td>
				        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Pokok</div></td>
				        <td width=\"7%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Bunga</div></td>
				        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Sisa Pokok</div></td>
				        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Sisa Bunga</div></td>
				        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Tgl Realisasi</div></td>
				       <td width=\"5%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Jangka Waktu</div></td>			       
				        <td width=\"7%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Tgl JT Tempo</div></td>
				    </tr>				
				</table>";
			$totalpokokglobal 		= 0;
			$totalsisapokokglobal	= 0;
			$totalsisamarginglobal 	= 0;
			foreach ($acctsourcefund as $kSF => $vSF) {
				$acctcreditsaccount_sourcefund = $this->AcctNominativeCreditsReport_model->getAcctNomintiveCreditsReport_SourceFund($sesi['start_date'], $sesi['end_date'], $vSF['source_fund_id'], $branch_id);

				if (!empty($acctcreditsaccount_sourcefund)) {
					$tbl3 .= "
							<br>
							<tr>
								<td colspan =\"7\" width=\"100%\" style=\"border-bottom: 1px solid black;\"><div style=\"font-size:10\"><b>" . $vSF['source_fund_name'] . "</b></div></td>
							</tr>
							<br>
						";
					$nov = 1;
					$totalbasilperjenis = 0;
					$totalsaldoperjenis = 0;
					$totalpokok 		= 0;
					$totalsisapokok 	= 0;
					$totalsisamargin 	= 0;
					foreach ($acctcreditsaccount_sourcefund as $k => $v) {
						$month 	= date('m', strtotime($sesi['end_date']));
						$year	= date('Y', strtotime($sesi['end_date']));
						$period = $month . $year;

						$credits_account_interest_last_balance = ($v['credits_account_interest_amount'] * $v['credits_account_period']) - ($v['credits_account_payment_to'] * $v['credits_account_interest_amount']);


						$tbl3 .= "
								<tr>
							    	<td width=\"3%\"><div style=\"text-align: left;\">" . $nov . "</div></td>
							        <td width=\"8%\"><div style=\"text-align: left;\">" . $v['credits_account_serial'] . "</div></td>
							        <td width=\"10%\"><div style=\"text-align: left;\">" . $v['member_name'] . "</div></td>
							        <td width=\"12%\"><div style=\"text-align: left;\">" . $v['member_address'] . "</div></td>
							        <td width=\"10%\"><div style=\"text-align: right;\">" . number_format($v['credits_account_amount'], 2) . "</div></td>
							        <td width=\"7%\"><div style=\"text-align: right;\">" . number_format($v['credits_account_interest'], 2) . "</div></td>
							        <td width=\"10%\"><div style=\"text-align: right;\">" . number_format($v['credits_account_last_balance'], 2) . "</div></td>
					         		<td width=\"10%\"><div style=\"text-align: right;\">" . number_format($credits_account_interest_last_balance, 2) . "</div></td>
					         		<td width=\"10%\"><div style=\"text-align: right;\">" . tgltoview($v['credits_account_date']) . "</div></td>
					         		<td width=\"5%\"><div style=\"text-align: right;\">" . $v['credits_account_period'] . "</div></td>
							        <td width=\"7%\"><div style=\"text-align: right;\">" . tgltoview($v['credits_account_due_date']) . "</div></td>
							    </tr>

							";

						$totalpokok += $v['credits_account_amount'];
						$totalsisapokok += $v['credits_account_last_balance'];
						$totalsisamargin += $credits_account_interest_last_balance;

						$nov++;
					}

					$tbl3 .= "
							<br>
							
							<tr>
								<td colspan =\"3\"><div style=\"font-size:10;text-align:left;font-style:italic\"></div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Subtotal </div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalpokok, 2) . "</div></td>
								<td colspan =\"2\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalsisapokok, 2) . "</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalsisamargin, 2) . "</div></td>
							</tr>
							<br>
						";

					$totalpokokglobal += $totalpokok;
					$totalsisapokokglobal += $totalsisapokok;
					$totalsisamarginglobal += $totalsisamargin;
				} else {
					continue;
				}
			}

			$tbl4 = "
						<tr>
								<td colspan =\"3\"><div style=\"font-size:10;text-align:left;font-style:italic\"></div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\"> </div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\"><b>POKOK</b></div></td>
								<td colspan =\"2\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\"><b>SISA POKOK</b></div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\"><b>SISA BUNGA</b></div></td>
							</tr>
						<tr>
							<td colspan =\"3\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : " . date('d-m-Y H:i:s') . "  " . $this->AcctNominativeCreditsReport_model->getUserName($auth['user_id']) . "</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Total </div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalpokokglobal, 2) . "</div></td>
							<td colspan =\"2\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalsisapokokglobal, 2) . "</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">" . number_format($totalsisamarginglobal, 2) . "</div></td>
						</tr>
								
				</table>";
		}

		$pdf->writeHTML($tbl1 . $tbl3 . $tbl4, true, false, false, false, '');


		ob_clean();

		// -----------------------------------------------------------------------------

		//Close and output PDF document
		$filename = 'Laporan_Nominatif_Pembiayaan.pdf';
		$pdf->Output($filename, 'I');

		//============================================================+
		// END OF FILE
		//============================================================+
	}

	public function export($sesi)
	{
		$auth 	=	$this->session->userdata('auth');
		// $sesi = array (
		// 	"start_date" 							=> tgltodb($this->input->post('start_date',true)),
		// 	"kelompok_laporan_simpanan_berjangka"	=> $this->input->post('kelompok_laporan_simpanan_berjangka',true),
		// 	"branch_id"								=> $this->input->post('branch_id',true),
		// );

		if ($auth['branch_status'] == 1) {
			if ($sesi['branch_id'] == '' || $sesi['branch_id'] == 0) {
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

		if (count($acctcreditsaccount) != 0) {
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
			$this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('U')->setWidth(20);


			$this->excel->getActiveSheet()->mergeCells("B1:J1");
			$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
			$this->excel->getActiveSheet()->getStyle('B3:S3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$this->excel->getActiveSheet()->getStyle('B3:S3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('B3:S3')->getFont()->setBold(true);

			if ($sesi['kelompok_laporan_pembiayaan'] == 0) {
				$this->excel->getActiveSheet()->setCellValue('B3', "No");
				$this->excel->getActiveSheet()->setCellValue('C3', "No. Kredit");
				$this->excel->getActiveSheet()->setCellValue('D3', "Nama");
				$this->excel->getActiveSheet()->setCellValue('E3', "Alamat");
				$this->excel->getActiveSheet()->setCellValue('F3', "Plafon");
				$this->excel->getActiveSheet()->setCellValue('G3', "Sisa Pokok");
				$this->excel->getActiveSheet()->setCellValue('H3', "Tanggal Pinjam");
				$this->excel->getActiveSheet()->setCellValue('I3', "Jangka Waktu");
				$this->excel->getActiveSheet()->setCellValue('J3', "Tanggal Jatuh Tempo");
				$this->excel->getActiveSheet()->setCellValue('K3', "Biaya Provisi");
				$this->excel->getActiveSheet()->setCellValue('L3', "Biaya Survei");
				$this->excel->getActiveSheet()->setCellValue('M3', "Biaya Asuransi");
				$this->excel->getActiveSheet()->setCellValue('N3', "Biaya Simpanan Wajib");
				$this->excel->getActiveSheet()->setCellValue('O3', "Biaya Administrasi");
				$this->excel->getActiveSheet()->setCellValue('P3', "Biaya Materai");
				$this->excel->getActiveSheet()->setCellValue('Q3', "Biaya Cadangan Resiko");
				$this->excel->getActiveSheet()->setCellValue('R3', "Biaya Simpanan Pokok");
				$this->excel->getActiveSheet()->setCellValue('S3', "Nama Sales");

				$this->excel->getActiveSheet()->setCellValue('B1', "DAFTAR NOMINATIF PEMBIAYAAN GLOBAL");
			} else if ($sesi['kelompok_laporan_pembiayaan'] == 1) {
				$this->excel->getActiveSheet()->setCellValue('B3', "No");
				$this->excel->getActiveSheet()->setCellValue('C3', "No. Kredit");
				$this->excel->getActiveSheet()->setCellValue('D3', "Nama");
				$this->excel->getActiveSheet()->setCellValue('E3', "Alamat");
				$this->excel->getActiveSheet()->setCellValue('F3', "Pokok");
				$this->excel->getActiveSheet()->setCellValue('G3', "Bunga");
				$this->excel->getActiveSheet()->setCellValue('H3', "Sisa Pokok");
				$this->excel->getActiveSheet()->setCellValue('I3', "Sisa Bunga");
				$this->excel->getActiveSheet()->setCellValue('J3', "Tanggal Realisasi");
				$this->excel->getActiveSheet()->setCellValue('K3', "Jangka Waktu");
				$this->excel->getActiveSheet()->setCellValue('L3', "Jatuh Tempo");
				$this->excel->getActiveSheet()->setCellValue('M3', "Biaya Provisi");
				$this->excel->getActiveSheet()->setCellValue('N3', "Biaya Survei");
				$this->excel->getActiveSheet()->setCellValue('O3', "Biaya Asuransi");
				$this->excel->getActiveSheet()->setCellValue('P3', "Biaya Simpanan Wajib");
				$this->excel->getActiveSheet()->setCellValue('Q3', "Biaya Administrasi");
				$this->excel->getActiveSheet()->setCellValue('R3', "Biaya Materai");
				$this->excel->getActiveSheet()->setCellValue('S3', "Biaya Cadangan Resiko");
				$this->excel->getActiveSheet()->setCellValue('T3', "Biaya Simpanan Pokok");
				$this->excel->getActiveSheet()->setCellValue('U3', "Nama Sales");

				$this->excel->getActiveSheet()->setCellValue('B1', "DAFTAR NOMINATIF PEMBIAYAAN PER JENIS KREDIT");
			} else {
				$this->excel->getActiveSheet()->setCellValue('B3', "No");
				$this->excel->getActiveSheet()->setCellValue('C3', "No. Kredit");
				$this->excel->getActiveSheet()->setCellValue('D3', "Nama");
				$this->excel->getActiveSheet()->setCellValue('E3', "Alamat");
				$this->excel->getActiveSheet()->setCellValue('F3', "Pokok");
				$this->excel->getActiveSheet()->setCellValue('G3', "Bunga");
				$this->excel->getActiveSheet()->setCellValue('H3', "Sisa Pokok");
				$this->excel->getActiveSheet()->setCellValue('I3', "Sisa Bunga");
				$this->excel->getActiveSheet()->setCellValue('J3', "Tanggal Realisasi");
				$this->excel->getActiveSheet()->setCellValue('K3', "Jangka Waktu");
				$this->excel->getActiveSheet()->setCellValue('L3', "Jatuh Tempo");
				$this->excel->getActiveSheet()->setCellValue('M3', "Biaya Provisi");
				$this->excel->getActiveSheet()->setCellValue('N3', "Biaya Survei");
				$this->excel->getActiveSheet()->setCellValue('O3', "Biaya Asuransi");
				$this->excel->getActiveSheet()->setCellValue('P3', "Biaya Simpanan Wajib");
				$this->excel->getActiveSheet()->setCellValue('Q3', "Biaya Administrasi");
				$this->excel->getActiveSheet()->setCellValue('R3', "Biaya Materai");
				$this->excel->getActiveSheet()->setCellValue('S3', "Biaya Cadangan Resiko");
				$this->excel->getActiveSheet()->setCellValue('T3', "Biaya Simpanan Pokok");
				$this->excel->getActiveSheet()->setCellValue('U3', "Nama Sales");

				$this->excel->getActiveSheet()->setCellValue('B1', "DAFTAR NOMINATIF PEMBIAYAAN PER JENIS SUMBER DANA");
			}
			$this->excel->getActiveSheet()->setCellValue('B2', "Periode : " . tgltoview($sesi['start_date']) . " S.D " . tgltoview($sesi['end_date']));

			$j = 4;
			$no = 0;
			$totalplafon	= 0;
			$totalsisapokok = 0;
			$totalProvisi	= 0;
			$totalSurvei	= 0;
			$totalAsurasi	= 0;
			$totalSimpananWajib = 0;
			$totalAdministrasi 	= 0;
			$totalMaterai 		= 0;
			$totalCadanganResiko = 0;
			$totalSimpananPokok	= 0;
			if ($sesi['kelompok_laporan_pembiayaan'] == 0) {
				foreach ($acctcreditsaccount as $key => $val) {
					if (is_numeric($key)) {
						$no++;
						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('B' . $j . ':S' . $j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->getStyle('B' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('C' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('D' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('E' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('F' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('G' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('H' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('I' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('J' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('K' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('L' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('M' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('N' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('O' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('P' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('Q' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('R' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('S' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);



						$this->excel->getActiveSheet()->setCellValue('B' . $j, $no);
						$this->excel->getActiveSheet()->setCellValueExplicit('C' . $j, $val['credits_account_serial'], PHPExcel_Cell_DataType::TYPE_STRING);
						$this->excel->getActiveSheet()->setCellValue('D' . $j, $val['member_name']);
						$this->excel->getActiveSheet()->setCellValue('E' . $j, $val['member_address']);
						$this->excel->getActiveSheet()->setCellValue('F' . $j, number_format($val['credits_account_amount'], 2));
						$this->excel->getActiveSheet()->setCellValue('G' . $j, number_format($val['credits_account_last_balance'], 2));
						$this->excel->getActiveSheet()->setCellValue('H' . $j, tgltoview($val['credits_account_date']));
						$this->excel->getActiveSheet()->setCellValue('I' . $j, $val['credits_account_period']);
						$this->excel->getActiveSheet()->setCellValue('J' . $j, tgltoview($val['credits_account_due_date']));
						$this->excel->getActiveSheet()->setCellValue('K' . $j, number_format($val['credits_account_provisi'], 2));
						$this->excel->getActiveSheet()->setCellValue('L' . $j, number_format($val['credits_account_komisi'], 2));
						$this->excel->getActiveSheet()->setCellValue('M' . $j, number_format($val['credits_account_insurance'], 2));
						$this->excel->getActiveSheet()->setCellValue('N' . $j, number_format($val['credits_account_stash'], 2));
						$this->excel->getActiveSheet()->setCellValue('O' . $j, number_format($val['credits_account_adm_cost'], 2));
						$this->excel->getActiveSheet()->setCellValue('P' . $j, number_format($val['credits_account_materai'], 2));
						$this->excel->getActiveSheet()->setCellValue('Q' . $j, number_format($val['credits_account_risk_reserve'], 2));
						$this->excel->getActiveSheet()->setCellValue('R' . $j, number_format($val['credits_account_principal'], 2));
						$this->excel->getActiveSheet()->setCellValue('S' . $j, $val['credits_account_sales_name'],);

						$totalplafon	+= $val['credits_account_amount'];
						$totalsisapokok += $val['credits_account_last_balance'];
						$totalProvisi 	+= $val['credits_account_provisi'];
						$totalSurvei	+= $val['credits_account_komisi'];
						$totalAsurasi	+= $val['credits_account_insurance'];
						$totalSimpananWajib	+= $val['credits_account_stash'];
						$totalAdministrasi	+= $val['credits_account_adm_cost'];
						$totalMaterai	+= $val['credits_account_materai'];
						$totalCadanganResiko	+= $val['credits_account_risk_reserve'];
						$totalSimpananPokok	+= $val['credits_account_principal'];
					} else {
						continue;
					}
					$j++;
				}
			} else if ($sesi['kelompok_laporan_pembiayaan'] == 1) {
				$i = 4;

				$jumlahpokok 	 = 0;
				$jumlahsisapokok = 0;
				$jumlahsisabunga = 0;
				$jumlahProvisi	= 0;
				$jumlahSurvei	= 0;
				$jumlahAsurasi	= 0;
				$jumlahSimpananWajib = 0;
				$jumlahAdministrasi 	= 0;
				$jumlahMaterai 		= 0;
				$jumlahCadanganResiko = 0;
				$jumlahSimpananPokok	= 0;

				foreach ($acctcredits as $k => $v) {
					$acctcreditsaccount_credits = $this->AcctNominativeCreditsReport_model->getAcctNomintiveCreditsReport_Credits($sesi['start_date'], $sesi['end_date'], $v['credits_id'], $branch_id);

					if (!empty($acctcreditsaccount_credits)) {
						$this->excel->getActiveSheet()->getStyle('B' . $i)->getFont()->setBold(true)->setSize(14);
						$this->excel->getActiveSheet()->getStyle('B' . $i . ':U' . $i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->mergeCells('B' . $i . ':U' . $i);
						$this->excel->getActiveSheet()->setCellValue('B' . $i, $v['credits_name']);

						$nov = 0;
						$j = $i + 1;

						$subtotalpokok 		= 0;
						$subtotalsisapokok	= 0;
						$subtotalsisabunga	= 0;
						$subtotalProvisi	= 0;
						$subtotalSurvei	= 0;
						$subtotalAsurasi	= 0;
						$subtotalSimpananWajib = 0;
						$subtotalAdministrasi 	= 0;
						$subtotalMaterai 		= 0;
						$subtotalCadanganResiko = 0;
						$subtotalSimpananPokok	= 0;

						foreach ($acctcreditsaccount_credits as $key => $val) {
							if (is_numeric($key)) {
								$no++;
								$this->excel->setActiveSheetIndex(0);
								$this->excel->getActiveSheet()->getStyle('B' . $j . ':U' . $j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
								$this->excel->getActiveSheet()->getStyle('B' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								$this->excel->getActiveSheet()->getStyle('C' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('D' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('E' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('F' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								$this->excel->getActiveSheet()->getStyle('G' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								$this->excel->getActiveSheet()->getStyle('H' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								$this->excel->getActiveSheet()->getStyle('I' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								$this->excel->getActiveSheet()->getStyle('J' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('K' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('L' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('M' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('N' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('O' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('P' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('Q' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('R' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('S' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('T' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('U' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);


								$this->excel->getActiveSheet()->setCellValue('B' . $j, $no);
								$this->excel->getActiveSheet()->setCellValueExplicit('C' . $j, $val['credits_account_serial'], PHPExcel_Cell_DataType::TYPE_STRING);
								$this->excel->getActiveSheet()->setCellValue('D' . $j, $val['member_name']);
								$this->excel->getActiveSheet()->setCellValue('E' . $j, $val['member_address']);
								$this->excel->getActiveSheet()->setCellValue('F' . $j, number_format($val['credits_account_amount'], 2));
								$this->excel->getActiveSheet()->setCellValue('G' . $j, number_format($val['credits_account_interest'], 2));
								$this->excel->getActiveSheet()->setCellValue('H' . $j, number_format($val['credits_account_last_balance'], 2));
								$this->excel->getActiveSheet()->setCellValue('I' . $j, number_format($val['credits_account_interest_last_balance'], 2));
								$this->excel->getActiveSheet()->setCellValue('J' . $j, tgltoview($val['credits_account_date']));
								$this->excel->getActiveSheet()->setCellValue('K' . $j, $val['credits_account_period']);
								$this->excel->getActiveSheet()->setCellValue('L' . $j, tgltoview($val['credits_account_due_date']));
								$this->excel->getActiveSheet()->setCellValue('M' . $j, number_format($val['credits_account_provisi'], 2));
								$this->excel->getActiveSheet()->setCellValue('N' . $j, number_format($val['credits_account_komisi'], 2));
								$this->excel->getActiveSheet()->setCellValue('O' . $j, number_format($val['credits_account_insurance'], 2));
								$this->excel->getActiveSheet()->setCellValue('P' . $j, number_format($val['credits_account_stash'], 2));
								$this->excel->getActiveSheet()->setCellValue('Q' . $j, number_format($val['credits_account_adm_cost'], 2));
								$this->excel->getActiveSheet()->setCellValue('R' . $j, number_format($val['credits_account_materai'], 2));
								$this->excel->getActiveSheet()->setCellValue('S' . $j, number_format($val['credits_account_risk_reserve'], 2));
								$this->excel->getActiveSheet()->setCellValue('T' . $j, number_format($val['credits_account_principal'], 2));
								$this->excel->getActiveSheet()->setCellValue('U' . $j, $val['credits_account_sales_name'],);
							} else {
								continue;
							}
							$j++;

							$subtotalpokok 		+= $val['credits_account_amount'];
							$subtotalsisapokok	+= $val['credits_account_last_balance'];
							$subtotalsisabunga	+= $val['credits_account_interest_last_balance'];
							$subtotalProvisi 	+= $val['credits_account_provisi'];
							$subtotalSurvei	+= $val['credits_account_komisi'];
							$subtotalAsurasi	+= $val['credits_account_insurance'];
							$subtotalSimpananWajib	+= $val['credits_account_stash'];
							$subtotalAdministrasi	+= $val['credits_account_adm_cost'];
							$subtotalMaterai	+= $val['credits_account_materai'];
							$subtotalCadanganResiko	+= $val['credits_account_risk_reserve'];
							$subtotalSimpananPokok	+= $val['credits_account_principal'];

							$i = $j;
						}
						$m = $j;

						$this->excel->getActiveSheet()->getStyle('B' . $m . ':U' . $m)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
						$this->excel->getActiveSheet()->getStyle('B' . $m . ':U' . $m)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->mergeCells('B' . $m . ':E' . $m);

						$this->excel->getActiveSheet()->setCellValue('B' . $m, 'SubTotal');

						$this->excel->getActiveSheet()->setCellValue('F' . $m, number_format($subtotalpokok, 2));
						$this->excel->getActiveSheet()->setCellValue('H' . $m, number_format($subtotalsisapokok, 2));
						$this->excel->getActiveSheet()->setCellValue('I' . $m, number_format($subtotalsisabunga, 2));
						$this->excel->getActiveSheet()->setCellValue('K' . $m, number_format($subtotalProvisi, 2));
						$this->excel->getActiveSheet()->setCellValue('L' . $m, number_format($subtotalSurvei, 2));
						$this->excel->getActiveSheet()->setCellValue('M' . $m, number_format($subtotalAsurasi, 2));
						$this->excel->getActiveSheet()->setCellValue('N' . $m, number_format($subtotalSimpananWajib, 2));
						$this->excel->getActiveSheet()->setCellValue('O' . $m, number_format($subtotalAdministrasi, 2));
						$this->excel->getActiveSheet()->setCellValue('P' . $m, number_format($subtotalMaterai, 2));
						$this->excel->getActiveSheet()->setCellValue('Q' . $m, number_format($subtotalCadanganResiko, 2));
						$this->excel->getActiveSheet()->setCellValue('R' . $m, number_format($subtotalSimpananPokok, 2));


						$i = $m + 1;
						$jumlahpokok 	 += $subtotalpokok;
						$jumlahsisapokok += $subtotalsisapokok;
						$jumlahsisabunga += $subtotalsisabunga;
						$jumlahProvisi	+= $subtotalProvisi;
						$jumlahSurvei	+= $subtotalSurvei;
						$jumlahAsurasi	+= $subtotalAsurasi;
						$jumlahSimpananWajib += $subtotalSimpananWajib;
						$jumlahAdministrasi 	+= $subtotalAdministrasi;
						$jumlahMaterai 		+= $subtotalMaterai;
						$jumlahCadanganResiko += $subtotalCadanganResiko;
						$jumlahSimpananPokok	+= $subtotalSimpananPokok;
					}
				}

				$j = $i;
			} else if ($sesi['kelompok_laporan_pembiayaan'] == 2) {
				$i = 4;

				foreach ($acctsourcefund as $k => $v) {
					$acctcreditsaccount_sourcefund = $this->AcctNominativeCreditsReport_model->getAcctNomintiveCreditsReport_SourceFund($sesi['start_date'], $sesi['end_date'], $v['source_fund_id'], $branch_id);

					if (!empty($acctcreditsaccount_sourcefund)) {
						$this->excel->getActiveSheet()->getStyle('B' . $i)->getFont()->setBold(true)->setSize(14);
						$this->excel->getActiveSheet()->getStyle('B' . $i . ':U' . $i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->mergeCells('B' . $i . ':U' . $i);
						$this->excel->getActiveSheet()->setCellValue('B' . $i, $v['source_fund_name']);

						$nov = 0;
						$j = $i + 1;

						$subtotalpokok 		= 0;
						$subtotalsisapokok	= 0;
						$subtotalsisabunga	= 0;
						$subtotalProvisi	= 0;
						$subtotalSurvei	= 0;
						$subtotalAsurasi	= 0;
						$subtotalSimpananWajib = 0;
						$subtotalAdministrasi 	= 0;
						$subtotalMaterai 		= 0;
						$subtotalCadanganResiko = 0;
						$subtotalSimpananPokok	= 0;

						foreach ($acctcreditsaccount_sourcefund as $key => $val) {
							if (is_numeric($key)) {
								$no++;
								$this->excel->setActiveSheetIndex(0);
								$this->excel->getActiveSheet()->getStyle('B' . $j . ':U' . $j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
								$this->excel->getActiveSheet()->getStyle('B' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								$this->excel->getActiveSheet()->getStyle('C' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('D' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('E' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('F' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								$this->excel->getActiveSheet()->getStyle('G' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								$this->excel->getActiveSheet()->getStyle('H' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								$this->excel->getActiveSheet()->getStyle('I' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								$this->excel->getActiveSheet()->getStyle('J' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('K' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('L' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('M' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('N' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('O' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('P' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('Q' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('R' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('S' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('T' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('U' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

								$this->excel->getActiveSheet()->setCellValue('B' . $j, $no);
								$this->excel->getActiveSheet()->setCellValueExplicit('C' . $j, $val['credits_account_serial'], PHPExcel_Cell_DataType::TYPE_STRING);
								$this->excel->getActiveSheet()->setCellValue('D' . $j, $val['member_name']);
								$this->excel->getActiveSheet()->setCellValue('E' . $j, $val['member_address']);
								$this->excel->getActiveSheet()->setCellValue('F' . $j, number_format($val['credits_account_amount'], 2));
								$this->excel->getActiveSheet()->setCellValue('G' . $j, number_format($val['credits_account_interest'], 2));
								$this->excel->getActiveSheet()->setCellValue('H' . $j, number_format($val['credits_account_last_balance'], 2));
								$this->excel->getActiveSheet()->setCellValue('I' . $j, number_format($val['credits_account_interest_last_balance'], 2));
								$this->excel->getActiveSheet()->setCellValue('J' . $j, tgltoview($val['credits_account_date']));
								$this->excel->getActiveSheet()->setCellValue('K' . $j, $val['credits_account_period']);
								$this->excel->getActiveSheet()->setCellValue('L' . $j, tgltoview($val['credits_account_due_date']));
								$this->excel->getActiveSheet()->setCellValue('M' . $j, number_format($val['credits_account_provisi'], 2));
								$this->excel->getActiveSheet()->setCellValue('N' . $j, number_format($val['credits_account_komisi'], 2));
								$this->excel->getActiveSheet()->setCellValue('O' . $j, number_format($val['credits_account_insurance'], 2));
								$this->excel->getActiveSheet()->setCellValue('P' . $j, number_format($val['credits_account_stash'], 2));
								$this->excel->getActiveSheet()->setCellValue('Q' . $j, number_format($val['credits_account_adm_cost'], 2));
								$this->excel->getActiveSheet()->setCellValue('R' . $j, number_format($val['credits_account_materai'], 2));
								$this->excel->getActiveSheet()->setCellValue('S' . $j, number_format($val['credits_account_risk_reserve'], 2));
								$this->excel->getActiveSheet()->setCellValue('T' . $j, number_format($val['credits_account_principal'], 2));
								$this->excel->getActiveSheet()->setCellValue('U' . $j, $val['credits_account_sales_name'],);
							} else {
								continue;
							}
							$j++;

							$subtotalpokok 		+= $val['credits_account_amount'];
							$subtotalsisapokok	+= $val['credits_account_last_balance'];
							$subtotalsisabunga	+= $val['credits_account_interest_last_balance'];
							$subtotalProvisi 	+= $val['credits_account_provisi'];
							$subtotalSurvei	+= $val['credits_account_komisi'];
							$subtotalAsurasi	+= $val['credits_account_insurance'];
							$subtotalSimpananWajib	+= $val['credits_account_stash'];
							$subtotalAdministrasi	+= $val['credits_account_adm_cost'];
							$subtotalMaterai	+= $val['credits_account_materai'];
							$subtotalCadanganResiko	+= $val['credits_account_risk_reserve'];
							$subtotalSimpananPokok	+= $val['credits_account_principal'];

							$i = $j;
						}

						$m = $j;

						$this->excel->getActiveSheet()->getStyle('B' . $m . ':U' . $m)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
						$this->excel->getActiveSheet()->getStyle('B' . $m . ':U' . $m)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->mergeCells('B' . $m . ':E' . $m);
						$this->excel->getActiveSheet()->setCellValue('B' . $m, 'SubTotal');

						$this->excel->getActiveSheet()->setCellValue('F' . $m, number_format($subtotalpokok, 2));
						$this->excel->getActiveSheet()->setCellValue('H' . $m, number_format($subtotalsisapokok, 2));
						$this->excel->getActiveSheet()->setCellValue('I' . $m, number_format($subtotalsisabunga, 2));
						$this->excel->getActiveSheet()->setCellValue('K' . $m, number_format($subtotalProvisi, 2));
						$this->excel->getActiveSheet()->setCellValue('L' . $m, number_format($subtotalSurvei, 2));
						$this->excel->getActiveSheet()->setCellValue('M' . $m, number_format($subtotalAsurasi, 2));
						$this->excel->getActiveSheet()->setCellValue('N' . $m, number_format($subtotalSimpananWajib, 2));
						$this->excel->getActiveSheet()->setCellValue('O' . $m, number_format($subtotalAdministrasi, 2));
						$this->excel->getActiveSheet()->setCellValue('P' . $m, number_format($subtotalMaterai, 2));
						$this->excel->getActiveSheet()->setCellValue('Q' . $m, number_format($subtotalCadanganResiko, 2));
						$this->excel->getActiveSheet()->setCellValue('R' . $m, number_format($subtotalSimpananPokok, 2));
						$i = $m + 1;

						// $jumlahAdministrasi += $subtotalAdministrasi;
					}
				}

				$j = $i;
			}

			$n = $j;
			//$grandtotal += $subtotalnominal;
			if ($sesi['kelompok_laporan_pembiayaan'] == 0) {
				$this->excel->getActiveSheet()->getStyle('B' . $n . ':S' . $n)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
				$this->excel->getActiveSheet()->getStyle('B' . $n . ':S' . $n)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->mergeCells('B' . $n . ':E' . $n);
				$this->excel->getActiveSheet()->setCellValue('B' . $n, 'Total');

				$this->excel->getActiveSheet()->setCellValue('F' . $n, number_format($totalplafon, 2));
				$this->excel->getActiveSheet()->setCellValue('G' . $n, number_format($totalsisapokok, 2));
				$this->excel->getActiveSheet()->setCellValue('K' . $n, number_format($totalProvisi, 2));
				$this->excel->getActiveSheet()->setCellValue('L' . $n, number_format($totalSurvei, 2));
				$this->excel->getActiveSheet()->setCellValue('M' . $n, number_format($totalAsurasi, 2));
				$this->excel->getActiveSheet()->setCellValue('N' . $n, number_format($totalSimpananWajib, 2));
				$this->excel->getActiveSheet()->setCellValue('O' . $n, number_format($totalAdministrasi, 2));
				$this->excel->getActiveSheet()->setCellValue('P' . $n, number_format($totalMaterai, 2));
				$this->excel->getActiveSheet()->setCellValue('Q' . $n, number_format($totalCadanganResiko, 2));
				$this->excel->getActiveSheet()->setCellValue('R' . $n, number_format($totalSimpananPokok, 2));
			} else {
				$this->excel->getActiveSheet()->getStyle('B' . $n . ':S' . $n)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
				$this->excel->getActiveSheet()->getStyle('B' . $n . ':S' . $n)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->mergeCells('B' . $n . ':E' . $n);
				$this->excel->getActiveSheet()->setCellValue('B' . $n, 'Total');

				$this->excel->getActiveSheet()->setCellValue('F' . $n, number_format($jumlahpokok, 2));
				$this->excel->getActiveSheet()->setCellValue('H' . $n, number_format($jumlahsisapokok, 2));
				$this->excel->getActiveSheet()->setCellValue('I' . $n, number_format($jumlahsisabunga, 2));
				$this->excel->getActiveSheet()->setCellValue('K' . $n, number_format($jumlahProvisi, 2));
				$this->excel->getActiveSheet()->setCellValue('L' . $n, number_format($jumlahSurvei, 2));
				$this->excel->getActiveSheet()->setCellValue('M' . $n, number_format($jumlahAsurasi, 2));
				$this->excel->getActiveSheet()->setCellValue('N' . $n, number_format($jumlahSimpananWajib, 2));
				$this->excel->getActiveSheet()->setCellValue('O' . $n, number_format($jumlahAdministrasi, 2));
				$this->excel->getActiveSheet()->setCellValue('P' . $n, number_format($jumlahMaterai, 2));
				$this->excel->getActiveSheet()->setCellValue('Q' . $n, number_format($jumlahCadanganResiko, 2));
				$this->excel->getActiveSheet()->setCellValue('R' . $n, number_format($jumlahSimpananPokok, 2));
				//$this->excel->getActiveSheet()->setCellValue('H'.$j, $totalsaldo);
			}
			$filename = 'Laporan_Nominatif_Pembiayaan.xls';
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="' . $filename . '"');
			header('Cache-Control: max-age=0');

			$objWriter = IOFactory::createWriter($this->excel, 'Excel5');
			ob_end_clean();
			$objWriter->save('php://output');
		} else {
			echo "Maaf data yang di eksport tidak ada !";
		}
	}
}
