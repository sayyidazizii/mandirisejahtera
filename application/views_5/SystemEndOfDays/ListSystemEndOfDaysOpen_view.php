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
<script type="text/javascript">
	base_url = "<?php echo base_url();?>";

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
			<a href="<?php echo base_url();?>end-of-days/open-branch">
				Buka Cabang
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	
?>	
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Total Amount Kemarin
				</div>
			</div>

			<div class="portlet-body">
				<div class="form-body">
					<table class="table table-bordered table-hover table-full-width" >
						
					<?php if($endofdays['end_of_days_status'] == '0'){?>
						<tr>
							<td><b>Total Debit</b></td>
							<td><b><?php echo nominal($endofdays['debit_amount']) ?></b></td>
						</tr>
						<tr>
							<td><b>Total Kredit</b></td>
							<td><b><?php echo nominal($endofdays['credit_amount']) ?></b></td>
						</tr>
					<?php } ?>
					<tr>
						<td>
						<?php if($endofdays['end_of_days_status'] == '1'){?>
							<div class="alert alert-danger alert-dismissable">                 
								Anda belum menutup cabang !
							</div>
						<?php } ?>
						</td>
						<td align="right">
						<?php if($endofdays['end_of_days_status'] == '0'){?>
						
						<div class="row">
							<div class="col-md-12 " style="text-align  : right !important;">
							<?php	
							echo form_open('end-of-days/process-open-branch',array('id' => 'myform', 'class' => '')); ?>
								<button type="submit" name="process_open_branch" id="process_open_branch" onClick='javascript:return confirm(\"apakah yakin buka cabang sekarang ?\")' value="<?= $endofdays['end_of_days_id']?>" class="btn blue">Buka Cabang</button>	
							<?php echo form_close(); ?>
							</div>
						<?php } ?>
						</td>
					</tr>
				</div>			
			</div>
			
		</div>
		
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
	
</div>
<?php echo form_close(); ?>