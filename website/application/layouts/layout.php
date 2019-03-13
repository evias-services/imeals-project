 <?php
 /* @see FrontLib_Controller_Action::initMenu */
 list($liHomeCls,
     $liMenuCls,
     $liPromotionsCls,
     $liNewsCls,
     $liContactCls) = $this->navigation_classes;
 ?>
 <?php echo $this->doctype(); ?>
 <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="<?php echo FrontLib_Lang::getCurrentLang(); ?>">
 <head>
     <link rel="icon"
           type="image/vnd.microsoft.icon"
           href="<?php echo $this->baseUrl() . "/favicon.ico"; ?>" />
     <?php echo $this->headTitle(); ?>
     <?php echo $this->headMeta(); ?>
     <link href="<?php echo $this->baseUrl() . "/styles/styles.css"; ?>"
           rel="stylesheet"
           type="text/css"
           media="screen" />

     <script type="text/javascript" src="<?php echo $this->baseUrl(); ?>/js/jquery.js"></script>
     <script type="text/javascript" src="<?php echo $this->baseUrl(); ?>/js/jquery-ui.js"></script>
     <script type="text/javascript" src="<?php echo $this->baseUrl(); ?>/js/timepicker.js"></script>
     <script type="text/javascript" src="<?php echo $this->baseUrl(); ?>/js/nyromodal.js"></script>
     <script type="text/javascript" src="<?php echo $this->baseUrl() . '/js/e-ui.js'; ?>"></script>

     <script type="text/javascript">
         $(document).ready(function(e) {
             eVias.eUI.initialize();

             /* display cart on every request! */
             <?php
                 $requestURI = $_SERVER["REQUEST_URI"];
                 $baseUrl    = $this->baseUrl();
             ?>
             var uriCart = '<?php echo $baseUrl; ?>/restaurant/orders/print-cart?ref=' + escape("<?php echo $requestURI; ?>");
             $.ajax(uriCart, {
                 type: 'GET',
                 async: true,
                 success: function( data, tstatus, xhr)
                 {
                     $("#cart").html(data);
                 }
             });
         });
     </script>

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
     <div class="hidden"><h1>eRestaurant - Restaurants in the Cloud</h1><p>Online restaurant delivery and booking application. Order meals online with your browser or smartphone or book a table in a restaurant.</p></div>

     <div id="top-bar">
         <div id="logo">&nbsp;</div>

         <div id="step-one-wrapper">
             <div id="step-one">
                 <ul id="steps">
                     <?php
                        $activeStep = 1;

                        if (isset($this->cart))
                            $activeStep = 2;

                        $relSearch = $activeStep == 1 ? " rel='active'" : "";
                        $relOrder  = $activeStep == 2 ? " rel='active'" : "";
                        $relDeliver= $activeStep == 3 ? " rel='active'" : "";
                     ?>
                     <li class="search-icon"><a tabindex="2" href="<?php echo $this->baseUrl(); ?>/" title="<?php echo FrontLib_Lang::tr("tooltip_searchbutton"); ?>" class="tooltiped"><img<?php echo $relSearch; ?> src="<?php echo $this->baseUrl(); ?>/images/search.png" alt="Search for restaurant or food" /><span><?php echo FrontLib_Lang::tr("button_search"); ?></span></a></li>
                     <li class="order-icon"><a tabindex="3" href="<?php echo $this->baseUrl(); ?>/restaurant/menu/print" class="tooltiped" title="<?php echo FrontLib_Lang::tr("tooltip_orderbutton"); ?>"><img<?php echo $relOrder; ?> src="<?php echo $this->baseUrl(); ?>/images/order.png" alt="Fill your cart for the order" /><span><?php echo FrontLib_Lang::tr("button_order"); ?></span></a></li>
                     <li><a tabindex="4" href="<?php echo $this->baseUrl(); ?>/restaurant/orders/send-cart" class="tooltiped" title="<?php echo FrontLib_Lang::tr("tooltip_deliverbutton"); ?>"><img<?php echo $relDeliver; ?> src="<?php echo $this->baseUrl(); ?>/images/deliver.png" alt="Your food is delivered" /><span><?php echo FrontLib_Lang::tr("button_deliver"); ?></span></a></li>
                 </ul>
             </div>

             <div id="address-input">
                 <div id="address-input-left"></div>

                 <div id="address-input-middle">
                     <form method="get" action="<?php echo $this->baseUrl(); ?>/restaurant/menu/print">
                         <input type="hidden"
                                id="selected_restaurant"
                                name="rid"
                                value="" />
                     </form>
                     <input type="text"
                            name="term"
                            id="address-real-input"
                            value="<?php echo FrontLib_Lang::tr("addr_input_example"); ?>"
                            onclick="this.value = ''"
                            onblur="if (!this.value.length) this.value = '<?php echo FrontLib_Lang::tr("addr_input_example"); ?>';"
                            class="ajax-search-field"
                            ajax-url="<?php echo $this->baseUrl(); ?>/graph/search"
                         tabindex="1" />
                     <img id="address-icon" src="<?php echo $this->baseUrl(); ?>/images/address-icon.png" alt="Input Your Address" />
                 </div>

                 <div id="address-input-right"></div>
             </div>
         </div>
         <div class="clear"></div>
     </div>

     <div id="content">

         <div id="right-column">
             <div id="custom-meal"></div>
             <div id="cart"></div>
         </div>
         <div id="left-column">

             <?php
             if (! empty($this->messages)) {
                 $messagesHtml = 1 == count($this->messages) ? $this->messages[0] : $this->messages;
                 if (is_array($messageHtml))
                     $messagesHtml = '<ul><li>' . implode('</li><li>', $this->messages) . '</li></ul>';
                 echo '<div class="msg"><p>' . $messagesHtml . '</p></div>';
             }
             if (! empty($this->errors)) {
                 $errorsHtml = 1 == count($this->errors) ? '<ul><li class="error-line">' . $this->errors[0] . '</li></ul>' : $this->errors;
                 if (is_array($errorsHtml))
                     $errorsHtml = '<ul><li class="error-line">' . implode('</li><li class="error-line">', $this->errors) . '</li></ul>';
                 echo '<div class="error">' . $errorsHtml . '</div>';
             }
             ?>
             <?php echo $this->layout()->content; ?>

             <div class="clear"></div>

         </div>

         <div class="clear"></div>

         <div id="footer">
             <p><span>COPYRIGHT &copy;2013 <a href="mailto:saive.gregory@gmail.com">Gr&eacute;gory Saive</a>, ALL RIGHTS RESERVED.</span></p>
         </div>

     </div>

</body>
</html>
