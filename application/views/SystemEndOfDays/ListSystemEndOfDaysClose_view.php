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
			<a href="<?php echo base_url();?>end-of-days/close-branch">
				Tutup Cabang
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
							<th width="10%">Nominal</th>
							<th width="10%">D/K</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$no = 1;
							$totaldebet = 0;
							$totalkredit = 0;
							if(empty($journal)){
								echo "
									<tr>
										<td colspan='8' align='center'>Emty Data</td>
									</tr>
								";
							} else {
								$id = 0;
								foreach ($journal as $key=>$val){

									if($val['journal_voucher_debit_amount'] <> 0 ){
										$nominal = $val['journal_voucher_debit_amount'];
										$status = "D";
									} else if($val['journal_voucher_credit_amount'] <> 0){
										$nominal = $val['journal_voucher_credit_amount'];
										$status = "K";
									}

									if($val['journal_voucher_id'] != $id){
										echo"
											<tr>			
												<td style='text-align:center; background-color:lightgrey'>$no.</td>
												<td style='text-align:left; background-color:lightgrey'>".$val['transaction_module_code']."</td>
												<td style='text-align:left; background-color:lightgrey'>".$val['journal_voucher_description']."</td>
												<td style='text-align:center; background-color:lightgrey'>".tgltoview($val['journal_voucher_date'])."</td>
												<td style='text-align:left; background-color:lightgrey'>".$val['account_code']."</td>
												<td style='text-align:left; background-color:lightgrey'>".$val['account_name']."</td>
												<td style='text-align:right; background-color:lightgrey'>".number_format($nominal, 2 )."</td>
												<td style='text-align:right; background-color:lightgrey'>".$status."</td>
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
												<td style='text-align:right;'>".number_format($nominal, 2 )."</td>
												<td style='text-align:right;'>".$status."</td>
											</tr>
										";
									}									
									
								$totaldebet += $val['journal_voucher_debit_amount'];
								$totalkredit += $val['journal_voucher_credit_amount'];	
								if($id != $val['journal_voucher_id']){
									$id = $val['journal_voucher_id'];
								}
								} 
							}
							
						?>
							<tr>
								<td colspan="8">
								
								</td>
							</tr>
							
							<tr>
								<td colspan="6" rowspan="2">
									<?php if(round($totaldebet) != round($totalkredit)){?>
										<div class="alert alert-danger alert-dismissable">                 
											Total Debet dan Kredit masih belum seimbang !
										</div>
									<?php } ?>
									</td>
								<td align="right"><b>Total Debet</td>
								<td align="right"><b><?php echo number_format($totaldebet, 2); ?></td>
							</tr>
							<tr>
								<td align="right"><b>Total Kredit</td>
								<td align="right"><b><?php echo number_format($totalkredit, 2); ?></b></td>
							</tr>
							
						</tbody>
					</table>
					
					<?php if(round($totaldebet) == round($totalkredit)){?>
						<?php if($endofdays['end_of_days_status'] == '1'){?>
							<div class="row">
								<div class="col-md-12 " style="text-align  : right !important;">
								<?php	echo form_open('end-of-days/process-close-branch',array('id' => 'myform', 'class' => '')); ?>
								<input type="hidden" name="debit_amount" id="debit_amount"  value="<?= $totaldebet;?>"/>
								<input type="hidden" name="credit_amount" id="credit_amount"  value="<?= $totalkredit;?>"/>
									<button type="submit" name="process_close_branch" id="process_close_branch" onClick='javascript:return confirm(\"apakah yakin tutup cabang sekarang ?\")' value="<?= $endofdays['end_of_days_id']?>" class="btn blue">Tutup Cabang</button>	
								<?php echo form_close(); ?>
								</div>
						<?php } ?>
					<?php } ?>
				</div>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
<?php echo form_close(); ?>