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

	.flexigrid div.pDiv input {
		vertical-align:middle !important;
	}
	
	.flexigrid div.pDiv div.pDiv2 {
		margin-bottom: 10px !important;
	}
	

</style>

<script>
	base_url = '<?php echo base_url();?>';

	function reset_search(){
		document.location = base_url+"general-ledger-report/reset-search";
	}
	
	function openform(){
		var a = document.getElementById("passwordf").style;
		if(a.display=="none"){
			a.display = "block";
		}else{
			a.display = "none";
		}
		// document.getElementById("code").style.display = "block";
		// document.getElementById("name").style.display = "block";
	}
</script>
<?php

	$option	= array(0 =>"Header", 1=>"Detail");

	$sesi	= 	$this->session->userdata('filter-AcctGeneralLedgerReport');
			if(!is_array($sesi)){
				$sesi['start_date']				= date("Y-m-d");
				$sesi['end_date']				= date("Y-m-d");
				$sesi['account_id']				= '';
			}
?>

		<div class = "page-bar">
			<ul class="page-breadcrumb">
				<li>
					<a href="<?php echo base_url();?>">
						Beranda
					</a>
					<i class="fa fa-angle-right"></i>
				</li>
				<li>
					<a href="<?php echo base_url();?>AcctGeneralLedgerReport">
						Laporan Buku Besar
					</a>
					<i class="fa fa-angle-right"></i>
				</li>
			</ul>
		</div>
		<h3 class="page-title">
			Laporan Buku Besar
		</h3>

 <div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					List
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
					<?php
						echo form_open('general-ledger-report/process-printing'); 
						if(empty($sesi['account_id'])){
							$sesi['account_id']='';
						}
					?>
					<div class = "row">
							<div class = "col-md-6">
								<div class="form-group form-md-line-input">
									<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date"  value="<?php echo tgltoview($sesi['start_date']);?>"/>
									<label class="control-label">Tanggal Mulai
										<span class="required">
											*
										</span>
									</label>
								</div>
							</div>

							<div class = "col-md-6">
								<div class="form-group form-md-line-input">
									<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date"  value="<?php echo tgltoview($sesi['end_date']);?>"/>
									<label class="control-label">Tanggal Akhir
										<span class="required">
											*
										</span>
									</label>
								</div>
							</div>

					</div>
					<div class = "row">
						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('account_id', $acctaccount, set_value('account_id', $sesi['account_id']),'id="account_id" class="form-control select2me"');
								?>
								<label class="control-label">Nama Account</label>
							</div>
						</div>
					</div>

					<!--
					<table class="table table-striped table-bordered table-hover table-checkable order-column" id="sample_1">
						<thead>
							<tr>
								<th width="5%" >No</th>
								<th width="10%">No. Invoice Penjualan</th>
								<th width="8%">Tanggal Invoice Penjualan</th>
								<th width="15%">Nama Pelanggan</th>
								<th width="15%">Nama Barang</th>
								<th width="8%">Satuan</th>
								<th width="7%">Qty</th>
								<th width="8%">Harga</th>
								<th width="8%">Subtotal</th>
								<th width="10%">Diskon</th>
								<th width="10%">Subtotal St Diskon</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$no=1;
								foreach ($AcctGeneralLedgerReport as $key=>$val){			
									echo"
										<tr>									
											<td>".$no."</td>
											<td>".$val['sales_invoice_no']."</td>
											<td>".tgltoview($val['sales_invoice_date'])."</td>
											<td>".$val['customer_name']."</td>
											<td>".$val['item_name']."</td>
											<td>".$val['item_unit_code']."</td>
											<td style='text-align:right'>".number_format($val['quantity'], 2)."</td>
											<td style='text-align:right'>".number_format($val['item_unit_price'], 2)."</td>
											<td style='text-align:right'>".number_format($val['subtotal_amount'], 2)."</td>
											<td style='text-align:right'>".number_format($val['discount_percentage'], 2)." % / ".number_format($val['discount_amount'], 2)."</td>
											<td style='text-align:right'>".number_format($val['subtotal_amount_after_discount'], 2)."</td>
											
										</tr>
									";
									$no++;
								} 
							?>
						</tbody>
					</table> 
				</div> -->
				<div class="row">
					<div class="col-md-12 " style="text-align  : right !important;">
						<input type="submit" name="Preview" id="Preview" value="Preview" class="btn blue" title="Preview">
						<!-- <a href='javascript:void(window.open("<?php echo base_url(); ?>AcctGeneralLedgerReport/export","_blank","top=100,left=200,width=300,height=300"));' title="Export to Excel" class="btn green-jungle"><i class="fa fa-download"></i> Export Data</a> -->
			
					</div>
				</div>
			</div>
		</div>
		
	</div>
</div> 
<?php echo form_close(); ?>