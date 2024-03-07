<div class="row">
	<div class="col-md-12">
		<!-- BEGIN PAGE TITLE & BREADCRUMB-->
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
			<li>
				<a href="<?php echo base_url();?>wiphistory/showdetail/"<?php echo $this->uri->segment(3); ?>>
					Detail WIP History
				</a>
			</li>
		</ul>
		<!-- END PAGE TITLE & BREADCRUMB-->
	</div>
</div>
<div class="row">
	<div class="col-md-12">	
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-reorder"></i>Detail WIP History
				</div>
				<div class="tools">
					<a href="javascript:;" class="collapse">
					</a>
					
				</div>
			</div>
			<div class="portlet-body form">
				<!-- BEGIN FORM-->
				<div class="form-body">
					<div class="row">
						<div class="col-md-6 ">
							<div class="form-group">
								<label class="control-label">Date</label>
									<input name="purchase_order_date" id="purchase_order_date" type="text" class="form-control" value="<?php echo tgltoview($result['wip_history_date']); ?>" readonly>
								
							</div>
						</div>	
					</div>
					<div class="row">
						<div class="col-md-6 ">
							<div class="form-group">
								<label class="control-label">Item</label>
									<input name="supplier_id" id="supplier_id" type="text" class="form-control" value="<?php echo $this->wiphistory_model->getitemname($result['item_id']); ?>" readonly>
								
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 ">
							<div class="form-group">
								<label class="control-label">Production Activity Result No</label>
									<input name="supplier_id" id="supplier_id" type="text" class="form-control" value="<?php echo $this->wiphistory_model->getproductionactivityno($result['production_activity_id']); ?>" readonly>
								
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 ">
							<div class="form-group">
								<label class="control-label">Quantity</label>
									<input name="warehouse_id" id="warehouse_id" type="text" class="form-control" value="<?php echo nominal($result['quantity']); ?>" readonly>
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>