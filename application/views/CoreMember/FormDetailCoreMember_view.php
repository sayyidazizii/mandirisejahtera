<style>
	th, td {
	  padding: 3px;
	}
	input:focus { 
	  background-color: 42f483;
	}
</style>


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
			<a href="<?php echo base_url();?>member">
				Daftar Anggota
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>member/detail/"<?php echo $coremember['member_id'] ?>>
				Detail Anggota 
			</a>
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
						Form Detail
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>member" class="btn btn-default btn-sm">
							<i class="fa fa-angle-left"></i>
							<span class="hidden-480">
								Kembali
							</span>
						</a>
					</div>
				</div>
				<div class="portlet-body form">
					<div class="form-body">
						<div class="row">
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">No. Anggota</td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_no" id="member_no" autocomplete="off" value="<?php echo set_value('member_no', $coremember['member_no']);?>" style="width: 60%" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Nama Anggota</td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo set_value('member_name', $coremember['member_name']);?>" style="width: 100%" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Sifat Anggota</td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo  $membercharacter[$coremember['member_character']];?>" style="width: 100%" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Jenis Kelamin</td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo $membergender[$coremember['member_gender']];?>" style="width: 100%" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Provinsi</td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo $coremember['province_name'];?>" style="width: 100%" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Kabupaten</td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo $coremember['city_name'];?>" style="width: 100%" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Kecamatan</td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo $coremember['kecamatan_name'];?>" style="width: 100%" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Kode Pos</td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo $coremember['member_postal_code'];?>" style="width: 100%" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Alamat</td>
										<td width="5%"></td>
										<td width="60%"><textarea rows="3" name="member_address" id="member_address" class="easyui-textarea" style="width: 100%" disabled><?php echo $coremember['member_address'];?></textarea></td>
									</tr>
								</table>
							</div>
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table>
									<tr>
										<td width="35%">Tempat Lahir</td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo $coremember['member_place_of_birth'];?>" style="width: 100%" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Tanggal Lahir</td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo tgltoview($coremember['member_date_of_birth']);?>" style="width: 100%" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">No. HP</td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_phone" id="member_phone" autocomplete="off" value="<?php echo $coremember['member_phone'];?>" style="width: 100%" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">No. Identitas</td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo $coremember['member_identity_no'];?>" style="width: 100%" readonly/>
										</td>
									</tr>
									
									<tr>
										<td width="35%">Nama Ibu Kandung</td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo $coremember['member_mother'];?>" style="width: 100%" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Saldo Simp Pokok</td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_principal_savings_last_balance_view" id="member_principal_savings_last_balance_view" autocomplete="off" value="<?php echo number_format($coremember['member_principal_savings_last_balance'], 2);?>" style="width: 100%" readonly/>
										</td>

									<tr>
										<td width="35%">Saldo Simp Khusus</td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_special_savings_last_balance_view" id="member_special_savings_last_balance_view" autocomplete="off" style="width: 100%" value="<?php echo number_format($coremember['member_special_savings_last_balance'], 2);?>" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">Saldo Simp Wajib</td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_mandatory_savings_last_balance_view" id="member_mandatory_savings_last_balance_view" autocomplete="off" style="width: 100%" value="<?php echo number_format($coremember['member_mandatory_savings_last_balance'], 2);?>" readonly/>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<div class="form-body">
						<div class="row">
							<table class="table table-striped table-bordered table-full-width">
								<tr>
									<td width="48%">
										<table class="table table-striped table-bordered table-hover table-full-width">
											<thead>
												<tr>
													<th colspan="3"><center style="font-weight: bold;">Daftar Simpanan <?php echo $coremember['member_name']; ?></center></th>
												</tr>
												<tr>
													<th width="3%"><center>No</center></th>
													<th width="7%"><center>No. Rek. Simpanan</center></th>
													<th width="12%"><center>Jenis Simpanan</center></th>
												</tr>
											</thead>
											<tbody>
												<?php 
													$no = 1;
													foreach ($acctsavingsaccount as $key => $val) {
														echo "
															<tr>
																<td align='center'>$no</td>
																<td>".$val['savings_account_no']."</td>
																<td>".$val['savings_name']."</td>
															</tr>
														";

														$no++;
													}
												?>
											</tbody>
										</table>
									</td>
									<td></td>
									<td width="48%">
										<table class="table table-striped table-bordered table-hover table-full-width">
											<thead>
												<tr>
													<th colspan="3"><center style="font-weight: bold;">Daftar Pembiayaan <?php echo $coremember['member_name']; ?></center></th>
												</tr>
												<tr>
													<th width="3%"><center>No</center></th>
													<th width="7%"><center>No. Akad</center></th>
													<th width="12%"><center>Jenis Pembiayaan</center></th>
												</tr>
											</thead>
											<tbody>
												<?php 
													$no = 1;
													foreach ($acctcreditsaccount as $key => $val) {
														echo "
															<tr>
																<td align='center'>$no</td>
																<td>".$val['credits_account_serial']."</td>
																<td>".$val['credits_name']."</td>
															</tr>
														";

														$no++;
													}
												?>
											</tbody>
										</table>
									</td>
								</tr>
							</table>
<!-- 
							<div class="col-md-1"></div>
							<div class="col-md-5">
								
							</div> -->
						</div>			
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
