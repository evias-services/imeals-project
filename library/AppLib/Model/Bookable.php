<?php

abstract class AppLib_Model_Bookable
    extends eVias_ArrayObject_Db
{
    abstract public function getCountPlacesSQL();
    abstract public function getObjectDependSQL();

    public function getCountPlaces()
    {
        $select = "
            select
                {$this->getCountPlacesSQL()}
            from
                {$this->tableName()}
            where
                {$this->tableName()}.{$this->primaryKey()} = :pk
        ";
        return $this->getAdapter()->fetchOne($select, array("pk" => $this->getPK()));
    }

    public function getAvailableSQL(array $params)
    {
        /* Available = table.count_places is big enough for
           input'ed people count AND fetchBookings conditions. */
        $sql = "
            select
              {$this->tableName()}.*,
              (" . $this->getCountPlacesSQL() . " - :cnt_people) as cnt_lost_places
            from
              {$this->tableName()}
              left join (
                select
                  {$this->tableName()}.{$this->primaryKey()} as id_object
                from
                  {$this->tableName()}
                where
                    :cnt_people <= " . $this->getCountPlacesSQL() . "
                    and " . $this->getObjectDependSQL() . "
                    and not exists (" . $this->fetchBookingsSQL() . ")
              )
              as matching_object on (
                    matching_object.id_object = {$this->tableName()}.{$this->primaryKey()})
            where
                matching_object.id_object IS NOT NULL
            order by
                cnt_lost_places ASC,
                {$this->tableName()}.id_room ASC
        ";

        switch (get_class($this)) {
            case "AppLib_Model_Room":
                $type = AppLib_Model_Booking::TYPE_ROOM;
                break;
            case "AppLib_Model_RoomTable":
                $type = AppLib_Model_Booking::TYPE_TABLE;
                break;
        }

        $sql_params = array(
            "rid"        => $params["rid"],
            "type"       => $type,
            "cnt_people" => !empty($params["count_people"]) ? $params["count_people"] : 1,
            "dstart"     => $params["timestamp_start"],
            "dend"       => $params["timestamp_end"],);

        return array($sql, $sql_params);
    }

    public function getNextAvailableSQL(array $params)
    {
        list($availableSQL, $sql_params) = $this->getAvailableSQL($params);
        return array (
            preg_replace(
                array("/:dstart/",
                      "/:dend/"),
                array("(:dstart::timestamp + '30 minutes')",
                      "(:dend::timestamp + '30 minutes')"),
                $availableSQL),
            array(
                "rid"        => $params["rid"],
                "type"       => AppLib_Model_Booking::TYPE_TABLE,
                "cnt_people" => !empty($params["count_people"]) ? $params["count_people"] : 1,
                "dstart"     => $params["timestamp_start"],
                "dend"       => $params["timestamp_end"],)
        );
    }

    public function fetchBookingsSQL()
    {
        /* WHERE conditions are:
           - input_date_start between booking_date_start and booking_date_end
           - input_date_end between booking_date_start and booking_date_end
           - booking_date_start between input_date_start and input_date_end
           - booking_date_end between input_date_start and input_date_end

         * These checks ensure that there is no booking with
         * dates in collision with the input dates. */
        $sql = "
            select
                b.*
            from
                e_booking b
            where
                b.id_booking_type = :type
                and b.id_booked_object = {$this->tableName()}.{$this->primaryKey()}
                and (
                    (" . $this->_getBetweenSQL(":dstart", "b.date_book_start", "b.date_book_end") . ")
                    or (" . $this->_getBetweenSQL(":dend", "b.date_book_start", "b.date_book_end") . ")
                    or (" . $this->_getBetweenSQL("b.date_book_start", ":dstart", ":dend") . ")
                    or (" . $this->_getBetweenSQL("b.date_book_end", ":dstart", ":dend") . ")
                )
        ";

        return $sql;
    }

    protected function _getBetweenSQL($input, $between, $and)
    {
        return "$input BETWEEN $between AND $and";
    }
}
