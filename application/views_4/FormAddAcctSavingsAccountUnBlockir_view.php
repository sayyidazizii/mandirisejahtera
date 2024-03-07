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
				url : "<?php echo site_url('AcctSavingsAccountBlockir/function_elements_edit');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
			}
		});
	}

	function reset_edit(){
		document.location = base_url+"AcctSavingsAccountBlockir/reset_edit/<?php echo $acctsavingsaccountblocker['member_id']?>";
	}

	$(document).ready(function(){
		$('#savings_account_blockir_amount_view').textbox({
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
				$('#savings_account_blockir_amount').textbox('setValue', value);
				$('#savings_account_blockir_amount_view').textbox('setValue', tampil);
				
				}else{
					loop=1;
					return;
				}
			
			}
		});
	});
</script>
<?php echo form_open('AcctSavingsAccountBlockir/processAddAcctSavingsAccountUnBlockir',array('id' => 'myform', 'class' => 'horizontal-form'));?>

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
			<a href="<?php echo base_url();?>AcctSavingsAccountBlockir">
				UnBlockir Rekening Anggota
			</a>
			<i class="fa fa-angle-right"></i>
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
						UnBlockir
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>AcctSavingsAccountBlockir/unBlockirAcctSavingsAccount" class="btn btn-default btn-sm">
							<i class="fa fa-angle-left"></i>
							<span class="hidden-480">
								Kembali
							</span>
						</a>
					</div>
				</div>
				<div class="portlet-body form">
					<div class="form-body">
						<div class="row">
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">No. Rek<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="savings_account_no" id="savings_account_no" autocomplete="off" value="<?php echo $acctsavingsaccountblocker['savings_account_no'];?>" style="width: 60%" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Nama Anggota<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo set_value('member_name', $acctsavingsaccountblocker['member_name']);?>" style="width: 100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Simpanan<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="savings_name" id="savings_name" autocomplete="off" value="<?php echo set_value('savings_name', $acctsavingsaccountblocker['savings_name']);?>" style="width: 100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Alamat<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_address" id="member_address" autocomplete="off" value="<?php echo set_value('member_address', $acctsavingsaccountblocker['member_address']);?>" style="width: 100%" readonly/></td>
									</tr>
									<!-- <tr>
										<td width="35%">Kabupaten<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="city_name" id="city_name" autocomplete="off" value="<?php echo set_value('city_name', $acctsavingsaccountblocker['city_name']);?>" style="width: 100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Kecamatan<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="kecamatan_name" id="kecamatan_name" autocomplete="off" value="<?php echo set_value('kecamatan_name', $acctsavingsaccountblocker['kecamatan_name']);?>" style="width: 100%" readonly/></td>
									</tr> -->
									<tr>
										<td width="35%">No. Identitas<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_identity_no" id="member_identity_no" autocomplete="off" value="<?php echo set_value('member_identity_no', $acctsavingsaccountblocker['member_identity_no']);?>" style="width: 100%" readonly/></td>
									</tr>
								</table>
							</div>
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table>
									<tr>
										<td width="35%">Saldo<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="savings_account_last_balance" id="savings_account_last_balance" autocomplete="off" style="width: 100%" value="<?php echo number_format($acctsavingsaccountblocker['savings_account_last_balance'], 2);?>" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Sifat Blokir<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="savings_account_blockir_type" id="savings_account_blockir_type" autocomplete="off" style="width: 100%" value="<?php echo $blockirtype[$acctsavingsaccountblocker['savings_account_blockir_type']];?>" readonly/>
										</td>

									<tr>
										<td width="35%">Saldo Blockir<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="savings_account_blockir_amount_view" id="savings_account_blockir_amount_view" autocomplete="off" value="<?php echo number_format($acctsavingsaccountblocker['savings_account_blockir_amount'], 2);?>" style="width: 100%"/>
										</td>
									</tr>
									<tr>
										<td width="35%"></td>
										<td width="5%">
											<input type="hidden" class="easyui-textbox" name="savings_account_blockir_id" id="savings_account_blockir_id" placeholder="id" value="<?php echo $acctsavingsaccountblocker['savings_account_blockir_id'];?>"/>

											<input type="hidden" class="easyui-textbox" name="savings_account_id" id="savings_account_id" placeholder="id" value="<?php echo $acctsavingsaccountblocker['savings_account_id'];?>"/>
										</td>
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
<?php echo form_close(); ?>