<?php
  //$set = '{"expiration":{"timezone":"America\/Denver","hour":"18","minute":"00"},"prices":{"21":{"0,10":"Free before 11PM, $10 after","0,15":"Free before 11PM, $15 after","0,20":"Free before 11PM, $20 after","0,25":"Free before 11PM, $25 after","0,30":"Free before 11PM, $30 after","5,10":"$5 before 11PM, $10 after (Limited Free before 11PM spots available for early-bird sign ups)","10,15":"$10 before 11PM, $15 after (Limited Free before 11PM spots available for early-bird sign ups)","15,20":"$15 before 11PM, $20 after (Limited Free before 11PM spots available for early-bird sign ups)","20,25":"$20 before 11PM, $25 after (Limited Free before 11PM spots available for early-bird sign ups)","25,30":"$25 before 11PM, $30 after (Limited Free before 11PM spots available for early-bird sign ups)"},"18":{"15,FF":"$15 before 11PM, full price after","20,FF":"$20 before 11PM, full price after","25,FF":"$25 before 11PM, full price after","30,FF":"$30 before 11PM, full price after","35,FF":"$35 before 11PM, full price after","NN,NN":"21+ ONLY EVENT"}},"misc":{"cleanup":true,"months":{"01":"Jan","02":"Feb","03":"Mar","04":"Apr","05":"May","06":"Jun","07":"Jul","08":"Aug","09":"Sep","10":"Oct","11":"Nov","12":"Dec"},"days":{"01":"01","02":"02","03":"03","04":"04","05":"05","06":"06","07":"07","08":"08","09":"09","10":"10","11":"11","12":"12","13":"13","14":"14","15":"15","16":"16","17":"17","18":"18","19":"19","20":"20","21":"21","22":"22","23":"23","24":"24","25":"25","26":"26","27":"27","28":"28","29":"29","30":"30","31":"31"},"format":{"months":"m","days":"d"}}}';
  /*
  $settings = array(
      "expiration" => array(
          "timezone" => $glTimeZone,
          "hour" => $glTimeHour,
          "minute" => $glTimeMinute
        ),

      "prices" => array(
          "21" => $glPrices21,
          "18" => $glPrices18
        ),

      "misc" => array(
          "cleanup" => array(
              "auto" => $glDeleteOld,
              "delay" => $glDeleteDelay
            ),
          "months" => $arrMonths,
          "days" => $arrDays,
          "format" => array(
              "months" => $dateFormatMonths,
              "days" => $dateFormatDays
            )
        )
    );
  */
  $settings = json_decode(file_get_contents("glsettings.txt"), true);

  function save_settings() {
    global $settings;
    //maintain the alphabetical order of $settings['residents']
    //asort($settings['residents']);
    return file_put_contents("glsettings.txt", json_encode($settings));
  }
/*
echo "<pre>";
print_r($settings);
echo "</pre>";
*/