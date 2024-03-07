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

	$(document).ready(function(){
		$('#member_mandatory_savings_view').textbox({
		   collapsible:false,
		   minimizable:false,
		   maximizable:false,
		   closable:false
		});

		$('#member_mandatory_savings_view').textbox('clear').textbox('textbox').focus();
	});

	function reset_data(){
		document.location = base_url+"member-transfer-mutation/reset";
	}

	function toRp(number) {
		var number = number.toString(), 
		rupiah = number.split('.')[0], 
		cents = (number.split('.')[1] || '') +'00';
		rupiah = rupiah.split('').reverse().join('')
			.replace(/(\d{3}(?!$))/g, '$1,')
			.split('').reverse().join('');
		return rupiah + '.' + cents.slice(0, 2);
	}

	function calculateSavings(){
		var savings_transfer_mutation_amount			= $('#savings_transfer_mutation_amount').val();
		var savings_account_from_opening_balance		= $('#savings_account_from_opening_balance').val();	
		var savings_account_to_opening_balance			= $('#savings_account_to_opening_balance').val();		

		var savings_account_to_last_balance;
		var savings_account_from_last_balance;

		/*console.log(mutation_function);
		console.log(savings_cash_mutation_opening_balance);
		console.log(savings_cash_mutation_amount);*/

		savings_account_from_last_balance = parseFloat(savings_account_from_opening_balance) - parseFloat(savings_transfer_mutation_amount);
		savings_account_to_last_balance = parseFloat(savings_account_to_opening_balance) + parseFloat(savings_transfer_mutation_amount);
	

		$('#savings_account_to_last_balance').textbox('setValue', savings_account_to_last_balance);
		$('#savings_account_from_last_balance').textbox('setValue', savings_account_from_last_balance);

	}

	$(document).ready(function(){
		$('#member_mandatory_savings_view').textbox({
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
				$('#member_mandatory_savings').textbox('setValue', value);
				$('#member_mandatory_savings_view').textbox('setValue', tampil);

				// calculateSavings();

				}else{
					loop=1;
					return;
				}
			
			}
		});
	});
