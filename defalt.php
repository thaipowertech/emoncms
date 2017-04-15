<!DOCTYPE PHP>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

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
            <li id="navOverview" role="presentation" class="active"><a href="defalt.php">Overview</a></li>
            <li id="navInverter" role="presentation"><a href="contain2.php">Power Meter</a></li>
            <li id="navAllInverter" role="presentation"><a href="contain3.php">All Inverter</a></li>
            <li id="navPVString" role="presentation"><a href="contain4.php">PV String</a></li>
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
  <div class="py-5 h-75">
    <div class="container-fluid">
      <div class="row">
                <div class="col-md-2">
                    <img src="Images/pv1.jpg" height="200" style="margin:25px auto;"/>
                </div>
                <div class="col-md-2" style="top:70px; text-align:center;">
                    <div class="panel panel-primary">
                        <div class="panel-heading">PV POWER (kW)</div>
                        <div class="panel-body">
                            <span id="MainContent_lbPvPower">0.00</span>
                        </div>
                    </div>
                </div>
        		<div class="col-md-2">
                    <img src="Images/cl1.png" height="200" style="margin:25px auto; display:block;"/>
                </div>
                <div class="col-md-2" style="top:28px; text-align:center;">
                    <div class="panel panel-primary" style="margin-bottom:0px;">
                        <div class="panel-heading">SI LOAD (kW)</div>
                        <div class="panel-body">
                            
                            <span id="MainContent_lbAcPowerSI">18.90</span>
                        </div>
                    </div>
                    <div class="panel panel-primary" style="margin-bottom:0px;">
                        <div class="panel-heading">SOLAR POWER (kW)</div>
                        <div class="panel-body">
                            <span id="MainContent_lbAcPower">0.00</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <img src="Images/WHA02.jpg" height="150" style="margin:25px auto; top:20px right:10px;" />       
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="panel panel-primary" style="height:260px;">
                        <div class="panel-heading">Environment</div>
                        <div class="panel-body" style="padding:0px;">
                            <img src="Images/cloud.png" style="height:80px; margin:10px auto; display:block;" />
                            <div class="form-horizontal">
                                <div class="form-group" style="height:12px; margin:2px auto; display:block;" />
                                    <label class="col-md-6 control-label">Irradiation</label>
                                    <label class="col-md-6 control-label" style="text-align:left"><span id="MainContent_lbIrradiation">0</span> W/m<sup>2</sup></label>
                                </div>
                                <div class="form-group" style="height:12px; margin:2px auto; display:block;" />
                                    <label class="col-md-6 control-label">Insolation</label>
                                    <label class="col-md-6 control-label" style="text-align:left"><span id="MainContent_lbInsolation">7.25</span> kWh/m<sup>2</sup></label>
                                </div>
                                <div class="form-group" style="height:12px; margin:2px auto; display:block;" />
                                    <label class="col-md-6 control-label">Ambient Temp</label>
                                    <label class="col-md-6 control-label" style="text-align:left"><span id="MainContent_lbAmbientTemp">29.31</span> &deg;C</label>
                                </div>
                                <div class="form-group" style="height:12px; margin:2px auto; display:block;" />
                                    <label class="col-md-6 control-label">PV Temp</label>
                                    <label class="col-md-6 control-label" style="text-align:left"><span id="MainContent_lbPvTemp">27.93</span> &deg;C</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" style="border: 5px solid #337ab7; height:260px;">
                    <div class="row">
                        <div class="col-md-12" style="text-align:center; margin-bottom:20px;">
                            <span style="position:absolute; right:10px; top:5px;">COD: 16/01/2560</span>
                            <h2>SAVING</h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <img src="Images/save.png" style="width:100%;" />
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Save E-Today</label>
                                <label class="form-control"><span id="MainContent_lbSaveEnergyToday">73.64</span> %</label>
                            </div>
                            <div class="form-group">
                                <label>Save E-Total</label>
                                <label class="form-control"><span id="MainContent_lbSaveEnergyTotal">56.65</span> %</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <img src="Images/carbon.png" style="width:100%;" />
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>CO<sub>2</sub>Reduce Today</label>
                                <label class="form-control"><span id="MainContent_lbSaveCo2Today">663.60</span> kg</label>
                            </div>
                            <div class="form-group">
                                <label>CO<sub>2</sub>Reduce Total</label>
                                <label class="form-control"><span id="MainContent_lbSaveCo2Total">73.85</span> Ton</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="panel panel-primary" style="height:260px;">
                        <div class="panel-heading">Energy Production</div>

                        <div class="panel-body" style="padding:0px;">
                            <img src="Images/light.png" style="height:80px; margin:10px auto; display:block;" />
                            <div class="form-horizontal">
                                <div class="form-group" style="height:12px; margin:2px auto; display:block;" />
                                    <label class="col-md-6 control-label">Energy Today</label>
                                    <label class="col-md-6 control-label" style="text-align:left"><span id="MainContent_lbEnergyToday">1106</span> kWh</label>
                                </div>
                                <div class="form-group" style="height:12px; margin:2px auto; display:block;" />
                                    <label class="col-md-6 control-label">Energy Total</label>
                                    <label class="col-md-6 control-label" style="text-align:left"><span id="MainContent_lbEnergyTotal">123.08</span> MWh</label>
                                </div>
                                <div class="form-group" style="height:12px; margin:2px auto; display:block;" />
                                    <label class="col-md-6 control-label">Plant PR</label>
                                    <label class="col-md-6 control-label" style="text-align:left"><span id="MainContent_lbPlantPR">0.33</span></label>
                                </div>
                                <div class="form-group" style="height:12px; margin:2px auto; display:block;" />
                                    <label class="col-md-6 control-label">Plant Yield</label>
                                    <label class="col-md-6 control-label" style="text-align:left"><span id="MainContent_lbPlantYeild">2.37</span></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
