<script>
	base_url = '<?php echo base_url();?>';
	function ulang(){
		document.getElementById("kelurahan_id").value 			= "<?php echo $coredusun['kelurahan_id'] ?>";
		document.getElementById("dusun_name").value 			= "<?php echo $coredusun['dusun_name'] ?>";
	}

</script>
<?php echo form_open('dusun/process-edit',array('id' => 'myform', 'class' => 'horizontal-form')); ?>

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
			<a href="<?php echo base_url();?>CoreDusun">
				Daftar Dusun
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>dusun/edit/"<?php $this->uri->segment(3); ?>>
				Edit Dusun 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->
<h3 class="page-title">
	Form Edit Dusun 
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
						<a href="<?php echo base_url();?>CoreDusun" class="btn btn-default btn-sm">
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
									<?php echo form_dropdown('kelurahan_id', $corekelurahan, set_value('kelurahan_id',$coredusun['kelurahan_id']),'id="kelurahan_id" class="form-control select2me"');?>
									<label class="control-label">Kelurahan<span class="required">*</span></label>
								</div>
							</div>
						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="dusun_name" id="dusun_name" value="<?php echo set_value('dusun_name',$coredusun['dusun_name']);?>"/>
									<label class="control-label">Nama Dusun<span class="required">*</span></label>
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
<input type="hidden" class="form-control" name="dusun_id" id="dusun_id" placeholder="id" value="<?php echo set_value('dusun_id',$coredusun['dusun_id']);?>"/>
<?php echo form_close(); ?>