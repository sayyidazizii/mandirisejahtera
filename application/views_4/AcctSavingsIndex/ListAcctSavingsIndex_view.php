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
			<a href="<?php echo base_url();?>AcctSavingsIndex">
				Perhitungan Index Simpanan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Perhitungan Index Simpanan
</h3>
<?php echo form_open('AcctSavingsIndex/processAddAcctSavingsIndex',array('id' => 'myform', 'class' => 'horizontal-form')); 

	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>	
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Perhitungan Index Simpanan
				</div>
			</div>
			<div class="portlet-body form">
				<div class="form-body">
					<div class="row">	
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="last_date" id="last_date" value="<?php echo date('d-m-Y');?>"/>
								<label class="control-label">Tanggal Diproses</label>
							</div>
						</div>					
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Pendapatan Bulan Ini</label>
								<input class="form-control"  type="text" name="income_amount" id="income_amount"/>
								
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-actions right">
							<button type="submit" name="Save" value="Save" id="Save" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Proses</i></button>
						</div>	
					</div>
						
				</div>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
<?php echo form_close(); ?>