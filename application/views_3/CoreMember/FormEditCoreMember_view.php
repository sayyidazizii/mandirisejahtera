<style>
	th, td {
	  padding: 3px;
	  font-size: 14px;
	}
	input:focus { 
	  background-color: 42f483;
	}
	input:read-only {
		background-color: f0f8ff;
	}
</style>
<script>
	base_url = '<?php echo base_url();?>';

	var loop = 1;

	$(document).ready(function(){
		$('#member_name').textbox({
		   collapsible:false,
		   minimizable:false,
		   maximizable:false,
		   closable:false
		});
		
		$('#member_name').textbox('textbox').focus();
	});

	function toRp(number) {
		var number = number.toString(), 
		rupiah = number.split('.')[0], 
		cents = (number.split('.')[1] || '') +'00';
		rupiah = rupiah.split('').reverse().join('')
			.replace(/(\d{3}(?!$))/g, '$1,')
			.split('').reverse().join('');
		return rupiah + '.' + cents.slice(0, 2);
	}


	function function_elements_edit(name, value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('member/elements-edit');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
			}
		});
	}

	$(document).ready(function(){
		$('#member_class_id').combobox({
			onChange: function(value){
				var member_class_id   = document.getElementById("member_class_id").value;
				// alert(member_class_id);
				$.ajax({
				type : "POST",
				url  : "<?php echo base_url(); ?>member/change-member-class/"+member_class_id,
				data : {member_class_id: member_class_id},
				success: function(data){
					$('#member_class_mandatory_savings_view').textbox({
						value: toRp(data),
					});
					$('#member_class_mandatory_savings').textbox({
						value: data,
					});
				}
				});
			}
			});
	});

	$(document).ready(function(){
		$('#company_id').combobox({
			onChange: function(value){
				var company_id   = document.getElementById("company_id").value;
				// alert(company_id);
				$.ajax({
				type : "POST",
				url  : "<?php echo base_url(); ?>member/change-company/"+company_id,
				data : {company_id: company_id},
				success: function(data){
					$('#member_company_mandatory_savings_view').textbox({
						value: toRp(data),
					});
					$('#member_company_mandatory_savings').textbox({
						value: data,
					});
				}
				});
			}
			});
	});

	$(document).ready(function(){
		$('#member_company_mandatory_savings_view').textbox({
			onChange: function(value){
				var member_company_mandatory_savings_view   = document.getElementById("member_company_mandatory_savings_view").value;
				// alert(company_id);
				$('#member_company_mandatory_savings_view').textbox({
					value: toRp(member_company_mandatory_savings_view),
				});
				$('#member_company_mandatory_savings').textbox({
					value: member_company_mandatory_savings_view,
				});
			}
			});
	});

	$(document).ready(function(){
		$('#member_class_mandatory_savings_view').textbox({
			onChange: function(value){
				var member_class_mandatory_savings_view   = document.getElementById("member_class_mandatory_savings_view").value;
				// alert(company_id);
				$('#member_class_mandatory_savings_view').textbox({
					value: toRp(member_class_mandatory_savings_view),
				});
				$('#member_class_mandatory_savings').textbox({
					value: member_class_mandatory_savings_view,
				});
			}
			});
	});
        function myformatter(date){
            var y = date.getFullYear();
            var m = date.getMonth()+1;
            var d = date.getDate();
            return (d<10?('0'+d):d)+'-'+(m<10?('0'+m):m)+'-'+y;
        }
        function myparser(s){
            if (!s) return new Date();
            var ss = (s.split('-'));
            var y = parseInt(ss[0],10);
            var m = parseInt(ss[1],10);
            var d = parseInt(ss[2],10);
            if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
                return new Date(d,m-1,y);
            } else {
                return new Date();
            }
        }
    
	function reset_edit(){
		document.location = base_url+"member/reset-edit/<?php echo $coremember['member_id']?>";
	}


	$(document).ready(function(){
		$('#province_id').combobox({
			onChange: function(value){
				var province_id   = document.getElementById("province_id").value;
				function_elements_add('province_id', value);
				function_elements_add('city_id', 0);
				function_elements_add('kecamatan_id', 0);
				function_elements_add('kelurahan_id', 0);
				$('#city_id').combobox({
					value: '--Pilih Salah Satu--',
				});
				$('#kecamatan_id').combobox({
					value: '--Pilih Salah Satu--',
				});
				$('#kelurahan_id').combobox({
					value: '--Pilih Salah Satu--',
				});
				$.ajax({
				type : "POST",
				url  : "<?php echo base_url(); ?>member/get-city/"+province_id,
				data : {province_id: province_id},
				success: function(data){
					/*alert(data);*/
						$('#city_id').combobox({
							url:"<?php echo base_url(); ?>member/get-city/"+province_id,
							valueField:'city_id',
							textField:'city_name',
							loadData:data
						});
				}
				});
			}
			});
	});


	$(document).ready(function(){
		$('#city_id').combobox({
			onChange: function(value){
				function_elements_add('city_id', value);
				function_elements_add('kecamatan_id', 0);
				function_elements_add('kelurahan_id', 0);
				$('#kecamatan_id').combobox({
					value: '--Pilih Salah Satu--',
				});
				$('#kelurahan_id').combobox({
					value: '--Pilih Salah Satu--',
				});
				$.ajax({
				type : "POST",
				url  : "<?php echo base_url(); ?>member/get-kecamatan/"+value,
				data : {value: value},
				success: function(data){
				/*	alert(data);*/
						$('#kecamatan_id').combobox({
							url:"<?php echo base_url(); ?>member/get-kecamatan/"+value,
							valueField:'kecamatan_id',
							textField:'kecamatan_name'
						});
						
					}
				});
			}
		});		
	});

	$(document).ready(function(){
		$('#kecamatan_id').combobox({
			onChange: function(value){
				function_elements_add('kecamatan_id', value);
				function_elements_add('kelurahan_id', 0);

				$('#kelurahan_id').combobox({
					value: '--Pilih Salah Satu--',
				});
				$.ajax({
				type : "POST",
				url  : "<?php echo base_url(); ?>member/get-kelurahan/"+value,
				data : {value: value},
				success: function(data){
				/*	alert(data);*/
						$('#kelurahan_id').combobox({
							url:"<?php echo base_url(); ?>member/get-kelurahan/"+value,
							valueField:'kelurahan_id',
							textField:'kelurahan_name'
						});
						
					}
				});
			}
		});		
	});

	$(document).ready(function(){
		$('#kelurahan_id').combobox({
			onChange: function(value){
				function_elements_add('kelurahan_id', value);
				$.ajax({
				type : "POST",
				url  : "<?php echo base_url(); ?>member/get-dusun/"+value,
				data : {value: value},
				success: function(data){
				/*	alert(data);*/
						$('#dusun_id').combobox({
							url:"<?php echo base_url(); ?>member/get-dusun/"+value,
							valueField:'dusun_id',
							textField:'dusun_name'
						});
						
					}
				});
			}
		});		
	});


	function function_elements_add(name, value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('member/elements-add');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
					// alert(msg);
			}
		});
	}
    $(document).ready(function(){
		$('#member_special_savings_view').textbox({
			onChange: function(value){
				console.log(value);
				console.log(loop);
				if(loop == 0){
					loop= 1;
					return;
				}
				if(loop ==1){
					loop =0;
					var tampil = toRp(value);
				$('#member_special_savings').textbox('setValue', value);
				$('#member_special_savings_view').textbox('setValue', tampil);
				
				}else{
					loop=1;
					return;
				}
			
			}
		});

		$('#member_mandatory_savings_view').textbox({
			onChange: function(value){
				console.log(value);
				console.log(loop);
				if(loop == 0){
					loop= 1;
					return;
				}
				if(loop ==1){
					loop =0;
					var tampil = toRp(value);
				$('#member_mandatory_savings').textbox('setValue', value);
				$('#member_mandatory_savings_view').textbox('setValue', tampil);

				
				}else{
					loop=1;
					return;
				}
			
			}
		});

		$('#member_principal_savings_view').textbox({
			onChange: function(value){
				console.log(value);
				console.log(loop);
				if(loop == 0){
					loop= 1;
					return;
				}
				if(loop ==1){
					loop =0;
					var tampil = toRp(value);
				$('#member_principal_savings').textbox('setValue', value);
				$('#member_principal_savings_view').textbox('setValue', tampil);

				
				}else{
					loop=1;
					return;
				}
			
			}
		});
		
		$('#member_working_type').combobox({
			onChange: function(value){
				function_elements_add('member_working_type', value);
				if(value == 1 || value == 2){
					//enable it
					$('#member_company_name').textbox('readonly',false); 
					$('#member_company_specialites').textbox('readonly',false); 
					$('#member_company_job_title').textbox('readonly',false); 
					$('#member_company_phone').textbox('readonly',false); 
					$('#member_company_address').textbox('readonly',false); 
					$('#member_company_city').textbox('readonly',false); 
					$('#member_company_period').textbox('readonly',false); 
					$('#member_company_postal_code').textbox('readonly',false); 
					
					//disable it
					$('#member_business_name').textbox('readonly',true); 
					$('#member_business_scale').combobox('readonly',true); 
					$('#member_business_period').textbox('readonly',true); 
					$('#member_business_address').textbox('readonly',true); 
					$('#member_business_city').textbox('readonly',true); 
					$('#member_business_owner').combobox('readonly',true); 
					$('#member_business_phone').textbox('readonly',true); 
					$('#member_business_postal_code').textbox('readonly',true); 
					$('#member_business_name').textbox({value : ""}); 
					$('#member_business_scale').combobox({value : ""}); 
					$('#member_business_period').textbox({value : ""}); 
					$('#member_business_address').textbox({value : ""}); 
					$('#member_business_city').textbox({value : ""}); 
					$('#member_business_owner').combobox({value : ""}); 
					$('#member_business_phone').textbox({value : ""}); 
					$('#member_business_postal_code').textbox({value : ""}); 
					
				}
				if(value == 3){
					$('#member_company_name').textbox('readonly',true); 
					$('#member_company_specialites').textbox('readonly',true); 
					$('#member_company_job_title').textbox('readonly',true); 
					$('#member_company_phone').textbox('readonly',true); 
					$('#member_company_address').textbox('readonly',true); 
					$('#member_company_city').textbox('readonly',true); 
					$('#member_company_period').textbox('readonly',true); 
					$('#member_company_postal_code').textbox('readonly',true); 
					$('#member_company_name').textbox({value : ""}); 
					$('#member_company_specialites').textbox({value : ""}); 
					$('#member_company_job_title').textbox({value : ""}); 
					$('#member_company_phone').textbox({value : ""}); 
					$('#member_company_address').textbox({value : ""}); 
					$('#member_company_city').textbox({value : ""}); 
					$('#member_company_period').textbox({value : ""}); 
					$('#member_company_postal_code').textbox({value : ""}); 

					$('#member_business_name').textbox('readonly',false); 
					$('#member_business_scale').combobox('readonly',false); 
					$('#member_business_period').textbox('readonly',false); 
					$('#member_business_address').textbox('readonly',false); 
					$('#member_business_city').textbox('readonly',false); 
					$('#member_business_owner').combobox('readonly',false); 
					$('#member_business_phone').textbox('readonly',false); 
					$('#member_business_postal_code').textbox('readonly',false); 
					
				}
			}
		});

		$('#partner_working_type').combobox({
			onChange: function(value){
				function_elements_add('partner_working_type', value);
				if(value == 1 || value == 2){
					$('#partner_company_name').textbox('readonly',false);
					$('#partner_company_specialites').textbox('readonly',false);
					$('#partner_company_job_title').textbox('readonly',false);
					$('#partner_company_phone').textbox('readonly',false);
					$('#partner_company_address').textbox('readonly',false);
					$('#partner_company_specialities').textbox('readonly',false);

					$('#partner_business_name').textbox('readonly',true);
					$('#partner_business_scale').combobox('readonly',true);
					$('#partner_business_period').textbox('readonly',true);
					$('#partner_business_owner').combobox('readonly',true);
					$('#partner_business_name').textbox({value : ""});
					$('#partner_business_scale').combobox({value : ""});
					$('#partner_business_period').textbox({value : ""});
					$('#partner_business_owner').combobox({value : ""});
					
				}
				if(value == 3){
					$('#partner_company_name').textbox('readonly',true);
					$('#partner_company_specialities').textbox('readonly',true);
					$('#partner_company_job_title').textbox('readonly',true);
					$('#partner_company_phone').textbox('readonly',true);
					$('#partner_company_address').textbox('readonly',true);
					$('#partner_company_name').textbox({value : ""});
					$('#partner_company_specialities').textbox({value : ""});
					$('#partner_company_job_title').textbox({value : ""});
					$('#partner_company_phone').textbox({value : ""});
					$('#partner_company_address').textbox({value : ""});

					$('#partner_business_name').textbox('readonly',false);
					$('#partner_business_scale').combobox('readonly',false);
					$('#partner_business_period').textbox('readonly',false);
					$('#partner_business_owner').combobox('readonly',false);
				}
			}
		});
	});

