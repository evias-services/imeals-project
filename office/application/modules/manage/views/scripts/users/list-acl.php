<h2><?php echo BackLib_Lang::tr("txt_h2_list_acl"); ?></h2>
<p><?php echo BackLib_Lang::tr("txt_p_list_acl"); ?></p>

<div>
    <?php
    echo $this->filterForm($this->filter, array(
        "id_acl_role"   => array("type" => "select", "options" => $this->acl_roles, "label" => BackLib_Lang::tr("l_filter_role")),
        "id_acl_action" => array("type" => "select", "options" => $this->acl_actions, "label" => BackLib_Lang::tr("l_filter_accessright")),
        "module"      => array("label" => BackLib_Lang::tr("l_filter_module")),
        "controller"  => array("label" => BackLib_Lang::tr("l_filter_controller")),
        "action"      => array("label" => BackLib_Lang::tr("l_filter_action"))
    ));
    ?>
<fieldset>
<legend><?php echo BackLib_Lang::tr("legend_listresults"); ?></legend>
<ul style="margin: 0px;">
    <li><a href="<?php echo $this->baseUrl(); ?>/manage/users/add-acl"><?php echo BackLib_Lang::tr("action_addacl"); ?></a></li>
</ul>
    <?php
    echo $this->paginationControl($this->paginator, 'Sliding', 'pagination.phtml');
    ?>
<form method="post" action="<?php echo $this->baseUrl(); ?>/manage/users/acls-action-grabber">
<table id="acls-list" cellpadding="3px" cellspacing="0px">
    <thead>
        <th>&nbsp;</th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_acl_id"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_acl_resource"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_acl_role"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_acl_right"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_acl_date_created"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_acl_date_updated"); ?></span></th>
    </thead>
    <tbody>
<?php
        foreach($this->paginator as $record)
        {
            $link       = $this->baseUrl() . "/manage/users/modify-acl/aid/" . $record->id_acl_config;
            $resource   = sprintf("/%s/%s/%s", $record->getResource()->module,
                                               $record->getResource()->controller,
                                               $record->getResource()->action);
            $role       = $record->getRoleLabel();
            $right      = strtoupper($record->getRightLabel());

            echo "<tr class='acl'>";
            echo "<td rel='acl-id'><input type='checkbox' name='acls[]' value='{$record->id_acl_config}' /></td>";
            echo "<td class='aid txtRight data'>{$record->id_acl_config}</td>";
            echo "<td class='txtCenter data'><a href='{$link}' class='ajax-link'><span class='bold'>{$resource}</a></span></td>";
            echo "<td class='txtCenter data'><span>{$role}</span></td>";
            echo "<td class='txtCenter data'><span>{$right}</span></td>";
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
                    <span><?php echo BackLib_Lang::tr("txt_tf_acl_select_actions"); ?></span>
                    &nbsp;&nbsp;<select name="selections_action" onchange="submit()">
                            <option value=""><?php echo BackLib_Lang::tr("txt_tf_acl_select_action_opt"); ?></option>
                            <option value="delete"><?php echo BackLib_Lang::tr("txt_tf_acl_action_delete"); ?></option>
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

$("table#acls-list tbody tr.acl td.data").each(function(i)
{
    /* handle click on item */
    this.addEventListener("click", itemClick, false);
});
</script>

