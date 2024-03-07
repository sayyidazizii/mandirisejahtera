<?php
error_reporting(0);
?>
<style>
th, td {
  padding: 2px;
  font-size: 13px;
}
input:focus { 
  background-color: 42f483;
}

.table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th{
    padding: 2px;
    vertical-align: top;
    border-top: 1px solid #e7ecf1;
}
.table td, .table th {
    font-size: 12px;
}

input:-moz-read-only { /* For Firefox */
  background-color: #e7ecf1;
}

	input:read-only {
  		background-color: f0f8ff;
	}
</style>
<script>
	base_url = '<?php echo base_url();?>';
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
		document.location = base_url+"member/reset-edit/<?php echo $coremember['member_id']?>";
	}

	var loopjumlah	= 1;
	var loopsimp 	= 1;
	var loopfine 	= 1;

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
		$.ajax({
					type: 'GET',
					url : base_url + 'cash-payments/get-detail-payment',
					data: {'credits_account_id' : <?php echo $this->uri->segment(3);?>},
					success: function(msg){
						$('#tabelpembayaran').html(msg);
					}
				});
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
	
	

	function hitungdenda(){
	 
	 let bayar_denda 		= document.getElementById("credits_payment_fine").value;

	 let jumlah_saknsi_bulan_ini_new = parseInt(jumlah_sanksi_bulan_ini, 10) + parseInt(bayar_denda, 10)
	 let jumlah_denda_new	= parseInt(jumlah_akumulasi_sanksi, 10) + parseInt(jumlah_saknsi_bulan_ini_new, 10);


	 $('#credits_account_accumulated_fines_view').textbox('setValue',toRp(jumlah_denda_new));
	 $('#credits_account_accumulated_fines').textbox('setValue',jumlah_denda_new);
	 $('#credits_payment_fine_amount_view').textbox('setValue',toRp(jumlah_saknsi_bulan_ini_new));
	 $('#credits_payment_fine_amount').textbox('setValue',jumlah_saknsi_bulan_ini_new);
 }
 function hitungtotal(){
		var pokok_angsuran 		= document.getElementById("credits_payment_principal").value;
		var interest_angsuran 	= document.getElementById("credits_payment_interest").value;
		// var pendapatan_jasa		= document.getElementById("interest_income").value;
		var bayar_denda 		= document.getElementById("credits_payment_fine").value;
		var pendapatan_lain		= document.getElementById("others_income").value;
		var simpanan_wajib 		= document.getElementById("member_mandatory_savings").value;

		if(interest_angsuran == ''){
			interest_angsuran = 0
		}
		
		if(pokok_angsuran == ''){
			pokok_angsuran = 0
		}
		
		if(bayar_denda == ''){
			bayar_denda = 0
		}

		// if(pendapatan_jasa == ''){
		// 	pendapatan_jasa = 0
		// }

		if(pendapatan_lain == ''){
			pendapatan_lain = 0
		}
		if(simpanan_wajib == ''){
			simpanan_wajib = 0
		}

		var total 				= parseFloat(pokok_angsuran) + parseFloat(interest_angsuran) + parseFloat(pendapatan_lain) + parseFloat(bayar_denda) + parseFloat(simpanan_wajib);
		
		$('#total_view').textbox('setValue',toRp(total));
		$('#total').textbox('setValue',total);
	}

	$(document).ready(function(){
		$('#credits_payment_fine_view').textbox({
			onChange: function(value){
				if(loopfine == 0){
					loopfine = 1;
					return;
				}
				if(loopfine ==1){
					loopfine = 0;
					var credits_payment_fine				= $('#credits_payment_fine_view').val();	
					$('#credits_payment_fine_view').textbox('setValue',toRp(credits_payment_fine));
					$('#credits_payment_fine').textbox('setValue',credits_payment_fine);			
					hitungdenda();
					hitungtotal();

				}else{
					loopfine=1;
					return;
				}
				
			}
		});
		$('#credits_payment_interest_view').textbox({
			onChange: function(value){
				if(loopfine == 0){
					loopfine = 1;
					return;
				}
				if(loopfine ==1){
					loopfine = 0;
					var credits_payment_interest				= $('#credits_payment_interest_view').val();	
					$('#credits_payment_interest_view').textbox('setValue',toRp(credits_payment_interest));
					$('#credits_payment_interest').textbox('setValue',credits_payment_interest);			
					hitungtotal();

				}else{
					loopfine=1;
					return;
				}
				
			}
		});
		$('#credits_payment_principal_view').textbox({
			onChange: function(value){
				if(loopfine == 0){
					loopfine = 1;
					return;
				}
				if(loopfine ==1){
					loopfine = 0;
					var credits_payment_principal				= $('#credits_payment_principal_view').val();	
					$('#credits_payment_principal_view').textbox('setValue',toRp(credits_payment_principal));
					$('#credits_payment_principal').textbox('setValue',credits_payment_principal);			
					hitungtotal();

				}else{
					loopfine=1;
					return;
				}
				
			}
		});
		$('#interest_income_view').textbox({
			onChange: function(value){
				if(loopfine == 0){
					loopfine = 1;
					return;
				}
				if(loopfine ==1){
					loopfine = 0;
					var interest_income				= $('#interest_income_view').val();	
					$('#interest_income_view').textbox('setValue',toRp(interest_income));
					$('#interest_income').textbox('setValue',interest_income);			
					hitungtotal();

				}else{
					loopfine=1;
					return;
				}
				
			}
		});
		$('#others_income_view').textbox({
			onChange: function(value){
				if(loopfine == 0){
					loopfine = 1;
					return;
				}
				if(loopfine ==1){
					loopfine = 0;
					var others_income				= $('#others_income_view').val();	
					$('#others_income_view').textbox('setValue',toRp(others_income));
					$('#others_income').textbox('setValue',others_income);			
					hitungtotal();

				}else{
					loopfine=1;
					return;
				}
				
			}
		});
		$('#member_mandatory_savings_view').textbox({
			onChange: function(value){
				// alert(looppokok);
				if(loopsimp == 0){
					loopsimp = 1;
					return;
				}
				
				if(loopsimp ==1){
					loopsimp = 0;
					nilai = (value);
					$('#member_mandatory_savings').textbox('setValue',value);
					$('#member_mandatory_savings_view').textbox('setValue',toRp(nilai));
					hitungtotal();

				}else{
					loopsimp = 1;
					return;
				}
			}
		});
	});

	function openInNewTab(href) {
	Object.assign(document.createElement('a'), {
		target: '_blank',
		href: href,
	}).click();
	}

	$(document).ready(function(){
        $("#Save").click(function(e){
			e.preventDefault();
			let dataString = $('#myform').serialize();
			let idlastpayment = '';
        	var credits_account_id 		= document.getElementById('credits_account_id').val;
			var angsuran_total 			= document.getElementById('total_view').val;
			var pokok_angsuran 			= document.getElementById("credits_payment_principal_view").value;
			if(credits_account_id == ''){
				alert("No. Rekening masih kosong");
				return false;
			}else if(angsuran_total == '' || parseFloat(angsuran_total) == 0.00){
				alert("Cek Alokasi Angsuran ! ");
				return false;
			}
			// else if(pokok_angsuran == '' || parseFloat(pokok_angsuran) == 0.00){
			// 	alert("Angsuran Pokok Wajib diisi !");
			// 	return false;
			// }
			
			$.ajax({
					type: 'post',
					data: dataString,       
					url: '<?php echo base_url();?>cash-payments/process-cash-payment',
					success: function (results) {
						
						idlastpayment = results;
						if(idlastpayment != 'error'){
							openInNewTab(base_url+"cash-payments/print-note-less/"+idlastpayment);
						}
						document.location = base_url+"cash-payments/add-cash-less";
					}
			});
		});
    });

