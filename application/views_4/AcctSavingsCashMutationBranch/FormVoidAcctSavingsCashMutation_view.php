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
			<a href="<?php echo base_url();?>savings-cash-mutation-branch">
				Daftar Mutasi Tabungan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Form Pembatalan Mutasi Tabungan
</h3>
<?php
// print_r($data);
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			 <div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Form Tambah
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>savings-cash-mutation-branch" class="btn btn-default btn-sm">
							<i class="fa fa-angle-left"></i>
							<span class="hidden-480">
								Kembali
							</span>
						</a>
					</div>
				</div>
				<div class="portlet-body">
					<div class="form-body">
						<div class="row">						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="savings_name" id="savings_name" value="<?php echo $acctsavingscashmutation['savings_account_no']; ?>" readonly/>
									<label class="control-label">No. Rekening</label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="savings_name" id="savings_name" value="<?php echo $acctsavingscashmutation['mutation_name']; ?>" readonly/>
									<label class="control-label">Sandi</label>
								</div>
							</div>
						</div>

						<div class="row">	
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="savings_name" id="savings_name" value="<?php echo $acctsavingscashmutation['savings_name']; ?>" readonly/>
									<label class="control-label">Jenis Tabungan<span class="required">*</span></label>
								</div>
							</div>					
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_name" id="member_name" value="<?php echo $acctsavingscashmutation['member_name']; ?>" readonly/>
									<label class="control-label">Nama Anggota<span class="required">*</span></label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">	
								<div class="form-group form-md-line-input">
									<?php echo form_textarea(array('rows'=>'3','name'=>'member_address','class'=>'form-control','id'=>'member_address','disabled'=>'disabled', 'value'=> $acctsavingscashmutation['member_address']))?>
									<label class="control-label">Alamat
										<span class="required">
											*
										</span>
									</label>

								</div>
							</div>
						</div>

						<div class="row">						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="city_name" id="city_name" value="<?php echo $this->AcctSavingsCashMutation_model->getCityName($acctsavingscashmutation['city_id']); ?>" readonly/>
									<label class="control-label">Kabupaten<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="kecamatan_name" id="kecamatan_name" value="<?php echo $this->AcctSavingsCashMutation_model->getKecamatanName($acctsavingscashmutation['kecamatan_id']); ?>" readonly/>
									<label class="control-label">Kecamatan<span class="required">*</span></label>
								</div>
							</div>
						</div>

						<div class="row">						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="identity_name" id="identity_name" value="<?php echo $memberidentity[$acctsavingscashmutation['identity_id']]; ?>" readonly/>
									<label class="control-label">Identitas<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_identity_no" id="member_identity_no" value="<?php echo $acctsavingscashmutation['member_identity_no']; ?>" readonly/>
									<label class="control-label">No. Identitas<span class="required">*</span></label>
								</div>
							</div>
						</div>

						<h3> Detail Mutasi Baru </h3>

						<div class="row">						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="savings_cash_mutation_opening_balance" id="savings_cash_mutation_opening_balance" value="<?php echo number_format($acctsavingscashmutation['savings_cash_mutation_opening_balance'], 2); ?>" readonly/>
									<label class="control-label">Saldo Lama<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="savings_cash_mutation_last_balance" id="savings_cash_mutation_last_balance" value="<?php echo number_format($acctsavingscashmutation['savings_cash_mutation_last_balance'], 2); ?>" readonly/>
									<label class="control-label">Saldo Baru<span class="required">*</span></label>
								</div>
							</div>
						</div>

						<div class="row">						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="savings_cash_mutation_date" id="savings_cash_mutation_date" value="<?php echo tgltoview($acctsavingscashmutation['savings_cash_mutation_date']); ?>" readonly/>
									<label class="control-label">Tanggal Transaksi<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="savings_cash_mutation_amount" id="savings_cash_mutation_amount" value="<?php echo number_format($acctsavingscashmutation['savings_cash_mutation_amount'], 2); ?>" readonly/>
									<label class="control-label">Jumlah (Rp)<span class="required">*</span></label>
								</div>
							</div>
						</div>
						

						<div class="row">
							<div class="col-md-12" style='text-align:right'>
								<a class="btn default red" data-toggle="modal" href="#modalvoidacctsavingscashmutation"><i class="fa fa-times"></i> Batal</a>
							</div>
						</div>
					</div>
				</div>
			 </div>
		</div>
	</div>
</div>

<?php echo form_open('savings-cash-mutation/process-void',array('id' => 'myform', 'class' => 'horizontal-form'));
?>
<script>
	$(document).ready(function(){
        $("#save").click(function(){
			var voided_remark = $("#voided_remark").val();
			
		  	if(voided_remark!=''){
				return true;
			}else{
				alert('Isikan Keterangan');
				return false;
			}
		});
    });
</script>
	<!-- /.modal -->
	<div class="modal fade bs-modal-lg" id="modalvoidacctsavingscashmutation" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
					<h4 class="modal-title">Membatalkan Mutasi Tabungan Tunai</h4>
				</div>
				<div class="modal-body">
					
					<div class="row">
						<div class="col-md-12">
							<label class="control-label">Keterangan</label>
							<div class="input-icon right">
								<i class="fa"></i>
								<?php echo form_textarea(array('rows'=>'3','name'=>'voided_remark','class'=>'form-control','id'=>'voided_remark','value'=>set_value('voided_remark',$acctsavingscashmutation['voided_remark'])))?>
							</div>	
						</div>	
					</div>
					
					<input type="hidden" class="form-control" name="savings_cash_mutation_id" id="savings_cash_mutation_id"  value="<?php echo set_value('savings_cash_mutation_id',$acctsavingscashmutation['savings_cash_mutation_id']);?>"/>

					<div class="modal-footer">
						<button type="button" class="btn default" data-dismiss="modal">Batal</button>
						<button type="submit" id="save" class="btn green-jungle"><i class="fa fa-check"></i> Simpan</button>
					</div>
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
	</div>
	<!-- /.modal -->
<?php
echo form_close(); 
?>
			
