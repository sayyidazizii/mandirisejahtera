<?php 
echo $count_payment;
exit;

error_reporting(0);?>
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
  background-color: #e7ecf1;
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
	
	function hanyaAngka(evt) {
		  var charCode = (evt.which) ? evt.which : event.keyCode
		   if (charCode > 31 && (charCode < 48 || charCode > 57))
 
		    return false;
		  return true;
		}

	$(document).ready(function(){
        $("#credits_account_id").change(function(){
            var credits_account_id = $("#credits_account_id").val();
            $.post(base_url + 'cash-payments/get-credit-account-detail',
			{credits_account_id: credits_account_id},
                function(data){	
					
                	// alert(data);
                	$("#member_id").val(data.member_id);
				   	$("#credits_id").val(data.credits_id);
				   	// $("#pembiayaan").val(data.pembiayaan);
				   	// $("#member_name").val(data.member_name);
					console.log(data.jumlah_angsuran);
					$('#member_name').textbox('setValue',data.member_name);
					$('#pembiayaan').textbox('setValue',data.pembiayaan);
					$('#member_address').textbox('setValue',data.member_address);
					$('#city_name').textbox('setValue',data.city_name);
					$('#kecamatan_name').textbox('setValue',data.kecamatan_name);
					$('#identity_name').textbox('setValue',data.identity_name);
					$('#jangka_waktu').textbox('setValue',data.jangka_waktu);
					$('#jatuh_tempo').textbox('setValue',data.jatuh_tempo);
					$('#jumlah_angsuran').textbox('setValue',data.jumlah_angsuran);
					$('#tanggal_realisasi').textbox('setValue',data.tanggal_realisasi);
					$('#sisa_pokok_view').textbox('setValue',toRp(data.sisa_pokok));
					$('#sisa_margin_view').textbox('setValue',toRp(data.sisa_margin));
					$('#saldo_piutang_view').textbox('setValue',toRp(data.saldo_piutang));
					$('#jumlah_angsuran_view').textbox('setValue',toRp(data.jumlah_angsuran));
					$('#angsuran_total_view').textbox('setValue',toRp(data.jumlah_angsuran));
					$('#angsuran_total_view1').textbox('setValue',toRp(data.jumlah_angsuran));
					$('#angsuran_total').textbox('setValue',toRp(data.jumlah_angsuran));
					$('#angsuran_pokok_view').textbox('setValue',toRp(data.angsuran_pokok));
					$('#angsuran_pokok').textbox('setValue',toRp(data.angsuran_pokok));
					$('#angsuran_margin_view').textbox('setValue',toRp(data.angsuran_margin));
					$('#angsuran_margin').textbox('setValue',toRp(data.angsuran_margin));
					// $('#angsuran_total').textbox('setValue',toRp(data.jumlah_angsuran));
					// $("#member_address").val(data.member_address);
					// $("#city_name").val(data.city_name);
					// $("#kecamatan_name").val(data.kecamatan_name);
					// $("#identity_name").val(data.identity_name);
					// $("#member_identity_no").val(data.member_identity_no);
					// $("#jangka_waktu").val(data.jangka_waktu);
					// $("#jatuh_tempo").val(data.jatuh_tempo);
					// $("#tanggal_realisasi").val(data.tanggal_realisasi);
					// $("#sisa_pokok_view").val(toRp(data.sisa_pokok));
					// $("#sisa_pokok_awal").val(data.sisa_pokok);
					// $("#sisa_pokok_awal").val(data.sisa_pokok);
					// $("#sisa_pokok_akhir").val(data.sisa_pokok);
					// $("#sisa_margin_view").val(toRp(data.sisa_margin));
					// $("#sisa_margin_awal").val(data.sisa_margin);
					// $("#sisa_margin_akhir").val(data.sisa_margin);
					// $("#saldo_piutang_view").val(toRp(data.saldo_piutang));
					// $("#angsuran_per_bulan").val(data.payment_amount);
					// $("#jumlah_angsuran_view").val(toRp(data.jumlah_angsuran));
					// $("#jumlah_angsuran").val(data.jumlah_angsuran);
					// $("#angsuran_pokok_view").val(toRp(data.angsuran_pokok));
					// $("#angsuran_pokok").val(data.angsuran_pokok);
					// $("#angsuran_margin_view").val(toRp(data.angsuran_margin));
					// $("#angsuran_margin").val(data.angsuran_margin);
					// $("#angsuran_total_view").val(toRp(data.jumlah_angsuran));
					// $("#angsuran_total").val(data.jumlah_angsuran);
				},
					'json'
				);
				
				
				$.ajax({
					type: 'GET',
					url : base_url + 'AcctCashLessRepayments/getDetailPayment',
					data: {'credits_account_id' : credits_account_id},
					success: function(msg){
						$('#tabelpembayaran').html(msg);
					}
				});				
            });
        });
		
	function hitungpayment(ganti){
		var jumlah_angsuran = $('#jumlah_angsuran').val();
		var pokok_angsuran = $('#angsuran_pokok').val();
		var margin_angsuran = $('#angsuran_margin').val();
		// var sisa_margin				= $('#sisa_margin_awal').val();	
		// var sisa_margin_awal = parseFloat(sisa_margin) + parseFloat(margin_angsuran);

		
		var jumlah_margin= 0;
		var jumlah_pokok= 0;
		var angsuran_total= 0;
		if(jumlah_angsuran == ''){
			alert('Isikan Jumlah Angsuran ! ');
			document.getElementById('angsuran_pokok_view').value		= '';
			document.getElementById('angsuran_pokok').value			= '';
			document.getElementById('angsuran_margin_view').value		= '';
			document.getElementById('angsuran_margin').value			= '';
			document.getElementById('angsuran_total_view').value			= '';
			document.getElementById('angsuran_total').value			= '';
		}else{
			if(ganti == 'jumlah'){
				jumlah_margin = 0;
				jumlah_pokok = 0;
			}else if(ganti == 'pokok'){
				jumlah_margin = parseFloat(jumlah_angsuran) - parseFloat(pokok_angsuran);
				jumlah_pokok = pokok_angsuran;
			}else if(ganti == 'margin'){
				jumlah_margin = margin_angsuran;
				jumlah_pokok = parseFloat(jumlah_angsuran) - parseFloat(margin_angsuran);
			}
			angsuran_total=parseFloat(jumlah_margin) + parseFloat(jumlah_pokok);
			// var saldo_margin = parseFloat(sisa_margin_awal) - patseFloat(jumlah_margin);	
			document.getElementById('angsuran_pokok_view').value		= toRp(jumlah_pokok);
			document.getElementById('angsuran_pokok').value			= jumlah_pokok;
			document.getElementById('angsuran_margin_view').value		= toRp(jumlah_margin);
			document.getElementById('angsuran_margin').value			= jumlah_margin;
			document.getElementById('angsuran_total_view').value			= toRp(angsuran_total);
			document.getElementById('angsuran_total').value			= angsuran_total;
			// document.getElementById('sisa_margin_view').value			= toRp(saldo_margin);
			// document.getElementById('sisa_margin_akhir').value			= saldo_margin;
		}
	}

	$(document).ready(function(){
        $("#jumlah_angsuran_view").change(function(){
			var jumlah_angsuran_view				= $('#jumlah_angsuran_view').val();			
			document.getElementById('jumlah_angsuran_view').value		= toRp(jumlah_angsuran_view);
			document.getElementById('jumlah_angsuran').value			= jumlah_angsuran_view;
			hitungpayment('jumlah');
		});
	});
	
	$(document).ready(function(){
        $("#angsuran_pokok_view").change(function(){
			var angsuran_pokok_view		= $('#angsuran_pokok_view').val();	
			var angsuran_pokok			= $('#angsuran_pokok').val();	
			// var sisa_pokok				= $('#sisa_pokok_awal').val();	
			// var sisa_pokok_awal = sisa_pokok + angsuran_pokok;
			// var saldo_pokok = sisa_pokok - angsuran_pokok_view;			

			document.getElementById('angsuran_pokok_view').value	= toRp(angsuran_pokok_view);
			document.getElementById('angsuran_pokok').value			= angsuran_pokok_view;
			// document.getElementById('sisa_pokok_view').value		= toRp(saldo_pokok);
			// document.getElementById('sisa_pokok_akhir').value		= saldo_pokok;
			hitungpayment('pokok');
			
		});
	});
	
	$(document).ready(function(){
        $("#angsuran_margin_view").change(function(){
			var angsuran_margin_view	= $('#angsuran_margin_view').val();	
			var angsuran_margin			= $('#angsuran_margin').val();		
			// var sisa_margin				= $('#sisa_margin_awal').val() + $('#angsuran_margin_view').val();	
			// var sisa_margin_awal = sisa_margin + angsuran_margin;
			// var saldo_margin = sisa_margin_awal - angsuran_margin_view;	

			document.getElementById('angsuran_margin_view').value		= toRp(angsuran_margin_view);
			document.getElementById('angsuran_margin').value			= angsuran_margin_view;
			// document.getElementById('sisa_margin_view').value			= toRp(saldo_margin);
			// document.getElementById('sisa_margin_akhir').value			= saldo_margin;
			hitungpayment('margin');
		});
	});
	
	

	$(document).ready(function(){
        $("#Save").click(function(){
        	// var credits_account_id 		= $("#credits_account_id").val();
        	var credits_account_id 		= document.getElementById('credits_account_id').value
			// var jumlah_angsuran_view 	= $("#jumlah_angsuran_view").val();
			var jumlah_angsuran_view 	= document.getElementById('jumlah_angsuran_view').value
			
			// var angsuran_total 			= $("#angsuran_total").val();
			var angsuran_total 			= document.getElementById('angsuran_total').value
			// var jumlah_angsuran 		= $("#jumlah_angsuran").val();
			var jumlah_angsuran 		= toRp(document.getElementById('jumlah_angsuran').value)
			console.log("jumlah angsuran="+jumlah_angsuran);
			console.log("angsuran total="+angsuran_total);
			if(credits_account_id == ''){
				alert("No. Rekening masih kosong");
				return false;
			}else if(jumlah_angsuran_view == ''){
				alert("Jumlah Angsuran Belum Dimasukkan");
				return false;
			}else if(angsuran_total != jumlah_angsuran){
				alert("Cek Alokasi Angsuran ! ");
				return false;
			}else{
				return true;
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
			<a href="<?php echo base_url();?>cash-payments/add">
				Tambah Pembayaran Pinjaman - Tunai 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->
<?php echo form_open('cash-payments/process-add',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
	$sesi 	= $this->session->userdata('unique');
	$data 	= $this->session->userdata('addacctcashpayment-'.$sesi['unique']);
?>
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
						Form Tambah
					</div>
				</div>
					<input type="hidden" class="form-control" name="member_id" id="member_id" autocomplete="off" readonly/> 
					<input type="hidden" class="form-control" name="credits_id" id="credits_id" autocomplete="off" readonly/>
					<div class="portlet-body">
						<div class="form-body">
						<div class="row">	
							<div class="col-md-5">
								<table>
									<tr>
										<td>No. Akad</td>
										<td>:</td> 
										<td><?php echo form_dropdown('credits_account_id', $accountcredit, '','id="credits_account_id" class="form-control select2me" ');?>
										</td>
									 </tr>
									 <tr>
										<td>Pembiayaan</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="pembiayaan" id="pembiayaan" autocomplete="off" readonly/>
										</td>
									 </tr>
									 <tr>
										<td>Nama</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" readonly/>
										</td>
									 </tr>
									 <tr>
										<td>Alamat</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="member_address" id="member_address" autocomplete="off" readonly/>
										</td>
									 </tr>
									 <tr>
										<td>Kota</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="member_city" id="member_city" autocomplete="off" readonly/>
										</td>
									 </tr>
									 <tr>
										<td>Identitas</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="member_identity" id="member_identity_no" autocomplete="off" readonly/>
										</td>
									 </tr>
									 <tr>
										<td>Jumlah Angsuran</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="jumlah_angsuran_view" id="jumlah_angsuran_view" onkeypress="return hanyaAngka(event)"/>
											<input type="hidden" class="easyui-textbox" name="jumlah_angsuran" id="jumlah_angsuran" />
										</td>
									 </tr>
									 <tr>
										<td>Angsuran Pokok</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="angsuran_pokok_view" id="angsuran_pokok_view" onkeypress="return hanyaAngka(event)"/>
											<input type="hidden" class="easyui-textbox" name="angsuran_pokok" id="angsuran_pokok" />
										</td>
									 </tr>
									 <tr>
										<td>Angsuran Margin</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="angsuran_margin_view" id="angsuran_margin_view" onkeypress="return hanyaAngka(event)"/>
												<input type="hidden" class="easyui-textbox" name="angsuran_margin" id="angsuran_margin" />
										</td>
									 </tr>
									 <tr>
										<td>Total</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="angsuran_total_view1" id="angsuran_total_view1" readonly/>
										<input type="hidden" class="easyui-textbox" name="angsuran_total" id="angsuran_total" readonly/>
										</td>
									 </tr>
								</table>
							</div>
							<div class="col-md-5">
								<table>
									<tr>
										<td>Tanggal Angsuran</td>
										<td>:</td> 
										<td><input type="text" name="tanggal_angsuran" id="tanggal_angsuran" value="<?php echo date('d-m-Y'); ?>" class="easyui-textbox" readonly>
										</td>
									 </tr>
									 <tr>
										<td>Tanggal Realisasi</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="tanggal_realisasi" id="tanggal_realisasi" autocomplete="off" readonly/>
										</td>
									 </tr>
									 <tr>
										<td>Jt Tempo</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="jatuh_tempo" id="jatuh_tempo" autocomplete="off" readonly/>
										</td>
									 </tr>
									 <tr>
										<td>Angsuran Ke</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="angsuran_ke" id="angsuran_ke" autocomplete="off" value="<?php echo $count_payment;?>" readonly/>
										</td>
									 </tr>
									 <tr>
										<td>Jangka Waktu</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="jangka_waktu" id="jangka_waktu" autocomplete="off" readonly/>
										</td>
									 </tr>
									 <tr>
										<td>SISA POKOK</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="sisa_pokok_view" id="sisa_pokok_view" readonly/>
                                                <input type="hidden" class="easyui-textbox" name="sisa_pokok_awal" id="sisa_pokok_awal" readonly/>
                                                <input type="hidden" class="easyui-textbox" name="sisa_pokok_akhir" id="sisa_pokok_akhir" readonly/>
										</td>
									 </tr>
									 <tr>
										<td>SISA MARGIN</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="sisa_margin_view" id="sisa_margin_view" readonly/>
                                                <input type="hidden" class="easyui-textbox" name="sisa_margin_awal" id="sisa_margin_awal" readonly/>
                                                <input type="hidden" class="easyui-textbox" name="sisa_margin_akhir" id="sisa_margin_akhir" readonly/>
										</td>
									 </tr>
									 <tr>
										<td>Saldo Piutang</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="saldo_piutang_view" id="saldo_piutang_view" readonly/>
										</td>
									 </tr>
									 <tr>
										<td>Angsuran Per Bulan</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="angsuran_per_bulan" id="angsuran_per_bulan" readonly/>
										</td>
									 </tr>
									 <tr>
										<td>Total</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="angsuran_total_view" id="angsuran_total_view" readonly/>
												<input type="hidden" class="easyui-textbox" name="angsuran_total" id="angsuran_total" readonly/>
										</td>
									 </tr>
								</table>
							</div>
						</div>
							 
							<div class="row">
								<div class="col-md-12" style='text-align:right'>
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
								<tr>
									<th>Ke</th>
									<th>Tgl Angsuran</th>
									<th>Angsuran Pokok</th>
									<th>Angsuran Margin</th>
									<th>Saldo Pokok</th>
									<th>Saldo Margin</th>
								</tr>
								</table>
							</div>
							
						</div>
					</div>
				
			 </div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
