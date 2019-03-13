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
	</head>
	<body><?php echo $this->layout()->content; ?></body>
</html>
