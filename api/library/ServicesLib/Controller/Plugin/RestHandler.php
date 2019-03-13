<?php

class ServicesLib_Controller_Plugin_RestHandler
    extends Zend_Controller_Plugin_Abstract
{
    private $dispatcher;
    private $defaultFormat = 'html';
    private $reflectionClass = null;
    private $acceptableFormats = array(
        'html',
        'xml',
        'php',
        'json'
    );

    private $responseTypes = array(
        'text/html'                         => 'html',
        'application/xhtml+xml'             => 'html',
        'text/xml'                          => 'xml',
        'application/xml'                   => 'xml',
        'application/xhtml+xml'             => 'xml',
        'text/php'                          => 'php',
        'application/php'                   => 'php',
        'application/x-httpd-php'           => 'php',
        'application/x-httpd-php-source'    => 'php',
        'text/javascript'                   => 'json',
        'application/json'                  => 'json',
        'application/javascript'            => 'json'
    );

    private $requestTypes = array(
        'multipart/form-data',
        'application/x-www-form-urlencoded',
        'text/xml',
        'application/xml',
        'text/php',
        'application/php',
        'application/x-httpd-php',
        'application/x-httpd-php-source',
        'text/javascript',
        'application/json',
        'application/javascript',
        false
    );

    public function __construct(Zend_Controller_Front $frontController)
    {
        $this->dispatcher = $frontController->getDispatcher();
    }

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->_response->setHeader('Vary', 'Accept');

        /* Cross-Origin Resource Sharing (CORS) */
        $this->_response->setHeader('Access-Control-Max-Age', '86400');
        $this->_response->setHeader('Access-Control-Allow-Origin', '*');
        $this->_response->setHeader('Access-Control-Allow-Credentials', 'true');

        $allowHeaders = array('Authorization', 'X-Authorization', 'Origin', 
                              'Accept', 'Content-Type', 'X-Requested-With', 
                              'X-HTTP-Method-Override');
        $this->_response->setHeader('Access-Control-Allow-Headers', implode(",", $allowHeaders));

        $class = $this->getReflectionClass($request);

        if ($this->isRestClass($class)) {
            /* application.ini */
            $this->setConfig();
            $this->setResponseFormat($request);

            /* Handle action and body */
            $this->handleActions($request);
            $this->handleRequestBody($request);
        }
    }

    private function setConfig()
    {
        $frontCtrl = Zend_Controller_Front::getInstance();
        $bootstrap = $frontCtrl->getParam('bootstrap');
        $options   = new Zend_Config($bootstrap->getOptions(), true);

        $rest = $options->get('rest', false);

        if ($rest) {
            $this->defaultFormat = $rest->default;
            $this->acceptableFormats = $rest->formats->toArray();
        }
    }

    /**
     * sets the response format and content type
     * uses the "format" query string paramter and the HTTP Accept header
     */
    private function setResponseFormat(Zend_Controller_Request_Abstract $request)
    {
        $format = false;

        if (in_array($request->getParam('format', 'none'), $this->responseTypes)) 
            $format = $request->getParam('format');
        else {
            $bestMimeType = $this->negotiateContentType($request);

            if (!$bestMimeType || $bestMimeType == '*/*') 
                /* no matching MIME, default xml. */
                $bestMimeType = 'application/xml';

            $format = $this->responseTypes[$bestMimeType];
        }

        if ($format === false || !in_array($format, $this->acceptableFormats)) {
            $request->setParam('format', $this->defaultFormat);

            if ($request->isOptions() === false)
                $request->dispatchError(ServicesLib_Controller_Response_Rest::UNSUPPORTED_TYPE, 'Unsupported Media/Format Type');
        } 
        else
            $request->setParam('format', $format);
    }

    /**
     * determines whether the requested actions exists
     * otherwise, triggers optionsAction.
     */
    private function handleActions(Zend_Controller_Request_Abstract $request)
    {
        $class   = $this->getReflectionClass($request);
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);

        $actions = array();
        foreach ($methods as &$method) {
            $class_name = $method->class;
            if ('ServicesLib_Controller_Rest' == $class_name) 
                continue; 

            $method_name = strtoupper($method->name);
            if ($method_name == '__CALL' && $method->class != 'Zend_Controller_Action') 
                /* get from request */
                $actions[] = $request->getMethod();
            elseif (substr($method_name, -6) == 'ACTION' && $method_name != 'INDEXACTION') 
                /* register action name */
                $actions[] = str_replace('ACTION', null, $method_name);
        }

        if (! in_array('OPTIONS', $actions)) 
            $actions[] = 'OPTIONS';

        /* Cross-Origin Resource Sharing (CORS) */
        $this->_response->setHeader('Access-Control-Allow-Methods', implode(', ', $actions));

        if (! in_array(strtoupper($request->getMethod()), $actions)) {
            $request->dispatchError(ServicesLib_Controller_Response_Rest::NOT_ALLOWED, 'Method Not Allowed');
            $this->_response->setHeader('Allow', implode(', ', $actions));
        }
    }

    /**
     * PHP only parses the body into $_POST if its a POST request
     * this parses the reqest body in accordance with RFC2616 spec regardless of the HTTP method
     */
    private function handleRequestBody(Zend_Controller_Request_Abstract $request)
    {
        $header = strtolower($request->getHeader('Content-Type'));

        // cleanup the charset part
        $header = current(explode(';', $header));

        // detect request body content type
        foreach ($this->requestTypes as $contentType) {
            if ($header == $contentType) {
                break;
            }
        }

        $rawBody = $request->getRawBody();

        /* Treat these two separately because of the way PHP treats POST */
        if (in_array($contentType, array('multipart/form-data', 'application/x-www-form-urlencoded'))) {
            if ($request->isPost() && $contentType == 'multipart/form-data') {
                foreach ($_FILES as &$file) {
                    if (array_key_exists('tmp_name', $file) && is_file($file['tmp_name'])) {
                        $data = file_get_contents($file['tmp_name']);
                        $file['content'] = base64_encode($data);
                    }
                }

                // reset the array pointer
                unset($file);
            } 
            else {
                switch ($contentType) {
                    case 'application/x-www-form-urlencoded':
                        parse_str($rawBody, $_POST);
                        break;

                    /* Enable $_FILES array for non-POST requests. */
                    case 'multipart/form-data':
                        // extract the boundary
                        parse_str(end(explode(';', $request->getHeader('Content-Type'))));

                        if (! isset($boundary)
                            || ! preg_match(sprintf('/--%s(.+)--%s--/s', $boundary, $boundary), $rawBody, $regs))
                            /* No need to remove boundary */
                            break;

                        /* Work with chunks */
                        $chunks = explode('--' . $boundary, trim($regs[1]));
                        foreach ($chunks as $chunk) {
                            $name_grp    = "?P<name>.+?";
                            $fname_grp   = "?P<filename>.+?";
                            $headers_grp = "?:\\r|\\n";
                            $pattern     = sprintf('Content-Disposition: form-data; name="(%s)"' 
                                                 . '(?:; filename="(%s)")?' 
                                                 . '(?P<headers>(%s)+?.+?(%s)+?)?'
                                                 . '(?P<data>.+)',
                                                 $name_grp, $fname_grp,
                                                 $headers_grp, $headers_grp);
                            if (preg_match('/' . $pattern . '/si', $chunk, $regs)) {

                                if (!empty($regs['filename'])) {
                                    /* File upload */

                                    $data = $regs['data'];

                                    $headers = $this->parseHeaders($regs['headers']);

                                    $_FILES[$regs['name']] = array(
                                        'name' => $regs['filename'],
                                        'type' => $headers['Content-Type'],
                                        'size' => mb_strlen($data),
                                        'content' => base64_encode($data)
                                    );
                                } 
                                else {
                                    /* Regular key=value combination */
                                    $_POST[$regs['name']] = trim($regs['data']);
                                }
                            }
                        }
                        break;
                }
            }

            $request->setParams($_POST + $_FILES);
        } 
        elseif (! empty($rawBody)) {
            /* Dealing with an encoded request */
            try {
                switch ($contentType) {
                    case 'text/javascript':
                    case 'application/json':
                    case 'application/javascript':
                        $_POST = (array) Zend_Json::decode($rawBody, Zend_Json::TYPE_OBJECT);
                        break;

                    case 'text/xml':
                    case 'application/xml':
                        $json = @Zend_Json::fromXml($rawBody);
                        $_POST = (array) Zend_Json::decode($json, Zend_Json::TYPE_OBJECT)->request;
                        break;

                    case 'text/php':
                    case 'application/x-httpd-php':
                    case 'application/x-httpd-php-source':
                        $_POST = (array) unserialize($rawBody);
                        break;

                    default:
                        $_POST = (array) $rawBody;
                        break;
                }

                $request->setParams($_POST);

            } 
            catch (Exception $e) {
                $request->dispatchError(ServicesLib_Controller_Response_Rest::BAD_REQUEST, 'Invalid Payload Format');
                return;
            }
        }
    }


    /**
     * constructs reflection class of the requested controoler
     **/
    private function getReflectionClass(Zend_Controller_Request_Abstract $request)
    {
        if ($this->reflectionClass === null) {
            $controller = $this->dispatcher->getControllerClass($request);
            if ($controller === false) 
                return false;

            $className  = $this->dispatcher->loadClass($controller);
            $this->reflectionClass = new ReflectionClass($className);
        }

        return $this->reflectionClass;
    }

    /**
     * determines if the requested controller is a RESTful controller
     **/
    private function isRestClass($class)
    {
        $rest_classes = array('Zend_Rest_Controller', 'ServicesLib_Controller_Rest');
        if ($class === false) 
            return false;
        elseif (in_array($class->name, $rest_classes)) 
            return true;
        else 
            return $this->isRestClass($class->getParentClass());
    }

    /**
     * utility function to replace http_parse_headers when its not available
     * see: http://pecl.php.net/pecl_http
     **/
    private function parseHeaders($header)
    {
        if (function_exists('http_parse_headers')) {
            return http_parse_headers($header);
        }

        $retVal = array();
        $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
        foreach ($fields as $field) {
            if (preg_match('/([^:]+): (.+)/m', $field, $match) ) {
                $match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
                if( isset($retVal[$match[1]]) )
                    $retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
                else
                    $retVal[$match[1]] = trim($match[2]);
            }
        }

        return $retVal;
    }

    /**
     * utility function to replace http_negotiate_content_type when its not available
     * see: http://pecl.php.net/pecl_http
     **/
    private function negotiateContentType($request)
    {
        if (function_exists('http_negotiate_content_type')) {
            return http_negotiate_content_type(array_keys($this->responseTypes));
        }

        $string = $request->getHeader('Accept');

        $mimeTypes = array();
        $string    = strtolower(str_replace(' ', '', $string));
        $types     = explode(',', $string);

        foreach ($types as $type) {
            $quality = 1;

            if (strpos($type, ';q='))
                /* Type + quality provided */
                list($type, $quality) = explode(';q=', $type);
            elseif (strpos($type, ';'))
                /* type option provided */
                list($type, ) = explode(';', $type);
            
            if (array_key_exists($type, $this->responseTypes) and !array_key_exists($quality, $mimeTypes)) {
                /* 0 quality means MIME type is invalid */
                $mimeTypes[$quality] = $type;
            }
        }

        krsort($mimeTypes);
        return current(array_values($mimeTypes));
    }
}
