<script>
	base_url = '<?php echo base_url();?>';
	function ulang(){
		document.getElementById("account_code").value 			= "";
		document.getElementById("account_name").value 			= "";
		document.getElementById("account_group").value 			= "";
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
<?php echo form_open('account/process-add',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
	$data = $this->session->userdata('addAcctAccount');

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
			<a href="<?php echo base_url();?>account">
				Daftar Perkiraan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>account/add">
				Tambah Perkiraan 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Form Tambah Perkiraan
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
						<a href="<?php echo base_url();?>account" class="btn btn-default btn-sm">
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
									<input type="text" class="form-control" id="account_code" name="account_code" autocomplete="off">
									<label class="control-label">Nomor Perkiraan</label>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" id="account_name" name="account_name" autocomplete="off">
									<label class="control-label">Nama Perkiraan</label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" id="account_group" name="account_group" autocomplete="off">
									<label class="control-label">Golongan Perkiraan</label>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<?php echo form_dropdown('account_status', $accountstatus, set_value('account_status',$data['account_status']),'id="account_status" class="form-control select2me"');?>
									<label class="control-label"></label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<?php echo form_dropdown('account_type_id', $kelompokperkiraan, set_value('account_type_id',$data['account_type_id']),'id="account_type_id" class="form-control select2me"');?>
									<label class="control-label">Kelompok Perkiraan</label>
								</div>
							</div>
						</div>
						


						<div class="row">
							<div class="col-md-12" style='text-align:right'>
								<button type="reset" name="Reset" value="Reset" class="btn btn-danger" onClick="ulang();"><i class="fa fa-times"> Batal</i></button>
								<button type="submit" name="Save" value="Save" id="Save" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Simpan</i></button>
							</div>	
						</div>
					</div>
				</div>
			 </div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>