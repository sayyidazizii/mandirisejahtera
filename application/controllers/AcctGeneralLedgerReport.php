<?php ob_start(); ?>
<?php defined('BASEPATH') OR exit('No direct script access allowed');

	Class AcctGeneralLedgerReport extends CI_Controller{
		public function __construct(){
			parent::__construct();

			// $menu = 'ledger';

			// $this->cekLogin();
			// $this->accessMenu($menu);

			$this->load->model('MainPage_model');
			$this->load->model('Connection_model');
			$this->load->model('AcctGeneralLedgerReport_model');
			$this->load->helper('sistem');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
			$this->load->helper('url');
			$this->load->database('default');
		}
		
		public function index(){
			$this->load->model('AcctGeneralLedgerReport_model');

			$auth 	= $this->session->userdata('auth');
			$sesi	= 	$this->session->userdata('filter-AcctGeneralLedgerReport');
			if(!is_array($sesi)){
				$sesi['month_period_start']		= '';
				$sesi['month_period_end']		= '';
				$sesi['year_period']			= date('Y');
				$sesi['account_id']				= '';
			}

			if(empty($sesi['branch_id'])){
				$sesi['branch_id']		= $auth['branch_id'];
			}

			// $start_date = tgltodb($sesi['start_date']);
			// $end_date 	= tgltodb($sesi['end_date']);

			$accountname 			= $this->AcctGeneralLedgerReport_model->getAccountName($sesi['account_id']);

			// $month_period 			= date('m', strtotime($start_date));
			// $year_period 			= date('Y', strtotime($start_date));

			if($sesi['month_period_start'] == 01){
				$last_month_start 	= 12;
				$last_year 			= $sesi['year_period'] - 1;
			} else {
				$last_month_start 	= $sesi['month_period_start'] - 1;
				$last_year			= $sesi['year_period'];
			}

			// if($sesi['month_period_end'] == 01){
			// 	$last_month_end 	= '12';
			// 	$last_year 			= $sesi['year_period'] - 1;
			// } else {
			// 	$last_month_end 	= $sesi['month_period'] - 1;
			// 	$last_year			= $sesi['year_period'];
			// }

			// print_r($sesi);
			$opening_balance 		= $this->AcctGeneralLedgerReport_model->getOpeningBalance($sesi['account_id'], $last_month_start, $last_year, $sesi['branch_id']);
			//print_r("a ".$opening_balance);exit;

			if(empty($opening_balance)){
				$opening_balance 	= $this->AcctGeneralLedgerReport_model->getLastBalance($sesi['account_id'], $last_month_start, $last_year, $sesi['branch_id']);

				//print_r("a ".$opening_balance);exit;
			}

			//print_r($opening_balance1);
			//print_r($opening_balance1);exit;

			$account_in_amount 		= $this->AcctGeneralLedgerReport_model->getAccountIn($sesi['account_id'], $last_month_start, $last_year, $sesi['branch_id']);

			$account_out_amount 		= $this->AcctGeneralLedgerReport_model->getAccountOut($sesi['account_id'], $last_month_start, $last_year, $sesi['branch_id']);

			$opening_balance_amount = ($opening_balance + $account_in_amount) - $account_out_amount;

			//print_r($opening_balance);exit;

			$account_id_status 		= $this->AcctGeneralLedgerReport_model->getAccountIDDefaultStatus($sesi['account_id']);

			$accountbalancedetail	= $this->AcctGeneralLedgerReport_model->getAcctAccountBalanceDetail($sesi['account_id'], $sesi['month_period_start'], $sesi['month_period_end'], $sesi['year_period'], $sesi['branch_id']);

			if(!empty($accountbalancedetail)){
				$last_balance 		= $opening_balance_amount;
				foreach ($accountbalancedetail as $key => $val) {
					$description 		= $this->AcctGeneralLedgerReport_model->getJournalVoucherDescription($val['transaction_id'], $val['account_id']);

					$journal_voucher_no = $this->AcctGeneralLedgerReport_model->getJournalVoucherNo($val['transaction_id']);

					$last_balance = ($last_balance + $val['account_in']) - $val['account_out'];

					if($account_id_status == 0 ){
						$debet 	= $val['account_in'];
						$kredit = $val['account_out'];

						if($last_balance >= 0){
							$last_balance_debet 	= $last_balance;
							$last_balance_kredit 	= 0;
						} else {
							$last_balance_debet 	= 0;
							$last_balance_kredit 	= $last_balance;
						}
					} else {
						$debet 	= $val['account_out'];
						$kredit = $val['account_in'];

						if($last_balance >= 0){
							$last_balance_debet 	= 0;
							$last_balance_kredit 	= $last_balance;
						} else {
							
							$last_balance_debet 	= $last_balance;
							$last_balance_kredit 	= 0;
						}
					}

					

					$data_acctaccountbalance[] = array (
						'transaction_date'			=> $val['transaction_date'],
						'transaction_no'			=> $journal_voucher_no,
						'transaction_description'	=> $description,
						'account_name'				=> $accountname,
						'account_in'				=> $debet,
						'account_out'				=> $kredit,
						'last_balance_debet'		=> $last_balance_debet,
						'last_balance_credit'		=> $last_balance_kredit,
					);
				}

				$count_data = count($accountbalancedetail);

				$rows 		= ceil($count_data / 400);
				$rowsexcel 	= ceil($count_data / 1000);
			} else {
				$data_acctaccountbalance = array ();

				$count_data = 0;

				$rows 		= 0;

				$rowsexcel 	= 0;
			}
			


			//print_r($data_acctaccountbalance);exit;

			

			
			$data['main_view']['AcctGeneralLedgerReport']	= $data_acctaccountbalance;

			$data['main_view']['opening_balance']			= $opening_balance_amount;

			$data['main_view']['account_id_status']			= $account_id_status;

			$data['main_view']['monthlist']					= $this->configuration->Month();

			$data['main_view']['file']						= $rows;

			$data['main_view']['fileexcel']					= $rowsexcel;
			
			$data['main_view']['acctaccount']				= create_double($this->AcctGeneralLedgerReport_model->getAcctAccount(),'account_id','account_name');

			$data['main_view']['corebranch']				= create_double($this->AcctGeneralLedgerReport_model->getCoreBranch(),'branch_id','branch_name');				

			$data['main_view']['content']					= 'AcctGeneralLedgerReport/ListAcctGeneralLedgerReport_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function filter(){
			$data = array (
				"month_period_start" 		=> $this->input->post('month_period_start',true),
				"month_period_end" 			=> $this->input->post('month_period_end',true),
				'year_period'				=> $this->input->post('year_period',true),
				'account_id'				=> $this->input->post('account_id',true),
				'branch_id'					=> $this->input->post('branch_id',true),
			);

			//print_r($data);exit;
			$this->session->set_userdata('filter-AcctGeneralLedgerReport',$data);
			redirect('general-ledger-report');
		}
		
		public function reset_data(){
	
			$sesi= $this->session->userdata('filter-AcctGeneralLedgerReport');

			$this->session->unset_userdata('filter-AcctGeneralLedgerReport');
			redirect('general-ledger-report');
		}

		public function pdf(){
			$baris 	= $this->uri->segment(3);
			$file 	= $this->uri->segment(4);


			// print_r($key);exit;

			$auth 	= $this->session->userdata('auth');
			$sesi	= 	$this->session->userdata('filter-AcctGeneralLedgerReport');
			if(!is_array($sesi)){
				$sesi['month_period_start']		= '';
				$sesi['month_period_end']		= '';
				$sesi['year_period']			= date('Y');
				$sesi['account_id']				= '';
			}

			if(empty($sesi['branch_id'])){
				$sesi['branch_id']		= $auth['branch_id'];
			}

			// $start_date = tgltodb($sesi['start_date']);
			// $end_date 	= tgltodb($sesi['end_date']);

			$accountname 			= $this->AcctGeneralLedgerReport_model->getAccountName($sesi['account_id']);

			// $month_period 			= date('m', strtotime($start_date));
			// $year_period 			= date('Y', strtotime($start_date));

			if($sesi['month_period_start'] == 01){
				$last_month_start 	= 12;
				$last_year 			= $sesi['year_period'] - 1;
			} else {
				$last_month_start 	= $sesi['month_period_start'] - 1;
				$last_year			= $sesi['year_period'];
			}

			// if($sesi['month_period_end'] == 01){
			// 	$last_month_end 	= '12';
			// 	$last_year 			= $sesi['year_period'] - 1;
			// } else {
			// 	$last_month_end 	= $sesi['month_period'] - 1;
			// 	$last_year			= $sesi['year_period'];
			// }

			// print_r($sesi);
			$opening_balance 		= $this->AcctGeneralLedgerReport_model->getOpeningBalance($sesi['account_id'], $last_month_start, $last_year, $sesi['branch_id']);

			if(empty($opening_balance)){
				$opening_balance 	= $this->AcctGeneralLedgerReport_model->getLastBalance($sesi['account_id'], $last_month_start, $last_year, $sesi['branch_id']);

				//print_r("a ".$opening_balance);exit;
			}

			//print_r($opening_balance1);
			//print_r($opening_balance1);exit;

			$account_in_amount 		= $this->AcctGeneralLedgerReport_model->getAccountIn($sesi['account_id'], $last_month_start, $last_year, $sesi['branch_id']);

			$account_out_amount 		= $this->AcctGeneralLedgerReport_model->getAccountOut($sesi['account_id'], $last_month_start, $last_year, $sesi['branch_id']);

			$opening_balance_amount = ($opening_balance + $account_in_amount) - $account_out_amount;

			//print_r($opening_balance);exit;

			$account_id_status 		= $this->AcctGeneralLedgerReport_model->getAccountIDDefaultStatus($sesi['account_id']);

			$accountbalancedetail	= $this->AcctGeneralLedgerReport_model->getAcctAccountBalanceDetail($sesi['account_id'], $sesi['month_period_start'], $sesi['month_period_end'], $sesi['year_period'], $sesi['branch_id']);

			$no = 0;
			if(!empty($accountbalancedetail)){
				$last_balance 		= $opening_balance_amount;
				foreach ($accountbalancedetail as $key => $val) {
					

					$description = $this->AcctGeneralLedgerReport_model->getJournalVoucherDescription($val['transaction_id'], $val['account_id']);

					$last_balance = ($last_balance + $val['account_in']) - $val['account_out'];

					if($account_id_status == 0 ){
						$debet 	= $val['account_in'];
						$kredit = $val['account_out'];

						if($last_balance >= 0){
							$last_balance_debet 	= $last_balance;
							$last_balance_kredit 	= 0;
						} else {
							$last_balance_debet 	= 0;
							$last_balance_kredit 	= $last_balance;
						}
					} else {
						$debet 	= $val['account_out'];
						$kredit = $val['account_in'];

						if($last_balance >= 0){
							$last_balance_debet 	= 0;
							$last_balance_kredit 	= $last_balance;
						} else {
							
							$last_balance_debet 	= $last_balance;
							$last_balance_kredit 	= 0;
						}
					}

					

					$data_acctaccountbalance[] = array (
						'no'						=> $no,
						'transaction_date'			=> $val['transaction_date'],
						'transaction_description'	=> $description,
						'account_name'				=> $accountname,
						'account_in'				=> $debet,
						'account_out'				=> $kredit,
						'last_balance_debet'		=> $last_balance_debet,
						'last_balance_credit'		=> $last_balance_kredit,
					);
					
				}
				$sisa = $no % 400;
			} else {
				$data_acctaccountbalance = array ();
				$sisa = 0;
			}

			

			// print_r($data_acctaccountbalance);exit;

			for ($i=0; $i <= $baris ; $i++) {
				
				if($i == $baris){
					// print_r("a");exit;
					$rows = $sisa;
				} else {
					$rows = 400;
				}

				$array_terpecah[$i] = array_splice($data_acctaccountbalance, 0, $rows);

				// print_r($array_terpecah);exit;

				
			}

			$datacetak = $array_terpecah[$file];

			// print_r($array_terpecah[$file]);exit;

			$this->processPrinting($datacetak);
		}
		

		public function processPrinting($data){
			$this->load->model('AcctGeneralLedgerReport_model');

			$auth 	= $this->session->userdata('auth');
			$sesi	= 	$this->session->userdata('filter-AcctGeneralLedgerReport');
			if(!is_array($sesi)){
				$sesi['month_period_start']		= '';
				$sesi['month_period_end']		= '';
				$sesi['year_period']			= date('Y');
				$sesi['account_id']				= '';
			}

			if(empty($sesi['branch_id'])){
				$sesi['branch_id']		= $auth['branch_id'];
			}

			// $start_date = tgltodb($sesi['start_date']);
			// $end_date 	= tgltodb($sesi['end_date']);

			$accountname 			= $this->AcctGeneralLedgerReport_model->getAccountName($sesi['account_id']);

			// $month_period 			= date('m', strtotime($start_date));
			// $year_period 			= date('Y', strtotime($start_date));

			if($sesi['month_period_start'] == 01){
				$last_month_start 	= 12;
				$last_year 			= $sesi['year_period'] - 1;
			} else {
				$last_month_start 	= $sesi['month_period_start'] - 1;
				$last_year			= $sesi['year_period'];
			}

			// if($sesi['month_period_end'] == 01){
			// 	$last_month_end 	= '12';
			// 	$last_year 			= $sesi['year_period'] - 1;
			// } else {
			// 	$last_month_end 	= $sesi['month_period'] - 1;
			// 	$last_year			= $sesi['year_period'];
			// }

			// print_r($sesi);
			$opening_balance 		= $this->AcctGeneralLedgerReport_model->getOpeningBalance($sesi['account_id'], $last_month_start, $last_year, $sesi['branch_id']);

			if(empty($opening_balance)){
				$opening_balance 	= $this->AcctGeneralLedgerReport_model->getLastBalance($sesi['account_id'], $last_month_start, $last_year, $sesi['branch_id']);

				//print_r("a ".$opening_balance);exit;
			}

			//print_r($opening_balance1);
			//print_r($opening_balance1);exit;

			$account_in_amount 		= $this->AcctGeneralLedgerReport_model->getAccountIn($sesi['account_id'], $last_month_start, $last_year, $sesi['branch_id']);

			$account_out_amount 		= $this->AcctGeneralLedgerReport_model->getAccountOut($sesi['account_id'], $last_month_start, $last_year, $sesi['branch_id']);

			$opening_balance_amount = ($opening_balance + $account_in_amount) - $account_out_amount;

			//print_r($opening_balance);exit;

			$account_id_status 		= $this->AcctGeneralLedgerReport_model->getAccountIDDefaultStatus($sesi['account_id']);

			$accountbalancedetail	= $this->AcctGeneralLedgerReport_model->getAcctAccountBalanceDetail($sesi['account_id'], $sesi['month_period_start'], $sesi['month_period_end'], $sesi['year_period'], $sesi['branch_id']);

			$motnhname 				= $this->configuration->Month();
			$accounstatus 			= $this->configuration->AccountStatus();
			$preferencecompany 		= $this->AcctGeneralLedgerReport_model->getPreferenceCompany();




			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(10, 10, 10, 10); // put space of 10 on top

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

			$pdf->SetFont('helvetica', '', 8);

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
			<table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
			    <tr>
			        <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">BUKU BESAR</div></td>
			    </tr>
			    <tr>
			    	<td><div style=\"text-align: center; font-size:12px\">PERIODE : ".$motnhname[$sesi['month_period']]." ".$sesi['year_period']."</div></td>
			    </tr>
			</table>
			";
			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl = "
			<br>
			<br>
			<table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: lef=ft; font-size:12px;font-weight: bold\">Nama. Perkiraan</div></td>
			        <td width=\"5%\"><div style=\"text-align: center; font-size:12px; font-weight: bold\">:</div></td>
			        <td width=\"65%\"><div style=\"text-align: left; font-size:12px; font-weight: bold\">".$accountname."</div></td>
				</tr>
				<tr>
			        <td width=\"20%\"><div style=\"text-align: lef=ft; font-size:12px;font-weight: bold\">Posisi Saldo</div></td>
			        <td width=\"5%\"><div style=\"text-align: center; font-size:12px; font-weight: bold\">:</div></td>
			        <td width=\"65%\"><div style=\"text-align: left; font-size:12px; font-weight: bold\">".$accounstatus[$account_id_status]."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: lef=ft; font-size:12px;font-weight: bold\">Saldo Awal</div></td>
			        <td width=\"5%\"><div style=\"text-align: center; font-size:12px; font-weight: bold\">:</div></td>
			        <td width=\"65%\"><div style=\"text-align: left; font-size:12px; font-weight: bold\">".number_format($opening_balance_amount, 2)."</div></td>
			    </tr>
			</table>";
			$pdf->writeHTML($tbl, true, false, false, false, '');
			

			$no = 1;
			$tblStock1 = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
			    <tr>
			        <td width=\"5%\" rowspan=\"2\"><div style=\"text-align: center;\">No</div></td>
			        <td width=\"12%\" rowspan=\"2\"><div style=\"text-align: center;\">Tanggal</div></td>
			        <td width=\"25%\" rowspan=\"2\"><div style=\"text-align: center;\">Uraian</div></td>
			        <td width=\"15%\" rowspan=\"2\"><div style=\"text-align: center;\">Debet </div></td>
			        <td width=\"15%\" rowspan=\"2\"><div style=\"text-align: center;\">Kredit </div></td>
			        <td width=\"30%\" colspan=\"2\"><div style=\"text-align: center;\">Saldo </div></td>
				</tr>
				
				<tr>
			        <td width=\"15%\"><div style=\"text-align: center;\">Debet </div></td>
			        <td width=\"15%\"><div style=\"text-align: center;\">Kredit </div></td>
			    </tr>
			
			     ";

			$no = 1;
			foreach ($data as $key => $val) {
				$tblStock2 .="
						<tr>			
							<td style=\"text-align:center\">$no.</td>
							<td style=\"text-align:center\">".tgltoview($val['transaction_date'])."</td>
							<td>".$val['transaction_description']."</td>
							<td><div style=\"text-align: right;\">".number_format($val['account_in'], 2)."</div></td>
							<td><div style=\"text-align: right;\">".number_format($val['account_out'], 2)."</div></td>
							<td><div style=\"text-align: right;\">".number_format($val['last_balance_debet'], 2)."</div></td>
							<td><div style=\"text-align: right;\">".number_format($val['last_balance_credit'], 2)."</div></td>
						</tr>
						
					";

					$total_debit += $val['account_in'];

					$total_kredit += $val['account_out'];

					$total_last_balance_debet += $val['last_balance_debet'];

					$total_last_balance_kredit += $val['last_balance_kredit']; 
				$no++;
			}

			// $tblStock4 = "
			// 		<tr>
			// 			<td colspan =\"2\" style=\"border-top: 1px solid black;\"><div style=\"font-size:10;text-align:left;font-style:italic\"></div></td>
			// 			<td style=\"border-top: 1px solid black\"><div style=\"font-size:8;font-weight:bold;text-align:center\">Total Debit Kredit </div></td>
			// 			<td style=\"border-top: 1px solid black\"><div style=\"font-size:8;text-align:right\">".number_format($total_debit, 2)."</div></td>
			// 			<td style=\"border-top: 1px solid black\"><div style=\"font-size:8;text-align:right\">".number_format($total_kredit, 2)."</div></td>
			// 		</tr>";

			// $tblStock5 = "
			// 		<tr>
			// 			<td colspan =\"2\" style=\"border-top: 1px solid black;\"><div style=\"font-size:10;text-align:left;font-style:italic\"></div></td>
			// 			<td style=\"border-top: 1px solid black\"><div style=\"font-size:8;font-weight:bold;text-align:center\">Saldo Akhir </div></td>
			// 			<td style=\"border-top: 1px solid black\"><div style=\"font-size:8;text-align:right\"></div></td>
			// 			<td style=\"border-top: 1px solid black\"><div style=\"font-size:8;text-align:right\"></div></td>
			// 			<td style=\"border-top: 1px solid black\"><div style=\"font-size:8;text-align:right\">".number_format($total_last_balance_debet, 2)."</div></td>
			// 			<td style=\"border-top: 1px solid black\"><div style=\"font-size:8;text-align:right\">".number_format($total_last_balance_kredit, 2)."</div></td>
			// 		</tr>";
			$tblStock6 = " </table>";

			$pdf->writeHTML($tblStock1.$tblStock2.$tblStock4.$tblStock5.$tblStock6, true, false, false, false, '');
			

			ob_clean();


			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Buku_Besar_'.$accountname.'.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function export(){
			$baris 	= $this->uri->segment(3);
			$file 	= $this->uri->segment(4);


			// print_r($key);exit;

			$auth 	= $this->session->userdata('auth');
			$sesi	= 	$this->session->userdata('filter-AcctGeneralLedgerReport');
			if(!is_array($sesi)){
				$sesi['month_period_start']		= '';
				$sesi['month_period_end']		= '';
				$sesi['year_period']			= date('Y');
				$sesi['account_id']				= '';
			}

			if(empty($sesi['branch_id'])){
				$sesi['branch_id']		= $auth['branch_id'];
			}

			// $start_date = tgltodb($sesi['start_date']);
			// $end_date 	= tgltodb($sesi['end_date']);

			$accountname 			= $this->AcctGeneralLedgerReport_model->getAccountName($sesi['account_id']);

			// $month_period 			= date('m', strtotime($start_date));
			// $year_period 			= date('Y', strtotime($start_date));

			if($sesi['month_period_start'] == 01){
				$last_month_start 	= 12;
				$last_year 			= $sesi['year_period'] - 1;
			} else {
				$last_month_start 	= $sesi['month_period_start'] - 1;
				$last_year			= $sesi['year_period'];
			}

			// if($sesi['month_period_end'] == 01){
			// 	$last_month_end 	= '12';
			// 	$last_year 			= $sesi['year_period'] - 1;
			// } else {
			// 	$last_month_end 	= $sesi['month_period'] - 1;
			// 	$last_year			= $sesi['year_period'];
			// }

			// print_r($sesi);
			$opening_balance 		= $this->AcctGeneralLedgerReport_model->getOpeningBalance($sesi['account_id'], $last_month_start, $last_year, $sesi['branch_id']);

			if(empty($opening_balance)){
				$opening_balance 	= $this->AcctGeneralLedgerReport_model->getLastBalance($sesi['account_id'], $last_month_start, $last_year, $sesi['branch_id']);

				//print_r("a ".$opening_balance);exit;
			}

			//print_r($opening_balance1);
			//print_r($opening_balance1);exit;

			$account_in_amount 		= $this->AcctGeneralLedgerReport_model->getAccountIn($sesi['account_id'], $last_month_start, $last_year, $sesi['branch_id']);

			$account_out_amount 		= $this->AcctGeneralLedgerReport_model->getAccountOut($sesi['account_id'], $last_month_start, $last_year, $sesi['branch_id']);

			$opening_balance_amount = ($opening_balance + $account_in_amount) - $account_out_amount;

			//print_r($opening_balance);exit;

			$account_id_status 		= $this->AcctGeneralLedgerReport_model->getAccountIDDefaultStatus($sesi['account_id']);

			$accountbalancedetail	= $this->AcctGeneralLedgerReport_model->getAcctAccountBalanceDetail($sesi['account_id'], $sesi['month_period_start'], $sesi['month_period_end'], $sesi['year_period'], $sesi['branch_id']);

			$no = 0;
			if(!empty($accountbalancedetail)){
				$last_balance 		= $opening_balance_amount;
				foreach ($accountbalancedetail as $key => $val) {
					

					$description = $this->AcctGeneralLedgerReport_model->getJournalVoucherDescription($val['transaction_id'], $val['account_id']);

					$last_balance = ($last_balance + $val['account_in']) - $val['account_out'];

					if($account_id_status == 0 ){
						$debet 	= $val['account_in'];
						$kredit = $val['account_out'];

						if($last_balance >= 0){
							$last_balance_debet 	= $last_balance;
							$last_balance_kredit 	= 0;
						} else {
							$last_balance_debet 	= 0;
							$last_balance_kredit 	= $last_balance;
						}
					} else {
						$debet 	= $val['account_out'];
						$kredit = $val['account_in'];

						if($last_balance >= 0){
							$last_balance_debet 	= 0;
							$last_balance_kredit 	= $last_balance;
						} else {
							
							$last_balance_debet 	= $last_balance;
							$last_balance_kredit 	= 0;
						}
					}

					

					$data_acctaccountbalance[] = array (
						'no'						=> $no,
						'transaction_date'			=> $val['transaction_date'],
						'transaction_description'	=> $description,
						'account_name'				=> $accountname,
						'account_in'				=> $debet,
						'account_out'				=> $kredit,
						'last_balance_debet'		=> $last_balance_debet,
						'last_balance_credit'		=> $last_balance_kredit,
					);
					
				}
				$sisa = $no % 1000;
			} else {
				$data_acctaccountbalance = array ();
				$sisa = 0;
			}

			

			// print_r($data_acctaccountbalance);exit;

			for ($i=0; $i <= $baris ; $i++) {
				
				if($i == $baris){
					// print_r("a");exit;
					$rows = $sisa;
				} else {
					$rows = 1000;
				}

				$array_terpecah[$i] = array_splice($data_acctaccountbalance, 0, $rows);

				// print_r($array_terpecah);exit;

				
			}

			$datacetak = $array_terpecah[$file];

			//print_r($array_terpecah[$file]);exit;

			$this->exportData($datacetak);
		}
		
		public function exportData($datacetak){
			$this->load->model('AcctGeneralLedgerReport_model');

			$auth 	= $this->session->userdata('auth');
			$sesi	= 	$this->session->userdata('filter-AcctGeneralLedgerReport');
			if(!is_array($sesi)){
				$sesi['month_period_start']		= '';
				$sesi['month_period_end']		= '';
				$sesi['year_period']			= date('Y');
				$sesi['account_id']				= '';
			}

			if(empty($sesi['branch_id'])){
				$sesi['branch_id']		= $auth['branch_id'];
			}

			// $start_date = tgltodb($sesi['start_date']);
			// $end_date 	= tgltodb($sesi['end_date']);

			$accountname 			= $this->AcctGeneralLedgerReport_model->getAccountName($sesi['account_id']);

			// $month_period 			= date('m', strtotime($start_date));
			// $year_period 			= date('Y', strtotime($start_date));

			if($sesi['month_period_start'] == 01){
				$last_month_start 	= 12;
				$last_year 			= $sesi['year_period'] - 1;
			} else {
				$last_month_start 	= $sesi['month_period_start'] - 1;
				$last_year			= $sesi['year_period'];
			}

			// if($sesi['month_period_end'] == 01){
			// 	$last_month_end 	= '12';
			// 	$last_year 			= $sesi['year_period'] - 1;
			// } else {
			// 	$last_month_end 	= $sesi['month_period'] - 1;
			// 	$last_year			= $sesi['year_period'];
			// }

			// print_r($sesi);
			$opening_balance 		= $this->AcctGeneralLedgerReport_model->getOpeningBalance($sesi['account_id'], $last_month_start, $last_year, $sesi['branch_id']);

			if(empty($opening_balance)){
				$opening_balance 	= $this->AcctGeneralLedgerReport_model->getLastBalance($sesi['account_id'], $last_month_start, $last_year, $sesi['branch_id']);

				//print_r("a ".$opening_balance);exit;
			}

			//print_r($opening_balance1);
			//print_r($opening_balance1);exit;

			$account_in_amount 		= $this->AcctGeneralLedgerReport_model->getAccountIn($sesi['account_id'], $last_month_start, $last_year, $sesi['branch_id']);

			$account_out_amount 		= $this->AcctGeneralLedgerReport_model->getAccountOut($sesi['account_id'], $last_month_start, $last_year, $sesi['branch_id']);

			$opening_balance_amount = ($opening_balance + $account_in_amount) - $account_out_amount;

			//print_r($opening_balance);exit;

			$account_id_status 		= $this->AcctGeneralLedgerReport_model->getAccountIDDefaultStatus($sesi['account_id']);

			$accountbalancedetail	= $this->AcctGeneralLedgerReport_model->getAcctAccountBalanceDetail($sesi['account_id'], $sesi['month_period_start'], $sesi['month_period_end'], $sesi['year_period'], $sesi['branch_id']);



			// $accountbalancedetail	= $this->AcctGeneralLedgerReport_model->getAcctAccountBalanceDetail($sesi['account_id'], $sesi['month_period'], $sesi['year_period'], $sesi['branch_id']);

			// if(!empty($accountbalancedetail)){
			// 	$last_balance 		= $opening_balance;
			// 	foreach ($accountbalancedetail as $key => $val) {
			// 		$description = $this->AcctGeneralLedgerReport_model->getJournalVoucherDescription($val['transaction_id'], $val['account_id']);

			// 		$last_balance = ($last_balance + $val['account_in']) - $val['account_out'];

			// 		if($account_id_status == 0 ){
			// 			$debet 	= $val['account_in'];
			// 			$kredit = $val['account_out'];

			// 			if($last_balance >= 0){
			// 				$last_balance_debet 	= $last_balance;
			// 				$last_balance_kredit 	= 0;
			// 			} else {
			// 				$last_balance_debet 	= 0;
			// 				$last_balance_kredit 	= $last_balance;
			// 			}
			// 		} else {
			// 			$debet 	= $val['account_out'];
			// 			$kredit = $val['account_in'];

			// 			if($last_balance >= 0){
			// 				$last_balance_debet 	= 0;
			// 				$last_balance_kredit 	= $last_balance;
			// 			} else {
							
			// 				$last_balance_debet 	= $last_balance;
			// 				$last_balance_kredit 	= 0;
			// 			}
			// 		}

					

			// 		$data_acctaccountbalance[] = array (
			// 			'transaction_date'			=> $val['transaction_date'],
			// 			'transaction_description'	=> $description,
			// 			'account_name'				=> $accountname,
			// 			'account_in'				=> $debet,
			// 			'account_out'				=> $kredit,
			// 			'last_balance_debet'		=> $last_balance_debet,
			// 			'last_balance_credit'		=> $last_balance_kredit,
			// 		);
			// 	}
			// } else {
			// 	$data_acctaccountbalance = array ();
			// }

			$motnhname 		= $this->configuration->Month();
			$accounstatus 	= $this->configuration->AccountStatus();


			
			if(count($datacetak)>=0){
				$this->load->library('excel');
				
				$this->excel->getProperties()->setCreator("ACTIONS")
									 ->setLastModifiedBy("ACTIONS")
									 ->setTitle("Buku Besar")
									 ->setSubject("")
									 ->setDescription("Buku Besar")
									 ->setKeywords("Buku Besar")
									 ->setCategory("Buku Besar");
									 
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		
				$this->excel->getActiveSheet()->mergeCells("B1:G1");

				$this->excel->getActiveSheet()->mergeCells("B8:B9");
				$this->excel->getActiveSheet()->mergeCells("C8:C9");
				$this->excel->getActiveSheet()->mergeCells("D8:D9");
				$this->excel->getActiveSheet()->mergeCells("E8:E9");
				$this->excel->getActiveSheet()->mergeCells("F8:F9");

				
				$this->excel->getActiveSheet()->mergeCells("G8:H8");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);

				$this->excel->getActiveSheet()->getStyle('B8:H8')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B9:H9')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

				$this->excel->getActiveSheet()->getStyle('B8:H8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B9:H9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

				$this->excel->getActiveSheet()->mergeCells("B5:C5");
				$this->excel->getActiveSheet()->getStyle('B5:D5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$this->excel->getActiveSheet()->getStyle('B5:D5')->getFont()->setBold(true);

				$this->excel->getActiveSheet()->mergeCells("B6:C6");
				$this->excel->getActiveSheet()->getStyle('B6:D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$this->excel->getActiveSheet()->getStyle('B6:D6')->getFont()->setBold(true);

				$this->excel->getActiveSheet()->mergeCells("B7:C7");
				$this->excel->getActiveSheet()->getStyle('B7:D7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$this->excel->getActiveSheet()->getStyle('B7:D7')->getFont()->setBold(true);
				
				$this->excel->getActiveSheet()->getStyle('D7')->getNumberFormat()->setFormatCode('0.00');



				
				$this->excel->getActiveSheet()->setCellValue('B1',"Buku Besar Dari Periode ".$motnhname[$sesi['month_period']]." ".$sesi['year_period']);	
				$this->excel->getActiveSheet()->setCellValue('B5',"Nama Perkiraan");
				$this->excel->getActiveSheet()->setCellValue('D5', $accountname);
				$this->excel->getActiveSheet()->setCellValue('B6',"Posisi Saldo");
				$this->excel->getActiveSheet()->setCellValue('D6',$accounstatus[$account_id_status]);
				$this->excel->getActiveSheet()->setCellValue('B7',"Saldo Awal");
				$this->excel->getActiveSheet()->setCellValue('D7',$opening_balance_amount);
				$this->excel->getActiveSheet()->setCellValue('B8',"No");
				$this->excel->getActiveSheet()->setCellValue('C8',"Tanggal");
				$this->excel->getActiveSheet()->setCellValue('D8',"Uraian");
				$this->excel->getActiveSheet()->setCellValue('E8',"Debet");
				$this->excel->getActiveSheet()->setCellValue('F8',"Kredit");
				$this->excel->getActiveSheet()->setCellValue('G8',"Saldo");

				
				$this->excel->getActiveSheet()->setCellValue('G9',"Debet");
				$this->excel->getActiveSheet()->setCellValue('H9',"Kredit");
				
				
				$j=10;
				$no=0;
				
				foreach($datacetak as $key=>$val){

					if(is_numeric($key)){
						
						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('B'.$j.':H'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

						$this->excel->getActiveSheet()->getStyle('E'.$j.':H'.$j)->getNumberFormat()->setFormatCode('0.00');
				
						$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

							$no++;
							$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
							$this->excel->getActiveSheet()->setCellValue('C'.$j, tgltoview($val['transaction_date']));
							$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['transaction_description']);
							$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['account_in']);
							$this->excel->getActiveSheet()->setCellValue('F'.$j, $val['account_out']);
							$this->excel->getActiveSheet()->setCellValue('G'.$j, $val['last_balance_debet']);
							$this->excel->getActiveSheet()->setCellValue('H'.$j, $val['last_balance_credit']);
							
							
						
					}else{
						continue;
					}
					$j++;
			
				}
				
				$filename='Buku_Besar_'.$accountname.'.xls';
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