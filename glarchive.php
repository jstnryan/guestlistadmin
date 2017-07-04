<?php
  //AJAX function
  function ajax_email_a($events) {
    global $db;
    $output['action'] = 'email';
    $output['event'] = $events;
    $output['result'] = "<textarea>";
    $query = "SELECT email FROM signups WHERE event_archive IN ('" . implode("', '", explode(',', $events)) . "')";
    $res = mysqli_query($db, $query);
    while ($row = mysqli_fetch_assoc($res)) {
      $output['result'] .= $row['email']."\n";
    }
    $output['result'] .= "</textarea>";
    echo json_encode($output);
  } //ajax_email_a($events)


  function getarchivedevents($timespan = NULL) {
    global $db, $user;

    $query = "SELECT id, user, year, month, day, headliner, pricing_21, pricing_18, maxsub, maxsub_21, maxsub_18, status FROM events_archive";
    //if ($user->type == 'promoter') { $query .= " ((user = '" . $userid . "') OR (user IN (SELECT id FROM users WHERE association = '$user->id'))) AND"; } elseif ($user->type != 'admin') { $query .= " (user = '" . $userid . "') AND"; }
    $query .= " ORDER BY CONCAT(LPAD(year, 4, '0'), LPAD(month, 2, '0'), LPAD(day, 2, '0')) DESC";
    $result = mysqli_query($db, $query) or die('Query failed: ' . mysqli_error($db));

    while ($row = mysqli_fetch_object($result)) {
      echo eventoutput_a($row);
    }
  } //geteventsbyuser_a()
  function eventoutput_a($row) {
    global $db, $user, $settings;

    //signup totals:
    $count_total = mysqli_result(mysqli_query($db, "SELECT COUNT(*) FROM signups_archive WHERE event = '$row->id'"), 0);
    $count_21 = mysqli_result(mysqli_query($db, "SELECT COUNT(*) FROM signups_archive WHERE event = '$row->id' AND age >= '21'"), 0);
    $count_18 = mysqli_result(mysqli_query($db, "SELECT COUNT(*) FROM signups_archive WHERE event = '$row->id' AND age <= '20'"), 0);

    $eventdate = getlancedatestring_view($row->year, $row->month, $row->day);
    $remain = "";
    $link = "";
    $options = <<<END
            <button class="linkbutton" id="view_$row->id" onclick="eView('$row->id');return false;">View</a></button>
            <button class="linkbutton" id="export_$row->id" onclick="eExport('$row->id');return false;">Export</a></button>
END;
    $artist = "<h3 class='eventlist'>:: " . dbgetusernamebyid($row->user) . "</h3>";
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
  } //eventoutput_a()

  function show_archive() {
    global $user;
?>
<?= getheader(); ?>
<?= navigation(); ?>
      <h2>Archived Event Lists:</h2>
      <table class="formtable" id="events_current">
<?php
    getarchivedevents();
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
          _SU3.ajax('?a=ajax_view_a&e='+event, processResponse);
        }
      }
      function eExport(event) {
        var row = document.getElementById('trwin-'+event);
        if (row.classList.contains('open')) {
          row.classList.remove('open');
          row.classList.add('closed');
        } else {
          _SU3.ajax('?a=ajax_export_a&e='+event, processResponse);
        }
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
          _SU3.ajax('?a=ajax_email_a&e='+evtStr, processResponse);
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
  } //show_archive()