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
		$('#deposito_member_heir').textbox({
		   collapsible:false,
		   minimizable:false,
		   maximizable:false,
		   closable:false
		});

		$('#deposito_member_heir').textbox('textbox').focus();
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

	function function_elements_add(name, value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('deposito-account/add-function-element');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
			}
		});
	}

	function reset_data(){
		document.location = base_url+"deposito-account/reset";
	}

	 $(document).ready(function(){
		 $('#deposito_id').combobox({
			  onChange: function(value){
			  	var deposito_id 	= +document.getElementById("deposito_id").value;

				$.post(base_url + 'deposito-account/get-deposite-account-no',
					{deposito_id: deposito_id},
					function(data){	
					var obj = $.parseJSON(data);
						$("#deposito_period").textbox('setValue',obj['deposito_period']);
						$("#deposito_account_no").textbox('setValue',obj['deposito_account_no']);
						$("#deposito_account_serial_no").textbox('setValue',obj['deposito_account_serial_no']);
						$("#deposito_account_due_date").textbox('setValue',obj['deposito_account_due_date']);
						$("#deposito_account_nisbah").textbox('setValue',obj['deposito_account_nisbah']);
					},
				)
			  }
			})
	});

	$(document).ready(function(){
		$('#deposito_account_amount_view').textbox({
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
				$('#deposito_account_amount').textbox('setValue', value);
				$('#deposito_account_amount_view').textbox('setValue', tampil);

				}else{
					loop=1;
					return;
				}
			
			}
		});
	});


