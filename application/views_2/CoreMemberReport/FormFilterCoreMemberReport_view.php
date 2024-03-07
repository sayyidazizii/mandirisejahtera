<style>
	th{
		font-size:14px  !important;
		font-weight: bold !important;
		text-align:center !important;
		margin : 0 auto;
		vertical-align:middle !important;
	}
	td{
		font-size:12px  !important;
		font-weight: normal !important;
	}
	
</style>
<?php 
	$sesi = $this->session->userdata('filter-CoreMemberReport');
?>
<div class="row-fluid">
	

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
			<a href="<?php echo base_url();?>member-report">
				Laporan Simpanan Anggota
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Laporan Simpanan Anggota
				</div>
			</div>
			

			<div class="portlet-body">
				<div class="form-body">

					<?php	echo form_open('member-report/viewport',array('id' => 'myform', 'class' => ''));  

						$auth = $this->session->userdata('auth');

						if(empty($data['member_character'])){
							$data['member_character'] = '';
						}

					?>

					<div class="row">						
						<div class="col-md-6">
							<div class="form-group  form-md-line-input">
								
								<?php echo form_dropdown('member_character', $membercharacter, set_value('member_character',$data['member_character']),'id="member_character" class="form-control select2me" style="width:70%"');?>
								<label class="control-label">Jenis Anggota</label>
							</div>
						</div>
					 <?php if($auth['branch_status'] == 1) { ?>
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('branch_id', $corebranch,set_value('branch_id',$sesi['branch_id']),'id="branch_id" class="form-control select2me" ');
								?>
								<label>Cabang</label>
							</div>
						</div>
						<?php } ?>
					</div>

					<div class="row">
						<div class="col-md-12" style='text-align:right'>
							<button type="submit" class="btn green-jungle" id="view" name="view" value="excel"><i class="fa fa-file-excel-o"></i> Export Excel</button>
							<button type="submit" class="btn green-jungle" id="view" name="view" value="pdf"><i class="fa fa-file-pdf-o"></i> Laporan Pdf</button>
						</div>	
					</div>
				</div>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
<?php echo form_close(); ?>