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

	function reset_data(){
		document.location = base_url+"acctincomestatement/reset_data";
	}

	mappia = "	<?php 
					$site_url = 'acctincomestatement/addGiroPayment';
					echo site_url($site_url); 
				?>";

	function function_elements_add(name, value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('acctincomestatement/function_elements_add');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
						// alert(name);
			}
		});
	}
	
	function function_state_add(value){

		$.ajax({
				type: "POST",
				url : "<?php echo site_url('acctincomestatement/function_state_add');?>",
				data : {'value' : value},
				success: function(msg){
			}
		});
	}

	function processAddArrayGiroPaymentItem(){
		
		var account_id_item					= document.getElementById("account_id_item").value;
		var giro_payment_item_amount		= document.getElementById("giro_payment_item_amount").value;
		var giro_payment_item_title			= document.getElementById("giro_payment_item_title").value;

		
			$('#offspinwarehouse').css('display', 'none');
			$('#onspinspinwarehouse').css('display', 'table-row');
			  $.ajax({
			  type: "POST",
			  url : "<?php echo site_url('acctincomestatement/processAddArrayGiroPaymentItem');?>",
			  data: {
			  		'account_id_item'					: account_id_item,
					'giro_payment_item_amount' 			: giro_payment_item_amount, 
					'giro_payment_item_title' 			: giro_payment_item_title, 
					'session_name' 						: "addarrayacctincomestatementstart-"
				},
			  success: function(msg){
			   window.location.replace(mappia);
			 }
			});
	}

	function toRp(number) {
		var number = number.toString(), 
		rupiah = number.split('.')[0], 
		cents = (number.split('.')[1] || '') +'00';
		rupiah = rupiah.split('').reverse().join('')
			.replace(/(\d{3}(?!$))/g, '$1,')
			.split('').reverse().join('');
		return rupiah + '.' + cents.slice(0, 2);
	}

	function check_giro_payment_item_amount(value) {
		if(isNaN(value)===true){
			  alert('please input only numbers! ');
			  document.getElementById("giro_payment_item_amount2").value = '';
			  document.getElementById("giro_payment_item_amount2").focus();
		}else if (value < 0 ){
			alert('Amount Cannot Less Than!');
			$('#giro_payment_item_amount2').val('');
			document.getElementById("giro_payment_item_amount2").focus();
		} else {
			$('#giro_payment_item_amount2').val(toRp(value));
			$('#giro_payment_item_amount').val(value);
		}
    }	
	
	function getJournalDataPopUp() {
		   $.ajax({
				type: "POST",
				url : "<?php echo site_url('acctincomestatement/listJournalPopUp');?>",
				success: function(msg){
					$('#container3').html(msg);
					
			}
			});
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
			<a href="<?php echo base_url();?>acctreport">
				Accounting Report
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>

<?php
$data=$this->session->userdata('filter-acctreport');
$year_now 	=	date('Y');
	if(!is_array($data)){
		$data['month_period']			= date('m');
		$data['year_period']			= $year_now;
		$data['id_report'] 				= '';
	}
	
	for($i=($year_now-2); $i<($year_now+2); $i++){
		$year[$i] = $i;
	} 
	// print_r($data); exit;
?> 
			<!-- END PAGE TITLE & BREADCRUMB-->
<h3 class="page-title">
Income Statement 
</h3>
<?php echo form_open('acctreport/filter',array('id' => 'myform', 'class' => '')); ?>
<div class="row">
	<div class="col-md-12">	
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Form Add
				</div>
				<div class="actions">
					<a href="<?php echo base_url();?>acctreport" class="btn btn-default btn-sm">
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
<!-- <div class="row">
	<div class="col-md-12">	
		<div class="portlet box blue">
			<div class="portlet-body"> -->
				<!-- BEGIN FORM-->
				<!-- <div class="form-body form">
					<div class="table-responsive"> -->
						<br>
						<?php
							echo form_open('acctreport/processPrinting'); 
						?>
						<table class="table table-bordered table-advance table-hover">
							<thead>
								<tr>
									<th style='text-align:center' colspan='5'><?php echo $preferencecompany['company_name']; ?>
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
									<tr>
										<td width='25%'></td>
										<td width='10%'><div style='text-align  : center !important;font-weight: bold;font-size: 13px;'><?php echo $this->configuration->Month[$data['month_period']]; ?> <?php echo $data['year_period']; ?></div></td>
										<td width='10%'><div style='text-align  : center !important;font-weight: bold;font-size: 13px;'><?php echo $this->configuration->Month[$month]; ?> <?php echo $year; ?></div></td>
										<td><div style='text-align  : center !important;font-weight: bold;font-size: 13px;'>Selisih</div></td>
										<td><div style='text-align  : center !important;font-weight: bold;font-size: 13px;'>%</div></td>
									</tr>
								</tr>
							</thead>
							<tbody>
								<?php
									$income = $this->acctreport_model->getIncomeStatement($data['id_report']);

									foreach ($income as $key => $val) {
										$indent_tab[$val['indent_tab']] = $val['indent_tab'];
										if($indent_tab[$val['indent_tab']] == 0){
											$tab = ' ';
										} else if($indent_tab[$val['indent_tab']] == 1){
											$tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
										} else if($indent_tab[$val['indent_tab']] == 2){
											$tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
										} else if($indent_tab[$val['indent_tab']] == 3){
											$tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
										}

										$indent_bold[$val['indent_bold']] = $val['indent_bold'];
										if($indent_bold[$val['indent_bold']] == 1){
											$bold = 'bold';
										} else {
											$bold = 'normal';
										}


										$formula[$val['id_no']] = $val['formula'];
										$operator[$val['id_no']] = $val['operator'];
										$type[$val['id_no']] = $val['type'];


										if($val['type'] == 'title'){
											echo "
												<tr>
													<td style='width: 20%'><div style='font-weight:".$bold."'>".$tab."".$val['field_name']."</div></td>
													<td style='text-align:right; width: 15%'><div style='font-weight:".$bold."'></div></td>
													<td style='text-align:right; width: 15%'><div style='font-weight:".$bold."'></div></td>
													<td style='text-align:right; width: 15%'><div style='font-weight:".$bold."'></div></td>
													<td style='text-align:right; width: 15%'><div style='font-weight:".$bold."'></div></td>
												</tr>
											";
										}

										if($val['type']	== 'subtitle'){
											echo "
												<tr>
													<td><div style='font-weight:".$bold."'>".$tab."".$val['field_name']."</div></td>
													<td style='text-align:right; width: 10%'><div style='font-weight:".$bold."'></div></td>
													<td style='text-align:right; width: 10%'><div style='font-weight:".$bold."'></div></td>
													<td style='text-align:right; width: 10%'><div style='font-weight:".$bold."'></div></td>
													<td style='text-align:right; width: 10%'><div style='font-weight:".$bold."'></div></td>
												</tr>
											";
										}

										if($val['type']	== 'loop'){
											$length = strlen($val['account_id']);

											$accountlistparent = $this->acctreport_model->getAccountListParent($length, $val['account_id']);

											$totalamount = 0;
											$totalamount_before = 0;
											if(!empty($accountlistparent)){
												foreach ($accountlistparent as $key => $val2) {
													$accountchild = $this->acctreport_model->getAccountChildAmount($val2['account_id'], $data['month_period'], $data['year_period']);
													$accountparent = $this->acctreport_model->getAccountParentAmount($val2['account_id'], $data['month_period'], $data['year_period']);

													$totalaccountamount = ($accountchild['account_in_amount'] - $accountchild['account_out_amount'])+ ($accountparent['account_in_amount'] - $accountparent['account_out_amount']);

													if($data['id_report'] == 2){
														$saldoaccountchild = $this->acctreport_model->getSaldoAccountChild($val2['account_id'], $data['month_period'], $data['year_period']);
														$saldoaccountparrent = $this->acctreport_model->getSaldoAccountParent($val2['account_id'], $data['month_period'], $data['year_period']);

														$totalsaldoawal = $saldoaccountchild + $saldoaccountparrent;

														$totalsaldoakhir = $totalsaldoawal + $totalaccountamount;
													}


													// -----------------------------------------------------------------------------------//

													$accountchildbefore = $this->acctreport_model->getAccountChildAmount($val2['account_id'], $month, $year);
													$accountparentbefore = $this->acctreport_model->getAccountParentAmount($val2['account_id'], $month, $year);

													$totalaccountamount_before = ($accountchildbefore['account_in_amount'] - $accountchildbefore['account_out_amount'])+ ($accountparentbefore['account_in_amount'] - $accountparentbefore['account_out_amount']);

													if($data['id_report'] == 2){
														$saldoaccountchildbefore = $this->acctreport_model->getSaldoAccountChild($val2['account_id'], $month, $year);
														$saldoaccountparrentbefore = $this->acctreport_model->getSaldoAccountParent($val2['account_id'],  $month, $year);
														$totalsaldoawalbefore = $saldoaccountchildbefore + $saldoaccountparrentbefore;

														$totalsaldoakhirbefore = $totalsaldoawalbefore + $totalaccountamount_before;

														$selisih = $totalsaldoakhir - $totalsaldoakhirbefore;
													} else{
														$selisih = $totalaccountamount - $totalaccountamount_before;
													}


													
													// $percentage = $selisih / 100;
													echo "
														<tr>
															<td><div style='font-weight:".$bold."'>".$tab."(".$val2['account_code'].") ".$val2['account_name']."</div> </td>";
															if($data['id_report'] == 2){
																// echo "aaaaaaaa";
																echo "
																	<td style='text-align:right'><div style='font-weight:".$bold."'>".$totalsaldoakhir."</div></td>
																	<td style='text-align:right'><div style='font-weight:".$bold."'>".$totalsaldoakhirbefore."</div></td>";
															} else {
																echo "
																	<td style='text-align:right'><div style='font-weight:".$bold."'>".$totalaccountamount."</div></td>
																	<td style='text-align:right'><div style='font-weight:".$bold."'>".$totalaccountamount_before."</div></td>";
															}
															echo "
															<td style='text-align:right'><div style='font-weight:".$bold."'>".$selisih."</div></td>
															<td style='text-align:right; width: 10%'><div style='font-weight:".$bold."'></div></td>
															
														<tr>
													";

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
										}

										if($val['type'] == 'sum'){
											if(!empty($val['formula']) && !empty($val['operator'])){
												$exp = explode('#', $val['formula']);
												$exp_op = explode('#', $val['operator']);

												

												$value = 0;
												$valuebefore = 0;
												for($i = 0; $i < count($exp); $i++){
														if($exp_op[$i] == '-'){
															if($value == 0 && $valuebefore == 0){
																$value = $value + $account[$exp[$i]];
																$valuebefore = $valuebefore + $accountbefore[$exp[$i]];
															} else {
																$value = $value - $account[$exp[$i]];
																$valuebefore = $valuebefore - $accountbefore[$exp[$i]];
															}
														} else if($exp_op[$i] == '+'){
															if($value == 0 && $valuebefore == 0){
																$value = $value + $account[$exp[$i]];
																$valuebefore = $valuebefore + $accountbefore[$exp[$i]];
															} else {
																$value = $value + $account[$exp[$i]];
																$valuebefore = $valuebefore + $accountbefore[$exp[$i]];
															}
														}

														$acct[$val['id_no']] = $value;	
														$acctbefore[$val['id_no']]	= $valuebefore;
												}

												$acctselisih[$val['id_no']] = $acct[$val['id_no']] - $acctbefore[$val['id_no']];
												echo "
													<tr>
														<td><div style='font-weight:".$bold."'>".$tab."".$val['field_name']."</div></td>
														<td style='text-align:right'><div style='font-weight:".$bold."'>".$acct[$val['id_no']]."</div></td>
														<td style='text-align:right'><div style='font-weight:".$bold."'>".$acctbefore[$val['id_no']]."</div></td>
														<td style='text-align:right'><div style='font-weight:".$bold."'>".$acctselisih[$val['id_no']]."</div></td>
														<td style='text-align:right; width: 10%'><div style='font-weight:".$bold."'></div></td>
													</tr>
												";

												echo "
													<tr>
														<td colspan='5'></td>
													</tr>
												";
												
											}		
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
