<script>
	function ulang(){
		document.getElementById("vendor_code").value = "";
		document.getElementById("vendor_name").value = "";
	}
	function onlyAlpha(e, t) {
		try {
            if (window.event) {
                var charCode = window.event.keyCode;
            }
            else if (e) {
                var charCode = e.which;
            }
            else { return true; }
				if (charCode == 32 || (charCode > 64 && charCode < 91) || (charCode > 96 && charCode < 123))
                    return true;
                else
                    return false;
            }
        catch (err) {
                alert(err.Description);
        }
	}
	function onlyAlphaNumeric(e, t) {
		try {
            if (window.event) {
                var charCode = window.event.keyCode;
            }
            else if (e) {
                var charCode = e.which;
            }
            else { return true; }
				if ((charCode == 46 && charCode > 31) || (charCode > 47 && charCode<58) || charCode == 44 || charCode == 32 || (charCode > 64 && charCode < 91) || (charCode > 96 && charCode < 123))
                    return true;
                else
                    return false;
            }
        catch (err) {
                alert(err.Description);
        }
	}
	function onlyAlphabets(e, t) {
		try {
            if (window.event) {
                var charCode = window.event.keyCode;
            }
            else if (e) {
                var charCode = e.which;
            }
            else { return true; }
				if ((charCode > 47 && charCode<58) || charCode == 32 || (charCode > 64 && charCode < 91) || (charCode > 96 && charCode < 123))
                    return true;
                else
                    return false;
            }
        catch (err) {
                alert(err.Description);
        }
	}
	
	function isNumberKey(evt){
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 
            && (charCode < 48 || charCode > 57))
            return false;

        return true;
    }
	
	$(document).ready(function(){
        $("#Save").click(function(){
			var vendor_code = $("#vendor_code").val();
			var vendor_name = $("#vendor_name").val();
			
		  	if(vendor_code!='' && vendor_name!=''){
				return true;
			}else{
				alert('Data of Vendor Not Yet Complete');
				// document.getElementById("journal_voucher_description").value = "";
				return false;
			}
		});
    });
</script>
<?php echo form_open('vendor/processaddvendor',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
	$data = $this->session->userdata('addvendor');
	// $code = $this->randomprefix1(4);
?>
<div class="row">
	<div class="col-md-12">
		<h3 class="page-title">
			Add Vendor		
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
			<li>
				<a href="<?php echo base_url();?>vendor/add">
					Add Vendor
				</a>
			</li>
		</ul>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						<i></i>Add Vendor
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
								<div class="form-group">
									<label class="control-label">Vendor Code<span class="required">*</span></label>
									<input type="text" name="vendor_code" id="vendor_code" value="<?php echo $data['vendor_code'];?>"class="form-control">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Vendor Name<span class="required">*</span></label>
									<input type="text" name="vendor_name" id="vendor_name" value="<?php echo $data['vendor_name'];?>"class="form-control">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Vendor Person in Charge<span class="required">*</span></label>
									<input type="text" name="vendor_pic" id="vendor_pic" value="<?php echo $data['vendor_pic'];?>"class="form-control">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Vendor Address<span class="required">*</span></label>
									<input type="text" name="vendor_address" id="vendor_address" value="<?php echo $data['vendor_address'];?>"class="form-control">
								</div>
							</div>	
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Vendor Home Phone<span class="required">*</span></label>
									<input type="text" name="vendor_home_phone" id="vendor_home_phone" value="<?php echo $data['vendor_home_phone'];?>"class="form-control">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Vendor Mobile Phone<span class="required">*</span></label>
									<input type="text" name="vendor_mobile_phone" id="vendor_mobile_phone" value="<?php echo $data['vendor_mobile_phone'];?>"class="form-control">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Vendor Email<span class="required">*</span></label>
									<input type="text" name="vendor_email" id="vendor_email" value="<?php echo $data['vendor_email'];?>"class="form-control">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label">Vendor Remark<span class="required">*</span></label>
									<textarea rows="3" name="vendor_remark" id="vendor_remark" class="form-control"><?php echo $data['vendor_remark'];?></textarea>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12 " style="text-align  : right !important;">
								<input type="reset" name="Reset" value="Reset" class="btn btn-danger" onClick="ulang();">
								<input type="submit" name="Save" id="Save" value="Save" class="btn green" title="Simpan Data">
							</div>	
						</div>
					</div>					
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>