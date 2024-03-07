<script>
	base_url = '<?= base_url()?>';

	$(document).ready(function(){
        $("#Save").click(function(){
			var maintenance_code 			= $("#maintenance_code").val();
			var maintenance_name 			= $("#maintenance_name").val();
			
		  	if(maintenance_code!='' && maintenance_name!='' ){
				return true;
			}else{
				alert('Data of Maintenance Not Yet Complete');
				return false;
			}
		});
    });
	
    function function_elements_add(name, value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('CoreCustomer/function_elements_add');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
			}
		});
	}
	
	function function_state_add(value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('CoreCustomer/function_state_add');?>",
				data : {'value' : value},
				success: function(msg){
			}
		});
	}

	function reset_data(){
		document.location = "<?php echo base_url();?>CoreCustomer/reset_data";
	}
</script>

<!-- BEGIN PAGE TITLE & BREADCRUMB-->
<div class = "page-bar">
	<ul class="page-breadcrumb">
		<li>
			<i class="fa fa-home"></i>
			<a href="<?php echo base_url();?>">
				Home
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>CoreCustomer">
				Customer List
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>CoreCustomer/addCoreCustomer">
				Edit Customer
			</a>
		</li>
	</ul>
</div>
<h3 class="page-title">
	Form Edit Customer		
</h3>
<?php echo form_open('CoreCustomer/processEditCoreCustomer',array('id' => 'myform', 'class' => 'horizontal-form')); 

?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Form Edit
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>CoreCustomer" class="btn btn-default btn-sm">
							<i class="fa fa-angle-left"></i>
							<span class="hidden-480">
								Back
							</span>
						</a>
					</div>
				</div>
				<div class="portlet-body">
					<div class="form-body">
						<?php
							echo $this->session->userdata('message');
							$this->session->unset_userdata('message');
						?>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" name="customer_name" id="customer_name" value="<?php echo $corecustomer['customer_name']; ?>" class="form-control">

									<input type="hidden" name="customer_id" id="customer_id" value="<?php echo $corecustomer['customer_id']; ?>">
									<label class="control-label">Customer Name<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" name="customer_contact_person" id="customer_contact_person" value="<?php echo $corecustomer['customer_contact_person']; ?>" class="form-control">
									<label class="control-label">Customer Contact Person<span class="required">*</span></label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" name="customer_phone_number" id="customer_phone_number" value="<?php echo $corecustomer['customer_phone_number']; ?>" class="form-control">
									<label class="control-label">Customer Phone<span class="required">*</span></label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="form-group form-md-line-input">
									<?php echo form_textarea(array('name'=>'customer_address', 'rows'=>'3', 'class'=>'form-control','id'=>'customer_address','value'=>set_value('customer_address',$corecustomer['customer_address'])))?>
									<label class="control-label">Customer Address<span class="required">*</span></label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12" style='text-align:right'>	
								<button type="button" class="btn red" onClick="reset_data();"><i class="fa fa-times"></i> Reset</button>
								<button type="submit" class="btn green-jungle"><i class="fa fa-check"></i> Save</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>

