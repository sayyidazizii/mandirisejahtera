<script>
	base_url = '<?php echo base_url();?>';
	function ulang(){
		document.getElementById("bank_account_code").value 			= "";
		document.getElementById("bank_account_name").value 			= "";
		document.getElementById("bank_account_no").value 			= "";
	}

	$(document).ready(function(){
        $("#Save").click(function(){
			var bank_account_code 	= $("#bank_account_code").val();
			var bank_account_name 	= $("#bank_account_name").val();
			var bank_account_no 	= $("#bank_account_no").val();
			
			if(bank_account_code == ''){
				alert("Kode Bank masih kosong");
				return false;
			} else if(bank_account_name == ''){
				alert("Nama Bank masih kosong");
				return false;
			}else if(bank_account_no == ''){
				alert("No. Rek masih kosong");
				return false;
			} else {
				return true;
			}
		});
    });
</script>
<?php echo form_open('bank-account/process-add',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
	$data = $this->session->userdata('addAcctBankAccount');

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
			<a href="<?php echo base_url();?>bank-account">
				Daftar Kode Bank
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>bank-account/add">
				Tambah Kode Bank 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Form Tambah Kode Bank
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
						<a href="<?php echo base_url();?>AcctBankAccount" class="btn btn-default btn-sm">
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
									<input type="text" class="form-control" id="bank_account_code" name="bank_account_code" autocomplete="off">
									<label class="control-label">Kode Bank</label>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" id="bank_account_name" name="bank_account_name" autocomplete="off">
									<label class="control-label">Nama Bank</label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" id="bank_account_no" name="bank_account_no" autocomplete="off">
									<label class="control-label">No. Rekening</label>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<?php echo form_dropdown('account_id', $acctaccount, set_value('account_id'),'id="account_id" class="form-control select2me"');?>
									<label class="control-label">No Perkiraan</label>
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