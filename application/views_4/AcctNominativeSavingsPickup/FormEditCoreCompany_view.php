<script>	
	function reset_edit(){
		document.location = "<?php echo base_url();?>company/reset-edit/<?php echo $corecompany['company_id']?>";
	}
</script>
<?php 
	echo form_open('company/process-edit',array('id' => 'myform', 'class' => 'horizontal-form')); 
?>

		<!-- BEGIN PAGE TITLE & BREADCRUMB-->
		<div class = "page-bar">
			<ul class="page-breadcrumb">
				<li>
					<a href="<?php echo base_url();?>">
						Beranda
					</a>
					<i class="fa fa-angle-right"></i>
				</li>
				<li>
					<a href="<?php echo base_url();?>company">
						Daftar Perusahaan
					</a>
					<i class="fa fa-angle-right"></i>
				</li>
				<li>
					<a href="<?php echo base_url();?>company/edit/<?php echo $corecompany['company_id']; ?>">
						Ubah Perusahaan
					</a>
					<i class="fa fa-angle-right"></i>
				</li>
			</ul>
		</div>
		<h3 class="page-title">
			Form Ubah Produk	
		</h3>
	
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Form Ubah
				</div>
				<div class="actions">
					<a href="<?php echo base_url();?>company" class="btn btn-default btn-sm">
						<i class="fa fa-angle-left"></i>
						<span class="hidden-480">
							Kembali
						</span>
					</a>
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body">					
					<?php
						echo $this->session->userdata('message');
						$this->session->unset_userdata('message');
					?>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<input type="text" name="company_name" id="company_name" value="<?php echo $corecompany['company_name'] ?>"  class="form-control" >
								<label class="control-label">Nama Perusahaan
								<span class="required">*</span></label>
							</div>
						</div>
					
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<input type="text" name="company_email" id="company_email" value="<?php echo $corecompany['company_email'];?>" class="form-control" >
								<label class="control-label">Email
								<span class="required">*</span></label>
								
						</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<input type="text" name="company_address" id="company_address" value="<?php echo $corecompany['company_address'] ?>"  class="form-control">
								<label class="control-label">Alamat
								<span class="required">*</span></label>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<input type="text" name="company_phone_number" id="company_phone_number" value="<?php echo $corecompany['company_phone_number'] ?>"  class="form-control" >
								<label class="control-label">No Telepon
								<span class="required">*</span></label>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<input type="text" name="company_mobile_number" id="company_mobile_number" value="<?php echo $corecompany['company_mobile_number'];?>" class="form-control" >
								<label class="control-label">No Hp
								<span class="required">*</span></label>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<input type="text" name="company_contact_person" id="company_contact_person" value="<?php echo $corecompany['company_contact_person'] ?>"  class="form-control">
								<label class="control-label">Contact Person
								<span class="required">*</span></label>
							</div>
						</div>
					</div>	
					
					<div class="row">
						<div class="col-md-12 " style="text-align  : right !important;">
							<button type="button" class="btn red" onClick="reset_edit();"><i class="fa fa-times"></i> Batal</button>
							<button type="submit" class="btn green-jungle"><i class="fa fa-check"></i> Simpan</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		</div>
	</div>
</div>
<input type="hidden" name="company_id" value="<?php echo $corecompany['company_id']; ?>"/>
<?php echo form_close(); ?>