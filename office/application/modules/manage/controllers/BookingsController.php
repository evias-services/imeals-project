<?php

class Manage_BookingsController
    extends BackLib_Controller_Action
{
    public function indexAction()
    {
        $filter  = $this->_getParam("filter", array());
        $page    = $this->_getParam('page', 1);

        $proxy   = new AppLib_Service_Proxy;
        $proxy->setService("booking", array("return_objects" => true));

        $result  = $proxy->__call("getBookings", array("filter" => $filter));

        $icnt = 25;
        $adapter    = new Zend_Paginator_Adapter_Array($result);
        $paginator  = new Zend_Paginator($adapter);
        $paginator->setItemCountPerPage($icnt);
        $paginator->setCurrentPageNumber($page);

        $this->view->paginator = $paginator;
        $this->view->filter    = $filter;
    }

    public function listBookingActionGrabberAction()
    {
        $bookings = $this->_getParam("bookings", array());
        $action   = $this->_getParam("selections_action", "");

        if (empty($bookings) || empty($action))
            $this->_redirect("/manage/bookings/index");

        $method     = strtolower($action) . "BookingAction";
        if (! method_exists($this, $method)) {
            $this->_helper
                ->getHelper("FlashMessenger")
                ->setNamespace("errors")
                ->addMessage(BackLib_Lang::tr("txt_error_invalidrequest"));
        }

        $action .= "-booking";
        $this->_forward($action, "bookings", "manage",
                array("bookings" => $bookings));
    }

    public function deleteBookingAction()
    {
        if ($this->getRequest()->isPost()
            && $this->_getParam("is_confirmed", null) !== null) {

            $bookings = $this->_getParam("bookings", array());

            $proxy   = new AppLib_Service_Proxy;
            $proxy->setService("booking");
            foreach ($bookings as $bid)
                $proxy->__call("deleteBooking", $bid);

            $this->_helper
                ->getHelper("FlashMessenger")
                ->setNamespace("messages")
                ->addMessage(BackLib_Lang::tr("txt_info_booking_delete"));

            $this->_redirect("/manage/bookings/index");
        }
        elseif ($this->getRequest()->isPost()) {

            $bids    = $this->_getParam("bookings", array());
            $filter  = array("id_booking" => $bids);

            $proxy    = new AppLib_Service_Proxy;
            $proxy->setService("booking", array("return_objects" => true));
            $bookings = $proxy->__call("getBookings", array("filter" => $filter));

            $this->view->bookings = $bookings;
        }
        else {
            $this->_helper
                ->getHelper("FlashMessenger")
                ->setNamespace("errors")
                ->addMessage(BackLib_Lang::tr("txt_error_invalidrequest"));

            $this->_redirect("/manage/restaurant/index");
        }
    }

    public function addBookingAction()
    {
        if ($this->getRequest()->isPost())
        {
            $input    = $this->getRequest()->getParams();
            $row_data = $input['booking'];

            $proxy    = new AppLib_Service_Proxy;
            $proxy->setService("booking", array("return_objects" => true));
            try {

                $booking = $proxy->__call("addBooking", $row_data);

                $save_msg  = BackLib_Lang::tr("txt_info_booking_save");
                switch ($booking->id_booking_type) {
                    case AppLib_Model_Booking::TYPE_ROOM:
                        $save_what = BackLib_Lang::tr("opt_booking_type_room");
                        break;
                    case AppLib_Model_Booking::TYPE_TABLE:
                        $save_what = BackLib_Lang::tr("opt_booking_type_table");
                        break;
                }

                $dstart = date("d-m-Y H:i", strtotime($booking->date_book_start));
                $dend   = date("d-m-Y H:i", strtotime($booking->date_book_end));

                $save_msg = sprintf($save_msg, $save_what,
                                   $booking->getCustomer()->realname,
                                   $dstart,
                                   $dend);

                $this->_helper
                     ->getHelper("FlashMessenger")
                     ->setNamespace("messages")
                     ->addMessage($save_msg);
            }
            catch (Exception $e) {
                $this->_helper
                     ->getHelper("FlashMessenger")
                     ->setNamespace("errors")
                     ->addMessage(sprintf(BackLib_Lang::tr("txt_error_booking_save"), $e->getMessage()));
            }

            $this->_redirect("/manage/bookings/index");
        }

        $proxy    = new AppLib_Service_Proxy;
        $proxy->setService("restaurant", array("return_objects" => true));

        $this->view->restaurants = $proxy->__call("getRestaurants", array("filter" => array()));
    }

    public function modifyBookingAction()
    {
        $this->view->layout()->disableLayout();

        if ($this->getRequest()->isGet()) {

            $itemID = $this->_getParam("bid");
            $filter = array("id_booking" => $itemID);

            $proxy    = new AppLib_Service_Proxy;
            $proxy->setService("booking", array("return_objects" => true));

            $bookings = $proxy->__call("getBookings", array("filter" => $filter));

            $this->view->booking = array_pop($bookings);

            $proxy->setService("restaurant", array("return_objects" => true));

            $this->view->restaurants = $proxy->__call("getRestaurants", array("filter" => array()));
            $this->render("add-booking");
        }
    }

    public function getAvailableObjectsAction()
    {
        if (! $this->getRequest()->isXmlHttpRequest())
            $this->_redirect("/manage/bookings/index");

        $this->view->layout()->disableLayout();

        $rid    = $this->_getParam("rid", null);
        $tid    = $this->_getParam("tid", null);
        $cp     = $this->_getParam("cp", null);
        $dstart = $this->_getParam("dstart", null);
        $dend   = $this->_getParam("dend", null);
        $tstart = $this->_getParam("tstart", null);
        $tend   = $this->_getParam("tend", null);

        try {
            $params = array(
                "id_restaurant"   => $rid,
                "id_booking_type" => $tid,
                "count_people"    => $cp,
                "date_book_start" => $dstart,
                "time_book_start" => $tstart,
                "date_book_end"   => $dend,
                "time_book_end"   => $tend
            );

            $service = AppLib_Service_Factory::getService("booking", array("return_objects" => true));
            $this->view->objects = $service->getObjectsAvailable($params);
        }
        catch (AppLib_Service_Fault $e) {
            die($e->getMessage());
            $this->view->objects = array();
        }
    }

    /* ROOMS */

    public function listRoomsAction()
    {
        $filter  = $this->_getParam("filter", array());
        $page    = $this->_getParam('page', 1);

        $proxy   = new AppLib_Service_Proxy;
        $proxy->setService("booking", array("return_objects" => true));

        $result  = $proxy->__call("getRooms", array("filter" => $filter));

        $icnt = 25;
        $adapter    = new Zend_Paginator_Adapter_Array($result);
        $paginator  = new Zend_Paginator($adapter);
        $paginator->setItemCountPerPage($icnt);
        $paginator->setCurrentPageNumber($page);

        $this->view->paginator = $paginator;
        $this->view->filter    = $filter;
    }

    public function listRoomsActionGrabberAction()
    {
        $rooms  = $this->_getParam("rooms", array());
        $action = $this->_getParam("selections_action", "");

        if (empty($rooms) || empty($action))
            $this->_redirect("/manage/bookings/list-rooms");

        $method     = strtolower($action) . "RoomAction";
        if (! method_exists($this, $method)) {
            $this->_helper
                ->getHelper("FlashMessenger")
                ->setNamespace("errors")
                ->addMessage(BackLib_Lang::tr("txt_error_invalidrequest"));
        }

        $action .= "-room";
        $this->_forward($action, "bookings", "manage",
                array("rooms" => $rooms));
    }

    public function deleteRoomAction()
    {
        if ($this->getRequest()->isPost()
            && $this->_getParam("is_confirmed", null) !== null) {

            $rooms = $this->_getParam("rooms", array());

            $proxy   = new AppLib_Service_Proxy;
            $proxy->setService("booking");
            foreach ($rooms as $rid)
                $proxy->__call("deleteRoom", $rid);

            $this->_helper
                ->getHelper("FlashMessenger")
                ->setNamespace("messages")
                ->addMessage(BackLib_Lang::tr("txt_info_room_delete"));

            $this->_redirect("/manage/bookings/list-rooms");
        }
        elseif ($this->getRequest()->isPost()) {

            $rids    = $this->_getParam("rooms", array());
            $filter  = array("id_room" => $rids);

            $proxy   = new AppLib_Service_Proxy;
            $proxy->setService("booking", array("return_objects" => true));
            $rooms   = $proxy->__call("getRooms", array("filter" => $filter));

            $this->view->rooms = $rooms;
        }
        else {
            $this->_helper
                ->getHelper("FlashMessenger")
                ->setNamespace("errors")
                ->addMessage(BackLib_Lang::tr("txt_error_invalidrequest"));

            $this->_redirect("/manage/bookings/list-rooms");
        }
    }

    public function addRoomAction()
    {
        if ($this->getRequest()->isPost())
        {
            $input    = $this->getRequest()->getParams();
            $row_data = $input['room'];

            /* format checkbox parameter */
            /* XXX */
//            $row_data["is_bookable"] = !empty($params["is_bookable"]);

            $proxy    = new AppLib_Service_Proxy;
            $proxy->setService("booking", array("return_objects" => true));
            try {
                $room = $proxy->__call("addRoom", $row_data);

                $this->_helper
                     ->getHelper("FlashMessenger")
                     ->setNamespace("messages")
                     ->addMessage(sprintf(BackLib_Lang::tr("txt_info_room_save"),
                                          $room->getTitle(),
                                          $room->getRestaurant()->getTitle()));
            }
            catch (Exception $e) {
                $this->_helper
                     ->getHelper("FlashMessenger")
                     ->setNamespace("errors")
                     ->addMessage(sprintf(BackLib_Lang::tr("txt_error_room_save"), $e->getMessage()));
            }

            $this->_redirect("/manage/bookings/list-rooms");
        }

        $proxy    = new AppLib_Service_Proxy;
        $proxy->setService("restaurant", array("return_objects" => true));

        $this->view->init_javascript = false;
        $this->view->restaurants = $proxy->__call("getRestaurants", array("filter" => array()));
    }

    public function modifyRoomAction()
    {
        $this->view->layout()->disableLayout();

        if ($this->getRequest()->isGet()) {

            $itemID = $this->_getParam("rid");
            $filter = array("id_room" => $itemID);

            $proxy    = new AppLib_Service_Proxy;
            $proxy->setService("booking", array("return_objects" => true));

            $rooms = $proxy->__call("getRooms", array("filter" => $filter));

            $this->view->room = array_pop($rooms);

            $proxy->setService("restaurant", array("return_objects" => true));

            $this->view->init_javascript = true;
            $this->view->restaurants = $proxy->__call("getRestaurants", array("filter" => array()));
            $this->render("add-room");
        }
    }

    /** TABLES **/

    public function listTablesAction()
    {
        $filter  = $this->_getParam("filter", array());
        $page    = $this->_getParam('page', 1);

        $proxy   = new AppLib_Service_Proxy;
        $proxy->setService("booking", array("return_objects" => true));

        $result  = $proxy->__call("getRoomTables", array("filter" => $filter));

        $icnt = 40;
        $adapter    = new Zend_Paginator_Adapter_Array($result);
        $paginator  = new Zend_Paginator($adapter);
        $paginator->setItemCountPerPage($icnt);
        $paginator->setCurrentPageNumber($page);

        $this->view->paginator = $paginator;
        $this->view->filter    = $filter;
    }

    public function listTablesActionGrabberAction()
    {
        $tables = $this->_getParam("tables", array());
        $action   = $this->_getParam("selections_action", "");

        if (empty($tables) || empty($action)) {
            $this->_helper
                ->getHelper("FlashMessenger")
                ->setNamespace("errors")
                ->addMessage(BackLib_Lang::tr("error_delete_nodata"));
            $this->_redirect("/manage/bookings/list-tables");
        }

        $method     = strtolower($action) . "TableAction";
        if (! method_exists($this, $method)) {
            $this->_helper
                ->getHelper("FlashMessenger")
                ->setNamespace("errors")
                ->addMessage(BackLib_Lang::tr("txt_error_invalidrequest"));
        }

        $action .= "-table";
        $this->_forward($action, "bookings", "manage",
                array("tables" => $tables));
    }

    public function deleteTableAction()
    {
        if ($this->getRequest()->isPost()
            && $this->_getParam("is_confirmed", null) !== null) {

            $tables = $this->_getParam("tables", array());

            $proxy   = new AppLib_Service_Proxy;
            $proxy->setService("booking");
            foreach ($tables as $tid)
                $proxy->__call("deleteRoomTable", $tid);

            $this->_helper
                ->getHelper("FlashMessenger")
                ->setNamespace("messages")
                ->addMessage(BackLib_Lang::tr("txt_info_room_table_delete"));

            $this->_redirect("/manage/bookings/list-tables");
        }
        elseif ($this->getRequest()->isPost()) {

            $tids    = $this->_getParam("tables", array());
            $filter  = array("id_table" => $tids);

            $proxy    = new AppLib_Service_Proxy;
            $proxy->setService("booking", array("return_objects" => true));
            $tables   = $proxy->__call("getRoomTables", array("filter" => $filter));

            $this->view->tables = $tables;
        }
        else {
            $this->_helper
                ->getHelper("FlashMessenger")
                ->setNamespace("errors")
                ->addMessage(BackLib_Lang::tr("txt_error_invalidrequest"));

            $this->_redirect("/manage/bookings/list-tables");
        }
    }

    public function addTableAction()
    {
        if ($this->getRequest()->isXmlHttpRequest())
            $this->view->layout()->disableLayout();

        if ($this->getRequest()->isPost())
        {
            $input    = $this->getRequest()->getParams();
            $row_data = $input['table'];

            $proxy    = new AppLib_Service_Proxy;
            $proxy->setService("booking");
            try {
                $proxy->__call("addRoomTable", $row_data);

                $this->_helper
                     ->getHelper("FlashMessenger")
                     ->setNamespace("messages")
                     ->addMessage(BackLib_Lang::tr("txt_info_room_table_save"));
            }
            catch (Exception $e) {
                $this->_helper
                     ->getHelper("FlashMessenger")
                     ->setNamespace("errors")
                     ->addMessage(sprintf(BackLib_Lang::tr("txt_error_room_table_save"), $e->getMessage()));
            }

            $this->_redirect("/manage/bookings/list-tables");
        }

        $proxy = new AppLib_Service_Proxy;
        $proxy->setService("booking", array("return_objects" => true));
        $this->view->rooms = $proxy->__call("getRooms", array("filter" => array()));
    }

    public function modifyTableAction()
    {
        $this->view->layout()->disableLayout();

        if ($this->getRequest()->isGet()) {

            $itemID = $this->_getParam("tid");
            $filter = array("id_table" => $itemID);

            $proxy    = new AppLib_Service_Proxy;
            $proxy->setService("booking", array("return_objects" => true));

            $rooms  = $proxy->__call("getRooms", array("filter" => array()));
            $tables = $proxy->__call("getRoomTables", array("filter" => $filter));

            $this->view->table = array_pop($tables);
            $this->view->rooms = $rooms;
            $this->render("add-table");
        }
    }

}
