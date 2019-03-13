    <thead>
        <th>&nbsp;</th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_order_id"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_order_restaurant_title"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_order_customer"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_order_date_created"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_order_date_updated"); ?></span></th>
    </thead>
    <tbody>
<?php
        $restaurant = Zend_Registry::get("restaurant");
        foreach($this->paginator as $record)
        {
            $customer = sprintf("%s (%s): %s %s, %s",
                                $record->realname,
                                $record->phone,
                                $record->address,
                                $record->zipcode,
                                $record->city);

            $unseenStyle = " style='background: #009c10;'";
            $unseenLink  = " style='color: #000'";
            $linkStyle   = $record->isSeenBy($restaurant->id_restaurant) ? "" : $unseenLink;
            $lineStyle   = $record->isSeenBy($restaurant->id_restaurant) ? "" : $unseenStyle;
            $lineRel     = " rel='" . ($record->isSeenBy($restaurant->id_restaurant) ? "seen" : "unseen") . "'";
            $link        = $this->baseUrl() . "/manage/orders/set-seen/oid/" . $record->id_order;

            echo "<tr class='order'{$lineStyle}{$lineRel}>";
            echo "<td><input type='checkbox' name='orders[]' value='{$record->id_order}' /></td>";
            echo "<td class='oid txtLeft data'>{$record->id_order}</td>";
            echo "<td class='txtCenter data'><strong>{$restaurant->title}</strong></td>";
            echo "<td class='txtLeft data'><a onclick='return false;' href='{$link}'{$linkStyle} class='ajax-link'>$customer</a></td>";
            echo "<td class='txtCenter data'><span>" . date("d-m-Y H:i", strtotime($record->date_created)) . "</span></td>";
            echo "<td class='txtCenter data'><span>" . (null === $record->date_updated ? "N/A" : date("d-m-Y H:i", strtotime($record->date_updated)))
                    . "</span></td>";
            echo "</tr>";
        }

?>
        <tr class="actions">
            <td colspan="6">
                <div>
                    <div>
                    <span><?php echo BackLib_Lang::tr("txt_tf_category_select_actions"); ?></span>
                    &nbsp;&nbsp;<select name="selections_action" onchange="submit()">
                            <option value=""><?php echo BackLib_Lang::tr("txt_tf_category_select_action_opt"); ?></option>
                            <option value="delete"><?php echo BackLib_Lang::tr("txt_tf_category_action_delete"); ?></option>
                        </select>
                    </div>
                </div>
            </td>
        </tr>

    </tbody>
