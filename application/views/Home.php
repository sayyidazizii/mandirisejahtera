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
	.amcharts-chart-div > a {
    display: none !important;
    }

    .list-toggle-container{
        background-color: #964b00;
    }

</style>
	
<script>
	base_url = '<?php echo base_url();?>';

    function reset_date(){
		document.location = base_url = "deposito-profit-sharing-check/reset-search";
	}

    $(document).ready(function(){
        $('#myModal').modal('show');
    });
	function reset_search(){
		document.location = base_url+"mainpage/reset_search";
	}

	function function_elements_add(name, value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('mainpage/function_elements_add');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
						// alert(name);
			}
		});
	}

	function function_state_add(value){
		// alert(value);
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('mainpage/function_state_add');?>",
				data : {'value' : value},
				success: function(msg){
			}
		});
	}

	$(document).ready(function(){
        $("#client_category_id").change(function(){
            var client_category_id = $("#client_category_id").val();
            $.ajax({
               type : "POST",
               url  : "<?php echo base_url(); ?>mainpage/getCoreClient",
               data : {client_category_id: client_category_id},
               success: function(data){
                   $("#client_id").html(data);
               }
            });
        });
    });

            var chart;

            var chartData = [
                <?php foreach ($json_pencairan_month_to_date as $key => $val) { ?>
                    {
                        "year": <?php echo $val['year']; ?>,
                        "income": <?php echo $val['income']; ?>,
                        "expenses": <?php echo $val['expenses']; ?>,
                        "jumlah_akun": <?php echo $val['jumlah_akun']; ?>,
                        "jumlah_akun_os": <?php echo $val['jumlah_akun_os']; ?>,
                    },    
                <?php } ?>
            ];

            AmCharts.ready(function () {

                // SERIAL CHART
                chart = new AmCharts.AmSerialChart();

                chart.dataProvider = chartData;
                chart.categoryField = "year";
                chart.startDuration = 1;

                // AXES
                // category
                var categoryAxis = chart.categoryAxis;
                categoryAxis.gridPosition = "start";

                // value
                var valueAxis = new AmCharts.ValueAxis();
                valueAxis.axisAlpha = 0;
                chart.addValueAxis(valueAxis);

                // GRAPHS
                // column graph
               
                var graph1 = new AmCharts.AmGraph();
                graph1.type = "column";
                graph1.title = "Pencairan ";
                graph1.fillColors = "#ADD981";
               // graph1.lineColor = "#ADD981";
                graph1.valueField = "income";
                graph1.lineAlpha = 0;
                graph1.fillAlphas = 1;
                graph1.balloonText = "<span style='font-size:13px;'>Total [[title]] :<b>[[value]]</b></span><br>dari [[jumlah_akun]] akun";
               // graph1.urlField = "url";
                chart.addGraph(graph1);

                var graph2 = new AmCharts.AmGraph();
                graph2.type = "column";
                graph2.title = "Outstanding ";
                graph2.lineColor = "#81acd9";
                graph2.valueField = "expenses";
                graph2.lineAlpha = 0;
                graph2.fillAlphas = 1;
                graph2.dashLengthField = "dashLengthColumn";
                graph2.alphaField = "alpha";
                graph2.balloonText = "<span style='font-size:13px;'> Total [[title]] :<b>[[value]]</b></span><br>dari [[jumlah_akun_os]] akun";
                graph2.urlField = "url";
                chart.addGraph(graph2);

                // LEGEND
                var legend = new AmCharts.AmLegend();
                legend.useGraphSettings = true;
                chart.addLegend(legend);

                // WRITE
                chart.write("chartdiv");
            });

            var chartData3 = <?php echo $kolektibilitas?>
               
            
            console.log(chartData3);
            AmCharts.ready(function () {
                // SERIAL CHART
                chart = new AmCharts.AmSerialChart();
                chart.dataProvider = chartData3;
                chart.categoryField ="minggu";

                // AXES
                // category
                var categoryAxis = chart.categoryAxis;
                categoryAxis.labelRotation = 90;
                categoryAxis.dashLength = 5;
                categoryAxis.gridPosition = "start";

                // value
                var valueAxis = new AmCharts.ValueAxis();
                valueAxis.title = "Kolektibilitas";
                valueAxis.dashLength = 5;
                chart.addValueAxis(valueAxis);

                // GRAPH

                var graph = new AmCharts.AmGraph();        
                graph.title = "Kolektibilitas 1 ";
                graph.fillColors = "#ADD981";

                graph.lineColor = "#ADD981";
                graph.valueField = "total1";
                graph.colorField = "color";
                graph.balloonText = "<span style='font-size:14px'>Kolektibilitas 1: <b>[[value]]</b></span>";
                graph.type = "column";
                graph.lineAlpha = 0;
                graph.fillAlphas = 1;
                chart.addGraph(graph);
                 // GRAPH 2
                var graph2 = new AmCharts.AmGraph();
                graph2.title = "Kolektibilitas 2 ";  
                graph2.fillColors = "#81acd9";
                graph2.lineColor = "#81acd9";
                graph2.valueField = "total2";
                graph2.colorField = "color";
                graph2.balloonText = "<span style='font-size:14px'>Kolektibilitas 2: <b>[[value]]</b></span>";
                graph2.type = "column";
                graph2.lineAlpha = 0;
                graph2.fillAlphas = 1;
                chart.addGraph(graph2);
                // GRAPH 2
                var graph3 = new AmCharts.AmGraph();
                graph3.title = "Kolektibilitas 3 ";
                graph3.fillColors = "#B5B8D3";
                graph3.lineColor = "#B5B8D3";
                graph3.valueField = "total3";
                graph3.colorField = "color";
                graph3.balloonText = "<span style='font-size:14px'>Kolektibilitas 3: <b>[[value]]</b></span>";
                graph3.type = "column";
                graph3.lineAlpha = 0;
                graph3.fillAlphas = 1;
                chart.addGraph(graph3);
                 // GRAPH 4
                var graph4 = new AmCharts.AmGraph();
                graph4.title = "Kolektibilitas 4 ";
                graph4.fillColors = "#F4E23B";

                graph4.lineColor = "#F4E23B";
                graph4.valueField = "total4";
                graph4.colorField = "color";
                graph4.balloonText = "<span style='font-size:14px'>Kolektibilitas 4: <b>[[value]]</b></span>";
                graph4.type = "column";
                graph4.lineAlpha = 0;
                graph4.fillAlphas = 1;
                chart.addGraph(graph4);
                 // GRAPH 5
                var graph5 = new AmCharts.AmGraph();
                graph5.title = "Kolektibilitas 5 ";
                graph5.fillColors = "#fc5203";

                graph5.lineColor = "#fc5203";
                graph5.valueField = "total5";
                graph5.colorField = "color";
                graph5.balloonText = "<span style='font-size:14px'>Kolektibilitas 5: <b>[[value]]</b></span>";
                graph5.type = "column";
                graph5.lineAlpha = 0;
                graph5.fillAlphas = 1;
                chart.addGraph(graph5);


                 var legend = new AmCharts.AmLegend();
                legend.useGraphSettings = true;
                chart.addLegend(legend);

                chart.write("chart_kolektibilitas");
            });
 
