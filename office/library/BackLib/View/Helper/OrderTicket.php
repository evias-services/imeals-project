<?php

class BackLib_View_Helper_OrderTicket
    extends Zend_View_Helper_Abstract
{
    private $_order = null;
    private $_cart  = null;
    private $_restaurant = null;

    public function orderTicket(AppLib_Model_Order $order, $action_dialog = true)
    {
        $this->_order = $order;
        $this->_cart  = $order->getCart();
        $this->_restaurant = Zend_Registry::get("restaurant");

        $html = "";

        if ($action_dialog)
            $html .= $this->_getActionsHTML();

        $html .= "<table class='active-order' cellpadding='0' cellspacing='3px' border='0px'>";
        $html .= "<tbody>";
        $html .= $this->_getRestaurantHTML();
        $html .= $this->_getOrderHTML();
        $html .= $this->_getDeliveryHTML();
        $html .= $this->_getFooterHTML();
        $html .= "</tobdy>";
        $html .= "</table>";

        return $html;
    }

    protected function _getActionsHTML()
    {
        /* XXX */
        $html = "
        <div>
            <ul class='actions-list box'>
                <li><button>Print</button></li>
                <li style='float:right;'><button>Communication</button></li>
            </ul>
        </div>

        <hr class='noscreen' />
        ";


        return $html;
    }

    protected function _getRestaurantHTML()
    {
        return "
        <tr>
            <td colspan='3' align='center'>
                <span class='upper-label'>" . $this->_restaurant->getTitle() . "</span>
            </td>
        </tr>
        <tr>
            <td colspan='3' align='center' style='line-height:13px;'>
                <span class='small'>" . $this->_restaurant->getLegalAddress() . "</span>
            </td>
        </tr>";
    }

    protected function _getOrderHTML()
    {
        $items = $this->_cart->getItems(true);
        $cnt   = $this->_cart->getQuantities();

        $norm_id = str_pad((string)$this->_order->id_order, 4, "0", STR_PAD_LEFT);
        $title   = BackLib_Lang::tr("order_num_title");

        /* Print title */
        $html    = "
        <tr><td colspan='3'>&nbsp;</td></tr>
        <tr>
            <td colspan='3' align='center'>
                <span class='upper-label bold underline'>" . $title ."&nbsp;#" . $norm_id . "</span>
            </td>
        </tr>
        ";

        $total_price = 0;
        foreach ($items as $item) {
            if ($item->getCategory()->getMenu()->id_restaurant != $this->_restaurant->id_restaurant)
                /* XXX Multi-restaurant */
                continue;

            $quantity = $cnt[$item->id_item];
            $title    = $item->getTitle();
            $price    = $item->getPrice();

            $total_price += $cnt[$item->id_item] * $item->getPrice();

            $customizations  = AppLib_Model_ItemCustomization::loadByItemAndCart($item->id_item, $this->_order->getCart()->id_cart);

            static $done_customized = array();

            if (! empty($customizations)
                && ! isset($done_customized[$item->id_item])) {
                if (count($customizations) < $quantity)
                    $html .= $this->_listItemDefaultHTML($item, $quantity - count($customizations), $price);
                else
                    $html .= "
                        <tr>
                            <td colspan='3' align='left'>
                            <span class='upper-label bold'>#{$item->meal_menu_number}&nbsp;{$item->getTitle()}</span></td>
                        </tr>";

                $html .= $this->_listItemCustomizedHTML($total_price, $item, $customizations);
                $done_customized[$item->id_item] = true;
            }
            elseif (empty($customizations)) {
                $html .= $this->_listItemDefaultHTML($item, $quantity, $price);
            }
        }

        /* Display TOTAL line */
        $html .= "
        <tr>
            <td colspan='3'><hr /></td>
        </tr>
        <tr>
            <td colspan='2'>
                <span class='upper-label bold'>total:</span>
            </td>
            <td align='right'>
                <span class='upper-label bold'>{$total_price},-&nbsp;&euro;</span>
            </td>
        </tr>";

        return $html;
    }

    protected function _listItemDefaultHTML(AppLib_Model_Item $item, $quantity, $price)
    {
        return "
            <tr>
                <td colspan='3' align='left'>
                <span class='upper-label bold'>#{$item->meal_menu_number}&nbsp;{$item->getTitle()}</span></td>
            </tr>
            <tr>
                <td colspan='2' align='left'><span class='upper-label'>{$quantity}x</span></td>
                <td align='right'><span class='upper-label bold'>{$price},-&nbsp;&euro;</span></td>
            </tr>
        ";
    }

    protected function _listItemCustomizedHTML(&$total_price, AppLib_Model_Item $item, array $customizations = array())
    {
        $html = "";

        $indexes = array_keys($customizations);
        $by_op   = array_values($customizations);

        foreach ($indexes as $ix => $meal_index) {

            $price = $item->getPrice();

            if (! empty($by_op[$ix]['+'])) {
                /* positive customizations were configured
                 * make sure the price is set right. */
                foreach ($by_op[$ix]['+'] as $wish)
                    $price += $wish->getCustom()->getPrice();
            }

            /* Display item price */
            $html .= "
            <tr>
                <td colspan='2' align='left'>
                    <span class='upper-label'>1x</span>
                </td>
                <td align='right'>
                    <span class='upper-label bold'>{$price},-&nbsp;&euro;</span>
                </td>
            </tr>
            ";

            /* Display customizations list */
            $html .= "
            <tr>
                <td colspan='3' align='center' style='line-height:10px;'>
                <ul>
            ";

            foreach ($by_op[$ix] as $op => $wishes) {
                foreach ($wishes as $wish) {
                    switch($op) {
                    default:
                    case '+':
                        $total_price += $wish->getCustom()->getPrice();
                        $custom = sprintf("%s %s(%s,-&euro;)", $op,
                                    $wish->getCustom()->getTitle(),
                                    $wish->getCustom()->getPrice());
                        break;
                    case '-':
                        $custom = sprintf("%s %s", $op,
                                    $wish->getCustom()->getTitle());

                   }

                   $html .= "
                   <li>
                       <span class='small italic'>{$custom}</span>
                   </li>
                   ";
                }
            }

            /* End of customizations list */
            $html .= "
                </ul>
                </td>
            </tr>
            ";
        }

        return $html;
    }

    protected function _getDeliveryHTML()
    {
        $norm_id  = str_pad((string)$this->_order->id_order, 4, "0", STR_PAD_LEFT);
        $title    = BackLib_Lang::tr("delivery_num_title");
        $comments = BackLib_Lang::tr("order_label_comments");

        $delivery = sprintf("%s<br />%s<br />%s %s<br />Tel.: %s<br />%s",
                            $this->_order->getCustomer()->realname,
                            $this->_order->getLocation()->address,
                            $this->_order->getLocation()->zipcode, $this->_order->getLocation()->city,
                            $this->_order->getCustomer()->phone,
                            (!empty($this->_order->getLocation()->comments) ? $comments . " {$this->_order->getLocation()->comments}" : ""));

        return "
            <tr>
                <td colspan='3' align='center' style='line-height:12px;'>
                    <span class='upper-label bold underline'>{$title}&nbsp;#{$norm_id}</span>
                </td>
            <tr>
            <tr>
                <td colspan='3' align='center'>
                    <span class='small'>{$delivery}</span>
                </td>
            </tr>
            <tr>
                <td colspan='3'><hr /></td>
            </tr>
        ";
    }

    protected function _getFooterHTML()
    {
        $order_date = date("d.m.Y H:i", strtotime($this->_order->date_created));
        return "
            <tr>
                <td colspan='3' align='left'>
                    <span class='small'>" . $order_date . "</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class='upper-label'>WEB ORDER</span>
                </td>
            </tr>
            <tr><td colspan='3'>&nbsp;</td></tr>
        ";
    }
}
