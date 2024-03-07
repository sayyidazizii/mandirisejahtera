<script>
	base_url = '<?php echo base_url();?>';
	function ulang(){
		document.getElementById("mutation_name").value = "";
		document.getElementById("mutation_code").value = "";
	}

	$(document).ready(function(){
        $("#Save").click(function(){
			var mutation_code = $("#mutation_code").val();
			
			if(mutation_code == ''){
				alert('Kode Mutasi masih kosong');
				return false;
			} else if(mutation_name == ''){
				alert('Nama Mutasi masih kosong');
				return false;
			} else {
				return true;
			}	
		});
    });
</script>
<?php echo form_open('mutation/process-add',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
	$data = $this->session->userdata('addAcctMutation');

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
			<a href="<?php echo base_url();?>mutation">
				Daftar Mutasi
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>mutation/add">
				Tambah Mutasi 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Form Tambah Mutasi
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
						<a href="<?php echo base_url();?>AcctMutation" class="btn btn-default btn-sm">
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
									<input type="text" class="form-control" name="mutation_code" id="mutation_code" autocomplete="off"/>
									<label class="control-label">Kode Mutasi<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="mutation_name" id="mutation_name" autocomplete="off"/>
									<label class="control-label">Nama Mutasi<span class="required">*</span></label>
								</div>
							</div>
						</div>

						<div class="row">						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="mutation_function" id="mutation_function" autocomplete="off"/>
									<label class="control-label">Fungsi (+/-)<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<?php 
										echo form_dropdown('mutation_status', $accountstatus, set_value('mutation_status'), 'id="mutation_status" class="form-control select2me"');
									?>
									<label class="control-label">D/K<span class="required">*</span></label>
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