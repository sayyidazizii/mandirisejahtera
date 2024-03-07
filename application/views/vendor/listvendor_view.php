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
<div class="row">
	<div class="col-md-12">
		<h3 class="page-title">
		Vendor
		</h3>
		<ul class="page-breadcrumb breadcrumb">
			<li>
				<i class="fa fa-home"></i>
				<a href="<?php echo base_url();?>">
					Home
				</a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<a href="<?php echo base_url();?>vendor">
					Vendor
				</a>
				<i class="fa fa-angle-right"></i>
			</li>
		</ul>
	</div>
</div>
<div class="row">
	<div class="col-md-12">  
		<div class="portlet">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-shopping-cart"></i>Vendor List
				</div>
				<div class="actions">
					<a href="<?php echo base_url();?>vendor/add" class="btn default yellow-stripe">
						<i class="fa fa-plus"></i>
						<span class="hidden-480">
							 New Vendor
						</span>
					</a>
					<!--<a href="<?php echo base_url();?>vendor/import" class="btn default yellow-stripe">
						<i class="fa fa-upload"></i>
						<span class="hidden-480">
							 Import Vendor
						</span>
					</a>-->
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-globe"></i>Vendor
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
				<?php
					echo $this->session->userdata('message');
					$this->session->unset_userdata('message');
				?>
					<table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
						<thead>
							<tr>
								<th style='text-align:center'>Code</th>
								<th style='text-align:center'>Name</th>
								<th style='text-align:center'>Address</th>
								<th style='text-align:center'>City</th>
								<th style='text-align:center'>Person In Charge</th>
								<th style='text-align:center'>Home Phone</th>
								<th style='text-align:center'>Mobile Phone</th>
								<th style='text-align:center'>Remark</th>
								<th style='text-align:center'>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								if(!is_array($vendor)){
									echo "<tr><th colspan='2'>Data Masih Kosong</th></tr>";
								} else {
									$vendor_status = array(0 => 'Self', 1 => 'Third Party');
									foreach($vendor as $key=>$val){
										echo"
											<tr>											
												<td>".$val['vendor_code']."</td>
												<td>".$val['vendor_name']."</td>
												<td>".$val['vendor_address']."</td>
												<td>".$val['vendor_city']."</td>
												<td>".$val['vendor_person_in_charge']."</td>
												<td>".$val['vendor_home_phone']."</td>
												<td>".$val['vendor_mobile_phone']."</td>
												<td>".$val['vendor_remark']."</td>
												<td>
													<a href='".$this->config->item('base_url').'vendor/edit/'.$val['vendor_id']."' class='btn default btn-xs purple'>
														<i class='fa fa-edit'></i> Edit
													</a>
													<a href='".$this->config->item('base_url').'vendor/delete/'.$val['vendor_id']."' onClick='javascript:return confirm(\"Are you sure you want to delete this entry ?\")' class='btn default btn-xs red'>
														<i class='fa fa-trash-o'></i> Delete
													</a>
												</td>
											</tr>
										";
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