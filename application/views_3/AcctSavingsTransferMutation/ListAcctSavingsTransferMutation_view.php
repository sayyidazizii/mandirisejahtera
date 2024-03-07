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
		document.location = base_url = "savings-transfer-mutation/reset-search";
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
			<a href="<?php echo base_url();?>savings-transfer-mutation">
				Daftar Mutasi Transfer Antar Rekening
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$sesi=$this->session->userdata('filter-acctsavingstransfermutation');

	if(!is_array($sesi)){
		$sesi['start_date']					= date('Y-m-d');
		$sesi['end_date']					= date('Y-m-d');
		$sesi['savings_account_from_id']	= '';
		$sesi['savings_account_to_id']		= '';
	}
?>	
<?php	echo form_open('savings-transfer-mutation/filter',array('id' => 'myform', 'class' => '')); 

	$start_date			= $sesi['start_date'];
	$end_date			= $sesi['end_date'];
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Daftar Mutasi Transfer Antar Rekening
				</div>
				<div class="actions">
					<a href="<?php echo base_url();?>savings-transfer-mutation/add" class="btn btn-default btn-sm">
						<i class="fa fa-plus"></i>
						<span class="hidden-480">
							Input Mutasi Tabungan Antar Rekening Baru
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

						<!-- <div class="col-md-4">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('savings_account_from_id', $acctsavingsaccount,set_value('savings_account_id',$sesi['savings_account_id']),'id="savings_account_from_id" class="form-control select2me"');
								?>
								<label>No. Rekening Asal</label>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('savings_account_to_id', $acctsavingsaccount,set_value('savings_account_id',$sesi['savings_account_id']),'id="savings_account_to_id" class="form-control select2me"');
								?>
								<label>No. Rekening Tujuan</label>
							</div>
						</div> -->
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
							<th width="15%">Tanggal Transfer</th>
							<th width="20%">No. Rek Asal</th>
							<th width="20%">No. Rek Tujuan</th>
							<th width="15%">Jumlah Transfer</th>
							<th width="10%">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$no = 1;
							if(empty($acctsavingstransfermutation)){
								echo "
									<tr>
										<td colspan='6' align='center'>Emty Data</td>
									</tr>
								";
							} else {
								foreach ($acctsavingstransfermutation as $key=>$val){
									$savingsaccountfrom = $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutationFrom($val['savings_transfer_mutation_id']);	

									$savingsaccountto 	= $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutationTo($val['savings_transfer_mutation_id']);

									echo"
										<tr>			
											<td style='text-align:center'>$no.</td>
											<td>".tgltoview($val['savings_transfer_mutation_date'])."</td>
											<td>".$this->AcctSavingsTransferMutation_model->getSavingsAccountNo($savingsaccountfrom['savings_account_id'])." - ".$this->AcctSavingsTransferMutation_model->getMemberName($savingsaccountfrom['member_id'])."</td>
											<td>".$this->AcctSavingsTransferMutation_model->getSavingsAccountNo($savingsaccountto['savings_account_id'])." - ".$this->AcctSavingsTransferMutation_model->getMemberName($savingsaccountto['member_id'])."</td>
											<td>".number_format($val['savings_transfer_mutation_amount'])."</td>
											<td>";
												if($val['validation'] == 0){
													echo "
														<a href='".$this->config->item('base_url').'savings-transfer-mutation/validation/'.$val['savings_transfer_mutation_id']."' class='btn default btn-xs green-jungle'>
															<i class='fa fa-check'></i> Validasi
														</a>
													";
												}
												
												echo "
											</td>
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