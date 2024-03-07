<script>
	base_url = '<?php echo base_url();?>';
	function ulang(){
		document.getElementById("company_code").value 			= "<?php echo $corecompany['company_code'] ?>";
		document.getElementById("company_name").value 			= "<?php echo $corecompany['company_name'] ?>";
		document.getElementById("company_city").value 			= "<?php echo $corecompany['company_city'] ?>";
	}
	$(document).ready(function(){
        $("#company_parent_id").change(function(){
		  var company_parent_id = $("#company_parent_id").val();
		  $.post(base_url + 'company/get-company-name',
		  {company_parent_id: company_parent_id},
				function(data) {
					$('#company_parent_name').val(data.company_name); 
				},
				'json'
			);
		});
    });
</script>
<?php echo form_open('company/process-edit',array('id' => 'myform', 'class' => 'horizontal-form')); ?>

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
			<a href="<?php echo base_url();?>company">
				Daftar Perusahaan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>company/edit/"<?php $this->uri->segment(3); ?>>
				Edit Perusahaan 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->
<h3 class="page-title">
	Form Edit Perusahaan 
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
						<div class="row">
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="company_code" id="company_code" value="<?php echo set_value('company_code',$corecompany['company_code']);?>"/>
									<label class="control-label">Kode Perusahaan<span class="required">*</span></label>
								</div>
							</div>
						
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="company_name" id="company_name" value="<?php echo set_value('company_name',$corecompany['company_name']);?>"/>
									<label class="control-label">Nama Perusahaan<span class="required">*</span></label>
								</div>
							</div>
							
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="company_mandatory_savings" id="company_mandatory_savings" value="<?php echo set_value('company_mandatory_savings',$corecompany['company_mandatory_savings']);?>"/>
									<label class="control-label">Iuran Wajib Karyawan<span class="required">*</span></label>
								</div>
							</div>
							
							<div class="col-md-12">
								<div class="form-group form-md-line-input">
									<textarea type="text" class="form-control" name="company_address" id="company_address" value=""><?php echo set_value('company_address',$corecompany['company_address']);?></textarea>
									<label class="control-label">Alamat</label>
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
<input type="hidden" class="form-control" name="company_id" id="company_id" placeholder="id" value="<?php echo set_value('company_id',$corecompany['company_id']);?>"/>
<?php echo form_close(); ?>