</script>
<?php echo form_open('cash-payments/process-cash-payment',array('id' => 'myform', 'class' => 'horizontal-form')); ?>

		<!-- BEGIN PAGE TITLE & BREADCRUMB-->
<div class="page-bar">
<?php 
	$segment3 						= $this->uri->segment(3);
	
	
	$segment4 						= $this->uri->segment(4); ?>
<?php 
	$credits_payment_date 			= date('Y-m-d');
	// $credits_payment_date 			= '2019-12-20';
	$date1 							= date_create($credits_payment_date);
	$date2 							= date_create($credit_account['credits_account_payment_date']);
	// if(substr($credit_account['credits_account_payment_to'], -1) == '*'){
	// 	$angsuranke 					= substr($credit_account['credits_account_payment_to'], -2, 1);
	// }else{
	$angsuranke 					= substr($credit_account['credits_account_payment_to'], -1) + 1;
	// }

	if($date1 > $date2){
		$interval                       = $date1->diff($date2);
    	$credits_payment_day_of_delay   = $interval->days;
	} else {
		$credits_payment_day_of_delay 		= 0;
	}

	$credits_payment_fine_amount 		= (($credit_account['credits_account_payment_amount'] * $credit_account['credits_fine']) / 100 ) * $credits_payment_day_of_delay;
	$credits_account_accumulated_fines 	= $credit_account['credits_account_accumulated_fines'] + $credits_payment_fine_amount;

	if($credit_account['payment_type_id'] == 1){
		$angsuranpokok 		= $credit_account['credits_account_principal_amount'];
		$angsuranbunga 	 	= $credit_account['credits_account_interest_amount'] + $interest_plus;
	} else if($credit_account['payment_type_id'] == 2){
		$angsuranbunga 	 	= $anuitas[$angsuranke]['angsuran_bunga'] + $interest_plus;
		$angsuranpokok 		= $anuitas[$angsuranke]['angsuran_pokok'];
	} else if($credit_account['payment_type_id'] == 3){
		$angsuranbunga 	 	= $slidingrate[$angsuranke]['angsuran_bunga'] + $interest_plus;
		$angsuranpokok 		= $slidingrate[$angsuranke]['angsuran_pokok'];
	} else if($credit_account['payment_type_id'] == 4){
		$angsuranbunga		= $angsuran_bunga_menurunharian + $interest_plus;
		$angsuranpokok		= 0;
	}
