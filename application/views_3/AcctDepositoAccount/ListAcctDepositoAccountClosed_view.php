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
	base_url = "<?php echo base_url(); ?>"
	function reset_search(){
		document.location = base_url = "reset-search-closed";
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
			<a href="<?php echo base_url();?>deposito-account">
				Daftar Rekening Simpanan Berjangka
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>deposito-account/get-closed">
				Penutupan Simpanan Berjangka
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->


<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$auth = $this->session->userdata('auth');
	$sesi=$this->session->userdata('filter-closedacctdepositoaccount');

	if(!is_array($sesi)){
		
		$sesi['deposito_id']			= '';
		$sesi['branch_id']				= '';
	}
?>	
<?php	echo form_open('deposito-account/filter-closed-deposito-account',array('id' => 'myform', 'class' => '')); 

?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Penutupan Simpanan Berjangka
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body form">
					 <!-- <div class = "row">
						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="<?php echo tgltoview($start_date);?>"/>
								<label class="control-label">Tanggal Awal
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="<?php echo tgltoview($end_date);?>"/>
								<label class="control-label">Tanggal Akhir
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>
					</div> -->

					 <div class = "row">
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('deposito_id', $acctdeposito,set_value('deposito_id',$sesi['deposito_id']),'id="deposito_id" class="form-control select2me" ');
								?>
								<label>Jenis Simpanan</label>
							</div>
						</div>

						<?php if($auth['branch_status'] == 1) { ?>
						<div class="col-md-6">
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
							<button type="button" class="btn red" onClick="reset_search();"><i class="fa fa-times"></i> Reset</button>
							<button type="submit" class="btn green-jungle"><i class="fa fa-search"></i> Find</button>
						</div>	
					</div>
				</div>
			</div>

<?php echo form_close(); ?>

			<div class="portlet-body">
				<div class="form-body">
					<table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
					<thead>
						<tr>
							<th width="5%">No</th>
							<th width="15%">Nama Anggota</th>
							<th width="15%">Jenis Simpanan Berjangka</th>
							<th width="12%">Nomor SimpKa</th>
							<th width="12%">Nomor Seri</th>
							<th width="10%">Tanggal Buka</th>
							<th width="10%">Tanggal Jatuh Tempo</th>
							<th width="15%">Nominal</th>
							<th width="8%">Bunga</th>
							<th width="8%">Pajak</th>
							<th width="6%">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$no = 1;
							if(empty($acctdepositoaccount)){
								echo "
									<tr>
										<td colspan='8' align='center'>Emty Data</td>
									</tr>
								";
							} else {
								foreach ($acctdepositoaccount as $key=>$val){	
									
									$deposito_accrual_last_balance = $this->AcctDepositoAccount_model->getAcctDepositoAccrualLastBalance($val['deposito_account_id']);

									$interest_total		 		   = $deposito_accrual_last_balance + $val['deposito_account_nisbah'];
									if($interest_total > 240000){
										$tax_total	= $interest_total * 10 / 100;
									}else{
										$tax_total 	= 0;
									}
								// print_r($acctdepositoaccount);								
									echo"
										<tr>			
											<td style='text-align:center'>$no.</td>
											<td>".$val['member_name']."</td>
											<td>".$val['deposito_name']."</td>
											<td>".$val['deposito_account_no']."</td>
											<td>".$val['deposito_account_serial_no']."</td>
											<td>".tgltoview($val['deposito_account_date'])."</td>
											<td>".tgltoview($val['deposito_account_due_date'])."</td>
											<td>".number_format($val['deposito_account_amount'])."</td>
											<td>".nominal($interest_total)."</td>
											<td>".nominal($tax_total)."</td>";
											if($val['deposito_account_blockir_status'] == 0){
												echo "
													<td>
														<a href='".$this->config->item('base_url').'deposito-account/add-closed/'.$val['deposito_account_id']."' class='btn default btn-xs yellow'>
															<i class='fa fa-edit'></i> Penutupan
														</a>
													</td>
												";
											} else {
												echo "
													<td>
														<a onClick='javascript:return confirm(\"Rekening Diblokir !!\")' class='btn default btn-xs yellow'>
															<i class='fa fa-edit'></i> Penutupan
														</a>
													</td>
												";
											}
											echo "
											
										</tr>
									";
									$no++;
								} 
							}
							
						?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
<?php echo form_close(); ?>