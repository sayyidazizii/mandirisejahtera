<script src="<?php echo base_url();?>assets/global/scripts/moment.js" type="text/javascript"></script>
<style>
	th, td {
	  padding: 3px;
	  font-size: 13px;
	}
	input:focus { 
	  background-color: 42f483;
	}
	.custom{

		margin: 0px; padding-top: 0px; padding-bottom: 0px; height: 50px; line-height: 50px; width: 50px;

	}
	.textbox .textbox-text{
		font-size: 13px;


	}
input:read-only {
		background-color: f0f8ff;
	}
</style>


<script>
	var loopp=1;
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

	function popuplink(url) {
		window.open(url, "", "width=800,height=600");
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
		var angsuran = document.getElementById("payment_period").value;
		var date2 	= document.getElementById("credits_account_date_new").value;
		var day2 	= date2.substring(0, 2);
		var month2 	= date2.substring(3, 5);
		var year2 	= date2.substring(6, 10);
		var date 	= year2 + '-' + month2 + '-' + day2;
		var date1	= new Date(date);
		var period 	= document.getElementById("new_credits_account_period").value;

		if(angsuran == 1){
			var a 		= moment(date1); 
			var b 		= a.add(period, 'month'); 
			
			var tmp 	= date1.setMonth(date1.getMonth() + period);
			var endDate = new Date(tmp);
			var name 	= 'credit_account_due_date';
			var value 	= b.format('DD-MM-YYYY');

			

			$('#new_due_date').textbox('setValue',b.format('DD-MM-YYYY'));
		} else {
			var week 		= period * 7;
			var testDate 	= new Date(date1);
			var tmp 		= testDate.setDate(testDate.getDate() + week);
			var date_tmp 	= testDate.toISOString();
			var day 		= date_tmp.substring(8, 10);
			var month 		= date_tmp.substring(5, 7);
			var year 		= date_tmp.substring(0, 4); 
			var name 		= 'credit_account_due_date';
			var value 		= day + '-' + month + '-' + year;

		

			$('#new_due_date').textbox('setValue',value);
		}
		
		
	}

	// function duedatecalc(data){
			
	// 		var date2 	= $("#credits_account_date_new").val();
	// 		var day2 	= date2.substring(0, 2);
	// 		var month2 	= date2.substring(3, 5);
	// 		var year2 	= date2.substring(6, 10);
	// 		var date 	= year2 + '-' + month2 + '-' + day2;
	// 		var date1	= new Date(date);
	// 		var period 	= $("#new_credits_account_period").val();
	// 		var a 		= moment(date1); 
	// 		var b 		= a.add(period, 'month'); 
			
	// 		var tmp 	= date1.setMonth(date1.getMonth() + period);
	// 		var endDate = new Date(tmp);
	// 		$('#new_due_date').textbox('setValue',b.format('DD-MM-YYYY'));
			
	// 	}

	function angsurancalc(){
		var harga_net 		= +document.getElementById("new_credits_account_last_balance").value;
		var bunga 			= +document.getElementById("new_credits_account_interest").value;
		var jangka 			= +document.getElementById("new_credits_account_period").value;
		var type_angsuran 	= +document.getElementById("payment_type_id").value;

		

		var persbunga 		= bunga / 100;

		console.log(persbunga);

		if(type_angsuran == 2){
			var bungaA = Math.pow((1 + parseFloat(persbunga)), jangka);
			var bungaB = Math.pow((1 + parseFloat(persbunga)), jangka) - 1;

			if(bungaB == 0){
				var bungaC = 0;
			} else {
				var bungaC = bungaA / bungaB;
			}
			
			var totalangsuran 	= harga_net * persbunga * bungaC ;
			var angsuranbunga2 	= (harga_net * bunga) / 100;
			var angsuranpokok 	= totalangsuran - angsuranbunga2;

			// console.log('bunga A' + bungaA);
			// console.log('angsuranpokok ' + angsuranpokok);
			// console.log('totalangsuran ' + totalangsuran);
			// console.log('bungaA ' + bungaA);
			// console.log('bungaB ' + bungaB);
			// console.log('bungaC ' + bungaC);
		} else {
			var angsuranpokok 	= harga_net / jangka;
			var angsuranbunga 	= (harga_net * bunga) / 100;
			var totalangsuran 	= angsuranpokok + angsuranbunga;

			var angsuranbunga2 	= totalangsuran - angsuranpokok;
			// alert(uangmuka);
		}

		


		$('#new_credits_account_payment_amount').textbox('setValue',totalangsuran);
		$('#new_credits_account_principal_amount').textbox('setValue',angsuranpokok);
		$('#new_credits_account_margin_amount').textbox('setValue',angsuranbunga2);

		$('#new_credits_account_payment_amount_view').textbox('setValue',toRp(totalangsuran));
		$('#new_credits_account_principal_amount_view').textbox('setValue',toRp(angsuranpokok));
		$('#new_credits_account_margin_amount_view').textbox('setValue',toRp(angsuranbunga2));


	}

