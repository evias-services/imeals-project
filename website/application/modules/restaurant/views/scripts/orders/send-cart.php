<?php
    $v_name = ! empty($this->values[0]) ? $this->values[0] : "";
    $v_zip  = ! empty($this->values[1]) ? $this->values[1] : "";
    $v_city = ! empty($this->values[2]) ? $this->values[2] : "";
    $v_addr = ! empty($this->values[3]) ? $this->values[3] : "";
    $v_tel  = ! empty($this->values[4]) ? $this->values[4] : "";
    $v_mail = ! empty($this->values[5]) ? $this->values[5] : "";
    $v_com  = ! empty($this->values[6]) ? $this->values[6] : "";
?>
<h1><?php echo FrontLib_Lang::tr("h1_send_cart"); ?></h1>
<p><?php echo FrontLib_Lang::tr("p_send_cart"); ?></p>

<fieldset>
    <legend><?php echo FrontLib_Lang::tr("legend_customer_form"); ?></legend>

    <form action="<?php echo $this->baseUrl() . "/restaurant/orders/preview-order"; ?>" method="post">
    <table cellpadding="0" cellspacing="3px" border="0">
    <tbody>
        <tr>
            <td align="right"><label for="customer[name]"><?php echo FrontLib_Lang::tr("label_customer_name"); ?></label></td>
            <td><input type="text" name="customer[name]" value="<?php echo $v_name; ?>" /></td>
        </tr>
        <tr>
            <td align="right"><label for="location[zipcode]"><?php echo FrontLib_Lang::tr("label_customer_city"); ?></label>
            <td><input type="text" class="zip"  name="location[zipcode]" value="<?php echo $v_zip; ?>" />
                <input type="text" class="city" name="location[city]" value="<?php echo $v_city; ?>" /></td>
        </tr>
        <tr><td align="right"><label for="location[address]"><?php echo FrontLib_Lang::tr("label_customer_address"); ?></label></td>
        <td><input type="text" name="location[address]" value="<?php echo $v_addr; ?>" /></td>
        </tr>

        <tr><td align="right"><label for="customer[phone]"><?php echo FrontLib_Lang::tr("label_customer_phone"); ?></label></td>
        <td><input type="text" name="customer[phone]" value="<?php echo $v_tel; ?>" /></td>
        </tr>

        <tr><td align="right"><label for="customer[email]"><?php echo FrontLib_Lang::tr("label_customer_email"); ?></label></td>
        <td><input type="text" name="customer[email]" value="<?php echo $v_mail; ?>" /></td>
        </tr>

        <tr><td align="right"><label for="location[comment]"><?php echo FrontLib_Lang::tr("label_customer_comment"); ?></label></td>
        <td><textarea name="customer[comment]"><?php echo $v_com; ?></textarea></td>
        </tr>

        <tr><td>&nbsp;</td><td>
        <input type="submit" class='submit-cart' value="<?php echo FrontLib_Lang::tr("submit_customer_form"); ?>" name="save_customer" />
            </td></tr>
    </tbody>
    </table>
    </form>
    <div class="clear"></div>

</fieldset>
