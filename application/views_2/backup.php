<?php
		echo form_open('backup/backup_db');
	?>
<div class="row">
				<div class="col-md-12">
					<!-- BEGIN PAGE TITLE & BREADCRUMB-->
					<h3 class="page-title">
					Backup
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
							<a href="<?php echo base_url();?>backup">
								Backup
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
						<i class="fa fa-shopping-cart"></i>Form Dump Sql File
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
										<label class="control-label">Last Back Up Database History : <b><big><?php echo SQL_DB; ?></big></b> </label><br>
									<?php
							$filename=$root_path."logbackup.log";
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
						<input class="span2" size="16" type="hidden" name='status' id="status" value='1'/>
					</div>
							</div>
							</div>

						<div class="form-actions right">
							<input type="submit" name="backup" value="Back Up" class="btn green" title="Backup Data">
						</div>
					</div>
			   </div>
			   </div>
		</div>
		
	</div>
</div>
</div>
<?php echo form_close(); ?>