$(document).ready(function(){
	$('#new_credits_account_period').textbox({
		onChange: function(value){
			duedatecalc(this);
			angsurancalc();
		}
	});
	$('#new_credits_account_interest').textbox({
		onChange: function(value){
			
			$('#new_credits_account_interest').textbox('setValue',value);
			
			angsurancalc();		
		}
	});
	$('#new_credits_account_last_balance_view').textbox({
		onChange: function(value){
			var credits_account_last_balance_old = $("#credits_account_last_balance_old").val();

			console.log(credits_account_last_balance_old);

			if(value >  credits_account_last_balance_old){
				alert("Plafon tidak boleh lebih dari sisa saldo pokok");

				$('#new_credits_account_last_balance_view').textbox('setValue',credits_account_last_balance_old);
				return false;
			} else {

				if(loopp == 0){
					loopp= 1;
					return;
				}
				if(loopp ==1){
					loopp =0;
					var tampil = toRp(value);
					$('#new_credits_account_last_balance').textbox('setValue',value);
					$('#new_credits_account_last_balance_view').textbox('setValue',tampil);

					angsurancalc();
				
				}else{
					loopp=1;
					return;
				}	
			}	
		}
	});
});

$(document).ready(function(){
	$("#Save").click(function(){
		var credits_account_id 					= $("#credits_account_id").val();
		var new_credits_account_last_balance 	= $("#new_credits_account_last_balance").val();
		var new_credits_account_period 			= $("#new_credits_account_period").val();

		
		if(credits_account_id == ''){
			alert("Data pinjaman masih kosong");
			return false;
		}else if(new_credits_account_last_balance == ''){
			alert("Data Pokok Pinjaman masih kosong");
			return false;
		}else if(new_credits_account_period == ''){
			alert("Data Jangka Waktu masih kosong");
			return false;
		}
	});
});



