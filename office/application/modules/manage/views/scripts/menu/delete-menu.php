<h2><?php echo BackLib_Lang::tr("txt_h2_delete_menu"); ?></h2>
<?php if (empty($this->menus)) : ?>
    <div class="error">,
        <p><?php echo BackLib_Lang::tr("txt_error_delete_no_menu"); ?></p>
    </div>
<?php else : ?>
    <p><?php echo BackLib_Lang::tr("txt_p1_delete_menu"); ?></p>
    <p><?php echo BackLib_Lang::tr("txt_p2_delete_menu"); ?></p>
    <ul>
    <?php foreach ($this->menus as $menu) :  ?>
        <li>&quot;<span class="bold"><?php echo $menu->title; ?></span>&quot;</li>
    <?php endforeach; ?>
    </ul>
    <div class="actions">
    <form action="<?php echo $this->baseUrl(); ?>/manage/menu/delete-menu" method="post">
        <?php foreach ($this->menus as $menu) :  ?>
        <input type="hidden" name="menus[]" value="<?php echo $menu->id_menu; ?>" />
        <?php endforeach; ?>
        <input type="submit"
               name="is_confirmed"
               value="<?php echo BackLib_Lang::tr("form_submit"); ?>" />

        <a href="<?php echo $this->baseUrl(); ?>/manage/menu/index"><?php echo BackLib_Lang::tr("form_cancel"); ?></a>
        <div class="clear"></div>
    </form>
    </div>
<?php endif; ?>
