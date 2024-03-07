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
	<?php
		echo $this->session->userdata('message');
		$this->session->unset_userdata('message');
	?>

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
			<a href="<?php echo base_url();?>member/update-status">
				Update Calon Anggota
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Update Calon Anggota <small>Kelola Calon Anggota</small>
</h3>
	<div class="row">
		<div class="col-md-12">
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Daftar
					</div>
				</div>
				<div class="portlet-body">
					<div class="form-body">
						<table id="myDataTable" class="table table-striped table-bordered table-hover table-full-width">
						<thead>
							<tr>
								<th width="3%">No</th>
								<th width="7%">No Anggota</th>
								<th width="15%">Nama</th>
								<th width="20%">Alamat</th>
								<th width="8%">Status</th>
								<th width="8%">Sifat</th>
								<th width="8%">No. Telp</th>
								<th width="10%">Simp Pokok</th>
								<!-- <th width="10%">Simp Khusus</th> -->
								<th width="10%">Simp Wajib</th>
								<th width="15%">Action</th>
								
							</tr>
						</thead>
						<tbody>
							
							</tbody>
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
            "url": "<?php echo site_url('member/get-list-status')?>",
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