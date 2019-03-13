<?php

class Manage_MenuController
    extends BackLib_Controller_Action
{
    public function indexAction()
    {
        $filter  = $this->_getParam("filter", array());
        $page    = $this->_getParam('page', 1);

        $filter["id_restaurant"] = array($this->getRestaurant()->id_restaurant);

        $proxy   = new AppLib_Service_Proxy;
        $proxy->setService("menu", array("return_objects" => true));

        $result  = $proxy->__call("getMenus", array("filter" => $filter));

        $icnt = 25;
        $adapter    = new Zend_Paginator_Adapter_Array($result);
        $paginator  = new Zend_Paginator($adapter);
        $paginator->setItemCountPerPage($icnt);
        $paginator->setCurrentPageNumber($page);

        $this->view->paginator = $paginator;
        $this->view->filter    = $filter;
    }

    public function listMenuActionGrabberAction()
    {
        $menus  = $this->_getParam("menus", array());
        $action = $this->_getParam("selections_action", "");

        if (empty($menus) || empty($action))
            $this->_redirect("/manage/menu/index");

        $method     = strtolower($action) . "MenuAction";
        if (! method_exists($this, $method)) {
            $this->_helper
                ->getHelper("FlashMessenger")
                ->setNamespace("errors")
                ->addMessage(BackLib_Lang::tr("txt_error_invalidrequest"));
        }

        $action .= "-menu";
        $this->_forward($action, "menu", "manage",
                array("menus" => $menus));
    }

    public function deleteMenuAction()
    {
        if ($this->getRequest()->isPost()
            && $this->_getParam("is_confirmed", null) !== null) {

            $menus = $this->_getParam("menus", array());

            $proxy = new AppLib_Service_Proxy;
            $proxy->setService("menu");
            foreach ($menus as $mid)
                $proxy->__call("deleteMenu", $mid);

            $this->_helper
                ->getHelper("FlashMessenger")
                ->setNamespace("messages")
                ->addMessage(BackLib_Lang::tr("txt_info_menu_delete"));

            $this->_redirect("/manage/menu/index");
        }
        elseif ($this->getRequest()->isPost()) {

            $mids    = $this->_getParam("menus", array());
            $filter  = array("id_menu" => $mids);

            $proxy = new AppLib_Service_Proxy;
            $proxy->setService("menu", array("return_objects" => true));
            $menus = $proxy->__call("getMenus", array("filter" => $filter));

            $this->view->menus = $menus;
        }
        else {
            $this->_helper
                ->getHelper("FlashMessenger")
                ->setNamespace("errors")
                ->addMessage(BackLib_Lang::tr("txt_error_invalidrequest"));

            $this->_redirect("/manage/menu/index");
        }
    }

    public function addMenuAction()
    {
        if ($this->getRequest()->isPost())
        {
            $input    = $this->getRequest()->getParams();
            $row_data = $input['menu'];

            $proxy = new AppLib_Service_Proxy;
            $proxy->setService("menu");
            try {
                $proxy->__call("addMenu", $row_data);

                $this->_helper
                     ->getHelper("FlashMessenger")
                     ->setNamespace("messages")
                     ->addMessage(BackLib_Lang::tr("txt_info_menu_save"));
            }
            catch (Exception $e) {
                $this->_helper
                     ->getHelper("FlashMessenger")
                     ->setNamespace("errors")
                     ->addMessage(sprintf(BackLib_Lang::tr("txt_error_menu_save"), $e->getMessage()));
            }

            $this->_redirect("/manage/menu/index");
        }

        $proxy       = new AppLib_Service_Proxy;
        $restaurants = $proxy->setService("restaurant", array("return_objects" => true))
                             ->__call("getRestaurants", array("filter" => array()));

        $this->view->restaurants = $restaurants;
    }

    public function modifyMenuAction()
    {
        $this->view->layout()->disableLayout();

        if ($this->getRequest()->isGet()) {

            $itemID = $this->_getParam("mid");
            $filter = array("id_menu" => $itemID);

            $proxy = new AppLib_Service_Proxy;
            $proxy->setService("menu", array("return_objects" => true));
            $menus = $proxy->__call("getMenus", array("filter" => $filter));

            $this->view->menu = array_pop($menus);

            $proxy       = new AppLib_Service_Proxy;
            $restaurants = $proxy->setService("restaurant", array("return_objects" => true))
                                 ->__call("getRestaurants", array("filter" => array()));
            $this->view->restaurants = $restaurants;

            $this->render("add-menu");
        }
    }

    public function listCategoriesAction()
    {
        $filter = $this->_getParam("filter", array());
        $page   = $this->_getParam('page', 1);
        $menu   = $this->getRestaurant()->getMenu();

        $service = AppLib_Service_Factory::getService("menu", array("return_objects" => true));
        $result  = $service->getCategories($menu->id_menu, array("filter" => $filter));

        $icnt = 20;
        $adapter    = new Zend_Paginator_Adapter_Array($result);
        $paginator  = new Zend_Paginator($adapter);
        $paginator->setItemCountPerPage($icnt);
        $paginator->setCurrentPageNumber($page);

        $this->view->paginator = $paginator;
        $this->view->filter    = $filter;
    }

    public function addCategoryAction()
    {
        if ($this->getRequest()->isPost()) {
            $input = $this->getRequest()->getParams();

            try {
                $row_data = $input["category"];
                $menu     = $this->getRestaurant()->getMenu();

                $service  = AppLib_Service_Factory::getService("menu", array("return_objects" => true));
                $category = $service->addCategory($menu->id_menu, $row_data);

                $this->_helper
                     ->getHelper("FlashMessenger")
                     ->setNamespace("messages")
                     ->addMessage(sprintf(BackLib_Lang::tr("txt_info_category_save"),
                                          $category->title));
            }
            catch (AppLib_Service_Fault $e) {
                $this->_helper
                     ->getHelper("FlashMessenger")
                     ->setNamespace("errors")
                     ->addMessage(sprintf(BackLib_Lang::tr("txt_error_category_save"), $e->getMessage()));
            }

            $this->_redirect("/manage/menu/list-categories");
        }

        $menu    = $this->getRestaurant()->getMenu();
        $service = AppLib_Service_Factory::getService("menu", array("return_objects" => true));

        $this->view->categories = $service->getCategories($menu->id_menu, array("filter" => array()));
    }

    public function deleteCategoryAction()
    {
        if ($this->getRequest()->isPost()
            && $this->_getParam("is_confirmed", null) !== null) {

            $categories = $this->_getParam("categories", array());
            $menu       = $this->getRestaurant()->getMenu();

            $service    = AppLib_Service_Factory::getService("menu");
            foreach ($categories as $cid)
                $service->deleteCategory($menu->id_menu, $cid);

            $this->_helper
                 ->getHelper("FlashMessenger")
                 ->setNamespace("messages")
                 ->addMessage(BackLib_Lang::tr("txt_info_category_delete"));

            $this->_redirect("/manage/menu/list-categories");
        }
        elseif ($this->getRequest()->isPost()) {

            $cat_ids    = $this->_getParam("categories", array());
            $menu       = $this->getRestaurant()->getMenu();

            $service    = AppLib_Service_Factory::getService("menu", array("return_objects" => true));
            $categories = $service->getCategories($menu->id_menu, array(
                "filter" => array(
                    "category_id" => $cat_ids)));

            $this->view->categories = $categories;
        }
        else {
            $this->_helper
                 ->getHelper("FlashMessenger")
                 ->setNamespace("errors")
                 ->addMessage(BackLib_Lang::tr("txt_error_invalidrequest"));

            $this->_redirect("/manage/menu/list-categories");
        }
    }

    public function modifyCategoryAction()
    {
        $this->view->layout()->disableLayout();

        if ($this->getRequest()->isGet()) {

            $itemID   = $this->_getParam("cid");
            $menu     = $this->getRestaurant()->getMenu();

            $service  = AppLib_Service_Factory::getService("menu", array("return_objects" => true));
            $category = $service->getCategories($menu->id_menu, array(
                "filter" => array(
                    "category_id" => $itemID)));

            $this->view->categories = $service->getCategories($menu->id_menu, array("filter" => array()));
            $this->view->category = $category[0];
            $this->render("add-category");
        }
    }

    public function categoriesActionGrabberAction()
    {
        $categories = $this->_getParam("categories", array());
        $action     = $this->_getParam("selections_action", "");

        if (empty($categories) || empty($action))
            $this->_redirect("/manage/menu/list-categories");

        $method     = strtolower($action) . "CategoryAction";
        if (! method_exists($this, $method)) {
                $this->_helper
                     ->getHelper("FlashMessenger")
                     ->setNamespace("errors")
                     ->addMessage(BackLib_Lang::tr("txt_error_invalidrequest"));
        }

        $action .= "-category";
        $this->_forward($action, "menu", "manage",
                        array("categories" => $categories));
    }

    public function listItemsAction()
    {
        $filter = $this->_getParam("filter", array());
        $page = $this->_getParam('page', 1);
        $icnt = 25;

        $rid = Zend_Registry::get("restaurant")->id_restaurant;
        $menu   = $this->getRestaurant()->getMenu();

        $service = AppLib_Service_Factory::getService("menu", array("return_objects" => true));
        $result  = $service->getItems($menu->id_menu, array("filter" => $filter));

        $adapter    = new Zend_Paginator_Adapter_Array($result);
        $paginator  = new Zend_Paginator($adapter);
        $paginator->setItemCountPerPage($icnt);
        $paginator->setCurrentPageNumber($page);

        $this->view->categories = $service->getCategories($menu->id_menu, array("filter" => array()));
        $this->view->paginator = $paginator;
        $this->view->filter    = $filter;
    }

    public function addItemAction()
    {
        if ($this->getRequest()->isPost()) {
            $input = $this->getRequest()->getParams();

            try {
                $row_data = $input["item"];
                $menu     = $this->getRestaurant()->getMenu();

                $service  = AppLib_Service_Factory::getService("menu", array("return_objects" => true));
                $item     = $service->addItem($menu->id_menu, $row_data);

                $this->_helper
                     ->getHelper("FlashMessenger")
                     ->setNamespace("messages")
                     ->addMessage(sprintf(BackLib_Lang::tr("txt_info_meal_save"),
                                          $item->title));
            }
            catch (AppLib_Service_Fault $e) {
                $this->_helper
                     ->getHelper("FlashMessenger")
                     ->setNamespace("errors")
                     ->addMessage(sprintf(BackLib_Lang::tr("txt_error_meal_save"), $e->getMessage()));
            }

            $this->_redirect("/manage/menu/list-items");
        }

        $menu    = $this->getRestaurant()->getMenu();
        $service = AppLib_Service_Factory::getService("menu", array("return_objects" => true));
        $this->view->categories = $service->getCategories($menu->id_menu, array("filter" => array()));
    }

    public function deleteItemAction()
    {
        if ($this->getRequest()->isPost()
            && $this->_getParam("is_confirmed", null) !== null) {

            $items = $this->_getParam("items", array());
            $menu  = $this->getRestaurant()->getMenu();

            $service = AppLib_Service_Factory::getService("menu");
            foreach ($items as $iid)
                $service->deleteItem($menu->id_menu, $iid);

            $this->_helper
                 ->getHelper("FlashMessenger")
                 ->setNamespace("messages")
                 ->addMessage(BackLib_Lang::tr("txt_info_meal_delete"));

            $this->_redirect("/manage/menu/list-items");
        }
        elseif ($this->getRequest()->isPost()) {

            $item_ids   = $this->_getParam("items", array());
            $menu       = $this->getRestaurant()->getMenu();

            $service = AppLib_Service_Factory::getService("menu", array("return_objects" => true));
            $items   = $service->getItems($menu->id_menu, array(
                "filter" => array(
                    "item_id" => $item_ids)));

            $this->view->items = $items;
        }
        else {
            $this->_helper
                 ->getHelper("FlashMessenger")
                 ->setNamespace("errors")
                 ->addMessage(BackLib_Lang::tr("txt_error_invalidrequest"));

            $this->_redirect("/manage/menu/list-items");
        }
    }

    public function modifyItemAction()
    {
        $this->view->layout()->disableLayout();

        if ($this->getRequest()->isGet()) {

            $itemID  = $this->_getParam("iid");
            $menu    = $this->getRestaurant()->getMenu();

            $service = AppLib_Service_Factory::getService("menu", array("return_objects" => true));
            $item    = $service->getItems($menu->id_menu, array(
                "filter" => array(
                    "item_id" => $itemID)));

            $this->view->categories = $service->getCategories($menu->id_menu, array("filter" => array()));
            $this->view->item = $item[0];
            $this->render("add-item");
        }
    }

    public function itemsActionGrabberAction()
    {
        $items  = $this->_getParam("items", array());
        $action = $this->_getParam("selections_action", "");

        if (empty($items) || empty($action))
            $this->_redirect("/manage/menu/list-items");

        $method     = strtolower($action) . "ItemAction";
        if (! method_exists($this, $method)) {
                $this->_helper
                     ->getHelper("FlashMessenger")
                     ->setNamespace("errors")
                     ->addMessage(sprintf("Invalid action provided."));
        }

        $action .= "-item";
        $this->_forward($action, "menu", "manage",
                        array("items" => $items));
    }
}
