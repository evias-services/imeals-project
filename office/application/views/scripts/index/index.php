<h2><?php echo BackLib_Lang::tr("h2_welcome_screen"); ?></h2>
<br />
<p><?php echo BackLib_Lang::tr("p1_welcome_screen"); ?></p>

<?php
if (! Zend_Auth::getInstance()->hasIdentity()) :
    echo $this->render("login/login.php");
endif; ?>
