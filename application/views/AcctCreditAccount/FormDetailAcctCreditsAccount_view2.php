


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

<h3 class="page-title">
	Detil Histori Angsuran Pinjaman
</h3>
<?php
// print_r($data);
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>
<div class="row">
	<div class="col-md-4">
		<div class="portlet"> 
			 <div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Member Data
					</div>
					<div class="actions">
					</div>
				</div>
				<div class="portlet-body">
					<div class="form-body">
						<div class="row">						
	
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<?php
									$isi=$this->uri->segment(3);
									if($isi > 0){
									echo form_dropdown('credit_id', $creditid, set_value('credit_id',$data['credit_id']),'id="credit_id" class="form-control select2me" ');
									}else{
										echo form_dropdown('credit_id', $creditid, set_value('credit_id',$data['credit_id']),'id="credit_id" class="form-control select2me" disabled');
									}
									?>
									<label class="control-label">Jenis Credit</label>
								</div>
							</div>
						</div>
						
						<div class="row">						
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_no" id="member_no" autocomplete="off" readonly value="<?php echo $coremember['member_no'];?>"/>
									<input type="hidden" class="form-control" name="member_id" id="member_id" autocomplete="off" readonly value="<?php echo $coremember['member_id'];?>"/>
									<label class="control-label">No. Anggota<span class="required">*</span></label>
								</div>
							</div>
									<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_nama" id="member_nama" autocomplete="off" readonly value="<?php echo $coremember['member_name'];?>"/>
									<label class="control-label">Nama Anggota<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
								<a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#memberlist">Select Member</a>
								</div>
							</div>
						</div>

						<div class="row">						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_date_of_birth" id="member_date_of_birth" autocomplete="off" readonly value="<?php echo $coremember['member_date_of_birth'];?>"/>
									<label class="control-label">Tanggal Lahir<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_gender" id="member_gender" autocomplete="off" readonly value="<?php if($coremember['member_gender']==1){echo "Perempuan";}else{"Laki-Laki";}?>"/>
									<label class="control-label">Jenis Kelamin<span class="required">*</span></label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">	
								<div class="form-group form-md-line-input">
								<textarea class="form-control" rows="3" id="comment" readonly ><?php echo $coremember['member_address'];?></textarea>
									<label class="control-label">Alamat
										<span class="required">
											*
										</span>
									</label>

								</div>
							</div>
						</div>

						<div class="row">						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="city_name" id="city_name" autocomplete="off" readonly value="<?php echo $coremember['city_name'];?>" />
									<label class="control-label">Kabupaten<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="kecamatan_name" id="kecamatan_name" autocomplete="off" readonly value="<?php echo $coremember['kecamatan_name'];?>"/>
									<label class="control-label">Kecamatan<span class="required">*</span></label>
								</div>
							</div>
						</div>

						<div class="row">						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_job" id="member_job" autocomplete="off" readonly value="<?php echo $coremember['member_job'];?>"/>
									<label class="control-label">Pekerjaan<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_phone1" id="member_phone1" autocomplete="off" readonly value="<?php echo $coremember['member_phone'];?>"/>
									<label class="control-label">No. Telp<span class="required">*</span></label>
								</div>
							</div>
						</div>

						<div class="row">						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="identity_name" id="identity_name" autocomplete="off" readonly value="<?php 
									if(!empty($coremember['member_identity'])){
										echo $memberidentity[$coremember['member_identity']];
									} else { echo " "; }?>" />
									<label class="control-label">Identitas<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_identity_no" id="member_identity_no" autocomplete="off" readonly value="<?php echo $coremember['member_identity_no'];?>"/>
									<label class="control-label">No. Identitas<span class="required">*</span></label>
								</div>
							</div>
						</div>
						</div>
						</div>
						</div>
						</div>
						</div>

	<div class="col-md-8">
		<div class="portlet"> 
			 <div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
					Detail Rekening Simpanan Berjangka Baru
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
						<div class="row">						
							<div class="col-md-3">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control form-control-inline  date-picker" data-date-format="dd-mm-yyyy" name="credit_account_date" id="credit_account_date" autocomplete="off" onchange="duedatecalc(this);" />
									<label class="control-label">Tanggal Realisasi<span class="required">*</span></label>
								</div>
							</div>
						
								<div class="col-md-3">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="credit_account_period" id="credit_account_period" autocomplete="off" value="" onchange="duedatecalc(this);angsurancalc();" />
									<label class="control-label">Jangka Waktu (Bulan)<span class="required">*</span></label>
								</div>
							</div>
								<div class="col-md-3">
								<div class="form-group form-md-line-input">
								<select name="deposito_account_due_date" class="form-control">
								<option value="Bulanan">Bulanan</option>
								<option value="Harian">Harian</option>
								<option value="Lain-lain">Lain-Lain</option>
							
								</select>
									<label class="control-label">Angsuran Tiap<span class="required">*</span></label>
								</div>
							</div>
									<div class="col-md-3">
								<div class="form-group form-md-line-input">
											<?php echo form_dropdown('sumberdana', $sumberdana, set_value('sumberdana',$data['sumberdana']),'id="sumberdana" class="form-control select2me" ');?>
									<label class="control-label">Sumber Dana<span class="required">*</span></label>
								</div>
							</div>
						</div>
						<div class="row">						
						<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="credit_account_due_date" id="credit_account_due_date" autocomplete="off" readonly />
									<label class="control-label">Jatuh Tempo<span class="required">*</span></label>
								</div>
							</div>
								<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="credit_account_serial" id="credit_account_serial" autocomplete="off" />
									<label class="control-label">No Akad<span class="required">*</span></label>
								</div>
							</div>
								<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<?php echo form_dropdown('office_id', $coreoffice, set_value('office_id',$data['office_id']),'id="office_id" class="form-control select2me" ');?>
									<label class="control-label">Ac. Officer<span class="required">*</span></label>
								</div>
							</div>
				
					
						</div>
						<div class="row">						
					
							<div class="col-md-3">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="credit_account_materai" id="credit_account_materai" autocomplete="off"/>
									<label class="control-label">Biaya Materai<span class="required">*</span></label>
								</div>
							</div>
								<div class="col-md-3">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="credit_account_adm_cost" id="credit_account_adm_cost" autocomplete="off" value=""/>
									<label class="control-label">Biaya administrasi<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="credit_account_notaris" id="credit_account_notaris" autocomplete="off"/>
									<label class="control-label">Biaya Notaris<span class="required">*</span></label>
								</div>
							</div>
										<div class="col-md-3">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="credit_account_insurance" id="credit_account_insurance" autocomplete="off" value="<?php echo set_value('deposito_account_amount_view',$data['deposito_account_amount_view']);?>"/>
									<label class="control-label">Biaya Asuransi<span class="required">*</span></label>
								</div>
							</div>
						</div>

							<div class="row">
							<div class="col-md-3">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="credit_account_net_price" id="credit_account_net_price" autocomplete="off" onkeyup="margincalc()" />
									<label class="control-label">Harga Pokok<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="credit_account_sell_price" id="credit_account_sell_price" autocomplete="off" value="" onkeyup="margincalc()"/>
									<label class="control-label">Harga Jual<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="credit_account_margin" id="credit_account_margin" autocomplete="off" onkeyup="marginhitung()" />
									<label class="control-label">Margin<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="credit_account_margin_rate" id="credit_account_margin_rate" autocomplete="off" onkeyup="marginhitung()" />
									<label class="control-label"></label>
								</div>
							</div>
						</div>
							<div class="row">
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="credit_account_payment_amount_net" id="credit_account_payment_amount_net" autocomplete="off" onkeyup="margincalc()" />
									<label class="control-label">Angsuran Pokok<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="credit_account_payment_amount_margin" id="credit_account_payment_amount_margin" autocomplete="off" value="" onkeyup="margincalc()"/>
									<label class="control-label">Angsuran Margin<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="credit_account_payment_amount" id="credit_account_payment_amount" autocomplete="off" />
									<label class="control-label">Jumlah Angsuran<span class="required">*</span></label>
								</div>
							</div>
						</div>
							<div class="row">
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="credit_account_um" id="credit_account_um" autocomplete="off" onkeyup="marginhitung()" />
									<label class="control-label">Uang Muka<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="credits_account_last_balance_principal" id="credits_account_last_balance_principal" autocomplete="off" onkeyup="marginhitung()" />
									<label class="control-label">Saldo Pokok<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="credits_account_last_balance_margin" id="credits_account_last_balance_margin" autocomplete="off" onkeyup="marginhitung()" />
									<label class="control-label">Saldo Margin<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
								<?php $urlagunan=base_url().'/credit-account/add-agunan';?>
								<button type="button" class="btn btn-info btn-sm" onclick="popuplink('<?php echo $urlagunan; ?>');">Input Agunan</button>
								</div>
							</div>
							
						</div>
						<div class="row">

							<div class="col-md-3">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="credit_account_nisbah_bmt" id="credit_account_nisbah_bmt" autocomplete="off" value="<?php echo set_value('deposito_account_amount_view',$data['deposito_account_amount_view']);?>"/>
									<label class="control-label">Nisbah (BMT)<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-3">
							<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="credit_account_nisbah_agt" id="credit_account_nisbah_agt" autocomplete="off" value="<?php echo set_value('deposito_account_amount_view',$data['deposito_account_amount_view']);?>"/>
									<label class="control-label">Nisbah (Anggota)<span class="required">*</span></label>
								</div>
							</div>
								<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<?php echo form_dropdown('savings_account_id', $acctsavingsaccount, set_value('savings_account_id',$data['savings_account_id']),'id="savings_account_id" class="form-control select2me" ');?>
									<label class="control-label">No. Simpanan<span class="required">*</span></label>
								</div>
							</div>
						</div>
						

						<div class="row">
							<div class="col-md-12" style='text-align:right'>
								<button type="reset" name="Reset" value="Reset" class="btn btn-danger" onClick="reset_data();"><i class="fa fa-times"> Batal</i></button>
								<button type="submit" name="Save" value="Save" id="Save" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Simpan</i></button>
							</div>	
						</div>
					</div>
				</div>
			 </div>
		</div>
	</div>
</div>
<div id="memberlist" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Member List</h4>
      </div>
      <div class="modal-body">
<table id="myDataTable">
	<thead>
    	<tr>
        	<th>No</th>
        	<th>Member No</th>
            <th>Member Nama</th>
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
        "order": [], //Initial no order.
        "ajax": {
            "url": "<?php echo site_url('credit-account/member-list')?>",
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
