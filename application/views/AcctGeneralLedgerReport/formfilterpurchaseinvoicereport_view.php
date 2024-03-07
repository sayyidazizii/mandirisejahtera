<script>
	base_url = '<?php echo base_url();?>';
	function reset_all(){
		document.location = base_url+"purchaseinvoicereport/reset_search";
	}
	
	function openform(){
		var a = document.getElementById("passwordf").style;
		if(a.display=="none"){
			a.display = "block";
		}else{
			a.display = "none";
		}
		// document.getElementById("code").style.display = "block";
		// document.getElementById("name").style.display = "block";
	}
</script>
<?php
	$option	= array(0 =>"Header", 1=>"Detail");

$data=$this->session->userdata('filter-purchaseinvoicereport');
if(!is_array($data)){
		$data['start_date']		=date('d-m-Y');
		$data['end_date']		=date('d-m-Y');
		$data['supplier_id']	='';
}
?>

	
		<!-- BEGIN PAGE TITLE & BREADCRUMB-->
		<div class = "page-bar">
			<ul class="page-breadcrumb">
				<li>
				
						<a href="<?php echo base_url();?>">
							Home
						</a>
					<i class="fa fa-angle-right"></i>
				</li>
				<li>
					<a href="<?php echo base_url();?>purchaseinvoicereport">
						Purchase Invoice Report
					</a>
					<i class="fa fa-angle-right"></i>
				</li>
			</ul>
		</div>
		<h3 class="page-title">
			Purchase Invoice Report
		</h3>
		<!-- END PAGE TITLE & BREADCRUMB-->


<?php echo form_open('purchaseinvoicereport/filter',array('id' => 'myform', 'class' => '')); ?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Filter List
				</div>
				<div class="tools">
					<a href="javascript:;" class='expand'></a>
				</div>
			</div>
	<div class="portlet-body display-hide">
		<div class="form-body form">
			<div class = "row">
				<div class = "col-md-6">
					<div class="form-group form-md-line-input">
						<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date"  value="<?php echo tgltoview($data['start_date']);?>"/>
						<label class="control-label">Start Date
							<span class="required">
								*
							</span>
						</label>
					</div>
				</div>

				<div class = "col-md-6">
					<div class="form-group form-md-line-input">
						<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date"  value="<?php echo tgltoview($data['end_date']);?>"/>
						<label class="control-label">End Date
							<span class="required">
								*
							</span>
						</label>
					</div>
				</div>
			</div>
		<div class="form-group">
			<label class="control-label">Supplier</label>
			<?php
				echo form_dropdown('supplier_id', $supplier,set_value('supplier_id',$data['supplier_id']),'id="supplier_id" class="form-control select2me"');
			?>
			<!-- <input type="hidden" name="supplier_id" id="supplier_id" value="<?php echo $data['supplier_id']; ?>"/> -->
			
		</div>
	<div class="form-group">
		<div class="form-actions right">
			<button type="button" class="btn red" onClick="reset_search();"><i class="fa fa-times"></i> Reset</button>
			<button type="submit" class="btn green-jungle"><i class="fa fa-search"></i> Find</button>
					
		</div>
	</div>
</div>
</div>
</div>
</div>
</div>
<?php echo form_close(); ?>