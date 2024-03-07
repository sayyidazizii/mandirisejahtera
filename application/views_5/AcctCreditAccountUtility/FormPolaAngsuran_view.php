<style>

th, td {
  padding: 3px;
}
td {
  font-size: 12px;
}
input:focus { 
  background-color: 42f483;
}
.custom{

margin: 0px; padding-top: 0px; padding-bottom: 0px; height: 50px; line-height: 50px; width: 50px;

}
.textbox .textbox-text{
font-size: 10px;


}

</style>
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$sesi=$this->session->userdata('polaAngsuran-AcctCreditsAccount');

?>	
	<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Pola Angsuran
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
				
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
					<?php echo form_close(); ?>
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
				<!-- END EXAMPLE TABLE PORTLET-->
			</div>
		</div>