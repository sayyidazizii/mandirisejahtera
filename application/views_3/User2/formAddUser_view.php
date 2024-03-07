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
<?php echo form_open('user/processAddUser',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
	<?php
		echo $this->session->userdata('message');
		$this->session->unset_userdata('message');
		$data = $this->session->userdata('AddUser');
	?>
<div class="row">
				<div class="col-md-12">
					<!-- BEGIN PAGE TITLE & BREADCRUMB-->
					<h3 class="page-title">
					Add User
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
							<a href="<?php echo base_url();?>user">
								User
							</a>
							<i class="fa fa-angle-right"></i>
						</li>
						<li>
							<a href="<?php echo base_url();?>user/Add">
								Add User
							</a>
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
						<i class="fa fa-shopping-cart"></i>User
					</div>
				</div>
				
				<div class="col-md-12">
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
						<div class="form-group">
								<label class="control-label">Name User
								<span class="required">
								*
								</span>
								</label>
								<input type="text" class="form-control" name="user_name" id="user_name" placeholder="Name User" onChange="warningusername(user_name);" value="<?php echo set_value('user_name',$data['user_name']);?>"/>
							</div>
						<div class="form-group">
								<label class="col-md-3 control-label">Username
								<span class="required">
								*
								</span>
								</label>
						
								<input type="text" class="form-control" name="username" id="username" placeholder="Admin" onChange="warningusername2(username);" value="<?php echo set_value('username',$data['username']);?>"/>
						
							</div>
							<div class="form-group">
								<label class="control-label">Password
								<span class="required">
								*
								</span>
								</label>
					
								<input type="password" class="form-control" name="password" id="password" value="<?php echo set_value('password',$data['password']);?>"/>
					
							
							</div>
							
							
							<div class="form-group">
								<label class="control-label">Group
								<span class="required">
								*
								</span>
								</label>
							
								<?php echo form_dropdown('user_group_id', $group,set_value('user_group_id',$data['user_group_id']),'id="user_group_id", class="form-control"');?>
		
							</div>
							
							<div class="form-actions right">
								<input type="reset" name="Reset" value="Reset" class="btn btn-danger" onClick="ulang();">
								<input type="submit" name="Save" value="Save" class="btn green" title="Simpan Data">
							</div>	
							
						</div>
					</div>
			   </div>
			   </div>
				
			</div>
		</div>
	</div>
	
	
<?php echo form_close(); ?>