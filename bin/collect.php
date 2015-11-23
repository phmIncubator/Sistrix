<?php

$apiKey = "KAWx97vR2C5GIN4NENanKWWzVrASAfKz";

include_once __DIR__ . "/../vendor/autoload.php";

$retriever = new \phmLabs\Sistrix\Retriever($apiKey);

$url = $argv[1];
$percentage = $argv[2];

$index = $retriever->getSichtbarkeitsIndex($url);

$filename = str_replace(".", "_", $url);
$filename = str_replace("http://", "", $filename);
$filename = str_replace("/", "_", $filename);

$filePath = __DIR__ . "/../archive/" . $filename;

if (file_exists($filePath)) {
    $lastRun = doubleval(file_get_contents($filePath));
} else {
    $lastRun = $index;
}

$minValue = $lastRun * ((100 - $percentage) / 100);

if ($index < $minValue) {
    $status = IncidentReporter::STATUS_FAILURE;
    $message = "The Sistrix 'Sichtbarkeitsindex' of '$url' sank by more than $percentage percent.";
} else {
    $status = IncidentReporter::STATUS_SUCCESS;
    $message = "";
}

$normalziedUrl = str_replace("http://", "", $url);

$reporter = new IncidentReporter();
$err = $reporter->send("sistrix_sichtbarskeitsindex" . $normalziedUrl, $normalziedUrl, "sistrix", $message, $status);

class IncidentReporter
{
    private $koaloMon = "http://dashboard.phmlabs.com/webhook/";

    const STATUS_SUCCESS = "success";
    const STATUS_FAILURE = "failure";

    public function send($identifier, $system, $tool, $message, $status)
    {
        $curl = curl_init();
        $responseBody = array(
            'system' => str_replace("http://", '', $system),
            'status' => $status,
            'message' => $message,
            'identifier' => $identifier,
            'type' => $tool
        );
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->koaloMon,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($responseBody),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return $err;
    }
}