<script src="<?php echo base_url();?>assets/global/scripts/moment.js" type="text/javascript"></script>

<?php
	$sesi 	= $this->session->userdata('unique');
	$data 	= $this->session->userdata('addcreditaccount-'.$sesi['unique']);


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
			<a href="<?php echo base_url();?>credit-account">
				Daftar Rekening Pinjaman
			</a>
			<i class="fa fa-angle-right"></i>
		</li>

	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->
<?php
// print_r($data);
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>
<div class="row">

	<div class="col-md-12">
		<div class="portlet"> 
			 <div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
					Daftar Pinjaman
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>" class="btn btn-default btn-sm">
							<i class="fa fa-angle-left"></i>
							<span class="hidden-480">
								Kembali
							</span>
						</a>
					</div>
				</div>
				<div class="portlet-body">
						<table id="myDataTable">
	<thead>
    	<tr>
        	<th>No</th>
        	<th>No Akad</th>
        	<th>Nama Nasabah</th>
        	<th>No Nasabah</th>
        	<th>Tgl Realisasi</th>
        	<th>Tgl Jatuh Tempo</th>
        	<th>Tenor</th>
        	<th>Harga Pokok</th>
        	<th>Harga Jual</th>
        	<th>Margin</th>

        </tr>
    </thead>
    <tbody></tbody>
</table>
					</div>
				</div>
			 </div>
		</div>
	</div>
</div>
<script type="text/javascript">
 
var table;
 
$(document).ready(function() {
 
    //datatables
    table = $('#myDataTable').DataTable({ 
 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
        "ajax": {
            "url": "<?php echo site_url('credit-account/credit-ajax')?>",
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
