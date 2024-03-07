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
<script type="text/javascript">
	base_url = "<?php echo base_url(); ?>"
	function reset_search(){
		document.location = base_url = "credits-agunan/reset-search";
	}
</script>
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
			<a href="<?php echo base_url();?>credits-agunan">
				Master Data Agunan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Master Data Agunan <small>Kelola Master Data Agunan</small>
</h3>
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
	
	$auth = $this->session->userdata('auth');

	if($auth['branch_status'] == 1){

	$sesi = $this->session->userdata('filter-acctcreditsagunan');

	if(!is_array($sesi)){
		$sesi['branch_id']			= '';
	}
?>	
<?php	echo form_open('credits-agunan/filter',array('id' => 'myform', 'class' => '')); ?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Master Data Agunan
				</div>
				<div class="actions" style="margin-right: 10px;">
						<a href='javascript:void(window.open("<?php echo base_url(); ?>credits-agunan/export","_blank","top=100,left=200,width=300,height=300"));' title="Export to Excel" class="btn btn-default btn-sm">
							<i class="fa fa-download"></i>
							<span class="hidden-480">
								Export Master Data Agunan
							</span>
						</a>
					</div>
			</div>
			<div class="portlet-body">
				<div class="form-body form">
					 <div class = "row">
						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<?php echo form_dropdown('branch_id', $corebranch, set_value('branch_id',$sesi['branch_id']),'id="branch_id" class="form-control select2me"');?>
								<label class="control-label">Cabang
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-actions right">
							<button type="button" class="btn red" onClick="reset_search();"><i class="fa fa-times"></i> Batal</button>
							<button type="submit" class="btn green-jungle"><i class="fa fa-search"></i> Cari</button>
						</div>	
					</div>
				</div>
			</div>

<?php echo form_close(); } else {?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Master Data Agunan
				</div>
			</div>

			<?php } ?>

			<div class="portlet-body">
				<div class="form-body">
					<table class="table table-striped table-bordered table-hover table-full-width" id="myDataTable">
					<thead>
						<tr>
							<th width="5%">No</th>
							<th width="10%">Nomor Akad</th>
							<th width="10%">Nama Anggota</th>
							<th width="10%">Status Agunan</th>
							<th width="10%">Tipe Agunan</th>
							<th width="10%">Sertifikat</th>
							<th width="8%">Luas</th>
							<th width="10%">Atas Nama</th>
							<th width="10%">Kedudukan</th>
							<th width="10%">Taksiran</th>
							<th width="10%">BPKB</th>
							<th width="10%">Jenis</th>
							<th width="10%">Nama</th>
							<th width="10%">Alamat</th>
							<th width="10%">No. Polisi</th>
							<th width="10%">No. Rangka</th>
							<th width="10%">No. Mesin</th>
							<th width="10%">Nama Dealer</th>
							<th width="10%">Alamat Dealer</th>
							<th width="10%">Taksiran</th>
							<th width="10%">Uang Muka Gross</th>
							<th width="10%">Nomor ATM/Jamsostek</th>
							<th width="10%">Atas Nama</th>
							<th width="10%">Nama Bank</th>
							<th width="10%">Taksiran</th>
							<th width="10%">Keterangan</th>
							<th width="10%">Aksi</th>
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
 
    table = $('#myDataTable').DataTable({ 
 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "pageLength": 5,
        "order": [], //Initial no order.
        "ajax": {
            "url": "<?php echo site_url('credits-agunan/get-list')?>",
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