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

</style>
<script>
	base_url = '<?= base_url()?>';
	mappia = "	<?php 
					$id = $this->uri->segment(3);
					$site_url = 'AcctCreditAccountUtility/addform/'.$id;
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

	$(document).on('change','#shm_taksiran_view',function(event){
		shm_taksiran_view				= $('#shm_taksiran_view')[0].value;	
		
		document.getElementById('shm_taksiran_view').value	= toRp(shm_taksiran_view);
		document.getElementById('shm_taksiran').value		= shm_taksiran_view;
		
	});

	function formupdate(data){
		
		if(data.value == "Sertifikat"){
			 document.getElementById("shm").style.display = "block";
			 document.getElementById("bpkb").style.display = "none";
			
		}
			if(data.value == "BPKB"){
			 document.getElementById("shm").style.display = "none";
			 document.getElementById("bpkb").style.display = "block";
		}		
	}

	function processAddArrayAgunan(){
		
		var tipe				= document.getElementById("tipe_agunan").value;
		var bpkb_nomor			= document.getElementById("bpkb_nomor").value;
		var bpkb_nama 			= document.getElementById("bpkb_nama").value;
		var bpkb_nopol 			= document.getElementById("bpkb_nopol").value;
		var bpkb_no_mesin 		= document.getElementById("bpkb_no_mesin").value;
		var bpkb_no_rangka 		= document.getElementById("bpkb_no_rangka").value;
		var bpkb_taksiran 		= document.getElementById("bpkb_taksiran").value;
		var bpkb_keterangan 	= document.getElementById("bpkb_keterangan").value;
		var shm_no_sertifikat 	= document.getElementById("shm_no_sertifikat").value;
		var shm_luas 			= document.getElementById("shm_luas").value;
		var shm_kedudukan 		= document.getElementById("shm_kedudukan").value;
		var shm_atas_nama 		= document.getElementById("shm_atas_nama").value;
		var shm_taksiran 		= document.getElementById("shm_taksiran").value;
		var shm_keterangan 		= document.getElementById("shm_keterangan").value;

	

			$('#offspinwarehouse').css('display', 'none');
			$('#onspinspinwarehouse').css('display', 'table-row');
			  $.ajax({
			  type: "POST",
			  url : "<?php echo site_url('AcctCreditAccountUtility/processAddArrayAgunan');?>",
			  data: {
					'tipe' 					: tipe,	
					'bpkb_nomor' 			: bpkb_nomor,
					'bpkb_nama' 			: bpkb_nama,
					'bpkb_nopol' 			: bpkb_nopol, 
					'bpkb_no_mesin' 		: bpkb_no_mesin, 
					'bpkb_no_rangka' 		: bpkb_no_rangka,
					'bpkb_taksiran'			: bpkb_taksiran,
					'bpkb_keterangan'		: bpkb_keterangan,	
					'shm_no_sertifikat' 	: shm_no_sertifikat,
					'shm_luas' 				: shm_luas, 
					'shm_kedudukan' 		: shm_kedudukan, 
					'shm_atas_nama' 		: shm_atas_nama,
					'shm_taksiran'			: shm_taksiran,
					'shm_keterangan'		: shm_keterangan,
					'session_name' 			: "addarrayacctcreditsagunan-"
				},
			  success: function(msg){
			   window.location.replace(mappia);
			 }
			});
	}
</script>

		<!-- <?php echo form_open('AcctCreditAccountUtility/processAddArrayAgunan',array('id' => 'myform', 'class' => 'horizontal-form')); ?> -->
		<div class="form-body">
			<table style="width: 100%;" border="0" padding:"0">
				<tbody  id="tipe" style="display:block" >
					<tr>
						<td>Pilih Tipe</td>
						<td> : </td>
						<td> <select name="tipe" id="tipe_agunan" class="form-control" onchange="formupdate(this)">
							<option value="pilih">Select</option>
							<option value="BPKB">BPKB</option>
							<option value="Sertifikat">Sertifikat</option>
							<option value="Lain-lain">Lain-Lain</option>
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
						<td>Nama</td>
						<td> : </td>
						<td> <input type="text" class="form-control" name="bpkb_nama" id="bpkb_nama" autocomplete="off"/>
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
						<td>Taksiran Rp</td>
						<td> : </td>
						<td><input type="text" class="form-control" name="bpkb_taksiran_view" id="bpkb_taksiran_view" autocomplete="off" value=""/>
						<input type="hidden" class="form-control" name="bpkb_taksiran" id="bpkb_taksiran" autocomplete="off" />
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
									<td>Nomor : ".$val['credits_agunan_bpkb_nomor'].", Nama : ".$val['credits_agunan_bpkb_nama'].", Nopol : ".$val['credits_agunan_bpkb_nopol'].", No. Rangka : ".$val['credits_agunan_bpkb_no_rangka'].", No. Mesin : ".$val['credits_agunan_bpkb_no_mesin'].", Taksiran : Rp. ".$val['credits_agunan_bpkb_taksiran'].", Ket : ".$val['credits_agunan_bpkb_keterangan']."</td>
								</tr>
							";
						} else if($val['credits_agunan_type'] == "Sertifikat"){
							echo "
								<tr>
									<td>$no</td>
									<td>".$val['credits_agunan_type']."</td>
									<td>Nomor : ".$val['credits_agunan_shm_no_sertifikat'].", Nama : ".$val['credits_agunan_shm_atas_nama'].", Luas : ".$val['credits_agunan_shm_luas'].", Kedudukan : ".$val['credits_agunan_shm_kedudukan'].", Taksiran : Rp. ".$val['credits_agunan_shm_taksiran'].", Ket : ".$val['credits_agunan_shm_keterangan']."</td>
								</tr>
							";
						}
						$no++;
					}
				}
			?>
		</table>
