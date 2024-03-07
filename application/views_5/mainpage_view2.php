<?php
	//header
	date_default_timezone_set("Asia/Jakarta");
	$auth 	= $this->session->userdata('auth');
	// echo "asdasd";exit;
	if(!empty($auth)){
		$this->load->view('header');
?>
<!-- BEGIN CONTAINER -->
<div class="page-container">
	<!-- BEGIN SIDEBAR -->
	<div class="page-sidebar-wrapper">
		<div class="page-sidebar navbar-collapse collapse">
			<!-- BEGIN SIDEBAR MENU -->
			<ul class="page-sidebar-menu" data-auto-scroll="true" data-slide-speed="200">
				<!--<li class="sidebar-toggler-wrapper">
					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
					<!--<div class="sidebar-toggler hidden-phone">
					</div>-->
					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
				<!--</li>
				<li class="sidebar-search-wrapper">
					<!-- BEGIN RESPONSIVE QUICK SEARCH FORM -->
					<!--<form class="sidebar-search" action="extra_search.html" method="POST">
						<div class="form-container">
							<!-- <div class="input-box">
								<a href="javascript:;" class="remove">
								</a>
								<input type="text" placeholder="Search..."/>
								<input type="button" class="submit" value=" "/>
							</div>
							-->
						<!--</div>
					</form>
					<!-- END RESPONSIVE QUICK SEARCH FORM -->
				<!--</li>-->
<?php
$menu = $this->MainPage_model->getParentMenu($auth['user_group_level']);
// print_r($menu); exit;
foreach($menu as $key=>$val){
	$datamenu = $this->MainPage_model->getDataParentmenu($val['detect']);
	if($datamenu['id_menu'] == '1'){
		$class 		= $this->uri->segment(1);
	if($class==''){$class='MainPage';}
		$active		= $this->MainPage_model->getActive($class);
		$compare 	= $datamenu['id_menu'];
		if($active==$compare){$stat = 'active';}else{$stat='';}
	?>
	<li class="start <?php echo $stat; ?>">
		<a href="<?php echo base_url() ?>">
		<i class="fa fa-home"></i>
		<span class="title">
			Home
		</span>
		<span class="selected">
		</span>
		</a>
	</li>
<?php
	}else{
		$class 		= $this->uri->segment(1);
		if($class==''){$class='MainPage';}
		$active		= $this->MainPage_model->getActive($class);
		$compare 	= $datamenu['id_menu'];
		if($active==$compare){$stat = 'active';}else{$stat='';}
		if($active==$compare){
		$arrow='<span class="selected">
		</span>
		<span class="arrow open">
		</span>';
		}
		else{
		$arrow='
		<span class="arrow">
		</span>';
		}
		echo'
			<li class="'.$stat.'">
				<a href="javascript:;">
					<i class="fa '.$datamenu['image'].'"></i>
					<span class="title">
						'.$datamenu['text'].'
					</span>
					'.$arrow.'
				</a>
				<ul class="sub-menu">
		';
		$datasubmenu= $this->MainPage_model->getParentSubMenu2($auth['user_group_level'],$val['detect']);
		foreach($datasubmenu as $key2=>$val2){
			$idmenucari = substr($val2['id_menu'],0,2);
			// $countsubmenu=count($this->MainPage_model->getSubMenu($idmenucari));
			$countsubmenu=count($this->MainPage_model->getSubMenu2($idmenucari));
			if($countsubmenu > 1){
				$submenuopen=$this->MainPage_model->getDataParentmenu($idmenucari);
				$class2 		= $this->uri->segment(1);
					if($class2==''){$class2='MainPage';}
					$active2		= $this->MainPage_model->getActive2($class2);
					$compare2 		= $submenuopen['id_menu'];
					if($active2==$compare2){$stat2 = 'active';}else{$stat2='';}
					if($active2==$compare2){
						$arrow2='<span class="selected">
						</span>
						<span class="arrow open">
						</span>';
						}
						else{
						$arrow2='
						<span class="arrow">
						</span>';
						}
					echo'
						<li class="'.$stat2.'">
							<a href="javascript:;">
								<i class="fa '.$submenuopen['image'].'"></i>
								<span class="title">
									'.$submenuopen['text'].'
								</span>
								'.$arrow2.'
							</a>
							<ul class="sub-menu">
					';
				$datasubmenu2= $this->MainPage_model->getParentSubMenu3($auth['user_group_level'],$submenuopen['id_menu']);	
				// print_r($datasubmenu2);exit;
				foreach($datasubmenu2 as $key3=>$val3){
					$idmenucari2 = substr($val3['id_menu'],0,3);
					// print_r($idmenucari2);exit;
					$countsubmenu2=count($this->MainPage_model->getSubMenu2($idmenucari2));
					// print_r($countsubmenu2);exit;
					if($countsubmenu2 > 2){
						$submenuopen2=$this->MainPage_model->getDataParentmenu($idmenucari2);
						$class3 		= $this->uri->segment(1);
						if($class3==''){$class3='MainPage';}
						$active3		= $this->MainPage_model->getActive3($class3);
						$compare3 		= $submenuopen2['id_menu'];
						if($active3==$compare3){$stat3 = 'active';}else{$stat3='';}
						if($active3==$compare3){
								$arrow3='<span class="selected">
								</span>
								<span class="arrow open">
								</span>';
								}
								else{
								$arrow3='
								<span class="arrow">
								</span>';
								}
							echo'
								<li class="'.$stat3.'">
									<a href="javascript:;">
										<i class="fa '.$submenuopen2['image'].'"></i>
										<span class="title">
											'.$submenuopen2['text'].'
										</span>
										'.$arrow3.'
									</a>
									<ul class="sub-menu">
							';
						$datasubmenu3= $this->MainPage_model->getParentSubMenu4($auth['user_group_level'],$submenuopen2['id_menu']);	
						// print_r($datasubmenu3);exit;
						foreach($datasubmenu3 as $key4=>$val4){
							$idmenucari3 = substr($val4['id_menu'],0,4);
							// $countsubmenu3=count($this->MainPage_model->getSubMenu3($idmenucari));
							// if($countsubmenu3 > 3){
								// $submenuopen3=$this->MainPage_model->getDataParentmenu($idmenucari3);
								// $class4 		= $this->uri->segment(1);
								// if($class4==''){$class3='MainPage';}
								// $active4		= $this->MainPage_model->getActive4($class3);
								// $compare4 		= $submenuopen3['id_menu'];
								// if($active4==$compare4){$stat4 = 'active';}else{$stat4='';}
								// if($active4==$compare4){
									// $arrow4='
									// <span class="selected"></span>
									// <span class="arrow open"></span>
									// ';
								// }else{
									// $arrow4='
									// <span class="arrow"></span>
									// ';
								// }
									// echo'
										// <li class="'.$stat4.'">
											// <a href="javascript:;">
												// <i class="fa '.$submenuopen3['image'].'"></i>
												// <span class="title">
													// '.$submenuopen3['text'].'
												// </span>
												// '.$arrow4.'
											// </a>
											// <ul class="sub-menu">
									// ';
								// echo'	
								// </li>
								// </ul>
								// ';
							// }else{
								$submenuopen4=$this->MainPage_model->getDataParentmenu($val4['id_menu']);
								$judul=$submenuopen4['text'];
								if($this->uri->segment(1)==$submenuopen4['id']){
									$class = "active";
								}else{
									$class = "";
								}
								echo'
									<li class="'.$class.'">
									<a href="'.base_url().$submenuopen4['id'].'">
										<i class="fa '.$submenuopen4['image'].'"></i>
										'.$judul.'
									</a>
									</li>
								';
							// }
						}
						echo'	
						</li>
						</ul>
						';
					}
					else{
					$submenuopen3=$this->MainPage_model->getDataParentmenu($val3['id_menu']);
					$judul=$submenuopen3['text'];
					if($this->uri->segment(1)==$submenuopen3['id']){
						$class = "active";
					}else{
						$class = "";
					}
					echo'
						<li class="'.$class.'">
							<a href="'.base_url().$submenuopen3['id'].'">
								<i class="fa '.$submenuopen3['image'].'"></i>
								'.$judul.'
							</a>
						</li>
					';
				}
				}
				echo'	
			</ul>
			</li>
			';
			}else{
				$submenuopen2=$this->MainPage_model->getDataParentmenu($val2['id_menu']);
				// print_r($submenuopen2);exit;
				$judul=$submenuopen2['text'];
				if($this->uri->segment(1)==$submenuopen2['id']){
					$class = "active";
				}else{
					$class = "";
				}
				echo'
					<li class="'.$class.'">
						<a href="'.base_url().$submenuopen2['id'].'">
							<i class="fa '.$submenuopen2['image'].'"></i>
							'.$judul.'
						</a>
					</li>
				';
			}
		}
		echo'	
			</ul>
			</li>
		';
	}
}
?>
			</ul>
			<!-- END SIDEBAR MENU -->
		</div>
	</div>
	<!-- END SIDEBAR -->
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
			<div class="modal fade" id="portlet-config" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
							<h4 class="modal-title">Modal title</h4>
						</div>
						<div class="modal-body">
							 Widget settings form goes here
						</div>
						<div class="modal-footer">
							<button type="button" class="btn blue">Save changes</button>
							<button type="button" class="btn default" data-dismiss="modal">Close</button>
						</div>
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal -->
			<!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->
			<!-- BEGIN STYLE CUSTOMIZER -->
			<!--<div class="theme-panel hidden-xs hidden-sm">
				<div class="toggler">
				</div>
				<div class="toggler-close">
				</div>
				<div class="theme-options">
					<div class="theme-option theme-colors clearfix">
						<span>
							 THEME COLOR
						</span>
						<ul>
							<li class="color-black current color-default" data-style="default">
							</li>
							<li class="color-blue" data-style="blue">
							</li>
							<li class="color-brown" data-style="brown">
							</li>
							<li class="color-purple" data-style="purple">
							</li>
							<li class="color-grey" data-style="grey">
							</li>
							<li class="color-white color-light" data-style="light">
							</li>
						</ul>
					</div>
					<div class="theme-option">
						<span>
							 Layout
						</span>
						<select class="layout-option form-control input-small">
							<option value="fluid" selected="selected">Fluid</option>
							<option value="boxed">Boxed</option>
						</select>
					</div>
					<div class="theme-option">
						<span>
							 Header
						</span>
						<select class="header-option form-control input-small">
							<option value="fixed" selected="selected">Fixed</option>
							<option value="default">Default</option>
						</select>
					</div>
					<div class="theme-option">
						<span>
							 Sidebar
						</span>
						<select class="sidebar-option form-control input-small">
							<option value="fixed">Fixed</option>
							<option value="default" selected="selected">Default</option>
						</select>
					</div>
					<div class="theme-option">
						<span>
							 Sidebar Position
						</span>
						<select class="sidebar-pos-option form-control input-small">
							<option value="left" selected="selected">Left</option>
							<option value="right">Right</option>
						</select>
					</div>
					<div class="theme-option">
						<span>
							 Footer
						</span>
						<select class="footer-option form-control input-small">
							<option value="fixed">Fixed</option>
							<option value="default" selected="selected">Default</option>
						</select>
					</div>
				</div>
			</div>-->
			<link href="<?php echo base_url();?>assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
			<!-- END STYLE CUSTOMIZER -->
			<!-- BEGIN PAGE HEADER-->

			<!-- END PAGE HEADER-->
			<!-- BEGIN PAGE CONTENT-->
			<?php
				$this->load->view($main_view['content'],$main_view);
			?>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- END CONTENT -->
</div>
<!-- END CONTAINER -->

<?php
		$this->load->view('footer');
		$url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
	}else{
		redirect('validationprocess');
	}
?>