<h2><?php echo BackLib_Lang::tr("txt_h2_list_users"); ?></h2>
<p><?php echo BackLib_Lang::tr("txt_p_list_users"); ?></p>

<div>
    <?php
    echo $this->filterForm($this->filter, array(
        "login" => array("label" => BackLib_Lang::tr("l_filter_login")),
        "email" => array("label" => BackLib_Lang::tr("l_filter_email"))
    ));
    ?>
<fieldset>
<legend><?php echo BackLib_Lang::tr("legend_listresults"); ?></legend>
<ul style="margin: 0px;">
    <li><a href="<?php echo $this->baseUrl(); ?>/manage/users/add-user"><?php echo BackLib_Lang::tr("action_adduser"); ?></a></li>
</ul>
    <?php
    echo $this->paginationControl($this->paginator, 'Sliding', 'pagination.phtml');
    ?>
<form method="post" action="<?php echo $this->baseUrl(); ?>/manage/users/users-action-grabber">
<table id="users-list" cellpadding="3px" cellspacing="0px">
    <thead>
        <th>&nbsp;</th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_user_id"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_user_realname"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_user_login"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_user_role"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_user_email"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_user_date_created"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_user_date_updated"); ?></span></th>
    </thead>
    <tbody>
<?php
        $imageEdit = "<img src='{$this->baseUrl()}/images/16x16_edit.jpg' alt='Edit' />";
        foreach($this->paginator as $record)
        {
            $link       = $this->baseUrl() . "/manage/users/modify-user/uid/" . $record->id_e_user;

            echo "<tr class='user'>";
            echo "<td rel='user-id'><input type='checkbox' name='users[]' value='{$record->id_e_user}' /></td>";
            echo "<td class='uid txtRight data'>{$record->id_e_user}</td>";
            echo "<td class='txtCenter data'><a href='{$link}' class='ajax-link'><span class='bold'>{$record->realname}</a></span></td>";
            echo "<td class='txtCenter data'><span>{$record->login}</span></td>";
            echo "<td class='txtCenter data'><span>{$record->getRoleLabel()}</span></td>";
            echo "<td class='txtCenter data'><span>{$record->email}</span></td>";
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
                    <span><?php echo BackLib_Lang::tr("txt_tf_user_select_actions"); ?></span>
                    &nbsp;&nbsp;<select name="selections_action" onchange="submit()">
                            <option value=""><?php echo BackLib_Lang::tr("txt_tf_user_select_action_opt"); ?></option>
                            <option value="delete"><?php echo BackLib_Lang::tr("txt_tf_user_action_delete"); ?></option>
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

$("table#users-list tbody tr.user td.data").each(function(i)
{
    /* handle click on item */
    this.addEventListener("click", itemClick, false);
});
</script>

