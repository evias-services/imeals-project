<h2><?php echo BackLib_Lang::tr("txt_h2_delete_acls"); ?></h2>
<?php if (empty($this->acls)) : ?>
    <div class="error">,
        <p><?php echo BackLib_Lang::tr("txt_error_delete_no_acls"); ?></p>
    </div>
<?php else : ?>
    <p><?php echo BackLib_Lang::tr("txt_p1_delete_acls"); ?></p>
    <br />
    <p><?php echo BackLib_Lang::tr("txt_p2_delete_acls"); ?></p>
    <br />
    <ul>
    <?php foreach ($this->acls as $acl) :  ?>
        <li>&quot;<span class="bold"><?php echo $acl->getRightLabel() . " access for " . $acl->getRoleLabel() . " on " . $acl->getResource()->toURI(); ?></span>&quot;</li>
    <?php endforeach; ?>
    </ul>
    <div class="actions">
    <form action="<?php echo $this->baseUrl(); ?>/manage/users/delete-acl" method="post">
        <?php foreach ($this->acls as $acl) :  ?>
        <input type="hidden" name="acls[]" value="<?php echo $acl->id_acl_config; ?>" />
        <?php endforeach; ?>
        <input type="submit"
               name="is_confirmed"
               value="<?php echo BackLib_Lang::tr("txt_form_submit_delete_acl"); ?>" />

        <a href="<?php echo $this->baseUrl(); ?>/manage/users/list-acl"><?php echo BackLib_Lang::tr("txt_form_cancel_delete_acl"); ?></a>
        <div class="clear"></div>
    </form>
    </div>
<?php endif; ?>
