<?php

class AppLib_Model_User
    extends eVias_ArrayObject_Db
{
    protected $_tableName = "e_users";
    protected $_pk        = "id_e_user";
    protected $_sequence  = "e_users_id_e_user_seq";
    protected $_fields    = array(
        "id_acl_role",
        "login",
        "email",
        "salt",
        "public_salt",
        "password",
        "realname",
        "date_created",
        "date_updated",);

    private $_roleLabel = null;

    protected function _preDelete($where)
    {
        if (empty($where) || !is_string($where))
            throw new InvalidArgumentException("Model_User::_preDelete: Invalid where statement provided.");

        /* Delete dependencies:
            - e_restaurant_access
         **/
        $access_where = "id_e_user IN (SELECT id_e_user FROM e_users WHERE $where)";

        $raccess = new AppLib_Model_RestaurantAccess;
        $raccess->delete($access_where);
    }

    public function getIdentifier()
    {
        return $this->login;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getRoleLabel()
    {
        if (null === $this->_roleLabel)
            $this->_roleLabel = $this->getAdapter()->fetchOne("
                SELECT
                    title
                FROM
                    e_acl_role
                WHERE
                    id_acl_role = :role_id", array("role_id" => $this->id_acl_role));

        return $this->_roleLabel;
    }

    public function getAccessibleRestaurants()
    {
        return AppLib_Model_Restaurant::getList(array(
            "as_array"   => false,
            "fields"     => array(
                "r.id_restaurant",
                "r.title",
                "r.address",
                "r.zipcode",
                "r.city",
                "r.country",
                "r.phone",
                "r.email",
                "r.numtav",
                "r.date_created",
                "r.date_updated"),
            "alias"      => "r",
            "joins"      => array(
                "JOIN e_restaurant_access ra USING (id_restaurant)"),
            "conditions" => array(
                "ra.id_e_user = :user_id"),
            "parameters" => array(
                "user_id" => $this->id_e_user,)
        ));
    }

    /**
     *
     */
    static public function generateSalt()
    {
        $public_salt  = Zend_Registry::get("config")->authentication->public_salt;

        $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $r_max = strlen($chars);
        $word  = sprintf("Grégory Saive%s", microtime());
        for ($i = 0; $i < 32; ++$i)
            $word .= $chars[rand(0, $r_max)];
        $word .= sprintf("%sGrégory Saive", microtime());
        return md5($public_salt . $word);
    }

    /**
     *
     */
    static public function login($username, $password)
    {
        if (empty($username) || empty($password))
            return false;

        $public_salt  = Zend_Registry::get("config")->authentication->public_salt;

        $adapter = new Zend_Auth_Adapter_DbTable(Zend_Registry::get('db'));
        $adapter->setTableName('e_users')
                ->setIdentityColumn('login')
                ->setCredentialColumn('password')
                ->setIdentity($username)
                ->setCredential($password);

        $adapter->setCredentialTreatment(
           "MD5('{$public_salt}' || ? || salt)");

        $result = Zend_Auth::getInstance()->authenticate($adapter);
        if (! $result->isValid())
            return false;

        $row = $adapter->getResultRowObject(array(
            'id_e_user',
            'login',
            'email',
            'realname'));

        Zend_Auth::getInstance()
            ->getStorage()
            ->write($row);

        return true;
    }

    /**
     *
     */
    static public function is_auth()
    {
        return Zend_Auth::getInstance()->hasIdentity();
    }
}
