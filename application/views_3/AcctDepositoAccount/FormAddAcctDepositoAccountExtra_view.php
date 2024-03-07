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
	// $(document).ready(function(){
	// 	$('#deposito_account_extra_period').textbox({
	// 	   collapsible:false,
	// 	   minimizable:false,
	// 	   maximizable:false,
	// 	   closable:false
	// 	});

	// 	$('#deposito_account_extra_period').textbox('clear').textbox('textbox').focus();
	// });

	$(document).ready(function(){
		let	loop = 1;
		function toRp(number) {
		var number = number.toString(), 
		rupiah = number.split('.')[0], 
		cents = (number.split('.')[1] || '') +'00';
		rupiah = rupiah.split('').reverse().join('')
			.replace(/(\d{3}(?!$))/g, '$1,')
			.split('').reverse().join('');
		return rupiah + '.' + cents.slice(0, 2);
		}
       $('#deposito_account_extra_period').textbox({
			onChange: function(value){
			var period 	= +document.getElementById("deposito_account_extra_period").value;

			 var counts = {
				    normal: [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31],
				    leap:   [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31]
			 }

        	var no = 1 + parseFloat(period);
        	var d = new Date(),
		        month = '' + (d.getMonth() + no),
		        day = '' + d.getDate(),
		        year = d.getFullYear();

		    var endYear  = d.getFullYear() + Math.ceil((no + d.getMonth()) / 12) - 1;
    		var yearType = ((endYear % 4 == 0) && (endYear % 100 != 0)) || (endYear % 400 == 0) ? 'leap' : 'normal';
    		var endMonth = (d.getMonth() + no) % 12;
    		var endDate  = Math.min(d.getDate(), counts[yearType][endMonth]);
			if(endMonth == 0){
    			endMonth = endMonth+12;
    		}
    		//alert(endMonth);
		    // if (month > 12 ){
		    // 	month = month - 12;
		    // 	year = year + 1;
		    // } else {
		    // 	month = month;
		    // 	year = year;
		    // }

		    if (endMonth.toString().length < 2){

		    	endMonth = '0' + endMonth;
		    } 
		    if (endDate.toString().length < 2) {
		    	endDate = '0' + endDate;
		    }

		    //var date = [day, month,  year].join('-');

		    var date = [endDate , endMonth , endYear].join('-');

		    console.log(date);
		    $('#deposito_account_extra_due_date').textbox('setValue', date);
		    // document.getElementById('deposito_account_extra_due_date').value		= [day, month,  year].join('-');
		}
        });
		$('#deposito_account_amount_adm_view').textbox({
			onChange: function(value){
				var name   	= 'deposito_account_amount_adm';
				var name2  	= 'deposito_account_amount_adm_view';

				if(loop == 0){
					loop= 1;
					return;
				}
				if(loop ==1){
					loop =0;
					var tampil = toRp(value);
				$('#deposito_account_amount_adm_view').textbox('setValue', tampil);
				$('#deposito_account_amount_adm').textbox('setValue', value);
				
				// function_elements_add(name, value);
				// function_elements_add(name2, tampil);
				}else{
					loop=1;
					return;
				}
			
			}
		});
    });
	
