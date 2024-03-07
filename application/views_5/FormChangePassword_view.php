<script>
	
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
</script>

<div class="workplace" style="padding:5px !important;"> 
<?php echo form_open('User/processChangePassword',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
	<?php
		echo $this->session->userdata('message');
		$this->session->unset_userdata('message');
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
			<a href="<?php echo base_url();?>change-password/<?php echo $this->uri->segment(3);?>">
				Ganti Password
			</a>
		</li>
	</ul>
</div>
<!-- END PAGE TITLE & BREADCRUMB-->
<h3 class="page-title">
Form Ganti Password
</h3>
			
	<div class="row">	
		<div class="col-md-12">
		   <div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Form Edit
					</div>
				</div>
					<div class="portlet-body form">
						<div class="form-body">
							<div class = "row">
								<div class = "col-md-6">
									<div class="form-group form-md-line-input">
											<input type="text" readonly name="username1" class="form-control" id="username1" placeholder="Admin" value="<?php echo $result['username'];?>"/>
											<input type="hidden" name="last_username" class="form-control" id="last_username" value="<?php echo $result['username'];?>"/>

											<label class="control-label">Username
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
								</div>
							</div>
							
							
							<div class = "row">
								<div class = "col-md-12">
							
									<div class="form-actions right">
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