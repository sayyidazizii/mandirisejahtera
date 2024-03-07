<?php 
	defined('BASEPATH') or exit('No direct script access allowed');
	ob_start();?>
<?php
	Class AcctProfitLossComparationReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctProfitLossComparationReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= 	$this->session->userdata('filter-AcctProfitLossComparationReport');

			$day 	= date("d");
			$month 	= date("m");
			$year 	= date("Y");

			if(!is_array($sesi)){
				$sesi['month_period']							= $month;
				$sesi['year_period']							= $year;
				$sesi['account_comparation_report_type']		= 1;
				$sesi['profit_loss_report_format']				= 3;
				$sesi['branch_id']								= $auth['branch_id'];
			}

			$data['main_view']['corebranch']								= create_double($this->AcctProfitLossComparationReport_model->getCoreBranch(),'branch_id','branch_name');

			$data['main_view']['acctprofitlosscomparationreport_top']		= $this->AcctProfitLossComparationReport_model->getAcctProfitLossComparationReport_Top($sesi['profit_loss_report_format']);

			$data['main_view']['acctprofitlosscomparationreport_bottom']	= $this->AcctProfitLossComparationReport_model->getAcctProfitLossComparationReport_Bottom();

			$data['main_view']['monthlist']									= $this->configuration->Month();

			$data['main_view']['accountcomparationreporttype']				= $this->configuration->AccountComparationReportType();

			$data['main_view']['profitlossreportformat']					= $this->configuration->ProfitLossReportFormat();

			$data['main_view']['content']									= 'AcctProfitLossComparationReport/AcctProfitLossComparationReport_view';

			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"month_period" 						=> $this->input->post('month_period',true),
				"year_period" 						=> $this->input->post('year_period',true),
				"account_comparation_report_type" 	=> $this->input->post('account_comparation_report_type',true),
				"profit_loss_report_format" 		=> $this->input->post('profit_loss_report_format',true),
				"branch_id" 						=> $this->input->post('branch_id',true),
			);

			$this->session->set_userdata('filter-AcctProfitLossComparationReport',$data);
			redirect('profit-loss-comparation');
		}

		public function processPrinting(){
			$auth = $this->session->userdata('auth');
			$data = $this->session->userdata('filter-AcctProfitLossComparationReport');
			if(!is_array($data)){
				$data['month_period']						= date('m');
				$data['year_period']						= date('Y');
				$data['account_comparation_report_type'] 	= 1;
				$data['profit_loss_report_format']			= 3;
				$data['branch_id']							= $auth['branch_id'];
			}
			
			$preferencecompany 							= $this->AcctProfitLossComparationReport_model->getPreferenceCompany();

			$acctprofitlosscomparationreport_top		= $this->AcctProfitLossComparationReport_model->getAcctProfitLossComparationReport_Top($data['profit_loss_report_format']);

			$acctprofitlosscomparationreport_bottom		= $this->AcctProfitLossComparationReport_model->getAcctProfitLossComparationReport_Bottom();

			// print_r($data); exit;

			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
			// Check the example n. 29 for viewer preferences

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

			$pdf->SetMargins(6, 6, 6, 6); // put space of 10 on top
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

			$month_now 	= $data['month_period'];
			$year_now 	= $data['year_period'];

			switch ($month_now) {
				case '01':
					$month_now_name = "JANUARI";
					break;
				case '02':
					$month_now_name = "FEBRUARI";
					break;
				case '03':
					$month_now_name = "MARET";
					break;
				case '04':
					$month_now_name = "APRIL";
					break;
				case '05':
					$month_now_name = "MEI";
					break;
				case '06':
					$month_now_name = "JUNI";
					break;
				case '07':
					$month_now_name = "JULI";
					break;
				case '08':
					$month_now_name = "AGUSTUS";
					break;
				case '09':
					$month_now_name = "SEPTEMBER";
					break;
				case '10':
					$month_now_name = "OKTOBER";
					break;
				case '11':
					$month_now_name = "NOVEMBER";
					break;
				case '12':
					$month_now_name = "DESEMBER";
					break;
				
				default:
					# code...
					break;
			}

			$minus_month	= mktime(0, 0, 0, date($data['month_period']) - 1);
			$month_before	= date('m', $minus_month);

			if($month_before == 12){
				$year_before = $year_now - 1;
			} else {
				$year_before = $year_now;
			}


			switch ($month_before) {
				case '01':
					$month_before_name = "JANUARI";
					break;
				case '02':
					$month_before_name = "FEBRUARI";
					break;
				case '03':
					$month_before_name = "MARET";
					break;
				case '04':
					$month_before_name = "APRIL";
					break;
				case '05':
					$month_before_name = "MEI";
					break;
				case '06':
					$month_before_name = "JUNI";
					break;
				case '07':
					$month_before_name = "JULI";
					break;
				case '08':
					$month_before_name = "AGUSTUS";
					break;
				case '09':
					$month_before_name = "SEPTEMBER";
					break;
				case '10':
					$month_before_name = "OKTOBER";
					break;
				case '11':
					$month_before_name = "NOVEMBER";
					break;
				case '12':
					$month_before_name = "DESEMBER";
					break;
				
				default:
					# code...
					break;
			}

			if ($data['account_comparation_report_type'] == 1){
				$period_before 	= $month_before_name." ".$year_before;
				$period_now 	= $month_now_name." ".$year_now;
			} else {
				$year_before 	= $year_now - 1;
				$period_before	= $year_before;
				$period_now		= $year_now;
			}

			/*print_r($preference_company);*/
			$tbl = "
				<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\">
				    <tr>
				        <td colspan=\"5\"><div style=\"text-align: center; font-size:14px\">LAPORAN KOMPARASI PERHITUNGAN SHU <BR>".$preferencecompany['company_name']."</div></td>
				    </tr>
				</table>
			";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tblHeader = "
			<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">";
		        $tblheader_top_before = "
		        	<tr>
		        		<td width=\"50%\" style=\"border-top:1px black solid;border-left:1px black solid;border-right:1px black solid\">	
			        		
		        			<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">
		        				<tr>
									<td colspan=\"2\" style=\"text-align:center;\">
										<div style=\"font-weight:bold\">PERIODE 
											".$period_before."
										</div>
									</td>
								</tr>";	

			        			$tblitem_top_before = "";
			        			foreach ($acctprofitlosscomparationreport_top as $keyTop => $valTop) {
									if($valTop['report_tab'] == 0){
										$report_tab = ' ';
									} else if($valTop['report_tab'] == 1){
										$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									} else if($valTop['report_tab'] == 2){
										$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									} else if($valTop['report_tab'] == 3){
										$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									}

									if($valTop['report_bold'] == 1){
										$report_bold = 'bold';
									} else {
										$report_bold = 'normal';
									}									

									if($valTop['report_type'] == 1){
										$tblitem_top1_before = "
											<tr>
												<td colspan=\"2\" style=\"width: 100%\"><div style=\"font-weight:".$report_bold."\">".$report_tab."".$valTop['account_name']."</div></td>
											</tr>";
									} else {
										$tblitem_top1_before = "";
									}


									if($valTop['report_type']	== 2){

										$tblitem_top2_before = "
											<tr>
												<td style=\"width: 73%\"><div style=\"font-weight:".$report_bold."\">".$report_tab."".$valTop['account_name']."</div></td>
												<td style=\"width: 25%\"><div style=\"font-weight:".$report_bold."\"></div></td>
											</tr>";
									} else {
										$tblitem_top2_before = "";
									}									

									if($valTop['report_type']	== 3){
										$accountamount 		= $this->AcctProfitLossComparationReport_model->getAccountAmount($valTop['account_id'], $month_before, $year_before, $data['account_comparation_report_type'], $data['branch_id']);

										$account_subtotal 	= ABS(($accountamount['account_in_amount'] - $accountamount['account_out_amount']));

										$tblitem_top3_before = "
											<tr>
												<td style=\"width: 73%\"><div style=\"font-weight:".$report_bold."\">".$report_tab."(".$valTop['account_code'].") ".$valTop['account_name']."</div> </td>
												<td style=\"text-align:right;width: 25%\">".number_format($account_subtotal, 2)."</td>
											</tr>";

										$account_amount_top_before[$valTop['report_no']] = $account_subtotal;

									} else {
										$tblitem_top3_before = "";
									}
									

									if($valTop['report_type'] == 5){
										if(!empty($valTop['report_formula']) && !empty($valTop['report_operator'])){
											$report_formula 	= explode('#', $valTop['report_formula']);
											$report_operator 	= explode('#', $valTop['report_operator']);

											$total_account_amount	= 0;
											for($i = 0; $i < count($report_formula); $i++){
												if($report_operator[$i] == '-'){
													if($total_account_amount == 0 ){
														$total_account_amount = $total_account_amount + $account_amount_top_before[$report_formula[$i]];
													} else {
														$total_account_amount = $total_account_amount - $account_amount_top_before[$report_formula[$i]];
													}
												} else if($report_operator[$i] == '+'){
													if($total_account_amount == 0){
														$total_account_amount = $total_account_amount + $account_amount_top_before[$report_formula[$i]];
													} else {
														$total_account_amount = $total_account_amount + $account_amount_top_before[$report_formula[$i]];
													}
												}
											}
											$tblitem_top5_before = "
												<tr>
													<td><div style=\"font-weight:".$report_bold."\">".$report_tab."".$valTop['account_name']."</div></td>
													<td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold."\">".number_format($total_account_amount, 2)."</div></td>
												</tr>";
										} else {
											$tblitem_top5_before = "";
										}
									} else {
										$tblitem_top5_before = "";
									}

									$tblitem_top_before .= $tblitem_top1_before.$tblitem_top2_before.$tblitem_top3_before.$tblitem_top5_before;

									if($valTop['report_type'] == 6){
										if(!empty($valTop['report_formula']) && !empty($valTop['report_operator'])){
											$report_formula 	= explode('#', $valTop['report_formula']);
											$report_operator 	= explode('#', $valTop['report_operator']);

											$grand_total_account_amount1_top_before	= 0;
											for($i = 0; $i < count($report_formula); $i++){
												if($report_operator[$i] == '-'){
													if($grand_total_account_amount1_top_before == 0 ){
														$grand_total_account_amount1_top_before = $grand_total_account_amount1_top_before + $account_amount_top_before[$report_formula[$i]];
													} else {
														$grand_total_account_amount1_top_before = $grand_total_account_amount1_top_before - $account_amount_top_before[$report_formula[$i]];
													}
												} else if($report_operator[$i] == '+'){
													if($grand_total_account_amount1_top_before == 0){
														$grand_total_account_amount1_top_before = $grand_total_account_amount1_top_before + $account_amount_top_before[$report_formula[$i]];
													} else {
														$grand_total_account_amount1_top_before = $grand_total_account_amount1_top_before + $account_amount_top_before[$report_formula[$i]];
													}
												}
											}
											
										} else {
											
										}
									} else {
										
									}

								}

		        $tblfooter_top_before	= "
		        		</table>
		        	</td>";




		        $tblheader_top_now = "
		        	
		        		<td width=\"50%\" style=\"border-top:1px black solid;border-left:1px black solid;border-right:1px black solid\">	
			        		
		        			<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">
		        				<tr>
									<td colspan=\"2\" style=\"text-align:center;\">
										<div style=\"font-weight:bold\">PERIODE 
											".$period_now."
										</div>
									</td>
								</tr>";		

			        			$tblitem_top_now = "";
			        			foreach ($acctprofitlosscomparationreport_top as $keyTop => $valTop) {
									if($valTop['report_tab'] == 0){
										$report_tab = ' ';
									} else if($valTop['report_tab'] == 1){
										$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									} else if($valTop['report_tab'] == 2){
										$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									} else if($valTop['report_tab'] == 3){
										$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									}

									if($valTop['report_bold'] == 1){
										$report_bold = 'bold';
									} else {
										$report_bold = 'normal';
									}									

									if($valTop['report_type'] == 1){
										$tblitem_top1_now = "
											<tr>
												<td colspan=\"2\" style=\"width: 100%\"><div style=\"font-weight:".$report_bold."\">".$report_tab."".$valTop['account_name']."</div></td>
											</tr>";
									} else {
										$tblitem_top1_now = "";
									}


									if($valTop['report_type']	== 2){

										$tblitem_top2_now = "
											<tr>
												<td style=\"width: 73%\"><div style=\"font-weight:".$report_bold."\">".$report_tab."".$valTop['account_name']."</div></td>
												<td style=\"width: 25%\"><div style=\"font-weight:".$report_bold."\"></div></td>
											</tr>";
									} else {
										$tblitem_top2_now = "";
									}									

									if($valTop['report_type']	== 3){
										$accountamount 		= $this->AcctProfitLossComparationReport_model->getAccountAmount($valTop['account_id'], $month_now, $year_now, $data['account_comparation_report_type'], $data['branch_id']);

										$account_subtotal 	= ABS(($accountamount['account_in_amount'] - $accountamount['account_out_amount']));

										$tblitem_top3_now = "
											<tr>
												<td style=\"width: 73%\"><div style=\"font-weight:".$report_bold."\">".$report_tab."(".$valTop['account_code'].") ".$valTop['account_name']."</div> </td>
												<td style=\"text-align:right;width: 25%\">".number_format($account_subtotal, 2)."</td>
											</tr>";

										$account_amount_top_now[$valTop['report_no']] = $account_subtotal;

									} else {
										$tblitem_top3_now = "";
									}
									

									if($valTop['report_type'] == 5){
										if(!empty($valTop['report_formula']) && !empty($valTop['report_operator'])){
											$report_formula 	= explode('#', $valTop['report_formula']);
											$report_operator 	= explode('#', $valTop['report_operator']);

											$total_account_amount	= 0;
											for($i = 0; $i < count($report_formula); $i++){
												if($report_operator[$i] == '-'){
													if($total_account_amount == 0 ){
														$total_account_amount = $total_account_amount + $account_amount_top_now[$report_formula[$i]];
													} else {
														$total_account_amount = $total_account_amount - $account_amount_top_now[$report_formula[$i]];
													}
												} else if($report_operator[$i] == '+'){
													if($total_account_amount == 0){
														$total_account_amount = $total_account_amount + $account_amount_top_now[$report_formula[$i]];
													} else {
														$total_account_amount = $total_account_amount + $account_amount_top_now[$report_formula[$i]];
													}
												}
											}
											$tblitem_top5_now = "
												<tr>
													<td><div style=\"font-weight:".$report_bold."\">".$report_tab."".$valTop['account_name']."</div></td>
													<td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold."\">".number_format($total_account_amount, 2)."</div></td>
												</tr>";
										} else {
											$tblitem_top5_now = "";
										}
									} else {
										$tblitem_top5_now = "";
									}

									$tblitem_top_now .= $tblitem_top1_now.$tblitem_top2_now.$tblitem_top3_now.$tblitem_top5_now;

									if($valTop['report_type'] == 6){
										if(!empty($valTop['report_formula']) && !empty($valTop['report_operator'])){
											$report_formula 	= explode('#', $valTop['report_formula']);
											$report_operator 	= explode('#', $valTop['report_operator']);

											$grand_total_account_amount1_top_now	= 0;
											for($i = 0; $i < count($report_formula); $i++){
												if($report_operator[$i] == '-'){
													if($grand_total_account_amount1_top_now == 0 ){
														$grand_total_account_amount1_top_now = $grand_total_account_amount1_top_now + $account_amount_top_now[$report_formula[$i]];
													} else {
														$grand_total_account_amount1_top_now = $grand_total_account_amount1_top_now - $account_amount_top_now[$report_formula[$i]];
													}
												} else if($report_operator[$i] == '+'){
													if($grand_total_account_amount1_top_now == 0){
														$grand_total_account_amount1_top_now = $grand_total_account_amount1_top_now + $account_amount_top_now[$report_formula[$i]];
													} else {
														$grand_total_account_amount1_top_now = $grand_total_account_amount1_top_now + $account_amount_top_now[$report_formula[$i]];
													}
												}
											}
											
										} else {
											
										}
									} else {
										
									}

								}

		        $tblfooter_top_now	= "
		        		</table>
		        	</td>
		        	
		        </tr>";
			       /* print_r("tblitem_top ");
			        print_r($tblitem_top);
			        exit; */

				$tblheader_bottom_before = "
					<tr>
			        	<td width=\"50%\" style=\"border-bottom:1px black solid;border-left:1px black solid;border-right:1px black solid\">	
			        		<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">";		
			        			$tblitem_bottom_before = "";
			        			foreach ($acctprofitlosscomparationreport_bottom as $keyBottom => $valBottom) {
									if($valBottom['report_tab'] == 0){
										$report_tab = ' ';
									} else if($valBottom['report_tab'] == 1){
										$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									} else if($valBottom['report_tab'] == 2){
										$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									} else if($valBottom['report_tab'] == 3){
										$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									}

									if($valBottom['report_bold'] == 1){
										$report_bold = 'bold';
									} else {
										$report_bold = 'normal';
									}									

									if($valBottom['report_type'] == 1){
										$tblitem_bottom1_before = "
											<tr>
												<td colspan=\"2\"><div style=\"font-weight:".$report_bold."\">".$report_tab."".$valBottom['account_name']."</div></td>
											</tr>";
									} else {
										$tblitem_bottom1_before = "";
									}



									if($valBottom['report_type'] == 2){
										$tblitem_bottom2_before = "
											<tr>
												<td style=\"width: 73%\"><div style=\"font-weight:".$report_bold."\">".$report_tab."".$valBottom['account_name']."</div></td>
												<td style=\"width: 25%\"><div style=\"font-weight:".$report_bold."\"></div></td>
											</tr>";
									} else {
										$tblitem_bottom2_before = "";
									}									

									if($valBottom['report_type']	== 3){
										$accountamount 		= $this->AcctProfitLossComparationReport_model->getAccountAmount($valBottom['account_id'], $month_before, $year_before, $data['account_comparation_report_type'], $data['branch_id']);

										$account_subtotal 	= ABS(($accountamount['account_in_amount'] - $accountamount['account_out_amount']));

										// print_r("account_subtotal ");
										// print_r($account_subtotal);
										// exit;

										$tblitem_bottom3_before = "
											<tr>
												<td style=\"width: 73%\"><div style=\"font-weight:".$report_bold."\">".$report_tab."(".$valBottom['account_code'].") ".$valBottom['account_name']."</div> </td>
												<td style=\"text-align:right;width: 25%\">".number_format($account_subtotal, 2)."</td>
											</tr>";

										$account_amount_bottom_before[$valBottom['report_no']] = $account_subtotal;

									} else {
										$tblitem_bottom3_before = "";
									}
									

									if($valBottom['report_type'] == 5){
										if(!empty($valBottom['report_formula']) && !empty($valBottom['report_operator'])){
											$report_formula 	= explode('#', $valBottom['report_formula']);
											$report_operator 	= explode('#', $valBottom['report_operator']);

											$total_account_amount2	= 0;
											for($i = 0; $i < count($report_formula); $i++){
												if($report_operator[$i] == '-'){
													if($total_account_amount2 == 0 ){
														$total_account_amount2 = $total_account_amount2 + $account_amount_bottom_before[$report_formula[$i]];
													} else {
														$total_account_amount2 = $total_account_amount2 - $account_amount_bottom_before[$report_formula[$i]];
													}
												} else if($report_operator[$i] == '+'){
													if($total_account_amount2 == 0){
														$total_account_amount2 = $total_account_amount2 + $account_amount_bottom_before[$report_formula[$i]];
													} else {
														$total_account_amount2 = $total_account_amount2 + $account_amount_bottom_before[$report_formula[$i]];
													}
												}
											}
											$tblitem_bottom5_before = "
												<tr>
													<td><div style=\"font-weight:".$report_bold."\">".$report_tab."".$valBottom['account_name']."</div></td>
													<td style=\"text-align:righr;\"><div style=\"font-weight:".$report_bold."\">".number_format($total_account_amount2, 2)."</div></td>
												</tr>";
										} else {
											$tblitem_bottom5_before = "";
										}
									} else {
										$tblitem_bottom5_before = "";
									}

									$tblitem_bottom_before .= $tblitem_bottom1_before.$tblitem_bottom2_before.$tblitem_bottom3_before.$tblitem_bottom5_before;


									if($valBottom['report_type'] == 6){
										if(!empty($valBottom['report_formula']) && !empty($valBottom['report_operator'])){
											$report_formula 	= explode('#', $valBottom['report_formula']);
											$report_operator 	= explode('#', $valBottom['report_operator']);

											$grand_total_account_amount2_before_bottom	= 0;
											for($i = 0; $i < count($report_formula); $i++){
												if($report_operator[$i] == '-'){
													if($grand_total_account_amount2_before_bottom == 0 ){
														$grand_total_account_amount2_before_bottom = $grand_total_account_amount2_before_bottom + $account_amount_bottom_before[$report_formula[$i]];
													} else {
														$grand_total_account_amount2_before_bottom = $grand_total_account_amount2_before_bottom - $account_amount_bottom_before[$report_formula[$i]];
													}
												} else if($report_operator[$i] == '+'){
													if($grand_total_account_amount2_before_bottom == 0){
														$grand_total_account_amount2_before_bottom = $grand_total_account_amount2_before_bottom + $account_amount_bottom_before[$report_formula[$i]];
													} else {
														$grand_total_account_amount2_before_bottom = $grand_total_account_amount2_before_bottom + $account_amount_bottom_before[$report_formula[$i]];
													}
												}
											}
										} else {
											
										}
									} else {
										
									}

								}
								// exit;

		       	$tblfooter_bottom_before = "
		       			</table>
		        	</td>
		        ";






		        $tblheader_bottom_now = "
					
			        	<td width=\"50%\" style=\"border-bottom:1px black solid;border-left:1px black solid;border-right:1px black solid\">	
			        		<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">";		
			        			$tblitem_bottom_now = "";
			        			foreach ($acctprofitlosscomparationreport_bottom as $keyBottom => $valBottom) {
									if($valBottom['report_tab'] == 0){
										$report_tab = ' ';
									} else if($valBottom['report_tab'] == 1){
										$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									} else if($valBottom['report_tab'] == 2){
										$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									} else if($valBottom['report_tab'] == 3){
										$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									}

									if($valBottom['report_bold'] == 1){
										$report_bold = 'bold';
									} else {
										$report_bold = 'normal';
									}									

									if($valBottom['report_type'] == 1){
										$tblitem_bottom1_now = "
											<tr>
												<td colspan=\"2\"><div style=\"font-weight:".$report_bold."\">".$report_tab."".$valBottom['account_name']."</div></td>
											</tr>";
									} else {
										$tblitem_bottom1_now = "";
									}



									if($valBottom['report_type'] == 2){
										$tblitem_bottom2_now = "
											<tr>
												<td style=\"width: 73%\"><div style=\"font-weight:".$report_bold."\">".$report_tab."".$valBottom['account_name']."</div></td>
												<td style=\"width: 25%\"><div style=\"font-weight:".$report_bold."\"></div></td>
											</tr>";
									} else {
										$tblitem_bottom2_now = "";
									}									

									if($valBottom['report_type']	== 3){
										$accountamount 		= $this->AcctProfitLossComparationReport_model->getAccountAmount($valBottom['account_id'], $month_now, $year_now, $data['account_comparation_report_type'], $data['branch_id']);

										$account_subtotal 	= ABS(($accountamount['account_in_amount'] - $accountamount['account_out_amount']));

										// print_r("account_subtotal ");
										// print_r($account_subtotal);
										// exit;

										$tblitem_bottom3_now = "
											<tr>
												<td style=\"width: 73%\"><div style=\"font-weight:".$report_bold."\">".$report_tab."(".$valBottom['account_code'].") ".$valBottom['account_name']."</div> </td>
												<td style=\"text-align:right;width: 25%\">".number_format($account_subtotal, 2)."</td>
											</tr>";

										$account_amount_bottom_now[$valBottom['report_no']] = $account_subtotal;

									} else {
										$tblitem_bottom3_now = "";
									}
									

									if($valBottom['report_type'] == 5){
										if(!empty($valBottom['report_formula']) && !empty($valBottom['report_operator'])){
											$report_formula 	= explode('#', $valBottom['report_formula']);
											$report_operator 	= explode('#', $valBottom['report_operator']);

											$total_account_amount2	= 0;
											for($i = 0; $i < count($report_formula); $i++){
												if($report_operator[$i] == '-'){
													if($total_account_amount2 == 0 ){
														$total_account_amount2 = $total_account_amount2 + $account_amount_bottom_now[$report_formula[$i]];
													} else {
														$total_account_amount2 = $total_account_amount2 - $account_amount_bottom_now[$report_formula[$i]];
													}
												} else if($report_operator[$i] == '+'){
													if($total_account_amount2 == 0){
														$total_account_amount2 = $total_account_amount2 + $account_amount_bottom_now[$report_formula[$i]];
													} else {
														$total_account_amount2 = $total_account_amount2 + $account_amount_bottom_now[$report_formula[$i]];
													}
												}
											}
											$tblitem_bottom5_now = "
												<tr>
													<td><div style=\"font-weight:".$report_bold."\">".$report_tab."".$valBottom['account_name']."</div></td>
													<td style=\"text-align:righr;\"><div style=\"font-weight:".$report_bold."\">".number_format($total_account_amount2, 2)."</div></td>
												</tr>";
										} else {
											$tblitem_bottom5_now = "";
										}
									} else {
										$tblitem_bottom5_now = "";
									}

									$tblitem_bottom_now .= $tblitem_bottom1_now.$tblitem_bottom2_now.$tblitem_bottom3_now.$tblitem_bottom5_now;


									if($valBottom['report_type'] == 6){
										if(!empty($valBottom['report_formula']) && !empty($valBottom['report_operator'])){
											$report_formula 	= explode('#', $valBottom['report_formula']);
											$report_operator 	= explode('#', $valBottom['report_operator']);

											$grand_total_account_amount2_bottom_now	= 0;
											for($i = 0; $i < count($report_formula); $i++){
												if($report_operator[$i] == '-'){
													if($grand_total_account_amount2_bottom_now == 0 ){
														$grand_total_account_amount2_bottom_now = $grand_total_account_amount2_bottom_now + $account_amount[$report_formula[$i]];
													} else {
														$grand_total_account_amount2_bottom_now = $grand_total_account_amount2_bottom_now - $account_amount[$report_formula[$i]];
													}
												} else if($report_operator[$i] == '+'){
													if($grand_total_account_amount2_bottom_now == 0){
														$grand_total_account_amount2_bottom_now = $grand_total_account_amount2_bottom_now + $account_amount[$report_formula[$i]];
													} else {
														$grand_total_account_amount2_bottom_now = $grand_total_account_amount2_bottom_now + $account_amount[$report_formula[$i]];
													}
												}
											}
										} else {
											
										}
									} else {
										
									}

								}
								// exit;

		       	$tblfooter_bottom_now = "
		       			</table>
		        	</td>
		        </tr>";

		        	$shu_before 				= $grand_total_account_amount1_top_before - $grand_total_account_amount2_before_bottom;
			        $shu_now 					= $grand_total_account_amount1_top_now - $grand_total_account_amount2_bottom_now;
					$preferencecompany 			= $this->AcctProfitLossComparationReport_model->getPreferenceCompany();
					$accountamounttax_now 		= $this->AcctProfitLossComparationReport_model->getAccountAmount($preferencecompany['account_income_tax_id'], $month_now, $year_now, $data['account_comparation_report_type'], $data['branch_id']);

					$income_tax_now 			= ABS(($accountamounttax_now['account_in_amount'] - $accountamounttax_now['account_out_amount']));
					
					$accountamounttax_before	= $this->AcctProfitLossComparationReport_model->getAccountAmount($preferencecompany['account_income_tax_id'], $month_before, $year_before, $data['account_comparation_report_type'], $data['branch_id']);
					$income_tax_before 			= ABS(($accountamounttax_before['account_in_amount'] - $accountamounttax_before['account_out_amount']));


			$tblFooter = "
			   
			    <tr>
			    	<td width=\"50%\" style=\"border:1px black solid;\">
			    		<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">
							<tr>
								<td style=\"width: 60%\"><div style=\"font-weight:bold;font-size:14px\">SHU SEBELUM PAJAK</div></td>
								<td style=\"width: 38%; text-align:right;\"><div style=\"font-weight:bold; font-size:14px\">".number_format($shu_before, 2)."</div></td>
							</tr>
							<tr>
								<td style=\"width: 60%\"><div style=\"font-weight:bold;font-size:14px\">PAJAK PENGHASILAN</div></td>
								<td style=\"width: 38%; text-align:right;\"><div style=\"font-weight:bold; font-size:14px\">".number_format($income_tax_before, 2)."</div></td>
							</tr>
							<tr>
								<td style=\"width: 60%\"><div style=\"font-weight:bold;font-size:14px\">SHU SETELAH PAJAK</div></td>
								<td style=\"width: 38%; text-align:right;\"><div style=\"font-weight:bold; font-size:14px\">".number_format($shu_before - $income_tax_before, 2)."</div></td>
							</tr>
			    		</table>
			    	</td>
			    	
			   
			    	<td width=\"50%\" style=\"border:1px black solid;\">
			    		<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">
							<tr>
								<td style=\"width: 60%\"><div style=\"font-weight:bold;font-size:14px\">SHU SEBELUM PAJAK</div></td>
								<td style=\"width: 38%; text-align:right;\"><div style=\"font-weight:bold; font-size:14px\">".number_format($shu_now, 2)."</div></td>
							</tr>
							<tr>
								<td style=\"width: 60%\"><div style=\"font-weight:bold;font-size:14px\">PAJAK PENGHASILAN</div></td>
								<td style=\"width: 38%; text-align:right;\"><div style=\"font-weight:bold; font-size:14px\">".number_format($income_tax_now, 2)."</div></td>
							</tr>
							<tr>
								<td style=\"width: 60%\"><div style=\"font-weight:bold;font-size:14px\">SHU SETELAH PAJAK</div></td>
								<td style=\"width: 38%; text-align:right;\"><div style=\"font-weight:bold; font-size:14px\">".number_format($shu_now - $income_tax_now, 2)."</div></td>
							</tr>
			    		</table>
			    	</td>
			    	
			    </tr>
			</table>";
			    
			$table = $tblHeader.$tblheader_top_before.$tblitem_top_before.$tblfooter_top_before.$tblheader_top_now.$tblitem_top_now.$tblfooter_top_now.$tblheader_bottom_before.$tblitem_bottom_before.$tblfooter_bottom_before.$tblheader_bottom_now.$tblitem_bottom_now.$tblfooter_bottom_now.$tblFooter;
				/*print_r("table ");
				print_r($table);
				exit;*/

			$pdf->writeHTML($table, true, false, false, false, '');

			
			
			
			//Close and output PDF document
			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Laporan Komparasi Perhitungan SHU '.$period_before.' - '.$period_now.'.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}


		public function exportAcctProfitLossComparationReport(){
			$auth = $this->session->userdata('auth');
			$data = $this->session->userdata('filter-AcctProfitLossComparationReport');
			if(!is_array($data)){
				$data['month_period']						= date('m');
				$data['year_period']						= date('Y');
				$data['account_comparation_report_type'] 	= 1;
				$data['profit_loss_report_format'] 			= 3;
				$data['branch_id']							= $auth['branch_id'];
			}

			$preferencecompany 							= $this->AcctProfitLossComparationReport_model->getPreferenceCompany();

			$acctprofitlosscomparationreport_top		= $this->AcctProfitLossComparationReport_model->getAcctProfitLossComparationReport_Top($data['profit_loss_report_format']);

			$acctprofitlosscomparationreport_bottom		= $this->AcctProfitLossComparationReport_model->getAcctProfitLossComparationReport_Bottom();

			$month_now 	= $data['month_period'];
			$year_now 	= $data['year_period'];

			switch ($month_now) {
				case '01':
					$month_now_name = "JANUARI";
					break;
				case '02':
					$month_now_name = "FEBRUARI";
					break;
				case '03':
					$month_now_name = "MARET";
					break;
				case '04':
					$month_now_name = "APRIL";
					break;
				case '05':
					$month_now_name = "MEI";
					break;
				case '06':
					$month_now_name = "JUNI";
					break;
				case '07':
					$month_now_name = "JULI";
					break;
				case '08':
					$month_now_name = "AGUSTUS";
					break;
				case '09':
					$month_now_name = "SEPTEMBER";
					break;
				case '10':
					$month_now_name = "OKTOBER";
					break;
				case '11':
					$month_now_name = "NOVEMBER";
					break;
				case '12':
					$month_now_name = "DESEMBER";
					break;
				
				default:
					# code...
					break;
			}

			$minus_month	= mktime(0, 0, 0, date($data['month_period']) - 1);
			$month_before	= date('m', $minus_month);

			if($month_before == 12){
				$year_before = $year_now - 1;
			} else {
				$year_before = $year_now;
			}


			switch ($month_before) {
				case '01':
					$month_before_name = "JANUARI";
					break;
				case '02':
					$month_before_name = "FEBRUARI";
					break;
				case '03':
					$month_before_name = "MARET";
					break;
				case '04':
					$month_before_name = "APRIL";
					break;
				case '05':
					$month_before_name = "MEI";
					break;
				case '06':
					$month_before_name = "JUNI";
					break;
				case '07':
					$month_before_name = "JULI";
					break;
				case '08':
					$month_before_name = "AGUSTUS";
					break;
				case '09':
					$month_before_name = "SEPTEMBER";
					break;
				case '10':
					$month_before_name = "OKTOBER";
					break;
				case '11':
					$month_before_name = "NOVEMBER";
					break;
				case '12':
					$month_before_name = "DESEMBER";
					break;
				
				default:
					# code...
					break;
			}

			if ($data['account_comparation_report_type'] == 1){
				$period_before 	= $month_before_name." ".$year_before;
				$period_now 	= $month_now_name." ".$year_now;
			} else {
				$year_before 	= $year_now - 1;
				$period_before	= $year_before;
				$period_now		= $year_now;
			}
			
			if(!empty($acctprofitlosscomparationreport_top && $acctprofitlosscomparationreport_bottom)){
				$this->load->library('excel');
				
				$this->excel->getProperties()->setCreator("SIS Integrated System")
									 ->setLastModifiedBy("SIS Integrated System")
									 ->setTitle("Laporan Komparasi Perhitungan SHU")
									 ->setSubject("")
									 ->setDescription("Laporan Komparasi Perhitungan SHU")
									 ->setKeywords("SHU, Perhitungan, Komparasi, Laporan")
									 ->setCategory("Laporan Komparasi Perhitungan SHU");
									 
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
				$this->excel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);

				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				
				$this->excel->getActiveSheet()->mergeCells("B1:E1");
				$this->excel->getActiveSheet()->mergeCells("B2:E2");
				$this->excel->getActiveSheet()->mergeCells("B4:C4");
				$this->excel->getActiveSheet()->mergeCells("D4:E4");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);

				$this->excel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true)->setSize(16);

				$this->excel->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

				$this->excel->getActiveSheet()->getStyle('D4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


				$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true)->setSize(12);
				$this->excel->getActiveSheet()->getStyle('D4')->getFont()->setBold(true)->setSize(12);
				$this->excel->getActiveSheet()->getStyle('B4:E4')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B4:E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				$this->excel->getActiveSheet()->setCellValue('B1',"Laporan Komparasi Perhitungan SHU ");	
				$this->excel->getActiveSheet()->setCellValue('B2',$preferencecompany['company_name']);	
				$this->excel->getActiveSheet()->setCellValue('B4',"PERIODE ".$period_before);	
				$this->excel->getActiveSheet()->setCellValue('D4',"PERIODE ".$period_now);	
				
				$j = 5;
				$no = 0;
				$grand_total = 0;
				
				foreach($acctprofitlosscomparationreport_top as $keyTop => $valTop){
					if(is_numeric($keyTop)){
						
						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('B'.$j.':C'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
						$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						

						if($valTop['report_tab'] == 0){
							$report_tab = ' ';
						} else if($valTop['report_tab'] == 1){
							$report_tab = '     ';
						} else if($valTop['report_tab'] == 2){
							$report_tab = '          ';
						} else if($valTop['report_tab'] == 3){
							$report_tab = '               ';
						}

						if($valTop['report_bold'] == 1){
							$this->excel->getActiveSheet()->getStyle('B'.$j)->getFont()->setBold(true);	
							$this->excel->getActiveSheet()->getStyle('C'.$j)->getFont()->setBold(true);	
						} else {
						
						}

						if($valTop['report_type'] == 1){
							$this->excel->getActiveSheet()->mergeCells("B".$j.":C".$j."");
							$this->excel->getActiveSheet()->setCellValue('B'.$j, $valTop['account_name']);
						}
							
						
						if($valTop['report_type']	== 2){
							$this->excel->getActiveSheet()->setCellValue('B'.$j, $valTop['account_name']);
						}
								

						if($valTop['report_type']	== 3){
							$accountamount 		= $this->AcctProfitLossComparationReport_model->getAccountAmount($valTop['account_id'], $month_before, $year_before, $data['account_comparation_report_type'], $data['branch_id']);

							$account_subtotal 	= ABS(($accountamount['account_in_amount'] - $accountamount['account_out_amount']));

							$this->excel->getActiveSheet()->setCellValue('B'.$j, $report_tab.$valTop['account_name']);
							$this->excel->getActiveSheet()->setCellValue('C'.$j, $report_tab.$account_subtotal);

							$account_amount_top_before[$valTop['report_no']] = $account_subtotal;
						}


						if($valTop['report_type'] == 5){
							if(!empty($valTop['report_formula']) && !empty($valTop['report_operator'])){
								$report_formula 	= explode('#', $valTop['report_formula']);
								$report_operator 	= explode('#', $valTop['report_operator']);

								$total_account_amount	= 0;
								for($i = 0; $i < count($report_formula); $i++){
									if($report_operator[$i] == '-'){
										if($value == 0 ){
											$total_account_amount = $total_account_amount + $account_amount_top_before[$report_formula[$i]];
										} else {
											$total_account_amount = $total_account_amount - $account_amount_top_before[$report_formula[$i]];
										}
									} else if($report_operator[$i] == '+'){
										if($total_account_amount == 0){
											$total_account_amount = $total_account_amount + $account_amount_top_before[$report_formula[$i]];
										} else {
											$total_account_amount = $total_account_amount + $account_amount_top_before[$report_formula[$i]];
										}
									}
								}

								$this->excel->getActiveSheet()->setCellValue('B'.$j, $report_tab.$valTop['account_name']);
								$this->excel->getActiveSheet()->setCellValue('C'.$j, $report_tab.$total_account_amount);
							}
						}

						if($valTop['report_type'] == 6){
							if(!empty($valTop['report_formula']) && !empty($valTop['report_operator'])){
								$report_formula 	= explode('#', $valTop['report_formula']);
								$report_operator 	= explode('#', $valTop['report_operator']);

								$grand_total_account_amount1_top_before	= 0;
								for($i = 0; $i < count($report_formula); $i++){
									if($report_operator[$i] == '-'){
										if($value == 0 ){
											$grand_total_account_amount1_top_before = $grand_total_account_amount1_top_before + $account_amount_top_before[$report_formula[$i]];
										} else {
											$grand_total_account_amount1_top_before = $grand_total_account_amount1_top_before - $account_amount_top_before[$report_formula[$i]];
										}
									} else if($report_operator[$i] == '+'){
										if($grand_total_account_amount1_top_before == 0){
											$grand_total_account_amount1_top_before = $grand_total_account_amount1_top_before + $account_amount_top_before[$report_formula[$i]];
										} else {
											$grand_total_account_amount1_top_before = $grand_total_account_amount1_top_before + $account_amount_top_before[$report_formula[$i]];
										}
									}
								}

								$this->excel->getActiveSheet()->setCellValue('B'.$j, $report_tab.$valTop['account_name']);
								$this->excel->getActiveSheet()->setCellValue('C'.$j, $report_tab.$grand_total_account_amount1_top_before);
							}

						}
								

					}else{
						continue;
					}

					$j++;
				}

				$j--;

				foreach($acctprofitlosscomparationreport_bottom as $keyBottom => $valBottom){
					if(is_numeric($keyTop)){
						
						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('B'.$j.':C'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
						$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						

						if($valBottom['report_tab'] == 0){
							$report_tab = ' ';
						} else if($valBottom['report_tab'] == 1){
							$report_tab = '     ';
						} else if($valBottom['report_tab'] == 2){
							$report_tab = '          ';
						} else if($valBottom['report_tab'] == 3){
							$report_tab = '               ';
						}

						if($valBottom['report_bold'] == 1){
							$this->excel->getActiveSheet()->getStyle('B'.$j)->getFont()->setBold(true);	
							$this->excel->getActiveSheet()->getStyle('C'.$j)->getFont()->setBold(true);	
						} else {
						
						}

						if($valBottom['report_type'] == 1){
							$this->excel->getActiveSheet()->mergeCells("B".$j.":C".$j."");
							$this->excel->getActiveSheet()->setCellValue('B'.$j, $valBottom['account_name']);
						}
							
						
						if($valBottom['report_type']	== 2){
							$this->excel->getActiveSheet()->setCellValue('B'.$j, $valBottom['account_name']);
						}
								

						if($valBottom['report_type']	== 3){
							$accountamount 		= $this->AcctProfitLossComparationReport_model->getAccountAmount($valBottom['account_id'], $month_before, $year_before, $data['account_comparation_report_type'], $data['branch_id']);

							$account_subtotal 	= ABS(($accountamount['account_in_amount'] - $accountamount['account_out_amount']));

							$this->excel->getActiveSheet()->setCellValue('B'.$j, $report_tab.$valBottom['account_name']);
							$this->excel->getActiveSheet()->setCellValue('C'.$j, $report_tab.$account_subtotal);

							$account_amount_bottom_before[$valBottom['report_no']] = $account_subtotal;
						}


						if($valBottom['report_type'] == 5){
							if(!empty($valBottom['report_formula']) && !empty($valBottom['report_operator'])){
								$report_formula 	= explode('#', $valBottom['report_formula']);
								$report_operator 	= explode('#', $valBottom['report_operator']);

								$total_account_amount2	= 0;
								for($i = 0; $i < count($report_formula); $i++){
									if($report_operator[$i] == '-'){
										if($value == 0 ){
											$total_account_amount2 = $total_account_amount2 + $account_amount_bottom_before[$report_formula[$i]];
										} else {
											$total_account_amount2 = $total_account_amount2 - $account_amount_bottom_before[$report_formula[$i]];
										}
									} else if($report_operator[$i] == '+'){
										if($total_account_amount2 == 0){
											$total_account_amount2 = $total_account_amount2 + $account_amount_bottom_before[$report_formula[$i]];
										} else {
											$total_account_amount2 = $total_account_amount2 + $account_amount_bottom_before[$report_formula[$i]];
										}
									}
								}

								$this->excel->getActiveSheet()->setCellValue('B'.$j, $report_tab.$valBottom['account_name']);
								$this->excel->getActiveSheet()->setCellValue('C'.$j, $report_tab.$total_account_amount2);
							}
						}

						if($valBottom['report_type'] == 6){
							if(!empty($valBottom['report_formula']) && !empty($valBottom['report_operator'])){
								$report_formula 	= explode('#', $valBottom['report_formula']);
								$report_operator 	= explode('#', $valBottom['report_operator']);

								$grand_total_account_amount2_bottom_before	= 0;
								for($i = 0; $i < count($report_formula); $i++){
									if($report_operator[$i] == '-'){
										if($value == 0 ){
											$grand_total_account_amount2_bottom_before = $grand_total_account_amount2_bottom_before + $account_amount_bottom_before[$report_formula[$i]];
										} else {
											$grand_total_account_amount2_bottom_before = $grand_total_account_amount2_bottom_before - $account_amount_bottom_before[$report_formula[$i]];
										}
									} else if($report_operator[$i] == '+'){
										if($grand_total_account_amount2_bottom_before == 0){
											$grand_total_account_amount2_bottom_before = $grand_total_account_amount2_bottom_before + $account_amount_bottom_before[$report_formula[$i]];
										} else {
											$grand_total_account_amount2_bottom_before = $grand_total_account_amount2_bottom_before + $account_amount_bottom_before[$report_formula[$i]];
										}
									}
								}

								$this->excel->getActiveSheet()->setCellValue('B'.$j, $report_tab.$valBottom['account_name']);
								$this->excel->getActiveSheet()->setCellValue('C'.$j, $report_tab.$grand_total_account_amount2_bottom_before);
							}

						}
								

					}else{
						continue;
					}

					$j++;
				}

				$total_row_before = $j;




				$j = 5;
				$no = 0;
				$grand_total = 0;
				
				foreach($acctprofitlosscomparationreport_top as $keyTop => $valTop){
					if(is_numeric($keyTop)){
						
						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('D'.$j.':E'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
						$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						

						if($valTop['report_tab'] == 0){
							$report_tab = ' ';
						} else if($valTop['report_tab'] == 1){
							$report_tab = '     ';
						} else if($valTop['report_tab'] == 2){
							$report_tab = '          ';
						} else if($valTop['report_tab'] == 3){
							$report_tab = '               ';
						}

						if($valTop['report_bold'] == 1){
							$this->excel->getActiveSheet()->getStyle('D'.$j)->getFont()->setBold(true);	
							$this->excel->getActiveSheet()->getStyle('E'.$j)->getFont()->setBold(true);	
						} else {
						
						}

						if($valTop['report_type'] == 1){
							$this->excel->getActiveSheet()->mergeCells("D".$j.":E".$j."");
							$this->excel->getActiveSheet()->setCellValue('D'.$j, $valTop['account_name']);
						}
							
						
						if($valTop['report_type']	== 2){
							$this->excel->getActiveSheet()->setCellValue('D'.$j, $valTop['account_name']);
						}
								

						if($valTop['report_type']	== 3){
							$accountamount 		= $this->AcctProfitLossComparationReport_model->getAccountAmount($valTop['account_id'], $month_now, $year_now, $data['account_comparation_report_type'], $data['branch_id']);

							$account_subtotal 	= ABS(($accountamount['account_in_amount'] - $accountamount['account_out_amount']));

							$this->excel->getActiveSheet()->setCellValue('D'.$j, $report_tab.$valTop['account_name']);
							$this->excel->getActiveSheet()->setCellValue('E'.$j, $report_tab.$account_subtotal);

							$account_amount_top_now[$valTop['report_no']] = $account_subtotal;
						}


						if($valTop['report_type'] == 5){
							if(!empty($valTop['report_formula']) && !empty($valTop['report_operator'])){
								$report_formula 	= explode('#', $valTop['report_formula']);
								$report_operator 	= explode('#', $valTop['report_operator']);

								$total_account_amount	= 0;
								for($i = 0; $i < count($report_formula); $i++){
									if($report_operator[$i] == '-'){
										if($value == 0 ){
											$total_account_amount = $total_account_amount + $account_amount_top_now[$report_formula[$i]];
										} else {
											$total_account_amount = $total_account_amount - $account_amount_top_now[$report_formula[$i]];
										}
									} else if($report_operator[$i] == '+'){
										if($total_account_amount == 0){
											$total_account_amount = $total_account_amount + $account_amount_top_now[$report_formula[$i]];
										} else {
											$total_account_amount = $total_account_amount + $account_amount_top_now[$report_formula[$i]];
										}
									}
								}

								$this->excel->getActiveSheet()->setCellValue('D'.$j, $report_tab.$valTop['account_name']);
								$this->excel->getActiveSheet()->setCellValue('E'.$j, $report_tab.$total_account_amount);
							}
						}

						if($valTop['report_type'] == 6){
							if(!empty($valTop['report_formula']) && !empty($valTop['report_operator'])){
								$report_formula 	= explode('#', $valTop['report_formula']);
								$report_operator 	= explode('#', $valTop['report_operator']);

								$grand_total_account_amount1_top_now	= 0;
								for($i = 0; $i < count($report_formula); $i++){
									if($report_operator[$i] == '-'){
										if($value == 0 ){
											$grand_total_account_amount1_top_now = $grand_total_account_amount1_top_now + $account_amount_top_now[$report_formula[$i]];
										} else {
											$grand_total_account_amount1_top_now = $grand_total_account_amount1_top_now - $account_amount_top_now[$report_formula[$i]];
										}
									} else if($report_operator[$i] == '+'){
										if($grand_total_account_amount1_top_now == 0){
											$grand_total_account_amount1_top_now = $grand_total_account_amount1_top_now + $account_amount_top_now[$report_formula[$i]];
										} else {
											$grand_total_account_amount1_top_now = $grand_total_account_amount1_top_now + $account_amount_top_now[$report_formula[$i]];
										}
									}
								}

								$this->excel->getActiveSheet()->setCellValue('E'.$j, $report_tab.$valTop['account_name']);
								$this->excel->getActiveSheet()->setCellValue('E'.$j, $report_tab.$grand_total_account_amount1_top_now);
							}

						}
								

					}else{
						continue;
					}

					$j++;
				}

				$j--;

				foreach($acctprofitlosscomparationreport_bottom as $keyBottom => $valBottom){
					if(is_numeric($keyTop)){
						
						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('D'.$j.':E'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
						$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						

						if($valBottom['report_tab'] == 0){
							$report_tab = ' ';
						} else if($valBottom['report_tab'] == 1){
							$report_tab = '     ';
						} else if($valBottom['report_tab'] == 2){
							$report_tab = '          ';
						} else if($valBottom['report_tab'] == 3){
							$report_tab = '               ';
						}

						if($valBottom['report_bold'] == 1){
							$this->excel->getActiveSheet()->getStyle('D'.$j)->getFont()->setBold(true);	
							$this->excel->getActiveSheet()->getStyle('E'.$j)->getFont()->setBold(true);	
						} else {
						
						}

						if($valBottom['report_type'] == 1){
							$this->excel->getActiveSheet()->mergeCells("D".$j.":E".$j."");
							$this->excel->getActiveSheet()->setCellValue('D'.$j, $valBottom['account_name']);
						}
							
						
						if($valBottom['report_type']	== 2){
							$this->excel->getActiveSheet()->setCellValue('D'.$j, $valBottom['account_name']);
						}
								

						if($valBottom['report_type']	== 3){
							$accountamount 		= $this->AcctProfitLossComparationReport_model->getAccountAmount($valBottom['account_id'], $data['month_period'], $data['year_period'], $data['account_comparation_report_type'], $data['branch_id']);

							$account_subtotal 	= ABS(($accountamount['account_in_amount'] - $accountamount['account_out_amount']));

							$this->excel->getActiveSheet()->setCellValue('D'.$j, $report_tab.$valBottom['account_name']);
							$this->excel->getActiveSheet()->setCellValue('E'.$j, $report_tab.$account_subtotal);

							$account_amount_bottom_now[$valBottom['report_no']] = $account_subtotal;
						}


						if($valBottom['report_type'] == 5){
							if(!empty($valBottom['report_formula']) && !empty($valBottom['report_operator'])){
								$report_formula 	= explode('#', $valBottom['report_formula']);
								$report_operator 	= explode('#', $valBottom['report_operator']);

								$total_account_amount	= 0;
								for($i = 0; $i < count($report_formula); $i++){
									if($report_operator[$i] == '-'){
										if($value == 0 ){
											$total_account_amount = $total_account_amount + $account_amount_bottom_now[$report_formula[$i]];
										} else {
											$total_account_amount = $total_account_amount - $account_amount_bottom_now[$report_formula[$i]];
										}
									} else if($report_operator[$i] == '+'){
										if($total_account_amount == 0){
											$total_account_amount = $total_account_amount + $account_amount_bottom_now[$report_formula[$i]];
										} else {
											$total_account_amount = $total_account_amount + $account_amount_bottom_now[$report_formula[$i]];
										}
									}
								}

								$this->excel->getActiveSheet()->setCellValue('D'.$j, $report_tab.$valBottom['account_name']);
								$this->excel->getActiveSheet()->setCellValue('E'.$j, $report_tab.$total_account_amount);
							}
						}

						if($valBottom['report_type'] == 6){
							if(!empty($valBottom['report_formula']) && !empty($valBottom['report_operator'])){
								$report_formula 	= explode('#', $valBottom['report_formula']);
								$report_operator 	= explode('#', $valBottom['report_operator']);

								$grand_total_account_amount2_bottom_now	= 0;
								for($i = 0; $i < count($report_formula); $i++){
									if($report_operator[$i] == '-'){
										if($value == 0 ){
											$grand_total_account_amount2_bottom_now = $grand_total_account_amount2_bottom_now + $account_amount_bottom_now[$report_formula[$i]];
										} else {
											$grand_total_account_amount2_bottom_now = $grand_total_account_amount2_bottom_now - $account_amount_bottom_now[$report_formula[$i]];
										}
									} else if($report_operator[$i] == '+'){
										if($grand_total_account_amount2_bottom_now == 0){
											$grand_total_account_amount2_bottom_now = $grand_total_account_amount2_bottom_now + $account_amount_bottom_now[$report_formula[$i]];
										} else {
											$grand_total_account_amount2_bottom_now = $grand_total_account_amount2_bottom_now + $account_amount_bottom_now[$report_formula[$i]];
										}
									}
								}

								$this->excel->getActiveSheet()->setCellValue('D'.$j, $report_tab.$valBottom['account_name']);
								$this->excel->getActiveSheet()->setCellValue('E'.$j, $report_tab.$grand_total_account_amount2_bottom_now);
							}
						}								

					}else{
						continue;
					}

					$j++;
				}

				$total_row_now = $j;

				if ($total_row_now > $total_row_before){
					$total_row = $total_row_now;
				} else {
					$total_row = $total_row_before;
				}

				$this->excel->getActiveSheet()->getStyle('B'.$total_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$this->excel->getActiveSheet()->getStyle('C'.$total_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

				$this->excel->getActiveSheet()->getStyle('D'.$total_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$this->excel->getActiveSheet()->getStyle('E'.$total_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

				$this->excel->getActiveSheet()->getStyle("B".$total_row.":E".$total_row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

				$this->excel->getActiveSheet()->getStyle("B".($total_row-2).":E".$total_row)->getFont()->setBold(true);	

				$shu_before 				= $grand_total_account_amount1_top_before - $grand_total_account_amount2_bottom_before;
				$accountamounttaxbefore 	= $this->AcctProfitLossComparationReport_model->getAccountAmount($preferencecompany['account_income_tax_id'], $month_before, $year_before, $data['account_comparation_report_type'], $data['branch_id']);

				$income_tax_before 			= ABS(($accountamounttaxbefore['account_in_amount'] - $accountamounttaxbefore['account_out_amount']));

				$shu_now 					= $grand_total_account_amount1_top_now - $grand_total_account_amount2_bottom_now;
				$accountamounttaxnow 		= $this->AcctProfitLossComparationReport_model->getAccountAmount($preferencecompany['account_income_tax_id'], $data['month_period'], $data['year_period'], $data['account_comparation_report_type'], $data['branch_id']);

				$income_tax_now 			= ABS(($accountamounttaxnow['account_in_amount'] - $accountamounttaxnow['account_out_amount']));

				$this->excel->getActiveSheet()->setCellValue('B'.($total_row-2), "SHU SEBELUM PAJAK");
				$this->excel->getActiveSheet()->setCellValue('C'.($total_row-2), $shu_before);
				$this->excel->getActiveSheet()->setCellValue('B'.($total_row-1), "PAJAK PENGHASILAN");
				$this->excel->getActiveSheet()->setCellValue('C'.($total_row-1), $income_tax_before);
				$this->excel->getActiveSheet()->setCellValue('B'.$total_row, "SHU SETELAH PAJAK");
				$this->excel->getActiveSheet()->setCellValue('C'.$total_row, $shu_before - $income_tax_before);

				$this->excel->getActiveSheet()->setCellValue('D'.($total_row-2), "SHU SEBELUM PAJAK");
				$this->excel->getActiveSheet()->setCellValue('E'.($total_row-2), $shu_now);
				$this->excel->getActiveSheet()->setCellValue('D'.($total_row-1), "PAJAK PENGHASILAN");
				$this->excel->getActiveSheet()->setCellValue('E'.($total_row-1), $income_tax_now);
				$this->excel->getActiveSheet()->setCellValue('D'.$total_row, "SHU SETELAH PAJAK");
				$this->excel->getActiveSheet()->setCellValue('E'.$total_row, $shu_now - $income_tax_now);

				$filename='Laporan Komparasi Perhitungan SHU '.$period_before.' - '.$period_now.'.xls';
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