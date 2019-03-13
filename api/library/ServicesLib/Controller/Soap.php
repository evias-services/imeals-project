<?php

abstract class ServicesLib_Controller_Soap
    extends Zend_Controller_Action
{
    protected $_soap_base_url = null;

    public function init()
    {
        $scheme  = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? "https" : "http";
        $host    = "services.e-restaurant.evias.loc";
        $uri     = "soap";

        $this->_soap_base_url = sprintf("%s://%s/%s", $scheme, $host, $uri);

        parent::init();
    }

    public function getSoapBaseUrl()
    {
        return $this->_soap_base_url;
    }

    public function getSimpleXMLResponse($method, $response_xml, $namespace_uri, $ns = "ns1")
    {
        $xml = simplexml_load_string($response_xml);
        $xml->registerXPathNamespace($ns, $namespace_uri);
        $sxml_response = $xml->xpath("//{$ns}:{$method}Response/return");

        return $sxml_response[0];
    }

    public function getResponseArray($sxml_response)
    {
        $response_array = array();
        if ($sxml_response->count()) {
            /* //ns1:methodResponse/return contains a
               list of <item> elements. */
            $current_item = array();
            foreach ($sxml_response->children() as $response_item) {
                $pair_key = (string) $response_item->key;
                $pair_val = (string) $response_item->value;
                $current_item[$pair_key] = $pair_val;
            }
            $response_array[] = $current_item;
        }
        else
            /* //ns1:methodResponse/return contains a
               direct value. (string, float, bool) */
            $response_array[] = (string) $sxml_response;

        return $response_array;
    }
}