</script>
<?php echo form_open('member/process-edit-member',array('id' => 'myform', 'class' => 'horizontal-form')); 
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
			<a href="<?php echo base_url();?>member/edit/"<?php echo $coremember['member_id'] ?>>
				Edit Anggota 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Form Edit
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
							<div class="col-md-6">
								<table class="table table-striped table-bordered table-hover table-full-width">
									<tr>
										<td colspan="3" align="center"><h4><b>Data Pribadi</b></h4></td>
									</tr>
									<tr>
										<td width="35%">No. Anggota<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_no" id="member_no" autocomplete="off" value="<?php echo set_value('member_no', $coremember['member_no']);?>" style="width: 100%"/>
										</td>
									</tr>
									<tr>
										<td width="35%">Nama Lengkap (sesuai KTP) <span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo set_value('member_name',$coremember['member_name']);?>" style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">Nama Panggilan <span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_nick_name" id="member_nick_name" autocomplete="off" value="<?php echo set_value('member_nick_name',$coremember['member_nick_name']);?>" style="width:100%;"/></td>
									</tr>
									<!-- <tr>
										<td width="35%">Sifat Anggota<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><?php echo form_dropdown('member_character', $membercharacter, set_value('member_character',$coremember['member_character']),'id="member_character" class="easyui-combobox" style="width: 100%"');?></td>
									</tr> -->
									<tr>
										<td width="35%">Jenis Kelamin<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><?php echo form_dropdown('member_gender', $membergender, set_value('member_gender',$coremember['member_gender']),'id="member_gender" class="easyui-combobox" style="width: 100%"');?></td>
									</tr>
									<tr>
										<td width="35%">Tempat Lahir<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_place_of_birth" id="member_place_of_birth" autocomplete="off" value="<?php echo set_value('member_place_of_birth',$coremember['member_place_of_birth']);?>" style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">Tanggal Lahir<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input class="easyui-datebox" data-options="formatter:myformatter,parser:myparser" type="text" name="member_date_of_birth" id="member_date_of_birth" value="<?php echo tgltoview($coremember['member_date_of_birth']);?>" style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">Provinsi<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><?php echo form_dropdown('province_id', $coreprovince, set_value('province_id',$coremember['province_id']),'id="province_id" class="easyui-combobox" style="width: 100%"');?></td>
									</tr>
									<tr>
										<td width="35%">Kabupaten<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<?php
												if (!empty($coremember['province_id'])){
													$corecity = create_double($this->CoreMember_model->getCoreCity($coremember['province_id']), 'city_id', 'city_name');

													echo form_dropdown('city_id', $corecity, set_value('city_id', $coremember['city_id']), 'id="city_id" class="easyui-combobox" style="width: 100%" ');
												} else {
											?>
												<select name="city_id" id="city_id" class="easyui-combobox" style="width: 100%">
													<option value="">--Pilih Salah Satu--</option>
												</select>
											<?php
												}
											?>
										</td>
									</tr>
									<tr>
										<td width="35%">Kecamatan<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<?php
												if (!empty($coremember['city_id'])){
													$corekecamatan = create_double($this->CoreMember_model->getCoreKecamatan($coremember['city_id']), 'kecamatan_id', 'kecamatan_name');

													echo form_dropdown('kecamatan_id', $corekecamatan, set_value('kecamatan_id', $coremember['kecamatan_id']), 'id="kecamatan_id" class="easyui-combobox" style="width: 100%" ');
												} else {
											?>
												<select name="kecamatan_id" id="kecamatan_id" class="easyui-combobox" style="width: 100%">
													<option value="">--Pilih Salah Satu--</option>
												</select>
											<?php
												}
											?>
										</td>
									</tr>
									<tr>
										<td width="35%">Kelurahan<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<?php
												if (!empty($coremember['kecamatan_id'])){
													$corekelurahan = create_double($this->CoreMember_model->getCoreKelurahan($coremember['kecamatan_id']), 'kelurahan_id', 'kelurahan_name');

													echo form_dropdown('kelurahan_id', $corekelurahan, set_value('kelurahan_id', $coremember['kelurahan_id']), 'id="kelurahan_id" class="easyui-combobox" style="width: 100%" ');
												} else {
											?>
												<select name="kelurahan_id" id="kelurahan_id" class="easyui-combobox" style="width: 100%">
													<option value="">--Pilih Salah Satu--</option>
												</select>
											<?php
												}
											?>
										</td>
									</tr>
									<tr>
										<td width="35%">Alamat (sesuai KTP)<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><textarea rows="3" name="member_address" id="member_address" class="easyui-textarea" style="width: 100%" ><?php echo $coremember['member_address'];?></textarea></td>
									</tr>
									<tr>
										<td width="35%">Alamat Tinggal Sekarang<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><textarea rows="3" name="member_address_now" id="member_address_now" class="easyui-textarea" style="width: 100%" ><?php echo $coremember['member_address_now'];?></textarea></td>
									</tr>
									<tr>
										<td width="35%">Kode Pos</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_postal_code" id="member_postal_code" autocomplete="off" value="<?php echo set_value('member_postal_code',$coremember['member_postal_code']);?>"  style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">Status Pernikahan<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('member_marital_status', $maritalstatus, set_value('member_marital_status',$coremember['member_marital_status']),'id="member_marital_status" class="easyui-combobox" style="width:100%;"');
											?>
												
										</td>
									</tr>
									<tr>
										<td width="35%">No. KTP/SIM</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_identity_no" id="member_identity_no" autocomplete="off" value="<?php echo set_value('member_identity_no',$coremember['member_identity_no']);?>" style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">No. KTP/SIM Pengampu</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_partner_identity_no" id="member_partner_identity_no" autocomplete="off" value="<?php echo set_value('member_partner_identity_no',$coremember['member_partner_identity_no']);?>" style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">Nomor Hp<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_phone" id="member_phone" autocomplete="off" value="<?php echo set_value('member_phone',$coremember['member_phone']);?>" style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">Nama Ibu Kandung</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_mother" id="member_mother" autocomplete="off" value="<?php echo set_value('member_mother',$coremember['member_mother']);?>" style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">Simpanan Wajib</td>
										<td width="5%">:</td>
										<!-- <td width="60%">
											<?php 
												echo form_dropdown('member_class_id', $corememberclass, set_value('member_class_id',$coremember['member_class_id']),'id="member_class_id" class="easyui-combobox" style="width:100%;"');
											?>
												
										</td> -->
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_class_mandatory_savings_view" id="member_class_mandatory_savings_view" autocomplete="off" value="<?php echo set_value('member_class_mandatory_savings_view',nominal($coremember['member_class_mandatory_savings']));?>" style="width:100%;" readonly/>
											<input type="hidden" class="easyui-textbox" name="member_class_mandatory_savings" id="member_class_mandatory_savings" autocomplete="off" value="<?php echo set_value('member_class_mandatory_savings',$coremember['member_class_mandatory_savings']);?>" style="width:100%;"/>
										</td>
									</tr>
									<tr>
										<td width="35%">Preferensi Simpanan Wajib</td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('member_debet_preference', $paymentpreference, set_value('member_debet_preference',$coremember['member_debet_preference']),'id="member_debet_preference" class="easyui-combobox" style="width:100%;"');
											?>
												
										</td>
									</tr>
									<tr>
										<td width="35%">No Rek Auto Debet</td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('member_debet_savings_account_id', $acctsavingsaccount, set_value('member_debet_savings_account_id',$coremember['member_debet_savings_account_id']),'id="member_debet_savings_account_id" class="easyui-combobox" style="width:100%;"');
											?>
												
										</td>
									</tr>
									<tr>
										<td width="35%">Jumlah Tanggungan</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_dependent" id="member_dependent" autocomplete="off" value="<?php echo set_value('member_dependent',$coremember['member_dependent']);?>" style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">Status Rumah<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('member_home_status', $homestatus, set_value('member_home_status',$coremember['member_home_status']),'id="member_home_status" class="easyui-combobox" style="width:100%;"');
											?>
												
										</td>
									</tr>
									<tr>
										<td width="35%">Lama Menetap<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_long_stay" id="member_long_stay" autocomplete="off" value="<?php echo set_value('member_long_stay',$coremember['member_long_stay']);?>" style="width:100%;"/></td>
									</tr>
									<!-- <tr>
										<td width="35%">Kendaraan yang Dimiliki<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('member_vehicle', $membervehicle, set_value('member_vehicle',$coremember['member_vehicle']),'id="member_vehicle" class="easyui-combobox" style="width:100%;"');
											?>
												
										</td>
									</tr> -->
									<tr>
										<td width="35%">Pendidikan Terakhir<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('member_last_education', $lasteducation, set_value('member_last_education',$coremember['member_last_education']),'id="member_last_education" class="easyui-combobox" style="width:100%;"');
											?>
												
										</td>
									</tr>
									<!-- <tr>
										<td width="35%">Pengguna Unit<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('member_unit_user', $unituser, set_value('member_unit_user',$coremember['member_unit_user']),'id="member_unit_user" class="easyui-combobox" style="width:100%;"');
											?>
												
										</td>
									</tr> -->
									<tr>
										<td width="35%">Nama Pengampu<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_partner_name" id="member_partner_name" autocomplete="off" value="<?php echo set_value('member_partner_name',$coremember['member_partner_name']);?>" style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">Email<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_email" id="member_email" autocomplete="off" value="<?php echo set_value('member_email',$coremember['member_email']);?>" style="width:100%;"/></td>
									</tr>
									<tr>
										<td colspan="3" align="left"><b>Ahli Waris</b></td>
									</tr>
									<tr>
										<td width="35%">Nama</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_heir" id="member_heir" autocomplete="off" value="<?php echo set_value('member_heir',$coremember['member_heir']);?>" style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">Hubungan</td>
										<td width="5%">:</td>
										<td width="60%"><?php echo form_dropdown('member_heir_relationship', $familyrelationship, set_value('member_heir_relationship',$coremember['member_heir_relationship']),'id="member_heir_relationship" class="easyui-combobox" style="width:100%" ');?></td>
									</tr>
									<tr>
										<td width="35%">No Telp</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_heir_mobile_phone" id="member_heir_mobile_phone" autocomplete="off" value="<?php echo set_value('member_heir_mobile_phone',$coremember['member_heir_mobile_phone']);?>" style="width:100%;"/></td>
									</tr>
								</table>
							</div>
							<div class="col-md-6">
								<table style="width:100%">
									<tr>
										<td colspan="3" align="center"><h4><b>Data Pekerjaan</b></h4></td>
									</tr>
									<tr>
										<td width="35%">Tipe Pekerjaan Pemohon<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('member_working_type', $workingtype, set_value('member_working_type',$corememberworking['member_working_type']),'id="member_working_type" class="easyui-combobox" style="width:100%;"');
											?>
												
										</td>
									</tr>
									<tr>
										<td width="35%">Nama Perusahaan</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_company_name" id="member_company_name" autocomplete="off" value="<?php echo set_value('member_company_name',$corememberworking['member_company_name']);?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Bidang Usaha</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_company_specialites" id="member_company_specialites" autocomplete="off" value="<?php echo set_value('member_company_specialities',$corememberworking['member_company_specialities']);?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Jabatan</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_company_job_title" id="member_company_job_title" autocomplete="off" value="<?php echo set_value('member_company_job_title',$corememberworking['member_company_job_title']);?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Masa Kerja</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_company_period" id="member_company_period" autocomplete="off" value="<?php echo set_value('member_company_period',$corememberworking['member_company_period']);?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Alamat Perusahaan</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_company_address" id="member_company_address" autocomplete="off" value="<?php echo set_value('member_company_address',$corememberworking['member_company_address']);?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Kota</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_company_city" id="member_company_city" autocomplete="off" value="<?php echo set_value('member_company_city',$corememberworking['member_company_city']);?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Kode Pos</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_company_postal_code" id="member_company_postal_code" autocomplete="off" value="<?php echo set_value('member_company_postal_code',$corememberworking['member_company_postal_code']);?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Telepon</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_company_phone" id="member_company_phone" autocomplete="off" value="<?php echo set_value('member_company_phone',$corememberworking['member_company_phone']);?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Sub Bidang Usaha</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_business_name" id="member_business_name" autocomplete="off" value="<?php echo set_value('member_business_name',$corememberworking['member_business_name']);?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Skala Usaha</td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('member_business_scale', $businessscale, set_value('member_business_scale',$corememberworking['member_business_scale']),'id="member_business_scale" class="easyui-combobox" style="width:100%"');
											?>
										</td>
									</tr>
									<tr>
										<td width="35%">Lama Usaha</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_business_period" id="member_business_period" autocomplete="off" value="<?php echo set_value('member_business_period',$corememberworking['member_business_period']);?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Kepemilikan Tempat Usaha</td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('member_business_owner', $businessowner, set_value('member_business_owner',$corememberworking['member_business_owner']),'id="member_business_owner" class="easyui-combobox" style="width:100%"');
											?>
										</td>
									</tr>
									<tr>
										<td width="35%">Alamat Tempat Usaha</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_business_address" id="member_business_address" autocomplete="off" value="<?php echo set_value('member_business_address',$corememberworking['member_business_address']);?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Kota</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_business_city" id="member_business_city" autocomplete="off" value="<?php echo set_value('member_business_city',$corememberworking['member_business_city']);?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Kode Pos</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_business_postal_code" id="member_business_postal_code" autocomplete="off" value="<?php echo set_value('member_business_postal_code',$corememberworking['member_business_postal_code']);?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Telepon</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_business_phone" id="member_business_phone" autocomplete="off" value="<?php echo set_value('member_business_phone',$corememberworking['member_business_phone']);?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Penghasilan Perbulan</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_monthly_income" id="member_monthly_income" autocomplete="off" value="<?php echo set_value('member_monthly_income',$corememberworking['member_monthly_income']);?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td colspan="3" align="center"><h4><b>Data Usaha</b></h4></td>
									</tr>
									<tr>
										<td width="35%">Tipe Usaha</td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('partner_working_type', $workingtype, set_value('partner_working_type',$corememberworking['partner_working_type']),'id="partner_working_type" class="easyui-combobox" style="width:100%;"');
											?>
										</td>
									</tr>										
										<tr>
											<td width="35%">Nama Perusahaan</td>
											<td width="5%">:</td>
											<td width="60%"><input type="text" class="easyui-textbox" name="partner_company_name" id="partner_company_name" autocomplete="off" value="<?php echo set_value('partner_company_name',$corememberworking['partner_company_name']);?>" style="width:100%"/></td>
										</tr>
										<tr>
											<td width="35%">Jabatan</td>
											<td width="5%">:</td>
											<td width="60%"><input type="text" class="easyui-textbox" name="partner_company_job_title" id="partner_company_job_title" autocomplete="off" value="<?php echo set_value('partner_company_job_title',$corememberworking['partner_company_job_title']);?>" style="width:100%"/></td>
										</tr>
										<tr>
											<td width="35%">Bidang Usaha</td>
											<td width="5%">:</td>
											<td width="60%"><input type="text" class="easyui-textbox" name="partner_company_specialities" id="partner_company_specialities" autocomplete="off" value="<?php echo set_value('partner_company_specialities',$corememberworking['partner_company_specialities']);?>" style="width:100%"></td>
										</tr>
										<tr>
											<td width="35%">Alamat Perusahaan</td>
											<td width="5%">:</td>
											<td width="60%"><input type="text" class="easyui-textbox" name="partner_company_address" id="partner_company_address" autocomplete="off" value="<?php echo set_value('partner_company_address',$corememberworking['partner_company_address']);?>" style="width:100%"/></td>
										</tr>
										<tr>
											<td width="35%">Telepon</td>
											<td width="5%">:</td>
											<td width="60%"><input type="text" class="easyui-textbox" name="partner_company_phone" id="partner_company_phone" autocomplete="off" value="<?php echo set_value('partner_company_phone',$corememberworking['partner_company_phone']);?>" style="width:100%"/></td>
										</tr>
										<tr>
											<td width="35%">Sub Bidang Usaha</td>
											<td width="5%">:</td>
											<td width="60%"><input type="text" class="easyui-textbox" name="partner_business_name" id="partner_business_name" autocomplete="off" value="<?php echo set_value('partner_business_name',$corememberworking['partner_business_name']);?>" style="width:100%"/></td>
										</tr>
										<tr>
											<td width="35%">Skala Usaha</td>
											<td width="5%">:</td>
											<td width="60%">
												<?php 
													echo form_dropdown('partner_business_scale', $businessscale, set_value('partner_business_scale',$corememberworking['partner_business_scale']),'id="partner_business_scale" class="easyui-combobox" style="width:100%"');
												?>
											</td>
										</tr>
										<tr>
											<td width="35%">Lama Usaha</td>
											<td width="5%">:</td>
											<td width="60%"><input type="text" class="easyui-textbox" name="partner_business_period" id="partner_business_period" autocomplete="off" value="<?php echo set_value('partner_business_period',$corememberworking['partner_business_period']);?>" style="width:100%"/></td>
										</tr>
										<tr>
											<td width="35%">Kepemilikan Tempat Usaha</td>
											<td width="5%">:</td>
											<td width="60%">
												<?php 
													echo form_dropdown('partner_business_owner', $businessowner, set_value('partner_business_owner',$corememberworking['partner_business_owner']),'id="partner_business_owner" class="easyui-combobox" style="width:100%"');
												?>
											</td>
										</tr>
										
									</table>
								</div>
							<div class="col-md-12">
								<div class="form-action right">
									<table width="100%">	 	
										<!-- <tr>
											<td colspan="3" align="center"><h4><b>Simpanan Anggota</b></h4></td>
										</tr>							
										<tr>
											<td width="35%">Simpanan Pokok<span class="required">*</span></td>
											<td width="5%"></td>
											<td width="60%">
												<input type="text" class="easyui-textbox" name="member_principal_savings_view" id="member_principal_savings_view" autocomplete="off" style="width: 100%" />
												<input type="hidden" class="easyui-textbox" name="member_principal_savings" id="member_principal_savings" autocomplete="off" />
											</td>
										</tr>
										<tr>
											<td width="35%">Simpanan Khusus<span class="required">*</span></td>
											<td width="5%"></td>
											<td width="60%">
												<input type="text" class="easyui-textbox" name="member_special_savings_view" id="member_special_savings_view" autocomplete="off" style="width: 100%" />
												<input type="hidden" class="easyui-textbox" name="member_special_savings" id="member_special_savings" autocomplete="off"/>
											</td>
										</tr>
										<tr>
											<td width="35%">Simpanan Wajib<span class="required">*</span></td>
											<td width="5%"></td>
											<td width="60%">
												<input type="text" class="easyui-textbox" name="member_mandatory_savings_view" id="member_mandatory_savings_view" autocomplete="off" style="width: 100%" />
												<input type="hidden" class="easyui-textbox" name="member_mandatory_savings" id="member_mandatory_savings" autocomplete="off"/>
											</td>
										</tr> -->
										 <tr>
											<td width="35%"></td>
											<td width="5%"></td>
											<td width="60%" align="right">
												<button type="button" class="btn red" onClick="reset_edit();"><i class="fa fa-times"></i> Batal</button>
												<button type="submit" class="btn green-jungle"><i class="fa fa-check"></i> Simpan</button>
											</td>
										</tr> 
									 </table>
								</div>
							</div> 
							
								<input type="hidden" class="form-control" name="member_id" id="member_id" placeholder="id" value="<?php echo set_value('member_id',$coremember['member_id']);?>"/>
								<input type="hidden" class="form-control" name="member_working_id" id="member_working_id" placeholder="id" value="<?php echo set_value('member_working_id',$corememberworking['member_working_id']);?>"/>

						</div>	
						<div class="col-md-1"></div>					
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php echo form_close(); ?>