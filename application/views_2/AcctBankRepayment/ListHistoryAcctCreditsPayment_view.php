<?php error_reporting(0); ?>
<style>
th, td {
  padding: 3px;
}
td {
  font-size: 12px;
}
input:focus { 
  background-color: 42f483;
}
.custom{

margin: 0px; padding-top: 0px; padding-bottom: 0px; height: 50px; line-height: 50px; width: 50px;

}
.textbox .textbox-text{
font-size: 10px;


}

</style>
	

<script>
	base_url = '<?php echo base_url();?>';
	function reset_search(){
		document.location = base_url+"credit-account/reset-search";
	}
</script>

<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>	
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Pencarian Pinjaman
				</div>
				<div class="tools">
					<a href="javascript:;" class='expand'></a>
				</div>
			</div>
			<div class="portlet-body display-show">
				<div class="form-body form">	
					<table style="width: 100%;" border="0" padding:"0">
						<tr>
							<td width="35%">No. Rek</td>
							<td width="5%">:</td>
							<td width="60%"><input type="text" disabled class="easyui-textbox" name="credits_account_serial" id="credits_account_serial" value="<?php echo set_value('credits_account_serial', $credit_account['credits_account_serial']);?>" style="width: 60%" readonly/> <a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#creditlist">Cari</a> </td>
						</tr>
						<tr>
							<td width="35%">Nama</td>
							<td width="5%">:</td>
							<td width="60%"><input type="text" disabled class="easyui-textbox" name="member_name" id="member_name" value="<?php echo set_value('member_name', $credit_account['member_name']);?>" style="width: 70%" readonly/></td>
						</tr>
						<tr>
							<td width="35%">Alamat</td>
							<td width="5%">:</td>
							<td width="60%"><?php echo form_textarea(array('rows'=>'2','name'=>'member_address','class'=>'easyui-textarea','id'=>'member_address','disabled'=>'disabled','value'=>$credit_account['member_address']))?></td>
						</tr>
					</table>

				</div>
			</div>
		</div>
	</div>
</div>
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
						<table class="table table-striped table-bordered table-hover table-full-width" id="sample_1">
						<tbody>
							<tr>    
								<th width='5%' style='text-align:center'>Ke.</th>	
								<th>Tanggal Angsuran</th>		
								<th style='text-align:center;'>Angsuran Pokok</th>	
								<th style='text-align:center;'>Angsuran Margin</th>	
								<th style='text-align:center;'>Saldo Pokok</th>	
								<th style='text-align:center;'>Saldo Margin</th>	
							</tr>
							<?php
								$no = 1;
								if(isset($acctcreditspayment)){
									foreach($acctcreditspayment as $key => $val){
										echo"
											<tr>
												<td style='text-align:center'>".$no."</td>
												<td>".tgltoview($val['credits_payment_date'])."</td>
												<td style='text-align:right;'>".number_format($val['credits_payment_principal'], 2)."</td>
												<td style='text-align:right;'>".number_format($val['credits_payment_margin'], 2)."</td>
												<td style='text-align:right;'>".number_format($val['credits_principal_last_balance'], 2)."</td>
												<td style='text-align:right;'>".number_format($val['credits_margin_last_balance'], 2)."</td>
											</tr>
										";
										$no++;
									}
								}else{
									echo"
											<tr>
												<td colspan='6'> Data Tidak Ditemukan</td>
											</tr>
										";
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
<div id="creditlist" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">List Pinjaman</h4>
      </div>
      <div class="modal-body">
<table id="myDataTable">
	<thead>
    	<tr>
        	<th>No. Akad</th>
        	<th>Nama</th>
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
            "url": "<?php echo site_url('cash-payments/credit-list')?>",
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