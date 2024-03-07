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

	function formupdate(data){
		
		if(data.value == 0  || data.value == 9){
			 document.getElementById("branch").style.display = "block";
			
		}
			if(data.value == 1){
			 document.getElementById("branch").style.display = "none";
		}		
	}
	
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
			<a href="<?php echo base_url();?>AcctConsolidationReportNew">
				Laporan Perhitungan SHU
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>

<?php
	$auth = $this->session->userdata('auth');
	$data = $this->session->userdata('filter-AcctProfitLossConsolidationReport');
	$year_now 	=	date('Y');
	if(!is_array($data)){
		$data['month_period']						= date('m');
		$data['year_period']						= $year_now;
		$data['consolidation_report'] 				= 1;
		$data['account_comparation_report_type'] 	= 1;
		$data['branch_id']							= $corebranch['branch_id'];
	}
	
	for($i = ($year_now-2); $i<($year_now+2); $i++){
		$year[$i] = $i;
	} 

	if($data['consolidation_report'] == 0 || $data['consolidation_report'] == 9){
		$branch_name = $this->AcctConsolidationReportNew_model->getBranchName($data['branch_id']);
	} else {
		$branch_name = 'GABUNGAN';
	}
	// print_r($data); exit;
?> 
			<!-- END PAGE TITLE & BREADCRUMB-->

