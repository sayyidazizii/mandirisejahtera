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
font-size: 10px;


}
input:read-only {
		background-color: f0f8ff;
	}
</style>


<script>
	var loopkomisi 		= 1;
	var loopadm 		= 1;
	var loopnot 		= 1;
	var loopins 		= 1;
	var loop_principal 	= 1;
	var loop_margin 	= 1;
	var loop_payment 	= 1;

	// console.log('loop_margin awal '+ loop_margin);

	 function function_elements_add(name, value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('credit-account/add-function-element');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
			}
		});
	}

	function reset_data(){
		document.location = base_url+"credit-account/reset-data";
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

	

	function angsuranflat(){
		var bunga 			= document.getElementById("credit_account_interest").value;
		var jangka 			= document.getElementById("credit_account_period").value;
		var pembiayaan 		= document.getElementById("credits_account_last_balance_principal").value;


		var persbunga = parseFloat(bunga) / 100;

		if(pembiayaan == ''){
			var totalangsuran = 0;
			var angsuranpokok = 0;
			var angsuranbunga2= 0;
		} else {
			
			var angsuranpokok 	= Math.ceil(pembiayaan / jangka);
			var angsuranbunga 	= Math.floor((pembiayaan * bunga) / 100);
			var totalangsuran 	= angsuranpokok + angsuranbunga;

			var angsuranbunga2 	= totalangsuran - angsuranpokok;

			// console.log('angsuranbunga2 ' + angsuranbunga2);
			// console.log('angsuranpokok ' + angsuranpokok);
			// console.log('totalangsuran ' + totalangsuran);
		}


		$('#credit_account_payment_amount').textbox('setValue',totalangsuran);
		$('#credits_account_principal_amount').textbox('setValue',angsuranpokok);
		$('#credits_account_interest_amount').textbox('setValue',angsuranbunga2);

		$('#credit_account_payment_amount_view').textbox('setValue',totalangsuran);
		$('#credits_account_principal_amount_view').textbox('setValue',toRp(angsuranpokok));
		$('#credits_account_interest_amount_view').textbox('setValue',toRp(angsuranbunga2));

		var ntotalangsuran 		= 'credit_account_payment_amount';
		var ntotalangsuran2 	= 'credit_account_payment_amount_view';
		var nangsuranpokok 		= 'credits_account_principal_amount';
		var nangsuranpokok2 	= 'credits_account_principal_amount_view';
		var nangsuranbunga 		= 'credits_account_interest_amount';
		var nangsuranbunga2 	= 'credits_account_interest_amount_view';

		function_elements_add(ntotalangsuran, totalangsuran);
		function_elements_add(ntotalangsuran2, totalangsuran);
		function_elements_add(nangsuranpokok, angsuranpokok);
		function_elements_add(nangsuranpokok2, toRp(angsuranpokok));
		function_elements_add(nangsuranbunga, angsuranbunga2);
		function_elements_add(nangsuranbunga2, toRp(angsuranbunga2));
		
	}

	function rate3(nprest, vlrparc, vp) {
		var guess = 0.25;
		var maxit = 100;
		var precision = 14;
		var guess = Math.round(guess,precision);
		for (var i=0 ; i<maxit; i++) {
			var divdnd = vlrparc - ( vlrparc * (Math.pow(1 + guess , -nprest)) ) - (vp * guess);
			var divisor = nprest * vlrparc * Math.pow(1 + guess , (-nprest - 1)) - vp;
			var newguess = guess - ( divdnd / divisor );
			var newguess = Math.round(newguess, precision);
			if (newguess == guess) {
				return newguess;
			} else {
				guess = newguess;
			}
		}
		return null;
	}

	function angsurananuitas(){
		var bunga 			= document.getElementById("credit_account_interest").value;
		var jangka 			= document.getElementById("credit_account_period").value;
		var pembiayaan 		= document.getElementById("credits_account_last_balance_principal").value;

		var persbunga = bunga / 100;
		

		if(pembiayaan == ''){
			var totalangsuran = 0;
			var angsuranpokok = 0;
			var angsuranbunga2= 0;
		} else {
			if(bunga == 0){
				var totalangsuran = 0;
				var angsuranpokok = 0;
				var angsuranbunga2= 0;
			} else {
				var bungaA = Math.pow((1 + parseFloat(persbunga)), jangka);
				var bungaB = Math.pow((1 + parseFloat(persbunga)), jangka) - 1;
				var bungaC = bungaA / bungaB;
				var totalangsuran 	= pembiayaan * persbunga * bungaC ;
				var angsuranbunga2 	= (pembiayaan * bunga) / 100;
				var angsuranpokok 	= totalangsuran - angsuranbunga2;
				var totangsuran 	= Math.round((pembiayaan *(persbunga))+pembiayaan / jangka);
				
				$.ajax({
						type: "POST",
						url : "<?php echo site_url('credit-account/rate4');?>",
						data : {'nprest' : jangka, 'vlrparc' : totangsuran, 'vp' : pembiayaan},
						success: function(rate){
							// rates = Math.round(rate, 14);
							console.log(rate);
							// console.log(rates);
							var angsuranbunga2 		= pembiayaan * rate;
							var angsuranpokok 		= totangsuran - angsuranbunga2;
							var totalangsuran		= angsuranbunga2 + angsuranpokok;

							$('#credit_account_payment_amount').textbox('setValue',totalangsuran);
							$('#credits_account_principal_amount').textbox('setValue',angsuranpokok);
							$('#credits_account_interest_amount').textbox('setValue',angsuranbunga2);

							$('#credit_account_payment_amount_view').textbox('setValue',totalangsuran);
							$('#credits_account_principal_amount_view').textbox('setValue',toRp(angsuranpokok));
							$('#credits_account_interest_amount_view').textbox('setValue',toRp(angsuranbunga2));
					}
				});

				// var rate			= rate3(jangka, totangsuran, pembiayaan);
				

			
			}
			
		}


		var ntotalangsuran 		= 'credit_account_payment_amount';
		var ntotalangsuran2 	= 'credit_account_payment_amount_view';
		var nangsuranpokok 		= 'credits_account_principal_amount';
		var nangsuranpokok2 	= 'credits_account_principal_amount_view';
		var nangsuranbunga 		= 'credits_account_interest_amount';
		var nangsuranbunga2 	= 'credits_account_interest_amount_view';

		function_elements_add(ntotalangsuran, totalangsuran);
		function_elements_add(ntotalangsuran2, totalangsuran);
		function_elements_add(nangsuranpokok, angsuranpokok);
		function_elements_add(nangsuranpokok2, toRp(angsuranpokok));
		function_elements_add(nangsuranbunga, angsuranbunga2);
		function_elements_add(nangsuranbunga2, toRp(angsuranbunga2));
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

	function duedatecalc(data){
		var angsuran 	= document.getElementById("payment_period").value;
		var date2 		= document.getElementById("credit_account_date").value;
		var day2 		= date2.substring(0, 2);
		var month2 		= date2.substring(3, 5);
		var year2 		= date2.substring(6, 10);
		var date 		= year2 + '-' + month2 + '-' + day2;
		var date1		= new Date(date);
		var period 		= document.getElementById("credit_account_period").value;

		if(angsuran == 1){
			var a 		= moment(date1);
			var b 		= a.add(period, 'month');
			
			var tmp 	= date1.setMonth(date1.getMonth() + period);
			var endDate = new Date(tmp);
			var name 	= 'credit_account_due_date';
			var value 	= b.format('DD-MM-YYYY');

			
			var testDate 	= new Date(date);
			var tmp2 		= testDate.setMonth(testDate.getMonth() + 1);
			var date_first 	= testDate.toISOString();
			var day2 		= date_first.substring(8, 10);
			var month2 		= date_first.substring(5, 7);
			var year2 		= date_first.substring(0, 4); 
			var first 		= day2 + '-' + month2 + '-' + year2;
			var name2 		= 'credit_account_payment_to';
			var value2 		= first;

			$('#credit_account_due_date').textbox('setValue',b.format('DD-MM-YYYY'));
			$('#credit_account_payment_to').textbox('setValue',first);
			function_elements_add(name, value);
			function_elements_add(name2, value2);
		} else {
			var week 		= period * 7;
			var testDate 	= new Date(date1);
			var tmp 		= testDate.setDate(testDate.getDate() + week);
			var date_tmp 	= testDate.toISOString();
			var day 		= date_tmp.substring(8, 10);
			var month 		= date_tmp.substring(5, 7);
			var year 		= date_tmp.substring(0, 4); 
			var name 		= 'credit_account_due_date';
			var value 		= day + '-' + month + '-' + year;

			var testDate2 	= new Date(date1);
			var tmp2 		= testDate2.setDate(testDate2.getDate() + 7);
			var date_first 	= testDate2.toISOString();
			var day2 		= date_first.substring(8, 10);
			var month2 		= date_first.substring(5, 7);
			var year2 		= date_first.substring(0, 4); 
			var first 		= day2 + '-' + month2 + '-' + year2;
			var name2 		= 'credit_account_payment_to';
			var value2 		= first;

		

			$('#credit_account_due_date').textbox('setValue',value);
			$('#credit_account_payment_to').textbox('setValue',first);
			function_elements_add(name, value);
			function_elements_add(name2, value2);
		}
		
		
	}

	function receivedamount(){
		var pinjaman 			= document.getElementById("credits_account_last_balance_principal").value;
		var by_admin 			= document.getElementById("credit_account_adm_cost").value;
		var by_provisi 			= document.getElementById("credit_account_provisi").value;
		var by_komisi 			= document.getElementById("credit_account_komisi").value;
		var by_materai 			= document.getElementById("credit_account_materai").value;
		var by_insurance 		= document.getElementById("credit_account_insurance").value;
		var by_risk_reserve 	= document.getElementById("credit_account_risk_reserve").value;
		var by_stash 			= document.getElementById("credit_account_stash").value;
		var by_principal 		= document.getElementById("credit_account_principal").value;

		if(by_admin == ''){
			by_admin = 0;
		}

		if(by_provisi == ''){
			by_provisi = 0;
		}

		if(by_komisi == ''){
			by_komisi = 0;
		}

		if(by_materai == ''){
			by_materai = 0;
		}

		if(by_insurance == ''){
			by_insurance = 0;
		}

		if(by_risk_reserve == ''){
			by_risk_reserve = 0;
		}

		if(by_stash == ''){
			by_stash = 0;
		}

		if(by_principal == ''){
			by_principal = 0;
		}

		var terima_bersih 	= parseFloat(pinjaman) - (parseFloat(by_admin) + parseFloat(by_provisi) + parseFloat(by_komisi) + parseFloat(by_materai) + parseFloat(by_insurance) + parseFloat(by_risk_reserve) + parseFloat(by_stash) + parseFloat(by_principal));

		$('#credit_account_amount_received').textbox('setValue',terima_bersih);
		$('#credit_account_amount_received_view').textbox('setValue',toRp(terima_bersih));

		var name 	= 'credit_account_amount_received';
		var name2 	= 'credit_account_amount_received_view';

		function_elements_add(name, terima_bersih);
		function_elements_add(name2, toRp(terima_bersih));
	}

	function hitungbungaflat(){
		var jumlah_angsuran 	= document.getElementById("credit_account_payment_amount").value;
		var angsuranpokok 		= document.getElementById("credits_account_principal_amount").value;
		var pinjaman 			= document.getElementById("credits_account_last_balance_principal").value;
		var period 				= document.getElementById("credit_account_period").value;
		var interest 			= document.getElementById("credits_account_interest_amount").value;

		var angsuranbunga 		= parseFloat(jumlah_angsuran) - parseFloat(angsuranpokok);
		
	
		var bunga 				= (parseFloat(angsuranbunga) * 12) /  parseFloat(pinjaman);
		var bunga_perbulan 		= (parseFloat(bunga) * 100) / 12;			
		var bungafix			= bunga_perbulan.toFixed(3);

		$('#credit_account_interest').textbox('setValue',bungafix);
		$('#credit_account_interest_view').textbox('setValue',bungafix);
		$('#credits_account_interest_amount').textbox('setValue',angsuranbunga);
		$('#credits_account_interest_amount_view').textbox('setValue',toRp(angsuranbunga));

		var name 	= 'credit_account_interest';
		var name2 	= 'credit_account_interest_view';
		var name3 	= 'credits_account_interest_amount';
		var name4 	= 'credits_account_interest_amount_view';

		function_elements_add(name, bungafix);
		function_elements_add(name2, bungafix);
		function_elements_add(name3, angsuranbunga);
		function_elements_add(name4, toRp(angsuranbunga));
	}

	function hitungbungaflatanuitas(){
		var jumlah_angsuran 	= document.getElementById("credit_account_payment_amount").value;
		var angsuranpokok 		= document.getElementById("credits_account_principal_amount").value;
		var pinjaman 			= document.getElementById("credits_account_last_balance_principal").value;
		var period 				= document.getElementById("credit_account_period").value;
		var interest 			= document.getElementById("credits_account_interest_amount").value;

		var angsuranpokok		= pinjaman/period;

		var angsuranbunga 		= parseFloat(jumlah_angsuran) - parseFloat(angsuranpokok);
		

		var bunga 				= (parseFloat(angsuranbunga) * 12) /  parseFloat(pinjaman);
		var bunga_perbulan 		= (parseFloat(bunga) * 100) / 12;			
		var bungafix			= bunga_perbulan.toFixed(3);

		$('#credit_account_interest').textbox('setValue',bungafix);
		$('#credit_account_interest_view').textbox('setValue',bungafix);
		$('#credits_account_interest_amount').textbox('setValue',angsuranbunga);
		$('#credits_account_interest_amount_view').textbox('setValue',toRp(angsuranbunga));

		var name 	= 'credit_account_interest';
		var name2 	= 'credit_account_interest_view';
		var name3 	= 'credits_account_interest_amount';
		var name4 	= 'credits_account_interest_amount_view';

		function_elements_add(name, bungafix);
		function_elements_add(name2, bungafix);
		function_elements_add(name3, angsuranbunga);
		function_elements_add(name4, toRp(angsuranbunga));
	}

	base_url = '<?php echo base_url();?>';


	$(document).ready(function(){
		$('#credit_id').combobox({
			onChange: function(value){
				var name 			= 'credits_id';
			  	var credits_id 		= +document.getElementById("credit_id").value;

			  	function_elements_add(name, value);

			  	$.post(base_url + 'credit-account/get-credits-account-serial',
					{credits_id: credits_id},
	                function(data){	
	                var obj = $.parseJSON(data)	
	                console.log(obj)	   
	                //alert(obj["credit_account_serial"]);
	                	// $('#credit_account_serial').textbox('setValue',obj["credit_account_serial"]);
					},
				
				)
			  }
			})
	});



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

		$('#payment_type_id').combobox({
			onChange: function(value){
				var name   	= 'payment_type_id';

				function_elements_add(name, value);
				
				if(value == 1 ){
					$('#credit_account_payment_amount_view').textbox('readonly',false);  // disable it
				} else if(value == 2 ){
					$('#credit_account_payment_amount_view').textbox('readonly',false);  // disable it
				} else if(value == 3 ){
					$('#credit_account_payment_amount_view').textbox('readonly',true);  // disable it
				} else if(value == 4 ){
					$('#credit_account_payment_amount_view').textbox('readonly',true);  // disable it
				}
			}
		});

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
				var payment_type_id = +document.getElementById("payment_type_id").value;

		    	if(payment_type_id == 1){
					angsuranflat();
				} else if(payment_type_id == 2){
					angsurananuitas();
				} else if(payment_type_id == 3){
					angsuranflat();
				} else if(payment_type_id == 4){
					angsuranflat();
				}
				

			    	function_elements_add(name, value);
			    	duedatecalc(this);
			    	
				
			}
		});

		
		$('#credits_account_last_balance_principal_view').textbox({
			onChange: function(value){
				var name   	= 'credits_account_last_balance_principal';
				var name2   = 'credits_account_last_balance_principal_view';
				var payment_type_id = +document.getElementById("payment_type_id").value;

				if(loop_principal == 0){
					loop_principal= 1;
					return;
				}
				if(loop_principal ==1){
					loop_principal =0;
					var tampil = toRp(value);
				$('#credits_account_last_balance_principal').textbox('setValue', value);
				$('#credits_account_last_balance_principal_view').textbox('setValue', tampil);

				function_elements_add(name, value);
				function_elements_add(name2, tampil);

				if(payment_type_id == 1){
					angsuranflat();
				} else if(payment_type_id == 2){
					angsurananuitas();
				} else if(payment_type_id == 3){
					angsuranflat();
				} else if(payment_type_id == 4){
					angsuranflat();
				}
				
				receivedamount();
				
				}else{
					loop_principal=1;
					return;
				}
			}
		});

		$('#credit_account_payment_amount_view').textbox({
			onChange: function(value){
				var name   			= 'credit_account_payment_amount';
				var name2   		= 'credit_account_payment_amount_view';
				var payment_type_id = +document.getElementById("payment_type_id").value;
				var interest 		= +document.getElementById("credits_account_interest_amount").value;

				if(loop_payment == 0){
					loop_payment= 1;
					return;
				}

				if(loop_payment ==1){
					loop_payment =0;
					var tampil = toRp(value);
					$('#credit_account_payment_amount').textbox('setValue', value);
					$('#credit_account_payment_amount_view').textbox('setValue', tampil);
						
					function_elements_add(name, value);
					function_elements_add(name2, tampil);

					//hitungbungaflat();
					if(payment_type_id == 1 || payment_type_id == 3 || payment_type_id == 4){
						if(interest > 0 || interest != ''){
							hitungbungaflat();
						}
					} else if(payment_type_id == 2){
						if(interest > 0 || interest != ''){
							hitungbungaflatanuitas();
						}
					}
				}else{
					loop_payment=1;
					return;
				}
			}
		});

		$('#credit_account_interest_view').textbox({
			onChange: function(value){
				var name   	= 'credit_account_interest';
				var name2   = 'credit_account_interest_view';
				var payment_type_id = +document.getElementById("payment_type_id").value;


				if(loop_margin == 0){
					loop_margin = 1;
					return;
				}

				// alert(loop_margin);

				if(loop_margin == 1){
					loop_margin = 0;
					var tampil = value;
					$('#credit_account_interest').textbox('setValue', value);
					$('#credit_account_interest_view').textbox('setValue', tampil);

					console.log('onChange credit_account_interest '+ value);
					console.log('onChange credit_account_interest_view '+ tampil);
						function_elements_add(name, value);
						function_elements_add(name2, tampil);
				
						if(payment_type_id == 1){
							angsuranflat();
						} else if(payment_type_id == 2){
							angsurananuitas();
						} else if(payment_type_id == 3){
							angsuranflat();
						}else if(payment_type_id == 4){
							angsuranflat();
						}
					
				}else{
					loop_margin = 1;
					return;
				}
			}
		});

		$('#credit_account_provisi_view').textbox({
			onChange: function(value){
				var name   	= 'credit_account_provisi';
				var name2   = 'credit_account_provisi_view';

				if(loopkomisi == 0){
					loopkomisi= 1;
					return;
				}
				if(loopkomisi ==1){
					loopkomisi =0;
					var tampil = toRp(value);
				$('#credit_account_provisi').textbox('setValue', value);
				$('#credit_account_provisi_view').textbox('setValue', tampil);

				function_elements_add(name, value);
				function_elements_add(name2, tampil);
				receivedamount();
				
				}else{
					loopkomisi=1;
					return;
				}
			}
		});

		$('#credit_account_komisi_view').textbox({
			onChange: function(value){
				var name   	= 'credit_account_komisi';
				var name2   = 'credit_account_komisi_view';

				if(loopkomisi == 0){
					loopkomisi= 1;
					return;
				}
				if(loopkomisi ==1){
					loopkomisi =0;
					var tampil = toRp(value);
				$('#credit_account_komisi').textbox('setValue', value);
				$('#credit_account_komisi_view').textbox('setValue', tampil);

				function_elements_add(name, value);
				function_elements_add(name2, tampil);
				receivedamount();
				
				}else{
					loopkomisi=1;
					return;
				}
			}
		});


		$('#credit_account_adm_cost_view').textbox({
			onChange: function(value){
				var name   	= 'credit_account_adm_cost';
				var name2   = 'credit_account_adm_cost_view';

				if(loopadm == 0){
					loopadm= 1;
					return;
				}
				if(loopadm ==1){
					loopadm =0;
					var tampil = toRp(value);
				$('#credit_account_adm_cost').textbox('setValue', value);
				$('#credit_account_adm_cost_view').textbox('setValue', tampil);

				function_elements_add(name, value);
				function_elements_add(name2, tampil);
				receivedamount();
				
				}else{
					loopadm =1;
					return;
				}
			}
		});


		$('#credit_account_insurance_view').textbox({
			onChange: function(value){
				var name   	= 'credit_account_insurance';
				var name2   = 'credit_account_insurance_view';

				if(loopins == 0){
					loopins= 1;
					return;
				}
				if(loopins ==1){
					loopins =0;
					var tampil = toRp(value);
				$('#credit_account_insurance').textbox('setValue', value);
				$('#credit_account_insurance_view').textbox('setValue', tampil);

				function_elements_add(name, value);
				function_elements_add(name2, tampil);
				receivedamount();
				
				}else{
					loopins=1;
					return;
				}
			}
		});


		$('#credit_account_materai_view').textbox({
			onChange: function(value){
				var name   	= 'credit_account_materai';
				var name2   = 'credit_account_materai_view';

				if(loopins == 0){
					loopins= 1;
					return;
				}
				if(loopins ==1){
					loopins =0;
					var tampil = toRp(value);
				$('#credit_account_materai').textbox('setValue', value);
				$('#credit_account_materai_view').textbox('setValue', tampil);

				function_elements_add(name, value);
				function_elements_add(name2, tampil);
				receivedamount();
				
				}else{
					loopins=1;
					return;
				}
			}
		});


		$('#credit_account_risk_reserve_view').textbox({
			onChange: function(value){
				var name   	= 'credit_account_risk_reserve';
				var name2   = 'credit_account_risk_reserve_view';

				if(loopins == 0){
					loopins= 1;
					return;
				}
				if(loopins ==1){
					loopins =0;
					var tampil = toRp(value);
				$('#credit_account_risk_reserve').textbox('setValue', value);
				$('#credit_account_risk_reserve_view').textbox('setValue', tampil);

				function_elements_add(name, value);
				function_elements_add(name2, tampil);
				receivedamount();
				
				}else{
					loopins=1;
					return;
				}
			}
		});


		$('#credit_account_stash_view').textbox({
			onChange: function(value){
				var name   	= 'credit_account_stash';
				var name2   = 'credit_account_stash_view';

				if(loopins == 0){
					loopins= 1;
					return;
				}
				if(loopins ==1){
					loopins =0;
					var tampil = toRp(value);
				$('#credit_account_stash').textbox('setValue', value);
				$('#credit_account_stash_view').textbox('setValue', tampil);

				function_elements_add(name, value);
				function_elements_add(name2, tampil);
				receivedamount();
				
				}else{
					loopins=1;
					return;
				}
			}
		});


		$('#credit_account_principal_view').textbox({
			onChange: function(value){
				var name   	= 'credit_account_principal';
				var name2   = 'credit_account_principal_view';

				if(loopins == 0){
					loopins= 1;
					return;
				}
				if(loopins ==1){
					loopins =0;
					var tampil = toRp(value);
				$('#credit_account_principal').textbox('setValue', value);
				$('#credit_account_principal_view').textbox('setValue', tampil);

				function_elements_add(name, value);
				function_elements_add(name2, tampil);
				receivedamount();
				
				}else{
					loopins=1;
					return;
				}
			}
		});

		$('#payment_period').combobox({
			onChange: function(value){
				var name   	= 'payment_period';

				function_elements_add(name, value);

			}
		});

		$('#sumberdana').combobox({
			onChange: function(value){
				var name   	= 'sumberdana';

				function_elements_add(name, value);

			}
		});
 
    });



