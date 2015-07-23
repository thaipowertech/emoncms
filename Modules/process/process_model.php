<?php
/*
 All Emoncms code is released under the GNU Affero General Public License.
 See COPYRIGHT.txt and LICENSE.txt.

 ---------------------------------------------------------------------
 Emoncms - open source energy visualisation
 Part of the OpenEnergyMonitor project:
 http://openenergymonitor.org
 */

// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

class Process
{
    private $mysqli;
    private $input;
    private $feed;
    private $schedule;
    private $log;
    private $mqtt = false;
    
    private $proc_initialvalue;  // save the input value at beginning of the processes list execution
    private $proc_skip_next;     // skip execution of next process in process list
    private $proc_goto;          // goto step in process list
    
    private $timezone = 'UTC';

    public function __construct($mysqli,$input,$feed,$timezone)
    {
        $this->mysqli = $mysqli;
        $this->input = $input;
        $this->feed = $feed;
        $this->log = new EmonLogger(__FILE__);
        if (!($timezone === NULL)) $this->timezone = $timezone;
     
        if (@include_once "Modules/schedule/schedule_model.php") { // false if not found
            $this->schedule = new Schedule($mysqli, $this->timezone);
        }
        
        // Load MQTT if enabled
        // Publish value to MQTT topic, see: http://openenergymonitor.org/emon/node/5943
        global $mqtt_enabled, $mqtt_server, $mqtt;
        if (isset($mqtt_enabled) && $mqtt_enabled == true & $mqtt == false)
        {
            require("Lib/phpMQTT.php");
            $mqtt = new phpMQTT($mqtt_server, 1883, "Emoncms Publisher");
            $this->mqtt = $mqtt;
        }
    }

