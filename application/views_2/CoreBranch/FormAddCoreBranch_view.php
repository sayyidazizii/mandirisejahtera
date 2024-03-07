<script>
	base_url = '<?php echo base_url();?>';
	function ulang(){
		document.getElementById("branch_code").value 			= "";
		document.getElementById("branch_name").value 			= "";
		document.getElementById("branch_address").value 		= "";
		document.getElementById("branch_city").value 			= "";
		document.getElementById("branch_contact_person").value 	= "";
		document.getElementById("branch_email").value 			= "";
		document.getElementById("branch_phone1").value 			= "";
		document.getElementById("branch_parent_id").value 		= "";
		document.getElementById("branch_parent_name").value 	= "";
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
<?php echo form_open('branch/process-add',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
	$data = $this->session->userdata('addCoreBranch');
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
			<a href="<?php echo base_url();?>branch">
				Daftar Cabang
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>branch/add">
				Tambah Cabang
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Form Tambah Cabang
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
									<input type="text" class="form-control" name="branch_code" id="branch_code" autocomplete="off" value="<?php echo set_value('branch_code',$data['branch_code']); ?>"/>
									<label class="control-label">Kode Cabang<span class="required">*</span></label>
								</div>
							</div>
						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="branch_name" id="branch_name" autocomplete="off" value="<?php echo set_value('branch_name',$data['branch_name']);?>"/>
									<label class="control-label">Nama Cabang<span class="required">*</span></label>
								</div>
							</div>
						</div>
						
						<div class="row">


							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="branch_email" id="branch_email" placeholder="Name" autocomplete="off" value="<?php echo set_value('branch_email',$data['branch_email']);?>"/>
									<label class="control-label">Email<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="branch_city" id="branch_city" placeholder="Name" autocomplete="off" value="<?php echo set_value('branch_city',$data['branch_city']);?>"/>
									<label class="control-label">Kota<span class="required">*</span></label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="branch_manager" id="branch_manager" placeholder="Name" autocomplete="off" value="<?php echo set_value('branch_manager',$data['branch_manager']);?>"/>
									<label class="control-label">Kepala Manager<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">	
								<div class="form-group form-md-line-input">
									<?php echo form_textarea(array('rows'=>'3','name'=>'branch_address','class'=>'form-control','id'=>'branch_address','value'=>set_value('branch_address',$data['branch_address'])))?>
									<label class="control-label">Alamat</label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="branch_contact_person" id="branch_contact_person"  autocomplete="off" value="<?php echo set_value('branch_contact_person',$data['branch_contact_person']); ?>"/>
									<label class="control-label">Orang yang dapat dihubungi<span class="required">*</span></label>
								</div>
							</div>
						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="branch_phone1" id="branch_phone1" autocomplete="off" value="<?php echo set_value('branch_phone1',$data['branch_phone1']);?>"/>
									<label class="control-label">No. Telp<span class="required">*</span></label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<?php echo form_dropdown('account_rak_id', $acctaccount, set_value('account_rak_id',$data['account_rak_id']),'id="account_rak_id" class="form-control select2me"');?>
									<label class="control-label">RAK Cabang<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<?php echo form_dropdown('account_aka_id', $acctaccount, set_value('account_aka_id',$data['account_aka_id']),'id="account_aka_id" class="form-control select2me"');?>
									<label class="control-label">AKA Cabang<span class="required">*</span></label>
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