<?php
	//header
	date_default_timezone_set("Asia/Jakarta");
	$auth 	= $this->session->userdata('auth');
	// echo "asdasd";exit;
	if(!empty($auth)){
		$this->load->view('header');
?>
				<div class="page-content-wrapper">
                    <!-- BEGIN CONTENT BODY -->
                    <div class="page-content">
                        <!-- BEGIN PAGE HEADER-->
                        <!-- BEGIN THEME PANEL -->
                        
                        <!-- END THEME PANEL -->
                        <!-- BEGIN PAGE BAR -->
                        
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <?php
							$this->load->view($main_view['content'],$main_view);

							
						?>
                    </div>
                    <!-- END CONTENT BODY -->
                </div>
            <!-- BEGIN FOOTER -->
<?php
		$this->load->view('footer');
		$url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
	} else{
		redirect('ValidationProcess');
		echo "base url ";
		echo base_url();
	}
?>