    public function get_process_list()
    {
        
        $list = array();
        
        // Note on engine selection
        
        // The engines listed against each process must be the supported engines for each process - and are only used in the input and node config GUI dropdown selectors
        // By using the create feed api and input set processlist its possible to create any feed type with any process list combination.
        // Only feeds capable of using a particular processor are displayed to the user and can be selected from the gui.
        // Daily datatype automaticaly adjust feed interval to 1d and user cant change it from gui.
        // If there is only one engine available for a processor, it is selected and user cant change it from gui.
        // The default selected engine is the first in the array of the supported engines for each processor.
        // Virtual feeds are feeds that are calculed in realtime when queried and use a processlist as post processor. 
        // Processors that write or update a feed are not supported and hidden from the gui on the context of virtual feeds.

        // description | Arg type | function | No. of datafields if creating feed | Datatype | Group | Engines

        $list[1] = array(_("Log to feed"),ProcessArg::FEEDID,"log_to_feed",1,DataType::REALTIME,"Main",array(Engine::PHPFIWA,Engine::PHPFINA,Engine::PHPTIMESERIES,Engine::MYSQL,Engine::MYSQLMEMORY));
        $list[2] = array(_("x"),ProcessArg::VALUE,"scale",0,DataType::UNDEFINED,"Calibration");                           
        $list[3] = array(_("+"),ProcessArg::VALUE,"offset",0,DataType::UNDEFINED,"Calibration");                          
        $list[4] = array(_("Power to kWh"),ProcessArg::FEEDID,"power_to_kwh",1,DataType::REALTIME,"Power",array(Engine::PHPFINA,Engine::PHPTIMESERIES,Engine::MYSQL,Engine::MYSQLMEMORY));               
        $list[5] = array(_("Power to kWh/d"),ProcessArg::FEEDID,"power_to_kwhd",1,DataType::DAILY,"Power",array(Engine::PHPTIMESERIES,Engine::MYSQL,Engine::MYSQLMEMORY));               
        $list[6] = array(_("x input"),ProcessArg::INPUTID,"times_input",0,DataType::UNDEFINED,"Input");                   
        $list[7] = array(_("Input on-time"),ProcessArg::FEEDID,"input_ontime",1,DataType::DAILY,"Input",array(Engine::PHPTIMESERIES,Engine::MYSQL,Engine::MYSQLMEMORY));                 
        $list[8] = array(_("Wh increments to kWh/d"),ProcessArg::FEEDID,"kwhinc_to_kwhd",1,DataType::DAILY,"Power",array(Engine::PHPTIMESERIES,Engine::MYSQL,Engine::MYSQLMEMORY));      
        $list[9] = array(_("kWh to kWh/d (OLD)"),ProcessArg::FEEDID,"kwh_to_kwhd_old",1,DataType::DAILY,"Deleted",array(Engine::PHPTIMESERIES));       // need to remove
        $list[10] = array(_("Upsert feed at day"),ProcessArg::FEEDID,"update_feed_data",1,DataType::DAILY,"Input",array(Engine::MYSQL,Engine::MYSQLMEMORY));           
        $list[11] = array(_("+ input"),ProcessArg::INPUTID,"add_input",0,DataType::UNDEFINED,"Input");                    
        $list[12] = array(_("/ input"),ProcessArg::INPUTID,"divide_input",0,DataType::UNDEFINED,"Input");                 
        $list[13] = array(_("Phaseshift"),ProcessArg::VALUE,"phaseshift",0,DataType::UNDEFINED,"Deleted");                             // need to remove
        $list[14] = array(_("Accumulator"),ProcessArg::FEEDID,"accumulator",1,DataType::REALTIME,"Misc",array(Engine::PHPFINA,Engine::PHPTIMESERIES,Engine::MYSQL,Engine::MYSQLMEMORY));                 
        $list[15] = array(_("Rate of change (REDIS)"),ProcessArg::FEEDID,"ratechange",1,DataType::REALTIME,"Misc",array(Engine::PHPFIWA,Engine::PHPFINA,Engine::PHPTIMESERIES));               
        $list[16] = array(_("Histogram"),ProcessArg::FEEDID,"histogram",2,DataType::HISTOGRAM,"Power",array(Engine::MYSQL,Engine::MYSQLMEMORY));                   
        $list[17] = array(_("Daily Average"),ProcessArg::FEEDID,"average",2,DataType::HISTOGRAM,"Deleted",array(Engine::PHPTIMESERIES));               // need to remove
        
        // to be reintroduced in post-processing
        $list[18] = array(_("Heat flux"),ProcessArg::FEEDID,"heat_flux",1,DataType::REALTIME,"Deleted",array(Engine::PHPFIWA,Engine::PHPFINA,Engine::PHPTIMESERIES));                  
        
        // need to remove - result can be achieved with allow_positive & power_to_kwhd
        $list[19] = array(_("Power gained to kWh/d"),ProcessArg::FEEDID,"power_acc_to_kwhd",1,DataType::DAILY,"Deleted",array(Engine::PHPTIMESERIES));              
        
        // - look into implementation that doesnt need to store the ref feed
        $list[20] = array(_("Total pulse count to pulse increment"),ProcessArg::FEEDID,"pulse_diff",1,DataType::REALTIME,"Pulse",array(Engine::PHPFINA,Engine::PHPTIMESERIES));
        
        // fixed works now with redis - look into state implementation without feed
        $list[21] = array(_("kWh to Power (REDIS)"),ProcessArg::FEEDID,"kwh_to_power",1,DataType::REALTIME,"Power",array(Engine::PHPFIWA,Engine::PHPFINA,Engine::PHPTIMESERIES));
        
        $list[22] = array(_("- input"),ProcessArg::INPUTID,"subtract_input",0,DataType::UNDEFINED,"Input");               
        $list[23] = array(_("kWh to kWh/d"),ProcessArg::FEEDID,"kwh_to_kwhd",2,DataType::DAILY,"Power",array(Engine::PHPTIMESERIES));                  // fixed works now with redis
        $list[24] = array(_("Allow positive"),ProcessArg::NONE,"allowpositive",0,DataType::UNDEFINED,"Limits");           
        $list[25] = array(_("Allow negative"),ProcessArg::NONE,"allownegative",0,DataType::UNDEFINED,"Limits");           
        $list[26] = array(_("Signed to unsigned"),ProcessArg::NONE,"signed2unsigned",0,DataType::UNDEFINED,"Misc");       
        $list[27] = array(_("Max daily value"),ProcessArg::FEEDID,"max_value",1,DataType::DAILY,"Misc",array(Engine::PHPTIMESERIES,Engine::MYSQL,Engine::MYSQLMEMORY));                        
        $list[28] = array(_("Min daily value"),ProcessArg::FEEDID,"min_value",1,DataType::DAILY,"Misc",array(Engine::PHPTIMESERIES,Engine::MYSQL,Engine::MYSQLMEMORY));  
                              
        $list[29] = array(_(" + feed"),ProcessArg::FEEDID,"add_feed",0,DataType::UNDEFINED,"Feed");        // Klaus 24.2.2014
        $list[30] = array(_(" - feed"),ProcessArg::FEEDID,"sub_feed",0,DataType::UNDEFINED,"Feed");        // Klaus 24.2.
        $list[31] = array(_(" * feed"),ProcessArg::FEEDID,"multiply_by_feed",0,DataType::UNDEFINED,"Feed");
        $list[32] = array(_(" / feed"),ProcessArg::FEEDID,"divide_by_feed",0,DataType::UNDEFINED,"Feed");
        $list[33] = array(_("Reset to ZERO"),ProcessArg::NONE,"reset2zero",0,DataType::UNDEFINED,"Misc");
        
        $list[34] = array(_("Wh Accumulator"),ProcessArg::FEEDID,"wh_accumulator",1,DataType::REALTIME,"Main",array(Engine::PHPFINA,Engine::PHPTIMESERIES));
        
        $list[35] = array(_("Publish to MQTT"),ProcessArg::TEXT,"publish_to_mqtt",1,DataType::UNDEFINED,"Main");     

        $list[36] = array(_("Reset to NULL"),ProcessArg::NONE,"reset2null",0,DataType::UNDEFINED,"Misc"); 
        $list[37] = array(_("Reset to Original"),ProcessArg::NONE,"reset2original",0,DataType::UNDEFINED,"Misc"); 

    if (is_object($this->schedule)) {
        $list[38] = array(_("If !schedule, ZERO"),ProcessArg::SCHEDULEID,"if_not_schedule_zero",0,DataType::UNDEFINED,"Schedule");
        $list[39] = array(_("If !schedule, NULL"),ProcessArg::SCHEDULEID,"if_not_schedule_null",0,DataType::UNDEFINED,"Schedule");
        $list[40] = array(_("If schedule, ZERO"),ProcessArg::SCHEDULEID,"if_schedule_zero",0,DataType::UNDEFINED,"Schedule");
        $list[41] = array(_("If schedule, NULL"),ProcessArg::SCHEDULEID,"if_schedule_null",0,DataType::UNDEFINED,"Schedule");
    }        
        $list[42] = array(_("If ZERO, skip next"),ProcessArg::NONE,"if_zero_skip",0,DataType::UNDEFINED,"Conditional");
        $list[43] = array(_("If !ZERO, skip next"),ProcessArg::NONE,"if_not_zero_skip",0,DataType::UNDEFINED,"Conditional");
        $list[44] = array(_("If NULL, skip next"),ProcessArg::NONE,"if_null_skip",0,DataType::UNDEFINED,"Conditional");
        $list[45] = array(_("If !NULL, skip next"),ProcessArg::NONE,"if_not_null_skip",0,DataType::UNDEFINED,"Conditional");
        
        $list[46] = array(_("If >, skip next"),ProcessArg::VALUE,"if_gt_skip",0,DataType::UNDEFINED,"Conditional - User value");
        $list[47] = array(_("If >=, skip next"),ProcessArg::VALUE,"if_gt_equal_skip",0,DataType::UNDEFINED,"Conditional - User value");
        $list[48] = array(_("If <, skip next"),ProcessArg::VALUE,"if_lt_skip",0,DataType::UNDEFINED,"Conditional - User value");
        $list[49] = array(_("If <=, skip next"),ProcessArg::VALUE,"if_lt_equal_skip",0,DataType::UNDEFINED,"Conditional - User value");
        $list[50] = array(_("If =, skip next"),ProcessArg::VALUE,"if_equal_skip",0,DataType::UNDEFINED,"Conditional - User value");
        $list[51] = array(_("If !=, skip next"),ProcessArg::VALUE,"if_not_equal_skip",0,DataType::UNDEFINED,"Conditional - User value");
        
        // A bit or warning: if user goto's in loop, the php will lock until the server defined timesout with an error
        $list[52] = array(_("GOTO"),ProcessArg::VALUE,"goto_process",0,DataType::UNDEFINED,"Misc");
        
        // $list[29] = array(_("save to input"),ProcessArg::INPUTID,"save_to_input",1,DataType::UNDEFINED);

        //Virtual Feed specific processors (WARNING: all virtual feed specific processors must be on the "Virtual" group because there is logic in the UI to hide it on input context)
        $list[53] = array(_("Source Feed"),ProcessArg::FEEDID,"source_feed_data_time",1,DataType::REALTIME,"Virtual");
        //$list[54] = array(_("Source Daily (TBD)"),ProcessArg::FEEDID,"get_feed_data_day",1,DataType::DAILY,"Virtual");        

        return $list;
    }

