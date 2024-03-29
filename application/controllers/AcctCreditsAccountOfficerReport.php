<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctCreditsAccountOfficerReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctCreditsAccountOfficerReport_model');
			$this->load->model('AcctCreditAccount_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('Configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}

		public function index(){
			$data['main_view']['coreoffice']	= create_double($this->AcctCreditsAccountOfficerReport_model->getCoreOffice(),'office_id', 'office_name');

			$corebranch 						= create_double_branch($this->AcctCreditsAccountOfficerReport_model->getCoreBranch(),'branch_id','branch_name');
			$corebranch[0] 						= 'Semua Cabang';
			ksort($corebranch);
			$data['main_view']['corebranch']	= $corebranch;	
			$data['main_view']['content'] 		= 'AcctCreditsAccountOfficerReport/FormFilterAcctCreditsAccountOfficerReport_view';
			$this->load->view('MainPage_view', $data);
		}

		public function viewreport(){
			$sesi = array (
				'office_id'		=> $this->input->post('office_id', true),
				'start_date'	=> tgltodb($this->input->post('start_date', true)),
				'end_date'		=> tgltodb($this->input->post('end_date', true)),
				'branch_id'		=> $this->input->post('branch_id', true),
				"view"			=> $this->input->post('view',true),
			);
			
			if($sesi['view'] == 'pdf'){
				$this->processPrinting($sesi);
			} else {
				$this->export($sesi);
			}
		}

		public function processPrinting($sesi){
			$auth 	=	$this->session->userdata('auth'); 

			if($auth['branch_status'] == 1){
				if($sesi['branch_id'] == '' || $sesi['branch_id'] == 0){
					$branch_id = '';
				} else {
					$branch_id = $sesi['branch_id'];
				}
			} else {
				$branch_id = $auth['branch_id'];
			}

			$acctcredits 			= $this->AcctCreditsAccountOfficerReport_model->getAcctCredits();
			$preferencecompany 		= $this->AcctCreditsAccountOfficerReport_model->getPreferenceCompany();
			



			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new tcpdf('L', PDF_UNIT, 'F4', true, 'UTF-8', false);

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
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preferencecompany['logo_koperasi']."\" alt=\"\" width=\"700%\" height=\"300%\"/>";

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
			$tbl = "
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
				    <tr>
				        <td><div style=\"text-align: left;font-size:10; font-weight:bold\">".$preferencecompany['company_name']."</div></td>		       
				    </tr>						
				</table>";

			$pdf->writeHTML($tbl0.$tbl, true, false, false, false, '');
			
			if(!empty($sesi['office_id'])){
				$tbl = "
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
					    <tr>
					        <td><div style=\"text-align: left;font-size:10; font-weight:bold\">DAFTAR NASABAH PINJAMAN : ".$this->AcctCreditsAccountOfficerReport_model->getOfficeName($sesi['office_id'])."</div></td>
					        <td><div style=\"text-align: left;font-size:10; font-weight:bold\">Mulai Tgl. ".tgltoview($sesi['start_date'])." S.D ".tgltoview($sesi['end_date'])."</div></td>			       
					    </tr>						
					</table>";

					$pdf->writeHTML($tbl, true, false, false, false, '');

					$tbl1 = "
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
					    <tr>
					        <td width=\"2%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
					        
					        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No. Kredit</div></td>
					       
					        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nama</div></td>
					       
					        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Alamat</div></td>
					        
					        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Pokok</div></td>
					        
					        <td width=\"8%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Bunga</div></td>
					        
					        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Sisa Pokok</div></td>
					        
					        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Jangka Waktu</div></td>
					        
					        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Angsuran</div></td>
					        
					        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Keterlambatan</div></td>

					        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Ak Denda</div></td>

					        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Pdptn Adm</div></td>
					    </tr>				
					</table>";

					$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
					foreach ($acctcredits as $kC => $vC) {
						$acctcreditsaccount 		= $this->AcctCreditsAccountOfficerReport_model->getAcctCreditsAccount($sesi['office_id'], $sesi['start_date'], $sesi['end_date'], $vC['credits_id'],$branch_id);

						if(!empty($acctcreditsaccount)){
							$tbl3 .= "
								<br>
								<tr>
									<td colspan =\"10\" style=\"border-bottom: 1px solid black;\"><div style=\"font-size:10\">".$vC['credits_name']."</div></td>
								</tr>
							";

							// foreach ($acctcreditsaccount as $k => $v) {								
							// 	$day 		= date('d-m-Y');
							// 	$start_ 	= new DateTime($day);
							// 	$end_ 		= new DateTime($v['credits_account_payment_date']);

							// 	if($end_ >= $start_){
							// 		$Keterlambatan 	= '0';
							// 		// $interval 	= $start_->diff($end_);
							// 	}else{
							// 		$interval 		= $start_->diff($end_);
							// 		$Keterlambatan 	= $interval->days;
							// 	}
							// }

							$no = 1;
							$totalprice 		=0;
							$totalmargin 		=0;
							$totalsaldoprice 	=0;
							$totalangs			=0;
							$totalakdenda		=0;
							$totaladm			=0;
								$grandtotalprice 		= 0;
								//$grandtotalmargin		= 0;
								$grandtotalsaldoprice 	= 0;
								$grandtotalangs			= 0;
								$grandtotalakdenda		= 0;
								$grandtotaladm			= 0;

							foreach ($acctcreditsaccount as $key => $val) {	
								$day 		= date('d-m-Y');
								$start_ 	= new DateTime($day);
								$end_ 		= new DateTime($val['credits_account_payment_date']);

								if($end_ >= $start_){
									$Keterlambatan 	= '0';
									// $interval 	= $start_->diff($end_);
								}else{
									$interval 		= $start_->diff($end_);
									$Keterlambatan 	= $interval->days;
								}

								$acctcredits_fine	= $this->AcctCreditAccount_model->getDetailByID($val['credits_account_id']);

								$credits_payment_fine_amount 		= (($val['credits_account_payment_amount'] * $acctcredits_fine['credits_fine']) / 100 ) * $Keterlambatan;

								$credits_account_accumulated_fines 	= $val['credits_account_accumulated_fines'] + $credits_payment_fine_amount;	

								$tbl3 .= "
									<tr>
								    	<td width=\"2%\"><div style=\"text-align: left;\">".$no."</div></td>

								        <td width=\"10%\"><div style=\"text-align: left;\">".$val['credits_account_serial']."</div></td>

								        <td width=\"10%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>

								        <td width=\"15%\"><div style=\"text-align: left;\">".$val['member_address']."</div></td>

								        <td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_account_amount'], 2)."</div></td>

								        <td width=\"8%\"><div style=\"text-align: center;\">".number_format($val['credits_account_interest'], 2)."</div></td>

								        <td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_account_last_balance'], 2)."</div></td>

								        <td width=\"5%\"><div style=\"text-align: right;\">".$val['credits_account_period']."</div></td>

								        <td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_account_payment_amount'],2)."</div></td>

								        <td width=\"10%\"><div style=\"text-align: right;\">".$Keterlambatan."</div></td>

								        <td width=\"5%\"><div style=\"text-align: right;\">".number_format($credits_account_accumulated_fines, 2)."</div></td>

								        <td width=\"5%\"><div style=\"text-align: right;\">".number_format($val['credits_account_adm_cost'], 2)."</div></td>
								    </tr>
								";
								$no++;

								$totalprice 		+= $val['credits_account_amount'];
								//$totalmargin 		+= $val['credits_account_interest'];
								$totalsaldoprice 	+= $val['credits_account_last_balance'];
								$totalangs			+= $val['credits_account_payment_amount'];
								$totalakdenda		+= $val['credits_account_accumulated_fines'];
								$totaladm			+= $val['credits_account_adm_cost'];

							}

						
							$tbl3 .= "	
								<tr>
									<td colspan =\"3\" style=\"border-top: 1px solid black;\"></td>
									<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;font-weight:bold;text-align:center\">Subtotal </div></td>
									<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalprice, 2)."</div></td>								
									<td colspan =\"2\" style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalsaldoprice, 2)."</div></td>
									<td colspan =\"2\" style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalangs, 2)."</div></td>
									<td colspan =\"2\" style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalakdenda, 2)."</div></td>
									<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totaladm, 2)."</div></td>

								</tr>";
								$grandtotalprice 		+= $totalprice;
								//$grandtotalmargin		+= $totalmargin;
								$grandtotalsaldoprice 	+= $totalsaldoprice;
								$grandtotalangs			+= $totalangs;
								$grandtotalakdenda		+= $totalakdenda;
								$grandtotaladm			+= $totaladm;
						}
					}
					
					
							

					$tbl4 = "
						<br>	
						<tr>
							<td colspan =\"3\" style=\"border-top: 1px solid black;\"><div style=\"font-size:9;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctCreditsAccountOfficerReport_model->getUserName($auth['user_id'])."</div></td>
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;font-weight:bold;text-align:center\">Jumlah </div></td>
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($grandtotalprice, 2)."</div></td>							
							<td colspan =\"2\"  style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($grandtotalsaldoprice, 2)."</div></td>
							<td colspan =\"2\"  style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($grandtotalangs, 2)."</div></td>
							<td colspan =\"2\" style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($grandtotalakdenda, 2)."</div></td>
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($grandtotaladm, 2)."</div></td>							


						</tr>								
					</table>";

					$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');
			} else {
				$tbl = "
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
					    <tr>
					        <td><div style=\"text-align: left;font-size:10; font-weight:bold\">DAFTAR NASABAH PINJAMAN</div></td>
					        <td><div style=\"text-align: left;font-size:10; font-weight:bold\">Mulai Tgl. ".tgltoview($sesi['start_date'])." S.D ".tgltoview($sesi['end_date'])."</div></td>			       
					    </tr>						
					</table>";

					$pdf->writeHTML($tbl, true, false, false, false, '');

					$tbl1 = "
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
					    <tr>
					        <td width=\"2%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>

					        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No. Kredit</div></td>

					        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nama</div></td>

					         <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Alamat</div></td>

					        <td width=\"3%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">BO</div></td>

					        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Pokok</div></td>

					        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Bunga</div></td>

					        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Sisa Pokok</div></td>

					        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Jangka Waktu</div></td>

					        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Angsuran</div></td>

					        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Keterlambatan</div></td>

					        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Ak Denda</div></td>

					        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Pdptn Adm</div></td>

					    </tr>				
					</table>";

					$no = 1;
				
					$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
					foreach ($acctcredits as $kC => $vC) {
						$acctcreditsaccount 		= $this->AcctCreditsAccountOfficerReport_model->getAcctCreditsAccount($sesi['office_id'], $sesi['start_date'], $sesi['end_date'], $vC['credits_id'], $branch_id);


						if(!empty($acctcreditsaccount)){
							$tbl3 .= "
								<br>
								<tr>
									<td colspan =\"11\" style=\"border-bottom: 1px solid black;\"><div style=\"font-size:10\">".$vC['credits_name']."</div></td>
								</tr>
							";

							$no = 1;
							$totalprice 		=0;
							$totalmargin 		=0;
							$totalsaldoprice 	=0;
							$totalangs			=0;
							$totalakdenda		=0;
							$totaladm			=0;
								$grandtotalprice 		= 0;
								//$grandtotalmargin		= 0;
								$grandtotalsaldoprice 	= 0;
								$grandtotalangs			= 0;
								$grandtotalakdenda		= 0;
								$grandtotaladm			= 0;
							foreach ($acctcreditsaccount as $key => $val) {	
								$day 		= date('d-m-Y');
								$start_ 	= new DateTime($day);
								$end_ 		= new DateTime($val['credits_account_payment_date']);

								if($end_ >= $start_){
									$Keterlambatan 	= '0';
									// $interval 	= $start_->diff($end_);
								}else{
									$interval 		= $start_->diff($end_);
									$Keterlambatan 	= $interval->days;
								}

								$acctcredits_fine	= $this->AcctCreditAccount_model->getDetailByID($val['credits_account_id']);

								$credits_payment_fine_amount 		= (($val['credits_account_payment_amount'] * $acctcredits_fine['credits_fine']) / 100 ) * $Keterlambatan;

								$credits_account_accumulated_fines 	= $val['credits_account_accumulated_fines'] + $credits_payment_fine_amount;	
				
								$tbl3 .= "
									<tr>
								    	<td width=\"2%\"><div style=\"text-align: left;\">".$no."</div></td>

								        <td width=\"10%\"><div style=\"text-align: left;\">".$val['credits_account_serial']."</div></td>

								        <td width=\"10%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>

								        <td width=\"15%\"><div style=\"text-align: left;\">".$val['member_address']."</div></td>

								        <td width=\"3%\"><div style=\"text-align: center;\">".$this->AcctCreditsAccountOfficerReport_model->getOfficeCode($val['office_id'])."</div></td>

								        <td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_account_amount'], 2)."</div></td>

								        <td width=\"5%\"><div style=\"text-align: right;\">".number_format($val['credits_account_interest'], 2)."</div></td>

								        <td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_account_last_balance'], 2)."</div></td>

								        <td width=\"5%\"><div style=\"text-align: center;\">".$val['credits_account_period']."</div></td>

								        <td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_account_payment_amount'],2)."</div></td>

								        <td width=\"10%\"><div style=\"text-align: right;\">".$Keterlambatan."</div></td>

								        <td width=\"5%\"><div style=\"text-align: right;\">".number_format($credits_account_accumulated_fines, 2)."</div></td>

								        <td width=\"5%\"><div style=\"text-align: right;\">".number_format($val['credits_account_adm_cost'], 2)."</div></td>
								        
								    </tr>
								";
								$no++;

								$totalprice 		+= $val['credits_account_amount'];
								//$totalmargin 		+= $val['credits_account_interest'];
								$totalsaldoprice 	+= $val['credits_account_last_balance'];
								$totalangs			+= $val['credits_account_payment_amount'];
								$totalakdenda		+= $val['credits_account_accumulated_fines'];
								$totaladm			+= $val['credits_account_adm_cost'];
							}

							

							$tbl3 .= "	
								<tr>
									<td colspan =\"5\" style=\"border-top: 1px solid black\"><div style=\"font-size:9;font-weight:bold;text-align:right\">Subtotal </div></td>
									
									<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalprice, 2)."</div></td>									
									<td colspan =\"2\" style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalsaldoprice, 2)."</div></td>
									
									<td colspan =\"2\" style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalangs, 2)."</div></td>
									
									<td colspan =\"2\" style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalakdenda, 2)."</div></td>		
									
									<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totaladm, 2)."</div></td>									


								</tr>";

							$grandtotalprice 		+= $totalprice;
							//$grandtotalmargin		+= $totalmargin;
							$grandtotalsaldoprice 	+= $totalsaldoprice;
							$grandtotalangs			+= $totalangs;
							$grandtotalakdenda		+= $totalakdenda;
							$grandtotaladm			+= $totaladm;
						}
					}
					

					$tbl4 = "
						<br>	
						<tr>
							<td colspan =\"3\" style=\"border-top: 1px solid black;\"><div style=\"font-size:9;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctCreditsAccountOfficerReport_model->getUserName($auth['user_id'])."</div></td>
							
							<td colspan =\"2\" style=\"border-top: 1px solid black\"><div style=\"font-size:9;font-weight:bold;text-align:right\">Jumlah </div></td>
							
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($grandtotalprice, 2)."</div></td>							
							
							<td colspan =\"2\" style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($grandtotalsaldoprice, 2)."</div></td>
							
							<td colspan =\"2\" style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($grandtotalangs, 2)."</div></td>	
							
							<td  colspan =\"2\" style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($grandtotalakdenda, 2)."</div></td>	
							
							<td  style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($grandtotaladm, 2)."</div></td>	

						</tr>								
					</table>";

					$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');
			}
			


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Laporan Daftar Nasabah Per BO.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function export($sesi){	
			$auth = $this->session->userdata('auth');
			
			if($auth['branch_status'] == 1){
				if($sesi['branch_id'] == '' || $sesi['branch_id'] == 0){
					$branch_id = '';
				} else {
					$branch_id = $sesi['branch_id'];
				}
			} else {
				$branch_id = $auth['branch_id'];
			}

			$acctcredits 			= $this->AcctCreditsAccountOfficerReport_model->getAcctCredits();
			$preferencecompany 		= $this->AcctCreditsAccountOfficerReport_model->getPreferenceCompany();

			
			if(count($acctcredits) != ''){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("CST FISRT")
									 ->setLastModifiedBy("CST FISRT")
									 ->setTitle("Laporan Nasabah Pinjaman")
									 ->setSubject("")
									 ->setDescription("Laporan Nasabah Pinjaman")
									 ->setKeywords("Laporan, Nasabah, Pinjaman")
									 ->setCategory("Laporan Nasabah Pinjaman");
									 
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
				$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(20);	

				if(empty($sesi['office_id'])){
					$this->excel->getActiveSheet()->mergeCells("B1:M1");
					$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
					
					$this->excel->getActiveSheet()->mergeCells("B2:M2");
					
					$this->excel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('B2')->getFont()->setSize(11);

					$this->excel->getActiveSheet()->getStyle('B3:M3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$this->excel->getActiveSheet()->getStyle('B3:M3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('B3:M3')->getFont()->setBold(true);
					$this->excel->getActiveSheet()->setCellValue('B1',"DAFTAR NASABAH PINJAMAN");
				} else {
					$this->excel->getActiveSheet()->mergeCells("B1:M1");
					$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
					
					$this->excel->getActiveSheet()->mergeCells("B2:M2");
					
					$this->excel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('B2')->getFont()->setSize(11);

					$this->excel->getActiveSheet()->getStyle('B3:M3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$this->excel->getActiveSheet()->getStyle('B3:M3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('B3:M3')->getFont()->setBold(true);
					$this->excel->getActiveSheet()->setCellValue('B1',"DAFTAR NASABAH PINJAMAN ".$this->AcctCreditsAccountOfficerReport_model->getOfficeName($sesi['office_id']));
				}
					$this->excel->getActiveSheet()->setCellValue('B2',"Periode : ".tgltoview($sesi['start_date'])." S.D ".tgltoview($sesi['end_date']));

					
					$this->excel->getActiveSheet()->setCellValue('B3',"No");
					$this->excel->getActiveSheet()->setCellValue('C3',"No. Rek");
					$this->excel->getActiveSheet()->setCellValue('D3',"Nama Anggota");
					$this->excel->getActiveSheet()->setCellValue('E3',"Alamat");				
					$this->excel->getActiveSheet()->setCellValue('F3',"Pokok");
					$this->excel->getActiveSheet()->setCellValue('G3',"Bunga");
					$this->excel->getActiveSheet()->setCellValue('H3',"Sisa Pokok");
					$this->excel->getActiveSheet()->setCellValue('I3',"Jangka Waktu");
					$this->excel->getActiveSheet()->setCellValue('J3',"Angsuran");
					$this->excel->getActiveSheet()->setCellValue('K3',"Keterlambatan");
					$this->excel->getActiveSheet()->setCellValue('L3',"Denda");
					$this->excel->getActiveSheet()->setCellValue('M3',"Pendapatan Administrasi");
								
				$no=0;
				$totalnominal = 0;

				if(empty($sesi['office_id'])){
					$i=4;
					foreach ($acctcredits as $k => $v) {
						$acctcreditsaccount 	= $this->AcctCreditsAccountOfficerReport_model->getAcctCreditsAccount($sesi['office_id'], $sesi['start_date'], $sesi['end_date'], $v['credits_id'], $branch_id);

					//	$acctcreditsaccount 	= $this->AcctCreditsAccountOfficerReport_model->getAcctCreditsAccount($sesi['start_date'], $sesi['end_date'], $v['credits_id'], $branch_id);

						if(!empty($acctcreditsaccount)){
						
							$this->excel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true)->setSize(14);
							$this->excel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$this->excel->getActiveSheet()->mergeCells('B'.$i.':K'.$i);
							$this->excel->getActiveSheet()->setCellValue('B'.$i, $v['credits_name']);

							foreach ($acctcreditsaccount as $k => $v) {								
								$day 		= date('d-m-Y');
								$start_ 	= new DateTime($day);
								$end_ 		= new DateTime($v['credits_account_payment_date']);

								if($end_ >= $start_){
									$Keterlambatan 	= '0';
									// $interval 	= $start_->diff($end_);
								}else{
									$interval 		= $start_->diff($end_);
									$Keterlambatan 	= $interval->days;
								}
							}

						$nov= 0;
						$j=$i+1;

						$subtotalpokok 			= 0;
						$subtotalbunga			= 0;
						$subtotalsisapokok 		= 0;
						$subtotalangs			= 0;
						$subtotalakdenda 		= 0;
						$subtotaladm 			= 0;
						$grandtotalpokok 		= 0;
						$grandtotalsisapokok 	= 0;
						$grandtotalangs 		= 0;
						$grandtotalakdenda 		= 0;
						$grandtotaladm 			= 0;

						foreach($acctcreditsaccount as $key=>$val){
							$acctcredits_fine	= $this->AcctCreditAccount_model->getDetailByID($val['credits_account_id']);

							$credits_payment_fine_amount 		= (($val['credits_account_payment_amount'] * $acctcredits_fine['credits_fine']) / 100 ) * $Keterlambatan;

							$credits_account_accumulated_fines 	= $val['credits_account_accumulated_fines'] + $credits_payment_fine_amount;	
			
							if(is_numeric($key)){
								$nov++;
								
								$this->excel->setActiveSheetIndex(0);
								$this->excel->getActiveSheet()->getStyle('B'.$j.':M'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
								$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								$this->excel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								$this->excel->getActiveSheet()->getStyle('K'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								$this->excel->getActiveSheet()->getStyle('L'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								$this->excel->getActiveSheet()->getStyle('M'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								
									$this->excel->getActiveSheet()->setCellValue('B'.$j, $nov);
									$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j, $val['credits_account_serial'],PHPExcel_Cell_DataType::TYPE_STRING);
									$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
									$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['member_address']);
									$this->excel->getActiveSheet()->setCellValue('F'.$j, number_format($val['credits_account_amount'],2));
									$this->excel->getActiveSheet()->setCellValue('G'.$j, number_format($val['credits_account_interest'],2));
									$this->excel->getActiveSheet()->setCellValue('H'.$j, number_format($val['credits_account_last_balance'],2));
									$this->excel->getActiveSheet()->setCellValue('I'.$j, $val['credits_account_period']);
									$this->excel->getActiveSheet()->setCellValue('J'.$j, number_format($val['credits_account_payment_amount'],2));
									$this->excel->getActiveSheet()->setCellValue('K'.$j, $Keterlambatan);
									$this->excel->getActiveSheet()->setCellValue('L'.$j, number_format($credits_account_accumulated_fines,2));
									$this->excel->getActiveSheet()->setCellValue('M'.$j, number_format($val['credits_account_adm_cost'],2));
								
							}else{
								continue;
							}
							$j++;
							$subtotalpokok 		+= $val['credits_account_amount'];
							$subtotalsisapokok 	+= $val['credits_account_last_balance'];
							$subtotalangs		+= $val['credits_account_payment_amount'];
							$subtotalakdenda 	+= $credits_account_accumulated_fines;
							$subtotaladm 		+= $val['credits_account_adm_cost'];
						}

						

						$m = $j;

						$this->excel->getActiveSheet()->getStyle('B'.$m.':M'.$m)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
						$this->excel->getActiveSheet()->getStyle('B'.$m.':M'.$m)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->mergeCells('B'.$m.':E'.$m);
						$this->excel->getActiveSheet()->setCellValue('B'.$m, 'SubTotal');

						$this->excel->getActiveSheet()->setCellValue('F'.$m, number_format($subtotalpokok,2));
						$this->excel->getActiveSheet()->setCellValue('H'.$m, number_format($subtotalsisapokok,2));
						$this->excel->getActiveSheet()->setCellValue('J'.$m, number_format($subtotalangs,2));
						$this->excel->getActiveSheet()->setCellValue('L'.$m, number_format($subtotalakdenda,2));
						$this->excel->getActiveSheet()->setCellValue('M'.$m, number_format($subtotaladm,2));

						$i = $m + 1;
						
						$grandtotalpokok 		+= $subtotalpokok;
						$grandtotalsisapokok 	+= $subtotalsisapokok;
						$grandtotalangs 		+= $subtotalangs;
						$grandtotalakdenda 		+= $subtotalakdenda;
						$grandtotaladm 			+= $subtotaladm;
						}

					}
					

				} else {
				
					$i=4;
				
					foreach ($acctcredits as $k => $v) {
					$acctcreditsaccount 	= $this->AcctCreditsAccountOfficerReport_model->getAcctCreditsAccount($sesi['office_id'], $sesi['start_date'], $sesi['end_date'], $v['credits_id'], $branch_id);

						if(!empty($acctcreditsaccount)){
						
							$this->excel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true)->setSize(14);
							$this->excel->getActiveSheet()->getStyle('B'.$i.':M'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$this->excel->getActiveSheet()->mergeCells('B'.$i.':M'.$i);
							$this->excel->getActiveSheet()->setCellValue('B'.$i, $v['credits_name']);


							foreach ($acctcreditsaccount as $k => $v) {								
								$day 		= date('d-m-Y');
								$start_ 	= new DateTime($day);
								$end_ 		= new DateTime($v['credits_account_payment_date']);

								if($end_ >= $start_){
									$Keterlambatan 	= '0';
									// $interval 	= $start_->diff($end_);
								}else{
									$interval 		= $start_->diff($end_);
									$Keterlambatan 	= $interval->days;
								}
							}

						$nov= 0;
						$j=$i+1;

						$subtotalpokok 			= 0;
						$subtotalbunga			= 0;
						$subtotalsisapokok 		= 0;
						$subtotalangs			= 0;
						$subtotalakdenda 		= 0;
						$subtotaladm	 		= 0;
						$grandtotalpokok 		= 0;
						$grandtotalsisapokok 	= 0;
						$grandtotalangs 		= 0;
						$grandtotalakdenda 		= 0;
						$grandtotaladm 			= 0;

						foreach($acctcreditsaccount as $key=>$val){
							$acctcredits_fine	= $this->AcctCreditAccount_model->getDetailByID($val['credits_account_id']);

							$credits_payment_fine_amount 		= (($val['credits_account_payment_amount'] * $acctcredits_fine['credits_fine']) / 100 ) * $Keterlambatan;

							$credits_account_accumulated_fines 	= $val['credits_account_accumulated_fines'] + $credits_payment_fine_amount;	

							if(is_numeric($key)){
								$nov++;
								
								$this->excel->setActiveSheetIndex(0);
								$this->excel->getActiveSheet()->getStyle('B'.$j.':M'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
								$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								$this->excel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								$this->excel->getActiveSheet()->getStyle('K'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								$this->excel->getActiveSheet()->getStyle('L'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								$this->excel->getActiveSheet()->getStyle('M'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								
									$this->excel->getActiveSheet()->setCellValue('B'.$j, $nov);
									$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j, $val['credits_account_serial'],PHPExcel_Cell_DataType::TYPE_STRING);
									$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
									$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['member_address']);
									
									$this->excel->getActiveSheet()->setCellValue('F'.$j, number_format($val['credits_account_amount'],2));
									$this->excel->getActiveSheet()->setCellValue('G'.$j, number_format($val['credits_account_interest'],2));
									$this->excel->getActiveSheet()->setCellValue('H'.$j, number_format($val['credits_account_last_balance'],2));
									$this->excel->getActiveSheet()->setCellValue('I'.$j, $val['credits_account_period']);
									$this->excel->getActiveSheet()->setCellValue('J'.$j, number_format($val['credits_account_payment_amount'],2));
									$this->excel->getActiveSheet()->setCellValue('K'.$j, $Keterlambatan);

									$this->excel->getActiveSheet()->setCellValue('L'.$j, number_format($credits_account_accumulated_fines ,2));
									$this->excel->getActiveSheet()->setCellValue('M'.$j, number_format($val['credits_account_adm_cost'] ,2));
								
							}else{
								continue;
							}
							$j++;
							$subtotalpokok 		+= $val['credits_account_amount'];
							$subtotalsisapokok 	+= $val['credits_account_last_balance'];
							$subtotalangs		+= $val['credits_account_payment_amount'];
							$subtotalakdenda 	+= $credits_account_accumulated_fines;
							$subtotaladm 		+= $val['credits_account_adm_cost'];
						}

						$grandtotalpokok 		+= $subtotalpokok;
						$grandtotalsisapokok 	+= $subtotalsisapokok;
						$grandtotalangs 		+= $subtotalangs;
						$grandtotalakdenda 		+= $subtotalakdenda;
						$grandtotaladm 			+= $subtotaladm;

						$m = $j;
						
						$this->excel->getActiveSheet()->getStyle('B'.$m.':M'.$m)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
						$this->excel->getActiveSheet()->getStyle('B'.$m.':M'.$m)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->mergeCells('B'.$m.':E'.$m);
						$this->excel->getActiveSheet()->setCellValue('B'.$m, 'SubTotal');

						$this->excel->getActiveSheet()->setCellValue('F'.$m, number_format($subtotalpokok,2));
						$this->excel->getActiveSheet()->setCellValue('H'.$m, number_format($subtotalsisapokok,2));
						$this->excel->getActiveSheet()->setCellValue('J'.$m, number_format($subtotalangs,2));
						$this->excel->getActiveSheet()->setCellValue('L'.$m, number_format($subtotalakdenda,2));
						$this->excel->getActiveSheet()->setCellValue('M'.$m, number_format($subtotaladm,2));

						$i = $m + 1;
						}
					//}					
					}
					
				}
				//print_r($grandtotalpokok); exit;
				$n = $i;
				
					$this->excel->getActiveSheet()->getStyle('B'.$n.':M'.$n)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
					$this->excel->getActiveSheet()->getStyle('B'.$n.':M'.$n)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$this->excel->getActiveSheet()->mergeCells('B'.$n.':E'.$n);
					$this->excel->getActiveSheet()->setCellValue('B'.$n, 'Total');

					$this->excel->getActiveSheet()->setCellValue('F'.$n, number_format($grandtotalpokok,2));
					$this->excel->getActiveSheet()->setCellValue('H'.$n, number_format($grandtotalsisapokok,2));
					$this->excel->getActiveSheet()->setCellValue('J'.$n, number_format($grandtotalangs,2));
					$this->excel->getActiveSheet()->setCellValue('L'.$n, number_format($grandtotalakdenda,2));
					$this->excel->getActiveSheet()->setCellValue('M'.$n, number_format($grandtotaladm,2));
				// 
				$filename='Laporan Daftar Nasabah Pinjaman Per BO.xls';
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