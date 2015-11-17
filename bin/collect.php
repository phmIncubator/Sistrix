<?php

$apiKey = "KAWx97vR2C5GIN4NENanKWWzVrASAfKz";

include_once __DIR__."/../vendor/autoload.php";

$retriever = new \phmLabs\Sistrix\Retriever($apiKey);

$index = $retriever->getSichtbarkeitsIndex("www.bravo.de");

var_dump($index);
