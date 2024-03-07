<style>
	th, td {
	padding: 3px;
	}
	input:focus { 
	background-color: 42f483;
	}
</style>
<script>
	base_url = '<?php echo base_url();?>';

	$(document).ready(function(){
		$("#province_id").change(function(){
			var province_id 	= $("#province_id").val();
			
			$.ajax({
			type : "POST",
			url  : "<?php echo base_url(); ?>member/get-city",
			data : {province_id: province_id},
			success: function(data){
				$("#city_id").html(data);
			}
			});
		});
	});

	$(document).ready(function(){
		$("#city_id").change(function(){
			var city_id 	= $("#city_id").val();
			
			$.ajax({
			type : "POST",
			url  : "<?php echo base_url(); ?>member/get-kecamatan",
			data : {city_id: city_id},
			success: function(data){
				$("#kecamatan_id").html(data);
			}
			});
		});
	});

	function function_elements_add(name, value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('member/elements-add');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
			}
		});
	}

	function reset_add(){
		document.location = base_url+"member/reset-add";
	}
	

	// $(document).ready(function(){
//        $("#Save").click(function(){
	// 		var member_principal_savings = $("#member_principal_savings").val();
			
	// 		if(member_principal_savings == ''){
	// 			return confirm("Simpanan Pokok kosong, apakah yakin ingin disimpan ?");
	// 		} 	
	// 	});
//    });
</script>
<?php 
	echo form_open('member/process-add',array('id' => 'myform', 'class' => 'horizontal-form')); 

	$unique 	= $this->session->userdata('unique');
	$data 		= $this->session->userdata('addCoreMember-'.$unique['unique']);

	if (empty($data['member_name'])){
		$data['member_name'] 				= '';
	}

	if (empty($data['member_gender'])){
		$data['member_gender'] 				= 9;
	}

	if (empty($data['province_id'])){
		$data['province_id'] 				= 0;
	}
		
	if (empty($data['city_id'])){
		$data['city_id'] 					= 0;
	}
		
	if (empty($data['kecamatan_id'])){
		$data['kecamatan_id'] 				= 0;
	}

	if (empty($data['member_place_of_birth'])){
		$data['member_place_of_birth'] 		= '';
	}

	if (empty($data['member_date_of_birth'])){
		$data['member_date_of_birth'] 		= date("Y-m-d");
	}	
		
	if (empty($data['member_address'])){
		$data['member_address'] 			= '';
	}
		
	if (empty($data['member_postal_code'])){
		$data['member_postal_code'] 		= '';
	}

	if (empty($data['member_phone'])){
		$data['member_phone'] 				= '';
	}
		
	if (empty($data['member_job'])){
		$data['member_job'] 				= '';
	}
		
	if (empty($data['member_character'])){
		$data['member_character'] 			= 9;
	}

	if (empty($data['member_identity'])){
		$data['member_identity'] 			= 9;
	}

	if (empty($data['member_identity_no'])){
		$data['member_identity_no'] 		= '';
	}
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
			<a href="<?php echo base_url();?>member">
				Daftar Anggota
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>member/add">
				Tambah Anggota 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Form Tambah Anggota
</h3>

