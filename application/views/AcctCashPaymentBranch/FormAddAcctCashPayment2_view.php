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
  		background-color: f0f8ff;
	}
</style>
<script>
	base_url = '<?php echo base_url();?>';

	var loopjumlah	= 1;
	var looppokok 	= 1;
	var loopmargin 	= 1;

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
		
	function hitungpayment(ganti){
		var jumlah_angsuran 	= +document.getElementById("jumlah_angsuran").value;
		var pokok_angsuran 		= +document.getElementById("angsuran_pokok").value;
		var margin_angsuran 	= +document.getElementById("angsuran_margin").value;
		var harga_pokok 		= +document.getElementById("harga_pokok").value;
		var total_margin 		= +document.getElementById("harga_margin").value;
		var sisa_pokok			= +document.getElementById("sisa_pokok_awal").value;
		// var sisa_margin				= $('#sisa_margin_awal').val();	
		// var sisa_margin_awal = parseFloat(sisa_margin) + parseFloat(margin_angsuran);

		/*alert(pokok_angsuran);*/
		
		if(jumlah_angsuran == ''){
			alert('Isikan Jumlah Angsuran ! ');
			$('#angsuran_pokok_view').textbox('setValue','');
			$('#angsuran_pokok').textbox('setValue','');
			$('#angsuran_margin_view').textbox('setValue','');
			$('#angsuran_margin').textbox('setValue','');
			$('#angsuran_total_view').textbox('setValue','');
			$('#angsuran_total').textbox('setValue','');
		}else{
			if(ganti == 'jumlah'){
				console.log(ganti);
				var total_pembiayaan 	= parseFloat(harga_pokok) + parseFloat(total_margin);

				var angsuran_pokok 		= (((parseFloat(harga_pokok) / parseFloat(total_pembiayaan)) * 100 ) / 100 ) * parseFloat(jumlah_angsuran);
				var angsuran_margin		= parseFloat(jumlah_angsuran) - parseFloat(angsuran_pokok);
				var angsuran_total 		= parseFloat(angsuran_pokok) + parseFloat(angsuran_margin);

				// var sisa_pokok_awal 	= parseFloat(sisa_pokok) + (parseFloat(angsuran_pokok));
				// var saldo_pokok 		= parseFloat(sisa_pokok) - (parseFloat(angsuran_pokok));	

				

				console.log(total_pembiayaan);
				console.log(angsuran_pokok);
				console.log(angsuran_margin);
				console.log(angsuran_total);

				
				$('#angsuran_pokok_view').textbox('setValue',angsuran_pokok);
				$('#angsuran_pokok').textbox('setValue',angsuran_pokok);
				$('#angsuran_margin_view').textbox('setValue',toRp(angsuran_margin));
				$('#angsuran_margin').textbox('setValue',angsuran_margin);
				$('#angsuran_total_view1').textbox('setValue',toRp(angsuran_total));
				$('#angsuran_total').textbox('setValue',angsuran_total);
				// $('#sisa_pokok_view').textbox('setValue',toRp(saldo_pokok));
				// $('#sisa_pokok_akhir').textbox('setValue',saldo_pokok);
			} else 
			if(ganti == 'pokok'){
				var pokok_angsuran2 	= +document.getElementById("angsuran_pokok_view").value;

				var angsuran_pokok 		= parseFloat(pokok_angsuran2);
				var angsuran_margin		= parseFloat(jumlah_angsuran) - parseFloat(angsuran_pokok);
				var angsuran_total 		= parseFloat(angsuran_pokok) + parseFloat(angsuran_margin);
				// var sisa_pokok_awal 	= parseFloat(sisa_pokok) + (parseFloat(angsuran_pokok));
				// var saldo_pokok 		= parseFloat(sisa_pokok) - (parseFloat(angsuran_pokok));	

				console.log(pokok_angsuran2);
				console.log(angsuran_pokok);
				console.log(angsuran_margin);
				console.log(angsuran_total);

				$('#angsuran_pokok_view').textbox('setValue',toRp(angsuran_pokok));
				$('#angsuran_pokok').textbox('setValue',angsuran_pokok);
				$('#angsuran_margin_view').textbox('setValue',toRp(angsuran_margin));
				$('#angsuran_margin').textbox('setValue',angsuran_margin);
				$('#angsuran_total_view1').textbox('setValue',toRp(angsuran_total));
				$('#angsuran_total').textbox('setValue',angsuran_total);
				// $('#sisa_pokok_view').textbox('setValue',saldo_pokok);
				// $('#sisa_pokok_akhir').textbox('setValue',saldo_pokok);

			} else {
				return;
			}
			
			
			
		}
	}


	$(document).ready(function(){
		$('#jumlah_angsuran_view').textbox({
			onChange: function(value){
				if(loopjumlah == 0){
					loopjumlah = 1;
					return;
				}
				if(loopjumlah ==1){
					loopjumlah = 0;
					var jumlah_angsuran_view				= $('#jumlah_angsuran_view').val();	
					$('#jumlah_angsuran_view').textbox('setValue',toRp(jumlah_angsuran_view));
					$('#jumlah_angsuran').textbox('setValue',jumlah_angsuran_view);			
					hitungpayment('jumlah');

				}else{
					loopjumlah=1;
					return;
				}
				
			}
		});

		$('#angsuran_pokok_view').textbox({
			onChange: function(value){
				// alert(looppokok);
				if(looppokok == 0){
					looppokok = 1;
					return;
				}
				
				if(looppokok ==1){
					looppokok = 0;
					nilai = (value);
					$('#angsuran_pokok').textbox('setValue',value);
					$('#angsuran_pokok_view').textbox('setValue',nilai);
					hitungpayment('pokok');
					console.log('nilai');
					console.log(nilai);

				}else{
					looppokok = 1;
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
			<a href="<?php echo base_url();?>cash-payments-branch/add-cash-payment">
				Tambah Pembayaran Pinjaman - Tunai 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->
<?php echo form_open('cash-payments-branch/process-add-cash-payment',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
	$sesi 			= $this->session->userdata('unique');
	$data 			= $this->session->userdata('addacctcashpayment-'.$sesi['unique']);
	$token 			= $this->session->userdata('acctcreditspaymentcashtoken-'.$unique['unique']);

	$saldo_piutang 	= $accountcredit['credits_account_last_balance_principal'] + $accountcredit['credits_account_last_balance_margin'];
	

	if($accountcredit['credits_account_net_price'] == 0){
		$harga_pokok = $accountcredit['credits_account_financing'];
	} else {
		$harga_pokok = $accountcredit['credits_account_net_price'] - $accountcredit['credits_account_um'];
	}

	$credits_payment_date = date('Y-m-d');

	$date1 = date_create($credits_payment_date);
	$date2 = date_create($accountcredit['credits_account_payment_date']);

	$credits_payment_day_of_delay 	= date_diff($date1, $date2)->format('%d');

	$angsuranke 					= $accountcredit['credits_account_payment_to'] + 1;
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
						<a href="<?php echo base_url();?>cash-payments-branch/ind-cash-payment" class="btn btn-default btn-sm">
							<i class="fa fa-angle-left"></i>
							<span class="hidden-480">
								Kembali
							</span>
						</a>
					</div>
				</div>
					<input type="hidden" class="form-control" name="member_id" id="member_id" autocomplete="off" value="<?php echo $accountcredit['member_id'];?>" readonly/> 
					<input type="hidden" class="form-control" name="credits_id" id="credits_id" autocomplete="off" readonly value="<?php echo $accountcredit['credits_id'];?>"/>
					<input type="hidden" class="form-control" name="harga_pokok" id="harga_pokok" autocomplete="off" readonly value="<?php echo $harga_pokok;?>"/>
					<input type="hidden" class="form-control" name="harga_margin" id="harga_margin" autocomplete="off" readonly value="<?php echo $accountcredit['credits_account_margin'];?>"/>
					<div class="portlet-body">
						<div class="form-body">
						<div class="row">
							<div class="col-md-1">	
							</div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">No. Akad<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text"  class="easyui-textbox" size="4" name="credits_account_serial" id="credits_account_serial" autocomplete="off" value="<?php echo $accountcredit['credits_account_serial'];?>" style="width: 50%" readonly/> &nbsp<a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#akadlist">Search</a>
											<input type="hidden"  class="easyui-textbox" name="credits_account_id" id="credits_account_id" autocomplete="off" value="<?php echo $accountcredit['credits_account_id'];?>" readonly/>
											<input type="hidden"  class="easyui-textbox" name="branch_asal_id" id="branch_asal_id" autocomplete="off" value="<?php echo $accountcredit['branch_id'];?>" readonly/>
									</td>
									</tr>
									 <tr>
										<td>Pembiayaan</td>
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
										<td>Identitas</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="member_identity" id="member_identity_no" value="<?php echo $accountcredit['member_identity'];?>" autocomplete="off" readonly/>
										</td>
									 </tr>
									 <tr>
										<td>Jumlah Angsuran</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="jumlah_angsuran_view" id="jumlah_angsuran_view"  value="<?php echo number_format($accountcredit['credits_account_payment_amount'], 2); ?>"/>
											<input type="hidden" class="easyui-textbox" name="jumlah_angsuran" id="jumlah_angsuran" value="<?php echo $accountcredit['credits_account_payment_amount']; ?>" />
										</td>
									 </tr>
									 <tr>
										<td>Angsuran Pokok</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="angsuran_pokok_view" id="angsuran_pokok_view"  value="<?php echo number_format($accountcredit['credits_account_principal_amount'], 2); ?>" />
											<input type="hidden" class="easyui-textbox" name="angsuran_pokok" id="angsuran_pokok" value="<?php echo $accountcredit['credits_account_principal_amount']; ?>" />
										</td>
									 </tr>
									 <tr>
										<td>Angsuran Margin</td>
										<td>:</td> 
										<td>
											<input type="text" class="easyui-textbox" name="angsuran_margin_view" id="angsuran_margin_view"  value="<?php echo number_format($accountcredit['credits_account_margin_amount'], 2); ?>" readonly/>
											<input type="hidden" class="easyui-textbox" name="angsuran_margin" id="angsuran_margin" value="<?php echo $accountcredit['credits_account_margin_amount']; ?>" />
										</td>
									 </tr>
									 <tr>
										<td>Total</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="angsuran_total_view1" id="angsuran_total_view1" value="<?php echo number_format($accountcredit['credits_account_payment_amount'], 2); ?>" readonly/>
												<input type="hidden" class="easyui-textbox" name="angsuran_total" id="angsuran_total" value="<?php echo $accountcredit['credits_account_payment_amount']; ?>" readonly/>
										</td>
									 </tr>
								</table>
							</div>
							<div class="col-md-5">
								<table width="100%">
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
									 </tr>
									 <tr>
										<td width="35%">Keterlambatan (Hari)</td>
										<td width="5%">:</td> 
										<td width="60%"><input type="text" name="credits_payment_day_of_delay" id="credits_payment_day_of_delay" value="<?php echo $credits_payment_day_of_delay; ?>" class="easyui-textbox" readonly>
										</td>
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
										<td>SISA POKOK</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="sisa_pokok_view" id="sisa_pokok_view" value="<?php echo number_format($accountcredit['credits_account_last_balance_principal']);?>" readonly/>
                                                <input type="hidden" class="easyui-textbox" name="sisa_pokok_awal" id="sisa_pokok_awal" value="<?php echo $accountcredit['credits_account_last_balance_principal'];?>" readonly/>
                                                <input type="hidden" class="easyui-textbox" name="sisa_pokok_akhir" id="sisa_pokok_akhir" value="<?php echo $accountcredit['credits_account_last_balance_principal'];?>" readonly/>
										</td>
									 </tr>
									 <tr>
										<td>SISA MARGIN</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="sisa_margin_view" id="sisa_margin_view"  value="<?php echo number_format($accountcredit['credits_account_last_balance_margin']);?>" readonly/>
                                                <input type="hidden" class="easyui-textbox" name="sisa_margin_awal" id="sisa_margin_awal" value="<?php echo $accountcredit['credits_account_last_balance_margin'];?>" readonly/>
                                                <input type="hidden" class="easyui-textbox" name="sisa_margin_akhir" id="sisa_margin_akhir" value="<?php echo $accountcredit['credits_account_last_balance_margin'];?>" readonly/>
										</td>
									 </tr>
									 <tr>
										<td>Saldo Piutang</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="saldo_piutang_view" id="saldo_piutang_view"  value="<?php echo number_format($saldo_piutang, 2);?>" readonly/>
										</td>
									 </tr>
									 <tr>
										<td>Angsuran Per Bulan</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="angsuran_per_bulan" id="angsuran_per_bulan" value="<?php echo number_format($accountcredit['credits_account_payment_amount'], 2);?>"  readonly/>
										</td>
									 </tr>
									 <tr>
										<td>Total</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="angsuran_total_view" id="angsuran_total_view"  value="<?php echo number_format($accountcredit['credits_account_payment_amount']);?>" readonly/>
												<input type="hidden" class="easyui-textbox" name="angsuran_total" id="angsuran_total" value="<?php echo $accountcredit['credits_account_payment_amount'];?>" readonly/>
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
								<table class="table table-striped table-bordered table-hover table-full-width">
									<thead>
										<tr>
											<th>Ke</th>
											<th>Tgl Angsuran</th>
											<th>Angsuran Pokok</th>
											<th>Angsuran Margin</th>
											<th>Saldo Pokok</th>
											<th>Saldo Margin</th>
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
												<td align='right'>".number_format($val['credits_payment_margin'], 2)."</td>
												<td align='right'>".number_format($val['credits_principal_last_balance'], 2)."</td>
												<td align='right'>".number_format($val['credits_margin_last_balance'], 2)."</td>
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
        	<th>No. Akad</th>
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
            "url": "<?php echo site_url('cash-payments-branch/akad-list-tunai')?>",
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
