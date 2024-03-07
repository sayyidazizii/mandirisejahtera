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
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Cetak Sertifikat Pemodal <?php echo $this->CoreCertificateOfInvestorReport_model->getMemberName($this->uri->segment(3)); ?>
				</div>
			</div>
			

			<div class="portlet-body">
				<div class="form-body">

					<?php	echo form_open('CoreCertificateOfInvestorReport/processPrinting',array('id' => 'myform', 'class' => ''));  ?>

					<div class="row">						
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Pengurus</label>
								<input class="form-control"  type="text" name="pengurus" id="pengurus"/>
								<input class="form-control"  type="hidden" name="member_id" id="member_id" value="<?php echo $this->uri->segment(3); ?>" readonly/>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Pengelola</label>
								<input class="form-control"  type="text" name="pengelola" id="pengelola"/>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12" style='text-align:right'>
							<button type="submit" name="Save" value="Save" id="Save" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Cetak</i></button>
						</div>	
					</div>
				</div>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
<?php echo form_close(); ?>