<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-bank/utils.php";

$withdrawal_history = dataHistory([usdt, withdrawal, history]);

$response[result] = [];

foreach ($withdrawal_history as $withdrawal_id) {
    if (dataGet([usdt, withdrawal, $withdrawal_id, txid]) == null) {
        $response[result][] = [
            withdrawal_id => $withdrawal_id,
            withdrawal_address => dataGet([usdt, withdrawal, $withdrawal_id, withdrawal_address]),
            amount => doubleval(dataGet([usdt, withdrawal, $withdrawal_id, amount])),
            username => dataGet([usdt, withdrawal, $withdrawal_id, username]),
            provider => dataGet([usdt, withdrawal, $withdrawal_id, provider]),
            time => dataInfo([usdt, withdrawal, $withdrawal_id])[data_time],
        ];
    }
}

commit($response);