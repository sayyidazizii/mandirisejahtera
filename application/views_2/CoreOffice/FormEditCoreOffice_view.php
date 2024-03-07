<style>
	th, td {
	  padding: 3px;
	}
	input:focus { 
	  background-color: 42f483;
	}


</style>
<script>
	base_url 	= '<?php echo base_url();?>';
	mappia 		= "<?php echo site_url('office/edit/'.$this->uri->segment(3)); ?>";
	function ulang(){
		document.getElementById("office_name").value 				= "<?php echo $coreoffice['office_name'] ?>";
		document.getElementById("office_code").value 				= "<?php echo $coreoffice['office_code'] ?>";
	}

	function function_elements_edit(name, value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('office/elements-edit');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
			}
		});
	}

	 $(document).ready(function(){
		$('#city_id').combobox({
			onChange: function(value){
				/*alert(value);
      		var city_id   = document.getElementById("city_id").value;
      		alert(city_id);*/
            
	            $.ajax({
	               type : "POST",
	               url  : "<?php echo base_url(); ?>office/get-kecamatan/"+value,
	               data : {value: value},
	               success: function(data){
	               /*	alert(data);*/
					 	$('#kecamatan_id').combobox({
							url:"<?php echo base_url(); ?>office/get-kecamatan/"+value,
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
	               url  : "<?php echo base_url(); ?>office/get-kelurahan/"+value,
	               data : {value: value},
	               success: function(data){
	               /*	alert(data);*/
					 	$('#kelurahan_id').combobox({
							url:"<?php echo base_url(); ?>office/get-kelurahan/"+value,
							valueField:'kelurahan_id',
							textField:'kelurahan_name'
						});
						
	              	}
	            });
  			}
  		});		
    });

    $(document).ready(function(){
		$('#office_code').textbox({
			onChange: function(value){
				var name   	= 'office_code';

		    	function_elements_edit(name, value);
			}
		});

		$('#office_name').textbox({
			onChange: function(value){
				// alert(value);
				var name   	= 'office_name';

		    	function_elements_edit(name, value);
			}
		});

		$('#branch_id').combobox({
			onChange: function(value){
				// alert(value);
				var name   	= 'branch_id';

		    	function_elements_edit(name, value);
			}
		});
	});

	function editCoreDusun(){
		var city_id 			= document.getElementById("city_id").value;
		var kecamatan_id 		= document.getElementById("kecamatan_id").value;
		var kelurahan_id 		= document.getElementById("kelurahan_id").value;
	
		$('#offspinwarehouse').css('display', 'none');
		$('#onspinspinwarehouse').css('display', 'table-row');
		  $.ajax({
		  type: "POST",
		  url : "<?php echo site_url('office/edit-dusun');?>",
		  data: {
		  			'city_id'			: city_id,
		  			'kecamatan_id'		: kecamatan_id,
		  			'kelurahan_id'		: kelurahan_id,
					'session_name' 		: "editcoredusun-"
				},
		  success: function(msg){
		   window.location.replace(mappia);
		 }
		});
	}

	$(document).ready(function(){
        $("#Save").click(function(){
			var office_code = $("#office_code").val();
			
			if(office_code == ''){
				alert('Kode BO masih kosong');
				return false;
			} else if(office_name == ''){
				alert('Nama BO masih kosong');
				return false;
			} else {
				return true;
			}	
		});
    });
</script>
<?php echo form_open('office/process-edit',array('id' => 'myform', 'class' => 'horizontal-form')); 

	$unique 		= $this->session->userdata('unique');
	$data 			= $this->session->userdata('editCoreOffice-'.$unique['unique']);

	// print_r($data);

	if (empty($data['office_code'])){
		$data['office_code'] 				= '';
	}

	if (empty($data['office_name'])){
		$data['office_name'] 				= '';
	}

	if (empty($data['branch_id'])){
		$data['branch_id'] 					= 9;
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
			<a href="<?php echo base_url();?>CoreOffice">
				Daftar Business Office (BO)
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>office/edit/"<?php $this->uri->segment(3); ?>>
				Edit Business Office (BO) 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->
<h3 class="page-title">
	Form Edit Business Office (BO) 
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
						Form Edit
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>CoreOffice" class="btn btn-default btn-sm">
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
							<div class="col-md-12">
								<table width="100%">
									<tr>
										<td width="20%">Kode BO<span class="required">*</span></td>
										<td width="40%">Nama BO<span class="required">*</span></td>
										<td width="40%">Cabang<span class="required">*</span></td>
									</tr>
									<tr>
										<td width="20%">
											<input type="text" class="easyui-textbox" name="office_code" id="office_code" value="<?php echo $coreoffice['office_code']; ?>" style="width: 100%" autofocus/>
										</td>

										<td width="40%">
											<input type="text" class="easyui-textbox" name="office_name" id="office_name" value="<?php echo $coreoffice['office_name']; ?>" style="width:100%"/>
										</td>

										<td width="40%">
											<?php echo form_dropdown('branch_id', $corebranch, set_value('branch_id',$coreoffice['branch_id']),'id="branch_id" class="easyui-combobox" style="width:100%"');?>
												
											</td>
									</tr>
								</table>
							</div>
						</div>
						<div class="row">	
							<div class="col-md-12">
								<table width="100%">
									<tr>
										<td colspan="4"> <h3>Data Dusun</h3></td>
										
									</tr>
									<tr>
										<td width="25%">Kabupaten<span class="required">*</span></td>
										<td width="25%">Kecamatan<span class="required">*</span></td>
										<td width="25%">Kelurahan<span class="required">*</span></td>
										<!-- <td width="25%">Dusun<span class="required">*</span></td> -->
									</tr>
									<tr>
										<td width="25%"><?php echo form_dropdown('city_id', $corecity, set_value('city_id',$data['city_id']),'id="city_id" class="easyui-combobox" style="width:100%"');?></td>
										<td width="25%">
											<input id="kecamatan_id" name="kecamatan_id" class="easyui-combobox" data-options="valueField:'kecamatan_id',textField:'text'" style="width: 100%">
										</td>
										<td width="25%">
											<input id="kelurahan_id" name="kelurahan_id" class="easyui-combobox" data-options="valueField:'kelurahan_id',textField:'text'" style="width: 100%">
										</td>
										<!-- <td width="25%">
											<input id="dusun_id" name="dusun_id" class="easyui-combobox" data-options="valueField:'dusun_id',textField:'text'" style="width: 100%">
										</td> -->
									</tr>
									<tr>
										<td colspan="4" align="right">
											<input type="button" name="add2" id="buttonAddArrayAcctjournalVoucher" value="Add" class="btn green-jungle" title="Simpan Data" onClick="editCoreDusun();">
										</td>
									</tr>
								</table>
							</div>
						</div>				
					</div>
				</div>
			 <div class="portlet-body">
			 	
				<div class="form-body">
					<table class="table table-striped table-bordered table-hover table-full-width">
						<thead>
							<tr>
								<th style="text-align: center; width: 5%">No.</th>
								<th style="text-align: center; width: 30%">Kelurahan</th>
								<!-- <th style="text-align: center; width: 30%">Dusun</th> -->
								<th style="text-align: center; width: 5%">Aksi</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$no = 1;
							$sesi 		= $this->session->userdata('unique');
							$datadusun 	= $this->session->userdata('editcoredusun-'.$sesi['unique']);

							if(!empty($datadusun)){
								foreach ($datadusun as $key => $val) {
									echo "
										<tr>
											<td>".$no."</td>
											<td>".$this->CoreOffice_model->getKelurahanName($val['kelurahan_id'])."</td>
											<td><a href='".$this->config->item('base_url').'office/delete-edit-dusun/'.$coreoffice['office_id'].'/'.$val['dusun_id']."' class='btn default btn-xs red', onClick='javascript:return confirm(\"apakah yakin ingin dihapus ?\")'>
														<i class='fa fa-trash-o'></i> Hapus
													</a></td>
										</tr>
									";

									$no++;
								}
							}
							?>
						</tbody>
					</table>
					<div class="row">
						<div class="col-md-12 " style="text-align  : right !important;">
							<input type="hidden" class="easyui-textbox" name="office_id" id="office_id" placeholder="id" value="<?php echo set_value('office_id',$coreoffice['office_id']);?>"/>

							<input type="hidden" class="easyui-textbox" name="user_id" id="user_id" placeholder="id" value="<?php echo set_value('user_id',$coreoffice['user_id']);?>"/>


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