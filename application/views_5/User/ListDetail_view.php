 <?php echo anchor('user/add', '<button type="button" class="btn btn-success">Tambah Data</button>', 'title="Tambah data user baru"');?>
 <?php
// print_r(base_url()); exit; 
 
 ?>
 <div class="row-fluid">
        <div class="span12">    
			 <div class="widget">
            <div class="head dark">
				 <div class="icon"><span class="icosg-target1"></span></div>
				<h2>List User</h2>   
				<div class="clear"></div>
			</div>
			<div class="block-fluid ">
				<table class="fpTable" cellpadding="0" cellspacing="0" width="100%">
				<thead>
					<tr>                                    
						<th width="10%" class="TAC">Action</th>						
						<th width="30%">Username</th>
						<th width="25%">User Jabatan Name</th>
						<th width="25%">Log Stat</th>
					</tr>
				</thead>
				<tbody>
					<?php
						if(!is_array($item)){
							echo "<tr><th colspan='4'>Data is Empty</th></tr>";
						} else {
							foreach ($item as $key=>$val){
								echo"
									<tr>
										<td class='TAC'>
                                        <a href='".$this->config->item('base_url').'User/Edit/'.$val['username']."'><span class='icon-pencil'></span></a> 
                                        <a href='".$this->config->item('base_url').'User/delete/'.$val['username']."'><span class='icon-trash'></span></a>
										</td>
										<td>".$val['username']."</td>
										<td>".$this->User_model->getGroupName($val['user_group_id'])."</td>
										<td style='text-align  : Right !important;' >".$val['log_stat']."</td>
									</tr>
								";					
							}
						}
					?>
					
				</tbody>
				</table>
			<div class="clear"></div>
			
			</div>
			</div>
		</div>
	</div>



