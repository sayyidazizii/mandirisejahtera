<?php 
	defined('BASEPATH') or exit('No direct script access allowed');
	ob_start(); ?>
<?php
	ini_set('memory_limit', '256M');
	Class AcctSavingsIndex extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctSavingsIndex_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['content']			= 'AcctSavingsIndex/ListAcctSavingsIndex_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processAddAcctSavingsIndex(){
			$auth = $this->session->userdata('auth');

			$data = array (
				'income_amount' 	=> $this->input->post('income_amount', true),
				'last_date'			=> tgltodb($this->input->post('last_date', true)),
			);

			$this->form_validation->set_rules('income_amount', 'Pendapatan Bulan Ini', 'required');

			if($this->form_validation->run()==true){
				
				$date = $data['last_date'];
				$month 	= date('m', strtotime($date));
				$year 	= date('Y', strtotime($date));
				$savings_index_period = $month.$year;

				$preferencecompany 			= $this->AcctSavingsIndex_model->getPreferenceCompany();

				if($preferencecompany['central_branch_id'] == $auth['branch_id']){
						$daily_average_balance_accumulation = $this->AcctSavingsIndex_model->getDailyAverageBalanceAccumulation($auth['branch_id']);

						$savings_last_balance_accumulation 	= $this->AcctSavingsIndex_model->getSavingsLastBalanceAccumulation($month, $year, $auth['branch_id']);

						$deposito_last_balance_accumulation	= $this->AcctSavingsIndex_model->getDepositoLastBalanceAccumulation($date, $auth['branch_id']);

						$total_accumulation = $daily_average_balance_accumulation + $deposito_last_balance_accumulation;

						$acctsavings 		= $this->AcctSavingsIndex_model->getAcctSavings();

						$acctdeposito		= $this->AcctSavingsIndex_model->getAcctDeposito(); 

						// print_r($total_accumulation);exit;

						foreach ($acctsavings as $keyS => $valS) {
							$bmt_percentage 		= 100 - $valS['savings_nisbah'];
							$daily_avreage_balance 	= $this->AcctSavingsIndex_model->getDailyAverageBalance_Savings($valS['savings_id'], $auth['branch_id']);

							$portion_per_savings 	= ($daily_avreage_balance / $total_accumulation) * $data['income_amount'];

							$savings_index_amount 	= (($portion_per_savings * $valS['savings_nisbah']) / 100 ) / $daily_avreage_balance;
							$savings_member_portion = ($portion_per_savings * $valS['savings_nisbah']) / 100;
							$savings_bmt_portion 	= ($portion_per_savings * $bmt_percentage) / 100;

							$dataacctsavingsindex = array (
								'savings_id'								=> $valS['savings_id'],
								'branch_id'									=> $auth['branch_id'],
								'income_amount'								=> $data['income_amount'],
								'daily_average_balance_accumulation' 		=> $total_accumulation,
								'savings_account_last_balance_accumulation' => $savings_last_balance_accumulation,
								'savings_index_amount'						=> $savings_index_amount,
								'savings_index_period'						=> $savings_index_period,
								'savings_nisbah'							=> $valS['savings_nisbah'],
								'savings_portion_total'						=> $portion_per_savings,
								'savings_member_portion'					=> $savings_member_portion,
								'savings_bmt_portion'						=> $savings_bmt_portion,
							);

							$this->AcctSavingsIndex_model->insertAcctSavingsIndex($dataacctsavingsindex);
						}

						foreach ($acctdeposito as $keyD => $valD) {
							$bmt_percentage 			= 100 - $valD['deposito_interest_rate'];
							$daily_avreage_balance 		= $this->AcctSavingsIndex_model->getDepositoLastBalance_Deposito($date, $valD['deposito_id'], $auth['branch_id']);

							$portion_per_deposito 		= ($daily_avreage_balance / $total_accumulation) * $data['income_amount'];

							$deposito_index_amount 		= (($portion_per_deposito * $valD['deposito_interest_rate']) / 100 ) / $daily_avreage_balance;

							$deposito_member_portion 	= ($portion_per_deposito * $valD['deposito_interest_rate']) / 100;
							$deposito_bmt_portion 		= ($portion_per_deposito * $bmt_percentage) / 100;

							$dataacctdepositoindex = array (
								'deposito_id'									=> $valD['deposito_id'],
								'branch_id'										=> $auth['branch_id'],
								'income_amount'									=> $data['income_amount'],
								'daily_average_balance_accumulation' 			=> $total_accumulation,
								'deposito_account_last_balance_accumulation'	=> $deposito_last_balance_accumulation,
								'deposito_index_amount'							=> $deposito_index_amount,
								'deposito_index_period'							=> $savings_index_period,
								'deposito_nisbah'								=> $valD['deposito_interest_rate'],
								'deposito_portion_total'						=> $portion_per_deposito,
								'deposito_member_portion'						=> $deposito_member_portion,
								'deposito_bmt_portion'							=> $deposito_bmt_portion,
							);


							$this->AcctSavingsIndex_model->insertAcctDepositoIndex($dataacctdepositoindex);

						}

						$auth = $this->session->userdata('auth');
						// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Perhitungan Index Simpanan Sukses
								</div> ";

						$this->session->set_userdata('message',$msg);
						redirect('AcctSavingsIndex/processPrinting/'.$savings_index_period);
					
				} else {
					$this->session->set_userdata('addacctsavingscashmutation',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Perhitungan Index hanya bisa dilakukan oleh Cabang Pusat
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('AcctSavingsIndex');
				}
			} else {
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('AcctSavingsIndex');
			}			
		}

		public function processPrinting(){
			$savings_index_period 	= $this->uri->segment(3);
			$auth 					= $this->session->userdata('auth');

			

			$acctsavingsindex	= $this->AcctSavingsIndex_model->getAcctSavingsIndexMAX($auth['branch_id'], $savings_index_period);
			$acctdepositoindex	= $this->AcctSavingsIndex_model->getAcctDepositoIndexMAX($auth['branch_id'], $savings_index_period);
			$total 				= $this->AcctSavingsIndex_model->getTotal($auth['branch_id'], $savings_index_period);
			

			foreach ($acctsavingsindex as $key => $val) {
				$datasavingsindex = $this->AcctSavingsIndex_model->getAcctSavingsIndex($val['savings_index_id']);
				$datasavings[] = array (
					'savings_name'					=> $datasavingsindex['savings_name'],
					'savings_nisbah'				=> $datasavingsindex['savings_nisbah'],
					'savings_index'					=> $datasavingsindex['savings_index_amount'],
					'savings_member_portion'		=> $datasavingsindex['savings_member_portion'],
					'savings_bmt_portion'			=> $datasavingsindex['savings_bmt_portion'],
				);
			}

			foreach ($acctdepositoindex as $key => $val) {
				$datadepositoindex = $this->AcctSavingsIndex_model->getAcctDepositoIndex($val['deposito_index_id']);
				$datadeposito[] = array (
					'deposito_name'					=> $datadepositoindex['deposito_name'],
					'deposito_nisbah'				=> $datadepositoindex['deposito_nisbah'],
					'deposito_index'				=> $datadepositoindex['deposito_index_amount'],
					'deposito_member_portion'		=> $datadepositoindex['deposito_member_portion'],
					'deposito_bmt_portion'			=> $datadepositoindex['deposito_bmt_portion'],
				);
			}





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

			$pdf->SetFont('helvetica', '', 8);

			// -----------------------------------------------------------------------------

			$tbl = "
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
					<tr>
						<td width=\"80%\"><div style=\"text-align: left;font-size: 16px\">KSPPS MADANI JAWA TIMUR </div></td>
				    </tr>
				    <tr>
						<td width=\"80%\"><div style=\"text-align: left;font-size: 14px\">HASIL PERHITUNGAN SRH DAN INDEX </div></td>
				    </tr>
				</table>
			";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl = "
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
					<tr>
						<td width=\"30%\"><div style=\"text-align: left;font-size: 12px\">TOTAL SALDO SIMP & BERJANGKA </div></td>
						<td width=\"60%\"><div style=\"text-align: left;font-size: 12px\">:  ".number_format($total['total_savings_deposito'], 2)."</div></td>
				    </tr>
				    <tr>
						<td width=\"30%\"><div style=\"text-align: left;font-size: 12px\">TOTAL SALDO RATA2 HARIAN </div></td>
						<td width=\"60%\"><div style=\"text-align: left;font-size: 12px\">:  ".number_format($total['total_srh'], 2)."</div></td>
				    </tr>
				    <tr>
						<td width=\"30%\"><div style=\"text-align: left;font-size: 12px\">JML PENDAPATAN BULAN INI</div></td>
						<td width=\"60%\"><div style=\"text-align: left;font-size: 12px\">:  ".number_format($total['total_income'], 2)."</div></td>
				    </tr>
				</table>
			";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl = "
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
					<tr>
						<td width=\"25%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;\">Nama Simp.</div></td>
						<td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;\">Nisbah</div></td>
				        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;\">Index</div></td>
				        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;\">Porsi Nasabah (+/-)</div></td>
				        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;\">Porsi BMT (+/-)</div></td>
				    </tr>
				</table>
			";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";

			$tbl1a = "
				<tr>
					<td colspan=\"5\">( Beban Bulan ini )</td>
				</tr>
			";

			foreach ($datasavings as $key => $val) {
				
				$tbl1a .= "
					<tr>
						<td width=\"25%\"><div style=\"text-align: left;\">&nbsp; ".$val['savings_name']."</div></td>
						<td width=\"10%\"><div style=\"text-align: right;\">".$val['savings_nisbah']." &nbsp;</div></td>
				        <td width=\"10%\"><div style=\"text-align: right;\">".$val['savings_index']." &nbsp;</div></td>
				        <td width=\"20%\"><div style=\"text-align: right;\">".number_format($val['savings_member_portion'], 2)." &nbsp;</div></td>
				        <td width=\"20%\"><div style=\"text-align: right;\">".number_format($val['savings_bmt_portion'], 2)." &nbsp;</div></td>
				    </tr>
				";

				$totalmembersavings += $val['savings_member_portion'];
				$totalbmtsavings 	+= $val['savings_bmt_portion'];
			}

			$tbl1b = "
				<tr>
					<td colspan=\"5\"></td>
				</tr>
				<tr>
					<td colspan=\"5\">( Beban Bulan depan )</td>
				</tr>
			";

			foreach ($datadeposito as $key => $val) {
				
				$tbl1b .= "
					<tr>
						<td width=\"25%\"><div style=\"text-align: left;\">&nbsp; ".$val['deposito_name']."</div></td>
						<td width=\"10%\"><div style=\"text-align: right;\">".$val['deposito_nisbah']." &nbsp;</div></td>
				        <td width=\"10%\"><div style=\"text-align: right;\">".$val['deposito_index']." &nbsp;</div></td>
				        <td width=\"20%\"><div style=\"text-align: right;\">".number_format($val['deposito_member_portion'], 2)." &nbsp;</div></td>
				        <td width=\"20%\"><div style=\"text-align: right;\">".number_format($val['deposito_bmt_portion'], 2)." &nbsp;</div></td>
				    </tr>
				";
				$totalmemberdeposito += $val['deposito_member_portion'];
				$totalbmtdeposito 	+= $val['deposito_bmt_portion'];
			}

			$totalmember = $totalmembersavings + $totalmemberdeposito;
			$totalbmt = $totalbmtdeposito + $totalbmtsavings;

			$tbl2 = "
					<tr>
						<td colspan =\"2\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  </div></td>
						<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Total </div></td>
						<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalmember, 2)."</div></td>
						<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalbmt, 2)."</div></td>
					</tr>
							
			</table>";

			$pdf->writeHTML($tbl.$tbl1a.$tbl1b.$tbl2, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Hasil perhitungan SRH dan Index.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}
	}
?>