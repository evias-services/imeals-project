<h2><?php echo BackLib_Lang::tr("txt_h2_list_menu"); ?></h2>
<p><?php echo BackLib_Lang::tr("txt_p_list_menu"); ?></p>

<div>
    <?php
     echo $this->filterForm($this->filter, array(
        "title" => array("label" => BackLib_Lang::tr("l_filter_title"))
     ));
    ?>
<fieldset>
<legend><?php echo BackLib_Lang::tr("legend_listresults"); ?></legend>
<ul style="margin: 0px;">
    <li><a href="<?php echo $this->baseUrl(); ?>/manage/menu/add-menu"><?php echo BackLib_Lang::tr("action_addmenu"); ?></a></li>
</ul>
    <?php
    /* Configuration pagination */
    echo $this->paginationControl($this->paginator, 'Sliding', 'pagination.phtml');
    ?>
<form method="post" action="<?php echo $this->baseUrl(); ?>/manage/menu/list-menu-action-grabber">
<table id="menu-list" cellpadding="3px" cellspacing="0px">
    <thead>
        <th>&nbsp;</th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_menu_id"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_menu_restaurant_title"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_menu_title"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_menu_date_created"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_menu_date_updated"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_menu_preview"); ?></span></th>
    </thead>
    <tbody>
<?php
        foreach($this->paginator as $record)
        {
            $restaurant   = "<strong>" . $record->getRestaurant()->getTitle() . "</strong>";
            $link_modify  = $this->baseUrl() . "/manage/menu/modify-menu/mid/" . $record->id_menu;
            $link_preview = $this->baseUrl() . "/manage/menu/preview-menu/mid/" . $record->id_menu;

            echo "<tr class='data-row menu'>";
            echo "<td rel='menu-id'><input type='checkbox' name='menus[]' value='{$record->id_menu}' /></td>";
            echo "<td class='mid txtRight data'>{$record->id_menu}</td>";
            echo "<td class='txtCenter data' rel='title'><a href='{$link_modify}' class='ajax-link'><span class='bold'>{$record->title}</span></a></td>";
            echo "<td class='txtCenter data'><span class='bold'>{$restaurant}</span></td>";
            echo "<td class='txtCenter data'><span>" . date("d-m-Y H:i", strtotime($record->date_created)) . "</span></td>";
            echo "<td class='txtCenter data'><span>" . (null === $record->date_updated ? "N/A" : date("d-m-Y H:i", strtotime($record->date_updated)))
                    . "</span></td>";
            echo "<td class='txtCenter' rel='preview'><a href='{$link_preview}' class='ajax-link'><span class='bold'>" . BackLib_Lang::tr("link_preview_menu") . "</span></a></td>";
            echo "</tr>";
        }

?>
        <tr class="actions">
            <td colspan="5">
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
    $(line.children("td.data")[1]).children("a").click();
}

$("table#menu-list tbody tr.menu td.data").each(function(i)
{
    /* handle click on item */
    this.addEventListener("click", itemClick, false);
});
</script>
