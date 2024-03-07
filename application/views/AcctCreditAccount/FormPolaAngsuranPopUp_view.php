		<link href="<?php echo base_url();?>assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url();?>assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url();?>assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url();?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url();?>assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url();?>assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url();?>assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url();?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url();?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo base_url();?>assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url();?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url();?>assets/pages/css/profile.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url();?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url();?>assets/global/css/components-rounded.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="<?php echo base_url();?>assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url();?>assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url();?>assets/layouts/layout/css/themes/darkblue.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <link href="<?php echo base_url();?>assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url();?>assets/global/plugins/jstree/dist/themes/default/style.min.css" rel="stylesheet" type="text/css" />     
        <link href="<?php echo base_url();?>assets/global/easyui/themes/default/easyui.css" rel="stylesheet" type="text/css" />     
        <link rel="shortcut icon" href="favicon.ico" /> 
        <script src="<?php echo base_url();?>assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo base_url();?>assets/global/easyui/jquery.easyui.min.js" type="text/javascript"></script>
        <script src="<?php echo base_url();?>assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="<?php echo base_url();?>assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
        <script src="<?php echo base_url();?>assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="<?php echo base_url();?>assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="<?php echo base_url();?>assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
		<style>
			th, td {
			  padding: 3px;
			  font-size: 13px;
			}
			input:focus { 
			  background-color: 42f483;
			}
			.custom{

				margin: 0px; padding-top: 0px; padding-bottom: 0px; height: 50px; line-height: 50px; width: 50px;

			}
			.textbox .textbox-text{
				font-size: 13px;


			}

		</style>
		
	 <div class="portlet box blue">
	
			 <div class="portlet box blue">
			<div class="portlet-body">
				<div class="row">
					<div class="col-md-5">

					<?php echo form_open('credit-account/cek-pola-angsuran',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
					<table width="50%">
						<input type="hidden" class="easyui-textbox" name="id_credit" value="<?php echo $this->uri->segment(3); ?>">
						<tr>
							<td width="5%"></td>
							<td width="20%"> 
							<input class="easyui-radiobutton" name="pola_angsuran" value="0" label="Flat" <?php if($this->uri->segment(4) == '' || $this->uri->segment(4) == '0'){ echo 'checked'; } ?>><br>
							<input class="easyui-radiobutton" name="pola_angsuran" value="1" label="Sliding Rate" <?php if($this->uri->segment(4) == '1'){ echo 'checked'; } ?>></td>
						</tr><tr>
							<td width="5%"></td>
							<td width="20%"> <button type="submit" name="Save" value="Save" id="Save" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Cek Pola Angsuran</i></button></td>
						</tr>
						</table>
					
					</div>
					
					<div class="col-md-5">
						<table class="table style="width: 100%;" border="0" padding:"0">
						<thead>
							<tr>
								<th width="5%">Ke</th>
								<th width="10%">SISA</th>
								<th width="20%">POKOK</th>
								<th width="25%">BASIL</th>
								<th width="10%">JUMLAH</th>
							</tr>
						</thead>
							<tbody>
								<?php
									foreach($datapola as $key=>$val){
										echo'
										<tr>
											<td width="5%">'.$val['ke'].'</td>
											<td width="10%">'.number_format(abs($val['sisa_pokok'])).'</td>
											<td width="20%">'.number_format(abs($val['angsuran_pokok'])).'</td>
											<td width="25%">'.number_format(abs($val['angsuran_margin'])).'</td>
											<td width="10%">'.number_format(abs($val['angsuran'])).'</th>
										</tr>
										';
										
									}
								?>
							</tbody>
							</table>
					</div>
					
					
					</div>
			</div>
			</div>
		<?php echo form_close(); ?>