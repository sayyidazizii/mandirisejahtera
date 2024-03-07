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
		document.location = base_url = "deposito-profit-sharing-check/reset-search";
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
			<a href="<?php echo base_url();?>deposito-profit-sharing-check">
				Daftar Simpanan Berjangka Dapat Bunga
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
<h3 class="page-title">
	Daftar Simpanan Berjangka Dapat Bunga	
</h3>		<!-- END PAGE TITLE & BREADCRUMB-->

<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$auth 	= $this->session->userdata('auth');
	$sesi 	= $this->session->userdata('filter-acctdepositoprofitsharingcheck');

	if(!is_array($sesi)){
		$sesi['start_date']		= date('Y-m-d');
		$sesi['end_date']		= date('Y-m-d');
		$sesi['branch_id']		= $auth['branch_id'];
	}
?>	
<?php	echo form_open('deposito-profit-sharing-check/filter',array('id' => 'myform', 'class' => '')); 

	$start_date			= $sesi['start_date'];
	$end_date			= $sesi['end_date'];
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Daftar Simpanan Berjangka Dapat Bunga
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body form">
					 <div class = "row">
						<div class = "col-md-4">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="<?php echo tgltoview($start_date);?>"/>
								<label class="control-label">Mulai Tanggal
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>
						<div class = "col-md-4">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="<?php echo tgltoview($end_date);?>"/>
								<label class="control-label">Sampai Tanggal
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
					<table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
					<thead>
						<tr>
							<th width="5%">No</th>
							<th width="10%">No. Rek Dep</th>
							<th width="10%">No. Seri</th>
							<th width="10%">Rek Simp</th>
							<th width="15%">Nama</th>
							<th width="20%">Alamat</th>
							<th width="10%">Tgl Mulai</th>
							<th width="10%">Jt Tempo</th>
							<th width="10%">Tgl Bunga</th>
							<th width="10%">Saldo</th>
							<th width="10%">Bunga</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$no = 1;
							if(empty($acctdepositoprofitsharingcheck)){
								echo "
									<tr>
										<td colspan='11' align='center'>Emty Data</td>
									</tr>
								";
							} else {
								foreach ($acctdepositoprofitsharingcheck as $key=>$val){
									$date = date('Y-m-d');
									if($val['deposito_profit_sharing_status'] == 1){
										$warna = 'lightgrey';
									} else {
										$warna = 'white';
									}

									echo"
										<tr>			
											<td style='text-align:center;background-color:".$warna."'>$no.</td>
											<td style='background-color:".$warna."'>".$val['deposito_account_no']."</td>
											<td style='background-color:".$warna."'>".$val['deposito_account_serial_no']."</td>
											<td style='background-color:".$warna."'>".$val['savings_account_no']."</td>
											<td style='background-color:".$warna."'>".$val['member_name']."</td>
											<td style='background-color:".$warna."'>".$val['member_address']."</td>
											<td style='background-color:".$warna."'>".tgltoview($val['deposito_account_date'])."</td>
											<td style='background-color:".$warna."'>".tgltoview($val['deposito_account_due_date'])."</td>
											<td style='background-color:".$warna."'>".tgltoview($val['deposito_profit_sharing_due_date'])."</td>
											<td style='text-align:right;background-color:".$warna."'>".number_format($val['deposito_account_last_balance'])."</td>
											<td style='text-align:right;background-color:".$warna."'>".number_format($val['deposito_profit_sharing_amount'])."</td>";
											if($val['deposito_profit_sharing_status'] == 0 && $val['deposito_account_status'] == 0){
												echo "
													<td>
																	
														<a href='".$this->config->item('base_url').'deposito-profit-sharing-check/add/'.$val['deposito_profit_sharing_id']."'class='btn default btn-xs blue'>
															<i class='fa fa-plus'></i> Proses Bunga
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