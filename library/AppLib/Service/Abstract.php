<?php

abstract class AppLib_Service_Abstract
    extends ArrayObject
{
    private $_return_objects = false;

    public function __construct(array $opts = array())
    {
        $this->setOptions($opts);
    }

    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $method = "set" . str_replace(" ", "", ucwords(str_replace("_", " ", $key)));

            if (method_exists($this, $method))
                $this->$method($value);
        }
    }

    protected function getReturnObjects()
    {
        return $this->_return_objects;
    }

    protected function setReturnObjects($flag)
    {
        $this->_return_objects = (bool) $flag;
        return $this;
    }

    protected function checkFields(array $user_input, array $mandatory_fields)
    {
        $missing_fields = array_diff(array_values($mandatory_fields), array_keys($user_input));

        if (! empty($missing_fields))
            throw new AppLib_Service_Fault(sprintf("Missing mandatory field(s): '%s'", implode(", ", $missing_fields)));
    }

    protected function getFilter(array $params, array $fields, $table_prefix = null)
    {
        $filter     = array();
        $sql_params = array();
        foreach ($fields as $field => $spec) {

            if (! array_key_exists($field, $params) || $params[$field] == "")
                /* Field not filtered */
                continue;

            $fobj = new AppLib_DbFilter($field, $spec);

            list($where, $sparams) = $fobj->getCondition($params[$field]);

            $filter[]   = $where;
            $sql_params = $sql_params + $sparams;
        }

        return array($filter, $sql_params);
    }

    public function dateToSQL ($date)
    {
        $date_replace = array(
                "/[\w]+\s/",
                "/\//",
                /* Inverse for ::timestamp cast */
                "/(\d+)-(\d+)-(\d+)/");
        return preg_replace($date_replace, array("", "-", "$3-$2-$1"), $date);
    }

    public function validateDbRow(array $params, array $fields)
    {
        $instances = array();
        foreach ($fields as $field_name => $spec) {
            $required = $spec["required"];
            $class    = !empty($spec["class"]) ? $spec["class"] : null;
            $pattern  = !empty($spec["match"]) ? $spec["match"] : null;

            if ($required && empty($params[$field_name]))
                throw new eVias_ArrayObject_Exception;

            if (empty($params[$field_name]))
                continue;

            if (null != $class) {
                $instances[] = call_user_func($class . "::loadById", $params[$field_name]);
                continue;
            }

            if (null != $pattern) {
                if (! (bool) preg_match($pattern, $params[$field_name]))
                    throw new eVias_ArrayObject_Exception;
            }
        }

        return $instances;
    }
}
