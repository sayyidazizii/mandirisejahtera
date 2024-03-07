
	<?php 
		echo form_open_multipart('restore/do_upload');
		echo $this->session->userdata('message');
		$this->session->unset_userdata('message');
	?>
	<div class="row">
				<div class="col-md-12">
					<!-- BEGIN PAGE TITLE & BREADCRUMB-->
					<h3 class="page-title">
					Restore
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
							<a href="<?php echo base_url();?>restore">
								Restore
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
						<i class="fa fa-shopping-cart"></i>Form Restore Sql File
					</div>
				</div>
				<div class="col-md-12">
			   <div class="portlet box red">
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
							<div class="row">
							<div class="col-md-6">
									<div class="form-group">
										<label class="control-label">Restore History : <b><big><?php echo SQL_DB; ?></big></b>  </label><br>
									<?php
									$filename=$root_path."logrestore.log";
									if (is_file($filename)){
										$fp  = fopen($filename, 'r');
										$content = fread($fp, filesize($filename));
										fclose($fp);
										$content=explode('
		',$content);
										$count = count($content);
										if ($count>5){
											for ($i=0;$i<5;$i++){
												echo $content[$i]."</br>";
											}
										} else {
											for ($i=0;$i<$count;$i++){
												echo $content[$i]."</br>";
											}
										}
									}
						?>
						<input type="file" name="userfile" size="20" />
					</div>
							</div>
							</div>

						<div class="form-actions right">
							<input type="submit" name="restore" value="Restore" class="btn green" title="Restore Data">
						</div>
					</div>
			   </div>
			   </div>
		</div>
		
	</div>
</div>
</div>
<?php echo form_close(); ?>
	