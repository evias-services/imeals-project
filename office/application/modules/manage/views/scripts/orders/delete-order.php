<h2><?php echo BackLib_Lang::tr("txt_h2_delete_orders"); ?></h2>
<?php if (empty($this->orders)) : ?>
    <div class="error">,
        <p><?php echo BackLib_Lang::tr("txt_error_delete_no_orders"); ?></p>
    </div>
<?php else : ?>
    <p><?php echo BackLib_Lang::tr("txt_p1_delete_orders"); ?></p>
    <br />
    <p><?php echo BackLib_Lang::tr("txt_p2_delete_orders"); ?></p>
    <br />
    <ul>
    <?php foreach ($this->orders as $order) :
            $customer = sprintf("[%s] %s (%s): %s %s, %s",
                    $order->getRestaurant()->title,
                    $order->getCustomer()->realname,
                    $order->getCustomer()->phone,
                    $order->getLocation()->address,
                    $order->getLocation()->zipcode,
                    $order->getLocation()->city);
        ?>
        <li><?php echo "#{$order->id_order}"; ?>&nbsp;&quot;<span class="bold"><?php echo "$customer"; ?></span>&quot;</li>
    <?php endforeach; ?>
    </ul>
    <div class="actions">
    <form action="<?php echo $this->baseUrl(); ?>/manage/orders/delete-order" method="post">
        <?php foreach ($this->orders as $order) :  ?>
        <input type="hidden" name="orders[]" value="<?php echo $order->id_order; ?>" />
        <?php endforeach; ?>
        <input type="submit"
               name="is_confirmed"
               value="<?php echo BackLib_Lang::tr("txt_form_submit_delete_orders"); ?>" />

        <a href="<?php echo $this->baseUrl(); ?>/manage/orders/list-orders"><?php echo BackLib_Lang::tr("txt_form_cancel_delete_orders"); ?></a>
        <div class="clear"></div>
    </form>
    </div>
<?php endif; ?>
