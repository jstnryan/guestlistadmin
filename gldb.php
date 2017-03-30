<?php
  //connect to db
  function dbconnect() {
    //super simple db connection
    //mysql_connect('68.178.143.77', 'glistformadmin', 'A93!282a') or die('Could not connect: ' . mysql_error());
    mysql_connect('glistformadmin.db.9212641.hostedresource.com', 'glistformadmin', 'A93!282a') or die('Could not connect: '. mysql_error());
    //mysql_connect('127.0.0.1', 'glistformadmin', 'A93!282a') or die('Could not connect: ' . mysql_error());
    mysql_select_db('glistformadmin') or die('Could not select database');
  } //dbconnect()
  function dbdisconnect() {
    mysql_close();
  } //dbdisconnect()

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function dbgetuserbyid($userid) {
    $query = "SELECT * FROM users WHERE (id = '" . $userid . "') LIMIT 1";
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());
    return mysql_fetch_object($result);
  } //dbgetuserbyid()
  function dbgetuserbyemail($username) {
    $query = "SELECT * FROM users WHERE (name = '" . $username . "' OR email = '" . $username . "')";
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());

    switch (mysql_num_rows($result)) {
      case 1:
        # Filter through rows and echo desired information
        while ($row = mysql_fetch_object($result)) {
          return $row;
        }
        break;
      default:
        return false;
        break;
    }
  } //dbgetuserbyemail()

  function dbgetusernamebyid($userid) {
    $query = "SELECT name FROM users WHERE (id = '$userid') LIMIT 1";
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());
    while ($row = mysql_fetch_assoc($result)) {
      return $row['name'];
    }
  } //dbgetusernamebyid()

//TODO: depreciated? (is now updated by individual user)
  function dbupdatepromoterlinksbyid($id, $links) {
    $query = "UPDATE promoterlinks SET links = '$links' WHERE id = $id";
    $res = mysql_query($query);
    if (!$res) {
      return "Error trying to update ";
    } else {
      return true;
    }
  } //dbupdatepromoterlinksbyid($id, $links)
  function dbgetpromoterlinksbyid($id) {
    $query = 'SELECT artistlinks FROM users WHERE id = '.$id.' LIMIT 1';
    //$query = "SELECT links FROM promoterlinks WHERE id = " . $id . " LIMIT 1";
    $res = mysql_query($query);
    while ($row = mysql_fetch_assoc($res)) {
      return $row['artistlinks'];
    }
  } //dbgetpromoterlinksbyid($id)

  function dbgetadditionalfieldsbyid($id) {
    $query = "SELECT html FROM additionalfields WHERE id = " . $id . " LIMIT 1";
    $res = mysql_query($query);
    while ($row = mysql_fetch_assoc($res)) {
      return $row['html'];
    }
  } //dbgetadditionalfieldsbyid($id)

  function dbgetpromoters() {
    $promoters = array();
    $query = "SELECT id, name FROM users WHERE type = 'promoter' AND status != 'inactive' ORDER BY name ASC";
    $res = mysql_query($query);
    while ($row = mysql_fetch_assoc($res)) {
      $promoters[$row['id']] = $row['name'];
    }
    return $promoters;
  } //dbgetpromoters()
  function dbgetpromoteroptions() {
    foreach (dbgetpromoters() as $id => $name) {
      echo "<option value='$id'>$name</option>";
    }
  } //dbgetpromoteroptions()

  function dbgetcustomfields() {
    $custom = array();
    $query = "SELECT id, name FROM additionalfields WHERE 1";
    $res = mysql_query($query);
    while ($row = mysql_fetch_assoc($res)) {
      $custom[$row['id']] = $row['name'];
    }
    return $custom;
  } //dbgetcustomfields()

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function getdayofweek($year, $month, $day) {
    //EX: Thursday
    //return strtoupper(substr(date("D", mktime(21,0,0,$month,$day,$year)),0,2));
    return date("l", mktime(21,0,0,$month,$day,$year));
  } //getdayofweek($year, $month, $day)
  function getmonthname($year, $month, $day) {
    //EX: January
    return date("F", mktime(21,0,0,$month,$day,$year));
  }
  function getlancedatestring($year, $month, $day) {
    return date('l | M d', mktime(21,0,0,$month,$day,$year));
  } //getlancedatestring($year, $month, $day)
  function getlancedatestring_view($year, $month, $day) {
    return date('D | M d, Y', mktime(21,0,0,$month,$day,$year));
  } //getlancedatestring($year, $month, $day)
  function getcompactdatestring($year, $month, $day) {
    return date('Y.m.d', mktime(21,0,0,$month,$day,$year));
  } //getcompactdatestring($year, $month, $day)