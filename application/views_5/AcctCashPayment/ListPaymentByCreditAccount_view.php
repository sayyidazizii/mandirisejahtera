<table class="table table-striped table-hover">
<tr>
	<th>Ke</th>
	<th>Tgl Angsuran</th>
	<th>Angsuran Pokok</th>
	<th>Angsuran Bunga</th>
	<th>Saldo Pokok</th>
	<th>Saldo  Bunga</th>
</tr>
<?php 
$no=1;
if(count($detailpayment) > 0){
	foreach ($detailpayment as $key=>$val){ 
	
	echo"
		<tr>
		<td>".$no."</td>
		<td>".tgltoview($val['credits_payment_date'])."</td>
		<td>".number_format($val['credits_payment_principal'], 2)."</td>
		<td>".number_format($val['credits_payment_interest'], 2)."</td>
		<td>".number_format($val['credits_principal_last_balance'], 2)."</td>
		<td>".number_format($val['credits_interest_last_balance'], 2)."</td>
		</tr>
	";
	$no++;
	}
}
 ?>

</table>