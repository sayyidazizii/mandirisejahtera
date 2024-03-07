<script>
	function ulang(){
		document.getElementById("username").value = "";
		document.getElementById("password").value = "";
		document.getElementById("user_group_id").value = "";
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
	function warningrepassword(inputname){
		var letter = /^[a-zA-Z-,]+(\s{0,1}[a-zA-Z-, ])*$/;
		if(inputname.value.match(letter)){
			return true;
		}else{
			alert('Please input alphanumeric characters only');
			document.getElementById("re_password").value = "";	
			$('#re_password').focus();
			return false;
		}
	}
</script>

<div class="workplace" style="padding:5px !important;"> 
<?php echo form_open('user/processEditUser',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
	<?php
		echo $this->session->userdata('message');
		$this->session->unset_userdata('message');
		$logstat = array('off'=>'off','on'=>'on');
		$auth = $this->session->userdata('auth');
		if($this->uri->segment(3)=='Administrator'){
			$group['1']=$auth['username'];
		}
	?>
	<div class="row">
		<div class="col-md-12">
			<!-- BEGIN PAGE TITLE & BREADCRUMB-->
			<h3 class="page-title">
			Edit User
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
					<a href="<?php echo base_url();?>user/Edit/<?php echo $this->uri->segment(3);?>">
						Edit User
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
			</div>
		</div>
	</div>
				
	<div class="row">	
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
									<label class="control-label">Username
									<span class="required">
									*
									</span>
									</label>
									<input type="text" readonly name="username" class="form-control" id="username" placeholder="Admin" value="<?php echo $result['username'];?>"/>
									<input type="hidden" name="last_username" class="form-control" id="last_username" value="<?php echo $result['username'];?>"/>
							</div>
							<div class="form-group">
								<label class="control-label">Change Password
								</label>
	
								<button class="btn btn-success" type="button" id='btn-change' onClick="openform()">Change Password</button>
								<table style="display:none;" id='passwordf' cellpadding="7">
											<tr>
										<td>New Password * </td>
										<td><input type="password" class="form-control" name="password" id="password" size='30'/></td>
									</tr>
									<tr>
										<td>Confirm New Password ** </td>
										<td><input type="password" class="form-control" name="re_password" id="re_password" size='30'/></td>
									</tr>
								</table>
							
							</div>
							
							<div class="form-group">
								<label class="control-label">Group
								<span class="required">
								*
								</span>
								</label>
	
								<?php echo form_dropdown('user_group_id', $group,$result['user_group_id'],'id="user_group_id", class="form-control"');?>
							</div>
						
							
							<div class="form-group">
								<label class="control-label">Login State
								<span class="required">
								*
								</span>
								</label>
					
								<?php echo form_dropdown('log_stat', $logstat,$result['log_stat'],'id="log_stat", class="form-control"');?>
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
	
	
<?php echo form_close(); ?>
</div>