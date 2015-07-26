<?php
  function getevent($event) {
    $e = "";
    $query = "SELECT * FROM events WHERE (id = '$event') LIMIT 1";
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());
    while ($row = mysql_fetch_assoc($result)) { $e = $row; }
    return $e;
  }
  function ajax_event($event) {
    echo json_encode(getevent($event));
  } //ajax_event($event)

  function event_update() {
    if (empty($_POST['update_event'])) { show_event(); return; }
    $query = "UPDATE events SET user = '$_POST[update_user]', year = '$_POST[update_year]', month = '$_POST[update_month]', day = '$_POST[update_day]', headliner = '$_POST[update_headliner]', pricing_21 = '$_POST[update_pricing_21]', pricing_18 = '$_POST[update_pricing_18]', maxsub = '$_POST[update_maxsub]', maxsub_21 = '$_POST[update_maxsub_21]', maxsub_18 = '$_POST[update_maxsub_18]'";
    $query .= " WHERE id = '$_POST[update_event]'";
    $res = mysql_query($query);
    if (!$res) {
      //$error = "I'm running out of patience for your shennanigans. Get your shit together.";
      $error = $query;
    } else {
      $error = array('link' => $_POST['update_event'], 'who' => dbgetusernamebyid($_POST['update_user']));
    }
    show_event($_POST['update_event'], $error);
  } //event_update()

  function geteventsselect($selected = NULL) {
    //$query = "SELECT id, name FROM users WHERE (type = 'promoter' OR type = 'artist') AND (status = 'active'";
    $query = "SELECT events.id, events.year, events.month, events.day, events.headliner, users.name";
    $query .= " FROM events INNER JOIN users ON events.user = users.id";
    $query .= " WHERE (CONCAT(LPAD(year, 4, '0'), LPAD(month, 2, '0'), LPAD(day, 2, '0')) >= '" . date('Ymd') . "')";
    $query .= " ORDER BY CONCAT(LPAD(year, 4, '0'), LPAD(month, 2, '0'), LPAD(day, 2, '0')) ASC";
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());
    while ($row = mysql_fetch_object($result)) {
      $sel = '';
      if ($row->id == $selected) { $sel = '" selected="selected'; }
      echo '<option value="'.$row->id.$sel.'">'.$row->id.' :: '.getcompactdatestring($row->year, $row->month, $row->day).' :: '.$row->name.'</option>';
    }
  } //geteventsselect()

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function show_event($event = NULL, $error = NULL) {
    $e = '';
    if (!empty($event)) {
      $e = getevent($event);
    }
?>
<?= getheader(); ?>
<?= navigation(); ?>
      <h2>Edit event details:</h2>
<?php
    if (!empty($error)) {
      if (is_array($error)) {
        echo '<div class="msgbox">';
        echo "<h3>".$error['who']."'s page was successfully updated.</h3>";
        echo '<p>The page with updated settings is located here: <a href="'.get_public_path()."/".$error['link'].'" target="_blank">'.get_public_path()."/".$error['link'].'</a></p>';
        echo "</div>";
      } else {
        echo '<div class="msgbox errbox">';
        echo "<h3>You've got all this power, and you go using it for evil.</h3>";
        echo "<p>".$error."</p>";
        echo "</div>";
      }
    }
?>
      <form name="update" action="?a=event_update" method="POST">
        <table class="formtable">
          <tr>
            <td>
              <h3>Select Event:</h3>
            </td>
            <td>
            </td>
          </tr>
          <tr>
            <td>
              <label for="update_event"><b>Event Code</b> (and details):</label>
            </td>
            <td>
              <select id="update_event" name="update_event" autofocus="autofocus" required="required">
<?php
    geteventsselect((!empty($event)) ? $event : NULL);
?>
              </select>
            </td>
          </tr>
          <tr>
            <td>
              <h3>Event Details:</h3>
            </td>
            <td>
            </td>
          </tr>
          <tr>
            <td>
              <label for="update_user"><b>Artist</b> / Group:</label>
            </td>
            <td>
              <select id="update_user" name="update_user" autofocus="autofocus" required="required">
<?php
    getusersselect(false, (!empty($event)) ? $e['user'] : NULL);
?>
              </select>
            </td>
          </tr>
          <tr>
            <td>
              <label for="update_year">Event Date, <b>Year</b>:</label>
            </td>
            <td>
              <input type="text" name="update_year" id="update_year" pattern="[0-9]{4}" maxlength="4" size="4" required="required" value="<?php if (!empty($event)) { echo $e['year']; } else { echo date('Y'); } ?>" />
            </td>
          </tr>
          <tr>
            <td>
              <label for="update_month">Event Date, <b>Month</b>:</label>
            </td>
            <td>
              <select id="update_month" name="update_month" required="required">
<?php
    getmonthsselect((!empty($event)) ? sprintf('%02d', $e['month']) : NULL);
?>
              </select>
            </td>
          </tr>
          <tr>
            <td>
              <label for="update_day">Event Date, <b>Day</b>:</label>
            </td>
            <td>
              <select id="update_day" name="update_day" required="required">
<?php
    getdaysselect((!empty($event)) ? sprintf('%02d', $e['day']) : NULL);
?>
              </select>
            </td>
          </tr>
          <tr>
            <td>
              <label for="update_headliner"><b>Headliner</b>(s):</label>
            </td>
            <td>
              <input type="text" id="update_headliner" name="update_headliner" required="required" value="<?php if (!empty($event)) { echo $e['headliner']; } ?>" />
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
              <input type="text" name="update_maxsub" id="update_maxsub" pattern="[+-]?[0-9]+" required="required" value="<?php if (!empty($event)) { echo $e['maxsub']; } else { echo '-1'; } ?>" /><br />
              <span class="smalltext">Positive numbers are the total number of signups allowed.<br />A value of zero (0) means no signups allowed.<br />A value of negative 1 (-1) means unlimited signups (up to the individual 18/21 limits below).</span>
            </td>
          </tr>
          <tr>
            <td>
              <label for="update_pricing_21"><b>21</b> and over:</label>
            </td>
            <td>
              <select id="update_pricing_21" name="update_pricing_21" required="required">
<?php
    getprice21((!empty($event)) ? $e['pricing_21'] : NULL);
?>
              </select>
            </td>
          </tr>
          <tr>
            <td>
            </td>
            <td>
              <input type="text" name="update_maxsub_21" id="update_maxsub_21" pattern="[+-]?[0-9]+" required="required" value="<?php if (!empty($event)) { echo $e['maxsub_21']; } else { echo '-1'; } ?>" /><br />
              <span class="smalltext">A value of negative 1 (-1) is limited only by the total, or unlimited if total is also -1.</span>
            </td>
          </tr>
          <tr>
            <td>
              <label for="update_pricing_18"><b>18</b> through 20:</label>
            </td>
            <td>
              <select id="update_pricing_18" name="update_pricing_18" required="required">
<?php
    getprice18((!empty($event)) ? $e['pricing_18'] : NULL);
?>
              </select>
            </td>
          </tr>
          <tr>
            <td>
            </td>
            <td>
              <input type="text" name="update_maxsub_18" id="update_maxsub_18" pattern="[+-]?[0-9]+" required="required" value="<?php if (!empty($event)) { echo $e['maxsub_18']; } else { echo '-1'; } ?>" /><br />
              <span class="smalltext">A value of negative 1 (-1) is limited only by the total, or unlimited if total is also -1.</span>
            </td>
          </tr>
          <tr>
            <td>
            </td>
            <td>
              <input type="submit" name="update_submit" value="submit" />
            </td>
          </tr>
        </table>
      </form>
    </div><!-- #centeredcontent -->
    <script type="text/javascript">
      // http://stackoverflow.com/questions/5350377/how-to-make-an-ajax-request-to-post-json-data-and-process-the-response

      // Just to namespace our functions and avoid collisions
      var _SU3 = _SU3 ? _SU3 : new Object();
      // Does a get request
      // url: the url to GET
      // callback: the function to call on server response. The callback function takes a
      // single arg, the response text.
      _SU3.ajax = function(url, callback){
          var ajaxRequest = _SU3.getAjaxRequest(callback);
          ajaxRequest.open("GET", url, true);
          ajaxRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
          ajaxRequest.send(null);
      };
      // Does a post request
      // callback: the function to call on server response. The callback function takes a
      // single arg, the response text.
      // url: the url to post to
      // data: the json obj to post
      _SU3.postAjax = function(url, callback, data) {
         var ajaxRequest = _SU3.getAjaxRequest(callback);
         ajaxRequest.open("POST", url, true);
         ajaxRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
         ajaxRequest.setRequestHeader("Connection", "close");
         ajaxRequest.send("data=" + encodeURIComponent(data));
      };
      // Returns an AJAX request obj
      _SU3.getAjaxRequest = function(callback) {
          var ajaxRequest;
          try {
              ajaxRequest = new XMLHttpRequest();
          } catch (e) {
              try {
                  ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
              } catch (e) {
                  try {
                      ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
                  } catch (e){
                      return null;
                  }
              }
          }
          ajaxRequest.onreadystatechange = function() {
              if (ajaxRequest.readyState == 4) {
                 // Prob want to do some error or response checking, but for
                 // this example just pass the responseText to our callback function
                 callback(ajaxRequest.responseText);
              }
          };
          return ajaxRequest;
      };

      function processResponse(responseText) {
//alert(responseText);
          var obj = JSON.parse(responseText);       // won't work all browsers, there are alternatives
          //user
          // http://stackoverflow.com/questions/10911526/how-to-change-html-selected-option-using-javascript
          if (obj['user']) { document.getElementById("update_user").value = obj['user']; }
          //year
          if (obj['year']) { document.getElementById("update_year").value = obj['year']; }
          //month
          //if (obj['month']) { document.getElementById("update_month").value = (obj['month'] < 10) ? '0'+obj['month'] : obj['month']; }
          if (obj['month']) { document.getElementById("update_month").value = obj['month']; }
          //day
          //if (obj['day']) { document.getElementById('update_day').value = (obj['day'] < 10) ? '0'+obj['day'] : obj['day']; }
          if (obj['day']) { document.getElementById('update_day').value = obj['day']; }
          //headliner
          if (obj['headliner']) { document.getElementById('update_headliner').value = obj['headliner']; }
          //total signups
          if (obj['maxsub']) { document.getElementById('update_maxsub').value = obj['maxsub']; }
          //21+ pricing
          if (obj['pricing_21']) { document.getElementById('update_pricing_21').value = obj['pricing_21']; }
          //21+ limit
          if (obj['maxsub_21']) { document.getElementById('update_maxsub_21').value = obj['maxsub_21']; }
          //18+ pricing
          if (obj['pricing_18']) { document.getElementById('update_pricing_18').value = obj['pricing_18']; }
          //18+ limit
          if (obj['maxsub_18']) { document.getElementById('update_maxsub_18').value = obj['maxsub_18']; }
      }
      var sel = document.getElementById("update_event");
      sel.onchange = function(){_SU3.ajax("?a=ajax_event&e="+sel.options[sel.selectedIndex].value, processResponse);};
<?php
    if (empty($event)) {
?>
      window.onload = function(){
        document.getElementById('update_event').selectedIndex = -1;
        document.getElementById('update_user').selectedIndex = -1;
      }
<?php
    }
?>
    </script>
  </body>
</html>
<?php
  } //show_event($event, $error)