<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-data/utils.php";

const credit_address = 'bank';
const credit_percent = 7;
const credit_pay_off_period_days = 30;

const staking_address = 'staking';
const staking_base_percent = 7;
const staking_pay_off_period_days = 30;


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
    $staking_address = get_required(staking_address);
    $staking_base_percent = get_required(staking_base_percent);
    $staking_pay_off_period_days = get_required(staking_pay_off_period_days);

    $last_tran = tokenLastTran($domain, $staking_address, $address);

    $unstaked = 0;
    if ($last_tran[to] == $staking_address) {
        if (time() - $last_tran[time] > $staking_pay_off_period_days * 24 * 60 * 60) {
            $unstaked = round($last_tran[amount] * (1 + $staking_base_percent / 100), 2);
            tokenChangePass($domain, $address, $pass);
            tokenSend($domain, $staking_address, $address, $unstaked);
        }
    }
    return $unstaked;
}


function stake($domain, $address, $amount, $pass)
{
    $staking_address = get_required(staking_address);
    $staking_base_percent = get_required(staking_base_percent);

    $last_tran = tokenLastTran($domain, $staking_address, $address);
    if ($last_tran[to] == $staking_address) error("You need to unstake first");

    $next_pay_off = round($amount * (1 + $staking_base_percent / 100), 2);
    if (tokenBalance($domain, $staking_address) < $next_pay_off) error("Not enough funds in staking address");

    return tokenSend($domain, $address, $staking_address, $amount, $pass);
}


function staked($address)
{
    $staking_address = get_required('staking_address');
    return select("select * from trans t1"
        . " left join tokens t2 on t1.`domain` = t2.`domain`"
        . " where (`from` = '$address' or `to` = '$address')"
        . " and (`from` = '$staking_address' or `to` = '$staking_address')"
        . " group by t1.`domain` HAVING `time` = MAX(`time`) order by `time` desc") ?: [];
}