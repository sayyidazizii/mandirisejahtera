<style>
	th{
		font-size:14px  !important;
		font-weight: bold !important;
		text-align:center !important;
		margin : 0 auto;
		vertical-align:middle !important;
	}
	td{
		font-size:12px  !important;
		font-weight: normal !important;
	}
	
	// select{
		// display: inline-block;
		// padding: 4px 6px;
		// margin-bottom: 0px !important;
		// font-size: 14px;
		// line-height: 20px;
		// color: #555555;
		// -webkit-border-radius: 3px;
		// -moz-border-radius: 3px;
		// border-radius: 3px;
	// }
	
	// label {
		// display: inline !important;
		// width:50% !important;
		// margin:0 !important;
		// padding:0 !important;
		// vertical-align:middle !important;
	// }
</style>
<script>
	base_url = '<?php echo base_url();?>';
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
<div class="row-fluid">
	

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
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Daftar Dusun <small>Kelola Dusun</small>
</h3>

<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>
<?php

	$sesi=$this->session->userdata('filter-coredusun');

	if(!is_array($sesi)){
		$sesi['kelurahan_id']			= '';
	}
?>	
<?php	echo form_open('dusun/filter',array('id' => 'myform', 'class' => '')); ?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Daftar
				</div>
				<div class="actions">
					<a href="<?php echo base_url();?>dusun/add" class="btn btn-default btn-sm">
						<i class="fa fa-plus"></i>
						<span class="hidden-480">
							Tambah Dusun Baru
						</span>
					</a>
				</div>
				</div>
			<div class="portlet-body">
				<div class="form-body form">
					 <div class = "row">
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<?php echo form_dropdown('city_id', $corecity, set_value('city_id'),'id="city_id" class="form-control select2me"');?>
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
					</div>

					<div class="row">
						<div class="form-actions right">
							<button type="button" class="btn red" onClick="reset_search();"><i class="fa fa-times"></i> Batal</button>
							<button type="submit" class="btn green-jungle"><i class="fa fa-search"></i> Cari</button>
						</div>	
					</div>
				</div>
			</div>

<?php echo form_close(); ?>

				
				<div class="portlet-body">
					<div class="form-body">
						<table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
						<thead>
							<tr>
								<th width="5%">No</th>
								<th width="15%">Kelurahan</th>
								<th width="15%">Nama Dusun</th>
								<th width="10%">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$no = 1;
								if(empty($coredusun)){
									echo "
										<tr>
											<td colspan='8' align='center'>Data Kosong</td>
										</tr>
									";
								} else {
									foreach ($coredusun as $key=>$val){									
										echo"
											<tr>			
												<td style='text-align:center'>$no.</td>
												<td>$val[kelurahan_name]</td>
												<td>$val[dusun_name]</td>
												<td>
													<a href='".$this->config->item('base_url').'dusun/edit/'.$val['dusun_id']."' class='btn default btn-xs purple'>
														<i class='fa fa-edit'></i> Edit
													</a>
													<a href='".$this->config->item('base_url').'dusun/delete/'.$val['dusun_id']."'class='btn default btn-xs red', onClick='javascript:return confirm(\"apakah yakin ingin dihapus ?\")'>
														<i class='fa fa-trash-o'></i> Hapus
													</a>
												</td>
											</tr>
										";
										$no++;
									} 
								}
								
							?>
							</tbody>
							</table>
						</div>
						</div>
					</div>
					<!-- END EXAMPLE TABLE PORTLET-->
				</div>
			</div>
<?php echo form_close(); ?>