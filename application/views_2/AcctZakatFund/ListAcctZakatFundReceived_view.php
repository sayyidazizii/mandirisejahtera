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
<script type="text/javascript">
	
	base_url = '<?php echo base_url();?>';

		function reset_search(){
		document.location = base_url+"AcctZakatFund/reset_data";
	}
</script>
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
			<a href="<?php echo base_url();?>AcctZakatFund">
				Penerimaan Dana Zakat
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->
<?php
	$auth = $this->session->userdata('auth');
	
	if($auth['branch_status'] == 1){

	$sesi = $this->session->userdata('filter-acctzakatfundreceived');

	if(!is_array($sesi)){
		$sesi['branch_id']			= '';
	}
?>	
<?php	echo form_open('AcctZakatFund/filterreceived',array('id' => 'myform', 'class' => '')); ?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Penerimaan Dana Zakat
				</div>
				<div class="actions">
					<a href="<?php echo base_url();?>AcctZakatFund/addAcctZakatFundReceived" class="btn btn-default btn-sm">
						<i class="fa fa-plus"></i>
						<span class="hidden-480">
							Input Penerimaan Dana Zakat
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
					Penerimaan Dana Zakat
				</div>
				<div class="actions">
					<a href="<?php echo base_url();?>AcctZakatFund/addAcctZakatFundReceived" class="btn btn-default btn-sm">
						<i class="fa fa-plus"></i>
						<span class="hidden-480">
							Input Penerimaan Dana Zakat
						</span>
					</a>
				</div>
			</div>
		<?php } ?>
				<div class="portlet-body">
					<div class="form-body">
						<table class="table table-striped table-bordered table-hover table-full-width" id="myDataTable">
							<thead>
								<tr>
									<th width="3%">No</th>
									<th width="10%">Tanggal Penerimaan Zakat</th>
									<th width="12%">Sumber Dana</th>
									<th width="20%">Jumlah Penerimaan Zakat</th>
									<th width="35%">Keterangan</th>
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
            "url": "<?php echo site_url('AcctZakatFund/getAcctZakatFundReceivedList')?>",
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