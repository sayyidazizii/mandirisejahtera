<script>
	base_url = '<?php echo base_url();?>';
	function ulang(){
		document.getElementById("job_code").value 			= "";
		document.getElementById("job_name").value 			= "";
	}

</script>
<?php echo form_open('CoreJob/processAddCoreJob',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
	$data = $this->session->userdata('addCoreJob');
?>

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
			<a href="<?php echo base_url();?>CoreJob/addCoreJob">
				Tambah Pekerjaan
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Form Tambah Pekerjaan
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
						Form Tambah
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
									<input type="text" class="form-control" name="job_code" id="job_code" autocomplete="off" value="<?php echo set_value('job_code',$data['job_code']); ?>"/>
									<label class="control-label">Kode Pekerjaan<span class="required">*</span></label>
								</div>
							</div>
						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="job_name" id="job_name" autocomplete="off" value="<?php echo set_value('job_name',$data['job_name']);?>"/>
									<label class="control-label">Nama Pekerjaan<span class="required">*</span></label>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-12" style='text-align:right'>
								<button type="reset" name="Reset" value="Batal" class="btn btn-danger" onClick="ulang();"><i class="fa fa-times"> Batal</i></button>
								<button type="submit" name="Save" value="Simpan" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Simpan</i></button>
							</div>	
						</div>
					</div>
				</div>
			 </div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>