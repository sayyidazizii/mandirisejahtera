<script>
	base_url = '<?php echo base_url();?>';
</script>
<?php echo form_open('credits-agunan/process-edit',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
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
			<a href="<?php echo base_url();?>credits-agunan">
				Daftar Agunan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>credits-agunan/edit/"<?php $this->uri->segment(3); ?>>
				Edit No. Rangka 
			</a>
		</li>
	</ul>
</div>
<h3 class="page-title">
	Form Edit No. Rangka 
</h3>
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>

<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Form Edit
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>credits-agunan" class="btn btn-default btn-sm">
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
									<input type="hidden" class="form-control" name="credits_agunan_id" id="credits_agunan_id" value="<?php echo set_value('credits_agunan_id',$creditsagunan['credits_agunan_id']);?>"/>
									<input type="text" class="form-control" name="credits_agunan_bpkb_no_rangka" id="credits_agunan_bpkb_no_rangka" value="<?php echo set_value('credits_agunan_bpkb_no_rangka',$creditsagunan['credits_agunan_bpkb_no_rangka']);?>"/>
									<label class="control-label">No. Rangka<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="hidden" class="form-control" name="credits_agunan_id" id="credits_agunan_id" value="<?php echo set_value('credits_agunan_id',$creditsagunan['credits_agunan_id']);?>"/>
									<input type="text" class="form-control" name="credits_agunan_bpkb_no_mesin" id="credits_agunan_bpkb_no_mesin" value="<?php echo set_value('credits_agunan_bpkb_no_mesin',$creditsagunan['credits_agunan_bpkb_no_mesin']);?>"/>
									<label class="control-label">No. Mesin<span class="required">*</span></label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12" style='text-align:right'>
								<button type="reset" name="Reset" value="Reset" class="btn btn-danger" onClick="ulang();"><i class="fa fa-times"> Batal</i></button>
								<button type="submit" name="Save" value="Save" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Simpan </i></button>
							</div>	
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>