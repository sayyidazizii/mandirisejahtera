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
			<a href="<?php echo base_url();?>AcctProfitLossComparationReport">
				Laporan Komparasi Perhitungan SHU
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>

<?php
	$auth = $this->session->userdata('auth');
	$data = $this->session->userdata('filter-AcctProfitLossComparationReport');
	$year_now 	=	date('Y');
	if(!is_array($data)){
		$data['month_period']						= date('m');
		$data['year_period']						= $year_now;
		$data['account_comparation_report_type'] 	= 1;
		$data['profit_loss_report_format'] 			= 3;
		$data['branch_id']							= $auth['branch_id'];
	}
	
	for($i = ($year_now-2); $i<($year_now+2); $i++){
		$year[$i] = $i;
	} 
	// print_r($data); exit;
?> 
			<!-- END PAGE TITLE & BREADCRUMB-->

<?php echo form_open('AcctProfitLossComparationReport/filter',array('id' => 'myform', 'class' => '')); ?>
<div class="row">
	<div class="col-md-12">	
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Laporan Komparasi Perhitungan SHU
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
						<div class="col-md-4">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('month_period', $monthlist,set_value('month_period',$data['month_period']),'id="month_period" class="form-control select2me"');
								?>
								<label>Periode</label>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('year_period', $year,set_value('year_period',$data['year_period']),'id="year_period" class="form-control select2me" ');
								?>
								<label></label>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('account_comparation_report_type', $accountcomparationreporttype, set_value('account_comparation_report_type', $data['account_comparation_report_type']),'id="account_comparation_report_type" class="form-control select2me"');
								?>
								<label>Komparasi</label>
							</div>
						</div>
						<!-- <div class="col-md-4">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('profit_loss_report_format', $profitlossreportformat, set_value('profit_loss_report_format', $data['profit_loss_report_format']),'id="profit_loss_report_format" class="form-control select2me"');
								?>
								<label>Format</label>
							</div>
						</div> -->
						<?php if($auth['branch_status'] == 1) { ?>
							<div class = "col-md-4">
								<div class="form-group form-md-line-input">
									<?php echo form_dropdown('branch_id', $corebranch, set_value('branch_id',$data['branch_id']),'id="branch_id" class="form-control select2me"');?>
									<label class="control-label">Cabang
										<span class="required">
											*
										</span>
									</label>
								</div>
							</div>
						<?php } ?>
					</div>
					<div class="form-actions right">
						<input type="submit" name="Find" value="Find" class="btn green" title="Search Data">
					</div>
