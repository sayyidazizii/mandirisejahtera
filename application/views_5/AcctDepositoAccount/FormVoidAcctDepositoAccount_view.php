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
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Form Void Rekening Simpanan Berjangka
</h3>
<?php
// print_r($acctdepositoaccount);
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
						<a href="<?php echo base_url();?>deposito-account" class="btn btn-default btn-sm">
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
									<input type="text" class="form-control" name="member_no" id="member_no" value="<?php echo $acctdepositoaccount['member_name'];?>" readonly/>
									<label class="control-label">Anggota</label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_no" id="member_no" value="<?php echo $acctdepositoaccount['deposito_name'];?>" readonly/>
									<label class="control-label">Jenis Simpanan Berjangka</label>
								</div>
							</div>
						</div>
						
						<div class="row">						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_no" id="member_no" value="<?php echo $acctdepositoaccount['member_no'];?>" readonly/>
									<label class="control-label">No. Anggota<span class="required">*</span></label>
								</div>
							</div>
						</div>

						<div class="row">						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_date_of_birth" id="member_date_of_birth" value="<?php echo tgltoview($acctdepositoaccount['member_date_of_birth']);?>" readonly/>
									<label class="control-label">Tanggal Lahir<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_gender" id="member_gender" value="<?php echo $membergender[$acctdepositoaccount['member_gender']];?>" readonly/>
									<label class="control-label">Jenis Kelamin<span class="required">*</span></label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">	
								<div class="form-group form-md-line-input">
									<?php echo form_textarea(array('rows'=>'3','name'=>'member_address','class'=>'form-control','id'=>'member_address','disabled'=>'disabled', 'value'=> $acctdepositoaccount['member_address']))?>
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
									<input type="text" class="form-control" name="city_name" id="city_name" value="<?php echo $this->AcctDepositoAccount_model->getCityName($acctdepositoaccount['city_id']);?>" readonly/>
									<label class="control-label">Kabupaten<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="kecamatan_name" id="kecamatan_name" value="<?php echo $this->AcctDepositoAccount_model->getKecamatanName($acctdepositoaccount['kecamatan_id']);?>" readonly/>
									<label class="control-label">Kecamatan<span class="required">*</span></label>
								</div>
							</div>
						</div>

						<div class="row">						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="job_name" id="job_name" value="<?php echo $acctdepositoaccount['member_job'];?>" readonly/>
									<label class="control-label">Pekerjaan<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_phone1" id="member_phone1" value="<?php echo $acctdepositoaccount['member_phone'];?>" readonly/>
									<label class="control-label">No. Telp<span class="required">*</span></label>
								</div>
							</div>
						</div>

						<div class="row">						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="identity_name" id="identity_name" value="<?php echo $memberidentity[$acctdepositoaccount['identity_id']];?>" readonly/>
									<label class="control-label">Identitas<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_identity_no" id="member_identity_no" value="<?php echo $acctdepositoaccount['member_identity_no'];?>" readonly/>
									<label class="control-label">No. Identitas<span class="required">*</span></label>
								</div>
							</div>
						</div>

						<h3> Detail Rekening Simpanan Berjangka Baru </h3>

						<div class="row">						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="deposito_period" id="deposito_period" value="<?php echo $acctdepositoaccount['deposito_account_period'];?>" readonly/>
									<label class="control-label">Jangka Waktu<span class="required">*</span></label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_no" id="member_no" value="<?php echo $acctdepositoaccount['savings_account_no'];?>" readonly/>
									<label class="control-label">No. Simpanan<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_no" id="member_no" value="<?php echo $this->AcctDepositoAccount_model->getOfficeName($acctdepositoaccount['office_id']);?>" readonly/>
									<label class="control-label">Ac. Officer<span class="required">*</span></label>
								</div>
							</div>
						</div>
						<div class="row">						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="deposito_account_date" id="deposito_account_date" autocomplete="off" value="<?php echo tgltoview($acctdepositoaccount['deposito_account_date']);?>" readonly/>
									<label class="control-label">Tanggal Buka<span class="required">*</span></label>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="deposito_account_no" id="deposito_account_no" value="<?php echo $acctdepositoaccount['deposito_account_no'];?>"  readonly/>
									<label class="control-label">No. SimpKa<span class="required">*</span></label>
								</div>
							</div>
						</div>
						<div class="row">						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="deposito_account_due_date" id="deposito_account_due_date" value="<?php echo tgltoview($acctdepositoaccount['deposito_account_due_date']);?>" readonly/>
									<label class="control-label">Tanggal Jatuh Tempo<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="deposito_account_serial_no" id="deposito_account_serial_no" value="<?php echo $acctdepositoaccount['deposito_account_serial_no'];?>" readonly/>
									<label class="control-label">No. Seri<span class="required">*</span></label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="deposito_account_nisbah" id="deposito_account_nisbah" value="<?php echo $acctdepositoaccount['deposito_account_nisbah'];?>" readonly/>
									<label class="control-label">Bagi Hasil<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="deposito_account_amount_view" id="deposito_account_amount_view" autocomplete="off" value="<?php echo number_format($acctdepositoaccount['deposito_account_amount'], 2);?>" readonly/>
									<label class="control-label">Nominal (Rp)<span class="required">*</span></label>
								</div>
							</div>
						</div>
						

						<div class="row">
							<div class="col-md-12" style='text-align:right'>
								<a class="btn default red" data-toggle="modal" href="#modalvoidacctdepositoaccount"><i class="fa fa-times"></i> Batal</a>
							</div>
						</div>
					</div>
				</div>
			 </div>
		</div>
	</div>
</div>

<?php echo form_open('deposito-account/process-void',array('id' => 'myform', 'class' => 'horizontal-form'));
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
	<div class="modal fade bs-modal-lg" id="modalvoidacctdepositoaccount" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
					<h4 class="modal-title">Membatalkan Rekening Simapan Berjangka Baru</h4>
				</div>
				<div class="modal-body">
					
					<div class="row">
						<div class="col-md-12">
							<label class="control-label">Keterangan</label>
							<div class="input-icon right">
								<i class="fa"></i>
								<?php echo form_textarea(array('rows'=>'3','name'=>'voided_remark','class'=>'form-control','id'=>'voided_remark','value'=>set_value('voided_remark',$acctdepositoaccount['voided_remark'])))?>
							</div>	
						</div>	
					</div>
					
					<input type="hidden" class="form-control" name="deposito_account_id" id="deposito_account_id"  value="<?php echo set_value('deposito_account_id',$acctdepositoaccount['deposito_account_id']);?>"/>

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
			