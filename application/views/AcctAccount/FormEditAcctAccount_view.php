<script>
	base_url = '<?php echo base_url();?>';
	function ulang(){
		document.getElementById("account_code").value 			= "<?php echo $acctaccount['account_code']; ?>";
		document.getElementById("account_name").value 			= "<?php echo $acctaccount['account_name']; ?>";
		document.getElementById("account_group").value 			= "<?php echo $acctaccount['account_group']; ?>";
	}

	$(document).ready(function(){
        $("#Save").click(function(){
			var account_code = $("#account_code").val();
			var account_name = $("#account_name").val();
			var account_group = $("#account_group").val();
			
			if(account_code == ''){
				alert("Nomor Perkiraan masih kosong");
				return false;
			} else if(account_name == ''){
				alert("Nama Perkiraan masih kosong");
				return false;
			}else if(account_group == ''){
				alert("Golongan Perkiraan masih kosong");
				return false;
			} else {
				return true;
			}
		});
    });
</script>
<?php echo form_open('account/process-edit',array('id' => 'myform', 'class' => 'horizontal-form')); ?>

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
			<a href="<?php echo base_url();?>account">
				Daftar Perkiraan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>account/edit/"<?php $this->uri->segment(3); ?>>
				Edit Perkiraan 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->
<h3 class="page-title">
	Form Edit Perkiraan 
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
						<a href="<?php echo base_url();?>account/" class="btn btn-default btn-sm">
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
									<input type="text" class="form-control" id="account_code" name="account_code" autocomplete="off" value="<?php echo $acctaccount['account_code']; ?>">
									<input type="hidden" class="form-control" id="account_code_old" name="account_code_old" autocomplete="off" value="<?php echo $acctaccount['account_code']; ?>">
									<label class="control-label">Nomor Perkiraan</label>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" id="account_name" name="account_name" autocomplete="off" value="<?php echo $acctaccount['account_name']; ?>">
									<label class="control-label">Nama Perkiraan</label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" id="account_group" name="account_group" autocomplete="off" value="<?php echo $acctaccount['account_group']; ?>">
									<label class="control-label">Golongan Perkiraan</label>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<?php echo form_dropdown('account_status', $accountstatus, set_value('account_status',$acctaccount['account_status']),'id="account_status" class="form-control select2me"');?>
									<label class="control-label"></label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<?php echo form_dropdown('account_type_id', $kelompokperkiraan, set_value('account_type_id',$acctaccount['account_type_id']),'id="account_type_id" class="form-control select2me"');?>
									<label class="control-label">Kelompok Perkiraan</label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12" style='text-align:right'>
								<button type="reset" name="Reset" value="Reset" class="btn btn-danger" onClick="ulang();"><i class="fa fa-times"> Batal</i></button>
								<button type="submit" name="Save" value="Save" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Simpan</i></button>
							</div>	
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<input type="hidden" class="form-control" name="account_id" id="account_id" placeholder="id" value="<?php echo set_value('account_id',$acctaccount['account_id']);?>"/>
<?php echo form_close(); ?>