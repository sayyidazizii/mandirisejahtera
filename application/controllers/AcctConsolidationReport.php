<?php 
	defined('BASEPATH') or exit('No direct script access allowed');
	ob_start();?>
<?php
	Class AcctConsolidationReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctConsolidationReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}

		public function index(){

		}
		
		public function AcctBalanceSheet(){
			$auth = $this->session->userdata('auth');

			if($auth['branch_status'] == 1){
				$sesi = $this->session->userdata('filter-AcctBalanceSheetConsolidationReport');
				if(!is_array($sesi)){
					$sesi['consolidation_report']	= 1;
					$sesi['branch_id']				= $auth['branch_id'];
				}
			} else {
				$sesi['consolidation_report']	= 1;
				$sesi['branch_id']				= $auth['branch_id'];
			}

			
			$preferencecompany				= $this->AcctConsolidationReport_model->getPreferenceCompany();
			$branch_name 					= $this->AcctConsolidationReport_model->getBranchName($preferencecompany['central_branch_id']);

			$corebranch = array (
					'branch_id'			=> $preferencecompany['central_branch_id'],
					'branch_name'		=> $branch_name,
				);

			$data['main_view']['consolidation']					= $this->configuration->ConsolidationReport();

			/*$data['main_view']['corebranch']					= create_double($this->AcctConsolidationReport_model->getCoreBranch(),'branch_id','branch_name');*/

			$data['main_view']['corebranch']					= $corebranch;

			$data['main_view']['acctbalancesheetreport_left']	= $this->AcctConsolidationReport_model->getAcctBalanceSheetReport_Left();

			$data['main_view']['acctbalancesheetreport_right']	= $this->AcctConsolidationReport_model->getAcctBalanceSheetReport_Right();
			$data['main_view']['content']						= 'AcctConsolidationReport/AcctBalanceSheetConsolidationReport_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filterAcctBalanceSheet(){
			$data = array (
				"consolidation_report" 	=> $this->input->post('consolidation_report',true),
				"branch_id" 			=> $this->input->post('branch_id',true),
			);

			$this->session->set_userdata('filter-AcctBalanceSheetConsolidationReport',$data);
			redirect('AcctConsolidationReport/AcctBalanceSheet');
		}

		public function processPrintingAcctBalanceSheet(){
			$auth = $this->session->userdata('auth');

			$sesi = $this->session->userdata('filter-AcctBalanceSheetConsolidationReport');
			if(!is_array($sesi)){
				$sesi['consolidation_report']	= 1;
				$sesi['branch_id']				= $auth['branch_id'];
			}

			if($sesi['consolidation_report'] == 0 || $sesi['consolidation_report'] == 9){
				$branch_name = $this->AcctConsolidationReport_model->getBranchName($sesi['branch_id']);
			} else {
				$branch_name = 'GABUNGAN';
			}

			$preferencecompany 				= $this->AcctConsolidationReport_model->getPreferenceCompany();

			$acctbalancesheetreport_left	= $this->AcctConsolidationReport_model->getAcctBalanceSheetReport_Left();

			/*print_r("acctbalancesheetreport_left ");
			print_r($acctbalancesheetreport_left);
			exit;*/

			$acctbalancesheetreport_right	= $this->AcctConsolidationReport_model->getAcctBalanceSheetReport_Right();

			// print_r($preference_company);

			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new TCPDF(P, PDF_UNIT, 'F4', true, 'UTF-8', false);
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
				        <td colspan=\"5\"><div style=\"text-align: center; font-size:14px\">LAPORAN NERACA ".$branch_name."<BR>".$preferencecompany['company_name']." <BR>Periode ".$period."</div></td>
				    </tr>
				</table>
			";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$minus_month= mktime(0, 0, 0, date($data['month_period'])-1);
			$month = date('m', $minus_month);

			if($month == 12){
				$year = $data['year_period'] - 1;
			} else {
				$year = $data['year_period'];
			}

			$tblHeader = "
			<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"1\">			        
			    <tr>";
			        $tblheader_left = "
			        	<td style=\"width: 50%\">	
			        		<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">";	
			        			$tblitem_left = "";
			        			foreach ($acctbalancesheetreport_left as $keyLeft => $valLeft) {
									if($valLeft['report_tab1'] == 0){
										$report_tab1 = ' ';
									} else if($valLeft['report_tab1'] == 1){
										$report_tab1 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									} else if($valLeft['report_tab1'] == 2){
										$report_tab1 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									} else if($valLeft['report_tab1'] == 3){
										$report_tab1 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
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
												<td style=\"width: 73%\"><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div></td>
												<td style=\"width: 25%\"><div style=\"font-weight:".$report_bold1."\"></div></td>
											</tr>";
									} else {
										$tblitem_left2 = "";
									}									

									if($valLeft['report_type1']	== 3){
										$last_balance1 = $this->AcctConsolidationReport_model->getLastBalance($valLeft['account_id1'], $sesi['consolidation_report'], $sesi['branch_id']);		

										$tblitem_left3 = "
											<tr>
												<td><div style=\"font-weight:".$report_bold1."\">".$report_tab1."(".$valLeft['account_code1'].") ".$valLeft['account_name1']."</div> </td>
												<td style=\"text-align:right;\">".number_format($last_balance1, 2)."</td>
											</tr>";

										$account_amount1_top[$valLeft['report_no']] = $last_balance1;

									} else {
										$tblitem_left3 = "";
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
											$tblitem_left5 = "
												<tr>
													<td><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div></td>
													<td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold1."\">".number_format($total_account_amount1, 2)."</div></td>
												</tr>";
										} else {
											$tblitem_left5 = "";
										}
									} else {
										$tblitem_left5 = "";
									}

									$tblitem_left .= $tblitem_left1.$tblitem_left2.$tblitem_left3.$tblitem_left5;

									if($valLeft['report_type1'] == 6){
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
											
										} else {
											
										}
									} else {
										
									}

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
										$report_tab2 = ' ';
									} else if($valRight['report_tab2'] == 1){
										$report_tab2 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									} else if($valRight['report_tab2'] == 2){
										$report_tab2 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									} else if($valRight['report_tab2'] == 3){
										$report_tab2 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									}

									if($valRight['report_bold2'] == 1){
										$report_bold2 = 'bold';
									} else {
										$report_bold2 = 'normal';
									}									

									if($valRight['report_type2'] == 1){
										$tblitem_right1 = "
											<tr>
												<td colspan=\"2\" width=\"100%\"><div style=\"font-weight:".$report_bold2."\">".$report_tab2."".$valRight['account_name2']."</div></td>
											</tr>";
									} else {
										$tblitem_right1 = "";
									}



									if($valRight['report_type2'] == 2){
										$tblitem_right2 = "
											<tr>
												<td style=\"width: 73%\"><div style=\"font-weight:".$report_bold2."\">".$report_tab2."".$valRight['account_name2']."</div></td>
												<td style=\"width: 25%\"><div style=\"font-weight:".$report_bold2."\"></div></td>
											</tr>";
									} else {
										$tblitem_right2 = "";
									}									

									if($valRight['report_type2']	== 3){
										$last_balance2 = $this->AcctConsolidationReport_model->getLastBalance($valRight['account_id2'], $sesi['consolidation_report'], $sesi['branch_id']);

										$tblitem_right3 = "
											<tr>
												<td><div style=\"font-weight:".$report_bold2."\">".$report_tab2."(".$valRight['account_code2'].") ".$valRight['account_name2']."</div> </td>
												<td style=\"text-align:right;\">".number_format($last_balance2, 2)."</td>
											</tr>";

										$account_amount2_bottom[$valRight['report_no']] = $last_balance2;
									} else {
										$tblitem_right3 = "";
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


									if($valRight['report_type2'] == 7){
										if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
											$report_formula2 	= explode('#', $valRight['report_formula2']);
											$report_operator2 	= explode('#', $valRight['report_operator2']);

											$total_account_amount2_bottom	= 0;
											for($i = 0; $i < count($report_formula2); $i++){
												if($report_operator2[$i] == '-'){
													if($total_account_amount2_bottom == 0 ){
														$total_account_amount2_bottom = $total_account_amount2_bottom + $account_amount2_bottom[$report_formula2[$i]];
													} else {
														$total_account_amount2_bottom = $total_account_amount2_bottom - $account_amount2_bottom[$report_formula2[$i]];
													}
												} else if($report_operator2[$i] == '+'){
													if($total_account_amount2_bottom == 0){
														$total_account_amount2_bottom = $total_account_amount2_bottom + $account_amount2_bottom[$report_formula2[$i]];
													} else {
														$total_account_amount2_bottom = $total_account_amount2_bottom + $account_amount2_bottom[$report_formula2[$i]];
													}
												}
											}
										}	

										if(!empty($valRight['report_formula3']) && !empty($valRight['report_operator3'])){
											$report_formula3 	= explode('#', $valRight['report_formula3']);
											$report_operator3 	= explode('#', $valRight['report_operator3']);

											$total_account_amount1_top	= 0;
											for($i = 0; $i < count($report_formula3); $i++){
												if($report_operator3[$i] == '-'){
													if($total_account_amount1_top == 0 ){
														$total_account_amount1_top = $total_account_amount1_top + $account_amount1_top[$report_formula3[$i]];
													} else {
														$total_account_amount1_top = $total_account_amount1_top - $account_amount1_top[$report_formula3[$i]];
													}
												} else if($report_operator3[$i] == '+'){
													if($total_account_amount1_top == 0){
														$total_account_amount1_top = $total_account_amount1_top + $account_amount1_top[$report_formula3[$i]];
													} else {
														$total_account_amount1_top = $total_account_amount1_top + $account_amount1_top[$report_formula3[$i]];
													}
												}
											}
										}

										$total_account_amount3 = $total_account_amount1_top - $total_account_amount2_bottom;

										
										$tblitem_right7 = "
											<tr>
												<td><div style=\"font-weight:".$report_bold2."\">".$report_tab2."(".$valRight['account_code2'].") ".$valRight['account_name2']."</div> </td>
												<td style=\"text-align:right;\">".number_format($total_account_amount3, 2)."</td>
											</tr>
											";
										
									} else {
										$tblitem_right7 = "";
									}

									$tblitem_right .= $tblitem_right1.$tblitem_right2.$tblitem_right3.$tblitem_right7.$tblitem_right5;


									if($valRight['report_type2'] == 6){
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

											$total_account_amount2 += $total_account_amount3;
										} else {
											
										}
									} else {
										
									}
								}

			       	$tblfooter_right = "
			       			</table>
			        	</td>";

			$tblFooter = "
			    </tr>
			    <tr>
			    	<td style=\"width: 50%\">
			    		<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">
			    			<tr>
								<td style=\"width: 70%\"><div style=\"font-weight:".$report_bold1.";font-size:14px\">".$report_tab1."".$valLeft['account_name1']."</div></td>
								<td style=\"width: 28%; text-align:right;\"><div style=\"font-weight:".$report_bold1."; font-size:14px\">".number_format($total_account_amount1, 2)."</div></td>
							</tr>
			    		</table>
			    	</td>
			    	<td style=\"width: 50%\">
			    		<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">
			    			<tr>
								<td style=\"width: 70%\"><div style=\"font-weight:".$report_bold2.";font-size:14px\">".$report_tab2."".$valRight['account_name2']."</div></td>
								<td style=\"width: 28%; text-align:right;\"><div style=\"font-weight:".$report_bold2."; font-size:14px\">".number_format($total_account_amount2, 2)."</div></td>
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

		public function exportAcctBalanceSheet(){
			$auth 	= $this->session->userdata('auth');
			$unique = $this->session->userdata('unique');

			$sesi = $this->session->userdata('filter-AcctBalanceSheetConsolidationReport');
			if(!is_array($sesi)){
				$sesi['consolidation_report']	= 1;
				$sesi['branch_id']				= $auth['branch_id'];
			}

			if($sesi['consolidation_report'] == 0 || $sesi['consolidation_report'] == 9){
				$branch_name = $this->AcctConsolidationReport_model->getBranchName($sesi['branch_id']);
			} else {
				$branch_name = 'GABUNGAN';
			}

			$preferencecompany 				= $this->AcctConsolidationReport_model->getPreferenceCompany();

			$acctbalancesheetreport_left	= $this->AcctConsolidationReport_model->getAcctBalanceSheetReport_Left();

			$acctbalancesheetreport_right	= $this->AcctConsolidationReport_model->getAcctBalanceSheetReport_Right();

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
				$this->excel->getActiveSheet()->setCellValue('B1',"Laporan Neraca ".$branch_name);	
				$this->excel->getActiveSheet()->setCellValue('B2',$preferencecompany['company_name']);	
				$this->excel->getActiveSheet()->setCellValue('B3',"Periode ".$period."");	
				
				$j = 5;
				$no = 0;
				$grand_total = 0;
				
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
							$last_balance1 = $this->AcctConsolidationReport_model->getLastBalance($valLeft['account_id1'], $sesi['consolidation_report'], $sesi['branch_id']);		

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
								$this->excel->getActiveSheet()->setCellValue('C'.$j, $report_tab1.$total_account_amount1);

								
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
							$last_balance2 = $this->AcctConsolidationReport_model->getLastBalance($valRight['account_id2'], $sesi['consolidation_report'], $sesi['branch_id']);		

							if (empty($last_balance2)){
								$last_balance2 = 0;
							}

							$this->excel->getActiveSheet()->setCellValue('D'.$j, $report_tab2.$valRight['account_name2']);
							$this->excel->getActiveSheet()->setCellValue('E'.$j, $report_tab2.$last_balance2);

							$account_amount2_bottom[$valRight['report_no']] = $last_balance2;

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

								
							} else {
								
							}
						} else {
							
						}

						if($valRight['report_type2'] == 7){
							if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
								$report_formula2 	= explode('#', $valRight['report_formula2']);
								$report_operator2 	= explode('#', $valRight['report_operator2']);

								$total_account_amount2_bottom	= 0;
								for($i = 0; $i < count($report_formula2); $i++){
									if($report_operator2[$i] == '-'){
										if($total_account_amount2_bottom == 0 ){
											$total_account_amount2_bottom = $total_account_amount2_bottom + $account_amount2_bottom[$report_formula2[$i]];
										} else {
											$total_account_amount2_bottom = $total_account_amount2_bottom - $account_amount2_bottom[$report_formula2[$i]];
										}
									} else if($report_operator2[$i] == '+'){
										if($total_account_amount2_bottom == 0){
											$total_account_amount2_bottom = $total_account_amount2_bottom + $account_amount2_bottom[$report_formula2[$i]];
										} else {
											$total_account_amount2_bottom = $total_account_amount2_bottom + $account_amount2_bottom[$report_formula2[$i]];
										}
									}
								}
								
							} else {
								
							}


							if(!empty($valRight['report_formula3']) && !empty($valRight['report_operator3'])){
								$report_formula3 	= explode('#', $valRight['report_formula3']);
								$report_operator3 	= explode('#', $valRight['report_operator3']);

								$total_account_amount1_top	= 0;
								for($i = 0; $i < count($report_formula3); $i++){
									if($report_operator3[$i] == '-'){
										if($total_account_amount1_top == 0 ){
											$total_account_amount1_top = $total_account_amount1_top + $account_amount1_top[$report_formula3[$i]];
										} else {
											$total_account_amount1_top = $total_account_amount1_top - $account_amount1_top[$report_formula3[$i]];
										}
									} else if($report_operator3[$i] == '+'){
										if($total_account_amount1_top == 0){
											$total_account_amount1_top = $total_account_amount1_top + $account_amount1_top[$report_formula3[$i]];
										} else {
											$total_account_amount1_top = $total_account_amount1_top + $account_amount1_top[$report_formula3[$i]];
										}
									}
								}
								
							} else {
								
							}

							$total_account_amount3 = $total_account_amount1_top - $total_account_amount2_bottom;

							$this->excel->getActiveSheet()->setCellValue('D'.$j, $report_tab2.$valRight['account_name2']);
							$this->excel->getActiveSheet()->setCellValue('E'.$j, $report_tab2.$total_account_amount3);
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
								
								$grand_total_account_amount2 += $total_account_amount3;
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

		public function AcctProfitLoss(){
			$auth 	= $this->session->userdata('auth');

			$sesi = $this->session->userdata('filter-AcctProfitLossConsolidationReport');
			if(!is_array($sesi)){
				$sesi['month_period'] 			= date('m');
				$sesi['year_period']			= date('Y');
				$sesi['consolidation_report']	= 1;
				$sesi['branch_id']				= $auth['branch_id'];
			}
			

			$preferencecompany				= $this->AcctConsolidationReport_model->getPreferenceCompany();
			$branch_name 					= $this->AcctConsolidationReport_model->getBranchName($preferencecompany['central_branch_id']);

			$corebranch = array (
					'branch_id'			=> $preferencecompany['central_branch_id'],
					'branch_name'		=> $branch_name,
				);

			$data['main_view']['consolidation']					= $this->configuration->ConsolidationReport();

			$data['main_view']['corebranch']					= $corebranch;

			$data['main_view']['acctprofitlossreport_top']		= $this->AcctConsolidationReport_model->getAcctProfitLossReport_Top();

			$data['main_view']['acctprofitlossreport_bottom']	= $this->AcctConsolidationReport_model->getAcctProfitLossReport_Bottom();

			$data['main_view']['monthlist']						= $this->configuration->Month();

			$data['main_view']['profitlossreporttype']			= $this->configuration->ProfitLossReportType();

			$data['main_view']['content']						= 'AcctConsolidationReport/AcctProfitLossConsolidationReport_view';

			$this->load->view('MainPage_view',$data);
		}

		public function filterAcctProfitLoss(){
			$data = array (
				"month_period" 				=> date('m'),
				"year_period" 				=> date('Y'),
				"consolidation_report" 		=> $this->input->post('consolidation_report',true),
				"branch_id"					=> $this->input->post('branch_id',true),
			);

			$this->session->set_userdata('filter-AcctProfitLossConsolidationReport',$data);
			redirect('AcctConsolidationReport/AcctProfitLoss');
		}

		public function processPrintingAcctProfitLoss(){
			$auth 	= $this->session->userdata('auth');

			$data = $this->session->userdata('filter-AcctProfitLossConsolidationReport');
			if(!is_array($data)){
				$data['month_period'] 			= date('m');
				$data['year_period']			= date('Y');
				$data['consolidation_report']	= 1;
				$data['branch_id']				= $auth['branch_id'];
			}

			if($data['consolidation_report'] == 0 || $data['consolidation_report'] == 9){
				$branch_name = $this->AcctConsolidationReport_model->getBranchName($data['branch_id']);
			} else {
				$branch_name = 'GABUNGAN';
			}

			$preference_company 			= $this->AcctConsolidationReport_model->getPreferenceCompany();

			$acctprofitlossreport_top		= $this->AcctConsolidationReport_model->getAcctProfitLossReport_Top();

			$acctprofitlossreport_bottom	= $this->AcctConsolidationReport_model->getAcctProfitLossReport_Bottom();

			// print_r($data); exit;

			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new TCPDF(P, PDF_UNIT, 'A4', true, 'UTF-8', false);
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

			$data['month_period'] 	= date("m");
			$data['year_period']  	= date("Y");

			$day 	= date("d");
			$month 	= date("m");
			$year 	= date("Y");

			switch ($data['month_period']) {
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

			// if ($data['consolidation_report'] == 1){
			// 	$period = $month_name." ".$data['year_period'];
			// } else {
			// 	$period = $data['year_period'];
			// }

			$period = $day." ".$month_name." ".$year;

			/*print_r($preference_company);*/
			$tbl = "
				<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\">
				    <tr>
				        <td colspan=\"5\"><div style=\"text-align: center; font-size:14px\">LAPORAN PERHITUNGAN SHU ".$branch_name."<br> ".$preference_company['company_name']."<br> Periode ".$period."</div></td>
				    </tr>
				</table>
			";

			$pdf->writeHTML($tbl, true, false, false, false, '');



			$minus_month= mktime(0, 0, 0, date($data['month_period'])-1);
			$month = date('m', $minus_month);

			if($month == 12){
				$year = $data['year_period'] - 1;
			} else {
				$year = $data['year_period'];
			}

			$tblHeader = "
			<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">";
		        $tblheader_top = "
		        	<tr>
		        		<td width=\"10%\"></td>
		        		<td width=\"80%\" style=\"border-top:1px black solid;border-left:1px black solid;border-right:1px black solid\">	
			        		
		        			<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">";	
			        			$tblitem_top = "";
			        			foreach ($acctprofitlossreport_top as $keyTop => $valTop) {
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
										$tblitem_top1 = "
											<tr>
												<td colspan=\"2\" style=\"width: 100%\"><div style=\"font-weight:".$report_bold."\">".$report_tab."".$valTop['account_name']."</div></td>
											</tr>";
									} else {
										$tblitem_top1 = "";
									}


									if($valTop['report_type']	== 2){

										$tblitem_top2 = "
											<tr>
												<td style=\"width: 73%\"><div style=\"font-weight:".$report_bold."\">".$report_tab."".$valTop['account_name']."</div></td>
												<td style=\"width: 25%\"><div style=\"font-weight:".$report_bold."\"></div></td>
											</tr>";
									} else {
										$tblitem_top2 = "";
									}									

									if($valTop['report_type']	== 3){
										$accountamount 		= $this->AcctConsolidationReport_model->getAccountAmount($valTop['account_id'], $data['month_period'], $data['year_period'], $data['consolidation_report'], $data['branch_id']);

										$account_subtotal 	= ABS(($accountamount['account_in_amount'] - $accountamount['account_out_amount']));

										$tblitem_top3 = "
											<tr>
												<td style=\"width: 73%\"><div style=\"font-weight:".$report_bold."\">".$report_tab."(".$valTop['account_code'].") ".$valTop['account_name']."</div> </td>
												<td style=\"text-align:right;width: 25%\">".number_format($account_subtotal, 2)."</td>
											</tr>";

										$account_amount[$valTop['report_no']] = $account_subtotal;

									} else {
										$tblitem_top3 = "";
									}
									

									if($valTop['report_type'] == 5){
										if(!empty($valTop['report_formula']) && !empty($valTop['report_operator'])){
											$report_formula 	= explode('#', $valTop['report_formula']);
											$report_operator 	= explode('#', $valTop['report_operator']);

											$total_account_amount	= 0;
											for($i = 0; $i < count($report_formula); $i++){
												if($report_operator[$i] == '-'){
													if($total_account_amount == 0 ){
														$total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
													} else {
														$total_account_amount = $total_account_amount - $account_amount[$report_formula[$i]];
													}
												} else if($report_operator[$i] == '+'){
													if($total_account_amount == 0){
														$total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
													} else {
														$total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
													}
												}
											}
											$tblitem_top5 = "
												<tr>
													<td><div style=\"font-weight:".$report_bold."\">".$report_tab."".$valTop['account_name']."</div></td>
													<td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold."\">".number_format($total_account_amount, 2)."</div></td>
												</tr>";
										} else {
											$tblitem_top5 = "";
										}
									} else {
										$tblitem_top5 = "";
									}

									$tblitem_top .= $tblitem_top1.$tblitem_top2.$tblitem_top3.$tblitem_top5;

									if($valTop['report_type'] == 6){
										if(!empty($valTop['report_formula']) && !empty($valTop['report_operator'])){
											$report_formula 	= explode('#', $valTop['report_formula']);
											$report_operator 	= explode('#', $valTop['report_operator']);

											$grand_total_account_amount1	= 0;
											for($i = 0; $i < count($report_formula); $i++){
												if($report_operator[$i] == '-'){
													if($grand_total_account_amount1 == 0 ){
														$grand_total_account_amount1 = $grand_total_account_amount1 + $account_amount[$report_formula[$i]];
													} else {
														$grand_total_account_amount1 = $grand_total_account_amount1 - $account_amount[$report_formula[$i]];
													}
												} else if($report_operator[$i] == '+'){
													if($grand_total_account_amount1 == 0){
														$grand_total_account_amount1 = $grand_total_account_amount1 + $account_amount[$report_formula[$i]];
													} else {
														$grand_total_account_amount1 = $grand_total_account_amount1 + $account_amount[$report_formula[$i]];
													}
												}
											}
											
										} else {
											
										}
									} else {
										
									}

								}

		        $tblfooter_top	= "
		        		</table>
		        	</td>
		        	<td width=\"10%\"></td>
		        </tr>";

			       /* print_r("tblitem_top ");
			        print_r($tblitem_top);
			        exit; */

				$tblheader_bottom = "
					<tr>
						<td width=\"10%\"></td>
			        	<td width=\"80%\" style=\"border-bottom:1px black solid;border-left:1px black solid;border-right:1px black solid\">	
			        		<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">";		
			        			$tblitem_bottom = "";
			        			foreach ($acctprofitlossreport_bottom as $keyBottom => $valBottom) {
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
										$tblitem_bottom1 = "
											<tr>
												<td colspan=\"2\" width=\"100%\"><div style=\"font-weight:".$report_bold."\">".$report_tab."".$valBottom['account_name']."</div></td>
											</tr>";
									} else {
										$tblitem_bottom1 = "";
									}



									if($valBottom['report_type'] == 2){
										$tblitem_bottom2 = "
											<tr>
												<td style=\"width: 73%\"><div style=\"font-weight:".$report_bold."\">".$report_tab."".$valBottom['account_name']."</div></td>
												<td style=\"width: 25%\"><div style=\"font-weight:".$report_bold."\"></div></td>
											</tr>";
									} else {
										$tblitem_bottom2 = "";
									}									

									if($valBottom['report_type']	== 3){
										$accountamount 		= $this->AcctConsolidationReport_model->getAccountAmount($valBottom['account_id'], $data['month_period'], $data['year_period'], $data['consolidation_report'], $data['branch_id']);

										$account_subtotal 	= ABS(($accountamount['account_in_amount'] - $accountamount['account_out_amount']));

										// print_r("account_subtotal ");
										// print_r($account_subtotal);
										// exit;

										$tblitem_bottom3 = "
											<tr>
												<td style=\"width: 73%\"><div style=\"font-weight:".$report_bold."\">".$report_tab."(".$valBottom['account_code'].") ".$valBottom['account_name']."</div> </td>
												<td style=\"text-align:right;width: 25%\">".number_format($account_subtotal, 2)."</td>
											</tr>";

										$account_amount[$valBottom['report_no']] = $account_subtotal;

									} else {
										$tblitem_bottom3 = "";
									}
									

									if($valBottom['report_type'] == 5){
										if(!empty($valBottom['report_formula']) && !empty($valBottom['report_operator'])){
											$report_formula 	= explode('#', $valBottom['report_formula']);
											$report_operator 	= explode('#', $valBottom['report_operator']);

											$total_account_amount2	= 0;
											for($i = 0; $i < count($report_formula); $i++){
												if($report_operator[$i] == '-'){
													if($total_account_amount == 0 ){
														$total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
													} else {
														$total_account_amount = $total_account_amount - $account_amount[$report_formula[$i]];
													}
												} else if($report_operator[$i] == '+'){
													if($total_account_amount == 0){
														$total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
													} else {
														$total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
													}
												}
											}
											$tblitem_bottom5 = "
												<tr>
													<td><div style=\"font-weight:".$report_bold."\">".$report_tab."".$valBottom['account_name']."</div></td>
													<td style=\"text-align:righr;\"><div style=\"font-weight:".$report_bold."\">".number_format($total_account_amount, 2)."</div></td>
												</tr>";
										} else {
											$tblitem_bottom5 = "";
										}
									} else {
										$tblitem_bottom5 = "";
									}

									$tblitem_bottom .= $tblitem_bottom1.$tblitem_bottom2.$tblitem_bottom3.$tblitem_bottom5;


									if($valBottom['report_type'] == 6){
										if(!empty($valBottom['report_formula']) && !empty($valBottom['report_operator'])){
											$report_formula 	= explode('#', $valBottom['report_formula']);
											$report_operator 	= explode('#', $valBottom['report_operator']);

											$grand_total_account_amount2	= 0;
											for($i = 0; $i < count($report_formula); $i++){
												if($report_operator[$i] == '-'){
													if($grand_total_account_amount2 == 0 ){
														$grand_total_account_amount2 = $grand_total_account_amount2 + $account_amount[$report_formula[$i]];
													} else {
														$grand_total_account_amount2 = $grand_total_account_amount2 - $account_amount[$report_formula[$i]];
													}
												} else if($report_operator[$i] == '+'){
													if($grand_total_account_amount2 == 0){
														$grand_total_account_amount2 = $grand_total_account_amount2 + $account_amount[$report_formula[$i]];
													} else {
														$grand_total_account_amount2 = $grand_total_account_amount2 + $account_amount[$report_formula[$i]];
													}
												}
											}
										} else {
											
										}
									} else {
										
									}

								}
								// exit;

		       	$tblfooter_bottom = "
		       			</table>
		        	</td>
		        	<td width=\"10%\"></td>
		        </tr>";


			        $shu = $grand_total_account_amount1 - $grand_total_account_amount2;

			$tblFooter = "
			   
			    <tr>
			    	<td width=\"10%\"></td>
			    	<td style=\"border:1px black solid;\">
			    		<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">
			    			<tr>
								<td style=\"width: 75%\"><div style=\"font-weight:bold;font-size:16px\">SHU</div></td>
								<td style=\"width: 23%; text-align:right;\"><div style=\"font-weight:bold; font-size:16px\">".number_format($shu, 2)."</div></td>
							</tr>
			    		</table>
			    	</td>
			    	<td width=\"10%\"></td>
			    </tr>
			</table>";
			    
			$table = $tblHeader.$tblheader_top.$tblitem_top.$tblfooter_top.$tblheader_bottom.$tblitem_bottom.$tblfooter_bottom.$tblFooter;
				/*print_r("table ");
				print_r($table);
				exit;*/

			$pdf->writeHTML($table, true, false, false, false, '');

			
			
			
			//Close and output PDF document
			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Laporan Rugi Laba.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}


		public function exportAcctProfitLossReport(){
			$auth = $this->session->userdata('auth');
			$data = $this->session->userdata('filter-AcctProfitLossConsolidationReport');
			if(!is_array($data)){
				$data['month_period'] 			= date('m');
				$data['year_period']			= date('Y');
				$data['consolidation_report']	= 1;
				$data['branch_id']				= $auth['branch_id'];
			}

			if($data['consolidation_report'] == 0 || $data['consolidation_report'] == 9){
				$branch_name = $this->AcctConsolidationReport_model->getBranchName($data['branch_id']);
			} else {
				$branch_name = 'GABUNGAN';
			}

			$preference_company 			= $this->AcctConsolidationReport_model->getPreferenceCompany();

			$acctprofitlossreport_top		= $this->AcctConsolidationReport_model->getAcctProfitLossReport_Top();

			$acctprofitlossreport_bottom	= $this->AcctConsolidationReport_model->getAcctProfitLossReport_Bottom();

			switch ($data['month_period']) {
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

			if ($data['consolidation_report'] == 1){
				$period = $month_name." ".$data['year_period'];
			} else {
				$period = $data['year_period'];
			}
			
			if(!empty($acctprofitlossreport_top && $acctprofitlossreport_bottom)){
				$this->load->library('excel');
				
				$this->excel->getProperties()->setCreator("SIS Integrated System")
									 ->setLastModifiedBy("SIS Integrated System")
									 ->setTitle("Laporan Perhitungan SHU")
									 ->setSubject("")
									 ->setDescription("Laporan Perhitungan SHU")
									 ->setKeywords("SHU, Perhitungan, Laporan")
									 ->setCategory("Laporan Perhitungan SHU");
									 
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
				$this->excel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				
				$this->excel->getActiveSheet()->mergeCells("B1:C1");
				$this->excel->getActiveSheet()->mergeCells("B2:C2");
				$this->excel->getActiveSheet()->mergeCells("B3:C3");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true)->setSize(12);

				$this->excel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true)->setSize(12);

			
				
				$this->excel->getActiveSheet()->setCellValue('B1',"Laporan Perhitungan SHU ".$branch_name);	
				$this->excel->getActiveSheet()->setCellValue('B2',$preference_company['company_name']);	
				$this->excel->getActiveSheet()->setCellValue('B3',"Periode ".$period);	
				
				$j = 5;
				$no = 0;
				$grand_total = 0;
				
				foreach($acctprofitlossreport_top as $keyTop => $valTop){
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
							$accountamount 		= $this->AcctConsolidationReport_model->getAccountAmount($valTop['account_id'], $data['month_period'], $data['year_period'], $data['consolidation_report'], $data['branch_id']);

							$account_subtotal 	= ABS(($accountamount['account_in_amount'] - $accountamount['account_out_amount']));

							$this->excel->getActiveSheet()->setCellValue('B'.$j, $report_tab.$valTop['account_name']);
							$this->excel->getActiveSheet()->setCellValue('C'.$j, $report_tab.$account_subtotal);

							$account_amount[$valTop['report_no']] = $account_subtotal;
						}


						if($valTop['report_type'] == 5){
							if(!empty($valTop['report_formula']) && !empty($valTop['report_operator'])){
								$report_formula 	= explode('#', $valTop['report_formula']);
								$report_operator 	= explode('#', $valTop['report_operator']);

								$total_account_amount	= 0;
								for($i = 0; $i < count($report_formula); $i++){
									if($report_operator[$i] == '-'){
										if($value == 0 ){
											$total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
										} else {
											$total_account_amount = $total_account_amount - $account_amount[$report_formula[$i]];
										}
									} else if($report_operator[$i] == '+'){
										if($total_account_amount == 0){
											$total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
										} else {
											$total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
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

								$grand_total_account_amount1	= 0;
								for($i = 0; $i < count($report_formula); $i++){
									if($report_operator[$i] == '-'){
										if($value == 0 ){
											$grand_total_account_amount1 = $grand_total_account_amount1 + $account_amount[$report_formula[$i]];
										} else {
											$grand_total_account_amount1 = $grand_total_account_amount1 - $account_amount[$report_formula[$i]];
										}
									} else if($report_operator[$i] == '+'){
										if($grand_total_account_amount1 == 0){
											$grand_total_account_amount1 = $grand_total_account_amount1 + $account_amount[$report_formula[$i]];
										} else {
											$grand_total_account_amount1 = $grand_total_account_amount1 + $account_amount[$report_formula[$i]];
										}
									}
								}

								$this->excel->getActiveSheet()->setCellValue('B'.$j, $report_tab.$valTop['account_name']);
								$this->excel->getActiveSheet()->setCellValue('C'.$j, $report_tab.$grand_total_account_amount1);
							}

						}
								

					}else{
						continue;
					}

					$j++;
				}

				$j--;

				foreach($acctprofitlossreport_bottom as $keyBottom => $valBottom){
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
							$accountamount 		= $this->AcctConsolidationReport_model->getAccountAmount($valBottom['account_id'], $data['month_period'], $data['year_period'], $data['consolidation_report'], $data['branch_id']);

							$account_subtotal 	= ABS(($accountamount['account_in_amount'] - $accountamount['account_out_amount']));

							$this->excel->getActiveSheet()->setCellValue('B'.$j, $report_tab.$valBottom['account_name']);
							$this->excel->getActiveSheet()->setCellValue('C'.$j, $report_tab.$account_subtotal);

							$account_amount[$valBottom['report_no']] = $account_subtotal;
						}


						if($valBottom['report_type'] == 5){
							if(!empty($valBottom['report_formula']) && !empty($valBottom['report_operator'])){
								$report_formula 	= explode('#', $valBottom['report_formula']);
								$report_operator 	= explode('#', $valBottom['report_operator']);

								$total_account_amount	= 0;
								for($i = 0; $i < count($report_formula); $i++){
									if($report_operator[$i] == '-'){
										if($value == 0 ){
											$total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
										} else {
											$total_account_amount = $total_account_amount - $account_amount[$report_formula[$i]];
										}
									} else if($report_operator[$i] == '+'){
										if($total_account_amount == 0){
											$total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
										} else {
											$total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
										}
									}
								}

								$this->excel->getActiveSheet()->setCellValue('B'.$j, $report_tab.$valBottom['account_name']);
								$this->excel->getActiveSheet()->setCellValue('C'.$j, $report_tab.$total_account_amount);
							}
						}

						if($valBottom['report_type'] == 6){
							if(!empty($valBottom['report_formula']) && !empty($valBottom['report_operator'])){
								$report_formula 	= explode('#', $valBottom['report_formula']);
								$report_operator 	= explode('#', $valBottom['report_operator']);

								$grand_total_account_amount2	= 0;
								for($i = 0; $i < count($report_formula); $i++){
									if($report_operator[$i] == '-'){
										if($value == 0 ){
											$grand_total_account_amount2 = $grand_total_account_amount2 + $account_amount[$report_formula[$i]];
										} else {
											$grand_total_account_amount2 = $grand_total_account_amount2 - $account_amount[$report_formula[$i]];
										}
									} else if($report_operator[$i] == '+'){
										if($grand_total_account_amount2 == 0){
											$grand_total_account_amount2 = $grand_total_account_amount2 + $account_amount[$report_formula[$i]];
										} else {
											$grand_total_account_amount2 = $grand_total_account_amount2 + $account_amount[$report_formula[$i]];
										}
									}
								}

								$this->excel->getActiveSheet()->setCellValue('B'.$j, $report_tab.$valBottom['account_name']);
								$this->excel->getActiveSheet()->setCellValue('C'.$j, $report_tab.$grand_total_account_amount2);
							}

						}
								

					}else{
						continue;
					}

					$j++;
				}

				$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

				$this->excel->getActiveSheet()->getStyle('B'.$j.':C'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

				$this->excel->getActiveSheet()->getStyle("B".$j.":C".$j)->getFont()->setBold(true);	

				$shu = $grand_total_account_amount1 - $grand_total_account_amount2;

				$this->excel->getActiveSheet()->setCellValue('B'.$j, "SHU");
				$this->excel->getActiveSheet()->setCellValue('C'.$j, $shu);

				$filename='Laporan Rugi Laba '.$period.'.xls';
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