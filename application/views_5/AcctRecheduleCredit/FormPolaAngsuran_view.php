<style>

	th{
		font-size:14px  !important;
		font-weight: bold !important;
		text-align:center !important;
		margin : 0 auto;
		vertical-align:middle !important;
	}
	td{
		font-size:12px  !important;
		font-weight: normal !important;
	}

	.flexigrid div.pDiv input {
		vertical-align:middle !important;
	}
	
	.flexigrid div.pDiv div.pDiv2 {
		margin-bottom: 10px !important;
	}
	

</style>

<div class="row-fluid">
	<?php
		echo $this->session->userdata('message');
		$this->session->unset_userdata('message');
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
			<a href="<?php echo base_url();?>credit-account/add-form">
				Rekening Pinjaman
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Histori Angsuran Pinjaman <small>Kelola Data Rekening Pinjaman</small>
</h3>

<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$sesi=$this->session->userdata('polaAngsuran-AcctCreditsAccount');

?>	
	<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Pola Angsuran
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
					<table class="table table-striped table-bordered table-hover table-full-width">
					<thead>
						<tr>
							<th width="5%">Ke</th>
							<th width="10%">SISA</th>
							<th width="20%">POKOK</th>
							<th width="25%">BASIL</th>
							<th width="10%">JUMLAH</th>
						</tr>
					</thead>
					<tbody>
						</tbody>
						</table>
					</div>
					</div>
				</div>
				<!-- END EXAMPLE TABLE PORTLET-->
			</div>
		</div>