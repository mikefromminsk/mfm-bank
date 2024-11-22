<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-db/params.php";

$lang = get_required(lang, "en");

echo file_get_contents("quiz/$lang.json");