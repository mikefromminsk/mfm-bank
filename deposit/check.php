<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-bank/utils.php";

$deposit_address = get_required(deposit_address);
$chain = get_required(chain);
$token = get_required(gas_domain);

$gas_domain = get_required(gas_domain);
$credit_address = get_required(bank_address);
$credit_percent = get_required(credit_percent);

//if (!dataExist([usdt, deposit, $deposit_address])) error("deposit address is not exist");

$deposit_start_event = getEvent(deposit_start, $chain, $deposit_address, null, $token);
if ($deposit_start_event == null) error("address is null");

//if ($deadline < time()) error("deposit time is finished");

function transactionFormat($amount, $timestamp, $txid)
{
    return [
        "amount" => $amount,
        "block_ts" => $timestamp,
        "transaction_id" => $txid,
    ];
}

function usdtTrc20Transactions($address)
{
    $response = [];
    $provider = PROVIDERS["TRON"];
    $trans_response = http_get_json("https://apilist.tronscanapi.com/api/new/token_trc20/transfers"
        . "?limit=20&start=0"
        . "&contract_address=$provider[contract]"
        . "&toAddress=$address");
    foreach ($trans_response["token_transfers"] as $trans) {
        if ($trans["finalResult"] == "SUCCESS") {
            $response[] = transactionFormat(
                $trans["quant"] / 1000000,
                ceil($trans["block_ts"] / 10000),
                $trans["transaction_id"],
            );
        }
    }
    return $response;
}

function usdtBep20Transactions($address)
{
    $response = [];
    $provider = PROVIDERS["BSC"];
    $trans_response = http_get_json("https://api.bscscan.com/api"
        . "?module=account&action=tokentx&startblock=0&endblock=99999999&page=1&offset=10&sort=asc"
        . "&address=$address"
        . "&contractaddress=$provider[contract]"
        . "&apikey=" . $GLOBALS[bscscan_api]);
    foreach ($trans_response["result"] as $trans) {
        $response[] = transactionFormat(
            $trans["value"] / 1000000000000000000,
            ceil($trans["timeStamp"]),
            $trans["hash"],
        );
    }
    return $response;
}

if ($chain == "TRON"){
    $trans = usdtTrc20Transactions($deposit_address);
} else if ($chain == "BSC") {
    $trans = usdtBep20Transactions($deposit_address);
    $response[trans] = $trans;
}

$deposited = 0;

$deposit_success_event = getEvent(deposit_success, $chain, $deposit_address, null, $token);
$last_deposited_time = $deposit_success_event[time] ?: 0;
foreach ($trans as $tran) {
    if ($tran[block_ts] > $last_deposited_time) {
        $deposited += $tran[amount];
    }
}

if ($deposited > 0) {
    $deposited = round(floor($deposited * 100) / 100, 2);
    tokenSend($token, $credit_address, $deposit_start_event[to], $deposited);
    //trackEvent(deposit_success, $chain, $deposit_address, $deposit_start_event[to], $token, $deposited);
}

$response[last_block_ts] = $last_deposited_time;
$response[deposited] = $deposited;

commit($response);