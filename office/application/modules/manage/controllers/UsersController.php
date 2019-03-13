<?php

class Manage_UsersController
    extends BackLib_Controller_Action
{
    public function indexAction()
    {
        $this->_redirect("/manage/users/list-users");
    }

    public function listUsersAction()
    {
        $filter = $this->_getParam("filter", array());
        $page   = $this->_getParam('page', 1);

        $service = AppLib_Service_Factory::getService("user", array("return_objects" => true));
        $result  = $service->getUsers(array("filter" => $filter));

        $icnt = 20;
        $adapter    = new Zend_Paginator_Adapter_Array($result);
        $paginator  = new Zend_Paginator($adapter);
        $paginator->setItemCountPerPage($icnt);
        $paginator->setCurrentPageNumber($page);

        $this->view->paginator = $paginator;
        $this->view->filter    = $filter;
    }

    public function modifyUserAction()
    {
        $this->view->layout()->disableLayout();

        $user_service       = AppLib_Service_Factory::getService("user", array("return_objects" => true));
        $acl_service        = AppLib_Service_Factory::getService("acl", array("return_objects" => true));
        $restaurant_service = AppLib_Service_Factory::getService("restaurant", array("return_objects" => true));

        if ($this->getRequest()->isGet()) {

            $itemID   = $this->_getParam("uid");

            $users    = $user_service->getUsers(array(
                "filter" => array(
                    "user_id" => $itemID)));

            $this->view->roles       = $acl_service->listRoles(array("filter" => array()));
            $this->view->restaurants = $restaurant_service->getRestaurants(array("filter" => array()));
            $this->view->user = $users[0];
            $this->render("add-user");
        }
    }

    public function addUserAction()
    {
        $user_service       = AppLib_Service_Factory::getService("user", array("return_objects" => true));
        $acl_service        = AppLib_Service_Factory::getService("acl", array("return_objects" => true));
        $restaurant_service = AppLib_Service_Factory::getService("restaurant", array("return_objects" => true));

        if ($this->getRequest()->isPost()) {
            $input = $this->getRequest()->getParams();

            try {
                $row_data = $input["user"];

                /* unicity + selection presence */
                $restaurants = array_unique(array_diff($row_data["restaurants"], array("" => "")));
                $row_data["restaurants"] = $restaurants;

                $config   = Zend_Registry::get("config");
                $psalt    = $config->authentication->public_salt;

                $row_data["public_salt"] = $psalt;

                $user     = $user_service->addUser($row_data);

                $this->_helper
                     ->getHelper("FlashMessenger")
                     ->setNamespace("messages")
                     ->addMessage(sprintf(BackLib_Lang::tr("txt_info_user_save"),
                                          $user->realname));
            }
            catch (AppLib_Service_Fault $e) {
                $this->_helper
                     ->getHelper("FlashMessenger")
                     ->setNamespace("errors")
                     ->addMessage(sprintf(BackLib_Lang::tr("txt_error_user_save"), $e->getMessage()));
            }

            $this->_redirect("/manage/users/list-users");
        }

        $this->view->roles       = $acl_service->listRoles(array("filter" => array()));
        $this->view->restaurants = $restaurant_service->getRestaurants(array("filter" => array()));
    }

    public function deleteUserAction()
    {
        if ($this->getRequest()->isPost()
            && $this->_getParam("is_confirmed", null) !== null) {

            $users = $this->_getParam("users", array());

            $service = AppLib_Service_Factory::getService("user");
            foreach ($users as $uid)
                $service->deleteUser($uid);

            $this->_helper
                 ->getHelper("FlashMessenger")
                 ->setNamespace("messages")
                 ->addMessage(BackLib_Lang::tr("txt_info_user_delete"));

            $this->_redirect("/manage/users/list-users");
        }
        elseif ($this->getRequest()->isPost()) {

            $user_ids = $this->_getParam("users", array());

            $service  = AppLib_Service_Factory::getService("user", array("return_objects" => true));
            $users    = $service->getUsers(array(
                "filter" => array(
                    "user_id" => $user_ids)));

            $this->view->users = $users;
        }
        else {
            $this->_helper
                 ->getHelper("FlashMessenger")
                 ->setNamespace("errors")
                 ->addMessage(BackLib_Lang::tr("txt_error_invalidrequest"));

            $this->_redirect("/manage/users/list-users");
        }
    }

    public function usersActionGrabberAction()
    {
        $users  = $this->_getParam("users", array());
        $action = $this->_getParam("selections_action", "");

        if (empty($users) || empty($action))
            $this->_redirect("/manage/users/list-users");

        $method     = strtolower($action) . "UserAction";
        if (! method_exists($this, $method)) {
                $this->_helper
                     ->getHelper("FlashMessenger")
                     ->setNamespace("errors")
                     ->addMessage(BackLib_Lang::tr("txt_error_invalidrequest"));
        }

        $action .= "-user";
        $this->_forward($action, "users", "manage",
                        array("users" => $users));
    }


    public function listAclAction()
    {
        $filter = $this->_getParam("filter", array());
        $page   = $this->_getParam('page', 1);

        $service = AppLib_Service_Factory::getService("acl", array("return_objects" => true));
        $result  = $service->listRules(array("filter" => $filter));

        $icnt = 50;
        $adapter    = new Zend_Paginator_Adapter_Array($result);
        $paginator  = new Zend_Paginator($adapter);
        $paginator->setItemCountPerPage($icnt);
        $paginator->setCurrentPageNumber($page);

        $this->view->acl_actions = array("" => BackLib_Lang::tr("opt_filter_select")) + $service->listActions(array());
        $this->view->acl_roles   = array("" => BackLib_Lang::tr("opt_filter_select")) + $service->listRoles(array());
        $this->view->paginator   = $paginator;
        $this->view->filter      = $filter;
    }

    public function modifyAclAction()
    {
        $this->view->layout()->disableLayout();

        if ($this->getRequest()->isGet()) {

            $itemID   = $this->_getParam("aid");

            $service = AppLib_Service_Factory::getService("acl", array("return_objects" => true));
            $acls    = $service->listRules(array(
                "filter" => array(
                    "id_acl_config" => $itemID)));

            $service = AppLib_Service_Factory::getService("acl", array("return_objects" => true));
            $this->view->resources = $service->listResources(array());
            $this->view->roles     = $service->listRoles(array());
            $this->view->actions   = $service->listActions(array());
            $this->view->acl = $acls[0];
            $this->render("add-acl");
        }
    }

    public function addAclAction()
    {
        if ($this->getRequest()->isPost()) {
            $input = $this->getRequest()->getParams();

            try {
                $row_data = $input["acl"];

                $config   = Zend_Registry::get("config");

                $service  = AppLib_Service_Factory::getService("acl", array("return_objects" => true));
                $user     = $service->addRule($row_data);

                $this->_helper
                     ->getHelper("FlashMessenger")
                     ->setNamespace("messages")
                     ->addMessage(sprintf(BackLib_Lang::tr("txt_info_acl_save")));
            }
            catch (AppLib_Service_Fault $e) {
                $this->_helper
                     ->getHelper("FlashMessenger")
                     ->setNamespace("errors")
                     ->addMessage(sprintf(BackLib_Lang::tr("txt_error_acl_save"), $e->getMessage()));
            }

            $this->_redirect("/manage/users/list-acl");
        }

        $service = AppLib_Service_Factory::getService("acl", array("return_objects" => true));
        $this->view->resources = $service->listResources(array());
        $this->view->roles     = $service->listRoles(array());
        $this->view->actions   = $service->listActions(array());
    }

    public function deleteAclAction()
    {
        if ($this->getRequest()->isPost()
            && $this->_getParam("is_confirmed", null) !== null) {

            $acls = $this->_getParam("acls", array());

            $service = AppLib_Service_Factory::getService("acl");
            foreach ($acls as $aid)
                $service->deleteRule($aid);

            $this->_helper
                 ->getHelper("FlashMessenger")
                 ->setNamespace("messages")
                 ->addMessage(BackLib_Lang::tr("txt_info_acl_delete"));

            $this->_redirect("/manage/users/list-acl");
        }
        elseif ($this->getRequest()->isPost()) {

            $acl_ids = $this->_getParam("acls", array());

            $service  = AppLib_Service_Factory::getService("acl", array("return_objects" => true));
            $acls     = $service->listRules(array(
                "filter" => array(
                    "id_acl_config" => $acl_ids)));

            $this->view->acls = $acls;
        }
        else {
            $this->_helper
                 ->getHelper("FlashMessenger")
                 ->setNamespace("errors")
                 ->addMessage(BackLib_Lang::tr("txt_error_invalidrequest"));

            $this->_redirect("/manage/users/list-acl");
        }
    }

    public function aclsActionGrabberAction()
    {
        $acls   = $this->_getParam("acls", array());
        $action = $this->_getParam("selections_action", "");

        if (empty($acls) || empty($action))
            $this->_redirect("/manage/users/list-acl");

        $method     = strtolower($action) . "AclAction";
        if (! method_exists($this, $method)) {
                $this->_helper
                     ->getHelper("FlashMessenger")
                     ->setNamespace("errors")
                     ->addMessage(BackLib_Lang::tr("txt_error_invalidrequest"));
        }

        $action .= "-acl";
        $this->_forward($action, "users", "manage",
                        array("acls" => $acls));
    }
}
