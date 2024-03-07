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
	base_url = '<?php echo base_url();?>';

	var loop = 1;

	// $(document).ready(function(){
	// 	$('#mutation_id').textbox({
	// 	   collapsible:false,
	// 	   minimizable:false,
	// 	   maximizable:false,
	// 	   closable:false
	// 	});

	// 	$('#mutation_id').textbox('clear').textbox('textbox').focus();
	// });

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
		$('#deposito_profit_sharing_amount_view').textbox({
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
				$('#deposito_profit_sharing_amount').textbox('setValue', value);
				$('#deposito_profit_sharing_amount_view').textbox('setValue', tampil);

				}else{
					loop=1;
					return;
				}
			
			}
		});
	});

</script>
<div class="row-fluid">
	

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
			<a href="<?php echo base_url();?>deposito-profit-sharing-check">
				Daftar Simpanan Berjangka Dapat Bunga
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
Simpanan Berjangka Dapat Bunga
</h3>
<?php	echo form_open('deposito-profit-sharing-check/process-update',array('id' => 'myform', 'class' => ''));  

$date = date('Y-m-d');
$month 	= date('m');
$year	= date('Y');

if($month == 01 || $month == 1){
	$month = 12;
	$year = $year - 1;
} else {
	$month = $month - 1;
	$year = $year;
}

if($month < 10){
	$month = '0'.$month;
}


$deposito_profit_sharing_period = $month.$year;
$deposito_interest_rate 		= $this->AcctDepositoProfitSharingCheck_model->getDepositoInterestRate($acctdepositoprofitsharing['deposito_id']);

$deposito_index_amount 			= ($deposito_interest_rate / 12) / 100;
$deposito_profit_sharing_amount = $deposito_index_amount * $acctdepositoprofitsharing['deposito_account_last_balance'];
// print_r($deposito_index_amount);

$savings_account_id = $this->uri->segment(4);
if(empty($savings_account_id)){
	$savings_account_id = $acctdepositoprofitsharing['savings_account_id'];
}

$acctsavingsaccount		= $this->AcctDepositoProfitSharingCheck_model->getAcctSavingsAccount_Detail($savings_account_id);

