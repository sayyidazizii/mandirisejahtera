<script>
	function CheckedAll () {
		for (var i = 0; i < document.getElementById('myform').elements.length; i++) {
		  document.getElementById('myform').elements[i].checked = true;
		}
    }
	
	function UnCheckedAll () {
		for (var i = 0; i < document.getElementById('myform').elements.length; i++) {
		  document.getElementById('myform').elements[i].checked = false;
		}
    }
	function ulang(){
		document.getElementById("user_group_name").value = "";
	}
	
	function warninggroupname(inputname){
		var letter = /^[a-zA-Z-,]+(\s{0,1}[a-zA-Z-, ])*$/;
		if(inputname.value.match(letter)){
			return true;
		}else{
			alert('Please input alphanumeric characters only');
			document.getElementById("user_group_name").value = "";	
			$('#user_group_name').focus();
			return false;
		}
	}
</script>

<?php echo form_open('usergroup/processEditUserGroup',array('id' => 'myform', 'class' => 'form-horizontal')); ?>
	<?php
		echo $this->session->userdata('message');
		$this->session->unset_userdata('message');
		echo form_hidden('user_group_id', $result['user_group_id']);
		echo form_hidden('old_user_group_name', $result['user_group_name']);
	?>
	<div class="row">
				<div class="col-md-12">
					<!-- BEGIN PAGE TITLE & BREADCRUMB-->
					<h3 class="page-title">
					User Group Edit
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
							<a href="<?php echo base_url();?>usergroup">
								User Group
							</a>
							<i class="fa fa-angle-right"></i>
						</li>
						<li>
							<a href="<?php echo base_url();?>usergroup/Edit/"<?php echo $this->uri->segment(3); ?>>
								User Group Edit
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
					<div class="portlet-body">
						<div class="form-body">
						<div class="form-group">
								<label class="col-md-2 control-label">Name Group
								<span class="required">
								*
								</span>
								</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="user_group_name" id="user_group_name" placeholder="Name User" onChange="warninggroupname(user_group_name);" value="<?php echo set_value('user_group_name',$result['user_group_name']);?>"/>
							</div>
							</div>	
						<div class="form-group">
								<label class="col-md-2 control-label">Privilege  Menu
								<span class="required">
								*
								</span>
								</label>
							<div class="col-md-6">
								<ul>
						<?php
							$auth		= $this->session->userdata('auth');
							$menulist1 	= $this->usergroup_model->getMenuList("_");
							foreach($menulist1 as $key=>$val){
								if($val['type']=='folder'){
									echo "<li>";
									echo '<label>'.$val['text']."</label>";
										echo "<ul>";
											$menulist2 = $this->usergroup_model->getMenuList($val['id_menu']."_");
											foreach($menulist2 as $key2=>$val2){
												if($val2['type']=='folder'){
													echo "<li>";
													echo '<label>'.$val2['text'].'</label>';
													echo "<ul>";
													$menulist3 = $this->usergroup_model->getMenuList($val2['id_menu']."_");
													foreach($menulist3 as $key3=>$val3){
														if($val3['type']=='folder'){
															echo "<li>";
															echo '<label>'.$val3['text'].'</label>';
															echo "<ul>";
															$menulist4 = $this->usergroup_model->getMenuList($val3['id_menu']."_");
															foreach($menulist4 as $key4=>$val4){
																	if($val4['type']=='folder'){
																	echo "<li>";
																	echo '<label>'.$val3['text'].'</label>';
																	}
																	else{
																		if($val4['id_menu']!='21' && $val4['id_menu']!='22'){
																		echo "<li>";
																		echo form_checkbox($val4['id_menu']."_FT",1,$this->usergroup_model->isThisMenuInGroup($result['user_group_id'],$val4['id_menu']),'');
																		echo "<label> ".$val4['text']."</label>";
																		echo "</li>";
																		}else if($auth['user_group_level']=='1' || $auth['user_group_level']=='2'){
																		echo "<li>";
																		echo form_checkbox($val4['id_menu']."_FT",1,$this->usergroup_model->isThisMenuInGroup($result['user_group_id'],$val4['id_menu']),'');
																		echo "<label> ".$val4['text']."</label>";
																		echo "</li>";
																	}else {continue;}
																	}
																}
																echo "</ul>";
															echo "</li>";
													}
													else
													{
													if($val3['id_menu']!='21' && $val3['id_menu']!='22'){
														echo "<li>";
														echo form_checkbox($val3['id_menu']."_FT",1,$this->usergroup_model->isThisMenuInGroup($result['user_group_id'],$val3['id_menu']),'');
														echo "<label> ".$val3['text']."</label>";
														echo "</li>";
													}else if($auth['user_group_level']=='1' || $auth['user_group_level']=='2'){
														echo "<li>";
														echo form_checkbox($val3['id_menu']."_FT",1,$this->usergroup_model->isThisMenuInGroup($result['user_group_id'],$val3['id_menu']),'');
														echo "<label> ".$val3['text']."</label>";
														echo "</li>";
													}else {continue;}
													
													}
													}
													echo "</ul>";
												echo "</li>";
													
												} else {
													if($val2['id_menu']!='21' && $val2['id_menu']!='22'){
														echo "<li>";
														echo form_checkbox($val2['id_menu']."_FT",1,$this->usergroup_model->isThisMenuInGroup($result['user_group_id'],$val2['id_menu']),'');
														echo "<label> ".$val2['text']."</label>";
														echo "</li>";
													}else if($auth['user_group_level']=='1' || $auth['user_group_level']=='2'){
														echo "<li>";
														echo form_checkbox($val2['id_menu']."_FT",1,$this->usergroup_model->isThisMenuInGroup($result['user_group_id'],$val2['id_menu']),'');
														echo "<label> ".$val2['text']."</label>";
														echo "</li>";
													}else {continue;}
												}
											}
										echo "</ul>";
									echo "</li>";
								} else {
									echo "<li>";
									echo form_checkbox($val['id_menu']."_FT",1,$this->usergroup_model->isThisMenuInGroup($result['user_group_id'],$val['id_menu']),'');
									echo "<label> ".$val['text']."</label>";
									echo "</li>";
								}
							}
						?>
						</ul>
						<a href="javascript:CheckedAll()" title="Check All">Check All</a> / <a href="javascript:UnCheckedAll()" title="UnCheck All">UnCheck All</a>
							
							</div>
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
		</div>
	</div>
	<!--<div class="span12" style="margin-left: 0px !important;"><?php echo anchor('usergroup', '<button type="button" class="btn btn-success">Kembali</button>', 'title="Kembali ke daftar user group"');?></div>
	<div class="row-fluid">
		<div class="span12">
			<div class="head">
				<div class="isw-documents"></div>
					<h1>Form Tambah Data Group User Baru</h1>
				<div class="clear"></div>
			</div>
			<div class="block-fluid">                        
				<div class="row-form">
					<div class="span2">Nama Group</div>
					<div class="span1">:</div>
					<div class="span9" style="margin-left:5px;"><input type="text" name="user_group_name" id="user_group_name" placeholder="Nama Group User" value="<?php echo $result['user_group_name'];?>" /></div>
					<div class="clear"></div>
				</div> 
				
				<div class="row-form">
					<div class="span2">Privilege  Menu</div>
					<div class="span1">:</div>
					<div class="span9" style="margin-left:5px;">
						<ul>
						<?php
							$auth		= $this->session->userdata('auth');
							$menulist1 	= $this->UserGroup_model->getMenuList("_");
							foreach($menulist1 as $key=>$val){
								if($val['type']=='folder'){
									echo "<li>";
									echo '<label>'.$val['text']."</label>";
										echo "<ul>";
											$menulist2 = $this->UserGroup_model->getMenuList($val['id_menu']."_");
											foreach($menulist2 as $key2=>$val2){
												if($val2['type']=='folder'){
													echo "<li>";
													echo '<label>'.$val2['text'].'</label>';
												} else {
													if($val2['id_menu']!='21' && $val2['id_menu']!='22'){
														echo "<li>";
														echo form_checkbox($val2['id_menu']."_FT",1,$this->UserGroup_model->isThisMenuInGroup($result['user_group_id'],$val2['id_menu']),'');
														echo "<label> ".$val2['text']."</label>";
														echo "</li>";
													}else if($auth['user_group_level']=='1' || $auth['user_group_level']=='2'){
														echo "<li>";
														echo form_checkbox($val2['id_menu']."_FT",1,$this->UserGroup_model->isThisMenuInGroup($result['user_group_id'],$val2['id_menu']),'');
														echo "<label> ".$val2['text']."</label>";
														echo "</li>";
													}else {continue;}
												}
											}
										echo "</ul>";
									echo "</li>";
								} else {
									echo "<li>";
									echo form_checkbox($val['id_menu']."_FT",1,$this->UserGroup_model->isThisMenuInGroup($result['user_group_id'],$val['id_menu']),'');
									echo "<label> ".$val['text']."</label>";
									echo "</li>";
								}
							}
						?>
						</ul>
						<a href="javascript:CheckedAll()" title="Check All">Check All</a> / <a href="javascript:UnCheckedAll()" title="UnCheck All">UnCheck All</a>
					</div>
					<div class="clear"></div>
				</div>
				
				<div class="row-form">
					<div class="btn-group" align="right">
						<input type="submit" name="Save" value="Simpan" class="btn ttLT" title="Simpan Data">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>-->
<?php echo form_close(); ?>