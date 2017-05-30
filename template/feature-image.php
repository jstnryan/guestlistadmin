<?php
	//ini_set("allow_url_fopen", true);

	$image = new stdClass();
	$image->year = (isset($_GET['y'])) ? $_GET['y'] : '0000';
	$image->month = (isset($_GET['m'])) ? $_GET['m'] : '00';
	$image->day = (isset($_GET['d'])) ? $_GET['d'] : '00';
	$image->dayofweek = (isset($_GET['w'])) ? $_GET['w'] : date("l", mktime(21,0,0,$image->month,$image->day,$image->year));

	//Do we already have this image?
	$image->uri = 'feature-image/feature-'.$image->year.$image->month.$image->day.'.jpg';
	if (file_exists($image->uri)) {
		header("Content-Type: image/jpeg");
		readfile($image->uri);
		exit;
	} //else:

	//Not found; figure out if it is available on Beta's server.
	//	ex: 'http://ftp.betanightclub.com/assets/images/email/2017/may/050917/3.jpg'
	$dayval = 0;
	switch ($image->dayofweek) {
		case "Monday":
			$dayval = -1;
			break;
		case "Tuesday":
			$dayval = 0;
			break;
		case "Wednesday":
			$dayval = 1;
			break;
		case "Thursday":
			$dayval = 2;
			break;
		case "Friday":
			$dayval = 3;
			break;
		case "Saturday":
			$dayval = 4;
			break;
		case "Sunday":
			$dayval = 5;
			break;
	  	default:
	  		$dayval = 6;
	}
	$image->uri = 'http://ftp.betanightclub.com/assets/images/email/'.$image->year.'/'.strtolower(date("M", mktime(21,0,0,$image->month,$image->day,$image->year))).'/'.date('mdy', mktime(21,0,0,$image->month,$image->day - $dayval,$image->year)).'/'.$dayval.'.jpg';
	echo $image->uri;
	exit;

	//$image->uri = 'feature-'.$image->dayofweek.'.jpg';

	//if(file_exists($image->uri)) {
		header("Content-Type: image/jpeg");
		readfile($image->uri);
	//} else {
	//	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	//}