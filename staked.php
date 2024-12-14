<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-bank/utils.php";

$address = get_required(address);

$response[staked] = staked($address);
$response[percent] = staking_percent;
$response[period_days] = staking_period_days;

commit($response);