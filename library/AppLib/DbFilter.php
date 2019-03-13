<?php

class AppLib_DbFilter
{
    private $_operator      = "=";
    private $_field_id      = null;
    private $_prefix        = null;
    private $_name          = null;
    private $_value         = null;
    private $_pattern       = null;
    private $_field_cast    = null;
    private $_value_cast    = null;

    public function __construct($fid, array $opts = array())
    {
        $this->_field_id = $fid;
        $this->setOptions($opts);

        if (null === $this->getFieldId()
            || null == $this->getOperator())
            throw new InvalidArgumentException;

        if (null === $this->getName())
            $this->setName($this->getFieldId());
    }

    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $method = "set" . str_replace(" ", "", ucwords(str_replace("_", " ", $key)));

            if (method_exists($this, $method))
                $this->$method($value);
        }
    }

    public function getCondition($value)
    {
        $sql_params = array();
        $filter     = "";

        /* 
         * According to the given operator, the comparison
         * for the condition is written differently. 
         * For now, it is possible to handle comparison
         * operator filters and search operator filters.
         * The left value and right value of the operation
         * are generated from options set on $this.
         */
        $lvalue = "";
        $rvalue = "";
        switch (strtolower($this->getOperator())) {
        case "like":
        case "ilike":
        case "not like":
            /* Process pattern logic for search operators. */
            if (null !== $this->getPattern() && is_string($value))
                $value = str_replace("?", $value, $this->getPattern());

            $val_may_be_array = true;
        /* ! nobreak ! */
        case "=":
        case "<":
        case ">":
        case "<=":
        case ">=":
        case "!=":
        case "<>":
            /* Configure left- and right-value for 
             * comparison operation. */
            if (!isset($val_may_be_array))
                $val_may_be_array = false;

            if (null !== $this->getPrefix())
                /* Prefix table name for restricting ambiguosity. */
                $lvalue .= sprintf("%s.", $this->getPrefix());
            $lvalue .= $this->getName();

            $rvalue = ":" . $this->getFieldId();

            if ($val_may_be_array && is_array($value)) {
                $filters = array();
                for ($i = 0, $m = count($value); $i < $m; $i++) {
                    $cur_value = $value[$i];
                    if (null !== $this->getPattern())
                        $cur_value = str_replace("?", $cur_value, $this->getPattern());

                    $filters[] = "$lvalue {$this->getOperator()} :{$this->getFieldId()}{$i}";
                    $sql_params[$this->getFieldId() . $i] = $cur_value;
                }

                if (!empty($filters))
                    $filters = implode(" OR ", $filters);
                else
                    $filters = "TRUE";
                $filter = "(" . $filters . ")";
            }
            else {
                /* Cast values if needed */
                if (null !== $this->getFieldCast())
                    $lvalue .= "::" . $this->getFieldCast();
                if (null !== $this->getValueCast())
                    $rvalue = "({$rvalue})::" . $this->getValueCast();

                /* Assemble filter condition */
                $filter = "{$lvalue} {$this->getOperator()} {$rvalue}";
                $sql_params[$this->getFieldId()] = $value;
            }
            break;

        case "in":
        case "not in":
            /* Configure left- and right-value for
             * search operation. */

            if (null !== $this->getPrefix())
                /* Prefix table name for restricting ambiguosity. */
                $lvalue .= sprintf("%s.", $this->getPrefix());
            $lvalue .= $this->getName();

            if (! is_array($value)) {
                /* Simple value provided. Only one prepared
                   parameter. */
                $rvalue = ":" . $this->getFieldId();
                $sql_params[$this->getFieldId()] = $value;
            }
            else {
                /* Multiple values provided, list must
                   be built. */
                $cnt = 0;
                foreach ($value as $val) {
                    $suffixed_id = $this->getFieldId() . "_" . $cnt;
                    $rvalue .= ($cnt > 0 ? ", " : "") . ":{$suffixed_id}";
                    $sql_params[$suffixed_id] = $val;
                    $cnt++;
                }
            }

            $operator = strtoupper($this->getOperator());
            $filter = "{$lvalue} {$operator} ({$rvalue})";
            break;

        default:
            throw new InvalidArgumentException;
        }

        return array($filter, $sql_params);
    }

    /* Option getters */

    public function getFieldId()
    { 
        return $this->_field_id; 
    }

    public function getPrefix()
    { 
        return $this->_prefix; 
    }

    public function getName()
    { 
        return $this->_name; 
    }

    public function getOperator()
    { 
        return $this->_operator; 
    }

    public function getValue()
    { 
        return $this->_value; 
    }

    public function getPattern()
    { 
        return $this->_pattern; 
    }

    public function getFieldCast()
    { 
        return $this->_field_cast; 
    }

    public function getValueCast()
    { 
        return $this->_value_cast; 
    }

    /* Option setters */

    public function setFieldId($p)
    {
        $this->_field_id = $p;
        return $this;
    }

    public function setPrefix($p)
    {
        $this->_prefix = $p;
        return $this;
    }

    public function setName($p)
    {
        $this->_name = $p;
        return $this;
    }

    public function setOperator($p)
    {
        $this->_operator = $p;
        return $this;
    }

    public function setValue($p)
    {
        $this->_value = $p;
        return $this;
    }

    public function setPattern($p)
    {
        $this->_pattern = $p;
        return $this;
    }

    public function setFieldCast($p)
    {
        $this->_field_cast = $p;
        return $this;
    }

    public function setValueCast($p)
    {
        $this->_value_cast = $p;
        return $this;
    }
}

