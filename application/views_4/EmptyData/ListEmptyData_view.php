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
			<a href="<?php echo base_url();?>EmptyData">
				Kosongkan Data
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Kosongkan Data
</h3>
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>	
<?php	echo form_open('empty-data/process-empty-data',array('id' => 'myform', 'class' => '')); 
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
			</div>
			<div class="portlet-body">
				<div class="form-body form">
					 <div class = "row">
						<div class = "col-md-12">
							<center>
								<h2><div style="color: red; font-weight: bold">PERHATIAN !!</div></h2>
								<h3>Proses Ini Akan Menghapus Semua Data</h3>
								<button type="submit" class="btn green-jungle"><i class="fa fa-check"></i> Proses</button>
							</center>
						</div>
					</div>

					

					<!-- <div class="row">
						<div class="form-actions right">
							<button type="button" class="btn red" onClick="reset_search();"><i class="fa fa-times"></i> Reset</button>
							<button type="submit" class="btn green-jungle"><i class="fa fa-search"></i> Find</button>
						</div>	
					</div> -->
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
