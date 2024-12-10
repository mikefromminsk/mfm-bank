<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-bank/utils.php";

$domain = get_required(domain);
$amount = get_int_required(amount);
$pass = get_required(pass);
$invite_next_hash = get_required(invite_next_hash);
$gas_address = get_required(gas_address);


if (dataExist([$domain, invite, $invite_next_hash])) error("bonus exist");

tokenRegScript($domain, share, "$domain/api/share/receive.php");

tokenSend($domain, $gas_address, share, $amount, $pass);

dataSet([$domain, share, $invite_next_hash, amount], $amount);
commit();
