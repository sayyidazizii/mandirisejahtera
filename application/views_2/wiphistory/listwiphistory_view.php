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
base_url = '<?php echo base_url();?>';
	function reset_all(){
		document.location = base_url+"wiphistory/reset_search";
	}
</script>
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>
<div class="row">
	<div class="col-md-12">
		<h3 class="page-title">
			WIP History
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
				<a href="<?php echo base_url();?>wiphistory">
					WIP History
				</a>
				<i class="fa fa-angle-right"></i>
			</li>
		</ul>
	</div>
</div>
<?php
$data=$this->session->userdata('filter-wiphistory');
if(!is_array($data)){
		$data['start_date']		= date('d-m-Y');
		$data['end_date']		= date('d-m-Y');
		$data['item_id']	= '';
	}
?>
<?php echo form_open('wiphistory/filter',array('id' => 'myform', 'class' => '')); ?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box red">
			<div class="portlet-title">
				<div class="caption">
					Filter List
				</div>
				<div class="tools">
					<a href="javascript:;" class='expand'></a>
				</div>
			</div>
			<div class="portlet-body display-hide">
				<div class="form-body">
					<div class="form-group">
									<label class="control-label">Date
									<span class="required">
									*
									</span>
									</label>
							<?php if($data['start_date'] != ''){?>
								<div class="input-group input-large date-picker input-daterange" data-date="11-10-2012" data-date-format="dd-mm-yyyy">
											<input type="text" class="form-control" name="start_date" id="start_date" value="<?php echo $data['start_date']; ?>" >
											<span class="input-group-addon">
												 to
											</span>
											<input type="text" class="form-control" name="end_date" id="end_date" value="<?php echo $data['end_date']; ?>" >
										</div>
							<?php }else { ?>
									<div class="input-group input-large date-picker input-daterange" data-date="11-10-2012" data-date-format="dd-mm-yyyy">
											<input type="text" class="form-control" name="start_date" id="start_date" value="<?php echo date('d-m-Y'); ?>" >
											<span class="input-group-addon">
												 to
											</span>
											<input type="text" class="form-control" name="end_date" id="end_date" value="<?php echo date('d-m-Y'); ?>" >
								</div>
							<?php } ?>
								</div>
								
						<div class="form-group">
							<label class="control-label">Item</label>
							<?php
								echo form_dropdown_search('item_id', $item,set_value('item_id',$data['item_id']),'id="item_id"');
							?>
						</div>	
					<div class="form-actions right">
						<input type="reset" name="Reset" value="Reset" class="btn btn-danger" onClick="reset_all();">
						<input type="submit" name="Find" value="Find" class="btn green" title="Search Data">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<?php
	$no2 = 0;
	foreach ($wiphistory->result_array() as $key=>$val){
		$detail_wip = $this->wiphistory_model->getdetailhistorywip($val['wip_history_date'], $data['item_id']);
		$jum = count($detail_wip);
?>
	<div class="row">
		<div class="col-md-12">
			<div class="portlet"> 
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i></i><?php echo tgltoview($val['wip_history_date']); ?>
						</div>
						<div class="tools">
							<a href="javascript:;" class='expand'></a>
						</div>
							
					</div>
					<div class="portlet-body display-hide">
						<div class="form-body">
							<div class="row">
								<div class="col-md-12">
									<table class="table table-striped table-bordered table-hover table-full-width">
										<thead>
											<tr>
												<th style='text-align:center' width='22%'>Item Name</th>
												<th style='text-align:center' width='15%'>Opening Balance</th>
												<th style='text-align:center' width='10%'>Qty Release</th>
												<th style='text-align:center' width='10%'>Qty Return</th>
												<th style='text-align:center' width='10%'>Qty Used</th>
												<th style='text-align:center' width='10%'>Qty Reject</th>
												<th style='text-align:center' width='10%'>Qty WIP</th>
												<th style='text-align:center' width='20%'>Last Update</th>
											</tr>
										</thead>
										<tbody>
											<?php
											// print_r($detail_wip);exit;
												foreach($detail_wip as $key2 => $val2){
													// $opening_balance = $this->wiphistory_model->getopeningbalance($val2['wip_history_id']);
													// $qty_wip = $this->wiphistory_model->getquantitywip($val2['wip_history_id']);
													$qty_used = $this->wiphistory_model->getquantityused($val2['item_id'],$val['wip_history_date']);
													$qty_release = $this->wiphistory_model->getquantityrelease($val2['item_id'],$val['wip_history_date']);
													$qty_releasefromrelease = $this->wiphistory_model->getquantityreleasefromrelease($val2['item_id'],$val['wip_history_date']);
													$qty_return = $this->wiphistory_model->getquantityreturn($val2['item_id'],$val['wip_history_date']);
													$qty_reject_filling = $this->wiphistory_model->getquantityrejectfilling($val2['item_id'],$val['wip_history_date']);
													$qty_reject_warehouse = $this->wiphistory_model->getquantityrejectwarehouse($val2['item_id'],$val['wip_history_date']);
													$qty_reject_supplier = $this->wiphistory_model->getquantityrejectsupplier($val2['item_id'],$val['wip_history_date']);
													$qty_reject = $qty_reject_filling +  $qty_reject_warehouse + $qty_reject_supplier;
													$a = $wiphistory->result_array();
													$key_akhir = ($wiphistory->num_rows()) - 1;
													/* if($key == $key_akhir){	
														$opening_balance = $this->wiphistory_model->getopeningbalancebyitemanddate2($val2['item_id'],$a[$key_akhir]['wip_history_date']);
														if($opening_balance == ''){
															$wipopeningbalance = $this->wiphistory_model->getqtywipopeningbalance($val2['item_id']);
															if($wipopeningbalance == ''){
																$wipopeningbalance=0;
															}
															$opening_balance=$wipopeningbalance;
														}
													} else { */
														// $opening_balance = $this->wiphistory_model->getopeningbalancebyitemanddate($val2['item_id'],$a[$key+1]['wip_history_date']);
														$opening_balance = $this->wiphistory_model->getqtyopeningbalancewipfromhistory($val2['item_id'],$val['wip_history_date']);
													/* } */
													if($opening_balance != ''){$opening_balance = $opening_balance;} else {$opening_balance= 0;}
													$qty_wip = $this->wiphistory_model->getqty_wipbyitemanddate($val2['item_id'],$val['wip_history_date']);
													echo"
														<tr>
															<td>".$this->wiphistory_model->getitemname($val2['item_id'])."</td>
															<td style='text-align:right'>".nominalkoma($opening_balance)."</td>
															<td style='text-align:right'>".nominalkoma($qty_releasefromrelease)."</td>
															<td style='text-align:right'>".nominalkoma($qty_return)."</td>
															<td style='text-align:right'>".nominalkoma($qty_used)."</td>
															<td style='text-align:right'>".nominalkoma($qty_reject)."</td>
															<td style='text-align:right'>".nominalkoma($qty_wip)."</td>
															<td style='text-align:left'>".date('d-m-Y H:i:s', strtotime($val2['created_on']))."</td>
														</tr>
													";
												}
											?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php $no2++;
	} 
?>