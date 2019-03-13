<h2><?php echo BackLib_Lang::tr("txt_h2_delete_room"); ?></h2>
<?php if (empty($this->rooms)) : ?>
    <div class="error">,
        <p><?php echo BackLib_Lang::tr("txt_error_delete_no_room"); ?></p>
    </div>
<?php else : ?>
    <p><?php echo BackLib_Lang::tr("txt_p1_delete_room"); ?></p>
    <p><?php echo BackLib_Lang::tr("txt_p2_delete_room"); ?></p>
    <ul>
    <?php foreach ($this->rooms as $room) :  ?>
        <li>&quot;<span class="bold"><?php echo $room->title; ?></span>&quot;</li>
    <?php endforeach; ?>
    </ul>
    <div class="actions">
    <form action="<?php echo $this->baseUrl(); ?>/manage/bookings/delete-room" method="post">
        <?php foreach ($this->rooms as $room) :  ?>
        <input type="hidden" name="rooms[]" value="<?php echo $room->id_room; ?>" />
        <?php endforeach; ?>
        <input type="submit"
               name="is_confirmed"
               value="<?php echo BackLib_Lang::tr("form_submit_delete"); ?>" />

        <a class="nyroModalClose" href="<?php echo $this->baseUrl(); ?>/manage/bookings/list-rooms"><?php echo BackLib_Lang::tr("form_cancel"); ?></a>
        <div class="clear"></div>
    </form>
    </div>
<?php endif; ?>
