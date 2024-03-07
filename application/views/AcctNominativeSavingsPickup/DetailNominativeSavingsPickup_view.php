
		<!-- BEGIN PAGE TITLE & BREADCRUMB-->
		<div class = "page-bar">
			<ul class="page-breadcrumb">
				<li>
					<a href="<?php echo base_url();?>">
						Beranda
					</a>
					<i class="fa fa-angle-right"></i>
				</li>
				<li>
					<a href="<?php echo base_url();?>sales-inv">
						Daftar Pickup
					</a>
					<i class="fa fa-angle-right"></i>
				</li>
			</ul>
		</div>
		<h3 class="page-title">
			Form Detail Pickup	
		</h3>
	
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Form Detail
				</div>
				<div class="actions">
					<a href="<?php echo base_url();?>nominative-savings-pickup" class="btn btn-default btn-sm">
						<i class="fa fa-angle-left"></i>
						<span class="hidden-480">
							Kembali
						</span>
					</a>
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body">					
					<?php
						echo $this->session->userdata('message');
						$this->session->unset_userdata('message');
					?>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<input readonly type="text" name="savings_cash_mutation_date" id="savings_cash_mutation_date" value="<?php echo tgltoview($savingspickup['savings_cash_mutation_date']) ?>"  class="form-control">
								<label class="control-label">Tanggal Pickup
								<span class="required">*</span></label>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<input readonly type="text" name="member_name" id="member_name" value="<?php echo $savingspickup['member_name'];?>" class="form-control" >
									<label class="control-label">Nama<span class="required">*</span></label>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<input readonly type="text" name="mutation_name" id="mutation_name" value="<?php echo $savingspickup['mutation_name'];?>" class="form-control" >
								<label class="control-label"> Transaksi
								<span class="required">*</span></label>
								
						</div>
					</div>
			
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<input readonly type="text" name="savings_cash_mutation_amount" id="savings_cash_mutation_amount" value="<?php echo $savingspickup['savings_cash_mutation_amount'] ?>"  class="form-control">
								<label class="control-label">Jumlah
								<span class="required">*</span></label>
							</div>
						</div>
					</div>

					<div class="row">
					<div class="col-md-12 " style="text-align  : right !important;">
					<a href="#" role="button" class="btn btn-info btn-sm btn red" data-toggle="modal" data-target="#pickup">Simpan</a>
			</div>
		</div>
	</div>
</div>
	</div>
		</div>

<?php echo form_open('nominative-savings-pickup/process-val-nominative',array('id' => 'myform', 'class' => 'horizontal-form'));
?>
<script>
	$(document).ready(function(){
        $("#save").click(function(){
			var pickup_remark = $("#pickup_remark").val();
			
		  	if(pickup_remark!=''){
				return true;
			}else{
				alert('Isikan Keterangan');
				return false;
			}
		});
    });
</script>
	<!-- /.modal -->
	<div class="modal fade bs-modal-lg" id="pickup" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
					<h4 class="modal-title">Transaksi</h4>
				</div>
				<div class="modal-body">
					
					<div class="row">
						<div class="col-md-12">
							<label class="control-label">Keterangan</label>
							<div class="input-icon right">
								<i class="fa"></i>
								<?php echo form_textarea(array('rows'=>'3','name'=>'pickup_remark','class'=>'form-control','id'=>'pickup_remark','value'=>set_value('pickup_remark',$savingspickup['pickup_remark'])))?>
							</div>	
						</div>	
					</div>
					
					<input type="hidden" class="form-control" name="savings_cash_mutation_id" id="savings_cash_mutation_id"  value="<?php echo set_value('savings_cash_mutation_id',$savingspickup['savings_cash_mutation_id']);?>"/>

					<div class="modal-footer">
						<button type="button" class="btn default" data-dismiss="modal">Batal</button>
						<button type="submit" id="save" class="btn red"><i class="fa fa-check"></i> Simpan</button>
					</div>
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
	</div>
	<!-- /.modal -->
<?php
echo form_close(); 
?>