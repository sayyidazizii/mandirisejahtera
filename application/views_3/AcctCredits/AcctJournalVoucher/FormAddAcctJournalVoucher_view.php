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


</style>
<script>
	var loop = 1;
	base_url = '<?php echo base_url();?>';
	mappia = "	<?php 
					$site_url = 'journal-voucher/add';
					echo site_url($site_url); 
				?>";

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
				url : "<?php echo site_url('ournal-voucher/elements-add');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
						// alert(name);
			}
		});
	}

	function reset_data(){
		document.location = base_url+"journal-voucher/reset-data";
	}

	function processAddArrayAcctjournalVoucher(){
		
		var account_id								= document.getElementById("account_id").value;
		var journal_voucher_amount					= document.getElementById("journal_voucher_amount").value;
		var journal_voucher_status					= document.getElementById("journal_voucher_status").value;
		var journal_voucher_description_item		= document.getElementById("journal_voucher_description").value;

		
			$('#offspinwarehouse').css('display', 'none');
			$('#onspinspinwarehouse').css('display', 'table-row');
			  $.ajax({
			  type: "POST",
			  url : "<?php echo site_url('journal-voucher/process-add-array');?>",
			  data: {
			  		'account_id'						: account_id,
					'journal_voucher_amount' 			: journal_voucher_amount, 
					'journal_voucher_status' 			: journal_voucher_status, 
					'journal_voucher_description_item'	: journal_voucher_description_item,
					'session_name' 						: "addacctjournalvoucheritem-"
				},
			  success: function(msg){
			   window.location.replace(mappia);
			 }
			});
	}

	$(document).ready(function(){
		$('#journal_voucher_description').textbox({
			onChange: function(value){
				var name   	= 'journal_voucher_description';

		    	function_elements_add(name, value);
			}
		});

		// $(document).ready(function(){
		// $('#journal_voucher_date').datebox({
		// 	onChange: function(value){
		// 		var name   	= 'journal_voucher_date';

		//     	function_elements_add(name, value);
		// 	}
		// });

		$('#journal_voucher_amount_view').textbox({
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
				$('#journal_voucher_amount').textbox('setValue', value);
				$('#journal_voucher_amount_view').textbox('setValue', tampil);
				

				}else{
					loop=1;
					return;
				}
			
			}
		});
	});
