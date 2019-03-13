<?php

/**
 * @example:
 *     $proxy = new AppLib_Service_Proxy;
 *
 *     // Proxied Service definition
 *     $proxy->setService("user")
 *           ->__getFunctions();
 *
 *     // Proxied Service use cases
 *     $proxy->setService("user", array("return_objects" => true));
 *
 *     // calls on 'user' service
 *     $proxy->__call("addUser", array(...));
 *     $proxy->__call("getUsers", array("filter" => $filter));
 *
 *     // changing active service + call
 *     $proxy->setService("restaurant")
 *           ->__call("getRestaurants", array("filter" => $filter))
 */
class AppLib_Service_Proxy
    extends AppLib_Service_Abstract
{
    private $_service_opts = null;
    private $_service_key  = null;
    private $_service      = null;

    static private $_instances = array();

    public function setService($s_key, array $s_opts = array())
    {
        if (! is_string($s_key))
            throw new InvalidArgumentException("AppLib_Service_Proxy/setService: Argument 1 must be a string.");

        $instance_key = sha1(sprintf("%s.%s", $s_key, serialize($s_opts)));
        if (! isset(self::$_instances[$instance_key]))
            /* Load service object with given options */
            self::$_instances[$instance_key] = AppLib_Service_Factory::getService($s_key, $s_opts);

        /* modify $this to work with set service. */
        $this->_service_key  = $s_key;
        $this->_service_opts = $s_opts;
        $this->_service = self::$_instances[$instance_key];
        return $this;
    }

    public function getService()
    {
        return $this->_service;
    }

    /**
     * This method executes a function $name on the last
     * set service (@see setService()). The method call is
     * encapsulated in a try catch to provide with generic
     * AppLib_Service_Fault exceptions.
     *
     * @param type $name        method name
     * @param type $arguments   method call arguments
     * @return mixed
     * @throws RuntimeException         on unset service object
     * @throws AppLib_Service_Fault     on invalid service method call arguments
     */
    public function __call($name, $arguments)
    {
        if (null === $this->getService())
            throw new RuntimeException("{$this->_service_key}/{$name}: Method setService must be called first.");

        if (! method_exists($this->getService(), $name))
            throw new AppLib_Service_Fault("Invalid method name '" . $name . "' for service '{$this->_service_key}'.");

        try {
            /* Execute proxied service method. */

            return $this->getService()->$name($arguments);
        }
        catch (AppLib_Service_Fault $e) {
            /* Forward exception */
            throw $e;
        }
        catch (eVias_ArrayObject_Exception $e1) {
            /* Generic object load error, display generic error message. */
            throw new AppLib_Service_Fault("{$this->_service_key}/{$name}: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            /* Unhandled PHP exception caught, display message. */
            throw new AppLib_Service_Fault(sprintf("{$this->_service_key}/{$name}: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * This method should returns a list of functions
     * which are available from the last set service.
     *
     * @return type
     * @throws UnexpectedValueException on unset service object
     */
    public function __getFunctions()
    {
        if (null === $this->getService())
            throw new UnexpectedValueException("{$this->_service_key}/{$name}: Method setService must be called first.");

        $sclass   = get_class($this->getService());
        $rclass   = new ReflectionClass($sclass);
        $rmethods = $rclass->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($rmethods as $refl_method) {
            $mname      = $refl_method->getName();
            $mclass     = $refl_method->getDeclaringClass()->getName();

            if ($sclass != $mclass)
                /* Inherited method. (not a service function) */
                continue;

            if ($refl_method->isConstructor() || $refl_method->isDestructor())
                /* Constructor/destructor logic is not listed publicly. */
                continue;

            $marguments = $this->_getParametersPrototype($refl_method->getParameters());
            $mprototype = sprintf("public function {$mname} (%s)", $marguments);

            $methods[$sclass . "::" . $mname] = $mprototype;
        }

        return $methods;
    }

    private function _getParametersPrototype(array $parameters)
    {
        $marguments = "";
        foreach ($parameters as $parameter) {
            $param_name  = "$" . $parameter->getName();

            //echo "<pre>";var_dump(get_class_methods("ReflectionParameter"));die;
            if ($parameter->isArray())
                /* array argument */
                $param_name = "array " . $param_name;
            elseif (null !== $parameter->getClass())
                /* class instance argument */
                $param_name = $parameter->getClass()->getName() . " " . $param_name;

            if ($parameter->isOptional()) {
                $param_name .= " = ";

                if ($parameter->isDefaultValueConstant())
                    /* default value is a constant */
                    $param_name .= $parameter->getDefaultValueConstantName();
                elseif (null !== $parameter->getClass() && $parameter->allowsNull())
                    /* class instance argument with default null */
                    $param_name .= "null";
                else
                    /* mixed default value, export */
                    $param_name .= var_export($parameter->getDefaultValue(), true);
            }

            $marguments .= (empty($marguments) ? "" : ", ") . $param_name;
        }

        $replace_what = array(
            "/\(([\s]*)([^\s])/",
            "/,([\s]*)([^\s])/",
            "/([^\s])([\s]*)\)/");

        $replace_by   = array(
            "( $2",
            ", $2",
            "$1 )");

        $marguments = preg_replace($replace_what, $replace_by, $marguments);

        return $marguments;
    }
}