<?php
/**
 * @package eVias_Core
 * @author   g.saive
 */

class eVias_ArrayObject_Db
	extends eVias_ArrayObject_Abstract
{
 	/**
     * table name
     *
     * @var string
     */
    protected $_tableName = null;


	/**
     * Primary key
     *
     * @var mixed
     */
    protected $_pk;

	/**
     * Sequence
     *
     * @var unknown_type
     */
    protected $_sequence;

	/**
     * Modifiable fields
     *
     * @var array
     */
    protected $_fields = array();

	/**
     * Database adapter
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * Default database adapter to use
     *
     * @var $_default_db Zend_Db_Adapter_Abstract
     */
    static protected $_DEFAULT_DB = null;

    /**
     * Sets the default database adapter to use
     *
     * @param Zend_Db_Adapter_Abstract $db The database adapter
     *
     * @return void
     */
    static public function setDefaultAdapter(Zend_Db_Adapter_Abstract $db)
    {
        self::$_DEFAULT_DB = $db;
    }

    /**
     * Returns default database adapter
     *
     * @return Zend_Db_Adapter_Abstract
     */
    static public function getDefaultAdapter()
    {
        return self::$_DEFAULT_DB;
    }

    static public function loadById($id)
    {
        $object = new static;

        return $object->_load($id);
    }

    static public function getList(array $params = array())
    {
        $obj = new static;

        $table      = $obj->tableName();
        $select     = "*";
        $from       = " $table";
        $where      = " TRUE";
        $group_by   = "";
        $order_by   = "";
        $offset     = "";
        $limit      = "";

        if (isset($params["alias"]))
            $from .= " " . $params["alias"];

        if (isset($params["fields"]) && is_array($params["fields"]))
            $select = implode(",", $params["fields"]);

        if (isset($params["joins"]) && is_array($params["joins"]))
            $from    .= " " . implode(" ", $params["joins"]);

        if (isset($params["conditions"]) 
            && is_array($params["conditions"])
            && !empty($params["conditions"]))
            $where    = implode(" AND ", $params["conditions"]);

        if (isset($params["group"]) && is_string($params["group"]))
            $group_by = " GROUP BY " . $params["group"];

        if (isset($params["order"]) && preg_match("/[\w_,]+ (DESC|ASC)/", $params["order"]))
            $order_by = " ORDER BY " . $params["order"];

        if (isset($params["limit"]) && is_numeric($params["limit"]))
            $limit    = " LIMIT " . $params["limit"];

        if (isset($params["offset"]) && is_numeric($params["offset"]))
            $limit    = " OFFSET " . $params["offset"];

        $sql = "
            SELECT
                $select
            FROM
                $from
            WHERE
                $where
            $group_by
            $order_by
            $limit
            $offset
        ";

        if (isset($params["parameters"]))
            $records = $obj->getAdapter()
                           ->fetchAll($sql, $params["parameters"]);
        else
            /* No parameter for prepared query */
            $records = $obj->getAdapter()
                           ->fetchAll($sql);
        $result = array();
        foreach ($records as $record) {

            $class   = get_called_class();
            $current = new $class;
            $current->bind($record);

            if (isset($params["as_array"]) && $params["as_array"])
                $result[] = $current->getArrayCopy();
            else
                $result[] = $current;
            unset($current);
        }

        return $result;
    }

    static public function getCount(array $params)
    {
        return count(static::getList(array_diff_key($params, array_flip(array("limit", "offset")))));
    }

	public static function fetchAll($className) {
		if (is_null(self::getDefaultAdapter())) {
			return 1;
		}

		$object		= new $className;
		$arrayOut	= array();

		$query		= "
			SELECT
				" . implode(', ', $object->fieldNames()) . "
			FROM
				" . $object->tableName() . "
			WHERE TRUE
		";

		$result = $object->getAdapter()->fetchAll($query);

		if (! $result) {
			return $arrayOut;
		}

		foreach ($result as $index => $row) {
			$tmpObject = new $className;
			$tmpObject->bind($row);
			$arrayOut[] = $tmpObject;
			unset($tmpObject);
		}

		return $arrayOut;
	}

    /**
     * Sets database adapter to use
     *
     * @param Zend_Db_Adapter_Abstract $db The database adapter
     *
     * @return eVias_ArrayObject_Db
     */
    public function setAdapter(Zend_Db_Adapter_Abstract $db)
    {
        $this->_db = $db;
        return $this;
    }

    /**
     * Returns defined database adapter
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function getAdapter()
    {
        if ( ! isset($this->_db) ) {
            $this->_db = self::$_DEFAULT_DB;
        }
        return $this->_db;
	}

	public function primaryKey() {
		return $this->_pk;
	}

    /**
     * Return the primary keys values
     *
     * @return array
     */
    public function getPK()
    {
        if (is_array($this->_pk)) {
            $pk_values = array();
            if (isset($this->_pk)) {
                foreach ($this->_pk as $pkey) {
                    $pk_values[$pkey] = $this->{$pkey};
                }
            }

            return $pk_values;
        } else {
            return $this->{$this->_pk};
        }
    }

    /**
     * Return the last sequence id
     *
     * @return mixed
     */
    public function getLastSequenceId()
    {
        $db = $this->getAdapter();
        try {
            $lastSequenceId = $db->lastSequenceId($this->_sequence);
        }
        catch (Exception $e) {
            $sql = 'SELECT last_value FROM ' . $this->_sequence . ';';
            $lastSequenceId = $db->fetchOne($sql);
        }
        return $lastSequenceId;
    }

    /**
     * Load object from database
     *
     * @param mixed $id The value of the primary key
     *    if array, then format:
     * ['pk_name1' => 'pk_val1', 'pk_name2' => 'pk_val2']
     *
     * @return E_ArrayObject_Abstract
     */
    protected function _load($id)
    {
        if ( is_array($this->_pk) ) {
            if ( ! is_array($id) ) {
                throw new E_ArrayObject_Exception( 'There is more than one primary key for ' . $this->_tableName . ', the data you wan\'t the object to be loaded with should be an array.');
            }

            if ( count($this->_pk) != count($id) ) {
                throw new E_ArrayObject_Exception( 'The count of primary keys should be equal to the count of data rows.' );
            }

            $whereTab = array();
            foreach ($this->_pk as $key) {
                $whereTab[] = $key . ' = :' . $key;
                $params[$key] = $id[$key];
            }

            $where = implode( ' and ', $whereTab );
        }
        else {
            $where = $this->_pk . ' = :id';
            $params = array('id' => $id);
        }

        return $this->_fetch($where, $params);
    }

    /**
     * Load object according to a where condition
     *
     * @param string $where  the select conditions with parameters
     * @param array  $params the parameters
     *
     * @return eVias_ArrayObject_Abstract
     */
    protected function _fetch($where, array $params = array())
    {
        $fields = implode(', ', array_merge((array) $this->_pk, $this->_fields));

        $sql = 'SELECT ' . $fields . ' FROM ' . $this->_tableName . ' WHERE ' . $where . ';';

        $row = $this->getAdapter()->fetchRow($sql, $params);
        if ( empty($row) ) {
            $formattedParams = str_replace(array("\n", "\r"), '', var_export($params, true));
            throw new eVias_ArrayObject_Exception($this->_tableName . ' (' . $where . ' : ' . $formattedParams . ')  does not exist');
        }

        $this->reset();
        $this->bind($row);

        return $this;
    }

    /**
     * Save data into database
     *
     * @return E_ArrayObject_Abstract
     */
    public function save()
    {
        $values = $this->getPK();

        if (! isset($values) || empty($values)) {
            $this->insert();
        }
        else {
            $this->update();
        }

        return $this;
    }

    /**
     * Insert data into the database
     *
     * @return E_ArrayObject_Abstract
     */
    public function insert()
    {
        $this->_preInsert();

        if( empty($this->_sequence) )
            $values = $this->asArray(true);
        else
            $values = $this->asArray();

        $this->getAdapter()->insert($this->_tableName, $values);

        if ( ! empty($this->_sequence) ) {
            parent::__set($this->_pk, $this->getLastSequenceId());
        }

        return $this;
    }

    /**
     * Update data into the database
     *
     * @return E_ArrayObject_Abstract
     */
    public function update()
    {
        if ( empty($this->{$this->_pk})) {
            throw new eVias_ArrayObject_Exception('Could not update, primary key is not set');
        }

        $this->_preUpdate();

        $db = $this->getAdapter();

        $values = $this->asArray();

        $db->update($this->_tableName, $values, $this->_getWhereClause());

        return $this;
    }

    /**
     * Delete data from the database
     *
     * @return E_ArrayObject_Abstract
     */
    public function delete($where)
    {
        $this->_preDelete($where);

        $db = $this->getAdapter();

        $db->delete($this->_tableName, $where);

        return $this;
    }

    /**
     * Pre insert logic
     *
     * @return void
     */
    protected function _preInsert()
    {
        $this->_setDateCreated()
            ->_setDateUpdated();
    }

    /**
     * Pre update logic
     *
     * @return void
     */
    protected function _preUpdate()
    {
        $this->_setDateUpdated();
    }

    /**
     * Pre delete logic
     *
     * @return void
     **/
    protected function _preDelete($where)
    {
    }

    /**
     * Set the creation date
     *
     * @return E_ArrayObject_Abstract
     */
    protected function _setDateCreated()
    {
        if ( in_array('date_created', $this->_fields) && empty($this->date_created) )
            $this->date_created = date('Y-m-d H:i:s');
        return $this;
    }

    /**
     * Set the update date
     *
     * @return E_ArrayObject_Abstract
     */
    protected function _setDateUpdated()
    {
        if ( in_array('date_updated', $this->_fields) )
            $this->date_updated = date('Y-m-d H:i:s');
        return $this;
    }

    /**
     * Returns a where clause where the parameters are already parsed.
     *
     * @return string
     */
    protected function _getWhereClause( ) {
        $where = '';
        if (is_array($this->_pk)) {
            $values = $this->getPK();

            $sqlTab = $keysTab = $whereTab = array();

            foreach ($this->_pk as $key) {
                $sqlTab[]   = $key . ' = ?';
                $keysTab[]  = $key;
            }

            for ($i = 0; $i < count($sqlTab); $i++) {
                $whereTab[] = $this->_db->quoteInto( $sqlTab[$i], $values[$keysTab[$i]] );
            }

            $where = implode( ' and ', $whereTab );
        }
        else {
            $where = $this->_db->quoteInto($this->_pk . ' = ?', $this->getPK());
        }

        return $where;
    }

    /**
     * Convert a boolean into its string representation
     *
     * @param  $bool boolean
     * @return string
     */
    public function booleanToString($bool)
    {
        return $bool ? 't' : 'f';
    }

    /**
     * Convert a string value into its boolean represantation
     *
     * @param  $bool boolean
     * @return string
     */
    public function stringToBoolean($str)
    {
        return ($str == 'true' || $str == 'TRUE' || $str == 't' || $str == 'T');
	}

	public function fieldNames() {
		return array_merge((array)$this->_pk, $this->_fields);
	}

	public function tableName() {
		if (empty($this->_tableName)) {
			throw new eVias_ArrayObject_Exception('Table name for object is not set.');
		}

		return $this->_tableName;
	}

    public function setFields(array $fields)
    {
        $this->_fields = $fields;
        return $this;
    }

    public function setTable($table)
    {
        $this->_tableName = $table;
        return $this;
    }

    public function setPrimaryKey($pk)
    {
        $this->_pk = $pk;
        return $this;
    }

    public function setSequence($seq)
    {
        $this->_sequence = $seq;
        return $this;
    }

}
