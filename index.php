<?php
  include "gldb.php";       //database functions
  include "glsettings.php"; //misc. program settings

  include "gllogin.php";    //login form
  include "glview.php";     //list of events
  include "glcreate.php";   //new list creator
  include "glusers.php";    //edit/create user
  include "gledit.php";     //edit settings
  include "glaccount.php";  //manage account
  include "glevent.php";    //edit/update events
  include "glhelp.php";     //help file

  include "glarchive.php";  //view archived events -- only for "god"

  $user = null;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function checkpass($userobj, $pass) {
    if ($userobj == !false && $userobj->password == $pass) {
      return true;
    }
    return false;
  } //checkpass()

  function redirect($url) {
    $u = 'http://' . $_SERVER['HTTP_HOST'];
    $u .= rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $u .= '/';  //$u .= '/gl/';
    $u .= $url;
    header('Location: ' . $u, true, 302);
    die();
  } //redirect()

  function getheader() {
    $header = <<<END
<!DOCTYPE HTML>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <meta name="author" content="Justin Ryan, justin@betanightclub.com" />
    <title>Club Data Tools - Guest List Form Administration Utility</title>
    <link href='glstyles.css' type='text/css' rel='stylesheet' />
  </head>
  <body>
    <div id="centeredcontent">
      <br />
END;
    return $header;
  } //getheader()

  function navigation() {
    $admin = "";
    global $user;
    if ($user->type == "admin" || $user->type == "god") {
      $admin = <<<END
	    			<li><a href="?a=create">Create Form</a></li>
            <li><a href="?a=event">Edit Event</a></li>
	    			<li><a href="?a=users">Administer Users</a></li>
	    			<li><a href="?a=settings">Edit Settings</a></li>
END;
    }
    if ($user->type == "god") {
      $admin .= <<<END
	    			<li><a href="?a=archive">View Archive</a></li>
END;
    }

    $links = <<<END
<header id="head">
	<div class="container">
    	<nav id="menu">
    		<input type="checkbox" id="toggle-nav"/>
    		<label id="toggle-nav-label" for="toggle-nav" title="Click for navigation menu">&#9776;</label>
    		<div class="box">
	    		<ul>
$admin
	    			<li><a href="?a=view">View Events</a></li>
	    			<li><a href="?a=account">Manage Account</a></li>
            <li><a href="?a=help">Contact / Help</a></li>
            <li><a href="?a=logout">Log Out</a></li>
	    		</ul>
    		</div>
    	</nav>
	</div>
</header>
END;
    return $links;
  } //navigation()

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  dbconnect();
  if (isset($_COOKIE['verified']) && ($_COOKIE['verified'] == true) && (isset($_COOKIE['user']))) {
    $user = dbgetuserbyid($_COOKIE['user']);
    //if (true) {
    if (!empty($_GET['a'])) {
      switch ($_GET['a']) {
        //AJAX REQUESTS:
        case "ajax_retrieve": //include "glusers.php"!
          ajax_retrieve((isset($_GET['userid'])) ? $_GET['userid'] : "");
          break;
        case "ajax_status": //include "glview.php"!
          ajax_status($_GET['e'], $_GET['s']);
          break;
        case "ajax_view": //include "glview.php"!
          ajax_view($_GET['e']);
          break;
        case "ajax_view_a": //include "glview.php"!
          ajax_view($_GET['e'], '_archive');
          break;
        case "ajax_export": //include "glview.php"!
          ajax_export($_GET['e']);
          break;
        case "ajax_export_a": //include "glview.php"!
          ajax_export($_GET['e'], '_archive');
          break;
        case "ajax_event": //include "glevent.php"
          ajax_event($_GET['e']);
          break;
        case "ajax_email": //include "glview.php"
          ajax_email($_GET['e']);
          break;
        case "ajax_archive": //include "glview.php"
          ajax_archive($_GET['e']);
          break;

        //FORM SUBMISSIONS:
        case "form_create":
          break;
        case "settings_update":
          settings_update(); //include "gledit.php"!
          break;
        case "account_update":
          account_update(); //include "glaccount.php"!
          break;
        case "user_update":
          user_update(); //include "glusers.php"!
          break;
        case "event_create":
          event_create(); //include "glcreate.php"!
          break;
        case "event_update":
          event_update(); //include "glevent.php"
          break;

        //PAGE REQUESTS:
        case "login":
          show_login(
            (isset($_COOKIE['email']) && !empty($_COOKIE['email']))?$_COOKIE['email']:"",
            (isset($_COOKIE['pass']) && !empty($_COOKIE['pass']))?$_COOKIE['pass']:"",
            (isset($_COOKIE['save']) && !empty($_COOKIE['save']))?$_COOKIE['save']:false
          );
          break;
        case "logout":
          setcookie('verified', false, time() - 3600);
          //setcookie('user', false, time() - 3600);
          redirect("?");
          break;
        case "create":
          show_create();
          break;
        case "users":
          show_users();
          break;
        case "settings":
          show_settings();
          break;
        case "account":
          show_account();
          break;
        case "event":
          show_event(!empty($_GET['e']) ? $_GET['e'] : NULL);
          break;
        case "archive":
          show_archive();
          break;
        case "help":
          show_help();
          break;
        case "view":
        default:
          //show list of events (glview)
          show_view();
          break;
      }
    } else {
      //Show default page (glview)
      redirect("?a=view");
    }
  } else {
    if (!empty($_GET['a']) && $_GET['a'] == "login") {
      //check user's credentials, set cookie
      $user = dbgetuserbyemail($_POST['gllogin_user']);
      if (checkpass($user, $_POST['gllogin_pass'])) {
        if ($_POST['gllogin_save']) {
          $time = time() + 31622400;
          setcookie('save', true, $time);
        } else {
          $time = 0;
        }
        setcookie("verified", true, $time);
        setcookie("user", $user->id, $time);
        setcookie('email', $user->email, $time);
        redirect("?a=view");
      } else {
        //error loging in
        echo "LOGIN ERROR!";
      }
    } else {
    //Show login form
    show_login(
      (isset($_COOKIE['email']) && !empty($_COOKIE['email']))?$_COOKIE['email']:"",
      (isset($_COOKIE['pass']) && !empty($_COOKIE['pass']))?$_COOKIE['pass']:"",
      (isset($_COOKIE['save']) && !empty($_COOKIE['save']))?$_COOKIE['save']:false
    );
    }
  }
  dbdisconnect();