    public function input($time, $value, $processList, $options = null)
    {
        $this->log->info("input() received time=$time\tvalue=$value");

        $this->proc_initialvalue = $value; // save the input value at beginning of the processes list execution
        $this->proc_skip_next = false;     // skip execution of next process in process list
    
        $process_list = $this->get_process_list();
        $pairs = explode(",",$processList);
        $total = count($pairs);
        for($this->proc_goto = 0 ; $this->proc_goto < $total ; $this->proc_goto++) {
            $inputprocess = explode(":", $pairs[$this->proc_goto]);  // Divide into process id and arg
            $processid = (int) $inputprocess[0];                     // Process id
            
            $arg = 0;
            if (isset($inputprocess[1]))
                $arg = $inputprocess[1];               // Can be value or feed id

            if (!isset($process_list[$processid])) throw new Exception("ABORTED: Processor '".$processid."' does not exists. Module missing?");
            $process_public = $process_list[$processid][2];          // get process public function name

            $value = $this->$process_public($arg,$time,$value,$options);      // execute process public function
            
            if ($this->proc_skip_next) {
                $this->proc_skip_next = false; $this->proc_goto++;
            }
        }
        return $value;
    }

    public function get_process($id)
    {
        $list = $this->get_process_list();
        if ($id>0 && $id<count($list)+1) return $list[$id];
    }

