<h2><?php echo BackLib_Lang::tr("txt_h2_list_tables"); ?></h2>
<p><?php echo BackLib_Lang::tr("txt_p_list_tables"); ?></p>

<div>
    <?php
     echo $this->filterForm($this->filter, array(
        "title" => array("label" => BackLib_Lang::tr("l_filter_title"))
     ));
    ?>
<fieldset>
<legend><?php echo BackLib_Lang::tr("legend_listresults"); ?></legend>
<ul style="margin: 0px;">
    <li><a href="<?php echo $this->baseUrl(); ?>/manage/bookings/add-table"><?php echo BackLib_Lang::tr("action_addtable"); ?></a></li>
</ul>
    <?php
    /* Configuration pagination */
    echo $this->paginationControl($this->paginator, 'Sliding', 'pagination.phtml');
    ?>
<form method="post" action="<?php echo $this->baseUrl(); ?>/manage/bookings/list-tables-action-grabber">
<table id="tables-list" cellpadding="3px" cellspacing="0px">
    <thead>
        <th>&nbsp;</th>
        <th><span><?php echo BackLib_Lang::tr("th_id"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_table_room"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_table_title"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_table_count_places"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("th_date_created"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("th_date_updated"); ?></span></th>
    </thead>
    <tbody>
<?php
        foreach($this->paginator as $record)
        {
            $link = $this->baseUrl() . "/manage/bookings/modify-table/tid/" . $record->id_table;
            echo "<tr class='data-row table'>";
            echo "<td rel='table-id'><input type='checkbox' name='tables[]' value='{$record->id_table}' /></td>";
            echo "<td class='tid txtRight data'><span>{$record->id_table}</span></td>";
            echo "<td class='txtCenter data'><span>{$record->getRoom()->getTitle()}</span></td>";
            echo "<td class='txtCenter data' rel='title'><a href='{$link}' class='ajax-link'><span class='bold'>{$record->getTitle()}</span></a></td>";
            echo "<td class='txtCenter data'><span>{$record->count_places}</span></td>";
            echo "<td class='txtCenter data'><span>" . date("d-m-Y H:i", strtotime($record->date_created)) . "</span></td>";
            echo "<td class='txtCenter data'><span>" . (null === $record->date_updated ? "N/A" : date("d-m-Y H:i", strtotime($record->date_updated)))
                    . "</span></td>";
            echo "</tr>";
        }

?>
        <tr class="actions">
            <td colspan="7">
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

$("table#tables-list tbody tr.table td.data").each(function(i)
{
    /* handle click on item */
    this.addEventListener("click", itemClick, false);
});
</script>
