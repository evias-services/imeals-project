<?php

class FrontLib_User
    extends eVias_ArrayObject_Db
{
    protected $_tableName = 'evias_user';
    protected $_pk = 'evias_user_id';
    protected $_fields = array(
        'login',
        'password',
        'last_log');

    public function save()
    {
        return $this->_save();
    }

    static public function loadByLogin($login, $pass)
    {
        $obj = new self;

        $tableName = $obj->tableName();

        $query = "
            SELECT
                evias_user_id,login, password, last_log
            FROM
                $tableName
            WHERE
                login = :log_access
                AND password = :log_pass
        ";

        $db = eVias_ArrayObject_Db::getDefaultAdapter();

        $result = $db->fetchRow($query, array('log_access' => $login, 'log_pass' => $pass));

        if (empty($result))
            return false;

        return $obj->bind($result);
    }
}
