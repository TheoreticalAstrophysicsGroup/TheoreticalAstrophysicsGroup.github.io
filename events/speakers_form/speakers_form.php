<?php

# __________________________________________________________________
# Some settings

# Organizer(s) name and email, sender (to the speaker) for uchu forum
$organizer_uchu_forum_ja = "郭";
$organizer_uchu_forum_en = "Tao Guo";
$email_uchu_forum = "guotao823@ccs.tsukuba.ac.jp, misamisa@ccs.tsukuba.ac.jp, kuroday@ccs.tsukuba.ac.jp, s2430049@u.tsukuba.ac.jp";
$email_sender_uchu_forum = "guotao823@ccs.tsukuba.ac.jp";

# Organizer(s) name and email, sender (to the speaker) for colloquia
$organizer_colloquium_ja = "郭";
$organizer_colloquium_en = "Tao Guo";
$email_colloquium = "guotao823@ccs.tsukuba.ac.jp, misamisa@ccs.tsukuba.ac.jp, kuroday@ccs.tsukuba.ac.jp";
$email_sender_colloquium = "guotao823@ccs.tsukuba.ac.jp";

# Mailagent sender
$email_mailagent = "speakers_form@ccs.tsukuba.ac.jp";

# Colloquium host also organizing
$colloquium_host_org = false;

# Testing
$test = false;
$email_recipient_tester = 'ayw@ccs.tsukuba.ac.jp';

# Location
$loc_en = "Seminar Room A";
$loc_ja = "会議室A";

# __________________________________________________________________
# Main bit


function str_replace_first($needle, $replace, $haystack) {
  $pos = strpos($haystack, $needle, $replace);
    if ($pos !== false) {
        $newstring = substr_replace($haystack, $replace, $pos, strlen($needle));
    }
  return $newstring;
}

