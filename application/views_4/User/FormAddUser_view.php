<script>
	function ulang(){
		document.getElementById("user_name").value = "";				
		document.getElementById("username").value = "";
		document.getElementById("password").value = "";
		document.getElementById("user_group_id").value = "";
	}
	
	function show_date(value){
		if(value==1){
			document.getElementById('factory_display').style.display = "block";
		}else{
			document.getElementById('factory_display').style.display='none';
		}
	}
	
	function warningusername(inputname){
		var letter = /^[a-zA-Z-,]+(\s{0,1}[a-zA-Z-, ])*$/;
		if(inputname.value.match(letter)){
			return true;
		}else{
			alert('Please input alphanumeric characters only');
			document.getElementById("user_name").value = "";	
			$('#user_name').focus();
			return false;
		}
	}
	function warningusername2(inputname){
		var letter = /^[a-zA-Z-,]+(\s{0,1}[a-zA-Z-, ])*$/;
		if(inputname.value.match(letter)){
			return true;
		}else{
			alert('Please input alphanumeric characters only');
			document.getElementById("username").value = "";	
			$('#username').focus();
			return false;
		}
	}
	function warningpassword(inputname){
		var letter = /^[a-zA-Z-,]+(\s{0,1}[a-zA-Z-, ])*$/;
		if(inputname.value.match(letter)){
			return true;
		}else{
			alert('Please input alphanumeric characters only');
			document.getElementById("password").value = "";	
			$('#password').focus();
			return false;
		}
	}
</script>
<div class="workplace" style="padding:5px !important;"> 
<?php echo form_open('user/process-add',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
	<?php
		echo $this->session->userdata('message');
		$this->session->unset_userdata('message');
		$data = $this->session->userdata('AddUser');
		$auth = $this->session->userdata('auth');
	?>
					<!-- BEGIN PAGE TITLE & BREADCRUMB-->
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li>
			<i class="fa fa-home"></i>
			<a href="<?php echo base_url();?>">
				Home
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>user">
				User List
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>user/add">
				Add User
			</a>
		</li>
	</ul>
</div>
<h3 class="page-title">
Form Add User
</h3>					
<!-- END PAGE TITLE & BREADCRUMB-->
			
			
		<div class="row">
			<div class="col-md-12">
			   <div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							Form Add
						</div>
						<div class="actions">
							<a href="<?php echo base_url();?>user" class="btn btn-sm btn-default">
								<i class="fa fa-angle-left"></i>
								<span class="hidden-480">
									Back
								</span>
							</a>
						</div>
					</div>
					<div class="portlet-body form">
						<div class="form-body">
							<div class = "row">
								<div class = "col-md-6">
									<div class="form-group form-md-line-input">
										<input type="text" class="form-control" name="username" id="username" placeholder="Admin" onChange="warningusername2(username);" value="<?php echo set_value('username',$data['username']);?>"/>
										<label class="control-label">Username
										<span class="required">
										*
										</span>
										</label>
									</div>
								</div>
							
								<div class = "col-md-6">
									<div class="form-group form-md-line-input">
										<input type="password" class="form-control" name="password" id="password" value="<?php echo set_value('password',$data['password']);?>"/>
							
										<label class="control-label">Password
										<span class="required">
										*
										</span>
										</label>
									</div>
								</div>
							</div>
							
							<div class = "row">
								<div class = "col-md-6">
									<div class="form-group form-md-line-input">									
										<?php echo form_dropdown('user_group_id', $group,set_value('user_group_id',$data['user_group_id']),'id="user_group_id", class="form-control"');?>
										<label class="control-label">Jabatan
										<span class="required">
										*
										</span>
										</label>
									</div>
								</div>
							<?php if($auth['branch_status'] == 1){ ?>
								<div class="col-md-6">
									<div class="form-group form-md-line-input">
										<?php echo form_dropdown('branch_id', $corebranch, set_value('branch_id',$data['branch_id']),'id="branch_id" class="form-control select2me"');?>
										<label class="control-label">Kantor</label>
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group form-md-line-input">
										<?php echo form_dropdown('branch_status', $branchstatus, set_value('branch_status',$data['branch_status']),'id="branch_status" class="form-control select2me"');?>
										<label class="control-label">Branch Status</label>
									</div>
								</div>
							<?php } else { ?>
								<input type="hidden" class="form-control" name="branch_status" id="branch_status" value="0"/>
							<?php } ?>
								
							</div>
							
							<div class = "row">
								<div class = "col-md-12">
									<div class="form-actions right">
										<input type="reset" name="Reset" value="Reset" class="btn btn-danger" onClick="ulang();">
										<input type="submit" name="Save" value="Save" class="btn green-jungle" title="Simpan Data">
									</div>	
								</div>
							</div>
							
						</div>
					</div>
			   </div>
			</div>
		</div>
<?php echo form_close(); ?>
</div>