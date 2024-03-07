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

	$(document).ready(function(){
        $("#savings_account_id").change(function(){
            var savings_account_id = $("#savings_account_id").val();
            // alert(savings_account_id);
            $.post(base_url + 'savings-bank-mutation/get-savings-account-detail',
			{savings_account_id: savings_account_id},
                function(data){			   
                	// alert(data);
				   	$("#savings_name").val(data.savings_name);
				   	$("#member_name").val(data.member_name);
					$("#member_address").val(data.member_address);
					$("#city_name").val(data.city_name);
					$("#kecamatan_name").val(data.kecamatan_name);
					$("#identity_name").val(data.identity_name);
					$("#member_identity_no").val(data.member_identity_no);
					$("#savings_account_last_balance").val(toRp(data.savings_account_last_balance));
					$("#savings_bank_mutation_opening_balance").val(data.savings_account_last_balance);
				},
					'json'
				);				
            });
        });


	$(document).ready(function(){
        $("#savings_bank_mutation_amount_view").change(function(){
			var savings_bank_mutation_amount_view				= $('#savings_bank_mutation_amount_view').val();
			var savings_bank_mutation_opening_balance			= $('#savings_bank_mutation_opening_balance').val();	
			var bank_account_id 								= $("#bank_account_id").val();	

			var savings_bank_mutation_last_balance;

			if(bank_account_id != ''){
				savings_bank_mutation_last_balance = parseFloat(savings_bank_mutation_opening_balance) + parseFloat(savings_bank_mutation_amount_view);
			} else {
				alert("Transfer Bank masih kosong");
					return false;
			}
			
			document.getElementById('savings_bank_mutation_amount_view').value			= toRp(savings_bank_mutation_amount_view);
			document.getElementById('savings_bank_mutation_amount').value				= savings_bank_mutation_amount_view;
			document.getElementById('savings_bank_mutation_last_balance_view').value	= toRp(savings_bank_mutation_last_balance);
			document.getElementById('savings_bank_mutation_last_balance').value			= savings_bank_mutation_last_balance;
		});
		
	});

	$(document).ready(function(){
        $("#Save").click(function(){
        	var savings_account_id 					= $("#savings_account_id").val();
			var bank_account_id 					= $("#bank_account_id").val();
			var savings_bank_mutation_amount_view 	= $("#savings_bank_mutation_amount_view").val();

			
			if(savings_account_id == ''){
				alert("No. Rekening masih kosong");
				return false;
			}else if(bank_account_id == ''){
				alert("Transfer Bank masih kosong");
				return false;
			}else if(savings_bank_mutation_amount_view == ''){
				alert("Jumlah Mutasi Masih Kosong");
				return false;
			} 	
		});
    });
</script>
<?php echo form_open('savings-bank-mutation/process-add',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
	$sesi 	= $this->session->userdata('unique');
	$data 	= $this->session->userdata('addacctsavings-'.$sesi['unique']);


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
			<a href="<?php echo base_url();?>savings-bank-mutation">
				Daftar Mutasi Tabungan Non Tunai
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>savings-bank-mutation/add">
				Tambah Mutasi Tabungan Non Tunai 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Form Tambah Mutasi Tabungan Non Tunai
</h3>
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
						<a href="<?php echo base_url();?>savings-bank-mutation" class="btn btn-default btn-sm">
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
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<?php echo form_dropdown('savings_account_id', $acctsavingsaccount, set_value('savings_account_id',$data['savings_account_id']),'id="savings_account_id" class="form-control select2me" ');?>
									<label class="control-label">No. Rekening</label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<?php echo form_dropdown('bank_account_id', $acctbankaccount, set_value('bank_account_id',$data['bank_account_id']),'id="bank_account_id" class="form-control select2me" ');?>
									<label class="control-label">Transfer Bank</label>
								</div>
							</div>
						</div>

						<div class="row">	
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="savings_name" id="savings_name" autocomplete="off" readonly/>
									<label class="control-label">Jenis Tabungan<span class="required">*</span></label>
								</div>
							</div>					
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_name" id="member_name" autocomplete="off" readonly/>
									<label class="control-label">Nama Anggota<span class="required">*</span></label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">	
								<div class="form-group form-md-line-input">
									<?php echo form_textarea(array('rows'=>'3','name'=>'member_address','class'=>'form-control','id'=>'member_address','disabled'=>'disabled'))?>
									<label class="control-label">Alamat
										<span class="required">
											*
										</span>
									</label>

								</div>
							</div>
						</div>

						<div class="row">						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="city_name" id="city_name" autocomplete="off" readonly/>
									<label class="control-label">Kabupaten<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="kecamatan_name" id="kecamatan_name" autocomplete="off" readonly/>
									<label class="control-label">Kecamatan<span class="required">*</span></label>
								</div>
							</div>
						</div>

						<div class="row">						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="identity_name" id="identity_name" autocomplete="off" readonly/>
									<label class="control-label">Identitas<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_identity_no" id="member_identity_no" autocomplete="off" readonly/>
									<label class="control-label">No. Identitas<span class="required">*</span></label>
								</div>
							</div>
						</div>

						<h3> Detail Mutasi Baru </h3>

						<div class="row">						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="savings_account_last_balance" id="savings_account_last_balance" autocomplete="off" readonly/>
									<input type="hidden" class="form-control" name="savings_bank_mutation_opening_balance" id="savings_bank_mutation_opening_balance" autocomplete="off" readonly/>
									<label class="control-label">Saldo Lama<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="savings_bank_mutation_last_balance_view" id="savings_bank_mutation_last_balance_view" autocomplete="off" readonly/>
									<input type="hidden" class="form-control" name="savings_bank_mutation_last_balance" id="savings_bank_mutation_last_balance" autocomplete="off" readonly/>
									<label class="control-label">Saldo Baru<span class="required">*</span></label>
								</div>
							</div>
						</div>

						<div class="row">						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="savings_bank_mutationdate" id="savings_bank_mutation_date" autocomplete="off" value="<?php echo date('d-m-Y'); ?>" readonly/>
									<label class="control-label">Tanggal Transaksi<span class="required">*</span></label>
								</div>
							</div>
							
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="savings_bank_mutation_amount_view" id="savings_bank_mutation_amount_view" autocomplete="off" value="<?php echo set_value('savings_bank_mutation_amount_view',$data['savings_bank_mutation_amount_view']);?>"/>
									<input type="hidden" class="form-control" name="savings_bank_mutation_amount" id="savings_bank_mutation_amount" autocomplete="off" value="<?php echo set_value('savings_bank_mutation_amount',$data['savings_bank_mutation_amount']);?>"/>
									<label class="control-label">Jumlah (Rp)<span class="required">*</span></label>
								</div>
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
<?php echo form_close(); ?>
