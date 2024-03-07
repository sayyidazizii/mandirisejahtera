<style>
	th, td {
	  padding: 2px;
	  font-size: 14px;
	}
	input:focus { 
	  background-color: 42f483;
	}
</style>
<script>
	base_url = '<?php echo base_url();?>';

	$(document).ready(function(){
		$('#member_no').textbox({
		   collapsible:false,
		   minimizable:false,
		   maximizable:false,
		   closable:false
		});
		
		$('#member_no').textbox('textbox').focus();
	});

  	$(document).ready(function(){
		 $('#province_id').combobox({
			  onChange: function(value){
			  	var province_id   = document.getElementById("province_id").value;
			  	/*alert(province_id);*/
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
				/*alert(value);
      		var city_id   = document.getElementById("city_id").value;
      		alert(city_id);*/
            
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
		$('#city_id').combobox({
			onChange: function(value){
				/*alert(value);
      		var city_id   = document.getElementById("city_id").value;
      		alert(city_id);*/
            
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
				/*alert(value);
      		var city_id   = document.getElementById("city_id").value;
      		alert(city_id);*/
            
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
				/*alert(value);
      		var city_id   = document.getElementById("city_id").value;
      		alert(city_id);*/
            
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

	function reset_add(){
		document.location = base_url+"member/reset-add";
	}

	$(document).ready(function(){
		$('#member_name').textbox({
			onChange: function(value){
				var name   	= 'member_name';

		    	function_elements_add(name, value);
			}
		});

		$('#member_gender').combobox({
			onChange: function(value){
				// alert(value);
				var name   	= 'member_gender';

		    	function_elements_add(name, value);
			}
		});

		$('#member_place_of_birth').textbox({
			onChange: function(value){
				var name   	= 'member_place_of_birth';

		    	function_elements_add(name, value);
			}
		});

		$('#member_date_of_birth').datebox({
			onChange: function(value){
				var name   	= 'member_date_of_birth';

		    	function_elements_add(name, value);
			}
		});

		$('#member_postal_code').textbox({
			onChange: function(value){
				var name   	= 'member_postal_code';

		    	function_elements_add(name, value);
			}
		});

		$('#member_phone').textbox({
			onChange: function(value){
				var name   	= 'member_phone';

		    	function_elements_add(name, value);
			}
		});

		$('#member_job').textbox({
			onChange: function(value){
				var name   	= 'member_job';

		    	function_elements_add(name, value);
			}
		});

		$('#member_identity_no').textbox({
			onChange: function(value){
				var name   	= 'member_identity_no';

		    	function_elements_add(name, value);
			}
		});

		$('#member_mother').textbox({
			onChange: function(value){
				var name   	= 'member_mother';

		    	function_elements_add(name, value);
			}
		});
	});
	
	
</script>
<?php 
	echo form_open('member/process-edit-utility',array('id' => 'myform', 'class' => 'horizontal-form')); 

	$unique 		= $this->session->userdata('unique');
	$data 			= $this->session->userdata('addCoreMember-'.$unique['unique']);
	$member_token 	= $this->session->userdata('coremembertoken-'.$unique['unique']);

	// print_r($data);

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

	if (empty($data['member_mother'])){
		$data['member_mother'] 				= '';
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
			<a href="<?php echo base_url();?>member/add-utility">
				Tambah Anggota 
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
						Form Tambah Anggota
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
							<div class="col-md-1">
								
							</div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">No. Anggota <span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_no" id="member_no" autocomplete="off" style="width:100%;"/>
											<input type="hidden" class="easyui-textbox" name="member_token" id="member_token" autocomplete="off" value="<?php echo $member_token;?>" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Nama Anggota <span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo set_value('member_gender',$data['member_name']);?>" style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">Jenis Kelamin<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
													echo form_dropdown('member_gender', $membergender, set_value('member_gender',$data['member_gender']),'id="member_gender" class="easyui-combobox" style="width:70%;"');
											?>
										</td>
									</tr>
									<tr>
										<td width="35%">Provinsi<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('province_id', $coreprovince, set_value('province_id', $data['province_id']),'id="province_id" class="easyui-combobox" style="width:70%;"');
											?>
										</td>
									</tr>
									<tr>
										<td width="35%">Kabupaten<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<?php
												/*print_r("province_id ");
												print_r($data['province_id']);*/

												if ($data['province_id'] != ''){
													$corecity = create_double($this->CoreMember_model->getCoreCity($data['province_id']), 'city_id', 'city_name');

													echo form_dropdown('city_id', $corecity, set_value('city_id', $data['city_id']),'id="city_id" class="easyui-combobox" onChange="function_elements_add(this.name, this.value);" style="width:70%;"');
												} else {
											?>
												<select name="city_id" id="city_id" class="easyui-combobox" style="width: 70%">
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
											

											<select name="kecamatan_id" id="kecamatan_id" class="easyui-combobox" style="width: 70%">
													<option value="">--Pilih Salah Satu--</option>
												</select>
												
										</td>
									</tr>
									<tr>
										<td width="35%">Kelurahan<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											

											<select name="kelurahan_id" id="kelurahan_id" class="easyui-combobox" style="width: 70%">
													<option value="">--Pilih Salah Satu--</option>
												</select>
												
										</td>
									</tr>
									<tr>
										<td width="35%">Dusun<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											

											<select name="dusun_id" id="dusun_id" class="easyui-combobox" style="width: 70%">
													<option value="">--Pilih Salah Satu--</option>
												</select>
												
										</td>
									</tr>
									<tr>
										<td width="35%">Tempat Lahir<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_place_of_birth" id="member_place_of_birth" value="<?php echo set_value('member_place_of_birth',$data['member_place_of_birth']);?>" style="width:100%;"></td>
									</tr>
									<tr>
										<td width="35%">Tanggal Lahir<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input class="easyui-datebox" data-options="formatter:myformatter,parser:myparser" type="text" name="member_date_of_birth" id="member_date_of_birth" value="<?php echo tgltoview($data['member_date_of_birth']);?>" style="width:70%;"/></td>
									</tr>
									<tr>
										<td width="35%">Alamat<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><textarea rows="2" name="member_address" id="member_address" class="easyui-textarea"  style="width:100%;"><?php echo $data['member_address'];?></textarea></td>
									</tr>
								</table>
							</div>
							<div class="col-md-1">
							</div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">Kode Pos</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_postal_code" id="member_postal_code" autocomplete="off" value="<?php echo set_value('member_postal_code',$data['member_postal_code']);?>"  style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">Nomor Telp<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_phone" id="member_phone" autocomplete="off" value="<?php echo set_value('member_phone',$data['member_phone']);?>" style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">Sifat Anggota<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('member_character', $membercharacter, set_value('member_character',$data['member_character']),'id="member_character" class="easyui-combobox" style="width:70%;"');
											?>
												
										</td>
									</tr>
									<tr>
										<td width="35%">Pekerjaan</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_job" id="member_job" autocomplete="off" value="<?php echo set_value('member_job',$data['member_job']);?>" style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">Identitas</td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('member_identity', $memberidentity, set_value('member_identity', $data['member_identity']),'id="member_identity" class="easyui-combobox" style="width:70%;"');
											?>
										</td>
									</tr>
									<tr>
										<td width="35%">Nomor Identitas</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_identity_no" id="member_identity_no" autocomplete="off" value="<?php echo set_value('member_identity_no',$data['member_identity_no']);?>" style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">Nama Ibu Kandung</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_mother" id="member_mother" autocomplete="off" value="<?php echo set_value('member_mother',$data['member_mother']);?>" style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%"></td>
										<td width="5%"></td>
										<td width="60%"></td>
									</tr>
									<tr>
										<td width="35%"></td>
										<td width="5%"></td>
										<td width="60%" align="right">
											<button type="button" class="btn red" onClick="reset_add();"><i class="fa fa-times"></i> Batal</button>
											<button type="submit" class="btn green-jungle"><i class="fa fa-check"></i> Simpan</button>
										</td>
									</tr>
								</table>
							</div>
						</div>						
					</div>
				</div>
			 </div>
		</div>
	</div>
</div>

<script type="text/javascript">
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
    </script>
<?php echo form_close(); ?>