<script>
	base_url = '<?php echo base_url();?>';
	function ulang(){
		document.getElementById("branch_code").value 			= "<?php echo $corebranch['branch_code'] ?>";
		document.getElementById("branch_name").value 			= "<?php echo $corebranch['branch_name'] ?>";
		document.getElementById("branch_city").value 			= "<?php echo $corebranch['branch_city'] ?>";
		document.getElementById("branch_address").value 		= "<?php echo $corebranch['branch_address'] ?>";
		document.getElementById("branch_contact_person").value 	= "<?php echo $corebranch['branch_contact_person'] ?>";
		document.getElementById("branch_email").value 			= "<?php echo $corebranch['branch_email'] ?>";
		document.getElementById("branch_phone1").value 			= "<?php echo $corebranch['branch_phone1'] ?>";
		document.getElementById("branch_parent_id").value 		= "<?php echo $corebranch['branch_parent_id'] ?>";
		document.getElementById("branch_parent_name").value 	= "<?php echo $corebranch['branch_parent_name'] ?>";
	}
	$(document).ready(function(){
        $("#branch_parent_id").change(function(){
		  var branch_parent_id = $("#branch_parent_id").val();
		  $.post(base_url + 'branch/get-branch-name',
		  {branch_parent_id: branch_parent_id},
				function(data) {
					$('#branch_parent_name').val(data.branch_name); 
				},
				'json'
			);
		});
    });
</script>
<?php echo form_open('branch/process-edit',array('id' => 'myform', 'class' => 'horizontal-form')); ?>

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
			<a href="<?php echo base_url();?>branch">
				Daftar Cabang
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>branch/edit/"<?php $this->uri->segment(3); ?>>
				Edit Cabang 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->
<h3 class="page-title">
	Form Edit Cabang 
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
						<a href="<?php echo base_url();?>branch" class="btn btn-default btn-sm">
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
									<input type="text" class="form-control" name="branch_code" id="branch_code" value="<?php echo set_value('branch_code',$corebranch['branch_code']);?>"/>
									<label class="control-label">Kode Cabang<span class="required">*</span></label>
								</div>
							</div>
						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="branch_name" id="branch_name" value="<?php echo set_value('branch_name',$corebranch['branch_name']);?>"/>
									<label class="control-label">Nama Cabang<span class="required">*</span></label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="branch_email" id="branch_email" autocomplete="off" value="<?php echo set_value('branch_email',$corebranch['branch_email']);?>"/>
									<label class="control-label">Email<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="branch_city" id="branch_city" autocomplete="off" value="<?php echo set_value('branch_city',$corebranch['branch_city']);?>"/>
									<label class="control-label">Kota<span class="required">*</span></label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="branch_manager" id="branch_manager" autocomplete="off" value="<?php echo set_value('branch_manager',$corebranch['branch_manager']);?>"/>
									<label class="control-label">Kepala Manager<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">	
								<div class="form-group form-md-line-input">
									<?php echo form_textarea(array('rows'=>'3','name'=>'branch_address','class'=>'form-control','id'=>'branch_address','value'=>set_value('branch_address',$corebranch['branch_address'])))?>
									<label class="control-label">Alamat</label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="branch_contact_person" id="branch_contact_person" autocomplete="off" value="<?php echo set_value('branch_contact_person',$corebranch['branch_contact_person']); ?>"/>
									<label class="control-label">Orang yang dapat dihubungi<span class="required">*</span></label>
								</div>
							</div>
						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="branch_phone1" id="branch_phone1"  autocomplete="off" value="<?php echo set_value('branch_phone1',$corebranch['branch_phone1']);?>"/>
									<label class="control-label">No. Telp<span class="required">*</span></label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<?php echo form_dropdown('account_rak_id', $acctaccount, set_value('account_rak_id',$corebranch['account_rak_id']),'id="account_rak_id" class="form-control select2me"');?>
									<label class="control-label">RAK Cabang<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<?php echo form_dropdown('account_aka_id', $acctaccount, set_value('account_aka_id',$corebranch['account_aka_id']),'id="account_aka_id" class="form-control select2me"');?>
									<label class="control-label">AKA Cabang<span class="required">*</span></label>
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
<input type="hidden" class="form-control" name="branch_id" id="branch_id" placeholder="id" value="<?php echo set_value('branch_id',$corebranch['branch_id']);?>"/>
<?php echo form_close(); ?>