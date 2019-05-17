<?php
    $keys = array('InputLastName', 'InputFirstName', 'InputLastNameKana', 'InputFirstNameKana', 'InputEmail', 'InputTel', 'InputAddress');

    $csv_line = array();
    foreach($keys as $key){
        array_push($csv_line,'' . $_GET[$key]);
    }

    $email = explode("@", $_GET['InputEmail'])
    $uname = $email[0]
    $fname = $uname . '.csv';
    $csv_line = implode(',', $csv_line);

    if(!file_exists($fname)){$csv_line = "\r\n" . $csv_line;}
    $fcon = fopen($fname,'a');
    $fcontent = $csv_line;
    fwrite($fcon,$csv_line);
    fclose($fcon);
?>