<?php echo form_open('AcctConsolidationReportNew/filterAcctProfitLoss',array('id' => 'myform', 'class' => '')); ?>
<div class="row">
	<div class="col-md-12">	
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Laporan Perhitungan SHU
				</div>
			</div>
			<div class="portlet-body">
				<!-- BEGIN FORM-->
				<div class="form-body form">
				<?php
					echo $this->session->userdata('message');
					$this->session->unset_userdata('message');
				?>
					<!-- <?php if($auth['branch_status'] == 1) { ?> -->
				<div class="row">
					<div class="col-md-3">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('month_period', $monthlist,set_value('month_period',$data['month_period']),'id="month_period" class="form-control select2me"');
								?>
								<label>Periode</label>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('year_period', $year,set_value('year_period',$data['year_period']),'id="year_period" class="form-control select2me" ');
								?>
								<label></label>
							</div>
						</div>

					<div class = "col-md-3">
						<div class="form-group form-md-line-input">
							<!-- <?php echo form_dropdown('consolidation_report', $consolidation, set_value('consolidation_report',$data['consolidation_report']),'id="consolidation_report" class="form-control select2me" onchange="formupdate(this)"');?> -->

							<input type="hidden" name="consolidation_report" id="consolidation_report" class="form-control" value="<?php echo $data['consolidation_report']; ?>" readonly>
							<input type="text" name="consolidation_report_view" id="consolidation_report_view" class="form-control" value="<?php echo $consolidation[$data['consolidation_report']]; ?>" readonly>
							<label class="control-label">Konsolidasi
								<span class="required">
									*
								</span>
							</label>
						</div>
					</div>
					<div class="col-md-3">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('account_comparation_report_type', $accountcomparationreporttype, set_value('account_comparation_report_type', $data['account_comparation_report_type']),'id="account_comparation_report_type" class="form-control select2me"');
								?>
								<label>Komparasi</label>
							</div>
						</div>

					<div class = "col-md-4" id="branch" style="display: block">
						<div class="form-group form-md-line-input">
							<!-- <?php echo form_dropdown('branch_id', $corebranch, set_value('branch_id',$data['branch_id']),'id="branch_id" class="form-control select2me" ');?> -->

							<input type="hidden" name="branch_id" id="branch_id" class="form-control" value="<?php echo $corebranch['branch_id']; ?>" readonly>
							<input type="text" name="branch_name" id="branch_name" class="form-control" value="<?php echo $corebranch['branch_name']; ?>" readonly>
							<label class="control-label">Cabang
								<span class="required">
									*
								</span>
							</label>
						</div>
					</div>
					<div class = "col-md-4">
						<div class="form-group form-md-line-input">
							<button type="submit" class="btn green-jungle"><i class="fa fa-search"></i> Cari</button>
							<label class="control-label">
							</label>
						</div>
					</div>
				</div>
				<!-- <?php } ?> -->
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
							echo form_open('AcctConsolidationReportNew/processPrintingAcctProfitLoss'); 

							$preferencecompany = $this->AcctConsolidationReportNew_model->getPreferenceCompany();

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
							if($data['account_comparation_report_type'] == 1){
								$period = $month_name." ".$data['year_period'];
							} else {
								$period = $data['year_period'];
							}
							
						?>
						<div class = "row">
							<div class = "col-md-2">
							</div>
							<div class = "col-md-8">
								<table class="table table-bordered table-advance table-hover">
									<thead>
										<tr>
											<td colspan='2' style='text-align:center;'>
												<div style='font-weight:bold'>LAPORAN PERHITUNGAN SHU <?php echo $branch_name; ?></div>
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
												<div style='font-weight:bold'>Period 
													<?php
														echo $period;
													?>
												</div>
											</td>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>
												<table class="table table-bordered table-advance table-hover">
													<?php
														foreach ($acctprofitlossreport_top as $key => $val) {
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
																	$account_subtotal 	= $this->AcctConsolidationReportNew_model->getAccountAmount($val['account_id'], $data['month_period'], $data['year_period'], $data['account_comparation_report_type'], $data['branch_id']);


																	echo "
																		<td><div style='font-weight:".$report_bold."'>".$report_tab."(".$val['account_code'].") ".$val['account_name']."</div> </td>
																		<td style='text-align:right'><div style='font-weight:".$report_bold."'>".number_format($account_subtotal, 2)."</div></td>
																	";

																	$account_amount[$val['report_no']] = $account_subtotal;
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

																	echo "
																		<td><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
																		<td style='text-align:right'><div style='font-weight:".$report_bold."'>".number_format($grand_total_account_amount1, 2)."</div></td>
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
													<?php
														foreach ($acctprofitlossreport_bottom as $key => $val) {
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
																	$account_subtotal 	= $this->AcctConsolidationReportNew_model->getAccountAmount($val['account_id'], $data['month_period'], $data['year_period'], $data['account_comparation_report_type'], $data['branch_id']);

																	echo "
																		<td><div style='font-weight:".$report_bold."'>".$report_tab."(".$val['account_code'].") ".$val['account_name']."</div> </td>
																		<td style='text-align:right'><div style='font-weight:".$report_bold."'>".number_format($account_subtotal, 2)."</div></td>
																	";

																	$account_amount[$val['report_no']] = $account_subtotal;
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

																	echo "
																		<td><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
																		<td style='text-align:right'><div style='font-weight:".$report_bold."'>".number_format($grand_total_account_amount2, 2)."</div></td>
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
														<td style="width: 70%">
															<div style='font-weight:bold; font-size:16px'>
																SHU 
															</div>
														</td >
														<td style="width: 25%; text-align:right" >
															<div style='font-weight:bold; font-size:16px'>
																<?php
																	$shu = $grand_total_account_amount1 - $grand_total_account_amount2;
																	echo number_format($shu, 2);
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

							<div class = "col-md-2">
							</div>
						</div>
					

					<div class="row">
						<div class="col-md-12 " style="text-align  : right !important;">
							<input type="submit" name="Preview" id="Preview" value="Preview" class="btn blue" title="Preview">
							<a href='javascript:void(window.open("<?php echo base_url(); ?>AcctConsolidationReportNew/exportAcctProfitLossReport","_blank","top=100,left=200,width=300,height=300"));' title="Export to Excel" class="btn blue"><i class="fa fa-print"></i> Export Data  </a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<!-- </div> -->
