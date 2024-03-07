
	<?php 
		echo $this->session->userdata('message');
		$this->session->unset_userdata('message');
	?>
	<div class="row">
				<div class="col-md-12">
					<!-- BEGIN PAGE TITLE & BREADCRUMB-->
					<h3 class="page-title">
					About
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
							<a href="<?php echo base_url();?>about">
								About
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
						<i class="fa fa-shopping-cart"></i>About System
					</div>
				</div>
				<div class="portlet box red">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-reorder"></i>Version
						</div>
						<div class="tools">
							<a href="javascript:;" class="collapse">
							</a>
							
						</div>
					</div>
					<div class="portlet-body form">
						<div class="form-body">
							<div class="row">
								<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">Version : <b><big><?php echo '1.4.17'; ?></big></b>  </label><br>
										</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">Last Update : <b><big><?php echo '24 May 2016'; ?></big></b>  </label><br>
										</div>
								</div>
							</div>
						</div>
				   </div>
				</div>
		
			</div>
		</div>
	</div>
<?php echo form_close(); ?>
	