<?php echo $this->doctype(); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="<?php echo BackLib_Lang::getCurrentLang(); ?>">
	<head>
        <link rel="icon"
              type="image/vnd.microsoft.icon"
              href="<?php echo $this->baseUrl() . "/favicon.ico"; ?>" />
		<?php echo $this->headMeta(); ?>
		<?php echo $this->headTitle(); ?>
        <link rel="stylesheet"
              media="screen,projection"
              type="text/css" href="<?php echo $this->baseUrl(); ?>/styles/main.css" /> <!-- MAIN STYLE SHEET -->
        <link rel="stylesheet"
              media="screen,projection"
              type="text/css" href="<?php echo $this->baseUrl(); ?>/styles/2col.css" title="2col" /> <!-- DEFAULT: 2 COLUMNS -->
        <link rel="alternate stylesheet" media="screen,projection" type="text/css" href="<?php echo $this->baseUrl(); ?>/styles/1col.css" title="1col" /> <!-- ALTERNATE: 1 COLUMN -->
        <link rel="stylesheet" href="<?php echo $this->baseUrl(); ?>/js/include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
        <!--[if lte IE 6]><link rel="stylesheet" media="screen,projection" type="text/css" href="<?php echo $this->baseUrl(); ?>/styles/main-ie6.css" /><![endif]--> <!-- MSIE6 -->
        <link rel="stylesheet"
              media="screen,projection"
              type="text/css" href="<?php echo $this->baseUrl(); ?>/styles/styles.css" /> <!-- GRAPHIC THEME -->

        <link rel="stylesheet"
              media="screen,projection"
              type="text/css" href="<?php echo $this->baseUrl(); ?>/styles/timepicker.css" /> <!-- GRAPHIC THEME -->

        <link rel="stylesheet"
              media="screen,projection"
              type="text/css" href="<?php echo $this->baseUrl(); ?>/styles/nyromodal.css" />

        <script type="text/javascript" src="<?php echo $this->baseUrl(); ?>/js/jquery.js"></script>
        <script type="text/javascript" src="<?php echo $this->baseUrl(); ?>/js/jquery-ui.js"></script>
        <script type="text/javascript" src="<?php echo $this->baseUrl(); ?>/js/switcher.js"></script>
        <script type="text/javascript" src="<?php echo $this->baseUrl(); ?>/js/toggle.js"></script>

        <!-- jQuery plugins and extensions -->
        <script type="text/javascript" src="<?php echo $this->baseUrl(); ?>/js/timepicker.js"></script>
        <script type="text/javascript" src="<?php echo $this->baseUrl(); ?>/js/nyromodal.js"></script>
        <script type="text/javascript" src="<?php echo $this->baseUrl(); ?>/js/e-ui.js"></script>
        <script type="text/javascript" src="<?php echo $this->baseUrl(); ?>/js/e-restaurant.js"></script>
        <script type="text/javascript" src="<?php echo $this->baseUrl(); ?>/js/e-menueditor.js"></script>
        <script type="text/javascript" src="<?php echo $this->baseUrl(); ?>/js/e-roomorganisator.js"></script>
        <script type="text/javascript">
        $(document).ready(function(){
            /* initializes tooltips */
            $(document).eRestaurant();

            var _widget_roomorg    = $("#room-organisator");
            var _widget_menueditor = $("#menu-editor");

            if (_widget_roomorg.length)
                eVias.eRoomOrganisator.initialize();

            if (_widget_menueditor.length)
                eVias.eMenuEditor.initialize();
        });
        </script>
	</head>
	<body>
        <div id="main">
            <div id="tray" class="box">
                <div class="f-left box">
                    <?php echo $this->restaurantSelector(); ?>
                </div>
                <p class="f-right">
                    <?php
                    if (Zend_Auth::getInstance()->hasIdentity()) :
                        $identity = Zend_Auth::getInstance()->getIdentity();
                        $realname = $identity->realname;
                        $username = $identity->login;
                        $email    = $identity->email;

                        $acl = new BackLib_AclCheck(array(
                            'identity'   => $identity,
                            'module'     => "manage",
                            'controller' => "users",
                            'action'     => "modify-user"));
                        $link = "#";
                        if ($acl->checkPermission())
                            $link     = $this->baseUrl() . "/manage/users/modify-user/uid/" . $identity->id_e_user;
                    ?>
                    User: <strong><a href="<?php echo $link; ?>" class="ajax-link"><?php echo sprintf("%s (%s : %s)", $realname, $username, $email); ?></a></strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong><a href="<?php echo $this->baseUrl(); ?>/default/login/logout" id="logout">Log out</a></strong>
                    <?php
                    else: ?>
                    Currently not logged in.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong><a href="<?php echo $this->baseUrl(); ?>/default/login/login" id="logout">Log in</a></strong>
                    <?php
                    endif; ?>
                </p>
            </div>

            <hr class="noscreen" />

            <div id="menu" class="box">
                    <?php
                    try {
                        $frontUrl = Zend_Registry::get("restaurant")->getSetting("domain_name");
                    ?>
                <ul class="box f-right">
                    <li><a target="_blank" href="<?php echo "http://" . $frontUrl; ?>"><span><strong>Visit site&raquo;</strong></span></a></li>
                </ul>
                    <?php
                    }
                    catch (Exception $e) {
                    }
                    ?>
                <?php echo $this->applicationMenu("top"); ?>
            </div>

            <hr class="noscreen" />

            <div id="cols" class="box">
                <div id="aside" class="box">
                    <div class="padding box">
                        <p id="logo">
                            <a href="<?php echo $this->baseUrl(); ?>"><img src="<?php echo $this->baseUrl(); ?>/images/e-restaurant.png" alt="e-restaurant logo" title="Visit Site" /></a>
                        </p>
                        <form action="#" method="get" id="search">
                        <fieldset>
                            <legend>Search</legend>
                            <p><input type="text" size="17" name="" class="input-text" />&nbsp;<input type="submit" value="OK" class="input-submit-02" /><br />
                        </fieldset>
                        </form>
                    </div>
                    <?php echo $this->applicationMenu("left"); ?>
                </div> <!-- /left-column -->

                <hr class="noscreen" />

                <div class="content box">
                    <?php
                    $errorsHtml = "";
                    if (! empty($this->system_errors) || ! empty($this->errors)) {

                        if (! empty($this->system_errors))
                            $errorsHtml .= '<ul><li><p>' . implode('</p></li><li><p>', $this->system_errors) . '</p></li></ul>';

                        if (! empty($this->errors))
                            $errorsHtml .= '<ul><li><span>' . implode('</span></li><li><span>', $this->errors) . '</span></li></ul>';

                        echo '<div class="msg error">' . $errorsHtml . '</div>';
                    }

                    if (! empty($this->messages)) {
                        $messagesHtml = '<ul><li><p>' . implode('</p></li><li><p>', $this->messages) . '</p></li></ul>';

                        echo '<div class="msg info">' . $messagesHtml . '</div>';
                    }
                    ?>

                    <?php echo $this->layout()->content; ?>
                </div>

            </div>

            <hr class="noscreen" />

            <div id="footer" class="box">
                <p class="f-left">Copyright &copy; 2013 <a href="http://www.evias.be">eVias Service</a>, All Rights Reserved &reg;&nbsp;&nbsp;-&nbsp;You can contact us <a href="mailto:service@evias.be">here</a> at any time.</p>
                <p class="f-right"><a href="http://www.adminizio.com/">Webdesign</a></p>
            </div>

        </div> <!-- end main -->

    <script type="text/javascript">

    var updateOrdersLinks = function() {
        $.ajax({
            url : '<?php echo $this->baseUrl() . "/manage/orders/fetch-orders"; ?>',
            method: 'get',
            async: false,
            success : function(data, textStatus, xhr) {

                var unseen = "";
                if (data != "0")
                    unseen = " (" + data + ")";

                $("li[rel='list-orders']").each(function(elm) {
                    /* Update list-orders links to display a
                     * count of unseen orders next to the
                     * label. */

                    var oldHtml = $($(this).children("a")[0]).html();
                    var newHtml = "";
                    var word    = "<?php echo BackLib_Lang::tr("txt_link_orders"); ?>";
                    if (oldHtml.indexOf("<span>") != -1)
                        newHtml = "<span>" + word + unseen + "</span>";
                    else
                        newHtml = word + unseen;

                    /* set new HTML (count unseen orders update) */
                    $($(this).children("a")[0]).html(newHtml);
                });
            }
        });
    };

/******** TIMEOUT CONFIGURATION **/

<?php
if (Zend_Auth::getInstance()->hasIdentity()) : ?>
    setInterval(function() {

        updateOrdersLinks();
        return false;
    }, 15000);

    (function() {

        updateOrdersLinks();

    })();
<?php
endif; ?>
    </script>
    </body>
</html>
