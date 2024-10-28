<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-bank/utils.php";

onlyInDebug();

$address = get_required(wallet_admin_address);
$password = get_required(wallet_admin_password);

$gas_domain = get_required(gas_domain);
$usd_domain = get_required(usd_domain);
$bank_address = get_required(bank_address);

tokenRegAccount($usd_domain, $address, $password);

$amount = tokenBalance($gas_domain, $address);

tokenRegScript($usd_domain, $bank_address, "mfm-bank/owner.php");
tokenRegScript($gas_domain, $bank_address, "mfm-bank/owner.php");

tokenSendAndCommit($gas_domain, $address, $bank_address, $amount, $password);

commit();
