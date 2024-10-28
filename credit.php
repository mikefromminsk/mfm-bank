<?php
require_once $_SERVER[DOCUMENT_ROOT] . "/mfm-token/utils.php";

$address = get_required(address);
$pass = get_required(pass);
$answers = get_required(answers);
$gas_domain = get_required(gas_domain);
$credit_address = get_config_required(credit_address);
$credit_multiplier = get_config_required(credit_multiplier);
$credit_percent = get_config_required(credit_percent);

$rating = rating($answers, $address);
tokenDelegate($gas_domain, $address, $pass, getScriptPath());
tokenSend($gas_domain, $credit_address, $address, $rating * $credit_multiplier);

commit();