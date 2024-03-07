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
		document.location = base_url +"journal-voucher/reset-search";
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
			<a href="<?php echo base_url();?>AcctJournalVoucher">
				Daftar Jurnal Umum
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
	$sesi 	= $this->session->userdata('filter-acctjournalvoucher');

	if(!is_array($sesi)){
		$sesi['start_date']			= date('Y-m-d');
		$sesi['end_date']			= date('Y-m-d');
		$sesi['branch_id']			= '';
	}
?>	
<?php	echo form_open('journal-voucher/filter',array('id' => 'myform', 'class' => '')); 

	$start_date					= $sesi['start_date'];
	$end_date					= $sesi['end_date'];
	//$sesi['branch_id']			= '';

	// $a = md5('123456');
	// print_r($a);
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Daftar Jurnal Umum
				</div>
				<div class="actions">
					<a href="<?php echo base_url();?>journal-voucher/add" class="btn btn-default btn-sm">
						<i class="fa fa-plus"></i>
						<span class="hidden-480">
							Tambah Jurnal Umum Baru
						</span>
					</a>
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
					<div class="form-body form">
						<table class="table table-striped table-bordered table-hover table-full-width">
						<thead>
							<tr>
								<th width="5%">No</th>
								<th width="10%">Tanggal</th>
								<th width="15%">Uraian</th>
								<th width="15%">No. Perkiraan</th>
								<th width="15%">Nama Perkiraan</th>
								<th width="10%">Jumlah</th>
								<th width="15%">D/K</th>
								<th width="15%">Aksi</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$no = 1;
								if(empty($acctjournalvoucher)){
									echo "
										<tr>
											<td colspan='8' align='center'>Data Kosong</td>
										</tr>
									";
								} else {
									//print_r($acctjournalvoucher);
									foreach ($acctjournalvoucher as $key=>$val){	
										$id = $this->AcctJournalVoucher_model->getMinID($val['journal_voucher_id']);

										// if($val['journal_voucher_amount'] <> 0){
											if($val['journal_voucher_debit_amount'] <> 0 ){
												$nominal = $val['journal_voucher_debit_amount'];
												$status = "D";
											} else if($val['journal_voucher_credit_amount'] <> 0){
												$nominal = $val['journal_voucher_credit_amount'];
												$status = "K";
											} else {
												$nominal = 0;
												$status = 'Kosong';
											}
										// }
										


										if($val['journal_voucher_item_id'] == $id){
											echo"
												<tr>			
													<td style='text-align:center'>$no.</td>
													<td>".tgltoview($val['journal_voucher_date'])."</td>
													<td>".$val['journal_voucher_description']."</td>
													<td>".$val['account_code']."</td>
													<td>".$val['account_name']."</td>
													<td>".number_format($nominal, 2)."</td>
													<td>".$status."</td>
													<td>
														<a href='".$this->config->item('base_url').'journal-voucher/process-printing/'.$val['journal_voucher_id']."'class='btn default btn-xs blue'>
																	<i class='fa fa-print'></i> Cetak Bukti
																</a>
													</td>
												</tr>
											";
											$no++;
										} else {
											echo"
												<tr>			
													<td style='text-align:center'></td>
													<td></td>
													<td></td>
													<td>&nbsp;&nbsp;&nbsp;&nbsp;".$val['account_code']."</td>
													<td>&nbsp;&nbsp;&nbsp;&nbsp;".$val['account_name']."</td>
													<td>".number_format($nominal, 2)."</td>
													<td>".$status."</td>
												</tr>
											";
										}
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