?>
<script>
	const jumlah_sanksi_bulan_ini = <?=$credits_payment_fine_amount?>;
	const jumlah_akumulasi_sanksi = <?=$credits_account_accumulated_fines?>;
</script>

	<ul class="page-breadcrumb">
		<li>
			<i class="fa fa-home"></i>
			<a href="<?php echo base_url();?>">
				Beranda
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>cash-payments/add-cash-less/">
				Angsuran Debet Tabungan
			</a>
		</li>
	</ul>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Debet Tabungan
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>cash-payments/ind-cash-less-payment" class="btn btn-default btn-sm">
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
							$token 			= $this->session->userdata('acctcreditspaymentlesstoken-'.$unique['unique']);

						?>

						<div class="row">
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">No. Perjanjian Kredit<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="20%"><input type="text"  class="easyui-textbox" size="4" name="credits_account_serial" id="credits_account_serial" autocomplete="off" value="<?php echo set_value('credits_account_serial', $credit_account['credits_account_serial']);?>" style="width: 50%" readonly/> &nbsp<a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#akadlist">Search</a></td>
									</tr>
									<tr>
										<td width="35%">Pinjaman<span class="required">*</span></td>
										<td width="5%"></td>
										<input type="hidden" size="4" name="credits_account_id" id="credits_account_id" autocomplete="off" value="<?php echo set_value('credits_account_id', $credit_account['credits_account_id']);?>" style="width: 50%" readonly/>
										<input type="hidden" size="4" name="credits_id" id="credits_id" autocomplete="off" value="<?php echo set_value('credits_id', $credit_account['credits_id']);?>" style="width: 50%" readonly/>
										<?php if($segment4 != ''){ ?>
											<input type="hidden" size="4" name="savings_account_id" id="savings_account_id" autocomplete="off" value="<?php echo set_value('savings_account_id', $saving_account['savings_account_id']);?>" readonly/>
										<?php } else { ?>
											<input type="hidden" size="4" name="savings_account_id" id="savings_account_id" autocomplete="off" value="" readonly/>
										<?php } ?>
										<td width="60%"><input type="text" class="easyui-textbox" name="credits_account_used" id="credits_account_used" autocomplete="off" value="<?php echo set_value('credits_name', $credit_account['credits_name']);?>" style="width: 100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Rek Simpanan<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="20%">
											<?php if($segment4 != ''){ ?>
												<input type="text"  class="easyui-textbox" size="4" name="savings_account_no" id="savings_account_no" autocomplete="off" value="<?php echo set_value('savings_account_no', $saving_account['savings_account_no']);?>" style="width: 50%" readonly/>
											<?php } else { ?>
												<input type="text"  class="easyui-textbox" size="4" name="savings_account_no" id="savings_account_no" autocomplete="off" value="<?php echo $credit_account['savings_account_no'];?>" style="width: 50%" readonly/>
											<?php } ?>
											 &nbsp <a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#simpananlist">Search</a> 
										</td>
									</tr>
									<tr>
										<td width="35%">Nama<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text"  class="easyui-textbox" size="4" name="member_name" id="member_name" autocomplete="off" value="<?php echo set_value('member_name', $credit_account['member_name']);?>" style="width: 50%" readonly/> </td>
									</tr>
									<tr>
										<td width="35%">Alamat<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
										<input type="text"  class="easyui-textbox" size="4" name="member_address" id="member_address" autocomplete="off" value="<?php echo set_value('member_address', $credit_account['member_address']);?>" style="width: 50%" readonly/> 
										</td>
									</tr>
									<tr>
										<td width="35%">Jumlah Tabungan<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="savings_account_last_balance" id="savings_account_last_balance" autocomplete="off"  style="width: 70%" value="<?php echo set_value('savings_account_last_balance', number_format($saving_account['savings_account_last_balance']));?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Tanggal Realisasi<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="credits_account_date" id="credits_account_date" autocomplete="off" value="<?php echo tgltoview($credit_account['credits_account_date']);?>" style="width: 70%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Jatuh Tempo<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="credits_account_payment_date" id="credits_account_payment_date" autocomplete="off" value="<?php echo tgltoview($credit_account['credits_account_payment_date']);?>" style="width: 70%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Tanggal Angsuran<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="credits_payment_date" id="credits_payment_date" autocomplete="off" value="<?php echo date('d-m-Y');?>" style="width: 70%" readonly/></td>
										<!-- <td width="60%"><input type="text" class="easyui-textbox" name="credits_payment_date" id="credits_payment_date" autocomplete="off" value="<?php echo tgltoview($credits_payment_date);?>" style="width: 70%" readonly/></td> -->
									</tr>
									<tr>
										<td width="35%">Angsuran Ke</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="credits_payment_to" id="credits_payment_to" autocomplete="off"  style="width: 70%" value="<?php echo $angsuranke;?>" readonly/></td>
									</tr>
								</table>
							</div>
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table>
									<tr>
										<td width="35%">Keterlambatan (Hari)</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="credits_payment_day_of_delay" id="credits_payment_day_of_delay" autocomplete="off"  style="width: 70%" value="<?php echo set_value('pembayaran_ke',$credits_payment_day_of_delay);?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Jumlah Sanksi<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="credits_account_accumulated_fines_view" id="credits_account_accumulated_fines_view" autocomplete="off" value="<?php echo number_format($credits_account_accumulated_fines, 2);?>" style="width: 70%" readonly/>
											<input type="hidden" class="easyui-textbox" name="credits_account_accumulated_fines" id="credits_account_accumulated_fines" autocomplete="off" value="<?php echo $credits_account_accumulated_fines;?>"/></td>

									</tr>
									<tr>
										<td width="35%">Sanksi Bulan Ini<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="credits_payment_fine_amount_view" id="credits_payment_fine_amount_view" autocomplete="off" value="<?php echo set_value('credits_payment_fine_amount_view', number_format($credits_payment_fine_amount_view, 2));?>" style="width: 70%" readonly/>
											<input type="hidden" class="easyui-textbox" name="credits_payment_fine_amount" id="credits_payment_fine_amount" autocomplete="off" value="<?php echo $credits_payment_fine_amount;?>"/></td>

									</td>
									</tr>
									<tr>
										<td width="35%">Jumlah Pinjaman<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="credits_account_amount" id="credits_account_amount" autocomplete="off" value="<?php echo set_value('credits_account_amount', number_format($credit_account['credits_account_amount']));?>" style="width: 70%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Outstanding<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="credits_account_last_balance" id="credits_account_last_balance" autocomplete="off" value="<?php echo set_value('credits_account_last_balance', number_format($credit_account['credits_account_last_balance']));?>" style="width: 70%" readonly/>
										<input type="hidden" class="easyui-textbox" name="sisa_bunga_awal" id="sisa_bunga_awal" value="<?php echo $credit_account['credits_account_interest_last_balance'];?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Saldo Cicilan<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
										<input type="text" class="easyui-textbox" name="credits_account_temp_installment_view" id="credits_account_temp_installment_view" autocomplete="off" value="<?php echo set_value('credits_account_temp_installment_view', number_format($credit_account['credits_account_temp_installment'])) ?>"  readonly/>
										<input type="hidden" class="easyui-textbox" name="credits_account_temp_installment" id="credits_account_temp_installment" value="<?php echo $credit_account['credits_account_temp_installment'];?>" readonly/></td>
										
									</tr>
									<tr>
										<td colspan="4"><div style="font-weight: bold">Guna Membayar (Kas Masuk)</div></td>
									</tr>
									<tr>
										<td width="35%">Angsuran Pokok (Rp)<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<?php if($credit_account['payment_type_id'] == 4){ ?>
												<input type="text" class="easyui-textbox" name="credits_payment_principal_view" id="credits_payment_principal_view" autocomplete="off" value="<?php echo number_format($angsuranpokok, 2);?>" style="width: 70%"/>
											<?php }else{ ?>
												<input type="text" class="easyui-textbox" name="credits_payment_principal_view" id="credits_payment_principal_view" autocomplete="off" value="<?php echo number_format($angsuranpokok, 2);?>" style="width: 70%" readonly/>
											<?php } ?>
											<input type="hidden" class="easyui-textbox" name="payment_type_id" id="payment_type_id" value="<?php echo $accountcredit['payment_type_id']; ?>" />
											<input type="hidden" class="easyui-textbox" name="credits_payment_principal" id="credits_payment_principal" autocomplete="off" value="<?php echo intval($angsuranpokok);?>"/>
											<input type="hidden" class="easyui-textbox" name="credits_payment_principal_actualy" id="credits_payment_principal_actualy" autocomplete="off" value="<?php echo intval($angsuranpokok);?>"/>
										</td>
									</tr>
									<tr>
										<td width="35%">Bunga Pinjaman (Rp)<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="credits_payment_interest_view" id="credits_payment_interest_view" autocomplete="off" value="<?php echo number_format($angsuranbunga, 2);?>" style="width: 70%" readonly/>
											<input type="hidden" class="easyui-textbox" name="credits_payment_interest" id="credits_payment_interest" autocomplete="off" value="<?php echo $angsuranbunga;?>" />
										</td>
									</tr>
									<tr>
										<td width="35%">Sanksi (Rp)<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="credits_payment_fine_view" id="credits_payment_fine_view" autocomplete="off" style="width: 70%" />
											<input type="hidden" class="easyui-textbox" name="credits_payment_fine" id="credits_payment_fine" autocomplete="off"/>
										</td>
									</tr>
									<tr>
										<td>Pendapatan Lain (Rp)</td>
										<td>:</td> 
										<td>
											<input type="text" class="easyui-textbox" name="others_income_view" id="others_income_view"  />
											<input type="hidden" class="easyui-textbox" name="others_income" id="others_income" />
										</td>
									 </tr>
									 <tr>
										<td colspan="4"><div style="font-weight: bold">Penerimaan (Kas Masuk)</div></td>
									 </tr>
									  <tr>
										<td>Simpanan Wajib</td>
										<td>:</td> 
										<td>
											<input type="text" class="easyui-textbox" name="member_mandatory_savings_view" id="member_mandatory_savings_view" value="<?php echo number_format($credit_account['member_mandatory_savings'], 2);?>"/>
											<input type="hidden" class="easyui-textbox" name="member_mandatory_savings" id="member_mandatory_savings" value="<?php echo $credit_account['member_mandatory_savings'];?>" readonly/>
										</td>
									 </tr>
									<tr>
										<td width="35%">Total(Rp)<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="total_view" id="total_view" autocomplete="off" value="<?php echo number_format($credit_account['credits_account_payment_amount'], 2);?>" style="width: 70%" readonly/>
											<input type="hidden" class="easyui-textbox" name="total" id="total" autocomplete="off" value="<?php echo $credit_account['credits_account_payment_amount'];?>" readonly/>

											<input type="hidden" class="form-control" name="credits_payment_period" id="credits_payment_period" autocomplete="off" readonly value="<?php echo $credit_account['credits_payment_period'];?>"/>
											<input type="hidden" class="form-control" name="jangka_waktu" id="jangka_waktu" autocomplete="off" readonly value="<?php echo $credit_account['credits_account_period'];?>"/>
										</td>
									</tr>
									
									<input type="hidden" class="easyui-textbox" name="credits_payment_token" id="credits_payment_token" value="<?php echo $token;?>" readonly/>
									
								</table>
									<div class="col-md-12" style='text-align:left'>
									<button type="reset" name="Reset" value="Reset" class="btn btn-danger" onClick="reset_data();"><i class="fa fa-times"> Batal</i></button>
									<button type="submit" name="Save" value="Save" id="Save" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Simpan</i></button>
								</div>
							</div> 
						</div>


						
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			 <div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Daftar Pembayaran
					</div>
				</div>
			
					<div class="portlet-body">
						<div class="form-body">
							<div id="tabelpembayaran">
								<table class="table table-striped table-hover">
								<thead>
										<tr>
											<th>Ke</th>
											<th>Tgl Angsuran</th>
											<th>Angsuran Pokok</th>
											<th>Bunga Pinjaman</th>
											<th>Pendapatan Bunga</th>
											<th>Pendapatan Lain</th>
											<th>Saldo Pokok</th>
											<th>Saldo Bunga</th>
										</tr>
								</thead>
								<tbody>
										<?php 
										$no=1;
										if(empty($detailpayment)){
											echo "<tr>
											<td align='center' colspan='9'> Data Angsuran belum ada !</td></tr>";
										} else {
											foreach ($detailpayment as $key=>$val){ 
	
											echo"
												<tr>
												<td>".$no."</td>
												<td>".tgltoview($val['credits_payment_date'])."</td>
												<td align='right'>".number_format($val['credits_payment_principal'], 2)."</td>
												<td align='right'>".number_format($val['credits_payment_interest'], 2)."</td>
												<td align='right'>".number_format($val['credits_interest_income'], 2)."</td>
												<td align='right'>".number_format($val['credits_others_income'], 2)."</td>
												<td align='right'>".number_format($val['credits_principal_last_balance'], 2)."</td>
												<td align='right'>".number_format($val['credits_interest_last_balance'], 2)."</td>
												</tr>
											";
											$no++;
											}
										} ?>
									</tbody>
								</table>
							</div>
							
						</div>
					</div>
				
			 </div>
		</div>
	</div>
