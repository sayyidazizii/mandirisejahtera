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

	var loopfine		= 1;
	var loopsimp 		= 1;
	var loopinterest 	= 1;
	var looppenalty 	= 1;

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
		var penalty_type_id 				= +document.getElementById("penalty_type_id").value;
		var credits_acquittance_principal 	= +document.getElementById("credits_acquittance_principal").value;
		var credits_acquittance_interest 	= +document.getElementById("credits_acquittance_interest").value;
		var credits_acquittance_fine 		= +document.getElementById("credits_acquittance_fine").value;	
		var credits_acquittance_penalty 	= +document.getElementById("credits_acquittance_penalty").value;

		var credits_account_last_balance 	= +document.getElementById("credits_account_last_balance").value;
		var credits_account_interest 		= +document.getElementById("credits_account_interest").value;
		var credits_account_interest_amount = +document.getElementById("credits_account_interest_amount").value;
		var credits_account_payment_amount 	= +document.getElementById("credits_account_payment_amount").value;
		var penalty 						= +document.getElementById("penalty").value;
		var payment_type_id 				= +document.getElementById("payment_type_id").value;
		
		if(credits_acquittance_principal == ''){
			credits_acquittance_principal = 0
		}

		if(credits_acquittance_interest == ''){
			credits_acquittance_interest = 0
		}

		if(credits_acquittance_fine == ''){
			credits_acquittance_fine = 0
		}

		if(penalty_type_id == 0 || penalty_type_id == ''){
			var credits_acquittance_penalty = 0;

		} else if(penalty_type_id == 1){

			var credits_acquittance_penalty = (parseFloat(credits_acquittance_principal) * parseFloat(penalty)) / 100;

		} else if(penalty_type_id == 2){

			if(payment_type_id == 1){

				var credits_acquittance_penalty = parseFloat(credits_account_interest_amount) * parseFloat(penalty) ;

			} else {
				var i;
				var bunga = parseFloat(credits_account_interest) / 100;
				var credits_acquittance_penalty;

				var sisapinjaman = parseFloat(credits_account_last_balance);
				for (i = 1; i <= penalty; i++) { 
				  

				  	var angsuranbunga 		= parseFloat(sisapinjaman) * bunga;
					var angsuranpokok 		= parseFloat(credits_account_payment_amount) - angsuranbunga;
					var sisapokok 			= parseFloat(sisapinjaman) - parseFloat(angsuranpokok);

					sisapinjaman 			= sisapinjaman - angsuranpokok;

					credits_acquittance_penalty = credits_acquittance_penalty + angsuranbunga;
				}
			}
			
		}

		var total 				= parseFloat(credits_acquittance_principal) + parseFloat(credits_acquittance_interest) + parseFloat(credits_acquittance_fine) + parseFloat(credits_acquittance_penalty);
		
		$('#credits_acquittance_penalty_view').textbox('setValue',toRp(credits_acquittance_penalty));
		$('#credits_acquittance_penalty').textbox('setValue',credits_acquittance_penalty);
		$('#credits_acquittance_amount_view').textbox('setValue',toRp(total));
		$('#credits_acquittance_amount').textbox('setValue',total);
	}

	$(document).ready(function(){
		$('#credits_acquittance_principal_view').textbox({
			onChange: function(value){
				// alert(looppokok);
				if(loopsimp == 0){
					loopsimp = 1;
					return;
				}
				
				if(loopsimp ==1){
					loopsimp = 0;
					nilai = (value);
					$('#credits_acquittance_principal').textbox('setValue',value);
					$('#credits_acquittance_principal_view').textbox('setValue',toRp(nilai));
					hitungtotal();
					console.log('nilai');
					console.log(nilai);

				}else{
					loopsimp = 1;
					return;
				}
			}
		});

		$('#credits_acquittance_interest_view').textbox({
			onChange: function(value){
				// alert(looppokok);
				if(loopinterest == 0){
					loopinterest = 1;
					return;
				}
				
				if(loopinterest ==1){
					loopinterest = 0;
					nilai = (value);
					$('#credits_acquittance_interest').textbox('setValue',value);
					$('#credits_acquittance_interest_view').textbox('setValue',toRp(nilai));
					hitungtotal();
					console.log('nilai');
					console.log(nilai);

				}else{
					loopinterest = 1;
					return;
				}
			}
		});

		$('#credits_acquittance_fine_view').textbox({
			onChange: function(value){
				if(loopfine == 0){
					loopfine = 1;
					return;
				}
				if(loopfine ==1){
					loopfine = 0;
					var credits_payment_fine				= $('#credits_acquittance_fine_view').val();	
					$('#credits_acquittance_fine_view').textbox('setValue',toRp(credits_payment_fine));
					$('#credits_acquittance_fine').textbox('setValue',credits_payment_fine);			
					hitungtotal();

				}else{
					loopfine=1;
					return;
				}
				
			}
		});

		$('#penalty').textbox({
			onChange: function(value){
						
					hitungtotal();
			}
		});

		// $('#credits_acquittance_penalty_view').textbox({
		// 	onChange: function(value){
		// 		if(looppenalty == 0){
		// 			looppenalty = 1;
		// 			return;
		// 		}
		// 		if(looppenalty ==1){
		// 			looppenalty = 0;
		// 			var credits_acquittance_penalty				= $('#credits_acquittance_penalty_view').val();	
		// 			$('#credits_acquittance_penalty_view').textbox('setValue',toRp(credits_acquittance_penalty));
		// 			$('#credits_acquittance_penalty').textbox('setValue',credits_acquittance_penalty);			
		// 			hitungtotal();

		// 		}else{
		// 			looppenalty=1;
		// 			return;
		// 		}
				
		// 	}
		// });

		
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
			<a href="<?php echo base_url();?>credits-acquittance/add">
				Tambah Pembayaran Pinjaman - Tunai 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->
<?php echo form_open('credits-acquittance/process-add',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
	$sesi 			= $this->session->userdata('unique');
	$data 			= $this->session->userdata('acctcreditsacquittance-'.$sesi['unique']);
	$token 			= $this->session->userdata('acctcreditsacquittancetoken-'.$unique['unique']);

	$sisaangsuran 	= $accountcredit['credits_account_period'] - $accountcredit['credits_account_payment_to'];

	if($accountcredit['payment_type_id'] == 1){
		$credits_account_interset_last_balance 	= $sisaangsuran * $accountcredit['credits_account_interest_amount'];

	} else if($accountcredit['payment_type_id'] == 2){
		$pinjaman 		= $accountcredit['credits_account_last_balance'];
		$bunga 			= $accountcredit['credits_account_interest'] / 100;
		$totangsuran 	= $accountcredit['credits_account_payment_amount'];

		$sisapinjaman = $pinjaman;
		for ($i=1; $i <= $sisaangsuran ; $i++) {
			$angsuranbunga 		= $sisapinjaman * $bunga;
			$angsuranpokok 		= $totangsuran - $angsuranbunga;
			$sisapokok 			= $sisapinjaman - $angsuranpokok;

			$sisapinjaman 		= $sisapinjaman - $angsuranpokok;

			$credits_account_interset_last_balance 			= $credits_account_interset_last_balance + $angsuranbunga;
		}
	}

?>
<?php
// print_r($credits_account_interset_last_balance);
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
						<a href="<?php echo base_url();?>credits-acquittance" class="btn btn-default btn-sm">
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
										<td width="35%">No. Perjanjian Pinjaman<span class="required">*</span></td>
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
										<td width="35%">Tanggal Pelunasan</td>
										<td width="5%">:</td> 
										<td width="60%"><input type="text" name="tanggal_angsuran" id="tanggal_angsuran" value="<?php echo date('d-m-Y'); ?>" class="easyui-textbox" readonly>
										</td>
									 </tr>
									
									 <tr>
										<td>Total Angsuran</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="credits_payment_to" id="credits_payment_to" autocomplete="off" value="<?php echo $accountcredit['credits_account_payment_to'];?>" readonly/>
										</td>
									 </tr>
									 <tr>
										<td>Jangka Waktu</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="jangka_waktu" id="jangka_waktu" autocomplete="off" value="<?php echo $accountcredit['credits_account_period'];?>" readonly/>
										</td>
									 </tr>
								</table>
							</div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">Sisa Pokok</td>
										<td width="5%">:</td> 
										<td width="60%">
											<input type="text" class="easyui-textbox" name="credits_account_last_balance_view" id="credits_account_last_balance_view" value="<?php echo number_format($accountcredit['credits_account_last_balance']);?>" readonly/>
                                                <input type="hidden" class="easyui-textbox" name="credits_account_last_balance" id="credits_account_last_balance" value="<?php echo $accountcredit['credits_account_last_balance'];?>" readonly/>
											
										</td>
									 </tr>
									 <tr>
										<td width="35%">Sisa Bunga</td>
										<td width="5%">:</td> 
										<td width="60%">
											<input type="text" name="credits_account_interset_last_balance_view" id="credits_account_interset_last_balance_view" value="<?php echo number_format($credits_account_interset_last_balance, 2); ?>" class="easyui-textbox" readonly>
											<input type="hidden" name="credits_account_interset_last_balance" id="credits_account_interset_last_balance" value="<?php echo $credits_account_interset_last_balance; ?>" class="easyui-textbox" readonly>

											<input type="hidden" name="credits_account_interest" id="credits_account_interest" value="<?php echo $accountcredit['credits_account_interest']; ?>" class="easyui-textbox" readonly>

											<input type="hidden" name="credits_account_payment_amount" id="credits_account_payment_amount" value="<?php echo $accountcredit['credits_account_payment_amount']; ?>" class="easyui-textbox" readonly>

											<input type="hidden" name="credits_account_interest_amount" id="credits_account_interest_amount" value="<?php echo $accountcredit['credits_account_interest_amount']; ?>" class="easyui-textbox" readonly>

											<input type="hidden" name="payment_type_id" id="payment_type_id" value="<?php echo $accountcredit['payment_type_id']; ?>" class="easyui-textbox" readonly>
										</td>
									 </tr>
									<tr>
										<td>Akumulasi Sanksi</td>
										<td>:</td> 
										<td><input type="text" name="credits_account_accumulated_fines_view" id="credits_account_accumulated_fines_view" value="<?php echo number_format($credits_account_accumulated_fines, 2); ?>" class="easyui-textbox" readonly>
											<input type="hidden" name="credits_account_accumulated_fines" id="credits_account_accumulated_fines" value="<?php echo $credits_account_accumulated_fines; ?>" class="easyui-textbox" readonly>
										</td>
									 </tr>
									 <tr>
										<td colspan="4"><div style="font-weight: bold">Pelunasan</div></td>
									 </tr>
									 <tr>
										<td>Pelunasan Pokok</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="credits_acquittance_principal_view" id="credits_acquittance_principal_view"  value="<?php echo number_format($accountcredit['credits_account_last_balance'], 2); ?>"/>
											<input type="hidden" class="easyui-textbox" name="credits_acquittance_principal" id="credits_acquittance_principal" value="<?php echo $accountcredit['credits_account_last_balance']; ?>" />
										</td>
									 </tr>
									 <tr>
										<td>Pelunasan Bunga</td>
										<td>:</td> 
										<td>
											<input type="text" class="easyui-textbox" name="credits_acquittance_interest_view" id="credits_acquittance_interest_view" />
											<input type="hidden" class="easyui-textbox" name="credits_acquittance_interest" id="credits_acquittance_interest" />
										</td>
									 </tr>
									  <tr>
										<td>Pelunasan Sanksi</td>
										<td>:</td> 
										<td>
											<input type="text" class="easyui-textbox" name="credits_acquittance_fine_view" id="credits_acquittance_fine_view"  />
											<input type="hidden" class="easyui-textbox" name="credits_acquittance_fine" id="credits_acquittance_fine" />
										</td>
									 </tr>
									  
									 <tr>
										<td>Pinalti (%)</td>
										<td>:</td> 
										<td>
											<?php
												echo form_dropdown('penalty_type_id', $penaltytype,set_value('penalty_type_id'),'id="penalty_type_id" class="easyui-combobox" style="width:52%" ');
											?>
											<input type="text" class="easyui-textbox" name="penalty" id="penalty" style="width:30%" />
										</td>
									 </tr>
									 <tr>
										<td>Jumlah Pinalti</td>
										<td>:</td> 
										<td>
											<input type="text" class="easyui-textbox" name="credits_acquittance_penalty_view" id="credits_acquittance_penalty_view" readonly />
											<input type="hidden" class="easyui-textbox" name="credits_acquittance_penalty" id="credits_acquittance_penalty" />
										</td>
									 </tr>
									 <tr>
										<td>Total Pelunasan</td>
										<td>:</td> 
										<td><input type="text" class="easyui-textbox" name="credits_acquittance_amount_view" id="credits_acquittance_amount_view" value="<?php echo number_format($accountcredit['credits_account_last_balance'], 2); ?>" readonly/>
												<input type="hidden" class="easyui-textbox" name="credits_acquittance_amount" id="credits_acquittance_amount" value="<?php echo  $accountcredit['credits_account_last_balance']; ?>" readonly/>
										</td>
									 </tr>
									
									 <input type="hidden" class="easyui-textbox" name="credits_acquittance_token" id="credits_acquittance_token" value="<?php echo $token;?>" readonly/>
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
<table id="akadtable" style="min-width: 100%;">
	<thead>
    	<tr>
        	<th>No</th>
        	<th>No. Perjanjian Pinjaman</th>
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
            "url": "<?php echo site_url('credits-acquittance/akad-list-tunai')?>",
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
