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
			<a href="<?php echo base_url();?>savings-account">
				Master Data Tabungan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Master Data Tabungan <small>Kelola Master Data Tabungan</small>
</h3>
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$auth = $this->session->userdata('auth');

	$sesi=$this->session->userdata('filter-acctsavingsaccount');

	if(!is_array($sesi)){
		
		$sesi['savings_id']			= '';
		$sesi['branch_id']			= '';
	}
?>	
<?php	echo form_open('savings-account/filter',array('id' => 'myform', 'class' => '')); ?>

<script type="text/javascript">
		base_url = '<?php echo base_url();?>';

	function reset_search(){
		document.location = base_url+"savings-account/reset-list";
	}

</script>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Pencarian
				</div>
				<div class="tools">
					<a href="javascript:;" class='expand'></a>
				</div>
			</div>
			<div class="portlet-body display-hide">
				<div class="form-body form">
					 <!-- <div class = "row">
						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="<?php echo tgltoview($start_date);?>"/>
								<label class="control-label">Tanggal Awal
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="<?php echo tgltoview($end_date);?>"/>
								<label class="control-label">Tanggal Akhir
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>
					</div> -->

					 <div class = "row">
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('savings_id', $acctsavings,set_value('savings_id',$sesi['savings_id']),'id="savings_id" class="form-control select2me" ');
								?>
								<label>Jenis Tabungan</label>
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
						<div class="form-actions right">
							<button type="button" class="btn red" onClick="reset_search();"><i class="fa fa-times"></i> Batal</button>
							<button type="submit" class="btn green-jungle"><i class="fa fa-search"></i> Cari</button>
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Daftar
				</div>
				<div class="actions">
					<a href="<?php echo base_url();?>savings-account/add" class="btn btn-default btn-sm">
						<i class="fa fa-plus"></i>
						<span class="hidden-480">
							Input Rekening Tabungan Baru
						</span>
					</a>
				</div>
				<!-- <?php if($export_id == 1){ ?>
					<div class="actions" style="margin-right: 10px;">
						<a href='javascript:void(window.open("<?php echo base_url(); ?>savings-account/export-master-data","_blank","top=100,left=200,width=300,height=300"));' title="Export to Excel"  class="btn btn-default btn-sm">
							<i class="fa fa-download"></i>
							<span class="hidden-480">
								Export Data Tabungan
							</span>
						</a>
					</div>
					<?php }?> -->
			</div>
			<div class="portlet-body">
				<div class="form-body">
					<table class="table table-striped table-bordered table-hover table-full-width" id="myDataTable">
					<thead>
						<tr>
							<th width="5%">No</th>
							<th width="10%">Nomor Rekening</th>
							<th width="10%">Nama Anggota</th>
							<th width="10%">Jenis Tabungan</th>
							<th width="8%">Tanggal Buka</th>
							<th width="10%">Setoran Awal</th>
							<th width="10%">Saldo</th>
							<th width="15%">Action</th>
						</tr>
					</thead>
					<tbody></tbody>
					</table>
				</div>
			
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
<script type="text/javascript">
 
var table;
 
$(document).ready(function() {
 
    //datatables
    table = $('#myDataTable').DataTable({ 
 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "pageLength": 5,
        "order": [], //Initial no order.
        "ajax": {
            "url": "<?php echo site_url('savings-account/get-list')?>",
            "type": "POST"
        },
        "columnDefs": [
        { 
            "targets": [ 0 ], //first column / numbering column
            "orderable": false, //set not orderable
        },
        ],
 
    });
 
});
</script>
<?php echo form_close(); ?>