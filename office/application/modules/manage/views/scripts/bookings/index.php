<h2><?php echo BackLib_Lang::tr("txt_h2_list_booking"); ?></h2>
<p><?php echo BackLib_Lang::tr("txt_p_list_booking"); ?></p>

<div>
    <?php
     echo $this->filterForm($this->filter, array(
        "title" => array("label" => BackLib_Lang::tr("l_filter_title"))
     ));
    ?>
<fieldset>
<legend><?php echo BackLib_Lang::tr("legend_listresults"); ?></legend>
<ul style="margin: 0px;">
    <li><a href="<?php echo $this->baseUrl(); ?>/manage/bookings/add-booking"><?php echo BackLib_Lang::tr("action_addbooking"); ?></a></li>
</ul>
    <?php
    /* Configuration pagination */
    echo $this->paginationControl($this->paginator, 'Sliding', 'pagination.phtml');
    ?>
<form method="post" action="<?php echo $this->baseUrl(); ?>/manage/bookings/list-booking-action-grabber">
<table id="booking-list" cellpadding="3px" cellspacing="0px">
    <thead>
        <th>&nbsp;</th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_booking_id"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_booking_type"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_booking_customer"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_booking_count_people"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_booking_booked_object"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_booking_date_book_start"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_booking_date_book_end"); ?></span></th>
    </thead>
    <tbody>
<?php
        foreach($this->paginator as $record)
        {
            $link = $this->baseUrl() . "/manage/bookings/modify-booking/bid/" . $record->id_booking;
            echo "<tr class='data-row booking'>";
            echo "<td rel='booking-id'><input type='checkbox' name='bookings[]' value='{$record->id_booking}' /></td>";
            echo "<td class='bid txtRight data'><span>{$record->id_booking}</span></td>";
            echo "<td class='txtCenter data'><span>{$record->getType()->title}</span></td>";
            echo "<td class='txtCenter data' rel='customer'><a href='{$link}' class='ajax-link'><span class='bold'>{$record->getCustomer()->getTitle()}</span></a></td>";
            echo "<td class='txtCenter data'><span>{$record->count_people}</span></td>";
            echo "<td class='txtCenter data'><span><strong>{$record->getBookedObject()->getTitle(true)}</strong></span></td>";
            echo "<td class='txtCenter data'><span>" . date("d-m-Y H:i", strtotime($record->date_book_start)) . "</span></td>";
            echo "<td class='txtCenter data'><span>" . (null === $record->date_book_end ? "N/A" : date("d-m-Y H:i", strtotime($record->date_book_end)));
            echo "</tr>";
        }

?>
        <tr class="actions">
            <td colspan="9">
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

$("table#booking-list tbody tr.booking td.data").each(function(i)
{
    /* handle click on item */
    this.addEventListener("click", itemClick, false);
});
</script>
