<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-db/params.php";

$redirect = get_required(redirect);

if (
    $redirect != "/mfm-bank/credit.php" &&
    $redirect != "/mfm-bank/deposit/check.php"
) {
    error("Invalid redirect");
}

require_once $_SERVER["DOCUMENT_ROOT"] . $redirect;