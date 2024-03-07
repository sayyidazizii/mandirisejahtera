<?php 
defined('BASEPATH') or exit('No direct script access allowed');
ob_start();?>
<?php
	ini_set('memory_limit', '512M');
	Class AcctBalanceSheetComparationReportNew extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctBalanceSheetComparationReportNew_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= 	$this->session->userdata('filter-AcctBalanceSheetComparationReportNew');

			$day 	= date("d");
			$month 	= date("m");
			$year 	= date("Y");

			if(!is_array($sesi)){
				$sesi['month_period']										= $month;
				$sesi['year_period']										= $year;
				$sesi['account_comparation_report_type']					= 1;
				$sesi['branch_id']											= $auth['branch_id'];
			}

			$data['main_view']['monthlist']									= $this->configuration->Month();

			$data['main_view']['corebranch']								= create_double($this->AcctBalanceSheetComparationReportNew_model->getCoreBranch(),'branch_id','branch_name');

			$data['main_view']['acctbalancesheetcomparationreport_left']	= $this->AcctBalanceSheetComparationReportNew_model->getAcctBalanceSheetComparationReportNew_Left();

			$data['main_view']['acctbalancesheetcomparationreport_right']	= $this->AcctBalanceSheetComparationReportNew_model->getAcctBalanceSheetComparationReportNew_Right();

			$data['main_view']['accountcomparationreporttype']				= $this->configuration->AccountComparationReportType();

			$data['main_view']['content']									= 'AcctBalanceSheetComparationReportNew/ListAcctBalanceSheetComparationReportNew_view';

			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"month_period" 						=> $this->input->post('month_period',true),
				"year_period" 						=> $this->input->post('year_period',true),
				'account_comparation_report_type'	=> $this->input->post('account_comparation_report_type',true),
				'branch_id'							=> $this->input->post('branch_id',true),
			);

			// print_r($data);exit;

			$this->session->set_userdata('filter-AcctBalanceSheetComparationReportNew',$data);
			redirect('balance-sheet-comparation');
		}

		public function processPrinting(){
			$preferencecompany 		= $this->AcctBalanceSheetComparationReportNew_model->getPreferenceCompany();

			$month 	= date("m");
			$year 	= date("Y");

			$auth 	= $this->session->userdata('auth');
			$sesi	= 	$this->session->userdata('filter-AcctBalanceSheetComparationReportNew');

			if(!is_array($sesi)){
				$sesi['month_period']						= $month;
				$sesi['year_period']						= $year;
				$sesi['account_comparation_report_type']	= 1;
				$sesi['branch_id']							= $auth['branch_id'];
			}

			$month_now 	= $sesi['month_period'];
			$year_now 	= $sesi['year_period'];

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

			$minus_month	= mktime(0, 0, 0, date($sesi['month_period']) - 1);
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

			if ($sesi['account_comparation_report_type'] == 1){
				$period_before 	= $month_before_name." ".$year_before;
				$period_now 	= $month_now_name." ".$year_now;
			} else {
				$year_before 	= $year_now - 1;
				$period_before	= $year_before;
				$period_now		= $year_now;
			}

			/*print_r("month_before ");
			print_r($month_before);
			print_r("<BR>");

			print_r("year_before ");
			print_r($year_before);
			print_r("<BR>");

			print_r("month_now ");
			print_r($month_now);
			print_r("<BR>");

			print_r("year_now ");
			print_r($year_now);
			print_r("<BR>");

			exit;*/

			$acctbalancesheetcomparationreport_left	= $this->AcctBalanceSheetComparationReportNew_model->getAcctBalanceSheetComparationReportNew_Left();

			$acctbalancesheetcomparationreport_right	= $this->AcctBalanceSheetComparationReportNew_model->getAcctBalanceSheetComparationReportNew_Right();

			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(6, 6, 6, 6); 
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

			$pdf->SetFont('helvetica', '', 8);

			// -----------------------------------------------------------------------------

			$tbl = "
				<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\">
				    <tr>
				        <td colspan=\"5\"><div style=\"text-align: center; font-size:14px\">LAPORAN KOMPARASI NERACA <BR> ".$preferencecompany['company_name']."</div></td>
				    </tr>
				</table>
			";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tblHeader = "
			<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"2\" border=\"1\">	
				<tr>
					<td colspan=\"2\" style=\"text-align:center;\">
						<div style=\"font-weight:bold\">PERIODE 
							".$period_before."
						</div>
					</td>

					<td colspan=\"2\" style=\"text-align:center;\">
						<div style=\"font-weight:bold\">PERIODE 
							".$period_now."
						</div>
					</td>
				</tr>		        
			    <tr>";
			        $tblheader_beforeleft = "
			        	<td style=\"width: 25%\">	
			        		<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"1\" border=\"0\">";
			        			$tblitem_beforeleft = "";
			        			foreach ($acctbalancesheetcomparationreport_left as $keyLeft => $valLeft) {
									if($valLeft['report_tab1'] == 0){
										$report_tab1 = '';
									} else if($valLeft['report_tab1'] == 1){
										$report_tab1 = '';
									} else if($valLeft['report_tab1'] == 2){
										$report_tab1 = '&nbsp;&nbsp;';
									} else if($valLeft['report_tab1'] == 3){
										$report_tab1 = '&nbsp;&nbsp;&nbsp;&nbsp;';
									}

									if($valLeft['report_bold1'] == 1){
										$report_bold1 = 'bold';
									} else {
										$report_bold1 = 'normal';
									}									

									if($valLeft['report_type1'] == 1){
										$tblitem_beforeleft1 = "
											<tr>
												<td colspan=\"2\" style=\"width: 100%\"><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div></td>
											</tr>";
									} else {
										$tblitem_beforeleft1 = "";
									}



									if($valLeft['report_type1']	== 2){
										$tblitem_beforeleft2 = "
											<tr>
												<td style=\"width: 63%\"><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div></td>
												<td style=\"width: 35%\"><div style=\"font-weight:".$report_bold1."\"></div></td>
											</tr>";
									} else {
										$tblitem_beforeleft2 = "";
									}									

									if($valLeft['report_type1']	== 3){
										$last_balance1 = $this->AcctBalanceSheetComparationReportNew_model->getLastBalance($valLeft['account_id1'], $month_before, $year_before, $sesi['account_comparation_report_type'], $sesi['branch_id']);	

										if(empty($last_balance1)){
											$last_balance1 = $this->AcctBalanceSheetComparationReportNew_model->getLastBalance($valLeft['account_id1'], $month_before, $year_before, $sesi['account_comparation_report_type'], $sesi['branch_id']);
										}	

										$tblitem_beforeleft3 = "
											<tr>
												<td><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div> </td>
												<td style=\"text-align:right;\">".number_format($last_balance1, 2)."</td>
											</tr>";

										$account_amount1_before[$valLeft['report_no']] = $last_balance1;

									} else {
										$tblitem_beforeleft3 = "";
									}								

									if($valLeft['report_type1']	== 10){
										$last_balance10_before = $this->AcctBalanceSheetComparationReportNew_model->getLastBalance($valLeft['account_id1'], $month_before, $year_before, $sesi['account_comparation_report_type'], $sesi['branch_id']);	

										if(empty($last_balance10_before)){
											$last_balance10_before = $this->AcctBalanceSheetComparationReportNew_model->getLastBalance($valLeft['account_id1'], $month_before, $year_before, $sesi['account_comparation_report_type'], $sesi['branch_id']);
										}	

										$account_amount10_before[$valLeft['report_no']] = $last_balance10_before;

									}
									

									if($valLeft['report_type1'] == 11){
										if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
											$report_formula1 	= explode('#', $valLeft['report_formula1']);
											$report_operator1 	= explode('#', $valLeft['report_operator1']);

											$total_account_amount10_before	= 0;
											for($i = 0; $i < count($report_formula1); $i++){
												if($report_operator1[$i] == '-'){
													if($total_account_amount10_before == 0 ){
														$total_account_amount10_before = $total_account_amount10_before + $account_amount10_before[$report_formula1[$i]];
													} else {
														$total_account_amount10_before = $total_account_amount10_before - $account_amount10_before[$report_formula1[$i]];
													}
												} else if($report_operator1[$i] == '+'){
													if($total_account_amount10_before == 0){
														$total_account_amount10_before = $total_account_amount10_before + $account_amount10_before[$report_formula1[$i]];
													} else {
														$total_account_amount10_before = $total_account_amount10_before + $account_amount10_before[$report_formula1[$i]];
													}
												}
											}
											$tblitem_beforeleft11 = "
												<tr>
													<td><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div></td>
													<td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold1."\">".number_format($total_account_amount10_before, 2)."</div></td>
												</tr>";
										} else {
											$tblitem_beforeleft11 = "";
										}
									} else {
										$tblitem_beforeleft11 = "";
									}
									

									if($valLeft['report_type1'] == 5){
										if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
											$report_formula1 	= explode('#', $valLeft['report_formula1']);
											$report_operator1 	= explode('#', $valLeft['report_operator1']);

											$total_account_amount1_before	= 0;
											for($i = 0; $i < count($report_formula1); $i++){
												if($report_operator1[$i] == '-'){
													if($total_account_amount1_before == 0 ){
														$total_account_amount1_before = $total_account_amount1_before + $account_amount1_before[$report_formula1[$i]];
													} else {
														$total_account_amount1_before = $total_account_amount1_before - $account_amount1_before[$report_formula1[$i]];
													}
												} else if($report_operator1[$i] == '+'){
													if($total_account_amount1_before == 0){
														$total_account_amount1_before = $total_account_amount1_before + $account_amount1_before[$report_formula1[$i]];
													} else {
														$total_account_amount1_before = $total_account_amount1_before + $account_amount1_before[$report_formula1[$i]];
													}
												}
											}
											$tblitem_beforeleft5 = "
												<tr>
													<td><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div></td>
													<td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold1."\">".number_format($total_account_amount1_before+$total_account_amount10_before, 2)."</div></td>
												</tr>";
										} else {
											$tblitem_beforeleft5 = "";
										}
									} else {
										$tblitem_beforeleft5 = "";
									}

									$tblitem_beforeleft .= $tblitem_beforeleft1.$tblitem_beforeleft2.$tblitem_beforeleft3.$tblitem_beforeleft11.$tblitem_beforeleft5;

									if($valLeft['report_type1'] == 6){
										if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
											$report_formula1 	= explode('#', $valLeft['report_formula1']);
											$report_operator1 	= explode('#', $valLeft['report_operator1']);

											$total_account_amount1_before_left	= 0;
											for($i = 0; $i < count($report_formula1); $i++){
												if($report_operator1[$i] == '-'){
													if($total_account_amount1_before_left == 0 ){
														$total_account_amount1_before_left = $total_account_amount1_before_left + $account_amount1_before[$report_formula1[$i]];
													} else {
														$total_account_amount1_before_left = $total_account_amount1_before_left - $account_amount1_before[$report_formula1[$i]];
													}
												} else if($report_operator1[$i] == '+'){
													if($total_account_amount1_before_left == 0){
														$total_account_amount1_before_left = $total_account_amount1_before_left + $account_amount1_before[$report_formula1[$i]];
													} else {
														$total_account_amount1_before_left = $total_account_amount1_before_left + $account_amount1_before[$report_formula1[$i]];
													}
												}
											}
											
										} else {
											
										}
									} else {
										
									}

								}

			        $tblfooter_beforeleft	= "
			        		</table>
			        	</td>";

			       /* print_r("tblitem_left ");
			        print_r($tblitem_left);
			        exit; */

			        $tblheader_beforeright = "
			        	<td style=\"width: 25%\">	
			        		<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"1\" border=\"0\">";		
			        			$tblitem_beforeright = "";
			        			foreach ($acctbalancesheetcomparationreport_right as $keyRight => $valRight) {
									if($valRight['report_tab2'] == 0){
										$report_tab2 = ' ';
									} else if($valRight['report_tab2'] == 1){
										$report_tab2 = '&nbsp;&nbsp;&nbsp;';
									} else if($valRight['report_tab2'] == 2){
										$report_tab2 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									} else if($valRight['report_tab2'] == 3){
										$report_tab2 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									}

									if($valRight['report_bold2'] == 1){
										$report_bold2 = 'bold';
									} else {
										$report_bold2 = 'normal';
									}									

									if($valRight['report_type2'] == 1){
										$tblitem_beforeright1 = "
											<tr>
												<td colspan=\"2\" width=\"100%\"><div style=\"font-weight:".$report_bold2."\">".$report_tab2."".$valRight['account_name2']."</div></td>
											</tr>";
									} else {
										$tblitem_beforeright1 = "";
									}



									if($valRight['report_type2'] == 2){
										$tblitem_beforeright2 = "
											<tr>
												<td style=\"width: 63%\"><div style=\"font-weight:".$report_bold2."\">".$report_tab2."".$valRight['account_name2']."</div></td>
												<td style=\"width: 35%\"><div style=\"font-weight:".$report_bold2."\"></div></td>
											</tr>";
									} else {
										$tblitem_beforeright2 = "";
									}									

									if($valRight['report_type2']	== 3){
										$last_balance2 = $this->AcctBalanceSheetComparationReportNew_model->getLastBalance($valRight['account_id2'], $month_before, $year_before, $sesi['account_comparation_report_type'], $sesi['branch_id']);

										if(empty($last_balance2)){
											$last_balance2 = $this->AcctBalanceSheetComparationReportNew_model->getLastBalance2($valRight['account_id1'], $month_before, $year_before, $sesi['account_comparation_report_type'], $sesi['branch_id']);
										}	

										$tblitem_beforeright3 = "
											<tr>
												<td><div style=\"font-weight:".$report_bold2."\">".$report_tab2."".$valRight['account_name2']."</div> </td>
												<td style=\"text-align:right;\">".number_format($last_balance2, 2)."</td>
											</tr>";

										$account_amount2_before[$valRight['report_no']] = $last_balance2;
									} else {
										$tblitem_beforeright3 = "";
									}
									

									if($valRight['report_type2'] == 5){
										if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
											$report_formula2 	= explode('#', $valRight['report_formula2']);
											$report_operator2 	= explode('#', $valRight['report_operator2']);

											$total_account_amount2	= 0;
											for($i = 0; $i < count($report_formula2); $i++){
												if($report_operator2[$i] == '-'){
													if($total_account_amount2 == 0 ){
														$total_account_amount2 = $total_account_amount2 + $account_amount2_before[$report_formula2[$i]];
													} else {
														$total_account_amount2 = $total_account_amount2 - $account_amount2_before[$report_formula2[$i]];
													}
												} else if($report_operator2[$i] == '+'){
													if($total_account_amount2 == 0){
														$total_account_amount2 = $total_account_amount2 + $account_amount2_before[$report_formula2[$i]];
													} else {
														$total_account_amount2 = $total_account_amount2 + $account_amount2_before[$report_formula2[$i]];
													}
												}
											}
											$tblitem_beforeright5 = "
												<tr>
													<td><div style=\"font-weight:".$report_bold2."\">".$report_tab2."".$valRight['account_name2']."</div></td>
													<td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold2."\">".number_format($total_account_amount2, 2)."</div></td>
												</tr>";
										} else {
											$tblitem_beforeright5 = "";
										}
									} else {
										$tblitem_beforeright5 = "";
									}

									if($valRight['report_type2'] == 7){
										if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
											$report_formula2 	= explode('#', $valRight['report_formula2']);
											$report_operator2 	= explode('#', $valRight['report_operator2']);

											$total_account_amount2_before	= 0;
											for($i = 0; $i < count($report_formula2); $i++){
												if($report_operator2[$i] == '-'){
													if($total_account_amount2_before == 0 ){
														$total_account_amount2_before = $total_account_amount2_before + $account_amount2_before[$report_formula2[$i]];
													} else {
														$total_account_amount2_before = $total_account_amount2_before - $account_amount2_before[$report_formula2[$i]];
													}
												} else if($report_operator2[$i] == '+'){
													if($total_account_amount2_before == 0){
														$total_account_amount2_before = $total_account_amount2_before + $account_amount2_before[$report_formula2[$i]];
													} else {
														$total_account_amount2_before = $total_account_amount2_before + $account_amount2_before[$report_formula2[$i]];
													}
												}
											}
										} 

										if(!empty($valRight['report_formula3']) && !empty($valRight['report_operator3'])){
											$report_formula3 	= explode('#', $valRight['report_formula3']);
											$report_operator3 	= explode('#', $valRight['report_operator3']);

											$total_account_amount1_before	= 0;
											for($i = 0; $i < count($report_formula3); $i++){
												if($report_operator3[$i] == '-'){
													if($total_account_amount1_before == 0 ){
														$total_account_amount1_before = $total_account_amount1_before + $account_amount1_before[$report_formula3[$i]];
													} else {
														$total_account_amount1_before = $total_account_amount1_before - $account_amount1_before[$report_formula3[$i]];
													}
												} else if($report_operator3[$i] == '+'){
													if($total_account_amount1_before == 0){
														$total_account_amount1_before = $total_account_amount1_before + $account_amount1_before[$report_formula3[$i]];
													} else {
														$total_account_amount1_before = $total_account_amount1_before + $account_amount1_before[$report_formula3[$i]];
													}
												}
											}
										} 

										$total_account_amount3_before = $total_account_amount1_before - $total_account_amount2_before;

										$tblitem_beforeright7 = "
											<tr>
												<td><div style=\"font-weight:".$report_bold2."\">".$report_tab2."".$valRight['account_name2']."</div></td>
												<td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold2."\">".number_format($total_account_amount3_before, 2)."</div></td>
											</tr>";
									} else {
										$tblitem_beforeright7 = "";
									}

									$tblitem_beforeright .= $tblitem_beforeright1.$tblitem_beforeright2.$tblitem_beforeright3.$tblitem_beforeright5.$tblitem_beforeright7;


									if($valRight['report_type2'] == 6){
										if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
											$report_formula2 	= explode('#', $valRight['report_formula2']);
											$report_operator2 	= explode('#', $valRight['report_operator2']);

											$total_account_amount2_before_right	= 0;
											for($i = 0; $i < count($report_formula2); $i++){
												if($report_operator2[$i] == '-'){
													if($total_account_amount2_before_right == 0 ){
														$total_account_amount2_before_right = $total_account_amount2_before_right + $account_amount2_before[$report_formula2[$i]];
													} else {
														$total_account_amount2_before_right = $total_account_amount2_before_right - $account_amount2_before[$report_formula2[$i]];
													}
												} else if($report_operator2[$i] == '+'){
													if($total_account_amount2_before_right == 0){
														$total_account_amount2_before_right = $total_account_amount2_before_right + $account_amount2_before[$report_formula2[$i]];
													} else {
														$total_account_amount2_before_right = $total_account_amount2_before_right + $account_amount2_before[$report_formula2[$i]];
													}
												}
											}

											$total_account_amount2_before_right += $total_account_amount3_before;
										} else {
											
										}
									} else {
										
									}

								}

			       	$tblfooter_beforeright = "
			       			</table>
			        	</td>";


			        /*NOW*/
			        $tblheader_nowleft = "
			        	<td style=\"width: 25%\">	
			        		<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"1\" border=\"0\">";
			        			$tblitem_nowleft = "";
			        			foreach ($acctbalancesheetcomparationreport_left as $keyLeft => $valLeft) {
									if($valLeft['report_tab1'] == 0){
										$report_tab1 = ' ';
									} else if($valLeft['report_tab1'] == 1){
										$report_tab1 = '&nbsp;&nbsp;&nbsp;';
									} else if($valLeft['report_tab1'] == 2){
										$report_tab1 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									} else if($valLeft['report_tab1'] == 3){
										$report_tab1 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									}

									if($valLeft['report_bold1'] == 1){
										$report_bold1 = 'bold';
									} else {
										$report_bold1 = 'normal';
									}									

									if($valLeft['report_type1'] == 1){
										$tblitem_nowleft1 = "
											<tr>
												<td colspan=\"2\" style=\"width: 100%\"><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div></td>
											</tr>";
									} else {
										$tblitem_nowleft1 = "";
									}



									if($valLeft['report_type1']	== 2){
										$tblitem_nowleft2 = "
											<tr>
												<td style=\"width: 63%\"><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div></td>
												<td style=\"width: 35%\"><div style=\"font-weight:".$report_bold1."\"></div></td>
											</tr>";
									} else {
										$tblitem_nowleft2 = "";
									}									

									if($valLeft['report_type1']	== 3){
										$last_balance1 = $this->AcctBalanceSheetComparationReportNew_model->getLastBalance($valLeft['account_id1'], $month_now, $year_now, $sesi['account_comparation_report_type'], $sesi['branch_id']);	

										if(empty($last_balance1)){
											$last_balance1 = $this->AcctBalanceSheetComparationReportNew_model->getLastBalance2($valLeft['account_id1'], $month_now, $year_now, $sesi['account_comparation_report_type'], $sesi['branch_id']);	
										}	

										$tblitem_nowleft3 = "
											<tr>
												<td><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div> </td>
												<td style=\"text-align:right;\">".number_format($last_balance1, 2)."</td>
											</tr>";

										$account_amount1_now[$valLeft['report_no']] = $last_balance1;

									} else {
										$tblitem_nowleft3 = "";
									}							

									if($valLeft['report_type1']	== 10){
										$last_balance10 = $this->AcctBalanceSheetComparationReportNew_model->getLastBalance($valLeft['account_id1'], $month_now, $year_now, $sesi['account_comparation_report_type'], $sesi['branch_id']);	

										if(empty($last_balance10)){
											$last_balance10 = $this->AcctBalanceSheetComparationReportNew_model->getLastBalance2($valLeft['account_id1'], $month_now, $year_now, $sesi['account_comparation_report_type'], $sesi['branch_id']);	
										}	

										$account_amount10_now[$valLeft['report_no']] = $last_balance10;

									}
									if($valLeft['report_type1'] == 11){
										if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
											$report_formula1 	= explode('#', $valLeft['report_formula1']);
											$report_operator1 	= explode('#', $valLeft['report_operator1']);

											$total_account_amount10	= 0;
											for($i = 0; $i < count($report_formula1); $i++){
												if($report_operator1[$i] == '-'){
													if($total_account_amount10 == 0 ){
														$total_account_amount10 = $total_account_amount10 + $account_amount10_now[$report_formula1[$i]];
													} else {
														$total_account_amount10 = $total_account_amount10 - $account_amount10_now[$report_formula1[$i]];
													}
												} else if($report_operator1[$i] == '+'){
													if($total_account_amount10 == 0){
														$total_account_amount10 = $total_account_amount10 + $account_amount10_now[$report_formula1[$i]];
													} else {
														$total_account_amount10 = $total_account_amount10 + $account_amount10_now[$report_formula1[$i]];
													}
												}
											}
											$tblitem_nowleft11 = "
												<tr>
													<td><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div></td>
													<td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold1."\">".number_format($total_account_amount10, 2)."</div></td>
												</tr>";
										} else {
											$tblitem_nowleft11 = "";
										}
									} else {
										$tblitem_nowleft11 = "";
									}
									

									if($valLeft['report_type1'] == 5){
										if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
											$report_formula1 	= explode('#', $valLeft['report_formula1']);
											$report_operator1 	= explode('#', $valLeft['report_operator1']);

											$total_account_amount1	= 0;
											for($i = 0; $i < count($report_formula1); $i++){
												if($report_operator1[$i] == '-'){
													if($total_account_amount1 == 0 ){
														$total_account_amount1 = $total_account_amount1 + $account_amount1_now[$report_formula1[$i]];
													} else {
														$total_account_amount1 = $total_account_amount1 - $account_amount1_now[$report_formula1[$i]];
													}
												} else if($report_operator1[$i] == '+'){
													if($total_account_amount1 == 0){
														$total_account_amount1 = $total_account_amount1 + $account_amount1_now[$report_formula1[$i]];
													} else {
														$total_account_amount1 = $total_account_amount1 + $account_amount1_now[$report_formula1[$i]];
													}
												}
											}
											$tblitem_nowleft5 = "
												<tr>
													<td><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div></td>
													<td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold1."\">".number_format($total_account_amount1+$total_account_amount10, 2)."</div></td>
												</tr>";
										} else {
											$tblitem_nowleft5 = "";
										}
									} else {
										$tblitem_nowleft5 = "";
									}

									$tblitem_nowleft .= $tblitem_nowleft1.$tblitem_nowleft2.$tblitem_nowleft3.$tblitem_nowleft11.$tblitem_nowleft5;

									if($valLeft['report_type1'] == 6){
										if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
											$report_formula1 	= explode('#', $valLeft['report_formula1']);
											$report_operator1 	= explode('#', $valLeft['report_operator1']);

											$total_account_amount1_now_left	= 0;
											for($i = 0; $i < count($report_formula1); $i++){
												if($report_operator1[$i] == '-'){
													if($total_account_amount1_now_left == 0 ){
														$total_account_amount1_now_left = $total_account_amount1_now_left + $account_amount1_now[$report_formula1[$i]];
													} else {
														$total_account_amount1_now_left = $total_account_amount1_now_left - $account_amount1_now[$report_formula1[$i]];
													}
												} else if($report_operator1[$i] == '+'){
													if($total_account_amount1_now_left == 0){
														$total_account_amount1_now_left = $total_account_amount1_now_left + $account_amount1_now[$report_formula1[$i]];
													} else {
														$total_account_amount1_now_left = $total_account_amount1_now_left + $account_amount1_now[$report_formula1[$i]];
													}
												}
											}
											
										} else {
											
										}
									} else {
										
									}

								}

			        $tblfooter_nowleft	= "
			        		</table>
			        	</td>";

			       /* print_r("tblitem_left ");
			        print_r($tblitem_left);
			        exit; */

			        $tblheader_nowright = "
			        	<td style=\"width: 25%\">	
			        		<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"1\" border=\"0\">";		
			        			$tblitem_nowright = "";
			        			foreach ($acctbalancesheetcomparationreport_right as $keyRight => $valRight) {
									if($valRight['report_tab2'] == 0){
										$report_tab2 = ' ';
									} else if($valRight['report_tab2'] == 1){
										$report_tab2 = '&nbsp;&nbsp;&nbsp;';
									} else if($valRight['report_tab2'] == 2){
										$report_tab2 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									} else if($valRight['report_tab2'] == 3){
										$report_tab2 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									}

									if($valRight['report_bold2'] == 1){
										$report_bold2 = 'bold';
									} else {
										$report_bold2 = 'normal';
									}									

									if($valRight['report_type2'] == 1){
										$tblitem_nowright1 = "
											<tr>
												<td colspan=\"2\" width=\"100%\"><div style=\"font-weight:".$report_bold2."\">".$report_tab2."".$valRight['account_name2']."</div></td>
											</tr>";
									} else {
										$tblitem_nowright1 = "";
									}



									if($valRight['report_type2'] == 2){
										$tblitem_nowright2 = "
											<tr>
												<td style=\"width: 63%\"><div style=\"font-weight:".$report_bold2."\">".$report_tab2."".$valRight['account_name2']."</div></td>
												<td style=\"width: 35%\"><div style=\"font-weight:".$report_bold2."\"></div></td>
											</tr>";
									} else {
										$tblitem_nowright2 = "";
									}									

									if($valRight['report_type2']	== 3){
										$last_balance2 = $this->AcctBalanceSheetComparationReportNew_model->getLastBalance($valRight['account_id2'], $month_now, $year_now, $sesi['account_comparation_report_type'], $sesi['branch_id']);

										if(empty($last_balance2)){
											$last_balance2 = $this->AcctBalanceSheetComparationReportNew_model->getLastBalance2($valRight['account_id2'], $month_now, $year_now, $sesi['account_comparation_report_type'], $sesi['branch_id']);
										}

										$tblitem_nowright3 = "
											<tr>
												<td><div style=\"font-weight:".$report_bold2."\">".$report_tab2."".$valRight['account_name2']."</div> </td>
												<td style=\"text-align:right;\">".number_format($last_balance2, 2)."</td>
											</tr>";

										$account_amount2_now[$valRight['report_no']] = $last_balance2;
									} else {
										$tblitem_nowright3 = "";
									}
									

									if($valRight['report_type2'] == 5){
										if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
											$report_formula2 	= explode('#', $valRight['report_formula2']);
											$report_operator2 	= explode('#', $valRight['report_operator2']);

											$total_account_amount2	= 0;
											for($i = 0; $i < count($report_formula2); $i++){
												if($report_operator2[$i] == '-'){
													if($total_account_amount2 == 0 ){
														$total_account_amount2 = $total_account_amount2 + $account_amount2_now[$report_formula2[$i]];
													} else {
														$total_account_amount2 = $total_account_amount2 - $account_amount2_now[$report_formula2[$i]];
													}
												} else if($report_operator2[$i] == '+'){
													if($total_account_amount2 == 0){
														$total_account_amount2 = $total_account_amount2 + $account_amount2_now[$report_formula2[$i]];
													} else {
														$total_account_amount2 = $total_account_amount2 + $account_amount2_now[$report_formula2[$i]];
													}
												}
											}
											$tblitem_nowright5 = "
												<tr>
													<td><div style=\"font-weight:".$report_bold2."\">".$report_tab2."".$valRight['account_name2']."</div></td>
													<td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold2."\">".number_format($total_account_amount2, 2)."</div></td>
												</tr>";
										} else {
											$tblitem_nowright5 = "";
										}
									} else {
										$tblitem_nowright5 = "";
									}


									if($valRight['report_type2'] == 7){
										if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
											$report_formula2 	= explode('#', $valRight['report_formula2']);
											$report_operator2 	= explode('#', $valRight['report_operator2']);

											$total_account_amount2_now	= 0;
											for($i = 0; $i < count($report_formula2); $i++){
												if($report_operator2[$i] == '-'){
													if($total_account_amount2_now == 0 ){
														$total_account_amount2_now = $total_account_amount2_now + $account_amount2_now[$report_formula2[$i]];
													} else {
														$total_account_amount2_now = $total_account_amount2_now - $account_amount2_now[$report_formula2[$i]];
													}
												} else if($report_operator2[$i] == '+'){
													if($total_account_amount2_now == 0){
														$total_account_amount2_now = $total_account_amount2_now + $account_amount2_now[$report_formula2[$i]];
													} else {
														$total_account_amount2_now = $total_account_amount2_now + $account_amount2_now[$report_formula2[$i]];
													}
												}
											}
										} 

										if(!empty($valRight['report_formula3']) && !empty($valRight['report_operator3'])){
											$report_formula3 	= explode('#', $valRight['report_formula3']);
											$report_operator3 	= explode('#', $valRight['report_operator3']);

											$total_account_amount1_now	= 0;
											for($i = 0; $i < count($report_formula3); $i++){
												if($report_operator3[$i] == '-'){
													if($total_account_amount1_now == 0 ){
														$total_account_amount1_now = $total_account_amount1_now + $account_amount1_now[$report_formula3[$i]];
													} else {
														$total_account_amount1_now = $total_account_amount1_now - $account_amount1_now[$report_formula3[$i]];
													}
												} else if($report_operator3[$i] == '+'){
													if($total_account_amount1_now == 0){
														$total_account_amount1_now = $total_account_amount1_now + $account_amount1_now[$report_formula3[$i]];
													} else {
														$total_account_amount1_now = $total_account_amount1_now + $account_amount1_now[$report_formula3[$i]];
													}
												}
											}
										} 

										$total_account_amount3_now = $total_account_amount1_now - $total_account_amount2_now;

										$tblitem_nowright7 = "
											<tr>
												<td><div style=\"font-weight:".$report_bold2."\">".$report_tab2."".$valRight['account_name2']."</div></td>
												<td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold2."\">".number_format($total_account_amount3_now, 2)."</div></td>
											</tr>";
									} else {
										$tblitem_nowright7 = "";
									}


									$tblitem_nowright .= $tblitem_nowright1.$tblitem_nowright2.$tblitem_nowright3.$tblitem_nowright5.$tblitem_nowright7;


									if($valRight['report_type2'] == 6){
										if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
											$report_formula2 	= explode('#', $valRight['report_formula2']);
											$report_operator2 	= explode('#', $valRight['report_operator2']);

											$total_account_amount2_now_right	= 0;
											for($i = 0; $i < count($report_formula2); $i++){
												if($report_operator2[$i] == '-'){
													if($total_account_amount2_now_right == 0 ){
														$total_account_amount2_now_right = $total_account_amount2_now_right + $account_amount2_now[$report_formula2[$i]];
													} else {
														$total_account_amount2_now_right = $total_account_amount2_now_right - $account_amount2_now[$report_formula2[$i]];
													}
												} else if($report_operator2[$i] == '+'){
													if($total_account_amount2_now_right == 0){
														$total_account_amount2_now_right = $total_account_amount2_now_right + $account_amount2_now[$report_formula2[$i]];
													} else {
														$total_account_amount2_now_right = $total_account_amount2_now_right + $account_amount2_now[$report_formula2[$i]];
													}
												}
											}

											$total_account_amount2_now_right += $total_account_amount3_now;
										} else {
											
										}
									} else {
										
									}

								}

			       	$tblfooter_nowright = "
			       			</table>
			        	</td>";

			$tblFooter = "
			    </tr>
			    <tr>
			    	<td style=\"width: 25%\">
			    		<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">
			    			<tr>
								<td style=\"width: 55%\"><div style=\"font-weight:".$report_bold1.";font-size:12px\">".$report_tab1."".$valLeft['account_name1']."</div></td>
								<td style=\"width: 43%; text-align:right;\"><div style=\"font-weight:".$report_bold1."; font-size:12px\">".number_format($total_account_amount1_before+$total_account_amount10_before, 2)."</div></td>
							</tr>
			    		</table>
			    	</td>
			    	<td style=\"width: 25%\">
			    		<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">
			    			<tr>
								<td style=\"width: 55%\"><div style=\"font-weight:".$report_bold2.";font-size:12px\">".$report_tab2."".$valRight['account_name2']."</div></td>
								<td style=\"width: 43%; text-align:right;\"><div style=\"font-weight:".$report_bold2."; font-size:12px\">".number_format($total_account_amount2_before, 2)."</div></td>
							</tr>
			    		</table>
			    	</td>
			    	<td style=\"width: 25%\">
			    		<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">
			    			<tr>
								<td style=\"width: 55%\"><div style=\"font-weight:".$report_bold1.";font-size:12px\">".$report_tab1."".$valLeft['account_name1']."</div></td>
								<td style=\"width: 43%; text-align:right;\"><div style=\"font-weight:".$report_bold1."; font-size:12px\">".number_format($total_account_amount1+$total_account_amount10, 2)."</div></td>
							</tr>
			    		</table>
			    	</td>
			    	<td style=\"width: 25%\">
			    		<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">
			    			<tr>
								<td style=\"width: 55%\"><div style=\"font-weight:".$report_bold2.";font-size:12px\">".$report_tab2."".$valRight['account_name2']."</div></td>
								<td style=\"width: 43%; text-align:right;\"><div style=\"font-weight:".$report_bold2."; font-size:12px\">".number_format($total_account_amount2, 2)."</div></td>
							</tr>
			    		</table>
			    	</td>
			    </tr>
			</table>";
			    
			$table = $tblHeader.$tblheader_beforeleft.$tblitem_beforeleft.$tblfooter_beforeleft.$tblheader_beforeright.$tblitem_beforeright.$tblfooter_beforeright.$tblheader_nowleft.$tblitem_nowleft.$tblfooter_nowleft.$tblheader_nowright.$tblitem_nowright.$tblfooter_nowright.$tblFooter;
				/*print_r("table ");
				print_r($table);
				exit;*/

			$pdf->writeHTML($table, true, false, false, false, '');

			
			
			
			//Close and output PDF document
			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Laporan Komparasi Neraca.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function exportAcctBalanceSheetComparationReportNew(){
			$auth 	= $this->session->userdata('auth');
			$unique = $this->session->userdata('unique');

			$preferencecompany 		= $this->AcctBalanceSheetComparationReportNew_model->getPreferenceCompany();

			$sesi	= 	$this->session->userdata('filter-AcctBalanceSheetComparationReportNew');

			$month 	= date("m");
			$year 	= date("Y");

			if(!is_array($sesi)){
				$sesi['month_period']						= $month;
				$sesi['year_period']						= $year;
				$sesi['account_comparation_report_type']	= 1;
				$sesi['branch_id']							= $auth['branch_id'];
			}

			$month_now 	= $sesi['month_period'];
			$year_now 	= $sesi['year_period'];

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

			$minus_month	= mktime(0, 0, 0, date($sesi['month_period']) - 1);
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

			if ($sesi['account_comparation_report_type'] == 1){
				$period_before 	= $month_before_name." ".$year_before;
				$period_now 	= $month_now_name." ".$year_now;
			} else {
				$year_before 	= $year_now - 1;
				$period_before	= $year_before;
				$period_now		= $year_now;
			}

			$acctbalancesheetcomparationreport_left	= $this->AcctBalanceSheetComparationReportNew_model->getAcctBalanceSheetComparationReportNew_Left();

			$acctbalancesheetcomparationreport_right	= $this->AcctBalanceSheetComparationReportNew_model->getAcctBalanceSheetComparationReportNew_Right();
			
			if(!empty($acctbalancesheetcomparationreport_left && $acctbalancesheetcomparationreport_right)){
				$this->load->library('excel');
				
				$this->excel->getProperties()->setCreator("SIS Integrated System")
									 ->setLastModifiedBy("SIS Integrated System")
									 ->setTitle("Laporan Komparasi Neraca")
									 ->setSubject("")
									 ->setDescription("Laporan Komparasi Neraca")
									 ->setKeywords("Neraca, Laporan, SIS, Integrated, Komparasi")
									 ->setCategory("Laporan Komparasi Neraca");
									 
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
				$this->excel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(50);
				$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(50);
				$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
				
				$this->excel->getActiveSheet()->mergeCells("B1:I1");
				$this->excel->getActiveSheet()->mergeCells("B2:I2");
				$this->excel->getActiveSheet()->mergeCells("B3:E3");
				$this->excel->getActiveSheet()->mergeCells("F3:I3");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true)->setSize(12);

				$this->excel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true)->setSize(12);

				$this->excel->getActiveSheet()->getStyle('F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('F3')->getFont()->setBold(true)->setSize(12);

				$this->excel->getActiveSheet()->getStyle('B4:I4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

				$this->excel->getActiveSheet()->getStyle('B4:I4')->getFont()->setBold(true);	
				$this->excel->getActiveSheet()->setCellValue('B1',"Laporan Komparasi Neraca ");	
				$this->excel->getActiveSheet()->setCellValue('B2',$preferencecompany['company_name']);	
				$this->excel->getActiveSheet()->setCellValue('B3',"Periode ".$period_before);	
				$this->excel->getActiveSheet()->setCellValue('F3',"Periode ".$period_now);	
				
				$j = 5;
				$no = 0;
				$grand_total = 0;
				
				foreach($acctbalancesheetcomparationreport_left as $keyLeft =>$valLeft){
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
							$last_balance1 = $this->AcctBalanceSheetComparationReportNew_model->getLastBalance($valLeft['account_id1'], $month_before, $year_before, $sesi['account_comparation_report_type'], $sesi['branch_id']);		

							if (empty($last_balance1)){
								$last_balance1 = $this->AcctBalanceSheetComparationReportNew_model->getLastBalance($valLeft['account_id1'], $month_before, $year_before, $sesi['account_comparation_report_type'], $sesi['branch_id']);
							}

							$this->excel->getActiveSheet()->setCellValue("B".$j, $report_tab1.$valLeft['account_name1']);
							$this->excel->getActiveSheet()->setCellValue("C".$j, $report_tab1.$last_balance1);

							$account_amount1_before[$valLeft['report_no']] = $last_balance1;

						} else {

						}							

						if($valLeft['report_type1']	== 10){
							$last_balance10 = $this->AcctBalanceSheetComparationReportNew_model->getLastBalance($valLeft['account_id1'], $month_before, $year_before, $sesi['account_comparation_report_type'], $sesi['branch_id']);		

							if (empty($last_balance10)){
								$last_balance10 = $this->AcctBalanceSheetComparationReportNew_model->getLastBalance($valLeft['account_id1'], $month_before, $year_before, $sesi['account_comparation_report_type'], $sesi['branch_id']);
							}

							$account_amount10_before[$valLeft['report_no']] = $last_balance10;

						} else {

						}
						

						if($valLeft['report_type1'] == 11){
							if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
								$report_formula1 	= explode('#', $valLeft['report_formula1']);
								$report_operator1 	= explode('#', $valLeft['report_operator1']);

								$total_account_amount10_before	= 0;
								for($i = 0; $i < count($report_formula1); $i++){
									if($report_operator1[$i] == '-'){
										if($total_account_amount10_before == 0 ){
											$total_account_amount10_before = $total_account_amount10_before + $account_amount10_before[$report_formula1[$i]];
										} else {
											$total_account_amount10_before = $total_account_amount10_before - $account_amount10_before[$report_formula1[$i]];
										}
									} else if($report_operator1[$i] == '+'){
										if($total_account_amount10_before == 0){
											$total_account_amount10_before = $total_account_amount10_before + $account_amount10_before[$report_formula1[$i]];
										} else {
											$total_account_amount10_before = $total_account_amount10_before + $account_amount10_before[$report_formula1[$i]];
										}
									}
								}

								$this->excel->getActiveSheet()->setCellValue('B'.$j, $report_tab1.$valLeft['account_name1']);
								$this->excel->getActiveSheet()->setCellValue('C'.$j, $report_tab1.$total_account_amount10_before);

								
							} else {
								
							}
						} else {
							
						}
						

						if($valLeft['report_type1'] == 5){
							if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
								$report_formula1 	= explode('#', $valLeft['report_formula1']);
								$report_operator1 	= explode('#', $valLeft['report_operator1']);

								$total_account_amount1_before	= 0;
								for($i = 0; $i < count($report_formula1); $i++){
									if($report_operator1[$i] == '-'){
										if($total_account_amount1_before == 0 ){
											$total_account_amount1_before = $total_account_amount1_before + $account_amount1_before[$report_formula1[$i]];
										} else {
											$total_account_amount1_before = $total_account_amount1_before - $account_amount1_before[$report_formula1[$i]];
										}
									} else if($report_operator1[$i] == '+'){
										if($total_account_amount1_before == 0){
											$total_account_amount1_before = $total_account_amount1_before + $account_amount1_before[$report_formula1[$i]];
										} else {
											$total_account_amount1_before = $total_account_amount1_before + $account_amount1_before[$report_formula1[$i]];
										}
									}
								}

								$this->excel->getActiveSheet()->setCellValue('B'.$j, $report_tab1.$valLeft['account_name1']);
								$this->excel->getActiveSheet()->setCellValue('C'.$j, $report_tab1.$total_account_amount1_before+$total_account_amount10_before);

								
							} else {
								
							}
						} else {
							
						}

						if($valLeft['report_type1'] == 6){
							if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
								$report_formula1 	= explode('#', $valLeft['report_formula1']);
								$report_operator1 	= explode('#', $valLeft['report_operator1']);

								$grand_total_account_amount1_before	= 0;
								for($i = 0; $i < count($report_formula1); $i++){
									if($report_operator1[$i] == '-'){
										if($grand_total_account_amount1_before == 0 ){
											$grand_total_account_amount1_before = $grand_total_account_amount1_before + $account_amount1_before[$report_formula1[$i]];
										} else {
											$grand_total_account_amount1_before = $grand_total_account_amount1_before - $account_amount1_before[$report_formula1[$i]];
										}
									} else if($report_operator1[$i] == '+'){
										if($grand_total_account_amount1_before == 0){
											$grand_total_account_amount1_before = $grand_total_account_amount1_before + $account_amount1_before[$report_formula1[$i]];
										} else {
											$grand_total_account_amount1_before = $grand_total_account_amount1_before + $account_amount1_before[$report_formula1[$i]];
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

				$total_row_left_before = $j;

				$j = 5;
				$no = 0;
				$grand_total = 0;

				foreach($acctbalancesheetcomparationreport_right as $keyRight =>$valRight){
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
							$last_balance2 = $this->AcctBalanceSheetComparationReportNew_model->getLastBalance($valRight['account_id2'], $month_before, $year_before, $sesi['account_comparation_report_type'], $sesi['branch_id']);		

							if (empty($last_balance2)){
								$last_balance2 = $this->AcctBalanceSheetComparationReportNew_model->getLastBalance($valRight['account_id2'], $month_before, $year_before, $sesi['account_comparation_report_type'], $sesi['branch_id']);	
							}

							$this->excel->getActiveSheet()->setCellValue('D'.$j, $report_tab2.$valRight['account_name2']);
							$this->excel->getActiveSheet()->setCellValue('E'.$j, $report_tab2.$last_balance2);

							$account_amount2_before[$valRight['report_no']] = $last_balance2;

						} else {

						}
						

						if($valRight['report_type2'] == 5){
							if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
								$report_formula2 	= explode('#', $valRight['report_formula2']);
								$report_operator2 	= explode('#', $valRight['report_operator2']);

								$total_account_amount2_before	= 0;
								for($i = 0; $i < count($report_formula2); $i++){
									if($report_operator2[$i] == '-'){
										if($total_account_amount2_before == 0 ){
											$total_account_amount2_before = $total_account_amount2_before + $account_amount2_before[$report_formula2[$i]];
										} else {
											$total_account_amount2_before = $total_account_amount2_before - $account_amount2_before[$report_formula2[$i]];
										}
									} else if($report_operator2[$i] == '+'){
										if($total_account_amount2_before == 0){
											$total_account_amount2_before = $total_account_amount2_before + $account_amount2_before[$report_formula2[$i]];
										} else {
											$total_account_amount2_before = $total_account_amount2_before + $account_amount2_before[$report_formula2[$i]];
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

								$total_account_amount2_before	= 0;
								for($i = 0; $i < count($report_formula2); $i++){
									if($report_operator2[$i] == '-'){
										if($total_account_amount2_before == 0 ){
											$total_account_amount2_before = $total_account_amount2_before + $account_amount2_before[$report_formula2[$i]];
										} else {
											$total_account_amount2_before = $total_account_amount2_before - $account_amount2_before[$report_formula2[$i]];
										}
									} else if($report_operator2[$i] == '+'){
										if($total_account_amount2_before == 0){
											$total_account_amount2_before = $total_account_amount2_before + $account_amount2_before[$report_formula2[$i]];
										} else {
											$total_account_amount2_before = $total_account_amount2_before + $account_amount2_before[$report_formula2[$i]];
										}
									}
								}
							} 


							if(!empty($valRight['report_formula3']) && !empty($valRight['report_operator3'])){
								$report_formula3 	= explode('#', $valRight['report_formula3']);
								$report_operator3 	= explode('#', $valRight['report_operator3']);

								$total_account_amount1_before	= 0;
								for($i = 0; $i < count($report_formula3); $i++){
									if($report_operator3[$i] == '-'){
										if($total_account_amount1_before == 0 ){
											$total_account_amount1_before = $total_account_amount1_before + $account_amount1_before[$report_formula3[$i]];
										} else {
											$total_account_amount1_before = $total_account_amount1_before - $account_amount1_before[$report_formula3[$i]];
										}
									} else if($report_operator3[$i] == '+'){
										if($total_account_amount1_before == 0){
											$total_account_amount1_before = $total_account_amount1_before + $account_amount1_before[$report_formula3[$i]];
										} else {
											$total_account_amount1_before = $total_account_amount1_before + $account_amount1_before[$report_formula3[$i]];
										}
									}
								}
							} 

							$total_account_amount3_before = $total_account_amount1_before - $total_account_amount2_before;

							$this->excel->getActiveSheet()->setCellValue('D'.$j, $report_tab2.$valRight['account_name2']);
							$this->excel->getActiveSheet()->setCellValue('E'.$j, $report_tab2.$total_account_amount3_before);

						} else {
							
						}



						if($valRight['report_type2'] == 6){
							if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
								$report_formula2 	= explode('#', $valRight['report_formula2']);
								$report_operator2 	= explode('#', $valRight['report_operator2']);

								$grand_total_account_amount2_before	= 0;
								for($i = 0; $i < count($report_formula2); $i++){
									if($report_operator2[$i] == '-'){
										if($grand_total_account_amount2_before == 0 ){
											$grand_total_account_amount2_before = $grand_total_account_amount2_before + $account_amount2_before[$report_formula2[$i]];
										} else {
											$grand_total_account_amount2_before = $grand_total_account_amount2_before - $account_amount2_before[$report_formula2[$i]];
										}
									} else if($report_operator2[$i] == '+'){
										if($grand_total_account_amount2_before == 0){
											$grand_total_account_amount2_before = $grand_total_account_amount2_before + $account_amount2_before[$report_formula2[$i]];
										} else {
											$grand_total_account_amount2_before = $grand_total_account_amount2_before + $account_amount2_before[$report_formula2[$i]];
										}
									}
								}
								
								$grand_total_account_amount2_before += $total_account_amount3_before;
							} else {
								
							}
						} else {
							
						}	

					}else{
						continue;
					}

					$j++;
				}

				$total_row_right_before = $j;





				/*NOW*/

				$j = 5;
				$no = 0;
				$grand_total = 0;
				
				foreach($acctbalancesheetcomparationreport_left as $keyLeft =>$valLeft){
					if(is_numeric($keyLeft)){
						
						$this->excel->setActiveSheetIndex(0);
				
						$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					
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
							$this->excel->getActiveSheet()->getStyle('F'.$j)->getFont()->setBold(true);	
							$this->excel->getActiveSheet()->getStyle('G'.$j)->getFont()->setBold(true);	
						} else {
							
						}									

						if($valLeft['report_type1'] == 1){
							$this->excel->getActiveSheet()->mergeCells("F".$j.":G".$j."");
							$this->excel->getActiveSheet()->setCellValue('F'.$j, $valLeft['account_name1']);
						} else {

						}



						if($valLeft['report_type1']	== 2){
							$this->excel->getActiveSheet()->setCellValue('F'.$j, $report_tab1.$valLeft['account_name1']);
						} else {

						}									

						if($valLeft['report_type1']	== 3){
							$last_balance1 = $this->AcctBalanceSheetComparationReportNew_model->getLastBalance($valLeft['account_id1'], $month_now, $year_now, $sesi['account_comparation_report_type'], $sesi['branch_id']);		

							if (empty($last_balance1)){
								$last_balance1 = $this->AcctBalanceSheetComparationReportNew_model->getLastBalance2($valLeft['account_id1'], $month_now, $year_now, $sesi['account_comparation_report_type'], $sesi['branch_id']);	
							}

							$this->excel->getActiveSheet()->setCellValue('F'.$j, $report_tab1.$valLeft['account_name1']);
							$this->excel->getActiveSheet()->setCellValue('G'.$j, $report_tab1.$last_balance1);

							$account_amount1_now[$valLeft['report_no']] = $last_balance1;

						} else {

						}							

						if($valLeft['report_type1']	== 10){
							$last_balance10 = $this->AcctBalanceSheetComparationReportNew_model->getLastBalance($valLeft['account_id1'], $month_now, $year_now, $sesi['account_comparation_report_type'], $sesi['branch_id']);		

							if (empty($last_balance10)){
								$last_balance10 = $this->AcctBalanceSheetComparationReportNew_model->getLastBalance($valLeft['account_id1'], $month_now, $year_now, $sesi['account_comparation_report_type'], $sesi['branch_id']);	
							}

							$account_amount10_now[$valLeft['report_no']] = $last_balance10;

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
											$total_account_amount10 = $total_account_amount10 + $account_amount10_now[$report_formula1[$i]];
										} else {
											$total_account_amount10 = $total_account_amount10 - $account_amount10_now[$report_formula1[$i]];
										}
									} else if($report_operator1[$i] == '+'){
										if($total_account_amount10 == 0){
											$total_account_amount10 = $total_account_amount10 + $account_amount10_now[$report_formula1[$i]];
										} else {
											$total_account_amount10 = $total_account_amount10 + $account_amount10_now[$report_formula1[$i]];
										}
									}
								}

								$this->excel->getActiveSheet()->setCellValue('F'.$j, $report_tab1.$valLeft['account_name1']);
								$this->excel->getActiveSheet()->setCellValue('G'.$j, $report_tab1.$total_account_amount10);

								
							} else {
								
							}
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
											$total_account_amount1 = $total_account_amount1 + $account_amount1_now[$report_formula1[$i]];
										} else {
											$total_account_amount1 = $total_account_amount1 - $account_amount1_now[$report_formula1[$i]];
										}
									} else if($report_operator1[$i] == '+'){
										if($total_account_amount1 == 0){
											$total_account_amount1 = $total_account_amount1 + $account_amount1_now[$report_formula1[$i]];
										} else {
											$total_account_amount1 = $total_account_amount1 + $account_amount1_now[$report_formula1[$i]];
										}
									}
								}

								$this->excel->getActiveSheet()->setCellValue('F'.$j, $report_tab1.$valLeft['account_name1']);
								$this->excel->getActiveSheet()->setCellValue('G'.$j, $report_tab1.$total_account_amount1+$total_account_amount10);

								
							} else {
								
							}
						} else {
							
						}

						if($valLeft['report_type1'] == 6){
							if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
								$report_formula1 	= explode('#', $valLeft['report_formula1']);
								$report_operator1 	= explode('#', $valLeft['report_operator1']);

								$grand_total_account_amount1_now	= 0;
								for($i = 0; $i < count($report_formula1); $i++){
									if($report_operator1[$i] == '-'){
										if($grand_total_account_amount1_now == 0 ){
											$grand_total_account_amount1_now = $grand_total_account_amount1_now + $account_amount1_now[$report_formula1[$i]];
										} else {
											$grand_total_account_amount1_now = $grand_total_account_amount1_now - $account_amount1_now[$report_formula1[$i]];
										}
									} else if($report_operator1[$i] == '+'){
										if($grand_total_account_amount1_now == 0){
											$grand_total_account_amount1_now = $grand_total_account_amount1_now + $account_amount1_now[$report_formula1[$i]];
										} else {
											$grand_total_account_amount1_now = $grand_total_account_amount1_now + $account_amount1_now[$report_formula1[$i]];
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

				$total_row_left_now = $j;

				$j = 5;
				$no = 0;
				$grand_total = 0;

				foreach($acctbalancesheetcomparationreport_right as $keyRight =>$valRight){
					if(is_numeric($keyRight)){
						
						$this->excel->setActiveSheetIndex(0);
				
						$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					
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
							$this->excel->getActiveSheet()->getStyle('H'.$j)->getFont()->setBold(true);	
							$this->excel->getActiveSheet()->getStyle('I'.$j)->getFont()->setBold(true);	
						} else {
							
						}									

						if($valRight['report_type2'] == 1){
							$this->excel->getActiveSheet()->mergeCells("H".$j.":I".$j);
							$this->excel->getActiveSheet()->setCellValue('H'.$j, $valRight['account_name2']);
						} else {

						}



						if($valRight['report_type2']	== 2){
							$this->excel->getActiveSheet()->setCellValue('H'.$j, $report_tab2.$valRight['account_name2']);
						} else {

						}									

						if($valRight['report_type2']	== 3){
							$last_balance2 = $this->AcctBalanceSheetComparationReportNew_model->getLastBalance($valRight['account_id2'], $month_now, $year_now, $sesi['account_comparation_report_type'], $sesi['branch_id']);		

							if (empty($last_balance2)){
								$last_balance2 = $this->AcctBalanceSheetComparationReportNew_model->getLastBalance($valRight['account_id2'], $month_now, $year_now, $sesi['account_comparation_report_type'], $sesi['branch_id']);
							}		

							$this->excel->getActiveSheet()->setCellValue('H'.$j, $report_tab2.$valRight['account_name2']);
							$this->excel->getActiveSheet()->setCellValue('I'.$j, $report_tab2.$last_balance2);

							$account_amount2_now[$valRight['report_no']] = $last_balance2;

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
											$total_account_amount2 = $total_account_amount2 + $account_amount2_now[$report_formula2[$i]];
										} else {
											$total_account_amount2 = $total_account_amount2 - $account_amount2_now[$report_formula2[$i]];
										}
									} else if($report_operator2[$i] == '+'){
										if($total_account_amount2 == 0){
											$total_account_amount2 = $total_account_amount2 + $account_amount2_now[$report_formula2[$i]];
										} else {
											$total_account_amount2 = $total_account_amount2 + $account_amount2_now[$report_formula2[$i]];
										}
									}
								}

								$this->excel->getActiveSheet()->setCellValue('H'.$j, $report_tab2.$valRight['account_name2']);
								$this->excel->getActiveSheet()->setCellValue('I'.$j, $report_tab2.$total_account_amount2);

								
							} else {
								
							}
						} else {
							
						}



						if($valRight['report_type2'] == 7){
							if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
								$report_formula2 	= explode('#', $valRight['report_formula2']);
								$report_operator2 	= explode('#', $valRight['report_operator2']);

								$total_account_amount2_now	= 0;
								for($i = 0; $i < count($report_formula2); $i++){
									if($report_operator2[$i] == '-'){
										if($total_account_amount2_now == 0 ){
											$total_account_amount2_now = $total_account_amount2_now + $account_amount2_now[$report_formula2[$i]];
										} else {
											$total_account_amount2_now = $total_account_amount2_now - $account_amount2_now[$report_formula2[$i]];
										}
									} else if($report_operator2[$i] == '+'){
										if($total_account_amount2_now == 0){
											$total_account_amount2_now = $total_account_amount2_now + $account_amount2_now[$report_formula2[$i]];
										} else {
											$total_account_amount2_now = $total_account_amount2_now + $account_amount2_now[$report_formula2[$i]];
										}
									}
								}
							} 

							if(!empty($valRight['report_formula3']) && !empty($valRight['report_operator3'])){
								$report_formula3 	= explode('#', $valRight['report_formula3']);
								$report_operator3 	= explode('#', $valRight['report_operator3']);

								$total_account_amount1_now	= 0;
								for($i = 0; $i < count($report_formula3); $i++){
									if($report_operator3[$i] == '-'){
										if($total_account_amount1_now == 0 ){
											$total_account_amount1_now = $total_account_amount1_now + $account_amount1_now[$report_formula3[$i]];
										} else {
											$total_account_amount1_now = $total_account_amount1_now - $account_amount1_now[$report_formula3[$i]];
										}
									} else if($report_operator3[$i] == '+'){
										if($total_account_amount1_now == 0){
											$total_account_amount1_now = $total_account_amount1_now + $account_amount1_now[$report_formula3[$i]];
										} else {
											$total_account_amount1_now = $total_account_amount1_now + $account_amount1_now[$report_formula3[$i]];
										}
									}
								}
							} 

							$total_account_amount3_now = $total_account_amount1_now - $total_account_amount2_now;

							$this->excel->getActiveSheet()->setCellValue('H'.$j, $report_tab2.$valRight['account_name2']);
							$this->excel->getActiveSheet()->setCellValue('I'.$j, $report_tab2.$total_account_amount3_now);
						} else {
							
						}

						if($valRight['report_type2'] == 6){
							if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
								$report_formula2 	= explode('#', $valRight['report_formula2']);
								$report_operator2 	= explode('#', $valRight['report_operator2']);

								$grand_total_account_amount2_now	= 0;
								for($i = 0; $i < count($report_formula2); $i++){
									if($report_operator2[$i] == '-'){
										if($grand_total_account_amount2_now == 0 ){
											$grand_total_account_amount2_now = $grand_total_account_amount2_now + $account_amount2_now[$report_formula2[$i]];
										} else {
											$grand_total_account_amount2_now = $grand_total_account_amount2_now - $account_amount2_now[$report_formula2[$i]];
										}
									} else if($report_operator2[$i] == '+'){
										if($grand_total_account_amount2_now == 0){
											$grand_total_account_amount2_now = $grand_total_account_amount2_now + $account_amount2_now[$report_formula2[$i]];
										} else {
											$grand_total_account_amount2_now = $grand_total_account_amount2_now + $account_amount2_now[$report_formula2[$i]];
										}
									}
								}
								
								$grand_total_account_amount2_now += $total_account_amount3_now;
							} else {
								
							}
						} else {
							
						}	

					}else{
						continue;
					}

					$j++;
				}

				$total_row_right_now = $j;


				/*$data_row = array ($total_row_left_before, $total_row_right_before, $total_row_left_now, $total_row_right_now);*/

				$data_row = array (
					'total_row_left_before'		=> $total_row_left_before,
					'total_row_right_before'	=> $total_row_right_before,
					'total_row_left_now'		=> $total_row_left_now,
					'total_row_right_now'		=> $total_row_right_now,
				);

				asort($data_row);

				/*$total_row = $data_row[0];*/

				foreach($data_row as $keyRow => $valRow) {
				    $total_row = $valRow;
				}

				/*print_r("data_row ");
				print_r($data_row);
				print_r("<BR> ");

				print_r("total_row ");
				print_r($total_row);
				print_r("<BR> ");
				exit;*/

				

				$this->excel->getActiveSheet()->getStyle('B'.$total_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$this->excel->getActiveSheet()->getStyle('C'.$total_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

				$this->excel->getActiveSheet()->getStyle('D'.$total_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$this->excel->getActiveSheet()->getStyle('E'.$total_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

				$this->excel->getActiveSheet()->getStyle('F'.$total_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$this->excel->getActiveSheet()->getStyle('G'.$total_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

				$this->excel->getActiveSheet()->getStyle('H'.$total_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$this->excel->getActiveSheet()->getStyle('I'.$total_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

				$this->excel->getActiveSheet()->getStyle("B".$total_row.":E".$total_row)->getFont()->setBold(true);	

				$this->excel->getActiveSheet()->getStyle("F".$total_row.":I".$total_row)->getFont()->setBold(true);	

				$this->excel->getActiveSheet()->setCellValue('B'.$total_row, $report_tab1.$valLeft['account_name1']);
				$this->excel->getActiveSheet()->setCellValue('C'.$total_row, $report_tab1.$total_account_amount1_before+$total_account_amount10_before);

				$this->excel->getActiveSheet()->setCellValue('D'.$total_row, $report_tab2.$valRight['account_name2']);
				$this->excel->getActiveSheet()->setCellValue('E'.$total_row, $report_tab2.$total_account_amount2_before);

				$this->excel->getActiveSheet()->setCellValue('F'.$total_row, $report_tab1.$valLeft['account_name1']);
				$this->excel->getActiveSheet()->setCellValue('G'.$total_row, $report_tab1.$total_account_amount1+$total_account_amount10);

				$this->excel->getActiveSheet()->setCellValue('H'.$total_row, $report_tab2.$valRight['account_name2']);
				$this->excel->getActiveSheet()->setCellValue('I'.$total_row, $report_tab2.$total_account_amount2);


				$filename='Laporan Neraca Komparasi Periode '.$period_before.' - '.$period_now.'.xls';
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