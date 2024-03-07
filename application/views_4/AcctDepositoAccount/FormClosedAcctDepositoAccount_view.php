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

<script type="text/javascript">
	base_url = '<?php echo base_url();?>';
	member_id = '<?php echo $this->uri->segment(3);?>';
	
	function reset_data(){
		document.location = base_url+"deposito-account/reset-close/"+member_id;
		}
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
		
		function function_elements_add(name, value){
			$.ajax({
					type: "POST",
					url : "<?php echo site_url('deposito-account/add-function-element');?>",
					data : {'name' : name, 'value' : value},
					success: function(msg){
				}
			});
		}
		
		

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
				
				function_elements_add(name, value);
				function_elements_add(name2, tampil);
				}else{
					loop=1;
					return;
				}
			
			}
		});
	});
</script>

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
			<a href="<?php echo base_url();?>deposito-account/get-closed">
				Penutupan Simpanan Berjangka
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Penutupan Simpanan Berjangka
</h3>
<?php echo form_open('deposito-account/process-closed',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
// print_r($acctdepositoaccount);
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
	$sesi 	= $this->session->userdata('unique');
	$data 	= $this->session->userdata('addacctdepositoaccount-'.$sesi['unique']);
	$token 	= $this->session->userdata('acctdepositoaccounttoken-'.$sesi['unique']);
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
						<a href="<?php echo base_url();?>deposito-account/get-closed" class="btn btn-default btn-sm">
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
											<input type="text" class="easyui-textbox" style="width:100%" name="deposito_account_no" id="deposito_account_no" value="<?php echo $acctdepositoaccount['deposito_account_no'];?>" readonly/>
											<input type="hidden" class="form-control" name="deposito_account_id" id="deposito_account_id" value="<?php echo $acctdepositoaccount['deposito_account_id'];?>" readonly/>
											<input type="hidden" class="form-control" name="deposito_account_closed_token" id="deposito_account_closed_token" value="<?php echo $token;?>" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">No. Seri</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width:100%" name="deposito_account_serial_no" id="deposito_account_serial_no" value="<?php echo $acctdepositoaccount['deposito_account_serial_no'];?>" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Jenis Simpanan Berjangka</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width:100%" name="deposito_name" id="deposito_name" value="<?php echo $acctdepositoaccount['deposito_name'];?>" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Anggota</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width:100%" name="member_name" id="member_name" value="<?php echo $acctdepositoaccount['member_name'];?>" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">No. Anggota</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width:100%" name="member_no" id="member_no" value="<?php echo $acctdepositoaccount['member_no'];?>" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Jenis Kelamin</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width:100%" name="member_gender" id="member_gender" value="<?php echo $membergender[$acctdepositoaccount['member_gender']];?>" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Alamat</td>
										<td width="5%">:</td>
										<td width="60%">
											<?php echo form_textarea(array('rows'=>'2','name'=>'member_address','class'=>'easyui-textarea','id'=>'member_address','disabled'=>'disabled', 'value'=> $acctdepositoaccount['member_address']))?>
										</td>
									</tr>
									<tr>
										<td width="35%">Kabupaten</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width:100%" name="city_name" id="city_name" value="<?php echo $this->AcctDepositoAccount_model->getCityName($acctdepositoaccount['city_id']);?>" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Kecamatan</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width:100%" name="kecamatan_name" id="kecamatan_name" value="<?php echo $this->AcctDepositoAccount_model->getKecamatanName($acctdepositoaccount['kecamatan_id']);?>" readonly/>
										</td>
									</tr>
									
								</table>
							</div>
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">Identitas</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width:100%" name="identity_name" id="identity_name" value="<?php echo $memberidentity[$acctdepositoaccount['identity_id']];?>" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">No. Identitas</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width:100%" name="member_identity_no" id="member_identity_no" value="<?php echo $acctdepositoaccount['member_identity_no'];?>" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Jangka Waktu (Bln)</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width:100%" name="deposito_account_period" id="deposito_account_period" value="<?php echo $acctdepositoaccount['deposito_account_period'];?>" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Saldo</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width:100%" name="deposito_account_amount" id="deposito_account_amount" value="<?php echo number_format($acctdepositoaccount['deposito_account_amount'], 2);?>" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Tanggal Mulai</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width:100%" name="deposito_account_date" id="deposito_account_date" value="<?php echo tgltoview($acctdepositoaccount['deposito_account_date']);?>" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Jatuh Tempo</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width:100%" name="deposito_account_due_date" id="deposito_account_due_date" value="<?php echo tgltoview($acctdepositoaccount['deposito_account_due_date']);?>" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Bunga (Rp)</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="interest_total_view" id="interest_total_view" autocomplete="off" style="width: 100%" value="<?php echo nominal($interest_total) ?>" readonly/>
										<input type="hidden" class="easyui-textbox" name="interest_total" id="interest_total" autocomplete="off" value="<?php echo $interest_total ?>"/></td>
										
									</tr>
									<tr>
										<td width="35%">Pajak (Rp)</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="tax_total_view" id="tax_total_view" autocomplete="off" style="width: 100%" value="<?php echo nominal($tax_total) ?>" readonly/>
										<input type="hidden" class="easyui-textbox" name="tax_total" id="tax_total" autocomplete="off" value="<?php echo $tax_total ?>"/></td>
										
									</tr>
									<tr>
										<td width="35%">Jasa Diterima (Rp)</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="interest_received_total_view" id="interest_received_total_view" autocomplete="off" style="width: 100%" value="<?php echo nominal($interest_total - $tax_total) ?>" readonly/>
										<input type="hidden" class="easyui-textbox" name="interest_received_total" id="interest_received_total" autocomplete="off" value="<?php echo $interest_total - $tax_total ?>"/></td>
										
									</tr>
									<tr>
										<td width="35%">Biaya Adm (Rp)</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="deposito_account_amount_adm_view" id="deposito_account_amount_adm_view" autocomplete="off" style="width: 100%" value="<?php echo set_value('deposito_account_amount_adm',$data['deposito_account_amount_adm_view']);?>" /><input type="hidden" class="easyui-textbox" name="deposito_account_amount_adm" id="deposito_account_amount_adm" autocomplete="off" value="<?php echo set_value('deposito_account_amount_adm',$data['deposito_account_amount_adm']);?>"/></td>
										
									</tr>

											
									<tr>
										<td width="35%">Rek Simpanan<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="20%">
												<input type="text"  class="easyui-textbox" size="4" name="savings_account_no" id="savings_account_no" autocomplete="off" value="<?php echo set_value('savings_account_no', $acctsavingsaccount['savings_account_no']);?>" style="width: 100%" readonly/>
											 &nbsp <a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#simpananlist">Cari No. Rek</a> 
											 <input type="hidden" class="easyui-textbox" name="deposito_account_penalty" id="deposito_account_penalty" autocomplete="off"/>

											<input type="hidden" class="easyui-textbox" name="deposito_account_amount" id="deposito_account_amount" autocomplete="off" value="<?php echo $acctdepositoaccount['deposito_account_amount'];?>"/>

											 <input type="hidden"  class="easyui-textbox" size="4" name="savings_account_id" id="savings_account_id" autocomplete="off" value="<?php echo set_value('savings_account_id', $acctsavingsaccount['savings_account_id']);?>" readonly/>
											<input type="hidden"  class="easyui-textbox" size="4" name="savings_id" id="savings_id" autocomplete="off" value="<?php echo set_value('savings_id', $acctsavingsaccount['savings_id']);?>" readonly/>
											 <input type="hidden"  class="easyui-textbox" size="4" name="member_id_savings" id="member_id_savings" autocomplete="off" value="<?php echo $acctsavingsaccount['member_id'];?>" readonly/>
											    <input type="hidden"  class="easyui-textbox" size="4" name="savings_account_last_balance" id="savings_account_last_balance" autocomplete="off" value="<?php echo set_value('savings_account_last_balance', $acctsavingsaccount['savings_account_last_balance']);?>" readonly/>
										</td>
									</tr>
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
<table id="simpantable" class="table table-striped table-bordered table-hover table-full-width" >
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
$deposito_account_id = $this->uri->segment(3);
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
		
            "url": "<?php echo site_url('deposito-account/get-savings-account-list/'.$deposito_account_id)?>",
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