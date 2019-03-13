<h2><?php echo BackLib_Lang::tr("txt_h2_delete_table"); ?></h2>
<?php if (empty($this->tables)) : ?>
    <div class="error">,
        <p><?php echo BackLib_Lang::tr("error_delete_nodata"); ?></p>
    </div>
<?php else : ?>
    <p><?php echo BackLib_Lang::tr("txt_p1_delete_table"); ?></p>
    <p><?php echo BackLib_Lang::tr("txt_p2_delete_table"); ?></p>
    <ul>
    <?php foreach ($this->tables as $table) :  ?>
        <li>&quot;<span class="bold"><?php echo $table->getTitle(); ?></span>&quot;</li>
    <?php endforeach; ?>
    </ul>
    <div class="actions">
    <form action="<?php echo $this->baseUrl(); ?>/manage/bookings/delete-table" method="post">
        <?php foreach ($this->tables as $table) :  ?>
        <input type="hidden" name="tables[]" value="<?php echo $table->id_table; ?>" />
        <?php endforeach; ?>
        <input type="submit"
               name="is_confirmed"
               value="<?php echo BackLib_Lang::tr("form_submit_delete"); ?>" />

        <a href="<?php echo $this->baseUrl(); ?>/manage/bookings/list-tables"><?php echo BackLib_Lang::tr("form_cancel"); ?></a>
        <div class="clear"></div>
    </form>
    </div>
<?php endif; ?>
