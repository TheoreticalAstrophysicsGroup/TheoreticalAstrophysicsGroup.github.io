<?php

    # Image upload DropzoneJS
    $ds = DIRECTORY_SEPARATOR;

    $target_dir = 'img';

    if (!empty($_FILES)) {

      // get third tuesday of this month
      date_default_timezone_set('Asia/Tokyo');
      $timestamp = new DateTime('third tuesday of this month');

      // if date has passed
      if ($timestamp < new DateTime()) {
        $timestamp->modify('third tuesday of next month');
      }
      $formattedDate = $timestamp->format('Y-m-d');

      $targetPath = dirname( __FILE__ ) . $ds . $target_dir . $ds;

      // Loop over multiple files uploaded together
      for($ifile = 0; $ifile < count($_FILES['file']['tmp_name']); $ifile++) {
        $tempFile = $_FILES['file']['tmp_name'][$ifile];
        $fname = $_FILES['file']['name'][$ifile];
	if ($ifile == 0) {
          $targetFile =  $targetPath . "talk-{$formattedDate}";
        }
        else {
          $targetFile =  $targetPath . "talk-{$formattedDate}-{$ifile}";
        }
        move_uploaded_file($tempFile, $targetFile);
      }

    }

?>
