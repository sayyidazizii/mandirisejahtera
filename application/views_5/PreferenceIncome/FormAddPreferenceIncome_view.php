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
	function ulang(){
		$("#income_name").textbox('setValue','');
		$("#income_percentage").textbox('setValue','');
	}

	$(document).ready(function(){
        $("#Save").click(function(){
			var income_name 		= $("#income_name").val();
			var income_percentage 	= $("#income_percentage").val();
			var income_group 		= $("#income_group").val();
			var account_id 			= $("#account_id").val();
			
			if(income_name == ''){
				alert("Nama masih kosong");
				return false;
			} else if(income_percentage == ''){
				alert("Persen masih kosong");
				return false;
			}else if(income_group == ''){
				alert("Golongan Perkiraan masih kosong");
				return false;
			}else if(account_id == ''){
				alert("No. Perkiraan masih kosong");
				return false;
			} else {
				return true;
			}
		});
    });
</script>
<?php echo form_open('preference-income/process-add',array('id' => 'myform', 'class' => 'horizontal-form')); ?>

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
			<a href="<?php echo base_url();?>PreferenceIncome">
				Konfigurasi Pendapatan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->

<?php
// print_r($data);
// print_r($member);
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			 <div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Konfigurasi Pendapatan
					</div>
				</div>
				<div class="portlet-body">
					<div class="form-body">
						<div class="row">	
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">Nama</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="income_name" id="income_name" style="width: 100%" autofocus/></td>
									</tr>
									<tr>
										<td width="35%">Persen</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="income_percentage" id="income_percentage" style="width:100%"/></td>
									</tr>
									
								</table>
							</div>
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">Kelompok</td>
										<td width="5%">:</td>
										<td width="60%"><?php echo form_dropdown('income_group', $kelompokperkiraan, set_value('income_group',$data['income_group']),'id="income_group" class="easyui-combobox" style="width:70%"');?></td>
									</tr>
									<tr>
										<td width="35%">No. Perkiraan</td>
										<td width="5%">:</td>
										<td width="60%"><?php echo form_dropdown('account_id', $acctaccount, set_value('account_id',$data['account_id']),'id="account_id" class="easyui-combobox" style="width:100%"');?></td>
									</tr>
									<tr>
										<td width="35%"></td>
										<td width="5%"></td>
										<td width="60%" align="right">
											<button type="button" class="btn red" onClick="ulang();"><i class="fa fa-times"></i> Batal</button>
											<button type="submit" class="btn green-jungle"><i class="fa fa-check"></i> Tambah</button>
										</td>
									</tr>
								</table>
							</div>					
						</div>						
					</div>
				</div>
				<?php echo form_close(); ?>
				
				<div class="portlet-body">
					<div class="form-body">
						<?php
							echo form_open('preference-income/process-edit'); 


							$unique 				= $this->session->userdata('unique');
							$datapreferenceincome	= $this->session->userdata('addpreferenceincome-'.$unique['unique']);
						?>
						<table class="table table-striped table-bordered table-hover table-full-width">
							<thead>
								<tr>
									<th style="text-align: center; width: 25%"></th>
									<th style="text-align: center; width: 10%">Persen</th>
									<th style="text-align: center; width: 15%">Kelompok</th>
									<th style="text-align: center; width: 25%">Perkiraan</th>
									<th style="text-align: center; width: 5%">Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php
									if(!empty($datapreferenceincome)){
										foreach ($datapreferenceincome as $key => $val) {
											echo "
												<input type='hidden' class='form-control' name='".$key."' id='".$key."' value='".$key."' readonly>
												<input type='hidden' class='form-control' name='income_id_".$key."' id='income_id_".$key."' value='".$val['income_id']."' readonly>

												<tr>
													<td>".$val['income_name']."</td>
													<td><input type='text' name='income_percentage_".$key."' id='income_percentage_".$key."' class='form-control' value='".$val['income_percentage']."' style='text-align:right;'>
													</td>
													<td>".form_dropdown('income_group_'.$key, $kelompokperkiraan, set_value('income_group',$val['income_group']),'id="income_group_'.$key.'" class="easyui-combobox" style="width:100%"')."</td>
													<td>".form_dropdown('account_id_'.$key, $acctaccount, set_value('account_id',$val['account_id']),'id="account_id_'.$key.'" class="easyui-combobox" style="width:100%"')."</td>";
													if($val['income_status'] == 0){
														echo "
															<td><a href='".$this->config->item('base_url').'preference-income/delete/'.$val['income_id']."' class='btn default btn-xs red', onClick='javascript:return confirm(\"apakah yakin ingin dihapus ?\")'>
																<i class='fa fa-trash-o'></i> Hapus
															</a></td>
														";
													}
												echo "
												</tr>
											";

											$total_percentage += $val['income_percentage'];
										}
									}
								?>
								<tr>
									<td></td>
									<td align="right"><b><?php echo $total_percentage; ?></b></td>
									<td></td>
									<td></td>
								</tr>
							</tbody>
						</table>
						<div class="row">
							<div class="col-md-12 " style="text-align  : right !important;">
								<button type="submit" class="btn green-jungle"><i class="fa fa-check"></i> Simpan Perubahan</button>
							</div>
						</div>
					</div>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
