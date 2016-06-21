<?php

require_once("Config.php");


class Sedori{

    private $amazonAPIParams    = array();
    private $amazonHTTPQuery    = null;
    private $amazonAPIFullURL   = null;
    private $amazonSignatureURL = null;
    private $amazonSignature    = null;
    private $amazonAPIResult    = null;


    public function __construct() {
/*
        $this->amazonAPIParams = array(
            "Service"           => "AWSECommerceService",
            "AWSAccessKeyId"    => AMAZON_ACCESS_KEY_ID,
            "AssociateTag"      => AMAZON_ASSOCIATE_TAG,
            "Operation"         => "ItemLookup",
            "ItemId"            => "B014UDYWG0",
            "ResponseGroup"     => "ItemAttributes,Images",
            "Timestamp"         => gmdate("Y-m-d\TH:i:s\Z")
        );
*/
        $this->amazonAPIParams  = array(
            "Service"           => "AWSECommerceService",
            "AWSAccessKeyId"    => AMAZON_ACCESS_KEY_ID,
            "AssociateTag"      => AMAZON_ASSOCIATE_TAG,
            "Operation"         => "ItemSearch",
            "Keywords"          => "the+hunger games",
            "SearchIndex"       => "Books",
            "Timestamp"         => gmdate("Y-m-d\TH:i:s\Z")
        );
        ksort($this->amazonAPIParams);
    }


    public function getAmazonAPIParams() {
        return $this->amazonAPIParams;
    }


    public function createAmazonHTTPQuery() {
        $this->amazonHTTPQuery = str_replace(array('+', '%7E'), array('%20', '~'), http_build_query($this->amazonAPIParams));
    }


    public function getAmazonHTTPQuery() {
        return $this->amazonHTTPQuery;
    }


    public function createAmazonSignatureURL() {
        $parseURL = parse_url(AMAZON_API_URL);

        $this->amazonSignatureURL = "GET\n" . $parseURL["host"] . "\n" . $parseURL["path"] . "\n" . $this->amazonHTTPQuery;
    }


    public function getAmazonSignatureURL() {
        return $this->amazonSignatureURL;
    }


    public function createAmazonSignature() {
        $signature  = null;
        $signature  = base64_encode(hash_hmac("sha256", $this->amazonSignatureURL, AMAZON_SECRET_ACCESS_KEY, true)); 
        $signature  = str_replace('%7E', '~', rawurlencode($signature));

        $this->amazonSignature = $signature;
    }


    public function getAmazonSignature() {
        return $this->amazonSignature;
    }


    public function createAmazonAPIFullURL() {
        $this->amazonAPIFullURL = AMAZON_API_URL . '?' . $this->amazonHTTPQuery . '&Signature=' . $this->amazonSignature;
    }


    public function getAmazonAPIFullURL() {
        return $this->amazonAPIFullURL;
    }


    public function callAmazonAPI() {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->amazonAPIFullURL);
        $this->amazonAPIResult = curl_exec($curl);
        curl_close($curl);
    }


    public function getAmazonAPIResult() {
        return $this->amazonAPIResult;
    }

}

?>
