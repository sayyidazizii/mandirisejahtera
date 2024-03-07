<?php 
defined('BASEPATH') or exit('No direct script access allowed');
ob_start();?>
<?php
	Class AcctBalanceSheetReportNew1 extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctBalanceSheetReportNew1_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$auth = $this->session->userdata('auth');

			$data['main_view']['monthlist']						= $this->configuration->Month();

			$data['main_view']['corebranch']					= create_double($this->AcctBalanceSheetReportNew1_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['acctbalancesheetreport_left']	= $this->AcctBalanceSheetReportNew1_model->getAcctBalanceSheetReportNew1_Left();

			$data['main_view']['acctbalancesheetreport_right']	= $this->AcctBalanceSheetReportNew1_model->getAcctBalanceSheetReportNew1_Right();

			$data['main_view']['content']						= 'AcctBalanceSheetReport/AcctBalanceSheetReportNew1_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"branch_id" 	=> $this->input->post('branch_id',true),
				"month_period" 	=> $this->input->post('month_period',true),
				"year_period" 	=> $this->input->post('year_period',true),
			);

			$this->session->set_userdata('filter-AcctBalanceSheetReportNew1',$data);
			redirect('balance-sheet');
		}

		public function processPrinting(){
			$sesi	= 	$this->session->userdata('filter-AcctBalanceSheetReportNew1');
			$auth 	= $this->session->userdata('auth');

			if($auth['branch_status'] == 1){
				if(!is_array($sesi)){
					$sesi['branch_id']			= $auth['branch_id'];
					$sesi['month_period']		= date('m');
					$sesi['year_period']		= date('Y');
				}
			} else {
				if(!is_array($sesi)){
					$sesi['branch_id']			= $auth['branch_id'];
					$sesi['month_period']		= date('m');
					$sesi['year_period']		= date('Y');

				}

				if(empty($sesi['branch_id'])){
					$sesi['branch_id'] 		= $auth['branch_id'];
				}
			}
			$branchname 					= $this->AcctBalanceSheetReportNew1_model->getBranchName($sesi['branch_id']);

			$preferencecompany 				= $this->AcctBalanceSheetReportNew1_model->getPreferenceCompany();

			$acctbalancesheetreport_left	= $this->AcctBalanceSheetReportNew1_model->getAcctBalanceSheetReportNew1_Left();

			$acctbalancesheetreport_right	= $this->AcctBalanceSheetReportNew1_model->getAcctBalanceSheetReportNew1_Right();

			

			// print_r($preference_company);

			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

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

			/*print_r($preference_company);*/

			$day 	= date("t", strtotime($sesi['month_period']));
			$month 	= $sesi['month_period'];
			$year 	= $sesi['year_period'];

			if($month == 12){
				$last_month 	= 01;
				$last_year 		= $year + 1;
			} else {
				$last_month 	= $month + 1;
				$last_year 		= $year;
			}

			// print_r($last_month);
			// print_r($last_year);exit;

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
				        <td colspan=\"5\"><div style=\"text-align: center; font-size:14px\">LAPORAN NERACA <BR>".$preferencecompany['company_name']." <BR>Periode ".$period." <BR> ".$branchname."</div> </td>
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
			        $tblheader_left = "
			        	<td style=\"width: 50%\">	
			        		<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">";	
								$tblitem_left = "";
								$grand_total_account_amount1 = 0;
								$grand_total_account_amount2 = 0;
			        			foreach ($acctbalancesheetreport_left as $keyLeft => $valLeft) {
									if($valLeft['report_tab1'] == 0){
										$report_tab1 = '';
									} else if($valLeft['report_tab1'] == 1){
										$report_tab1 = '&nbsp;&nbsp;&nbsp;';
									} else if($valLeft['report_tab1'] == 2){
										$report_tab1 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									} else if($valLeft['report_tab1'] == 3){
										$report_tab1 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									}

									if($valLeft['report_bold1'] == 1){
										$report_bold1 = 'bold';
									} else {
										$report_bold1 = 'normal';
									}									

									if($valLeft['report_type1'] == 1){
										$tblitem_left1 = "
											<tr>
												<td colspan=\"2\" style=\"width: 100%\"><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div></td>
											</tr>";
									} else {
										$tblitem_left1 = "";
									}



									if($valLeft['report_type1']	== 2){
										$tblitem_left2 = "
											<tr>
												<td style=\"width: 70%\"><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div></td>
												<td style=\"width: 30%\"><div style=\"font-weight:".$report_bold1."\"></div></td>
											</tr>";
									} else {
										$tblitem_left2 = "";
									}									

									if($valLeft['report_type1']	== 3){
										$last_balance1 	= $this->AcctBalanceSheetReportNew1_model->getLastBalance($valLeft['account_id1'], $sesi['branch_id'], $last_month, $last_year);		

										$tblitem_left3 = "
											<tr>
												<td><div style=\"font-weight:".$report_bold1."\">".$report_tab1."(".$valLeft['account_code1'].") ".$valLeft['account_name1']."</div> </td>
												<td style=\"text-align:right;\">".number_format($last_balance1, 2)."</td>
											</tr>";

										$account_amount1_top[$valLeft['report_no']] = $last_balance1;

									} else {
										$tblitem_left3 = "";
									}

									if($valLeft['report_type1']	== 10){
										$last_balance10 	= $this->AcctBalanceSheetReportNew1_model->getLastBalance($valLeft['account_id1'], $sesi['branch_id'], $last_month, $last_year);		


										$account_amount10_top[$valLeft['report_no']] = $last_balance10;

									} else {
									}
									

									if($valLeft['report_type1'] == 11){
										if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
											$report_formula1 	= explode('#', $valLeft['report_formula1']);
											$report_operator1 	= explode('#', $valLeft['report_operator1']);

											$total_account_amount10	= 0;
											for($i = 0; $i < count($report_formula1); $i++){
												if($report_operator1[$i] == '-'){
													if($total_account_amount10 == 0 ){
														$total_account_amount10 = $total_account_amount10 + $account_amount10_top[$report_formula1[$i]];
													} else {
														$total_account_amount10 = $total_account_amount10 - $account_amount10_top[$report_formula1[$i]];
													}
												} else if($report_operator1[$i] == '+'){
													if($total_account_amount10 == 0){
														$total_account_amount10 = $total_account_amount10 + $account_amount10_top[$report_formula1[$i]];
													} else {
														$total_account_amount10 = $total_account_amount10 + $account_amount10_top[$report_formula1[$i]];
													}
												}
											}

											$grand_total_account_amount1 = $grand_total_account_amount1 + $total_account_amount10;

											$tblitem_left10 = "
												<tr>
													<td><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div></td>
													<td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold1."\">".number_format($total_account_amount10, 2)."</div></td>
												</tr>";
										} else {
											$tblitem_left10 = "";
										}
									} else {
										$tblitem_left10 = "";
									}

									if($valLeft['report_type1']	== 7){
										$last_balance1 	= $this->AcctBalanceSheetReportNew1_model->getLastBalance($valLeft['account_id1'], $sesi['branch_id'], $last_month, $last_year);		

										$tblitem_left7 = "
											<tr>
												<td><div style=\"font-weight:".$report_bold1."\">".$report_tab1."(".$valLeft['account_code1'].") ".$valLeft['account_name1']."</div> </td>
												<td style=\"text-align:right;\">(".number_format($last_balance1, 2).")</td>
											</tr>";

										$account_amount1_top[$valLeft['report_no']] = $last_balance1;

									} else {
										$tblitem_left7 = "";
									}
									

									if($valLeft['report_type1'] == 5){
										if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
											$report_formula1 	= explode('#', $valLeft['report_formula1']);
											$report_operator1 	= explode('#', $valLeft['report_operator1']);

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

											$grand_total_account_amount1 = $grand_total_account_amount1 + $total_account_amount1;

											$tblitem_left5 = "
												<tr>
													<td><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div></td>
													<td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold1."\">".number_format($total_account_amount1+$total_account_amount10, 2)."</div></td>
												</tr>";
										} else {
											$tblitem_left5 = "";
										}
									} else {
										$tblitem_left5 = "";
									}

									$tblitem_left .= $tblitem_left1.$tblitem_left2.$tblitem_left3.$tblitem_left10.$tblitem_left7.$tblitem_left5;

									// if($valLeft['report_type1'] == 6){
									// 	if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
									// 		$report_formula1 	= explode('#', $valLeft['report_formula1']);
									// 		$report_operator1 	= explode('#', $valLeft['report_operator1']);

									// 		$total_account_amount1	= 0;
									// 		for($i = 0; $i < count($report_formula1); $i++){
									// 			if($report_operator1[$i] == '-'){
									// 				if($total_account_amount1 == 0 ){
									// 					$total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
									// 				} else {
									// 					$total_account_amount1 = $total_account_amount1 - $account_amount1_top[$report_formula1[$i]];
									// 				}
									// 			} else if($report_operator1[$i] == '+'){
									// 				if($total_account_amount1 == 0){
									// 					$total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
									// 				} else {
									// 					$total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
									// 				}
									// 			}
									// 		}
											
									// 	} else {
											
									// 	}
									// } else {
										
									// }

								}

			        $tblfooter_left	= "
			        		</table>
			        	</td>";

			       /* print_r("tblitem_left ");
			        print_r($tblitem_left);
			        exit; */

			        $tblheader_right = "
			        	<td style=\"width: 50%\">	
			        		<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">";		
			        			$tblitem_right = "";
			        			foreach ($acctbalancesheetreport_right as $keyRight => $valRight) {
									if($valRight['report_tab2'] == 0){
										$report_tab2 = '';
									} else if($valRight['report_tab2'] == 1){
										$report_tab2 = '&nbsp;&nbsp;&nbsp;';
									} else if($valRight['report_tab2'] == 2){
										$report_tab2 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									} else if($valRight['report_tab2'] == 3){
										$report_tab2 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									}

									if($valRight['report_bold2'] == 1){
										$report_bold2 = 'bold';
									} else {
										$report_bold2 = 'normal';
									}									

									if($valRight['report_type2'] == 1){
										$tblitem_right1 = "
											<tr>
												<td colspan=\"2\"><div style=\"font-weight:".$report_bold2."\">".$report_tab2."".$valRight['account_name2']."</div></td>
											</tr>";
									} else {
										$tblitem_right1 = "";
									}



									if($valRight['report_type2'] == 2){
										$tblitem_right2 = "
											<tr>
												<td style=\"width: 70%\"><div style=\"font-weight:".$report_bold2."\">".$report_tab2."".$valRight['account_name2']."</div></td>
												<td style=\"width: 30%\"><div style=\"font-weight:".$report_bold2."\"></div></td>
											</tr>";
									} else {
										$tblitem_right2 = "";
									}									

									if($valRight['report_type2']	== 3){
										$last_balance2 	= $this->AcctBalanceSheetReportNew1_model->getLastBalance($valRight['account_id2'], $sesi['branch_id'], $last_month, $last_year);

										$tblitem_right3 = "
											<tr>
												<td><div style=\"font-weight:".$report_bold2."\">".$report_tab2."(".$valRight['account_code2'].") ".$valRight['account_name2']."</div> </td>
												<td style=\"text-align:right;\">".number_format($last_balance2, 2)."</td>
											</tr>";

										$account_amount2_bottom[$valRight['report_no']] = $last_balance2;
									} else {
										$tblitem_right3 = "";
									}

									if($valRight['report_type2'] == 8){
										$sahu_tahun_lalu = $this->AcctBalanceSheetReportNew1_model->getSHUTahunLalu($sesi['branch_id'], $month, $year);

										if(empty($sahu_tahun_lalu)){
											$sahu_tahun_lalu = 0;
										}


										
										$tblitem_right8 = "
											<tr>
												<td><div style=\"font-weight:".$report_bold2."\">".$report_tab2."(".$valRight['account_code2'].") ".$valRight['account_name2']."</div> </td>
												<td style=\"text-align:right;\">".number_format($sahu_tahun_lalu, 2)."</td>
											</tr>
											";

										$account_amount2_bottom[$valRight['report_no']] = $sahu_tahun_lalu;
									} else {
										$tblitem_right8 = "";
									}

									if($valRight['report_type2'] == 7){
										$profit_loss = $this->AcctBalanceSheetReportNew1_model->getProfitLossAmount($sesi['branch_id'], $month, $year);

										if(empty($profit_loss)){
											$profit_loss = 0;
										}

										
										$tblitem_right7 = "
											<tr>
												<td><div style=\"font-weight:".$report_bold2."\">".$report_tab2."(".$valRight['account_code2'].") ".$valRight['account_name2']."</div> </td>
												<td style=\"text-align:right;\">".number_format($profit_loss, 2)."</td>
											</tr>
											";

										$account_amount2_bottom[$valRight['report_no']] = $profit_loss;
									} else {
										$tblitem_right7 = "";
									}
									

									if($valRight['report_type2'] == 5){
										if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
											$report_formula2 	= explode('#', $valRight['report_formula2']);
											$report_operator2 	= explode('#', $valRight['report_operator2']);

											$total_account_amount2	= 0;
											for($i = 0; $i < count($report_formula2); $i++){
												if($report_operator2[$i] == '-'){
													if($total_account_amount2 == 0 ){
														$total_account_amount2 = $total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
													} else {
														$total_account_amount2 = $total_account_amount2 - $account_amount2_bottom[$report_formula2[$i]];
													}
												} else if($report_operator2[$i] == '+'){
													if($total_account_amount2 == 0){
														$total_account_amount2 = $total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
													} else {
														$total_account_amount2 = $total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
													}
												}
											}

											$grand_total_account_amount2 = $grand_total_account_amount2 + $total_account_amount2;

											$tblitem_right5 = "
												<tr>
													<td><div style=\"font-weight:".$report_bold2."\">".$report_tab2."".$valRight['account_name2']."</div></td>
													<td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold2."\">".number_format($total_account_amount2, 2)."</div></td>
												</tr>";
										} else {
											$tblitem_right5 = "";
										}
									} else {
										$tblitem_right5 = "";
									}


									

									$tblitem_right .= $tblitem_right1.$tblitem_right2.$tblitem_right3.$tblitem_right8.$tblitem_right7.$tblitem_right5;


									// if($valRight['report_type2'] == 6){
									// 	if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
									// 		$report_formula2 	= explode('#', $valRight['report_formula2']);
									// 		$report_operator2 	= explode('#', $valRight['report_operator2']);

									// 		$total_account_amount2	= 0;
									// 		for($i = 0; $i < count($report_formula2); $i++){
									// 			if($report_operator2[$i] == '-'){
									// 				if($total_account_amount2 == 0 ){
									// 					$total_account_amount2 = $total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
									// 				} else {
									// 					$total_account_amount2 = $total_account_amount2 - $account_amount2_bottom[$report_formula2[$i]];
									// 				}
									// 			} else if($report_operator2[$i] == '+'){
									// 				if($total_account_amount2 == 0){
									// 					$total_account_amount2 = $total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
									// 				} else {
									// 					$total_account_amount2 = $total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
									// 				}
									// 			}
									// 		}

									// 		$total_account_amount2 = $total_account_amount2 + $profit_loss;
									// 	} else {
											
									// 	}
									// } else {
										
									// }
									
								}

			       	$tblfooter_right = "
			       			</table>
			        	</td>";

			$tblFooter = "
			    </tr>
			    <tr>
			    	<td style=\"width: 50%\">
			    		<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\">
			    			<tr>
								<td style=\"width: 60%\"><div style=\"font-weight:".$report_bold1.";font-size:12px\">".$report_tab1."".$valLeft['account_name1']."</div></td>
								<td style=\"width: 40%; text-align:right;\"><div style=\"font-weight:".$report_bold1."; font-size:14px\">".number_format($grand_total_account_amount1, 2)."</div></td>
							</tr>
			    		</table>
			    	</td>
			    	<td style=\"width: 50%\">
			    		<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\">
			    			<tr>
								<td style=\"width: 60%\"><div style=\"font-weight:".$report_bold2.";font-size:12px\">".$report_tab2."".$valRight['account_name2']."</div></td>
								<td style=\"width: 40%; text-align:right;\"><div style=\"font-weight:".$report_bold2."; font-size:14px\">".number_format($grand_total_account_amount2, 2)."</div></td>
							</tr>
			    		</table>
			    	</td>
			    </tr>
			</table>";
			    
			$table = $tblHeader.$tblheader_left.$tblitem_left.$tblfooter_left.$tblheader_right.$tblitem_right.$tblfooter_right.$tblFooter;
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

		public function exportAcctBalanceSheetReportNew1(){
			$auth 	= $this->session->userdata('auth');
			$unique = $this->session->userdata('unique');
			$sesi	= 	$this->session->userdata('filter-AcctBalanceSheetReportNew1');

			if($auth['branch_status'] == 1){
				if(!is_array($sesi)){
					$sesi['branch_id']			= $auth['branch_id'];
					$sesi['month_period']		= date('m');
					$sesi['year_period']		= date('Y');
				}
			} else {
				if(!is_array($sesi)){
					$sesi['branch_id']			= $auth['branch_id'];
					$sesi['month_period']		= date('m');
					$sesi['year_period']		= date('Y');

				}

				if(empty($sesi['branch_id'])){
					$sesi['branch_id'] 		= $auth['branch_id'];
				}
			}

			$preferencecompany 				= $this->AcctBalanceSheetReportNew1_model->getPreferenceCompany();

			$acctbalancesheetreport_left	= $this->AcctBalanceSheetReportNew1_model->getAcctBalanceSheetReportNew1_Left();

			$acctbalancesheetreport_right	= $this->AcctBalanceSheetReportNew1_model->getAcctBalanceSheetReportNew1_Right();

			$day 	= date("t", strtotime($sesi['month_period']));
			$month 	= $sesi['month_period'];
			$year 	= $sesi['year_period'];

			// if($month == 01){
			// 	$last_month 	= 12;
			// 	$last_year 		= $year - 1;
			// } else {
			// 	$last_month 	= $month - 1;
			// 	$last_year 		= $year;
			// }
            if($month == 12){
				$last_month 	= 01;
				$last_year 		= $year + 1;
			} else {
				$last_month 	= $month + 1;
				$last_year 		= $year;
			}
							
// 			$last_month 	= $month;
// 			$last_year 		= $year;

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
			
			if(!empty($acctbalancesheetreport_left && $acctbalancesheetreport_right)){
				$this->load->library('excel');
				
				$this->excel->getProperties()->setCreator("SIS Integrated System")
									 ->setLastModifiedBy("SIS Integrated System")
									 ->setTitle("Laporan Neraca")
									 ->setSubject("")
									 ->setDescription("Laporan Neraca")
									 ->setKeywords("Neraca, Laporan, SIS, Integrated")
									 ->setCategory("Laporan Neraca");
									 
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
				$this->excel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				
				$this->excel->getActiveSheet()->mergeCells("B1:E1");
				$this->excel->getActiveSheet()->mergeCells("B2:E2");
				$this->excel->getActiveSheet()->mergeCells("B3:E3");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true)->setSize(12);

				$this->excel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true)->setSize(12);

				$this->excel->getActiveSheet()->getStyle('B4:E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B4:E4')->getFont()->setBold(true);	
				$this->excel->getActiveSheet()->setCellValue('B1',"Laporan Neraca ");	
				$this->excel->getActiveSheet()->setCellValue('B2',$preferencecompany['company_name']);	
				$this->excel->getActiveSheet()->setCellValue('B3',"Periode ".$period."");	
				
				$j = 5;
				$no = 0;
				$grand_total = 0;
				$grand_total_account_amount1 = 0;
				$grand_total_account_amount2 = 0;
				
				foreach($acctbalancesheetreport_left as $keyLeft =>$valLeft){
					if(is_numeric($keyLeft)){
						
						$this->excel->setActiveSheetIndex(0);
						/*$this->excel->getActiveSheet()->getStyle('B'.$j.':C'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);*/
				
						$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					
						if($valLeft['report_tab1'] == 0){
							$report_tab1 = ' ';
						} else if($valLeft['report_tab1'] == 1){
							$report_tab1 = '     ';
						} else if($valLeft['report_tab1'] == 2){
							$report_tab1 = '          ';
						} else if($valLeft['report_tab1'] == 3){
							$report_tab1 = '               ';
						}

						if($valLeft['report_bold1'] == 1){
							$this->excel->getActiveSheet()->getStyle('B'.$j)->getFont()->setBold(true);	
							$this->excel->getActiveSheet()->getStyle('C'.$j)->getFont()->setBold(true);	
						} else {
							
						}									

						if($valLeft['report_type1'] == 1){
							$this->excel->getActiveSheet()->mergeCells("B".$j.":C".$j."");
							$this->excel->getActiveSheet()->setCellValue('B'.$j, $valLeft['account_name1']);
						} else {

						}



						if($valLeft['report_type1']	== 2){
							$this->excel->getActiveSheet()->setCellValue('B'.$j, $report_tab1.$valLeft['account_name1']);
						} else {

						}									

						if($valLeft['report_type1']	== 3){
							$last_balance1 = $this->AcctBalanceSheetReportNew1_model->getLastBalance($valLeft['account_id1'], $sesi['branch_id'], $last_month, $last_year);		

							if (empty($last_balance1)){
								$last_balance1 = 0;
							}

							$this->excel->getActiveSheet()->setCellValue('B'.$j, $report_tab1.$valLeft['account_name1']);
							$this->excel->getActiveSheet()->setCellValue('C'.$j, $report_tab1.$last_balance1);

							$account_amount1_top[$valLeft['report_no']] = $last_balance1;

						} else {

						}

						if($valLeft['report_type1']	== 10){
							$last_balance10 = $this->AcctBalanceSheetReportNew1_model->getLastBalance($valLeft['account_id1'], $sesi['branch_id'], $last_month, $last_year);		

							if (empty($last_balance10)){
								$last_balance10 = 0;
							}

							$account_amount10_top[$valLeft['report_no']] = $last_balance10;

						} else {

						}
						

						if($valLeft['report_type1'] == 11){
							if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
								$report_formula1 	= explode('#', $valLeft['report_formula1']);
								$report_operator1 	= explode('#', $valLeft['report_operator1']);

								$total_account_amount10	= 0;
								for($i = 0; $i < count($report_formula1); $i++){
									if($report_operator1[$i] == '-'){
										if($total_account_amount10 == 0 ){
											$total_account_amount10 = $total_account_amount10 + $account_amount10_top[$report_formula1[$i]];
										} else {
											$total_account_amount10 = $total_account_amount10 - $account_amount10_top[$report_formula1[$i]];
										}
									} else if($report_operator1[$i] == '+'){
										if($total_account_amount10 == 0){
											$total_account_amount10 = $total_account_amount10 + $account_amount10_top[$report_formula1[$i]];
										} else {
											$total_account_amount10 = $total_account_amount10 + $account_amount10_top[$report_formula1[$i]];
										}
									}
								}

								$this->excel->getActiveSheet()->setCellValue('B'.$j, $report_tab1.$valLeft['account_name1']);
								$this->excel->getActiveSheet()->setCellValue('C'.$j, $report_tab1.$total_account_amount10);
								
								$grand_total_account_amount1 +=  $total_account_amount10;

								
							} else {
								
							}
						} else {
							
						}

						if($valLeft['report_type1']	== 7){
							$last_balance1 = $this->AcctBalanceSheetReportNew1_model->getLastBalance($valLeft['account_id1'], $sesi['branch_id'], $last_month, $last_year);		

							if (empty($last_balance1)){
								$last_balance1 = 0;
							}

							$this->excel->getActiveSheet()->setCellValue('B'.$j, $report_tab1.$valLeft['account_name1']);
							$this->excel->getActiveSheet()->setCellValue('C'.$j, $report_tab1.$last_balance1);

							$account_amount1_top[$valLeft['report_no']] = $last_balance1;

						} else {

						}
						

						if($valLeft['report_type1'] == 5){
							if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
								$report_formula1 	= explode('#', $valLeft['report_formula1']);
								$report_operator1 	= explode('#', $valLeft['report_operator1']);

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

								$this->excel->getActiveSheet()->setCellValue('B'.$j, $report_tab1.$valLeft['account_name1']);
								$this->excel->getActiveSheet()->setCellValue('C'.$j, $report_tab1.($total_account_amount1+$total_account_amount10));
								
								$grand_total_account_amount1 +=  $total_account_amount1;

								
							} else {
								
							}
						} else {
							
						}

						if($valLeft['report_type1'] == 6){
							if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
								$report_formula1 	= explode('#', $valLeft['report_formula1']);
								$report_operator1 	= explode('#', $valLeft['report_operator1']);

								$grand_total_account_amount1	= 0;
								for($i = 0; $i < count($report_formula1); $i++){
									if($report_operator1[$i] == '-'){
										if($grand_total_account_amount1 == 0 ){
											$grand_total_account_amount1 = $grand_total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
										} else {
											$grand_total_account_amount1 = $grand_total_account_amount1 - $account_amount1_top[$report_formula1[$i]];
										}
									} else if($report_operator1[$i] == '+'){
										if($grand_total_account_amount1 == 0){
											$grand_total_account_amount1 = $grand_total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
										} else {
											$grand_total_account_amount1 = $grand_total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
										}
									}
								}
								
							} else {
								
							}
						} else {
							
						}	

					}else{
						continue;
					}

					$j++;
				}

				$total_row_left = $j;

				$j = 5;
				$no = 0;
				$grand_total = 0;

				foreach($acctbalancesheetreport_right as $keyRight =>$valRight){
					if(is_numeric($keyRight)){
						
						$this->excel->setActiveSheetIndex(0);
						/*$this->excel->getActiveSheet()->getStyle('D'.$j.':E'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);*/
				
						$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					
						if($valRight['report_tab2'] == 0){
							$report_tab2 = ' ';
						} else if($valRight['report_tab2'] == 1){
							$report_tab2 = '     ';
						} else if($valRight['report_tab2'] == 2){
							$report_tab2 = '          ';
						} else if($valRight['report_tab2'] == 3){
							$report_tab2 = '               ';
						}

						if($valRight['report_bold2'] == 1){
							$this->excel->getActiveSheet()->getStyle('D'.$j)->getFont()->setBold(true);	
							$this->excel->getActiveSheet()->getStyle('E'.$j)->getFont()->setBold(true);	
						} else {
							
						}									

						if($valRight['report_type2'] == 1){
							$this->excel->getActiveSheet()->mergeCells("D".$j.":E".$j."");
							$this->excel->getActiveSheet()->setCellValue('D'.$j, $valRight['account_name2']);
						} else {

						}



						if($valRight['report_type2']	== 2){
							$this->excel->getActiveSheet()->setCellValue('D'.$j, $report_tab2.$valRight['account_name2']);
						} else {

						}									

						if($valRight['report_type2']	== 3){
							$last_balance2 = $this->AcctBalanceSheetReportNew1_model->getLastBalance($valRight['account_id2'], $sesi['branch_id'], $last_month, $last_year);		

							if (empty($last_balance2)){
								$last_balance2 = 0;
							}

							$this->excel->getActiveSheet()->setCellValue('D'.$j, $report_tab2.$valRight['account_name2']);
							$this->excel->getActiveSheet()->setCellValue('E'.$j, $report_tab2.$last_balance2);

							$account_amount2_bottom[$valRight['report_no']] = $last_balance2;

						} else {

						}

						if($valRight['report_type2']	== 8){
							$sahu_tahun_lalu = $this->AcctBalanceSheetReportNew1_model->getSHUTahunLalu($sesi['branch_id'], $month, $year);

							if(empty($sahu_tahun_lalu)){
								$sahu_tahun_lalu = 0;
							}


							$this->excel->getActiveSheet()->setCellValue('D'.$j, $report_tab2.$valRight['account_name2']);
							$this->excel->getActiveSheet()->setCellValue('E'.$j, $report_tab2.$sahu_tahun_lalu);

							$account_amount2_bottom[$valRight['report_no']] = $sahu_tahun_lalu;

						} else {

						}

						if($valRight['report_type2']	== 7){
							$profit_loss = $this->AcctBalanceSheetReportNew1_model->getProfitLossAmount($sesi['branch_id'], $month, $year);

							if(empty($profit_loss)){
								$profit_loss = 0;
							}

							$this->excel->getActiveSheet()->setCellValue('D'.$j, $report_tab2.$valRight['account_name2']);
							$this->excel->getActiveSheet()->setCellValue('E'.$j, $report_tab2.$profit_loss);

							$account_amount2_bottom[$valRight['report_no']] = $profit_loss;

						} else {

						}
						

						if($valRight['report_type2'] == 5){
							if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
								$report_formula2 	= explode('#', $valRight['report_formula2']);
								$report_operator2 	= explode('#', $valRight['report_operator2']);

								$total_account_amount2	= 0;
								for($i = 0; $i < count($report_formula2); $i++){
									if($report_operator2[$i] == '-'){
										if($total_account_amount2 == 0 ){
											$total_account_amount2 = $total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
										} else {
											$total_account_amount2 = $total_account_amount2 - $account_amount2_bottom[$report_formula2[$i]];
										}
									} else if($report_operator2[$i] == '+'){
										if($total_account_amount2 == 0){
											$total_account_amount2 = $total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
										} else {
											$total_account_amount2 = $total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
										}
									}
								}

								$this->excel->getActiveSheet()->setCellValue('D'.$j, $report_tab2.$valRight['account_name2']);
								$this->excel->getActiveSheet()->setCellValue('E'.$j, $report_tab2.$total_account_amount2);

								
								$grand_total_account_amount2 += $total_account_amount2;

								
							} else {
								
							}
						} else {
							
						}
						

						if($valRight['report_type2'] == 6){
							if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
								$report_formula2 	= explode('#', $valRight['report_formula2']);
								$report_operator2 	= explode('#', $valRight['report_operator2']);

								$grand_total_account_amount2	= 0;
								for($i = 0; $i < count($report_formula2); $i++){
									if($report_operator2[$i] == '-'){
										if($grand_total_account_amount2 == 0 ){
											$grand_total_account_amount2 = $grand_total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
										} else {
											$grand_total_account_amount2 = $grand_total_account_amount2 - $account_amount2_bottom[$report_formula2[$i]];
										}
									} else if($report_operator2[$i] == '+'){
										if($grand_total_account_amount2 == 0){
											$grand_total_account_amount2 = $grand_total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
										} else {
											$grand_total_account_amount2 = $grand_total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
										}
									}
								}
							} else {
								
							}
						} else {
							
						}	

					}else{
						continue;
					}

					$j++;
				}

				$total_row_right = $j;

				if ($total_row_left > $total_row_right){
					$total_row_right = $total_row_left;
				} else if ($total_row_left < $total_row_right){
					$total_row_left = $total_row_right;
				}

				$this->excel->getActiveSheet()->getStyle('B'.$total_row_left)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$this->excel->getActiveSheet()->getStyle('C'.$total_row_left)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

				$this->excel->getActiveSheet()->getStyle('D'.$total_row_right)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$this->excel->getActiveSheet()->getStyle('E'.$total_row_right)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

				$this->excel->getActiveSheet()->getStyle("B".$total_row_left.":E".$total_row_right)->getFont()->setBold(true);	

				$this->excel->getActiveSheet()->setCellValue('B'.$total_row_left, $report_tab1.$valLeft['account_name1']);
				$this->excel->getActiveSheet()->setCellValue('C'.$total_row_left, $report_tab1.$grand_total_account_amount1);

				$this->excel->getActiveSheet()->setCellValue('D'.$total_row_right, $report_tab2.$valRight['account_name2']);
				$this->excel->getActiveSheet()->setCellValue('E'.$total_row_right, $report_tab2.$grand_total_account_amount2);


				$filename='Laporan Neraca Periode '.$period.'.xls';
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