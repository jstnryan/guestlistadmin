<?php
  function dbdump_zip($username, $password, $hostname, $dbname) {
    // if mysqldump is on the system path you do not need to specify the full path
    // simply use "mysqldump --add-drop-table ..." in this case
    $dumpfname = $dbname . "_" . date("Y-m-d_H-i-s").".sql";
    $command = "mysqldump --opt --host='$hostname' --user='$username' --password='$password' $dbname > $dumpfname";
    system($command);

    // zip the dump file
    $zipfname = $dbname . "_" . date("Y-m-d_H-i-s").".zip";
    $zip = new ZipArchive();
    if($zip->open($zipfname,ZIPARCHIVE::CREATE)) {
       $zip->addFile($dumpfname,$dumpfname);
       $zip->close();
    }

    // read zip file and send it to standard output
    if (file_exists($zipfname)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($zipfname));
        flush();
        readfile($zipfname);
        exit;
    }
  }

  function dbdump($username, $password, $hostname, $dbname) {
    ob_start();

    // if mysqldump is on the system path you do not need to specify the full path
    // simply use "mysqldump --add-drop-table ..." in this case
    $command = "mysqldump --opt --host='$hostname' --user='$username' --password='$password' $dbname";
    system($command);

    $dump = ob_get_contents();
    ob_end_clean();

    // send dump file to the output
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($dbname . "_" . date("Y-m-d_H-i-s").".sql"));
    flush();
    echo $dump;
    exit();
  }

  //dbdump_zip('glistformadmin', 'A93!282a', '68.178.143.77', 'glistformadmin');
  dbdump_zip('glistformadmin', 'A93!282a', 'glistformadmin.db.9212641.hostedresource.com', 'glistformadmin');

  //dbdump('glistformadmin', 'A93!282a', 'glistformadmin.db.9212641.hostedresource.com', 'glistformadmin');