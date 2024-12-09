<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-data/utils.php";

const credit_address = 'bank';
const credit_percent = 7;
const credit_period_days = 30;

const staking_address = 'staking';
const staking_percent = 7;
const staking_period_days = 30;


const PROVIDERS = [
    "BSC" => [
        name => "BSC",
        title => "BSC BEP-20",
        contract => '0x55d398326f99059ff775485246999027b3197955',
        min_deposit => 5,
        fee => 1,
        deposit_addresses => [
            "0x1e0426Ba2E77eDdf7FfB19C57B992c4dcC6455F4",
        ],
        deadline_interval => 60 * 30,
    ],
    "TRON" => [
        name => "TRON",
        title => "Tron TRC-20",
        min => 0.02,
        contract => 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t',
        min_deposit => 5,
        fee => 1,
        deposit_addresses => [
            "TPWZ6TNgYBCh18Bf4EVfKesoHHRJ4w8SgT",
            "TSXvWWCsysLQoPujPCEbYQySXP66ZvN57b",
        ],
        deadline_interval => 60 * 30,
    ],
];

function findInArray($array, $key, $value)
{
    foreach ($array as $item) {
        if ($item[$key] == $value) {
            return $item;
        }
    }
    return null;
}

function rating($answers, $address = null)
{
    $rating = 0;
    for ($i = 0; $i < sizeof($answers); $i++) {
        $answer = $answers[$i];
        if ($answer[answer] == $answer[correct]) {
            $rating += $i + 1;
        }
    }
    //trackEvent("mfm-credit", $quiz[level], $address, $question[question], $question[answer] == $answer[answer]);
    return $rating;
}


function unstake($domain, $address, $pass)
{
    $last_tran = tokenLastTran($domain, $address, staking_address);
    $unstaked = 0;
    if ($last_tran[to] == staking_address) {
        /*if (time() - $last_tran[time] > staking_period_days * 24 * 60 * 60)*/ {
            $unstaked = round($last_tran[amount] * (1 + staking_percent / 100), 2);
        }
        tokenChangePass($domain, $address, $pass);
        tokenSend($domain, staking_address, $address, $unstaked);
    }
    return $unstaked;
}


function stake($domain, $address, $amount, $pass)
{
    $last_tran = tokenLastTran($domain, staking_address, $address);
    if ($last_tran[to] == staking_address) error("You need to unstake first");
    $next_pay_off = round($amount * (1 + staking_percent / 100), 2);
    if (tokenBalance($domain, staking_address) < $next_pay_off) error("Not enough funds in staking address");
    return tokenSend($domain, $address, staking_address, $amount, $pass);
}


function staked($address, $count = 10)
{
    $stake_trans = tokenTrans(null, $address, staking_address, 0, 50);
    $staked = [];
    foreach ($stake_trans as $tran) {
        if ($staked[$tran[domain]] == null) {
            $tran[percent] = staking_percent;
            $tran[period_days] = staking_period_days;
            $staked[$tran[domain]] = $tran;
        }
    }
    $staked = array_values($staked);
    return array_filter($staked, function ($tran) {
        return $tran[to] == staking_address;
    });
}