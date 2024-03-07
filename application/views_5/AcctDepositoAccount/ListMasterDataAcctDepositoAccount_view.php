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
			<a href="<?php echo base_url();?>deposito-account">
				Master Data Simpanan Berjangka
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->


<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$auth = $this->session->userdata('auth');
	$sesi=$this->session->userdata('filter-masterdataacctdepositoaccount');

	if(!is_array($sesi)){
		// $sesi['start_date']			= date('Y-m-d');
		// $sesi['end_date']			= date('Y-m-d');
		$sesi['deposito_id']			= '';
		$sesi['branch_id']				= '';
	}
?>	
<?php	echo form_open('deposito-account/filter-master-data',array('id' => 'myform', 'class' => '')); 

	// $start_date			= $sesi['start_date'];
	// $end_date			= $sesi['end_date'];
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Master Data Simpanan Berjangka
				</div>
				
					<div class="actions" style="margin-right: 10px;">
						<a href='javascript:void(window.open("<?php echo base_url(); ?>deposito-account/export","_blank","top=100,left=200,width=300,height=300"));' title="Export to Excel"  class="btn btn-default btn-sm">
							<i class="fa fa-download"></i>
							<span class="hidden-480">
								Export Data Simpanan Berjangka
							</span>
						</a>
					</div>
				
			</div>
			<div class="portlet-body">
				<div class="form-body form">
					 <!-- <div class = "row">
						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="<?php echo tgltoview($start_date);?>" autocomplete="off"/>
								<label class="control-label">Tanggal Awal
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="<?php echo tgltoview($end_date);?>" autocomplete="off"/>
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
									echo form_dropdown('deposito_id', $acctdeposito,set_value('deposito_id',$sesi['deposito_id']),'id="deposito_id" class="form-control select2me" ');
								?>
								<label>Jenis Simpanan</label>
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

<?php echo form_close(); ?>

			<div class="portlet-body">
				<div class="form-body">
					<table class="table table-striped table-bordered table-hover table-full-width" id="myDataTable">
					<thead>
						<tr>
							<th width="5%">No</th>
							<th width="12%">Nomor SimpKa</th>
							<th width="15%">Nama Anggota</th>
							<th width="15%">Jenis Simpanan Berjangka</th>
							<th width="15%">Jenis Perpanjangan</th>
							<th width="12%">Nomor Seri</th>
							<th width="10%">Tanggal Buka</th>
							<th width="10%">Tanggal Jatuh Tempo</th>
							<th width="15%">Nominal</th>
							<th width="8%">Bunga</th>
						</tr>
					</thead>
					<tbody></tbody>
					</table>
				</div>
				<!-- <div class="row">
					<div class="col-md-12 " style="text-align  : right !important;">
						<a href='javascript:void(window.open("<?php echo base_url(); ?>deposito-account/export","_blank","top=100,left=200,width=300,height=300"));' title="Export to Excel"  class="btn btn-md green-jungle"><span class="glyphicon glyphicon-print"></span> Export Data</a>
					</div>
				</div> -->
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
            "url": "<?php echo site_url('deposito-account/get-master-data-list')?>",
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