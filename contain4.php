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
                    Highcharts.chart('container', {
                        chart:
                        {
                            type: 'column'
                        },
                        title:
                        {
                            text: 'PV String (1 String = 315 Wp x 20 Modules)'
                        },
                        subtitle:
                        {
                            text: 'MPPT1 = 3 String & MPPT2 = 2 String (INV11 - MPPT1 = 2 String & MPPT2 = 2 String)'
                        },
                        xAxis:
                        {
                            categories: [
                                'INV1',
                                'INV2',
                                'INV3',
                                'INV4',
                                'INV5',
                                'INV6',
                                'INV7',
                                'INV8',
                                'INV9',
                                'INV10',
                                'INV11',
                                'INV12',
                                'INV13',
                                'INV14',
                                'INV15'
                            ],
                            crosshair: true
                        },
                        yAxis:
                        {
                            min: 0,
                            title:
                            {
                                text: 'Power (kW)'
                            }
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
                                name: 'MPPT1',
                                data: [0.3, 0.2, 0.1, 0.9, 0.9, 0.9, 0.9, 0.6, 0.4, 0, 0.1, 0.3, 0.9, 0.3, 0.1]
                            }, {
                                name: 'MPPT2',
                                data: [0.4, 0.9, 0.8, 0.2, 0.2, 0.1, 0, 0.4, 0.8, 0.9, 0.8, 0.5, 0.1, 0.6, 0.7]
                            }]
                        });
                    });
</script>
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
            <li id="navPVString" role="presentation class="active"><a href="contain4.php">PV String</a></li>
            <li id="navEnergy" role="presentation"><a href="contain5.php">Energy</a></li>
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
<div class="row" style="height: 60px;">
	<div class="col-md-6" style="top: 20px; left: 20px;">  
  		<span class="radio-inline"><input id="MainContent_rbtnPower" type="radio" name="ctl00$MainContent$PV" value="rbtnPower" checked="checked" /><label for="MainContent_rbtnPower">Power</label></span>
  		<span class="radio-inline"><input id="MainContent_rbtnVoltage" type="radio" name="ctl00$MainContent$PV" value="rbtnVoltage" onclick="javascript:setTimeout(&#39;__doPostBack(\&#39;ctl00$MainContent$rbtnVoltage\&#39;,\&#39;\&#39;)&#39;, 0)" /><label for="MainContent_rbtnVoltage">Voltage</label></span>
  		<span class="radio-inline"><input id="MainContent_rbtnCurrent" type="radio" name="ctl00$MainContent$PV" value="rbtnCurrent" onclick="javascript:setTimeout(&#39;__doPostBack(\&#39;ctl00$MainContent$rbtnCurrent\&#39;,\&#39;\&#39;)&#39;, 0)" /><label for="MainContent_rbtnCurrent">Current</label></span>
     </div>
</div>
<div id="container" style="min-width: 1000px; height: 450px; margin: 10px; auto"></div> <!Show of Chart>
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