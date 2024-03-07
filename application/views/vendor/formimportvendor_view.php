<?php echo form_open_multipart('vendor/processimportvendor',array('class' => 'form-horizontal')); ?>
	<?php
		echo $this->session->userdata('message');
		$this->session->unset_userdata('message');
		$data = $this->session->userdata('importvendor');
	?>
<div class="row">
	<div class="col-md-12">
		<!-- BEGIN PAGE TITLE & BREADCRUMB-->
		<h3 class="page-title">
			Vendor Import
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
				<a href="<?php echo base_url();?>vendor/import">
					Vendor Import
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
					<i class="fa fa-shopping-cart"></i>Vendor Import
				</div>
			</div>
			<div class="col-md-12 ">
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-reorder"></i>Example File
						</div>
						<div class="tools">
							<a href="javascript:;" class="collapse">
							</a>
							
						</div>
					</div>
					<div class="portlet-body form">
						<div class="form-body">
							<div class="form-group">
								<label class="col-md-6 control-label">Download one of the below files and add/update vendor
								</label>
								<div class="col-md-6">
								<a href="<?php echo $this->config->item('base_url')?>exampledata/vendor_blank.xlsx"><span class="btn btn-large btn-block btn-primary">Download Excel Template for NEW Vendor</span></a>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-6 control-label">OR
								</label>
								<div class="col-md-6">
								<a href="<?php echo $this->config->item('base_url')?>exampledata/vendor.xlsx"><span class="btn btn-large btn-block btn-primary">Download Excel Template for EXISTING Vendor</span></a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-12 ">
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-reorder"></i>Upload File .xls
						</div>
						<div class="tools">
							<a href="javascript:;" class="collapse">
							</a>
						</div>
					</div>
					<div class="portlet-body form">
						<div class="form-body">
							<div class="form-group">
								<label class="control-label col-md-3">Choose File</label>
								<div class="col-md-5">
									<div class="fileinput fileinput-new" data-provides="fileinput">
										<div class="input-group input-large">
											<div class="form-control uneditable-input span3" data-trigger="fileinput">
												<i class="fa fa-file fileinput-exists"></i>&nbsp;
												<span class="fileinput-filename">
												</span>
											</div>
											<span class="input-group-addon btn default btn-file">
												<span class="fileinput-new">
													Select file
												</span>
												<span class="fileinput-exists">
													Change
												</span>
												<input type="file" name="filexls">
											</span>
											<a href="#" class="input-group-addon btn default fileinput-exists" data-dismiss="fileinput">
												Remove
											</a>
										</div>
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
</div>

<?php echo form_close(); ?>