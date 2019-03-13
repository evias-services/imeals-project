<h2><?php echo BackLib_Lang::tr("txt_h2_list_rooms"); ?></h2>
<p><?php echo BackLib_Lang::tr("txt_p_list_rooms"); ?></p>

<div>
    <?php
     echo $this->filterForm($this->filter, array(
        "title" => array("label" => BackLib_Lang::tr("l_filter_title"))
     ));
    ?>
<fieldset>
<legend><?php echo BackLib_Lang::tr("legend_listresults"); ?></legend>
<ul style="margin: 0px;">
    <li><a href="<?php echo $this->baseUrl(); ?>/manage/bookings/add-room"><?php echo BackLib_Lang::tr("action_addroom"); ?></a></li>
</ul>
    <?php
    /* Configuration pagination */
    echo $this->paginationControl($this->paginator, 'Sliding', 'pagination.phtml');
    ?>
<form method="post" action="<?php echo $this->baseUrl(); ?>/manage/bookings/list-rooms-action-grabber">
<table id="rooms-list" cellpadding="3px" cellspacing="0px">
    <thead>
        <th>&nbsp;</th>
        <th><span><?php echo BackLib_Lang::tr("th_id"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_room_restaurant"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_room_title"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_room_is_bookable"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("th_date_created"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("th_date_updated"); ?></span></th>
    </thead>
    <tbody>
<?php
        foreach($this->paginator as $record)
        {
            $room_bookable = $record->is_bookable ? BackLib_Lang::tr("yes") : BackLib_Lang::tr("no");
            $link = $this->baseUrl() . "/manage/bookings/modify-room/rid/" . $record->id_room;
            echo "<tr class='data-row room'>";
            echo "<td rel='room-id'><input type='checkbox' name='rooms[]' value='{$record->id_room}' /></td>";
            echo "<td class='rid txtRight data'><span>{$record->id_room}</span></td>";
            echo "<td class='txtCenter data'><span>{$record->getRestaurant()->title}</span></td>";
            echo "<td class='txtCenter data' rel='title'><a href='{$link}' class='ajax-link'><span class='bold'>{$record->getTitle()}</span></a></td>";
            echo "<td class='txtCenter data'><span class='bold'>{$room_bookable}</span></td>";
            echo "<td class='txtCenter data'><span>" . date("d-m-Y H:i", strtotime($record->date_created)) . "</span></td>";
            echo "<td class='txtCenter data'><span>" . (null === $record->date_updated ? "N/A" : date("d-m-Y H:i", strtotime($record->date_updated)))
                    . "</span></td>";
            echo "</tr>";
        }

?>
        <tr class="actions">
            <td colspan="6">
                <div>
                    <div>
                    <span><?php echo BackLib_Lang::tr("resultslist_select_actions"); ?></span>
                    &nbsp;&nbsp;<select name="selections_action" onchange="submit()">
                            <option value=""><?php echo BackLib_Lang::tr("resultslist_select_action_opt"); ?></option>
                            <option value="delete"><?php echo BackLib_Lang::tr("resultslist_action_delete"); ?></option>
                        </select>
                    </div>
                </div>
            </td>
        </tr>
    </tbody>
</table>
</form>
<?php
    /* Configuration pagination */
    echo $this->paginationControl($this->paginator, 'Sliding', 'pagination.phtml');
?>
</fieldset>
</div>
<br />
<hr />

<script type="text/javascript">
var itemClick = function(evt) {
    /* Produce click on link. */
    var line = $(this.parentNode);
    $(line.children("td.data")[2]).children("a").click();
}

$("table#rooms-list tbody tr.room td.data").each(function(i)
{
    /* handle click on item */
    this.addEventListener("click", itemClick, false);
});
</script>
