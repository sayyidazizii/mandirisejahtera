<script>
	base_url = '<?php echo base_url();?>';
	function ulang(){
		document.getElementById("identity_code").value 			= "<?php echo $coreidentity['identity_code'] ?>";
		document.getElementById("identity_name").value 			= "<?php echo $coreidentity['identity_name'] ?>";
		
	}
	
</script>
<?php echo form_open('CoreIdentity/processEditCoreIdentity',array('id' => 'myform', 'class' => 'horizontal-form')); ?>

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
			<a href="<?php echo base_url();?>CoreIdentity">
				Daftar Identitas
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>CoreIdentity/editCoreIdentity/"<?php $this->uri->segment(3); ?>>
				Edit Identitas 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->
<h3 class="page-title">
	Form Edit Identitas 
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
						<a href="<?php echo base_url();?>CoreIdentity" class="btn btn-default btn-sm">
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
									<input type="text" class="form-control" name="identity_code" id="identity_code" value="<?php echo set_value('identity_code',$coreidentity['identity_code']);?>"/>
									<label class="control-label">Kode Identitas<span class="required">*</span></label>
								</div>
							</div>
						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="identity_name" id="identity_name" value="<?php echo set_value('identity_name',$coreidentity['identity_name']);?>"/>
									<label class="control-label">Nama Identitas<span class="required">*</span></label>
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
<input type="hidden" class="form-control" name="identity_id" id="identity_id" placeholder="id" value="<?php echo set_value('identity_id',$coreidentity['identity_id']);?>"/>
<?php echo form_close(); ?>