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
			<a href="<?php echo base_url();?>account">
				Daftar Perkiraan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Daftar Perkiraan <small>Kelola Perkiraan</small>
</h3>
	<div class="row">
		<div class="col-md-12">
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Daftar
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>account/add" class="btn btn-default btn-sm">
							<i class="fa fa-plus"></i>
							<span class="hidden-480">
								Tambah Perkiraan Baru
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
								<th width="10%">No Perkiraan</th>
								<th width="20%">Nama Perkiraan</th>
								<th width="15%">Golongan Perkiraan</th>
								<th width="15%">Kelompok Perkiraan</th>
								<th width="10%">D/K</th>
								<th width="15%">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$no = 1;
								if(empty($acctaccount)){
									echo "
										<tr>
											<td colspan='11' align='center'>Emty Data</td>
										</tr>
									";
								} else {
									foreach ($acctaccount as $key=>$val){									
										echo"
											<tr>			
												<td style='text-align:center'>$no.</td>
												<td style='text-align:left'>".$val['account_code']."</td>
												<td style='text-align:left'>".$val['account_name']."</td>
												<td>".$val['account_group']."</td>
												<td>".$kelompokperkiraan[$val['account_type_id']]."</td>
												<td>".$accountstatus[$val['account_status']]."</td>
												
												<td>
													<a href='".$this->config->item('base_url').'account/edit/'.$val['account_id']."' class='btn default btn-xs purple'>
														<i class='fa fa-edit'></i> Edit
													</a>
													<a href='".$this->config->item('base_url').'account/delete/'.$val['account_id']."'class='btn default btn-xs red', onClick='javascript:return confirm(\"apakah yakin ingin dihapus ?\")'>
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