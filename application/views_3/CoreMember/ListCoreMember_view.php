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
			<a href="<?php echo base_url();?>member">
				Master Data Anggota
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Master Data Anggota <small>Kelola Data Anggota</small>
</h3>    
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$auth = $this->session->userdata('auth');
?>
<?php
	if($auth['branch_status'] == 1){

	$sesi=$this->session->userdata('filter-coremember');

	if(!is_array($sesi)){
		$sesi['company_id']			= '';
	}
?>	
<!-- <?php	echo form_open('member/filter',array('id' => 'myform', 'class' => '')); ?>
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
					 <div class = "row">
						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<?php echo form_dropdown('company_id', $corecompany, set_value('company_id',$sesi['company_id']),'id="company_id" class="form-control select2me"');?>
								<label class="control-label">Perusahaan
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
		</div>
	</div>
</div>
<?php echo form_close(); }?> -->
	<div class="row">
		<div class="col-md-12">
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Daftar
					</div>
					
					<div class="actions ">
						<a href="<?php echo base_url();?>member/add" class="btn btn-default btn-sm">
							<i class="fa fa-plus"></i>
							<span class="hidden-480">
								Input Data Anggota
							</span>
						</a>
					</div>
					<?php if($export_menu_id_mapping == 1){ ?>
					<div class="actions" style="margin-right: 10px;">
						<a href='javascript:void(window.open("<?php echo base_url(); ?>member/export-master-data","_blank","top=100,left=200,width=300,height=300"));' title="Export to Excel" class="btn btn-default btn-sm">
							<i class="fa fa-download"></i>
							<span class="hidden-480">
								Export Data Anggota
							</span>
						</a>
					</div>
					<?php }?>
				</div>
				<div class="portlet-body">
					<div class="form-body">
						<table id="myDataTable" class="table table-striped table-bordered table-hover table-full-width">
							<thead>
								<tr>
									<th width="3%">No</th>
									<th width="7%">No Anggota</th>
									<th width="12%">Nama</th>
									<th width="15%">Alamat</th>
									<!-- <th width="15%">Perusahaan</th> -->
									<th width="8%">Status</th>
									<th width="8%">No. Telp</th>
									<th width="10%">Simp Pokok</th>
									<th width="10%">Simp Khusus</th>
									<th width="10%">Simp Wajib</th>
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
        "order": [
			[1, "asc" ]
		], //Initial no order.
        "ajax": {
            "url": "<?php echo site_url('member/get-member-list')?>",
            "type": "POST"
        },
        "columnDefs": [
        { 
            "targets": [ 0 ], //first column / numbering column
            "orderable": true, //set not orderable   
        },
        ],
 
    });
 
});
</script>
<?php echo form_close(); ?>