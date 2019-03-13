<?php

class Default_SoapController
    extends ServicesLib_Controller_Soap
{
    public function queryAction()
    {
        $this->getHelper('viewRenderer')
             ->setNoRender(true);

        /* Resource validation by service instanciation */
        $resource  = $this->_getParam("rtype", "application");
        if (! in_array($resource, AppLib_Service_Factory::getResourceTypes()))
            /** XXX log **/
            $resource = "application";

        $className = get_class(AppLib_Service_Factory::getService($resource));

        /* Non-WSDL server mode */
        $server = new Zend_Soap_Server(null, array(
            "location"    => $this->getSoapBaseUrl() . "/wsdl/rtype/{$resource}",
            "uri"         => $this->getSoapBaseUrl() . "/query/rtype/{$resource}",
            "soap_server" => SOAP_1_2,
            "cache_wsdl"  => WSDL_CACHE_NONE));
        $server->setClass($className);
        $server->registerFaultException(array("AppLib_Service_Fault"));
        $server->handle();
    }

    public function wsdlAction()
    {
        $this->getHelper('viewRenderer')
             ->setNoRender(true);

        try {
            /* WSDL server mode */
            $resource  = $this->_getParam("rtype", "application");
            if (! in_array($resource, AppLib_Service_Factory::getResourceTypes()))
                /** XXX log **/
                $resource = "application";

            $className = get_class(AppLib_Service_Factory::getService($resource));
           
            $wsdl = new Zend_Soap_AutoDiscover;
            $wsdl->setUri($this->getSoapBaseUrl() . "/query/rtype/{$resource}");
            $wsdl->setClass($className);
            $wsdl->handle();
        }
        catch (Exception $e) {
            die("ERROR: " . $e->getMessage());
        }
    }

    public function testAction()
    {
        $this->getHelper('viewRenderer')
             ->setNoRender(true);

        $html  = "<!doctype html>";
        $html .= "<html xmlns='http://www.w3.org/1999/xhtml'>";
        $html .= "<head><meta charset='UTF-8'></head><body>";
        foreach (AppLib_Service_Factory::getResourceTypes()
                        as $rtype) {

            try {
                /* WSDL client mode */
                $wsdl   = $this->getSoapBaseUrl() . "/wsdl/rtype/{$rtype}";
                $client = new Zend_Soap_Client($wsdl, array(
                    "cache_wsdl" => WSDL_CACHE_NONE));

                $label = ucfirst($rtype);
                $html .= "<fieldset><legend>e-restaurant.eu $label Service</legend>";
                $html .= "<form action='' method='post'>";
                $html .= "<select name='soap_method'>";
                $html .= "    <option value=''>Select a SOAP method..</option>";
                foreach ($client->getFunctions() as $index => $function) {
                    $matches = array();
                    preg_match_all("/^([^ ]+) ([^\( ]+) ?(\([^\)]*\))$/", $function, $matches);

                    if (empty($matches[2]) || empty($matches[2][0]))
                        continue;

                    $method_name = trim($matches[2][0]);
                    $html .= "<option value='$method_name'>$function</option>";
                }
                $html .= "</select>";
                $html .= "<input type='submit' name='exec_query' value='__doRequest' />";
                $html .= "<input type='hidden' name='rtype' value='{$rtype}' />";
                $html .= "</form>";
                $html .= "</fieldset>";

                unset($client);
            } 
            catch ( SoapFault $exp )  {
                $html .= 'ERROR: [' . $exp->faultcode . '] ' . $exp->faultstring;
            } 
            catch ( Exception $exp2 ) {
                $html .= 'ERROR: ' . $exp2->getMessage();
            }

            $html .= "<br /><br />";
        }

        if ($this->getRequest()->isPost()) {
            /* Send SOAP query */
            $rtype  = $this->_getParam("rtype", "application");
            $method = $this->_getParam("soap_method", null);

            $valid  = null !== $method;
            $valid  = $valid && in_array($rtype, AppLib_Service_Factory::getResourceTypes());

            if (! $valid)
                $this->_redirect("/soap/test");

            /* Execute SOAP function ; Non-WSDL client mode */
            $sxml_response  = null;
            $response_array = array();
            try {
                $wsdl   = $this->getSoapBaseUrl() . "/wsdl/rtype/{$rtype}";
                $client = new Zend_Soap_Client($wsdl, array(
                    "cache_wsdl"   => WSDL_CACHE_NONE, 
                    "soap_version" => SOAP_1_2));

                $xmlns_uri      = $this->getSoapBaseUrl() . "/query/rtype/{$rtype}";
                try {
                    $client->$method();
                    $result_xml = $client->getLastResponse();

                    $sxml_response  = $this->getSimpleXMLResponse($method, $result_xml, $xmlns_uri);
                    $response_array = $this->getResponseArray($sxml_response);

                    unset($doc, $xml);
                }
                catch (SoapFault $exp) {
                    $result_xml = 'SOAP ERROR: [' . $exp->faultcode . '] ' . $exp->faultstring;
                } 

                $request_xml  = $client->getLastRequest();
                $request_sxml = simplexml_load_string($request_xml);
                
                $request_doc = dom_import_simplexml($request_sxml)->ownerDocument;
                $request_doc->formatOutput = true;
                $request = $request_doc->saveXML();
            }
            catch (Exception $e) {
                $result_xml  = 'ERROR: ' . $e->getMessage();
                $request = "";
            }
            
            $html .= "<fieldset><legend>LAST REQUEST</legend>";
            $html .= "<pre>" . htmlspecialchars($request) . "</pre>"; 
            $html .= "</fieldset>";
            $html .= "<fieldset><legend>LAST RESPONSE</legend>";
            $html .= "<br /><strong>VALUE (as Array): </strong><br />";
            $html .= "<pre>" . var_export($response_array, true) . "</pre>";

            if ($sxml_response instanceof SimpleXMLElement) {
                $dom = dom_import_simplexml($sxml_response)
                    ->ownerDocument;
                $dom->formatOutput = true;
                $html .= "<br />----------------------------<br />";
                $html .= "<br /><br /><strong>XML: </strong><br />";
                $html .= "<pre>" . htmlspecialchars($dom->saveXML()) . "</pre>";
            }

            $html .= "</fieldset>";
        }

        echo $html . "</body></html>";
    }
}
