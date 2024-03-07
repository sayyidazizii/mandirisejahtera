<script>
	base_url = '<?php echo base_url();?>';
	function ulang(){
		document.getElementById("member_class_code").value 			= "<?php echo $corememberclass['member_class_code'] ?>";
		document.getElementById("member_class_name").value 			= "<?php echo $corememberclass['member_class_name'] ?>";
		document.getElementById("member_class_city").value 			= "<?php echo $corememberclass['member_class_city'] ?>";
	}
	$(document).ready(function(){
        $("#member_class_parent_id").change(function(){
		  var member_class_parent_id = $("#member_class_parent_id").val();
		  $.post(base_url + 'member-class/get-member-class-name',
		  {member_class_parent_id: member_class_parent_id},
				function(data) {
					$('#member_class_parent_name').val(data.member_class_name); 
				},
				'json'
			);
		});
    });
</script>
<?php echo form_open('member-class/process-edit',array('id' => 'myform', 'class' => 'horizontal-form')); ?>

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
			<a href="<?php echo base_url();?>member-class">
				Daftar Keanggotaan Member
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>member-class/edit/"<?php $this->uri->segment(3); ?>>
				Edit Keanggotaan Member 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->
<h3 class="page-title">
	Form Edit Keanggotaan Member 
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
						Form Edit
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>member-class" class="btn btn-default btn-sm">
							<i class="fa fa-angle-left"></i>
							<span class="hidden-480">
								Kembali
							</span>
						</a>
					</div>
				</div>
				<div class="portlet-body">
					<div class="form-body">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_class_code" id="member_class_code" value="<?php echo set_value('member_class_code',$corememberclass['member_class_code']);?>"/>
									<label class="control-label">Kode Keanggotaan Member<span class="required">*</span></label>
								</div>
							</div>
						
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_class_name" id="member_class_name" value="<?php echo set_value('member_class_name',$corememberclass['member_class_name']);?>"/>
									<label class="control-label">Nama Keanggotaan Member<span class="required">*</span></label>
								</div>
							</div>
							
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_class_mandatory_savings" id="member_class_mandatory_savings" value="<?php echo set_value('member_class_mandatory_savings',$corememberclass['member_class_mandatory_savings']);?>"/>
									<label class="control-label">Jumlah<span class="required">*</span></label>
								</div>
							</div>
						</div>


						<div class="row">
							<div class="col-md-12" style='text-align:right'>
								<button type="reset" name="Reset" value="Reset" class="btn btn-danger" onClick="ulang();"><i class="fa fa-times"> Batal</i></button>
								<button type="submit" name="Save" value="Save" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Simpan </i></button>
							</div>	
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<input type="hidden" class="form-control" name="member_class_id" id="member_class_id" placeholder="id" value="<?php echo set_value('member_class_id',$corememberclass['member_class_id']);?>"/>
<?php echo form_close(); ?>