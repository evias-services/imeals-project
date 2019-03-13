<fieldset>
    <legend><?php echo BackLib_Lang::tr("txt_menu_edit_form_legend"); ?></legend>

    <form action="<?php echo $this->baseUrl(); ?>/manage/menu/edit" method="post">
        <label for="category[id_category]"><?php echo BackLib_Lang::tr("txt_menu_edit_form_category"); ?></label>
        <select name="category[id_category]">
            <option value=""><?php echo BackLib_Lang::tr("txt_opt_menu_edit_select_category"); ?></option>
            <?php foreach (AppLib_Model_Category::getList() as $category)
                    echo "<option value='{$category->id_category}'>{$category->title}</option>"; ?>
        </select>

        <div class="clear"></div>
        <label for="category[position]"><?php echo BackLib_Lang::tr("txt_menu_edit_form_position"); ?></label>
        <select name="category[position]">
            <option value=""><?php echo BackLib_Lang::tr("txt_opt_menu_edit_select_position"); ?></option>
            <option value="begin"><?php echo "1"; ?></option>
            <option value="next_pos"><?php echo AppLib_Model_Menu::getInstance()->getNextPosition(); ?></option>
        </select>

        <div class="clear"></div>
        <input type="submit" name="do_process" value="<?php echo BackLib_Lang::tr("txt_opt_menu_edit_submit"); ?>" />
    </form>
</fieldset>

<fieldset>
    <legend><?php echo BackLib_Lang::tr("txt_menu_preview_form_legend"); ?></legend>

    <?php echo $this->partial("menu/print.php"); ?>
</fieldset>
