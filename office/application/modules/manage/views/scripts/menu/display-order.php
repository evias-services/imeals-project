<?php
$restaurant = Zend_Registry::get("restaurant");

$items = $this->order->getCart()->getItems(true);
$cnt   = $this->order->getCart()->getQuantities();

if (! empty($items)) :
    $totalPrice = 0;
?>
    <table class="active-order" cellpadding='0' cellspacing='3px' border='0px'>
        <tbody>
            <tr>
                <td colspan="3" align="center"><span class="upper-label"><?php echo $restaurant->getTitle(); ?></span></td> 
            </tr>
            <tr>
                <td colspan="3" align="center" style="line-height:10px;"><span class="small"><?php echo $restaurant->getLegalAddress(); ?></span></td> 
            </tr>
            <tr><td colspan="3">&nbsp;</td></tr>
            <tr>
                <td colspan="3" align="center">
                    <span class="upper-label bold underline">
                        <?php $norm_id = str_pad((string)$this->order->id_order, 4, "0", STR_PAD_LEFT); ?>
                        <?php echo BackLib_Lang::tr("order_num_title"); ?>&nbsp;#<?php echo $norm_id; ?>
                    </span>
                </td>
            </tr>

            <?php
            foreach ($items as $item) :
                $quantity = $cnt[$item->id_item];
                $title    = $item->getTitle();
                $price    = $item->getPrice();

                $by_meal_idx = AppLib_Model_ItemCustomization::loadByItemAndCart($item->id_item, $this->order->getCart()->id_cart);

                $totalPrice += $cnt[$item->id_item] * $item->getPrice();
            ?>

            <tr>
                <td colspan="3" align="left"><span class="upper-label bold"><?php echo "#{$item->meal_menu_number} {$item->getTitle()}"; ?></span></td>
            </tr>
            <!-- print custom meal data -->
            <?php
            if (count($by_meal_idx)) { ?>
               <?php
               $indexes = array_keys($by_meal_idx);
               $by_op   = array_values($by_meal_idx);

               foreach ($indexes as $ix => $meal_index) :
               ?>

            <tr>
                <td colspan="2" align="left"><span class="upper-label"><?php echo "1x"; ?></span></td>
                <td align="right">
                    <?php 
                    if (! empty($by_op[$ix]['+'])) {
                        foreach ($by_op[$ix]['+'] as $wish)
                            $price += $wish->getCustom()->getPrice();
                    } ?>
                    <span class="upper-label bold"><?php echo "$price,-&nbsp;&euro;"; ?></span>
                </td>
            </tr>

            <tr>
                <td colspan="3" align="center" style="line-height:10px;">
                    <ul>
                    <?php
                   foreach ($by_op[$ix] as $op => $wishes) :
                       foreach ($wishes as $wish):
                           switch($op) {
                           default:
                           case '+':
                               $totalPrice += $wish->getCustom()->getPrice();
                               $custom = sprintf("%s %s(%s,-&euro;)", $op,
                                            $wish->getCustom()->getTitle(),
                                            $wish->getCustom()->getPrice());
                               break;
                           case '-':
                               $custom = sprintf("%s %s", $op,
                                            $wish->getCustom()->getTitle());

                           }
                   ?>
                        <li><span class="smalli italic"><?php echo $custom; ?></span></li> 
                   <?php
                       endforeach;
                   endforeach;?>
                   </ul>
               </td>
            </tr>
            <!-- end custom meal data -->
           <?php
               endforeach; 
           }
           else {?>

            <tr>
                <td colspan="2" align="left"><span class="upper-label"><?php echo "{$quantity}x"; ?></span></td>
                <td align="right"><span class="upper-label bold"><?php echo "$price,-&nbsp;&euro;"; ?></span></td>
            </tr>

           <?php
           } 
           endforeach; ?>
            <tr><td colspan="3"><hr /></td></tr>
            <tr><td colspan="2"><span class="upper-label bold">total:</span><td align="right"><span class="upper-label bold"><?php echo "$totalPrice,-&nbsp;&euro;"; ?></span></td></td>
            <tr><td colspan="3" align="center" style="line-height:12px;">
                <span class="upper-label bold underline">
                    <?php $norm_id = str_pad((string)$this->order->id_order, 4, "0", STR_PAD_LEFT); ?>
                    <?php echo BackLib_Lang::tr("delivery_num_title"); ?>&nbsp;#<?php echo $norm_id; ?>
                </span>
            </td><tr>
            <tr><td colspan="3" align="center">
                <span class="small">
                    <?php 
                    echo $this->order->firstname . " " . $this->order->lastname . "<br />";
                    echo $this->order->address . "<br />";
                    echo $this->order->zipcode . " " . $this->order->city . "<br />";
                    echo "Tel.:" . $this->order->phone . "<br />";
                    if (!empty($this->order->comments))
                        echo BackLib_Lang::tr("order_label_comments") . $this->order->comments; 
                    ?>
                </span>
            </td></tr>
            <tr><td colspan="3"><hr /></td></tr>
            <tr><td colspan="3" align="left">
                <span class="small"><?php echo date("d.m.Y H:i", strtotime($this->order->date_created));  ?></span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="upper-label">WEB ORDER</span>
            </td></tr>
            <tr><td colspan="3">&nbsp;</td></tr>
        </tbody> 
    </table>

<?php
endif;?>
