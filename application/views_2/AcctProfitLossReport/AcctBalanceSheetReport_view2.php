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
			<a href="<?php echo base_url();?>AcctReport">
				Accounting Report
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>

<?php
$data=$this->session->userdata('filter-AcctReport');
$year_now 	=	date('Y');
	if(!is_array($data)){
		$data['month_period']			= date('m');
		$data['year_period']			= $year_now;
		$data['id_report'] 				= '';
	}
	
	for($i = ($year_now-2); $i<($year_now+2); $i++){
		$year[$i] = $i;
	} 
	// print_r($data); exit;
?> 
			<!-- END PAGE TITLE & BREADCRUMB-->
<h3 class="page-title">
Income Statement 
</h3>
<?php echo form_open('AcctReport/filter',array('id' => 'myform', 'class' => '')); ?>
<div class="row">
	<div class="col-md-12">	
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Form Add
				</div>
				<div class="actions">
					<a href="<?php echo base_url();?>AcctReport" class="btn btn-default btn-sm">
						<i class="fa fa-angle-left"></i>
						<span class="hidden-480">
							Back
						</span>
					</a>
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
								<label>From Period</label>
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
									echo form_dropdown('id_report', $reporttype,set_value('id_report',$data['id_report']),'id="id_report" class="form-control select2me"');
								?>
								<label>Report Type</label>
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
					<div class="table-responsive">
						<br>
						<?php
							echo form_open('AcctReport/processPrinting'); 
						?>
						<table class="table table-bordered table-advance table-hover">
							<thead>
								<tr>
									<th style='text-align:center' colspan='4'><?php echo $preferencecompany['company_name']; ?>
										<br><?php echo $this->configuration->ReportType[$data['id_report']]; ?><br>Periode <?php echo $this->configuration->Month[$data['month_period']]; ?> <?php echo $data['year_period']; ?>
									</th>
									<?php
										$minus_month= mktime(0, 0, 0, date($data['month_period'])-1);
										$month = date('m', $minus_month);

										if($month == 12){
											$year = $data['year_period'] - 1;
										} else {
											$year = $data['year_period'];
										}

										// print_r($data['id_report']);
									?>
									<!-- <tr>
										<td width='25%'></td>
										<td width='10%'><div style='text-align  : center !important;font-weight: bold;font-size: 13px;'><?php echo $this->configuration->Month[$data['month_period']]; ?> <?php echo $data['year_period']; ?></div></td>
										<td width='10%'><div style='text-align  : center !important;font-weight: bold;font-size: 13px;'><?php echo $this->configuration->Month[$month]; ?> <?php echo $year; ?></div></td>
										<td><div style='text-align  : center !important;font-weight: bold;font-size: 13px;'>Selisih</div></td>
										<td><div style='text-align  : center !important;font-weight: bold;font-size: 13px;'>%</div></td>
									</tr> -->
								</tr>
							</thead>
							<tbody>
								<?php
									foreach ($acctbalancesheetreport as $key => $val) {
										if($val['report_tab1'] == 0){
											$report_tab1 = ' ';
										} else if($val['report_tab1'] == 1){
											$report_tab1 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
										} else if($val['report_tab1'] == 2){
											$report_tab1 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
										} else if($val['report_tab1'] == 3){
											$report_tab1 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
										}

										if($val['report_bold1'] == 1){
											$report_bold1 = 'bold';
										} else {
											$report_bold1 = 'normal';
										}

										if($val['report_tab2'] == 0){
											$report_tab2 = ' ';
										} else if($val['report_tab2'] == 1){
											$report_tab2 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
										} else if($val['report_tab2'] == 2){
											$report_tab2 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
										} else if($val['report_tab2'] == 3){
											$report_tab2 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
										}

										if($val['report_bold2'] == 1){
											$report_bold2 = 'bold';
										} else {
											$report_bold2 = 'normal';
										}


										/*$formula[$val['id_no']] = $val['formula'];
										$operator[$val['id_no']] = $val['operator'];
										$type[$val['id_no']] = $val['type'];
*/
										echo "
											<tr>";

											if($val['report_type1'] == 1){
												echo "
													<td style='width: 50%' colspan='2'><div style='font-weight:".$report_bold1."'>".$report_tab1."".$val['account_name1']."</div></td>
													";
											}
											if($val['report_type2'] == 1){
												echo "
													<td style='width: 50%' colspan='2'><div style='font-weight:".$report_bold2."'>".$report_tab2."".$val['account_name2']."</div></td>
													";
											}
										echo "
											</tr>";

										echo "
											<tr>";

											if($val['report_type1']	== 2){
												echo "
													<td><div style='font-weight:".$report_bold1."'>".$report_tab1."".$val['account_name1']."</div></td>
													<td><div style='font-weight:".$report_bold1."'></div></td>
													";
											}

											if($val['report_type1']	== 2){
												echo "
													<td><div style='font-weight:".$report_bold2."'>".$report_tab2."".$val['account_name2']."</div></td>
													<td><div style='font-weight:".$report_bold2."'></div></td>
													";
											}
												
										echo "
											</tr>";

										/*if($val['type']	== 'loop'){
											$length = strlen($val['account_id']);

											$accountlistparent = $this->acctreport_model->getAccountListParent($length, $val['account_id']);

										

											$totalamount = 0;
											$totalamount_before = 0;
											if(!empty($accountlistparent)){
												foreach ($accountlistparent as $key => $val2) {
													

													$accountparent = $this->acctreport_model->getAccountParentAmount($val2['account_id'], $data['month_period'], $data['year_period']);

													
													$totalaccountamount = ABS(($accountparent['account_in_amount'] - $accountparent['account_out_amount']));

													if($data['id_report'] == 2){
													

														$saldoaccountparrent = $this->acctreport_model->getSaldoAccountParent($val2['account_id'], $data['month_period'], $data['year_period']);


														$totalsaldoawal = $saldoaccountparrent;

														$totalsaldoakhir = $totalsaldoawal + $totalaccountamount;
													}


													// -----------------------------------------------------------------------------------//


													$accountparentbefore = $this->acctreport_model->getAccountParentAmount($val2['account_id'], $month, $year);

													$totalaccountamount_before = ABS(($accountparentbefore['account_in_amount'] - $accountparentbefore['account_out_amount']));

													if($data['id_report'] == 2){
													
														
														$saldoaccountparrentbefore = $this->acctreport_model->getSaldoAccountParent($val2['account_id'],  $month, $year);


														$totalsaldoawalbefore = $saldoaccountparrentbefore;

														$totalsaldoakhirbefore = $totalsaldoawalbefore + $totalaccountamount_before;

														$selisih = $totalsaldoakhir - $totalsaldoakhirbefore;
													} else{
														$selisih = $totalaccountamount - $totalaccountamount_before;
													}


													
													// $percentage = $selisih / 100;
													$total_account = $totalsaldoakhir + $totalsaldoakhirbefore + $totalaccountamount + $totalaccountamount_before + $selisih;

													if ($total_account <> 0){
														echo "
															<tr>
																<td><div style='font-weight:".$bold."'>".$tab."(".$val2['account_code'].") ".$val2['account_name']."</div> </td>";
																if($data['id_report'] == 2){
																	// echo "aaaaaaaa";
																	echo "
																		<td style='text-align:right'><div style='font-weight:".$bold."'>".number_format($totalsaldoakhir, 2)."</div></td>
																		<td style='text-align:right'><div style='font-weight:".$bold."'>".number_format($totalsaldoakhirbefore, 2)."</div></td>";

																	if ($totalsaldoakhirbefore > 0){
																		$selisih_persen = $selisih / $totalsaldoakhirbefore * 100;
																	} else {
																		$selisih_persen = 0;
																	}

																} else {
																	echo "
																		<td style='text-align:right'><div style='font-weight:".$bold."'>".number_format($totalaccountamount, 2)."</div></td>
																		<td style='text-align:right'><div style='font-weight:".$bold."'>".number_format($totalaccountamount_before, 2)."</div></td>";

																	if ($totalaccountamount_before > 0){
																		$selisih_persen = $selisih / $totalaccountamount_before * 100;
																	} else {
																		$selisih_persen = 0;
																	}
																}

																echo "
																<td style='text-align:right'><div style='font-weight:".$bold."'>".number_format($selisih, 2)."</div></td>
																<td style='text-align:right; width: 10%'><div style='font-weight:".$bold."'>".number_format($selisih_persen, 2)." %</div></td>
																
															</tr>
														";
													}

													if($data['id_report'] == 2){
														$totalamount = $totalamount + $totalsaldoakhir;
														$totalamount_before = $totalamount_before + $totalsaldoakhirbefore;
													} else {
														$totalamount = $totalamount + $totalaccountamount;
														$totalamount_before = $totalamount_before + $totalaccountamount_before;
													}

													

													$account[$val['id_no']] = $totalamount;
													$accountbefore[$val['id_no']] = $totalamount_before;
													$accountselisih[$val['id_no']] = $selisih;
													// $accountpercentage[$val['id_no']] = $percentage;
												}
											}
										}*/

										echo "
											<tr>";

											if($val['report_type1']	== 3){
												$last_balance1 = $this->AcctBalanceSheetReport_model->getLastBalance($val['account_id1']);

												$account_amount1[$val['report_no']] = $last_balance1;
											}

											if($val['report_type2']	== 3){
												$last_balance2 = $this->AcctBalanceSheetReport_model->getLastBalance($val['account_id2']);

												$account_amount2[$val['report_no']] = $last_balance2;
											}

											if ($val['account_code1'] == ""){
												echo "
													<td></td>
													<td></td>
												";	
											} else {
												echo "
													<td><div style='font-weight:".$report_bold1."'>".$report_tab1."(".$val['account_code1'].") ".$val['account_name1']."</div> </td>
													<td style='text-align:right'><div style='font-weight:".$report_bold1."'>".number_format($last_balance1, 2)."</div></td>
												";
											}
											
											if ($val['account_code2'] == ""){
												echo "
													<td></td>
													<td></td>
												";
											} else {
												echo "
													<td><div style='font-weight:".$report_bold2."'>".$report_tab2."(".$val['account_code2'].") ".$val['account_name2']."</div> </td>
													<td style='text-align:right'><div style='font-weight:".$report_bold2."'>".number_format($last_balance2, 2)."</div></td>
												";
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
															if($value == 0 ){
																$total_account_amount1 = $total_account_amount1 + $account_amount1[$report_formula1[$i]];
															} else {
																$total_account_amount1 = $total_account_amount1 - $account_amount1[$report_formula1[$i]];
															}
														} else if($report_operator1[$i] == '+'){
															if($total_account_amount1 == 0){
																$total_account_amount1 = $total_account_amount1 + $account_amount1[$report_formula1[$i]];
															} else {
																$total_account_amount1 = $total_account_amount1 + $account_amount1[$report_formula1[$i]];
															}
														}
													}

													echo "
														<td><div style='font-weight:".$report_bold1."'>".$report_tab1."".$val['account_name1']."</div></td>
														<td style='text-align:right'><div style='font-weight:".$report_bold1."'>".number_format($total_account_amount1, 2)."</div></td>
														";
											}

											if($val['report_type2'] == 5){
												if(!empty($val['report_formula2']) && !empty($val['report_operator2'])){
													$report_formula2 	= explode('#', $val['report_formula2']);
													$report_operator2 	= explode('#', $val['report_operator2']);

													$total_account_amount2 = 0;
													for($i = 0; $i < count($report_formula1); $i++){
														if($report_operator2[$i] == '-'){
															if($value == 0 ){
																$total_account_amount2 = $total_account_amount2 + $account_amount2[$report_formula2[$i]];
															} else {
																$total_account_amount2 = $total_account_amount2 - $account_amount1[$report_formula1[$i]];
															}
														} else if($report_operator2[$i] == '+'){
															if($total_account_amount2 == 0){
																$total_account_amount2 = $total_account_amount2 + $account_amount2[$report_formula2[$i]];
															} else {
																$total_account_amount2 = $total_account_amount2 + $account_amount2[$report_formula2[$i]];
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
												
											
										}

									}
								?>
							</tbody>
						</table>
					</div>

					<div class="row">
						<div class="col-md-12 " style="text-align  : right !important;">
							<input type="submit" name="Preview" id="Preview" value="Preview" class="btn blue" title="Preview">
							<!-- <a href='javascript:void(window.open("<?php echo base_url(); ?>invtitemstockreport/exportInvtItemStock","_blank","top=100,left=200,width=300,height=300"));' title="Export to Excel"> Export Data  <img src='<?php echo base_url(); ?>img/Excel.png' height="32" width="32"></a> -->
												
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<!-- </div> -->
