<script src="<?php echo base_url();?>assets/global/scripts/moment.js" type="text/javascript"></script>
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

margin: 0px; padding-top: 0px; padding-bottom: 0px; 

}
.textbox .textbox-text{
font-size: 12px;


}
input:read-only {
		background-color: f0f8ff;
	}
</style>
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
	
	$(document).ready(function(){	
		$('#credits_account_date').datebox({
			onChange: function(value){
				var name   		= 'credits_account_date';

				var angsuran 	= document.getElementById("credits_payment_period").value;
				var period 		= document.getElementById("credits_account_period").value;
				var date2 		= document.getElementById("credits_account_date").value;
				var day2 		= date2.substring(0, 2);
				var month2 		= date2.substring(3, 5);
				var year2 		= date2.substring(6, 10);
				var date 		= year2 + '-' + month2 + '-' + day2;
				var date1		= new Date(date);

				if(angsuran == 1){
					var a 		= moment(date1); 
					var b 		= a.add(period, 'month'); 

					var testDate = new Date(date);
					var tmp2 = testDate.setMonth(testDate.getMonth() + 1);
					var date_first = testDate.toISOString();
					var day2 = date_first.substring(8, 10);
					var month2 = date_first.substring(5, 7);
					var year2 = date_first.substring(0, 4);
					var first = day2 + '-' + month2 + '-' + year2;
					
					$('#credits_account_due_date').textbox('setValue',b.format('DD-MM-YYYY'));
					$('#credits_account_payment_date').textbox('setValue',first);
				}else{
					var week 		= period * 7;
					var testDate 	= new Date(date1);
					var tmp 		= testDate.setDate(testDate.getDate() + week);
					var date_tmp 	= testDate.toISOString();
					var day 		= date_tmp.substring(8, 10);
					var month 		= date_tmp.substring(5, 7);
					var year 		= date_tmp.substring(0, 4); 
					var name 		= 'credit_account_due_date';
					var value 		= day + '-' + month + '-' + year;
					
					var testDate2 = new Date(date1);
					var tmp2 = testDate2.setDate(testDate2.getDate() + 7);
					var date_first = testDate2.toISOString();
					var day2 = date_first.substring(8, 10);
					var month2 = date_first.substring(5, 7);
					var year2 = date_first.substring(0, 4);
					var first = day2 + '-' + month2 + '-' + year2;

					$('#credits_account_due_date').textbox('setValue',value);
					$('#credits_account_payment_date').textbox('setValue',first);
				}

			}
		});
	});
</script>
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
				Daftar Pinjaman
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>credit-account/edit-date/<?php echo $this->uri->segment(3);?>">
				Edit Tanggal Pinjaman
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
<?php
	echo form_open('credit-account/process-edit-date'); 
	if(substr($acctcreditsaccount['credits_account_payment_to'], -1) == '*'){
		$angsuranke = $acctcreditsaccount['credits_account_payment_to'];
	}else{
		$angsuranke = substr($acctcreditsaccount['credits_account_payment_to'], -1);
	}
	$member_address = $acctcreditsaccount['member_address']." ".$acctcreditsaccount['kecamatan_name']." ".$acctcreditsaccount['city_name']." ".$acctcreditsaccount['province_name'];
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			 <div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Form Edit Tanggal
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>credit-account" class="btn btn-default btn-sm">
							<i class="fa fa-angle-left"></i>
							<span class="hidden-480">
								Kembali
							</span>
						</a>
					</div>
				</div>
			
				<div class="portlet-body">
					<div class="row">
						<div class="col-md-5">
							<table style="width: 100%;" border="0" padding="0">
								<tr>
									<td width="35%">No. Perjanjian Pinjaman</td>
									<td width="5%"> : </td>
									<td width="60%">
										<input class="easyui-textbox" type="text" name="credits_account_serial" readonly id="credits_account_serial" value="<?php echo $acctcreditsaccount['credits_account_serial']; ?>" style="width: 100%"/>

										<input type="hidden" name="credits_account_id" readonly id="credits_account_id" value="<?php echo $acctcreditsaccount['credits_account_id']; ?>"/>

									</td>
								</tr>
								<tr>
									<td width="35%">Nama Anggota</td>
									<td width="5%"> : </td>
									<td width="60%">
										<input class="easyui-textbox" type="text" name="member_name" readonly id="member_name" value="<?php echo $acctcreditsaccount['member_name']; ?>" style="width: 100%"/>

									</td>
								</tr>
								
								<tr>
									<td width="35%">No. Identitas</td>
									<td width="5%"> : </td>
									<td width="60%">
										<input class="easyui-textbox" type="text" name="member_identity_no" readonly id="member_identity_no" value="<?php echo $acctcreditsaccount['member_identity_no']; ?>" style="width: 100%"/>

									</td>
								</tr>
								<tr>
									<td width="35%">Jenis Pinjaman</td>
									<td width="5%"> : </td>
									<td width="60%">
										<input name="credits_name" id="credits_name" type="text" class="easyui-textbox" value="<?php echo $acctcreditsaccount['credits_name'];?>" style="width: 100%" readonly>

									</td>
								</tr>
								<tr>
									<td width="35%">Tanggal Realisasi</td>
									<td width="5%"> : </td>
									<td width="60%">
									<input type="text" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser" name="credits_account_date" id="credits_account_date" autocomplete="off" onChange="duedatecalc(this);" value="<?php echo tgltoview($acctcreditsaccount['credits_account_date']); ?>" />

									</td>
								</tr>
								<tr>
									<td width="35%">Jangka Waktu</td>
									<td width="5%"> : </td>
									<td width="60%">
										<input name="credits_account_period" id="credits_account_period" value="<?php echo $acctcreditsaccount['credits_account_period'];?>" type="text" class="easyui-textbox" style="width: 100%" readonly>
										<input name="credits_payment_period" id="credits_payment_period" value="<?php echo $acctcreditsaccount['credits_payment_period'];?>" type="hidden" class="easyui-textbox" style="width: 100%">

									</td>
								</tr>
								<tr>
									<td width="35%">Tanggal Jatuh Tempo</td>
									<td width="5%"> : </td>
									<td width="60%">
										<input name="credits_account_due_date" id="credits_account_due_date" value="<?php echo tgltoview($acctcreditsaccount['credits_account_due_date']); ?>" type="text" class="easyui-textbox" style="width: 100%" readonly>
										<input name="credits_account_payment_date" id="credits_account_payment_date" value="<?php echo tgltoview($acctcreditsaccount['credits_account_payment_date']); ?>" type="hidden" class="easyui-textbox" style="width: 100%" readonly>

									</td>
								</tr>
							</table>
							<br>
							<br>
							<div class="row">
								<div class="col-md-12" style='text-align:right'>
									<button type="submit" name="Save" value="Save" id="Save" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Simpan</i></button>
								</div>	
							</div>
						</div>
							
					</div>
				 </div>
			</div>
		</div>
	</div>
</div>

<?php echo form_close(); ?>