</div>
<!-- 
DataTable
!-->
<div id="akadlist" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Member List</h4>
      </div>
      <div class="modal-body">
<table id="akadtable" class="table table-striped table-bordered table-hover table-full-width">
	<thead>
    	<tr>
        	<th>No</th>
        	<th>No. Perjanjian Kredit</th>
            <th>Member Nama</th>
            <th>Member No</th>
            <th>Tanggal Pinjam</th>
            <th>Jatuh Tempo</th>

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
<div id="simpananlist" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Rekening List</h4>
      </div>
      <div class="modal-body">
<table id="simpantable" class="table table-striped table-bordered table-hover table-full-width">
	<thead>
    	<tr>
        	<th>No</th>
        	<th>Member No</th>
            <th>Member Nama</th>
            <th>No Rekening</th>
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
<?php echo form_close(); ?>

<script type="text/javascript">
 
var table;
 
$(document).ready(function() {
 
    //datatables
    table = $('#akadtable').DataTable({ 
 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "pageLength": 5,
        "order": [], //Initial no order.
        "ajax": {
            "url": "<?php echo site_url('cash-payments/akad-list')?>",
            "type": "POST"
        },
        "columnDefs": [
        { 
            "targets": [ 0 ], //first column / numbering column
            "orderable": false, //set not orderable
        },
        ],
 
    });
	table = $('#simpantable').DataTable({ 
 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "pageLength": 5,
        "order": [], //Initial no order.
        "ajax": {
		
            "url": "<?php echo site_url('cash-payments/simpan-list/'.$segment3)?>",
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
