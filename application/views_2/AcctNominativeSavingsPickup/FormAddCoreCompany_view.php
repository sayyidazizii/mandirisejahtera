<script>
	
    function function_elements_add(name, value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('company/elements-add');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
			}
		});
	}
	
	function function_state_add(value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('company/state-add');?>",
				data : {'value' : value},
				success: function(msg){
			}
		});
	}

	function reset_add(){
		document.location = "<?php echo base_url();?>company/reset-add";
	}

	
</script>

<?php echo form_open('company/process-add',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
	
	$unique 	= $this->session->userdata('unique');
	$data 		= $this->session->userdata('addcorecompany-'.$unique['unique']);

	if(empty($data['company_name'])){
		$data['company_name']='';
	}
	if(empty($data['company_email'])){
		$data['company_email']='';
	}
	if(empty($data['company_address'])){
		$data['company_address']='';
	}
	if(empty($data['company_phone_number'])){
		$data['company_phone_number']='';
	}
	if(empty($data['company_mobile_number'])){
		$data['company_mobile_number']='';
	}
	if(empty($data['company_contact_person'])){
		$data['company_contact_person']='';
	}
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
					<a href="<?php echo base_url();?>company/add">
						Tambah Perusahaan
					</a>
				</li>
			</ul>
		</div>
		<h3 class="page-title">
			Form Tambah Perusahaan	
		</h3>

<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Form Tambah
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
								<input type="text" name="company_name" id="company_name" value="<?php echo $data['company_name'] ?>"  class="form-control" onChange="function_elements_add(this.name, this.value);">
								<label class="control-label">Nama Perusahaan
								<span class="required">*</span></label>
							</div>
						</div>
					
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<input type="text" name="company_email" id="company_email" value="<?php echo $data['company_email'] ?>"  class="form-control" onChange="function_elements_add(this.name, this.value);">
								<label class="control-label">Email Perusahaan
								<span class="required">*</span></label>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<input type="text" name="company_address" id="company_address" value="<?php echo $data['company_address'] ?>"  class="form-control" onChange="function_elements_add(this.name, this.value);">
								<label class="control-label">Alamat Perusahaan
								<span class="required">*</span></label>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<input type="text" name="company_phone_number" id="company_phone_number" value="<?php echo $data['company_phone_number'] ?>"  class="form-control" onChange="function_elements_add(this.name, this.value);">
								<label class="control-label">Nomor Telepon
								<span class="required">*</span></label>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<input type="text" name="company_mobile_number" id="company_mobile_number" value="<?php echo $data['company_mobile_number'] ?>"  class="form-control" onChange="function_elements_add(this.name, this.value);">
								<label class="control-label">Nomor Hp
								<span class="required">*</span></label>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<input type="text" name="company_contact_person" id="company_contact_person" value="<?php echo $data['company_contact_person'] ?>"  class="form-control" onChange="function_elements_add(this.name, this.value);">
								<label class="control-label">Contact Person
								<span class="required">*</span></label>
							</div>
						</div>
					</div>	
					
					<div class="row">
						<div class="col-md-12 " style="text-align  : right !important;">
							<button type="button" class="btn red" onClick="reset_add();"><i class="fa fa-times"></i> Batal</button>
							<button type="submit" class="btn green-jungle"><i class="fa fa-check"></i> Simpan</button>
						</div>
					</div>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>