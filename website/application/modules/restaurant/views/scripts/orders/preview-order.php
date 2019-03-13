<div id="order-preview">
    <div class="cart-preview">
<?php
    echo $this->cart($this->cart, $this->requestURI, array(
            "cart_min_price" => $this->cart_min_price,
            "currency_html"  => $this->cart_current,
            "submit_button"  => false));
?>
    </div>
    <table cellpadding='0' cellspacing='3px' border='0'>
    <tbody>
        <tr>
            <td colspan="3" align="center" class="table-title"><span><?php echo FrontLib_Lang::tr("prev_cart_address_table"); ?></span></td>
        </tr>

        <tr>
            <td>&nbsp;</td>
            <td class="label"><?php echo FrontLib_Lang::tr("label_customer_name"); ?></td>
            <td class="value"><?php echo $this->order->getCustomer()->realname; ?></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td class="label"><?php echo FrontLib_Lang::tr("label_customer_city"); ?></td>
            <td class="value"><?php echo $this->order->getLocation()->zipcode . ", " . $this->order->getLocation()->city; ?></td>
        </tr>

        <tr>
            <td>&nbsp;</td>
            <td class="label"><?php echo FrontLib_Lang::tr("label_customer_address"); ?></td>
            <td class="value"><?php echo $this->order->getLocation()->address; ?></td>
        </tr>

        <tr>
            <td>&nbsp;</td>
            <td class="label"><?php echo FrontLib_Lang::tr("label_customer_phone"); ?></td>
            <td class="value"><?php echo $this->order->getCustomer()->phone; ?></td>
        </tr>

        <tr>
            <td>&nbsp;</td>
            <td class="label"><?php echo FrontLib_Lang::tr("label_customer_email"); ?></td>
            <td class="value"><?php echo $this->order->getCustomer()->email; ?></td>
        </tr>

        <tr>
            <td>&nbsp;</td>
            <td class="label"><?php echo FrontLib_Lang::tr("label_customer_comment"); ?></td>
            <td class="value"><?php echo $this->order->getLocation()->comments; ?></td>
        </tr>

    </tbody>
    </table>

    <hr />

    <form action="" method="post">
        <input type="hidden" name="oid" value="<?php echo $this->order->id_order; ?>" />
        <input type="submit" name="confirm-cart" value="<?php echo FrontLib_Lang::tr("confirm_cart_submit"); ?>" />
    </form>
</div>
