<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-bank/utils.php";

$address = get_required(address);
$answers = get_required(answers);
$answers = json_decode($answers, true);
$pass = get_required(pass);

// test что не был уже ранее выдан кредит
$rating = rating($answers, $address);
tokenSend(gas_domain, credit_address, $address, $rating);

tokenDelegate(gas_domain, $address, $pass, "mfm-exchange/owner.php");

$response[rating] = $rating;

commit($response);