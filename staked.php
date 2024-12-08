<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-bank/utils.php";

$address = get_required(address);

$response[staked] = staked($address);

commit($response);