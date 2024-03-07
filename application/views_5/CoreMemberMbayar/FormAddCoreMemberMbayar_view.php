<style>
	th, td {
	  padding: 3px;
	}
	input:focus { 
	  background-color: 42f483;
	}
</style>
<script>
	base_url = '<?php echo base_url();?>';

	var loop = 1;

	$(document).ready(function(){
		$('#member_name').textbox({
		   collapsible:false,
		   minimizable:false,
		   maximizable:false,
		   closable:false
		});
		
		$('#member_name').textbox('textbox').focus();
	});
	function reset_add(){
		document.location = base_url+"member-mbayar/reset-add";
	}
</script>
<?php echo form_open('member-mbayar/process-add',array('id' => 'myform', 'class' => 'horizontal-form')); 
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
			<a href="<?php echo base_url();?>member-mbayar/add/"<?php echo $this->uri->segment(3) ?>>
				Rekening Mbayar Anggota
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Form Edit
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>member-mbayar" class="btn btn-default btn-sm">
							<i class="fa fa-angle-left"></i>
							<span class="hidden-480">
								Kembali
							</span>
						</a>
					</div>
				</div>
				<div class="portlet-body form">
					<div class="form-body">
						<?php
							echo $this->session->userdata('message');
							$this->session->unset_userdata('message');
						?>

						<div class="row">
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">No. Anggota<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_no" id="member_no" autocomplete="off" value="<?php echo set_value('member_no', $coremember['member_no']);?>" style="width: 60%" readonly/> <a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#memberlistfrom">Cari Anggota</a>
										</td>
									</tr>
									<tr>
										<td width="35%">Nama Anggota<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo set_value('member_name', $coremember['member_name']);?>" style="width: 100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Sifat Anggota<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo $membercharacter[$coremember['member_character']];?>" style="width: 100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Provinsi<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo $coremember['province_name'];?>" style="width: 100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Kabupaten<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo $coremember['city_name'];?>" style="width: 100%" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Kecamatan<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo $coremember['kecamatan_name'];?>" style="width: 100%" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Kelurahan<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo $coremember['kelurahan_name'];?>" style="width: 100%" readonly/>
										</td>
									</tr>
									<!-- <tr>
										<td width="35%">Dusun<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo $coremember['dusun_name'];?>" style="width: 100%" readonly/>
										</td>
									</tr> -->
									<tr>
										<td width="35%">Alamat<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><textarea rows="3" name="member_address" id="member_address" class="easyui-textarea" style="width: 100%" ><?php echo $coremember['member_address'];?></textarea></td>
									</tr>
								</table>
							</div>
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<h3>Pilih Rekening Untuk Mbayar</h3>
								<table width="100%">
									<tr>
										<td width="35%">No. Rekening<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_no" id="member_no" autocomplete="off" value="<?php echo set_value('savings_account_no', $acctsavingsaccount['savings_account_no']);?>" style="width: 60%" readonly/> <a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#memberlistto">Cari Rekening</a>
										</td>
									</tr>
									<tr>
										<td width="35%">Nama<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo $acctsavingsaccount['member_name'];?>" style="width: 100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Jenis Simpanan<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo  $acctsavingsaccount['savings_name'];?>" style="width: 100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Saldo<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo number_format($acctsavingsaccount['savings_account_last_balance'], 2);?>" style="width: 100%" readonly/></td>
									</tr>
									<tr>
										<td width="35%"></td>
										<td width="5%"></td>
										<td width="60%" align="right">
											<button type="button" class="btn red" onClick="reset_add();"><i class="fa fa-times"></i> Batal</button>
											<button type="submit" class="btn green-jungle"><i class="fa fa-check"></i> Simpan</button>
										</td>
									</tr>
								</table>
							</div>
							
								<input type="hidden" class="form-control" name="member_id" id="member_id" placeholder="id" value="<?php echo set_value('member_id',$coremember['member_id']);?>"/>
								<input type="hidden" class="form-control" name="savings_account_id" id="savings_account_id" placeholder="id" value="<?php echo set_value('savings_account_id',$acctsavingsaccount['savings_account_id']);?>"/>

						</div>						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="memberlistfrom" class="modal fade" role="dialog">
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
		        	<th>No. Anggota</th>
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
<div id="memberlistto" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Daftar Rekening Anggota</h4>
      </div>
      <div class="modal-body">
		<table id="myDataTable2" class="table table-striped table-bordered table-hover table-full-width">
			<thead>
		    	<tr>
		        	<th>No</th>
		        	<th>No. Rek</th>
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
<?php $member_id = $this->uri->segment(3); ?>
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
            "url": "<?php echo site_url('member-mbayar/get-list-member-edit')?>",
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


 
$(document).ready(function() {
 
    //datatables
    var table = $('#myDataTable2').DataTable({ 
 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "pageLength": 5,
        "order": [], //Initial no order.
        "ajax": {
            "url": "<?php echo site_url('member-mbayar/get-list-savings-account/'.$member_id)?>",
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