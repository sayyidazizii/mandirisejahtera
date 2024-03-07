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
	base_url = '<?php echo base_url();?>';

	var loop = 1;

	$(document).ready(function(){
		$('#savings_member_heir').textbox({
		   collapsible:false,
		   minimizable:false,
		   maximizable:false,
		   closable:false
		});

		$('#savings_member_heir').textbox('textbox').focus();
	});

	 function function_elements_add(name, value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('savings-account/elements-add');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
			}
		});
	}

	function reset_data(){
		document.location = base_url+"savings-account/reset_data";
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

	 $(document).ready(function(){
		 $('#savings_id').combobox({
			  onChange: function(value){
			  	var savings_id 	= +document.getElementById("savings_id").value;

			  

			   $.post(base_url + 'savings-account/get-savings-account-no',
				{savings_id: savings_id},
                function(data){
                console.log(data);	
                var obj = $.parseJSON(data)		   
                	$('#savings_interest_rate').textbox('setValue',obj["savings_interest_rate"]);
				},
				
				)
			  }
			})
	});

	$(document).ready(function(){
		$('#savings_account_first_deposit_amount_view').textbox({
			onChange: function(value){
				var name   	= 'savings_account_first_deposit_amount';
				var name2   = 'savings_account_first_deposit_amount_view';

				console.log(value);
				console.log(loop);
				if(loop == 0){
					loop= 1;
					return;
				}
				if(loop ==1){
					loop =0;
					var tampil = toRp(value);
				$('#savings_account_first_deposit_amount').textbox('setValue', value);
				$('#savings_account_first_deposit_amount_view').textbox('setValue', tampil);
				
				function_elements_add(name, value);
				function_elements_add(name2, tampil);
				}else{
					loop=1;
					return;
				}
			
			}
		});
	});

	$(document).ready(function(){
		$('#savings_account_adm_amount_view').textbox({
			onChange: function(value){
				var name   	= 'savings_account_adm_amount';
				var name2   = 'savings_account_adm_amount_view';

				console.log(value);
				console.log(loop);
				if(loop == 0){
					loop= 1;
					return;
				}
				if(loop ==1){
					loop =0;
					var tampil = toRp(value);
				$('#savings_account_adm_amount').textbox('setValue', value);
				$('#savings_account_adm_amount_view').textbox('setValue', tampil);
				
				function_elements_add(name, value);
				function_elements_add(name2, tampil);
				}else{
					loop=1;
					return;
				}
			
			}
		});
	});

	$(document).ready(function(){
		$('#savings_member_heir').textbox({
			onChange: function(value){
				var name   	= 'savings_member_heir';

		    	function_elements_add(name, value);
			}
		});
	});


	$(document).ready(function(){
        $("#Save").click(function(){
        	var savings_id 								= $("#savings_id").val();
			var savings_account_first_deposit_amount 	= $("#savings_account_first_deposit_amount_view").val();
			var savings_account_adm_amount 				= $("#savings_account_adm_amount_view").val();
			var office_id 								= $("#office_id").val();


			
			if(savings_id == ''){
				alert("Jenis Tabungan masih kosong");
				return false;
			}else if(savings_account_first_deposit_amount == ''){
				alert("Jumlah Setoran masih kosong");
				return false;
			}else if(office_id == ''){
				alert("Business Officer (BO) masih kosong");
				return false;
			}else if(savings_account_adm_amount == ''){
				alert("Biaya Administrasi masih kosong");
				return false;
			} 	
		});
    });
</script>
<?php echo form_open('savings-account/process-add',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
	$sesi 	= $this->session->userdata('unique');
	$data 	= $this->session->userdata('addacctsavingsaccount-'.$sesi['unique']);
	$auth 	= $this->session->userdata('auth');
	$token 	= $this->session->userdata('acctsavingsaccounttoken-'.$sesi['unique']);

	if (empty($data['member_id'])){
		$data['member_id'] 					= 0;
	}

	if (empty($data['savings_id'])){
		$data['savings_id'] 				= 0;
	}

	if (empty($data['savings_account_date'])){
		$data['savings_account_date'] 		= date('d-m-Y');
	}

	if (empty($data['savings_member_heir'])){
		$data['savings_member_heir'] 		= '';
	}

	if (empty($data['savings_member_heir_address'])){
		$data['savings_member_heir_address'] 						= '';
	}

	if (empty($data['savings_member_heir_relationship'])){
		$data['savings_member_heir_relationship'] 					= '';
	}

	if (empty($data['savings_interest_rate'])){
		$data['savings_interest_rate'] 			= 0;
	}

	if (empty($data['office_id'])){
		$data['office_id'] 					= 0;
	}

	if (empty($data['savings_account_first_deposit_amount_view'])){
		$data['savings_account_first_deposit_amount_view'] 				= '';
	}

	if (empty($data['savings_account_first_deposit_amount'])){
		$data['savings_account_first_deposit_amount'] 					= '';
	}

	if (empty($data['savings_account_adm_amount_view'])){
		$data['savings_account_adm_amount_view'] 						= '';
	}

	if (empty($data['savings_account_adm_amount'])){
		$data['savings_account_adm_amount'] 							= '';
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
			<a href="<?php echo base_url();?>AcctSavingsAccount">
				Daftar Rekening Tabungan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>savings-account/add">
				Tambah Rekening Tabungan 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->

<?php
// print_r($auth);
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			 <div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Form Tambah Rekening Tabungan
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>savings-account" class="btn btn-default btn-sm">
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
							<input type="hidden" class="form-control" name="member_id" id="member_id" value="<?php echo $coremember['member_id'];?>" readonly/>

							<input type="hidden" class="form-control" name="branch_id" id="branch_id" value="<?php echo $auth['branch_id'];?>" readonly/>

							<input type="hidden" class="form-control" name="savings_account_token" id="savings_account_token" value="<?php echo $token;?>" readonly/>

							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">No. Anggota</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_name" id="member_name" value="<?php echo $coremember['member_no'];?>" style="width: 60%" readonly autofocus/> <a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#memberlist">Cari Anggota</a> </td>
									</tr>
									<tr>
										<td width="35%">Anggota</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_no" id="member_no" value="<?php echo $coremember['member_name'];?>" style="width: 100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Tanggal Lahir</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-datebox date-picker" name="member_date_of_birth" id="member_date_of_birth" value="<?php echo $coremember['member_date_of_birth'];?>" style="width: 70%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Jenis Kelamin</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_gender" id="member_gender" value="<?php if($coremember['member_gender'] != ''){ echo $membergender[$coremember['member_gender']];}
										else {
											echo '';
										}
										?>" style="width: 100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Alamat</td>
										<td width="5%">:</td>
										<td width="60%"><?php echo form_textarea(array('rows'=>'2','name'=>'member_address','class'=>'easyui-textarea','id'=>'member_address','disabled'=>'disabled','value'=>$coremember['member_address']))?></td>
									</tr>
									<tr>
										<td width="35%">Kabupaten</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="city_name" id="city_name" value="<?php echo $coremember['city_name'];?>" style="width: 100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Kecamatan</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="kecamatan_name" id="kecamatan_name" value="<?php echo $coremember['kecamatan_name'];?>" style="width: 100%" readonly/></td>
									</tr>
									<!-- <tr>
										<td width="35%">Pekerjaan</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_job" id="member_job" value="<?php echo $coremember['member_job'];?>" style="width: 100%" readonly/></td>
									</tr> -->
									<tr>
										<td width="35%">No. Telp</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_phone" id="member_phone" value="<?php echo $coremember['member_phone'];?>" style="width: 100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Nama Ibu</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_mother" id="member_mother" value="<?php echo $coremember['member_mother'];?>" style="width: 100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">No. Identitas</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_identity_no" id="member_identity_no" value="<?php echo $coremember['member_identity_no'];?>" style="width: 100%" readonly/></td>
									</tr>
								</table>
							</div>
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td colspan="3" align="left"><b>Ahli Waris</b></td>
									</tr>
									<tr>
										<td width="35%">Nama</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="savings_member_heir" id="savings_member_heir" value="<?php echo set_value('savings_member_heir',$data['savings_member_heir']);?>"  style="width: 100%" /></td>
									</tr>
									<tr>
										<td width="35%">Alamat</td>
										<td width="5%">:</td>
										<td width="60%"><?php echo form_textarea(array('rows'=>'2','name'=>'savings_member_heir_address','class'=>'easyui-textarea','id'=>'savings_member_heir_address','value'=>$data['savings_member_heir_address']))?></td>
									</tr>
									<tr>
										<td width="35%">Hub. Keluarga</td>
										<td width="5%">:</td>
										<td width="60%"><?php echo form_dropdown('savings_member_heir_relationship', $familyrelationship, set_value('savings_member_heir_relationship',$data['savings_member_heir_relationship']),'id="savings_member_heir_relationship" class="easyui-combobox" style="width:70%" ');?></td>
									</tr>
									<tr>
										<td width="35%">Jenis Tabungan</td>
										<td width="5%">:</td>
										<td width="60%"><?php echo form_dropdown('savings_id', $acctsavings, set_value('savings_id',$data['savings_id']),'id="savings_id" class="easyui-combobox" style="width: 70%"');?></td>
									</tr>
									<tr>
										<td width="35%">Tanggal Buka<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser" id="savings_account_date" value="<?php echo date('d-m-Y'); ?>" readonly style="width: 70%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Bunga (%) Per Bulan<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="savings_interest_rate" id="savings_interest_rate" autocomplete="off" style="width: 100%" readonly /></td>
									</tr>
									<tr>
										<td width="35%">Business Officer (BO)<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><?php echo form_dropdown('office_id', $coreoffice, set_value('office_id',$data['office_id']),'id="office_id" class="easyui-combobox" style="width:70%" ');?></td>
									</tr>
									<tr>
										<td width="35%">Setoran (Rp)<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="savings_account_first_deposit_amount_view" id="savings_account_first_deposit_amount_view" autocomplete="off" style="width: 100%" value="<?php echo set_value('savings_account_first_deposit_amount_view',$data['savings_account_first_deposit_amount_view']);?>" /></td>
									</tr>
									<!-- <tr>
										<td width="35%">Biaya Adm (Rp)<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="savings_account_adm_amount_view" id="savings_account_adm_amount_view" autocomplete="off" style="width: 100%" value="<?php echo set_value('savings_account_adm_amount_view',$data['savings_account_adm_amount_view']);?>" /></td>
									</tr> -->

									<input type="hidden" class="easyui-textbox" name="savings_account_first_deposit_amount" id="savings_account_first_deposit_amount" autocomplete="off" value="<?php echo set_value('savings_account_first_deposit_amount',$data['savings_account_first_deposit_amount']);?>"/>

									<input type="hidden" class="easyui-textbox" name="savings_account_adm_amount" id="savings_account_adm_amount" autocomplete="off" value="<?php echo set_value('savings_account_adm_amount',$data['savings_account_adm_amount']);?>"/>

									<tr>
										<td width="35%"></td>
										<td width="5%"></td>
										<td width="60%" align="right">
											<button type="button" class="btn red" onClick="reset_data();"><i class="fa fa-times"></i> Batal</button>
											<button type="submit" name="Save" id="Save" class="btn green-jungle"><i class="fa fa-check"></i> Simpan</button>
										</td>
									</tr>
								</table>
							</div>
						</div>




						<!-- <h3> Ahli Waris </h3> -->


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
		        	<th>Member No</th>
		            <th>Member Nama</th>
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
            "url": "<?php echo site_url('savings-account/get-list-member')?>",
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
