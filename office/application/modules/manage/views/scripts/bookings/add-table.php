<?php
function isEdit($table)
{
    return null !== $table && is_object($table);
}

$currentID   = isEdit($this->table) ? $this->table->getPK() : "";
$currentRoom = isEdit($this->table) ? $this->table->id_room : "";
$currentNum  = isEdit($this->table) ? $this->table->table_number : "";
$currentCntP = isEdit($this->table) ? $this->table->count_places : "";
?>
<div class="content" style="margin: 0px;">
    <h2><?php echo BackLib_Lang::tr("txt_h2_table_edit"); ?></h2>
    <?php echo BackLib_Lang::tr("txt_p_table_edit"); ?>
    <br />

    <fieldset>
    <legend><?php echo BackLib_Lang::tr("txt_form_table_legend"); ?></legend>
    <form method="post" action="<?php echo $this->baseUrl(); ?>/manage/bookings/add-table">

        <label for="table[id_room]"><?php echo BackLib_Lang::tr("form_label_table_room"); ?></label>
        <select name="table[id_room]" id="table_room">
            <option value=""><?php echo BackLib_Lang::tr("opt_filter_select"); ?></option>
            <?php foreach ($this->rooms as $room) {
                    $selected = $currentRoom == $room->id_room ? " selected='selected'" : "";
                    echo "<option{$selected} value='{$room->id_room}'>{$room->getTitle()}</option>"; } ?>
        </select>
        <div class="clear"></div><br />

        <label for="table[table_number]"><?php echo BackLib_Lang::tr("form_label_table_number"); ?></label>
        <input type="text" name="table[table_number]" value="<?php echo $currentNum; ?>" />
        <div class="clear"></div>

        <label for="table[count_places]"><?php echo BackLib_Lang::tr("form_label_table_count_places"); ?></label>
        <input type="text" name="table[count_places]" value="<?php echo $currentCntP; ?>" />
        <div class="clear"></div><br />

        <input type="hidden" id="table_id" name="table[id_table]" value="<?php echo $currentID; ?>" />
        <input type="submit" name="process_edit_table" value="<?php echo BackLib_Lang::tr("form_submit"); ?>" />
        <a class="nyroModalClose" href="#"><?php echo BackLib_Lang::tr("form_cancel"); ?></a>
    </form>
    </fieldset>
</div>
