<?php

class AppLib_Service_Booking
    extends AppLib_Service_Abstract
{
    /**
     * getBookings
     *
     * @params array $params
     * @return array
     * @throws AppLib_Service_Fault on application error
     **/
    public function getBookings(array $params = array())
    {
        try {
            /* Configure query */
            list($filter, $sql_params) = $this->getFilter($params["filter"], array(
                    "id_restaurant" => array("operator" => "=")));

            $conditions = array();

            /* Execute query */
            $restaurants = AppLib_Model_Booking::getList(array(
                "as_array"   => !$this->getReturnObjects(),
                "parameters" => $sql_params,
                "conditions" => array_merge($filter, $conditions),
                "order"      => "e_booking.date_book_start DESC"));

            return $restaurants;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("booking/getBookings: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("booking/getBookings: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * addBooking
     *
     * @params array $params
     * @return boolean
     * @throws AppLib_Service_Fault on invalid arguments
     **/
    public function addBooking(array $params)
    {
        $this->checkFields($params, array(
            "id_booking_type",
            "id_restaurant",
            "id_booked_object",
            "count_people",
            "date_book_start",
            "time_book_start"));

        if (empty($params["id_customer"]) && empty($params["customer"]))
            throw new AppLib_Service_Fault("Missing customer parameters.");

        try {
            /* validate parameters (and load objects) */
            list($restaurant, , $customer) = $this->validateDbRow($params, array(
                "id_restaurant" => array(
                    "class"    => "AppLib_Model_Restaurant",
                    "required" => true),
                "id_booking_type" => array(
                    "class"    => "AppLib_Model_BookingType",
                    "required" => true),
                "id_customer"   => array(
                    "class"    => "AppLib_Model_Customer",
                    "required" => false),
                "date_book_start" => array(
                    "match"    => "/^([\w]{3} )?([0-9]{2}[\/\-][0-9]{2}[\/\-][0-9]{4})$/",
                    "required" => true),
                "date_book_end" => array(
                    "match"    => "/^([\w]{3} )?([0-9]{2}[\/\-][0-9]{2}[\/\-][0-9]{4})$/",
                    "required" => false),
                "time_book_start" => array(
                    "match"    => "/^([0-9]{2}):([0-9]{2})$/",
                    "required" => true),
                "time_book_end" => array(
                    "match"    => "/^([0-9]{2}):([0-9]{2})$/",
                    "required" => !empty($params["date_book_end"])),
            ));

            /* load Object. */
            switch ($params["id_booking_type"]) {
                case AppLib_Model_Booking::TYPE_ROOM:
                    $object = AppLib_Model_Room::loadById((int) $params["id_booked_object"]);
                    break;
                case AppLib_Model_Booking::TYPE_TABLE:
                    $object = AppLib_Model_RoomTable::loadById((int) $params["id_booked_object"]);
                    break;
            }

            /* First save customer */
            if (empty($params["id_customer"])) {
                if (! is_array($params["customer"]))
                    throw new eVias_ArrayObject_Exception;

                $service  = new AppLib_Service_Customer(array("return_objects" => true));
                $callp = array(
                    "name" => $params["customer"]["realname"],
                    "phone" => $params["customer"]["phone"],
                    "email" => $params["customer"]["email"]);
                $customer = $service->editCustomer($restaurant->id_restaurant, $callp);
            }

            /* configure e_booking DateTime. */
            $date_start = $this->dateToSQL($params["date_book_start"]);
            $time_start = $params["time_book_start"];
            $date_end   = !empty($params["date_book_end"])
                    ? $this->dateToSQL($params["date_book_end"])
                    : $this->dateTimeIncrement($date_start, $time_start);
            $time_end   = !empty($params["date_book_end"])
                    ? $params["time_book_end"]
                    : $this->timeIncrementByDefaultPeriod($time_start);

            $book_start = "{$date_start} {$time_start}";
            $book_end   = "{$date_end} {$time_end}";

            /* Process saving of e_booking entry. */
            $booking = new AppLib_Model_Booking;
            $booking->id_booking_type  = $params["id_booking_type"];
            $booking->id_restaurant    = $restaurant->getPK();
            $booking->id_customer      = $customer->getPK();
            $booking->id_booked_object = $object->getPK();
            $booking->count_people     = $params["count_people"];
            $booking->date_book_start  = $book_start;
            $booking->date_book_end    = $book_end;

            if (! empty($params["id_booking"]))
                /* Update Mode */
                $booking->id_booking = $params["id_booking"];

            $booking->save();

            if ($this->getReturnObjects())
                return $booking;

            return $booking->getArrayCopy();
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("booking/addBooking: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("booking/addBooking: '%s'.", $e2->getMessage()));
        }
    }


    /**
     * deleteBooking
     *
     * @params integer  $restaurant_id
     * @return boolean
     * @throws AppLib_Service_Fault on invalid arguments
     **/
    public function deleteBooking($booking_id)
    {
        try {
            $booking = AppLib_Model_Booking::loadById($booking_id);

            $condition  = sprintf("id_booking = %d", $booking_id);
            $booking->delete($condition);
            return true;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("booking/deleteBooking: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("booking/deleteBooking: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * getFirstObjectAvailable
     *
     * @param array $params
     * @return AppLib_Model_Room|AppLib_Model_RoomTable|array
     * @throws AppLib_Service_Fault
     */
    public function getObjectsAvailable(array $params)
    {
        $this->checkFields($params, array(
            "id_restaurant",
            "id_booking_type",
            "count_people",
            "date_book_start",
            "time_book_start"));

        try {
            /* restaurant + DateTime validation */
            $this->validateDbRow($params, array(
                "id_restaurant" => array(
                    "class"    => "AppLib_Model_Restaurant",
                    "required" => true),
                "date_book_start" => array(
                    "match"    => "/^([\w]{3} )?([0-9]{2}[\/\-][0-9]{2}[\/\-][0-9]{4})$/",
                    "required" => true),
                "date_book_end" => array(
                    "match"    => "/^([\w]{3} )?([0-9]{2}[\/\-][0-9]{2}[\/\-][0-9]{4})$/",
                    "required" => false),
                "time_book_start" => array(
                    "match"    => "/^([0-9]{2}):([0-9]{2})$/",
                    "required" => true),
                "time_book_end" => array(
                    "match"    => "/^([0-9]{2}):([0-9]{2})$/",
                    "required" => !empty($params["date_book_end"])),
            ));

            /* Validate booking_type */
            switch ($params["id_booking_type"]) {
                case AppLib_Model_Booking::TYPE_ROOM:
                    $class = "AppLib_Model_Room";
                    break;

                case AppLib_Model_Booking::TYPE_TABLE:
                    $class = "AppLib_Model_RoomTable";
                    break;

                default:
                    throw new eVias_ArrayObject_Exception;
            }

            /* format dates for SQL */
            $date_start = $this->dateToSQL($params["date_book_start"]);
            $time_start = $params["time_book_start"];
            $date_end   = !empty($params["date_book_end"])
                    ? $this->dateToSQL($params["date_book_end"])
                    : $this->dateTimeIncrement($date_start, $time_start);
            $time_end   = !empty($params["date_book_end"])
                    ? $params["time_book_end"]
                    : $this->timeIncrementByDefaultPeriod($time_start);

            /* Execute availability query */
            $tobj = new $class;

            $available_params = array(
                "rid"                 => $params["id_restaurant"],
                "timestamp_start"     => "{$date_start} {$time_start}",
                "timestamp_end"       => "{$date_end} {$time_end}",
                "count_people"        => $params["count_people"]);
            list($sql, $sql_params) = $tobj->getAvailableSQL($available_params);

            $result = $tobj->getAdapter()
                           ->fetchAll($sql, $sql_params);

            $availability_perfect_match = true;
            if (empty($result)) {
                /* Algorithm for finding next possible availabilities
                    - Check for next half hour
                    - Check for combining tables if missing places is less than..
                 * Break condition is next day. */
                do {
                    $availability_perfect_match = false;

                    list($sql, $sql_params) = $tobj->getNextAvailableSQL($available_params);

                    $result = $tobj->getAdapter()
                                   ->fetchAll($sql, $sql_params);

                    if ($date_start != $this->dateTimeIncrement($date_start, $time_start)
                        || $date_end != $this->dateTimeIncrement($date_end, $time_end))
                        /* Next fetch would check for next day. */
                        break;

                    $time_start = $this->timeIncrementByDefaultPeriod($time_start);
                    $time_end   = $this->timeIncrementByDefaultPeriod($time_end);

                    $available_params["timestamp_start"] = "{$date_start} {$time_start}";
                    $available_params["timestamp_end"]   = "{$date_end} {$time_end}";
                }
                while (empty($result));
            }

            if ($this->getReturnObjects()) {
                $objects = array();
                foreach ($result as $item) {
                    $obj = new $class;
                    $obj->bind($item);
                    $obj->availability_start = $available_params["timestamp_start"];
                    $obj->availability_end   = $available_params["timestamp_end"];
                    $obj->availability_perfect_match = $availability_perfect_match;

                    $objects[] = $obj;
                }
                return $objects;
            }

            return $result;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("booking/getObjectsAvailable: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("booking/getObjectsAvailable: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * getRooms
     *
     * @params array $params
     * @return array
     * @throws AppLib_Service_Fault on application error
     **/
    public function getRooms(array $params = array())
    {
        /* XXX id_restaurant mandatory */
        try {
            /* Configure query */
            list($filter, $sql_params) = $this->getFilter($params["filter"], array(
                    "id_restaurant" => array("operator" => "="),
                    "id_room"       => array("operator" => "in")));

            $conditions = array();

            /* Execute query */
            $rooms = AppLib_Model_Room::getList(array(
                "as_array"   => !$this->getReturnObjects(),
                "parameters" => $sql_params,
                "conditions" => array_merge($filter, $conditions)));

            return $rooms;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("booking/getRooms: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("booking/getRooms: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * addRoom
     *
     * @params array $params
     * @return boolean
     * @throws AppLib_Service_Fault on invalid arguments
     **/
    public function addRoom(array $params)
    {
        $this->checkFields($params, array(
            "id_restaurant",
            "title",));

        try {
            /* Validate restaurant */
            AppLib_Model_Restaurant::loadById($params["id_restaurant"]);

            if (isset($params["tables"]) && !is_array($params["tables"]))
                throw Exception("Tables parameter must be an array.");
            elseif (! isset($params["tables"]))
                $params["tables"] = array();

            /* Process */
            $room = new AppLib_Model_Room;
            $room->id_restaurant = $params["id_restaurant"];
            $room->title         = $params["title"];
            $room->is_bookable   = 'f'; /* XXX $params["is_bookable"]; */

            if (! empty($params["id_room"]))
                /* Update Mode */
                $room->id_room = $params["id_room"];

            $room->save();

            foreach ($params["tables"] as $idx => $t_spec) {
                $rt_params = array(
                    "id_room" => $room->getPK(),
                    "room_position"=> $t_spec["room_position"],
                    "table_number" => $t_spec["table_number"],
                    "count_places" => $t_spec["count_places"]);

                if (isset($t_spec["id_table"]))
                    /* update existing table */
                    $rt_params["id_table"] = $t_spec["id_table"];

                $this->addRoomTable($rt_params);
            }

            if ($this->getReturnObjects())
                return $room;

            return $room->getArrayCopy();
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("booking/addRoom: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("booking/addRoom: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * deleteRoom
     *
     * @params integer  $restaurant_id
     * @return boolean
     * @throws AppLib_Service_Fault on invalid arguments
     **/
    public function deleteRoom($room_id)
    {
        try {
            $room = AppLib_Model_Room::loadById($room_id);

            $condition  = sprintf("id_room = %d", $room_id);
            $room->delete($condition);
            return true;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("booking/deleteRoom: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("booking/deleteRoom: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * getRoomTables
     *
     * @params array $params
     * @return array
     * @throws AppLib_Service_Fault on application error
     **/
    public function getRoomTables(array $params = array())
    {
        /* XXX id_room should be mandatory */
        try {
            /* Configure query */
            list($filter, $sql_params) = $this->getFilter($params["filter"], array(
                    "id_restaurant" => array("operator" => "="),
                    "id_room" => array("operator" => "="),
                    "id_table" => array("operator" => "in")));

            $conditions = array();

            /* Execute query */
            $restaurants = AppLib_Model_RoomTable::getList(array(
                "as_array"   => !$this->getReturnObjects(),
                "parameters" => $sql_params,
                "conditions" => array_merge($filter, $conditions)));

            return $restaurants;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("booking/getRoomTables: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("booking/getRoomTables: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * addRoomTable
     *
     * @params array $params
     * @return boolean
     * @throws AppLib_Service_Fault on invalid arguments
     **/
    public function addRoomTable(array $params)
    {
        $this->checkFields($params, array(
            "id_room",
            "table_number",
            "count_places"));

        try {
            /* Validate restaurant */
            AppLib_Model_Room::loadById($params["id_room"]);

            /* Process */
            $table = new AppLib_Model_RoomTable;
            $table->id_room         = $params["id_room"];
            $table->table_number    = (int) $params["table_number"];
            $table->count_places    = (int) $params["count_places"];
            $table->room_position   = $params["room_position"];

            if (! empty($params["id_table"]))
                /* Update Mode */
                $table->id_table = $params["id_table"];

            $table->save();

            if ($this->getReturnObjects())
                return $table;

            return $table->getArrayCopy();
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("booking/addRoomTable: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("booking/addRoomTable: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * deleteRoomTable
     *
     * @params integer  $restaurant_id
     * @return boolean
     * @throws AppLib_Service_Fault on invalid arguments
     **/
    public function deleteRoomTable($table_id)
    {
        try {
            $table = AppLib_Model_RoomTable::loadById($table_id);

            $condition  = sprintf("id_table = %d", $table_id);
            $table->delete($condition);
            return true;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("booking/deleteRoomTable: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("booking/deleteRoomTable: '%s'.", $e2->getMessage()));
        }
    }

    public function timeIncrementByDefaultPeriod($time)
    {
        list($h, $m) = explode(":", $time);

        $m = (int) $m;
        $m = $m + 30;

        if ($m >= 60) {
            $m = $m - 60;
            $h = $h + 1;

            if ($h == 24)
                $h = 0;
        }

        $h = str_pad((string) $h, 2, '0', STR_PAD_LEFT);
        $m = str_pad((string) $m, 2, '0', STR_PAD_LEFT);

        return "{$h}:{$m}";
    }

    public function dateTimeIncrement($date, $time)
    {
        $plus_one_day = date("Y-m-d", strtotime("+ 1day", strtotime($date)));
        list($inc_h,) = explode(":", $this->timeIncrementByDefaultPeriod($time));

        if ($inc_h == "00")
            return $plus_one_day;

        return $date;
    }


}