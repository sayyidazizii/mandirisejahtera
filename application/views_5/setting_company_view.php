<script>
	function refreshrate(value){
		if(isNaN(value)){
			alert("Value must be a valid number");
			document.getElementById("refreshrate").value = 0;
		}else if(parseInt(value)<0){
			alert("Value must not be a negative");
			document.getElementById("refreshrate").value = 0;
		}else if(parseInt(value)<10){
			alert("Value must not be a less than 10 seconds");
			document.getElementById("refreshrate").value = 0;
		}else{
			document.getElementById("refreshrate").value = value;
		}
	}

	function ulang(){
		document.getElementById("company_name").value='';
		document.getElementById("company_address").value='';
		document.getElementById("company_city").value='';
		document.getElementById("company_state").value='';
		document.getElementById("company_postal_code").value='';
		document.getElementById("company_phone").value='';
		document.getElementById("company_website").value='';
		document.getElementById("company_email").value='';
	}
	
	function warningname(inputname){
		var letter = /^[a-zA-Z-,]+(\s{0,1}[a-zA-Z-, ])*$/;
		if(inputname.value.match(letter)){
			return true;
		}else{
			alert('Please input alphanumeric characters only');
			document.getElementById("company_name").value = "";	
			$('#company_name').focus();
			return false;
		}
	}
	
	function warningpostal(value){
		if(isNaN(value)===true || value ==''){
			alert('Please Input Number Only! ');
			document.getElementById('company_postal_code').value	= '';
			$('#company_postal_code').focus();
		}else{
			document.getElementById('company_postal_code').value	= value;
		}
	}
	
	function warningphone(value){
		if(isNaN(value)===true || value ==''){
			alert('Please Input Number Only! ');
			document.getElementById('company_phone').value	= '';
			$('#company_phone').focus();
		}else{
			document.getElementById('company_phone').value	= value;
		}
	}
	
	function warningfax(value){
		if(isNaN(value)===true || value ==''){
			alert('Please Input Number Only! ');
			document.getElementById('company_fax').value	= '';
			$('#company_fax').focus();
		}else{
			document.getElementById('company_fax').value	= value;
		}
	}
	
	function warningnpwp(value){
		if(isNaN(value)===true || value ==''){
			alert('Please Input Number Only! ');
			document.getElementById('company_npwp').value	= '';
			$('#company_npwp').focus();
		}else{
			document.getElementById('company_npwp').value	= value;
		}
	}
	
	function warningworkdays(value){
		if(isNaN(value)===true || value ==''){
			alert('Please Input Number Only! ');
			document.getElementById('work_days').value	= '';
			$('#work_days').focus();
		}else{
			document.getElementById('work_days').value	= value;
		}
	}
	
	function warningworkshift(value){
		if(isNaN(value)===true || value ==''){
			alert('Please Input Number Only! ');
			document.getElementById('work_shift').value	= '';
			$('#work_shift').focus();
		}else{
			document.getElementById('work_shift').value	= value;
		}
	}
	
	function warningaddress(inputname){
		var letter = /^[a-zA-Z-,]+(\s{0,1}[a-zA-Z-, ])*$/;
		if(inputname.value.match(letter)){
			return true;
		}else{
			alert('Please input alphanumeric characters only');
			document.getElementById("company_address").value = "";	
			$('#company_address').focus();
			return false;
		}
	}
	
	function warningcity(inputname){
		var letter = /^[a-zA-Z-,]+(\s{0,1}[a-zA-Z-, ])*$/;
		if(inputname.value.match(letter)){
			return true;
		}else{
			alert('Please input alphanumeric characters only');
			document.getElementById("company_city").value = "";	
			$('#company_city').focus();
			return false;
		}
	}
	
	function warningstate(inputname){
		var letter = /^[a-zA-Z-,]+(\s{0,1}[a-zA-Z-, ])*$/;
		if(inputname.value.match(letter)){
			return true;
		}else{
			alert('Please input alphanumeric characters only');
			document.getElementById("company_state").value = "";	
			$('#company_state').focus();
			return false;
		}
	}
	
	function openform(){
		var a = document.getElementById("passwordf").style;
		if(a.display=="none"){
			a.display = "block";
		}else{
			a.display = "none";
			document.getElementById("password").value ='';
			document.getElementById("re_password").value ='';
		}
	}
	
	function plus () {
		var qty = $("#company_fiscal_year")[0].value;
		var qty = parseInt(qty)+1;
		document.getElementById("company_fiscal_year").value = qty;	
    }
	
	function minus () {
		var qty = $("#company_fiscal_year")[0].value;
		var qty = parseInt(qty)-1;
		document.getElementById("company_fiscal_year").value = qty;	
    }
