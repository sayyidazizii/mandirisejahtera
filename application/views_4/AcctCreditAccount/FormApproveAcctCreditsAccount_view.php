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
input:read-only {
		background-color: f0f8ff;
	}
</style>
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
			<a href="<?php echo base_url();?>credit-account/approving/<?php echo $this->uri->segment(3);?>">
				Persetujuan Pinjaman
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
<?php
	echo form_open('credit-account/process-approve',array('id' => 'myform', 'class' => 'horizontal-form'));
	$sesi 	= $this->session->userdata('unique');

	$token 	= $this->session->userdata('acctcreditsaccounttoken-'.$sesi['unique']);

	$member_address = $acctcreditsaccount['member_address']." ".$acctcreditsaccount['kecamatan_name']." ".$acctcreditsaccount['city_name']." ".$acctcreditsaccount['province_name'];
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			 <div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Form Persetujuan
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>credit-account" class="btn btn-default btn-sm">
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
									<td width="35%">No. Perjanjian Kredit</td>
									<td width="5%"> : </td>
									<td width="60%">
										<input class="easyui-textbox" type="text" name="credits_account_serial" readonly id="credits_account_serial" value="<?php echo $acctcreditsaccount['credits_account_serial']; ?>" style="width: 100%"/>

										<input type="hidden" name="credits_account_id" readonly id="credits_account_id" value="<?php echo $acctcreditsaccount['credits_account_id']; ?>"/>

										<input type="hidden" name="credits_id" readonly id="credits_id" value="<?php echo $acctcreditsaccount['credits_id']; ?>"/>
										
										<input type="hidden" class="easyui-textbox" name="credits_account_token" id="credits_account_token" autocomplete="off" value="<?php echo $token;?>"/>

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
										<input name="credits_account_amount" id="credits_account_amount_" type="text" class="easyui-textbox" value="<?php echo number_format($acctcreditsaccount['credits_account_amount'], 2);?>" style="width: 100%" readonly>
										<input name="credits_account_amount" id="credits_account_amount" type="hidden" class="form-control" value="<?php echo $acctcreditsaccount['credits_account_amount'];?>" style="width: 100%" readonly>

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
									<td width="35%">Angsuran Bunga</td>
									<td width="5%"> : </td>
									<td width="60%">
										<input class="easyui-textbox" type="text" name="credits_account_interest_amount" readonly id="credits_account_interest_amount" value="<?php echo number_format($acctcreditsaccount['credits_account_interest_amount'], 2); ?>" style="width: 100%"/>

									</td>
								</tr>
								<tr>
									<td width="35%">Jumlah Angsuran</td>
									<td width="5%"> : </td>
									<td width="60%">
										<input class="easyui-textbox" type="text" name="credits_account_payment_amount" readonly id="credits_account_payment_amount" value="<?php echo number_format($acctcreditsaccount['credits_account_payment_amount'], 2); ?>" style="width: 100%"/>

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
									<td>
										<input type="hidden" class="easyui-textbox" name="credits_account_adm_cost" id="credits_account_adm_cost" autocomplete="off" value="<?php echo $acctcreditsaccount['credits_account_adm_cost']; ?>"/>
										
										<input type="hidden" class="easyui-textbox" name="credits_account_provisi" id="credits_account_provisi" autocomplete="off" value="<?php echo $acctcreditsaccount['credits_account_provisi']; ?>"/>
										
										<input type="hidden" class="easyui-textbox" name="credits_account_komisi" id="credits_account_komisi" autocomplete="off" value="<?php echo $acctcreditsaccount['credits_account_komisi']; ?>"/>

										<input type="hidden" class="easyui-textbox" name="credits_account_insurance" id="credits_account_insurance" autocomplete="off" value="<?php echo set_value('credits_account_insurance',$acctcreditsaccount['credits_account_insurance']);?>"/>

										<input type="hidden" class="easyui-textbox" name="credits_account_materai" id="credits_account_materai" autocomplete="off" value="<?php echo set_value('credits_account_materai',$acctcreditsaccount['credits_account_materai']);?>"/>

										<input type="hidden" class="easyui-textbox" name="credits_account_risk_reserve" id="credits_account_risk_reserve" autocomplete="off" value="<?php echo set_value('credits_account_risk_reserve',$acctcreditsaccount['credits_account_risk_reserve']);?>"/>

										<input type="hidden" class="easyui-textbox" name="credits_account_stash" id="credits_account_stash" autocomplete="off" value="<?php echo set_value('credits_account_stash',$acctcreditsaccount['credits_account_stash']);?>"/>
									</td>
								</tr>
								<tr>
									
									<td>										
										<input type="hidden" class="easyui-textbox" name="credits_account_notaris" id="credits_account_notaris" autocomplete="off" value="<?php echo set_value('credits_account_notaris',$acctcreditsaccount['credits_account_notaris']);?>"/>
									</td>
									
									<td>										
									</td>
								</tr>
								<tr>
									
									<td>										
										<input type="hidden" class="easyui-textbox" name="credits_account_amount_received" id="credits_account_amount_received" autocomplete="off" value="<?php echo set_value('credits_account_amount_received',$acctcreditsaccount['credits_account_amount_received']);?>"/>
									</td>
								
									<td><input type="hidden" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser" name="credits_account_date" id="credits_account_date" autocomplete="off" value="<?php echo tgltoview($acctcreditsaccount['credits_account_date']); ?>" />
									</td>
									<input type="hidden" class="easyui-textbox" name="credits_account_notaris" id="credits_account_notaris" autocomplete="off" value="<?php echo set_value('credits_account_notaris',$acctcreditsaccount['credits_account_notaris']);?>"/>
								</tr>

								<tr>
									<td width="35%"></td>
									<td width="5%"></td>
									<td width="60%">
									<div class="row">
										<div class="col-md-12 " style="text-align  : right !important;">
											<input type="submit" name="Simpan" id="Simpan" value="Simpan" class="btn blue" title="Simpan">
										</div>
									</div>
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

<!-- 
	<div class="row">
		<div class="col-md-12">	
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						List Data Angsuran
					</div>
				</div>
				<div class="portlet-body ">
					<!-- BEGIN FORM
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
	</div> -->

<?php echo form_close(); ?>