<h2><?php echo BackLib_Lang::tr("txt_h2_delete_users"); ?></h2>
<?php if (empty($this->users)) : ?>
    <div class="error">,
        <p><?php echo BackLib_Lang::tr("txt_error_delete_no_users"); ?></p>
    </div>
<?php else : ?>
    <p><?php echo BackLib_Lang::tr("txt_p1_delete_users"); ?></p>
    <br />
    <p><?php echo BackLib_Lang::tr("txt_p2_delete_users"); ?></p>
    <br />
    <ul>
    <?php foreach ($this->users as $user) :  ?>
        <li>&quot;<span class="bold"><?php echo $user->realname . " (" . $user->login . ": " . $user->email . ")"; ?></span>&quot;</li>
    <?php endforeach; ?>
    </ul>
    <div class="actions">
    <form action="<?php echo $this->baseUrl(); ?>/manage/users/delete-user" method="post">
        <?php foreach ($this->users as $user) :  ?>
        <input type="hidden" name="users[]" value="<?php echo $user->id_e_user; ?>" />
        <?php endforeach; ?>
        <input type="submit"
               name="is_confirmed"
               value="<?php echo BackLib_Lang::tr("txt_form_submit_delete_user"); ?>" />

        <a href="<?php echo $this->baseUrl(); ?>/manage/users/list-users"><?php echo BackLib_Lang::tr("txt_form_cancel_delete_user"); ?></a>
        <div class="clear"></div>
    </form>
    </div>
<?php endif; ?>
