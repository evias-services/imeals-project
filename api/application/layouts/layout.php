<?php echo $this->doctype(); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
        <link rel="icon"
              type="image/vnd.microsoft.icon"
              href="<?php echo $this->baseUrl() . "/favicon.ico"; ?>" />
        <title>e-Restaurant.eu Services</title>
        
        <!-- google analytics -->
        <script type="text/javascript">
            var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
            document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
        </script>
        <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push( 
          ['_setAccount', 'UA-38242427-2'], 
          ['_trackPageview'] 
        ); 
        </script>
	</head>
	<body>
       <?php echo $this->layout()->content; ?>
    </body>
</html>
