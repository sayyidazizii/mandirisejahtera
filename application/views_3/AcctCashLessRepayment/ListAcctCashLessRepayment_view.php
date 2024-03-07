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
	base_url = "<?php echo base_url();?>";

	function reset_search(){
		document.location = base_url +"AcctCashLessRepayments/reset_cashless";
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
			<a href="<?php echo base_url();?>AcctCashLessRepayments/indAcctCashLessRepayment">
				Daftar Pembayaran Non Tunai
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<!-- <h3 class="page-title">
	Daftar Pembiayaan <small>Kelola Pembiayaan</small>
</h3> -->
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$auth = $this->session->userdata('auth');
	$sesi = $this->session->userdata('filter-AcctCashLessRepaymentless');

	if(!is_array($sesi)){
		$sesi['start_date']			= date('Y-m-d');
		$sesi['end_date']			= date('Y-m-d');
		$sesi['credits_id']			= '';
		$sesi['branch_id']			= '';
	}
?>	
<?php	echo form_open('AcctCashLessRepayments/filteracctAcctCashLessRepayment',array('id' => 'myform', 'class' => '')); 

	$start_date			= $sesi['start_date'];
	$end_date			= $sesi['end_date'];
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Daftar Pembayaran Non Tunai
				</div>
				<!-- <div class="tools">
					<a href="javascript:;" class='expand'></a>
				</div> -->
				<div class="actions">
					<a href="<?php echo base_url();?>AcctCashLessRepayments/addAcctCashLessRepayment" class="btn btn-default btn-sm">
						<i class="fa fa-plus"></i>
						<span class="hidden-480">
							Tambah Pembayaran Non Tunai Baru
						</span>
					</a>
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body form">
					<div class = "row">
						<div class = "col-md-3">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="<?php echo tgltoview($start_date);?>" autocomplete="off"/>
								<label class="control-label">Tanggal Awal
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<div class = "col-md-3">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="<?php echo tgltoview($end_date);?>" autocomplete="off"/>
								<label class="control-label">Tanggal Akhir
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('credits_id', $acctcredits,set_value('credits_id',$sesi['credits_id']),'id="credits_id" class="form-control select2me" ');
								?>
								<label>Jenis Pembiayaan</label>
							</div>
						</div>

						<?php if($auth['branch_status'] == 1) { ?>
						<div class="col-md-3">
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
<!-- 		</div>
	</div>
</div> -->
<?php echo form_close(); ?>
<!-- <div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Daftar
				</div>
				
			</div> -->
			<div class="portlet-body">
				<div class="form-body">
					<table class="table table-striped table-bordered table-hover table-full-width" id="myDataTable">
					<thead>
						<tr>
							<th width="5%">No</th>
							<th width="12%">No. Perjanjian Kredit</th>
							<th width="15%">Nama Anggota</th>
							<th width="15%">Jenis Pinjaman</th>
							<th width="15%">No. Rek Simp.</th>
							<th width="10%">Tanggal Pinjaman</th>
							<th width="10%">Angsuran Pokok</th>
							<th width="10%">Angsuran Bunga</th>
							<th width="10%">Action</th>
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
            "url": "<?php echo site_url('AcctCashLessRepayments/getAcctAcctCashLessRepayment')?>",
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