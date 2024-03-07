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

<script>
	function reset_search(){
		document.getElementById("start_date").value = "<?php echo date('d-m-Y'); ?>";
	}
</script>
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
			<a href="<?php echo base_url();?>ledger-report/cash-teller-report">
				Laporan Arus Kas Harian
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Laporan Arus Kas Harian<small> Kelola Laporan Arus Kas Harian</small>
</h3>
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$auth 	= $this->session->userdata('auth');
?>	
<?php	echo form_open('ledger-report/process-printing-teller-report',array('id' => 'myform', 'class' => '')); 
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
								<label class="control-label">Tanggal
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<?php if($auth['branch_status'] == 1) { ?>
						<div class="col-md-6">
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
