<style>
	th, td {
	  padding: 3px;
	}
	input:focus { 
	  background-color: 42f483;
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
			<a href="<?php echo base_url();?>AcctSavingsProfitSharingReport">
				Daftar Bagi Hasil Simpanan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->


<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Daftar Bagi Hasil Simpanan
				</div>
			</div>
			<div class="portlet-body">
				<?php
					echo form_open('AcctSavingsProfitSharingReport/processPrinting'); 
				?>
				<div class="portlet-body">
					<div class="form-body">
						<table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
						<thead>
							<tr>
								<th style="text-align: center; width: 5%">No</th>
								<th style="text-align: center; width: 15%">No. Rek</th>
								<th style="text-align: center; width: 15%">Nama</th>
								<th style="text-align: center; width: 8%">Sandi</th>
								<th style="text-align: center; width: 15%">Nominal</th>
								<th style="text-align: center; width: 15%">Saldo</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$no = 1;
								if(empty($acctsavingsprofitsharing)){
									echo "
										<tr>
											<td colspan='8' align='center'>Emty Data</td>
										</tr>
									";
								} else {
									foreach ($acctsavingsprofitsharing as $key=>$val){									
										echo"
											<tr>			
												<td style='text-align:center'>$no.</td>
												<td>".$val['savings_account_no']."</td>
												<td>".$val['member_name']."</td>
												<td>".$this->AcctSavingsProfitSharingReport_model->getMutationCode($preference['savings_profit_sharing_id'])."</td>
												<td style='text-align:right'>".number_format($val['savings_profit_sharing_amount'], 2)."</td>
												<td style='text-align:right'>".number_format($val['savings_account_last_balance'], 2)."</td>
											</tr>
										";
										$no++;
									} 
								}
								
							?>
							</tbody>
						</table>
						<div class="row">
							<div class="col-md-12 " style="text-align  : right !important;">
								<input type="submit" name="Preview" id="Preview" value="Preview" class="btn blue" title="Preview">
								<!-- <a href='javascript:void(window.open("<?php echo base_url(); ?>acctaccountbalance/exportInvtItemStock","_blank","top=100,left=200,width=300,height=300"));' title="Export to Excel"> Export Data  <img src='<?php echo base_url(); ?>img/Excel.png' height="32" width="32"></a> -->	
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- END EXAMPLE TABLE PORTLET-->
		</div>
	</div>
</div>
<?php echo form_close(); ?>