</script>
<?php echo form_open('journal-voucher/process-add',array('id' => 'myform', 'class' => 'horizontal-form')); ?>


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
			<a href="<?php echo base_url();?>AcctJournalVoucher">
				Daftar Jurnal Umum
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>journal-voucher/add">
				Tambah Jurnal Umum 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$sesi = $this->session->userdata('unique');
	$data = $this->session->userdata('addacctjournalvoucher-'.$sesi['unique']);
	$token= $this->session->userdata('acctjournalvouchertoken-'.$sesi['unique']);

	if(empty($data['journal_voucher_date'])){
		$data['journal_voucher_date']	= date('d-m-Y');
	}

	if(empty($data['journal_voucher_description'])){
		$dara['journal_voucher_description'] = '';
	}
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			 <div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Form Tambah Jurnal Umum
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>AcctJournalVoucher" class="btn btn-default btn-sm">
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
										<td width="35%">Tanggal</td>
										<td width="5%">:</td>
										<td width="60%">
											<input class="easyui-datebox" data-options="formatter:myformatter,parser:myparser" type="text" name="journal_voucher_date" id="journal_voucher_date" onChange="function_elements_add(this.name, this.value);" value="<?php echo tgltoview($data['journal_voucher_date']);?>" style="width: 60%"/>

											<input class="easyui-textbox" type="hidden" name="journal_voucher_token" id="journal_voucher_token" value="<?php echo $token;?>"/>
										</td>
									</tr>
									<tr>
										<td width="35%">Uraian</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="journal_voucher_description" id="journal_voucher_description" placeholder="Uraian" onChange="function_elements_add(this.name, this.value);" style="width: 100%" value="<?php echo $data['journal_voucher_description']; ?>">
										</td>
									</tr>
								</table>
								<h2></h2>
								<table width="100%">
									<tr>
										<td width="35%">No. Perkiraan</td>
										<td width="5%">:</td>
										<td width="60%">
											<?php echo form_dropdown('account_id', $acctaccount,set_value('account_id',$data['account_id']),'id="account_id" class="easyui-combobox" style="width:100%"');?>
										</td>
									</tr>
									<tr>
										<td width="35%">Jumlah</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="journal_voucher_amount_view" id="journal_voucher_amount_view" style="width: 100%">
											<input type="hidden" class="easyui-textbox" name="journal_voucher_amount" id="journal_voucher_amount">
										</td>
									</tr>
									<tr>
										<td width="35%">D/K</td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('journal_voucher_status', $accountstatus, set_value('journal_voucher_status'), 'id="journal_voucher_status" class="easyui-combobox" style="width: 100%"');
											?>
										</td>
									</tr>
									<tr>
										<td width="35%"></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="button" name="add2" id="buttonAddArrayAcctjournalVoucher" value="Add" class="btn green-jungle" title="Simpan Data" onClick="processAddArrayAcctjournalVoucher();">
										</td>
									</tr>
								</table>
							</div>
							
							<div class="col-md-6">
								<table width="100%" class="table table-striped table-bordered table-hover table-full-width">
									<tr>
										<td width="20%">No. Perkiraan</td>
										<td width="30%">Nama Perkiraan</td>
										<td width="25%">Debit</td>
										<td width="25%">Kredit</td>
									</tr>

									<?php
										$sesi = $this->session->userdata('unique');
										$acctjournalvoucheritem = $this->session->userdata('addacctjournalvoucheritem-'.$sesi['unique']);

										if(empty($acctjournalvoucheritem)){

										} else {
											foreach ($acctjournalvoucheritem as $key => $val) {
												echo "
												<tr>
													<td>".$this->AcctJournalVoucher_model->getAccountCode($val['account_id'])."</td>
													<td>".$this->AcctJournalVoucher_model->getAccountName($val['account_id'])."</td>";
													if($val['journal_voucher_status']==0){
													echo"
														<td style='text-align:right'>".number_format($val['journal_voucher_amount'], 2)."</td>
														<td></td>";
													} else {
														echo"
														<td></td>
														<td style='text-align:right'>(".number_format($val['journal_voucher_amount'], 2).")</td>";
													}
													echo"
												</tr>
												";
											}
										}
									?>


								</table>
								<table width="100%">
									<tr>
										<td width="35%"></td>
										<td width="5%"></td>
										<td width="60%">
											<button type="button" class="btn red" onClick="reset_data();"><i class="fa fa-times"></i> Batal</button>

											<a href="#" data-target="#confirm" data-toggle="modal" class="btn green-jungle"><i class="fa fa-check"></i> Simpan </a>

											<!-- <button type="submit" class="btn green-jungle"><i class="fa fa-check"></i> Simpan</button> -->
											<!-- <a href="#" class="btn blue"><i class="fa fa-print"></i> Cetak Bukti</a> -->
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
<div class="modal fade bs-modal-lg" id="confirm" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title">Konfirmasi</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">							
						<div class="form-group form-md-line-input">
							<input type="text"  name="remark" id="remark" class="form-control" readonly value="<?php echo $data['journal_voucher_description'];?>" >
						
							<label class="control-label">Uraian</label>
							
						</div>
					</div>
				</div>

				<table width="100%" class="table table-striped table-bordered table-hover table-full-width">
					<tr>
						<td width="20%">No. Perkiraan</td>
						<td width="30%">Nama Perkiraan</td>
						<td width="25%">Debit</td>
						<td width="25%">Kredit</td>
					</tr>

					<?php
						$sesi = $this->session->userdata('unique');
						$acctjournalvoucheritem = $this->session->userdata('addacctjournalvoucheritem-'.$sesi['unique']);

						if(empty($acctjournalvoucheritem)){

						} else {
							foreach ($acctjournalvoucheritem as $key => $val) {
								echo "
								<tr>
									<td>".$this->AcctJournalVoucher_model->getAccountCode($val['account_id'])."</td>
									<td>".$this->AcctJournalVoucher_model->getAccountName($val['account_id'])."</td>";
									if($val['journal_voucher_status']==0){
									echo"
										<td style='text-align:right'>".number_format($val['journal_voucher_amount'], 2)."</td>
										<td></td>";
									} else {
										echo"
										<td></td>
										<td style='text-align:right'>(".number_format($val['journal_voucher_amount'], 2).")</td>";
									}
									echo"
								</tr>
								";
							}
						}
					?>


				</table>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
				<button type="submit" class="btn green">Simpan</button>
			</div>
		</div>
	</div>
</div>



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