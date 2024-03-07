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
		echo form_open('emailconfiguration/processconfiguration');
	?>
	<div class="row">
				<div class="col-md-12">
					<!-- BEGIN PAGE TITLE & BREADCRUMB-->
					<h3 class="page-title">
					Email Configuration
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
							<a href="<?php echo base_url();?>emailconfiguration">
								Email Configuration
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
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							Form Edit
						</div>
					</div>
					<div class="portlet-body">
						<div class="form-body">
						<?php
						echo $this->session->userdata('message');
						$this->session->unset_userdata('message');
						?>

						<h4 class = "form-section bold">Email Configuration</h4>
				
						<div class="row">
							<div class="col-md-6">
									<div class="form-group">
										<label class="control-label">Email Subject
										<span class="required">
										*
										</span>
										</label>
										<input type="text" class="form-control" name="email_subject" id="email_subject" placeholder="IGN" value="<?php echo set_value('email_subject',$result['email_subject']);?>"/>
									</div>
							</div>
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">Email Content
								</label>
								<?php echo form_textarea(array('name'=>'email_content','rows'=>'3','class'=>'form-control','id'=>'email_content','value'=>set_value('email_content',$result['email_content']))) ?>
							</div>
						</div>
						</div>

	

						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Email From
									</label>
									<input type="text" class="form-control" name="email_from" id="email_from" placeholder="Surakarta" value="<?php echo set_value('email_from',$result['email_from']);?>"/>
								</div>
							</div>
						</div>
						
						<h4 class = "form-section bold">Email Configuration for Send Training</h4>
						
						<div class="row">
							<div class="col-md-6">
									<div class="form-group">
										<label class="control-label">Email Subject
										<span class="required">
										*
										</span>
										</label>
										<input type="text" class="form-control" name="email_training_subject" id="email_training_subject" placeholder="IGN" value="<?php echo set_value('email_training_subject',$result2['email_training_subject']);?>"/>
									</div>
							</div>
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">Email Content
								</label>
								<?php echo form_textarea(array('name'=>'email_training_content','rows'=>'3','class'=>'form-control','id'=>'email_training_content','value'=>set_value('email_training_content',$result2['email_training_content']))) ?>
							</div>
						</div>
						</div>

	

						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Email From
									</label>
									<input type="text" class="form-control" name="email_training_from" id="email_training_from" value="<?php echo set_value('email_training_from',$result2['email_from']);?>"/>
								</div>
							</div>
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