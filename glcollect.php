<?php
  include "gldb.php";       //database functions
  include "glsettings.php"; //misc. program settings

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function show_collection_page($event, $limit21 = false, $limit18 = false) {
    if ($limit21 && $limit18) { show_error_page('limit'); return; } //should never hit this block, but the list is full, if so
    global $settings;

    //get artist details
    $query = "SELECT id, name, artistlinks, promoterlinks, customfields, association FROM users WHERE id = $event->user LIMIT 1";
    $res = mysql_query($query);
    while ($artist = mysql_fetch_object($res)) {

      $title = getlancedatestring($event->year, $event->month, $event->day)." :: ".$artist->name."&#39;s Guest List :: ".$event->headliner;

      $banner = "";
      if (file_exists('glheader/glheader_'.$artist->id.'.jpg')) { $banner = '<style type="text/css">#masthead {background-image:url("glheader/glheader_'.$artist->id.'.jpg")};</style>'; }

      $links = "";
      if (!empty($artist->artistlinks)) {
        foreach (explode("\n", $artist->artistlinks) as $val) { $links .= '<a href="'.$val.'" target="_blank">'.$val.'</a><br />'; }
        $links .= '<br />';
      }
      if (!empty($artist->promoterlinks)) {
        $arr = explode(',', $artist->promoterlinks);
        foreach ($arr as $id) {
          if ($id == $artist->id) { continue; } //prevent adding promoter's own links twice

          $lnk = dbgetpromoterlinksbyid($id);
          if (!empty($lnk)) {
            foreach (explode("\n", $lnk) as $value) { $links .= '<a href="'.$value.'" target="_blank">'.$value.'</a><br />'; }
          }
          $links .= '<br />';
        }
      }

      $promoter = '';
      if (!empty($artist->association)) {
        $pro = dbgetpromoters();
        $promoter = ', and '.$pro[$artist->association];
      }

      $extrafields = "";
      if ($artist->customfields > 0) {
        $extrafields = dbgetadditionalfieldsbyid($artist->customfields);
        //hacky little trick to fill in "Promoter Name" for Global Dance lists
        if ($artist->customfields == 5 && isset($_GET['promoter'])) {
          $extrafields = str_replace('value="" id="signup_custom1"', 'value="'.$_GET['promoter'].'" id="signup_custom1"', $extrafields);
        }
      }

      if ($limit21) {
        $price21 = "<b>THE 21+ LIST IS CURRENTLY FULL.</b>";
        $option_21plus = '';
      } else {
        $price21 = $settings['prices']['21'][$event->pricing_21];
        $option_21plus = '<br /><label><input type="radio" name="signup_age" value="21" id="signup_age_21" required="required" >21+</label>';
      }
      if ($limit18) {
        $price18 = "<b>THE 18+ LIST IS CURRENTLY FULL.</b>";
        $option_18plus = '';
      } else {
        $price18 = $settings['prices']['18'][$event->pricing_18];
        $option_18plus = '<br /><span class="smalltext">This is a 21+ only event. Please confirm that you are over the age of 21.</span>';
        if ($event->pricing_18 != "NN,NN") {
          $option_18plus = '<br /><label><input type="radio" name="signup_age" value="18" id="signup_age_18" required="required" />18+</label>';
        }
      }

      $output = <<<END
<!DOCTYPE HTML>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width" />
    <meta name="description" content="Sign-up on the guest list for Beta Nightclub by following this link." />
    <link rel="shortcut icon" href="http://www.betanightclub.com/favicon.ico" type="image/x-icon" />
    <link rel="image_src" href="http://tech.betanightclub.com/gl/fbicon.jpg" />
    <meta property="og:image" content="http://tech.betanightclub.com/gl/fbicon.jpg" />
    <meta property="og:title" content="$title" />
    <meta property="og:url" content="http://tech.betanightclub.com/gl/$event->id" />
    <meta property="og:type" content="article" />
    <meta property="article:expiration_time" content="{$event->year}-{$event->month}-{$event->day}T{$settings['expiration']['hour']}:{$settings['expiration']['minute']}-07:00" />
    <meta property="og:site_name" content="Beta Nightclub" />
    <meta property="og:description" content="Sign-up on the guest list for Beta Nightclub by following this link." />
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@betanightclub">
    <meta name="twitter:creator" content="@lb1justin">
    <meta name="twitter:title" content="$title">
    <meta name="twitter:description" content="Use this page to sign-up on for guest list access to Beta Nightclub on {$event->month}/{$event->day} featuring {$event->headliner}, courtesy of {$artist->name}.">
    <meta name="twitter:image" content="http://www.betanightclub.com/images/hidden/guestlist/fbicon.jpg">
    <meta name="author" content="Betatech, justin@betanightclub.com" />
    <title>$title</title>
    <link href='glstyles.css' type='text/css' rel='stylesheet' />
    <script src="http://betanightclub.com/js/cufon-yui.js" type="text/javascript"></script>
    <script src="http://betanightclub.com/js/Social_Gothic_400.font.js" type="text/javascript"></script>
    <script src="http://betanightclub.com/js/helper.js" type="text/javascript"></script>
    $banner
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
      ga('create', 'UA-8872962-6', 'auto');
      ga('send', 'pageview');
    </script>
  </head>
  <body>
    <div id="centeredcontent">
      <div id="masthead">Beta Nightclub</div>
      <h2>$title</h2>
      <p>This form allows you guest entry on {$artist->name}&#39;s list at the guest list door for the following lists:
        <br />21+: $price21
        <br />18+: $price18
      </p>
      $links
      <hr />
      <form action="?a=signup" method="POST" id="signup_form">
        <table class="formtable">
          <tr>
            <td>
              <input type="hidden" name="signup_event" value="{$event->id}" />
              <label for="signup_name" class="formtitle cfont">First &amp; Last Name:</label>
              <br /><input type="text" name="signup_name" value="" id="signup_name" autofocus="autofocus" required="required" />
            </td>
            <td>
              <label for="signup_email"><span class="formtitle cfont">Email Address:</span> <span class="smallertext">Checked for validity</span></label>
              <br /><input type="email" name="signup_email" value="" id="signup_email" required="required" />
            </td>
          </tr>
          <tr>
            <td class="halfwidth">
              <label for="signup_age" class="formtitle cfont">Age:</label>
              $option_21plus
              $option_18plus
            </td>
            <td class="halfwidth">
              <label for="signup_gender" class="formtitle cfont">Gender:</label>
              <br /><label><input type="radio" name="signup_gender" value="female" id="signup_gender_female" required="required" />Female</label>
              <br /><label><input type="radio" name="signup_gender" value="male" id="signup_gender_male" />Male</label>
            </td>
          </tr>
$extrafields
        </table>
        <input type="submit" name="signup_submit" value="Submit" />
        <p class="smalltext">By filling out this form you agree to allow Beta Nightclub (including {$artist->name}$promoter) to send you email updates about upcoming events and special offers. [Beta Nightclub and associated promotion groups do not share your personal information.]</p>
      </form>
      <hr />
    </div><!-- #centeredcontent -->
    <script type="text/javascript"> Cufon.now(); </script>
  </body>
</html>
END;
    } //end while (SQL query)

    echo $output;
  } //show_collection_page()

  function collect_signup($event) {
    //check for duplicates:
    $query = "SELECT COUNT(*) FROM signups WHERE event = '$_POST[signup_event]' AND name = '" . mysql_real_escape_string($_POST[signup_name]) . "' AND email = '" . mysql_real_escape_string($_POST[signup_email]) . "' AND age = '$_POST[signup_age]' AND gender = '$_POST[signup_gender]'";
    $res = mysql_query($query);
    if (mysql_result($res, 0) > 0) {
      show_error_page('duplicate');
    } else {
      $query = "INSERT INTO signups (event, name, email, age, gender";
      if (isset($_POST['signup_custom1'])) { $query .= ", custom1";
        if (isset($_POST['signup_custom2'])) { $query .= ", custom2";
          if (isset($_POST['signup_custom3'])) { $query .= ", custom3";
            if (isset($_POST['signup_custom4'])) { $query .= ", custom4"; }
          }}}
      $query .= ") VALUES ('$event->id', '" . mysql_real_escape_string($_POST[signup_name]) . "', '" . mysql_real_escape_string($_POST[signup_email]) . "', '$_POST[signup_age]', '$_POST[signup_gender]'";
      if (isset($_POST['signup_custom1'])) { $query .= ", '" . mysql_real_escape_string($_POST[signup_custom1]) . "'";
        if (isset($_POST['signup_custom2'])) { $query .= ", '" . mysql_real_escape_string($_POST[signup_custom2]) . "'";
          if (isset($_POST['signup_custom3'])) { $query .= ", '" . mysql_real_escape_string($_POST[signup_custom3]) . "'";
            if (isset($_POST['signup_custom4'])) { $query .= ", '" . mysql_real_escape_string($_POST[signup_custom4]) . "'"; }
          }}}
      $query .= ")";
      $res2 = mysql_query($query);
      if (!$res2) {
        show_error_page('database');
      } else {
        show_confirmation_page($event, $_POST['signup_name'], $_POST['signup_age']);
      }
    }
  } //collect_signup()

  function show_confirmation_page($event, $user, $age) {
    global $settings;

    //int representing the time/date of show for use in date/time formatting on this page
    $showtime = mktime(21,0,0,$event->month,$event->day,$event->year);

    $res = mysql_query("SELECT name FROM users WHERE id = '$event->user' LIMIT 1");
    while ($row = mysql_fetch_assoc($res)) { $artist = $row['name']; }

    $banner = "";
    if (file_exists('glheader/glheader_'.$event->user.'.jpg')) { $banner = '<style type="text/css">#masthead {background-image:url("glheader/glheader_'.$event->user.'.jpg")};</style>'; }

    if ($settings['expiration']['hour'] == 12) {
      $timestr = "12:".$settings['expiration']['minute']."PM";
    } elseif($settings['expiration']['hour'] > 12) {
      $timestr = ($settings['expiration']['hour'] - 12).":".$settings['expiration']['minute']."PM";
    } else {
      $timestr = $settings['expiration']['hour'].":".$settings['expiration']['minute']."AM";
    }

?>
<!DOCTYPE HTML>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="description" content="Thank you for signing up on the guest list." />
    <link rel="shortcut icon" href="http://www.betanightclub.com/favicon.ico" type="image/x-icon" />
    <link rel="image_src" href="http://www.betanightclub.com/images/hidden/guestlist/fbicon.jpg" />
    <meta property="og:image" content="http://www.betanightclub.com/fbthumb.jpg" />
    <meta property="og:description" content="Thank you for signing up on the guest list." />
    <meta name="author" content="Justin Ryan, justin@betanightclub.com" />
    <title><?php echo strtoupper(substr(date("D", $showtime),0,2))."/".$event->month."/".$event->day." :: ".$artist."&#39;s Guest List :: ".$event->headliner; ?></title>
    <link href='glstyles.css' type='text/css' rel='stylesheet' />
    <script src="http://betanightclub.com/js/cufon-yui.js" type="text/javascript"></script>
    <script src="http://betanightclub.com/js/Social_Gothic_400.font.js" type="text/javascript"></script>
    <script src="http://betanightclub.com/js/helper.js" type="text/javascript"></script>
    <?php echo $banner; ?>
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
      ga('create', 'UA-8872962-6', 'auto');
      ga('send', 'pageview');
    </script>
  </head>
  <body>
    <div id="centeredcontent">
      <div id="masthead">Beta Nightclub</div>
      <h2>Thanks for signing up <?php $freeloader = explode(" ", $user, 2); echo $freeloader[0]; ?>!</h1>
      <h3>You've been added to the <?php echo $age; ?>+ guest list for <?php echo $event->headliner; ?> on <?php echo date("l", $showtime).", ".$settings['misc']['months'][sprintf('%02d', $event->month)]." ".$settings['misc']['days'][sprintf('%02d', $event->day)]; ?>.</h3>
      <h4>The prices for this list are: <?php echo ($age == "21") ? $settings['prices']['21'][$event->pricing_21] : $settings['prices']['18'][$event->pricing_18]; ?>.</h4>
      <p>This sign-up form cuts off at <?= $timestr ?> MST. If you have signed up after <?= $timestr ?>, you will not be added to the guest list. List prices subject to change.</p>
      <hr>
      <center>
        <a class="twitter-timeline" href="https://twitter.com/BetaNightclub" data-widget-id="364828822652219392">Tweets by @BetaNightclub</a>
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
      </center>
    </div><!-- #centeredcontent -->
    <script type="text/javascript"> Cufon.now(); </script>
  </body>
</html>
<?php
  } //show_confirmation_page()

  function show_error_page($error) {
    global $settings;
    if ($settings['expiration']['hour'] == 12) {
      $timestr = "12:".$settings['expiration']['minute']."PM";
    } elseif($settings['expiration']['hour'] > 12) {
      $timestr = ($settings['expiration']['hour'] - 12).":".$settings['expiration']['minute']."PM";
    } else {
      $timestr = $settings['expiration']['hour'].":".$settings['expiration']['minute']."AM";
    }

    switch ($error) {
      case 'limit':
        $title = 'Signup limit reached';
        $message = "This list has already reached its maximum signup limit.";
        $content = <<<END
      <h3>Sorry, the maximum number of guests allowed on this list has been reached.</h3>
      <p>Unfortunately it seems that this list was rather popular, and you were unable to submit your name before all the spots were filled.</p>
END;
        break;
      case 'limit18':
        $title = 'Signup limit reached';
        $message = "This list has already reached its maximum signup limit for 18+ guests.";
        $content = <<<END
      <h3>Sorry, the maximum number of 18+ guests allowed on this list has been reached.</h3>
      <p>Unfortunately it seems that this list was rather popular, and you were unable to submit your name before all the 18+ spots were filled.</p>
END;
        break;
      case 'limit21':
        $title = 'Signup limit reached';
        $message = "This list has already reached its maximum signup limit for 21+ guests.";
        $content = <<<END
      <h3>Sorry, the maximum number of 21+ guests allowed on this list has been reached.</h3>
      <p>Unfortunately it seems that this list was rather popular, and you were unable to submit your name before all the 21+ spots were filled.</p>
END;
        break;
      case 'duplicate':
        $title = 'You already signed up';
        $message = "You're already signed up on this list.";
        $content = <<<END
      <h3>It seems you&apos;ve already signed up for this guest list.</h3>
      <p>No need to worry! We&apos;ll make sure your name is on the list the night of the show.</p>
END;
        break;
      case 'closed':
        $title = 'Guest list closed';
        $message = 'Sorry, this list is now closed.';
        $content = <<<END
      <h3>The owner or administrator of this list has deactivated it.</h3>
      <p>This could be due to any number of reasons, but likely the list was growing too large, or there was a mistake in pricing.</p>
END;
        break;
      case 'expired':
        $title = 'Guest list expired';
        $message = 'Sorry, this list is now closed.';
        $content = <<<END
      <h3>The time window for signing up on this guest list has passed.</h3>
      <p>Please remember that all Beta Nightclub sign-up forms expire at $timestr MST, the day of show.</p>
END;
        break;
      case 'paused':
        $title = 'Guest list on hold';
        $message = 'Sorry, submitting names to this list is currently on hold.';
        $content = <<<END
      <h3>Please try again at a later time.</h3>
      <p>The number of names submitted to this list may be nearing capacity, or the person administering the list has chosen to temporarily suspend it. If you&apos;d like, you can try submitting your information to this list again at a later time, after it has been reactivated.</p>
END;
        break;
      case 'invalid':
        $title = 'Invalid guest list form';
        $message = 'This is an invalid guest list form.';
        $content = <<<END
      <h3>The form you are requesting is invalid, or may be very old and has been removed.</h3>
      <p>These sorts of things do happen from time to time.</p>
END;
        break;
      case 'database':
        $title = 'Database error';
        $message = 'Sorry, an error was encountered when trying to add your name to the list.';
        $content = <<<END
      <h3>Please try again at a later time.</h3>
      <p>An unknown error was encountered when tyring to submit to this list. That cute secretary that everybody is trying to bang may have gotten pissed off, and run away with our database. We&apos;ll try to get this all sorted, but we encourage you to try submitting your name again in the future.</p>
END;
        break;
      case 'required':
        $title = 'Required fields were missing';
        $message = 'Sorry, it seems some required fields were not received.';
        $content = <<<END
      <h3>Please enter the required information.</h3>
      <p>One or more of the required fields was not submitted with your form. That&apos;s okay, these things happen sometimes. It&apos;s possible you forgot to fill out something before submitting, but we&apos;ll just play on the safe side and say it was your computer&apos;s fault. Not to worry, you can simply <a href="javascript:history.back()">go back</a> (or use your browser&apos;s back button) and fill in the required information.</p>
END;
        break;
      case 'error':
      default:
        $title = 'Unspecified error';
        $message = 'Sorry, it seems there has been an error with this list.';
        $content = <<<END
      <h3>Please try again at a later time.</h3>
      <p>An unknown error was encountered when tyring to submit to this list. It is possible that the Beta Nightclub IT team is being invaded by malicious alien life forms at this time. It is also possible that this whole thing is your fault. Whatever the cause, so long as your planet is not currently being invaded by hideous creatures from elsewhere, we encourage you to try again.</p>
END;
        break;
    }
    $output = <<<END
<!DOCTYPE HTML>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="description" content="$message" />
    <link rel="shortcut icon" href="http://www.betanightclub.com/favicon.ico" type="image/x-icon" />
    <link rel="image_src" href="http://www.betanightclub.com/images/hidden/guestlist/fbicon.jpg" />
    <meta property="og:image" content="http://www.betanightclub.com/fbthumb.jpg" />
    <meta property="og:description" content="$message" />
    <meta name="author" content="Justin Ryan, justin@betanightclub.com" />
    <title>$title</title>
    <link href='glstyles.css' type='text/css' rel='stylesheet' />
    <script src="http://betanightclub.com/js/cufon-yui.js" type="text/javascript"></script>
    <script src="http://betanightclub.com/js/Social_Gothic_400.font.js" type="text/javascript"></script>
    <script src="http://betanightclub.com/js/helper.js" type="text/javascript"></script>
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
      ga('create', 'UA-8872962-6', 'auto');
      ga('send', 'pageview', '{$_SERVER['REQUEST_URI']}/$error');
    </script>
  </head>
  <body>
    <div id="centeredcontent">
      <div id="masthead">Beta Nightclub</div>
      <h2>$message</h2>
$content
      <p>Tickets can be purchased online, until 8pm MST, through Beta Nightclub&apos;s website by <a href="http://www.betanightclub.com/events.php">clicking here</a>, or through <a href="http://www.flavorus.com/profile/Beta/10081">our affiliate Flavorus (Groovetickets)</a>. Tickets can also be purchased at Beta Nightclub&apos;s box-office, day of show, starting at 9pm MST until close.</p>
    </div><!-- #centeredcontent -->
    <script type="text/javascript"> Cufon.now(); </script>
  </body>
</html>
END;

    echo $output;
  } //show_error_page()

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  dbconnect();
  $query = "SELECT * FROM events WHERE id = '$_GET[e]' LIMIT 1";
  $res = mysql_query($query);
  if (mysql_num_rows($res) == 0) {
    show_error_page('invalid');
  } else {
    while ($event = mysql_fetch_object($res)) {
      switch ($event->status) {
        case "closed":
          show_error_page('closed');
          break;
        case "paused":
          show_error_page('paused');
          break;
        default:
          if ($event->year.sprintf("%02d", $event->month).sprintf("%02d", $event->day) >= date('Ymd')) {
            if (($event->year.sprintf("%02d", $event->month).sprintf("%02d", $event->day) == date('Ymd')) && (($settings['expiration']['hour'] <= date('G')) || (($settings['expiration']['hour'] == date('G')) && ($settings['expiration']['minute'] <= date('i'))))) {
              //expired
              show_error_page('expired');
            } else {
              //valid
              //Check that limits haven't been reached:
              $atlimit18 = false; $atlimit21 = false; $atlimitall = false;
              $count18 = 0; $count21 = 0; $countall = 0;
              $query_count18 = "SELECT COUNT(*) as count FROM signups WHERE event = '$event->id' AND age <= 20";
              $res_18 = mysql_query($query_count18);
              while ($res18 = mysql_fetch_object($res_18)) { $count18 = $res18->count; }
              $query_count21 = "SELECT COUNT(*) as count FROM signups WHERE event = '$event->id' AND age >= 21";
              $res_21 = mysql_query($query_count21);
              while ($res21 = mysql_fetch_object($res_21)) { $count21 = $res21->count; }
              $countall = $count18 + $count21;
              if (($event->pricing_18 != 'NN,NN') && ($event->maxsub_18 != -1 && $count18 >= $event->maxsub_18)) { $atlimit18 = true; }
              if ($event->maxsub_21 != -1 && $count21 >= $event->maxsub_21) { $atlimit21 = true; }
              if ($event->maxsub != -1 && $countall >= $event->maxsub) { $atlimitall = true; }
              if ($atlimitall) {
                //no space left on either age list
                show_error_page('limit');
              } else {
                //space left on 18, 21 or both lists
                if (isset($_GET['a']) && ($_GET['a'] == 'signup')) {
                  //user submitted a signup request
                  if ($_POST['signup_age'] >= 21 && $atlimit21) {
                    show_error_page('limit21');
                  } elseif ($_POST['signup_age'] <= 20 && $atlimit18) {
                    show_error_page('limit18');
                  } else {
                    //check for blank required fields
                    if (!trim($_POST['signup_name']) || empty($_POST['signup_email']) || empty($_POST['signup_age'])) {
                      //something was blank
                      show_error_page('required');
                    } else {
                      //everything looks good!
                      collect_signup($event); //show_confirmation_page();
                    }
                  }
                } else {
                  //show signup page
                  show_collection_page($event, $atlimit21, $atlimit18);
                }
              }
            }
          } else {
            //expired
            show_error_page('expired');
          }
        }
      } //switch ($event->status)
  }
  dbdisconnect();
