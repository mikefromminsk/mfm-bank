<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-bank/utils.php";

$address = get_required(address);
$chain = get_required(chain);
$token = usdt;

$provider = PROVIDERS[$chain];

$time = time();

// find last blocked deposit address
$event = getEvent($chain, null, $address, $token, deposit_start);

if ($event != null && $time - $event[time] < $provider[deadline_interval]) {
    $deposit_address = $event[from_id];
}

if ($deposit_address == null) {
    // get free address
    foreach ($provider[deposit_addresses] as $token_deposit_address) {
        if ($time - getEvent($chain, $token_deposit_address, null, $token, deposit_start)[time] < $provider[deadline_interval]) continue;
        $deposit_address = $token_deposit_address;
        break;
    }
    if ($deposit_address == null) error("all addresses are busy");
    trackEvent($chain, $deposit_address, $address, $token, deposit_start);
}

$response[deadline] = getEvent($chain, $deposit_address, $address, $token, deposit_start)[time] + $provider[deadline_interval];
$response[deposit_address] = $deposit_address;
$response[min_deposit] = $provider[min_deposit];
$response[success] = true;

commit($response);