</script>
<?php echo form_open('deposito-account/process-add',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
	$sesi 	= $this->session->userdata('unique');
	$data 	= $this->session->userdata('addacctdepositoaccount-'.$sesi['unique']);
	$token 	= $this->session->userdata('acctdepositoaccounttoken-'.$sesi['unique']);
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
			<a href="<?php echo base_url();?>deposito-account">
				Daftar Rekening Simpanan Berjangka
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>deposito-account/add">
				Tambah Rekening Simpanan Berjangka
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->
<?php
// print_r($data);
// print_r($member);
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			 <div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Form Tambah Rekening Simpanan Berjangka
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>deposito-account" class="btn btn-default btn-sm">
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
							<input type="hidden" class="form-control" name="member_id" id="member_id" value="<?php echo $coremember['member_id']; ?>" readonly/>

							<input type="hidden" class="form-control" name="deposito_account_token" id="deposito_account_token" value="<?php echo $token; ?>" readonly/>

							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">No. Anggota</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_no" id="member_no" value="<?php echo $coremember['member_no'];?>" style="width: 60%" readonly autofocus/> <a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#memberlist"><i class="fa fa-search"></i> Cari Anggota</a> </td>
									</tr>
									<tr>
										<td width="35%">Anggota</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_name" id="member_name" value="<?php echo $coremember['member_name']; ?>" style="width:100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Tanggal Lahir</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_date_of_birth" id="member_date_of_birth" value="<?php echo tgltoview($coremember['member_date_of_birth']); ?>" style="width:70%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Jenis Kelamin</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_gender" id="member_gender" value="<?php if($coremember['member_gender'] != ''){ echo $membergender[$coremember['member_gender']];}
										else {
											echo '';
										}
										?>" style="width:100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Alamat</td>
										<td width="5%">:</td>
										<td width="60%"><?php echo form_textarea(array('rows'=>'2','name'=>'member_address','class'=>'easyui-textarea','id'=>'member_address','disabled'=>'disabled','value'=>$coremember['member_address']))?></td>
									</tr>
									<tr>
										<td width="35%">Kabupaten</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="city_name" id="city_name" value="<?php echo $coremember['city_name']; ?>" style="width:100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Kecamatan</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="kecamatan_name" id="kecamatan_name" value="<?php echo $coremember['kecamatan_name']; ?>" style="width:100%" readonly/></td>
									</tr>
									<!-- <tr>
										<td width="35%">Pekerjaan</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="job_name" id="job_name" value="<?php echo $coremember['member_job']; ?>" style="width:100%" readonly/></td>
									</tr> -->
									<tr>
										<td width="35%">No. Telp</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_phone" id="member_phone" value="<?php echo $coremember['member_phone']; ?>" style="width:100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Nama Ibu</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_mother" id="member_mother" value="<?php echo $coremember['member_mother'];?>" style="width:100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">No. Identitas</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_identity_no" id="member_identity_no" value="<?php echo $coremember['member_identity_no']; ?>" style="width:100%" readonly/></td>
									</tr>
									<tr>
										<td colspan="3" align="left"><b>Ahli Waris</b></td>
									</tr>
									
									<tr>
										<td width="35%">Nama</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="deposito_member_heir" id="deposito_member_heir" value="<?php echo set_value('deposito_member_heir',$data['deposito_member_heir']);?>" style="width: 100%" /></td>
									</tr>
									<tr>
										<td width="35%">Alamat</td>
										<td width="5%">:</td>
										<td width="60%"><?php echo form_textarea(array('rows'=>'1','name'=>'deposito_member_heir_address','class'=>'easyui-textarea','id'=>'deposito_member_heir_address','value'=>$data['deposito_member_heir_address']))?></td>
									</tr>
									<tr>
										<td width="35%">Hub. Keluarga</td>
										<td width="5%">:</td>
										<td width="60%"><?php echo form_dropdown('deposito_member_heir_relationship', $familyrelationship, set_value('deposito_member_heir_relationship',$data['deposito_member_heir_relationship']),'id="deposito_member_heir_relationship" class="easyui-combobox" style="width:70%"');?></td>
									</tr>
								</table>
							</div>
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">No. Simpanan<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="savings_account_no" id="savings_account_no" autocomplete="off" value="<?php echo $acctsavingsaccount['savings_account_no']; ?>" readonly/> <a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#savingsaccountlist"><i class="fa fa-search"></i> Cari No. Rek</a>
											<input type="hidden" class="easyui-textbox" name="savings_account_id" id="savings_account_id"  value="<?php echo $acctsavingsaccount['savings_account_id']; ?>"/>
										</td>
									</tr>
									<tr>
										<td width="35%">Jenis Simpanan Berjangka</td>
										<td width="5%">:</td>
										<td width="60%"><?php echo form_dropdown('deposito_id', $acctdeposito, set_value('deposito_id',$data['deposito_id']),'id="deposito_id" class="easyui-combobox" style="width:70%"');?></td>
									</tr>
									<tr>
										<td width="35%">Jenis Perpanjangan Simpanan Berjangka</td>
										<td width="5%">:</td>
										<td width="60%"><?php echo form_dropdown('deposito_account_extra_type', $depositoextratype, set_value('deposito_account_extra_type',$data['deposito_account_extra_type']),'id="deposito_account_extra_type" class="easyui-combobox" style="width:70%"');?></td>
									</tr>
									<tr>
										<td width="35%">Jangka Waktu <span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="deposito_period" id="deposito_period" autocomplete="off" style="width:100%" readonly /></td>
									</tr>
									
									<!-- <tr>
										<td width="35%">Nama<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="savings_account_no" id="savings_account_no" autocomplete="off" style="width: 100%" /></td>
									</tr> -->
									<tr>
										<td width="35%">Business Office (BO)<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><?php echo form_dropdown('office_id', $coreoffice, set_value('office_id',$data['office_id']),'id="office_id" class="easyui-combobox" style="width:70%" ');?></td>
									</tr>
									<tr>
										<td width="35%">Tanggal Buka<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="deposito_account_date" id="deposito_account_date" autocomplete="off" value="<?php echo date('d-m-Y'); ?>" style="width:70%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Tanggal Jatuh Tempo<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="deposito_account_due_date" id="deposito_account_due_date" autocomplete="off" style="width:70%" readonly /></td>
									</tr>
									<tr>
										<td width="35%">No. SimpKa<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="deposito_account_no" id="deposito_account_no" autocomplete="off" style="width:100%" readonly /></td>
									</tr>
									<tr>
										<td width="35%">No. Seri<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="deposito_account_serial_no" id="deposito_account_serial_no" autocomplete="off" style="width:100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Suku Bunga (%)<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="deposito_account_nisbah" id="deposito_account_nisbah" autocomplete="off" style="width:100%" readonly /></td>
									</tr>
									<tr>
										<td width="35%">Nominal (Rp)<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="deposito_account_amount_view" id="deposito_account_amount_view" autocomplete="off" value="<?php echo set_value('deposito_account_amount_view',$data['deposito_account_amount_view']);?>" style="width:100%"/></td>
									</tr>
									<input type="hidden" class="easyui-textbox" name="deposito_account_amount" id="deposito_account_amount" autocomplete="off" value="<?php echo set_value('deposito_account_amount',$data['deposito_account_amount']);?>"/>
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
<div id="memberlist" class="modal fade" role="dialog">
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

<div id="savingsaccountlist" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Daftar Rekening</h4>
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

<?php 
$member_id = $this->uri->segment(3);
?>
<script type="text/javascript">
 
 
$(document).ready(function() {
 
    //datatables
    var table = $('#myDataTable').DataTable({ 
 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "pageLength": 5,
        "order": [], //Initial no order.
        "ajax": {
            "url": "<?php echo site_url('deposito-account/get-list-core-member')?>",
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
            "url": "<?php echo site_url('deposito-account/get-list-savings-account/'.$member_id)?>",
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
            return (d<10?('0'+d):d)+'-'+(m<10?('0'+m):m)+'-'+y;
        }
        function myparser(s){
            if (!s) return new Date();
            var ss = (s.split('-'));
            var y = parseInt(ss[0],10);
            var m = parseInt(ss[1],10);
            var d = parseInt(ss[2],10);
            if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
                return new Date(d,m-1,y);
            } else {
                return new Date();
            }
        }
    </script>

<?php echo form_close(); ?>
