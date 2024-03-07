<style>
	th, td {
	  padding: 3px;
	  font-size: 13px;
	}
	input:focus { 
	  background-color: 42f483;
	}
	.custom{

		margin: 0px; padding-top: 0px; padding-bottom: 0px; height: 50px; line-height: 50px; width: 50px;

	}
	.textbox .textbox-text{
		font-size: 13px;


	}
	input:read-only {
		background-color: f0f8ff;
	}
</style>
<script>
	base_url = '<?= base_url()?>';
	mappia = "	<?php 
					$id = $this->uri->segment(3);
					$site_url = 'credit-account/add-form/'.$id;
					echo site_url($site_url); 
				?>";

	function toRp(number) {
		var number = number.toString(), 
		rupiah = number.split('.')[0], 
		cents = (number.split('.')[1] || '') +'00';
		rupiah = rupiah.split('').reverse().join('')
			.replace(/(\d{3}(?!$))/g, '$1,')
			.split('').reverse().join('');
		return rupiah + '.' + cents.slice(0, 2);
	}

	$(document).on('change','#bpkb_taksiran_view',function(event){
		bpkb_taksiran_view				= $('#bpkb_taksiran_view')[0].value;	
		
		document.getElementById('bpkb_taksiran_view').value	= toRp(bpkb_taksiran_view);
		document.getElementById('bpkb_taksiran').value		= bpkb_taksiran_view;
		
	});

	$(document).on('change','#bpkb_gross_view',function(event){
		bpkb_gross_view				= $('#bpkb_gross_view')[0].value;	
		
		document.getElementById('bpkb_gross_view').value	= toRp(bpkb_gross_view);
		document.getElementById('bpkb_gross').value			= bpkb_gross_view;
		
	});

	$(document).on('change','#shm_taksiran_view',function(event){
		shm_taksiran_view				= $('#shm_taksiran_view')[0].value;	
		
		document.getElementById('shm_taksiran_view').value	= toRp(shm_taksiran_view);
		document.getElementById('shm_taksiran').value		= shm_taksiran_view;
		
	});

	function formupdate(data){
		if(data.value != ''){
				if(data.value == "Sertifikat"){
				document.getElementById("shm").style.display 			= "block";
				document.getElementById("bpkb").style.display 			= "none";
				document.getElementById("atmjamsostek").style.display 	= "none";
				document.getElementById("other").style.display 			= "none";
				
			}else if(data.value == "BPKB"){
				document.getElementById("shm").style.display 			= "none";
				document.getElementById("bpkb").style.display 			= "block";
				document.getElementById("atmjamsostek").style.display 	= "none";
				document.getElementById("other").style.display 			= "none";

			}else if(data.value == "ATM / Jamsostek"){
				document.getElementById("shm").style.display 			= "none";
				document.getElementById("bpkb").style.display 			= "none";
				document.getElementById("atmjamsostek").style.display 	= "block";
				document.getElementById("other").style.display 			= "none";

			}else {
				document.getElementById("shm").style.display 			= "none";
				document.getElementById("bpkb").style.display 			= "none";
				document.getElementById("atmjamsostek").style.display 	= "none";
				document.getElementById("other").style.display 			= "block";
			}
		}
	}

	function processAddArrayAgunan(){
		
		var tipe					= document.getElementById("tipe_agunan").value;
		var bpkb_nomor				= document.getElementById("bpkb_nomor").value;
		var bpkb_type				= document.getElementById("bpkb_type").value;
		var bpkb_nama 				= document.getElementById("bpkb_nama").value;
		var bpkb_address 			= document.getElementById("bpkb_address").value;
		var bpkb_nopol 				= document.getElementById("bpkb_nopol").value;
		var bpkb_no_mesin 			= document.getElementById("bpkb_no_mesin").value;
		var bpkb_no_rangka 			= document.getElementById("bpkb_no_rangka").value;
		var bpkb_dealer_name 		= document.getElementById("bpkb_dealer_name").value;
		var bpkb_dealer_address 	= document.getElementById("bpkb_dealer_address").value;
		var bpkb_taksiran 			= document.getElementById("bpkb_taksiran").value;
		var bpkb_gross 				= document.getElementById("bpkb_gross").value;
		var bpkb_keterangan 		= document.getElementById("bpkb_keterangan").value;
		var shm_no_sertifikat 		= document.getElementById("shm_no_sertifikat").value;
		var shm_luas 				= document.getElementById("shm_luas").value;
		var shm_no_gs 				= document.getElementById("shm_no_gs").value;
		var shm_tanggal_gs 			= document.getElementById("shm_tanggal_gs").value;
		var shm_kedudukan 			= document.getElementById("shm_kedudukan").value;
		var shm_atas_nama 			= document.getElementById("shm_atas_nama").value;
		var shm_taksiran 			= document.getElementById("shm_taksiran").value;
		var shm_keterangan 			= document.getElementById("shm_keterangan").value;
		var atmjamsostek_nomor 		= document.getElementById("atmjamsostek_nomor").value;
		var atmjamsostek_nama 		= document.getElementById("atmjamsostek_nama").value;
		var atmjamsostek_bank 		= document.getElementById("atmjamsostek_bank").value;
		var atmjamsostek_taksiran 	= document.getElementById("atmjamsostek_taksiran").value;
		var atmjamsostek_keterangan = document.getElementById("atmjamsostek_keterangan").value;
		var other_keterangan 		= document.getElementById("other_keterangan").value;

	

			$('#offspinwarehouse').css('display', 'none');
			$('#onspinspinwarehouse').css('display', 'table-row');
			  $.ajax({
			  type: "POST",
			  url : "<?php echo site_url('credit-account/process-add-array-agunan');?>",
			  data: {
					'tipe' 						: tipe,	
					'bpkb_nomor' 				: bpkb_nomor,
					'bpkb_type' 				: bpkb_type,
					'bpkb_nama' 				: bpkb_nama,
					'bpkb_address' 				: bpkb_address,
					'bpkb_nopol' 				: bpkb_nopol, 
					'bpkb_no_mesin' 			: bpkb_no_mesin, 
					'bpkb_no_rangka' 			: bpkb_no_rangka,
					'bpkb_dealer_name' 			: bpkb_dealer_name,
					'bpkb_dealer_address' 		: bpkb_dealer_address,
					'bpkb_taksiran'				: bpkb_taksiran,
					'bpkb_gross'				: bpkb_gross,
					'bpkb_keterangan'			: bpkb_keterangan,	
					'shm_no_sertifikat' 		: shm_no_sertifikat,
					'shm_luas' 					: shm_luas, 
					'shm_no_gs' 				: shm_no_gs, 
					'shm_tanggal_gs' 			: shm_tanggal_gs, 
					'shm_kedudukan' 			: shm_kedudukan, 
					'shm_atas_nama' 			: shm_atas_nama,
					'shm_taksiran'				: shm_taksiran,
					'shm_keterangan'			: shm_keterangan,
					'atmjamsostek_nama'			: atmjamsostek_nama,
					'atmjamsostek_bank'			: atmjamsostek_bank,
					'atmjamsostek_nomor'		: atmjamsostek_nomor,
					'atmjamsostek_taksiran'		: atmjamsostek_taksiran,
					'atmjamsostek_keterangan'	: atmjamsostek_keterangan,
					'other_keterangan'			: other_keterangan,
					'session_name' 				: "addarrayacctcreditsagunan-"
				},
			  success: function(msg){
			   window.location.replace(mappia);
			 }
			});
	}
