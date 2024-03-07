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

				alert("Apakah Anda Yakin Akan Memproses Bunga ??");
				return true;			
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
	Hasil Perhitungan Bunga Simpanan
</h3>
<?php 
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>	
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Hasil Perhitungan Bunga Simpanan
				</div>
			</div>
			<div class="portlet-body form">
				<div class="form-body">
					<div class="row">	
						<?php
							echo form_open('savings-profit-sharing/process-update');  ?>	
						<table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
						<thead>
							<tr>
								<th width="5%">No</th>
								<th width="10%">Periode Bunga</th>
								<th width="10%">No. Rekening</th>
								<th width="15%">Nama</th>
								<th width="15%">Alamat</th>
								<th width="12%">Saldo</th>
								<!-- <th width="15%">Bunga</th> -->
								<th width="10%">Pajak</th>
								<th width="10%">Bunga</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$no = 1;
								if(empty($acctsavingsprofitsharingtemp)){
									echo "
										<tr>
											<td colspan='11' align='center'>Bunga Belum Dihitung</td>
										</tr>
									";
								} else {
									foreach ($acctsavingsprofitsharingtemp as $key=>$val){
										$month = substr($val['savings_profit_sharing_temp_period'], 0, 2);	
										$year = substr($val['savings_profit_sharing_temp_period'], 2, 4);		
															
										echo"
											<tr>			
												<td style='text-align:center'>$no.</td>
												<td style='text-align:left'>".$monthname[$month]." ".$year."</td>
												<td style='text-align:left'>".$val['savings_account_no']."</td>
												<td style='text-align:left'>".$val['member_name']."</td>
												<td>".$val['member_address']."</td>
												<td style='text-align:right'>".number_format($val['savings_account_last_balance'], 2)."</td>
												<td style='text-align:right'>".number_format($val['savings_tax_temp_amount'], 2)."</td>
												<td style='text-align:right'>".number_format($val['savings_profit_sharing_temp_amount'], 2)."</td>
												
												
											</tr>
										";
										$no++;

										$period = $val['savings_profit_sharing_temp_period'];
									} 
								}
							// print_r($period);	
							?>
							</tbody>
							</table>
					</div>

					<div class="row">
						<div class="form-actions right">
							<!-- <a href="<?php echo base_url(); ?>AcctSavingsProfitSharingNew/viewindex/<?php echo $period; ?>" class="btn btn-md blue"><i class="fa fa-eye"></i> Lihat Index</a> -->
							<a href="<?php echo base_url(); ?>savings-profit-sharing/recalculate/<?php echo $period; ?>" class="btn btn-md red"><i class="fa fa-refresh"></i> Hitung Ulang</a>
							<button type="submit" name="Save" value="Save" id="Save" class="btn green-jungle" title="Simpan Data" ><i class="fa fa-check"> Proses</i></button>
						</div>	
					</div>
						
				</div>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
<?php echo form_close(); ?>