<?php
function isEdit($item)
{
    return null !== $item && is_object($item);
}

$currentID          = isEdit($this->item) ? $this->item->getPK() : "";
$currentTitle       = isEdit($this->item) ? $this->item->getTitle() : "";
$currentSmallDesc   = isEdit($this->item) ? $this->item->getSmallDesc() : "";
$currentDescription = isEdit($this->item) ? $this->item->getDescription() : "";
$currentPrice       = isEdit($this->item) ? $this->item->getPrice() : "";
$currentCategory    = isEdit($this->item) ? $this->item->getCategory()->id_category : "";
?>
<div class="content" style="margin: 0px;">
    <h2><?php echo BackLib_Lang::tr("txt_h2_menu_additem"); ?></h2>
    <?php echo BackLib_Lang::tr("txt_p_menu_additem"); ?>
    <br />

    <fieldset class="item-form">
        <legend><?php echo BackLib_Lang::tr("txt_form_item_legend"); ?></legend>

    <form method="post" action="<?php echo $this->baseUrl(); ?>/manage/menu/add-item">

        <label for="item[title]"><?php echo BackLib_Lang::tr("txt_form_item_title"); ?></label>
        <input type="text" id="item_title" name="item[title]" value="<?php echo $currentTitle; ?>" />

        <div class="clear"></div>
        <label for="item[small_desc]"><?php echo BackLib_Lang::tr("txt_form_item_small_desc"); ?></label>
        <textarea class="small_desc" id="item_small_desc" name="item[small_desc]"><?php echo $currentSmallDesc; ?></textarea>

        <div class="clear"></div>
        <label for="item[id_category]"><?php echo BackLib_Lang::tr("txt_form_item_category"); ?></label>
        <select name="item[id_category]" id="item_category">
            <option value=""><?php echo BackLib_Lang::tr("txt_opt_item_select_category"); ?></option>
            <?php foreach ($this->categories as $category) {
                    $selected = $currentCategory == $category->id_category ? " selected='selected'" : "";
                    echo "<option{$selected} value='{$category->id_category}'>{$category->title}</option>"; } ?>
        </select>

        <div class="clear"></div>
        <div class="clear"></div>
        <label for="item[price]"><?php echo BackLib_Lang::tr("txt_form_item_price"); ?></label>
        <input type="text" id="item_price" name="item[price]" value="<?php echo $currentPrice; ?>" />

        <div class="clear"></div>
        <input type="hidden" id="item_id" name="item[id_item]" value="<?php echo $currentID; ?>" />
        <input type="submit" name="item[process]" value="<?php echo BackLib_Lang::tr("form_submit"); ?>" />
        <a class="nyroModalClose" href="#"><?php echo BackLib_Lang::tr("form_cancel"); ?></a>
    </form>
    </fieldset>
</div>
