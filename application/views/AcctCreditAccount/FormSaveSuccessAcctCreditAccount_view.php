<script src="<?php echo base_url(); ?>assets/global/scripts/moment.js" type="text/javascript"></script>
<style>
	th,
	td {
		padding: 3px;
	}

	td {
		font-size: 12px;
	}

	input:focus {
		background-color: 42f483;
	}

	.custom {

		margin: 0px;
		padding-top: 0px;
		padding-bottom: 0px;
		height: 50px;
		line-height: 50px;
		width: 50px;

	}

	.textbox .textbox-text {
		font-size: 10px;


	}

	input:read-only {
		background-color: f0f8ff;
	}
</style>

<?php
$sesi 	= $this->session->userdata('unique');
$data 	= $this->session->userdata('addcreditaccount-' . $sesi['unique']);

/*$total_credits_account = $acctcreditsaccount['credits_account_financing'];

	$rate = ($acctcreditsaccount['credits_account_margin'] / $total_credits_account)*100;*/


?>

<!-- BEGIN PAGE TITLE & BREADCRUMB-->

<!-- END PAGE TITLE & BREADCRUMB-->

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
						Input Data Baru Pinjaman
					</div>
					<div class="actions">
						<a href="<?php echo base_url(); ?>credit-account/add-form" class="btn btn-default btn-sm">
							<i class="fa fa-angle-left"></i>
							<span class="hidden-480">
								Kembali
							</span>
						</a>
					</div>
				</div>

				<div class="portlet-body">
					<div class="col-md-4">
						<table style="width: 100%;" border="0" padding:"0">
							<tbody>
								<tr>
									<td>No. Anggota</td>
									<td>:</td>
									<td><input type="text" class="form-control" name="member_no" id="member_no" autocomplete="off" readonly value="<?php echo $coremember['member_no']; ?>" tabindex="-1" />
										<input type="hidden" class="form-control" name="member_id" id="member_id" autocomplete="off" readonly value="<?php echo $coremember['member_id']; ?>" />
									</td>
									<td></td>
								</tr>
								<tr>
									<td>Nama Anggota</td>
									<td>:</td>
									<td><input type="text" class="form-control" name="member_nama" id="member_nama" autocomplete="off" readonly value="<?php echo $coremember['member_name']; ?>" tabindex="-1" /></td>
									<td></td>
								</tr>
								<tr>
									<td>Tanggal Lahir</td>
									<td>:</td>
									<td><input type="text" tabindex="-1" class="form-control" name="member_date_of_birth" id="member_date_of_birth" autocomplete="off" readonly value="<?php echo tgltoview($coremember['member_date_of_birth']); ?>" /></td>
									<td></td>
								</tr>
								<tr>
									<td>Jenis Kelamin</td>
									<td>:</td>
									<td><input type="text" tabindex="-1" class="form-control" name="member_gender" id="member_gender" autocomplete="off" readonly value="<?php echo $membergender[$coremember['member_gender']]; ?>" /></td>
									<td></td>
								</tr>
								<tr>
									<td>No. Telp</td>
									<td>:</td>
									<td><input type="text" tabindex="-1" class="form-control" name="member_phone1" id="member_phone1" autocomplete="off" readonly value="<?php echo $coremember['member_phone']; ?>" /></td>
									<td></td>
								</tr>
								<tr>
									<td>Alamat</td>
									<td>:</td>
									<td><textarea class="form-control" tabindex="-1" rows="3" id="comment" readonly><?php echo $coremember['city_name']; ?>, <?php echo $coremember['kecamatan_name']; ?>, <?php echo $coremember['member_address']; ?></textarea></td>
									<td></td>
								</tr>
								<!-- <tr>
									<td>Pekerjaan</td>
									<td>:</td>
									<td><input type="text" class="form-control" name="job_name" id="job_name" autocomplete="off" readonly value="<?php echo $coremember['member_company_job_title']; ?>"tabindex="-1"/></td>
									<td></td>
								</tr> -->
								<tr>
									<td>Nama Ibu</td>
									<td>:</td>
									<td><input type="text" class="form-control" name="member_mother" id="member_mother" autocomplete="off" readonly value="<?php
																																							echo $coremember['member_mother'];

																																							?>" tabindex="-1" /></td>
									<td></td>
								</tr>
								<tr>
									<td>No. Identitas</td>
									<td>:</td>
									<td><input type="text" class="form-control" name="member_identity_no" id="member_identity_no" autocomplete="off" readonly value="<?php echo $coremember['member_identity_no']; ?>" tabindex="-1" /></td>
									<td></td>
								</tr>

							</tbody>
						</table>
					</div>
					<div class="col-md-8">
						<table style="width: 100%;" border="0" padding:"0">
							<tbody>
								<!-- <tr>
									<td>Pola Angsuran</td>
									<td>:</td>
									<td><?php $urlagunan = base_url() . 'credit-account/angsuran/' . $acctcreditsaccount['credits_account_id']; ?><button type="button" class="btn btn-info btn-sm" onclick="popuplink('<?php echo $urlagunan; ?>');">Pola Angsuran</button>
									</td>
									<td></td>
									<td></td>
									<td></td>
								</tr> -->
								<tr>
									<td>Jenis Pinjaman</td>
									<td>:</td>
									<td> <?php
											$isi = $this->uri->segment(3);
											if ($isi > 0) {
												echo form_dropdown('credit_id', $creditid, set_value('credit_id', $acctcreditsaccount['credits_id']), 'id="credit_id" class="form-control select2me" disabled="disabled"');
											} else {
												echo form_dropdown('credit_id', $creditid, set_value('credit_id', $acctcreditsaccount['credits_id']), 'id="credit_id" class="form-control select2me" disabled="disabled"');
											}
											?>
									</td>
									<td>Sumber Dana</td>
									<td>:</td>
									<td><?php
										$isi = $this->uri->segment(3);
										if ($isi > 0) {
											echo form_dropdown('sumberdana', $sumberdana, set_value('sumberdana', $acctcreditsaccount['source_fund_id']), 'id="credit_id" class="form-control select2me" disabled="disabled"');
										} else {
											echo form_dropdown('sumberdana', $sumberdana, set_value('sumberdana', $acctcreditsaccount['source_fund_id']), 'id="credit_id" class="form-control select2me" disabled');
										}
										?></td>
								</tr>
								<tr>
									<td>Angsuran Tiap</td>
									<td>:</td>
									<td>
										<select name="deposito_account_due_date" class="easyui-combobox">
											<option value="Bulanan">Bulanan</option>
											<option value="Harian">Harian</option>
											<option value="Lain-lain">Lain-Lain</option>
										</select>
									</td>
									<td>Jenis Angsuran</td>
									<td>:</td>
									<td>
										<input type="text" class="easyui-textbox" name="credit_account_date" id="credit_account_date" value="<?php echo $paymenttype[$acctcreditsaccount['payment_type_id']]; ?>" autocomplete="off" readonly />
									</td>
								</tr>
								<tr>
									<td>Tanggal Realisasi</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" name="credit_account_date" id="credit_account_date" value="<?php echo tgltoview($acctcreditsaccount['credits_account_date']); ?>" autocomplete="off" readonly />
									</td>
									<td>Preferensi Angsuran</td>
									<td>:</td>
									<td>
										<input type="text" class="easyui-textbox" name="credit_account_date" id="credit_account_date" value="<?php echo $paymentpreference[$acctcreditsaccount['payment_preference_id']]; ?>" autocomplete="off" readonly />
									</td>
								</tr>
								<tr>
									<td>Angsuran Ke I</td>
									<td>:</td>
									<td>
										<input type="text" class="easyui-textbox" readonly name="credit_account_period" id="credit_account_period" value="<?php echo tgltoview($acctcreditsaccount['credits_account_due_date']); ?>" autocomplete="off" />
									</td>
									<td>Jangka waktu(Bulan)</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" readonly name="credit_account_period" id="credit_account_period" value="<?php echo $acctcreditsaccount['credits_account_period']; ?>" autocomplete="off" />
									</td>
								</tr>
								<td>Agunan</td>
								<td>:</td>
								<td> <a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#agunan">Agunan</a></td>
								<td>Jatuh Tempo</td>
								<td>:</td>
								<td><input type="text" class="easyui-textbox" name="credit_account_due_date" id="credit_account_due_date" value="<?php echo tgltoview($acctcreditsaccount['credits_account_due_date']); ?>" autocomplete="off" readonly /></td>
								<tr>
									<td>Ac. Officer</td>
									<td>:</td>
									<td><?php echo form_dropdown('office_id', $coreoffice, set_value('office_id', $acctcreditsaccount['office_id']), 'id="office_id" class="form-control select2me" disabled'); ?></td>
									<td>No Perjanjian Kredit</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" readonly name="credit_account_serial" id="credit_account_serial" value="<?php echo $acctcreditsaccount['credits_account_serial']; ?>" autocomplete="off" />
									</td>
								</tr>
								<tr>
									<td>Pinjaman</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" readonly name="credits_account_last_balance_principal_view" id="credits_account_last_balance_principal_view" autocomplete="off" value="<?php echo number_format($acctcreditsaccount['credits_account_amount']); ?>" />

									</td>
									<td>Bunga (%) Per Bulan</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" readonly name="credit_account_margin_view" id="credit_account_margin_view" autocomplete="off" value="<?php echo number_format($acctcreditsaccount['credits_account_interest'], 3); ?>" />

									</td>
								</tr>
								<tr>
									<td>Angsuran Pokok</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" readonly name="credits_account_principal_amount_view" id="credits_account_principal_amount_view" autocomplete="off" value="<?php echo number_format($acctcreditsaccount['credits_account_principal_amount']); ?>" />

									</td>
									<td>Angsuran Bunga</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" readonly name="credits_account_margin_amount_view" id="credits_account_margin_amount_view" autocomplete="off" value="<?php echo number_format($acctcreditsaccount['credits_account_interest_amount']); ?>" />

									</td>
								</tr>
								<tr>
									<td>Jumlah Angsuran</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" readonly name="credit_account_payment_amount_view" id="credit_account_payment_amount_view" autocomplete="off" value="<?php echo number_format($acctcreditsaccount['credits_account_payment_amount']); ?>" />

									</td>
								</tr>
								<tr>
									<td>Biaya Provisi</td>
									<td>:</td>
									<td>
										<input type="text" class="easyui-textbox" readonly name="credit_account_materai_view" id="credit_account_materai_view" value="<?php echo number_format($acctcreditsaccount['credits_account_provisi']); ?>" autocomplete="off" />
									</td>
									<td>Biaya Administrasi</td>
									<td>:</td>
									<td>
										<input type="text" class="easyui-textbox" readonly name="credit_account_adm_cost_view" id="credit_account_adm_cost_view" autocomplete="off" value="<?php echo number_format($acctcreditsaccount['credits_account_adm_cost']); ?>" />
									</td>
								</tr>

								<tr>
									<td>Terima Bersih</td>
									<td>:</td>
									<td>
										<input type="text" class="easyui-textbox" readonly name="credit_account_insurance_view" id="credit_account_insurance_view" autocomplete="off" value="<?php echo set_value('credits_account_amount_received', number_format($acctcreditsaccount['credits_account_amount_received'], 2)); ?>" />

									</td>
									<td>No Simpanan</td>
									<td>:</td>
									<td><?php echo form_dropdown('savings_account_id', $acctsavingsaccount, set_value('savings_account_id', $acctcreditsaccount['savings_account_id']), 'id="savings_account_id" class="form-control select2me" disabled'); ?>
									</td>

								</tr>
								<tr>
									<td>Nama Sales</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" readonly name="credit_account_adm_cost" id="credit_account_adm_cost" autocomplete="off" value="<?php echo $acctcreditsaccount['credits_account_sales_name']; ?>" /></td>
								</tr>
							</tbody>
						</table>
					</div>


					<div class="row">
						<div class="col-md-12" style='text-align:right'>
							<a href="<?php echo base_url() . 'credit-account/print-note/' . $acctcreditsaccount['credits_account_id']; ?>" class="btn green-jungle"><i class="fa fa-print"></i> Cetak Kwitansi</a>
							<a href="<?php echo base_url() . 'credit-account/add-form'; ?>" class="btn green-jungle">New</a>
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

					<!-- 	<div class="row">
					<div class="col-md-5">
					<?php echo form_open('credit-account/cek-pola-angsuran', array('id' => 'myform', 'class' => 'horizontal-form')); ?>
					<table width="50%">
						<input type="hidden" class="easyui-textbox" name="id_credit" value="<?php echo $this->uri->segment(3); ?>">
						<tr>
							<td width="5%"></td>
							<td width="20%"> 
							<input class="easyui-radiobutton" name="pola_angsuran" value="0" label="Flat" <?php if ($this->uri->segment(4) == '' || $this->uri->segment(4) == '0') {
																												echo 'checked';
																											} ?>><br>
							<input class="easyui-radiobutton" name="pola_angsuran" value="1" label="Sliding Rate" <?php if ($this->uri->segment(4) == '1') {
																														echo 'checked';
																													} ?>></td>
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
									<th width="20%">ANGSURAN JASA</th>
									<th width="10%">TOTAL ANGSURAN</th>
									<th width="10%">SISA POKOK</th>
								</tr>
							</thead>
							<tbody>
								<?php
								if ($datapola) {
									foreach ($datapola as $key => $val) {
										echo '
											<tr>
												<td width="5%">' . $val['ke'] . '</td>
												<td width="10%">' . number_format(abs($val['opening_balance'])) . '</td>
												<td width="20%">' . number_format(abs($val['angsuran_pokok'])) . '</td>
												<td width="25%">' . number_format(abs($val['angsuran_bunga'])) . '</td>
												<td width="10%">' . number_format(abs($val['angsuran'])) . '</th>
												<td width="10%">' . number_format(abs($val['last_balance'])) . '</td>
											</tr>
											';
									}
								} else {
									echo "<tr><td align='center' colspan='6'> Data Angsuran belum ada </td> </tr>";
								}
								?>
							</tbody>
						</table>
					</div>


				</div>
				<?php echo form_open('credit-account/print-pola-angsuran', array('id' => 'myform', 'class' => 'horizontal-form')); ?>
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
<div id="agunan" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Agunan</h4>
			</div>
			<div class="modal-body">
				<?php $this->load->view('AcctCreditAccount/FormShowCreditAgunan'); ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>

	</div>
</div>

<script type="text/javascript">
	function myformatter(date) {
		var y = date.getFullYear();
		var m = date.getMonth() + 1;
		var d = date.getDate();
		return (d < 10 ? ('0' + d) : d) + '-' + (m < 10 ? ('0' + m) : m) + '-' + y;
	}

	function myparser(s) {
		if (!s) return new Date();
		var ss = (s.split('-'));
		var y = parseInt(ss[0], 10);
		var m = parseInt(ss[1], 10);
		var d = parseInt(ss[2], 10);
		if (!isNaN(y) && !isNaN(m) && !isNaN(d)) {
			return new Date(d, m - 1, y);
		} else {
			return new Date();
		}
	}

	function popuplink(url) {
		window.open(url, "", "width=800,height=600");
	}
</script>

<?php echo form_close(); ?>