<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Form Tambah
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>member" class="btn btn-default btn-sm">
							<i class="fa fa-angle-left"></i>
							<span class="hidden-480">
								Kembali
							</span>
						</a>
					</div>
				</div>
				<div class="portlet-body form">
					<div class="form-body">
						<?php
							echo $this->session->userdata('message');
							$this->session->unset_userdata('message');
						?>
						<div class="row">
							<div class="col-md-2">
							</div>
							<div class="col-md-8">
								<table>
									<tr>
										<td width="35%">Jenis Kelamin<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><?php echo form_dropdown('member_gender', $membergender, set_value('member_gender',$data['member_gender']),'id="member_gender" class="easyui-combobox" onChange="function_elements_add(this.name, this.value);"');?></td>
									</tr>
									<tr>
										<td width="35%">Provinsi<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><?php echo form_dropdown('province_id', $coreprovince, set_value('province_id', $data['province_id']),'id="province_id" class="easyui-combobox" onChange="function_elements_add(this.name, this.value);"');?></td>
									</tr>
									<tr>
										<td width="35%">Kabupaten<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<?php
												if (!empty($data['province_id'])){
													$corecity = create_double($this->CoreMember_model->getCoreCity($data['province_id']), 'city_id', 'city_name');

													echo form_dropdown('city_id', $corecity, set_value('city_id', $data['city_id']), 'id="city_id" class="easyui-combobox" onChange="function_elements_add(this.name, this.value);"');
												} else {
											?>
												<select name="city_id" id="city_id" class="easyui-combobox">
													<option value="">--Pilih Salah Satu--</option>
												</select>
											<?php
												}
											?>
										</td>
									</tr>
									<tr>
										<td width="35%">Kecamatan<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												if (!empty($data['city_id'])){
													$corekecamatan = create_double($this->CoreMember_model->getCoreKecamatan($data['city_id']), 'kecamatan_id', 'kecamatan_name');

													echo form_dropdown('kecamatan_id', $corekecamatan, set_value('kecamatan_id', $data['kecamatan_id']), 'id="kecamatan_id" class="easyui-combobox" onChange="function_elements_add(this.name, this.value);"');
												} else {
											?>
												<select name="kecamatan_id" id="kecamatan_id" class="easyui-combobox">
													<option value="">--Pilih Salah Satu--</option>
												</select>
											<?php 
												}
											?>
										</td>
									</tr>
									<tr>
										<td width="35%">Tempat Lahir<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_place_of_birth" id="member_place_of_birth" value="<?php echo set_value('member_place_of_birth',$data['member_place_of_birth']);?>" onChange="function_elements_add(this.name, this.value);" size="50%"></td>
									</tr>
									<tr>
										<td width="35%">Tanggal Lahir<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input class="easyui-datebox date-picker" data-date-format="dd-mm-yyyy" type="text" name="member_date_of_birth" id="member_date_of_birth" value="<?php echo tgltoview($data['member_date_of_birth']);?>" onChange="function_elements_add(this.name, this.value);"/></td>
									</tr>
									<tr>
										<td width="35%">Alamat<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><textarea rows="3" name="member_address" id="member_address" class="easyui-textarea" onChange="function_elements_add(this.name, this.value);"><?php echo $data['member_address'];?></textarea></td>
									</tr>
									<tr>
										<td width="35%">Kode Pos<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_postal_code" id="member_postal_code" autocomplete="off" value="<?php echo set_value('member_postal_code',$data['member_postal_code']);?>" onChange="function_elements_add(this.name, this.value);" size="50%"/></td>
									</tr>
									<tr>
										<td width="35%">Nomor Telp<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_phone" id="member_phone" autocomplete="off" value="<?php echo set_value('member_phone',$data['member_phone']);?>" onChange="function_elements_add(this.name, this.value);"/></td>
									</tr>
									<tr>
										<td width="35%">Sifat Anggota<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('member_character', $membercharacter, set_value('member_character',$data['member_character']),'id="member_character" class="easyui-combobox" onChange="function_elements_add(this.name, this.value);"');
											?>
												
										</td>
									</tr>
									<tr>
										<td width="35%">Pekerjaan<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_job" id="member_job" autocomplete="off" value="<?php echo set_value('member_job',$data['member_job']);?>" onChange="function_elements_add(this.name, this.value);"/></td>
									</tr>
									<tr>
										<td width="35%">Identitas<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('member_identity', $memberidentity, set_value('member_identity', $data['member_identity']),'id="member_identity" class="easyui-combobox" onChange="function_elements_add(this.name, this.value);"');
											?>
										</td>
									</tr>
									<tr>
										<td width="35%">Nomor Identitas<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_identity_no" id="member_identity_no" autocomplete="off" value="<?php echo set_value('member_identity_no',$data['member_identity_no']);?>" onChange="function_elements_add(this.name, this.value);"/></td>
									</tr>
								</table>
							</div>
							<div class="col-md-2">
							</div>
						</div>
						
						<!-- <div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_mother" id="member_mother" autocomplete="off" value="<?php echo set_value('member_mother',$data['member_mother']);?>"/>
									<label class="control-label">Nama Ibu Kandung<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_heir" id="member_heir" autocomplete="off" value="<?php echo set_value('member_heir',$data['member_heir']);?>"/>
									<label class="control-label">Ahli Waris<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_family_relationship" id="member_family_relationship" autocomplete="off" value="<?php echo set_value('member_family_relationship',$data['member_family_relationship']);?>"/>
									<label class="control-label">Hub. Keluarga<span class="required">*</span></label>
								</div>
							</div>
						</div> -->
						

						<!-- <div class="row">
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_principal_savings" id="member_principal_savings" autocomplete="off" value="<?php echo set_value('member_principal_savings',$data['member_principal_savings']);?>"/>
									<label class="control-label">Simpanan Pokok<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_special_savings" id="member_special_savings"  autocomplete="off" value="<?php echo set_value('member_special_savings',$data['member_special_savings']);?>"/>
									<label class="control-label">Simpanan Khusus<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_mandatory_savings" id="member_mandatory_savings"  autocomplete="off" value="<?php echo set_value('member_mandatory_savings',$data['member_mandatory_savings']);?>"/>
									<label class="control-label">Simpanan Wajib<span class="required">*</span></label>
								</div>
							</div>
						</div> -->

						<div class="row">
							<div class="form-actions right">
								<button type="button" class="btn red" onClick="reset_add();"><i class="fa fa-times"></i> Batal</button>
								<button type="submit" class="btn green-jungle"><i class="fa fa-check"></i> Simpan</button>
							</div>	
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>