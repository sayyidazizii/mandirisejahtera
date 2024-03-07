<?php error_reporting(0);?>
<script src="<?php echo base_url();?>assets/global/scripts/moment.js" type="text/javascript"></script>
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



	function toRp(number) {
		var number = number.toString(), 
		rupiah = number.split('.')[0], 
		cents = (number.split('.')[1] || '') +'00';
		rupiah = rupiah.split('').reverse().join('')
			.replace(/(\d{3}(?!$))/g, '$1,')
			.split('').reverse().join('');
		return rupiah + '.' + cents.slice(0, 2);
	}

	function formatDate(date) {
	    var d = new Date(date),
	        month = '' + (d.getMonth() + 1),
	        day = '' + d.getDate(),
	        year = d.getFullYear();

	    if (month.length < 2) month = '0' + month;
	    if (day.length < 2) day = '0' + day;

	    return [year, month, day].join('-');
	}

	function duedatecalc(data){
		var angsuran = document.getElementById("credits_payment_period").value;
		var date2 	= document.getElementById("credits_payment_date_old").value;

		console.log(date2);
		var day2 	= date2.substring(0, 2);
		var month2 	= date2.substring(3, 5);
		var year2 	= date2.substring(6, 10);
		var date 	= year2 + '-' + month2 + '-' + day2;
		var date1	= new Date(date);
		var period 	= document.getElementById("credits_grace_period").value;

		if(angsuran == 1){
			var a 		= moment(date1); 
			var b 		= a.add(period, 'month'); 
			
			var tmp 	= date1.setMonth(date1.getMonth() + period);
			var endDate = new Date(tmp);
			var name 	= 'credits_payment_date_new';
			var value 	= b.format('DD-MM-YYYY');

			
			var testDate 	= new Date(date);
			var tmp2 		= testDate.setMonth(testDate.getMonth() + 1);
			var date_first 	= testDate.toISOString();
			var day2 		= date_first.substring(8, 10);
			var month2 		= date_first.substring(5, 7);
			var year2 		= date_first.substring(0, 4); 
			var first 		= day2 + '-' + month2 + '-' + year2;

			// console.log(first);
			/*alert(date2);
			alert(day2);
			alert(month2);
			alert(year2);
			alert(b);
			alert(date1);
			alert(endDate);*/
			$('#credits_payment_date_new').textbox('setValue',b.format('DD-MM-YYYY'));

		} else {
			var week 		= period * 7;
			var testDate 	= new Date(date1);
			var tmp 		= testDate.setDate(testDate.getDate() + week);
			var date_tmp 	= testDate.toISOString();
			var day 		= date_tmp.substring(8, 10);
			var month 		= date_tmp.substring(5, 7);
			var year 		= date_tmp.substring(0, 4);

			var testDate2 	= new Date(date1);
			var tmp2 		= testDate2.setDate(testDate2.getDate() + 7);
			var date_first 	= testDate2.toISOString();
			var day2 		= date_first.substring(8, 10);
			var month2 		= date_first.substring(5, 7);
			var year2 		= date_first.substring(0, 4); 
			var first 		= day2 + '-' + month2 + '-' + year2;


		

			$('#credits_payment_date_new').textbox('setValue',value);
		}
		
		
	}

	$(document).ready(function(){	

		$('#credits_grace_period').textbox({
			onChange: function(value){

				duedatecalc(this);
			}
		});
	});
	

	$(document).ready(function(){
        $("#Save").click(function(){
        	var credits_grace_period 	= document.getElementById('credits_grace_period').value
			

			if(credits_grace_period == ''){
				alert("Periode Penundaan Angsuran masih kosong !");
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
<?php echo form_open('credits-payment-suspend/process-add',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
	$sesi 			= $this->session->userdata('unique');
	$data 			= $this->session->userdata('addacctcashpayment-'.$sesi['unique']);


	$angsuranke 						= $accountcredit['credits_account_payment_to'] + 1;
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
						<a href="<?php echo base_url();?>AcctCreditsPaymentSuspend" class="btn btn-default btn-sm">
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
										<td><input type="text" class="easyui-textbox" name="jatuh_tempo" id="jatuh_tempo" autocomplete="off" value="<?php echo tgltoview($accountcredit['credits_account_due_date']);?>" readonly/>
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
								</table>
							</div>
							<div class="col-md-5">
								<table width="100%">
									<h4>Penundaan Angsuran</h4>
									<tr>
										<td width="35%">Tanggal Angsuran Lama</td>
										<td width="5%">:</td> 
										<td width="60%"><input type="text" name="credits_payment_date_old" id="credits_payment_date_old" value="<?php echo tgltoview($accountcredit['credits_account_payment_date']); ?>" class="easyui-textbox" readonly>
										</td>
									 </tr>

									 <tr>
										<td width="35%">Angsuran</td>
										<td width="5%">:</td> 
										<td width="60%"><input type="text" name="credits_payment_period_view" id="credits_payment_period_view" value="<?php echo $creditspaymentperiod[$accountcredit['credits_payment_period']]; ?>" class="easyui-textbox" readonly>
										</td>
									 </tr>

									<tr>
										<td width="35%">Periode Penundaan</td>
										<td width="5%">:</td> 
										<td width="60%">
											<input type="text" name="credits_grace_period" id="credits_grace_period" class="easyui-textbox" >
										</td>
									 </tr>
									 <tr>
										<td width="35%">Tanggal Angsuran Baru</td>
										<td width="5%">:</td> 
										<td width="60%">
											<input type="text" name="credits_payment_date_new" id="credits_payment_date_new" class="easyui-textbox" readonly>
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
            "url": "<?php echo site_url('cash-payments/akad-list-tunai')?>",
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
