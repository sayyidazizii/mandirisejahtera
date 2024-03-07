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
	 <div class="portlet box blue">
	<div class="portlet-body">
	<?php echo form_open('credit-account/agunan-add/save',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
					<div class="form-body">
				<div class="row">						
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
								<select name="tipe" class="form-control">
								<option value="BPKB">BPKB</option>
								<option value="Sertifikat">Sertifikat</option>
								<option value="Lain-lain">Lain-Lain</option>
								</select>	
								<label class="control-label">Pilih tipe<span class="required">*</span></label>
						</div>
				</div>
				</div>
				<div class="row">						
					<div class="col-md-3">
						<div class="form-group form-md-line-input">
								<input type="text" class="form-control" name="nama" id="nama" autocomplete="off" value=""/>
								<label class="control-label">Nama<span class="required">*</span></label>
						</div>
					</div>
							<div class="col-md-3">
						<div class="form-group form-md-line-input">
								<input type="text" class="form-control" name="alamat" id="alamat" autocomplete="off" value=""/>
								<label class="control-label">Alamat<span class="required">*</span></label>
						</div>
					</div>
				</div>
				<div class="row">						
					<div class="col-md-3">
						<div class="form-group form-md-line-input">
								<input type="text" class="form-control" name="noseri" id="noseri" autocomplete="off" value=""/>
								<label class="control-label" id="seri">No.Seri<span class="required">*</span></label>
						</div>
					</div>
									<div class="col-md-3">
						<div class="form-group form-md-line-input">
								<input type="text" class="form-control" name="taksiran" id="taksiran" autocomplete="off" value=""/>
								<label class="control-label" id="seri">Taksiran<span class="required">*</span></label>
						</div>
					</div>
				</div>
								<div class="row">
							<div class="col-md-6">	
								<div class="form-group form-md-line-input">
								<textarea name="keterangan" class="form-control" rows="3" id="comment"></textarea>
									<label class="control-label">Keterangan
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12" style='text-align:left'>
								<button type="submit" name="Save" value="Save" id="Save" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Simpan</i></button>
							</div>	
						</div>
				</div>
				</div>
		</div>
		
	
	
			 <div class="portlet box blue">
			<div class="portlet-body">
<table class="table table-striped table-hover">
<tr>
	<th>No</th>
	<th>Nama</th>
	<th>Alamat</th>
	<th>No Seri</th>
	<th>Taksiran</th>
	<th>Keterangan</th>
</tr>
<?php $i=0; 
if(!empty($data)){
	foreach ($data as $row) { $i=$i+1; ?>
<tr>
	<td><?php echo $i; ?></td>
	<td><?php echo $row["nama"]; ?></td>
	<td><?php echo $row["alamat"]; ?></td>
	<td><?php echo $row["noseri"]; ?></td>
	<td><?php echo $row["taksiran"]; ?></td>
	<td><?php echo $row["tipe"]; ?></td>
</tr>
<?php }
} ?>
</table>
			</div>
			</div>
		<?php echo form_close(); ?>