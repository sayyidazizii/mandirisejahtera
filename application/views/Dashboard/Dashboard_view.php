<script>
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

                // line
                // var graph2 = new AmCharts.AmGraph();
                // graph2.type = "line";
                // // graph2.title = "Expenses";
                // graph2.lineColor = "#fcd202";
                // graph2.valueField = "expenses";
                // graph2.lineThickness = 3;
                // graph2.bullet = "round";
                // graph2.bulletBorderThickness = 3;
                // graph2.bulletBorderColor = "#fcd202";
                // graph2.bulletBorderAlpha = 1;
                // graph2.bulletColor = "#ffffff";
                // graph2.dashLengthField = "dashLengthLine";
                // // graph2.balloonText = "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>";
                // chart.addGraph(graph2);

                // LEGEND
                var legend = new AmCharts.AmLegend();
                legend.useGraphSettings = true;
                chart.addLegend(legend);

                // WRITE
                chart.write("chartdiv");
            });

            var chartData3 = [
            <?php foreach ($kolektibilitas as $key => $val) { ?>
                {
                    "minggu": "<?php echo $val['minggu']; ?>",
                    "total1": <?php echo $val['total1']; ?>,
                    "total2": <?php echo $val['total2']; ?>,
                    "total3": <?php echo $val['total3']; ?>,
                    "total4": <?php echo $val['total4']; ?>,
                },
            <?php } ?>
               
            ];
            AmCharts.ready(function () {
                // SERIAL CHART
                chart = new AmCharts.AmSerialChart();
                chart.dataProvider = chartData3;
                chart.categoryField ="minggu";
                // the following two lines makes chart 3D
                // chart.depth3D = 20;
                // chart.angle = 30;

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
                graph4.lineColor = "#F4E23B";
                graph4.valueField = "total4";
                graph4.colorField = "color";
                graph4.balloonText = "<span style='font-size:14px'>Kolektibilitas 4: <b>[[value]]</b></span>";
                graph4.type = "column";
                graph4.lineAlpha = 0;
                graph4.fillAlphas = 1;
                chart.addGraph(graph4);


                // CURSOR
                var chartCursor = new AmCharts.ChartCursor();
                chartCursor.cursorAlpha = 0;
                chartCursor.zoomable = false;
                chartCursor.categoryBalloonEnabled = false;
                chart.addChartCursor(chartCursor);

                chart.creditsPosition = "top-right";
               
                // WRITE
                chart.write("chart_kolektibilitas");
            });
 
        </script>



<div class="row">
    <div class="col-md-12"> 
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <?php $month = date('m'); ?>
                    Grafik Bulan <?php echo $monthname[$month]; ?>
                </div>
            </div>
            <div class="portlet-body">
                <!-- BEGIN FORM-->
                <div class="form-body">
                    <div id="chartdiv" style="width:100%; height:400px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12"> 
        <div class="portlet box blue">
             <div class="portlet-title">
                <div class="caption">
                     <?php $month = date('m'); ?>
                    
                    Grafik Kolektibilitas Bulan <?php echo $monthname[$month]; ?>
                </div>
            </div>
            <div class="portlet-body">
                <!-- BEGIN FORM-->
                <div class="form-body">
                    <div id="chart_kolektibilitas" style="width:100%; height:400px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>