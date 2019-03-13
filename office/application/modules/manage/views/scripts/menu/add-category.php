<?php
function isEdit($cat)
{
    return null !== $cat && is_object($cat);
}

$currentID    = isEdit($this->category) ? $this->category->getPK() : "";
$currentParent= isEdit($this->category) ? $this->category->getParent() : "";
$currentTitle = isEdit($this->category) ? $this->category->getTitle() : "";
$currentDesc  = isEdit($this->category) ? $this->category->getDescription() : "";
?>
<div class="content" style="margin: 0px;">
    <h2><?php echo BackLib_Lang::tr("txt_h2_menu_addcategory"); ?></h2>
    <?php echo BackLib_Lang::tr("txt_p_menu_addcategory"); ?>
    <br />

    <fieldset class="category-form">
        <legend><?php echo BackLib_Lang::tr("txt_form_category_legend"); ?></legend>

    <form method="post" action="<?php echo $this->baseUrl(); ?>/manage/menu/add-category">

        <label for="category[title]"><?php echo BackLib_Lang::tr("txt_form_category_title"); ?></label>
        <input type="text" id="category_title" name="category[title]" value="<?php echo $currentTitle; ?>" />

        <div class="clear"></div>
        <label for="category[description]"><?php echo BackLib_Lang::tr("txt_form_category_description"); ?></label>
        <input type="text" id="category_description" name="category[description]" value="<?php echo $currentDesc; ?>" />

        <div class="clear"></div>
        <label for="category[id_parent_category]"><?php echo BackLib_Lang::tr("txt_form_category_parent"); ?></label>
        <select id="category_parent" name="category[id_parent_category]">
            <option value=""><?php echo BackLib_Lang::tr("txt_form_category_select_parent"); ?></option>
            <?php
            foreach ($this->categories as $category) {
                $label  = "";
                $parent = $category->getParent();
                while ($parent != null) {
                    $label .= $parent->getTitle() . "&nbsp;&gt;&nbsp;";
                    $parent = $parent->getParent();
                }
                $label .= $category->getTitle();
                echo "<option value='{$category->id_category}'>$label</option>";
            }
            ?>
        </select>

        <div class="clear"></div>
        <input type="hidden" id="category_id" name="category[id_category]" value="<?php echo $currentID; ?>" />
        <input type="submit" name="category[process]" value="<?php echo BackLib_Lang::tr("form_submit"); ?>" />
        <a class="nyroModalClose" href="#"><?php echo BackLib_Lang::tr("form_cancel"); ?></a>
    </form>
    </fieldset>
</div>
