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
<!-- <script type="text/javascript">
	base_url = "<?php echo base_url();?>";

	function reset_search(){
		document.location = base_url +"AcctMemorialJournal/reset_search";
	}
</script> -->
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
			<a href="<?php echo base_url();?>AcctMemorialJournal">
				Daftar Jurnal Memorial
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
					Daftar Jurnal Memorial
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
					<table class="table table-bordered table-hover table-full-width" >
					<thead>
						<tr>
							<th width="5%">No</th>
							<th width="6%">Bukti</th>
							<th width="20%">Uraian</th>
							<th width="10%">Tanggal</th>
							<!-- <th width="10%">Nomor</th> -->
							<th width="10%">No. Per</th>
							<th width="15%">Perkiraan</th>
							<th width="10%">Debit</th>
							<th width="10%">Kredit</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$no = 1;
							if(empty($acctmemorialjournal)){
								echo "
									<tr>
										<td colspan='8' align='center'>Emty Data</td>
									</tr>
								";
							} else {
								foreach ($acctmemorialjournal as $key=>$val){
									$id = $this->AcctMemorialJournal_model->getMinID($val['journal_voucher_id']);

									if($val['journal_voucher_item_id'] == $id){
										echo"
											<tr>			
												<td style='text-align:center; background-color:lightgrey'>$no.</td>
												<td style='text-align:left; background-color:lightgrey'>".$val['transaction_module_code']."</td>
												<td style='text-align:left; background-color:lightgrey'>".$val['journal_voucher_description']."</td>
												<td style='text-align:center; background-color:lightgrey'>".tgltoview($val['journal_voucher_date'])."</td>
												<td style='text-align:left; background-color:lightgrey'>".$val['account_code']."</td>
												<td style='text-align:left; background-color:lightgrey'>".$val['account_name']."</td>
												<td style='text-align:right; background-color:lightgrey'>".number_format($val['journal_voucher_debit_amount'], 2 )."</td>
												<td style='text-align:right; background-color:lightgrey'>".number_format($val['journal_voucher_credit_amount'], 2)."</td>
											</tr>
										";
										$no++;
									} else {
										echo"
											<tr>			
												<td style='text-align:center'></td>
												<td></td>
												<td></td>
												<td></td>
												<td>&nbsp;&nbsp;&nbsp;&nbsp;".$val['account_code']."</td>
												<td>&nbsp;&nbsp;&nbsp;&nbsp;".$val['account_name']."</td>
												<td style='text-align:right'>".number_format($val['journal_voucher_debit_amount'], 2 )."</td>
												<td style='text-align:right'>".number_format($val['journal_voucher_credit_amount'], 2)."</td>
											</tr>
										";
									}									
									
									
								} 
							}
							
						?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
<?php echo form_close(); ?>