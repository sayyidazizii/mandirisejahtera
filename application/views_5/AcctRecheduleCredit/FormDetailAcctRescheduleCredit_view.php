<script src="<?php echo base_url();?>assets/global/scripts/moment.js" type="text/javascript"></script>
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

<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			 <div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
					Data Baru Pembiayaan
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>" class="btn btn-default btn-sm">
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
								<table style="width: 100%;" border="0" padding="0" >
									<tbody>
										<tr>
											<td width="35%">No. Rek</td>
											<td width="5%">:</td>
											<td width="60%">
												<input type="text" disabled class="easyui-textbox" name="credits_account_serial" id="credits_account_serial" value="<?php echo set_value('credits_account_serial', $credit_account['credits_account_serial']);?>" style="width: 60%" readonly/> <a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#creditlist">Cari</a> 
											</td>
										</tr>
										<tr>
											<td width="35%">Pembiayaan</td>
											<td width="5%">:</td>
											<td width="60%">
												<input type="text" class="easyui-textbox" name="source_fund_id" id="source_fund_id" value="<?php echo set_value('credits_name', $credit_account['credits_name']);?>" style="width: 100%" disabled/>
											</td>
										</tr>
										<tr>
											<td width="35%">Nama</td>
											<td width="5%">:</td>
											<td width="60%">
												<input type="text" disabled class="easyui-textbox" name="member_name" id="member_name" value="<?php echo set_value('member_name', $credit_account['member_name']);?>" style="width: 70%" readonly/>
											</td>
										</tr>

										<tr>
											<td width="35%">Alamat</td>
											<td width="5%">:</td>
											<td width="60%">
												<?php echo form_textarea(array('rows'=>'2','name'=>'member_address','class'=>'easyui-textarea','id'=>'member_address','disabled'=>'disabled','value'=>$credit_account['member_address']))?>
													
												</td>
										</tr>
										<tr>
											<td width="35%">Kota</td>
											<td width="5%">:</td>
											<td width="60%">
												<input type="text" disabled class="easyui-textbox" name="city_name" id="city_name" value="<?php echo set_value('city_name', $credit_account['city_name']);?>" style="width: 100%" readonly/>
											</td>
										</tr>
										<tr>
											<td width="35%">Nama Ibu</td>
											<td width="5%">:</td>
											<td width="60%">
												<input type="text" disabled class="easyui-textbox" name="member_mother" id="member_mother" value="<?php echo $credit_account['member_mother'];?>" style="width: 100%" readonly/>
											</td>
										</tr>
										<tr>
											<td width="35%">No. Identitas</td>
											<td width="5%">:</td>
											<td width="60%">
												<input type="text" disabled class="easyui-textbox" name="member_identity_no" id="member_identity_no" value="<?php echo set_value('member_identity_no', $credit_account['member_identity_no']);?>" style="width: 100%" readonly/>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">Tanggal System</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" readonly class="easyui-textbox" name="credits_account_date_new" id="credits_account_date_new" value="<?php echo tgltoview($credit_account['credits_account_date']);?>" style="width: 100%" />
										</td>
									</tr>
									<tr>
										<td width="35%">Jangka Waktu</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" readonly class="easyui-textbox" name="credits_account_period_old" id="credits_account_period_old" value="<?php echo $credit_account['credits_account_period'];?>" style="width: 100%" />
										</td>
									</tr>
									<tr>
										<td width="35%">Jatuh Tempo</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="new_due_date" id="new_due_date" value="<?php echo tgltoview($credit_account['credits_account_due_date']);?>" style="width: 100%" />
										</td>
									</tr>
									<tr>
										<td width="35%">SLD POKOK</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" disabled class="easyui-textbox" name="credits_account_last_balance_principal_old_view" id="credits_account_last_balance_principal_old_view" value="<?php echo number_format($credit_account['credits_account_last_balance'], 2);?>" style="width: 100%" />
										</td>
									</tr>
									<tr>
										<td width="35%">SLD MARGIN</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" disabled class="easyui-textbox" name="credits_account_last_balance_margin_old_view" id="credits_account_last_balance_margin_old_view" value="<?php echo number_format($credit_account['credits_account_interest'], 2);?>" style="width: 100%" />
										</td>
									</tr>
									<tr>
										<td width="35%">Angsuran Pokok</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="new_credits_account_principal_amount_view" id="new_credits_account_principal_amount_view" value="<?php echo number_format($credit_account['credits_account_principal_amount'], 2);?>" disabled />
										</td>
									</tr>
									<tr>
										<td width="35%">Angsuran Margin</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="new_credits_account_margin_amount_view" id="new_credits_account_margin_amount_view" value="<?php echo number_format($credit_account['credits_account_interest_amount'], 2);?>" style="width: 100%" disabled/>
										</td>
									</tr>
									<tr>
										<td width="35%">Jumlah Angsuran</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="new_credits_account_payment_amount_view" id="new_credits_account_payment_amount_view" value="<?php echo number_format($credit_account['credits_account_payment_amount'], 2);?>" style="width: 100%" disabled/>
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
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Pola Angsuran
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
				
					<div class="row">
						<!-- <div class="col-md-5">
							<?php echo form_open('AcctRescheduleCredit/cekPolaAngsuran',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
							<table width="50%">
								<input type="hidden" class="easyui-textbox" name="id_credit" value="<?php echo $this->uri->segment(3); ?>">
								<tr>
									<td width="5%"></td>
									<td width="20%"> 
									<input class="easyui-radiobutton" name="pola_angsuran" value="0" label="Flat" <?php if($this->uri->segment(4) == '' || $this->uri->segment(4) == '0'){ echo 'checked'; } ?>><br>
									<input class="easyui-radiobutton" name="pola_angsuran" value="1" label="Sliding Rate" <?php if($this->uri->segment(4) == '1'){ echo 'checked'; } ?>></td>
								</tr><tr>
									<td width="5%"></td>
									<td width="20%"> <button type="submit" name="Save" value="Save" id="Save" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Cek Pola Angsuran</i></button></td>
								</tr>
							</table>
							<?php echo form_close(); ?>
						</div> -->
					
						<div class="col-md-12">
							<table class="table" style="width: 100%;" border="0" padding:"0">
							<thead>
								<tr>
									<th width="5%">Ke</th>
									<th width="10%">SALDO POKOK</th>
									<th width="20%">ANGSURAN POKOK</th>
									<th width="20%">ANGSURAN BUNGA</th>
									<th width="10%">TOTAL ANGSURAN</th>
									<th width="10%">SISA POKOK</th>
								</tr>
							</thead>
								<tbody>
									<?php
										foreach($datapola as $key=>$val){
											echo'
											<tr>
												<td width="5%">'.$val['ke'].'</td>
												<td width="10%">'.number_format(abs($val['opening_balance']), 2).'</td>
												<td width="20%">'.number_format(abs($val['angsuran_pokok']), 2).'</td>
												<td width="25%">'.number_format(abs($val['angsuran_bunga']), 2).'</td>
												<td width="10%">'.number_format(abs($val['angsuran']), 2).'</th>
												<td width="10%">'.number_format(abs($val['last_balance']), 2).'</td>
											</tr>
											';
											
										}
									?>
								</tbody>
								</table>
						</div>				
					</div>
					<?php echo form_open('AcctRescheduleCredit/printPolaAngsuran',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
					<input type="hidden" class="easyui-textbox" name="id_credit" value="<?php echo $this->uri->segment(3); ?>">
					<input type="hidden" class="easyui-textbox" name="pola" value="<?php echo $this->uri->segment(4); ?>">
					
					<div class="row">
						<div class="col-md-12 " style="text-align  : right !important;">
							<input type="submit" name="Preview" id="Preview" value="Print" class="btn blue" title="Print">
						</div>
					</div>
					<?php echo form_close(); ?>
					</div>
				</div>
				<!-- END EXAMPLE TABLE PORTLET-->
			</div>
		</div>
	</div>
