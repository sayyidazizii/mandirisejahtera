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
<script>
	base_url = '<?php echo base_url();?>';

	function reset_data(){
		document.location = base_url+"acctincomestatement/reset_data";
	}

	mappia = "	<?php 
					$site_url = 'acctincomestatement/addGiroPayment';
					echo site_url($site_url); 
				?>";

	function function_elements_add(name, value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('acctincomestatement/function_elements_add');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
						// alert(name);
			}
		});
	}
	
	function function_state_add(value){

		$.ajax({
				type: "POST",
				url : "<?php echo site_url('acctincomestatement/function_state_add');?>",
				data : {'value' : value},
				success: function(msg){
			}
		});
	}
	
</script>
			<!-- BEGIN PAGE TITLE & BREADCRUMB-->
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li>
			<a href="<?php echo base_url();?>">
				Beranda
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>AcctRecalculateEOM">
				Proses End Of Month
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>

<?php
$auth=$this->session->userdata('auth');
$data=$this->session->userdata('filter-AcctRecalculateEOM');
$year_now 	=	date('Y');
	if(!is_array($data)){
		$data['month_period']			= date('m');
		$data['year_period']			= $year_now;
		$data['branch_id']				= $auth['branch_id'];
	}

	if($auth['branch_status'] == 1){
		if(empty($data['branch_id'])){
			$data['branch_id'] = $auth['branch_id'];
		}
	} else {
		$data['branch_id'] = $auth['branch_id'];
	}
	
	
	for($i=($year_now-2); $i<($year_now+2); $i++){
		$year[$i] = $i;
	} 
	// print_r($data); exit;
?> 
			<!-- END PAGE TITLE & BREADCRUMB-->
<h3 class="page-title">
Proses End Of Month
</h3>
<?php echo form_open('AcctRecalculateEOM/processAcctRecalculateEOM',array('id' => 'myform', 'class' => '')); ?>
<div class="row">
	<div class="col-md-12">	
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					
				</div>
				<div class="actions">
					<a href="<?php echo base_url();?>AcctRecalculateEOM" class="btn btn-default btn-sm">
						<i class="fa fa-angle-left"></i>
						<span class="hidden-480">
							Kembali
						</span>
					</a>
				</div>
			</div>
			<div class="portlet-body">
				<!-- BEGIN FORM-->
				<div class="form-body form">
				<?php
					echo $this->session->userdata('message');
					$this->session->unset_userdata('message');
				?>
					<div class = "row">
						<div class="col-md-3">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('month_period', $monthlist,set_value('month_period',$data['month_period']),'id="month_period" class="form-control select2me"');
								?>
								<label>Periode</label>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('year_period', $year,set_value('year_period',$data['year_period']),'id="year_period" class="form-control select2me" ');
								?>
								<label></label>
							</div>
						</div>
						<?php if($auth['branch_status'] == 1){ ?>
							<div class = "col-md-6">
								<div class="form-group form-md-line-input">
									<?php
										echo form_dropdown('branch_id', $corebranch, set_value('branch_id', $data['branch_id']),'id="branch_id" class="form-control select2me"');
									?>
									<label class="control-label">Cabang</label>
								</div>
							</div>
					<?php } ?>
					</div>
					<div class="form-actions right">
						<input type="submit" name="Find" value="Proses" class="btn green" title="Search Data">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
