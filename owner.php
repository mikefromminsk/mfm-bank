<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-db/params.php";

$redirect = get_required(redirect);

if ($redirect != "mfm-exchange/owner.php") {
    error("Invalid redirect");
}

// token send amount = 0 and delegate is empty

require_once $_SERVER["DOCUMENT_ROOT"] . "/$redirect";