    public function scale($arg, $time, $value)
    {
        return $value * $arg;
    }

    public function divide($arg, $time, $value)
    {
        return $value / $arg;
    }

    public function offset($arg, $time, $value)
    {
        return $value + $arg;
    }

    public function allowpositive($arg, $time, $value)
    {
        if ($value<0) $value = 0;
        return $value;
    }

    public function allownegative($arg, $time, $value)
    {
        if ($value>0) $value = 0;
        return $value;
    }

    public function reset2zero($arg, $time, $value)
    {
         $value = 0;
         return $value;
    }

    public function reset2original($arg, $time, $value)
    {
         return $this->proc_initialvalue;
    }

    public function reset2null($arg, $time, $value)
    {
         return null;
    }

    public function signed2unsigned($arg, $time, $value)
    {
        if($value < 0) $value = $value + 65536;
        return $value;
    }

    public function log_to_feed($id, $time, $value)
    {
        $this->feed->insert_data($id, $time, $time, $value);

        return $value;
    }

    //---------------------------------------------------------------------------------------
    // Times value by current value of another input
    //---------------------------------------------------------------------------------------
    public function times_input($id, $time, $value)
    {
        return $value * $this->input->get_last_value($id);
    }

    public function divide_input($id, $time, $value)
    {
        $lastval = $this->input->get_last_value($id);
        if($lastval > 0){
            return $value / $lastval;
        } else {
            return null; // should this be null for a divide by zero?
        }
    }
    
    public function update_feed_data($id, $time, $value)
    {
        $time = $this->getstartday($time);

        $feedname = "feed_".trim($id)."";
        $result = $this->mysqli->query("SELECT time FROM $feedname WHERE `time` = '$time'");
        $row = $result->fetch_array();

        if (!$row)
        {
            $this->mysqli->query("INSERT INTO $feedname (time,data) VALUES ('$time','$value')");
        }
        else
        {
            $this->mysqli->query("UPDATE $feedname SET data = '$value' WHERE `time` = '$time'");
        }
        return $value;
    } 

    public function add_input($id, $time, $value)
    {
        return $value + $this->input->get_last_value($id);
    }

    public function subtract_input($id, $time, $value)
    {
        return $value - $this->input->get_last_value($id);
    }

    //---------------------------------------------------------------------------------------
    // Power to kwh
    //---------------------------------------------------------------------------------------
    public function power_to_kwh($feedid, $time_now, $value)
    {
        $new_kwh = 0;

        // Get last value
        $last = $this->feed->get_timevalue($feedid);

        if (!isset($last['value'])) $last['value'] = 0;
        $last_kwh = $last['value']*1;
        $last_time = $last['time']*1;

        // only update if last datapoint was less than 2 hour old
        // this is to reduce the effect of monitor down time on creating
        // often large kwh readings.
        if ($last_time && (time()-$last_time)<7200)
        {
            // kWh calculation
            $time_elapsed = ($time_now - $last_time);
            $kwh_inc = ($time_elapsed * $value) / 3600000.0;
            $new_kwh = $last_kwh + $kwh_inc;
        } else {
            // in the event that redis is flushed the last time will
            // likely be > 7200s ago and so kwh inc is not calculated
            // rather than enter 0 we enter the last value
            $new_kwh = $last_kwh;
        }

        $padding_mode = "join";
        $this->feed->insert_data($feedid, $time_now, $time_now, $new_kwh, $padding_mode);
        
        return $value;
    }

