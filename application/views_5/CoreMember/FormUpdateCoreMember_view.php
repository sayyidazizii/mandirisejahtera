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
	
	/* // select{
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
	// } */
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
			<a href="<?php echo base_url();?>member/update-status">
				Update Calon Anggota
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Update Calon Anggota <small>Kelola Calon Anggota</small>
</h3>
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
						<table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
						<thead>
							<tr>
								<th width="5%">No</th>
								<th width="10%">No Anggota</th>
								<th width="15%">Nama</th>
								<th width="20%">Alamat</th>
								<th width="15%">Jenis Kelamain</th>
								<th width="15%">Status</th>
								<th width="15%">No. Telp</th>
								<!-- <th width="15%">Simpanan Pokok</th>
								<th width="15%">Simpanan Khusus</th>
								<th width="15%">Simpanan Wajib</th> -->
								<th width="10%">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$no = 1;
								if(empty($coremember)){
									echo "
										<tr>
											<td colspan='11' align='center'>Emty Data</td>
										</tr>
									";
								} else {
									foreach ($coremember as $key=>$val){									
										echo"
											<tr>			
												<td style='text-align:center'>$no.</td>
												<td>".$val['member_no']."</td>
												<td>".$val['member_name']."</td>
												<td>".$val['member_address']."</td>
												<td>".$membergender[$val['member_gender']]."</td>
												<td>".$memberstatus[$val['member_status']]."</td>
												<td>".$val['member_phone']."</td>
												
												<td>
													<a href='".$this->config->item('base_url').'member/edit/'.$val['member_id']."' class='btn default btn-xs purple'>
														<i class='fa fa-edit'></i> Edit
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