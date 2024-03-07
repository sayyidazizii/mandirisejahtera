<style>
  
	th{
		font-size:14px  !important;
		font-weight: bold !important;
		text-align:center !important;
		margin : 0 auto;
		vertical-align:middle !important;
	}
	td{
		font-size:12px  !important;
		font-weight: normal !important;
	}

	.flexigrid div.pDiv input {
		vertical-align:middle !important;
	}
	
	.flexigrid div.pDiv div.pDiv2 {
		margin-bottom: 10px !important;
	}
	

</style>
<script>
	base_url = '<?php echo base_url();?>';


	
</script>
			<!-- BEGIN PAGE TITLE & BREADCRUMB-->
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li>
			<a href="<?php echo base_url();?>">
				Home
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>AcctBalanceSheetComparationReport">
				Laporan Neraca
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>

<?php
	$data 		= $this->session->userdata('filter-AcctBalanceSheetComparationReport');
	$year_now 	= date('Y');

	if(!is_array($data)){
		$data['month_period']			= date('m');
		$data['year_period']			= $year_now;
	}
	
	for($i = ($year_now-2); $i<($year_now+2); $i++){
		$year[$i] = $i;
	} 
?> 
			<!-- END PAGE TITLE & BREADCRUMB-->

<?php echo form_open('AcctBalanceSheetComparationReport/filter',array('id' => 'myform', 'class' => '')); ?>
<div class="row">
	<div class="col-md-12">	
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Laporan Neraca
				</div>
			</div>
			<div class="portlet-body">
				<!-- BEGIN FORM-->
				<div class="form-body form">
				<?php
					echo $this->session->userdata('message');
					$this->session->unset_userdata('message');
				?>
					<div class = "row">
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('month_period', $monthlist,set_value('month_period',$data['month_period']),'id="month_period" class="form-control select2me"');
								?>
								<label>Periode</label>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('year_period', $year,set_value('year_period',$data['year_period']),'id="year_period" class="form-control select2me" ');
								?>
								<label></label>
							</div>
						</div>
					</div>
					<div class="form-actions right">
						<input type="submit" name="Find" value="Find" class="btn green" title="Search Data">
					</div>
<!-- 				</div>
			</div>
		</div>
	</div>
