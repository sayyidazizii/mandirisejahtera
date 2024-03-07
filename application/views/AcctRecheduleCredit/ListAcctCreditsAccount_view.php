<style>

	th{
		font-size:14px  !important;
		font-weight: bold !important;
		text-align:center !important;
		margin : 0 auto;
		vertical-align:middle !important;
	}
	td{
		font-size:12px  !important;
		font-weight: normal !important;
	}

	.flexigrid div.pDiv input {
		vertical-align:middle !important;
	}
	
	.flexigrid div.pDiv div.pDiv2 {
		margin-bottom: 10px !important;
	}
	

</style>

<script>
	base_url = '<?php echo base_url();?>';
	function reset_search(){
		document.location = base_url+"credit-account/reset-search";
	}
</script>

<div class="row-fluid">
	<?php
		echo $this->session->userdata('message');
		$this->session->unset_userdata('message');
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
			<a href="<?php echo base_url();?>credit-account/add-form">
				Rekening Pinjaman
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Histori Angsuran Pinjaman <small>Kelola Data Rekening Pinjaman</small>
</h3>

<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$sesi=$this->session->userdata('filter-AcctCreditsAccount');

	if(!is_array($sesi)){
		$sesi['start_date']			= date('Y-m-d');
		$sesi['end_date']			= date('Y-m-d');
		$sesi['member_id']			= '';
		$sesi['credits_id']			= '';
	}
?>	
<?php	echo form_open('credit-account/filter',array('id' => 'myform', 'class' => '')); 

	$start_date			= $sesi['start_date'];
	$end_date			= $sesi['end_date'];
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Pencarian
				</div>
				<div class="tools">
					<a href="javascript:;" class='expand'></a>
				</div>
			</div>
			<div class="portlet-body display-hide">
				<div class="form-body form">
					 <div class = "row">
						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="<?php echo tgltoview($start_date);?>"/>
								<label class="control-label">Tanggal Awal
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="<?php echo tgltoview($end_date);?>"/>
								<label class="control-label">Tanggal Akhir
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>
					</div>

					 <div class = "row">
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('member_id', $coremember,set_value('member_id',$sesi['member_id']),'id="member_id" class="form-control select2me"');
								?>
								<label>Anggota</label>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('credits_id', $acctcredits, set_value('credits_id', $sesi['credits_id']),'id="credits_id" class="form-control select2me" ');
								?>
								<label>Jenis Pinjaman</label>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-actions right">
							<button type="button" class="btn red" onClick="reset_search();"><i class="fa fa-times"></i> Batal</button>
							<button type="submit" class="btn green-jungle"><i class="fa fa-search"></i> Cari</button>
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
	<div class="row">
		<div class="col-md-12">
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Daftar
					</div>
				</div>
				<div class="portlet-body">
					<div class="form-body">
						<table class="table table-striped table-bordered table-hover table-full-width" id="sample_1">
						<thead>
							<tr>
								<th width="5%">No</th>
								<th width="10%">No Anggota</th>
								<th width="20%">Nama</th>
								<th width="25%">Alamat</th>
								<th width="10%">Pinjaman</th>
								<th width="10%">No Akad</th>
								<th width="10%">Tanggal Akad</th>
								<th width="10%">Aksi</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$no = 1;
								if(empty($acctcreditsaccount)){
									echo "
										<tr>
											<td colspan='11' align='center'>Data Kosong</td>
										</tr>
									";
								} else {
									foreach ($acctcreditsaccount as $key=>$val){									
										echo"
											<tr>			
												<td style='text-align:center'>$no.</td>
												<td>".$val['member_no']."</td>
												<td>".$val['member_name']."</td>
												<td>".$val['member_address']." ".$val['kecamatan_name']." ".$val['city_name']." ".$val['province_name']."</td>
												<td>".$val['credits_name']."</td>
												<td>".$val['credits_account_serial']."</td>
												<td>".tgltoview($val['credits_account_date'])."</td>
												
												
												<td>
													<a href='".$this->config->item('base_url').'credit-account/show-detail/'.$val['credits_account_id']."' class='btn default btn-xs yellow-lemon'>
														<i class='fa fa-bars'></i> Detil
													</a>
												</td>
											</tr>
										";
										$no++;
									} 
								}
								
							?>
							</tbody>
							</table>
						</div>
						</div>
					</div>
					<!-- END EXAMPLE TABLE PORTLET-->
				</div>
			</div>
<?php echo form_close(); ?>