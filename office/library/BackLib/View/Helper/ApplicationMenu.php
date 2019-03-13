<?php

class BackLib_View_Helper_ApplicationMenu
    extends Zend_View_Helper_Abstract
{
    const POSITION_TOP  = "top";
    const POSITION_LEFT = "left";

    private $_pages = array(
        "/default/index/index"              => array("label" => "txt_link_dashboard", "pattern" => "\/default\/?.*"),
        "/manage/users/list-users"          => array("label" => "txt_link_users",
            "subpages" => array(
                "/manage/users/list-users"    => array("label" => "txt_link_listusers", "pattern" => "\/manage\/users\/([\w\-]+users?)(\/.*)?"),
                "/manage/users/list-acl"      => array("label" => "txt_link_listacl", "pattern" => "\/manage\/users\/([\w\-]+acl)(\/.*)?")),
            "pattern" => "\/manage\/users\/?.*"),
        "/manage/restaurant/index"          => array("label" => "txt_link_restaurant",
            "subpages" => array(
                "/manage/restaurant/index"    => array("label" => "txt_link_restaurants", "pattern" => "\/manage\/restaurant\/index(\/.*)?"),
                "/manage/restaurant/settings" => array("label" => "txt_link_restaurant_settings", "pattern" => "\/manage\/restaurant\/settings(\/.*)?"),
                "/manage/menu/index"            => array("label" => "txt_link_menus", "pattern" => "\/manage\/menu(\/.*)?"),
                "/manage/customers/list-customers"  => array("label" => "txt_link_customers", "pattern" => "\/manage\/customers\/?.*")),
            "pattern" => "\/manage\/(restaurant|menu|customers)\/?.*"),
        "/manage/orders/list-orders"        => array("label" => "txt_link_orders", "pattern" => "\/manage\/orders\/?.*", "attribs" => array("rel" => "list-orders")),
        "/manage/bookings/index"                      => array("label" => "txt_link_booking",
            "subpages" => array(
                "/manage/bookings/index"       => array("label" => "txt_link_bookings", "pattern" => "\/manage\/bookings\/index(\/.*)?"),
                "/manage/bookings/list-rooms"  => array("label" => "txt_link_bookings_list_rooms", "pattern" => "\/manage\/bookings\/([\w\-]+rooms?)(\/.*)?")),
            "pattern" => "\/manage\/bookings\/?.*"),
        "/manage/gallery/index"                   => array("label" => "txt_link_gallery", "pattern" => "\/manage\/gallery\/?.*"),
    );

    private $_roleId = BackLib_AclCheck::ROLE_GUEST;

    public function applicationMenu($position)
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $identity      = Zend_Auth::getInstance()->getIdentity();
            $this->_roleId = AppLib_Model_AclConfig::getRoleIdByLogin ($identity->login);
        }

        switch($position) {
        default:
        case self::POSITION_TOP:
            return $this->_top();

        case self::POSITION_LEFT:
            return $this->_left();
        }
    }

    protected function _isActive($uri_pattern)
    {
        $requestURI = $_SERVER["REQUEST_URI"];
        $baseUrl    = str_replace("/", "\/", $this->view->baseUrl());

        return (bool) preg_match("/^{$baseUrl}{$uri_pattern}/", $requestURI);
    }

    protected function _isSubActive($uri_pattern)
    {
        $requestURI = $_SERVER["REQUEST_URI"];
        $baseUrl    = str_replace("/", "\/", $this->view->baseUrl());

        return (bool) preg_match("/^{$baseUrl}{$uri_pattern}/", $requestURI);
    }

    protected function _top()
    {
        $baseUrl = $this->view->baseUrl();

        $html = "";
        $html .= "<ul class='box'>";
        foreach ($this->_pages as $uri => $page_config) {
            $label = BackLib_Lang::tr($page_config["label"]);
            $link  = $baseUrl . $uri;

            /* ACL check. */
            list($m, $c, $a) = explode("/", ltrim($uri, "/"));
            if (empty($a))
                $a = "index";

            /* XXX check for children permission. */
            $acl_check = new BackLib_AclCheck(array(
                "identity"   => Zend_Auth::getInstance()->getIdentity(),
                "module"     => $m,
                "controller" => $c,
                "action"     => $a));
            if (! $acl_check->checkPermission())
                /* Do not display link, access denied. */
                continue;

            $id    = "";
            if ($this->_isActive($page_config["pattern"]))
                $id = " id='menu-active'";

            $attribs = "";
            if (isset($page_config["attribs"])) {
                foreach ($page_config["attribs"] as $attr => $val) {
                    if ($attr == "id")
                        continue;

                    $attribs .= " $attr='$val'";
                }
            }

            $html .= "<li{$id}{$attribs}><a href='{$link}'><span>{$label}</span></a></li>";
        }
        $html .= "</ul>";
        return $html;
    }

    protected function _left()
    {
        $baseUrl = $this->view->baseUrl();

        $html = "";
        $html .= "<ul class='box'>";
        foreach ($this->_pages as $uri => $page_config) {
            $label = BackLib_Lang::tr($page_config["label"]);
            $link  = $baseUrl . $uri;

            /* ACL check. */
            list($m, $c, $a) = explode("/", ltrim($uri, "/"));
            if (empty($a))
                $a = "index";

            $acl_check = new BackLib_AclCheck(array(
                "identity"   => Zend_Auth::getInstance()->getIdentity(),
                "module"     => $m,
                "controller" => $c,
                "action"     => $a));
            if (! $acl_check->checkPermission())
                /* Do not display link, access denied. */
                continue;

            $id    = "";
            if ($this->_isActive($page_config["pattern"]))
                $id = " id='submenu-active'";

            $attribs = "";
            if (isset($page_config["attribs"])) {
                foreach ($page_config["attribs"] as $attr => $val) {
                    if ($attr == "id")
                        continue;

                    $attribs .= " $attr='$val'";
                }
            }

            if (empty($page_config["subpages"])) {
                /* No subpages available. */
                $html .= "<li{$id}{$attribs}><a href='{$link}'>{$label}</a></li>";
                continue;
            }

            $html .= "<li{$id}{$attribs}><a href='{$link}'>{$label}</a>";
            $html .= "<ul>";
            foreach ($page_config["subpages"] as $s_uri => $s_config) {
                $s_label = BackLib_Lang::tr($s_config["label"]);
                $s_link  = $baseUrl . $s_uri;

                /* subpage ACL check. */
                list($s_m, $s_c, $s_a) = explode("/", ltrim($s_uri, "/"));
                if (empty($s_a))
                    $s_a = "index";

                $s_acl_check = new BackLib_AclCheck(array(
                    "identity"   => Zend_Auth::getInstance()->getIdentity(),
                    "module"     => $s_m,
                    "controller" => $s_c,
                    "action"     => $s_a));
                if (! $s_acl_check->checkPermission())
                    /* Do not display link, access denied. */
                    continue;

                $s_id    = "";
                if ($this->_isSubActive($s_config["pattern"]))
                    $s_id = " id='action-active'";

                $s_attribs = "";
                if (isset($s_config["attribs"])) {
                    foreach ($s_config["attribs"] as $attr => $val) {
                        if ($attr == "id")
                            continue;

                        $s_attribs .= " $attr='$val'";
                    }
                }

                $html .= "<li{$s_id}{$s_attribs}><a href='$s_link'>{$s_label}</a></li>";
            }
            $html .= "</ul>";
            $html .= "</li>";
        }
        $html .= "</ul>";
        return $html;
    }
}

