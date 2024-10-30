<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-bank/utils.php";

$address = get_required(address);
$answers = get_required(answers);
$answers = json_decode($answers, true);

$credit_percent = get_required(credit_percent);

$response[rating] = rating($answers, $address);
$response[percent] = $credit_percent;

$response[answers] = $answers;

commit($response);