    public function power_to_kwhd($feedid, $time_now, $value)
    {
        $new_kwh = 0;

        // Get last value
        $last = $this->feed->get_timevalue($feedid);

        if (!isset($last['value'])) $last['value'] = 0;
        if (!isset($last['time'])) $last['time'] = $time_now;
        $last_kwh = $last['value']*1;
        $last_time = $last['time']*1;

        $current_slot = $this->getstartday($time_now);
        $last_slot = $this->getstartday($last_time);    

        $time_elapsed = ($time_now - $last_time);   
        if ($time_elapsed>0 && $time_elapsed<7200) { // 2hrs
            // kWh calculation
            $kwh_inc = ($time_elapsed * $value) / 3600000.0;
        } else {
            // in the event that redis is flushed the last time will
            // likely be > 7200s ago and so kwh inc is not calculated
            // rather than enter 0 we dont increase it
            $kwh_inc = 0;
        }

        if($last_slot == $current_slot) {
            $new_kwh = $last_kwh + $kwh_inc;
        } else {
            # We are working in a new slot (new day) so don't increment it with the data from yesterday
            $new_kwh = $kwh_inc;
        }

        $this->feed->update_data($feedid, $time_now, $current_slot, $new_kwh);

        return $value;
    }


    public function kwh_to_kwhd($feedid, $time_now, $value)
    {
        global $redis;
        if (!$redis) return $value; // return if redis is not available
        
        $currentkwhd = $this->feed->get_timevalue($feedid);
        $last_time = $currentkwhd['time'];
        
        //$current_slot = floor($time_now / 86400) * 86400;
        //$last_slot = floor($last_time / 86400) * 86400;
        $current_slot = $this->getstartday($time_now);
        $last_slot = $this->getstartday($last_time);

        if ($redis->exists("process:kwhtokwhd:$feedid")) {
            $lastkwhvalue = $redis->hmget("process:kwhtokwhd:$feedid",array('time','value'));
            $kwhinc = $value - $lastkwhvalue['value'];

            // kwh values should always be increasing so ignore ones that are less
            // assume they are errors
            if ($kwhinc<0) { $kwhinc = 0; $value = $lastkwhvalue['value']; }
            
            if($last_slot == $current_slot) {
                $new_kwh = $currentkwhd['value'] + $kwhinc;
            } else {
                $new_kwh = $kwhinc;
            }

            $this->feed->update_data($feedid, $time_now, $current_slot, $new_kwh);
        }
        
        $redis->hMset("process:kwhtokwhd:$feedid", array('time' => $time_now, 'value' => $value));

        return $value;
    }

    //---------------------------------------------------------------------------------------
    // input on-time counter
    //---------------------------------------------------------------------------------------
    public function input_ontime($feedid, $time_now, $value)
    {
        // Get last value
        $last = $this->feed->get_timevalue($feedid);
        $last_time = $last['time'];
        
        //$current_slot = floor($time_now / 86400) * 86400;
        //$last_slot = floor($last_time / 86400) * 86400;
        $current_slot = $this->getstartday($time_now);
        $last_slot = $this->getstartday($last_time);
        
        if (!isset($last['value'])) $last['value'] = 0;
        $ontime = $last['value'];
        $time_elapsed = 0;
        
        if ($value > 0 && (($time_now-$last_time)<7200))
        {
            $time_elapsed = $time_now - $last_time;
            $ontime += $time_elapsed;
        }
        
        if($last_slot != $current_slot) $ontime = $time_elapsed;

        $this->feed->update_data($feedid, $time_now, $current_slot, $ontime);

        return $value;
    }

    //--------------------------------------------------------------------------------
    // Display the rate of change for the current and last entry
    //--------------------------------------------------------------------------------
    public function ratechange($feedid, $time, $value)
    {
        global $redis;
        if (!$redis) return $value; // return if redis is not available
        
        if ($redis->exists("process:ratechange:$feedid")) {
            $lastvalue = $redis->hmget("process:ratechange:$feedid",array('time','value'));
            $ratechange = $value - $lastvalue['value'];
            $this->feed->insert_data($feedid, $time, $time, $ratechange);
        }
        $redis->hMset("process:ratechange:$feedid", array('time' => $time, 'value' => $value));

        // return $ratechange;
    }

    public function save_to_input($inputid, $time, $value)
    {
        $this->input->set_timevalue($inputid, $time, $value);
        return $value;
    }

    public function kwhinc_to_kwhd($feedid, $time_now, $value)
    {
        $last = $this->feed->get_timevalue($feedid);
        $last_time = $last['time'];
        
        //$current_slot = floor($time_now / 86400) * 86400;
        //$last_slot = floor($last_time / 86400) * 86400;
        $current_slot = $this->getstartday($time_now);
        $last_slot = $this->getstartday($last_time);
               
        $new_kwh = $last['value'] + ($value / 1000.0);
        if ($last_slot != $current_slot) $new_kwh = ($value / 1000.0);
        
        $this->feed->update_data($feedid, $time_now, $current_slot, $new_kwh);

        return $value;
    }

