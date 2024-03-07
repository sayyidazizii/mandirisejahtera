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
			<a href="<?php echo base_url();?>savings-bank-mutation">
				Daftar Mutasi Setoran Tabungan Non Tunai
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Form Pembatalan  Mutasi Setoran Tabungan Non Tunai
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
						Form Detail
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>savings-bank-mutation" class="btn btn-default btn-sm">
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
									<input type="text" class="form-control" name="savings_account_no" id="savings_account_no" autocomplete="off" value="<?php echo $acctsavingsbankmutation['savings_account_no']; ?>" readonly/>
									<label class="control-label">No. Rekening</label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="bank_account_name" id="bank_account_name" autocomplete="off" value="<?php echo $acctsavingsbankmutation['bank_account_name'] .' - '. $acctsavingsbankmutation['bank_account_name']; ?>" readonly/>
									<label class="control-label">Transfer Bank</label>
								</div>
							</div>
						</div>

						<div class="row">	
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="savings_name" id="savings_name" autocomplete="off" value="<?php echo $acctsavingsbankmutation['savings_name']; ?>" readonly/>
									<label class="control-label">Jenis Tabungan<span class="required">*</span></label>
								</div>
							</div>					
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_name" id="member_name" autocomplete="off" value="<?php echo $acctsavingsbankmutation['member_name']; ?>" readonly/>
									<label class="control-label">Nama Anggota<span class="required">*</span></label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">	
								<div class="form-group form-md-line-input">
									<?php echo form_textarea(array('rows'=>'3','name'=>'member_address','class'=>'form-control','id'=>'member_address','disabled'=>'disabled','value' => $acctsavingsbankmutation['member_address']))?>
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
									<input type="text" class="form-control" name="city_name" id="city_name" autocomplete="off" value="<?php echo $acctsavingsbankmutation['city_name']; ?>" readonly/>
									<label class="control-label">Kabupaten<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="kecamatan_name" id="kecamatan_name" autocomplete="off" value="<?php echo $acctsavingsbankmutation['kecamatan_name']; ?>" readonly/>
									<label class="control-label">Kecamatan<span class="required">*</span></label>
								</div>
							</div>
						</div>

						<div class="row">						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="identity_name" id="identity_name" autocomplete="off" value="<?php echo $acctsavingsbankmutation['identity_name']; ?>" readonly/>
									<label class="control-label">Identitas<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_identity_no" id="member_identity_no" autocomplete="off" value="<?php echo $acctsavingsbankmutation['member_identity_no']; ?>" readonly/>
									<label class="control-label">No. Identitas<span class="required">*</span></label>
								</div>
							</div>
						</div>

						<h3> Detail Mutasi Baru </h3>

						<div class="row">						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="savings_bank_mutation_opening_balance" id="savings_bank_mutation_opening_balance" value="<?php echo number_format($acctsavingsbankmutation['savings_bank_mutation_opening_balance'], 2); ?>" autocomplete="off" readonly/>
									<label class="control-label">Saldo Lama<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="savings_bank_mutation_last_balance" id="savings_bank_mutation_last_balance" value="<?php echo number_format($acctsavingsbankmutation['savings_bank_mutation_last_balance'], 2); ?>" autocomplete="off" readonly/>
									<label class="control-label">Saldo Baru<span class="required">*</span></label>
								</div>
							</div>
						</div>

						<div class="row">						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="savings_bank_mutationdate" id="savings_bank_mutation_date" autocomplete="off" value="<?php echo tgltoview($acctsavingsbankmutation['savings_bank_mutation_date']); ?>" readonly/>
									<label class="control-label">Tanggal Transaksi<span class="required">*</span></label>
								</div>
							</div>
							
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="savings_bank_mutation_amount" id="savings_bank_mutation_amount" autocomplete="off" value="<?php echo number_format($acctsavingsbankmutation['savings_bank_mutation_amount'], 2); ?>" readonly/>
									<label class="control-label">Jumlah (Rp)<span class="required">*</span></label>
								</div>
							</div>
						</div>
						

						<div class="row">
							<div class="col-md-12" style='text-align:right'>
								<a class="btn default red" data-toggle="modal" href="#modalvoidacctsavingsbankmutation"><i class="fa fa-times"></i> Batal</a>
							</div>
						</div>
					</div>
				</div>
			 </div>
		</div>
	</div>
</div>

<?php echo form_open('savings-bank-mutation/process-void',array('id' => 'myform', 'class' => 'horizontal-form'));
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
	<div class="modal fade bs-modal-lg" id="modalvoidacctsavingsbankmutation" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
					<h4 class="modal-title">Membatalkan Setoran Tabungan Non Tunai</h4>
				</div>
				<div class="modal-body">
					
					<div class="row">
						<div class="col-md-12">
							<label class="control-label">Keterangan</label>
							<div class="input-icon right">
								<i class="fa"></i>
								<?php echo form_textarea(array('rows'=>'3','name'=>'voided_remark','class'=>'form-control','id'=>'voided_remark','value'=>set_value('voided_remark',$acctsavingsbankmutation['voided_remark'])))?>
							</div>	
						</div>	
					</div>
					
					<input type="hidden" class="form-control" name="savings_bank_mutation_id" id="savings_bank_mutation_id"  value="<?php echo set_value('savings_bank_mutation_id',$acctsavingsbankmutation['savings_bank_mutation_id']);?>"/>

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
			
