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
	
</style>
<script type="text/javascript">
	base_url = "<?php echo base_url();?>";

	function reset_search(){
		document.location = base_url +"memorial-journal/reset-search";
	}
</script>
<div class="row-fluid">
	

			<!-- BEGIN PAGE TITLE & BREADCRUMB-->
<div class="page-bar">	
	<ul class="page-breadcrumb">
		<li>
			<i class="fa fa-home"></i>
			<a href="<?php echo base_url();?>">
				Beranda
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>memorial-journal">
				Daftar Jurnal Memorial
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$auth 	= $this->session->userdata('auth');
	$sesi 	= $this->session->userdata('filter-acctmemorialjournal');

	if(!is_array($sesi)){
		$sesi['start_date']			= date('Y-m-d');
		$sesi['end_date']			= date('Y-m-d');
		$sesi['branch_id']			= '';
	}
?>	
<?php	echo form_open('memorial-journal/filter',array('id' => 'myform', 'class' => '')); 

	$start_date			= $sesi['start_date'];
	$end_date			= $sesi['end_date'];
	// $a = md5('123456');
	// print_r($auth);
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Daftar Jurnal Memorial
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body form">
					 <div class = "row">
						<div class = "col-md-4">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="<?php echo tgltoview($start_date);?>"/>
								<label class="control-label">Tanggal Awal
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<div class = "col-md-4">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="<?php echo tgltoview($end_date);?>"/>
								<label class="control-label">Tanggal Akhir
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<?php if($auth['branch_status'] == 1) { ?>
						<div class="col-md-4">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('branch_id', $corebranch,set_value('branch_id',$sesi['branch_id']),'id="branch_id" class="form-control select2me" ');
								?>
								<label>Cabang</label>
							</div>
						</div>
						<?php } ?>
					</div>

					<div class="row">
						<div class="form-actions right">
							<button type="button" class="btn red" onClick="reset_search();"><i class="fa fa-times"></i> Batal</button>
							<button type="submit" class="btn green-jungle"><i class="fa fa-search"></i> Cari</button>
						</div>	
					</div>
				</div>
			</div>
<?php echo form_close(); ?>

			<div class="portlet-body">
				<div class="form-body">
					<table class="table table-bordered table-hover table-full-width" >
					<thead>
						<tr>
							<th width="5%">No</th>
							<th width="6%">Bukti</th>
							<th width="20%">Uraian</th>
							<th width="10%">Tanggal</th>
							<!-- <th width="10%">Nomor</th> -->
							<th width="10%">No. Per</th>
							<th width="15%">Perkiraan</th>
							<th width="10%">Nominal</th>
							<th width="10%">D/K</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$no = 1;
							$totaldebet = 0;
							$totalkredit = 0;
							if(empty($acctmemorialjournal)){
								echo "
									<tr>
										<td colspan='8' align='center'>Emty Data</td>
									</tr>
								";
							} else {
								$id = 0;
								foreach ($acctmemorialjournal as $key=>$val){

									if($val['journal_voucher_debit_amount'] <> 0 ){
										$nominal = $val['journal_voucher_debit_amount'];
										$status = "D";
									} else if($val['journal_voucher_credit_amount'] <> 0){
										$nominal = $val['journal_voucher_credit_amount'];
										$status = "K";
									}

									if($val['journal_voucher_id'] != $id){
										echo"
											<tr>			
												<td style='text-align:center; background-color:lightgrey'>$no.</td>
												<td style='text-align:left; background-color:lightgrey'>".$val['transaction_module_code']."</td>
												<td style='text-align:left; background-color:lightgrey'>".$val['journal_voucher_description']."</td>
												<td style='text-align:center; background-color:lightgrey'>".tgltoview($val['journal_voucher_date'])."</td>
												<td style='text-align:left; background-color:lightgrey'>".$val['account_code']."</td>
												<td style='text-align:left; background-color:lightgrey'>".$val['account_name']."</td>
												<td style='text-align:right; background-color:lightgrey'>".number_format($nominal, 2 )."</td>
												<td style='text-align:right; background-color:lightgrey'>".$status."</td>
											</tr>
										";
										$no++;
									} else {
										echo"
											<tr>			
												<td style='text-align:center'></td>
												<td></td>
												<td></td>
												<td></td>
												<td>&nbsp;&nbsp;&nbsp;&nbsp;".$val['account_code']."</td>
												<td>&nbsp;&nbsp;&nbsp;&nbsp;".$val['account_name']."</td>
												<td style='text-align:right;'>".number_format($nominal, 2 )."</td>
												<td style='text-align:right;'>".$status."</td>
											</tr>
										";
									}									
									
								$totaldebet += $val['journal_voucher_debit_amount'];
								$totalkredit += $val['journal_voucher_credit_amount'];	
								if($id != $val['journal_voucher_id']){
									$id = $val['journal_voucher_id'];
								}
								} 
							}
							
						?>
							<tr>
								<td colspan="8"></td>
							</tr>
							<tr>
								<td colspan="6" align="right"><b>Total Debet</td>
								<td align="right"><b><?php echo number_format($totaldebet, 2); ?></td>
							</tr>
							<tr>
								<td colspan="6" align="right"><b>Totel Kredit</td>
								<td align="right"><b><?php echo number_format($totalkredit, 2); ?></b></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
<?php echo form_close(); ?>