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
			<a href="<?php echo base_url();?>savings-mandatory-hasnt-paid-report">
				Daftar tunggakan simpanan wajib
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Daftar tunggakan simpanan wajib<small> Kelola Daftar tunggakan simpanan wajib</small>
</h3>
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

?>	
<?php	echo form_open('savings-mandatory-hasnt-paid-report/viewreport',array('id' => 'myform', 'class' => '')); 
	$auth = $this->session->userdata('auth');

	$year_now 	= date('Y');

	for($i = ($year_now-3); $i<($year_now+3); $i++){
		$year[$i] = $i;
	} 
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Pencarian
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body form">
					 <div class = "row">
						<div class = "col-md-4">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="<?php echo date('d-m-Y');?>" autocomplate="off"/>
								<label class="control-label">Mulai Tanggal
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>
						<div class = "col-md-4">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="<?php echo date('d-m-Y');?>" autocomplate="off"/>
								<label class="control-label">Selesai Tanggal
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div><!-- 
						<div class = "col-md-4">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('month_period', $monthlist,set_value('month_period', date('m')),'id="month_period" class="form-control select2me"');
								?>
								<label>Periode</label>
							</div>						
						</div>
						<div class="col-md-4">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('year_period', $year,set_value('year_period', $year_now),'id="year_period" class="form-control select2me" ');
								?>
								<label>Tahun</label>
							</div>
						</div> -->
						<?php if($auth['branch_status'] == 1) { ?>
						<div class="col-md-4">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('branch_id', $corebranch,set_value('branch_id'),'id="branch_id" class="form-control select2me" ');
								?>
								<label>Cabang</label>
							</div>
						</div>
						<?php } ?>
					</div>

					

					<div class="row">
						<div class="form-actions right">
							<button type="submit" class="btn green-jungle" id="view" name="view" value="excel"><i class="fa fa-file-excel-o"></i> Export data</button>
							<button type="submit" class="btn green-jungle" id="view" name="view" value="pdf"><i class="fa fa-file-pdf-o"></i> Laporan Pdf</button>	
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
