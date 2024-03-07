<style>
	th, td {
	  padding: 3px;
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

	function reset_edit(){
		document.location = base_url+"member/reset-edit-member/<?php echo $coremember['member_id']?>";
	}

	$(document).ready(function(){
		 $('#province_id').combobox({
			  onChange: function(value){
			  	var province_id   = document.getElementById("province_id").value;
			  	// alert(province_id);
			    $.ajax({
	               type : "POST",
	               url  : "<?php echo base_url(); ?>member/get-city/"+province_id,
	               data : {province_id: province_id},
	               success: function(data){
	               		$('#city_id').combobox({
							url:"<?php echo base_url(); ?>member/get-city/"+province_id,
							valueField:'id',
							textField:'text'
						});
	               }
	            });
			  }
			});
	});

	$(document).ready(function(){
		 $('#mutation_id').combobox({
			  onChange: function(value){
			  	var mutation_id 	= +document.getElementById("mutation_id").value;

			  

			   $.post(base_url + 'member/get-mutation-function',
				{mutation_id: mutation_id},
	            function(data){	
	            var obj = $.parseJSON(data)		   
	            	console.log(data);
	            	$('#mutation_function').textbox('setValue',obj);
				},
				
				)
			  }
			})
	});

	function calPrincipalSavings(){
		var member_principal_savings_last_balance	= $('#member_principal_savings_last_balance').val();
		var member_principal_savings				= $('#member_principal_savings').val();
		var mutation_function 						= $('#mutation_function').val();

		var member_principal_savings_last_balance_new;

		if(mutation_function == '+'){
			member_principal_savings_last_balance_new = parseFloat(member_principal_savings_last_balance) + parseFloat(member_principal_savings);
		} else if(mutation_function == '-'){
			member_principal_savings_last_balance_new = parseFloat(member_principal_savings_last_balance) - parseFloat(member_principal_savings);
		} else {
			alert("Sandi masih kosong");
				return false;
		}
		

		$('#member_principal_savings_last_balance_view').textbox('setValue', toRp(member_principal_savings_last_balance_new));
		$('#member_principal_savings_last_balance').textbox('setValue', member_principal_savings_last_balance_new);

	}

	function calSpecialSavings(){
		var member_special_savings_last_balance		= $('#member_special_savings_last_balance').val();
		var member_special_savings					= $('#member_special_savings').val();
		var mutation_function 						= $('#mutation_function').val();	

		if(member_special_savings_last_balance == ''){
			member_special_savings_last_balance = 0;
		}

		var member_special_savings_last_balance_new;

		if(mutation_function == '+'){
			member_special_savings_last_balance_new = parseFloat(member_special_savings_last_balance) + parseFloat(member_special_savings);
		} else if(mutation_function == '-'){
			member_special_savings_last_balance_new = parseFloat(member_special_savings_last_balance) - parseFloat(member_special_savings);
		} else {
			alert("Sandi masih kosong");
				return false;
		}

		console.log(member_special_savings_last_balance);
		
		
		$('#member_special_savings_last_balance_view').textbox('setValue', toRp(member_special_savings_last_balance_new));
		$('#member_special_savings_last_balance').textbox('setValue', member_special_savings_last_balance_new);
	}

	function calMandatorySavings(){
		var member_mandatory_savings_last_balance	= $('#member_mandatory_savings_last_balance').val();
		var member_mandatory_savings				= $('#member_mandatory_savings').val();
		var mutation_function 						= $('#mutation_function').val();

		var member_mandatory_savings_last_balance_new;

		if(mutation_function == '+'){
			member_mandatory_savings_last_balance_new = parseFloat(member_mandatory_savings_last_balance) + parseFloat(member_mandatory_savings);
		} else if(mutation_function == '-'){
			member_mandatory_savings_last_balance_new = parseFloat(member_mandatory_savings_last_balance) - parseFloat(member_mandatory_savings);
		} else {
			alert("Sandi masih kosong");
				return false;
		}

		

		$('#member_mandatory_savings_last_balance_view').textbox('setValue', toRp(member_mandatory_savings_last_balance_new));
		$('#member_mandatory_savings_last_balance').textbox('setValue', member_mandatory_savings_last_balance_new);
	}

	$(document).ready(function(){
		$('#city_id').combobox({
			onChange: function(value){
				// alert(value);
      		var city_id   =document.getElementById("city_id").value;
      		// alert(city_id);
            
	            $.ajax({
	               type : "POST",
	               url  : "<?php echo base_url(); ?>member/get-kecamatan/"+city_id,
	               data : {city_id: city_id},
	               success: function(data){
	               	alert($data);
					 	$('#kecamatan_id').combobox({
							url:"<?php echo base_url(); ?>member/get-kecamatan/"+city_id,
							valueField:'id',
							textField:'text'
						});
						
	              	}
	            });
  			}
  		});		
    });

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

				calSpecialSavings();
				
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

				calMandatorySavings();
				
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

				calPrincipalSavings();
				
				}else{
					loop=1;
					return;
				}
			
			}
		});
	});
