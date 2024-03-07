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

	var loop = 1;

	$(document).ready(function(){
		$('#deposito_id').textbox({
		   collapsible:false,
		   minimizable:false,
		   maximizable:false,
		   closable:false
		});
		
		$('#deposito_id').textbox('textbox').focus();
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

	$(document).ready(function(){
		 $('#deposito_id').combobox({
			  onChange: function(value){
			  	var deposito_id 	= +document.getElementById("deposito_id").value;

			  

			   $.post(base_url + 'deposito-account/get-deposite-account-no',
				{deposito_id: deposito_id},
                function(data){	
                var obj = $.parseJSON(data);
                	console.log(obj);	   
                	$("#deposito_period").textbox('setValue',obj['deposito_period']);
				   	$("#deposito_account_no").textbox('setValue',obj['deposito_account_no']);
				   	$("#deposito_account_serial_no").textbox('setValue',obj['deposito_account_serial_no']);
				   	$("#deposito_account_due_date").textbox('setValue',obj['deposito_account_due_date']);
				   	$("#deposito_account_nisbah").textbox('setValue',obj['deposito_account_nisbah']);
				},
				
				)
			  }
			})
	});


	$(document).ready(function(){
		$('#deposito_account_amount_view').textbox({
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
				$('#deposito_account_amount').textbox('setValue', value);
				$('#deposito_account_amount_view').textbox('setValue', tampil);

				}else{
					loop=1;
					return;
				}
			
			}
		});
	});
</script>
<?php echo form_open('deposito-account/process-add-new',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
	$sesi 	= $this->session->userdata('unique');
	$data 	= $this->session->userdata('addacctdeposito-'.$sesi['unique']);
	$token 	= $this->session->userdata('acctdepositoaccounttoken-'.$sesi['unique']);


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
			<a href="<?php echo base_url();?>deposito-account">
				Daftar Rekening Simpanan Berjangka
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>deposito-account/add">
				Tambah Rekening Simpanan Berjangka
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->
<?php
// print_r($data);
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			 <div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Form Tambah Rekening Simpanan Berjangka
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>deposito-account" class="btn btn-default btn-sm">
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
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">No. Dep</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width:100%"
											name="member_date_of_birth" id="member_date_of_birth" autocomplete="off" value="<?php echo $acctdepositoaccount['deposito_account_no']; ?>" readonly/>
											<input type="hidden" class="easyui-textbox" style="width:100%"
											name="deposito_account_token" id="deposito_account_token" autocomplete="off" value="<?php echo $token; ?>" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Nama</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width:100%"
											name="member_name" id="member_name" autocomplete="off" value="<?php echo $acctdepositoaccount['member_name']; ?>" readonly/>
											<input type="hidden" class="easyui-textbox" name="member_id" id="member_id" value="<?php echo $acctdepositoaccount['member_id']; ?>" autocomplete="off" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Tanggal Lahir</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width:100%" name="member_date_of_birth" id="member_date_of_birth" value="<?php echo tgltoview($acctdepositoaccount['member_name']); ?>" autocomplete="off" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Jenis Kelamin</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width:100%" name="member_date_of_birth" id="member_date_of_birth" value="<?php echo $membergender[$acctdepositoaccount['member_gender']]; ?>" autocomplete="off" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Alamat</td>
										<td width="5%">:</td>
										<td width="60%"><?php echo form_textarea(array('rows'=>'2','name'=>'member_address','class'=>'easyui-textbox','id'=>'member_address','disabled'=>'disabled', 'value'=> $acctdepositoaccount['member_address']))?></td>
									</tr>
									<tr>
										<td width="35%">Kabupaten</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width:100%" name="city_name" id="city_name" autocomplete="off" value="<?php echo $this->AcctDepositoAccount_model->getCityName($acctdepositoaccount['city_id']); ?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Kecamatan</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width:100%" name="kecamatan_name" id="kecamatan_name" autocomplete="off" value="<?php echo $this->AcctDepositoAccount_model->getKecamatanName($acctdepositoaccount['kecamatan_id']); ?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Pekerjaan</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width:100%" name="job_name" id="job_name" autocomplete="off" value="<?php echo $acctdepositoaccount['member_job']; ?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">No. Telp</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width:100%" name="member_phone" id="member_phone" autocomplete="off" value="<?php echo $acctdepositoaccount['member_phone']; ?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Identitas</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width:100%" name="member_phone" id="member_phone" autocomplete="off" value="<?php echo $memberidentity[$acctdepositoaccount['identity_id']]; ?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">No. Identitas</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width:100%" name="member_identity_no" id="member_identity_no" autocomplete="off" value="<?php echo $acctdepositoaccount['member_identity_no']; ?>" readonly/></td>
									</tr>
								</table>
							</div>
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">Jenis Simpanan<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><?php echo form_dropdown('deposito_id', $acctdeposito, set_value('deposito_id',$data['deposito_id']),'id="deposito_id" class="easyui-combobox" style="width:70%" ');?></td>
									</tr>
									<tr>
										<td width="35%">Jangka Waktu<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width:100%" name="deposito_period" id="deposito_period" autocomplete="off" readonly /></td>
									</tr>
									<tr>
										<td width="35%">No. Simpanan<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><?php echo form_dropdown('savings_account_id', $acctsavingsaccount, set_value('savings_account_id',$data['savings_account_id']),'id="savings_account_id" class="easyui-combobox" style="width:100%"');?></td>
									</tr>
									<tr>
										<td width="35%">Tanggal Buka<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width:70%" name="deposito_account_date" id="deposito_account_date" autocomplete="off" value="<?php echo date('d-m-Y'); ?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">No. SimpKa<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width:100%" name="deposito_account_no" id="deposito_account_no" autocomplete="off" readonly  /></td>
									</tr>
									<tr>
										<td width="35%">Tanggal Jatuh Tempo<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width:70%" name="deposito_account_due_date" id="deposito_account_due_date" autocomplete="off"  readonly /></td>
									</tr>
									<tr>
										<td width="35%">No. Seri<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width:100%" name="deposito_account_serial_no" id="deposito_account_serial_no" autocomplete="off"  readonly/></td>
									</tr>
									<tr>
										<td width="35%">Nisbah(%)<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width:100%" name="deposito_account_nisbah" id="deposito_account_nisbah" autocomplete="off" readonly  /></td>
									</tr>
									<tr>
										<td width="35%">Nominal (Rp)<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width:100%" name="deposito_account_amount_view" id="deposito_account_amount_view" autocomplete="off" value="<?php echo set_value('deposito_account_amount_view',$data['deposito_account_amount_view']);?>"/>
											<input type="hidden" class="easyui-textbox" name="deposito_account_amount" id="deposito_account_amount" autocomplete="off" value="<?php echo set_value('deposito_account_amount',$data['deposito_account_amount']);?>"/>
										</td>
									</tr>
									<tr>
										<td width="35%"></td>
										<td width="5%"></td>
										<td width="60%" align="right">
											<button type="button" class="btn red" onClick="reset_data();"><i class="fa fa-times"></i> Batal</button>
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
<?php echo form_close(); ?>
