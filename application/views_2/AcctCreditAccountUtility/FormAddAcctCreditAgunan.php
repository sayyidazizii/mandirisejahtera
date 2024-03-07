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
        <link rel="shortcut icon" href="favicon.ico" /> 
        <script src="<?php echo base_url();?>assets/global/plugins/jquery.min.js" type="text/javascript"></script>
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
		<script>
		function toRp(number) {
			var number = number.toString(), 
			rupiah = number.split('.')[0], 
			cents = (number.split('.')[1] || '') +'00';
			rupiah = rupiah.split('').reverse().join('')
				.replace(/(\d{3}(?!$))/g, '$1,')
				.split('').reverse().join('');
			return rupiah + '.' + cents.slice(0, 2);
		}
		
		$(document).on('change','#bpkb_taksiran_view',function(event){
			bpkb_taksiran_view				= $('#bpkb_taksiran_view')[0].value;	
			
			document.getElementById('bpkb_taksiran_view').value	= toRp(bpkb_taksiran_view);
			document.getElementById('bpkb_taksiran').value		= bpkb_taksiran_view;
			
		});
	
		$(document).on('change','#shm_taksiran_view',function(event){
			shm_taksiran_view				= $('#shm_taksiran_view')[0].value;	
			
			document.getElementById('shm_taksiran_view').value	= toRp(shm_taksiran_view);
			document.getElementById('shm_taksiran').value		= shm_taksiran_view;
			
		});
		
		function formupdate(data){
			
			if(data.value == "Sertifikat"){
				 document.getElementById("shm").style.display = "block";
				 document.getElementById("bpkb").style.display = "none";
				
			}
				if(data.value == "BPKB"){
				 document.getElementById("shm").style.display = "none";
				 document.getElementById("bpkb").style.display = "block";
			}
			
			
		}
		</script>
	 <div class="portlet box blue">
	<div class="portlet-body">
	<?php echo form_open('credit-account/agunan-add/save',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
	<div class="form-body">
	<table style="width: 100%;" border="0" padding:"0">
<tbody  id="tipe" style="display:block" >
			<tr>
				<td>Pilih Tipe</td>
				<td> : </td>
				<td> <select name="tipe" class="form-control" onchange="formupdate(this)">
					<option value="pilih">Select</option>
					<option value="BPKB">BPKB</option>
					<option value="Sertifikat">Sertifikat</option>
					<option value="Lain-lain">Lain-Lain</option>
					</select>
				</td>
			</tr>
</tbody>
<tbody  id="bpkb" style="display:none">
			<tr>
				<td>BPKB</td>
				<td> : </td>
				<td>  <input type="text" class="form-control" name="bpkb_nomor" id="bpkb_nomor" autocomplete="off" value=""/>
				</td>
			</tr>
			<tr>
				<td>Nama</td>
				<td> : </td>
				<td> <input type="text" class="form-control" name="bpkb_nama" id="bpkb_nama" autocomplete="off" value=""/>
				</td>
			</tr>
			<tr>
				<td>No.Pol</td>
				<td> : </td>
				<td> <input type="text" class="form-control" name="bpkb_nopol" id="bpkb_nopol" autocomplete="off" value=""/>
				</td>
			</tr>
			<tr>
				<td>No.Mesin</td>
				<td> : </td>
				<td> <input type="text" class="form-control" name="bpkb_no_mesin" id="bpkb_no_mesin" autocomplete="off" value=""/>
				</td>
			</tr>
			<tr>
				<td>No.Rangka</td>
				<td> : </td>
				<td> <input type="text" class="form-control" name="bpkb_no_rangka" id="bpkb_no_rangka" autocomplete="off" value=""/>
				</td>
			</tr>
			<tr>
				<td>Taksiran Rp</td>
				<td> : </td>
				<td><input type="text" class="form-control" name="bpkb_taksiran_view" id="bpkb_taksiran_view" autocomplete="off" value=""/>
				<input type="hidden" class="form-control" name="bpkb_taksiran" id="bpkb_taksiran" autocomplete="off" value=""/>
				</td>
			</tr>
			<tr>
				<td>Keterangan</td>
				<td> : </td>
				<td><input type="text" class="form-control" name="bpkb_keterangan" id="bpkb_keterangan" autocomplete="off" value=""/>
				</td>
			</tr>
			</tbody>
			<tbody  id="shm" style="display:none">
			<tr>
				<td>No. Sertifikat</td>
				<td> : </td>
				<td>  <input type="text" class="form-control" name="shm_no_sertifikat" id="shm_no_sertifikat" autocomplete="off" value=""/>
				</td>
			</tr>
			<tr>
				<td>Luas</td>
				<td> : </td>
				<td> <input type="text" class="form-control" name="shm_luas" id="shm_luas" autocomplete="off" value=""/>
				</td>
			</tr>
			<tr>
				<td>Atas Nama</td>
				<td> : </td>
				<td> <input type="text" class="form-control" name="shm_atas_nama" id="shm_atas_nama" autocomplete="off" value=""/>
				</td>
			</tr>
			<tr>
				<td>Kedudukan</td>
				<td> : </td>
				<td><input type="text" class="form-control" name="shm_kedudukan" id="shm_kedudukan" autocomplete="off" value=""/>
				</td>
			</tr>
			<tr>
				<td>Taksiran Rp</td>
				<td> : </td>
				<td><input type="text" class="form-control" name="shm_taksiran_view" id="shm_taksiran_view" autocomplete="off" value=""/>
				<input type="hidden" hidden class="form-control" name="shm_taksiran" id="shm_taksiran" autocomplete="off" value=""/>
				</td>
			</tr>
			<tr>
				<td>Keterangan</td>
				<td> : </td>
				<td><input type="text" class="form-control" name="shm_keterangan" id="shm_keterangan" autocomplete="off" value=""/>
				</td>
			</tr>
			</tbody>

	</table>
	<div class="row">
		<div class="col-md-12" style='text-align:left'>
			<button type="submit" name="Save" value="Save" id="Save" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Simpan</i></button>
		</div>	
	</div>
	</div>
</div>
		
<?php
	$unique = $this->session->userdata('unique');

	$agunan_data = $this->session->userdata('agunan_data-'.$unique['unique']);

	print_r("agunan_data");
	print_r($agunan_data);
?>
	
			 <div class="portlet box blue">
			<div class="portlet-body">
<table class="table table-striped table-hover">
<tr>
	<th>No</th>
	<th>Type</th>
	<th>Keterangan</th>
</tr>
<?php 

$i=0; 
if(!empty($data)){
	$ket='';
	foreach ($data as $row) { $i=$i+1; 
		if($row["tipe"] == 'Sertifikat'){
			$ket='No. Sertifikat : '. $row["shm_no_sertifikat"].' Luas : '. $row["shm_luas"].' An : '. $row["shm_atas_nama"].' Kedudukan : '. $row["shm_kedudukan"].' Ket : '.$row['shm_keterangan'] ;
		}else{
			$ket='No. BPKB : '. $row["bpkb_nomor"].' Nama : '. $row["bpkb_nama"].' NoPol : '. $row["bpkb_nopol"].' No. Mesin : '. $row["bpkb_no_mesin"].' No. Rangka : '. $row["bpkb_no_rangka"].' Ket : '.$row['bpkb_keterangan'] ;
		}
	
	?>
<tr>
	<td><?php echo $i; ?></td>
	<td><?php echo $row["tipe"]; ?></td>
	<td><?php echo $ket; ?></td>
</tr>
<?php }
} ?>
</table>
			</div>
			</div>
		<?php echo form_close(); ?>