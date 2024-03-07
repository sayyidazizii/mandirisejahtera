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
	
	// select{
		// display: inline-block;
		// padding: 4px 6px;
		// margin-bottom: 0px !important;
		// font-size: 14px;
		// line-height: 20px;
		// color: #555555;
		// -webkit-border-radius: 3px;
		// -moz-border-radius: 3px;
		// border-radius: 3px;
	// }
	
	// label {
		// display: inline !important;
		// width:50% !important;
		// margin:0 !important;
		// padding:0 !important;
		// vertical-align:middle !important;
	// }
</style>
<div class="row-fluid">
	

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
			<a href="<?php echo base_url();?>CoreIdentity">
				Daftar Identitas
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Daftar Identitas <small>Kelola Identitas</small>
</h3>

<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>
	<div class="row">
		<div class="col-md-12">
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Daftar
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>CoreIdentity/addCoreIdentity" class="btn btn-default btn-sm">
							<i class="fa fa-plus"></i>
							<span class="hidden-480">
								Tambah Identitas Baru
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
								<th width="10%">Kode</th>
								<th width="15%">Nama</th>
								<th width="10%">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$no = 1;
								if(empty($coreidentity)){
									echo "
										<tr>
											<td colspan='8' align='center'>Data Kosong</td>
										</tr>
									";
								} else {
									foreach ($coreidentity as $key=>$val){									
										echo"
											<tr>			
												<td style='text-align:center'>$no.</td>
												<td>$val[identity_code]</td>
												<td>$val[identity_name]</td>
												<td>
													<a href='".$this->config->item('base_url').'CoreIdentity/editCoreIdentity/'.$val['identity_id']."' class='btn default btn-xs purple'>
														<i class='fa fa-edit'></i> Edit
													</a>
													<a href='".$this->config->item('base_url').'CoreIdentity/deleteCoreIdentity/'.$val['identity_id']."'class='btn default btn-xs red', onClick='javascript:return confirm(\"apakah yakin ingin dihapus ?\")'>
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