$sesi 	= $this->session->userdata('unique');
$token 	= $this->session->userdata('acctdepositoprofitsharingcheck-'.$sesi['unique']);
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Form Tambah
				</div>
				<div class="actions">
					<a href="<?php echo base_url();?>deposito-profit-sharing-check" class="btn btn-default btn-sm">
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
									<td width="35%">No. SimpKa</td>
									<td width="5%">:</td>
									<td width="60%">
										<input type="hidden" name="deposito_profit_sharing_id" id="deposito_profit_sharing_id" value="<?php echo $acctdepositoprofitsharing['deposito_profit_sharing_id']; ?>">
										<input type="text" class="easyui-textbox" style="width: 100%" name="deposito_account_no" id="deposito_account_no" value="<?php echo $acctdepositoprofitsharing['deposito_account_no']; ?>" readonly>
										<input type="hidden" class="easyui-textbox" style="width: 100%" name="deposito_profit_sharing_token" id="deposito_profit_sharing_token" value="<?php echo $token; ?>" readonly>
									</td>
								</tr>
								<tr>
									<td width="35%">No. Seri</td>
									<td width="5%">:</td>
									<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="deposito_account_serial_no" id="deposito_account_serial_no" value="<?php echo $acctdepositoprofitsharing['deposito_account_serial_no']; ?>" readonly></td>
								</tr>
								<tr>
									<td width="35%">Nama</td>
									<td width="5%">:</td>
									<td width="60%">
										<input type="text" class="easyui-textbox" style="width: 100%" name="member_name" id="member_name" value="<?php echo $acctdepositoprofitsharing['member_name']; ?>" readonly></td>
								</tr>
								<tr>
									<td width="35%">Alamat</td>
									<td width="5%">:</td>
									<td width="60%"><textarea rows="2" name="member_address" id="member_address" class="easyui-textarea" style="width: 100%" disabled="disabled"><?php echo $acctdepositoprofitsharing['member_address']; ?></textarea></td>
								</tr>
						<!-- 		<tr>
									<td width="35%">Kabupaten</td>
									<td width="5%">:</td>
									<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="deposito_account_id" id="deposito_account_id" value="<?php echo $acctdepositoprofitsharing['city_name']; ?>"></td>
								</tr>
								<tr>
									<td width="35%">Kecamatan</td>
									<td width="5%">:</td>
									<td width="60%"></td>
								</tr> -->
								<tr>
									<td width="35%">No. Telp</td>
									<td width="5%">:</td>
									<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="member_phone" id="deposito_account_id" value="<?php echo $acctdepositoprofitsharing['member_phone']; ?>" readonly></td>
								</tr>
								<tr>
									<td width="35%">Jangka Waktu</td>
									<td width="5%">:</td>
									<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="deposito_period" id="deposito_period" value="<?php echo $acctdepositoprofitsharing['deposito_account_period']; ?>" readonly></td>
								</tr>
								
							</table>
						</div>
						<div class="col-md-1"></div>						
						<div class="col-md-5">
							<table width="100%">
								<tr>
									<td width="35%">Tanggal Buka</td>
									<td width="5%">:</td>
									<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="deposito_account_date" id="deposito_account_date" value="<?php echo tgltoview($acctdepositoprofitsharing['deposito_account_date']); ?>" readonly></td>
								</tr>
								<tr>
									<td width="35%">Jatuh Tempo</td>
									<td width="5%">:</td>
									<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="deposito_account_due_date" id="deposito_account_due_date" value="<?php echo tgltoview($acctdepositoprofitsharing['deposito_account_due_date']); ?>" readonly></td>
								</tr>
								<tr>
									<td width="35%">Saldo</td>
									<td width="5%">:</td>
									<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="deposito_account_last_balance" id="deposito_account_last_balance" value="<?php echo number_format($acctdepositoprofitsharing['deposito_account_last_balance'], 2); ?>" readonly></td>
								</tr>
								<tr>
									<td width="35%">Bunga</td>
									<td width="5%">:</td>
									<td width="60%">
										<input type="text" class="easyui-textbox" style="width: 100%" name="deposito_profit_sharing_amount_view" id="deposito_profit_sharing_amount_view" value="<?php echo number_format($deposito_profit_sharing_amount, 2); ?>" readonly>

										<input type="hidden" class="easyui-textbox" name="deposito_profit_sharing_amount" id="deposito_profit_sharing_amount" value="<?php echo $deposito_profit_sharing_amount; ?>">
										<input type="hidden"  name="deposito_index_amount" id="deposito_index_amount" value="<?php echo $deposito_index_amount; ?>">
										<input type="hidden" name="deposito_profit_sharing_period" id="deposito_profit_sharing_period" value="<?php echo $deposito_profit_sharing_period; ?>">
										<input type="hidden" name="deposito_id" id="deposito_id" value="<?php echo $acctdepositoprofitsharing['deposito_id']; ?>">
									</td>
								</tr>
								<tr>
									<!-- <td width="35%">Rek Simpanan<span class="required">*</span></td>
									<td width="5%"></td>
									<td width="20%"> -->
											<!-- <input type="text"  class="easyui-textbox" size="4" name="savings_account_no" id="savings_account_no" autocomplete="off" value="<?php echo set_value('savings_account_no', $acctsavingsaccount['savings_account_no']);?>" style="width: 100%" readonly/>
											&nbsp <a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#simpananlist">Cari No. Rek</a> -->

											<input type="hidden"  name="savings_account_id" id="savings_account_id" autocomplete="off" value="<?php echo set_value('savings_account_id', $savings_account_id);?>" readonly/>
											<input type="hidden"   name="savings_id" id="savings_id" autocomplete="off" value="<?php echo set_value('savings_id', $acctsavingsaccount['savings_id']);?>" readonly/>
											<input type="hidden"  name="member_id_savings" id="member_id_savings" autocomplete="off" value="<?php echo $acctsavingsaccount['member_id'];?>" readonly/>
											<input type="hidden"  name="savings_account_last_balance" id="savings_account_last_balance" autocomplete="off" value="<?php echo set_value('savings_account_last_balance', $acctsavingsaccount['savings_account_last_balance']);?>" readonly/>
									</td>
								<tr>
									<td width="35%"></td>
									<td width="5%"></td>
									<td width="60%" align="right">
										<button type="submit" class="btn green-jungle"><i class="fa fa-check"></i> Simpan</button>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
<!-- 
DataTable
!-->
<div id="simpananlist" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Data Simpanan</h4>
      </div>
      <div class="modal-body">
<table id="simpantable">
	<thead>
    	<tr>
        	<th>No</th>
        	<th>No Rekening</th>
        	<th>Anggota</th>
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
<?php 
$deposito_profit_sharing_id = $this->uri->segment(3);
 ?>

<script type="text/javascript">
 
var table;
 
$(document).ready(function() {
	table = $('#simpantable').DataTable({ 
 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "pageLength": 5,
        "order": [], //Initial no order.
        "ajax": {
		
            "url": "<?php echo site_url('deposito-profit-sharing-check/get-savings-account-list/'.$deposito_profit_sharing_id)?>",
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