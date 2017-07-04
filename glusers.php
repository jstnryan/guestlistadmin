<?php
  //AJAX function
  function ajax_retrieve($userid) {
    global $db;
    $who = "";
    if ($userid == 'NEW') {
      $who = '';
    } else {
      $query = "SELECT type, name, email, artistlinks, promoterlinks, customfields, association, status FROM users WHERE (id = '$userid') LIMIT 1";
      $result = mysqli_query($db, $query) or die('Query failed: ' . mysqli_error($db));
      while ($row = mysqli_fetch_object($result)) {
        $who['type'] = $row->type;
        $who['name'] = $row->name;
        $who['email'] = $row->email;
        $who['links'] = explode("\n", $row->artistlinks);
        $who['promoter_links'] = $row->promoterlinks;
        $who['additional_fields'] = $row->customfields;
        $who['association'] = $row->association;
        $who['status'] = $row->status;
      }
    }
    echo json_encode($who);
  } //ajax_retrieve()

  function user_update() {
    global $db;
    $status = "active";
    if (isset($_POST['user_pwreset'])) { $status = "reset"; }
    if (isset($_POST['user_delete'])) { $status = "inactive"; }
    if ($_POST['user_id'] == 0) { $status = "reset"; }

    $prolinks = '';
    if (isset($_POST['user_plinks'])) {
      foreach ($_POST['user_plinks'] as $id => $val) {
        if ($_POST['user_plinks'][$id] == true) {
          if (!empty($prolinks)) { $prolinks .= ','; }
          $prolinks .= $id;
        }
      }
    }

    //remove blanks lines, and empty space at end of string
    $usrlinks = rtrim(preg_replace('/^[ \t]*[\r\n]+/m', '', $_POST['user_links']));

//TODO: need a better temporary password system for new users!
    if ($_POST['user_id'] == "0") { //create new
      $query = "INSERT INTO users (type, name, email, password, artistlinks, promoterlinks, customfields, association, status)";
      $query .= " VALUES ('$_POST[user_type]', '$_POST[user_name]', '$_POST[user_email]', 'password', '$usrlinks', '$prolinks', $_POST[user_fields], $_POST[user_association], 'active')";
    } else { //edit existing
      $query = "UPDATE users SET type = '$_POST[user_type]', name = '$_POST[user_name]', email = '$_POST[user_email]', artistlinks = '$usrlinks', promoterlinks = '$prolinks', customfields = $_POST[user_fields], association = $_POST[user_association], status = '$status'";
      $query .= " WHERE id = $_POST[user_id]";
    }
    $res = mysqli_query($db, $query);
    if (!$res) {
      $error = "Why do you have to fuck with people's shit like that? I don't even think you know what you're supposed to be doing.";
      //$error = $query;
    } else {
      $error = "SUCCESS";
    }
    show_users($error);
  } //user_update()

  function show_users($error = NULL) {
    global $user;
?>
<?= getheader(); ?>
<?= navigation(); ?>
      <h2>Administer Users:</h2>
<?php
    if (!empty($error)) {
      if ($error === "SUCCESS") {
        echo '<div class="msgbox">';
        echo "<h3>User's profile has been saved successfully.</h3>";
        echo "<p>Look at you go, all proactive an' shit.</p>";
        echo "</div>";
      } else {
        echo '<div class="msgbox errbox">';
        echo "<h3>You done fucked something up, didn't you?</h3>";
        echo "<p>".$error."</p>";
        echo "</div>";
      }
    }
?>
      <form name="users" action="?a=user_update" method="POST">
        <table class="formtable">
          <tr>
            <td>
              <label for="user_id">Artist / Group:</label>
            </td>
            <td>
              <select id="user_id" name="user_id">
                <option value="0">NEW ARTIST / GROUP</option>
<?php
    if ($user->type == 'god') {
      getusersselect(true, NULL, true); //show other god users
    } else {
      getusersselect(true); //include from "glcreate.php"!
    }
?>
              </select>
            </td>
          </tr>
          <tr>
            <td>
              <label for="user_type">User Type:</label>
            </td>
            <td>
              <select id="user_type" name="user_type">
                <option value="artist">Artist / Group</option>
                <option value="promoter">Promoter</option>
                <option value="admin">Administrator</option>
<?php
    if ($user->type == 'god') {
?>
                <option value="god">GOD</option>
<?php
    }
?>
              </select>
            </td>
          </tr>
          <tr>
            <td>
              <label for="user_name">Name:</label>
            </td>
            <td>
              <input type="text" name="user_name" id="user_name" required="required" value="" />
            </td>
          </tr>
          <tr>
            <td>
              <label for="user_email">Email:</label>
            </td>
            <td>
              <input type="email" name="user_email" id="user_email" value="" /><br />
              <span class="smalltext"><b>Not required</b> for users who will not check their lists directly (such as cases where Mahesh submits guest list). In these cases, ensure the "Promoter Association" is set correctly, below.</span>
            </td>
          </tr>
          <tr>
            <td>
              <label for="user_links">Users's Links:</label>
            </td>
            <td>
              <textarea name="user_links" id="user_links"></textarea><br />
              <span class="smalltext">One link per line; use full URL ("http://facebook.com/jstnryan").</span>
            </td>
          </tr>
          <tr>
            <td>
              <label>Promoter Links:</label>
            </td>
            <td>
<?php
      foreach (dbgetpromoters() as $id => $name) {
        echo '<label class="halfwidth"><input type="checkbox" name="user_plinks['.$id.']" id="user_plinks_'.$id.'">'.$name.'</label>';
      }
?>
            </td>
          </tr>
<!--
          <tr>
            <td>
              <label for="user_header">Header Image:</label>
            </td>
            <td>
              !-- <input type="file" id="user_header" name="user_header" /> --
              <input type="text" id="user_header" name="user_header" /><br />
              <span class="smalltext">Filename of custom image ("glheader_drgon.jpg"), leave blank for default.</span>
            </td>
          </tr>
-->
          <tr>
            <td>
              <label>Custom Fields:</label>
            </td>
            <td>
              <label class="fullwidth"><input type="radio" name="user_fields" id="user_fields_0" value="0" checked="checked">None</label>
<?php
      foreach (dbgetcustomfields() as $id => $name) {
        echo '<label class="fullwidth"><input type="radio" name="user_fields" id="user_fields_'.$id.'" value="'.$id.'">'.$name.'</label>';
      }
?>
            </td>
          </tr>
          <tr>
            <td>
              <label for="user_association">Promoter Association:</label>
            </td>
            <td>
              <select id="user_association" name="user_association">
                <option value="0">None</option>
<?= dbgetpromoteroptions(); ?>
              </select><br />
              <span class="smalltext">This is the promoter for the night/room that the artist is playing (mostly applicable to theLounge). For example, pretty much everybody playing upstairs on a Saturday will be associated with 'Mahesh Presents.' 'None' is for Beta residents who submit their own guest list.</span>
            </td>
          </tr>
          <tr>
            <td>
              <label for="user_pwreset">Reset Password:</label>
            </td>
            <td>
              <label class="fullwidth"><input type="checkbox" name="user_pwreset" id="user_pwreset">Reset this artist's / group's password.</label>
              <span class="smalltext">Use this <b>only</b> if the user has forgotten his/her password, and has requested assistance to reset it.</span>
            </td>
          </tr>
          <tr>
            <td>
              <label for="user_delete">Remove Entry:</label>
            </td>
            <td>
              <label class="fullwidth"><input type="checkbox" name="user_delete" id="user_delete">Deactivate this artist's / group's entry.</label>
              <span class="smalltext">This artist will no longer show up in the "Create Form" list.</span>
            </td>
          </tr>
          <tr>
            <td>
            </td>
            <td>
              <input type="submit" name="user_submit" value="submit" />
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
          //type
          // http://stackoverflow.com/questions/10911526/how-to-change-html-selected-option-using-javascript
          if (obj['type']) { document.getElementById("user_type").value = obj['type']; }
          //name
          if (obj['name']) { document.getElementById("user_name").value = obj['name']; } else { document.getElementById("user_name").value = ""; }
          //email
          if (obj['email']) { document.getElementById("user_email").value = obj['email']; } else { document.getElementById("user_email").value = ""; }
          //links
          if (obj['links']) {
            document.getElementById("user_links").value = "";
            if (obj['links'] == 'false') {
                document.getElementById("user_links").value = "";
            } else {
              if (obj['links'] instanceof Array) {
                var ta = document.getElementById("user_links");
                var i;
                for (i = 0; i < obj['links'].length; ++i) {
                  ta.value += obj['links'][i]+"\n";
                }
              } else {
                document.getElementById("user_links").value = obj['links'];
              }
            }
          } else {
            document.getElementById("user_links").value = "";
          }
          //[promoter]_links
          //disable all checkboxes
          var inputs = document.getElementsByTagName("input");
          for (var i = 0; i < inputs.length; i++) { if (inputs[i].id.indexOf("user_plinks_") == 0) inputs[i].checked = false; }
          //all checkboxes should be unchecked per above
          if (obj['promoter_links']) {
            var links = obj['promoter_links'].split(',');
            for (var i = 0; i < links.length; ++i) { document.getElementById("user_plinks_"+links[i]).checked = true; }
          }
          //header
          //if (obj['header']) { document.getElementById("who_header").value = (obj['header'] == 'false') ? "" : obj['header']; } else { document.getElementById("who_header").value = ""; }
          //additional_fields => user_fields
          if (obj['additional_fields']) { document.getElementById("user_fields_"+obj['additional_fields']).checked = true; } else { document.getElementById("user_fields_0").checked = true; }
          //association
          if (obj['association']) { document.getElementById("user_association").value = obj['association']; }
          // => who_pwreset
          document.getElementById("user_pwreset").checked = false;
          // => who_delete
          if (obj['status']) {
            if (obj['status'] == 'inactive') {
              document.getElementById("user_delete").checked = true;
            } else {
              document.getElementById("user_delete").checked = false;
            }
          }
      }
      var sel = document.getElementById("user_id");
      sel.onchange = function(){_SU3.ajax("?a=ajax_retrieve&userid="+sel.options[sel.selectedIndex].value, processResponse);};
    </script>
  </body>
</html>
<?php
  } //show_users()