</script>
<?php echo form_open('credit-account/add',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
	$sesi 	= $this->session->userdata('unique');
	$data 	= $this->session->userdata('addcreditaccount-'.$sesi['unique']);
	$token 	= $this->session->userdata('acctcreditsaccounttoken-'.$sesi['unique']);

	if(empty($data['credits_id'])){
		$data['credits_id'] = 9;
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

	if(empty($data['credit_account_payment_to'])){
		$data['credit_account_payment_to'] = date('d-m-Y');
	}

	if(empty($data['credit_account_provisi'])){
		$data['credit_account_provisi'] = '';
	}

	if(empty($data['credit_account_provisi_view'])){
		$data['credit_account_provisi_view'] = '';
	}

	if(empty($data['credit_account_komisi'])){
		$data['credit_account_komisi'] = '';
	}
	
	if(empty($data['credit_account_komisi_view'])){
		$data['credit_account_komisi_view'] = '';
	}

	if(empty($data['credit_account_materai'])){
		$data['credit_account_materai'] = '';
	}
	
	if(empty($data['credit_account_materai_view'])){
		$data['credit_account_materai_view'] = '';
	}

	if(empty($data['credit_account_risk_reserve'])){
		$data['credit_account_risk_reserve'] = '';
	}
	
	if(empty($data['credit_account_risk_reserve_view'])){
		$data['credit_account_risk_reserve_view'] = '';
	}

	if(empty($data['credit_account_stash'])){
		$data['credit_account_stash'] = '';
	}

	if(empty($data['credit_account_principal'])){
		$data['credit_account_principal'] = '';
	}
	
	if(empty($data['credit_account_stash_view'])){
		$data['credit_account_stash_view'] = '';
	}
	
	if(empty($data['credit_account_principal_view'])){
		$data['credit_account_principal_view'] = '';
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

	if(empty($data['credits_account_last_balance_principal'])){
		$data['credits_account_last_balance_principal'] = '';
	}

	if(empty($data['credits_account_last_balance_principal_view'])){
		$data['credits_account_last_balance_principal_view'] = '';
	}

	if(empty($data['credit_account_interest'])){
		$data['credit_account_interest'] = '';
	}

	if(empty($data['credit_account_interest_view'])){

		$data['credit_account_interest_view'] = '';
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

	if(empty($data['credits_account_interest_amount'])){
		$data['credits_account_interest_amount'] = '';
	}

	if(empty($data['credits_account_interest_amount_view'])){
		$data['credits_account_interest_amount_view'] = '';
	}

	if(empty($data['credit_account_payment_amount'])){
		$data['credit_account_payment_amount'] = '';
	}

	if(empty($data['credit_account_payment_amount_view'])){
		$data['credit_account_payment_amount_view'] = '';
	}

	// if(empty($data['credit_account_notaris'])){
	// 	$data['credit_account_notaris'] = '';
	// }

	// if(empty($data['credit_account_notaris_view'])){
	// 	$data['credit_account_notaris_view'] = '';
	// }

	if(empty($data['credit_account_insurance'])){
		$data['credit_account_insurance'] = '';
	}

	if(empty($data['credit_account_insurance_view'])){
		$data['credit_account_insurance_view'] = '';
	}

	if(empty($data['credit_account_amount_received_view'])){
		$data['credit_account_amount_received_view'] = '';
	}

	if(empty($data['credit_account_amount_received'])){
		$data['credit_account_amount_received'] = '';
	}

	if(empty($data['office_id'])){
		$data['office_id'] = 0;
	}

	if(empty($data['savings_account_id'])){
		$data['savings_account_id'] = 0;
	}

	if(empty($data['payment_period'])){
		$data['payment_period'] = 0;
	}

	if(empty($data['sumberdana'])){
		$data['sumberdana'] = 0;
	}

	if(empty($data['payment_type_id'])){
		$data['payment_type_id'] = 0;
	}

	if(empty($coremember)){
		$coremember['member_id']				= '';
		$coremember['member_no']				= '';
		$coremember['member_name']				= '';
		$coremember['member_gender'] 			= 0;
		$coremember['member_date_of_birth']		= '';
		$coremember['member_phone'] 			= '';
		$coremember['member_mother']			= '';
		$coremember['member_address']			= '';
		$coremember['city_name']				= '';
		$coremember['kecamatan_name']			= '';
		$coremember['member_identity_no']		= '';
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
					Input Data Baru Pinjaman
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
									<td>
										<input type="text" tabindex="-1" class="form-control" name="member_gender" id="member_gender" autocomplete="off" readonly value="<?php echo $membergender[$coremember['member_gender']];?>"/></td>
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
								<!-- <tr>
									<td>Pekerjaan</td>
									<td>:</td>
									<td><input type="text" class="form-control" name="job_name" id="job_name" autocomplete="off" readonly value="<?php echo $coremember['member_company_job_title'];?>"tabindex="-1"/></td>
									<td></td>
								</tr> -->
								<tr>
									<td>Nama Ibu</td>
									<td>:</td>
									<td><input type="text" class="form-control" name="member_mother" id="member_mother" autocomplete="off" readonly value="<?php 
											echo $coremember['member_mother'];
										
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
					<table style="width: 100%;" border="0" padding="0">
							<tbody>
								<tr>
									<td>Jenis Pinjaman</td>
									<td>:</td>
									<td><?php
										$isi=$this->uri->segment(3);
										//	if($isi > 0){
											echo form_dropdown('credit_id', $creditid, set_value('credit_id',$data['credits_id']),'id="credit_id" class="easyui-combobox"');
										// }else{
										// 	echo form_dropdown('credit_id', $creditid, set_value('credit_id',$data['credits_id']),'id="credit_id" class="easyui-combobox" disabled');
										// }
									?>
									</td>
									<td>Sumber Dana</td>
									<td>:</td>
									<td>
										<?php
										$isi=$this->uri->segment(3);
										if($isi > 0){
											echo form_dropdown('sumberdana', $sumberdana, set_value('sumberdana',$data['sumberdana']),'id="sumberdana" class="easyui-combobox"');
										}else{
											echo form_dropdown('sumberdana', $sumberdana, set_value('sumberdana',$data['sumberdana']),'id="sumberdana" class="easyui-combobox" disabled');
										}
										?>
									</td>
								</tr>
								<tr>
									<td>Angsuran Tiap</td>
									<td>:</td>
									<td>
										<?php echo form_dropdown('payment_period', $paymentperiod, set_value('payment_period',$data['payment_period']),'id="payment_period" class="easyui-combobox"');?>	
									</td>
									<td>Jenis Angsuran</td>
									<td>:</td>
									<td>
										<?php 
										echo form_dropdown('payment_type_id', $paymenttype, set_value('payment_type_id',1),'id="payment_type_id" class="easyui-combobox"');	
										?>
									</td>
								</tr>
								<tr>
									<td>Tanggal Realisasi</td>
									<td>:</td>
									<td>	<input type="text" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser" name="credit_account_date" id="credit_account_date" autocomplete="off" readonly onChange="duedatecalc(this);" value="<?php echo tgltoview($data['credit_account_date']); ?>" />
									</td>
									<td>Preferensi Angsuran</td>
									<td>:</td>
									<td>
										<?php 
										echo form_dropdown('payment_preference_id', $paymentpreference, set_value('payment_preference_id',1),'id="payment_preference_id" class="easyui-combobox"');
										?>
									</td>
								</tr>
								<tr>
									<td>Tanggal Angsuran I</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" name="credit_account_payment_to" id="credit_account_payment_to" autocomplete="off" data-options="formatter:myformatter,parser:myparser" value="<?php echo tgltoview($data['credit_account_payment_to']); ?>" readonly />
									</td>
									<td>Jangka waktu</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" name="credit_account_period" id="credit_account_period" autocomplete="off" value="<?php echo $data['credit_account_period']; ?>" onChange="duedatecalc(this);" />
									</td>
								</tr>
								<tr>
									<td>Agunan</td>
									<td>:</td>
									<td>	<a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#agunan">Input Agunan</a></td>
									<td>Jatuh Tempo</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" name="credit_account_due_date" id="credit_account_due_date" autocomplete="off" data-options="formatter:myformatter,parser:myparser" value="<?php echo tgltoview($data['credit_account_due_date']); ?>" readonly /></td>
								</tr>
								<tr>
									<td>Business Office (BO)</td>
									<td>:</td>
									<td><?php echo form_dropdown('office_id', $coreoffice, set_value('office_id',$data['office_id']),'id="office_id" class="form-control select2me" onChange="function_elements_add(this.name, this.value);" ');?></td>
									<!-- <td>No Perjanjian Kredit</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" name="credit_account_serial" id="credit_account_serial" autocomplete="off" value="<?php echo $data['credit_account_serial']; ?>" readonly/>
									</td> -->

								</tr>

								<tr>
									<td>Plafon Pinjaman</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" name="credits_account_last_balance_principal_view" id="credits_account_last_balance_principal_view" autocomplete="off" value="<?php echo $data['credits_account_last_balance_principal_view'];?>"/>
										<input type="hidden" class="easyui-textbox" name="credits_account_last_balance_principal" id="credits_account_last_balance_principal" autocomplete="off" value="<?php echo $data['credits_account_last_balance_principal'];?>"/>
									</td>
									<td>Bunga (%)</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" name="credit_account_interest_view" id="credit_account_interest_view" autocomplete="off" value="<?php echo $data['credit_account_interest_view'];?>"/>
										<input type="hidden" class="easyui-textbox" name="credit_account_interest" id="credit_account_interest" autocomplete="off" value="<?php echo $data['credit_account_interest'];?>"/>
									</td>
								</tr>
								<tr>
									<td>Angsuran Pokok</td>
									<td>:</td>
									<td>
										<input type="text" class="easyui-textbox" name="credits_account_principal_amount_view" id="credits_account_principal_amount_view" autocomplete="off" value="<?php echo $data['credits_account_principal_amount_view'];?>" readonly/>
										<input type="hidden" class="easyui-textbox" name="credits_account_principal_amount" id="credits_account_principal_amount" autocomplete="off" value="<?php echo $data['credits_account_principal_amount'];?>"/>
									</td>
									<td>Angsuran Bunga</td>
									<td>:</td>
									<td><input type="text" class="easyui-textbox" name="credits_account_interest_amount_view" id="credits_account_interest_amount_view" autocomplete="off" value="<?php echo $data['credits_account_interest_amount_view'];?>" readonly/>
										<input type="hidden" class="easyui-textbox" name="credits_account_interest_amount" id="credits_account_interest_amount" autocomplete="off" value="<?php echo $data['credits_account_interest_amount'];?>"/>
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
									<td>Biaya Provisi</td>
									<td>:</td>
									<td>
										<input type="text" class="easyui-textbox" name="credit_account_provisi_view" id="credit_account_provisi_view" autocomplete="off" value="<?php echo $data['credit_account_provisi_view']; ?>"/>
										<input type="hidden" class="easyui-textbox" name="credit_account_provisi" id="credit_account_provisi" autocomplete="off" value="<?php echo $data['credit_account_provisi']; ?>"/>
									</td>
									<td>Biaya Administrasi</td>
									<td>:</td>
									<td>
										<input type="text" class="easyui-textbox" name="credit_account_adm_cost_view" id="credit_account_adm_cost_view" autocomplete="off" value="<?php echo $data['credit_account_adm_cost_view']; ?>"/>
										<input type="hidden" class="easyui-textbox" name="credit_account_adm_cost" id="credit_account_adm_cost" autocomplete="off" value="<?php echo $data['credit_account_adm_cost']; ?>"/>
									</td>
								</tr>
								<tr>
									<td>Biaya Survei</td>
									<td>:</td>
									<td>
										<input type="text" class="easyui-textbox" name="credit_account_komisi_view" id="credit_account_komisi_view" autocomplete="off" value="<?php echo $data['credit_account_komisi_view']; ?>"/>
										<input type="hidden" class="easyui-textbox" name="credit_account_komisi" id="credit_account_komisi" autocomplete="off" value="<?php echo $data['credit_account_komisi']; ?>"/>
									</td>
									<td>Biaya Materai</td>
									<td>:</td>
									<td>
										<input type="text" class="easyui-textbox" name="credit_account_materai_view" id="credit_account_materai_view" autocomplete="off" value="<?php echo $data['credit_account_materai_view']; ?>"/>
										<input type="hidden" class="easyui-textbox" name="credit_account_materai" id="credit_account_materai" autocomplete="off" value="<?php echo $data['credit_account_materai']; ?>"/>
									</td>
								</tr>
								<tr>
									<td>Biaya Asuransi</td>
									<td>:</td>
									<td>
										<input type="text" class="easyui-textbox" name="credit_account_insurance_view" id="credit_account_insurance_view" autocomplete="off" value="<?php echo $data['credit_account_insurance_view']; ?>"/>
										<input type="hidden" class="easyui-textbox" name="credit_account_insurance" id="credit_account_insurance" autocomplete="off" value="<?php echo $data['credit_account_insurance']; ?>"/>
									</td>
									<td>Biaya Cadangan Resiko</td>
									<td>:</td>
									<td>
										<input type="text" class="easyui-textbox" name="credit_account_risk_reserve_view" id="credit_account_risk_reserve_view" autocomplete="off" value="<?php echo $data['credit_account_risk_reserve_view']; ?>"/>
										<input type="hidden" class="easyui-textbox" name="credit_account_risk_reserve" id="credit_account_risk_reserve" autocomplete="off" value="<?php echo $data['credit_account_risk_reserve']; ?>"/>
									</td>
								</tr>
								<tr>
									<td>Biaya Simpanan Wajib</td>
									<td>:</td>
									<td>
										<input type="text" class="easyui-textbox" name="credit_account_stash_view" id="credit_account_stash_view" autocomplete="off" value="<?php echo $data['credit_account_stash_view']; ?>"/>
										<input type="hidden" class="easyui-textbox" name="credit_account_stash" id="credit_account_stash" autocomplete="off" value="<?php echo $data['credit_account_stash']; ?>"/>
									</td>
									<td>Biaya Simpanan Pokok</td>
									<td>:</td>
									<td>
										<input type="text" class="easyui-textbox" name="credit_account_principal_view" id="credit_account_principal_view" autocomplete="off" value="<?php echo $data['credit_account_principal_view']; ?>"/>
										<input type="hidden" class="easyui-textbox" name="credit_account_principal" id="credit_account_principal" autocomplete="off" value="<?php echo $data['credit_account_principal']; ?>"/>
									</td>
								</tr>
								<tr>
								</tr>
								<tr>
									<td>Terima Bersih</td>
									<td>:</td>
									<td>
										<input type="text" class="easyui-textbox" name="credit_account_amount_received_view" id="credit_account_amount_received_view" autocomplete="off" value="<?php echo set_value('credit_account_amount_received_view',$data['credit_account_amount_received_view']);?>"/>
										<input type="hidden" class="easyui-textbox" name="credit_account_amount_received" id="credit_account_amount_received" autocomplete="off" value="<?php echo set_value('credit_account_amount_received',$data['credit_account_amount_received']);?>"/>
									</td>
									<td>No Simpanan</td>
									<td>:</td>
									<td><?php echo form_dropdown('savings_account_id', $acctsavingsaccount, set_value('savings_account_id',$data['savings_account_id']),'id="savings_account_id" class="form-control select2me" onChange="function_elements_add(this.name, this.value);"');?>
									</td>
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
		<table id="myDataTable2" style="min-width:100%">
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
      		<?php $this->load->view('AcctCreditAccount/FormAddAcctCreditsAgunan_view'); ?>
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
    table = $('#myDataTable2').DataTable({ 
		
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "pageLength": 5,
        "order": [], //Initial no order.
        "ajax": {
            "url": "<?php echo site_url('credit-account/member-list')?>",
            "type": "POST",
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
