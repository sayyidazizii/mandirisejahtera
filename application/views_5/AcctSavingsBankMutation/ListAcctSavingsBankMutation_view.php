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
	base_url = "<?php echo base_url(); ?>"
	function reset_search(){
		document.location = base_url = "savings-bank-mutation/reset-search";
	}
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
			<a href="<?php echo base_url();?>savings-bank-mutation">
				Daftar Tabungan Bank
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Daftar Tabungan Bank<small>Kelola Tabungan Bank</small>
</h3>
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$sesi=$this->session->userdata('filter-acctsavingsbankmutation');

	if(!is_array($sesi)){
		$sesi['start_date']				= date('Y-m-d');
		$sesi['end_date']				= date('Y-m-d');
		$sesi['savings_account_id']		= '';
	}
?>	
<?php	echo form_open('savings-bank-mutation/filter',array('id' => 'myform', 'class' => '')); 

	$start_date			= $sesi['start_date'];
	$end_date			= $sesi['end_date'];
?>
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
						<div class = "col-md-4">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="<?php echo tgltoview($start_date);?>"/>
								<label class="control-label">Tanggal Awal
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<div class = "col-md-4">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="<?php echo tgltoview($end_date);?>"/>
								<label class="control-label">Tanggal Akhir
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('savings_account_id', $acctsavingsaccount,set_value('savings_account_id',$sesi['savings_account_id']),'id="savings_account_id" class="form-control select2me"');
								?>
								<label>No. Rekening</label>
							</div>
						</div>
					</div>

					

					<div class="row">
						<div class="form-actions right">
							<button type="button" class="btn red" onClick="reset_search();"><i class="fa fa-times"></i> Reset</button>
							<button type="submit" class="btn green-jungle"><i class="fa fa-search"></i> Find</button>
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
					Daftar
				</div>
				<div class="actions">
					<a href="<?php echo base_url();?>savings-bank-mutation/add" class="btn btn-default btn-sm">
						<i class="fa fa-plus"></i>
						<span class="hidden-480">
							Tambah Tabungan Bank Baru
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
							<th width="10%">Nama Anggota</th>
							<th width="15%">Jenis Tabungan</th>
							<th width="15%">Nomor Rekeing</th>
							<th width="15%">Tanggal Transfer</th>
							<th width="15%">Transfer Bank</th>
							<th width="15%">Jumlah</th>
							
						</tr>
					</thead>
					<tbody>
						<?php
							$no = 1;
							if(empty($acctsavingsbankmutation)){
								echo "
									<tr>
										<td colspan='8' align='center'>Emty Data</td>
									</tr>
								";
							} else {
								foreach ($acctsavingsbankmutation as $key=>$val){									
									echo"
										<tr>			
											<td style='text-align:center'>".$no."</td>
											<td>".$val['member_name']."</td>
											<td>".$val['savings_name']."</td>
											<td>".$val['savings_account_no']."</td>
											<td>".tgltoview($val['savings_bank_mutation_date'])."</td>
											<td>".$val['account_code']." - ".$val['bank_account_name']."</td>
											<td>".number_format($val['savings_bank_mutation_amount'])."</td>

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