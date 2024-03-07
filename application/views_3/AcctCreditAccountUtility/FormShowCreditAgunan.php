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
	<table class="table table-striped table-hover">
		<tr>
			<th>No</th>
			<th>Type</th>
			<th>Keterangan</th>
		</tr>
		<?php 
			$no = 1;
			if(empty($acctcreditsagunan)){

			} else {
				foreach ($acctcreditsagunan as $key => $val) {
					if($val['credits_agunan_type'] == 1){
						echo "
							<tr>
								<td>$no</td>
								<td>".$val['credits_agunan_type']."</td>
								<td>Nomor : ".$val['credits_agunan_bpkb_nomor'].", Nama : ".$val['credits_agunan_bpkb_nama'].", Nopol : ".$val['credits_agunan_bpkb_nopol'].", No. Rangka : ".$val['credits_agunan_bpkb_no_rangka'].", No. Mesin : ".$val['credits_agunan_bpkb_no_mesin'].", Taksiran : Rp. ".$val['credits_agunan_bpkb_taksiran'].", Ket : ".$val['credits_agunan_bpkb_keterangan']."</td>
							</tr>
						";
					} else if($val['credits_agunan_type'] == 2){
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
