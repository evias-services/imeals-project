<form method='post' action='<?php echo $this->baseUrl() . "/restaurant/orders/item-to-cart"; ?>'>
    <div id='modifiable-meal'>
        <h2>Extras?</h2>
        <ul>
            <?php
            $i = 0;
            foreach ($this->customization_items as $item) :
            ?>
            <li>
                <img src="<?php echo $this->baseUrl() . "/images/plus-sign.png"; ?>" 
                     alt='add <?php echo $item->getTitle(); ?>' 
                     rel="add-<?php echo $item->getPK(); ?>" />
                <img src="<?php echo $this->baseUrl() . "/images/minus-sign.png"; ?>" 
                     alt='remove <?php echo $item->getTitle(); ?>' 
                     rel='del-<?php echo $item->getPK(); ?>'/>
                <?php echo $item->getTitle(); ?> 
            </li>
            <?php
            endforeach; ?>
        </ul>

        <div id='item_id'></div>
        <div id='current-item-price'></div>
        <div id='wishes'></div>
    </div>
    <div id='immutable-meal'>
        <p><?php echo FrontLib_Lang::tr("msg_confirm_meal_order"); ?></p>
    </div>
    <input type='submit' value='<?php echo FrontLib_Lang::tr("button_addtocart"); ?>' />
    <button id='cancel_item' onclick='return false;'><?php echo FrontLib_Lang::tr("button_canceladdtocart"); ?></button>
</form>

<script type="text/javascript">
    $(document).ready(function(ev)
    {
        var closeBox = function(ev)
        {
            // remove registered wishes
            var in_wishes = $("#custom-meal #wishes input[type='hidden']").each(function(elm) {
                $(this).remove();
            });
            $("#custom-meal form ul li").each(function(elm) {
                $(this).css("background",  "transparent");
                $(this).css("border", "1px solid transparent");
            });

            // hide box
            $("#item_id").html("");
            $("#custom-meal").css("display", "none");
            return false;
        };

        var cancelButton = $("#custom-meal form button#cancel_item");
        if (cancelButton.length)
            $(cancelButton).click(closeBox);

        var getCustomPrice = function()
        {
            var menuitem_id = $("#custom-meal #item_id input").val();
            var wishes      = $("#custom-meal #wishes input[rel='add']");

            params = "item_id=" + menuitem_id;
            wishes.each(function(elm) {
                params = params + "&wishes[]=" + $(this).val();
            });

            $.ajax('<?php echo $this->baseUrl(); ?>/restaurant/menu/get-current-price',
            {
                type: 'post',
                async: true,
                data: params,
                success: function(data, tst, xhr) {
                    $("#current-item-price").html(data);
                }
            });
        };

        $("#custom-meal form ul li img").each(function(elm) {
            $(this).click(function(ev) {

                var p_min  = $(this).attr("rel").indexOf("-");
                var action = $(this).attr("rel").substr(0, p_min);
                var item   = $(this).attr("rel").substr(p_min+1);

                //var in_wishes = $$("#custom-meal #wishes input[rel='" + action + "'][value='" + item + "']")[0];
                var container = $("#custom-meal #wishes");

                var custom_for_item = $("#custom-meal #wishes input[value='" + item + "']");
                if (! custom_for_item.length) {
                    /* wish for this item does not exist yet */
                    $("<input type='hidden' rel='" + action + "' value='" + item + "' name='wishes[" + action + "][]' />")
                        .appendTo($(container));

                    var custom_for_item = $("#custom-meal #wishes input[value='" + item + "']");
                }

                if (action == "add") {

                    if (typeof custom_for_item != 'undefined'
                        && $(custom_for_item).attr('rel') == "del") {
                        // disable already existing wish
                        $(custom_for_item).remove();
                        $(this).parent("li").css("background", "transparent");
                        $(this).parent("li").css("border", "1px solid transparent");
                        getCustomPrice();
                        return ;
                    }

                    /* change background of li */
                    $(this).parent("li").css("background",  "#398526");
                    $(this).parent("li").css("border", "1px solid #19500b");
                }
                else {

                    if (typeof custom_for_item != 'undefined'
                        && $(custom_for_item).attr('rel') == "add") {
                        // disable already existing wish
                        $(custom_for_item).remove();
                        $(this).parent("li").css("background", "transparent");
                        $(this).parent("li").css("border", "1px solid transparent");
                        getCustomPrice();
                        return ;
                    }

                    /* change background of li */
                    $(this).parent("li").css("background", "#860a0a");
                    $(this).parent("li").css("border", "1px solid #500707");
                }

                getCustomPrice();
            });
        });

    });
</script>