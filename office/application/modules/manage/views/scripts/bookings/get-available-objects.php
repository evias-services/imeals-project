
<?php
if (empty($this->objects)) : ?>
    <p class='dark-red'><u><strong><?php echo BackLib_Lang::tr("error_availability_match"); ?></strong></u></p>
<?php
else :
    $first_object = $this->objects[0];
?>
    <h4><?php echo BackLib_Lang::tr("h4_availability_response"); ?></h4>

    <?php
    if (! $first_object->availability_perfect_match) : ?>
    <p class='dark-red'><u><strong><?php echo BackLib_Lang::tr("warning_availability_match"); ?></strong></u></p>
    <?php
    endif ; ?>

    <ul class='availability-suggestions'>
        <?php
        foreach ($this->objects as $object) :
            list(, $avail_time) = explode(" ", $object->availability_start);
            list(, $endavail_time) = explode(" ", $object->availability_end);
            $title = $object->getTitle();

            if ($object instanceof AppLib_Model_RoomTable)
                $title = $object->getTitle(true);

            $cnt_p = sprintf("(%d %s)", $object->getCountPlaces(),
                                        BackLib_Lang::tr("object_count_places"));
        ?>
        <li>
            <input  type='radio'
                    style='float:left;'
                    group='booked_object'
                    name='booking[id_booked_object]'
                    rel='<?php echo "{$avail_time}-{$endavail_time}"; ?>'
                    value='<?php echo $object->getPK(); ?>' />
            <span>
                <strong><?php echo $title; ?></strong>&nbsp;<?php echo $cnt_p; ?>
                &nbsp;<?php echo BackLib_Lang::tr("free_at"); ?>
                &nbsp;<strong><u><?php echo $avail_time; ?></u></strong>
            </span>
        </li>
        <?php
        endforeach; ?>
    </ul>
    <div id='actions_require_select_option'>
        <p class='dark-red'><u><strong><?php echo BackLib_Lang::tr("require_select_availability"); ?></strong></u></p>
    </div>
<?php
endif; ?>
