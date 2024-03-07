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

	.flexigrid div.pDiv input {
		vertical-align:middle !important;
	}
	
	.flexigrid div.pDiv div.pDiv2 {
		margin-bottom: 10px !important;
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
			<a href="<?php echo base_url();?>deposito-account-blockir">
				Daftar Rekening Simp Berjangka Diblockir
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Daftar Rekening Simp Berjangka Diblockir <small>Kelola Daftar Rekening Simp Berjangka  Diblockir</small>
</h3>
	<div class="row">
		<div class="col-md-12">
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						UnBlockir
					</div>
				</div>
				<div class="portlet-body">
					<div class="form-body">
						<table id="myDataTable" class="table table-striped table-bordered table-hover table-full-width">
							<thead>
								<tr>
									<th width="3%">No</th>
									<th width="7%">No SimpKa</th>
									<th width="12%">Nama</th>
									<th width="15%">Alamat</th>
									<th width="8%">Sifat Blockir</th>
									<th width="8%">Status</th>
									<th width="8%">Tanggal Blockir</th>
									<th width="10%">Tanggal UnBlockir</th>
									<th width="10%">Saldo Diblokir</th>
									<th width="5%">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php
									if(empty($acctdepositoaccountblockir)){
										echo "
											<tr>
												<td colspan='10' align='center'>Data Kosong</td>
											</tr>
										";
									} else {
										foreach ($acctdepositoaccountblockir as $key => $val) {
											echo "
												<tr>
													<td>$no</td>
													<td>".$val['deposito_account_no']."</td>
													<td>".$val['member_name']."</td>
													<td>".$val['member_address']."</td>
													<td>".$blockirtype[$val['deposito_account_blockir_type']]."</td>
													<td>".$blockirstatus[$val['deposito_account_blockir_status']]."</td>
													<td>".tgltoview($val['deposito_account_blockir_date'])."</td>
													<td>".tgltoview($val['deposito_account_unblockir_date'])."</td>
													<td>".number_format($val['deposito_account_blockir_amount'], 2)."</td>
													<td>
														<a href='".$this->config->item('base_url').'deposito-account-blockir/add-unblockir/'.$val['deposito_account_blockir_id']."' class=' btn btn-sm red'><i class='fa fa-edit'></i> UnBlock</a>
													</td>
												</tr>
											";
										}
									}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
					<!-- END EXAMPLE TABLE PORTLET-->
		</div>
	</div>
<!-- <script type="text/javascript">
 
var table;
 
$(document).ready(function() {
 
    //datatables
    table = $('#myDataTable').DataTable({ 
 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "pageLength": 5,
        "order": [], //Initial no order.
        "ajax": {
            "url": "<?php echo site_url('deposito-account-blockir/get-list-unblockir')?>",
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
</script> -->
<?php echo form_close(); ?>