</script>
<?php echo form_open('member-transfer-mutation/process-add',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
	$sesi 	= $this->session->userdata('unique');
	$data 	= $this->session->userdata('addacctsavings-'.$sesi['unique']);


	$token 	= $this->session->userdata('membertransfermutationtoken-'.$sesi['unique']);
?>

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
			<a href="<?php echo base_url();?>member-transfer-mutation">
				Daftar Mutasi Debit Simpanan Wajib
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>member-transfer-mutation/add">
				Tambah Mutasi Debit Simpanan Wajib
			</a>
		</li>
	</ul>
</div>
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
						Form Transfer
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>member-transfer-mutation" class="btn btn-default btn-sm">
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
										<td width="35%">Tanggal Transfer</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width: 70%" name="member_transfer_mutation_date" id="member_transfer_mutation_date" value="<?php echo date('d-m-Y'); ?>" readonly>
										</td>
									</tr>
									<tr>
										<td colspan="3" align="left"><b>Data Anggota</b></td>
									</tr>
									<tr>
										<td width="35%">No. Anggota</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="hidden" name="member_id" id="member_id" value="<?php echo $coremember['member_id']; ?>">

											<input type="text" class="easyui-textbox" style="width: 60%" name="member_no" id="member_no" value="<?php echo $coremember['member_no']; ?>" readonly>

											<a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#memberlistfrom">Cari Anggota</a>
										</td>
									</tr>
									<tr>
										<td width="35%">Nama Anggota</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width: 100%" name="member_name" id="member_name" value="<?php echo $coremember['member_name']; ?>" readonly>
										</td>
									</tr>
									<tr>
										<td width="35%">Alamat</td>
										<td width="5%">:</td>
										<td width="60%">
											<textarea rows="2" name="member_address" id="member_address" class="easyui-textarea" style="width: 100%" disabled="disabled"><?php echo $coremember['member_address']; ?></textarea>
										</td>
									</tr>
									<tr>
										<td width="35%">Kabupaten</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width: 100%" name="savings_account_from_no" id="savings_account_from_no" value="<?php echo $coremember['city_name']; ?>" readonly>
										</td>
									</tr>
									<tr>
										<td width="35%">Kecamatan</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width: 100%" name="savings_account_from_no" id="savings_account_from_no" value="<?php echo $coremember['kecamatan_name']; ?>" readonly>
										</td>
									</tr>
									<tr>
										<td width="35%">Saldo Simpanan Wajib</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width: 100%" name="member_mandatory_savings_last_balance_view" id="member_mandatory_savings_last_balance_view" value="<?php echo number_format($coremember['member_mandatory_savings_last_balance'], 2); ?>" readonly>

											<input type="hidden" class="easyui-textbox" name="member_mandatory_savings_last_balance" id="member_mandatory_savings_last_balance" value="<?php echo $coremember['member_mandatory_savings_last_balance']; ?>" readonly>
										</td>
									</tr>
								</table>
							</div>
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td colspan="3" align="left"><b>Data Simpanan</b></td>
									</tr>
									<tr>
										<td width="35%">No. Rekening</td>
										<td width="5%">:</td>
										<td width="60%">
											
											<input type="text" class="easyui-textbox" style="width: 60%" name="savings_account_no" id="savings_account_no" value="<?php echo $acctsavingsaccount['savings_account_no']; ?>" readonly>

											<a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#memberlistto">Cari No. Rek</a>

											<input type="hidden" name="savings_account_id" id="savings_account_id" value="<?php echo $acctsavingsaccount['savings_account_id']; ?>">
										</td>
									</tr>
									<tr>
										<td width="35%">Simpanan</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="hidden" name="savings_id" id="savings_id" value="<?php echo $acctsavingsaccount['savings_id']; ?>">
											<input type="text" class="easyui-textbox" style="width: 100%" name="savings_name" id="savings_name" value="<?php echo $acctsavingsaccount['savings_name']; ?>" readonly>
										</td>
									</tr>
									<tr>
										<td width="35%">Nama</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width: 100%" name="savings_account_from_no" id="savings_account_from_no" value="<?php echo $acctsavingsaccount['member_name']; ?>" readonly>
										</td>
									</tr>
									<tr>
										<td width="35%">Saldo Simp.</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width: 100%" name="savings_account_last_balance" id="savings_account_last_balance" value="<?php echo number_format($acctsavingsaccount['savings_account_last_balance'], 2); ?>" readonly>
										</td>
									</tr>

									<tr>
										<td width="35%">Sandi</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="hidden" class="easyui-textbox" name="mutation_id" id="mutation_id" value="<?php echo $acctmutation['mutation_id']; ?>" readonly>
											<input type="text" class="easyui-textbox" style="width: 100%" name="mutation_name" id="mutation_name" value="<?php echo $acctmutation['mutation_name']; ?>" readonly>
										</td>
									</tr>
									<tr>
										<td width="35%">Jumlah Simpanan Wajib(Rp)</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_mandatory_savings_view" id="member_mandatory_savings_view" autocomplete="off"/>		
											<input type="hidden" class="easyui-textbox" name="member_mandatory_savings" id="member_mandatory_savings" autocomplete="off"/>
										</td>
									</tr>

									<input type="hidden" class="easyui-textbox" name="member_transfer_mutation_token" id="member_transfer_mutation_token" autocomplete="off" value="<?php echo $token; ?>"/>
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
<div id="memberlistfrom" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Daftar Anggota</h4>
      </div>
      <div class="modal-body">
		<table id="myDataTable" class="table table-striped table-bordered table-hover table-full-width">
			<thead>
		    	<tr>
		        	<th>No</th>
		        	<th>No. Anggota</th>
		            <th>Nama Anggota</th>
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

<div id="memberlistto" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Daftar Simpanan</h4>
      </div>
      <div class="modal-body">
		<table id="myDataTable2" class="table table-striped table-bordered table-hover table-full-width">
			<thead>
		    	<tr>
		        	<th>No</th>
		        	<th>No. Rek</th>
		            <th>Nama Anggota</th>
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
<?php $member_id = $this->uri->segment(3); ?>
<script type="text/javascript">
 
 
$(document).ready(function() {
 
    //datatables
    var table = $('#myDataTable').DataTable({ 
 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "pageLength": 5,
        "order": [], //Initial no order.
        "ajax": {
            "url": "<?php echo site_url('member-transfer-mutation/add-member')?>",
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


 
$(document).ready(function() {
 
    //datatables
    var table = $('#myDataTable2').DataTable({ 
 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "pageLength": 5,
        "order": [], //Initial no order.
        "ajax": {
            "url": "<?php echo site_url('member-transfer-mutation/add-savings-account/'.$member_id)?>",
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
