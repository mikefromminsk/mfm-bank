<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-bank/utils.php";

$domain = get_required(domain);
$amount = get_required(amount);
$address = get_required(address);
$pass = get_required(pass);

$response[next_hash] = stake($domain, $address, $amount, $pass);
$response[staked] = $amount;

commit($response);