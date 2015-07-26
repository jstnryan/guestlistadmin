<?php
  function show_login($user = "", $pass = "", $save = false, $error_title = "", $error_content = "") {
?>
<?= getheader(); ?>
      <h2>Beta Nightclub Guest List Administration:</h2>
<?php
    if (!empty($error_title) || !empty($error_content)) {
      echo '<div class="msgbox errbox">';
      if (!empty($error_title)) {
        echo "<h3>" . $error_title . "</h3>";
      }
      if (!empty($error_content)) {
        echo "<p>" . $error_content . "</p>";
      }
      echo "</div>";
    }
?>
      <form action="?a=login" method="POST">
        <table class="formtable">
          <tr>
            <td>
              <h3>Log In:</h3>
            </td>
            <td>
            </td>
          </tr>
          <tr>
            <td>
              <label for="gllogin_user"><b>Email Address</b>:</label>
            </td>
            <td>
              <input type="email" name="gllogin_user" id="gllogin_user" required="required" value="<?= $user; ?>" />
            </td>
          </tr>
          <tr>
            <td>
              <label for="gllogin_pass"><b>Password</b>:</label>
            </td>
            <td>
              <input type="password" name="gllogin_pass" id="gllogin_pass" required="required" value="<?= $pass; ?>" />
            </td>
          </tr>
          <tr>
            <td>
            </td>
            <td>
              <input type="submit" />
            </td>
          </tr>
          <tr>
            <td>&nbsp;
            </td>
            <td>
            </tr>
          </tr>
          <tr>
            <td>
            </td>
            <td>
              <label class="fullwidth"><input type="checkbox" name="gllogin_save" id="gllogin_save" value="save" <?php if ($save) { echo 'checked="checked" '; } ?>/>Save my credentials on this device.</label>
              <span class="smalltext">This will save your username and password <b>on this device</b> for up to one year. Uncheck this option if you are using a public computer or device which others have access to, or to clear previously saved credentials.</span>
            </td>
          </tr>
<!--
          <tr>
            <td>
            </td>
            <td>
              <label class="fullwidth"><input type="checkbox" name="gllogin_reset" id="gllogin_rest" value="reset">Reset lost password.</label>
              <span class="smalltext">Check this box if you have forgotten your password. Ensure your email address or username is entered in the appropriate box above. You will be emailed recovery instructions at the address associated with your account.</span>
            </td>
          </tr>
-->
        </table>
      </form>
    </div><!-- #centeredcontent -->
  </body>
</html>
<?php
  } //show_login()