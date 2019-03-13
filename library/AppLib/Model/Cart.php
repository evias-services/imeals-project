<?php

class AppLib_Model_Cart
    extends eVias_ArrayObject_Db
{
    private $_usr_sess = null;
    private $_session  = null;
    private $_items      = array();
    private $_quantities = array();

    protected $_tableName = "e_cart";
    protected $_pk        = "id_cart";
    protected $_sequence  = "e_cart_id_cart_seq";
    protected $_fields    = array(
        "id_user_session",
        "date_created",
        "date_updated",);

    static private $_instance = null;
    static public function getInstance()
    {
        if (!isset(self::$_instance))
            self::$_instance = new self;

        return self::$_instance;
    }

    public function loadBySession(Zend_Session_Namespace $sess)
    {
        $this->_session = $sess;

        $this->loadUserSession($sess);    

        try {
            /* Cart retrieval */
            $this->_fetch(
                "id_user_session = :id_user_session",
                array("id_user_session" => $this->_usr_sess->id_user_session));
        }
        catch (eVias_ArrayObject_Exception $e) {
            /* JIT Cart creation */
            $this->id_restaurant   = $this->_usr_sess->id_restaurant;
            $this->id_user_session = $this->_usr_sess->id_user_session;
            $this->save();
        }

        return $this;
    }

    private function loadUserSession(Zend_Session_Namespace $sess)
    {
        $this->_usr_sess = new eVias_ArrayObject_Db;
        $this->_usr_sess
             ->setTable("e_user_session")
             ->setPrimaryKey("id_user_session")
             ->setSequence("e_user_session_id_user_session_seq")
             ->setFields(array(
                "sess_id",
                "date_created",
                "date_updated"));

        if (isset($this->_session->sess_id)) {
            /* session namespace gotten has valid session identity */

            try {
                /* Try fetching currently active session 
                   by session_id() */
                $this->_usr_sess->_fetch(
                    "sess_id = :sid",
                    array("sid" => $this->_session->sess_id));
            }
            catch (eVias_ArrayObject_Exception $e) {
                /* save user_session entry */

                $this->_usr_sess->sess_id       = $this->_session->sess_id;
                $this->_usr_sess->save();
            }
        }
        else {
            /* session namespace gotten has no session identifier */

            $this->_usr_sess->sess_id = Zend_Session::getId();
            $this->save();

            /* reference passed object modification */
            $sess->sess_id            = $this->_usr_sess->sess_id;
        }
        return $this;
    }

    public function getItems($force = false)
    {
        if ($force || empty($this->_items)) {
            $sql = "
                SELECT 
                    id_item, 
                    count(*) as quantity 
                FROM
                    e_cart_item
                WHERE
                    id_cart = :cid
                GROUP BY   
                    id_item";

            $ids = $this->getAdapter()
                        ->fetchAll($sql,array("cid" => $this->id_cart));

            $this->_items = array();
            foreach ($ids as $idx => $item_data) {
                $this->_quantities[$item_data["id_item"]] = $item_data["quantity"];
                
                $item = AppLib_Model_Item::loadById($item_data["id_item"]);

                $this->_items[$item->id_item] = $item;
                unset($item);
            }
        }
        
        return $this->_items;
    }

    public function getQuantities()
    {
        $sql = "
            SELECT
                id_item,
                count(*) as quantity
            FROM e_cart_item
            WHERE id_cart = :cid
            GROUP BY id_item
        ";

        $rows = $this->getAdapter()->fetchAll($sql, array("cid" => $this->id_cart));

        foreach ($rows as $idx => $row) 
            $this->_quantities[$row["id_item"]] = $row["quantity"];

        return $this->_quantities;
    }

    public function addItem(AppLib_Model_Item $item)
    {
        if (empty($item->id_item))
            throw new InvalidArgumentException("Item must be loaded.");
        elseif (empty($this->id_cart))
            throw new InvalidArgumentException("Cart must be saved first.");

        $sql = "
            INSERT INTO e_cart_item
                (id_cart, id_item, date_updated)
            VALUES (
                :cid, :iid, NULL
            )
        ";

        $this->getAdapter()
             ->query($sql, array(
                "cid" => $this->id_cart,
                "iid" => $item->id_item,));

        /* Make sure all items are loaded. */
        $this->getItems(true);
        return $this;
    }
    
    public function rmItem(AppLib_Model_Item $item)
    {
    	if (empty($item->id_item))
    		throw new InvalidArgumentException("Item must be loaded.");
    	elseif (empty($this->id_cart))
    		throw new InvalidArgumentException("Cart must be saved first.");
    	
    	$sql = "DELETE FROM e_cart_item WHERE id_item = :iid AND id_cart = :cid";
    	$this->getAdapter()->query($sql, array("iid" => $item->id_item, "cid" => $this->id_cart));
    }

    public function rmCustoms(AppLib_Model_Item $item)
    {
     	if (empty($item->id_item))
    		throw new InvalidArgumentException("Item must be loaded.");
    	elseif (empty($this->id_cart))
    		throw new InvalidArgumentException("Cart must be saved first.");
        
        $sql = "DELETE FROM e_cart_item_customization WHERE id_item = :iid and id_cart = :cid";
    	$this->getAdapter()->query($sql, array("iid" => $item->id_item, "cid" => $this->id_cart));
    }

    public function clear()
    {
        $sql = "
            DELETE 
            FROM e_cart_item
            WHERE id_cart = :cid";

        $this->getAdapter()
             ->query($sql, array(
                "cid" => $this->id_cart,));
    }

    public function getTotalPrice()
    {
        return 0;
    }

}
