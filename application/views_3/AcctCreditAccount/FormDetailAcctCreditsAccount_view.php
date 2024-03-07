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

margin: 0px; padding-top: 0px; padding-bottom: 0px; 

}
.textbox .textbox-text{
font-size: 12px;


}
</style>
<script>
	
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
			<a href="<?php echo base_url();?>credit-account/detail">
				Daftar Pinjaman
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>credit-account/show-detail/<?php echo $this->uri->segment(3);?>">
				Detail Pinjaman
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
<?php
	echo form_open('credit-account/process-printing'); 
	if(substr($acctcreditsaccount['credits_account_payment_to'], -1) == '*'){
		$angsuranke = $acctcreditsaccount['credits_account_payment_to'];
	}else{
		$angsuranke = substr($acctcreditsaccount['credits_account_payment_to'], -1);
	}
	$member_address = $acctcreditsaccount['member_address']." ".$acctcreditsaccount['kecamatan_name']." ".$acctcreditsaccount['city_name']." ".$acctcreditsaccount['province_name'];
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
						<a href="<?php echo base_url();?>credit-account/detail" class="btn btn-default btn-sm">
							<i class="fa fa-angle-left"></i>
							<span class="hidden-480">
								Kembali
							</span>
						</a>
					</div>
				</div>
			
				<div class="portlet-body">
					<div class="row">
						<div class="col-md-5">
							<table style="width: 100%;" border="0" padding="0">
								<tr>
									<td width="35%">No. Perjanjian Pinjaman</td>
									<td width="5%"> : </td>
									<td width="60%">
										<input class="easyui-textbox" type="text" name="credits_account_serial" readonly id="credits_account_serial" value="<?php echo $acctcreditsaccount['credits_account_serial']; ?>" style="width: 100%"/>

										<input type="hidden" name="credits_account_id" readonly id="credits_account_id" value="<?php echo $acctcreditsaccount['credits_account_id']; ?>"/>

									</td>
								</tr>
								<tr>
									<td width="35%">Nama Anggota</td>
									<td width="5%"> : </td>
									<td width="60%">
										<input class="easyui-textbox" type="text" name="member_name" readonly id="member_name" value="<?php echo $acctcreditsaccount['member_name']; ?>" style="width: 100%"/>

									</td>
								</tr>
								<tr>
									<td width="35%">Alamat Anggota</td>
									<td width="5%"> : </td>
									<td width="60%">
										<textarea class="easyui-textarea" row="3" name="member_address" readonly id="member_address" style="width: 100%"/><?php echo $member_address; ?></textarea>

									</td>
								</tr>
								
								<tr>
									<td width="35%">No. Identitas</td>
									<td width="5%"> : </td>
									<td width="60%">
										<input class="easyui-textbox" type="text" name="member_identity_no" readonly id="member_identity_no" value="<?php echo $acctcreditsaccount['member_identity_no']; ?>" style="width: 100%"/>

									</td>
								</tr>
								<tr>
									<td width="35%">Jenis Pinjaman</td>
									<td width="5%"> : </td>
									<td width="60%">
										<input name="credits_name" id="credits_name" type="text" class="easyui-textbox" value="<?php echo $acctcreditsaccount['credits_name'];?>" style="width: 100%" readonly>

									</td>
								</tr>
								<tr>
									<td width="35%">Tanggal Realisasi</td>
									<td width="5%"> : </td>
									<td width="60%">
										<input name="credits_account_date" id="credits_account_date" value="<?php echo tgltoview($acctcreditsaccount['credits_account_date']); ?>" type="text" class="easyui-textbox" style="width: 100%" readonly>

									</td>
								</tr>
								<tr>
									<td width="35%">Jangka Waktu</td>
									<td width="5%"> : </td>
									<td width="60%">
										<input name="credits_account_period" id="credits_account_period" value="<?php echo $acctcreditsaccount['credits_account_period'];?>" type="text" class="easyui-textbox" style="width: 100%" readonly>

									</td>
								</tr>
								<tr>
									<td width="35%">Tanggal Jatuh Tempo</td>
									<td width="5%"> : </td>
									<td width="60%">
										<input name="credits_account_due_date" id="credits_account_due_date" value="<?php echo tgltoview($acctcreditsaccount['credits_account_due_date']); ?>" type="text" class="easyui-textbox" style="width: 100%" readonly>

									</td>
								</tr>
							</table>
						</div>
						<div class="col-md-1"></div>
						<div class="col-md-5">
							<table style="width: 100%;" border="0" padding="0">
								<tr>
									<td width="35%">Jumlah Pinjaman</td>
									<td width="5%"> : </td>
									<td width="60%">
										<input name="credits_account_amount" id="credits_account_amount" type="text" class="easyui-textbox" value="<?php echo number_format($acctcreditsaccount['credits_account_amount'], 2);?>" style="width: 100%" readonly>

									</td>
								</tr>
								<tr>
									<td width="35%">Jenis Angsuran</td>
									<td width="5%"> : </td>
									<td width="60%">
										<input name="payment_type_id" id="payment_type_id" type="text" class="easyui-textbox" value="<?php echo $paymenttype[$acctcreditsaccount['payment_type_id']];?>" style="width: 100%" readonly>

									</td>
								</tr>
								<tr>
									<td width="35%">Angsuran Pokok</td>
									<td width="5%"> : </td>
									<td width="60%">
										<input class="easyui-textbox" type="text" name="credits_account_principal_amount" readonly id="credits_account_principal_amount" value="<?php echo number_format($acctcreditsaccount['credits_account_principal_amount'], 2); ?>" style="width: 100%"/>

									</td>
								</tr>
								<tr>
									<td width="35%">Prosentase Bunga </td>
									<td width="5%"> : </td>
									<td width="60%">
										<input class="easyui-textbox" type="text" name="credits_account_interest" readonly id="credits_account_interest" value="<?php echo $acctcreditsaccount['credits_account_interest']; ?>" style="width: 100%"/>

									</td>
								</tr>
								<tr>
									<td width="35%">Saldo Pokok </td>
									<td width="5%"> : </td>
									<td width="60%">
										<input class="easyui-textbox" type="text" name="credits_account_last_balance" readonly id="credits_account_last_balance" value="<?php echo number_format($acctcreditsaccount['credits_account_last_balance']); ?>" style="width: 100%"/>

									</td>
								</tr>
								<tr>
									<td width="35%">Tanggal Angsuran Terakhir</td>
									<td width="5%"> : </td>
									<td width="60%">
										<input class="easyui-textbox" type="text" name="credits_account_last_payment_date" readonly id="credits_account_last_payment_date" value="<?php echo tgltoview($acctcreditsaccount['credits_account_last_payment_date']); ?>" style="width: 100%"/>

									</td>
								</tr>
								<tr>
									<td width="35%">Angsuran Terakhir </td>
									<td width="5%"> : </td>
									<td width="60%">
										<input class="easyui-textbox" type="text" name="credits_account_payment_to" readonly id="credits_account_payment_to" value="<?php echo $angsuranke; ?>" style="width: 100%"/>

									</td>
								</tr>
								<tr>
									<td width="35%">Jumlah Sanksi </td>
									<td width="5%"> : </td>
									<td width="60%">
										<input class="easyui-textbox" type="text" name="credits_Account_accumulated_fines" readonly id="credits_Account_accumulated_fines" value="<?php echo number_format($acctcreditsaccount['credits_account_accumulated_fines'], 2); ?>" style="width: 100%"/>

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
										<th style='text-align:center;'>Angsuran Bunga</th>	
										<th style='text-align:center;'>Saldo Pokok</th>	
										<th style='text-align:center;'>Saldo Bunga</th>
										<th style='text-align:center;'>Sanksi</th>
										<th style='text-align:center;'>Ak Sanksi</th>
									</tr>
									<?php
										$no = 1;
										if($acctcreditspayment){
											foreach($acctcreditspayment as $key => $val){
												echo"
													<tr>
														<td style='text-align:center'>".$no."</td>
														<td>".tgltoview($val['credits_payment_date'])."</td>
														<td style='text-align:right;'>".nominal($val['credits_payment_principal'])."</td>
														<td style='text-align:right;'>".nominal($val['credits_payment_interest'])."</td>
														<td style='text-align:right;'>".nominal($val['credits_principal_last_balance'])."</td>
														<td style='text-align:right;'>".nominal($val['credits_interest_last_balance'])."</td>
														<td style='text-align:right;'>".nominal($val['credits_payment_fine'])."</td>
														<td style='text-align:right;'>".nominal($val['credits_payment_fine_last_balance'])."</td>
													</tr>
												";
												$no++;
											}
										}else{
											echo"
													<tr>
														<td colspan='8' align='center'> Pinjaman ini belum diangsur</td>
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