</script>
<?php
	$data=$this->session->userdata('filter-mainpage');
	if(!is_array($data)){
		$data['start_date']				= date('d-m-Y');
		$data['end_date']				= date('d-m-Y');
		$data['client_category_id']		= '';
		$data['client_id']				= '';

	}
?>
<?php echo $this->session->userdata('message_password');?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Menu Utama 
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body form">
					<div class = "row">
						<div class="col-lg-4">
                            <div class="mt-element-list">
                                <div class="mt-list-container list-simple ext-1 group">
                                    <a class="list-toggle-container">
                                        <div class="list-toggle done uppercase"> ANGGOTA
                                        </div>
                                    </a>
                                    <div class="panel-collapse collapse in" id="completed-simple">
                                        <ul>
                                        <?php foreach($menus as $menu){
                                                if($menu['id_menu']==21){ ?>
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">
                                                    <i class="icon-check"></i>
                                                </div>
                                                <div class="list-item-content">
                                                    <h3 class="uppercase">
                                                        <a href="<?php echo base_url()."member"; ?>">Data Anggota</a>
                                                    </h3>
                                                </div>
                                            </li>
                                            <?php } }?>
                                        <?php foreach($menus as $menu){
                                                if($menu['id_menu']==221){ ?>
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">
                                                    <i class="icon-check"></i>
                                                </div>
                                                <div class="list-item-content">
                                                    <h3 class="uppercase">
                                                        <a href="<?php echo base_url()."member/edit-member-savings"; ?>">Transaksi Simpanan Anggota</a>
                                                    </h3>
                                                </div>
                                            </li>     
                                            <?php } }?>                                           
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="mt-element-list">
                                <div class="mt-list-container list-simple ext-1 group">
                                    <a class="list-toggle-container">
                                        <div class="list-toggle uppercase"> Simpanan
                                            
                                        </div>
                                    </a>
                                    <div class="panel-collapse collapse in" id="completed-simple">
                                        <ul>
                                        <?php foreach($menus as $menu){
                                                if($menu['id_menu']==311){ ?>
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">
                                                    <i class="icon-check"></i>
                                                </div>
                                                <div class="list-item-content">
                                                    <h3 class="uppercase">
                                                        <a href="<?php echo base_url()."savings-account"?>">Tabungan</a>
                                                    </h3>
                                                </div>
                                            </li>
                                            <?php } }?>   
                                        <?php foreach($menus as $menu){
                                                if($menu['id_menu']==312){ ?>
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">
                                                    <i class="icon-check"></i>
                                                </div>
                                                <div class="list-item-content">
                                                    <h3 class="uppercase">
                                                        <a href="<?php echo base_url()."deposito-account"?>">Simpanan Berjangka</a>
                                                    </h3>
                                                </div>
                                            </li>      
                                            <?php } }?>                                                 
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="mt-element-list">
                                <div class="mt-list-container list-simple ext-1 group">
                                    <a class="list-toggle-container">
                                        <div class="list-toggle done uppercase"> Lending

                                        </div>
                                    </a>
                                    <div class="panel-collapse collapse in" id="completed-simple">
                                        <ul>
                                        <?php foreach($menus as $menu){
                                                if($menu['id_menu']==51){ ?>
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">
                                                    <i class="icon-check"></i>
                                                </div>
                                                <div class="list-item-content">
                                                    <h3 class="uppercase">
                                                        <a href="<?php echo base_url()."creddit-account/add-form"?>">Rekening Baru</a>
                                                    </h3>
                                                </div>
                                            </li>
                                            <?php } }?>   
                                        <?php foreach($menus as $menu){
                                                if($menu['id_menu']==56){ ?>
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">
                                                    <i class="icon-check"></i>
                                                </div>
                                                <div class="list-item-content">
                                                    <h3 class="uppercase">
                                                        <a href="<?php echo base_url()."creddit-account/detail"?>">Histori Angsuran Pinjaman</a>
                                                    </h3>
                                                </div>
                                            </li>     
                                            <?php } }?>                                                  
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                   	</div>
                   	<BR>
                   	<BR>
                   	<div class = "row">
                        <div class="col-lg-4">
                            <div class="mt-element-list">
                                <div class="mt-list-container list-simple ext-1 group">
                                    <a class="list-toggle-container">
                                        <div class="list-toggle uppercase"> Mutasi Tabungan
                                            
                                        </div>
                                    </a>
                                    <div class="panel-collapse collapse in" id="completed-simple">
                                        <ul>
                                        <?php foreach($menus as $menu){
                                                if($menu['id_menu']==411){ ?>
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">
                                                    <i class="icon-check"></i>
                                                </div>
                                                <div class="list-item-content">
                                                    <h3 class="uppercase">
                                                        <a href="<?php echo base_url()."savings-cash-mutation"?>">Mutasi Tunai</a>
                                                    </h3>
                                                </div>
                                            </li>
                                            <?php } }?>   
                                        <?php foreach($menus as $menu){
                                                if($menu['id_menu']==412){ ?>
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">
                                                    <i class="icon-check"></i>
                                                </div>
                                                <div class="list-item-content">
                                                    <h3 class="uppercase">
                                                        <a href="<?php echo base_url()."savings-transfer-mutation"?>">Mutasi Antar Rekening</a>
                                                    </h3>
                                                </div>
                                            </li>    
                                            <?php } }?>                                                   
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                         <div class="col-lg-4">
                            <div class="mt-element-list">
                                <div class="mt-list-container list-simple ext-1 group">
                                    <a class="list-toggle-container">
                                        <div class="list-toggle done uppercase"> Mutasi Simpanan Berjangka

                                        </div>
                                    </a>
                                    <div class="panel-collapse collapse in" id="completed-simple">
                                        <ul>
                                        <?php foreach($menus as $menu){
                                                if($menu['id_menu']==422){ ?>
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">
                                                    <i class="icon-check"></i>
                                                </div>
                                                <div class="list-item-content">
                                                    <h3 class="uppercase">
                                                        <a href="<?php echo base_url()."deposito-account/deposito-account-due-date"?>">Perpanjangan</a>
                                                    </h3>
                                                </div>
                                            </li>
                                            <?php } }?>   
                                        <?php foreach($menus as $menu){
                                                if($menu['id_menu']==312){ ?>
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">
                                                    <i class="icon-check"></i>
                                                </div>
                                                <div class="list-item-content">
                                                    <h3 class="uppercase">
                                                        <a href="<?php echo base_url()."deposito-account/add-new-deposito-account"?>">Simp Berjangka Baru</a>
                                                    </h3>
                                                </div>
                                            </li>       
                                            <?php } }?>                                                
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="mt-element-list">
                                <div class="mt-list-container list-simple ext-1 group">
                                    <a class="list-toggle-container">
                                        <div class="list-toggle uppercase"> Angsuran
                                            
                                        </div>
                                    </a>
                                    <div class="panel-collapse collapse in" id="completed-simple">
                                        <ul>
                                        <?php foreach($menus as $menu){
                                                if($menu['id_menu']==61){ ?>
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">
                                                    <i class="icon-check"></i>
                                                </div>
                                                <div class="list-item-content">
                                                    <h3 class="uppercase">
                                                        <a href="<?php echo base_url()."cash-payments/add"?>">Angsuran Tunai</a>
                                                    </h3>
                                                </div>
                                            </li>
                                            <?php } }?>   
                                        <?php foreach($menus as $menu){
                                                if($menu['id_menu']==62){ ?>
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">
                                                    <i class="icon-check"></i>
                                                </div>
                                                <div class="list-item-content">
                                                    <h3 class="uppercase">
                                                        <a href="<?php echo base_url()."cash-payments/add-cash-less"?>">Angsuran Non Tunai</a>
                                                    </h3>
                                                </div>
                                            </li>
                                            <?php } }?>   
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
							<?php
								?>
							</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
    <div class="col-md-12"> 
        <div class="portlet box blue">
            <div class="portlet-body">
                <div class="form-body form">
                <div class="row">
                    <div class="col-md-12"> 
                            <div class="mt-element-list">
                            <div class="mt-list-container list-simple ext-1 group">
                                    <a class="list-toggle-container">
                                    <div class="list-toggle done uppercase">
                                    
                                    <?php $month = date('m'); ?>
                                    Grafik Bulan <?php echo $monthname[$month]; ?>
                                    </div>
                                </a>
                            </div>
                            <div class="portlet-body">
                                <div class="form-body">
                                    <div id="chartdiv" style="width:100%; height:400px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12"> 
                        <div class="mt-element-list">
                            <div class="mt-list-container list-simple ext-1 group">
                                    <a class="list-toggle-container">
                                    <div class="list-toggle done uppercase">
                                        <?php $month = date('m'); ?>
                                    Grafik Kolektibilitas Bulan <?php echo $monthname[$month]; ?>
                                    </div>
                                </a>
                            </div>
                                <div class="portlet-body">
                                    <div class="form-body">
                                        <div id="chart_kolektibilitas" style="width:100%; height:400px;"> </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modall -->

<div id ="myModal" class="modal fade" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <!-- Modal Content -->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Notifikasi</h4>
            </div>
            <div class="modal-body">
                <?php if(count($acctdepositoprofitsharing) >0){

                    echo "<p style='color:red'> Hari ini ada ".count($acctdepositoprofitsharing)." jasa simpanan berjangka yang sudah jatuh tempo </p>";
                } else {
                    echo "<p> Hari ini tidak ada jasa simpanan berjangka yang jatuh tempo </p>";
                } ?>
            </div>
            <div class="modal-footer">
                <?php if(count($acctdepositoprofitsharing) >0){
                 ?>

                 <button onClick="reset_date();" class="btn btn-default" >open</button>
                 <button type="reset" class="btn btn-default" data-dismiss="modal">close</button>

                <?php 
                }else{
                ?>

                <button type="reset" class="btn btn-default" data-dismiss="modal">close</button>

                <?php 
                } ?>
            </div>
        </div>
    </div>
</div>