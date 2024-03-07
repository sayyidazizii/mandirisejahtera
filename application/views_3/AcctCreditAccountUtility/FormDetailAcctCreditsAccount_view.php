<style>
	th, td {
	  padding: 3px;
	}
	td {
	  font-size: 12px;
	}
	input:focus { 
	  background-color: 42f483;
	}
	.custom{

	margin: 0px; padding-top: 0px; padding-bottom: 0px; height: 50px; line-height: 50px; width: 50px;

	}
	.textbox .textbox-text{
	font-size: 10px;


	}
</style>
<script>
	
</script>

<?php
	echo form_open('credit-account/process-printing'); 
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			 <div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Form Detail
					</div>
				</div>
			
				<div class="portlet-body">
					<div class="form-body">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input class="form-control" type="text" name="credits_account_serial" readonly id="credits_account_serial" value="<?php echo $acctcreditsaccount['credits_account_serial']; ?>"/>

									<input class="form-control" type="hidden" name="credits_account_id" readonly id="credits_account_id" value="<?php echo $acctcreditsaccount['credits_account_id']; ?>"/>
									<label class="control-label">No Akad</label>	
								</div>
							</div>	

							<div class="col-md-6 ">
								<div class="form-group form-md-line-input">
									<input name="credits_name" id="credits_name" type="text" class="form-control" value="<?php echo $acctcreditsaccount['credits_name'];?>" readonly>
									<label class="control-label">Jenis Pinjaman</label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input class="form-control" type="text" name="member_name" readonly id="member_name" value="<?php echo $acctcreditsaccount['member_name']; ?>"/>
									<label class="control-label">Nama Anggota</label>	
								</div>
							</div>	
						</div>

						<div class = "row">
							<div class="col-md-12">
								<div class="form-group form-md-line-input">
									<?php
										$member_address = $acctcreditsaccount['member_address']." ".$acctcreditsaccount['kecamatan_name']." ".$acctcreditsaccount['city_name']." ".$acctcreditsaccount['province_name']
									?>
									<textarea name="member_address" id="member_address" type="text" class="form-control" readonly><?php echo $member_address;?></textarea>
									<label class="control-label">Alamat</label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input class="form-control" type="text" name="member_identity" readonly id="member_identity" value="<?php echo $memberidentity[$acctcreditsaccount['member_identity']]; ?>"/>
									<label class="control-label">Identitas</label>	
								</div>
							</div>	

							<div class="col-md-6 ">
								<div class="form-group form-md-line-input">
									<input name="member_identity_no" id="member_identity_no" type="text" class="form-control" value="<?php echo $acctcreditsaccount['member_identity_no'];?>" readonly>
									<label class="control-label">No Identitas</label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input class="form-control" type="text" name="credits_account_date" readonly id="credits_account_date" value="<?php echo tgltoview($acctcreditsaccount['credits_account_date']); ?>"/>
									<label class="control-label">Tanggal Realisasi</label>	
								</div>
							</div>	

							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input name="credits_account_period" id="credits_account_period" type="text" class="form-control" value="<?php echo $acctcreditsaccount['credits_account_period'];?>" readonly>
									<label class="control-label">Jangka Waktu</label>
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input name="credits_account_due_date" id="credits_account_due_date" type="text" class="form-control" value="<?php echo tgltoview($acctcreditsaccount['credits_account_due_date']);?>" readonly>
									<label class="control-label">Jatuh Tempo</label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input class="form-control" type="text" name="credits_account_principal_amount" readonly id="credits_account_principal_amount" value="<?php echo nominal($acctcreditsaccount['credits_account_principal_amount']); ?>"/>
									<label class="control-label">Angsuran Pokok / Bulan</label>	
								</div>
							</div>	

							<div class="col-md-6 ">
								<div class="form-group form-md-line-input">
									<input name="credits_account_margin_amount" id="credits_account_margin_amount" type="text" class="form-control" value="<?php echo nominal($acctcreditsaccount['credits_account_margin_amount']);?>" readonly>
									<label class="control-label">Angsuran Margin / Bulan</label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input class="form-control" type="text" name="credits_account_last_balance_principal" readonly id="credits_account_last_balance_principal" value="<?php echo nominal($acctcreditsaccount['credits_account_last_balance_principal']); ?>"/>
									<label class="control-label">Saldo Pokok</label>	
								</div>
							</div>	

							<div class="col-md-6 ">
								<div class="form-group form-md-line-input">
									<input name="credits_account_last_balance_margin" id="credits_account_last_balance_margin" type="text" class="form-control" value="<?php echo nominal($acctcreditsaccount['credits_account_last_balance_margin']);?>" readonly>
									<label class="control-label">Saldo Margin</label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<?php
										$no = 0;
										$credits_payment_date = '';
										if(isset($acctcreditspayment)){
											foreach ($acctcreditspayment as $key=>$val){	
												$credits_payment_date = $val['credits_payment_date'];
												$no++;
											}
										}
										

										$date1 = new Datetime();
										$date2 = new Datetime($acctcreditsaccount['credits_account_date']);

										$diff = $date1->diff($date2);

										$months = (($diff->format('%y') * 12) + $diff->format('%m')) + 1;
									?>
									<input class="form-control" type="text" name="credits_payment_date" readonly id="credits_payment_date" value="<?php echo $credits_payment_date; ?>"/>
									<label class="control-label">Tanggal Angsuran Terakhir</label>	
								</div>
							</div>	

							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input name="credits_payment_total_count" id="credits_payment_total_count" type="text" class="form-control" value="<?php echo $no;?>" readonly>
									<label class="control-label">Angsuran Ke</label>
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input name="credits_payment_paid" id="credits_payment_paid" type="text" class="form-control" value="<?php echo $months;?>" readonly>
									<label class="control-label">Angsuran Seharusnya</label>
								</div>
							</div>
						</div>


						
					</div>
				
				 </div>
			</div>
		</div>
	</div>
