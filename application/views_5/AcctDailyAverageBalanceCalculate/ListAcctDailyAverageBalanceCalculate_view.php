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
			<a href="<?php echo base_url();?>AcctDailyAverageBalanceCalculate">
				Perhitungan Saldo Rata - Rata Harian Akhir Bulan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Perhitungan Saldo Rata - Rata Harian Akhir Bulan
</h3>
<?php echo form_open('AcctDailyAverageBalanceCalculate/processAddAcctDailyAverageBalanceCalculate',array('id' => 'myform', 'class' => 'horizontal-form')); 

	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>	
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Perhitungan Saldo Rata - Rata Harian Akhir Bulan
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
					<div class="row">						
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="daily_average_balance_calculate_date" id="daily_average_balance_calculate_date" value="<?php echo date('d-m-Y');?>"/>
								<label class="control-label">Tanggal Diproses</label>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<?php echo form_dropdown('savings_id', $acctsavings, set_value('savings_id'),'id="savings_id" class="form-control select2me" ');?>
								<label class="control-label">Jenis Simpanan</label>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12" style='text-align:right'>
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