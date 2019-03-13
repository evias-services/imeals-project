<?php

class FrontLib_View_Helper_Cart
    extends Zend_View_Helper_Abstract
{
    public function cart(AppLib_Model_Cart $cart, $request_uri, array $options)
    {
        if (! isset($options["cart_min_price"]) || ! is_int($options["cart_min_price"]))
            $options["cart_min_price"] = 10;
        if (! isset($options["currency_html"]))
            $options["currency_html"] = "&euro;";

        $total_price = 0;
        $items       = $cart->getItems();
        $quantities  = $cart->getQuantities();

        $html  = "<ul>";

        /* Cart items list */
        $html .= "<li>";
        $html .= "<h2>" . FrontLib_Lang::tr("cart_title") . "</h2>";
        $html .= "<ul>";
        if (empty($items))
            $html .= "<li><span>" . FrontLib_Lang::tr("cart_no_items") . "</span></li>";
        else 
            /* $total_price is incremented by reference */
            $html .= $this->getItemsHTML($cart, $total_price, $items, $quantities, "&euro;");
        $html .= "</ul>";
        $html .= "</li>"; 
        /* end cart items list */
        
        /* Total line */
        $html .= $this->getTotalHTML($total_price, 
                                     $options["cart_min_price"], 
                                     $options["currency_html"]);
        /* end total line */

        $html .= "</ul>";

        /* print (or not) submit button */
        if ($options["submit_button"] && $total_price >= (int) $options["cart_min_price"]) { 
            $html .= "<form action='" . $this->view->baseUrl() . "/restaurant/orders/send-cart' method='post'>";
            $html .= "<input type='submit' name='confirm_cart' value='" . FrontLib_Lang::tr("submit_confirm_cart") . "' />";
            $html .= "</form>";
        }

        return $html;
    }

    public function getItemsHTML(AppLib_Model_Cart $cart, &$total_price, array $items, array $quantities, $currency = "&euro;")
    {
        $html = "";
        foreach ($items as $item) {
            $quantity = $quantities[$item->id_item];
            $title    = $item->getTitle();
            $price    = $item->getPrice();

            $item_customizations = AppLib_Model_ItemCustomization::loadByItemAndCart($item->id_item, $cart->id_cart);

            $total_price += $quantities[$item->id_item] * $item->getPrice();

            /* Print current item line */
            $html .= "<li>";

            /* Print item title (and remove action) */
            $html .= sprintf("<img src='%s' alt='%s' rel='%s' />",
                             $this->view->baseUrl() . "/images/minus-sign.png",
                             FrontLib_Lang::tr("txt_cart_item_remove") . " {$item->getTitle()}",
                    		 $item->getPK());
            $html .= sprintf("<a href='#'>%s&nbsp;%s&nbsp;-&nbsp;%s;-&nbsp;%s</a>",
                             $quantity,
                             $title,
                             $price,
                             $currency);

            /* Print item_customizations for current item */
            if (count($item_customizations)) {
                $indexes = array_keys($item_customizations);
                $by_op   = array_values($item_customizations);

                foreach ($indexes as $ix => $meal_index) {
                    $subtitle = $ix > 0 ? FrontLib_Lang::tr("txt_cart_item_andonce")
                                        : FrontLib_Lang::tr("txt_cart_item_where"); 

                    $html .= "<ul>";
                    $html .= "<li class='inter-title'>$subtitle</li>";

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
                            
                            $html .= "<li rel='" . sprintf("%s_%s_%d", $op, $wish->id_item, $meal_index) ."'>";
                            $html .= "<span>" . $custom . "</span>";
                            $html .= "</li>"; 
                        }
                    }
                    /* end $by_op iteration */
               
                    $html .=  "</ul>";
                }
            }
            /* end item_customization for current item */

            $html .= "</li>";
            /* end current item line */
        }

        return $html;
    }

    public function getTotalHTML($total, $min_price, $currency)
    {
        $html = "<li>";
        $html .= sprintf("<h2>%s&nbsp;%s,-&nbsp;%s&nbsp;(*)</h2>",
                          FrontLib_Lang::tr("cart_total_label"),
                          $total,
                          $currency);
        $html .= "<span class='italic'>" . FrontLib_Lang::tr("txt_delivery_excluded") . "</span><br />";
        if ($total < (int) $min_price) {
            $msg_min_price = sprintf(FrontLib_Lang::tr("txt_cart_minimum_price"), 
                                     $min_price, 
                                     $currency);
            $html .= "<span>" . $msg_min_price . "</span>";
        }
        $html .= "</li>";

        return $html;
    }
}

