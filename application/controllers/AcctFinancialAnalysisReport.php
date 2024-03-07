<?php 
	defined('BASEPATH') or exit('No direct script access allowed');
	ob_start();?>
<?php
	Class AcctFinancialAnalysisReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctFinancialAnalysisReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$auth 	= $this->session->userdata('auth');
			if($auth['branch_status'] == 1){ 
				$sesi	= 	$this->session->userdata('filter-AcctFinancialAnalysisReport');
				if(!is_array($sesi)){
					$sesi['branch_id']		= $auth['branch_id'];
				}
			} else {
				$sesi['branch_id']	= $auth['branch_id'];
			}

			$data['main_view']['corebranch']				= create_double($this->AcctFinancialAnalysisReport_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['acctfinancialanalysisLCR']	= $this->AcctFinancialAnalysisReport_model->getAcctFinancialAnalysisLCR();
			$data['main_view']['acctfinancialanalysisCAR']	= $this->AcctFinancialAnalysisReport_model->getAcctFinancialAnalysisCAR();
			$data['main_view']['acctfinancialanalysisFDR']	= $this->AcctFinancialAnalysisReport_model->getAcctFinancialAnalysisFDR();
			$data['main_view']['acctfinancialanalysisBOPO']	= $this->AcctFinancialAnalysisReport_model->getAcctFinancialAnalysisBOPO();

			$data['main_view']['acctbalancesheetreport_left']	= $this->AcctFinancialAnalysisReport_model->getAcctBalanceSheetReport_Left();
			$data['main_view']['acctfinancialanalysisLDR']		= $this->AcctFinancialAnalysisReport_model->getAcctBalanceSheetReport_LeftLDR();
			$data['main_view']['acctprofitlossreport_top']		= $this->AcctFinancialAnalysisReport_model->getAcctProfitLossReport_Top();
			$data['main_view']['acctprofitlossreport_bottom']	= $this->AcctFinancialAnalysisReport_model->getAcctProfitLossReport_Bottom();

			$data['main_view']['content']					= 'AcctFinancialAnalysisReport/AcctFinancialAnalysisReport_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"branch_id"					=> $this->input->post('branch_id',true),
			);

			$this->session->set_userdata('filter-AcctFinancialAnalysisReport',$data);
			redirect('fincancial-analysis');
		}

		public function processPrinting(){
			$auth 	= $this->session->userdata('auth');
			if($auth['branch_status'] == 1){
				$sesi	= 	$this->session->userdata('filter-AcctFinancialAnalysisReport');
				$year_now 	=	date('Y');
				if(!is_array($sesi)){
					$data['month_period']				= date('m');
					$data['year_period']				= $year_now;
					$data['profit_loss_report_type'] 	= 1;
					$data['branch_id'] 					= $auth['branch_id'];
				}
			} else {
				$sesi['branch_id']	= $auth['branch_id'];
			}

			$preferencecompany 				= $this->AcctFinancialAnalysisReport_model->getPreferenceCompany();

			$acctfinancialanalysisLCR	= $this->AcctFinancialAnalysisReport_model->getAcctBalanceSheetReport_Left();
			$acctfinancialanalysisCAR	= $this->AcctFinancialAnalysisReport_model->getAcctFinancialAnalysisCAR();
			$acctfinancialanalysisFDR	= $this->AcctFinancialAnalysisReport_model->getAcctBalanceSheetReport_LeftLDR();
			$acctprofitlossreport_top	= $this->AcctFinancialAnalysisReport_model->getAcctProfitLossReport_Top();

			// print_r($preference_company);

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

			$pdf->SetFont('helvetica', '', 10);

			// -----------------------------------------------------------------------------

			/*print_r($preference_company);*/

			$day 	= date("d");
			$month 	= date("m");
			$year 	= date("Y");

			switch ($month) {
				case '01':
					$month_name = "Januari";
					break;
				case '02':
					$month_name = "Februari";
					break;
				case '03':
					$month_name = "Maret";
					break;
				case '04':
					$month_name = "April";
					break;
				case '05':
					$month_name = "Mei";
					break;
				case '06':
					$month_name = "Juni";
					break;
				case '07':
					$month_name = "Juli";
					break;
				case '08':
					$month_name = "Agustus";
					break;
				case '09':
					$month_name = "September";
					break;
				case '10':
					$month_name = "Oktober";
					break;
				case '11':
					$month_name = "November";
					break;
				case '12':
					$month_name = "Desember";
					break;
				
				default:
					# code...
					break;
			}

			$period = $day." ".$month_name." ".$year;

			$tbl = "
				<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\">
				    <tr>
						<td colspan=\"2\" style=\"text-align:center;\">
							<div style=\"font-weight:bold\">Tingkat Kesehatan (Rasio Keuangan)</div>
						</td>
					</tr>
					<tr>
						<td colspan=\"2\" style=\"text-align:center;\">
							<div style=\"font-weight:bold\">
								".$preferencecompany['company_name']."	
							</div>
						</td>
					</tr>
					<tr>
						<td colspan=\"2\" style=\"text-align:center;\">
							<div style=\"font-weight:bold\">Periode 
								".$period."
							</div>
						</td>
					</tr>
				</table>
			";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			// $minus_month= mktime(0, 0, 0, date($data['month_period'])-1);
			// $month = date('m', $minus_month);

			// if($month == 12){
			// 	$year = $data['year_period'] - 1;
			// } else {
			// 	$year = $data['year_period'];
			// }

			$tblHeader = "
			<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"1\">			        
			    <tr>";
			        $tblheader_LCR = "
			        	<td style=\"width: 50%\">	
			        		<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">";	
			        			$tblitem_LCR = "";
			        			foreach ($acctfinancialanalysisLCR as $key => $val) {
									if($val['report_type1']	== 3){
								$last_balance1 = $this->AcctFinancialAnalysisReport_model->getLastBalance($data['branch_id'], $val['account_id1']);

								$account_amount1_top[$val['report_no']] = $last_balance1;
													}

								if($val['report_type2']	== 3){
								$last_balance2 = $this->AcctFinancialAnalysisReport_model->getLastBalance($data['branch_id'], $val['account_id2']);

								$account_amount2_top[$val['report_no']] = $last_balance2;
														
								}


								if($val['report_type1'] == 5){
									if(!empty($val['report_formula1']) && !empty($val['report_operator1'])){
									$report_formula1 	= explode('#', $val['report_formula1']);
									$report_operator1 	= explode('#', $val['report_operator1']);

															//print_r($report_operator1);

								$total_account_amount1	= 0;
									for($i = 0; $i < count($report_formula1); $i++){
									if($report_operator1[$i] == '-'){
									if($total_account_amount1 == 0 ){
									$total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
									} else {
									$total_account_amount1 = $total_account_amount1 - $account_amount1_top[$report_formula1[$i]];
																		}
									} else if($report_operator1[$i] == '+'){
									if($total_account_amount1 == 0){
									$total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
									} else {
									$total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
																		}
																	}
																}
														}
													}
													

												if($val['report_type2'] == 5){
														if(!empty($val['report_formula2']) && !empty($val['report_operator2'])){
															$report_formula2 	= explode('#', $val['report_formula2']);
															$report_operator2 	= explode('#', $val['report_operator2']);


															$total_account_amount2	= 0;
																for($i = 0; $i < count($report_formula2); $i++){
																	if($report_operator2[$i] == '-'){
																		if($total_account_amount2 == 0 ){
																			$total_account_amount2 = $total_account_amount2 + $account_amount2_top[$report_formula2[$i]];
																		} else {
																			$total_account_amount2 = $total_account_amount2 - $account_amount2_top[$report_formula2[$i]];
																		}
																	} else if($report_operator2[$i] == '+'){
																		if($total_account_amount2 == 0){
																			$total_account_amount2 = $total_account_amount2 + $account_amount2_top[$report_formula2[$i]];
																		} else {
																			$total_account_amount2 = $total_account_amount2 + $account_amount2_top[$report_formula2[$i]];
																		}
																	}
																}
														}
													}
												}


								if($total_account_amount1 == 0 && $total_account_amount2 == 0){
										$RASIO_LCR = 0;

									} else {
								$RASIO_LCR = ($total_account_amount1 / $total_account_amount2);

								}

								$tblitem_LCR ="
									<tr>
										<td style=\"text-align:center\" colspan=\"2\"><div style=\"font-weight: bold\">LCR (Likuiditas Cash Ratio)</div></td>
									</tr>
									<tr>
										<td style=\"text-align:center\"><div style=\"font-weight: bold\">".number_format($total_account_amount1,2)."</div></td>
										<td style=\"text-align:center\"><div style=\"font-weight: bold\">".number_format($total_account_amount2,2)."</div></td>
									</tr>
									<tr>
										<td style=\"text-align:center;height: 50px\" colspan=\"2\"><div style=\"font-weight: bold; font-size: 20px\">".number_format($RASIO_LCR, 2)." %</div></td>
									</tr>
								";

			        $tblfooter_LCR	= "
			        		</table>
			        	</td>
			        ";

		        	$tblheader_CAR = "
		        	<td style=\"width: 50%\">	
		        		<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">";	
		        			$tblitem_CAR = "";
		        			foreach ($acctfinancialanalysisCAR as $key => $val) {
								if($val['report_type1']	== 1){
									$last_balance1 = $this->AcctFinancialAnalysisReport_model->getLastBalance($val['account_id1'], $sesi['branch_id']);

									$account_amount1_top[$val['report_no']] = $last_balance1;
								}

								if($val['report_type2']	== 1){
									$last_balance2 = $this->AcctFinancialAnalysisReport_model->getLastBalance($val['account_id2'], $sesi['branch_id']);

									$account_amount2_top[$val['report_no']] = $last_balance2;
								}

								if($val['report_type1'] == 2){
									if(!empty($val['report_formula1']) && !empty($val['report_operator1'])){
										$report_formula1 	= explode('#', $val['report_formula1']);
										$report_operator1 	= explode('#', $val['report_operator1']);

										$total_account_amount1	= 0;
										for($i = 0; $i < count($report_formula1); $i++){
											if($report_operator1[$i] == '-'){
												if($total_account_amount1 == 0 ){
													$total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
												} else {
													$total_account_amount1 = $total_account_amount1 - $account_amount1_top[$report_formula1[$i]];
												}
											} else if($report_operator1[$i] == '+'){
												if($total_account_amount1 == 0){
													$total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
												} else {
													$total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
												}
											}
										}
									}
								}

								if($val['report_type2'] == 2){
									if(!empty($val['report_formula2']) && !empty($val['report_operator2'])){
										$report_formula2 	= explode('#', $val['report_formula2']);
										$report_operator2 	= explode('#', $val['report_operator2']);

										$total_account_amount2	= 0;
										for($i = 0; $i < count($report_formula2); $i++){
											if($report_operator2[$i] == '-'){
												if($total_account_amount2 == 0 ){
													$total_account_amount2 = $total_account_amount2 + $account_amount2_top[$report_formula2[$i]];
												} else {
													$total_account_amount2 = $total_account_amount2 - $account_amount2_top[$report_formula2[$i]];
												}
											} else if($report_operator2[$i] == '+'){
												if($total_account_amount2 == 0){
													$total_account_amount2 = $total_account_amount2 + $account_amount2_top[$report_formula2[$i]];
												} else {
													$total_account_amount2 = $total_account_amount2 + $account_amount2_top[$report_formula2[$i]];
												}
											}
										}															
									}
								}
							}

							if($total_account_amount1 == 0 && $total_account_amount2 == 0){
								$RASIO_CAR = 0;
							} else {
								$RASIO_CAR = ($total_account_amount1 / $total_account_amount2) * 100;
							}

							$tblitem_CAR ="
								<tr>
									<td style=\"text-align:center\" colspan=\"2\"><div style=\"font-weight: bold\">CAR (Capital Aset Ratio)</div></td>
								</tr>
								<tr>
									<td style=\"text-align:center\"><div style=\"font-weight: bold\">".number_format($total_account_amount1,2)."</div></td>
									<td style=\"text-align:center\"><div style=\"font-weight: bold\">".number_format($total_account_amount2,2)."</div></td>
								</tr>
								<tr>
									<td style=\"text-align:center;height: 50px\" colspan=\"2\"><div style=\"font-weight: bold; font-size: 20px\">".number_format($RASIO_CAR, 2)." %</div></td>
								</tr>
							";

		        $tblfooter_CAR	= "
		        		</table>
		        	</td>
		        </tr>
		        <tr>
		       ";

			    $tblheader_FDR = "
			        	<td style=\"width: 50%\">	
			        		<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">";	
			        			$tblitem_FDR = "";
			        			foreach ($acctfinancialanalysisFDR as $key => $val) {
									if($val['report_type1']	== 3){
														$last_balance1 = $this->AcctFinancialAnalysisReport_model->getLastBalance($data['branch_id'], $val['account_id1']);

														$account_amount1_top[$val['report_no']] = $last_balance1;
														// print_r('<br>');
														// print_r($last_balance1);
													}

													
												

													if($val['report_type1'] == 5){
														if(!empty($val['report_formula1']) && !empty($val['report_operator1'])){
															$report_formula1 	= explode('#', $val['report_formula1']);
															$report_operator1 	= explode('#', $val['report_operator1']);

															//print_r($report_operator1);

															$total_account_amount1	= 0;
																for($i = 0; $i < count($report_formula1); $i++){
																	if($report_operator1[$i] == '-'){
																		if($total_account_amount1 == 0 ){
																			$total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
																		} else {
																			$total_account_amount1 = $total_account_amount1 - $account_amount1_top[$report_formula1[$i]];
																		}
																	} else if($report_operator1[$i] == '+'){
																		if($total_account_amount1 == 0){
																			$total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
																		} else {
																			$total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
																		}
																	}
																}
														}
													}

													$account_id = $this->AcctFinancialAnalysisReport_model->getAcctBalanceSheetReport_Left();
												foreach ($account_id as $key1 => $val1) {

													

													if($val1['report_type2']	== 3){
														$last_balance2 = $this->AcctFinancialAnalysisReport_model->getLastBalance($data['branch_id'], $val1['account_id2']);

														$account_amount2_top[$val1['report_no']] = $last_balance2;

													// 	print_r('<br>');
													// print_r($last_balance2);
													}

													if($val1['report_type2'] == 5){
														if(!empty($val1['report_formula2']) && !empty($val1['report_operator2'])){
															$report_formula2 	= explode('#', $val1['report_formula2']);
															$report_operator2 	= explode('#', $val1['report_operator2']);

															//print_r($last_balance2);

															$total_account_amount2	= 0;
															for($i = 0; $i < count($report_formula2); $i++){
																if($report_operator2[$i] == '-'){
																	if($total_account_amount2 == 0 ){
																		$total_account_amount2 = $total_account_amount2 + $account_amount2_top[$report_formula2[$i]];
																	} else {
																		$total_account_amount2 = $total_account_amount2 - $account_amount2_top[$report_formula2[$i]];
																	}
																} else if($report_operator2[$i] == '+'){
																	if($total_account_amount2 == 0){
																		$total_account_amount2 = $total_account_amount2 + $account_amount2_top[$report_formula2[$i]];
																	} else {
																		$total_account_amount2 = $total_account_amount2 + $account_amount2_top[$report_formula2[$i]];
																	// print_r('<br>');
																	// print_r($total_account_amount2);
																	}
																}
															}
														}
													}													
												}
											}

												if($total_account_amount1 == 0 && $total_account_amount2 == 0){
													$RASIO_FDR = 0;
												} else {
													$RASIO_FDR = ($total_account_amount1 / $total_account_amount2);
												}
										

								$tblitem_FDR ="
									<tr>
										<td style=\"text-align:center\" colspan=\"2\"><div style=\"font-weight: bold\">FDR (Financing to Debt Ratio)</div></td>
									</tr>
									<tr>
										<td style=\"text-align:center\"><div style=\"font-weight: bold\">".number_format($total_account_amount1,2)."</div></td>
										<td style=\"text-align:center\"><div style=\"font-weight: bold\">".number_format($total_account_amount2,2)."</div></td>
									</tr>
									<tr>
										<td style=\"text-align:center;height: 50px\" colspan=\"2\"><div style=\"font-weight: bold; font-size: 20px\">".number_format($RASIO_FDR, 2)." %</div></td>
									</tr>
								";

			        $tblfooter_FDR	= "
			        		</table>
			        	</td>";

		        	$tblheader_BOPO = "
		        	<td style=\"width: 50%\">	
		        		<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">";	
		        			$tblitem_BOPO = "";
		        			foreach ($acctprofitlossreport_top as $key2 => $val2) {

													if($val2['report_type']	== 3){
														$last_balance2 = $this->AcctFinancialAnalysisReport_model->getAccountAmount($val2['account_id'], $data['month_period'], $data['year_period'], $data['profit_loss_report_type'], $data['branch_id']);
														

														$account_amount1 	= (($last_balance2['account_in_amount'] - $last_balance2['account_out_amount']));			

														$account_amount3_top[$val2['report_no']] = $account_amount1;

														// print_r('<br>');
														// print_r($account_amount3_top);
														

													}

								
													if($val2['report_type'] == 5){
															if(!empty($val2['report_formula']) && !empty($val2['report_operator'])){
																$report_formula3 	= explode('#', $val2['report_formula']);
																$report_operator3 	= explode('#', $val2['report_operator']);

																

															$total_account_amount3	= 0;
															for($i = 0; $i < count($report_formula3); $i++){
																if($report_operator3[$i] == '-'){
																	if($total_account_amount3 == 0 ){
																		$total_account_amount3 = $total_account_amount3 + $account_amount3_top[$report_formula3[$i]];
																	} else {
																		$total_account_amount3 = $total_account_amount3 - $account_amount3_top[$report_formula3[$i]];
																	}
																} else if($report_operator3[$i] == '+'){
																	if($total_account_amount3 == 0){
																		$total_account_amount3 = $total_account_amount3 + $account_amount3_top[$report_formula3[$i]];
																	} else {
																		$total_account_amount3 = $total_account_amount3 + $account_amount3_top[$report_formula3[$i]];
																	}
															}
														}
													}
												}
											}
											
												$account_id = $this->AcctFinancialAnalysisReport_model->getAcctProfitLossReport_Bottom();
												foreach ($account_id as $key4 => $val4) {

															if($val4['report_type']	== 3){
																	$accountamount 		= $this->AcctFinancialAnalysisReport_model->getAccountAmount($val4['account_id'], $data['month_period'], $data['year_period'], $data['profit_loss_report_type'], $data['branch_id']);
														

																	$account_subtotal 	= ABS(($accountamount['account_in_amount'] - $accountamount['account_out_amount']));
																

																	$account_amount[$val4['report_no']] = $account_subtotal;

																}

															

															if($val4['report_type'] == 6){
															if(!empty($val4['report_formula']) && !empty($val4['report_operator'])){
																$report_formula3 	= explode('#', $val4['report_formula']);
																$report_operator3 	= explode('#', $val4['report_operator']);

																

															$total_account_amount4	= 0;
															for($i = 0; $i < count($report_formula3); $i++){
																if($report_operator3[$i] == '-'){
																	if($total_account_amount4 == 0 ){
																		$total_account_amount4 = $total_account_amount4 + $account_amount[$report_formula3[$i]];
																	} else {
																		$total_account_amount4 = $total_account_amount4 - $account_amount[$report_formula3[$i]];
																	}
																} else if($report_operator3[$i] == '+'){
																	if($total_account_amount4 == 0){
																		$total_account_amount4 = $total_account_amount4 + $account_amount[$report_formula3[$i]];
																	} else {
																		$total_account_amount4 = $total_account_amount4 + $account_amount[$report_formula3[$i]];
																}
															}
														}
													}
												 }
												}

												if($total_account_amount3 == 0 && $total_account_amount4 == 0){
													$RASIO_BOPO = 0;

												} else {
													$RASIO_BOPO = ($total_account_amount3 / $total_account_amount4);

												}

							$tblitem_BOPO ="
								<tr>
									<td style=\"text-align:center\" colspan=\"2\"><div style=\"font-weight: bold\">BOPO (Beban Operasional vs Pendapatan Operasional)</div></td>
								</tr>
								<tr>
									<td style=\"text-align:center\"><div style=\"font-weight: bold\">".number_format($total_account_amount3,2)."</div></td>
									<td style=\"text-align:center\"><div style=\"font-weight: bold\">".number_format($total_account_amount4,2)."</div></td>
								</tr>
								<tr>
									<td style=\"text-align:center;height: 50px\" colspan=\"2\"><div style=\"font-weight: bold; font-size: 20px\">".number_format($RASIO_BOPO, 2)." %</div></td>
								</tr>
							";

		        $tblfooter_BOPO	= "
		        		</table>
		        	</td>
		        </tr>";

			$tblFooter = "
			</table>";
			    
			$table = $tblHeader.$tblheader_LCR.$tblitem_LCR.$tblfooter_LCR.$tblheader_CAR.$tblitem_CAR.$tblfooter_CAR.$tblheader_FDR.$tblitem_FDR.$tblfooter_FDR.$tblheader_BOPO.$tblitem_BOPO.$tblfooter_BOPO.$tblFooter;
				/*print_r("table ");
				print_r($table);
				exit;*/

			$pdf->writeHTML($table, true, false, false, false, '');

			
			
			
			//Close and output PDF document
			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Laporan Neraca.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		// public function exportAcctFinancialAnalysisReport(){
		// 	$auth 	= $this->session->userdata('auth');
		// 	$unique = $this->session->userdata('unique');

		// 	$preferencecompany 				= $this->AcctFinancialAnalysisReport_model->getPreferenceCompany();

		// 	$acctbalancesheetreport_left	= $this->AcctFinancialAnalysisReport_model->getAcctFinancialAnalysisReport_Left();

		// 	$acctbalancesheetreport_right	= $this->AcctFinancialAnalysisReport_model->getAcctFinancialAnalysisReport_Right();

		// 	$day 	= date("d");
		// 	$month 	= date("m");
		// 	$year 	= date("Y");

		// 	switch ($month) {
		// 		case '01':
		// 			$month_name = "Januari";
		// 			break;
		// 		case '02':
		// 			$month_name = "Februari";
		// 			break;
		// 		case '03':
		// 			$month_name = "Maret";
		// 			break;
		// 		case '04':
		// 			$month_name = "April";
		// 			break;
		// 		case '05':
		// 			$month_name = "Mei";
		// 			break;
		// 		case '06':
		// 			$month_name = "Juni";
		// 			break;
		// 		case '07':
		// 			$month_name = "Juli";
		// 			break;
		// 		case '08':
		// 			$month_name = "Agustus";
		// 			break;
		// 		case '09':
		// 			$month_name = "September";
		// 			break;
		// 		case '10':
		// 			$month_name = "Oktober";
		// 			break;
		// 		case '11':
		// 			$month_name = "November";
		// 			break;
		// 		case '12':
		// 			$month_name = "Desember";
		// 			break;
				
		// 		default:
		// 			# code...
		// 			break;
		// 	}

		// 	$period = $day." ".$month_name." ".$year;
			
		// 	if(!empty($acctbalancesheetreport_left && $acctbalancesheetreport_right)){
		// 		$this->load->library('excel');
				
		// 		$this->excel->getProperties()->setCreator("SIS Integrated System")
		// 							 ->setLastModifiedBy("SIS Integrated System")
		// 							 ->setTitle("Laporan Neraca")
		// 							 ->setSubject("")
		// 							 ->setDescription("Laporan Neraca")
		// 							 ->setKeywords("Neraca, Laporan, SIS, Integrated")
		// 							 ->setCategory("Laporan Neraca");
									 
		// 		$this->excel->setActiveSheetIndex(0);
		// 		$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		// 		$this->excel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);
		// 		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
		// 		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		// 		$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
		// 		$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				
		// 		$this->excel->getActiveSheet()->mergeCells("B1:E1");
		// 		$this->excel->getActiveSheet()->mergeCells("B2:E2");
		// 		$this->excel->getActiveSheet()->mergeCells("B3:E3");
		// 		$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		// 		$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
		// 		$this->excel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		// 		$this->excel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true)->setSize(12);

		// 		$this->excel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		// 		$this->excel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true)->setSize(12);

		// 		$this->excel->getActiveSheet()->getStyle('B4:E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		// 		$this->excel->getActiveSheet()->getStyle('B4:E4')->getFont()->setBold(true);	
		// 		$this->excel->getActiveSheet()->setCellValue('B1',"Laporan Neraca ");	
		// 		$this->excel->getActiveSheet()->setCellValue('B2',$preferencecompany['company_name']);	
		// 		$this->excel->getActiveSheet()->setCellValue('B3',"Periode ".$period."");	
				
		// 		$j = 5;
		// 		$no = 0;
		// 		$grand_total = 0;
				
		// 		foreach($acctbalancesheetreport_left as $keyLeft =>$valLeft){
		// 			if(is_numeric($keyLeft)){
						
		// 				$this->excel->setActiveSheetIndex(0);
		// 				/*$this->excel->getActiveSheet()->getStyle('B'.$j.':C'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);*/
				
		// 				$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		// 				$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					
		// 				if($valLeft['report_tab1'] == 0){
		// 					$report_tab1 = ' ';
		// 				} else if($valLeft['report_tab1'] == 1){
		// 					$report_tab1 = '     ';
		// 				} else if($valLeft['report_tab1'] == 2){
		// 					$report_tab1 = '          ';
		// 				} else if($valLeft['report_tab1'] == 3){
		// 					$report_tab1 = '               ';
		// 				}

		// 				if($valLeft['report_bold1'] == 1){
		// 					$this->excel->getActiveSheet()->getStyle('B'.$j)->getFont()->setBold(true);	
		// 					$this->excel->getActiveSheet()->getStyle('C'.$j)->getFont()->setBold(true);	
		// 				} else {
							
		// 				}									

		// 				if($valLeft['report_type1'] == 1){
		// 					$this->excel->getActiveSheet()->mergeCells("B".$j.":C".$j."");
		// 					$this->excel->getActiveSheet()->setCellValue('B'.$j, $valLeft['account_name1']);
		// 				} else {

		// 				}



		// 				if($valLeft['report_type1']	== 2){
		// 					$this->excel->getActiveSheet()->setCellValue('B'.$j, $report_tab1.$valLeft['account_name1']);
		// 				} else {

		// 				}									

		// 				if($valLeft['report_type1']	== 3){
		// 					$last_balance1 = $this->AcctFinancialAnalysisReport_model->getLastBalance($valLeft['account_id1']);		

		// 					if (empty($last_balance1)){
		// 						$last_balance1 = 0;
		// 					}

		// 					$this->excel->getActiveSheet()->setCellValue('B'.$j, $report_tab1.$valLeft['account_name1']);
		// 					$this->excel->getActiveSheet()->setCellValue('C'.$j, $report_tab1.$last_balance1);

		// 					$account_amount1_top[$valLeft['report_no']] = $last_balance1;

		// 				} else {

		// 				}
						

		// 				if($valLeft['report_type1'] == 5){
		// 					if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
		// 						$report_formula1 	= explode('#', $valLeft['report_formula1']);
		// 						$report_operator1 	= explode('#', $valLeft['report_operator1']);

		// 						$total_account_amount1	= 0;
		// 						for($i = 0; $i < count($report_formula1); $i++){
		// 							if($report_operator1[$i] == '-'){
		// 								if($total_account_amount1 == 0 ){
		// 									$total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
		// 								} else {
		// 									$total_account_amount1 = $total_account_amount1 - $account_amount1_top[$report_formula1[$i]];
		// 								}
		// 							} else if($report_operator1[$i] == '+'){
		// 								if($total_account_amount1 == 0){
		// 									$total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
		// 								} else {
		// 									$total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
		// 								}
		// 							}
		// 						}

		// 						$this->excel->getActiveSheet()->setCellValue('B'.$j, $report_tab1.$valLeft['account_name1']);
		// 						$this->excel->getActiveSheet()->setCellValue('C'.$j, $report_tab1.$total_account_amount1);

								
		// 					} else {
								
		// 					}
		// 				} else {
							
		// 				}

		// 				if($valLeft['report_type1'] == 6){
		// 					if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
		// 						$report_formula1 	= explode('#', $valLeft['report_formula1']);
		// 						$report_operator1 	= explode('#', $valLeft['report_operator1']);

		// 						$grand_total_account_amount1	= 0;
		// 						for($i = 0; $i < count($report_formula1); $i++){
		// 							if($report_operator1[$i] == '-'){
		// 								if($grand_total_account_amount1 == 0 ){
		// 									$grand_total_account_amount1 = $grand_total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
		// 								} else {
		// 									$grand_total_account_amount1 = $grand_total_account_amount1 - $account_amount1_top[$report_formula1[$i]];
		// 								}
		// 							} else if($report_operator1[$i] == '+'){
		// 								if($grand_total_account_amount1 == 0){
		// 									$grand_total_account_amount1 = $grand_total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
		// 								} else {
		// 									$grand_total_account_amount1 = $grand_total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
		// 								}
		// 							}
		// 						}
								
		// 					} else {
								
		// 					}
		// 				} else {
							
		// 				}	

		// 			}else{
		// 				continue;
		// 			}

		// 			$j++;
		// 		}

		// 		$total_row_left = $j;

		// 		$j = 5;
		// 		$no = 0;
		// 		$grand_total = 0;

		// 		foreach($acctbalancesheetreport_right as $keyRight =>$valRight){
		// 			if(is_numeric($keyRight)){
						
		// 				$this->excel->setActiveSheetIndex(0);
		// 				/*$this->excel->getActiveSheet()->getStyle('D'.$j.':E'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);*/
				
		// 				$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		// 				$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					
		// 				if($valRight['report_tab2'] == 0){
		// 					$report_tab2 = ' ';
		// 				} else if($valRight['report_tab2'] == 1){
		// 					$report_tab2 = '     ';
		// 				} else if($valRight['report_tab2'] == 2){
		// 					$report_tab2 = '          ';
		// 				} else if($valRight['report_tab2'] == 3){
		// 					$report_tab2 = '               ';
		// 				}

		// 				if($valRight['report_bold2'] == 1){
		// 					$this->excel->getActiveSheet()->getStyle('D'.$j)->getFont()->setBold(true);	
		// 					$this->excel->getActiveSheet()->getStyle('E'.$j)->getFont()->setBold(true);	
		// 				} else {
							
		// 				}									

		// 				if($valRight['report_type2'] == 1){
		// 					$this->excel->getActiveSheet()->mergeCells("D".$j.":E".$j."");
		// 					$this->excel->getActiveSheet()->setCellValue('D'.$j, $valRight['account_name2']);
		// 				} else {

		// 				}



		// 				if($valRight['report_type2']	== 2){
		// 					$this->excel->getActiveSheet()->setCellValue('D'.$j, $report_tab2.$valRight['account_name2']);
		// 				} else {

		// 				}									

		// 				if($valRight['report_type2']	== 3){
		// 					$last_balance2 = $this->AcctFinancialAnalysisReport_model->getLastBalance($valRight['account_id2']);		

		// 					if (empty($last_balance2)){
		// 						$last_balance2 = 0;
		// 					}

		// 					$this->excel->getActiveSheet()->setCellValue('D'.$j, $report_tab2.$valRight['account_name2']);
		// 					$this->excel->getActiveSheet()->setCellValue('E'.$j, $report_tab2.$last_balance2);

		// 					$account_amount2_bottom[$valRight['report_no']] = $last_balance2;

		// 				} else {

		// 				}
						

		// 				if($valRight['report_type2'] == 5){
		// 					if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
		// 						$report_formula2 	= explode('#', $valRight['report_formula2']);
		// 						$report_operator2 	= explode('#', $valRight['report_operator2']);

		// 						$total_account_amount2	= 0;
		// 						for($i = 0; $i < count($report_formula2); $i++){
		// 							if($report_operator2[$i] == '-'){
		// 								if($total_account_amount2 == 0 ){
		// 									$total_account_amount2 = $total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
		// 								} else {
		// 									$total_account_amount2 = $total_account_amount2 - $account_amount2_bottom[$report_formula2[$i]];
		// 								}
		// 							} else if($report_operator2[$i] == '+'){
		// 								if($total_account_amount2 == 0){
		// 									$total_account_amount2 = $total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
		// 								} else {
		// 									$total_account_amount2 = $total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
		// 								}
		// 							}
		// 						}

		// 						$this->excel->getActiveSheet()->setCellValue('D'.$j, $report_tab2.$valRight['account_name2']);
		// 						$this->excel->getActiveSheet()->setCellValue('E'.$j, $report_tab2.$total_account_amount2);

								
		// 					} else {
								
		// 					}
		// 				} else {
							
		// 				}

		// 				if($valRight['report_type2'] == 6){
		// 					if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
		// 						$report_formula2 	= explode('#', $valRight['report_formula2']);
		// 						$report_operator2 	= explode('#', $valRight['report_operator2']);

		// 						$grand_total_account_amount2	= 0;
		// 						for($i = 0; $i < count($report_formula2); $i++){
		// 							if($report_operator2[$i] == '-'){
		// 								if($grand_total_account_amount2 == 0 ){
		// 									$grand_total_account_amount2 = $grand_total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
		// 								} else {
		// 									$grand_total_account_amount2 = $grand_total_account_amount2 - $account_amount2_bottom[$report_formula2[$i]];
		// 								}
		// 							} else if($report_operator2[$i] == '+'){
		// 								if($grand_total_account_amount2 == 0){
		// 									$grand_total_account_amount2 = $grand_total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
		// 								} else {
		// 									$grand_total_account_amount2 = $grand_total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
		// 								}
		// 							}
		// 						}
								
		// 					} else {
								
		// 					}
		// 				} else {
							
		// 				}

		// 				if($valRight['report_type2'] == 7){
		// 					if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
		// 						$report_formula2 	= explode('#', $valRight['report_formula2']);
		// 						$report_operator2 	= explode('#', $valRight['report_operator2']);

		// 						$total_account_amount2_bottom	= 0;
		// 						for($i = 0; $i < count($report_formula2); $i++){
		// 							if($report_operator2[$i] == '-'){
		// 								if($total_account_amount2_bottom == 0 ){
		// 									$total_account_amount2_bottom = $total_account_amount2_bottom + $account_amount2_bottom[$report_formula2[$i]];
		// 								} else {
		// 									$total_account_amount2_bottom = $total_account_amount2_bottom - $account_amount2_bottom[$report_formula2[$i]];
		// 								}
		// 							} else if($report_operator2[$i] == '+'){
		// 								if($total_account_amount2_bottom == 0){
		// 									$total_account_amount2_bottom = $total_account_amount2_bottom + $account_amount2_bottom[$report_formula2[$i]];
		// 								} else {
		// 									$total_account_amount2_bottom = $total_account_amount2_bottom + $account_amount2_bottom[$report_formula2[$i]];
		// 								}
		// 							}
		// 						}
								
		// 					} else {
								
		// 					}


		// 					if(!empty($valRight['report_formula3']) && !empty($valRight['report_operator3'])){
		// 						$report_formula3 	= explode('#', $valRight['report_formula3']);
		// 						$report_operator3 	= explode('#', $valRight['report_operator3']);

		// 						$total_account_amount1_top	= 0;
		// 						for($i = 0; $i < count($report_formula3); $i++){
		// 							if($report_operator3[$i] == '-'){
		// 								if($total_account_amount1_top == 0 ){
		// 									$total_account_amount1_top = $total_account_amount1_top + $account_amount1_top[$report_formula3[$i]];
		// 								} else {
		// 									$total_account_amount1_top = $total_account_amount1_top - $account_amount1_top[$report_formula3[$i]];
		// 								}
		// 							} else if($report_operator3[$i] == '+'){
		// 								if($total_account_amount1_top == 0){
		// 									$total_account_amount1_top = $total_account_amount1_top + $account_amount1_top[$report_formula3[$i]];
		// 								} else {
		// 									$total_account_amount1_top = $total_account_amount1_top + $account_amount1_top[$report_formula3[$i]];
		// 								}
		// 							}
		// 						}
								
		// 					} else {
								
		// 					}

		// 					$total_account_amount3 = $total_account_amount1_top - $total_account_amount2_bottom;

		// 					$this->excel->getActiveSheet()->setCellValue('D'.$j, $report_tab2.$valRight['account_name2']);
		// 					$this->excel->getActiveSheet()->setCellValue('E'.$j, $report_tab2.$total_account_amount3);
		// 				} else {
							
		// 				}	

		// 			}else{
		// 				continue;
		// 			}

		// 			$j++;
		// 		}

		// 		$total_row_right = $j;

		// 		if ($total_row_left > $total_row_right){
		// 			$total_row_right = $total_row_left;
		// 		} else if ($total_row_left < $total_row_right){
		// 			$total_row_left = $total_row_right;
		// 		}

		// 		$this->excel->getActiveSheet()->getStyle('B'.$total_row_left)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		// 		$this->excel->getActiveSheet()->getStyle('C'.$total_row_left)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

		// 		$this->excel->getActiveSheet()->getStyle('D'.$total_row_right)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		// 		$this->excel->getActiveSheet()->getStyle('E'.$total_row_right)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

		// 		$this->excel->getActiveSheet()->getStyle("B".$total_row_left.":E".$total_row_right)->getFont()->setBold(true);	

		// 		$this->excel->getActiveSheet()->setCellValue('B'.$total_row_left, $report_tab1.$valLeft['account_name1']);
		// 		$this->excel->getActiveSheet()->setCellValue('C'.$total_row_left, $report_tab1.$grand_total_account_amount1);

		// 		$this->excel->getActiveSheet()->setCellValue('D'.$total_row_right, $report_tab2.$valRight['account_name2']);
		// 		$this->excel->getActiveSheet()->setCellValue('E'.$total_row_right, $report_tab2.$grand_total_account_amount2);


		// 		$filename='Laporan Neraca Periode '.$period.'.xls';
		// 		header('Content-Type: application/vnd.ms-excel');
		// 		header('Content-Disposition: attachment;filename="'.$filename.'"');
		// 		header('Cache-Control: max-age=0');
							 
		// 		$objWriter = IOFactory::createWriter($this->excel, 'Excel5');  
		// 		ob_end_clean();
		// 		$objWriter->save('php://output');
		// 	}else{
		// 		echo "Maaf data yang di eksport tidak ada !";
		// 	}
		// }
	}
?>