</script>
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
			<a href="<?php echo base_url();?>deposito-account/get-due-date">
				Perpanjangan Simpanan Berjangka
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Perpanjangan Simpanan Berjangka
</h3>
<?php echo form_open('deposito-account/process-add-extra',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
// print_r($acctdepositoaccount);
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			 <div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Form Detail
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>deposito-account/get-due-date" class="btn btn-default btn-sm">
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
										<td width="35%">No. Simpka</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width: 100%" name="deposito_account_no" id="deposito_account_no" value="<?php echo $acctdepositoaccount['deposito_account_no'];?>" readonly/>
											<input type="hidden" class="form-control" name="deposito_account_id" id="deposito_account_id" value="<?php echo $acctdepositoaccount['deposito_account_id'];?>" readonly/>
											<input type="hidden" class="form-control" name="deposito_account_extra_token" id="deposito_account_extra_token" value="<?php echo $token;?>" readonly/>
										
										</td>
									</tr>
									<tr>
										<td width="35%">No. Seri</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="deposito_account_serial_no" id="deposito_account_serial_no" value="<?php echo $acctdepositoaccount['deposito_account_serial_no'];?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Jenis Simpanan Berjangka</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="deposito_name" id="deposito_name" value="<?php echo $acctdepositoaccount['deposito_name'];?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Anggota</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="member_name" id="member_name" value="<?php echo $acctdepositoaccount['member_name'];?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">No. Anggota</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="member_no" id="member_no" value="<?php echo $acctdepositoaccount['member_no'];?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Jenis Kelamin</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="member_gender" id="member_gender" value="<?php echo $membergender[$acctdepositoaccount['member_gender']];?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Alamat</td>
										<td width="5%">:</td>
										<td width="60%"><?php echo form_textarea(array('rows'=>'2','name'=>'member_address','class'=>'easyui-textarea','id'=>'member_address','disabled'=>'disabled', 'value'=> $acctdepositoaccount['member_address']))?></td>
									</tr>
									<tr>
										<td width="35%">Kabupaten</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="city_name" id="city_name" value="<?php echo $this->AcctDepositoAccount_model->getCityName($acctdepositoaccount['city_id']);?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Kecamatan</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="kecamatan_name" id="kecamatan_name" value="<?php echo $this->AcctDepositoAccount_model->getKecamatanName($acctdepositoaccount['kecamatan_id']);?>" readonly/></td>
									</tr>
									
									
								</table>
							</div>
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">Identitas</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="identity_name" id="identity_name" value="<?php echo $memberidentity[$acctdepositoaccount['identity_id']];?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">No. Identitas</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="member_identity_no" id="member_identity_no" value="<?php echo $acctdepositoaccount['member_identity_no'];?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Jangka Waktu (Bln)</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="deposito_account_period" id="deposito_account_period" value="<?php echo $acctdepositoaccount['deposito_account_period'];?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Saldo</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="deposito_account_amount" id="deposito_account_amount" value="<?php echo number_format($acctdepositoaccount['deposito_account_amount'], 2);?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Tanggal Mulai</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 70%" name="deposito_account_date" id="deposito_account_date" value="<?php echo tgltoview($acctdepositoaccount['deposito_account_date']);?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Jatuh Tempo</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 70%" readonly name="deposito_account_due_date" id="deposito_account_due_date" value="<?php echo tgltoview($acctdepositoaccount['deposito_account_due_date']);?>" /></td>
									</tr>
									<tr>
										<td colspan="3" align="left"><b>Perpanjangan Simpanan</b></td>
									</tr>
									<tr>
										<td width="35%">Jangka Waktu (Bln)</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="deposito_account_extra_period" id="deposito_account_extra_period" autocomplete="off"/></td>
									</tr>
									<tr>
										<td width="35%">Tanggal Mulai</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 70%" name="deposito_account_extra_date" id="deposito_account_extra_date" autocomplete="off" value="<?php echo date('d-m-Y'); ?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Jatuh Tempo</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width: 70%" readonly name="deposito_account_extra_due_date" id="deposito_account_extra_due_date" autocomplete="off" />
										</td>
									</tr>
									<tr>
										<td width="35%">Biaya Adm (Rp)</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="deposito_account_amount_adm_view" id="deposito_account_amount_adm_view" autocomplete="off" style="width: 100%" value="<?php echo set_value('deposito_account_amount_adm',$data['deposito_account_amount_adm']);?>" /><input type="hidden" class="easyui-textbox" name="deposito_account_amount_adm" id="deposito_account_amount_adm" autocomplete="off" value="<?php echo set_value('deposito_account_amount_adm',$data['deposito_account_amount_adm']);?>"/></td>
										
									</tr>

									<input type="hidden" class="form-control" name="member_id" id="member_id" value="<?php echo $acctdepositoaccount['member_id'];?>" readonly/>
									<input type="hidden" class="form-control" name="deposito_id" id="deposito_id" value="<?php echo $acctdepositoaccount['deposito_id'];?>" readonly/>
									<input type="hidden" class="form-control" name="deposito_account_nisbah" id="deposito_account_nisbah" value="<?php echo $acctdepositoaccount['deposito_account_nisbah'];?>" readonly/>
									<input type="hidden" class="form-control" name="deposito_account_amount" id="deposito_account_amount" value="<?php echo $acctdepositoaccount['deposito_account_amount'];?>" readonly/>
									<input type="hidden" class="form-control" name="savings_account_id" id="savings_account_id" value="<?php echo $acctdepositoaccount['savings_account_id'];?>" readonly/>
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