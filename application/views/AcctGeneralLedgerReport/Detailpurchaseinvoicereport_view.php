<div class="row-fluid">
	<?php
		echo $this->session->userdata('message');
		$this->session->unset_userdata('message');
	?>
	
	
					<!-- BEGIN PAGE TITLE & BREADCRUMB-->
					<div class = "page-bar">
						<ul class="page-breadcrumb ">
							<li>
								<i class="fa fa-home"></i>
								<a href="<?php echo base_url();?>">
									Home
								</a>
								<i class="fa fa-angle-right"></i>
							</li>
							<li>
								<a href="<?php echo base_url();?>purchaseinvoicereport">
									Purchase Invoice
								</a>
								<i class="fa fa-angle-right"></i>
							</li>
							<li>
								<a href="<?php echo base_url();?>purchaseinvoicereport/showdetail/<?php echo $this->uri->segment(3); ?>">
									Purchase Invoice Detail
								</a>
								<i class="fa fa-angle-right"></i>
							</li>
						</ul>
					</div>
					<h3 class="page-title">
						Purchase Invoice Detail
					</h3>
					<!-- END PAGE TITLE & BREADCRUMB-->
				
		
		<div class="row">
				<div class="col-md-12">
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								Form Detail
							</div>
						</div>
						<div class="portlet-body">
						<div class="form-body">
						<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Supplier
								<span class="required">
								*
								</span>
								</label>
								<input class="form-control" type="text" name='supplier_id' readonly id='supplier_id' value="<?php echo $this->purchaseinvoicereport_model->getsuppliername($header['supplier_id']);?>">
				
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Purchase Invoice No
								<span class="required">
								*
								</span>
								</label>
									<input type="text" class="form-control" name="purchase_invoice_no" id="purchase_invoice_no" placeholder="123456" readonly value="<?php echo set_value('purchase_invoice_no',$header['purchase_invoice_no']);?>">
						
							</div>
						</div>
						</div>
						
						<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Purchase Invoice Date</label>
									<input name="purchase_invoice_date" id="purchase_invoice_date" type="text" class="form-control" value="<?php echo $header['purchase_invoice_date']; ?>" readonly>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Purchase Invoice Due Date</label>
								<input name="purchase_invoice_due_date" id="purchase_invoice_due_date" type="text" class="form-control" value="<?php
													echo tgltoview($header['purchase_invoice_due_date']);
											?>" readonly>
							</div>
						</div>
						</div>
					<div class="row">
						<div class="col-md-6 ">
							<div class="form-group">
								<label class="control-label">Payment Terms</label>
									<input type="text" class="form-control" name="purchase_invoice_payment_terms" id="purchase_invoice_payment_terms" placeholder="20" readonly value="<?php echo set_value('purchase_invoice_payment_terms',$header['purchase_order_payment_terms']);?>">
							</div>
						</div>
						</div>
						<div class="row">
					<div class="col-md-12 ">
						<div class="form-group">
								<label class="control-label">Purchase Invoice Remark</label>
									<input type="text" class="form-control" name="purchase_invoice_remark" id="purchase_invoice_remark" placeholder="Remark" readonly value="<?php echo set_value('purchase_invoice_remark',$header['purchase_invoice_remark']);?>">
							
						</div>
						<label></label>
						</div>
						</div>
						</div>
					</div>
					<!-- END EXAMPLE TABLE PORTLET-->
				</div>
			</div>	
		</div>	
		
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
							<div class="table-responsive">
								<table class="table table-bordered table-advance table-hover">
									<thead>
										<tr>
											<th width="3%">No</th>                                    
											<th width="20%">Name</th>                                                                     
											<th width="15%">Price</th>                                    
											<th width="8%">Quantity</th>                                                                           
											<th width="10%">Discount (%)</th>                                         
											<th width="10%">Discount</th>                                   
											<th>Subtotal</th>
										</tr>
									</thead>
									<tbody>
									<?php
										if($detail->num_rows <= '0'){
											echo "<tr><th colspan='8' style='text-align  : center !important;'>Data is Empty</th></tr>";
										} else {
											$no =1;
											foreach ($detail->result_array() as $key=>$val){
												echo"
													<tr>
														<td style='text-align:center;'>".$no.". 
														</td>
														<td style='text-align:left;'>".$this->purchaseinvoicereport_model->getItemName($val['item_id'])."</td>
														<td style='text-align:right;'>".nominal($val[item_unit_cost])."
														</td>
														<td style='text-align:right;'>".nominal($val[quantity])."</td>
														<td style='text-align:right;'>".nominal($val[discount_percentage])."
														</td>
														<td style='text-align:right;'>".nominal($val[discount_base_amount])."
														</td>
														<td style='text-align:right;'>".nominal($val[subtotal_base_amount_after_discount])."
														</td>
													</tr>
												";
												$no++;
												$subtotal += $val['subtotal_base_amount_after_discount'];
											}
										}
									?>	
									
									<tr>
										<td colspan = "6"><b>Sub Total</b></td>
										<td style='text-align  : right !important;'><?php echo nominal($header['subtotal_base_amount']); ?>
										</td>
									</tr>
									<tr>
										<td colspan = "5"><b>Discount</b></td>
										<td style='text-align  : right !important;'>
											<?php echo nominal($header['discount_percentage']); ?> %
										</td>
										<td style='text-align  : right !important;'> <?php echo nominal($header['discount_base_amount']); ?>
									</td>
									</tr>		
									
									<tr>
										<td colspan = "5"><b>PPn</b></td>
										<td style='text-align  : right !important;'><?php echo nominal($header['ppn_percentage']); ?> %
										</td>
										<td style='text-align  : right !important;'><?php echo nominal($header['ppn_base_amount']); ?>
										</td>
									</tr>
									<tr>
										<td colspan = "6"><b>Freight Cost</b></td>
										<td style='text-align  : right !important;'><?php echo nominal($header['freight_cost_base_amount']); ?>
										</td>
									</tr>	
									<tr>
										<td colspan = "6"><b>Total</b></td>
										<td style='text-align  : right !important;'><?php echo nominal($header['total_base_amount']); ?>
										</td>
									</tr>
										
									</tbody>
								</table>
						</div>
						</div>
					</div>
					<!-- END EXAMPLE TABLE PORTLET-->
				</div>
			</div>	
		</div>	
		
<?php echo form_close(); ?>