</div>

	<div class="row">
		<div class="col-md-12">	
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						List Data Angsuran
					</div>
				</div>
				<div class="portlet-body ">
					<!-- BEGIN FORM-->
					<div class="form-body">
						<div class="table-responsive">
							<table class="table table-bordered table-advance table-hover" width="100%">
								<tbody>
									<tr>    
										<th width='5%' style='text-align:center'>Ke.</th>	
										<th>Tanggal Angsuran</th>		
										<th style='text-align:center;'>Angsuran Pokok</th>	
										<th style='text-align:center;'>Angsuran Margin</th>	
										<th style='text-align:center;'>Saldo Pokok</th>	
										<th style='text-align:center;'>Saldo Margin</th>	
									</tr>
									<?php
										$no = 1;
										if(isset($acctcreditspayment)){
											foreach($acctcreditspayment as $key => $val){
												echo"
													<tr>
														<td style='text-align:center'>".$no."</td>
														<td>".tgltoview($val['credits_payment_date'])."</td>
														<td style='text-align:right;'>".nominal($val['credits_payment_principal'])."</td>
														<td style='text-align:right;'>".nominal($val['credits_payment_margin'])."</td>
														<td style='text-align:right;'>".nominal($val['credits_principal_last_balance'])."</td>
														<td style='text-align:right;'>".nominal($val['credits_margin_last_balance'])."</td>
													</tr>
												";
												$no++;
											}
										}else{
											echo"
													<tr>
														<td colspan='6'> Data Tidak Ditemukan</td>
													</tr>
												";
										}
										
									?>			
									
								</tbody>
							</table>
						</div>
					</div>

					<BR>
					<BR>
					<div class="row">
						<div class="col-md-12 " style="text-align  : right !important;">
							<input type="submit" name="Preview" id="Preview" value="Preview" class="btn blue" title="Preview">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php echo form_close(); ?>