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

margin: 0px; padding-top: 0px; padding-bottom: 0px; height: 50px; line-height: 50px; width: 50px;

}
.textbox .textbox-text{
font-size: 10px;


}

</style>


<script>
	var loop 			= 1;
	var loop_principal 	= 1;
	var loop_margin 	= 1;

	console.log('loop_margin awal '+ loop_margin);

	 function function_elements_add(name, value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('AcctCreditAccountUtility/function_elements_add');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
			}
		});
	}

	function reset_data(){
		document.location = base_url+"AcctCreditAccountUtility/reset_data";
	}

	function toRp(number) {
		var number = number.toString(), 
		rupiah = number.split('.')[0], 
		cents = (number.split('.')[1] || '') +'00';
		rupiah = rupiah.split('').reverse().join('')
			.replace(/(\d{3}(?!$))/g, '$1,')
			.split('').reverse().join('');
		return rupiah + '.' + cents.slice(0, 2);
	}

	function angsurancalc(){
		var harga_sell= +document.getElementById("credit_account_sell_price").value;
		var harga_net= +document.getElementById("credit_account_net_price").value;
		var margin= +document.getElementById("credit_account_margin").value;
		var jangka= +document.getElementById("credit_account_period").value;
		var uangmuka= +document.getElementById("credit_account_um").value;

		
		var totalangsuran = (margin / jangka)+((harga_net - uangmuka) / jangka);
		var angsuranpokok = (harga_net - uangmuka) / jangka;
		var angsuranmargin = margin / jangka;
		var pembiayaan = harga_sell - uangmuka;

		if(margin == '' || margin == 0){
			var rate = 0;
		} else {
			var rate = (margin /pembiayaan) * 100;
		}
		 
		console.log('pembiayaan')
		console.log(pembiayaan)
		// alert(uangmuka);
		
		$('#credit_account_payment_amount_net').textbox('setValue',Math.round((harga_sell - uangmuka) / jangka));
		$('#credit_account_payment_amount_margin').textbox('setValue',Math.round(margin / jangka));
		$('#credit_account_payment_amount').textbox('setValue',Math.round((margin / jangka)+((harga_net - uangmuka) / jangka)));
		$('#credits_account_principal_amount').textbox('setValue',Math.round((harga_net - uangmuka) / jangka));
		$('#credits_account_margin_amount').textbox('setValue',Math.round(margin / jangka));
		$('#credits_account_last_balance_principal').textbox('setValue',Math.round(harga_sell - uangmuka));
		


		$('#credit_account_payment_amount_view').textbox('setValue',toRp(totalangsuran));
		$('#credits_account_principal_amount_view').textbox('setValue',toRp(angsuranpokok));
		$('#credits_account_margin_amount_view').textbox('setValue',toRp(angsuranmargin));
		$('#credits_account_last_balance_principal_view').textbox('setValue',toRp(pembiayaan));
		$('#credit_account_rate').textbox('setValue',toRp(rate));

		var npembiayaan 		= 'credits_account_last_balance_principal';
		var npembiayaan2 		= 'credits_account_last_balance_principal_view';
		var nmargin 			= 'credit_account_margin';
		var nmargin2 			= 'credit_account_margin_view';
		var nrate 				= 'credit_account_rate';
		var ntotalangsuran 		= 'credit_account_payment_amount';
		var ntotalangsuran2 	= 'credit_account_payment_amount_view';
		var nangsuranpokok 		= 'credits_account_principal_amount';
		var nangsuranpokok2 	= 'credits_account_principal_amount_view';
		var nangsuranmargin 	= 'credits_account_margin_amount';
		var nangsuranmargin2 	= 'credits_account_margin_amount_view';


		if(loop_principal == 0){
			loop_principal= 1;
			return;
		}
		if(loop_principal ==1){
			loop_principal =0;
			$('#credits_account_last_balance_principal').textbox('setValue',pembiayaan);
			$('#credits_account_last_balance_principal_view').textbox('setValue',toRp(pembiayaan));
			function_elements_add(npembiayaan, pembiayaan);
			function_elements_add(npembiayaan2, toRp(pembiayaan));
		
		}else{
			loop_principal=1;
			return;
		}


		
		

		
		function_elements_add(nmargin, margin);
		function_elements_add(nmargin2, toRp(margin));
		function_elements_add(nrate, toRp(rate));
		function_elements_add(ntotalangsuran, totalangsuran);
		function_elements_add(ntotalangsuran2, toRp(totalangsuran));
		function_elements_add(nangsuranpokok, angsuranpokok);
		function_elements_add(nangsuranpokok2, toRp(angsuranpokok));
		function_elements_add(nangsuranmargin, angsuranmargin);
		function_elements_add(nangsuranmargin2, toRp(angsuranmargin));


		
	}

	function angsurancalc2(){
		var margin 		= document.getElementById("credit_account_margin").value;
		var jangka 		= document.getElementById("credit_account_period").value;
		var pembiayaan 	= document.getElementById("credits_account_last_balance_principal").value;

		console.log('angsurancalc2 pembiayaan ' + pembiayaan);

		var totalangsuran = (margin / jangka)+(pembiayaan / jangka);
		var angsuranpokok = pembiayaan / jangka;
		var angsuranmargin = margin / jangka;
		if(margin == '' || margin == 0){
			var rate = 0;
		} else {
			var rate = (margin /pembiayaan) * 100;
		}

		// alert(uangmuka);

		$('#credit_account_payment_amount_margin').textbox('setValue',Math.round(margin / jangka));
		$('#credit_account_payment_amount').textbox('setValue',Math.round((margin / jangka)+(pembiayaan / jangka)));
		$('#credits_account_principal_amount').textbox('setValue',Math.round(pembiayaan / jangka));
		$('#credits_account_margin_amount').textbox('setValue',Math.round(margin / jangka));

		$('#credit_account_payment_amount_view').textbox('setValue',toRp(totalangsuran));
		$('#credits_account_principal_amount_view').textbox('setValue',toRp(angsuranpokok));
		$('#credits_account_margin_amount_view').textbox('setValue',toRp(angsuranmargin));
		$('#credit_account_rate').textbox('setValue',toRp(rate));
		// $('#credits_account_last_balance_principal_view').textbox('setValue',toRp(pembiayaan));

		var nrate 				= 'credit_account_rate';
		var ntotalangsuran 		= 'credit_account_payment_amount';
		var ntotalangsuran2 	= 'credit_account_payment_amount_view';
		var nangsuranpokok 		= 'credits_account_principal_amount';
		var nangsuranpokok2 	= 'credits_account_principal_amount_view';
		var nangsuranmargin 	= 'credits_account_margin_amount';
		var nangsuranmargin2 	= 'credits_account_margin_amount_view';

		function_elements_add(nrate, toRp(rate));
		function_elements_add(ntotalangsuran, totalangsuran);
		function_elements_add(ntotalangsuran2, toRp(totalangsuran));
		function_elements_add(nangsuranpokok, angsuranpokok);
		function_elements_add(nangsuranpokok2, toRp(angsuranpokok));
		function_elements_add(nangsuranmargin, angsuranmargin);
		function_elements_add(nangsuranmargin2, toRp(angsuranmargin));
	}

	function popuplink(url) {
		window.open(url, "", "width=800,height=600");
	}

	function formatDate(date) {
	    var d = new Date(date),
	        month = '' + (d.getMonth() + 1),
	        day = '' + d.getDate(),
	        year = d.getFullYear();

	    if (month.length < 2) month = '0' + month;
	    if (day.length < 2) day = '0' + day;

	    return [year, month, day].join('-');
	}

	function margincalc(){
		var harga_net= +document.getElementById("credit_account_net_price").value;
		var harga_sell= +document.getElementById("credit_account_sell_price").value;
		var margin = harga_sell - harga_net;

		if(loop_margin == 0){
			loop_margin= 1;
			return;
		}
		if(loop_margin ==1){
			loop_margin =0;
		$('#credit_account_margin').textbox('setValue',harga_sell - harga_net);
		$('#credit_account_margin_view').textbox('setValue',toRp(margin));

		angsurancalc();
		
		}else{
			loop_margin=1;
			return;
		}

		
	}

	function marginhitung(){
		var harga_net= +document.getElementById("credit_account_net_price").value;
		var harga_sell= +document.getElementById("credit_account_sell_price").value; 
		var margin = harga_sell - harga_net;
		$('#credit_account_margin').textbox('setValue',harga_sell - harga_net);
		$('#credit_account_margin_view').textbox('setValue',toRp(margin));
		angsurancalc();
	}

	function pembiayaan(){
		var pembiayaan= +document.getElementById("credits_account_last_balance_principal").value;

		console.log('function pembiayaan ' + pembiayaan);

		$('#credits_account_last_balance_principal').textbox('setValue',pembiayaan);
		$('#credits_account_last_balance_principal_view').textbox('setValue',toRp(pembiayaan));
		angsurancalc2();
	}

	function duedatecalc(data){
		var date2 	= document.getElementById("credit_account_date").value;
		var day2 	= date2.substring(0, 2);
		var month2 	= date2.substring(3, 5);
		var year2 	= date2.substring(6, 10);
		var date 	= year2 + '-' + month2 + '-' + day2;
		var date1	= new Date(date);
		var period 	= document.getElementById("credit_account_period").value;
		var a 		= moment(date1); 
		var b 		= a.add(period, 'month'); 
		
		var tmp 	= date1.setMonth(date1.getMonth() + period);
		var endDate = new Date(tmp);
		var name 	= 'credit_account_due_date';
		var value 	= b.format('DD-MM-YYYY');

		/*alert(date2);
		alert(day2);
		alert(month2);
		alert(year2);
		alert(b);
		alert(date1);
		alert(endDate);*/
		$('#credit_account_due_date').textbox('setValue',b.format('DD-MM-YYYY'));
		function_elements_add(name, value);
		
	}
	base_url = '<?php echo base_url();?>';


	$(document).ready(function(){
		$('#credit_id').combobox({
			onChange: function(value){
				var name 			= 'credit_id';
			  	var credits_id 		= +document.getElementById("credit_id").value;

			  	function_elements_add(name, value);


			   	if(credits_id == 1 || credits_id == 2){
			   		$('#credit_account_net_price_view').textbox('disable',true);  // disable it
			   		$('#credit_account_net_price').textbox('readonly',true);  // disable it
					$('#credit_account_sell_price_view').textbox('disable',true);  // disable it
					$('#credit_account_sell_price').textbox('readonly',true);  // disable it
					$('#credit_account_um_view').textbox('disable',true);  // disable it
					$('#credit_account_um').textbox('readonly',true);  // disable it
					$('#credit_account_nisbah_bmt').textbox('readonly',false);  // disable it
				} else {
					$('#credit_account_net_price_view').textbox('readonly',false);  // enable it
			   		$('#credit_account_net_price').textbox('readonly',false);  // enable it
					$('#credit_account_sell_price_view').textbox('readonly',false);  // enable it
					$('#credit_account_sell_price').textbox('readonly',false);  // enable it
					$('#credit_account_um_view').textbox('readonly',false);  // enable it
					$('#credit_account_um').textbox('readonly',false);  // enable it
					$('#credit_account_nisbah_bmt').textbox('readonly',true);  // disable it
				} 
			  }
			})
	});

	// $(document).ready(function(){
	// 	 $('#sumberdana').combobox({
	// 		  onChange: function(value){
	// 		  	var name 			= 'sumberdana';
	// 		  	var credits_id 		= +document.getElementById("credit_id").value;
	// 		  	var source_fund_id 	= +document.getElementById("sumberdana").value;

	// 		  	function_elements_add(name, value);

	// 		   $.post(base_url + 'AcctCreditAccountUtility/getCreditsAccountSerial',
	// 			{credits_id: credits_id, source_fund_id: source_fund_id},
 //                function(data){	
 //                var obj = $.parseJSON(data)	
 //                console.log(obj)	   
 //                	$('#credit_account_serial').textbox('setValue',obj["credit_account_serial"]);
	// 			},
				
	// 			)
	// 		  }
	// 		})
	// });


	$(document).ready(function(){
		
        $("#member_id").change(function(){
            var member_id = $("#member_id").val();
            // alert(member_id);
            $.post(base_url + 'deposito-account/get-core-member-detail',
			{member_id: member_id},
                function(data){			   
                	// alert(data);
				   	$("#member_no").val(data.member_no);
				   	$("#member_date_of_birth").val(data.member_date_of_birth);
				   	$("#member_gender").val(data.member_gender);
					$("#member_address").val(data.member_address);
					$("#city_name").val(data.city_name);
					$("#kecamatan_name").val(data.kecamatan_name);
					$("#member_job").val(data.member_job);
					$("#identity_name").val(data.identity_name);
					$("#member_identity_no").val(data.member_identity_no);
					$("#member_phone1").val(data.member_phone);
				},
					'json'
				);				
            });
        });

	$(document).ready(function(){		
		$('#credit_account_date').datebox({
			onChange: function(value){
				var name   	= 'credit_account_date';

		    	function_elements_add(name, value);
	    		duedatecalc(this);
			}
		});

		$('#credit_account_period').textbox({
			onChange: function(value){
				var name   		= 'credit_account_period';
				var credit_id 	= +document.getElementById("credit_id").value;

		    	
			    if(credit_id == 1 || credit_id == 2){
			    	function_elements_add(name, value);
			    	duedatecalc(this);
			    	angsurancalc2();
			    } else {
			    	function_elements_add(name, value);
			    	duedatecalc(this);
			    	angsurancalc();
			    }
				
			}
		});

		$('#credit_account_serial').textbox({
			onChange: function(value){
				var name   	= 'credit_account_serial';

		    	function_elements_add(name, value);
			}
		});

		$('#credit_account_nisbah_bmt').textbox({
			onChange: function(value){
				var name   	= 'credit_account_nisbah_bmt';

		    	function_elements_add(name, value);
			}
		});

		$('#credit_account_nisbah_agt').textbox({
			onChange: function(value){
				var name   	= 'credit_account_nisbah_agt';

		    	function_elements_add(name, value);
			}
		});

		$('#credit_account_net_price_view').textbox({
			onChange: function(value){
				var name   	= 'credit_account_net_price';
				var name2   = 'credit_account_net_price_view';
			/*margincalc();*/


			console.log(value);
			console.log(loop);
				if(loop == 0){
					loop= 1;
					return;
				}
				if(loop ==1){
					loop =0;
					var tampil = toRp(value);
				$('#credit_account_net_price').textbox('setValue', value);
				$('#credit_account_net_price_view').textbox('setValue', tampil);

				function_elements_add(name, value);
				function_elements_add(name2, tampil);
				
				}else{
					loop=1;
					return;
				}
			
			}
		});
		$('#credit_account_sell_price_view').textbox({
			onChange: function(value){
				var name   	= 'credit_account_sell_price';
				var name2   = 'credit_account_sell_price_view';


				if(loop == 0){
					loop= 1;
					return;
				}
				if(loop ==1){
					loop =0;
					var tampil = toRp(value);
				$('#credit_account_sell_price').textbox('setValue', value);
				$('#credit_account_sell_price_view').textbox('setValue', tampil);
				
				
				function_elements_add(name, value);
				function_elements_add(name2, tampil);
				margincalc();
				}else{
					loop=1;
					return;
				}
			}
		});

	
		$('#credit_account_um_view').textbox({
			onChange: function(value){
				var name   	= 'credit_account_um';
				var name2   = 'credit_account_um_view';

				if(loop == 0){
					loop= 1;
					return;
				}
				if(loop ==1){
					loop =0;
					var tampil = toRp(value);
					$('#credit_account_um').textbox('setValue', value);
					$('#credit_account_um_view').textbox('setValue', tampil);
					console.log(value);
					
					
					function_elements_add(name, value);
					function_elements_add(name2, tampil);
					angsurancalc();
				}else{
					loop=1;
					return;
				}
			}
		});

		$('#credit_account_adm_cost_view').textbox({
			onChange: function(value){
				var name   	= 'credit_account_adm_cost';
				var name2   = 'credit_account_adm_cost_view';

				if(loop == 0){
					loop= 1;
					return;
				}
				if(loop ==1){
					loop =0;
					var tampil = toRp(value);
				$('#credit_account_adm_cost').textbox('setValue', value);
				$('#credit_account_adm_cost_view').textbox('setValue', tampil);

				function_elements_add(name, value);
				function_elements_add(name2, tampil);
				
				}else{
					loop=1;
					return;
				}
			}
		});

		$('#credit_account_materai_view').textbox({
			onChange: function(value){
				var name   	= 'credit_account_materai';
				var name2   = 'credit_account_materai_view';

				if(loop == 0){
					loop= 1;
					return;
				}
				if(loop ==1){
					loop =0;
					var tampil = toRp(value);
				$('#credit_account_materai').textbox('setValue', value);
				$('#credit_account_materai_view').textbox('setValue', tampil);

				function_elements_add(name, value);
				function_elements_add(name2, tampil);
				
				}else{
					loop=1;
					return;
				}
			}
		});

		$('#credit_account_insurance_view').textbox({
			onChange: function(value){
				var name   	= 'credit_account_insurance';
				var name2   = 'credit_account_insurance_view';

				if(loop == 0){
					loop= 1;
					return;
				}
				if(loop ==1){
					loop =0;
					var tampil = toRp(value);
				$('#credit_account_insurance').textbox('setValue', value);
				$('#credit_account_insurance_view').textbox('setValue', tampil);

				function_elements_add(name, value);
				function_elements_add(name2, tampil);
				
				}else{
					loop=1;
					return;
				}
			}
		});

		$('#credit_account_notaris_view').textbox({
			onChange: function(value){
				var name   	= 'credit_account_notaris';
				var name2   = 'credit_account_notaris_view';

				if(loop == 0){
					loop= 1;
					return;
				}
				if(loop ==1){
					loop =0;
					var tampil = toRp(value);
				$('#credit_account_notaris').textbox('setValue', value);
				$('#credit_account_notaris_view').textbox('setValue', tampil);

				function_elements_add(name, value);
				function_elements_add(name2, tampil);
				
				}else{
					loop=1;
					return;
				}
			}
		});

		$('#credits_account_last_balance_principal_view').textbox({
			onChange: function(value){
				var name   	= 'credits_account_last_balance_principal';
				var name2   = 'credits_account_last_balance_principal_view';
				var credits_id 		= +document.getElementById("credit_id").value;


				if(loop_principal == 0){
					loop_principal = 1;
					return;
				}
				if(loop_principal == 1){
					loop_principal =0;
					var tampil = toRp(value);
					$('#credits_account_last_balance_principal').textbox('setValue', value);
					$('#credits_account_last_balance_principal_view').textbox('setValue', tampil);

					console.log('onChange credits_account_last_balance_principal '+ value);
					console.log('onChange credits_account_last_balance_principal_view '+ tampil);
					
					if(credit_id == 1 || credit_id ==2){
						function_elements_add(name, value);
						function_elements_add(name2, tampil);
						pembiayaan();
					}
					
				
				
				}else{
					loop_principal = 1;
					return;
				}
			}
		});

		$('#credit_account_margin_view').textbox({
			onChange: function(value){
				var name   	= 'credit_account_margin';
				var name2   = 'credit_account_margin_view';
				var credits_id 		= +document.getElementById("credit_id").value;

				if(loop_margin == 0){
					loop_margin = 1;
					return;
				}
				if(loop_margin == 1){
					loop_margin = 0;
					var tampil = toRp(value);
					$('#credit_account_margin').textbox('setValue', value);
					$('#credit_account_margin_view').textbox('setValue', tampil);

					console.log('onChange credit_account_margin '+ value);
					console.log('onChange credit_account_margin_view '+ tampil);
					// if(credit_id == 1 || credit_id == 2){
						function_elements_add(name, value);
						function_elements_add(name2, tampil);
						angsurancalc2();
					// }
					
				}else{
					loop_margin = 1;
					return;
				}
			}
		});
			   
        $("#deposito_id").change(function(){
            var deposito_id = $("#deposito_id").val();
            // alert(member_id);
            $.post(base_url + 'deposito-account/get-deposite-account-no',
			{deposito_id: deposito_id},
                function(data){			   
                	// alert(data);
                	$("#deposito_period").val(data.deposito_period);
				   	$("#deposito_account_no").val(data.deposito_account_no);
				   	$("#deposito_account_due_date").val(data.deposito_account_due_date);
				   	$("#deposito_account_nisbah").val(data.deposito_account_nisbah);
				},
					'json'
				);				
        });
    });

	$(document).on('change','#deposito_account_amount_view',function(event){
		deposito_account_amount_view				= $('#deposito_account_amount_view')[0].value;	
		
		document.getElementById('deposito_account_amount_view').value	= toRp(deposito_account_amount_view);
		document.getElementById('deposito_account_amount').value		= deposito_account_amount_view;
		
	});

	$(document).ready(function(){
        $("#Save").click(function(){
        	var member_id 						= $("#member_id").val();
			var deposito_id 					= $("#deposito_id").val();
			var deposito_account_amount_view 	= $("#deposito_account_amount_view").val();
			var savings_account_id 				= $("#savings_account_id").val();

			
			if(member_id == ''){
				alert("Member masih kosong");
				return false;
			}else if(deposito_id == ''){
				alert("Jenis Simpanan Berjangka masih kosong");
				return false;
			}else if(deposito_account_amount_view == ''){
				alert("Nominal masih kosong");
				return false;
			} 
			// else if(savings_account_id == ''){
			// 	alert("Rek. Simpanan masih kosong");
			// 	return false;
			// } 	
		});
    });



