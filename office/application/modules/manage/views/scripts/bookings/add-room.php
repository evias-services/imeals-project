<?php
function isEdit($room)
{
    return null !== $room && is_object($room);
}

$currentID    = isEdit($this->room) ? $this->room->getPK() : "";
$currentResID = isEdit($this->room) ? $this->room->id_restaurant : "";
$currentTitle = isEdit($this->room) ? $this->room->title : "";
$currentTables= isEdit($this->room) ? $this->room->getTables() : array();
?>
<div class="content" style="margin: 0px;">
    <h2><?php echo BackLib_Lang::tr("txt_h2_room_edit"); ?></h2>
    <?php echo BackLib_Lang::tr("txt_p_room_edit"); ?>
    <br />

    <fieldset>
    <legend><?php echo BackLib_Lang::tr("txt_form_room_legend"); ?></legend>
    <form method="post" action="<?php echo $this->baseUrl(); ?>/manage/bookings/add-room">

        <p>
            <label for="room[id_restaurant]"><?php echo BackLib_Lang::tr("form_label_room_restaurant"); ?></label>
            <select name="room[id_restaurant]" id="room_restaurant">
                <option value=""><?php echo BackLib_Lang::tr("opt_filter_select"); ?></option>
                <?php foreach ($this->restaurants as $restaurant) {
                        $selected = $currentResID == $restaurant->id_restaurant ? " selected='selected'" : "";
                        echo "<option{$selected} value='{$restaurant->id_restaurant}'>{$restaurant->title}</option>"; } ?>
            </select>
        </p>

        <p>
            <label for="room[title]"><?php echo BackLib_Lang::tr("form_label_room_title"); ?></label>
            <input type="text" id="room_title" name="room[title]" value="<?php echo $currentTitle; ?>" />
        </p><br />

        <h4><?php echo BackLib_Lang::tr("h4_room_tables"); ?></h4>
        <p><?php echo BackLib_Lang::tr("p_room_organisator"); ?></p>

        <div style="display:none;" id="object_count_places_text"><?php echo BackLib_Lang::tr("object_count_places"); ?></div>
        <div style="display:none;" id="table_default_title"><?php echo BackLib_Lang::tr("table_default_title"); ?></div>
        <div id="room-organisator">
            <div class="room-snaptarget ui-widget-header">

                <?php
                if (empty($currentTables)) { ?>
                <div class="table-draggable draggable ui-widget-content">
                    <input type="hidden" rel="position" name="room[tables][0][room_position]" value="" />
                    <input type="hidden" rel="number" name="room[tables][0][table_number]" value="1" />
                    <p align="center"><strong>Table 1</strong></p>
                    <p><input style="width: 15px; margin-left:0px;" class="spinner" name="room[tables][0][count_places]" value="0" /><span><strong>&nbsp;<?php echo BackLib_Lang::tr("object_count_places"); ?></strong></span></p>
                </div>
                <?php
                }
                else {
                    $i = 0;
                    foreach ($currentTables as $table) : ?>
                <div class="table-draggable draggable ui-widget-content" rel="<?php echo $table->id_table; ?>">
                    <input type="hidden" rel="id" name="room[tables][<?php echo $i; ?>][id_table]" value="<?php echo $table->id_table; ?>" />
                    <input type="hidden" rel="number" name="room[tables][<?php echo $i; ?>][table_number]" value="<?php echo $table->table_number; ?>" />
                    <input type="hidden" rel="position" name="room[tables][<?php echo $i; ?>][room_position]" value="<?php echo $table->room_position; ?>" />
                    <p align="center"><strong>Table <?php echo $table->table_number; ?></strong></p>
                    <p><input style="width: 15px; margin-left:0px;" class="spinner" name="room[tables][<?php echo $i; ?>][count_places]" value="<?php echo $table->getCountPlaces(); ?>" /><span><strong>&nbsp;<?php echo BackLib_Lang::tr("object_count_places"); ?></strong></span></p>
                </div>
                <?php
                        $i++;
                    endforeach;
                } ?>
            </div>
            <div class="clear"></div>

            <ul style="margin: 0px;">
                <li style="background:white; float:left;"><button id="room-addtable" class="button"><?php echo BackLib_Lang::tr("action_addtable"); ?></button></li>
            </ul>

        </div>
        <div class="clear"></div>

        <p>
            <input type="hidden" id="room_id" name="room[id_room]" value="<?php echo $currentID; ?>" />
            <input type="submit" class="button" name="process_edit_room" value="<?php echo BackLib_Lang::tr("form_submit"); ?>" />
            <a class="nyroModalClose" href="#"><?php echo BackLib_Lang::tr("form_cancel"); ?></a>
        </p>
    </form>
    </fieldset>
</div>
