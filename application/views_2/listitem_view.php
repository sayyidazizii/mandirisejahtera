<?php 
	$this->load->view('item/formfilteritem_view'); 
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box red">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-reorder"></i>Item List
				</div>
			</div>
			<div class="portlet-body">
			<div class="form-body">
				<table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
				<!--<table class="table table-striped table-bordered table-hover table-full-width">-->
					<thead>
						<tr>
							<th style='text-align:center'>Code</th>
							<th style='text-align:center'>Barcode</th>
							<th style='text-align:center'>Name</th>
							<th style='text-align:center'>Stock</th>
							<th style='text-align:center'>Unit</th>
							<th style='text-align:center'>Shelf</th>
							<th style='text-align:center'>Cost</th>
							<th style='text-align:center'>Price</th>
							<th style='text-align:center'>Type</th>
							<th style='text-align:center'>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php
							// if(empty($item)){
								// echo "<tr><td style='text-align:center' colspan='10'>Data Masih Kosong</td></tr>";
							// } else {
								foreach($item as $key=>$val){
									echo"
										<tr>											
											<td>".$val['item_code']."</td>
											<td>".$val['item_barcode']."</td>
											<td>".$val['item_name']."</td>
											<td>".$val['last_balance']."</td>
											<td>".$this->library_model->getunitsymbol($val['item_unit_id'])."</td>
											<td>".$val['item_shelf_location']."</td>
											<td>".nominal($val['item_unit_cost'])."</td>
											<td>".nominal($val['item_unit_price'])."</td>
											<td>".$this->configuration->ItemType[$val['item_type']]."</td>
											<td>
												<a href='".base_url().'item/edit/'.$val['item_id']."' class='btn default btn-xs purple'>
													<i class='fa fa-edit'></i> Edit
												</a>
												<a href='".base_url().'item/delete/'.$val['item_id']."' onClick='javascript:return confirm(\"Are you sure you want to delete this entry ?\")' class='btn default btn-xs red'>
													<i class='fa fa-trash-o'></i> Delete
												</a>
											</td>
										</tr>
									";
								}
							// }
						?>
					</tbody>
				</table>
			</div>
			</div>
		</div>
	</div>
</div>