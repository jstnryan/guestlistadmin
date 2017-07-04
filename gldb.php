<?php
	//if (!function_exists('mysql_result')) {
		/*
		function mysql_result($result, $number, $field = 0) {
			mysqli_data_seek($result, $number);
			$row = mysqli_fetch_array($result);
			return $row[$field];
		}
		*/
		function mysqli_result($result, $number, $field = 0) {
			mysqli_data_seek($result, $number);
			$row = mysqli_fetch_array($result);
			return $row[$field];
		}
	//}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

	$db = new mysqli;

	//connect to db
	function dbconnect() {
		global $db;
		$dbsettings = parse_ini_file('settings.ini');
		//echo $dbsettings['host']."/".$dbsettings['user']."/".$dbsettings['password']."/".$dbsettings['dbname']; die;
		$db = mysqli_connect($dbsettings['host'], $dbsettings['username'], $dbsettings['password'], $dbsettings['dbname']);// or die('Could not connect: '. mysqli_error($db)); //LOL, this is possibly the most stupid line of code I've ever written.. if the _connect fails, there will be no reference ($db) to pull an _error.
		////mysqli_select_db('clubdata_guestlist') or die('Could not select database');
		if ($db->connect_error) { die('Connect Error (' . $db->connect_errno . ') ' . $db->connect_error); }
	} //dbconnect()
	function dbdisconnect() {
		global $db;
		mysqli_close($db);
	} //dbdisconnect()

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

	function dbgetuserbyid($userid) {
		global $db;
		$query = "SELECT * FROM users WHERE (id = '" . $userid . "') LIMIT 1";
		$result = mysqli_query($db, $query) or die('Query failed (dbgetuserbyid): ' . mysqli_error($db));
		return mysqli_fetch_object($result);
	} //dbgetuserbyid()
	function dbgetuserbyemail($username) {
		global $db;
		$query = "SELECT * FROM users WHERE (name = '" . $username . "' OR email = '" . $username . "')";
		$result = mysqli_query($db, $query) or die('Query failed (dbgetuserbyemail): ' . mysqli_error($db));

		switch (mysqli_num_rows($result)) {
			case 1:
				# Filter through rows and echo desired information
				while ($row = mysqli_fetch_object($result)) {
					return $row;
				}
				break;
			default:
				return false;
				break;
		}
	} //dbgetuserbyemail()

	function dbgetusernamebyid($userid) {
		global $db;
		$query = "SELECT name FROM users WHERE (id = '$userid') LIMIT 1";
		$result = mysqli_query($db, $query) or die('Query failed (dbgetusernamebyid): ' . mysqli_error($db));
		while ($row = mysqli_fetch_assoc($result)) {
			return $row['name'];
		}
	} //dbgetusernamebyid()

//TODO: depreciated? (is now updated by individual user)
	function dbupdatepromoterlinksbyid($id, $links) {
		global $db;
		$query = "UPDATE promoterlinks SET links = '$links' WHERE id = $id";
		$res = mysqli_query($db, $query);
		if (!$res) {
			return "Error trying to update ";
		} else {
			return true;
		}
	} //dbupdatepromoterlinksbyid($id, $links)
	function dbgetpromoterlinksbyid($id) {
		global $db;
		$query = 'SELECT artistlinks FROM users WHERE id = '.$id.' LIMIT 1';
		//$query = "SELECT links FROM promoterlinks WHERE id = " . $id . " LIMIT 1";
		$res = mysqli_query($db, $query);
		while ($row = mysqli_fetch_assoc($res)) {
			return $row['artistlinks'];
		}
	} //dbgetpromoterlinksbyid($id)

	function dbgetadditionalfieldsbyid($id) {
		global $db;
		$query = "SELECT html FROM additionalfields WHERE id = " . $id . " LIMIT 1";
		$res = mysqli_query($db, $query);
		while ($row = mysqli_fetch_assoc($res)) {
			return $row['html'];
		}
	} //dbgetadditionalfieldsbyid($id)

	function dbgetpromoters() {
		global $db;
		$promoters = array();
		$query = "SELECT id, name FROM users WHERE type = 'promoter' AND status != 'inactive' ORDER BY name ASC";
		$res = mysqli_query($db, $query);
		while ($row = mysqli_fetch_assoc($res)) {
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
		global $db;
		$custom = array();
		$query = "SELECT id, name FROM additionalfields WHERE 1";
		$res = mysqli_query($db, $query);
		while ($row = mysqli_fetch_assoc($res)) {
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
	function getmonthname_threechar($year, $month, $day) {
		//EX: Mar
		return date("M", mktime(21,0,0,$month,$day,$year));
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