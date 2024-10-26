<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-credit/utils.php";

$address = get_required(address);
$answers = get_required(answers);

$response[rating] = rating($answers, $address);
$response[multiplier] = 1;
$response[period] = 100;
$response[percent] = 0.1;
$response[amount] = $response[rating] * $response[multiplier];

commit($response);