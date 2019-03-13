<h2><?php echo BackLib_Lang::tr("h2_denied_screen"); ?></h2>
<br />
<p><?php echo BackLib_Lang::tr("p1_denied_screen"); ?></p>

<?php
if (! Zend_Auth::getInstance()->hasIdentity()):
    echo $this->render("login/login.php");
endif;
?>
