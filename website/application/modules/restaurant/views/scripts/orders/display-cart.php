<?php
/* disablePages remove entire cart */
$disablePages = array(
    $this->baseUrl() . "/restaurant/orders/confirm-order",
    $this->baseUrl() . "/restaurant/orders/preview-order",);

/* disableSubmitPages removes submit button */
$disableSubmitPages = array(
    $this->baseUrl() . "/restaurant/orders/send-cart",
    $this->baseUrl() . "/restaurant/orders/preview-order",);

if (! in_array($this->requestURI, $disablePages)) {
    echo $this->cart($this->cart, $this->requestURI, array(
            "cart_min_price" => $this->cart_min_price,
            "currency_html"  => $this->cart_current,
            "submit_button"  => ! in_array($this->requestURI, $disableSubmitPages)));
}
?>

<script type="text/javascript">
	$("#cart ul li ul li img").each(function(elm) {
		$(elm).click(function(ev) {
			var item_id = $(elm).attr("rel");

            /* Delete item from cart */
            var uriDel = '<?php echo $this->baseUrl(); ?>/restaurant/orders/del-item-from-cart';
	        $.ajax(uriDel, {
	            type: 'POST',
	            async: false,
                data: "item_id=" + item_id
	        });

            /* Re-Print cart */
            var uriCart = '<?php echo $this->baseUrl(); ?>/restaurant/orders/print-cart?ref=' + escape("<?php echo $_SERVER["REQUEST_URI"]; ?>");
            $.ajax(uriCart, {
                type: 'GET',
                async: true,
                success: function(data, tstatus, xhr)
                {
                    $("#cart").html(data);
                }
            });
		});
	});
</script>
