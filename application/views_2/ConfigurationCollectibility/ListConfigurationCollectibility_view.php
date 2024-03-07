<style>
	th, td {
	  padding: 3px;
	}
	input:focus { 
	  background-color: 42f483;
	}


</style>
<script>
	base_url = '<?php echo base_url();?>';
</script>
<?php echo form_open('configuration-collectibility/process-add',array('id' => 'myform', 'class' => 'horizontal-form')); ?>

		<!-- BEGIN PAGE TITLE & BREADCRUMB-->
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li>
			<i class="fa fa-home"></i>
			<a href="<?php echo base_url();?>">
				Beranda
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>configuration-collectibility">
				Konfigurasi Kolektibilitas
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Konfigurasi Kolektibilitas
</h3>
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			 <div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
					</div>
				</div>
				<div class="portlet-body">
					<div class="form-body">
						<div class="row">						
							<div class="col-md-1"></div>
							<div class="col-md-8">
								<table width="100%" border="0">
									<?php 

									$no =1;
									// print_r($collectibility);
									foreach ($collectibility as $key => $val) {
										echo "
											<tr>
												<input type='hidden' class='easyui-textbox' name='".$key."' id='".$key."' value='".$key."' autofocus/>
												<input type='hidden' class='easyui-textbox' name='collectibility_id_".$key."' id='collectibility_id_".$key."' value='".$val['collectibility_id']."' autofocus/>

												<td width='25%'>".$no.". ".$val['collectibility_name']."</td>
												<td width='1%'>:</td>
												<td width='20%'><input type='text' class='easyui-textbox' name='collectibility_bottom_".$key."' id='collectibility_bottom_".$key."' value='".$val['collectibility_bottom']."' style='width: 60%' autofocus/></td>
												<td width='5%'>S.D</td>
												<td width='20%'><input type='text' class='easyui-textbox' name='collectibility_top_".$key."' id='collectibility_top_".$key."' value='".$val['collectibility_top']."' style='width: 60%' autofocus/></td>
												<td width='15%'>PPAP (%)</td>
												<td width='20%'><input type='text' class='easyui-textbox' name='collectibility_ppap_".$key."' id='collectibility_ppap_".$key."' value='".$val['collectibility_ppap']."' style='width: 60%' autofocus/></td>
											</tr>
										";
										$no++;
									} ?>
								</table>
							</div>
						</div>



						<div class="row">
							<div class="col-md-12" style='text-align:right'>
								<button type="reset" name="Reset" value="Reset" class="btn btn-danger" onClick="ulang();"><i class="fa fa-times"> Batal</i></button>
								<button type="submit" name="Save" value="Save" id="Save" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Simpan</i></button>
							</div>	
						</div>
					</div>
				</div>
			 </div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>