    public function accumulator($feedid, $time, $value)
    {
        $last = $this->feed->get_timevalue($feedid);
        $value = $last['value'] + $value;
        $padding_mode = "join";
        $this->feed->insert_data($feedid, $time, $time, $value, $padding_mode);
        return $value;
    }
    /*
    public function accumulator_daily($feedid, $time_now, $value)
    {
        $last = $this->feed->get_timevalue($feedid);
        $value = $last['value'] + $value;
        $feedtime = $this->getstartday($time_now);
        $this->feed->update_data($feedid, $time_now, $feedtime, $value);
        return $value;
    }*/

    //---------------------------------------------------------------------------------
    // This method converts power to energy vs power (Histogram)
    //---------------------------------------------------------------------------------
    public function histogram($feedid, $time_now, $value)
    {

        ///return $value;

        $feedname = "feed_" . trim($feedid) . "";
        $new_kwh = 0;
        // Allocate power values into pots of varying sizes
        if ($value < 500) {
            $pot = 50;

        } elseif ($value < 2000) {
            $pot = 100;

        } else {
            $pot = 500;
        }

        $new_value = round($value / $pot, 0, PHP_ROUND_HALF_UP) * $pot;

        $time = $this->getstartday($time_now);

        // Get the last time
        $lastvalue = $this->feed->get_timevalue($feedid);
        $last_time = $lastvalue['time'];

        // kWh calculation
        if ((time()-$last_time)<7200) {
            $time_elapsed = ($time_now - $last_time);
            $kwh_inc = ($time_elapsed * $value) / 3600000;
        } else {
            $kwh_inc = 0;
        }

        // Get last value
        $result = $this->mysqli->query("SELECT * FROM $feedname WHERE time = '$time' AND data2 = '$new_value'");

        if (!$result) return $value;

        $last_row = $result->fetch_array();

        if (!$last_row)
        {
            $result = $this->mysqli->query("INSERT INTO $feedname (time,data,data2) VALUES ('$time','0.0','$new_value')");

            $this->feed->set_timevalue($feedid, $new_value, $time_now);
            $new_kwh = $kwh_inc;
        }
        else
        {
            $last_kwh = $last_row['data'];
            $new_kwh = $last_kwh + $kwh_inc;
        }

        // update kwhd feed
        $this->mysqli->query("UPDATE $feedname SET data = '$new_kwh' WHERE time = '$time' AND data2 = '$new_value'");

        $this->feed->set_timevalue($feedid, $new_value, $time_now);
        return $value;
    }

    public function pulse_diff($feedid,$time_now,$value)
    {
        $value = $this->signed2unsigned(false,false, $value);

        if($value>0)
        {
            $pulse_diff = 0;
            $last = $this->feed->get_timevalue($feedid);
            if ($last['time']) {
                // Need to handle resets of the pulse value (and negative 2**15?)
                if ($value >= $last['value']) {
                    $pulse_diff = $value - $last['value'];
                } else {
                    $pulse_diff = $value;
                }
            }

            // Save to allow next difference calc.
            $this->feed->insert_data($feedid,$time_now,$time_now,$value);

            return $pulse_diff;
        }
    }

    public function kwh_to_power($feedid,$time,$value)
    {
        global $redis;
        if (!$redis) return $value; // return if redis is not available
        
        if ($redis->exists("process:kwhtopower:$feedid")) {
            $lastvalue = $redis->hmget("process:kwhtopower:$feedid",array('time','value'));
            $kwhinc = $value - $lastvalue['value'];
            $joules = $kwhinc * 3600000.0;
            $timeelapsed = ($time - $lastvalue['time']);
            $power = $joules / $timeelapsed;
            $this->feed->insert_data($feedid, $time, $time, $power);
        }
        $redis->hMset("process:kwhtopower:$feedid", array('time' => $time, 'value' => $value));

        return $power;
    }

    public function max_value($feedid, $time_now, $value)
    {
        // Get last values
        $last = $this->feed->get_timevalue($feedid);
        $last_val = $last['value'];
        $last_time = $last['time'];
        $feedtime = $this->getstartday($time_now);
        $time_check = $this->getstartday($last_time);

        // Runs on setup and midnight to reset current value - (otherwise db sets 0 as new max)
        if ($time_check != $feedtime) {
            $this->feed->insert_data($feedid, $time_now, $feedtime, $value);
        } else {
            if ($value > $last_val) $this->feed->update_data($feedid, $time_now, $feedtime, $value);
        }
        return $value;
    }

