<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recaptcha_response'])) {

    # Build POST request:
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_secret = '';
    $recaptcha_response = $_POST['recaptcha_response'];

    # Make and decode POST request:
    $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
    $recaptcha = json_decode($recaptcha);

    # Take action based on the score returned:
    if ($recaptcha->score >= 0.0) {

    # Verified

    # Form variables
    $iry = $_POST['InputRankYear'];
    $iln = $_POST['InputLastName'];
    $ifn = $_POST['InputFirstName'];
    $ilnr = $_POST['InputLastNameRomaji'];
    $ifnr = $_POST['InputFirstNameRomaji'];
    $ie1 = $_POST['InputEmail1'];
    $ie2 = $_POST['InputEmail2'];
    $it1 = $_POST['InputTel1'];
    $it2 = $_POST['InputTel2'];
    $it3 = $_POST['InputTel3'];
    $it4 = $_POST['InputTel4'];
    $ip = $_POST['InputPostal'];
    $ia = $_POST['InputAddress'];
    $ir = $_POST['InputResearch'];
    $ih = $_POST['InputHomepage'];

    # Email username
    $email = explode('@', $ie1);
    $uname = $email[0];

    # Create filenames
    $fbase = '../../membersform_data/';  // For site on charon
    // $fbase = '../../../membersform_data/';  // For my LAMPP setup
    $fname_tex = $fbase . strtolower($ilnr) . "_" . strtolower($ifnr) . '.tex';
    $fname_yml = $fbase . strtolower($ilnr) . "_" . strtolower($ifnr) . '.html';

    # No forward slashes if strings empty
    $fse2 = empty($ie2) ? "" : "/";
    $fst4 = empty($it4) ? "" : "/";

    # Construct latex lines and yaml lines
    $latexlines = "$iln $ifn & 〒$ip $ia & $it1 $fst4 $it4 \\\\\r\n$ilnr $ifnr & \\texttt{ $ie1 } $fse2 \\texttt{ $ie2 } & $it2 \\\\";
    $yamllines = "---\r\nname: $iln $ifn\r\nname: $ifnr $ilnr\r\nemail: $uname\r\ntel: $it3\r\nposition: $iry\r\nhomepage: \"$ih\"\r\nresearch: $ir\r\n---";

    # Fix spaces in curly braces
    $latexlines = str_replace("{ ", "{", $latexlines);
    $latexlines = str_replace(" }", "}", $latexlines);

    # Replace all zenkaku spaces and repeating spaces with one hankaku space
    # Unicode u is crucial here, otherwise character set is changed.
    $latexlines = preg_replace('/[ 　]+/u', ' ', $latexlines);
    $yamllines = preg_replace('/[ 　]+/u', ' ', $yamllines);

    # Unify commas
    $latexlines = str_replace("、", "，", $latexlines);
    $yamllines = str_replace("、", "，", $yamllines);

    # Write files
    $fcon = fopen($fname_tex, 'w');
    fwrite($fcon, $latexlines);
    fclose($fcon);
    $fcon = fopen($fname_yml, 'w');
    fwrite($fcon, $yamllines);
    fclose($fcon);

    # Email 
    $formcontent = "$latexlines\r\n\r\n$yamllines";
    $recipient = 'astro.ccs.tsukuba@gmail.com';
    $subject = "TAG member $iln $ifn ($ilnr $ifnr) info";
    $mailheader = "From: $ie1 \r\n";
    mail($recipient, $subject, $formcontent, $mailheader);

    echo "<p>　</p>";
    echo "<p>　</p>";
    echo '<h4 class="pull-left">ご協力ありがとうございました。　</h4>';
    echo '<a href="."><button type="submit" class="btn btn-primary pull-right">再入力</button></a>';

    } else {

    # Not verified

    echo "<p>　</p>";
    echo "<p>　</p>";
    echo '<h4 class="pull-left">You have failed the recaptcha test. Please try again.　</h4>';
    echo '<a href="."><button class="btn btn-primary pull-right">再入力</button></a>';

    }
} ?>