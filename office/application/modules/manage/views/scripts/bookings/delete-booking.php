<h2><?php echo BackLib_Lang::tr("txt_h2_delete_restaurant"); ?></h2>
<?php if (empty($this->restaurants)) : ?>
    <div class="error">,
        <p><?php echo BackLib_Lang::tr("txt_error_delete_no_restaurant"); ?></p>
    </div>
<?php else : ?>
    <p><?php echo BackLib_Lang::tr("txt_p1_delete_restaurant"); ?></p>
    <p><?php echo BackLib_Lang::tr("txt_p2_delete_restaurant"); ?></p>
    <ul>
    <?php foreach ($this->restaurants as $restaurant) :  ?>
        <li>&quot;<span class="bold"><?php echo $restaurant->title; ?></span>&quot;</li>
    <?php endforeach; ?>
    </ul>
    <div class="actions">
    <form action="<?php echo $this->baseUrl(); ?>/manage/restaurant/delete-restaurant" method="post">
        <?php foreach ($this->restaurants as $restaurant) :  ?>
        <input type="hidden" name="restaurants[]" value="<?php echo $restaurant->id_restaurant; ?>" />
        <?php endforeach; ?>
        <input type="submit"
               name="is_confirmed"
               value="<?php echo BackLib_Lang::tr("txt_form_submit_delete_restaurant"); ?>" />

        <a href="<?php echo $this->baseUrl(); ?>/manage/restaurant/index"><?php echo BackLib_Lang::tr("txt_form_cancel_delete_restaurant"); ?></a>
        <div class="clear"></div>
    </form>
    </div>
<?php endif; ?>
