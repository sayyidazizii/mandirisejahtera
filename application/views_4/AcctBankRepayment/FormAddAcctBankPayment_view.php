<?php error_reporting(0);?>
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

	var loopfine	= 1;
	var loopsimp 	= 1;
	var loopinterest 	= 1;

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
		
	function hitungtotal(){
		var pokok_angsuran 		= +document.getElementById("angsuran_pokok").value;
		var interest_angsuran 	= +document.getElementById("angsuran_interest").value;
		var bayar_denda 		= +document.getElementById("credits_payment_fine").value;
		var simpanan_wajib 		= +document.getElementById("member_mandatory_savings").value;
		var jumlah_denda 		= +document.getElementById("credits_account_accumulated_fines").value;
		var denda_bln_ini 		= +document.getElementById("credits_payment_fine_amount").value;
		
		
		if(bayar_denda == ''){
			bayar_denda = 0
		}

		if(simpanan_wajib == ''){
			simpanan_wajib = 0
		}

		if(denda_bln_ini > 0){
			var denda_bln_ini_new	= parseFloat(denda_bln_ini) - parseFloat(bayar_denda);
		} else {
			var denda_bln_ini_new	= 0;
		}

		console.log(jumlah_denda);


		var total 				= parseFloat(pokok_angsuran) + parseFloat(interest_angsuran) + parseFloat(bayar_denda);
		var jumlah_denda_new	= parseFloat(jumlah_denda) - parseFloat(bayar_denda);
		
		$('#angsuran_total_view1').textbox('setValue',toRp(total));
		$('#credits_account_accumulated_fines_view').textbox('setValue',toRp(jumlah_denda_new));
		$('#credits_account_accumulated_fines').textbox('setValue',jumlah_denda_new);
		$('#credits_payment_fine_amount_view').textbox('setValue',toRp(denda_bln_ini_new));
		$('#credits_payment_fine_amount').textbox('setValue',denda_bln_ini_new);
	}

	function hitungtotalsimpanan(){
		var pokok_angsuran 		= +document.getElementById("angsuran_pokok").value;
		var interest_angsuran 	= +document.getElementById("angsuran_interest").value;
		var bayar_denda 		= +document.getElementById("credits_payment_fine").value;
		var simpanan_wajib 		= +document.getElementById("member_mandatory_savings").value;
		var jumlah_denda 		= +document.getElementById("credits_account_accumulated_fines").value;
		var denda_bln_ini 		= +document.getElementById("credits_payment_fine_amount").value;
		
		
		var total 				= parseFloat(pokok_angsuran) + parseFloat(interest_angsuran) + parseFloat(bayar_denda) + parseFloat(simpanan_wajib);
		
		$('#angsuran_total_view1').textbox('setValue',toRp(total));
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
					hitungtotalsimpanan();
					console.log('nilai');
					console.log(nilai);

				}else{
					loopsimp = 1;
					return;
				}
			}
		});
	});
	
	

	$(document).ready(function(){
        $("#Save").click(function(){
        	var credits_account_id 		= document.getElementById('credits_account_id').value
			var jumlah_angsuran_view 	= document.getElementById('jumlah_angsuran_view').value
			
			var angsuran_total 			= document.getElementById('angsuran_total').value
			var jumlah_angsuran 		= document.getElementById('jumlah_angsuran').value
			console.log("jumlah_angsuran="+jumlah_angsuran);
			console.log("angsuran total="+angsuran_total);

			if(credits_account_id == ''){
				alert("No. Rekening masih kosong");
				return false;
			}else if(jumlah_angsuran_view == ''){
				alert("Jumlah Angsuran Belum Dimasukkan");
				return false;
			}else if(angsuran_total == '' || parseFloat(angsuran_total) == 0.00){
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
			<a href="<?php echo base_url();?>bank-payments/add">
				Tambah Pembayaran Pinjaman via Bank 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->
<?php echo form_open('bank-payments/process-add',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
	$sesi 			= $this->session->userdata('unique');
	$data 			= $this->session->userdata('addAcctBankPayment-'.$sesi['unique']);
	$token 			= $this->session->userdata('acctcreditspaymentcashtoken-'.$unique['unique']);


	$credits_payment_date = date('Y-m-d');
	// $credits_payment_date 			= '2020-02-18';
	$date1 = date_create($credits_payment_date);
	$date2 = date_create($accountcredit['credits_account_payment_date']);

	

	if($date1 > $date2){
		$interval                       = $date1->diff($date2);
    	$credits_payment_day_of_delay   = $interval->days;
	} else {
		$credits_payment_day_of_delay 	= 0;
	}
	

	// print_r($credits_payment_day_of_delay);
$saldobunga = $accountcredit['credits_account_interest_last_balance'] + $accountcredit['credits_account_interest_amount'] ;
	$angsuranke 						= $accountcredit['credits_account_payment_to'] + 1;

	$credits_payment_fine_amount 		= (($accountcredit['credits_account_payment_amount'] * $accountcredit['credits_fine']) / 100 ) * $credits_payment_day_of_delay;
	$credits_account_accumulated_fines 	= $accountcredit['credits_account_accumulated_fines'] + $credits_payment_fine_amount;

	if($accountcredit['payment_type_id'] == 1){
		$angsuranpokok 		= $accountcredit['credits_account_principal_amount'];
		$angsuranbunga 	 	= $accountcredit['credits_account_interest_amount'];
	} else if($accountcredit['payment_type_id'] == 2){
		$angsuranbunga 	 	= ($accountcredit['credits_account_last_balance'] * $accountcredit['credits_account_interest']) /100;
		$angsuranpokok 		= $accountcredit['credits_account_payment_amount'] - $angsuranbunga;
	}
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
					<div class="actions">
						<a href="<?php echo base_url();?>bank-payments/ind-bank-payment" class="btn btn-default btn-sm">
							<i class="fa fa-angle-left"></i>
							<span class="hidden-480">
								Kembali
							</span>
						</a>
					</div>
				</div>
					<input type="hidden" class="form-control" name="member_id" id="member_id" autocomplete="off" value="<?php echo $accountcredit['member_id'];?>" readonly/> 
					<input type="hidden" class="form-control" name="credits_id" id="credits_id" autocomplete="off" readonly value="<?php echo $accountcredit['credits_id'];?>"/>
					<input type="hidden" class="form-control" name="credits_payment_period" id="credits_payment_period" autocomplete="off" readonly value="<?php echo $accountcredit['credits_payment_period'];?>"/>
					
					<div class="portlet-body">
						<div class="form-body">
						<div class="row">
							<div class="col-md-1">	
							</div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">No. Perjanjian Kredit<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text"  class="easyui-textbox" size="4" name="credits_account_serial" id="credits_account_serial" autocomplete="off" value="<?php echo $accountcredit['credits_account_serial'];?>" style="width: 50%" readonly/> &nbsp<a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#akadlist">Search</a>
											<input type="hidden"  class="easyui-textbox" name="credits_account_id" id="credits_account_id" autocomplete="off" value="<?php echo $accountcredit['credits_account_id'];?>" readonly/>
									</td>
									</tr>
									 <tr>
										<td>Pinjaman</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="pembiayaan" id="pembiayaan" autocomplete="off" value="<?php echo $accountcredit['credits_name'];?>" readonly/>
										</td>
									 </tr>
									 <tr>
										<td>Nama</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo $accountcredit['member_name'];?>" readonly/>
										</td>
									 </tr>
									 <tr>
										<td>Alamat</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="member_address" id="member_address" autocomplete="off" value="<?php echo $accountcredit['member_address'];?>" readonly/>
										</td>
									 </tr>
									 <tr>
										<td>Kota</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="member_city" id="member_city" autocomplete="off" value="<?php echo $accountcredit['city_name'];?>" readonly/>
										</td>
									 </tr>
									 <tr>
										<td>Tanggal Realisasi</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="tanggal_realisasi" id="tanggal_realisasi" autocomplete="off" value="<?php echo tgltoview($accountcredit['credits_account_date']);?>"  readonly/>
										</td>
									 </tr>
									 <tr>
										<td>Jt Tempo</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="jatuh_tempo" id="jatuh_tempo" autocomplete="off" value="<?php echo tgltoview($accountcredit['credits_account_payment_date']);?>" readonly/>
										</td>
									 </tr>
									<tr>
										<td width="35%">Tanggal Angsuran</td>
										<td width="5%">:</td> 
										<td width="60%"><input type="text" name="tanggal_angsuran" id="tanggal_angsuran" value="<?php echo date('d-m-Y'); ?>" class="easyui-textbox" readonly>
										</td>

										<!-- <td width="60%"><input type="text" name="tanggal_angsuran" id="tanggal_angsuran" value="<?php echo tgltoview($credits_payment_date); ?>" class="easyui-textbox" readonly>
										</td> -->
									 </tr>
									
									 <tr>
										<td>Angsuran Ke</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="credits_payment_to" id="credits_payment_to" autocomplete="off" value="<?php echo count($detailpayment)+1;?>" readonly/>
											<input type="hidden" class="easyui-textbox" name="credits_account_payment_date" id="credits_account_payment_date" autocomplete="off" value="<?php echo $accountcredit['credits_account_payment_date'];?>" readonly/>
										</td>
									 </tr>
									 <tr>
										<td>Jangka Waktu</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="jangka_waktu" id="jangka_waktu" autocomplete="off" value="<?php echo $accountcredit['credits_account_period'];?>" readonly/>
										</td>
									 </tr>
									  <tr>
										<td width="35%">Keterlambatan </td>
										<td width="5%">:</td> 
										<td width="60%"><input type="text" name="credits_payment_day_of_delay" id="credits_payment_day_of_delay" value="<?php echo $credits_payment_day_of_delay; ?>" class="easyui-textbox" readonly>
										</td>
									 </tr>
								</table>
							</div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">Bank Transfer</td>
										<td width="5%"> : </td>
										<td width="60%"><?php echo form_dropdown('bank_account_id', $acctbankaccount, set_value('bank_account_id',$data['bank_account_id']),'id="bank_account_id" class="easyui-combobox" style="width:60%"');?></td>
									</tr>
									<tr>
										<td width="35%">Jumlah Denda</td>
										<td width="5%">:</td> 
										<td width="60%">
											<input type="text" name="credits_account_accumulated_fines_view" id="credits_account_accumulated_fines_view" value="<?php echo number_format($credits_account_accumulated_fines, 2); ?>" class="easyui-textbox" readonly>
											<input type="hidden" name="credits_account_accumulated_fines" id="credits_account_accumulated_fines" value="<?php echo $credits_account_accumulated_fines; ?>" class="easyui-textbox" readonly>
										</td>
									 </tr>
									 <tr>
										<td width="35%">Denda Bulan Ini</td>
										<td width="5%">:</td> 
										<td width="60%">
											<input type="text" name="credits_payment_fine_amount_view" id="credits_payment_fine_amount_view" value="<?php echo number_format($credits_payment_fine_amount, 2); ?>" class="easyui-textbox" readonly>
											<input type="hidden" name="credits_payment_fine_amount" id="credits_payment_fine_amount" value="<?php echo $credits_payment_fine_amount; ?>" class="easyui-textbox" readonly>
										</td>
									 </tr>
									<tr>
										<td>Jumlah Pinjaman</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="sisa_pokok_view" id="sisa_pokok_view" value="<?php echo number_format($accountcredit['credits_account_amount']);?>" readonly/>
										</td>
									 </tr>
									 <tr>
										<td>Outstanding</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="sisa_pokok_view" id="sisa_pokok_view" value="<?php echo number_format($accountcredit['credits_account_last_balance']);?>" readonly/>
                                                <input type="hidden" class="easyui-textbox" name="sisa_pokok_awal" id="sisa_pokok_awal" value="<?php echo $accountcredit['credits_account_last_balance'];?>" readonly/>
                                                <input type="hidden" class="easyui-textbox" name="sisa_pokok_akhir" id="sisa_pokok_akhir" value="<?php echo $accountcredit['credits_account_last_balance'];?>" readonly/>

											<input type="hidden" class="easyui-textbox" name="sisa_bunga_awal" id="sisa_bunga_awal" value="<?php echo $accountcredit['credits_account_interest_last_balance'];?>" readonly/> 
										</td>
									 </tr>
									 <tr>
										<td colspan="4"><div style="font-weight: bold">Guna Membayar (Kas Masuk)</div></td>
									 </tr>

									 <tr>
										<td>Angsuran Pokok</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="angsuran_pokok_view" id="angsuran_pokok_view"  value="<?php echo number_format($angsuranpokok, 2); ?>" readonly/>
											<input type="hidden" class="easyui-textbox" name="angsuran_pokok" id="angsuran_pokok" value="<?php echo $angsuranpokok; ?>" />
										</td>
									 </tr>
									 <tr>
										<td>Angsuran Bunga</td>
										<td>:</td> 
										<td>
											<input type="text" class="easyui-textbox" name="angsuran_interest_view" id="angsuran_interest_view"  value="<?php echo number_format($angsuranbunga, 2); ?>" readonly/>
											<input type="hidden" class="easyui-textbox" name="angsuran_interest" id="angsuran_interest" value="<?php echo $angsuranbunga; ?>" />
											
											<input type="hidden" class="easyui-textbox" name="saldo_bunga" id="saldo_bunga" value="<?php echo $saldobunga; ?>" />
											
										</td>
									 </tr>
									  <tr>
										<td>Denda</td>
										<td>:</td> 
										<td>
											<input type="text" class="easyui-textbox" name="credits_payment_fine_view" id="credits_payment_fine_view"  />
											<input type="hidden" class="easyui-textbox" name="credits_payment_fine" id="credits_payment_fine" />
										</td>
									 </tr>
									 <tr>
										<td colspan="4"><div style="font-weight: bold">Penerimaan (Kas Masuk)</div></td>
									 </tr>
									  <tr>
										<td>Simpanan Wajib</td>
										<td>:</td> 
										<td>
											<input type="text" class="easyui-textbox" name="member_mandatory_savings_view" id="member_mandatory_savings_view" />
											<input type="hidden" class="easyui-textbox" name="member_mandatory_savings" id="member_mandatory_savings" />
										</td>
									 </tr>
									 <tr>
										<td>Total</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="angsuran_total_view1" id="angsuran_total_view1" value="<?php echo number_format($accountcredit['credits_account_payment_amount'], 2); ?>" readonly/>
												<input type="hidden" class="easyui-textbox" name="angsuran_total" id="angsuran_total" value="<?php echo $accountcredit['credits_account_payment_amount']; ?>" readonly/>
										</td>
									 </tr>
									
									 <input type="hidden" class="easyui-textbox" name="credits_payment_token" id="credits_payment_token" value="<?php echo $token;?>" readonly/>
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
								<table class="table table-striped table-bordered table-hover table-full-width">
									<thead>
										<tr>
											<th>Ke</th>
											<th>Tgl Angsuran</th>
											<th>Angsuran Pokok</th>
											<th>Angsuran Bunga</th>
											<th>Saldo Pokok</th>
											<th>Saldo Bunga</th>
										</tr>
									</thead>
									<tbody>
										<?php 
										$no=1;
										if(empty($detailpayment)){

										} else {
											foreach ($detailpayment as $key=>$val){ 
	
											echo"
												<tr>
												<td>".$no."</td>
												<td>".tgltoview($val['credits_payment_date'])."</td>
												<td align='right'>".number_format($val['credits_payment_principal'], 2)."</td>
												<td align='right'>".number_format($val['credits_payment_interest'], 2)."</td>
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

<div id="akadlist" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Member List</h4>
      </div>
      <div class="modal-body">
<table id="akadtable">
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
            "url": "<?php echo site_url('bank-payments/akad-list-tunai')?>",
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
