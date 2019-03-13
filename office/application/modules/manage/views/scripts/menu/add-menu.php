<?php
function isEdit($menu)
{
    return null !== $menu && is_object($menu);
}

$currentID      = isEdit($this->menu) ? $this->menu->getPK() : "";
$currentResID   = isEdit($this->menu) ? $this->menu->id_restaurant : "";
$currentTitle   = isEdit($this->menu) ? $this->menu->title : "";
$currentCategories = isEdit($this->menu) ? $this->menu->getCategories() : array();
?>
<div class="content" style="margin: 0px;">
    <h2><?php echo BackLib_Lang::tr("txt_h2_menu_edit"); ?></h2>
    <?php echo BackLib_Lang::tr("txt_p_menu_edit"); ?>
    <br />

    <fieldset>
    <legend><?php echo BackLib_Lang::tr("txt_form_menu_legend"); ?></legend>
    <form method="post" action="<?php echo $this->baseUrl(); ?>/manage/menu/add-menu">

        <p>
            <label for="menu[id_restaurant]"><?php echo BackLib_Lang::tr("txt_form_menu_restaurant"); ?></label>
            <select name="menu[id_restaurant]" id="menu_restaurant">
                <option value=""><?php echo BackLib_Lang::tr("txt_opt_menu_select_restaurant"); ?></option>
                <?php foreach ($this->restaurants as $restaurant) {
                        $selected = $currentResID == $restaurant->id_restaurant ? " selected='selected'" : "";
                        echo "<option{$selected} value='{$restaurant->id_restaurant}'>{$restaurant->title}</option>"; } ?>
            </select>
        </p>

        <p>
            <label for="menu[title]"><?php echo BackLib_Lang::tr("form_label_menu_title"); ?></label>
            <input type="text" id="menu_title" name="menu[title]" value="<?php echo $currentTitle; ?>" />
        </p><br />

        <h4><?php echo BackLib_Lang::tr("h4_menu_edition"); ?></h4>
        <p><?php echo BackLib_Lang::tr("p_menu_editor"); ?></p>

        <div id="menu-editor">

            <ul style="margin: 0px;">
                <li style="background:white; float:left;"><button class="button menu-addcategory"><?php echo BackLib_Lang::tr("menu_editor_action_addcategory"); ?></button></li>
                <li style="background:white; float:left;"><button class="button menu-addmeal"><?php echo BackLib_Lang::tr("menu_editor_action_addmeal"); ?></button></li>
            </ul>
            <div class="clear"></div>

            <div style="display:none;" id="category_default_title"><?php echo BackLib_Lang::tr("category_default_title"); ?></div>
            <div style="display:none;" id="category_default_desc"><?php echo BackLib_Lang::tr("category_default_desc"); ?></div>
            <div style="display:none;" id="meal_default_title"><?php echo BackLib_Lang::tr("meal_default_title"); ?></div>
            <div style="display:none;" id="meal_default_desc"><?php echo BackLib_Lang::tr("meal_default_desc"); ?></div>
            <div style="display:none;" id="label_meal_menu_number"><?php echo BackLib_Lang::tr("label_meal_menu_number"); ?></div>
            <div style="display:none;" id="label_can_be_customized"><?php echo BackLib_Lang::tr("label_can_be_customized"); ?></div>
            <div style="display:none;" id="error_select_category_for_meal"><?php echo BackLib_Lang::tr("error_select_category_for_meal"); ?></div>
            <div style="display:none;" id="warning_more_than_one_category"><?php echo BackLib_Lang::tr("warning_more_than_one_category"); ?></div>
            <div style="display:none;" id="confirm_delete_category"><?php echo sprintf(BackLib_Lang::tr("confirm_delete_category"), "\n\n", "\n\n"); ?></div>

            
            <?php

            /* XXX remove duplicate code. */
            
            if (empty($currentCategories)) { ?>
            
            <div class="ui-widget-header categories-list">
                <div class="category" rel="0">
                    <div class="category-presentation ui-widget-content">
                        <div class="write hidden">
                            <input type="text" style="width: 150px;" name="menu[categories][0][title]" value="<?php echo BackLib_Lang::tr("category_default_title"); ?> 1" />
                            <input type="text" style="width: 210px;" iname="menu[categories][0][description]" value="<?php echo BackLib_Lang::tr("category_default_desc"); ?>" />
                        </div>
                        <div class="read">
                            <p align="center"><span><strong><?php echo BackLib_Lang::tr("category_default_title"); ?> 1</strong></span></p>
                            <p align="left"><span><i><?php echo BackLib_Lang::tr("category_default_desc"); ?></i></span></p>
                        </div>
                    </div>
                    <div class="category-actions">
                        <ul>
                            <li rel="0" class="delete"><button><img src="<?php echo $this->baseUrl(); ?>/images/ico-delete.gif" alt="Delete Category" /></button></li>
                            <li rel="0" class="modify"><button><img src="<?php echo $this->baseUrl(); ?>/images/edit.png" alt="Edit Category" /></button></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="ui-widget-header meals-list">
                <div class="meals" rel="0">
                    <div class="meal" rel="0">
                        <div class="meal-data ui-widget-content">
                            <div class="write hidden">
                                <p align="center"><input style="margin-left: 25%; width: 180px; font-weight:bold; font-size:12pt;" type="text" style="width: 150px;" name="menu[categories][0][meals][0][title]" value="<?php echo BackLib_Lang::tr("meal_default_title"); ?> 1" /></p>
                                <p align="left"><input type="text" style="font-weight:bold; font-size:9pt; font-style: italic" name="menu[categories][0][meals][0][small_desc]" value="<?php echo BackLib_Lang::tr("meal_default_desc"); ?>" /></p>
                                <br />
                                <p align="left">
                                    <span style="float:left;"><?php echo BackLib_Lang::tr("label_meal_menu_number"); ?></span>
                                    <input type="text" class="spinner" name="menu[categories][0][meals][0][meal_menu_number]" value="1" />
                                </p>
                                <div class="clear"></div>
                                <p align="left">
                                    <span><?php echo BackLib_Lang::tr("label_can_be_customized"); ?></span>
                                    <input type="checkbox" name="menu[categories][0][meals][0][can_be_customized]" value="" />
                                </p>
                                <div class="clear"></div>
                            </div>
                            <div class="read">
                                <p align="center"><span style="font-size: 12pt;"><i>#1</i>&nbsp;<strong><?php echo BackLib_Lang::tr("meal_default_title"); ?> 1</strong></span></p>
                                <p align="left"><span style="font-size: 9pt;"><i><?php echo BackLib_Lang::tr("label_ingredients"), "&nbsp;", BackLib_Lang::tr("meal_default_desc"); ?></i></span></p>
                                <p align="left"><span><strong><?php echo BackLib_Lang::tr("label_meal_menu_number"); ?></strong></span>&nbsp;<i>1</i></p>
                                <p align="left"><span><strong><?php echo BackLib_Lang::tr("label_can_be_customized"); ?></strong></span>&nbsp;<i><?php echo BackLib_Lang::tr("no"); ?></i></p>
                            </div>
                        </div>
                        <div class="meal-actions">
                            <ul>
                                <li rel="0" class="delete"><button><img src="<?php echo $this->baseUrl(); ?>/images/ico-delete32.png" alt="Delete Meal" /></button></li>
                                <li rel="0" class="modify"><button><img src="<?php echo $this->baseUrl(); ?>/images/edit32.png" alt="Edit Meal" /></button></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php
            }
            else {
            ?>
            
            <div class="ui-widget-header categories-list">

            <?php
                $c_items = array();
                $c = 0;
                foreach ($currentCategories as $category) :
                    $c_items[] = $category->getItems();
            ?>
                <div class="category" rel="<?php echo $c; ?>">
                    <div class="category-presentation ui-widget-content">
                        <div class="write hidden">
                            <input type="text" style="width: 150px;" id="category-<?php echo $c; ?>-title" name="menu[categories][<?php echo $c; ?>][title]" value="<?php echo $category->title; ?>" />
                            <input type="text" style="width: 210px;" id="category-<?php echo $c; ?>-description" name="menu[categories][<?php echo $c; ?>][description]" value="<?php echo $category->description; ?>" />
                        </div>
                        <div class="read">
                            <input type="hidden" name="menu[categories][<?php echo $c; ?>][id_category]" value="<?php echo $category->id_category; ?>" />
                            <p align="center"><span><strong><?php echo $category->title; ?></strong></span></p>
                            <p align="left"><span><i><?php echo $category->description; ?></i></span></p>
                        </div>
                    </div>
                    <div class="category-actions">
                        <ul>
                            <li rel="<?php echo $c; ?>" class="delete"><button><img src="<?php echo $this->baseUrl(); ?>/images/ico-delete.gif" alt="Delete Category" /></button></li>
                            <li rel="<?php echo $c; ?>" class="modify"><button><img src="<?php echo $this->baseUrl(); ?>/images/edit.png" alt="Edit Category" /></button></li>
                        </ul>
                    </div>
                </div>

            <?php
                    $c++;
                endforeach;
            ?>

            </div>
            
            <div class="ui-widget-header meals-list">

            <?php
                foreach ($c_items as $cix => $items) :
            ?>
                <div class="meals" rel="<?php echo $cix; ?>" style="display:none;">
                    <?php
                    $m = 0;
                    foreach ($items as $meal) : ?>
                    <div class="meal" rel="<?php echo $m; ?>">
                        <div class="meal-data ui-widget-content">
                            <div class="write hidden">
                                <input type="hidden" name="menu[categories][<?php echo $cix; ?>][meals][<?php echo $m ;?>][id_item]" value="<?php echo $meal->id_item; ?>" />
                                <p align="center"><input style="margin-left: 25%; width: 180px; font-weight:bold; font-size:12pt;" type="text" style="width: 150px;" name="menu[categories][<?php echo $cix; ?>][meals][<?php echo $m; ?>][title]" value="<?php echo $meal->title; ?>" /></p>
                                <p align="left"><input type="text" style="font-weight:bold; font-size:9pt; font-style: italic" name="menu[categories][<?php echo $cix; ?>][meals][<?php echo $m; ?>][small_desc]" value="<?php echo $meal->small_desc; ?>" /></p>
                                <br />
                                <p align="left">
                                    <span style="float:left;"><?php echo BackLib_Lang::tr("label_meal_menu_number"); ?></span>
                                    <input type="text" class="spinner" name="menu[categories][<?php echo $cix; ?>][meals][<?php echo $m; ?>][meal_menu_number]" value="<?php echo $meal->meal_menu_number; ?>" />
                                </p>
                                <div class="clear"></div>
                                <p align="left">
                                    <span><?php echo BackLib_Lang::tr("label_can_be_customized"); ?></span>
                                    <?php $checked = $meal->can_be_customized ? " checked='checked'" : ""; ?>
                                    <input type="checkbox"<?php echo $checked; ?> name="menu[categories][<?php echo $cix; ?>][meals][<?php echo $m; ?>][can_be_customized]" />
                                </p>
                                <div class="clear"></div>
                            </div>
                            <div class="read">
                                <p align="center"><span style="font-size: 12pt;"><i>#<?php echo $meal->meal_menu_number; ?></i>&nbsp;<strong><?php echo $meal->title; ?></strong></span></p>
                                <p align="left"><span style="font-size: 9pt;"><i><?php echo BackLib_Lang::tr("label_ingredients"), "&nbsp;", $meal->small_desc; ?></i></span></p>
                                <p align="left"><span><strong><?php echo BackLib_Lang::tr("label_meal_menu_number"); ?></strong></span>&nbsp;<i><?php echo $meal->meal_menu_number; ?></i></p>
                                <p align="left"><span><strong><?php echo BackLib_Lang::tr("label_can_be_customized"); ?></strong></span>&nbsp;<i><?php echo $meal->can_be_customized ? BackLib_Lang::tr("yes") : BackLib_Lang::tr("no"); ?></i></p>
                            </div>
                        </div>
                        <div class="meal-actions">
                            <ul>
                                <li rel="<?php echo $m; ?>" class="delete"><button><img src="<?php echo $this->baseUrl(); ?>/images/ico-delete32.png" alt="Delete Meal" /></button></li>
                                <li rel="<?php echo $m; ?>" class="modify"><button><img src="<?php echo $this->baseUrl(); ?>/images/edit32.png" alt="Edit Meal" /></button></li>
                            </ul>
                        </div>
                    </div>
                    <?php
                        $m++;
                    endforeach; /* foreach $category->getItems() */ ?>
                </div>
            <?php
                endforeach;
            ?>
               
            </div>
            
            <?php
            } /* end else */ ?>

            
            <div class="clear"></div>

            <ul style="margin: 0px;">
                <li style="background:white; float:left;"><button class="button menu-addcategory"><?php echo BackLib_Lang::tr("menu_editor_action_addcategory"); ?></button></li>
                <li style="background:white; float:left;"><button class="button menu-addmeal"><?php echo BackLib_Lang::tr("menu_editor_action_addmeal"); ?></button></li>
            </ul>
            <div class="clear"></div>
        </div>

        <br />

        <input type="hidden" id="menu_id" name="menu[id_menu]" value="<?php echo $currentID; ?>" />
        <input type="submit" class="button" name="process_edit_menu" value="<?php echo BackLib_Lang::tr("form_submit"); ?>" />
        <a class="nyroModalClose" href="#"><?php echo BackLib_Lang::tr("form_cancel"); ?></a>
    </form>
    </fieldset>
</div>
