<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-bank/utils.php";

$address = get_required(address);
$answers = get_required(answers);
$answers = json_decode($answers, true);
$pass = get_required(pass);

$gas_domain = get_required(gas_domain);
$bank_address = get_required(bank_address);
$credit_percent = get_required(credit_percent);

// test что не был уже ранее выдан кредит
$rating = rating($answers, $address);
tokenSend($gas_domain, $bank_address, $address, $rating);

tokenDelegate($gas_domain, $address, $pass, "mfm-exchange/owner.php");

$response[rating] = $rating;

commit($response);