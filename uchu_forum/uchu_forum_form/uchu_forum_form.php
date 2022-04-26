<?php


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

    # Some email addresses
    $email_uchu_forum = "utsumi@ccs.tsukuba.ac.jp";
    $email_sender = 'ayw@ccs.tsukuba.ac.jp';

    # Location
    $loc_en = "Online";
    $loc_ja = "オンライン開催";
    #$loc_en = "CCS Workshop room";
    #$loc_ja = "CCS ワークショップ室";
    $twdy_ja = "火";
    $twdy_en = "Tue";

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
    $itdate = $_POST['InputTalkDay'];
    $itkw1 = $_POST['InputTalkKw1'];
    $itkw2 = $_POST['InputTalkKw2'];
    $itkw3 = $_POST['InputTalkKw3'];

    # Translating inputted talk types to category names
    $ttype_ids = array(
      '宇宙フォーラム' => 'uchu_forum', 
      'コロキウム' => 'colloquium', 
      'Uchu Forum' => 'uchu_forum', 
      'Colloquium' => 'colloquium', 
    );

    # Create filenames
    $fbase = '../../uchu_forum_form_data/';  // For site on charon
    // $fbase = '../../../uchu_forum_form_data/';  // For my LAMPP setup
    $date_str = str_replace("/", "-", $itdate);
    $fname_yml_ja = $fbase . 'ja/' . $date_str . '-' . strtolower($ilnr) . '.html';
    $fname_yml_en = $fbase . 'en/' . $date_str . '-' . strtolower($ilnr) . '.html';
    
    $host_bl = explode(" | ", $ish);
    $host_ja = $host_bl[0];
    $host_en = $host_bl[1];
    # Create position string for students
    # Just keeping for reference
    #$pos = preg_match('/^[DMY][0-5]/', $iry) ? $iry : "";
    #$pos = preg_match('/研究生/u', $iry) ? "研究生" : $pos;

    # Replace underscores in emails (assume this is the only special character in email addresses) - keeping as reference
    #$ie1 = str_replace("_", "\\_", $ie1);
    #$ie2 = str_replace("_", "\\_", $ie2);

    # TODO: Test whether replacements are working
    # Replace latex characters that need escaping in address
    #$ia = escape_all_latex_special_chars($ia);

    # TODO: make English and Japanese yamllines. 
    # TODO: Add - lang: ja  and - lang: en to the yamllines. 

    # Date string
    $date_str_ja = preg_replace('/[0-9][0-9][0-9][0-9]-/u', '', $date_str);
    $date_str_ja = str_replace("-", "月", $date_str_ja) . "日";
    
    # Construct email lines and file lines
    $email_astro = "
宇宙部塵理論研究室の皆様

筑波大学、宇宙理論研究室の内海です。
今月の宇宙フォーラムは、" . $iaffja . "の " . $iln . " " . $ifn . " 氏にご講演していただきます。
講演タイトルおよび概要を下記に記載いたしましたのでご確認ください。

Zoom を用いたリモート開催となります。
以下、Zoom の情報です。

【Zoom 情報】
https://us02web.zoom.us/j/89630613401pwd=NXRhU3ZCam9jVmhyY25CbU5ZUjJhdz09
ミーティングID: 896 3061 3401
パスコード: 189822

講演タイトルおよび概要は以下の通りです。

------------------------------------------------------------------------------------------------
日時：" . $date_str_ja . "（{$twdy_ja}） $ittime ~
講演者：  " . $iln . " " . $ifn . " 氏（" . $iaffja . "）
タイトル： " . $itt ."
概要：
" . $ita ."
---------------------------------------------------------------------------------------------
";

    $email_speaker = "
title: $itt
speaker: $iln $ifn | $lnr $ifnr
affil: $iaffja | $iaffen
webpage: $ih
date: $date_str （$twdy_ja | $tdwy_en）
time: $ittime
place: $loc_ja | $loc_en
host: $ish
tags: $itkw1, $itkw2, $itkw3
images: 
talk category: $ity

";


    $file_ja = "---
title: $itt
speaker: $iln $ifn 氏
affil: $iaffja
webpage: \"$ih\"
date: $date_str
time: \"$ittime\" # Must use quotes
place: $loc_ja
host: $host_ja
lang: ja
tags: [$itkw1, $itkw2, $itkw3]
#pdf: 
img_thumb: uchu-forum-$date_str-thumb.jpg
img:
  - uchu-forum-$date_str.jpg
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
host: $host_en
lang: en
tags: [$itkw1, $itkw2, $itkw3]
#pdf: 
img_thumb: uchu-forum-$date_str-thumb.jpg
img:
  - uchu-forum-$date_str.jpg
categories:
  - $ttype_ids[$ity]
  - en
---

$ita

";

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

    # Email 
    $formcontent = "$email_astro";
    $recipient = 'astro.ccs.tsukuba@gmail.com';
    #$recipient = $email_uchu_forum;
    $subject = "今月の宇宙フォーラム：" . $date_str_ja . "（{$twdy_ja}） $ittime";
    $mailheader = "From: $email_sender \n";
    mail($recipient, $subject, $formcontent, $mailheader);

    # Email 
    $formcontent = "$email_speaker";
    $recipient = 'astro.ccs.tsukuba@gmail.com';
    #$recipient = $ie;
    $subject = "宇宙フォーラム Uchu forum " . $date_str_ja . "（$twdy_ja | $twdy_en） $ittime";
    $mailheader = "From: $email_sender \n";
    mail($recipient, $subject, $formcontent, $mailheader);

    # Write files
    $fcon = fopen($fname_yml_ja, 'w');
    fwrite($fcon, $file_ja);
    fclose($fcon);

    $fcon = fopen($fname_yml_en, 'w');
    fwrite($fcon, $file_en);
    fclose($fcon);


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
