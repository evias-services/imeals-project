<?php

class AppLib_Model_Room
    extends AppLib_Model_Bookable
{
    protected $_tableName = "e_room";
    protected $_pk        = "id_room";
    protected $_sequence  = "e_room_id_room_seq";
    protected $_fields    = array(
        "id_restaurant",
        "title",
        "is_bookable",
        "date_created",
        "date_updated",);

    private $_restaurant = null;
    private $_tables = array();

    protected function _preDelete($where)
    {
        if (empty($where) || !is_string($where))
            throw new InvalidArgumentException("Model_Room::_preDelete: Invalid where statement provided.");

        /* Delete dependencies:
            - e_room_table
            - e_booking[id_booking_type=1]
         **/
        $tables_where = "id_room IN (SELECT id_room FROM e_room WHERE $where)";

        $table = new AppLib_Model_RoomTable;
        $table->delete($tables_where);

        $bookings_where = "
            id_booking_type = (SELECT id_booking_type FROM e_booking_type WHERE title = 'ROOM')
            AND id_booked_object IN (SELECT id_room FROM e_room WHERE $where)";

        $booking = new AppLib_Model_Booking;
        $booking->delete($bookings_where);
    }

    public function getRestaurant()
    {
        if (! isset($this->_restaurant))
            $this->_restaurant = AppLib_Model_Restaurant::loadById($this->id_restaurant);

        return $this->_restaurant;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getCountPlacesSQL()
    {
        return "
            coalesce(
                (select
                    sum(count_places)
                 from
                   e_room_table
                 where
                   id_room = e_room.id_room),
                0)
        ";
    }

    public function getObjectDependSQL()
    {
        return "is_bookable is true AND id_restaurant = :rid";
    }

    public function getTables()
    {
        if (empty($this->_tables)) {
            $this->_tables = AppLib_Model_RoomTable::getList(array(
                "as_array" => false,
                "conditions" => array("id_room = :rid"),
                "parameters" => array("rid" => $this->id_room)
            ));
        }

        return $this->_tables;
    }
}