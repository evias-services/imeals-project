<h2><?php echo BackLib_Lang::tr("txt_h2_list_orders"); ?></h2>
<?php echo BackLib_Lang::tr("txt_p_list_orders"); ?>
<br />
<div class="fLeft" id="current-order">&nbsp;</div>
<div class="fLeft">
<fieldset>
<legend><?php echo BackLib_Lang::tr("legend_listresults"); ?></legend>
    <?php
    /* Configuration pagination */
    echo $this->paginationControl($this->paginator, 'Sliding', 'pagination.phtml');
    ?>
<form method="post" action="<?php echo $this->baseUrl(); ?>/manage/orders/action-grabber">
<table id="order_list" cellpadding="3px" cellspacing="0px">
    <thead>
        <th>&nbsp;</th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_order_id"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_order_restaurant_title"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_order_customer"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_order_date_created"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_order_date_updated"); ?></span></th>
    </thead>
    <tbody>
<?php
        $restaurant = Zend_Registry::get("restaurant");
        foreach($this->paginator as $record)
        {
            $customer = sprintf("%s (%s): %s %s, %s",
                                $record->getCustomer()->realname,
                                $record->getCustomer()->phone,
                                $record->getLocation()->address,
                                $record->getLocation()->zipcode,
                                $record->getLocation()->city);

            $unseenStyle = " style='background: #009c10;'";
            $unseenLink  = " style='color: #000'";
            $linkStyle   = $record->isSeenBy($restaurant->id_restaurant) ? "" : $unseenLink;
            $lineStyle   = $record->isSeenBy($restaurant->id_restaurant) ? "" : $unseenStyle;
            $lineRel     = " rel='" . ($record->isSeenBy($restaurant->id_restaurant) ? "seen" : "unseen") . "'";
            $link        = $this->baseUrl() . "/manage/orders/set-seen/oid/" . $record->id_order;

            echo "<tr class='order'{$lineStyle}{$lineRel}>";
            echo "<td><input type='checkbox' name='orders[]' value='{$record->id_order}' /></td>";
            echo "<td class='oid txtLeft data'>{$record->id_order}</td>";
            echo "<td class='txtCenter data'><strong>{$restaurant->title}</strong></td>";
            echo "<td class='txtLeft data'><a onclick='return false;' href='{$link}'{$linkStyle} class='ajax-link'>$customer</a></td>";
            echo "<td class='txtCenter data'><span>" . date("d-m-Y H:i", strtotime($record->date_created)) . "</span></td>";
            echo "<td class='txtCenter data'><span>" . (null === $record->date_updated ? "N/A" : date("d-m-Y H:i", strtotime($record->date_updated)))
                    . "</span></td>";
            echo "</tr>";
        }

?>
        <tr class="actions">
            <td colspan="6">
                <div>
                    <div>
                    <span><?php echo BackLib_Lang::tr("txt_tf_category_select_actions"); ?></span>
                    &nbsp;&nbsp;<select name="selections_action" onchange="submit()">
                            <option value=""><?php echo BackLib_Lang::tr("txt_tf_category_select_action_opt"); ?></option>
                            <option value="delete"><?php echo BackLib_Lang::tr("txt_tf_category_action_delete"); ?></option>
                        </select>
                    </div>
                </div>
            </td>
        </tr>

    </tbody>
</table>
</form>
<?php
    /* Configuration pagination */
    echo $this->paginationControl($this->paginator, 'Sliding', 'pagination.phtml');
?>
</fieldset>
</div>
<div class="clear"></div>
<br />
<hr />

<div id="current-order">
</div>

<audio preload="auto">
  <source src="<?php echo $this->baseUrl() . "/images/new.wav"; ?>" type="audio/wav">
  <object data="<?php echo $this->baseUrl() . "/images/new.wav"; ?>" type="audio/wav" autostart="false">
      <embed src="<?php echo $this->baseUrl() . "/images/new.wav"; ?>" autostart="false" alt="Could not load audio" />
  </object>
</audio>

<script type="text/javascript">
/********** FUNCTIONS **/
    var getChecked = function() {
        var checkedOrders = [];
        $("tr.order td input[type='checkbox']").each(function(cbox) {
            if (this.checked)
                checkedOrders.push(this.value);
        });
        return checkedOrders;
    };

    var setChecked = function(list) {
        for (i in list) {
            var oid = parseInt(list[i]);
            if (isNaN(oid))
                break;
            $("tr.order td input[type='checkbox'][value='" + oid + "']")[0].checked = true;
        }
    };

    var getPageParam = function() {
        var pageParam = "";
        var pos = 0;
        if ((pos = window.location.href.indexOf("/page/")) != -1)
            pageParam = "/page/" + window.location.href.substr(pos + 6);

        return pageParam;
    };

    var updateOrdersList = function() {
        var checkedOrders = getChecked();
        $.ajax({
            url : '<?php echo $this->baseUrl() . "/manage/orders/list-orders-table-content"; ?>' + getPageParam(),
            method: 'get',
            async: false,
            success : function(data, textStatus, xhr) {
                $("#order_list").html(data);
            }
        });
        setChecked(checkedOrders);
    };

    var orderClick = function(evt) {
        var line = $(this.parentNode);
        var oid  = $(line).children("td.oid").html();

        /* Produce click on link (open nyromodal) */
        $(line.children("td.data")[2]).children("a").click();

        /* update table */
        updateOrdersList();

        return false;
    };

/******** TIMEOUT CONFIGURATION **/
    setInterval(function() {

        updateOrdersList();

        /* Check for unseen orders */
        var hasUnseenOrder = false;
        if ($("tr.order[rel='unseen']").length)
            hasUnseenOrder = true;

        /* Play sound if incoming order */
        if (hasUnseenOrder)
            $("audio")[0].play();

        /* update click listeners and eRestaurant object */
        $(document).ready(function(evt) {
            $("tr.order td.data").each(function(elm) {
                this.addEventListener("click", orderClick, false);
            });

            $(document).eRestaurant();
        });

        return false;
    }, 20000);

$(document).ready(function(evt) {
    $("tr.order td.data").each(function(elm) {
        this.addEventListener("click", orderClick, false);
    });

    $(document).eRestaurant();
});
</script>