</script>

		<!-- <?php echo form_open('credit-account/process-add-array-agunan',array('id' => 'myform', 'class' => 'horizontal-form')); ?> -->
		<div class="form-body">
			<table style="width: 100%;" border="0" padding:"0">
				<tbody  id="tipe" style="display:block" >
					<tr>
						<td>Pilih Tipe</td>
						<td> : </td>
						<td> <select name="tipe" id="tipe_agunan" class="form-control" onchange="formupdate(this)">
							<option value="">Select</option>
							<option value="BPKB">BPKB</option>
							<option value="Sertifikat">Sertifikat</option>
							<option value="Bilyet Simpanan Berjangka">Bilyet Simpanan Berjangka</option>
							<option value="Elektro">Elektro</option>
							<option value="Dana Keanggotaan">Dana Keanggotaan</option>
							<option value="Tabungan">Tabungan</option>
							<option value="ATM / Jamsostek">ATM / Jamsostek</option>
							</select>
						</td>
					</tr>
				</tbody>
				<tbody  id="bpkb" style="display:none">
					<tr>
						<td>BPKB</td>
						<td> : </td>
						<td>  <input type="text" class="form-control" name="bpkb_nomor" id="bpkb_nomor" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td>Jenis Kendaraan</td>
						<td> : </td>
						<td> <input type="text" class="form-control" name="bpkb_type" id="bpkb_type" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td>Nama</td>
						<td> : </td>
						<td> <input type="text" class="form-control" name="bpkb_nama" id="bpkb_nama" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td>Alamat</td>
						<td> : </td>
						<td> <input type="text" class="form-control" name="bpkb_address" id="bpkb_address" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td>No.Pol</td>
						<td> : </td>
						<td> <input type="text" class="form-control" name="bpkb_nopol" id="bpkb_nopol" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td>No.Mesin</td>
						<td> : </td>
						<td> <input type="text" class="form-control" name="bpkb_no_mesin" id="bpkb_no_mesin" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td>No.Rangka</td>
						<td> : </td>
						<td> <input type="text" class="form-control" name="bpkb_no_rangka" id="bpkb_no_rangka" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td>Nama Dealer</td>
						<td> : </td>
						<td>
						<input type="text" class="form-control" name="bpkb_dealer_name" id="bpkb_dealer_name" autocomplete="off" />
						</td>
					</tr>
					<tr>
						<td>Alamat Dealer</td>
						<td> : </td>
						<td>
						<input type="text" class="form-control" name="bpkb_dealer_address" id="bpkb_dealer_address" autocomplete="off" />
						</td>
					</tr>
					<tr>
						<td>Taksiran Rp</td>
						<td> : </td>
						<td><input type="text" class="form-control" name="bpkb_taksiran_view" id="bpkb_taksiran_view" autocomplete="off" value=""/>
						<input type="hidden" class="form-control" name="bpkb_taksiran" id="bpkb_taksiran" autocomplete="off" />
						</td>
					</tr>
					<tr>
						<td>Uang Muka Gross Rp</td>
						<td> : </td>
						<td><input type="text" class="form-control" name="bpkb_gross_view" id="bpkb_gross_view" autocomplete="off" value=""/>
						<input type="hidden" class="form-control" name="bpkb_gross" id="bpkb_gross" autocomplete="off" />
						</td>
					</tr>
					<tr>
						<td>Keterangan</td>
						<td> : </td>
						<td><input type="text" class="form-control" name="bpkb_keterangan" id="bpkb_keterangan" autocomplete="off" />
						</td>
					</tr>
				</tbody>
				<tbody  id="shm" style="display:none">
					<tr>
						<td>No. Sertifikat</td>
						<td> : </td>
						<td>  <input type="text" class="form-control" name="shm_no_sertifikat" id="shm_no_sertifikat" autocomplete="off" />
						</td>
					</tr>
					<tr>
						<td>Luas</td>
						<td> : </td>
						<td> <input type="text" class="form-control" name="shm_luas" id="shm_luas" autocomplete="off" />
						</td>
					</tr>
					<tr>
						<td>No Gambar Situasi</td>
						<td> : </td>
						<td> <input type="text" class="form-control" name="shm_no_gs" id="shm_no_gs" autocomplete="off" />
						</td>
					</tr>
					<tr>
						<td>Tanggal Gambar Situasi</td>
						<td> : </td>
						<td> <input type="date" class="form-control" name="shm_tanggal_gs" id="shm_tanggal_gs" autocomplete="off" />
						</td>
					</tr>
					<tr>
						<td>Atas Nama</td>
						<td> : </td>
						<td> <input type="text" class="form-control" name="shm_atas_nama" id="shm_atas_nama" autocomplete="off" />
						</td>
					</tr>
					<tr>
						<td>Kedudukan</td>
						<td> : </td>
						<td><input type="text" class="form-control" name="shm_kedudukan" id="shm_kedudukan" autocomplete="off" />
						</td>
					</tr>
					<tr>
						<td>Taksiran Rp</td>
						<td> : </td>
						<td><input type="text" class="form-control" name="shm_taksiran_view" id="shm_taksiran_view" autocomplete="off" value=""/>
						<input type="hidden" hidden class="form-control" name="shm_taksiran" id="shm_taksiran" autocomplete="off" />
						</td>
					</tr>
					<tr>
						<td>Keterangan</td>
						<td> : </td>
						<td><input type="text" class="form-control" name="shm_keterangan" id="shm_keterangan" autocomplete="off" />
						</td>
					</tr>
				</tbody>
				<tbody  id="atmjamsostek" style="display:none">
					<tr>
						<td>Nomor ATM</td>
						<td> : </td>
						<td>  <input type="text" class="form-control" name="atmjamsostek_nomor" id="atmjamsostek_nomor" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td>Atas Nama</td>
						<td> : </td>
						<td> <input type="text" class="form-control" name="atmjamsostek_nama" id="atmjamsostek_nama" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td>Nama Bank</td>
						<td> : </td>
						<td> <input type="text" class="form-control" name="atmjamsostek_bank" id="atmjamsostek_bank" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td>Taksiran Rp</td>
						<td> : </td>
						<td> <input type="text" class="form-control" name="atmjamsostek_taksiran" id="atmjamsostek_taksiran" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td>Rek. Tabungan / No. BPJS</td>
						<td> : </td>
						<td><input type="text" class="form-control" name="atmjamsostek_keterangan" id="atmjamsostek_keterangan" autocomplete="off" />
						</td>
					</tr>
				</tbody>
				<tbody  id="other" style="display:none">
					<tr>
						<td>Keterangan</td>
						<td> : </td>
						<td><textarea type="text" class="form-control" name="other_keterangan" id="other_keterangan" autocomplete="off" style="height:60px;" ></textarea>
						</td>
					</tr>
				</tbody>
			</table>
		<div class="row">
			<div class="col-md-12" style='text-align:left'>
				<input type="button" name="add2" id="buttonAddArrayInvtGoodsReceivedNote" value="Add" class="btn green-jungle" title="Simpan Data" onClick="processAddArrayAgunan();">
			</div>	
		</div>
		<!-- <?php echo form_close(); ?> -->

		<?php 
			$sesi = $this->session->userdata('unique');
			$daftaragunan = $this->session->userdata('addarrayacctcreditsagunan-'.$sesi['unique']);
			// print_r($daftaragunan);
		?>

		<table class="table table-striped table-hover">
			<tr>
				<th>No</th>
				<th>Type</th>
				<th>Keterangan</th>
			</tr>
			<?php 
				$no = 1;
				if(empty($daftaragunan)){

				} else {
					foreach ($daftaragunan as $key => $val) {
						if($val['credits_agunan_type'] == "BPKB"){
							echo "
								<tr>
									<td>$no</td>
									<td>".$val['credits_agunan_type']."</td>
									<td>Nomor : ".$val['credits_agunan_bpkb_nomor'].", Jenis: ".$val['credits_agunan_bpkb_type'].", Nama : ".$val['credits_agunan_bpkb_nama'].", Alamat: ".$val['credits_agunan_bpkb_address'].", Nopol : ".$val['credits_agunan_bpkb_nopol'].", No. Rangka : ".$val['credits_agunan_bpkb_no_rangka'].", No. Mesin : ".$val['credits_agunan_bpkb_no_mesin'].", Nama Dealer: ".$val['credits_agunan_bpkb_dealer_name'].", Alamat Dealer: ".$val['credits_agunan_bpkb_dealer_address'].", Taksiran : Rp. ".$val['credits_agunan_bpkb_taksiran'].", Uang Muka Gross : Rp. ".$val['credits_agunan_bpkb_gross'].", Ket : ".$val['credits_agunan_bpkb_keterangan']."</td>
								</tr>
							";
						} else if($val['credits_agunan_type'] == "Sertifikat"){
							echo "
								<tr>
									<td>$no</td>
									<td>".$val['credits_agunan_type']."</td>
									<td>Nomor : ".$val['credits_agunan_shm_no_sertifikat'].", Nama : ".$val['credits_agunan_shm_atas_nama'].", Luas : ".$val['credits_agunan_shm_luas'].", No.GS : ".$val['credits_agunan_shm_no_gs'].", Tgl.GS : ".$val['credits_agunan_shm_gambar_gs'].", Kedudukan : ".$val['credits_agunan_shm_kedudukan'].", Taksiran : Rp. ".$val['credits_agunan_shm_taksiran'].", Ket : ".$val['credits_agunan_shm_keterangan']."</td>
								</tr>
							";
						}else if($val['credits_agunan_type'] == "ATM / Jamsostek"){
							echo "
								<tr>
									<td>$no</td>
									<td>".$val['credits_agunan_type']."</td>
									<td>Nomor : ".$val['credits_agunan_atmjamsostek_nomor'].", Atas Nama : ".$val['credits_agunan_atmjamsostek_nama'].", Nama Bank : ".$val['credits_agunan_atmjamsostek_bank'].", Taksiran : Rp. ".$val['credits_agunan_atmjamsostek_taksiran'].", Ket : ".$val['credits_agunan_atmjamsostek_keterangan']."</td>
								</tr>
							";
						}else{
							echo "
								<tr>
									<td>$no</td>
									<td>".$val['credits_agunan_type']."</td>
									<td>Keterangan : ".$val['credits_agunan_other_keterangan']."</td>
								</tr>
							";
						}
						$no++;
					}
				}
			?>
		</table>
