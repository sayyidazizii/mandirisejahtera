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
			<a href="<?php echo base_url();?>CoreCertificateOfInvestorReport">
				Sertifikat Pemodal
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Sertifikat Pemodal
</h3>
<!-- <?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$sesi=$this->session->userdata('filter-acctnominativedepositoreport');

	if(!is_array($sesi)){
		$sesi['start_date']					= date('Y-m-d');
		$sesi['kelompok_laporan_simpanan_berjangka']	= 0;
	}
?>	
<?php	echo form_open('CoreCertificateOfInvestorReport/filter',array('id' => 'myform', 'class' => '')); 

	$start_date			= $sesi['start_date'];
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
						<div class = "col-md-4">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="<?php echo tgltoview($start_date);?>"/>
								<label class="control-label">Tanggal
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>


						<div class="col-md-4">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('kelompok_laporan_simpanan_berjangka', $kelompoklaporansimpananberjangka,set_value('kelompok_laporan_simpanan_berjangka',$sesi['kelompok_laporan_simpanan_berjangka']),'id="kelompok_laporan_simpanan_berjangka" class="form-control select2me"');
								?>
								<label>Kelompok</label>
							</div>
						</div>
					</div>

					

					<div class="row">
						<div class="form-actions right">
							<button type="button" class="btn red" onClick="reset_search();"><i class="fa fa-times"></i> Reset</button>
							<button type="submit" class="btn green-jungle"><i class="fa fa-search"></i> Find</button>
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?> -->
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Daftar Anggota
				</div>
			</div>
			
			<div class="portlet-body">
				<div class="form-body">
					<table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
					<thead>
						<tr>
							<th width="5%">No</th>
							<th width="15%">No. Anggota</th>
							<th width="20%">Nama</th>
							<th width="25%">Alamat</th>
							<th width="15%">Pekerjaan</th>
							<th width="10%">Nominal</th>
							<th width="5%"></th>
						</tr>
					</thead>
					<tbody>
						<?php
							$no = 1;
							if(empty($coremember)){
								echo "
									<tr>
										<td colspan='7' align='center'>Emty Data</td>
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
											<td>".$val['member_job']."</td>
											<td style='text-align:right'>".number_format($val['member_special_savings'])."</td>";
											echo "
												<td>
																
													<a href='".$this->config->item('base_url').'CoreCertificateOfInvestorReport/addCertificatOfInvestorReport/'.$val['member_id']."'class='btn default btn-xs blue'>
														<i class='fa fa-plus'></i> Pilih
													</a>
												</td>
											";
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