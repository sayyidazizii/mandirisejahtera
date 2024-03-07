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

<script>

	 function function_elements_add(name, value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('nominative-savings-pickup/function-elements-add');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
						
			}
		});
	}
	
</script>

<?php
	$data=$this->session->userdata('filter-AcctNominativeSavingsPickup');

	if(!is_array($data)){
		$data['start_date']				= tgltodb(date("Y-m-d"));
		$data['office_id']				= '';

	}
	if(empty($data['office_id'])){
	$data['office_id'] = '';
	}
	
	
?>

		<!-- BEGIN PAGE TITLE & BREADCRUMB-->
		<div class = "page-bar">
			<ul class="page-breadcrumb ">
				<li>
					<a href="<?php echo base_url();?>">
						Beranda
					</a>
					<i class="fa fa-angle-right"></i>
				</li>
				<li>
					<a href="<?php echo base_url();?>company">
						Daftar Pickup
					</a>
					<i class="fa fa-angle-right"></i>
				</li>
			</ul>
		</div>
		<h3 class="page-title">
			Daftar Pickup <small>Kelola Pickup</small>
		</h3>
		<!-- END PAGE TITLE & BREADCRUMB-->



<?php echo form_open('AcctNominativeSavingsPickup/filter',array('id' => 'myform', 'class' => '')); ?>
<!-- <div class="row">
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
						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" onChange="function_elements_add(this.name, this.value);" value="<?php echo tgltoview($data['start_date']);?>"/>
								<label class="control-label">Tanggal Mulai
									<span class="required">
										*
									</span>
								</label>
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
</div> -->
<!-- <?php echo form_close(); ?> -->
		
	
	<div class="row">
		<div class="col-md-12">
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Daftar Pickup
					</div>
					<!-- <div class="actions">
						<a href="<?php echo base_url();?>company/add" class="btn btn-default btn-sm">
							<i class="fa fa-plus"></i>
							<span class="hidden-480">
								Tambah Pickup Baru
							</span>
						</a>
					</div> -->
				</div>
				<div class="portlet-body">
				<div class="form-body form">
					<div class = "row">
						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" onChange="function_elements_add(this.name, this.value);" value="<?php echo tgltoview($data['start_date']);?>"/>
								<label class="control-label">Tanggal Mulai
									<span class="required">
										*
									</span>
								</label>
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
	<!-- 	</div>
	</div>
</div> -->
<?php echo form_close(); ?>


				<div class="portlet-body">
					<div class="form-body">
						<?php
							echo $this->session->userdata('message');
							$this->session->unset_userdata('message');
						?>
						<table class="table table-striped table-bordered table-hover table-checkable order-column" id="sample_1">
							<thead>
								<tr>
									<th style='text-align:center' width="5%">No</th>
									<th style='text-align:center' width="15%">Tanggal</th>
									<th style='text-align:center' width="15%">Nama Operator</th>
									<th style='text-align:center' width="15%">Nama Anggota</th>
									<th style='text-align:center' width="10%">Transaksi</th>
									<th style='text-align:center' width="10%">Jumlah</th>
									<th style='text-align:center' width="10%">Keterangan</th>
									<th style='text-align:center' width="10%">Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$no = 1;
									if(!is_array($savingspickup)){
										echo "<tr><th colspan='2'>Data Masih Kosong</th></tr>";
									} else {
										foreach($savingspickup as $key=>$val){
											echo"
												<tr>
													<td>".$no."</td>
													<td>".$val['savings_cash_mutation_date']."</td>
													<td>".$val['operated_name']."</td>
													<td>".$val['member_name']."</td>
													<td>".$val['mutation_name']."</td>
													<td>".$val['savings_cash_mutation_amount']."</td>
													
													<td>".$val['savings_cash_mutation_remark']."</td>";

													if ($val['pickup_status']==0) {
													echo"
													
													<td>
														<a href='".$this->config->item('base_url').'AcctNominativeSavingsPickup/showdetail/'.$val['savings_cash_mutation_id']."' class='btn default btn-xs purple'>
															<i class='fa fa-edit'></i> Proses
														</a>
													</td>";
												}else{

												echo"
													
													<td>
														
													 Telah Disetorkan
														
													</td>
												</tr>
											";
											$no++;
										}
									}
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php echo form_close(); ?>