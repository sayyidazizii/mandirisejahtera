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
			<a href="<?php echo base_url();?>deposito-account">
				Master Data Simpanan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>deposito-account/get-list-savings-account">
				Data Rek Simpanan Anggota
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Data Rek Simpanan Anggota
</h3>
<?php
	$sesi=$this->session->userdata('filter-listacctsavingsaccount');

	if(!is_array($sesi)){
		$sesi['jenis_simpanan']			= '';
	}
?>	
<?php	echo form_open('deposito-account/filter-list-savings-account',array('id' => 'myform', 'class' => '')); ?>

<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Pencarian
				</div>
				<div class="tools">
					<a href="javascript:;" class='expand'></a>
				</div>
			</div>
			<div class="portlet-body display-hide">
				<div class="form-body form">

					 <div class = "row">
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('savings_id', $acctsavings,set_value('savings_id',$sesi['savings_id']),'id="savings_id" class="form-control select2me"');
								?>
								<label>Jenis Simpanan</label>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-actions right">
							<button type="button" class="btn red" onClick="reset_search();"><i class="fa fa-times"></i> Batal</button>
							<button type="submit" class="btn green-jungle"><i class="fa fa-search"></i> Cari</button>
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Daftar Rek Simpanan
				</div>
				<div class="actions">
					<a href="<?php echo base_url();?>deposito-account" class="btn btn-default btn-sm">
						<i class="fa fa-angle-left"></i>
						<span class="hidden-480">
							Kembali
						</span>
					</a>
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
					<table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
					<thead>
						<tr>
							<th width="5%">No</th>
							<th width="7%">No Rekening</th>
							<th width="15%">Nama</th>
							<th width="10%">Jenis Simpanan</th>
							<th width="10%">Tanggal Buka</th>
							<th width="15%">Saldo</th>
							<th width="5%">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$no = 1;
							if(empty($acctsavingsaccount)){

								echo "
									<tr>
										<td colspan='8' align='center'>Emty Data</td>
									</tr>
								";
							} else {
								foreach ($acctsavingsaccount as $key=>$val){	
								// $coremember = $this->session->userdata('member');
								// print_r($coremember);								
									echo"
										<tr>			
											<td style='text-align:center'>$no.</td>
											<td>".$val['savings_account_no']."</td>
											<td>".$val['member_name']."</td>
											<td>".$val['savings_name']."</td>
											<td>".$val['savings_account_date']."</td>
											<td>".$val['savings_account_last_balance']."</td>
											<td>
												<a href='".$this->config->item('base_url').'deposito-account/add/'.$member_id.'/'.$val['savings_account_id']."' class='btn default btn-xs blue'>
													<i class='fa fa-plus'></i> Pilih
												</a>
											</td>
										</tr>
									";
									$no++;
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