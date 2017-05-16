<?php
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