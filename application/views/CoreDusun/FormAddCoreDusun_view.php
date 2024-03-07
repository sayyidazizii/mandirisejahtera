<script>
	base_url = '<?php echo base_url();?>';
	function ulang(){
		document.getElementById("kelurahan_id").value 			= "";
		document.getElementById("dusun_name").value 			= "";
	}

	$(document).ready(function(){
        $("#city_id").change(function(){
            var city_id 	= $("#city_id").val();
            
            $.ajax({
               type : "POST",
               url  : "<?php echo base_url(); ?>dusun/get-kecamatan",
               data : {city_id: city_id},
               success: function(data){
                   $("#kecamatan_id").html(data);
               }
            });
        });
    });

    $(document).ready(function(){
        $("#kecamatan_id").change(function(){
            var kecamatan_id 	= $("#kecamatan_id").val();
            
            $.ajax({
               type : "POST",
               url  : "<?php echo base_url(); ?>dusun/get-kelurahan",
               data : {kecamatan_id: kecamatan_id},
               success: function(data){
                   $("#kelurahan_id").html(data);
               }
            });
        });
    });
	
</script>
<?php echo form_open('dusun/process-add',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
	$data = $this->session->userdata('addCoreDusun');
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
			<a href="<?php echo base_url();?>CoreDusun">
				Daftar Dusun
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>dusun/add">
				Tambah Dusun
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Form Tambah Dusun
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
									<?php echo form_dropdown('city_id', $corecity, set_value('city_id',$data['city_id']),'id="city_id" class="form-control select2me"');?>
									<label class="control-label">Kabupaten<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<select name="kecamatan_id" id="kecamatan_id" class="form-control select2me">
										<option value="">--Pilih Salah Satu--</option>
									</select>
									<label class="control-label">Kecamatan<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<select name="kelurahan_id" id="kelurahan_id" class="form-control select2me">
										<option value="">--Pilih Salah Satu--</option>
									</select>
									<label class="control-label">Kelurahan<span class="required">*</span></label>
								</div>
							</div>
						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="dusun_name" id="dusun_name"/>
									<label class="control-label">Nama Dusun<span class="required">*</span></label>
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