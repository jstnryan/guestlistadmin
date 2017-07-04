<?php
  function settings_update() {
    global $settings;
    $settings['expiration']['timezone'] = $_POST['settings_tz'];
    $settings['expiration']['hour'] = $_POST['settings_hour'];
    $settings['expiration']['minute'] = $_POST['settings_minute'];
    //$settings['misc']['cleanup'] = $_POST['settings_clean'];
    if (isset($_POST['settings_clean'])) { $settings['misc']['cleanup']['auto'] = true; } else { $settings['misc']['cleanup']['auto'] = false; }
    $settings['misc']['cleanup']['delay'] = $_POST['settings_delay'];
    $settings['misc']['shorturl']['enable'] = $_POST['settings_shorten'];
    $settings['misc']['shorturl']['url'] = rtrim($_POST['settings_shorturl'], '/');
    if (!save_settings()) {
      $error = "For some reason, the Advanced Settings could not be saved. It is suspected taht al1 hellll i5 Br3^k1nq l00se on 7he sEvV#r34a.....a#42";
    } else {
//DEPRECATED -- Remove!
/*
      $array = array('settings_links_beta', 'settings_links_global', 'settings_links_mahesh', 'settings_links_reload', 'settings_links_punchis', 'settings_links_submission');
      foreach ($array as $id => $name) {
        $error = dbupdatepromoterlinksbyid(($id + 1), $_POST[$name]);
        if ($error !== true) {
          $error .= "'" . $name . "'. To be honest, it's probably your fault.";
          break;
        }
      }
*/
      $error = "SUCCESS";
    }
    show_settings($error);
  } //settings_update()

  function show_settings($error = NULL) {
    global $settings;
?>
<?= getheader(); ?>
<?= navigation(); ?>
      <h2>Advanced Settings:</h2>
<?php
    if (!empty($error)) {
      if ($error === "SUCCESS") {
        echo '<div class="msgbox">';
        echo "<h3>Successfully updated global application settings.</h3>";
        echo "<p>Nice work. We considered giving you a plaque for your fine work, but it wasn't in the budget.</p>";
        echo "</div>";
      } else {
        echo '<div class="msgbox errbox">';
        echo "<h3>Crap. That didn't work correctly, did it?</h3>";
        echo "<p>".$error."</p>";
        echo "</div>";
      }
    }
?>
      <form name="settings" action="?a=settings_update" method="POST">
        <h3>List RSVP Expiration:</h3>
        <table class="formtable">
          <tr>
            <td>
              <label for="settings_tz">Time Zone:</label>
            </td>
            <td>
              <input type="text" id="settings_tz" name="settings_tz" required="required" value="<?php echo $settings['expiration']['timezone']; ?>" /><br />
              <span class="smalltext">See the list of <a href="http://php.net/manual/en/timezones.php" target=_blank>supported timezones</a>.</span>
            </td>
          </tr>
          <tr>
            <td>
              <label for="settings_hour">Default Expire Time, <b>Hour</b>:</label>
            </td>
            <td>
              <select id="settings_hour" name="settings_hour">
<?php
  for ($i = 0; $i <= 23; $i++) {
    echo '<option value="'.str_pad($i, 2, '0', STR_PAD_LEFT).'"';
    if ($settings['expiration']['hour'] == $i) {
      echo ' selected="selected"';
    }
    echo '>'.str_pad($i, 2, '0', STR_PAD_LEFT).'</option>';
  }
?>
              </select>
            </td>
          </tr>
          <tr>
            <td>
              <label for="settings_minute">Default Expire Time, <b>Minute</b>:</label>
            </td>
            <td>
              <select id="settings_minute" name="settings_minute">
<?php
  for ($i = 0; $i <= 59; $i++) {
    echo '<option value="'.str_pad($i, 2, '0', STR_PAD_LEFT).'"';
    if ($settings['expiration']['minute'] == $i) {
      echo ' selected="selected"';
    }
    echo '>'.str_pad($i, 2, '0', STR_PAD_LEFT).'</option>';
  }
?>
              </select>
            </td>
          </tr>
        </table>
        <h3>Archiving:</h3>
        <table class="formtable">
          <tr>
            <td>
              <label>Auto-Archive:</label>
            </td>
            <td>
              <label><input type="checkbox" id="settings_clean" name="settings_clean"<?php if ($settings['misc']['cleanup']['auto'] == true) { echo ' checked="checked"'; } ?> />Enable auto-archiving of expired forms</label>
            </td>
          </tr>
          <tr>
            <td>
              <label for="settings_delay">Delay (days):</label>
            </td>
            <td>
              <input type="text" id="settings_delay" name="settings_delay" value="<?= $settings['misc']['cleanup']['delay']; ?>" />
            </td>
          </tr>
        </table>
        <h3>Short URL:</h3>
        <table class="formtable">
          <tr>
            <td>
              <label>Enable:</label>
            </td>
            <td>
              <label><input type="checkbox" id="settings_shorten" name="settings_shorten"<?php if ($settings['misc']['shorturl']['enable'] == true) { echo ' checked="checked"'; } ?> />Enable use of short URL alternate links</label>
            </td>
          </tr>
          <tr>
            <td>
              <label for="settings_shorturl">Delay (days):</label>
            </td>
            <td>
              <input type="text" id="settings_shorturl" name="settings_shorturl" value="<?= $settings['misc']['shorturl']['url']; ?>" /><br />
              <span class="smalltext"><b>WARNING:</b> Modifying this url also requires the appropriate changes to .htaccess directives!</span>
            </td>
          </tr>
<!-- //TODO: depreciated, remove
          <tr>
            <td>
              <h3>Promoter Links:</h3>
            </td>
            <td>
            </td>
          </tr>
<?php
    foreach (dbgetpromoters() as $id => $name) {
?>
          <tr>
            <td>
              <label for="settings_links_<?= $id; ?>"><?= $name; ?>:</label>
            </td>
            <td>
              <textarea name="settings_links[<?= $id; ?>]" id="settings_links_<?= $id; ?>"><?= dbgetpromoterlinksbyid($id); ?></textarea>
            </td>
          </tr>
<?php
    }
?>
          <tr>
            <td>
              <h3>Promoter Links:</h3>
            </td>
            <td>
            </td>
          </tr>
          <tr>
            <td>
              <label for="settings_links_beta">Beta Nightclub:</label>
            </td>
            <td>
              <textarea name="settings_links_beta" id="settings_links_beta"><?= dbgetpromoterlinksbyid(1); ?></textarea><br />
            </td>
          </tr>
          <tr>
            <td>
              <label for="settings_links_global">Global Dance:</label>
            </td>
            <td>
              <textarea name="settings_links_global" id="settings_links_global"><?= dbgetpromoterlinksbyid(2); ?></textarea><br />
            </td>
          </tr>
          <tr>
            <td>
              <label for="settings_links_mahesh">Mahesh Presents:</label>
            </td>
            <td>
              <textarea name="settings_links_mahesh" id="settings_links_mahesh"><?= dbgetpromoterlinksbyid(3); ?></textarea><br />
            </td>
          </tr>
          <tr>
            <td>
              <label for="settings_links_reload">Reload:</label>
            </td>
            <td>
              <textarea name="settings_links_reload" id="settings_links_reload"><?= dbgetpromoterlinksbyid(4); ?></textarea><br />
            </td>
          </tr>
          <tr>
            <td>
              <label for="settings_links_punchis">PUNCHIS:</label>
            </td>
            <td>
              <textarea name="settings_links_punchis" id="settings_links_punchis"><?= dbgetpromoterlinksbyid(5); ?></textarea><br />
            </td>
          </tr>
          <tr>
            <td>
              <label for="settings_links_submission">Sub.Mission:</label>
            </td>
            <td>
              <textarea name="settings_links_submission" id="settings_links_submission"><?= dbgetpromoterlinksbyid(6); ?></textarea><br />
              <span class="smalltext">One link per line; use full URL ("http://facebook.com/jstnryan").</span>
            </td>
          </tr>
-->
          <tr>
            <td>
            </td>
            <td>
              <input type="submit" name="submit" value="settings_submit" />
            </td>
          </tr>
        </table>
      </form>
    </div><!-- #centeredcontent -->
  </body>
</html>
<?php
  } //show_edit()