<?php
    $categories = $this->menu->getCategories();
    $items      = array();

    $link_ids   = array();
    foreach ($categories as $category) :
        /* If category title appears more than once, links have to differ. */
        $clean_title = AppLib_Utils::cleanUrl($category->getTitle());
        $link_label  = AppLib_Utils::suffixNthIn($clean_title, $link_ids);
        $link_ids[$category->id_category] = $link_label;

        $items[$category->id_category] = $category->getItems();
    endforeach;
?>

<!-- RENDERING OF CATEGORIES NAVIGATION -->
<div id="categories-navigation">
    <ul>
<?php foreach ($categories as $category) :?>
        <li>
            <h3><a class="category-name-link" rel="<?php echo $category->id_category; ?>" href="#<?php echo $link_ids[$category->id_category]; ?>"><?php echo $category->getTitle(); ?></a></h3>
        </li>
<?php endforeach;?>
    </ul>
</div>

<!-- RENDERING OF CATEGORIES MEALS -->
<?php foreach ($categories as $category) : ?>
<div class="category-content" rel="<?php echo $category->id_category; ?>">
    <h4><a class="category-content-mark"
           rel="<?php echo $category->id_category; ?>"
           id="<?php echo $link_ids[$category->id_category] ?>"><?php echo $category->getTitle(); ?></a>
    </h4>

    <?php if (empty($items[$category->id_category])) : ?>
        <p><?php echo BackLib_Lang::tr("txt_no_meals_in_displayed_category"); ?></p>
    <?php else : ?>
        <ul>
        <?php foreach ($items[$category->id_category] as $meal) : ?>
            <li><div>
                <div class="top-line">
                    <div class="meal-title"><span class="meal-title"><?php echo $meal->getTitle(); ?></span></div>
                    <div class="meal-price">
                        <div class="price-tag" rel="<?php echo $meal->id_item; ?>"><span class="price">15,99 &euro;</span></div>
                        <div class="add-to-cart" rel="<?php echo $meal->id_item; ?>">Add to cart</div>
                    </div>
                    <div class="clear"></div>
                </div>

                <div class="bottom-line">
                    <div class="label fLeft"><span class="label">Zutaten / Ingr&eacute;dients :</span></div>
                    <div class="ingredients fLeft"><span class="meal-ing"><?php echo $meal->getSmallDesc(); ?></span><div>
                    <div class="clear"></div>
               </div>
            </div></li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
<?php endforeach;?>

<script type="text/javascript">
    var hideContents = function()
    {
        $(".category-content").each(function(elm) {
            $(this).hide();
        });
    };

    var showContent = function()
    {
        var linkElm = $(this);

        hideContents();

        $(".category-content-mark").each(function(mark) {
            var mark = $(this);

            if (linkElm.attr("rel") == mark.attr("rel"))
                /* Found clicked category's content. */
                $(".category-content[rel='" + mark.attr("rel") + "']").show();
        });

        return false;
    };

    (function() {
        hideContents();

        /* Register click handlers */
        $("a.category-name-link").each(function(elm) {
            this.addEventListener("click", showContent, false);
        });

    })();
</script>