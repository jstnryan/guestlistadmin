<?php
  function event_create() {
    $newid = get_random_string(6);
    $query = "SELECT id FROM events WHERE id = '$newid'";
    $res = mysql_query($query);
    if (mysql_num_rows($res) > 0) {
      event_create();
      return;
    }
    $maxsub18 = $_POST['event_maxsub_18'];
    if ($_POST['event_pricing_18'] == 'NN,NN') { $maxsub18 = '0'; }
    $query = "INSERT INTO events (id, user, year, month, day, headliner, pricing_21, pricing_18, maxsub, maxsub_21, maxsub_18, status, expire_hour, expire_minute)";
    $query .= " VALUES ('$newid', $_POST[event_user], $_POST[event_year], $_POST[event_month], $_POST[event_day], '$_POST[event_headliner]', '$_POST[event_pricing_21]', '$_POST[event_pricing_18]', $_POST[event_maxsub], $_POST[event_maxsub_21], $maxsub18, 'active', $_POST[event_expire_hour], $_POST[event_expire_minute])";
    $res = mysql_query($query);
    if (!$res) {
      $error = "I'm running out of patience for your shennanigans. Get your shit together.";
      //$error = $query;
    } else {
      $error = array('link' => $newid, 'who' => dbgetusernamebyid($_POST['event_user']));
    }
    show_create($_POST['event_year'], $_POST['event_month'], $_POST['event_day'], $_POST['event_headliner'], $_POST['event_maxsub'], $_POST['event_pricing_21'], $_POST['event_maxsub_21'], $_POST['event_pricing_18'], $_POST['event_maxsub_18'], $error);
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function get_random_string($length) {
    $valid_chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    $num_valid_chars = strlen($valid_chars);
    $random_string = "";
    for ($i = 0; $i < $length; $i++) {
      $random_pick = mt_rand(1, $num_valid_chars);
      $random_char = $valid_chars[$random_pick-1];
      $random_string .= $random_char;
    }
    return $random_string;
  } //get_random_string()

  function build_page_url() {
    $pageURL = 'http';
    //if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
      $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
      $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
  } //build_page_url()

  function get_public_path(){
    global $settings;
    if ($settings['misc']['shorturl']['enable']) {
      return $settings['misc']['shorturl']['url'];
    } else {
      $parts = parse_url(build_page_url());
      return $parts['scheme']."://".$parts['host'].substr($parts['path'],0,strrpos($parts['path'],"/"));
    }
  } //get_public_path()

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function getusersselect($showinactive = false, $selected = NULL, $showgod = FALSE) {
    //$query = "SELECT id, name FROM users WHERE (type = 'promoter' OR type = 'artist') AND (status = 'active'";
    $query = "SELECT id, name, type";
    if ($showinactive) { $query .= ", status"; }
    $query .= " FROM users WHERE (status = 'active'";
    if ($showinactive) { $query .= " OR status = 'inactive'"; }
    if (!$showgod) { $query .= ") AND (type != 'god'"; }
    $query .= ") ORDER BY name ASC";
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());
    while ($row = mysql_fetch_object($result)) {
      $sel = '';
      if ($row->id == $selected) { $sel = '" selected="selected'; }
      $type = '';
      if ($row->type !== 'artist') { $type = ' ['.strtoupper($row->type).']'; }
      if ($showinactive && $row->status == 'inactive') { $type .= ' [INACTIVE]'; }
      echo '<option value="'.$row->id.$sel.'">'.$row->name.$type.'</option>';
    }
  } //getusersselect()
  function getmonthsselect($month = NULL) {
    global $settings;
    foreach ($settings['misc']['months'] as $key => $val) {
      echo '<option value="'.$key.'"';
      if(!empty($month)){
        if ($month == $key) {
          echo ' selected="selected"';
        }
      } else {
        if (date($settings['misc']['format']['months']) == $key) {
          echo ' selected="selected"';
        }
      }
      echo '>'.$val.'</option>';
    }
  } //getmonthsselect()
  function getdaysselect($day = NULL) {
    global $settings;
    foreach ($settings['misc']['days'] as $key => $val) {
      echo '<option value="'.$key.'"';
      if(!empty($day)){
        if ($day == $key) {
          echo ' selected="selected"';
        }
      } else {
        //THIS NEEDS TO BE MODIFIED SO THE DATE FORMAT MATCHES THE ONE USED IN glSettings.php
        if (date($settings['misc']['format']['days']) == $key) {
          echo ' selected="selected"';
        }
      }
      echo '>'.$val.'</option>';
    }
  } //getdaysselect()
  function getprice21($price21 = NULL) {
    global $settings;
    foreach ($settings['prices']['21'] as $key => $val) {
      echo '<option value="'.$key.'"';
      if(!empty($price21)){
        if ($price21 == $key) {
          echo ' selected="selected"';
        }
      }
      echo '>'.$val.'</option>';
    }
  } //getprice21()
  function getprice18($price18 = NULL) {
    global $settings;
    foreach ($settings['prices']['18'] as $key => $val) {
      echo '<option value="'.$key.'"';
      if(!empty($price18)){
        if ($price18 == $key) {
          echo ' selected="selected"';
        }
      }
      echo '>'.$val.'</option>';
    }
  } //getprice18()

  function show_create($year = NULL, $month = NULL, $day = NULL, $headliner = NULL, $limit = NULL, $price21 = NULL, $limit21 = NULL, $price18 = NULL, $limit18 = NULL, $error = NULL) {
    global $settings;
?>
<?= getheader(); ?>
<?= navigation(); ?>
      <h2>Create a new guest list sign-up form:</h2>
<?php
    if (!empty($error)) {
      if (is_array($error)) {
        echo '<div class="msgbox">';
        echo "<h3>".$error['who']."'s page was successfully generated.</h3>";
        echo '<p>The new page is located here: <a href="'.get_public_path()."/".$error['link'].'" target="_blank">'.get_public_path()."/".$error['link'].'</a></p>';
        echo "</div>";
      } else {
        echo '<div class="msgbox errbox">';
        echo "<h3>What now? You screwed something up again.</h3>";
        echo "<p>".$error."</p>";
        echo "</div>";
      }
    }
?>
      <form name="events" action="?a=event_create" method="POST">
        <table class="formtable">
          <tr>
            <td>
              <h3>Event Details:</h3>
            </td>
            <td>
            </td>
          </tr>
          <tr>
            <td>
              <label for="event_user"><b>Artist</b> / Group:</label>
            </td>
            <td>
              <select id="event_user" name="event_user" autofocus="autofocus" required="required">
<?php
    getusersselect();
?>
              </select>
            </td>
          </tr>
          <tr>
            <td>
              <label for="event_year">Event Date, <b>Year</b>:</label>
            </td>
            <td>
              <input type="text" name="event_year" id="event_year" pattern="[0-9]{4}" maxlength="4" size="4" required="required" value="<?php if (!empty($year)) { echo $year; } else { echo date('Y'); } ?>" />
            </td>
          </tr>
          <tr>
            <td>
              <label for="event_month">Event Date, <b>Month</b>:</label>
            </td>
            <td>
              <select id="event_month" name="event_month" required="required">
<?php
    getmonthsselect($month);
?>
              </select>
            </td>
          </tr>
          <tr>
            <td>
              <label for="event_day">Event Date, <b>Day</b>:</label>
            </td>
            <td>
              <select id="event_day" name="event_day" required="required">
<?php
    getdaysselect($day);
?>
              </select>
            </td>
          </tr>
          <tr>
            <td>
              <label for="event_headliner"><b>Headliner</b>(s):</label>
            </td>
            <td>
              <input type="text" id="event_headliner" name="event_headliner" required="required" value="<?php if (!empty($headliner)) { echo $headliner; } ?>" />
            </td>
          </tr>
          <tr>
            <td>
              <h3>List Pricing & Limits:</h3>
            </td>
            <td>
            </td>
          </tr>
          <tr>
            <td>
              <label for="maxsub"><b>Total</b> signups limit:</label>
            </td>
            <td>
              <input type="text" name="event_maxsub" id="event_maxsub" pattern="[+-]?[0-9]+" required="required" value="<?php if (!empty($limit)) { echo $limit; } else { echo '-1'; } ?>" /><br />
              <span class="smalltext">Positive numbers are the total number of signups allowed.<br />A value of zero (0) means no signups allowed.<br />A value of negative 1 (-1) means unlimited signups (up to the individual 18/21 limits below).</span>
            </td>
          </tr>
          <tr>
            <td>
              <label for="event_pricing_21"><b>21</b> and over:</label>
            </td>
            <td>
              <select id="event_pricing_21" name="event_pricing_21" required="required">
<?php
    getprice21($price21);
?>
              </select>
            </td>
          </tr>
          <tr>
            <td>
            </td>
            <td>
              <input type="text" name="event_maxsub_21" id="event_maxsub_21" pattern="[+-]?[0-9]+" required="required" value="<?php if (!empty($limit21)) { echo $limit21; } else { echo '-1'; } ?>" /><br />
              <span class="smalltext">A value of negative 1 (-1) is limited only by the total, or unlimited if total is also -1.</span>
            </td>
          </tr>
          <tr>
            <td>
              <label for="event_pricing_18"><b>18</b> through 20:</label>
            </td>
            <td>
              <select id="event_pricing_18" name="event_pricing_18" required="required">
<?php
    getprice18($price18);
?>
              </select>
            </td>
          </tr>
          <tr>
            <td>
            </td>
            <td>
              <input type="text" name="event_maxsub_18" id="event_maxsub_18" pattern="[+-]?[0-9]+" required="required" value="<?php if (!empty($limit18)) { echo $limit18; } else { echo '-1'; } ?>" /><br />
              <span class="smalltext">A value of negative 1 (-1) is limited only by the total, or unlimited if total is also -1.</span>
            </td>
          </tr>
          <tr>
            <td>
              <h3>List RSVP Expiration:</h3>
            </td>
            <td>
            </td>
          </tr>
          <tr>
            <td>
              <label for="event_expire_hour">Expire Time, <b>Hour</b>:</label>
            </td>
            <td>
              <select id="event_expire_hour" name="event_expire_hour">
<?php
  for ($i = 0; $i <= 23; $i++) {
    echo '<option value="'.str_pad($i, 2, '0', STR_PAD_LEFT).'"';
    if (!empty($event)) {
      if ($e['expire_hour'] == $i) {
        echo ' selected="selected"';
      }
    } else {
      if ($settings['expiration']['hour'] == $i) {
        echo ' selected="selected"';
      }
    }
    echo '>'.str_pad($i, 2, '0', STR_PAD_LEFT).'</option>';
  }
?>
              </select>
            </td>
          </tr>
          <tr>
            <td>
              <label for="event_expire_minute">Expire Time, <b>Minute</b>:</label>
            </td>
            <td>
              <select id="event_expire_minute" name="event_expire_minute">
<?php
  for ($i = 0; $i <= 59; $i++) {
    echo '<option value="'.$i.'"';
    if (!empty($event)) {
      if ($e['expire_minute'] == $i) {
        echo ' selected="selected"';
      }
    } else {
      if ($settings['expiration']['minute'] == $i) {
        echo ' selected="selected"';
      }
    }
    echo '>'.str_pad($i, 2, '0', STR_PAD_LEFT).'</option>';
  }
?>
              </select>
            </td>
          </tr>
          <tr>
            <td>
            </td>
            <td>
              <input type="submit" name="event_submit" value="submit" />
            </td>
          </tr>
        </table>
      </form>
    </div><!-- #centeredcontent -->
    <script type="text/javascript">
      window.onload = function(){
        document.getElementById('event_user').selectedIndex = -1;
      }
    </script>
  </body>
</html>
<?php
  } //show_create()