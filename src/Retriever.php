<?php
/**
 * Created by PhpStorm.
 * User: nils.langner
 * Date: 17.11.15
 * Time: 08:22
 */

namespace phmLabs\Sistrix;


class Retriever
{
    private $apiKey;
    private $format;

    private $sichtbarkeitsIndexUrl = 'http://api.sistrix.net/domain.sichtbarkeitsindex?api_key=#apiKey#&domain=#domain#&format=#format#';

    public function __construct($apiKey, $format = "json")
    {
        $this->apiKey = $apiKey;
        $this->format = $format;
    }

    private function getUrl($url, array $parameters)
    {
        $parameters["apiKey"] = $this->apiKey;
        $parameters["format"] = $this->format;

        foreach($parameters as $key => $parameter) {
            $url = str_replace('#' . $key . '#',$parameter, $url);
        }

        return $url;
    }

    public function getSichtbarkeitsIndex($domain)
    {
        $url = $this->getUrl($this->sichtbarkeitsIndexUrl, array('domain' => $domain));

        $content = json_decode(file_get_contents($url));

        return $content->answer[0]->sichtbarkeitsindex[0]->value;
    }
}