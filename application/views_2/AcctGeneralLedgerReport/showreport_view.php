 <?php
	date_default_timezone_set("Asia/Jakarta");
	$_this = & get_Instance();
	$url=$_this->uri->segment(1);
	$id = $this->uri->segment(3);
	$sesi = $this->session->userdata('filter-purchaseinvoicereport');
	if(!is_array($sesi)){
		$sesi['start_date'] 	= '';
		$sesi['end_date'] 		= '';
		$sesi['supplier_id']	= '';
	}
	$start_date = tgltodb($sesi['start_date']);
	$end_date = tgltodb($sesi['end_date']);
	
	$purchaseinvoice = $this->purchaseinvoicereport_model->getexport($start_date,$end_date,$sesi['supplier_id']);
	// print_r($purchaseorder->result_array());exit;
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel=stylesheet href="<?php echo base_url();?>css/isi.css" type=text/css media=screen>
<link rel=stylesheet href="<?php echo base_url();?>css/printt.css" type=text/css media=print>
<style>
	table {
		border-collapse:collapse;
	}
	
	td label {
		margin : 0 0 0 auto;
		font-size: 12px;
	}
	th{
		font-family:Arial  !important;
		letter-spacing:0px !important;
		font-size: 10px !important;
		font-weight: normal !important;
		margin : 0 auto;
		vertical-align:middle !important;
	}
	td{
		font-family:Arial  !important;
		letter-spacing:0px !important;
		font-size: 10px !important;
		font-weight: normal !important;
		margin : 0 auto;
		vertical-align:middle !important;
	}
	
	#page-wrap { width: 800px; margin: 0 auto;  border: 1px solid black; padding:5px;}
	
	#terms { text-align: center; margin: 20px 0 0 0; }
	
	#items { clear: both; width: 100%; margin: 30px 10px 0 0; }
	#items th { background: #eee; border: 1px solid black;}
	
	.col1 {		
		float:left;
		width:250px;
		padding:5px;
		margin:0px;
	}
	.col2 {
		align:right;
		float:right;
		width:250px;
		padding:5px;
		margin:0px;
		margin-bottom:10px;
	}
</style>
<script>
	base_url = '<?= base_url() ?>';
	function printNota(){
		alamat=document.getElementById("urlnya").value;
		window.print();
		document.location = base_url+alamat;
	}
</script>
<body>
	<div id="wrapper">
		<div id="page-wrap">
			<table width="100%">
				<tr style="text-align:center;border: 0px solid black;width:25%">
					<td><h1><b><i>Report Purchase Invoice</i></b></h1></td>					
				</tr>				
			</table>
			<input type="hidden" name="urlnya" id="urlnya" value="<?php echo $url; ?>"/>
			<div class='col1'>
				<table border="0" cellpadding="0">
					<tr>
						<td><b><?php echo $company['company_name']; ?></b></td>	
					</tr>						
					<?php
						if($sesi['start_date'] != '' && $sesi['end_date'] != ''){
					?>					
					<tr>
						<td width='50px'>Date</td>
						<td width='15px'> : </td>
						<td width='65px'><?php echo tgltoview($sesi['start_date']); ?></td>
						<td width='15px'> - </td>
							<td width='65px'><?php echo tgltoview($sesi['end_date']); ?></td>
					</tr>	
					<?php } ?>						
				</table>
			</div>
			<table id="items" width="100%" cellpadding="3" class="table table-striped table-bordered table-hover table-full-width">
				<tr>
					<th><b>Purchase Invoice No</b></th>					
					<th><b>Supplier</b></th>
					<th><b>Purchase Order No</b></th>
					<th><b>Date</b></th>
					<th><b>Due Date</b></th>
					<th><b>Item</b></th>
					<th><b>Quantity</b></th>
					<th><b>Item Unit Cost</b></th>
					<th><b>Subtotal</b></th>
					<th><b>Discount(%)</b></th>
					<th><b>Discount</b></th>
					<th><b>Total</b></th>
				</tr>
				<?php
					$no = 1;
					foreach($purchaseinvoice->result_array() as $key=>$val){	
						$id = $this->purchaseinvoicereport_model->getMinID($val['purchase_invoice_id']);	
						if($val['purchase_invoice_item_id']==$id){
							echo"
								<tr>
									<td>".$val['purchase_invoice_no']."</td>
									<td>".$this->purchaseinvoicereport_model->getsuppliername($val['supplier_id'])."</td>
									<td>".$this->purchaseinvoicereport_model->getpurchaseorderno($val['purchase_order_id'])."</td>
									<td>".tgltoview($val['purchase_invoice_date'])."</td>
									<td>".tgltoview($val['purchase_invoice_due_date'])."</td>
									<td>".$this->purchaseinvoicereport_model->getitemname($val['item_id'])."</td>
									<td style='text-align:right'>".nominal($val['quantity'])."</td>
									<td style='text-align:right'>".nominal($val['item_unit_cost'])."</td>
									<td style='text-align:right'>".nominal($val['subtotal_base_amount'])."</td>
									<td style='text-align:right'>".$val['discount_percentage']."</td>
									<td style='text-align:right'>".nominal($val['discount_base_amount'])."</td>
									<td style='text-align:right'>".nominal($val['subtotal_base_amount_after_discount'])."</td>									
								</tr>
							";		
						}else{
							echo"
								<tr>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td>".$this->purchaseinvoicereport_model->getitemname($val['item_id'])."</td>
									<td style='text-align:right'>".nominal($val['quantity'])."</td>
									<td style='text-align:right'>".nominal($val['item_unit_cost'])."</td>
									<td style='text-align:right'>".nominal($val['subtotal_base_amount'])."</td>
									<td style='text-align:right'>".$val['discount_percentage']."</td>
									<td style='text-align:right'>".nominal($val['discount_base_amount'])."</td>
									<td style='text-align:right'>".nominal($val['subtotal_base_amount_after_discount'])."</td>	
								</tr>
							";
						}
						$no++;
					}
				?>
			</table>
		</div>
	</div>
	<div id="isi">
		<center><p><a href="javascript:printNota()"> <img src="<?php echo base_url();?>img/Device-Printer-icon.png" width="50px" height="50px"></a></p></center>
	</div>
</body>