    public function min_value($feedid, $time_now, $value)
    {
        // Get last values
        $last = $this->feed->get_timevalue($feedid);
        $last_val = $last['value'];
        $last_time = $last['time'];
        $feedtime = $this->getstartday($time_now);
        $time_check = $this->getstartday($last_time);

        // Runs on setup and midnight to reset current value - (otherwise db sets 0 as new min)
        if ($time_check != $feedtime) {
            $this->feed->insert_data($feedid, $time_now, $feedtime, $value);
        } else {
            if ($value < $last_val) $this->feed->update_data($feedid, $time_now, $feedtime, $value);
        }
        return $value;

    }
    
    public function add_feed($feedid, $time, $value)
    {
        $last = $this->feed->get_timevalue($feedid);
        $value = $last['value'] + $value;
        return $value;
    }

    public function sub_feed($feedid, $time, $value)
    {
        $last  = $this->feed->get_timevalue($feedid);
        $myvar = $last['value'] *1;
        return $value - $myvar;
    }
    
    public function multiply_by_feed($feedid, $time, $value)
    {
        $last = $this->feed->get_timevalue($feedid);
        $value = $last['value'] * $value;
        return $value;
    }

   public function divide_by_feed($feedid, $time, $value)
    {
        $last  = $this->feed->get_timevalue($feedid);
        $myvar = $last['value'] *1;
        
        if ($myvar!=0) {
            return $value / $myvar;
        } else {
            return 0;
        }
    }
    
    public function wh_accumulator($feedid, $time, $value)
    {
        $max_power = 25000;
        $totalwh = $value;
        
        global $redis;
        if (!$redis) return $value; // return if redis is not available

        if ($redis->exists("process:whaccumulator:$feedid")) {
            $last_input = $redis->hmget("process:whaccumulator:$feedid",array('time','value'));
    
            $last_feed  = $this->feed->get_timevalue($feedid);
            $totalwh = $last_feed['value'];
            
            $time_diff = $time - $last_feed['time'];
            $val_diff = $value - $last_input['value'];
            
            $power = ($val_diff * 3600) / $time_diff;
            
            if ($val_diff>0 && $power<$max_power) $totalwh += $val_diff;
            
            $padding_mode = "join";
            $this->feed->insert_data($feedid, $time, $time, $totalwh, $padding_mode);
            
        }
        $redis->hMset("process:whaccumulator:$feedid", array('time' => $time, 'value' => $value));

        return $totalwh;
    }
    
    public function publish_to_mqtt($topic, $time, $value)
    {
        // Publish value to MQTT topic, see: http://openenergymonitor.org/emon/node/5943
        if ($this->mqtt && $this->mqtt->connect()) {
            $this->mqtt->publish($topic,$value,0);
            $this->mqtt->close();
        }
        
        return $value;
    }
    
    // Schedule
    public function if_not_schedule_zero($scheduleid, $time, $value) {
        $result = $this->schedule->match($scheduleid, $time);
        return ($result ? $value : 0);
    }
    public function if_not_schedule_null($scheduleid, $time, $value) {
        $result = $this->schedule->match($scheduleid, $time);
        return ($result ? $value : null);
    }
    public function if_schedule_zero($scheduleid, $time, $value) {
        $result = $this->schedule->match($scheduleid, $time);
        return ($result ? 0 : $value);
    }
    public function if_schedule_null($scheduleid, $time, $value) {
        $result = $this->schedule->match($scheduleid, $time);
        return ($result ? null : $value);
    }
    
    // Conditional process list flow
    public function if_zero_skip($noarg, $time, $value) {
        if ($value == 0)
            $this->proc_skip_next = true;
        return $value;
    }
    public function if_not_zero_skip($noarg, $time, $value) {
        if ($value != 0)
            $this->proc_skip_next = true;
        return $value;
    }
    public function if_null_skip($noarg, $time, $value) {
        if ($value === NULL)
            $this->proc_skip_next = true;
        return $value;
    }
    public function if_not_null_skip($noarg, $time, $value) {
        if (!($value === NULL))
            $this->proc_skip_next = true;
        return $value;
    }

    public function if_gt_skip($arg, $time, $value) {
        if ($value > $arg)
            $this->proc_skip_next = true;
        return $value;
    }
    public function if_gt_equal_skip($arg, $time, $value) {
        if ($value >= $arg)
            $this->proc_skip_next = true;
        return $value;
    }
    public function if_lt_skip($arg, $time, $value) {
        if ($value < $arg)
            $this->proc_skip_next = true;
        return $value;
    }
    public function if_lt_equal_skip($arg, $time, $value) {
        if ($value <= $arg)
            $this->proc_skip_next = true;
        return $value;
    }
    
    public function if_equal_skip($arg, $time, $value) {
        if ($value == $arg)
            $this->proc_skip_next = true;
        return $value;
    }
    public function if_not_equal_skip($arg, $time, $value) {
        if ($value != $arg)
            $this->proc_skip_next = true;
        return $value;
    }
    
