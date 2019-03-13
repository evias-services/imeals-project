<h2><?php echo BackLib_Lang::tr("txt_h2_list_items"); ?></h2>
<p><?php echo BackLib_Lang::tr("txt_p_list_items"); ?></p>

<div class="rounded-graybg">
    <?php
    echo $this->filterForm($this->filter, array(
        "title" => array("label" => BackLib_Lang::tr("l_filter_title"))
    ));
    ?>
<fieldset>
<legend><?php echo BackLib_Lang::tr("legend_listresults"); ?></legend>
<ul style="margin: 0px;">
    <li><a href="<?php echo $this->baseUrl(); ?>/manage/menu/add-item"><?php echo BackLib_Lang::tr("action_additem"); ?></a></li>
</ul>

    <?php
    echo $this->paginationControl($this->paginator, 'Sliding', 'pagination.phtml');
    ?>
<form method="post" action="<?php echo $this->baseUrl(); ?>/manage/menu/items-action-grabber">
<table id="meals-list" cellpadding="3px" cellspacing="0px">
    <thead>
        <th>&nbsp;</th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_item_id"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_item_title"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_item_price"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_item_date_created"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_item_date_updated"); ?></span></th>
    </thead>
    <tbody>
<?php
        $imageEdit = "<img src='{$this->baseUrl()}/images/16x16_edit.jpg' alt='Edit' />";
        foreach($this->paginator as $record)
        {
            $link = $this->baseUrl() . "/manage/menu/modify-item/iid/" . $record->id_item;
            echo "<tr class='meal'>";
            echo "<td><input type='checkbox' name='items[]' value='{$record->id_item}' /></td>";
            echo "<td class='iid txtRight data'>{$record->id_item}</td>";
            echo "<td class='txtCenter data'><a href='{$link}' class='ajax-link'><span class='bold'>{$record->title}</span></a></td>";
            echo "<td class='txtCenter data'><span class='bold'>{$record->getPrice()},-&nbsp;&euro;</span></td>";
            echo "<td class='txtCenter data'><span>" . date("d-m-Y H:i", strtotime($record->date_created)) . "</span></td>";
            echo "<td class='txtCenter data'><span>" . (empty($record->date_updated) ? "N/A" : date("d-m-Y H:i", strtotime($record->date_updated)))
                    . "</span></td>";
            echo "</tr>";
        }

?>
        <tr class="actions">
            <td colspan="6">
                <div>
                    <div>
                    <span><?php echo BackLib_Lang::tr("txt_tf_category_select_actions"); ?></span>
                    &nbsp;&nbsp;<select name="selections_action" onchange="submit()">
                            <option value=""><?php echo BackLib_Lang::tr("txt_tf_category_select_action_opt"); ?></option>
                            <option value="delete"><?php echo BackLib_Lang::tr("txt_tf_category_action_delete"); ?></option>
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
    $(line.children("td.data")[1]).children("a").click();
}

$("table#meals-list tbody tr.meal td.data").each(function(i)
{
    /* handle click on item */
    this.addEventListener("click", itemClick, false);
});
</script>

