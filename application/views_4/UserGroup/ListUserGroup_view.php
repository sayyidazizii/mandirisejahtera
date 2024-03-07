<style>
	input[type="text"] {
		height:30px !important; 
		width:50% !important;
		margin : 0 auto;
	}
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
	
	.flexigrid div.pDiv input {
		vertical-align:middle !important;
	}
	
	.flexigrid div.pDiv div.pDiv2 {
		margin-bottom: 10px !important;
	}
</style>
	<?php 
		echo $this->session->userdata('message');
		$this->session->unset_userdata('message');
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
			<a href="<?php echo base_url();?>user-group">
				User Jabatan List
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
	<!-- END PAGE TITLE & BREADCRUMB-->
<h3 class="page-title">
	User Jabatan List <small>Manage User Jabatan</small>
</h3>		
			<div class="row">
				<div class="col-md-12">
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								List
							</div>
							<div class="actions">
								<a href="<?php echo base_url();?>user-group/add" class="btn btn-sm btn-default">
									<i class="fa fa-plus"></i>
									<span class="hidden-480">
										Add New User Jabatan
									</span>
								</a>
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
									Level
								</th>
								<th width="25%">
									Name
								</th>
								<th width="25%">
									Action
								</th>
							</tr>
							</thead>
							<tbody>
							<?php
								$no = 1;
								foreach ($usergroup as $key=>$val){
									if($val['user_group_id'] != '1'){
									echo"
										<tr>			
											<td style='text-align:center'>$no.</td>
											<td>$val[user_group_level]</td>
											<td>$val[user_group_name]</td>
											<td>
												<a href='".$this->config->item('base_url').'user-group/edit/'.$val['user_group_id']."' class='btn default btn-xs purple'>
													<i class='fa fa-edit'></i> Edit
												</a>
												<a href='".$this->config->item('base_url').'user-group/delete/'.$val['user_group_id']."'class='btn default btn-xs red', onClick='javascript:return confirm(\"apakah yakin ingin dihapus ?\")'>
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