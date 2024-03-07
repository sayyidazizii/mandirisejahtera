<?php
	defined('BASEPATH') or exit('No direct script access allowed');
?>

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
			<a href="<?php echo base_url();?>AcctCreditsRescheduleReport">
				Daftar Reschedulling Pembiayaan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Daftar Reschedulling Pembiayaan<small> Kelola Daftar Reschedulling Pembiayaan</small>
</h3>
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>	
<?php	echo form_open('AcctCreditsRescheduleReport/processPrinting',array('id' => 'myform', 'class' => '')); 
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Pencarian
				</div>
				<!-- <div class="tools">
					<a href="javascript:;" class='expand'></a>
				</div> -->
			</div>
			<div class="portlet-body">
				<div class="form-body form">
					 <div class = "row">
						<div class = "col-md-4">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="<?php echo date('d-m-Y');?>" autocomplate="off"/>
								<label class="control-label">Tanggal Mulai
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<div class = "col-md-4">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="<?php echo date('d-m-Y');?>" autocomplate="off"/>
								<label class="control-label">Tanggal Akhir
									<span class="required">
										*
									</span>
								</label>
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
<?php echo form_close(); ?>