</script>
<?php echo form_open('AcctRescheduleCredit/addcreditaccount',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
	$sesi 	= $this->session->userdata('unique');
	$data 	= $this->session->userdata('addcreditaccount-'.$sesi['unique']);


?>

		<!-- BEGIN PAGE TITLE & BREADCRUMB-->

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
					Input Data Baru Pinjaman
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>" class="btn btn-default btn-sm">
							<i class="fa fa-angle-left"></i>
							<span class="hidden-480">
								Kembali
							</span>
						</a>
					</div>
				</div>
				<div class="portlet-body">
				
					<div class="col-md-5">
							<input type="hidden" class="form-control" name="credits_account_id" id="credits_account_id" value="<?php echo set_value('credits_account_id', $credit_account['credits_account_id']);?>" readonly/>
							<input type="hidden" class="form-control" name="credits_account_payment_to_old" id="credits_account_payment_to_old" value="<?php echo $credit_account['credits_account_payment_to'];?>" readonly/>
						<table style="width: 100%;" border="0" >
							<tbody>
								<tr>
									<td width="35%">No. Rek</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" readonly class="easyui-textbox" name="credits_account_serial" id="credits_account_serial" value="<?php echo set_value('credits_account_serial', $credit_account['credits_account_serial']);?>" style="width: 60%" readonly/> <a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#creditlist">Cari</a> </td>
								</tr>
								<tr>
									<td width="35%">Pinjaman</td>
									<td width="5%">:</td>
									<td width="60%"><input type="text" class="easyui-textbox" name="source_fund_id" id="source_fund_id" value="<?php echo $credit_account['credits_name'];?>" style="width: 100%" readonly/></td>
								</tr>
								<tr>
									<td width="35%">Nama</td>
									<td width="5%">:</td>
									<td width="60%"><input type="text"  class="easyui-textbox" name="member_name" id="member_name" value="<?php echo set_value('member_name', $credit_account['member_name']);?>" style="width: 70%" readonly/></td>
								</tr>

								<tr>
									<td width="35%">Alamat</td>
									<td width="5%">:</td>
									<td width="60%"><?php echo form_textarea(array('rows'=>'2','name'=>'member_address','class'=>'easyui-textarea','id'=>'member_address','readonly'=>'readonly','value'=>$credit_account['member_address']))?></td>
								</tr>
								<tr>
									<td width="35%">Kota</td>
									<td width="5%">:</td>
									<td width="60%"><input type="text" readonly class="easyui-textbox" name="city_name" id="city_name" value="<?php echo set_value('city_name', $credit_account['city_name']);?>" style="width: 100%" readonly/></td>
								</tr>
								<tr>
									<td width="35%">Nama Ibu</td>
									<td width="5%">:</td>
									<td width="60%"><input type="text" readonly class="easyui-textbox" name="identity_name" id="identity_name" value="<?php echo $credit_account['member_mother'] ?>" style="width: 100%" readonly/></td>
								</tr>
								<tr>
									<td width="35%">No. Identitas</td>
									<td width="5%">:</td>
									<td width="60%"><input type="text" readonly class="easyui-textbox" name="member_identity_no" id="member_identity_no" value="<?php echo set_value('member_identity_no', $credit_account['member_identity_no']);?>" style="width: 100%" readonly/></td>
								</tr>
							</tbody>
						</table>
						</div>
							<div class="col-md-1"></div>
							<div class="col-md-5">
							<table width="100%">
								<tr>
									<td width="35%">Tanggal System</td>
									<td width="5%">:</td>
									<td width="60%">
										<input type="text" readonly class="easyui-textbox" name="date" id="date" value="<?php echo date('d-m-Y');?>" style="width: 100%" readonly="readonly" />
										<input type="hidden" readonly name="credits_account_date_new" id="credits_account_date_new" value="<?php echo date('d-m-Y');?>" />
										<input type="hidden" readonly name="credits_account_date_old" id="credits_account_date_old" value="<?php echo $credit_account['credits_account_date'];?>"/>
									</td>
								</tr>
								<tr>
									<td width="35%">Jangka Waktu</td>
									<td width="5%">:</td>
									<td width="60%">
										<input type="text" readonly class="easyui-textbox" name="period" id="period" value="<?php echo $credit_account['credits_account_period'];?>" style="width: 100%" readonly="readonly" />
										<input type="hidden" readonly name="credits_account_period_old" id="credits_account_period_old" value="<?php echo $credit_account['credits_account_period'];?>"/>
										<input type="hidden" readonly name="payment_period" id="payment_period" value="<?php echo $credit_account['credits_payment_period'];?>"/>
									</td>
								</tr>
								<tr>
									<td width="35%">SLD POKOK</td>
									<td width="5%">:</td>
									<td width="60%">
										<input type="text" readonly class="easyui-textbox" name="credits_account_last_balance_old_view" id="credits_account_last_balance_old_view" value="<?php echo number_format($credit_account['credits_account_last_balance'], 2);?>" style="width: 100%" />

										<input type="hidden" class="easyui-textbox" name="credits_account_last_balance_old" id="credits_account_last_balance_old" value="<?php echo $credit_account['credits_account_last_balance'];?>"/>
									</td>
								</tr>
								<tr>
									<td width="35%">Akumulasi Denda</td>
									<td width="5%">:</td>
									<td width="60%">
										<input type="text" readonly class="easyui-textbox" name="credits_account_accumulated_fines_view" id="credits_account_accumulated_fines_view" value="<?php echo number_format($credit_account['credits_account_accumulated_fines'], 2);?>" style="width: 100%" />

										<input type="hidden" class="easyui-textbox" name="credits_account_accumulated_fines" id="credits_account_accumulated_fines" value="<?php echo $credit_account['credits_account_accumulated_fines'];?>"/>
									</td>
								</tr>
								<tr>
									<td width="35%">Plafon</td>
									<td width="5%">:</td>
									<td width="60%">
									<input type="text" class="easyui-textbox" name="new_credits_account_last_balance_view" id="new_credits_account_last_balance_view" value="<?php echo set_value('new_credits_account_last_balance', number_format($credit_account['credits_account_last_balance']));?>" style="width: 100%" />
									<input type="hidden" class="easyui-textbox" name="new_credits_account_last_balance" id="new_credits_account_last_balance" value="<?php echo set_value('new_credits_account_last_balance', $credit_account['credits_account_last_balance']);?>" />
									</td>
								</tr>
								<tr>
									<td width="35%">Jk. Waktu</td>
									<td width="5%">:</td>
									<td width="60%">
										<input type="text" class="easyui-textbox" name="new_credits_account_period" id="new_credits_account_period" autocomplete="off" value="" style="width: 30%"  />		

										<input type="text" class="easyui-textbox" name="new_due_date" id="new_due_date" value="" onChange="function_elements_add(this.name, this.value);" style="width: 65%" readonly="" />								
										<input type="hidden"  name="payment_type_id" id="payment_type_id" autocomplete="off" value="<?php echo set_value('payment_type_id', $credit_account['payment_type_id']);?>" />

										
										<input type="hidden" class="easyui-textbox" name="credits_account_due_date_old" id="credits_account_due_date_old" value="<?php echo $credit_account['credits_account_due_date']; ?>"/>
									</td>
									
								</tr>
								<tr>
									<td width="35%">Bunga Baru</td>
									<td width="5%">:</td>
									<td width="60%">
									<input type="text" class="easyui-textbox" name="new_credits_account_interest" id="new_credits_account_interest" value="" style="width: 100%"/>

									<input type="hidden" class="easyui-textbox" name="credits_account_interest_old" id="credits_account_interest_old" value="<?php echo set_value('credits_account_interest', $credit_account['credits_account_interest']);?>" />
									</td>
								</tr>
								<!-- <tr>
									<td width="35%">Jatuh Tempo</td>
									<td width="5%">:</td>
									<td width="60%">
										<input type="text" class="easyui-textbox" name="new_due_date" id="new_due_date" value="" onChange="function_elements_add(this.name, this.value);" style="width: 100%" />
										<input type="hidden" class="easyui-textbox" name="credits_account_due_date_old" id="credits_account_due_date_old" value="<?php echo $credit_account['credits_account_due_date']; ?>"/>
									</td>
								</tr> -->
								<tr>
									<td width="35%">Angsuran Pokok</td>
									<td width="5%">:</td>
									<td width="60%">
									<input type="text" class="easyui-textbox" name="new_credits_account_principal_amount_view" id="new_credits_account_principal_amount_view" readonly />
									<input type="hidden" class="easyui-textbox" name="new_credits_account_principal_amount" id="new_credits_account_principal_amount" />
									</td>
								</tr>
								<tr>
									<td width="35%">Angsuran Bunga</td>
									<td width="5%">:</td>
									<td width="60%">
									<input type="text" class="easyui-textbox" name="new_credits_account_margin_amount_view" id="new_credits_account_margin_amount_view" value="" style="width: 100%" readonly/>
									<input type="hidden" class="easyui-textbox" name="new_credits_account_margin_amount" id="new_credits_account_margin_amount" value="" />
									</td>
								</tr>
								<tr>
									<td width="35%">Jumlah Angsuran</td>
									<td width="5%">:</td>
									<td width="60%">
									<input type="text" class="easyui-textbox" name="new_credits_account_payment_amount_view" id="new_credits_account_payment_amount_view" style="width: 100%" readonly/>
									<input type="hidden" class="easyui-textbox" name="new_credits_account_payment_amount" id="new_credits_account_payment_amount" />
									</td>
								</tr>
							
							
							</table>
						</div>
						<div class="col-md-1"></div>
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
<div id="creditlist" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">List Pinjaman</h4>
      </div>
      <div class="modal-body">
<table id="myDataTable">
	<thead>
    	<tr>
        	<th>No. Akad</th>
        	<th>Nama</th>
            <th>Alamat</th>
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
    table = $('#myDataTable').DataTable({ 
 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "pageLength": 5,
        "order": [], //Initial no order.
        "ajax": {
            "url": "<?php echo site_url('AcctRescheduleCredit/akadlist')?>",
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
<script type="text/javascript">
        function myformatter(date){
            var y = date.getFullYear();
            var m = date.getMonth()+1;
            var d = date.getDate();
            return y+'-'+(m<10?('0'+m):m)+'-'+(d<10?('0'+d):d);
        }
        function myparser(s){
            if (!s) return new Date();
            var ss = (s.split('-'));
            var y = parseInt(ss[0],10);
            var m = parseInt(ss[1],10);
            var d = parseInt(ss[2],10);
            if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
                return new Date(y,m-1,d);
            } else {
                return new Date();
            }
        }
    </script>

<?php echo form_close(); ?>
