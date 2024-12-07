<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-bank/utils.php";

$domain = get_required(domain);
$address = get_required(address);
$pass = get_required(pass);

$response[unstaked] = unstake($domain, $address, $pass);

commit($response);