<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-data/utils.php";

$usd_domain = 'usdt';

$bank_address = 'bank';
$credit_percent = 7;

function findInArray($array, $key, $value)
{
    foreach ($array as $item) {
        if ($item[$key] == $value) {
            return $item;
        }
    }
    return null;
}

function rating($answers, $address = null)
{
    $quiz = json_decode(file_get_contents("quiz.json"), true);
    $passedLevels = 0;
    foreach ($answers as $answer) {
        if (!isset($answer[level])) continue;
        if (!isset($answer[question])) continue;
        if (!isset($answer[answer])) continue;
        $question = findInArray($quiz, "level", $answer[level]);
        if ($question == null) continue;
        $question = findInArray($question[questions], "question", $answer[question]);
        if ($question == null) continue;
        if ($question[answer] == $answer[answer]) {
            $levelIndex = array_search($question, $quiz);
            $passedLevels += $levelIndex + 1;
        }
        if ($address != null) {
            trackEvent("mfm-credit", $quiz[level], $address, $question[question], $question[answer] == $answer[answer]);
        }
    }
    return $passedLevels;
}