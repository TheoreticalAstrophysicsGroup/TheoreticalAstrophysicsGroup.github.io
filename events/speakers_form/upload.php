<?php

    # Image upload DropzoneJS
    $ds = DIRECTORY_SEPARATOR;

    $target_dir = 'img';

    if (!empty($_FILES)) {

      $today = new DateTime();
      $formattedDate = $today->format('Y-m-d');

      $targetPath = dirname( __FILE__ ) . $ds . $target_dir . $ds;

      // Loop over multiple files uploaded together
      for($ifile = 0; $ifile < count($_FILES['file']['tmp_name']); $ifile++) {
        $tempFile = $_FILES['file']['tmp_name'][$ifile];

        $fname = $_FILES['file']['name'][$ifile];
	$ext = pathinfo($fname, PATHINFO_EXTENSION);
	#$file = new SplFileInfo($tempFile);
	#$ext  = $file->getExtension();

	if ($ifile == 0) {
          $targetFile =  $targetPath . "talk-{$formattedDate}" . ".{$ext}";
        }
        else {
          $targetFile =  $targetPath . "talk-{$formattedDate}-{$ifile}" . ".{$ext}";
        }
        move_uploaded_file($tempFile, $targetFile);
      }

    }

?>
