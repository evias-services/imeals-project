<?php
function isEdit($acl)
{
    return null !== $acl && is_object($acl);
}

$currentID      = isEdit($this->acl) ? $this->acl->getPK() : "";
$currentRes     = isEdit($this->acl) ? $this->acl->id_acl_resource : "";
$currentRole    = isEdit($this->acl) ? $this->acl->id_acl_role : "";
$currentAction  = isEdit($this->acl) ? $this->acl->id_acl_action : "";
$currentAllowed = isEdit($this->acl) ? $this->acl->is_allowed : "";
?>
<div class="content" style="margin: 0px;">
    <h2><?php echo BackLib_Lang::tr("txt_h2_acls_addacl"); ?></h2>
    <?php echo BackLib_Lang::tr("txt_p_acls_addacl"); ?>
    <br />

    <fieldset class="user-form">
        <legend><?php echo BackLib_Lang::tr("txt_form_acl_legend"); ?></legend>

    <form method="post" action="<?php echo $this->baseUrl(); ?>/manage/users/add-acl">

        <label for="acl[id_acl_resource]"><?php echo BackLib_Lang::tr("txt_form_acl_resource"); ?></label>
        <select name="acl[id_acl_resource]" id="acl_resource">
            <option value=""><?php echo BackLib_Lang::tr("opt_filter_select"); ?></option>
            <?php foreach ($this->resources as $rid => $resource) {
                    $selected   = $currentRes == $rid ? " selected='selected'" : "";
                    echo "<option{$selected} value='{$rid}'>{$resource}</option>"; } ?>
        </select>

        <div class="clear"></div><br />
        <label for="acl[id_acl_role]"><?php echo BackLib_Lang::tr("txt_form_acl_role"); ?></label>
        <select name="acl[id_acl_role]" id="acl_role">
            <option value=""><?php echo BackLib_Lang::tr("opt_filter_select"); ?></option>
            <?php foreach ($this->roles as $rid => $role) {
                    $selected   = $currentRole == $rid ? " selected='selected'" : "";
                    echo "<option{$selected} value='{$rid}'>{$role}</option>"; } ?>
        </select>

        <div class="clear"></div><br />
        <label for="acl[id_acl_action]"><?php echo BackLib_Lang::tr("txt_form_acl_action"); ?></label>
        <select name="acl[id_acl_action]" id="acl_action">
            <option value=""><?php echo BackLib_Lang::tr("opt_filter_select"); ?></option>
            <?php foreach ($this->actions as $aid => $action) {
                    $selected   = $currentAction == $aid ? " selected='selected'" : "";
                    echo "<option{$selected} value='{$aid}'>{$action}</option>"; } ?>
        </select>

        <div class="clear"></div><br />
        <label for="acl[is_allowed]"><?php echo BackLib_Lang::tr("txt_form_acl_is_allowed"); ?></label>
        <select name="acl[is_allowed]" id="acl_is_allowed">
            <option value=""><?php echo BackLib_Lang::tr("opt_filter_select"); ?></option>
            <?php
                $selectedAllow = $currentAllowed ? " selected='selected'" : "";
                $selectedDeny  = !$currentAllowed ? " selected='selected'" : "";
            ?>
            <option<?php echo $selectedAllow; ?> value='1'><?php echo BackLib_Lang::tr("txt_form_acl_allow"); ?></option>
            <option<?php echo $selectedDeny; ?> value='0'><?php echo BackLib_Lang::tr("txt_form_acl_deny"); ?></option>
        </select>

        <div class="clear"></div><br />
        <input type="hidden" id="acl_id" name="acl[id_acl_config]" value="<?php echo $currentID; ?>" />
        <input type="submit" class="button" name="acl[process]" value="<?php echo BackLib_Lang::tr("form_submit"); ?>" />
        <a class="nyroModalClose" href="#"><?php echo BackLib_Lang::tr("form_cancel"); ?></a>
    </form>
    </fieldset>
</div>
