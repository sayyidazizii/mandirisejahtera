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
	$(document).ready(function(){
        $("#Save").click(function(){
        	var month 			= $("#month_period").val();
			var year 			= $("#year_period").val();

			
			if(month == ''){
				alert("Bulan masih kosong");
				return false;
			}else if(year == ''){
				alert("Tahun masih kosong");
				return false;
			}else {
				alert("Apakah Periode Bulan dan Tahun yang Anda Pilih Sudah Benar ??");
				return true;
			} 
			// else if(savings_account_id == ''){
			// 	alert("Rek. Simpanan masih kosong");
			// 	return false;
			// } 	
		});
    });
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
			<a href="<?php echo base_url();?>savings-profit-sharing">
				Perhitungan Bunga Simpanan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Perhitungan Bunga Simpanan
</h3>
<?php echo form_open('savings-profit-sharing/process-add',array('id' => 'myform', 'class' => 'horizontal-form')); 

	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
	$year_now 	=	date('Y');
	
	for($i = ($year_now-2); $i<($year_now+2); $i++){
		$year[$i] 	= $i;
	}

	$period 				= $this->AcctSavingsProfitSharingNew_model->getPeriodLog();
	$last_month 			= substr($period['period'],0,2);
	$next_month 			= $last_month + 1;
	
	if($last_month == 12){
		$next_month 		= 1;
	} else {
		$next_month 		= $last_month + 1;
	}

	if($next_month < 10){
		$next_period = '0'.$next_month;
	} else {
		$next_period = $next_month;
	}

	$data['year_period'] 	= $year_now;
	$data['month_period'] 	= $next_period;

	// print_r($next_period);
?>	
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Perhitungan Bunga Simpanan
				</div>
			</div>
			<div class="portlet-body form">
				<div class="form-body">
					<div class="row">	
						<div class="col-md-3">
							<div class="form-group form-md-line-input">
								<input type="text" name="month_period_name" id="month_period_name" class="form-control" value="<?php echo $month[$data['month_period']]; ?>" readonly>

								<input type="hidden" name="month_period" id="month_period" class="form-control" value="<?php echo $data['month_period']; ?>">
								<label class="control-label">Bulan</label>
							</div>
						</div>					
						<div class="col-md-3">
							<div class="form-group form-md-line-input">
								<?php echo form_dropdown('year_period', $year, set_value('year_period', $data['year_period']),'id="year_period" class="form-control select2me" ');?>
								<label class="control-label">Tahun</label>
								
							</div>
						</div>
						<!-- <div class="col-md-3">
							<div class="form-group form-md-line-input">
								
								<input class="form-control"  type="text" name="income_amount" id="income_amount"/>
								<label class="control-label">Pendapatan</label>
								
							</div>
						</div> -->
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								
								<input class="form-control"  type="text" name="savings_account_minimum" id="savings_account_minimum"/>
								<label class="control-label">Saldo Minimal</label>
								
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-actions right">
							<a href="<?php echo base_url(); ?>savings-profit-sharing/list-data" class="btn yellow"><i class="fa fa-bars"></i> Daftar Bunga</a>
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