</script>
<?php echo form_open('member/process-edit-member-savings',array('id' => 'myform', 'class' => 'horizontal-form')); 

$unique = $this->session->userdata('unique');
$token 	= $this->session->userdata('coremembertokenedit-'.$unique['unique']);
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
			<a href="<?php echo base_url();?>member/edit-member-savings/"<?php echo $coremember['member_id'] ?>>
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
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">No. Anggota<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_no" id="member_no" autocomplete="off" value="<?php echo set_value('member_no', $coremember['member_no']);?>" style="width: 60%" readonly/> <a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#memberlist">Cari Anggota</a>
										</td>
									</tr>
									<tr>
										<td width="35%">Nama Anggota<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo set_value('member_name', $coremember['member_name']);?>" style="width: 100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Sifat Anggota<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><?php echo form_dropdown('member_character', $membercharacter, set_value('member_character',$coremember['member_character']),'id="member_character" class="easyui-combobox" style="width: 70%"');?></td>
									</tr>
									<!-- <tr>
										<td width="35%">Jenis Kelamin<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><?php echo form_dropdown('member_gender', $membergender, set_value('member_gender',$coremember['member_gender']),'id="member_gender" class="easyui-combobox" style="width: 70%"');?></td>
									</tr> -->
									<tr>
										<td width="35%">Provinsi<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><?php echo form_dropdown('province_id', $coreprovince, set_value('province_id',$coremember['province_id']),'id="province_id" class="easyui-combobox" style="width: 70%"');?></td>
									</tr>
									<tr>
										<td width="35%">Kabupaten<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<?php
												if (!empty($coremember['province_id'])){
													$corecity = create_double($this->CoreMember_model->getCoreCity($coremember['province_id']), 'city_id', 'city_name');

													echo form_dropdown('city_id', $corecity, set_value('city_id', $coremember['city_id']), 'id="city_id" class="easyui-combobox" style="width: 70%" ');
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
										<td width="5%"></td>
										<td width="60%">
											<?php
												if (!empty($coremember['city_id'])){
													$corekecamatan = create_double($this->CoreMember_model->getCoreKecamatan($coremember['city_id']), 'kecamatan_id', 'kecamatan_name');

													echo form_dropdown('kecamatan_id', $corekecamatan, set_value('kecamatan_id', $coremember['kecamatan_id']), 'id="kecamatan_id" class="easyui-combobox" style="width: 70%" ');
												} else {
											?>
												<select name="kecamatan_id" id="kecamatan_id" class="easyui-combobox" style="width: 70%">
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

													echo form_dropdown('kelurahan_id', $corekelurahan, set_value('kelurahan_id', $coremember['kelurahan_id']), 'id="kelurahan_id" class="easyui-combobox" style="width: 70%" ');
												} else {
											?>
												<select name="kelurahan_id" id="kelurahan_id" class="easyui-combobox" style="width: 70%">
													<option value="">--Pilih Salah Satu--</option>
												</select>
											<?php
												}
											?>
										</td>
									</tr>
									<!-- <tr>
										<td width="35%">Dusun<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<?php
												if (!empty($coremember['kelurahan_id'])){
													$coredusun = create_double($this->CoreMember_model->getCoreDusun($coremember['kelurahan_id']), 'dusun_id', 'dusun_name');

													echo form_dropdown('dusun_id', $coredusun, set_value('dusun_id', $coremember['dusun_id']), 'id="dusun_id" class="easyui-combobox" style="width: 70%" ');
												} else {
											?>
												<select name="dusun_id" id="dusun_id" class="easyui-combobox" style="width: 70%">
													<option value="">--Pilih Salah Satu--</option>
												</select>
											<?php
												}
											?>
										</td>
									</tr> -->
									<tr>

<!-- 										<td width="35%">Tempat Lahir<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_place_of_birth" id="member_place_of_birth" autocomplete="off" value="<?php echo set_value('member_place_of_birth', $coremember['member_place_of_birth']);?>" style="width: 100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Tanggal Lahir<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-datebox date-picker" name="member_date_of_birth" id="member_date_of_birth" autocomplete="off" value="<?php echo tgltoview($coremember['member_date_of_birth']);?>" style="width: 70%" readonly/></td>
									</tr> -->
									<tr>
										<td width="35%">Alamat<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><textarea rows="1" name="member_address" id="member_address" class="easyui-textarea" style="width: 100%" ><?php echo $coremember['member_address'];?></textarea></td>
									</tr>
									<!-- <tr>
										<td width="35%">Kode Pos<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_postal_code" id="member_postal_code" autocomplete="off" value="<?php echo set_value('member_postal_code', $coremember['member_postal_code']);?>" style="width: 100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Nomor Telp<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_phone" id="member_phone" autocomplete="off" value="<?php echo set_value('member_phone', $coremember['member_phone']);?>" style="width: 100%" readonly/></td>
									</tr> -->
								</table>
							</div>
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table>
									<tr>
									<td colspan="3" align="left"><b>Saldo</b></td>
									</tr>
									<tr>
										<td width="35%">Saldo Simp Pokok</td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_principal_savings_last_balance_view" id="member_principal_savings_last_balance_view" autocomplete="off" value="<?php echo number_format($coremember['member_principal_savings_last_balance'], 2);?>" style="width: 100%" disabled/>

											<input type="hidden" class="easyui-textbox" name="member_principal_savings_last_balance" id="member_principal_savings_last_balance" value="<?php echo set_value('member_principal_savings_last_balance', $coremember['member_principal_savings_last_balance']);?>" autocomplete="off" />
										</td>

									<tr>
										<td width="35%">Saldo Simp Khusus</td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_special_savings_last_balance_view" id="member_special_savings_last_balance_view" autocomplete="off" style="width: 100%" value="<?php echo number_format($coremember['member_special_savings_last_balance'], 2);?>" disabled/>

											<input type="hidden" class="easyui-textbox" name="member_special_savings_last_balance" id="member_special_savings_last_balance" value="<?php echo set_value('member_special_savings_last_balance', $coremember['member_special_savings_last_balance']);?>" autocomplete="off"/>
										</td>
									</tr>
									<tr>
										<td width="35%">Saldo Simp Wajib</td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_mandatory_savings_last_balance_view" id="member_mandatory_savings_last_balance_view" autocomplete="off" style="width: 100%" value="<?php echo number_format($coremember['member_mandatory_savings_last_balance'], 2);?>" disabled/>

											<input type="hidden" class="easyui-textbox" name="member_mandatory_savings_last_balance" id="member_mandatory_savings_last_balance" value="<?php echo set_value('member_mandatory_savings_last_balance', $coremember['member_mandatory_savings_last_balance']);?>" autocomplete="off"/>
										
										</td>
									</tr>
									<!-- <tr>
									<td colspan="3" align="left"><b>Nominal</b></td>
									</tr>
									<tr>
										<td width="35%">Nominal Simp Wajib Perusahaan</td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_company_mandatory_savings_view" id="member_company_mandatory_savings_view" autocomplete="off" style="width: 100%" value="<?php echo number_format($coremember['member_company_mandatory_savings'], 2);?>" disabled/>
											<input type="hidden" class="easyui-textbox" name="member_company_mandatory_savings" id="member_company_mandatory_savings" value="<?php echo set_value('member_company_mandatory_savings', $coremember['member_company_mandatory_savings']);?>" autocomplete="off"/>
										</td>
									</tr>
									<tr>
										<td width="35%">Nominal Simp Wajib Keanggotaan</td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_class_mandatory_savings_view" id="member_class_mandatory_savings_view" autocomplete="off" style="width: 100%" value="<?php echo number_format($coremember['member_class_mandatory_savings'], 2);?>" disabled/>
											<input type="hidden" class="easyui-textbox" name="member_class_mandatory_savings" id="member_class_mandatory_savings" value="<?php echo set_value('member_class_mandatory_savings', $coremember['member_class_mandatory_savings']);?>" autocomplete="off"/>
										</td>
									</tr> -->
								</table>
								 
						
								<h3>Input Simpanan</h3>
								<table>
									<tr>
										<td width="35%">Sandi<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<?php echo form_dropdown('mutation_id', $acctmutation, set_value('mutation_id'),'id="mutation_id" class="easyui-combobox" style="width:70%" ');?>
											
										</td>
									</tr>
										<input type="hidden" class="easyui-textbox" name="mutation_function" id="mutation_function" autocomplete="off" readonly/>

									
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
									</tr>
								</table>
								

								<input type="hidden" class="form-control" name="member_id" id="member_id" placeholder="id" value="<?php echo set_value('member_id',$coremember['member_id']);?>"/>

								<input type="hidden" class="form-control" name="member_token_edit" id="member_token_edit" placeholder="id" value="<?php echo $token;?>"/>

								<table width="100%">
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
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="memberlist" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Daftar Anggota</h4>
      </div>
      <div class="modal-body">
		<table id="myDataTable" class="table table-striped table-bordered table-hover table-full-width">
			<thead>
		    	<tr>
		        	<th>No</th>
		        	<th>Member No</th>
		            <th>Member Nama</th>
		            <th>Alamat</th>
		            <th>Action</th>
		        </tr>
		    </thead>
		    <tbody></tbody>
		</table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<script type="text/javascript">
 
var table;
 
$(document).ready(function() {
 
    //datatables
    table = $('#myDataTable').DataTable({ 
 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "pageLength": 5,
        "order": [], //Initial no order.
        "ajax": {
            "url": "<?php echo site_url('member/get-list-edit')?>",
            "type": "POST"
        },
        "columnDefs": [
        { 
            "targets": [ 0 ], //first column / numbering column
            "orderable": false, //set not orderable
        },
        ],
 
    });
 
});
</script>
<?php echo form_close(); ?>