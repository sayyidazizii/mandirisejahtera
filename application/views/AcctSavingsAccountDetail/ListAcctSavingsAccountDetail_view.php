<style>
	th, td {
	  padding: 3px;
	}
	input:focus { 
	  background-color: 42f483;
	}


</style>
<script type="text/javascript">
	base_url = '<?php echo base_url();?>';

		function reset_search(){
		document.location = base_url+"savings-account-detail/reset_search";
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
			<a href="<?php echo base_url();?>savings-account-detail/show-detail">
				Daftar Saldo Rata - Rata Harian
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$sesi=$this->session->userdata('filter-acctsavingsaccountdetail');

	if(!is_array($sesi)){
		$sesi['start_date']			= date('Y-m-d');
		$sesi['end_date']			= date('Y-m-d');
		$sesi['savings_account_id']	= '';
	}
?>	
<?php	echo form_open('savings-account-detail/filter',array('id' => 'myform', 'class' => '')); 

	$start_date			= $sesi['start_date'];
	$end_date			= $sesi['end_date'];
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Saldo Rata - Rata Harian
				</div>

			</div>
			<div class="portlet-body">
				<div class="form-body form">
					 <div class="row">
						<div class="col-md-1"></div>
						<div class="col-md-5">
							<table width="100%">
								<tr>
									<td width="35%">No Rekening</td>
									<td width="5%">:</td>
									<td width="60%">
										<input class="easyui-textbox" type="text" name="savings_account_no" id="savings_account_no"  value="<?php echo $acctsavingsaccount['savings_account_no'];?>" style="width: 60%" readonly/>
										<input class="easyui-textbox" type="hidden" name="savings_account_id" id="savings_account_id"  value="<?php echo $acctsavingsaccount['savings_account_id'];?>"/>
										<a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#memberlist">Cari No Rek</a>
										
									</td>
								</tr>
								<tr>
									<td width="35%">Nama</td>
									<td width="5%">:</td>
									<td width="60%"><input class="easyui-textbox" type="text" name="end_date" id="end_date"  value="<?php echo $acctsavingsaccount['member_name'];?>" readonly/></td>
								</tr>
								<tr>
									<td width="35%">Alamat</td>
									<td width="5%">:</td>
									<td width="60%"><input class="easyui-textbox" type="text" name="start_date" id="start_date"  value="<?php echo $acctsavingsaccount['member_address']?>" readonly/></td>
								</tr>
							</table>
						</div>
						<div class="col-md-1"></div>
						<div class="col-md-5">
							<table width="100%">
								<tr>
									<td width="35%">Tanggal Mulai</td>
									<td width="5%">:</td>
									<td width="60%"><input class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"type="text" name="start_date" id="start_date"  value="<?php echo tgltoview($sesi['start_date']);?>"/></td>
								</tr>
								<tr>
									<td width="35%">Tanggal Akhir</td>
									<td width="5%">:</td>
									<td width="60%"><input class="easyui-datebox" data-options="formatter:myformatter,parser:myparser" type="text" name="end_date" id="end_date"  value="<?php echo tgltoview($sesi['end_date']);?>"/></td>
								</tr>
								<tr>
									<td width="35%"></td>
									<td width="5%"></td>
									<td width="60%" align="left">
										<button type="button" class="btn red" onClick="reset_search();"><i class="fa fa-times"></i> Batal</button>
										<button type="submit" class="btn green-jungle"><i class="fa fa-check"></i> Cari</button>
									</td>
								</tr>
							</table>
						</div>
					</div>
			
			<div class="portlet-body">
				<div class="form-body">
					<table class="table table-bordered table-hover table-full-width" >
					<thead>
						<tr>
							<th width="3%">No</th>
							<th width="8%">No. Rek</th>
							<th width="15%">Nama</th>
							<th width="10%">Mutasi</th>
							<th width="10%">Tanggal</th>
							<th width="15%">Deskripsi</th>
							<th width="10%">Saldo Awal</th>
							<th width="10%">Mutasi Masuk</th>
							<th width="10%">Mutasi Keluar</th>
							<th width="10%">Saldo Akhir</th>
							<th width="10%">SRH</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$no = 1;
							if(empty($acctsavingsaccountdetail)){
								echo "
									<tr>
										<td colspan='11' align='center'>Emty Data</td>
									</tr>
								";
							} else {
								foreach ($acctsavingsaccountdetail as $key=>$val){
									echo"
										<tr>			
											<td style='text-align:center'>$no.</td>
											<td style='text-align:left'>".$val['savings_account_no']."</td>
											<td style='text-align:left'>".$val['member_name']."</td>
											<td style='text-align:left'>".$val['mutation_name']."</td>
											<td style='text-align:center'>".tgltoview($val['today_transaction_date'])."</td>
											<td style='text-align:left'>".$val['transaction_code']."</td>
											<td style='text-align:right'>".number_format($val['opening_balance'], 2)."</td>
											<td style='text-align:right'>".number_format($val['mutation_in'], 2 )."</td>
											<td style='text-align:right'>".number_format($val['mutation_out'], 2 )."</td>
											<td style='text-align:right'>".number_format($val['last_balance'], 2 )."</td>
											<td style='text-align:right'>".number_format($val['daily_average_balance'], 2 )."</td>
										</tr>
									";
									$no++;								
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
<div id="memberlist" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Daftar Anggota</h4>
      </div>
      <div class="modal-body">
		<table id="myDataTable" class="table table-striped table-bordered table-hover table-full-width">
			<thead>
		    	<tr>
		        	<th>No</th>
		        	<th>No Rek</th>
		            <th>Nama Anggota</th>
		            <th>Alamat</th>
		            <th>Action</th>
		        </tr>
		    </thead>
		    <tbody></tbody>
		</table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
        "pageLength": 5,
        "order": [], //Initial no order.
        "ajax": {
            "url": "<?php echo site_url('savings-account-detail/get-list-savings-account')?>",
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

<script type="text/javascript">
        function myformatter(date){
            var y = date.getFullYear();
            var m = date.getMonth()+1;
            var d = date.getDate();
            return (d<10?('0'+d):d)+'-'+(m<10?('0'+m):m)+'-'+y;
        }
        function myparser(s){
            if (!s) return new Date();
            var ss = (s.split('-'));
            var y = parseInt(ss[0],10);
            var m = parseInt(ss[1],10);
            var d = parseInt(ss[2],10);
            if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
                return new Date(d,m-1,y);
            } else {
                return new Date();
            }
        }
    </script>

<?php echo form_close(); ?>