function str_replace_last($needle, $replace, $haystack) {
  $pos = strrpos($haystack, $needle);
    if ($pos !== false) {
        $newstring = substr_replace($haystack, $replace, $pos, strlen($needle));
    }
  return $newstring;
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recaptcha_response'])) {

    # Translating inputted talk types to category names
    $ttype_ids = array(
      '宇宙フォーラム' => 'uchu_forum', 
      'コロキウム' => 'colloquia', 
      'Uchu Forum' => 'uchu_forum', 
      'Colloquium' => 'colloquia', 
    );

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
    $iaffja = $_POST['InputAffilJa'];
    $iaffen = $_POST['InputAffilEn'];
    $ie = $_POST['InputEmail'];
    $ih = $_POST['InputHomepage'];
    $ish = $_POST['InputSpeakerHost'];

    $ity = $_POST['InputTalkType'];
    $itt = $_POST['InputTalkTitle'];
    $ita = $_POST['InputTalkAbstract'];
    $ittime = $_POST['InputTalkTime'];
    $itdate = $_POST['InputTalkDate'];
    $itkw1 = $_POST['InputTalkKw1'];
    $itkw2 = $_POST['InputTalkKw2'];
    $itkw3 = $_POST['InputTalkKw3'];
    $itr = $_POST['InputTalkRemarks'];

    # These are hidden form variables
    $ilg = $_POST['InputLang'];
    $insja = $_POST['InputNamesJa'];
    $insen = $_POST['InputNamesEn'];
    $inscl = $_POST['InputNamesCl'];
    $ies = $_POST['InputEmails'];

    # TODO: Use DeepL to get English titles and abstracts, and other stuff, if missing.

    # Defaults for Japanese names when not given (e.g. in English form, they are optional)
    if (empty($iln)) $iln = $ilnr;
    if (empty($ifn)) $iln = $ifnr;
    if (empty($iaffja)) $iaffja = $iaffen;

    # Host emails: create names-email array. Names will be Romaji (English) names only.
    $names_ja = explode("|", $insja);
    $names_en = explode("|", $insen);
    $names_cl = explode("|", $inscl);
    $emails = explode("|", $ies);
    $name_cl_email_arr = array_combine($names_cl, $emails);
    $name_cl_name_ja_arr = array_combine($names_cl, $names_ja);
    $name_cl_name_en_arr = array_combine($names_cl, $names_en);

    # Get host email, and names in English and Japanese
    $host_name_cl = trim($ish);
    $host_email = $name_cl_email_arr["{$host_name_cl}"];
    if (!strpos($host_email, "@")) {
      $host_email = $host_email . "@ccs.tsukuba.ac.jp";
    }
    $host_name_ja = $name_cl_name_ja_arr["{$host_name_cl}"];
    $host_name_en = $name_cl_name_en_arr["{$host_name_cl}"];

    # More flexibility for Japanese host names
    $host_name_ja_bl = explode(" ", $host_name_ja);
    $host_name_ja_sn = $host_name_ja_bl[0];
    $host_name_ja_fn = $host_name_ja_bl[1];
    $host_name_ja_nsp = $host_name_ja_sn . $host_name_ja_fn;

    # Create filenames
    $fbase = 'yml/';
    $date_str = str_replace("/", "-", $itdate);
    $fname_yml_ja = $fbase . 'ja/' . $date_str . '-' . strtolower($ilnr) . '.html';
    $fname_yml_en = $fbase . 'en/' . $date_str . '-' . strtolower($ilnr) . '.html';

    # Titles sometimes contain colons which we cannot have.
    $itt = str_replace(":", "&#58;", $itt);

    # Create position string for students
    # Just keeping for reference
    #$pos = preg_match('/^[DMY][0-5]/', $iry) ? $iry : "";
    #$pos = preg_match('/研究生/u', $iry) ? "研究生" : $pos;

    # Date string
    $days = array('日', '月', '火', '水', '木', '金', '土');
    $date_str_ja = date("n月j日",strtotime($date_str));
    $date_str_en = date("F j",strtotime($date_str));
    $twdy_en = date("D", strtotime($date_str));
    $twdy_ja = $days[date("w", strtotime($date_str))];

    # Uchu forum or colloquium-dependent strings
    if ($ttype_ids[$ity] == "uchu_forum") {
      $organizer_ja = $organizer_uchu_forum_ja;
      $organizer_en = $organizer_uchu_forum_en;
      $subject_str = "宇宙フォーラム (Uchu Forum)";
      $email_intro_ja = "<p>今月の宇宙フォーラムは，" . $iaffja . "の " . $iln . " " . $ifn . " 氏に<br/>ご講演していただきます。 </p><p>講演タイトルおよび概要を下記に記載いたしましたのでご確認ください。</p>" . 
        "<p>宇宙フォーラム後には講演者と学生のみの議論の時間を<br/>設けて頂きました（開催予定時刻：17:15 ~ 17:45）。<br/>学生の方はそちらも奮ってご参加ください。</p>";
      $email_intro_en = "<p>This month's Uchu Forum will be given by " . $ifnr . " " . $ilnr . " from " . $iaffen . ".<br/>Please find the title and abstract of the talk below.</p>" . 
        "<p>Following the talk, there will be a discussion session with the speaker exclusively for students (scheduled from 17:15 to 17:45). We encourage all students to actively participate in this discussion session.</p>";
      $file_str = 'uchu-forum';
      $org_email_to = $email_uchu_forum;
      $email_sender = $email_sender_uchu_forum;
    } else {
        if ($colloquium_host_org) {
          $organizer_ja = $host_name_ja_sn;
          $organizer_en = $host_name_en;
	} else {
          $organizer_ja = $organizer_colloquium_ja;
          $organizer_en = $organizer_colloquium_en;
	}
      $subject_str = "コロキウム (Colloquium)";
      $email_intro_ja = "<p>" . $date_str_ja . "（{$twdy_ja}）に" . $iaffja . "の " . $iln . " " . $ifn . " 氏に<br/>ご講演していただきます。 講演タイトルおよび概要を下記に記載いたしましたのでご確認ください。</p>";
      $email_intro_en = "<p>We are pleased to announce that a colloquium will be given on " . $date_str_en . " ({$twdy_en}) " . " by " . $ifnr . " " . $ilnr . " from " . $iaffen . ".<br/>Please find the title and abstract of the talk below.</p>" . 
      $file_str = 'colloquium';
      if ($colloquium_host_org) {
        $org_email_to = $email_colloquium . ', ' . $host_email;
      } else {
        $org_email_to = $email_colloquium;
      }
      $email_sender = $email_sender_colloquium;
    }


    # Rename files uploaded by Dropbzone according to whether its a colloquium or an uchu_forum
    # NOTE: If you change anything below, you may need to make the same change in upload.php.
    $ds = DIRECTORY_SEPARATOR;

    $target_dir = 'img';
   
    $today = new DateTime();
    $formattedDate = $today->format('Y-m-d');

    // Loop over multiple files uploaded together
    $targetPath = dirname( __FILE__ ) . $ds . $target_dir . $ds;
    $dir = new DirectoryIterator($targetPath);
    $ifile = 0;
    foreach ($dir as $fileinfo) {
       $tempFname = $fileinfo->getFilename();
       if (!$fileinfo->isDot() && strpos($tempFname, $formattedDate) && str_starts_with($tempFname, "talk-")) {
           $tempFile = $targetPath . $tempFname;
	   $ext = $fileinfo->getExtension();
	   if ($ifile == 0) {
	       $targetFile = $targetPath . $file_str . "-{$date_str}" . ".{$ext}";
	   }
	   else {
	       $targetFile = $targetPath . $file_str . "-{$date_str}-{$ifile}" . ".{$ext}";
	   }
           rename($tempFile, $targetFile);
           $ifile++;
       }
    }

    # Get list of filenames from file written by upload.php
    $img_names = "";
    $iimg = 0;
    $img_list_fp = fopen($target_dir . $ds . "images.txt", "r");

    if ($img_list_fp) {
        while (($buffer = fgets($img_list_fp)) !== false) {
	    if ($iimg == 0) {
                $img_names = $img_names . $buffer;
	    } else {
                $img_names = $img_names . ", " . $buffer;
	    }
	    $iimg++;
        }
        fclose($img_list_fp);
    }
    rename($target_dir . $ds . "images.txt", $target_dir . $ds . "images-bak.txt");


    # Construct email lines and file lines
    $email_astro = "

<p>Remarks by speaker: $itr </p>

<hr>

<p>宇宙観測研究室 / 宇宙物理理論研究室の皆様</p>
<p>(See below for email in English)</p>

<p>筑波大学，宇宙理論研究室の{$organizer_ja}です。</p>

{$email_intro_ja}

<p>計算科学研究センター{$loc_ja}と Zoom のハイブリッド開催となります。</p>

<p>以下 Zoom の情報です。</p>
<dl>
<dt>リンク： https://us02web.zoom.us/j/89630613401?pwd=NXRhU3ZCam9jVmhyY25CbU5ZUjJhdz09</dt>
<dt>ミーティングID： 896 3061 3401</dt>
<dt>パスコード： 189822</dt>
</dl>

<p>講演タイトルおよび概要は以下の通りです。</p>
<hr>
<dl>
<dt>日時： " . $date_str_ja . "（{$twdy_ja}） $ittime ~ </dt>
<dt>場所： " . $loc_ja . "</dt>
<dt>講演者： " . $iln . " " . $ifn . " 氏（" . $iaffja . "） </dt>
<dt>タイトル： " . $itt . "</dt>
</dl>
<div style='display: flex; justify-content: center;'>
<div>概要</div>
</div>
<br />
" . $ita . "
<br />
<br />
<hr>
<br />
<br />


<p>To all members of the Observational and Theoretical Astrophysics Groups,</p>

{$email_intro_en}

<p>The talk will be held in {$loc_en} of the Center for Computational Sciences and will also be streamed via Zoom.</p>

<p>Please find the Zoom details below:</p>
<dl>
<dt>Link: https://us02web.zoom.us/j/89630613401?pwd=NXRhU3ZCam9jVmhyY25CbU5ZUjJhdz09</dt>
<dt>Meeting ID: 896 3061 3401</dt>
<dt>Passcode: 189822</dt>
</dl>

<p>The details of the talk are as follows:</p>
<hr>
<dl>
<dt>Time: " . $date_str_en . " ({$twdy_en})" . " $ittime ~ </dt>
<dt>Place: " . $loc_en . "</dt>
<dt>Presenter: " . $ifnr . " " . $ilnr . " (" . $iaffen . ") </dt>
<dt>Title: " . $itt . "</dt>
</dl>
<div style='display: flex; justify-content: center;'>
<div>Abstract</div>
</div>
<br />
" . $ita . "
<br />
<br />
<hr>
<br />
<br />

";



    $email_speaker = "
<p>$iln 様</p>
<p>講演についてのデータ入力ありがとうございました。
下記のようにデータを受け取りました。</p>

<p>Dear speaker,</p>
<p>Thank you for providing information for your talk.
We have received the following information.</p>

<hr>
<dl>
<dt>・speaker・・・</dt> <dd>$iln $ifn | $ilnr $ifnr </dd><br />
<dt>・affiliation・・・</dt> <dd>$iaffja | $iaffen </dd><br />
<dt>・webpage・・・</dt> <dd>$ih </dd><br />
<dt>・date・・・</dt> <dd>$date_str （{$twdy_ja} | {$twdy_en}）</dd><br />
<dt>・time・・・</dt> <dd>$ittime </dd><br />
<dt>・place・・・</dt> <dd>$loc_ja | $loc_en </dd><br />
<dt>・host・・・</dt> <dd>$host_name_ja | $host_name_en </dd><br />
<dt>・talk category・・・</dt> <dd>$ity </dd><br />
<dt>・title・・・</dt> <dd>$itt </dd><br />
<dt>・keywords・・・</dt> <dd>$itkw1, $itkw2, $itkw3 </dd><br />
<dt>・abstract・・・</dt> <dd>$ita </dd><br />
<dt>・remarks・・・</dt> <dd>$itr </dd><br />
<dt>・images upladed・・・</dt> <dd>$img_names </dd>
</dl>
<hr>
&nbsp;

";


    $file_ja = "---
title: $itt
speaker: $iln $ifn 氏
affil: $iaffja
webpage: \"$ih\"
date: $date_str
time: \"$ittime\" # Must use quotes
place: $loc_ja
host: $host_name_ja_nsp
lang: ja
tags: [$itkw1, $itkw2, $itkw3]
#pdf: 
img_thumb: {$file_str}-{$date_str}-thumb.jpg
img:
  - {$file_str}-{$date_str}.jpg
remarks: $itr
categories:
  - $ttype_ids[$ity]
  - ja
---

$ita

";

    $file_en = "---
title: $itt
speaker: $ifnr $ilnr
affil: $iaffen
webpage: \"$ih\"
date: $date_str
time: \"$ittime\" # Must use quotes
place: $loc_en
host: $host_name_en
lang: en
tags: [$itkw1, $itkw2, $itkw3]
#pdf: 
img_thumb: {$file_str}-$date_str-thumb.jpg
img:
  - {$file_str}-$date_str.jpg
remarks: $itr
categories:
  - $ttype_ids[$ity]
  - en
---

$ita

";


    # Bulk replacements

    # Fix html http -> https
    $email_astro = str_replace("http:", "https:", $email_astro);
    $email_speaker = str_replace("http:", "https:", $email_speaker);
    $file_ja = str_replace("http:", "https:", $file_ja);
    $file_en = str_replace("http:", "https:", $file_en);

    # Unify commas and full stops.
    $email_astro = str_replace("、", "，", $email_astro);
    $email_speaker = str_replace("、", "，", $email_speaker);
    $file_ja = str_replace("、", "，", $file_ja);
    $file_en = str_replace("、", "，", $file_en);

    $email_astro = str_replace("．", "。", $email_astro);
    $email_speaker = str_replace("．", "。", $email_speaker);
    $file_ja = str_replace("．", "。", $file_ja);
    $file_en = str_replace("．", "。", $file_en);

    # Email to organizer/host

    mb_language("neutral");  
    $formcontent = "$email_astro";
    if ($test) {
      $recipient = $email_recipient_tester;
    } else {
      $recipient = $org_email_to;
    }
    $subject = $subject_str . "：" . $date_str_ja . "（{$twdy_ja}） $ittime";
    $mailheader  = "MIME-Version: 1.0" . "\r\n";
    $mailheader .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $mailheader .= "From: {$email_mailagent} \r\n";
    $mailheader .= "Sender: {$email_mailagent} \r\n";
    $mailheader .= "X-Mailer: PHP/" . phpversion();
    mb_send_mail($recipient, $subject, $formcontent, $mailheader);

    # Email to speaker
    $formcontent = "$email_speaker";
    if ($test) {
      $recipient = $email_recipient_tester;
    } else {
      $recipient = $ie;
    }
    $subject = $subject_str . " " . $date_str . "（{$twdy_ja} | {$twdy_en}） $ittime";
    $mailheader  = "MIME-Version: 1.0" . "\r\n";
    $mailheader .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $mailheader .= "From: {$email_sender} \r\n";
    $mailheader .= "Sender: {$email_mailagent} \r\n";
    $mailheader .= "X-Mailer: PHP/" . phpversion();
    mb_send_mail($recipient, $subject, $formcontent, $mailheader);

    # Write files
    $fcon = fopen($fname_yml_ja, 'w');
    fwrite($fcon, $file_ja);
    fclose($fcon);

    $fcon = fopen($fname_yml_en, 'w');
    fwrite($fcon, $file_en);
    fclose($fcon);


    } else {

    # Not recaptcha verified

    }

} 
?>