</script>
<?php echo form_open('AcctCreditAccountUtility/addcreditaccount',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
	$sesi 	= $this->session->userdata('unique');
	$data 	= $this->session->userdata('addcreditaccount-'.$sesi['unique']);
	$token 	= $this->session->userdata('acctcreditsaccounttoken-'.$sesi['unique']);

	if(empty($data['credit_id'])){
		$data['credit_id'] = '';
	}

	if(empty($data['sumberdana'])){
		$data['sumberdana'] = '';
	}

	if(empty($data['credit_account_date'])){
		$data['credit_account_date'] = date('d-m-Y');
	}

	if(empty($data['credit_account_period'])){
		$data['credit_account_period'] = '';
	}

	if(empty($data['credit_account_due_date'])){
		$data['credit_account_due_date'] = date('d-m-Y');
	}

	if(empty($data['credit_account_materai'])){
		$data['credit_account_materai'] = '';
	}

	if(empty($data['credit_account_materai_view'])){
		$data['credit_account_materai_view'] = '';
	}

	if(empty($data['credit_account_serial'])){
		$data['credit_account_serial'] = '';
	}

	if(empty($data['credit_account_adm_cost_view'])){
		$data['credit_account_adm_cost_view'] = '';
	}

	if(empty($data['credit_account_adm_cost'])){
		$data['credit_account_adm_cost'] = '';
	}

	if(empty($data['credit_account_net_price_view'])){
		$data['credit_account_net_price_view'] = '';
	}

	if(empty($data['credit_account_net_price'])){
		$data['credit_account_net_price'] = '';
	}

	if(empty($data['credit_account_sell_price'])){
		$data['credit_account_sell_price'] = '';
	}

	if(empty($data['credit_account_sell_price_view'])){
		$data['credit_account_sell_price_view'] = '';
	}

	if(empty($data['credit_account_um'])){
		$data['credit_account_um'] = '';
	}

	if(empty($data['credit_account_um_view'])){
		$data['credit_account_um_view'] = '';
	}

	if(empty($data['credits_account_last_balance_principal'])){
		$data['credits_account_last_balance_principal'] = '';
	}

	if(empty($data['credits_account_last_balance_principal_view'])){
		$data['credits_account_last_balance_principal_view'] = '';
	}

	if(empty($data['credit_account_margin'])){
		$data['credit_account_margin'] = '';
	}

	if(empty($data['credit_account_margin_view'])){

		$data['credit_account_margin_view'] = '';
	}

	if(empty($data['credit_account_rate'])){
		$data['credit_account_rate'] = 0;
	}

	if(empty($data['credits_account_principal_amount_view'])){
		$data['credits_account_principal_amount_view'] = '';
	}

	if(empty($data['credits_account_principal_amount'])){
		$data['credits_account_principal_amount'] = '';
	}

	if(empty($data['credits_account_margin_amount'])){
		$data['credits_account_margin_amount'] = '';
	}

	if(empty($data['credits_account_margin_amount_view'])){
		$data['credits_account_margin_amount_view'] = '';
	}

	if(empty($data['credit_account_payment_amount'])){
		$data['credit_account_payment_amount'] = '';
	}

	if(empty($data['credit_account_payment_amount_view'])){
		$data['credit_account_payment_amount_view'] = '';
	}

	if(empty($data['credit_account_notaris'])){
		$data['credit_account_notaris'] = '';
	}

	if(empty($data['credit_account_notaris_view'])){
		$data['credit_account_notaris_view'] = '';
	}

	if(empty($data['credit_account_insurance'])){
		$data['credit_account_insurance'] = '';
	}

	if(empty($data['credit_account_insurance_view'])){
		$data['credit_account_insurance_view'] = '';
	}

	if(empty($data['credit_account_nisbah_bmt'])){
		$data['credit_account_nisbah_bmt'] = '';
	}

	if(empty($data['credit_account_nisbah_agt'])){
		$data['credit_account_nisbah_agt'] = '';
	}

	if(empty($data['office_id'])){
		$data['office_id'] = 0;
	}

	if(empty($data['savings_account_id'])){
		$data['savings_account_id'] = 0;
	}

?>

		<!-- BEGIN PAGE TITLE & BREADCRUMB-->

		<!-- END PAGE TITLE & BREADCRUMB-->

<?php
// print_r($data);
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			 <div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
					Input Data Baru Pembiayaan
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
				<div class="col-md-4">
						<table style="width: 100%;" border="0" padding:"0">
							<tbody>
								<tr>
									<td>No. Anggota</td>
									<td>:</td>
									<td><input type="text" class="form-control" name="member_no" id="member_no" autocomplete="off" readonly value="<?php echo $coremember['member_no'];?>" tabindex="-1"/>
									<input type="hidden" class="form-control" name="member_id" id="member_id" autocomplete="off" readonly value="<?php echo $coremember['member_id'];?>"/></td>
									<td><a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#memberlist">Select Member</a></td>
								</tr>
								<tr>
									<td>Nama Anggota</td>
									<td>:</td>
									<td><input type="text" class="form-control" name="member_nama" id="member_nama" autocomplete="off" readonly value="<?php echo $coremember['member_name'];?>" tabindex="-1"/></td>
									<td></td>
								</tr>
								<tr>
									<td>Tanggal Lahir</td>
									<td>:</td>
									<td><input type="text"tabindex="-1" class="form-control" name="member_date_of_birth" id="member_date_of_birth" autocomplete="off" readonly value="<?php echo tgltoview($coremember['member_date_of_birth']);?>"/></td>
									<td></td>
								</tr>
								<tr>
									<td>Jenis Kelamin</td>
									<td>:</td>
									<td><input type="text" tabindex="-1" class="form-control" name="member_gender" id="member_gender" autocomplete="off" readonly value="<?php echo $membergender[$coremember['member_gender']];?>"/></td>
									<td></td>
								</tr>
								<tr>
									<td>No. Telp</td>
									<td>:</td>
									<td><input type="text" tabindex="-1" class="form-control" name="member_phone1" id="member_phone1" autocomplete="off" readonly value="<?php echo $coremember['member_phone'];?>"/></td>
									<td></td>
								</tr>
								<tr>
									<td>Alamat</td>
									<td>:</td>
									<td><textarea class="form-control" tabindex="-1" rows="3" id="comment" readonly ><?php echo $coremember['city_name'];?>, <?php echo $coremember['kecamatan_name'];?>, <?php echo $coremember['member_address'];?></textarea></td>
									<td></td>
								</tr>
								<tr>
									<td>Pekerjaan</td>
									<td>:</td>
									<td><input type="text" class="form-control" name="job_name" id="job_name" autocomplete="off" readonly value="<?php echo $coremember['member_job'];?>"tabindex="-1"/></td>
									<td></td>
								</tr>
								<tr>
									<td>Identitas</td>
									<td>:</td>
									<td><input type="text" class="form-control" name="identity_name" id="identity_name" autocomplete="off" readonly value="<?php 
											echo $memberidentity[$coremember['member_identity']];
										
										?>" tabindex="-1"/></td>
									<td></td>
								</tr>
								<tr>
									<td>No. Identitas</td>
									<td>:</td>
									<td><input type="text" class="form-control" name="member_identity_no" id="member_identity_no" autocomplete="off" readonly value="<?php echo $coremember['member_identity_no'];?>" tabindex="-1"/></td>
									<td></td>
								</tr>
								
							</tbody>
						</table>
				</div>
				<div class="col-md-8">
					<table style="width: 100%;" border="0" padding:"0">
							<tbody>
								<tr>
									<td>Jenis Akad</td>
									<td>:</td>
									<td><?php
										$isi=$this->uri->segment(3);
											if($isi > 0){
												echo form_dropdown('credit_id', $creditid, set_value('credit_id',$data['credit_id']),'id="credit_id" class="easyui-combobox"');
											}else{
												echo form_dropdown('credit_id', $creditid, set_value('credit_id',$data['credit_id']),'id="credit_id" class="easyui-combobox" disabled');
											}
									?>
									</td>
									<td>Sumber Dana</td>
									<td>:</td>
									<td><?php
									$isi=$this->uri->segment(3);
									if($isi > 0){
										echo form_dropdown('sumberdana', $sumberdana, set_value('sumberdana',$data['sumberdana']),'id="sumberdana" class="easyui-combobox"');
									}else{
										echo form_dropdown('sumberdana', $sumberdana, set_value('sumberdana',$data['sumberdana']),'id="sumberdana" class="easyui-combobox" disabled');
									}
									?></td>
								</tr>
								<tr>
									<td>Tanggal Realisasi</td>
									<td>:</td>
									<td>	<input type="text" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser" name="credit_account_date" id="credit_account_date" autocomplete="off"  onChange="duedatecalc(this);" value="<?php echo tgltoview($data['credit_account_date']); ?>" />
									</td>
									<td>Jangka waktu(Bulan)</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" name="credit_account_period" id="credit_account_period" autocomplete="off" value="<?php echo $data['credit_account_period']; ?>" onChange="duedatecalc(this);angsurancalc();" />
									</td>
								</tr>
								<tr>
									<td>Angsuran Tiap</td>
									<td>:</td>
									<td>
										<select name="deposito_account_due_date" class="easyui-combobox">
											<option value="Bulanan">Bulanan</option>
											<option value="Harian">Harian</option>
											<option value="Lain-lain">Lain-Lain</option>							
										</select>	
									</td>
									<td>Jatuh Tempo</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" name="credit_account_due_date" id="credit_account_due_date" autocomplete="off" data-options="formatter:myformatter,parser:myparser" value="<?php echo tgltoview($data['credit_account_due_date']); ?>" readonly /></td>
									</tr>
									<tr>
									<td>Business Office (BO)</td>
									<td>:</td>
									<td><?php echo form_dropdown('office_id', $coreoffice, set_value('office_id',$data['office_id']),'id="office_id" class="form-control select2me" onChange="function_elements_add(this.name, this.value);" ');?></td>

								</tr>
								<tr>
									<td>Biaya Materai</td>
									<td>:</td>
									<td>
										<input type="text" class="easyui-textbox" name="credit_account_materai_view" id="credit_account_materai_view" autocomplete="off" value="<?php echo $data['credit_account_materai_view']; ?>"/>
										<input type="hidden" class="easyui-textbox" name="credit_account_materai" id="credit_account_materai" autocomplete="off" value="<?php echo $data['credit_account_materai']; ?>"/>
									</td>
									<td>No Akad</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" name="credit_account_serial" id="credit_account_serial" autocomplete="off" value="<?php echo $data['credit_account_serial']; ?>" />
									</td>
								</tr>
								<tr>
									<td>Biaya Administrasi</td>
									<td>:</td>
									<td>
										<input type="text" class="easyui-textbox" name="credit_account_adm_cost_view" id="credit_account_adm_cost_view" autocomplete="off" value="<?php echo $data['credit_account_adm_cost_view']; ?>"/>
										<input type="hidden" class="easyui-textbox" name="credit_account_adm_cost" id="credit_account_adm_cost" autocomplete="off" value="<?php echo $data['credit_account_adm_cost']; ?>"/>
									</td>

									<td>Harga Pokok</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" name="credit_account_net_price_view" id="credit_account_net_price_view" autocomplete="off" value="<?php echo $data['credit_account_net_price_view']; ?>"/>
										<input type="hidden" class="easyui-textbox" name="credit_account_net_price" id="credit_account_net_price" autocomplete="off" value="<?php echo $data['credit_account_net_price']; ?>"/>
									</td>
								</tr>
								<tr>
									<td>Harga Jual</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" name="credit_account_sell_price_view" id="credit_account_sell_price_view" autocomplete="off" value="<?php echo $data['credit_account_sell_price_view']; ?>"/>
										<input type="hidden" class="easyui-textbox" name="credit_account_sell_price" id="credit_account_sell_price" autocomplete="off" value="<?php echo $data['credit_account_sell_price']; ?>"/>
									</td>
									<td>Uang Muka</td>
									<td>:</td>
									<td> <input type="text" class="easyui-textbox" name="credit_account_um_view" id="credit_account_um_view" autocomplete="off" value="<?php echo $data['credit_account_um_view']; ?>"/>
										<input type="hidden" class="easyui-textbox" name="credit_account_um" id="credit_account_um" autocomplete="off" value="<?php echo $data['credit_account_um']; ?>"/>
									</td>
								</tr>
								<tr>
									<td>Pembiayaan</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" name="credits_account_last_balance_principal_view" id="credits_account_last_balance_principal_view" autocomplete="off" value="<?php echo $data['credits_account_last_balance_principal_view'];?>"/>
										<input type="hidden" class="easyui-textbox" name="credits_account_last_balance_principal" id="credits_account_last_balance_principal" autocomplete="off" value="<?php echo $data['credits_account_last_balance_principal'];?>"/>
									</td>
									<td>Margin</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" name="credit_account_margin_view" id="credit_account_margin_view" autocomplete="off" value="<?php echo $data['credit_account_margin_view'];?>"/>
										<input type="hidden" class="easyui-textbox" name="credit_account_margin" id="credit_account_margin" autocomplete="off" value="<?php echo $data['credit_account_margin'];?>"/>
										<input type="text" class="easyui-textbox" readonly name="credit_account_rate" id="credit_account_rate" autocomplete="off" style="width: 30%" value="<?php echo $data['credit_account_rate'];?>"/>
									</td>
								</tr>
								<tr>
									<td>Angsuran Pokok</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" name="credits_account_principal_amount_view" id="credits_account_principal_amount_view" autocomplete="off" value="<?php echo $data['credits_account_principal_amount_view'];?>"/>
										<input type="hidden" class="easyui-textbox" name="credits_account_principal_amount" id="credits_account_principal_amount" autocomplete="off" value="<?php echo $data['credits_account_principal_amount'];?>"/>
									</td>
									<td>Angsuran Margin</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" name="credits_account_margin_amount_view" id="credits_account_margin_amount_view" autocomplete="off" value="<?php echo $data['credits_account_margin_amount_view'];?>"/>
										<input type="hidden" class="easyui-textbox" name="credits_account_margin_amount" id="credits_account_margin_amount" autocomplete="off" value="<?php echo $data['credits_account_margin_amount'];?>"/>
									</td>
								</tr>
								<tr>
									<td>Jumlah Angsuran</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" name="credit_account_payment_amount_view" id="credit_account_payment_amount_view" autocomplete="off" value="<?php echo $data['credit_account_payment_amount_view'];?>"/>
										<input type="hidden" class="easyui-textbox" name="credit_account_payment_amount" id="credit_account_payment_amount" autocomplete="off" value="<?php echo $data['credit_account_payment_amount'];?>"/>
									</td>
								</tr>
								<tr>
									<td>Nisbah (BMT)</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" name="credit_account_nisbah_bmt" id="credit_account_nisbah_bmt" autocomplete="off" value="<?php echo set_value('credit_account_nisbah_bmt',$data['credit_account_nisbah_bmt']);?>"/>
									</td>
									<td>Nisbah (Anggota)</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" name="credit_account_nisbah_agt" id="credit_account_nisbah_agt" autocomplete="off" value="<?php echo set_value('credit_account_nisbah_agt',$data['credit_account_nisbah_agt']);?>"/>
									</td>
								</tr>

								<tr>
									<td>Biaya Notaris</td>
									<td>:</td>
									<td>
										<input type="text" class="easyui-textbox" name="credit_account_notaris_view" id="credit_account_notaris_view" autocomplete="off" value="<?php echo set_value('credit_account_notaris_view',$data['credit_account_notaris_view']);?>"/>
										<input type="hidden" class="easyui-textbox" name="credit_account_notaris" id="credit_account_notaris" autocomplete="off" value="<?php echo set_value('credit_account_notaris',$data['credit_account_notaris']);?>"/>
									</td>
									<td>Biaya Asuransi</td>
									<td>:</td>
									<td>
										<input type="text" class="easyui-textbox" name="credit_account_insurance_view" id="credit_account_insurance_view" autocomplete="off" value="<?php echo set_value('credit_account_insurance',$data['credit_account_insurance']);?>"/>
										<input type="hidden" class="easyui-textbox" name="credit_account_insurance" id="credit_account_insurance" autocomplete="off" value="<?php echo set_value('credit_account_insurance',$data['credit_account_insurance']);?>"/>
									</td>
								</tr>
								<tr>
									<td>No Simpanan</td>
									<td>:</td>
									<td><?php echo form_dropdown('savings_account_id', $acctsavingsaccount, set_value('savings_account_id',$data['savings_account_id']),'id="savings_account_id" class="form-control select2me" onChange="function_elements_add(this.name, this.value);"');?>
									</td>
									<td>Agunan</td>
									<td>:</td>
									<td>	<a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#agunan">Input Agunan</a></td>
								</tr>

								<input type="hidden" class="easyui-textbox" name="credits_account_token" id="credits_account_token" autocomplete="off" value="<?php echo $token;?>"/>
							</tbody>
						</table>
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

<div id="agunan" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Agunan</h4>
      </div>
      <div class="modal-body">
      		<?php $this->load->view('AcctCreditAccountUtility/FormAddAcctCreditsAgunan_view'); ?>
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
            "url": "<?php echo site_url('AcctCreditAccountUtility/memberlist')?>",
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
