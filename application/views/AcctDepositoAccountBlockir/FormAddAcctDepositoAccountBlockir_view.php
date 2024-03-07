<style>
	th, td {
	  padding: 3px;
	}
	input:focus { 
	  background-color: 42f483;
	}
</style>
<script>
	base_url = '<?php echo base_url();?>';

	var loop = 1;

	$(document).ready(function(){
		$('#member_name').textbox({
		   collapsible:false,
		   minimizable:false,
		   maximizable:false,
		   closable:false
		});
		
		$('#member_name').textbox('textbox').focus();
	});

	function toRp(number) {
		var number = number.toString(), 
		rupiah = number.split('.')[0], 
		cents = (number.split('.')[1] || '') +'00';
		rupiah = rupiah.split('').reverse().join('')
			.replace(/(\d{3}(?!$))/g, '$1,')
			.split('').reverse().join('');
		return rupiah + '.' + cents.slice(0, 2);
	}

	function function_elements_edit(name, value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('deposito-account-blockir/function-elements-edit');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
			}
		});
	}

	function reset_edit(){
		document.location = base_url+"deposito-account-blockir/reset-edit/<?php echo $acctdepositoaccount['member_id']?>";
	}

	$(document).ready(function(){
		$('#deposito_account_blockir_amount_view').textbox({
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
				$('#deposito_account_blockir_amount').textbox('setValue', value);
				$('#deposito_account_blockir_amount_view').textbox('setValue', tampil);
				
				}else{
					loop=1;
					return;
				}
			
			}
		});
	});
</script>
<?php echo form_open('deposito-account-blockir/process-add-blockir',array('id' => 'myform', 'class' => 'horizontal-form'));

	if(empty($data['deposito_account_blockir_type'])){
		$data['deposito_account_blockir_type'] = 9;
	}
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
			<a href="<?php echo base_url();?>AcctDepositoAccountBlockir">
				Blockir Rekening Anggota
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->

<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Blockir
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>AcctDepositoAccountBlockir" class="btn btn-default btn-sm">
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
						?>

						<div class="row">
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">No. Rek<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="deposito_account_no" id="deposito_account_no" autocomplete="off" value="<?php echo set_value('deposito_account_no', $acctdepositoaccount['deposito_account_no']);?>" style="width: 60%" readonly/> <a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#memberlist">Cari Anggota</a>
										</td>
									</tr>
									<tr>
										<td width="35%">Nama Anggota<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo set_value('member_name', $acctdepositoaccount['member_name']);?>" style="width: 100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Simpanan<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="deposito_name" id="deposito_name" autocomplete="off" value="<?php echo set_value('deposito_name', $acctdepositoaccount['deposito_name']);?>" style="width: 100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Alamat<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_address" id="member_address" autocomplete="off" value="<?php echo set_value('member_address', $acctdepositoaccount['member_address']);?>" style="width: 100%" readonly/></td>
									</tr>
									<!-- <tr>
										<td width="35%">Kabupaten<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="city_name" id="city_name" autocomplete="off" value="<?php echo set_value('city_name', $acctdepositoaccount['city_name']);?>" style="width: 100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Kecamatan<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="kecamatan_name" id="kecamatan_name" autocomplete="off" value="<?php echo set_value('kecamatan_name', $acctdepositoaccount['kecamatan_name']);?>" style="width: 100%" readonly/></td>
									</tr> -->
									<tr>
										<td width="35%">No. Identitas<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_identity_no" id="member_identity_no" autocomplete="off" value="<?php echo set_value('member_identity_no', $acctdepositoaccount['member_identity_no']);?>" style="width: 100%" readonly/></td>
									</tr>
								</table>
							</div>
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table>
									<tr>
										<td width="35%">Saldo<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="deposito_account_amount" id="deposito_account_amount" autocomplete="off" style="width: 100%" value="<?php echo number_format($acctdepositoaccount['deposito_account_amount'], 2);?>" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Sifat Blokir<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<?php echo form_dropdown('deposito_account_blockir_type', $blockirtype, set_value('deposito_account_blockir_type',$data['deposito_account_blockir_type']),'id="deposito_account_blockir_type" class="easyui-combobox" style="width: 70%"');?>
										</td>

									<tr>
										<td width="35%">Saldo Blockir</td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="deposito_account_blockir_amount_view" id="deposito_account_blockir_amount_view" autocomplete="off" style="width: 100%"/>

											<input type="hidden" class="easyui-textbox" name="deposito_account_blockir_amount" id="deposito_account_blockir_amount" autocomplete="off"/>
										</td>
									</tr>
								</table>
								 
						
								
								

								<input type="hidden" class="easyui-textbox" name="member_id" id="member_id" placeholder="id" value="<?php echo set_value('member_id',$acctdepositoaccount['member_id']);?>"/>

								<input type="hidden" class="easyui-textbox" name="deposito_account_id" id="deposito_account_id" placeholder="id" value="<?php echo set_value('deposito_account_id',$acctdepositoaccount['deposito_account_id']);?>"/>

								<table width="100%">
									<tr>
										<td width="35%"></td>
										<td width="5%"></td>
										<td width="60%" align="right">
											<button type="button" class="btn red" onClick="reset_edit();"><i class="fa fa-times"></i> Batal</button>
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
<div id="memberlist" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Daftar Rekening Simp Berjangka Anggota</h4>
      </div>
      <div class="modal-body">
		<table id="myDataTable" class="table table-striped table-bordered table-hover table-full-width">
			<thead>
		    	<tr>
		        	<th>No</th>
		        	<th>No SimpKa</th>
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
            "url": "<?php echo site_url('AcctDepositoAccountBlockir/getListAcctDepositoAccount')?>",
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