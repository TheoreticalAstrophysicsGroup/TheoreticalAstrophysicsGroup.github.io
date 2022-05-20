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

    # Some settings

    # Organizer, sender
    $organizer_uchu_forum = "内海";
    $email_uchu_forum = "utsumi@ccs.tsukuba.ac.jp";
    $email_sender = 'ayw@ccs.tsukuba.ac.jp';

    # Location
    $loc_en = "Online";
    $loc_ja = "オンライン開催";
    #$loc_en = "CCS Workshop room";
    #$loc_ja = "CCS ワークショップ室";
    $twdy_ja = "火";
    $twdy_en = "Tue";

    # Translating inputted talk types to category names
    $ttype_ids = array(
      '宇宙フォーラム' => 'uchu_forum', 
      'コロキウム' => 'colloquium', 
      'Uchu Forum' => 'uchu_forum', 
      'Colloquium' => 'colloquium', 
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

    # TODO: Use DeepL to get English titles and abstracts, and other stuff, if missing.

    # Defaults for Japanese names when not given (e.g. in English form, they are optional)
    if (empty($iln)) $iln = $ilnr;
    if (empty($ifn)) $iln = $ifnr;
    if (empty($iaffja)) $iaffja = $iaffen;

    # Create filenames
    $fbase = '../../uchu_forum_form_data/';  // For site on charon
    $date_str = str_replace("/", "-", $itdate);
    $fname_yml_ja = $fbase . 'ja/' . $date_str . '-' . strtolower($ilnr) . '.html';
    $fname_yml_en = $fbase . 'en/' . $date_str . '-' . strtolower($ilnr) . '.html';
    
    # English and Japanese host names
    $host_bl = explode(" | ", $ish);
    $host_ja = $host_bl[0];
    $host_en = $host_bl[1];

    # Titles sometimes contain colons which we cannot have.
    $itt = str_replace(":", "&#58;", $itt);

    # Create position string for students
    # Just keeping for reference
    #$pos = preg_match('/^[DMY][0-5]/', $iry) ? $iry : "";
    #$pos = preg_match('/研究生/u', $iry) ? "研究生" : $pos;

    # Date string
    $date_str_ja = date("n月j日",strtotime($date_str));
 
    # Construct email lines and file lines
    $email_astro = "

<p>Remarks by speaker: $itr </p>

<hr>

<p>宇宙部塵理論研究室の皆様</p>

<p>筑波大学，宇宙理論研究室の{$organizer_uchu_forum}です。</p>

<p>今月の宇宙フォーラムは，" . $iaffja . "の " . $iln . " " . $ifn . " 氏に<br/>ご講演していただきます。
講演タイトルおよび概要を下記に記載いたしましたのでご確認ください。</p>

<p>Zoom を用いたリモート開催となります。 以下Zoom の情報です。</p>
<dl>
<dt>リンク： https://us02web.zoom.us/j/89630613401pwd=NXRhU3ZCam9jVmhyY25CbU5ZUjJhdz09</dt>
<dt>ミーティングID： 896 3061 3401</dt>
<dt>パスコード： 189822</dt>
</dl>

<p>講演タイトルおよび概要は以下の通りです。</p>

<hr>
<dl>
<dt>日時： " . $date_str_ja . "（{$twdy_ja}） $ittime ~ </dt>
<dt>講演者： " . $iln . " " . $ifn . " 氏（" . $iaffja . "） </dt>
<dt>タイトル： " . $itt . "</dt>
</dl>

<div style='display: flex; justify-content: center;'>
<div>概要</div>
</div>
<br />
" . $ita . "
<hr>
";

    $email_speaker = "
<p>$iln 様</p>
<p>宇宙フォーラムのデータ入力ありがとうございました。
下記のようにデータを受け取りました。</p>

<p>Dear speaker,</p>
<p>Thank you for providing information for your Uchu-forum talk.
We have received the following information.</p>

<hr>
<dl>
<dt>speaker:</dt> <dd>$iln $ifn | $ilnr $ifnr </dd>
<dt>affiliation:</dt> <dd>$iaffja | $iaffen </dd>
<dt>webpage:</dt> <dd>$ih </dd>
<dt>date:</dt> <dd>$date_str （{$twdy_ja} | {$twdy_en}）</dd>
<dt>time:</dt> <dd>$ittime </dd>
<dt>place:</dt> <dd>$loc_ja | $loc_en </dd>
<dt>host:</dt> <dd>$ish </dd>
<dt>talk category:</dt> <dd>$ity </dd>
<dt>title:</dt> <dd>$itt </dd>
<dt>keywords:</dt> <dd>$itkw1, $itkw2, $itkw3 </dd>
<dt>abstract:</dt> <dd>$ita </dd>
<dt>images:</dt> <dd></dd>
<dt>remarks:</dt> <dd>$itr </dd>
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
host: $host_ja
lang: ja
tags: [$itkw1, $itkw2, $itkw3]
#pdf: 
img_thumb: uchu-forum-{$date_str}-thumb.jpg
img:
  - uchu-forum-{$date_str}.jpg
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
host: $host_en
lang: en
tags: [$itkw1, $itkw2, $itkw3]
#pdf: 
img_thumb: uchu-forum-$date_str-thumb.jpg
img:
  - uchu-forum-$date_str.jpg
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

    # Email 
    $formcontent = "$email_astro";
    $recipient = "astro.ccs.tsukuba@gmail.com";
    #$recipient = $email_uchu_forum;
    $subject = "今月の宇宙フォーラム：" . $date_str_ja . "（{$twdy_ja}） $ittime";
    $mailheader  = "MIME-Version: 1.0" . "\r\n";
    $mailheader .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $mailheader .= "From: $email_sender \r\n";
    $mailheader .= "X-Mailer: PHP/" . phpversion();
    mail($recipient, $subject, $formcontent, $mailheader);

    # Email 
    $formcontent = "$email_speaker";
    $recipient = "astro.ccs.tsukuba@gmail.com";
    #$recipient = $ie;
    $subject = "宇宙フォーラム Uchu forum " . $date_str_ja . "（{$twdy_ja} | {$twdy_en}） $ittime";
    $mailheader  = "MIME-Version: 1.0" . "\r\n";
    $mailheader .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $mailheader .= "From: $email_sender \r\n";
    $mailheader .= "X-Mailer: PHP/" . phpversion();
    mail($recipient, $subject, $formcontent, $mailheader);

    # Write files
    $fcon = fopen($fname_yml_ja, 'w');
    fwrite($fcon, $file_ja);
    fclose($fcon);

    $fcon = fopen($fname_yml_en, 'w');
    fwrite($fcon, $file_en);
    fclose($fcon);

    # Image upload DropzoneJS
    $ds = DIRECTORY_SEPARATOR;
 
    $target_dir = 'img';
 
    if (!empty($_FILES)) {
        $tempFile = $_FILES['file']['tmp_name'];
        $targetPath = dirname( __FILE__ ) . $ds. $target_dir . $ds;
        $targetFile =  $targetPath. $_FILES['file']['name'];
        move_uploaded_file($tempFile, $targetFile);
    }


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
