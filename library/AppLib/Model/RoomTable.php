<?php

class AppLib_Model_RoomTable
    extends AppLib_Model_Bookable
{
    protected $_tableName = "e_room_table";
    protected $_pk        = "id_table";
    protected $_sequence  = "e_room_table_id_table_seq";
    protected $_fields    = array(
        "id_room",
        "table_number",
        "room_position",
        "count_places",
        "date_created",
        "date_updated",);

    private $_room = null;

    protected function _preDelete($where)
    {
        if (empty($where) || !is_string($where))
            throw new InvalidArgumentException("Model_Room::_preDelete: Invalid where statement provided.");

        /* Delete dependencies:
            - e_booking[id_booking_type='TABLE']
         **/

        $bookings_where = "
            id_booking_type = (SELECT id_booking_type FROM e_booking_type WHERE title = 'TABLE')
            AND id_booked_object IN (SELECT id_table FROM e_room_table WHERE $where)";

        $booking = new AppLib_Model_Booking;
        $booking->delete($bookings_where);
    }

    public function getRoom()
    {
        if (! isset($this->_room))
            $this->_room = AppLib_Model_Room::loadById($this->id_room);

        return $this->_room;
    }

    public function getTitle($withRoom = false)
    {
        $room = "";
        if ($withRoom)
            $room = $this->getRoom()->getTitle() . ": ";
        return sprintf("%sTable %d", $room, $this->table_number);
    }

    public function getCountPlacesSQL()
    {
        return "e_room_table.count_places";
    }

    public function getObjectDependSQL()
    {
        return "id_room in (select id_room from e_room where id_restaurant = :rid)";
    }
}