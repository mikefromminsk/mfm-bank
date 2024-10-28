<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-bank/utils.php";

$address = get_required(address);
$pass = get_required(pass);
$answers = get_required(answers);

$gas_domain = get_required(gas_domain);
$usd_domain = get_required(usd_domain);
$bank_address = get_required(bank_address);
$credit_percent = get_required(credit_percent);

// test что не был уже ранее выдан кредит
$rating = rating($answers, $address);
tokenDelegate($gas_domain, $address, $pass, "mfm-bank/owner.php");
tokenSend($gas_domain, $bank_address, $address, $rating);

commit();