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
			<a href="<?php echo base_url();?>member/print-book">
				Cetak Buku Anggota
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Cetak Buku Anggota
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
								<th width="3%">No</th>
								<th width="6%">No Anggota</th>
								<th width="10%">Nama</th>
								<th width="15%">Alamat</th>
								<th width="8%">Status</th>
								<th width="8%">Sifat</th>
								<th width="8%">No. Telp</th>
								<!-- <th width="10%">Simp Pokok</th>
								<th width="10%">Simp Wajib</th> -->
								<th width="15%">Action</th>
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
												<td>".$memberstatus[$val['member_status']]."</td>
												<td>".$membercharacter[$val['member_character']]."</td>
												<td>".$val['member_phone']."</td>";

													// if($val['member_principal_savings'] <> 0 && $val['member_status'] == 0){
														echo "
															<td>
																
																<a href='".$this->config->item('base_url').'member/process-print-book/'.$val['member_id']."'class='btn default btn-xs blue'>
																	<i class='fa fa-print'></i> Cetak Cover
																</a>
	
															</td>
														";
													// } else {
													// 	echo "<td></td>";
													// }
											

											echo "
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