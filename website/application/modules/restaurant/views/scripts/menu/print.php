<?php
$categories = $this->menu->getCategories();
$items      = array();

$link_ids   = array();
foreach ($categories as $category) {
    /* If category title appears more than once, links have to differ. */
    /* XXX: performance upgrade, use category ID for uniqueness instead of suffixNthIn */
    $clean_title = AppLib_Utils::cleanUrl($category->getTitle());
    $link_label  = AppLib_Utils::suffixNthIn($clean_title, $link_ids);
    $link_ids[$category->id_category] = $link_label;

    $items[$category->id_category] = $category->getItems();
}
?>

<div id="restaurant-menu">

<!-- RENDERING OF CATEGORIES NAVIGATION -->
<div id="categories-navigation">
    <ul>
<?php foreach ($categories as $category) { ?>
        <li>
            <h3><a class="category-name-link" rel="<?php echo $category->id_category; ?>" href="#<?php echo $link_ids[$category->id_category]; ?>"><?php echo $category->getTitle(); ?></a></h3>
        </li>
<?php } ?>
    </ul>
    <ul class="category-images">
<?php foreach ($categories as $category) { ?>
        <li rel="<?php echo $category->id_category; ?>">
            <img src="<?php echo $this->baseUrl() . "/images/resources/category/{$category->id_category}.png"; ?>" alt="<?php echo $category->getTitle(); ?>" />
        </li>
<?php } ?>

    </ul>
</div>

<div id="menu-splash">
    <img src="<?php echo $this->baseUrl() . "/images/resources/misc/menu-splash.png"; ?>"
         alt="La carte du restaurant / Die Speisekarte unseres Restaurants" />
</div>


<!-- RENDERING OF CATEGORIES MEALS -->
<?php foreach ($categories as $category) { ?>
<div class="category-content">
    <h4><a class="category-content-mark"
           rel="<?php echo $category->id_category; ?>"
           id="<?php echo $link_ids[$category->id_category] ?>"><?php echo $category->getTitle(); ?></a>
    </h4>

   <?php if (empty($items[$category->id_category])) : ?>
        <p><?php echo FrontLib_Lang::tr("txt_no_meals_in_displayed_category"); ?></p>
    <?php else : ?>
        <ul>
        <?php foreach ($items[$category->id_category] as $meal) : ?>
            <li rel="<?php echo $meal->id_item; ?>">
                <div class="meal">
                    <h3>
                        <a title="Add '<?php echo $meal->getTitle(); ?>' to your cart"
                           href="<?php echo $this->baseUrl() . "/restaurant/orders/item-to-cart/item_id/" . $meal->id_item; ?>"
                           >
                            <?php echo $meal->getTitle(); ?>
                        </a>
                    </h3>
                    <span class="label"><?php echo FrontLib_Lang::tr("menu_meal_small_desc"); ?></span><span>&nbsp; <?php echo $meal->getSmallDesc(); ?></span>
                </div>
                <div class="add-to-cart" rel="<?php echo $meal->id_item; ?>">
                    <h5>&nbsp;<?php echo $meal->getPrice(); ?>,-&nbsp; &euro;</h5>
                </div>
                <div class="clear"></div>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
<?php } ?>

<div class="clear"></div>
</div>
<!-- END restaurant-menu -->

<script type="text/javascript">
    /* Runpoint */
$(document).ready(function(eventready)
{
    $.ajax("<?php echo $this->baseUrl(); ?>/restaurant/menu/custom-meal", {
        type: "POST",
        async: true,
        data: "mid=" + "<?php echo $this->menu->id_menu; ?>",
        success: function (data, tst, xhr) {
            $("#custom-meal").html(data);
        }
    });

    /* register functions */
    var getCustomMeal = function(item_id, disable)
    {
        /* Remove registered wishes */
        var in_wishes = $("#custom-meal #wishes input[type='hidden']").each(function(elm) {
            $(elm).remove();
        });
        $("#custom-meal form ul li").each(function(elm) {
            $(this).css("background", "transparent");
            $(this).css("border", "1px solid transparent");
        });

        /* Configure new display */
        /* XXX !! */
        modifyDisplay    = 'block';
        immutableDisplay = 'none';

        $("#item_id")[0].innerHTML = "<input type='hidden' value='" + item_id + "' name='item_id' />";
        $("#custom-meal").css("display", "block");
        $("#custom-meal #modifiable-meal").css("display", modifyDisplay);
        $("#custom-meal #immutable-meal").css("display", immutableDisplay);

        var begTween = disable === true ? 623 : 50;
        var endTween = disable === true ? 50 : 623;

        /* execute tween */
        window.scrollTo(0, 300);
        /* XXX tween effect */

        var displayBlock = "block";
        if (disable === true)
            displayBlock = "none";

        $("#custom-meal").css("display", displayBlock);
    };

    /* Disable category contents */
    $(".category-content").each(function(elm) {
        $(this).css("display", "none");
    });

    /* Register OPEN CATEGORY */
    $("a.category-name-link").each(function(elm) {
        $(this).click(function(ev) {
            var linkElm = this;

            var category = $(linkElm).attr("rel");
            $("ul.category-images li").each(function(el) { $(this).css("display", "none") });
            $("ul.category-images li[rel='" + category + "']").css("display", "block");

            $("#menu-splash").css("display", 'none');

            /* Browse through marks to find content to be displayed. */
            $(".category-content-mark").each(function(mark) {

                var container = $(this)[0].parentNode.parentNode;
                if ($(linkElm).attr('rel') == $(this).attr('rel'))
                    $(container).css("display", 'block');
                else
                    $(container).css("display", 'none');
            });

            /* Disable custom meal. */
            //getCustomMeal(103, true);
            return false;
        });
    });

    /* Handle ITEM TO CART */
    $(".category-content ul li").each(function(elm) {

        $(this).click(function(ev)
        {

            /* disable custom meal first */
            getCustomMeal(103, true);

            var item_id = $(this).attr('rel');

            var uriCurrentPrice = '<?php echo $this->baseUrl(); ?>/restaurant/menu/get-current-price';
            $.ajax(uriCurrentPrice, {
                type: 'POST',
                async: true,
                data: 'item_id=' + item_id,
                success: function(data, tstatus, xhr)
                {
                    $("#current-item-price").html(data);
                }
            });

            /* enable again once data is received. */
            getCustomMeal(item_id, false);
            return false;
        });

        $(this).mouseenter(function(ev)
        {
            $(this).children("div.meal h3 a").each(function(elm) { $(elm).css("font", "bold 20px Tahoma, Arial, sans-serif"); });
            $(this).children("div.meal h3 a").each(function(elm) { $(elm).css("color", "#fff"); });
            $(this).children("div.meal h3 a").each(function(elm) { $(elm).css("text-decoration", "underline"); });
        });

        $(this).mouseleave(function(ev)
        {
            $(this).children("div.meal h3 a").each(function(elm) { $(elm).css("font", "normal 20px Tahoma, Arial, sans-serif"); });
            $(this).children("div.meal h3 a").each(function(elm) { $(elm).css("color", "#746d4f"); });
            $(this).children("div.meal h3 a").each(function(elm) { $(elm).css("text-decoration", "none"); });
        });
    });

});
</script>
