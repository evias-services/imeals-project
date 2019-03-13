<h2><?php echo BackLib_Lang::tr("txt_h2_list_restaurant"); ?></h2>
<p><?php echo BackLib_Lang::tr("txt_p_list_restaurant"); ?></p>

<div>
    <?php
     echo $this->filterForm($this->filter, array(
        "title" => array("label" => BackLib_Lang::tr("l_filter_title"))
     ));
    ?>
<fieldset>
<legend><?php echo BackLib_Lang::tr("legend_listresults"); ?></legend>
<ul style="margin: 0px;">
    <li><a href="<?php echo $this->baseUrl(); ?>/manage/restaurant/add-restaurant"><?php echo BackLib_Lang::tr("action_addrestaurant"); ?></a></li>
</ul>
    <?php
    /* Configuration pagination */
    echo $this->paginationControl($this->paginator, 'Sliding', 'pagination.phtml');
    ?>
<form method="post" action="<?php echo $this->baseUrl(); ?>/manage/restaurant/list-restaurant-action-grabber">
<table id="restaurant-list" cellpadding="3px" cellspacing="0px">
    <thead>
        <th>&nbsp;</th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_restaurant_id"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_restaurant_title"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_restaurant_zipcode"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_restaurant_city"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_restaurant_address"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_restaurant_date_created"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_restaurant_date_updated"); ?></span></th>
    </thead>
    <tbody>
<?php
        $imageEdit = "<img src='{$this->baseUrl()}/images/16x16_edit.jpg' alt='Edit' />";
        foreach($this->paginator as $record)
        {
            $link = $this->baseUrl() . "/manage/restaurant/modify-restaurant/rid/" . $record->id_restaurant;
            echo "<tr class='restaurant'>";
            echo "<td rel='restaurant-id'><input type='checkbox' name='restaurants[]' value='{$record->id_restaurant}' /></td>";
            echo "<td class='rid txtRight data'>{$record->id_restaurant}</td>";
            echo "<td class='txtCenter data' rel='title'><a href='{$link}' class='ajax-link'><span class='bold'>{$record->title}</span></a></td>";
            echo "<td class='txtCenter data'><span>{$record->zipcode}</span></td>";
            echo "<td class='txtCenter data'><span>{$record->city}</span></td>";
            echo "<td class='txtCenter data'><span>{$record->address}</span></td>";
            echo "<td class='txtCenter data'><span>" . date("d-m-Y H:i", strtotime($record->date_created)) . "</span></td>";
            echo "<td class='txtCenter data'><span>" . (null === $record->date_updated ? "N/A" : date("d-m-Y H:i", strtotime($record->date_updated)))
                    . "</span></td>";
            echo "</tr>";
        }

?>
        <tr class="actions">
            <td colspan="8">
                <div>
                    <div>
                    <span><?php echo BackLib_Lang::tr("txt_tf_restaurant_select_actions"); ?></span>
                    &nbsp;&nbsp;<select name="selections_action" onchange="submit()">
                            <option value=""><?php echo BackLib_Lang::tr("txt_tf_restaurant_select_action_opt"); ?></option>
                            <option value="delete"><?php echo BackLib_Lang::tr("txt_tf_restaurant_action_delete"); ?></option>
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

$("table#restaurant-list tbody tr.restaurant td.data").each(function(i)
{
    /* handle click on item */
    this.addEventListener("click", itemClick, false);
});
</script>
