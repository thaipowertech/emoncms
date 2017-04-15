<!DOCTYPE PHP>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
 <! Header of Highchart ////////////////>
  	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
  	<script src="http://code.highcharts.com/highcharts.js"></script>
	<script src="http://code.highcharts.com/modules/exporting.js"></script> 
<script>$(function () {
                        Highcharts.chart('chartDaily', {
                            title: {
                                text: 'Energy Daily (2017-04-13)'
                            },
                            xAxis: [{
                                categories: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30],
                                crosshair: true
                            }],
                            yAxis: [{ // Primary yAxis
                                min: 0,
                                title:
                                {
                                    text: 'Energy (kWh)'
                                }
                            }, { // Tertiary yAxis
                                min: 0,
                                title: {
                                    text: 'Insolation (kWh/m2)'
                                },
                                opposite: true
                            }],
                            tooltip: {
                                shared: true
                            },
                            series: [{
                                name: 'Energy',
                                type: 'column',
                                data: [1343,142,1895,1759,1094,1352,1514,159,153,2208,1088,1106,80,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]
                            }, {
                                name: 'Insolation',
                                type: 'line',
                                yAxis: 1,
                                color: '#FF0000',
                                data: [3967.7,5484.783,5929.483,5194.867,3017.317,3616.117,5550.133,5324.817,6802.35,6498.117,3244.95,7245.733,4030.3,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]
                            }]
                        });
                    });$(function () {
                    Highcharts.chart('chartMonthly', {
                        chart:
                        {
                            type: 'column'
                        },
                        title:
                        {
                            text: 'Energy Monthly (2017)'
                        },
                        xAxis:
                        {
                            categories: [1,2,3,4,5,6,7,8,9,10,11,12],
                            crosshair: true
                        },
                        yAxis:
                        {
                            min: 0,
                            title:
                            {
                                text: 'Energy (kWh)'
                            }
                        },
                        legend: {
                            enabled: false
                        },
                        tooltip: {
                            shared: true,
                            useHTML: true
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.2,
                                borderWidth: 0
                            }
                        },
                        series: [{
                                name: 'Energy',
                                data: [21638,39148,48340,14010,0,0,0,0,0,0,0,0]
                            }]
                        });
                    });$(function () {
                    Highcharts.chart('chartYearly', {
                        chart:
                        {
                            type: 'column'
                        },
                        title:
                        {
                            text: 'Energy Total'
                        },
                        xAxis:
                        {
                            categories: [2017,2018,2019,2020,2021,2022,2023,2024,2025,2026,2027,2028],
                            crosshair: true
                        },
                        yAxis:
                        {
                            min: 0,
                            title:
                            {
                                text: 'Energy (kWh)'
                            }
                        },
                        legend: {
                            enabled: false
                        },
                        tooltip: {
                            shared: true,
                            useHTML: true
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.2,
                                borderWidth: 0
                            }
                        },
                        series: [{
                                name: 'Energy',
                                data: [123137,0,0,0,0,0,0,0,0,0,0,0]
                            }]
                        });
                    });</script>

<body>
<! Layout Pages ////////////////////////////////////////////////////////////////////////////////////////////////////////> 
  <div class="py-5 w-100 h-25">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-2" style="height: 60px;">
         <a href="#" style="position: absolute; bottom: 0px; left: 10px;"><img src="Images/WHA.png" height="50" /></a>
            </div>
        <div class="col-md-8 style="height: 80px;">
          <ul class="nav nav-pills" style="position: absolute; top: 10px;">
            <li id="navOverview" role="presentation"><a href="defalt.php">Overview</a></li>
            <li id="navInverter" role="presentation"><a href="contain2.php">Power Meter</a></li>
            <li id="navAllInverter" role="presentation"><a href="contain3.php">All Inverter</a></li>
            <li id="navPVString" role="presentation"><a href="contain4.php">PV String</a></li>
            <li id="navEnergy" role="presentation class="active"><a href="contain5.php">Energy</a></li>
            <li id="navDatalog" role="presentation"><a href="contain6.php">Datalog</a></li>
            <li id="navReport" role="presentation"><a href="contain7.php">Report</a></li>
            <li style="top:8px; left:20px;"></li>
          </ul>
        </div>
        <div class="col-md-2" style="height: 40px; text-align: right: 0px;">
                <span style="position: absolute; bottom: 0px; right: 10px;"><input id="ChkAutoSlide" type="checkbox" name="ctl00$ChkAutoSlide" onClick="javascript:setTimeout(&#39;__doPostBack(\&#39;ctl00$ChkAutoSlide\&#39;,\&#39;\&#39;)&#39;, 0)" /><label for="ChkAutoSlide">Auto Slide</label></span>
            </div>
      </div>
    </div>
  </div>
<!Contain Defalt Pages /////////////////////////////////////////////////////////////////////////////////////////////////////>
 <div class="container-fluid">
    <div class="row">
        <div id="chartDaily" class="col-md-12" style="min-width: 1000px; height: 270px; margin: 0px; auto"></div>
    </div>
    <div class="row">
        <div id="chartMonthly" class="col-md-6" style="height: 270px;"></div>
        <div id="chartYearly" class="col-md-6" style="height: 270px;"></div>
    </div>
<! Foot Layout ///////////////////////////////////////////////////////////////////////////////////////////////////////////>
	   <footer>
                <div class="form-group has-feedback" style="display:normal">
                    <label class="control-label col-md-2" for="tbxWarning">2017-04-12 20:04</label>
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-addon">Alert</span>
                            <input name="ctl00$tbxWarning" type="text" id="tbxWarning" class="form-control" style="mar" />
                            <span class="glyphicon glyphicon-warning-sign form-control-feedback" aria-hidden="true"></span>
                        </div>
                    </div>
                </div>
            </footer>         
        
</div>
</body>
</html>