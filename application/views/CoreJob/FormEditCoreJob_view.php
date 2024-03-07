<script>
	base_url = '<?php echo base_url();?>';
	function ulang(){
		document.getElementById("job_code").value 			= "<?php echo $corejob['job_code'] ?>";
		document.getElementById("job_name").value 			= "<?php echo $corejob['job_name'] ?>";
		
	}
	
</script>
<?php echo form_open('CoreJob/processEditCoreJob',array('id' => 'myform', 'class' => 'horizontal-form')); ?>

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
			<a href="<?php echo base_url();?>CoreJob">
				Daftar Pekerjaan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>CoreJob/editCoreJob/"<?php $this->uri->segment(3); ?>>
				Edit Pekerjaan 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->
<h3 class="page-title">
	Form Edit Pekerjaan 
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
						<a href="<?php echo base_url();?>CoreJob" class="btn btn-default btn-sm">
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
									<input type="text" class="form-control" name="job_code" id="job_code" value="<?php echo set_value('job_code',$corejob['job_code']);?>"/>
									<label class="control-label">Kode Pekerjaan<span class="required">*</span></label>
								</div>
							</div>
						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="job_name" id="job_name" value="<?php echo set_value('job_name',$corejob['job_name']);?>"/>
									<label class="control-label">Nama Pekerjaan<span class="required">*</span></label>
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
<input type="hidden" class="form-control" name="job_id" id="job_id" placeholder="id" value="<?php echo set_value('job_id',$corejob['job_id']);?>"/>
<?php echo form_close(); ?>