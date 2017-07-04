<?php
  function account_update() {
    global $db, $user;

    $query = "UPDATE users SET name = '$_POST[account_name]', email = '$_POST[account_email]', artistlinks = '$_POST[account_links]', customfields = $_POST[account_fields]";
    if (!empty($_POST['account_pass'])) { $query .= ", password = '$_POST[account_pass]'"; }
    $query .= " WHERE id = $user->id";
    $res = mysqli_query($db, $query);
    if (!$res) {
      $error = "You've really done it now. This mistake of yours could have grave consequences. Or maybe it wont. Try again, if you dare!";
    } else {
      $error = "SUCCESS";
    }
    $user = dbgetuserbyid($user->id); //update user info
    show_account($error);
  }

  function show_account($error = NULL) {
    global $user;
?>
<?= getheader(); ?>
<?= navigation(); ?>
      <h2>Manage Account:</h2>
<?php
    if (!empty($error)) {
      if ($error === "SUCCESS") {
        echo '<div class="msgbox">';
        echo "<h3>Your account settings were successfully updated.</h3>";
        echo "<p>Give yourself a pat on the back, because you're awesome.</p>";
        echo "</div>";
      } else {
        echo '<div class="msgbox errbox">';
        echo "<h3>Uh oh! There was an error updating your account settings.</h3>";
        echo "<p>".$error."</p>";
        echo "</div>";
      }
    }
?>
      <form name="account" action="?a=account_update" method="POST">
        <table class="formtable">
          <tr>
            <td>
              <label for="account_name">Name:</label>
            </td>
            <td>
              <input type="text" name="account_name" id="account_name" required="required" value="<?= $user->name ?>" /><br />
              <span class="smalltext">Your artist or DJ name as it will appear publicly on your forms.</span>
            </td>
          </tr>
          <tr>
            <td>
              <label for="account_links">Links:</label>
            </td>
            <td>
              <textarea name="account_links" id="account_links"><?= $user->artistlinks ?></textarea><br />
              <span class="smalltext">One link per line; <b>MUST use full URL</b> ("<b>http://</b>facebook.com/jstnryan").</span><br />
              <span class="smalltext">These are the links which will appear on your generated guest list pages after guests successfully sign up on your list.</span>
            </td>
          </tr>
          <tr>
            <td>
              <label>Custom Fields:</label>
            </td>
            <td>
              <label class="fullwidth"><input type="radio" name="account_fields" id="account_fields_0" value="0"<?php if ($user->customfields == 0) { echo ' checked="checked"'; } ?>>None</label>
<?php
      foreach (dbgetcustomfields() as $id => $name) {
        echo '<label class="fullwidth"><input type="radio" name="account_fields" id="account_fields_'.$id.'" value="'.$id.'"';
        if ($user->customfields == $id) { echo ' checked="checked"'; }
        echo '>'.$name.'</label>';
      }
?>
              <span class="smalltext"><b>Select "None"</b> unless you require guests to input additional fields along with their submissions.</span>
            </td>
          </tr>
          <tr>
            <td>
              <label for="account_email">Email:</label>
            </td>
            <td>
              <input type="email" name="account_email" id="account_email" required="required" value="<?= $user->email ?>" /><br />
              <span class="smalltext">Your email address serves as your login id and is never made public.</span>
            </td>
          </tr>
          <tr>
            <td>
              <label for="account_pass">Password:</label>
            </td>
            <td>
              <input type="text" name="account_pass" id="account_pass" value="" /><br />
              <span class="smalltext"><b>Leave blank</b> unless you intend to change your password.</span>
            </td>
          </tr>
          <tr>
            <td>
            </td>
            <td>
              <input type="submit" name="submit" value="account_submit" />
            </td>
          </tr>
        </table>
      </form>
      <br />
      <hr />
      <br />
      <table class="formtable">
        <tr>
          <td>Log Out:</td>
          <td>
            <a href="?a=logout">Click here to log out.</a>
          </td>
        </tr>
      </table>
    </div><!-- #centeredcontent -->
  </body>
</html>
<?php
  } //show_account()
?>