</div> -->
<?php echo form_close(); ?>
<div class="row">
	<div class="col-md-12">	
		<div class="portlet box blue">
			<div class="portlet-body">
				<!-- BEGIN FORM-->
				<div class="form-body form">
					
						<br>
						<?php
							echo form_open('AcctBalanceSheetComparationReport/processPrinting'); 

							$day 	= date("d");
							$month 	= date("m");
							$year 	= date("Y");

							$period 		= $day." ".$month_name." ".$year;

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

						?>
						<table class="table table-bordered table-advance table-hover">
							<thead>
								<tr>
									<td colspan='4' style='text-align:center;'>
										<div style='font-weight:bold'>LAPORAN NERACA</div>
									</td>
								</tr>
								<tr>
									<td colspan='2' style='text-align:center;'>
										<div style='font-weight:bold'>PERIODE 
											<?php
												echo $month_before_name." ".$year_before;
											?>
										</div>
									</td>

									<td colspan='2' style='text-align:center;'>
										<div style='font-weight:bold'>PERIODE
											<?php
												echo $month_now_name." ".$year_now;
											?>
										</div>
									</td>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style='width: 20%'>
										<table class="table table-bordered table-advance table-hover">
											<?php
												foreach ($acctbalancesheetcomparationreport_left as $key => $val) {
													if($val['report_tab1'] == 0){
														$report_tab1 = ' ';
													} else if($val['report_tab1'] == 1){
														$report_tab1 = '&nbsp;&nbsp;&nbsp;';
													} else if($val['report_tab1'] == 2){
														$report_tab1 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
													} else if($val['report_tab1'] == 3){
														$report_tab1 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
													}

													if($val['report_bold1'] == 1){
														$report_bold1 = 'bold';
													} else {
														$report_bold1 = 'normal';
													}

													echo "
														<tr>";

														if($val['report_type1'] == 1){
															echo "
																<td colspan='2'><div style='font-weight:".$report_bold1."'>".$report_tab1."".$val['account_name1']."</div></td>
																";
														}
														
													echo "
														</tr>";

													echo "
														<tr>";

														if($val['report_type1']	== 2){
															echo "
																<td style='width: 75%'><div style='font-weight:".$report_bold1."'>".$report_tab1."".$val['account_name1']."</div></td>
																<td style='width: 25%'><div style='font-weight:".$report_bold1."'></div></td>
																";
														}
															
													echo "
														</tr>";

													echo "
														<tr>";

														if($val['report_type1']	== 3){
															$last_balance1 = $this->AcctBalanceSheetComparationReport_model->getLastBalance($val['account_id1'], $month_before, $year_before);

															echo "
																<td><div style='font-weight:".$report_bold1."'>".$report_tab1."(".$val['account_code1'].") ".$val['account_name1']."</div> </td>
																<td style='text-align:right'><div style='font-weight:".$report_bold1."'>".number_format($last_balance1, 2)."</div></td>
															";

															$account_amount1_before[$val['report_no']] = $last_balance1;
														}

														
															
													echo "
														</tr>";

													echo "
														<tr>";

														if($val['report_type1'] == 5){
															if(!empty($val['report_formula1']) && !empty($val['report_operator1'])){
																$report_formula1 	= explode('#', $val['report_formula1']);
																$report_operator1 	= explode('#', $val['report_operator1']);

																$total_account_amount1	= 0;
																for($i = 0; $i < count($report_formula1); $i++){
																	if($report_operator1[$i] == '-'){
																		if($grand_total_account_amount2 == 0 ){
																			$grand_total_account_amount2 = $total_account_amount1 + $account_amount1_before[$report_formula1[$i]];
																		} else {
																			$total_account_amount1 = $total_account_amount1 - $account_amount1_before[$report_formula1[$i]];
																		}
																	} else if($report_operator1[$i] == '+'){
																		if($total_account_amount1 == 0){
																			$total_account_amount1 = $total_account_amount1 + $account_amount1_before[$report_formula1[$i]];
																		} else {
																			$total_account_amount1 = $total_account_amount1 + $account_amount1_before[$report_formula1[$i]];
																		}
																	}
																}

																echo "
																	<td><div style='font-weight:".$report_bold1."'>".$report_tab1."".$val['account_name1']."</div></td>
																	<td style='text-align:right'><div style='font-weight:".$report_bold1."'>".number_format($total_account_amount1, 2)."</div></td>
																	";
															}
														}

													echo "			
														</tr>";

													echo "
														<tr>";

														if($val['report_type1'] == 6){
															if(!empty($val['report_formula1']) && !empty($val['report_operator1'])){
																$report_formula1 	= explode('#', $val['report_formula1']);
																$report_operator1 	= explode('#', $val['report_operator1']);

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

																$grand_total_account_name1_before = $val['account_name1'];
															}
														}

													echo "			
														</tr>";	

												}
											?>
										</table>
									</td>
									<td style='width: 20%'>
										<table class="table table-bordered table-advance table-hover">
											<?php
												foreach ($acctbalancesheetcomparationreport_right as $key => $val) {
													if($val['report_tab2'] == 0){
														$report_tab2 = ' ';
													} else if($val['report_tab2'] == 1){
														$report_tab2 = '&nbsp;&nbsp;&nbsp;';
													} else if($val['report_tab2'] == 2){
														$report_tab2 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
													} else if($val['report_tab2'] == 3){
														$report_tab2 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
													}

													if($val['report_bold2'] == 1){
														$report_bold2 = 'bold';
													} else {
														$report_bold2 = 'normal';
													}

													echo "
														<tr>";

														if($val['report_type2'] == 1){
															echo "
																<td colspan='2'><div style='font-weight:".$report_bold2."'>".$report_tab2."".$val['account_name2']."</div></td>
																";
														}
														
													echo "
														</tr>";

													echo "
														<tr>";

														if($val['report_type2']	== 2){
															echo "
																<td style='width: 75%'><div style='font-weight:".$report_bold2."'>".$report_tab2."".$val['account_name2']."</div></td>
																<td style='width: 25%'><div style='font-weight:".$report_bold2."'></div></td>
																";
														}
															
													echo "
														</tr>";

													echo "
														<tr>";

														if($val['report_type2']	== 3){
															$last_balance2 = $this->AcctBalanceSheetComparationReport_model->getLastBalance($val['account_id2'], $month_before, $year_before);

															echo "
																<td><div style='font-weight:".$report_bold2."'>".$report_tab2."(".$val['account_code2'].") ".$val['account_name2']."</div> </td>
																<td style='text-align:right'><div style='font-weight:".$report_bold2."'>".number_format($last_balance2, 2)."</div></td>
															";

															$account_amount2_before[$val['report_no']] = $last_balance2;
														}

														
															
													echo "
														</tr>";

													echo "
														<tr>";

														if($val['report_type2'] == 5){
															if(!empty($val['report_formula2']) && !empty($val['report_operator2'])){
																$report_formula2 	= explode('#', $val['report_formula2']);
																$report_operator2 	= explode('#', $val['report_operator2']);

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

																echo "
																	<td><div style='font-weight:".$report_bold2."'>".$report_tab2."".$val['account_name2']."</div></td>
																	<td style='text-align:right'><div style='font-weight:".$report_bold2."'>".number_format($total_account_amount2, 2)."</div></td>
																	";
															}	
														}
													echo "			
														</tr>";
																

													echo "
														<tr>";

														if($val['report_type2'] == 6){
															if(!empty($val['report_formula2']) && !empty($val['report_operator2'])){
																$report_formula2 	= explode('#', $val['report_formula2']);
																$report_operator2 	= explode('#', $val['report_operator2']);

																$grand_total_account_amount2_before	= 0;
																for($i = 0; $i < count($report_formula2); $i++){
																	if($report_operator2[$i] == '-'){
																		if($grand_total_account_amount2_before == 0 ){
																			$grand_total_account_amount2_before = $grand_total_account_amount2 + $account_amount2_before[$report_formula2[$i]];
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

																$grand_total_account_name2_before = $val['account_name2'];

															}	
														}

													echo "			
														</tr>";	
												}
											?>
										</table>
									</td>

									


									<td style='width: 20%'>
										<table class="table table-bordered table-advance table-hover">
											<?php
												foreach ($acctbalancesheetcomparationreport_left as $key => $val) {
													if($val['report_tab1'] == 0){
														$report_tab1 = ' ';
													} else if($val['report_tab1'] == 1){
														$report_tab1 = '&nbsp;&nbsp;&nbsp;';
													} else if($val['report_tab1'] == 2){
														$report_tab1 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
													} else if($val['report_tab1'] == 3){
														$report_tab1 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
													}

													if($val['report_bold1'] == 1){
														$report_bold1 = 'bold';
													} else {
														$report_bold1 = 'normal';
													}

													echo "
														<tr>";

														if($val['report_type1'] == 1){
															echo "
																<td colspan='2'><div style='font-weight:".$report_bold1."'>".$report_tab1."".$val['account_name1']."</div></td>
																";
														}
														
													echo "
														</tr>";

													echo "
														<tr>";

														if($val['report_type1']	== 2){
															echo "
																<td style='width: 75%'><div style='font-weight:".$report_bold1."'>".$report_tab1."".$val['account_name1']."</div></td>
																<td style='width: 25%'><div style='font-weight:".$report_bold1."'></div></td>
																";
														}
															
													echo "
														</tr>";

													echo "
														<tr>";

														if($val['report_type1']	== 3){
															$last_balance1 = $this->AcctBalanceSheetComparationReport_model->getLastBalance($val['account_id1'], $month_now, $year_now);

															echo "
																<td><div style='font-weight:".$report_bold1."'>".$report_tab1."(".$val['account_code1'].") ".$val['account_name1']."</div> </td>
																<td style='text-align:right'><div style='font-weight:".$report_bold1."'>".number_format($last_balance1, 2)."</div></td>
															";

															$account_amount1_now[$val['report_no']] = $last_balance1;
														}

														
															
													echo "
														</tr>";

													echo "
														<tr>";

														if($val['report_type1'] == 5){
															if(!empty($val['report_formula1']) && !empty($val['report_operator1'])){
																$report_formula1 	= explode('#', $val['report_formula1']);
																$report_operator1 	= explode('#', $val['report_operator1']);

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

																echo "
																	<td><div style='font-weight:".$report_bold1."'>".$report_tab1."".$val['account_name1']."</div></td>
																	<td style='text-align:right'><div style='font-weight:".$report_bold1."'>".number_format($total_account_amount1, 2)."</div></td>
																	";
															}
														}

													echo "			
														</tr>";

													echo "
														<tr>";

														if($val['report_type1'] == 6){
															if(!empty($val['report_formula1']) && !empty($val['report_operator1'])){
																$report_formula1 	= explode('#', $val['report_formula1']);
																$report_operator1 	= explode('#', $val['report_operator1']);

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

																$grand_total_account_name1_now = $val['account_name1'];
															}
														}

													echo "			
														</tr>";	

												}
											?>
										</table>
									</td>
									<td style='width: 20%'>
										<table class="table table-bordered table-advance table-hover">
											<?php
												foreach ($acctbalancesheetcomparationreport_right as $key => $val) {
													if($val['report_tab2'] == 0){
														$report_tab12 = ' ';
													} else if($val['report_tab2'] == 1){
														$report_tab2 = '&nbsp;&nbsp;&nbsp;';
													} else if($val['report_tab2'] == 2){
														$report_tab2 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
													} else if($val['report_tab2'] == 3){
														$report_tab2 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
													}

													if($val['report_bold2'] == 1){
														$report_bold2 = 'bold';
													} else {
														$report_bold2 = 'normal';
													}

													echo "
														<tr>";

														if($val['report_type2'] == 1){
															echo "
																<td colspan='2'><div style='font-weight:".$report_bold2."'>".$report_tab2."".$val['account_name2']."</div></td>
																";
														}
														
													echo "
														</tr>";

													echo "
														<tr>";

														if($val['report_type2']	== 2){
															echo "
																<td style='width: 75%'><div style='font-weight:".$report_bold2."'>".$report_tab2."".$val['account_name2']."</div></td>
																<td style='width: 25%'><div style='font-weight:".$report_bold2."'></div></td>
																";
														}
															
													echo "
														</tr>";

													echo "
														<tr>";

														if($val['report_type2']	== 3){
															$last_balance2 = $this->AcctBalanceSheetComparationReport_model->getLastBalance($val['account_id2'], $month_now, $year_now);

															echo "
																<td><div style='font-weight:".$report_bold2."'>".$report_tab2."(".$val['account_code2'].") ".$val['account_name2']."</div> </td>
																<td style='text-align:right'><div style='font-weight:".$report_bold2."'>".number_format($last_balance2, 2)."</div></td>
															";

															$account_amount2_now[$val['report_no']] = $last_balance2;
														}

														
															
													echo "
														</tr>";

													echo "
														<tr>";

														if($val['report_type2'] == 5){
															if(!empty($val['report_formula2']) && !empty($val['report_operator2'])){
																$report_formula2 	= explode('#', $val['report_formula2']);
																$report_operator2 	= explode('#', $val['report_operator2']);

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

																echo "
																	<td><div style='font-weight:".$report_bold2."'>".$report_tab2."".$val['account_name2']."</div></td>
																	<td style='text-align:right'><div style='font-weight:".$report_bold2."'>".number_format($total_account_amount2, 2)."</div></td>
																	";
															}	
														}
													echo "			
														</tr>";
																

													echo "
														<tr>";

														if($val['report_type2'] == 6){
															if(!empty($val['report_formula2']) && !empty($val['report_operator2'])){
																$report_formula2 	= explode('#', $val['report_formula2']);
																$report_operator2 	= explode('#', $val['report_operator2']);

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

																$grand_total_account_name2_now = $val['account_name2'];

															}	
														}

													echo "			
														</tr>";	
												}
											?>
										</table>
									</td>
								</tr>

								<tr>
									<td style='width: 20%'>
										<table class="table table-bordered table-advance table-hover">
											<tr>
												<?php
													echo "
														<td style=\"width: 70%\"><div style=\"font-weight:".$report_bold1.";font-size:14px\">".$report_tab1."".$grand_total_account_name1_before."</div>
														</td>
														<td style=\"width: 28%; text-align:right;\"><div style=\"font-weight:".$report_bold1."; font-size:14px\">".number_format($grand_total_account_amount1_before, 2)."</div>
														</td>
													";
												?>
											</tr>
										</table>
									</td>

									<td style='width: 20%'>
										<table class="table table-bordered table-advance table-hover">
											<tr>
												<?php 
													echo "
														<td style=\"width: 70%\"><div style=\"font-weight:".$report_bold2.";font-size:14px\">".$report_tab2."".$grand_total_account_name2_before."</div></td>
														<td style=\"width: 28%; text-align:right;\"><div style=\"font-weight:".$report_bold2."; font-size:14px\">".number_format($grand_total_account_amount2_before, 2)."</div></td>
													";
												?>
											</tr>
										</table>
									</td>

									<td style='width: 20%'>
										<table class="table table-bordered table-advance table-hover">
											<tr>
												<?php
													echo "
														<td style=\"width: 70%\"><div style=\"font-weight:".$report_bold1.";font-size:14px\">".$report_tab1."".$grand_total_account_name1_now."</div>
														</td>
														<td style=\"width: 28%; text-align:right;\"><div style=\"font-weight:".$report_bold1."; font-size:14px\">".number_format($grand_total_account_amount1_now, 2)."</div>
														</td>
													";
												?>
											</tr>
										</table>
									</td>

									<td style='width: 20%'>
										<table class="table table-bordered table-advance table-hover">
											<tr>
												<?php 
													echo "
														<td style=\"width: 70%\"><div style=\"font-weight:".$report_bold2.";font-size:14px\">".$report_tab2."".$grand_total_account_name2_before."</div></td>
														<td style=\"width: 28%; text-align:right;\"><div style=\"font-weight:".$report_bold2."; font-size:14px\">".number_format($grand_total_account_amount2_now, 2)."</div></td>
													";
												?>
											</tr>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					

					<div class="row">
						<div class="col-md-12 " style="text-align  : right !important;">
							<input type="submit" name="Preview" id="Preview" value="Preview" class="btn blue" title="Preview">
							<a href='javascript:void(window.open("<?php echo base_url(); ?>AcctBalanceSheetComparationReport/exportAcctBalanceSheetComparationReport","_blank","top=100,left=200,width=300,height=300"));' title="Export to Excel" class="btn blue"><i class="fa fa-print"></i> Export Data  </a>
												
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<!-- </div> -->
