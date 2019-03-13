<?php

class AppLib_Model_Company
    extends eVias_ArrayObject_Db
{
    protected $_tableName = "e_company";
    protected $_pk        = "id_company";
    protected $_sequence  = "e_company_id_company_seq";
    protected $_fields    = array(
        "title",
        "description",
        "address",
        "zipcode",
        "city",
        "country",
        "phone",
        "email",
        "numtav",
        "date_created",
        "date_updated",);
    
    protected $_defaults = array(
        "title" => "eVias Service",
        "description" => "This company must be configured.",
        "address" => "742 Evergreen terrace",
        "zipcode" => "95370",
        "city"    => "Springfield, CA",
        "country" => "us",
        "phone"   => "001 555-3223",
        "email"   => "default@e-restaurant.eu",
        "numtav"  => "TVA BE 2988815294");

    static private $_instance = null;
    protected $_white         = false;
    private   $_restaurants   = array();

    static public function getInstance()
    {
        if (null === self::$_instance) {
            try {
                self::$_instance = self::getList(); 
                self::$_instance = self::$_instance[0];

                if (null === self::$_instance)
                    throw new Exception;

                /* Check if company data must be configured. */
                foreach (self::$_instance->_defaults as $field => $default_val) {
                    if (self::$_instance->$f == $default_val) {
                        self::$_instance->_white = true;
                        break;
                    }
                }
            }
            catch (Exception $e) {
                self::$_instance = new self;
                self::$_instance->_setDefaults();

                self::$_instance->save();
            }
        }

        return self::$_instance;
    }

    public function getRestaurant($restaurant_id = null, $force_query = false)
    {
        if (null === $restaurant_id)
            $restaurant_id = 1;

        if (!$force_query && isset($this->_restaurants[$restaurant_id]))
            return $this->_restaurants[$restaurant_id];

        try {
            /* Try and fetch by ID */
            $restaurant = AppLib_Model_Restaurant::loadById($restaurant_id);
        }
        catch (Exception $e) {
            if ($restaurant_id == 1) {
                /* JiT creation */
                $restaurant = new AppLib_Model_Restaurant;
                $restaurant->setDefaultValues();

                /* No _sequence is set on Model_Restaurant! */
                $restaurant->insert();
            }
            else
                throw new AppLib_Model_Exception(
                        sprintf("An error occured: '%s'", $e->getMessage())); 
        }

        $this->_restaurants[$restaurant_id] = $restaurant;
        return $restaurant;
    }

    public function isWhite()
    {
        return $this->_white;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getLegalAddress($as_html = true)
    {
        $eol = "<br />";
        if (! $as_html)
            $eol = "\n";

        return sprintf("%s{$eol}%s-%s %s{$eol}Tel. Num.: %s{$eol}%s{$eol}",
            $this->address,
            strtoupper($this->country), $this->zipcode, $this->city, 
            $this->phone,
            $this->numtav);
    }

    protected function _setDefaults()
    {
        foreach ($this->_defaults as $f => $v) {
            $this->$f = $v;
        }
        /* Set object to be "white". */
        $this->_white = true;
    }
}
