<?php

    # Image upload DropzoneJS
    $ds = DIRECTORY_SEPARATOR;
 
    $target_dir = 'img';
 
    if (!empty($_FILES)) {
      for($ifile = 0; $ifile < count($_FILES['file']['tmp_name']); $ifile++) {
        $tempFile = $_FILES['file']['tmp_name'][$ifile];
        $targetPath = dirname( __FILE__ ) . $ds. $target_dir . $ds;
        $targetFile =  $targetPath. $_FILES['file']['name'][$ifile];
        move_uploaded_file($tempFile, $targetFile);
      }
    }


    // Image uploads 
    // TODO: adjust the above with additional checks below

    //$target_dir = "img/";
    //$target_file = $target_dir . basename($_FILES["file"]["name"]);
    //$uploadOk = 1;
    //$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    //if(isset($_POST["submit"])) {
    //  $check = getimagesize($_FILES["file"]["tmp_name"]);
    //  if($check !== false) {
    //    echo "File is an image - " . $check["mime"] . ".";
    //    $uploadOk = 1;
    //  } else {
    //    echo "File is not an image.";
    //    $uploadOk = 0;
    //  }
    //}

    // Limit file size to 30 Mb
    //if ($_FILES["file"]["size"] > 30000000 {
    //  echo "File: Sorry, your file is too large (max 30 MB). 画像ファイルは 30 MB まででお願いします。";
    //  $uploadOk = 0;
    //}

    // Allow certain file formats
    //if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
    //  echo "File: Sorry, only jpg/jpeg & png files allowed. 画像ファイルは jpg/jpeg & png でお願いします。";
    //  $uploadOk = 0;
    //}

    // Check if $uploadOk is set to 0 by an error
    //if ($uploadOk == 0) {
    //  echo "Sorry, your files were not uploaded.";

    // If everything is ok, try to upload file
    //} else {
    //  if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
    //    echo "The files ". htmlspecialchars( basename( $_FILES["file"]["name"])). " has been uploaded.";
    //  } else {
    //    echo "Sorry, there was an error uploading your file.";
    //  }
    //}

} ?>
