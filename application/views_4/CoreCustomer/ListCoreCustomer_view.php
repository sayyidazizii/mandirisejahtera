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
</style>
<?php //echo form_open('user/processAddUser',array('id' => 'myform')); ?>
<div class="row-fluid">
			
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
				<a href="<?php echo base_url();?>CoreCustomer">
					Customer List
				</a>
				<i class="fa fa-angle-right"></i>
			</li>
		</ul>
	</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Customer List <small>Manage Customer</small>
</h3>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					List
				</div>
				<div class="actions">
					<a href="<?php echo base_url();?>CoreCustomer/addCoreCustomer" class="btn btn-default btn-sm">
						<i class="fa fa-plus"></i>
						<span class="hidden-480">
							Add New Customer
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
				<table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
				<thead>
				<tr>
					<th width="5%">
						 No
					</th>
					<th width="15%">
						Name
					</th>
					<th width="15%">
						Company Code
					</th>
					<th width="25%">
						Address
					</th>
					<th width="10%">
						Contact Person
					</th>
					<th width="15%">
						Phone
					</th>
					<th width="15%">
						Email
					</th>
					<th width="25%">
						Action
					</th>
				</tr>
				</thead>
				<tbody>
				<?php
					$no = 1;
					foreach ($corecustomer as $key=>$val){
						$detail = "<a href='".$this->config->item('base_url').'CoreCustomer/showdetail/'.$val['customer_id']."' class='btn default btn-xs yellow'><i class='fa fa-bars'></i> Detail</a>";
						echo"
							<tr>									
								<td>$no</td>
								<td>$val[customer_name]</td>
								<td>$val[customer_company_code]</td>
								<td>$val[customer_address]</td>
								<td>$val[customer_contact_person]</td>
								<td>$val[customer_phone_number]</td>
								<td>$val[customer_email]</td>
						
								<td>
									
									<a href='".$this->config->item('base_url').'CoreCustomer/editCoreCustomer/'.$val['customer_id']."' class='btn default btn-xs purple'><i class='fa fa-bars'></i> Edit</a>
									<a href='".$this->config->item('base_url').'CoreCustomer/delete/'.$val['customer_id']."'class='btn default btn-xs red', onClick='javascript:return confirm(\"apakah yakin ingin dihapus ?\")'>
													<i class='fa fa-trash-o'></i> Delete
												</a>
								</td>
							</tr>
						";
						$no++;
				} ?>
				</tbody>
				</table>
			</div>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>