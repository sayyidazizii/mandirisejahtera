<style>
	th, td {
	  padding: 2px;
	  font-size: 14px;
	}
	input:focus { 
	  background-color: 42f483;
	}
</style>
<script>
	var loop = 1;
	base_url = '<?php echo base_url();?>';

	// $(document).ready(function(){
	// 	$('#zakat_fund_distribution_date').datebox({
	// 	   collapsible:false,
	// 	   minimizable:false,
	// 	   maximizable:false,
	// 	   closable:false
	// 	});
		
	// 	$('#zakat_fund_distribution_date').datebox('datebox').focus();
	// });

	function toRp(number) {
		var number = number.toString(), 
		rupiah = number.split('.')[0], 
		cents = (number.split('.')[1] || '') +'00';
		rupiah = rupiah.split('').reverse().join('')
			.replace(/(\d{3}(?!$))/g, '$1,')
			.split('').reverse().join('');
		return rupiah + '.' + cents.slice(0, 2);
	}

	function calSaldoZakat(){
		var zakat_fund_opening_balance		= $('#zakat_fund_opening_balance').val();
		var zakat_fund_distribution_amount	= $('#zakat_fund_distribution_amount').val();

		if(zakat_fund_opening_balance == ''){
			zakat_fund_opening_balance = 0;
		}

		var zakat_fund_last_balance;

		zakat_fund_last_balance = parseFloat(zakat_fund_opening_balance) - parseFloat(zakat_fund_distribution_amount);

		$('#zakat_fund_last_balance').textbox('setValue', zakat_fund_last_balance);
	}

	$(document).ready(function(){
		$('#zakat_fund_distribution_amount_view').textbox({
			onChange: function(value){
				console.log(value);
				console.log(loop);
				if(loop == 0){
					loop= 1;
					return;
				}
				if(loop ==1){
					loop =0;
					var tampil = toRp(value);
				$('#zakat_fund_distribution_amount').textbox('setValue', value);
				$('#zakat_fund_distribution_amount_view').textbox('setValue', tampil);

				calSaldoZakat();
				
				}else{
					loop=1;
					return;
				}
			
			}
		});
	});

	
	
</script>
<?php 
	echo form_open('AcctZakatFund/processAddAcctZakatFundDistribution',array('id' => 'myform', 'class' => 'horizontal-form')); 

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
			<a href="<?php echo base_url();?>AcctZakatFund/getAcctZakatFundDistribution">
				Penyaluran Dana Zakat
			</a>
			<i class="fa fa-angle-right"></i>
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
						Form Tambah
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>AcctZakatFund/getAcctZakatFundDistribution" class="btn btn-default btn-sm">
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
							<div class="col-md-1">
								
							</div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">Tanggal<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input class="easyui-datebox" data-options="formatter:myformatter,parser:myparser" type="text" name="zakat_fund_distribution_date" id="zakat_fund_distribution_date" value="<?php echo date('d-m-Y');?>" style="width:70%;"/></td>
									</tr>
									<tr>
										<td width="35%">Disalurkan Ke <span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<?php 
												echo form_dropdown('zakat_fund_distribution_to', $distribution, set_value('zakat_fund_distribution_to'),'id="zakat_fund_distribution_to" class="easyui-combobox" style="width:70%;"');
											?>
										</td>
									</tr>
									<tr>
										<td width="35%">Keterangan<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><textarea rows="2" name="zakat_fund_description" id="zakat_fund_description" class="easyui-textarea"  style="width:100%;"></textarea></td>
									</tr>
									<tr>
										<td width="35%">Saldo <span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="zakat_fund_opening_balance_view" id="zakat_fund_opening_balance_view" autocomplete="off" style="width:100%;" value="<?php  echo number_format($zakat_fund_opening_balance, 2); ?>" readonly/><input type="hidden" class="easyui-textbox" name="zakat_fund_opening_balance" id="zakat_fund_opening_balance" autocomplete="off" value="<?php  echo $zakat_fund_opening_balance; ?>" /><input type="hidden" class="easyui-textbox" name="zakat_fund_last_balance" id="zakat_fund_last_balance" autocomplete="off" />
										</td>
									</tr>
									<tr>
										<td width="35%">Jumlah (Rp) <span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="zakat_fund_distribution_amount_view" id="zakat_fund_distribution_amount_view" autocomplete="off" style="width:100%;"/>
											<input type="hidden" class="easyui-textbox" name="zakat_fund_distribution_amount" id="zakat_fund_distribution_amount" autocomplete="off"/>
										</td>
									</tr>
									<tr>
										<td width="35%"></td>
										<td width="5%"></td>
										<td width="60%"></td>
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
							<div class="col-md-1">
							</div>
							<div class="col-md-5">
							</div>
						</div>						
					</div>
				</div>
			 </div>
		</div>
	</div>
</div>

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