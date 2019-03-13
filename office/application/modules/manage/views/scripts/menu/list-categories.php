<h2><?php echo BackLib_Lang::tr("txt_h2_list_categories"); ?></h2>
<p><?php echo BackLib_Lang::tr("txt_p_list_categories"); ?></p>

<div>
    <?php
    echo $this->filterForm($this->filter, array(
        /* default filter field type is "text" */
        "title" => array("label" => BackLib_Lang::tr("l_filter_title"))
    ));
    ?>
<fieldset>
<legend><?php echo BackLib_Lang::tr("legend_listresults"); ?></legend>
<ul style="margin: 0px;">
    <li><a href="<?php echo $this->baseUrl(); ?>/manage/menu/add-category"><?php echo BackLib_Lang::tr("action_addcategory"); ?></a></li>
</ul>
    <?php
    echo $this->paginationControl($this->paginator, 'Sliding', 'pagination.phtml');
    ?>
<form method="post" action="<?php echo $this->baseUrl(); ?>/manage/menu/categories-action-grabber">
<table id="categories-list" cellpadding="3px" cellspacing="0px">
    <thead>
        <th>&nbsp;</th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_category_id"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_category_title"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_category_parent"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_category_date_created"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_category_date_updated"); ?></span></th>
    </thead>
    <tbody>
<?php
        $imageEdit = "<img src='{$this->baseUrl()}/images/16x16_edit.jpg' alt='Edit' />";
        foreach($this->paginator as $record)
        {
            $parentName = null === $record->getParent() ? "N/A" : $record->getParent()->title;
            $link       = $this->baseUrl() . "/manage/menu/modify-category/cid/" . $record->id_category;

            echo "<tr class='category'>";
            echo "<td rel='category-id'><input type='checkbox' name='categories[]' value='{$record->id_category}' /></td>";
            echo "<td class='cid txtRight data'>{$record->id_category}</td>";
            echo "<td class='txtCenter data'><a href='{$link}' class='ajax-link'><span class='bold'>{$record->title}</a></span></td>";
            echo "<td class='txtCenter data'><span>$parentName</span></td>";
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

$("table#categories-list tbody tr.category td.data").each(function(i)
{
    /* handle click on item */
    this.addEventListener("click", itemClick, false);
});
</script>

