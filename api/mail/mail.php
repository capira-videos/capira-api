<?php

$name = clean_string($_POST['name']);
$title = clean_string($_POST['title']);
$school = clean_string($_POST['school']);
$customerEmail = clean_string($_POST['email']);
$lang = clean_string($_POST['lang']);
$startDate = clean_string($_POST['startDate']);
$questions = clean_string($_POST['questions']);

$serviceEmail = "robin_woll@capira.de";

$betreff = "Capira Licence";

$header = "Return-Path: " . $customerEmail . "\r\n MIME-Version: 1.0\r\n";
$header .= "Content-type: text/html; charset=iso-8859-1\r\n";
$header .= "From: $customerEmail\r\n";
$header .= "Reply-To: $customerEmail\r\n";
$header .= "X-Mailer: Microsoft Office Outlook 12.0";

$mailtext = "name: " . $name . "\r\n";
$mailtext .= "title: " . $title . "\r\n";
$mailtext .= "school: " . $school . "\r\n";
$mailtext .= "lang: " . $lang . "\r\n";
$mailtext .= "startDate: " . $startDate . "\r\n";
$mailtext .= "questions: " . $questions . "\r\n";

mail($serviceEmail, $betreff, $mailtext, $header);

echo "Mail wurde gesendet!";
?>