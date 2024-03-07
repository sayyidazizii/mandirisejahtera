<script>
	function ulang(){
		document.getElementById("username").value = "";
		document.getElementById("password").value = "";
		document.getElementById("user_group_id").value = "";
	}
</script>
<style>
	th{
		font-size:14px  !important;
		font-weight: bold !important;
		text-align:center !important;
		margin : 0 auto;
		vertical-align:middle !important;
	}
	td{
		font-size:12px  !important;
		font-weight: normal !important;
	}
	
	select{
		display: inline-block;
		padding: 4px 6px;
		margin-bottom: 0px !important;
		font-size: 14px;
		line-height: 20px;
		color: #555555;
		-webkit-border-radius: 3px;
		-moz-border-radius: 3px;
		border-radius: 3px;
	}
	
	label {
		display: inline !important;
		width:50% !important;
		margin:0 !important;
		padding:0 !important;
		vertical-align:middle !important;
	}
</style>
<?php //echo form_open('user/processAddUser',array('id' => 'myform')); ?>
	<?php
		echo $this->session->userdata('message');
		$this->session->unset_userdata('message');
		$data = $this->session->userdata('AddUser');
		// $a=tgltodb('02/03/1993');
		// print_r($a); exit;
	?>
	<div class="row">
				<div class="col-md-12">
					<!-- BEGIN PAGE TITLE & BREADCRUMB-->
					<h3 class="page-title">
					User
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
					</ul>
					<!-- END PAGE TITLE & BREADCRUMB-->
				</div>
		</div>
		<div class="row">       
			<div class="col-md-12">  
				<div class="portlet">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-shopping-cart"></i>User List
							</div>
							<div class="actions">
								<a href="<?php echo base_url();?>user/add" class="btn default yellow-stripe">
									<i class="fa fa-plus"></i>
									<span class="hidden-480">
										 New User
									</span>
								</a>
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
								<i class="fa fa-globe"></i>User
							</div>
						</div>
						<div class="portlet-body">
						<div class="form-body">
							<table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
							<thead>
							<tr>
								<th width="5%">
									No.
								</th>
								<th width="25%">
									Username
								</th>
								<th width="25%">
									Group
								</th>
								<th width="25%">
									Action
								</th>
							</tr>
							</thead>
							<tbody>
							<?php
								$no = 1;
								foreach ($user->result_array() as $key=>$val){
									if($val['user_group_id'] != '1'){
									echo"
										<tr>			
											<td style='text-align:center'>$no.</td>
											<td>$val[username]</td>
											<td>".$this->user_model->getGroupName($val[user_group_id])."</td>
											<td>
												<a href='".$this->config->item('base_url').'user/Edit/'.$val[username]."' class='btn default btn-xs purple'>
													<i class='fa fa-edit'></i> Edit
												</a>
												<a href='".$this->config->item('base_url').'user/delete/'.$val[username]."'class='btn default btn-xs red', onClick='javascript:return confirm(\"apakah yakin ingin dihapus ?\")'>
													<i class='fa fa-trash-o'></i> Delete
												</a>
											</td>
										</tr>
									";
									$no++;
									}
							} ?>
							</tbody>
							</table>
						</div>
						</div>
					</div>
					<!-- END EXAMPLE TABLE PORTLET-->
				</div>
			</div>
<?php echo form_close(); ?>