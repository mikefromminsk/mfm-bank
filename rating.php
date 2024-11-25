<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-bank/utils.php";

$address = get_required(address);
$answers = get_required(answers);
$answers = json_decode($answers, true);

$credit_percent = get_required(credit_percent);
$pay_off_period_days = get_required(pay_off_period_days);

$response[rating] = rating($answers, $address);
$response[percent] = $credit_percent;
$response[pay_off_period_days] = $pay_off_period_days;

$response[answers] = $answers;

commit($response);