<!-- </div>
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
							echo form_open('AcctProfitLossComparationReport/processPrinting'); 

							$preferencecompany 	= $this->AcctProfitLossComparationReport_model->getPreferenceCompany();

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
						?>
						<div class = "row">
							<div class = "col-md-12">
								<table class="table table-bordered table-advance table-hover">
									<thead>
										<tr>
											<td colspan='2' style='text-align:center;'>
												<div style='font-weight:bold'>LAPORAN KOMPARASI PERHITUNGAN SHU
												</div>
											</td>
										</tr>
										<tr>
											<td colspan='2' style='text-align:center;'>
												<div style='font-weight:bold'>
													<?php
														echo $preferencecompany['company_name'];
													?>
												</div>
											</td>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td style='width: 50%'>
												<table class="table table-bordered table-advance table-hover">
													<tr>
														<td colspan='2' style='text-align:center;'>
															<div style='font-weight:bold'>PERIODE
																<?php
																	echo $period_before;
																?>
															</div>
														</td>
													</tr>
													<?php
														foreach ($acctprofitlosscomparationreport_top as $key => $val) {
															if($val['report_tab'] == 0){
																$report_tab = ' ';
															} else if($val['report_tab'] == 1){
																$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
															} else if($val['report_tab'] == 2){
																$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
															} else if($val['report_tab'] == 3){
																$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
															}

															if($val['report_bold'] == 1){
																$report_bold = 'bold';
															} else {
																$report_bold = 'normal';
															}

															echo "
																<tr>";

																if($val['report_type'] == 1){
																	echo "
																		<td colspan='2'><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
																		";
																}
																
															echo "
																</tr>";

															echo "
																<tr>";

																if($val['report_type']	== 2){
																	echo "
																		<td style='width: 75%'><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
																		<td style='width: 25%'><div style='font-weight:".$report_bold."'></div></td>
																		";
																}
																	
															echo "
																</tr>";

															echo "
																<tr>";

																if($val['report_type']	== 3){
																	$accountamount 		= $this->AcctProfitLossComparationReport_model->getAccountAmount($val['account_id'], $month_before, $year_before, $data['account_comparation_report_type'], $data['branch_id']);

																	$account_subtotal 	= ABS(($accountamount['account_in_amount'] - $accountamount['account_out_amount']));

																	echo "
																		<td><div style='font-weight:".$report_bold."'>".$report_tab."(".$val['account_code'].") ".$val['account_name']."</div> </td>
																		<td style='text-align:right'><div style='font-weight:".$report_bold."'>".number_format($account_subtotal, 2)."</div></td>
																	";

																	$account_amount_top_before[$val['report_no']] = $account_subtotal;
																}

																
																	
															echo "
																</tr>";

															echo "
																<tr>";

																if($val['report_type'] == 5){
																	if(!empty($val['report_formula']) && !empty($val['report_operator'])){
																		$report_formula 	= explode('#', $val['report_formula']);
																		$report_operator 	= explode('#', $val['report_operator']);

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

																		echo "
																			<td><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
																			<td style='text-align:right'><div style='font-weight:".$report_bold."'>".number_format($total_account_amount, 2)."</div></td>
																			";
																}

															}

															echo "			
																</tr>";
			

															if($val['report_type'] == 6){
																if(!empty($val['report_formula']) && !empty($val['report_operator'])){
																	$report_formula 	= explode('#', $val['report_formula']);
																	$report_operator 	= explode('#', $val['report_operator']);

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

																	echo "
																		<td><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
																		<td style='text-align:right'><div style='font-weight:".$report_bold."'>".number_format($grand_total_account_amount1_top_before, 2)."</div></td>
																		";
																}

															}
														}
													?>
												</table>
											</td>


											<td style='width: 50%'>
												<table class="table table-bordered table-advance table-hover">
													<tr>
														<td colspan='2' style='text-align:center;'>
															<div style='font-weight:bold'>PERIODE 
																<?php
																	echo $period_now;
																?>
															</div>
														</td>
													</tr>
													<?php
														foreach ($acctprofitlosscomparationreport_top as $key => $val) {
															if($val['report_tab'] == 0){
																$report_tab = ' ';
															} else if($val['report_tab'] == 1){
																$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
															} else if($val['report_tab'] == 2){
																$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
															} else if($val['report_tab'] == 3){
																$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
															}

															if($val['report_bold'] == 1){
																$report_bold = 'bold';
															} else {
																$report_bold = 'normal';
															}

															echo "
																<tr>";

																if($val['report_type'] == 1){
																	echo "
																		<td colspan='2'><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
																		";
																}
																
															echo "
																</tr>";

															echo "
																<tr>";

																if($val['report_type']	== 2){
																	echo "
																		<td style='width: 75%'><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
																		<td style='width: 25%'><div style='font-weight:".$report_bold."'></div></td>
																		";
																}
																	
															echo "
																</tr>";

															echo "
																<tr>";

																if($val['report_type']	== 3){
																	$accountamount 		= $this->AcctProfitLossComparationReport_model->getAccountAmount($val['account_id'], $month_now, $year_now, $data['account_comparation_report_type'], $data['branch_id']);

																	$account_subtotal 	= ABS(($accountamount['account_in_amount'] - $accountamount['account_out_amount']));

																	echo "
																		<td><div style='font-weight:".$report_bold."'>".$report_tab."(".$val['account_code'].") ".$val['account_name']."</div> </td>
																		<td style='text-align:right'><div style='font-weight:".$report_bold."'>".number_format($account_subtotal, 2)."</div></td>
																	";

																	$account_amount_top_now[$val['report_no']] = $account_subtotal;
																}

																
																	
															echo "
																</tr>";

															echo "
																<tr>";

																if($val['report_type'] == 5){
																	if(!empty($val['report_formula']) && !empty($val['report_operator'])){
																		$report_formula 	= explode('#', $val['report_formula']);
																		$report_operator 	= explode('#', $val['report_operator']);

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

																		echo "
																			<td><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
																			<td style='text-align:right'><div style='font-weight:".$report_bold."'>".number_format($total_account_amount, 2)."</div></td>
																			";
																}

															}

															echo "			
																</tr>";
			

															if($val['report_type'] == 6){
																if(!empty($val['report_formula']) && !empty($val['report_operator'])){
																	$report_formula 	= explode('#', $val['report_formula']);
																	$report_operator 	= explode('#', $val['report_operator']);

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

																	echo "
																		<td><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
																		<td style='text-align:right'><div style='font-weight:".$report_bold."'>".number_format($grand_total_account_amount1_top_now, 2)."</div></td>
																		";
																}

															}
														}
													?>
												</table>
											</td>
										</tr>
										<tr>	
											<td style='width: 50%'>
												<table class="table table-bordered table-advance table-hover">
													<?php
														foreach ($acctprofitlosscomparationreport_bottom as $key => $val) {
															if($val['report_tab'] == 0){
																$report_tab = ' ';
															} else if($val['report_tab'] == 1){
																$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
															} else if($val['report_tab'] == 2){
																$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
															} else if($val['report_tab'] == 3){
																$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
															}

															if($val['report_bold'] == 1){
																$report_bold = 'bold';
															} else {
																$report_bold = 'normal';
															}

															echo "
																<tr>";

																if($val['report_type'] == 1){
																	echo "
																		<td colspan='2'><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
																		";
																}
																
															echo "
																</tr>";

															echo "
																<tr>";

																if($val['report_type']	== 2){
																	echo "
																		<td style='width: 75%'><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
																		<td style='width: 25%'><div style='font-weight:".$report_bold."'></div></td>
																		";
																}
																	
															echo "
																</tr>";

															echo "
																<tr>";

																if($val['report_type']	== 3){
																	$accountamount 		= $this->AcctProfitLossComparationReport_model->getAccountAmount($val['account_id'], $month_before, $year_before, $data['account_comparation_report_type'], $data['branch_id']);

																	$account_subtotal 	= ABS(($accountamount['account_in_amount'] - $accountamount['account_out_amount']));

																	echo "
																		<td><div style='font-weight:".$report_bold."'>".$report_tab."(".$val['account_code'].") ".$val['account_name']."</div> </td>
																		<td style='text-align:right'><div style='font-weight:".$report_bold."'>".number_format($account_subtotal, 2)."</div></td>
																	";

																	$account_amount_bottom_before[$val['report_no']] = $account_subtotal;
																}

																
																	
															echo "
																</tr>";

															echo "
																<tr>";

																if($val['report_type'] == 5){
																	if(!empty($val['report_formula']) && !empty($val['report_operator'])){
																		$report_formula 	= explode('#', $val['report_formula']);
																		$report_operator 	= explode('#', $val['report_operator']);

																		$total_account_amount	= 0;
																		for($i = 0; $i < count($report_formula); $i++){
																			if($report_operator[$i] == '-'){
																				if($value == 0 ){
																					$total_account_amount = $total_account_amount + $account_amount_bottom_before[$report_formula[$i]];
																				} else {
																					$total_account_amount = $total_account_amount - $account_amount_bottom_before[$report_formula[$i]];
																				}
																			} else if($report_operator[$i] == '+'){
																				if($total_account_amount == 0){
																					$total_account_amount = $total_account_amount + $account_amount_bottom_before[$report_formula[$i]];
																				} else {
																					$total_account_amount = $total_account_amount + $account_amount_bottom_before[$report_formula[$i]];
																				}
																			}
																		}

																		echo "
																			<td><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
																			<td style='text-align:right'><div style='font-weight:".$report_bold."'>".number_format($total_account_amount, 2)."</div></td>
																			";
																}
															}

															echo "			
																</tr>";
			
															if($val['report_type'] == 6){
																if(!empty($val['report_formula']) && !empty($val['report_operator'])){
																	$report_formula 	= explode('#', $val['report_formula']);
																	$report_operator 	= explode('#', $val['report_operator']);

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

																	echo "
																		<td><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
																		<td style='text-align:right'><div style='font-weight:".$report_bold."'>".number_format($grand_total_account_amount2_bottom_before, 2)."</div></td>
																		";
																}

															}



														}
													?>
												</table>
											</td>

											<td style='width: 50%'>
												<table class="table table-bordered table-advance table-hover">
													<?php
														foreach ($acctprofitlosscomparationreport_bottom as $key => $val) {
															if($val['report_tab'] == 0){
																$report_tab = ' ';
															} else if($val['report_tab'] == 1){
																$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
															} else if($val['report_tab'] == 2){
																$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
															} else if($val['report_tab'] == 3){
																$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
															}

															if($val['report_bold'] == 1){
																$report_bold = 'bold';
															} else {
																$report_bold = 'normal';
															}

															echo "
																<tr>";

																if($val['report_type'] == 1){
																	echo "
																		<td colspan='2'><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
																		";
																}
																
															echo "
																</tr>";

															echo "
																<tr>";

																if($val['report_type']	== 2){
																	echo "
																		<td style='width: 75%'><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
																		<td style='width: 25%'><div style='font-weight:".$report_bold."'></div></td>
																		";
																}
																	
															echo "
																</tr>";

															echo "
																<tr>";

																if($val['report_type']	== 3){
																	$accountamount 		= $this->AcctProfitLossComparationReport_model->getAccountAmount($val['account_id'], $month_now, $year_now, $data['account_comparation_report_type'], $data['branch_id']);

																	$account_subtotal 	= ABS(($accountamount['account_in_amount'] - $accountamount['account_out_amount']));

																	echo "
																		<td><div style='font-weight:".$report_bold."'>".$report_tab."(".$val['account_code'].") ".$val['account_name']."</div> </td>
																		<td style='text-align:right'><div style='font-weight:".$report_bold."'>".number_format($account_subtotal, 2)."</div></td>
																	";

																	$account_amount_bottom_now[$val['report_no']] = $account_subtotal;
																}

																
																	
															echo "
																</tr>";

															echo "
																<tr>";

																if($val['report_type'] == 5){
																	if(!empty($val['report_formula']) && !empty($val['report_operator'])){
																		$report_formula 	= explode('#', $val['report_formula']);
																		$report_operator 	= explode('#', $val['report_operator']);

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

																		echo "
																			<td><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
																			<td style='text-align:right'><div style='font-weight:".$report_bold."'>".number_format($total_account_amount, 2)."</div></td>
																			";
																}
															}

															echo "			
																</tr>";
			
															if($val['report_type'] == 6){
																if(!empty($val['report_formula']) && !empty($val['report_operator'])){
																	$report_formula 	= explode('#', $val['report_formula']);
																	$report_operator 	= explode('#', $val['report_operator']);

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

																	echo "
																		<td><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
																		<td style='text-align:right'><div style='font-weight:".$report_bold."'>".number_format($grand_total_account_amount2_bottom_now, 2)."</div></td>
																		";
																}

															}



														}
													?>
												</table>
											</td>
										</tr>
										<tr>	
											<td>
												<table class="table table-bordered table-advance table-hover">
													<tr>
														<td  style="width: 70%">
															<div style='font-weight:bold; font-size:16px'>
																SHU SEBELUM PAJAK
															</div>
														</td >
														<td style="width: 25%; text-align:right" >
															<div style='font-weight:bold; font-size:16px'>
																<?php
																	$shu_before = $grand_total_account_amount1_top_before - $grand_total_account_amount2_bottom_before;
																	echo number_format($shu_before, 2);
																?>	
															</div>
														</td>
													</tr>
													<tr>
														<td  style="width: 70%">
															<div style='font-weight:bold; font-size:16px'>
																PAJAK PENGHASILAN
															</div>
														</td >
														<td style="width: 25%; text-align:right" >
															<div style='font-weight:bold; font-size:16px'>
																<?php
																	$accountamounttaxbefore 		= $this->AcctProfitLossComparationReport_model->getAccountAmount($preferencecompany['account_income_tax_id'], $month_before, $year_before, $data['account_comparation_report_type'], $data['branch_id']);

																	$tax_income_before 	= ABS(($accountamounttaxbefore['account_in_amount'] - $accountamounttaxbefore['account_out_amount']));

																	echo number_format($tax_income_before, 2);
																?>	
															</div>
														</td>
													</tr>
													<tr>
														<td  style="width: 70%">
															<div style='font-weight:bold; font-size:16px'>
																SHU SETELAH PAJAK
															</div>
														</td >
														<td style="width: 25%; text-align:right" >
															<div style='font-weight:bold; font-size:16px'>
																<?php
																	$shu_before = $grand_total_account_amount1_top_before - $grand_total_account_amount2_bottom_before - $tax_income_before;
																	echo number_format($shu_before, 2);
																?>	
															</div>
														</td>
													</tr>
												</table>
											</td>

											<td>
												<table class="table table-bordered table-advance table-hover">
													<tr>
														<td  style="width: 70%">
															<div style='font-weight:bold; font-size:16px'>
																SHU SEBELUM PAJAK
															</div>
														</td >
														<td style="width: 25%; text-align:right" >
															<div style='font-weight:bold; font-size:16px'>
																<?php
																	$shu_now = $grand_total_account_amount1_top_now - $grand_total_account_amount2_bottom_now;
																	echo number_format($shu_now, 2);
																?>	
															</div>
														</td>
													</tr><tr>
														<td  style="width: 70%">
															<div style='font-weight:bold; font-size:16px'>
																PAJAK PENGHASILAN
															</div>
														</td >
														<td style="width: 25%; text-align:right" >
															<div style='font-weight:bold; font-size:16px'>
																<?php
																	$accountamounttax		= $this->AcctProfitLossComparationReport_model->getAccountAmount($preferencecompany['account_income_tax_id'], $month_now, $year_now, $data['account_comparation_report_type'], $data['branch_id']);
																	
																	$income_tax 	= ABS(($accountamounttax['account_in_amount'] - $accountamounttax['account_out_amount']));

																	echo number_format($income_tax, 2);
																?>	
															</div>
														</td>
													</tr><tr>
														<td  style="width: 70%">
															<div style='font-weight:bold; font-size:16px'>
																SHU SETELAH PAJAK
															</div>
														</td >
														<td style="width: 25%; text-align:right" >
															<div style='font-weight:bold; font-size:16px'>
																<?php
																	$shu_now = $grand_total_account_amount1_top_now - $grand_total_account_amount2_bottom_now - $income_tax;
																	echo number_format($shu_now, 2);
																?>	
															</div>
														</td>
													</tr>
												</table>
											</td>
										</tr>
										
									</tbody>
								</table>
							</div>
						</div>
					

					<div class="row">
						<div class="col-md-12 " style="text-align  : right !important;">
							<input type="submit" name="Preview" id="Preview" value="Preview" class="btn blue" title="Preview">
							<a href='javascript:void(window.open("<?php echo base_url(); ?>AcctProfitLossComparationReport/exportAcctProfitLossComparationReport","_blank","top=100,left=200,width=300,height=300"));' title="Export to Excel" class="btn blue"><i class="fa fa-print"></i> Export Data  </a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<!-- </div> -->
