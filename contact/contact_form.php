<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recaptcha_response'])) {
#if (1) {

    # __________________________________________________________________
    # Some settings

    # Mailagent sender
    $email_mailagent = "contact_form@ccs.tsukuba.ac.jp";

    $test = false;
    $email_recipient_tester = 'ayw@ccs.tsukuba.ac.jp';

    # __________________________________________________________________
    # Main bit


    # Build POST request
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_secret = 'h6LfBNuAUAAAAAPHLNjqqyflSYifsYxf7ndGvGqzN';
    $recaptcha_response = $_POST['recaptcha_response'];

    # Make and decode POST request
    $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
    $recaptcha = json_decode($recaptcha);

    # Take action based on the score returned
    if ($recaptcha->score >= 0.0) {

    # Verified

    # Form variables
    $iln = $_POST['InputLastName'];
    $ifn = $_POST['InputFirstName'];
    $ilnr = $_POST['InputLastNameRomaji'];
    $ifnr = $_POST['InputFirstNameRomaji'];
    $iem = $_POST['InputEmail'];
    $irp = $_POST['InputRecipient'];
    $isb = $_POST['InputSubject'];
    $ibd = $_POST['InputBody'];

    # These are hidden form variables
    $ilg = $_POST['InputLang'];
    $ins = $_POST['InputNames'];
    $ies = $_POST['InputEmails'];

    # Create names-email array
    $names = explode("|", $ins);
    $emails = explode("|", $ies);
    $name_email_arr = array_combine($names, $emails);

    # Japanese names are optional in English form
    # Use Romaji names in that case
    if (empty($iln)) $iln = $ilnr;
    if (empty($ifn)) $ifn = $ifnr;
    
    # Recipient
    $recipient_name = trim($irp);
    $recipient = $name_email_arr["{$recipient_name}"];
    if (!strpos($recipient, "@")) {
      $recipient = $recipient . "@ccs.tsukuba.ac.jp";
    }

    # Construct email lines and file lines
    $email_body = "

<p>以下のメッセージは，{$iln} {$ifn} さんから宇宙物理理論研究室のウェブページの問い合わせフォームを通じて送信されました。</p>
<p>The following message was sent to you by {$ifnr} {$ilnr} through the Theoretical Astrophysics Group webpage contact form. </p>
<p>(https://https://www2.ccs.tsukuba.ac.jp/Astro/contact/).</p>

<br />
<hr>
<br />

    {$ibd}

";

    # Just kept for debugging
    #error_log("Recipient for contact form message is: {$recipient} <{$recipient_name}>", 3, "/Applications/MAMP/logs/php_error.log");
   
    # Email
    mb_language("neutral");  
    if ($test) {
      $recipient = $email_recipient_tester;
    } 
    $subject = $isb;
    $mailheader  = "MIME-Version: 1.0" . "\r\n";
    $mailheader .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $mailheader .= "From: $iem \r\n";
    $mailheader .= "Sender: {$email_mailagent} \r\n";
    $mailheader .= "X-Mailer: PHP/" . phpversion();
    mb_send_mail($recipient, $subject, $email_body, $mailheader);

    } else {

    # Not recaptcha verified

    }




} 
?>