</script>
	<?php
		// echo form_open_multipart('');
		echo form_open_multipart('settingcompany/processsetting');
		
		$period=$this->configuration->Bulan;
	?>
	<div class="row">
				<div class="col-md-12">
					<!-- BEGIN PAGE TITLE & BREADCRUMB-->
					<h3 class="page-title">
					Setting Company
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
							<a href="<?php echo base_url();?>settingcompany">
								Setting Company
							</a>
							<i class="fa fa-angle-right"></i>
						</li>
					</ul>
					<!-- END PAGE TITLE & BREADCRUMB-->
				</div>
			</div>
	<div class="row">
		<div class="col-md-12">
			<div class="portlet"> 
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-shopping-cart"></i>Setting Company
					</div>
				</div>
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-reorder"></i>General
						</div>
						<div class="tools">
							<a href="javascript:;" class="collapse">
							</a>
							
						</div>
					</div>
					<div class="portlet-body form">
						<div class="form-body">
						<?php
						echo $this->session->userdata('message');
						$this->session->unset_userdata('message');
						?>

						
							<div class="col-md-6">
									<div class="form-group">
										<label class="control-label">Company Name
										<span class="required">
										*
										</span>
										</label>
										<input type="text" class="form-control" name="company_name" id="company_name" onChange="" placeholder="IGN" value="<?php echo set_value('company_name',$result['company_name']);?>"/>
									</div>
							</div>


						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">Address
								</label>
								<?php echo form_textarea(array('name'=>'company_address','rows'=>'3','class'=>'form-control','id'=>'company_address','value'=>set_value('company_address',$result['company_address']))) ?>
							</div>
						</div>

	

							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Phone 1
									</label>
									<input type="text" class="form-control" name="company_home_phone1" id="company_home_phone1" onChange="" placeholder="Surakarta" value="<?php echo set_value('company_home_phone1',$result['company_home_phone1']);?>"/>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Phone 2
									</label>
									<input type="text" class="form-control" name="company_home_phone2" id="company_home_phone2" onChange="" placeholder="Indonesia" value="<?php echo set_value('company_home_phone2',$result['company_home_phone2']);?>"/>
							</div>
							</div>
							
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Work Days
									</label>
									<input type="text" class="form-control" name="work_days" id="work_days" onChange="warningworkdays(this.value);" placeholder="5" value="<?php echo set_value('work_days',$result['work_days']);?>"/>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Work Shift
									</label>
									<input type="text" class="form-control" name="work_shift" id="work_shift" onChange="warningworkshift(this.value);" placeholder="8" value="<?php echo set_value('work_shift',$result['work_shift']);?>"/>
							</div>
							</div>

						
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Fax
									</label>
									<input type="text" class="form-control" name="company_fax_number" id="company_fax_number" onChange="" placeholder="57784" value="<?php echo set_value('company_fax_number',$result['company_fax_number']);?>"/>
							</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Tax
									</label>
									<input type="text" class="form-control" name="company_tax_number" id="company_tax_number" placeholder="0271" onChange="warningtax(this.value);" value="<?php echo set_value('company_tax_number',$result['company_tax_number']);?>"/>
								</div>
							</div>
							
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Tax Date
									</label>
									<div class="input-group">
										<div class="input-group input-medium date date-picker" data-date-format="dd-mm-yyyy">
											<input name="company_tax_date" id="company_tax_date" type="text" class="form-control" value="<?php if (empty($result['company_tax_date'])){
														echo "";
													}else{
														echo tgltoview($result['company_tax_date']);
												}?>" readonly>
											<span class="input-group-btn">
												<button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
											</span>
										</div>
								</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">PPn
									</label>
									<input type="text" class="form-control" name="ppn" id="ppn" onChange="" placeholder="10" value="<?php echo set_value('ppn',$result['ppn']);?>"/>
							</div>
							</div>
							
							<div class="col-md-6 ">
							<div class="form-group">
								<label class="control-label">Current Period</label>
								<?php
									echo form_dropdown_search('company_current_period', $period,set_value('company_current_period',$result['company_current_period']),'id="company_current_period"');
									?>
	
							</div>
						</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Fiscal Year
									</label>
										<div class="input-group input-small">
											<input type="text" name="company_fiscal_year" id="company_fiscal_year" class="spinner-input form-control" value="<?php if (empty($result['company_fiscal_year'])){
													echo date('Y');
												}else{
													echo $result['company_fiscal_year'];
												}?>" maxlength="6">
													<div class="spinner-buttons input-group-btn btn-group-vertical">
														<button type="button" id="tambah" onClick='plus();' class="btn spinner-up btn-xs blue">
														<i class="fa fa-angle-up"></i>
														</button>
														<button type="button" id="kurang" onClick='minus();' class="btn spinner-down btn-xs blue">
														<i class="fa fa-angle-down"></i>
														</button>
													</div>
												</div>		
							</div>
							</div>
							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label">Last Period</label>
									<?php
										echo form_dropdown_search('company_last_period', $period,set_value('company_last_period',$result['company_last_period']),'id="company_last_period"');
										?>
		
								</div>
							</div>
						
							<div class="row">	
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Using Period</label>
									<div class="input-group">
											<div class="input-group date date-picker" data-date-format="M yyyy">
											<input name="company_using_period" id="company_using_period" type="text" class="form-control" value="<?php if (empty($result['company_using_period'])){
													echo periodtoview(date('M yyyy'));
												}else{
													echo periodtoview($result['company_using_period']);
												}?>" readonly>
											<span class="input-group-btn">
												<button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
											</span>
										</div>	
										</div>
								</div>
								</div>
							
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Relative Path
									</label>
									<input type="text" class="form-control" name="stockist_application_relative_path" id="stockist_application_relative_path" value="<?php echo set_value('stockist_application_relative_path',$result['stockist_application_relative_path']);?>"/>
								</div>
							</div>
							</div>
							<div class="row">	
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Expired Notification Days
									</label>
									<input type="text" class="form-control" name="expired_days_notification" id="expired_days_notification" value="<?php echo set_value('expired_days_notification',$result['expired_days_notification']);?>"/>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Setting Decrease Stock
									</label>
									<?php 
									$setting_stock = array(
										'0' =>'Allow Minus',
										'1' =>'Not Allow Minus',
									);
									echo form_dropdown_search('default_setting_stock', $setting_stock, set_value('default_setting_stock',$result['default_setting_stock']),'id="default_setting_stock"');?>
								</div>
							</div>
							</div>
							<div class="row">	
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Minimal Stock Warning Type
									</label>
									
									<?php 
									$minimal_stock_warning_type = array(
										'0' =>'Per Item',
										'1' =>'Per Warehouse',
									);
									echo form_dropdown_search('minimal_stock_warning_type', $minimal_stock_warning_type, set_value('minimal_stock_warning_type',$result['minimal_stock_warning_type']),'id="minimal_stock_warning_type"');?>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Minimal Stock Warning Percentage
									</label>
									<input type="text" class="form-control" name="minimal_stock_warning_percentage" id="minimal_stock_warning_percentage" value="<?php echo set_value('minimal_stock_warning_percentage',$result['minimal_stock_warning_percentage']);?>"/>
								</div>
							</div>

							<div class="col-md-12">
								<div class="form-group">
								<label class="control-label">Default Password</label>
								<input type="password" readonly name="username" class="form-control" id="opassword" name="opassword" placeholder="Admin" value="<?php echo $result['default_password'];?>"/>
							</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
								<label class="control-label">Change Password
								</label>
	
								<button class="btn btn-success" type="button" id='btn-change' onClick="openform()">Change Default Password</button>
								<table style="display:none;" id='passwordf' cellpadding="7">
											<tr>
										<td>New Default Password * </td>
										<td><input type="password" class="form-control" name="password" id="password" size='30'/></td>
									</tr>
									<tr>
										<td>Confirm New Default Password ** </td>
										<td><input type="password" class="form-control" name="re_password" id="re_password" size='30'/></td>
									</tr>
								</table>
							
							</div>
							</div>

							<div class="col-md-12">	
								<div class="form-group">
									<label class="control-label">Logo
									</label>
									<input type="file" name="company_logo"  id="company_logo" size="50"/>
							<?php
								if($result['company_logo']!=''){
									echo "<br><img src='".base_url()."img/".$result['company_logo']."' height='150' width='150'>";
								}
							?>
								</div>
							</div>
							
							
							<div class='col-md-3'>
								<div class="form-group">
									<label class="control-label"> Refresh Rate (seconds)<span class="required">*</span></label>
									<input type="text" class="form-control" name="refreshrate" id="refreshrate" onchange='refreshratechange(this.value)' value="<?php echo $result['refreshrate']; ?>">
								</div>
							</div>

						<div class="row">	
						</div>
						<div class="form-actions right">
							<input type="submit" name="Save" value="Save" class="btn green" title="Simpan Data">
						</div>
						</div>
					</div>
			   </div>
		</div>
		
	</div>
</div>
<?php echo form_close(); ?>