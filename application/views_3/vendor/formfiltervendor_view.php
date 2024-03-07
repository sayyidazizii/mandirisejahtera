<?php
	echo form_open("vendor/filter", array('class' => 'horizontal-form'));
	$sesi = $this->session->userdata('filter-vendor');
	if(!is_array($sesi)){
		$sesi['vendor_name']			='';
		$sesi['status']		='';
	}
?>
<script>
	base_url = '<?php echo base_url(); ?>';	
	function ulang(){
		document.getElementById("vendor_name").value = "";
		document.getElementById("status").value = "";
	}	
	
	function warningvendorname(inputname) {
		//var letter = /^[0-9a-zA-Z]+$+ +/;  
		var letter = /^[a-zA-Z-,]+(\s{0,1}[a-zA-Z-, ])*$/
		if(inputname.value.match(letter)){
			return true;
		}else{
			alert('Please input alphanumeric characters only');
			document.getElementById("vendor_name").value = "";
			return false;
		}
	}
</script>
<?php 
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>
<div class="row">
	<div class="col-md-12">
		<!-- BEGIN PAGE TITLE & BREADCRUMB-->
		<h3 class="page-title">
			Vendor		
		</h3>
		<ul class="page-breadcrumb breadcrumb">
			<li>
				<i class="fa fa-home"></i>
				<a href="<?php echo base_url();?>">
					Home
				</a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<a href="<?php echo base_url();?>vendor">
					Vendor
				</a>
			</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Vendor List
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>vendor/add" class="btn default yellow-stripe">
							<i class="fa fa-plus"></i>
							<span class="hidden-480">
								New Vendor 
							</span>
						</a>
						<a href="<?php echo base_url();?>vendor/export" class="btn default yellow-stripe">
							<i class="fa fa-download"></i>
							<span class="hidden-480">
								Export
							</span>
						</a>
						<a href="<?php echo base_url();?>vendor/import" class="btn default yellow-stripe">
							<i class="fa fa-upload"></i>
							<span class="hidden-480">
								Import
							</span>
						</a>
					</div>
				</div>
				<div class="portlet-body">
					<div class="form-body">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Vendor Name</label>
									<input type="text" name="vendor_name" id="vendor_name" value="<?php echo $sesi['vendor_name'];?>" class="form-control" onChange="warningvendorname(vendor_name);" placeholder="Vendor Name">
								</div>
							</div>
						</div>
						
						<div class="row">
						
							<div class="form-group">
								<label class="control-label">Vendor Status</label>
								<div class="input-group col-md-8">
									<label class="checkbox inline">
									<input type="radio" name="status" id="status" value="1" checked <?php $sesi['status'] ?> > Active </label>
								
									<label class="checkbox inline">
									<input type="radio" name="status" id="status" value="0" <?php $data['status'] ?> > Not Active </label>
								</div>
							</div>
						
					</div>
						
						<div class="row">
							<div class="col-md-12 " style="text-align  : right !important;">
								<input type="reset" name="Reset" value="Reset" class="btn btn-danger" onclick="ulang()">
								<button type="submit" class="btn blue"> Find</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>		
	</div>
</div>