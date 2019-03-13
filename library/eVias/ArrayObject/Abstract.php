<?php
/**
 * @package eVias_Core
 * @author   g.saive
 * @brief : implements an abstract layer for
 *			objects represented as arrays
 */

abstract class eVias_ArrayObject_Abstract 
	extends ArrayObject
{
    /**
     * Properties getter
     *
     * @param string $key The name of the property
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->offsetExists($key) ? $this->offsetGet($key) : null;
    }

    /**
     * Properties setter
     *
     * @param string $key   The name of the property
     * @param mixed  $value The value of the property
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * Isset accessor
     *
     * @param string $key The name of the property
     *
     * @return bool
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Unset accessor
     *
     * @param string $key The name of the property
     *
     * @return void
     */
    public function __unset($key)
    {
        if ( $this->offsetExists($key) ) {
            $this->offsetUnset($key);
        }
    }

    /**
     * Reset all data
     *
     * @return E_ArrayObject_Abstract
     */
    public function reset()
    {
        $this->exchangeArray(array());
        return $this;
    }

    /**
     * Bind the content of $values into the array
     * $values can be any traversable object or an array
     *
     * @param mixed $values IteratorAggregate|Traversable|array
     *
     * @return E_ArrayObject_Abstract
     *
     * @throws E_Exception
     */
    public function bind($values)
    {
        if ( ! is_array($values) && ! $values instanceof Traversable ) {
            throw new eVias_ArrayObject_Exception('Invalid type given : ' . var_export($values, true));
        }

        foreach ( $values as $key => $val ) {
            $this->$key = $val;
        }

        return $this;
    }

    /**
     * Return fields as array
     *
     * @return array
     */
    public function asArray($with_pkey = false)
    {
        $fields = array_flip($this->_fields);
        if ($with_pkey)
            $fields[$this->_pk] = count($this->_fields);

        return array_intersect_key($this->getArrayCopy(), $fields);
    }

}
