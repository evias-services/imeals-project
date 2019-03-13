<fieldset><legend><?php echo BackLib_Lang::tr("h2_login"); ?></legend>
<?php echo BackLib_Lang::tr("p_login"); ?>
<br />

<form action="<?php echo $this->baseUrl(); ?>/default/login/login" method="post">
    <label for="identifier"><?php echo BackLib_Lang::tr("label_form_login_identifier"); ?></label>
    <input type="text" name="identifier" class="short" value="" />
    <br />

    <label for="credential"><?php echo BackLib_Lang::tr("label_form_login_password"); ?></label>
    <input type="password" name="credential" value="" />
    <br />

    <input type="hidden" name="referer" value="<?php echo $this->referer; ?>" />
    <input type="submit" name="process_login" value="Log-In" />
</form>
</fieldset>
