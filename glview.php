<?php
  //AJAX function
  function ajax_status($event, $status) {
    if (empty($status) || !isset($status) || is_null($status) || $status == 'cancel') {
      $result = false;
    } else {
      $query = "UPDATE events SET status = '$status' WHERE id = '$event' LIMIT 1";
      $result = mysql_query($query) or die('Query failed: ' . mysql_error());
    }
    if (!$result) {
      $query = "SELECT status FROM events WHERE id = '$event' LIMIT 1";
      $res = mysql_query($query);
      while ($row = mysql_fetch_assoc($res)) { $output['result'] = $row['status']; }
    } else {
      $output['result'] = $status;
    }
    $output['action'] = 'status';
    $output['event'] = $event;
    echo json_encode($output);
  } //ajax_status($event, $status)

  function ajax_view($event, $archive = '') {
    $output['action'] = 'view';
    $output['event'] = $event;
    $output['result'] = <<<'END'
  <table class='view_table'>
    <thead>
      <tr>
        <th>Time</th>
        <th>Name</th>
        <th>Email</th>
        <th>Age</th>
        <th>Gender</th>
        <th>Custom1</th>
        <th>Custom2</th>
        <th>Custom3</th>
        <th>Custom4</th>
      </tr>
    </thead>
    <tbody>
END;
    $query = "SELECT time, name, email, age, gender, custom1, custom2, custom3, custom4 FROM signups$archive WHERE event = '$event'";
    $res = mysql_query($query);
    while ($row = mysql_fetch_assoc($res)) {
      $output['result'] .= <<<END
      <tr>
        <td class="nowrap">$row[time]</td>
        <td class="nowrap">$row[name]</td>
        <td class="nowrap">$row[email]</td>
        <td class="nowrap">$row[age]</td>
        <td class="nowrap">$row[gender]</td>
        <td>$row[custom1]</td>
        <td>$row[custom2]</td>
        <td>$row[custom3]</td>
        <td>$row[custom4]</td>
      </tr>
END;
    }
    $output['result'] .= <<<'END'
    </tbody>
  </table>
END;
    echo json_encode($output);
  } //ajax_view($event)

  function ajax_export($event, $archive = '') {
    $output['action'] = 'export';
    $output['event'] = $event;
    $output['result'] = "<textarea>21+ GUESTS:\n";
    $query = "SELECT name FROM signups$archive WHERE event = '$event' AND age >= '21'";
    $res = mysql_query($query);
    while ($row = mysql_fetch_assoc($res)) {
      $output['result'] .= $row['name']."\n";
    }
    $output['result'] .= "\n18+ GUESTS:";
    $query = "SELECT name FROM signups$archive WHERE event = '$event' AND age <= '20'";
    $res = mysql_query($query);
    while ($row = mysql_fetch_assoc($res)) {
      $output['result'] .= "\n".$row['name'];
    }
    $output['result'] .= "</textarea>";
    echo json_encode($output);
  } //ajax_export(event)

  function ajax_email($events) {
    $output['action'] = 'email';
    $output['event'] = $events;
    $output['result'] = "<textarea>";
    $query = "SELECT email FROM signups WHERE event IN ('" . implode("', '", explode(',', $events)) . "')";
    $res = mysql_query($query);
    while ($row = mysql_fetch_assoc($res)) {
      $output['result'] .= $row['email']."\n";
    }
    $output['result'] .= "</textarea>";
    echo json_encode($output);
  } //ajax_email($events)

  function ajax_archive($event) {
    $output['action'] = 'archiveALL';
    $output['event'] = $event;
    $query_1 = "INSERT INTO events_archive SELECT * FROM events WHERE id IN ('" . implode("', '", explode(',', $event)) . "')";
    $query_2 = "DELETE FROM events WHERE id IN ('" . implode("', '", explode(',', $event)) . "')";
    $query_3 = "INSERT INTO signups_archive SELECT * FROM signups WHERE event IN ('" . implode("', '", explode(',', $event)) . "')";
    $query_4 = "DELETE FROM signups WHERE event IN ('" . implode("', '", explode(',', $event)) . "')";
    mysql_query("START TRANSACTION");
    if (mysql_query($query_1) && mysql_query($query_2) && mysql_query($query_3) && mysql_query($query_4)) {
      mysql_query("COMMIT");
      $output['result'] = true;
    } else {
      mysql_query("ROLLBACK");
      //$output['result'] = $query_1."\n".$query_2."\n".$query_3."\n".$query_4;
      $output['result'] = false;
    }
    echo json_encode($output);
  } //ajax_archive($event)

  function geteventsbyuser($userid, $getcurrent = true) {
    global $user;

    if ($getcurrent) {
      $query = "SELECT id,";
      if ($user->type == 'admin' || $user->type == 'promoter' || $user->type == 'god') { $query .= " user,"; }
      $query .= " year, month, day, headliner, pricing_21, pricing_18, maxsub, maxsub_21, maxsub_18, status FROM events WHERE";
      if ($user->type == 'promoter') { $query .= " ((user = '" . $userid . "') OR (user IN (SELECT id FROM users WHERE association = '$user->id'))) AND"; } elseif ($user->type != 'admin' && $user->type != 'god') { $query .= " (user = '" . $userid . "') AND"; }
      $query .= " (CONCAT(LPAD(year, 4, '0'), LPAD(month, 2, '0'), LPAD(day, 2, '0')) >= '" . date('Ymd') . "') ORDER BY CONCAT(LPAD(year, 4, '0'), LPAD(month, 2, '0'), LPAD(day, 2, '0')) ASC";
    } else {
      $query = "SELECT id,";
      if ($user->type == 'admin' || $user->type == 'promoter' || $user->type == 'god') { $query .= " user,"; }
      $query .= " year, month, day, headliner, pricing_21, pricing_18, maxsub, maxsub_21, maxsub_18, status FROM events WHERE";
      if ($user->type == 'promoter') { $query .= " ((user = '" . $userid . "') OR (user IN (SELECT id FROM users WHERE association = '$user->id'))) AND"; } elseif ($user->type != 'admin' && $user->type != 'god') { $query .= " (user = '" . $userid . "') AND"; }
      $query .= " (CONCAT(LPAD(year, 4, '0'), LPAD(month, 2, '0'), LPAD(day, 2, '0')) < '" . date('Ymd') . "') ORDER BY CONCAT(LPAD(year, 4, '0'), LPAD(month, 2, '0'), LPAD(day, 2, '0')) DESC";
    }
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());

    while ($row = mysql_fetch_object($result)) {
      echo eventoutput($row, $getcurrent);
    }
  } //geteventsbyuser()
  function eventoutput($row, $activeevent = false) {
    global $user, $settings;

    //signup totals:
    $count_total = mysql_result(mysql_query("SELECT COUNT(*) FROM signups WHERE event = '$row->id'"), 0);
    $count_21 = mysql_result(mysql_query("SELECT COUNT(*) FROM signups WHERE event = '$row->id' AND age >= '21'"), 0);
    $count_18 = mysql_result(mysql_query("SELECT COUNT(*) FROM signups WHERE event = '$row->id' AND age <= '20'"), 0);

    $eventdate = getlancedatestring_view($row->year, $row->month, $row->day);
    $remain = "";
    $link = "";
    if ($activeevent) {
      $end = new DateTime($row->year . '-' . $row->month . '-' . $row->day . 'T' . $settings['expiration']['hour'] . ':' . $settings['expiration']['minute'] . ':00', new DateTimeZone('America/Denver'));
      $diff = $end->diff(new DateTime());
      $remain = $diff->format("<br />Remain: <b>%ad, %hh, %im</b>");
      $link = '<br />Form link: <a href="'.get_public_path()."/".$row->id.'" target="_blank">'.get_public_path()."/".$row->id.'</a>'; //include "glcreate.php"!
    }
    $options = <<<END
            <button class="linkbutton" id="view_$row->id" onclick="eView('$row->id');return false;">View</a></button>
            <button class="linkbutton" id="export_$row->id" onclick="eExport('$row->id');return false;">Export</a></button>
END;
    if ($activeevent) {
      $selected = array('active' => '', 'paused' => '', 'closed' => '');
      $selected[$row->status] = ' selected="selected"';
      $options .= <<<END
            <select class="linkbutton eventstatus" id="status_$row->id" onchange="eStatus('$row->id')">
              <option value="active"$selected[active]>Active</option>
              <option value="paused"$selected[paused]>Paused</option>
              <option value="closed"$selected[closed]>Closed</option>
END;
      if ($user->type == 'admin' || $user->type == 'god') {
        $options .= <<<END
              <option value="edit">Edit</option>
END;
      }
      $options .= <<<END
            </select>
END;
  }
  if (!$activeevent && ($user->type == 'admin' || $user->type == 'god')) {
    $options .= <<<END
            <!-- <button class="linkbutton" id="archive_$row->id" onclick="eArchive('$row->id')">Archive</button> -->
            <button class="linkbutton butselect" id="select_$row->id" onclick="eSelect('$row->id');return false;">Select</button>
END;
  }
  if ($user->type == 'admin' || $user->type == 'promoter' || $user->type == 'god') {
    $artist = "<h3 class='eventlist'>:: " . dbgetusernamebyid($row->user) . "</h3>";
  } else { $artist = ''; }
$res = <<<END
        <tr class="evtr" id="evtr-$row->id">
          <td>
            <h3 class="eventlist">$eventdate :: $row->headliner</h3>
          </td>
          <td id="evtd-$row->id" class="evstat-$row->status">
            {$artist}{$options}<br />
            <span class="smalltext">Count (Limit); All: <b>$count_total</b> ($row->maxsub), 21+: <b>$count_21</b> ($row->maxsub_21), 18+: <b>$count_18</b> ($row->maxsub_18){$remain}{$link}</span>
          </td>
        </tr>
        <tr class="trwin closed" id="trwin-$row->id"><td class="tdwin" id="tdwin-$row->id" colspan="2"><div class="divwin" id="divwin-$row->id"></div></td></tr>
END;
      return $res;
  }

  function show_view() {
    global $user;
?>
<?= getheader(); ?>
<?= navigation(); ?>
      <h2>Current Event Lists:</h2>
      <table class="formtable" id="events_current">
<?php
    geteventsbyuser($user->id, true);
?>
      </table>
      <hr />
      <h2>Elapsed Event Lists:</h2>
      <table class="formtable" id="events_elapsed">
<?php
    geteventsbyuser($user->id, false);

    if ($user->type == 'admin' || $user->type == 'god') {
?>
          <tr>
            <td>
            </td>
            <td>
              <br /><br />
              <h3 class="eventlist">:: Export Emails :: Archive Events</h3>
              <button class="linkbutton" id="archive_export" onclick="emailExport();return false;">Export</button>
              <button class="linkbutton" id="archive_submit" onclick="eArchive();return false;">Archive</button>
              <button class="linkbutton" id="selectall" onclick="eSelect('ALL');return false;">Select All</button>
            </td>
          </tr>
          <tr class="trwin closed" id="trwin-ALL"><td class="tdwin" id="tdwin-ALL" colspan="2"><div class="divwin" id="divwin-ALL"></div></td></tr>
<?php
    }
?>
      </table>
    </div><!-- #centeredcontent -->
    <script type="text/javascript">
      var eventList = [];
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
        var obj = JSON.parse(responseText);
//alert(responseText);
        if (obj['result'] !== true) {
          switch (obj['action']) {
            case 'status':
              document.getElementById('status_'+obj['event']).value = obj['result'];
              document.getElementById('evtd-'+obj['event']).className = "evstat-"+obj['result'];
              break;
            case 'view':
            case 'export':
              closeAll();
              document.getElementById('divwin-'+obj['event']).innerHTML = obj['result'];
              var row = document.getElementById('trwin-'+obj['event']);
              if (row.classList.contains('closed')) {
                row.classList.remove('closed');
              }
              row.classList.add('open');
              break;
            case 'email':
              closeAll();
              document.getElementById('divwin-ALL').innerHTML = obj['result'];
              var row = document.getElementById('trwin-ALL');
              if (row.classList.contains('closed')) {
                row.classList.remove('closed');
              }
              row.classList.add('open');
              break;
            case 'archiveALL':
              //this means there was an error trying to single/multiple archive
              alert("There was an error trying to archive the selected event(s).");
//alert(obj['result']);
closeAll();
document.getElementById('divwin-ALL').innerHTML = obj['result'];
var row = document.getElementById('trwin-ALL');
if (row.classList.contains('closed')) {
  row.classList.remove('closed');
}
row.classList.add('open');
              break;
          }
        } else {
          if (obj['action'] == 'archiveALL') {
            //success single/multiple archive
            if (obj['event'].length > 0) {
              var events = obj['event'].split(',');
              for (var i = 0; i < events.length; i++) {
                //remove rows
                var element = document.getElementById('evtr-'+events[i]);
                element.parentNode.removeChild(element);
                element = document.getElementById('trwin-'+events[i]);
                element.parentNode.removeChild(element);
              }
            }
          }
        }
      }

      //var sel = document.getElementById("user_id");
      //sel.onchange = function(){_SU3.ajax("?a=ajax_retrieve&userid="+sel.options[sel.selectedIndex].value, processResponse);};
      function eView(event) {
        var row = document.getElementById('trwin-'+event);
        if (row.classList.contains('open')) {
          row.classList.remove('open');
          row.classList.add('closed');
        } else {
          _SU3.ajax('?a=ajax_view&e='+event, processResponse);
        }
      }
      function eExport(event) {
        var row = document.getElementById('trwin-'+event);
        if (row.classList.contains('open')) {
          row.classList.remove('open');
          row.classList.add('closed');
        } else {
          _SU3.ajax('?a=ajax_export&e='+event, processResponse);
        }
      }
      function eStatus(event) {
        var sel = document.getElementById('status_'+event);
        var status = sel.options[sel.selectedIndex].value
        switch (status) {
/*
          case "archive":
            if (confirm("Are you certain you want to archive this active event?\n\nNeither the artist nor the promoter will have access to any existing signups.")) {
              eArchive(event);
            } else {
              _SU3.ajax('?a=ajax_status&e='+event+'&s=', processResponse);
            }
            break;
*/
          case "edit":
              document.location.href = '?a=event&e='+event;
            break;
          default:
            _SU3.ajax('?a=ajax_status&e='+event+'&s='+status, processResponse);
            break;
        }
      }
      function eArchive() {
          var evtStr = '';
          var nEvents = eventList.length;
          for (var i = 0; i < nEvents; i++) {
            if (!i == 0) { evtStr += ','; }
            evtStr += eventList[i];
          }
          _SU3.ajax('?a=ajax_archive&e='+evtStr, processResponse);
      }
      function emailExport() {
        var row = document.getElementById('trwin-ALL');
        if (row.classList.contains('open')) {
          row.classList.remove('open');
          row.classList.add('closed');
        } else {
          var evtStr = '';
          var nEvents = eventList.length;
          if (nEvents > 0) {
            for (var i = 0; i < nEvents; i++) {
              if (!i == 0) { evtStr += ','; }
              evtStr += eventList[i];
            }
          }
          _SU3.ajax('?a=ajax_email&e='+evtStr, processResponse);
        }
      }
      function eSelect(event) {
        if (event == 'ALL') {
          if (document.getElementById('selectall').innerHtml == 'Select All') {
            var rows = document.getElementById('events_elapsed').getElementsByClassName('evtr');
            var i = rows.length;
            while (i--) {
              if (!rows[i].classList.contains('evsel-sel')) {
                rows[i].classList.add('evsel-sel');
                var but = rows[i].getElementsByClassName('butselect');
                var j = but.length;
                while (j--) { but[j].innerHTML = 'Deselect'; }
                eventList[eventList.length] = rows[i].id.substr(5);//"evtr-XXXXXX"
              }
            }
            document.getElementById('selectall').innerHtml = 'Deselect All';
          } else {
            var rows = document.getElementById('events_elapsed').getElementsByClassName('evtr');
            var i = rows.length;
            while (i--) {
              if (rows[i].classList.contains('evsel-sel')) {
                rows[i].classList.remove('evsel-sel');
                var but = rows[i].getElementsByClassName('butselect');
                var j = but.length;
                while (j--) { but[j].innerHTML = 'Select'; }
              }
            }
            document.getElementById('selectall').innerHtml = 'Select All';
            while (eventList.length > 0) {
              eventList.pop();
            }
          }
        } else {
          var elm = document.getElementById('evtr-'+event);
          var but = document.getElementById('select_'+event);
          //if (elm.className.match(/(?:^|\s)evsel-sel(?!\S)/)) {
          if (elm.classList.contains('evsel-sel')) {
            //elm.className = 'evsel-none';
            elm.classList.remove('evsel-sel');
            but.innerHTML = 'Select';
            if (eventList.indexOf(event) != -1) {
              eventList.splice(eventList.indexOf(event), 1);
            }
          } else {
            //elm.className = 'evsel-sel';
            elm.classList.add('evsel-sel');
            but.innerHTML = 'Deselect';
            if (eventList.indexOf(event) == -1) {
              eventList[eventList.length] = event;
            }
          }
        }
      }

      function closeAll() {
        var rows = document.getElementsByClassName('trwin');
        var i = rows.length;
        while (i--) {
          if (rows[i].classList.contains('open')) {
            rows[i].classList.remove('open');
            rows[i].classList.add('closed');
            return;
          }
        }
      }
    </script>
  </body>
</html>
<?php
  } //show_view()