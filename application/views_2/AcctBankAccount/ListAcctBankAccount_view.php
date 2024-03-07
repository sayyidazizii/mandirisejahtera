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
	

</style>
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
			<a href="<?php echo base_url();?>AcctBankAccount">
				Daftar Kode Bank
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Daftar Kode Bank <small>Kelola Kode Bank</small>
</h3>
	<div class="row">
		<div class="col-md-12">
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Daftar
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>bank-account/add" class="btn btn-default btn-sm">
							<i class="fa fa-plus"></i>
							<span class="hidden-480">
								Tambah Kode Bank Baru
							</span>
						</a>
					</div>
				</div>
				<div class="portlet-body">
					<div class="form-body">
						<table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
						<thead>
							<tr>
								<th width="5%">No</th>
								<th width="10%">Kode Bank</th>
								<th width="20%">Nama Bank</th>
								<th width="15%">No. Rekening</th>
								<th width="15%">No Perkiraan</th>
								<th width="15%">D/K</th>
								<th width="10%">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$no = 1;
								if(empty($acctbankaccount)){
									echo "
										<tr>
											<td colspan='11' align='center'>Emty Data</td>
										</tr>
									";
								} else {
									foreach ($acctbankaccount as $key=>$val){									
										echo"
											<tr>			
												<td style='text-align:center'>$no.</td>
												<td style='text-align:left'>".$val['bank_account_code']."</td>
												<td style='text-align:left'>".$val['bank_account_name']."</td>
												<td>".$val['bank_account_no']."</td>
												<td>".$val['account_code']." - ".$val['account_name']."</td>
												<td>".$accountstatus[$val['account_status']]."</td>
												
												<td>
													<a href='".$this->config->item('base_url').'bank-account/edit/'.$val['bank_account_id']."' class='btn default btn-xs purple'>
														<i class='fa fa-edit'></i> Edit
													</a>
													<a href='".$this->config->item('base_url').'bank-account/delete/'.$val['bank_account_id']."'class='btn default btn-xs red', onClick='javascript:return confirm(\"apakah yakin ingin dihapus ?\")'>
														<i class='fa fa-trash-o'></i> Hapus
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