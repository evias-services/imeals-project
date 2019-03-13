<h2><?php echo BackLib_Lang::tr("txt_h2_delete_items"); ?></h2>
<?php if (empty($this->items)) : ?>
    <div class="error">,
        <p><?php echo BackLib_Lang::tr("txt_error_delete_no_items"); ?></p>
    </div>
<?php else : ?>
    <p><?php echo BackLib_Lang::tr("txt_p1_delete_items"); ?></p>
    <br />
    <p><?php echo BackLib_Lang::tr("txt_p2_delete_items"); ?></p>
    <br />
    <ul>
    <?php foreach ($this->items as $item) :  ?>
        <li>&quot;<span class="bold"><?php echo $item->title; ?></span>&quot;</li>
    <?php endforeach; ?>
    </ul>
    <div class="actions">
    <form action="<?php echo $this->baseUrl(); ?>/manage/menu/delete-item" method="post">
        <?php foreach ($this->items as $item) :  ?>
        <input type="hidden" name="items[]" value="<?php echo $item->id_item; ?>" />
        <?php endforeach; ?>
        <input type="submit"
               name="is_confirmed"
               value="<?php echo BackLib_Lang::tr("txt_form_submit_delete_item"); ?>" />

        <a href="<?php echo $this->baseUrl(); ?>/manage/menu/list-items"><?php echo BackLib_Lang::tr("txt_form_cancel_delete_item"); ?></a>
        <div class="clear"></div>
    </form>
    </div>
<?php endif; ?>
