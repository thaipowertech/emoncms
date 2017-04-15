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
            <li id="navOverview" role="presentation"><a href="defalt.php">Overview</a></li>
            <li id="navInverter" role="presentation"><a href="contain2.php">Power Meter</a></li>
            <li id="navAllInverter" role="presentation"><a href="contain3.php">All Inverter</a></li>
            <li id="navPVString" role="presentation"><a href="contain4.php">PV String</a></li>
            <li id="navEnergy" role="presentation"><a href="contain5.php">Energy</a></li>
            <li id="navDatalog" role="presentation"><a href="contain6.php">Datalog</a></li>
            <li id="navReport" role="presentation class="active"><a href="contain7.php">Report</a></li>
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