    public function goto_process($proc_no, $time, $value){
        $this->proc_goto = $proc_no - 2;
        return $value;
    }


    // Used as Virtual feed source of data (read from other feeds). Gets feed data for the specified time range in $options variable, 
    // Set data_sampling to false in settings.php to allow precise average feed data calculation. It will be 10x slower!
    public function source_feed_data_time($feedid, $time, $value, $options)
    {
        global $data_sampling;
        $starttime = microtime(true);
        $value = null;
        if (isset($options['start']) && isset($options['end'])) {
            $start = $options['start']; // if option array has start and end time, use it
            $end = $options['end'];
            if (isset($options['interval'])) {
                $interval=$options['interval'];
            } else {
                $interval = ($end - $start);
            }
            if ($data_sampling) {
                // To speed up reading, but will miss some average data
                $meta=$this->feed->get_meta($feedid);
                if (isset($meta->interval) && (int)$meta->interval > 1) {
                    $interval = (int)$meta->interval; // set engine interval 
                    $end = $start; 
                    $start = $end - ($interval * 2); // search past x interval secs
                } else if ($interval > 5000) { //83m interval is high a table scan will happen in engine
                    $end = $start; 
                    $start = $end - 60; // force search past 1m 
                    $interval = 1;
                } else if ($interval > 300) { // 5m
                    $end = $start; 
                    $start = $end - 20; //  search past 20s
                    $interval = 1;
                } else if ($interval < 5) { // 5s
                    $end = $start; 
                    $start = $end - 10; //  search past 10s
                    $interval = 1;
                }
            }
        } else {
            $end = $time; 
            $start = $end - 10; // else search past 10 secs 
            $interval = 1;
        }
        
        //$this->log->info("source_feed_data_time() ". ($data_sampling ? "SAMPLING ":"") ."feedid=$feedid start=$start end=$end len=".(($end - $start))." int=$interval - BEFORE GETDATA");

        $start*=1000; // convert to miliseconds for engine
        $end*=1000;
        $data = $this->feed->get_data($feedid,$start,$end,$interval,1,1); // get data from feed engine with skipmissing and limit interval options

        if ($data) {
            $cnt=count($data);
            if ($cnt>0) {
                $p = 0;
                $sum = 0;
                while($p<$cnt) {
                    if (isset($data[$p][1]) && !is_nan($data[$p][1])) {
                        $sum += $data[$p][1];
                    }
                    $p++;
                }
                $value = ($sum / $cnt); // return average value
            }
            // logging 
            $endtime = microtime(true);
            $timediff = $endtime - $starttime;
            $this->log->info("source_feed_data_time() ". ($data_sampling ? "SAMPLING ":"") ."feedid=$feedid start=".($start/1000)." end=".($end/1000)." len=".(($end - $start)/1000)." int=$interval cnt=$cnt value=$value took=$timediff ");
        }
        return $value;
    }


    //CHAVEIRO TBD: virtual feed daily - not be required on sql engine but needs tests on other engines
    public function get_feed_data_day($id, $time, $value, $options)
    {
        if ($options['start'] && $options['end']) {
            $time = $this->getstartday($options['start']);
        } else {
            $time = $this->getstartday($time);
        }

        $feedname = "feed_".trim($id)."";
        $result = $this->mysqli->query("SELECT data FROM $feedname WHERE `time` = '$time'");
        if ($result != null ) $row = $result->fetch_array();
        if (isset($row))
        {
            return $row['data'];
        }
        else
        {
            return null;
        }
    }

    
    
    // No longer used
    public function average($feedid, $time_now, $value) { return $value; } // needs re-implementing    
    public function phaseshift($id, $time, $value) { return $value; }
    public function kwh_to_kwhd_old($feedid, $time_now, $value) { return $value; }
    public function power_acc_to_kwhd($feedid,$time_now,$value) { return $value; } // Process can now be achieved with allow positive process before power to kwhd

    //------------------------------------------------------------------------------------------------------
    // Calculate the energy used to heat up water based on the rate of change for the current and a previous temperature reading
    // See http://harizanov.com/2012/05/measuring-the-solar-yield/ for more info on how to use it
    //------------------------------------------------------------------------------------------------------
    public function heat_flux($feedid,$time_now,$value) { return $value; } // Removed to be reintroduced as a post-processing based visualisation calculated on the fly.
    
    // Get the start of the day
    public function getstartday($time_now)
    {
        $now = DateTime::createFromFormat("U", (int)$time_now);
        $now->setTimezone(new DateTimeZone($this->timezone));
        $now->setTime(0,0);    // Today at 00:00
        return $now->format("U");
    }

}