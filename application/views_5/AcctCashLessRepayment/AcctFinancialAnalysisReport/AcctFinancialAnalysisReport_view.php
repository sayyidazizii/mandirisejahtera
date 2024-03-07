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
			<a href="<?php echo base_url();?>AcctFinancialAnalysisReport">
				Tingkat Kesehatan (Rasio Keuangan)
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>

<?php
	$auth 		= $this->session->userdata('auth');
	$data 		= $this->session->userdata('filter-AcctFinancialAnalysisReport');
	$year_now 	=	date('Y');
	if(!is_array($data)){
		$data['month_period']				= date('m');
		$data['year_period']				= $year_now;
		$data['profit_loss_report_type'] 	= 1;
		$data['branch_id'] 					= $auth['branch_id'];
	}
	
	for($i = ($year_now-2); $i<($year_now+2); $i++){
		$year[$i] = $i;
	} 
	// print_r($data); exit;
?> 
			<!-- END PAGE TITLE & BREADCRUMB-->

<?php echo form_open('AcctFinancialAnalysisReport/filter',array('id' => 'myform', 'class' => '')); ?>
<div class="row">
	<div class="col-md-12">	
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Tingkat Kesehatan (Rasio Keuangan)
				</div>
			</div>
			<div class="portlet-body">
				<!-- BEGIN FORM-->
				<div class="form-body form">
				<?php
					echo $this->session->userdata('message');
					$this->session->unset_userdata('message');
				?>
				<?php if($auth['branch_status'] == 1) { ?>
				<div class="row">
					<div class = "col-md-6">
						<div class="form-group form-md-line-input">
							<?php echo form_dropdown('branch_id', $corebranch, set_value('branch_id',$data['branch_id']),'id="branch_id" class="form-control select2me"');?>
							<label class="control-label">Cabang
								<span class="required">
									*
								</span>
							</label>
						</div>
					</div>
					<div class = "col-md-6">
						<div class="form-group form-md-line-input">
							<button type="submit" class="btn green-jungle"><i class="fa fa-search"></i> Cari</button>
							<label class="control-label">
							</label>
						</div>
					</div>
				</div>
				<?php } ?>
					
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
							echo form_open('AcctFinancialAnalysisReport/processPrinting'); 

							$preferencecompany = $this->AcctFinancialAnalysisReport_model->getPreferenceCompany();

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
						?>
						<table class="table table-bordered table-advance table-hover">
							<thead>
								<tr>
									<td colspan='2' style='text-align:center;'>
										<div style='font-weight:bold'>Tingkat Kesehatan (Rasio Keuangan)</div>
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
								<tr>
									<td colspan='2' style='text-align:center;'>
										<div style='font-weight:bold'>Periode 
											<?php
												echo $period;
											?>
										</div>
									</td>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style='width: 50%; height: 25%'>
										<table class="table table-bordered table-advance table-hover">
											<?php
												foreach ($acctbalancesheetreport_left as $key => $val) {

													

													if($val['report_type1']	== 3){
														$last_balance1 = $this->AcctFinancialAnalysisReport_model->getLastBalance($data['branch_id'], $val['account_id1']);

														$account_amount1_top[$val['report_no']] = $last_balance1;
													}

													if($val['report_type2']	== 3){
														$last_balance2 = $this->AcctFinancialAnalysisReport_model->getLastBalance($data['branch_id'], $val['account_id2']);

														$account_amount2_top[$val['report_no']] = $last_balance2;
														// print_r('<br>');
														// print_r($account_amount2_top);
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

											?>

											<tr>
												<td style='text-align:center' colspan="2"><div style='font-weight: bold'>LCR (Likuiditas Cash Ratio)</div></td>
											</tr>
											<tr>
												<td style='text-align:center'><div style='font-weight: bold'><?php echo number_format($total_account_amount1,2); ?></div></td>
												<td style='text-align:center'><div style='font-weight: bold'><?php echo number_format($total_account_amount2,2); ?></div></td>
											</tr>
											<tr>
											<tr>
												<td style='text-align:center;height: 50px' colspan="2"><div style='font-weight: bold; font-size: 20px'><?php echo number_format($RASIO_LCR , 2); ?> %</div></td>
											</tr>
										</table>
									</td>
									<td style='width: 50%; height: 25%'>
										<table class="table table-bordered table-advance table-hover">
											<?php
												foreach ($acctfinancialanalysisCAR as $key => $val) {

													// print_r('<br>');
													// print_r($val);
													if($val['report_type1']	== 1){
														$last_balance1 = $this->AcctFinancialAnalysisReport_model->getLastBalance($val['account_id1'], $data['branch_id']);

														$account_amount1_top[$val['report_no']] = $last_balance1;


													}

													if($val['report_type2']	== 1){
														$last_balance2 = $this->AcctFinancialAnalysisReport_model->getLastBalance($val['account_id2'], $data['branch_id']);

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
											?>
											<tr>
												<td style='text-align:center' colspan="2"><div style='font-weight: bold'>CAR (Capital Aset Ratio)</div></td>
											</tr>
											<tr>
												<td style='text-align:center'><div style='font-weight: bold'><?php echo number_format($total_account_amount1,2); ?></div></td>
												<td style='text-align:center'><div style='font-weight: bold'><?php echo number_format($total_account_amount2,2); ?></div></td>
											</tr>
											<tr>
											<tr>
												<td style='text-align:center;height: 50px' colspan="2"><div style='font-weight: bold; font-size: 20px'><?php echo number_format($RASIO_CAR, 2); ?> %</div></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td style='width: 50%; height: 25%'>
										<table class="table table-bordered table-advance table-hover">
											<?php
												foreach ($acctfinancialanalysisLDR as $key => $val) {
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
											?>
											
											<tr>
												<td style='text-align:center' colspan="2"><div style='font-weight: bold'>LDR (Loan to Debt Ration)</div></td>
											</tr>
											<tr>
												<td style='text-align:center'><div style='font-weight: bold'><?php echo number_format($total_account_amount1,2); ?></div></td>
												<td style='text-align:center'><div style='font-weight: bold'><?php echo number_format($total_account_amount2,2); ?></div></td>
											</tr>
											<tr>
												<td style='text-align:center;height: 50px' colspan="2"><div style='font-weight: bold; font-size: 20px'><?php echo number_format($RASIO_FDR, 2); ?> %</div></td>
											</tr>
										</table>
									</td>
									<td style='width: 50%; height: 25%'>
										<table class="table table-bordered table-advance table-hover">
											<?php
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


											

											?>

											<tr>
												<td style='text-align:center' colspan="2"><div style='font-weight: bold'>BOPO (Beban Operasional vs Pendapatan Operasional)</div></td>
											</tr>
											<tr>
												<td style='text-align:center'><div style='font-weight: bold'><?php echo number_format($total_account_amount3,2); ?></div></td>
												<td style='text-align:center'><div style='font-weight: bold'><?php echo number_format($total_account_amount4,2); ?></div></td>
											</tr>
											<tr>
											<tr>
												<td style='text-align:center;height: 50px' colspan="2"><div style='font-weight: bold; font-size: 20px'><?php echo number_format($RASIO_BOPO, 2); ?> %</div></td>
											</tr>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					

					<div class="row">
						<div class="col-md-12 " style="text-align  : right !important;">
							<input type="submit" name="Preview" id="Preview" value="Preview" class="btn blue" title="Preview">
							<!-- <a href='javascript:void(window.open("<?php echo base_url(); ?>AcctFinancialAnalysisReport/exportAcctFinancialAnalysisReport","_blank","top=100,left=200,width=300,height=300"));' title="Export to Excel" class="btn blue"><i class="fa fa-print"></i> Export Data  </a> -->
												
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

<!-- </div> -->
