<style>
	th, td {
	padding: 2px;
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

	function toRp(number) {
		var number = number.toString(), 
		rupiah = number.split('.')[0], 
		cents = (number.split('.')[1] || '') +'00';
		rupiah = rupiah.split('').reverse().join('')
			.replace(/(\d{3}(?!$))/g, '$1,')
			.split('').reverse().join('');
		return rupiah + '.' + cents.slice(0, 2);
	}

	
	$(document).ready(function(){
		
		$('#member_name').textbox({
		collapsible:false,
		minimizable:false,
		maximizable:false,
		closable:false
		});
		
		$('#member_name').textbox('textbox').focus();
	});

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

	$(document).ready(function(){
		$('#member_class_id').combobox({
			onChange: function(value){
				function_elements_add('member_class_id', value);
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
					function_elements_add('member_class_mandatory_savings', data);
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
				function_elements_add('company_id', value);
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
					function_elements_add('member_company_mandatory_savings', data);
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
				function_elements_add('member_company_mandatory_savings', value);
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
				function_elements_add('member_class_mandatory_savings', value);
				$('#member_class_mandatory_savings_view').textbox({
					value: toRp(member_class_mandatory_savings_view),
				});
				$('#member_class_mandatory_savings').textbox({
					value: member_class_mandatory_savings_view,
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

	
	$('#member_monthly_income_view').textbox({
		onChange: function(value){
			var name   	= 'member_monthly_income';
			var name2   = 'member_monthly_income_view';
			var payment_type_id = +document.getElementById("payment_type_id").value;
			
			var tampil = toRp(value);
			$('#member_monthly_income').textbox('setValue', value);
			$('#member_monthly_income_view').textbox('setValue', tampil);

			function_elements_add(name, value);
			function_elements_add(name2, tampil);
			
		}
	});


	function reset_add(){
		document.location = base_url+"member/reset-add";
	}

	$(document).ready(function(){
		
		$('#member_home_status').combobox({
			onChange: function(value){
			function_elements_add('member_home_status', value);
		}});
		$('#member_heir_relationship').combobox({
			onChange: function(value){
			function_elements_add('member_heir_relationship', value);
		}});
		$('#member_marital_status').combobox({
			onChange: function(value){
			function_elements_add('member_marital_status', value);
		}});
		$('#member_last_education').combobox({
			onChange: function(value){
			function_elements_add('member_last_education', value);
		}});
		$('#member_heir_mobile_phone').textbox({
			onChange: function(value){
				function_elements_add('member_heir_mobile_phone', value);
			}
		});
		$('#member_heir').textbox({
			onChange: function(value){
				function_elements_add('member_heir', value);
			}
		});
		$('#member_email').textbox({
			onChange: function(value){
				function_elements_add('member_email', value);
			}
		});
		$('#member_partner_name').textbox({
			onChange: function(value){
				function_elements_add('member_partner_name', value);
			}
		});
		$('#member_long_stay').textbox({
			onChange: function(value){
				function_elements_add('member_long_stay', value);
			}
		});
		$('#member_partner_identity_no').textbox({
			onChange: function(value){
				function_elements_add('member_partner_identity_no', value);
			}
		});
		$('#member_dependent').textbox({
			onChange: function(value){
			function_elements_add('member_dependent', value);
		}});
		$('#member_name').textbox({
			onChange: function(value){
				var name   	= 'member_name';

				function_elements_add(name, value);
			}
		});
		$('#member_nick_name').textbox({
			onChange: function(value){
				var name   	= 'member_nick_name';

				function_elements_add(name, value);
			}
		});
		$('#member_gender').combobox({
			onChange: function(value){
				
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
		$('#member_address').on('change', function() {
			function_elements_add('member_address', $(this).val());
		});
		$('#member_address_now').on('change', function() {
			function_elements_add('member_address_now', $(this).val());
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
		$('#member_company_name').textbox({
			onChange: function(value){
				function_elements_add('member_company_name', value);
			}
		});
		$('#member_company_specialites').textbox({
			onChange: function(value){
				function_elements_add('member_company_specialites', value);
			}
		});
		$('#member_company_job_title').textbox({
			onChange: function(value){
				function_elements_add('member_company_job_title', value);
			}
		});
		$('#member_company_phone').textbox({
			onChange: function(value){
				function_elements_add('member_company_phone', value);
			}
		});
		$('#member_company_phone').textbox({
			onChange: function(value){
				function_elements_add('member_company_phone', value);
			}
		});
		$('#member_company_address').textbox({
			onChange: function(value){
				function_elements_add('member_company_address', value);
			}
		});
		$('#member_company_city').textbox({
			onChange: function(value){
				function_elements_add('member_company_city', value);
			}
		});
		$('#member_company_period').textbox({
			onChange: function(value){
				function_elements_add('member_company_period', value);
			}
		});
		$('#member_company_postal_code').textbox({
			onChange: function(value){
				function_elements_add('member_company_postal_code', value);
			}
		});
		$('#member_business_name').textbox({
			onChange: function(value){
				function_elements_add('member_business_name', value);
			}
		});
		
		$('#member_business_scale').combobox({
			onChange: function(value){
				function_elements_add('member_business_scale', value);
			}
		});
		$('#member_business_period').textbox({
			onChange: function(value){
				function_elements_add('member_business_period', value);
			}
		});
		$('#member_business_address').textbox({
			onChange: function(value){
				function_elements_add('member_business_address', value);
			}
		});
		$('#member_business_city').textbox({
			onChange: function(value){
				function_elements_add('member_business_city', value);
			}
		});
		$('#member_business_owner').combobox({
			onChange: function(value){
				function_elements_add('member_business_owner', value);
			}
		});
		$('#member_business_phone').textbox({
			onChange: function(value){
				function_elements_add('member_business_phone', value);
			}
		});
		$('#member_business_postal_code').textbox({
			onChange: function(value){
				function_elements_add('member_business_postal_code', value);
			}
		});
		$('#member_monthly_income').textbox({
			onChange: function(value){
				function_elements_add('member_monthly_income', value);
			}
		});
		// member_business_scale
		$('#member_business_scale').textbox({
			onChange: function(value){
			function_elements_add('member_business_scale', value);
		}});
		$('#member_business_owner').textbox({
			onChange: function(value){
			function_elements_add('member_business_owner', value);
		}});
		$('#partner_company_name').textbox({
			onChange: function(value){
			function_elements_add('partner_company_name', value);
		}});
		$('#partner_business_owner').combobox({
			onChange: function(value){
			function_elements_add('partner_business_owner', value);
		}});
		$('#partner_company_address').textbox({
			onChange: function(value){
			function_elements_add('partner_company_address', value);
		}});
		$('#partner_company_phone').textbox({
			onChange: function(value){
			function_elements_add('partner_company_phone', value);
		}});
		$('#partner_company_job_title').textbox({
			onChange: function(value){
			function_elements_add('partner_company_job_title', value);
		}});
		$('#partner_business_name').textbox({
			onChange: function(value){
			function_elements_add('partner_business_name', value);
		}});
		$('#partner_business_scale').combobox({
			onChange: function(value){
			function_elements_add('partner_business_scale', value);
		}});
		$('#partner_business_period').textbox({
			onChange: function(value){
			function_elements_add('partner_business_period', value);
		}});
		$('#partner_company_specialities').textbox({
			onChange: function(value){
			function_elements_add('partner_company_specialities', value);
		}});
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
<?php 
	echo form_open('member/process-add',array('id' => 'myform', 'class' => 'horizontal-form')); 

	$unique 		= $this->session->userdata('unique');
	$data 			= $this->session->userdata('addCoreMember-'.$unique['unique']);
	$member_token 	= $this->session->userdata('coremembertoken-'.$unique['unique']);
	
	if(!is_array($data)){
		$data['member_name']				= '';
		$data['member_gender'] 				= 9;
		$data['province_id'] 				= 0;
		$data['kelurahan_id'] 				= 0;
		$data['city_id'] 					= 0;
		$data['kecamatan_id'] 				= 0;
		$data['member_place_of_birth'] 		= '';
		$data['member_date_of_birth'] 		= date("Y-m-d");
		$data['member_address'] 			= '';
		$data['member_postal_code'] 		= '';
		$data['member_phone'] 				= '';
		$data['member_job'] 				= '';
		$data['member_character'] 			= 9;
		$data['member_identity'] 			= 9;
		$data['member_identity_no'] 		= '';
		$data['member_mother'] 				= '';
		$data['member_address_now'] 		= '';
		$data['member_home_phone'] 			= '';
		$data['member_dependent'] 			= '';
		$data['member_home_status'] 		= 0;
		$data['member_vehicle'] 			= 0;
		$data['member_last_education'] 		= 0;
		$data['member_long_stay'] 			= '';
		$data['member_unit_user'] 			= 0;
		$data['member_working_type'] 		= 0;
		$data['member_company_name'] 		= '';
		$data['member_partner_name'] 		= '';
		$data['member_email'] 				= '';
		$data['member_company_specialites'] = '';
		$data['member_company_job_title'] 	= '';
		$data['member_company_period'] 		= '';
		$data['member_company_address'] 	= '';
		$data['member_company_city'] 		= '';
		$data['member_company_phone'] 		= '';
		$data['partner_company_specialities']= '';
		$data['partner_company_job_title'] 	= '';
		$data['partner_company_address'] 	= '';
		$data['partner_company_phone'] 		= '';
		$data['partner_business_name'] 		= '';
		$data['partner_business_scale'] 	= 0;
		$data['partner_business_period'] 	= '';
		$data['partner_business_owner'] 	= 0;
		$data['member_partner_identity_no'] = 0;
		$data['member_nick_name']			= '';
		$data['member_heir']				= '';
		$data['member_heir_mobile_phone']	= '';
		$data['member_heir_relationship']	= '';
		$data['member_monthly_income']		= '';
		$data['member_monthly_income_view']	= '';
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
							
						?>
						<div class="row">
							<!-- <div class="col-md-1">
								
							</div> -->
							<div class="col-md-5">
								<table style="width:100%">
									<tr>
										<td style="width:100%" colspan="3" align="center"><h4><b>Data Anggota</b></h4></td>
									</tr>
									<tr>
										<td width="35%">Nama Lengkap (sesuai KTP) <span class="required" style="color : red">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
										<input type="hidden" class="easyui-textbox" name="member_token" id="member_token" autocomplete="off" value="<?php echo set_value('member_token',$member_token);?>" style="width:100%;"/>
											<input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo set_value('member_name',$data['member_name']);?>" index="0" style="width:100%;"/>
										
										</td>
									</tr>
									<tr>
										<td width="35%">Nama Panggilan </td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_nick_name" id="member_nick_name" autocomplete="off" value="<?php echo set_value('member_nick_name',$data['member_nick_name']);?>" index="0" style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">Jenis Kelamin<span class="required" style="color : red">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												
													echo form_dropdown('member_gender', $membergender, set_value('member_gender',$data['member_gender']),'id="member_gender" class="easyui-combobox" style="width:100%;"');
											?>
										</td>
									</tr>
									<tr>
										<td width="35%">Tempat Lahir<span class="required" style="color : red">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_place_of_birth" id="member_place_of_birth" value="<?php echo set_value('member_place_of_birth',$data['member_place_of_birth']);?>" style="width:100%;"></td>
									</tr>
									<tr>
										<td width="35%">Tanggal Lahir<span class="required" style="color : red">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input class="easyui-datebox" data-options="formatter:myformatter,parser:myparser" type="text" name="member_date_of_birth" id="member_date_of_birth" value="<?php echo tgltoview($data['member_date_of_birth']);?>" style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">Provinsi<span class="required" style="color : red">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('province_id', $coreprovince, set_value('province_id', $data['province_id']),'id="province_id" class="easyui-combobox" style="width:100%;"');
											?>
										</td>
									</tr>
									<tr>
										<td width="35%">Kabupaten<span class="required" style="color : red">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<?php
												/*print_r("province_id ");
												print_r($data['province_id']);*/

												if ($data['city_id'] != ''){
													$corecity = create_double($this->CoreMember_model->getCoreCity($data['province_id']), 'city_id', 'city_name');

													echo form_dropdown('city_id', $corecity, set_value('city_id', $data['city_id']),'id="city_id" class="easyui-combobox" onChange="function_elements_add(this.name, this.value);" style="width:100%;"');
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
										<td width="35%">Kecamatan<span class="required" style="color : red">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
										
												if ($data['city_id'] != ''){
													$corekecamatan = create_double($this->CoreMember_model->getCoreKecamatan($data['city_id']), 'kecamatan_id', 'kecamatan_name');
													echo form_dropdown('kecamatan_id', $corekecamatan, set_value('kecamatan_id', $data['kecamatan_id']),'id="kecamatan_id" class="easyui-combobox" style="width:100%;"');
												} else {
											?><select name="kecamatan_id" id="kecamatan_id" class="easyui-combobox" style="width: 100%">
													<option value="">--Pilih Salah Satu--</option>
												</select>
											<?php } ?>
			
										</td>
									</tr>
									<tr>
										<td width="35%">Kelurahan<span class="required" style="color : red">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											
												<?php 
												if ($data['kecamatan_id'] != ''){
													$corekelurahan = create_double($this->CoreMember_model->getCoreKelurahan($data['kecamatan_id']), 'kelurahan_id', 'kelurahan_name');
													echo form_dropdown('kelurahan_id', $corekelurahan, set_value('kelurahan_id', $data['kelurahan_id']),'id="kelurahan_id" class="easyui-combobox" style="width:100%;"');
												} else {
											?><select name="kelurahan_id" id="kelurahan_id" class="easyui-combobox" style="width: 100%">
													<option value="">--Pilih Salah Satu--</option>
												</select>
											<?php } ?>
										</td>
									</tr>
									
									<tr>
										<td width="35%">Alamat Sesuai KTP<span class="required" style="color : red">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><textarea rows="3" name="member_address" id="member_address" class="easyui-textarea"  style="width:100%;"><?php echo $data['member_address'];?></textarea></td>
									</tr>
									<tr>
										<td width="35%">Alamat Tinggal Sekarang<span class="required" style="color : red">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><textarea rows="3" name="member_address_now" id="member_address_now" class="easyui-textarea"  style="width:100%;"><?php echo $data['member_address_now'];?></textarea></td>
									</tr>
									<tr>
										<td width="35%">Kode Pos</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_postal_code" id="member_postal_code" autocomplete="off" value="<?php echo set_value('member_postal_code',$data['member_postal_code']);?>"  style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">Status Pernikahan</td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('member_marital_status', $maritalstatus, set_value('member_marital_status',$data['member_marital_status']),'id="member_marital_status" class="easyui-combobox" style="width:100%;"');
											?>
												
										</td>
									</tr>
									<tr>
										<td width="35%">No. KTP/SIM</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_identity_no" id="member_identity_no" autocomplete="off" value="<?php echo set_value('member_identity_no',$data['member_identity_no']);?>" style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">No. KTP/SIM Pengampu</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_partner_identity_no" id="member_partner_identity_no" autocomplete="off" value="<?php echo set_value('member_partner_identity_no',$data['member_partner_identity_no']);?>" style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">Nomor HP</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_phone" id="member_phone" autocomplete="off" value="<?php echo set_value('member_phone',$data['member_phone']);?>" style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">Nama Ibu Kandung<span class="required" style="color : red">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_mother" id="member_mother" autocomplete="off" value="<?php echo set_value('member_mother',$data['member_mother']);?>" style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">Simpanan Wajib<span class="required" style="color : red">*</span></td>
										<td width="5%">:</td>
										<!-- <td width="60%">
											<?php 
												echo form_dropdown('member_class_id', $corememberclass, set_value('member_class_id',$data['member_class_id']),'id="member_class_id" class="easyui-combobox" style="width:100%;"');
											?>
												
										</td> -->
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_class_mandatory_savings_view" id="member_class_mandatory_savings_view" value="<?php echo set_value('member_class_mandatory_savings_view',nominal($preferencecompany['member_mandatory_savings']));?>" style="width:100%;" readonly/>
											<input type="hidden" class="easyui-textbox" name="member_class_mandatory_savings" id="member_class_mandatory_savings" value="<?php echo set_value('member_class_mandatory_savings',$preferencecompany['member_mandatory_savings']);?>" style="width:100%;"/>
										</td>
									</tr>
									<!-- <tr>
										<td width="35%"></td>
										<td width="5%"></td>
									</tr> -->
									<!-- <tr>
										<td width="35%">Iuran Wajib Perusahaan</td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('company_id', $corecompany, set_value('company_id',$data['company_id']),'id="company_id" class="easyui-combobox" style="width:100%;"');
											?>
												
										</td>
									</tr>
									<tr>
										<td width="35%"></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_company_mandatory_savings_view" id="member_company_mandatory_savings_view"  value="<?php echo set_value('member_company_mandatory_savings',$data['member_company_mandatory_savings']);?>"  autocomplete="off" style="width:100%;"/>
											<input type="hidden" class="easyui-textbox" name="member_company_mandatory_savings" id="member_company_mandatory_savings"  value="<?php echo set_value('member_company_mandatory_savings',$data['member_company_mandatory_savings']);?>"  autocomplete="off" style="width:100%;"/>
										</td>
									</tr> -->
									<tr>
										<td width="35%">Jumlah Tanggungan</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_dependent" id="member_dependent" autocomplete="off" value="<?php echo set_value('member_dependent',$data['member_dependent']);?>" style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">Status Rumah</td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('member_home_status', $homestatus, set_value('member_home_status',$data['member_home_status']),'id="member_home_status" class="easyui-combobox" style="width:100%;"');
											?>
												
										</td>
									</tr>
									<tr>
										<td width="35%">Lama Menetap</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_long_stay" id="member_long_stay" autocomplete="off" value="<?php echo set_value('member_long_stay',$data['member_long_stay']);?>" style="width:100%;"/></td>
									</tr>
									<!-- <tr>
										<td width="35%">Kendaraan yang Dimiliki<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('member_vehicle', $membervehicle, set_value('member_vehicle',$data['member_vehicle']),'id="member_vehicle" class="easyui-combobox" style="width:100%;"');
											?>
												
										</td>
									</tr> -->
									<tr>
										<td width="35%">Pendidikan Terakhir</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('member_last_education', $lasteducation, set_value('member_last_education',$data['member_last_education']),'id="member_last_education" class="easyui-combobox" style="width:100%;"');
											?>
												
										</td>
									</tr>
									<!-- <tr>
										<td width="35%">Pengguna Unit</td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('member_unit_user', $unituser, set_value('member_unit_user',$data['member_unit_user']),'id="member_unit_user" class="easyui-combobox" style="width:100%;"');
											?>
												
										</td>
									</tr> -->
									<tr>
										<td width="35%">Nama Pengampu</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_partner_name" id="member_partner_name" autocomplete="off" value="<?php echo set_value('member_partner_name',$data['member_partner_name']);?>" style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">Email</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_email" id="member_email" autocomplete="off" value="<?php echo set_value('member_email',$data['member_email']);?>" style="width:100%;"/></td>
									</tr>
									<tr>
										<td colspan="3" align="left"><b>Ahli Waris</b></td>
									</tr>
									<tr>
										<td width="35%">Nama</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_heir" id="member_heir" autocomplete="off" value="<?php echo set_value('member_heir',$data['member_heir']);?>" style="width:100%;"/></td>
									</tr>
									<tr>
										<td width="35%">Hubungan</td>
										<td width="5%">:</td>
										<td width="60%"><?php echo form_dropdown('member_heir_relationship', $familyrelationship, set_value('member_heir_relationship',$data['member_heir_relationship']),'id="member_heir_relationship" class="easyui-combobox" style="width:100%" ');?></td>
									</tr>
									<tr>
										<td width="35%">No Telp</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_heir_mobile_phone" id="member_heir_mobile_phone" autocomplete="off" value="<?php echo set_value('member_heir_mobile_phone',$data['member_heir_mobile_phone']);?>" style="width:100%;"/></td>
									</tr>
								</table>
							</div>
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table style="width:100%">
									<tr>
										<td colspan="3" align="center"><h4><b>Data Pekerjaan</b></h4></td>
									</tr>
									<tr>
										<td width="35%">Tipe Pekerjaan Pemohon</td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('member_working_type', $workingtype, set_value('member_working_type',$data['member_working_type']),'id="member_working_type" class="easyui-combobox" style="width:100%;"');
											?>
												
										</td>
									</tr>
									<tr>
										<td width="35%">Nama Perusahaan</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_company_name" id="member_company_name" autocomplete="off" <?php echo ($data['member_working_type'] == 3) ? 'readonly' : '';  ?> value="<?php echo ($data['member_working_type'] == 1 || $data['member_working_type'] == 2) ? set_value('member_company_name',$data['member_company_name']) : ' ' ?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Bidang Usaha</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_company_specialites" id="member_company_specialites" autocomplete="off" <?php echo ($data['member_working_type'] == 3) ? 'readonly' : '';  ?> value="<?php echo ($data['member_working_type'] == 1 || $data['member_working_type'] == 2) ? set_value('member_company_specialites',$data['member_company_specialites']) : ''?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Jabatan</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_company_job_title" id="member_company_job_title" autocomplete="off" <?php echo ($data['member_working_type'] == 3) ? 'readonly' : '';  ?> value="<?php echo ($data['member_working_type'] == 1 || $data['member_working_type'] == 2) ? set_value('member_company_job_title',$data['member_company_job_title']) : ''?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Masa Kerja</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_company_period" id="member_company_period" autocomplete="off" <?php echo ($data['member_working_type'] == 3) ? 'readonly' : '';  ?> value="<?php echo ($data['member_working_type'] == 1 || $data['member_working_type'] == 2) ? set_value('member_company_period',$data['member_company_period']) : '' ?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Alamat Perusahaan</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_company_address" id="member_company_address" autocomplete="off" <?php echo ($data['member_working_type'] == 3) ? 'readonly' : '';  ?> value="<?php echo ($data['member_working_type'] == 1 || $data['member_working_type'] == 2) ? set_value('member_company_address',$data['member_company_address']) : ''?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Kota</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_company_city" id="member_company_city" autocomplete="off" <?php echo ($data['member_working_type'] == 3) ? 'readonly' : '';  ?> value="<?php echo($data['member_working_type'] == 1 || $data['member_working_type'] == 2) ? set_value('member_company_city',$data['member_company_city']): ''?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Kode Pos</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_company_postal_code" id="member_company_postal_code" autocomplete="off" <?php echo ($data['member_working_type'] == 3) ? 'readonly' : '';  ?> value="<?php echo ($data['member_working_type'] == 1 || $data['member_working_type'] == 2) ?  set_value('member_company_postal_code',$data['member_company_postal_code']): ''	?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Telepon</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_company_phone" id="member_company_phone" autocomplete="off" <?php echo ($data['member_working_type'] == 3) ? 'readonly' : '';  ?> value="<?php echo ($data['member_working_type'] == 1 || $data['member_working_type'] == 2) ?  set_value('member_company_phone',$data['member_company_phone']) : ''?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Sub Bidang Usaha</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_business_name" id="member_business_name" autocomplete="off" <?php echo ($data['member_working_type'] == 1 || $data['member_working_type'] == 2) ? 'readonly' : '' ?> value="<?php echo ($data['member_working_type'] == 3) ?  set_value('member_business_name',$data['member_business_name']) : '' ?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Skala Usaha</td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												$readonly = ($data['member_working_type'] == 1 || $data['member_working_type'] == 2) ? 'readonly' : '';
												$member_business_scale_temp = ($data['member_working_type'] == 3) ? $data['member_business_scale'] : '' ;
												echo form_dropdown('member_business_scale', $businessscale, set_value('member_business_scale',$member_business_scale_temp),'id="member_business_scale" class="easyui-combobox" style="width:100%"'.$readonly.'');
											?>
										</td>
									</tr>
									<tr>
										<td width="35%">Lama Usaha</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_business_period" id="member_business_period" autocomplete="off" <?php echo ($data['member_working_type'] == 1 || $data['member_working_type'] == 2) ? 'readonly' : '' ?> value="<?php echo ($data['member_working_type'] == 3) ? set_value('member_business_period',$data['member_business_period']) : ''?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Kepemilikan Tempat Usaha</td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
											$readonly = ($data['member_working_type'] == 1 || $data['member_working_type'] == 2) ? 'readonly' : '';
											$member_business_owner_temp = ($data['member_working_type'] == 3) ? $data['member_business_owner'] : '' ;
												echo form_dropdown('member_business_owner', $businessowner, set_value('member_business_owner',$member_business_owner_temp),'id="member_business_owner" class="easyui-combobox" style="width:100%"'.$readonly.'');
											?>
										</td>
									</tr>
									<tr>
										<td width="35%">Alamat Tempat Usaha</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_business_address" id="member_business_address" autocomplete="off" <?php echo ($data['member_working_type'] == 1 || $data['member_working_type'] == 2) ? 'readonly' : '' ?> value="<?php echo ($data['member_working_type'] == 3) ? set_value('member_business_address',$data['member_business_address']): ''?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Kota</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_business_city" id="member_business_city" autocomplete="off" <?php echo ($data['member_working_type'] == 1 || $data['member_working_type'] == 2) ? 'readonly' : '' ?> value="<?php echo ($data['member_working_type'] == 3) ? set_value('member_business_city',$data['member_business_city']):''?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Kode Pos</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_business_postal_code" id="member_business_postal_code" autocomplete="off" <?php echo ($data['member_working_type'] == 1 || $data['member_working_type'] == 2) ? 'readonly' : '' ?> value="<?php echo ($data['member_working_type'] == 3) ? set_value('member_business_postal_code',$data['member_business_postal_code']) : ''?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Telepon</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_business_phone" id="member_business_phone" autocomplete="off" <?php echo ($data['member_working_type'] == 1 || $data['member_working_type'] == 2) ? 'readonly' : '' ?> value="<?php echo ($data['member_working_type'] == 3) ? set_value('member_business_phone',$data['member_business_phone']):''?>" style="width:100%"/></td>
									</tr>
									<tr>
										<td width="35%">Penghasilan Perbulan</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_monthly_income" id="member_monthly_income" autocomplete="off" value="<?php echo set_value('member_monthly_income',$data['member_monthly_income']);?>" style="width:100%"/>
										</td>
									</tr>
									<tr>
										<td colspan="3" height="100%"></td>
									</tr>
									<tr>
										<td colspan="3" align="center"><h4><b>Data Usaha</b></h4></td>
									</tr>
									<tr>
										<td width="35%">Tipe Usaha</td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
											// partner_working_type
												echo form_dropdown('partner_working_type', $workingtype, set_value('partner_working_type',$data['partner_working_type']),'id="partner_working_type" class="easyui-combobox" style="width:100%;"');
											?>
										</td>
									</tr>
										
										<tr>
											<td width="35%">Nama Perusahaan</td>
											<td width="5%">:</td>
											<td width="60%"><input type="text" class="easyui-textbox" name="partner_company_name" id="partner_company_name" autocomplete="off" <?php echo ($data['partner_working_type'] == 1 || $data['partner_working_type'] == 2) ? '' : 'readonly' ?>  value="<?php echo ($data['partner_working_type'] == 1 || $data['partner_working_type'] == 2) ? set_value('partner_company_name',$data['partner_company_name']) : '' ?>" style="width:100%"/></td>
										</tr>
										<tr>
											<td width="35%">Jabatan</td>
											<td width="5%">:</td>
											<td width="60%"><input type="text" class="easyui-textbox" name="partner_company_job_title" id="partner_company_job_title" autocomplete="off" <?php echo ($data['partner_working_type'] == 1 || $data['partner_working_type'] == 2) ? '' : 'readonly' ?>  value="<?php echo ($data['partner_working_type'] == 1 || $data['partner_working_type'] == 2) ? set_value('partner_company_job_title',$data['partner_company_job_title']) : '' ?>" style="width:100%"/></td>
										</tr>
										<tr>
											<td width="35%">Bidang Usaha</td>
											<td width="5%">:</td>
											<td width="60%"><input type="text" class="easyui-textbox" name="partner_company_specialities" id="partner_company_specialities" autocomplete="off" <?php echo ($data['partner_working_type'] == 1 || $data['partner_working_type'] == 2) ? '' : 'readonly' ?>  value="<?php echo ($data['partner_working_type'] == 1 || $data['partner_working_type'] == 2) ? set_value('partner_company_specialities',$data['partner_company_specialities']) : '' ?>" style="width:100%"></td>
										</tr>
										<tr>
											<td width="35%">Alamat Perusahaan</td>
											<td width="5%">:</td>
											<td width="60%"><input type="text" class="easyui-textbox" name="partner_company_address" id="partner_company_address" autocomplete="off" <?php echo ($data['partner_working_type'] == 1 || $data['partner_working_type'] == 2) ? '' : 'readonly' ?>  value="<?php echo ($data['partner_working_type'] == 1 || $data['partner_working_type'] == 2) ? set_value('partner_company_address',$data['partner_company_address']) : '' ?>" style="width:100%"/></td>
										</tr>
										<tr>
											<td width="35%">Telepon</td>
											<td width="5%">:</td>
											<td width="60%"><input type="text" class="easyui-textbox" name="partner_company_phone" id="partner_company_phone" autocomplete="off" <?php echo ($data['partner_working_type'] == 1 || $data['partner_working_type'] == 2) ? '' : 'readonly' ?>  value="<?php echo ($data['partner_working_type'] == 1 || $data['partner_working_type'] == 2) ? set_value('partner_company_phone',$data['partner_company_phone']) : '' ?>" style="width:100%"/></td>
										</tr>
										<tr>
											<td width="35%">Sub Bidang Usaha</td>
											<td width="5%">:</td>
											<td width="60%"><input type="text" class="easyui-textbox" name="partner_business_name" id="partner_business_name" autocomplete="off" <?php echo ($data['partner_working_type'] == 3) ? '' : 'readonly' ?> value="<?php echo ($data['partner_working_type'] == 3) ? set_value('partner_business_name',$data['partner_business_name']) : '' ?>" style="width:100%"/></td>
										</tr>
										<tr>
											<td width="35%">Skala Usaha</td>
											<td width="5%">:</td>
											<td width="60%">
												<?php 
													$readonly = ($data['partner_working_type'] == 1 || $data['partner_working_type'] == 2) ? 'readonly' : '';
													$partner_business_scale_temp = ($data['partner_working_type'] == 3) ? $data['partner_business_scale'] : '' ;
													echo form_dropdown('partner_business_scale', $businessscale, set_value('partner_business_scale',$partner_business_scale_temp),'id="partner_business_scale" class="easyui-combobox" '.$readonly.' style="width:100%"');
												?>
											</td>
										</tr>
										<tr>
											<td width="35%">Lama Usaha</td>
											<td width="5%">:</td>
											<td width="60%"><input type="text" class="easyui-textbox" name="partner_business_period" id="partner_business_period" autocomplete="off" <?php echo ($data['partner_working_type'] == 3) ? '' : 'readonly' ?> value="<?php echo ($data['partner_working_type'] == 3) ? set_value('partner_business_period',$data['partner_business_period']) : '' ?>" style="width:100%"/></td>
										</tr>
										<tr>
											<td width="35%">Kepemilikan Tempat Usaha</td>
											<td width="5%">:</td>
											<td width="60%">
												<?php 
													$readonly = ($data['partner_working_type'] == 1 || $data['partner_working_type'] == 2) ? 'readonly' : '';
													$partner_business_owner_temp = ($data['partner_working_type'] == 3) ? $data['partner_business_owner'] : '' ;
													echo form_dropdown('partner_business_owner', $businessowner, set_value('partner_business_owner',$partner_business_owner_temp),'id="partner_business_owner" class="easyui-combobox" '.$readonly.' style="width:100%"');
												?>
											</td>
										</tr>
									</table>
								</div>
								<table style="width:100%;">
									<td width="35%"></td>
									<td width="5%"></td>
									<td width="60%" align="right">
										<button type="button" class="btn red" onClick="reset_add();"><i class="fa fa-times"></i> Batal</button>
										<button type="submit" class="btn green-jungle"><i class="fa fa-check"></i> Simpan</button>
									</td>
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

	$(document).ready(function () {
		

		$('input').on("keypress", function(e) {
            /* ENTER PRESSED*/
            if (e.keyCode == 13) {
                /* FOCUS ELEMENT */
				
                let inputs = $(this).parents("form").eq(0).find(":input");
                let idx = inputs.index(this);
				

                if (idx == inputs.length - 1) {
                    inputs[0].select()
                } else {
                    inputs[idx + 3].focus(); //  handles submit buttons
                    inputs[idx + 3].select();
                }
                return false;
            }
        });
		

		$('#myform').on('submit', function(e){
			e.preventDefault();
			
			member_name 			= $('#member_name').val();
			member_place_of_birth 	= $('#member_place_of_birth').val();
			member_date_of_birth 	= $('#member_date_of_birth').val();

			$.ajax({
				type : "POST",
				dataType: 'json',
				url  : "<?php echo base_url(); ?>member/check-name-placeofbirth",
				data : {
					'member_name' : member_name,
					'member_place_of_birth' : member_place_of_birth,
					'member_date_of_birth' : member_date_of_birth
				},
				success: function(data){
					if(data.data != null){
						
							if (confirm(`Ada data yang mirip ! | Nama : ${data.data.member_name} , Tanggal Lahir : ${data.data.member_date_of_birth} , Tempat Lahir : ${data.data.member_place_of_birth} ,  | Apakah anda yakin ingin menambahkan member in ? `)) {
								processAddDataMember();
							}else {
								
							}
						}else{
							processAddDataMember();
						}
					}
				});
		});
		function processAddDataMember() {
			$.ajax({
				type : "POST",
				url  : "<?php echo base_url(); ?>member/process-add",
				data :	$('#myform').serialize(),
				success: function(data){
					
					document.location = base_url+"member/add";
					return "<?php if ($this->session->userdata('message_check') == 1) { 
						$this->session->set_userdata('message_check',0);
						$this->session->unset_userdata('message');
					} ?>";
				}
			});
		}
		return '<?php $check = $this->session->userdata('message_check') + 1;
				$this->session->set_userdata('message_check',$check);
		?>'
})
		
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