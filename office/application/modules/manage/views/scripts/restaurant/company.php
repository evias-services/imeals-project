<?php
$company = AppLib_Model_Company::getInstance();
?>
<h2><?php echo BackLib_Lang::tr("txt_h2_company_edit"); ?></h2>
<?php echo BackLib_Lang::tr("txt_p_company_edit"); ?>
<br />

<fieldset>
<legend><?php echo BackLib_Lang::tr("txt_form_company_legend"); ?></legend>
<form method="post" action="<?php echo $this->baseUrl(); ?>/manage/restaurant/company">
    <label for="company[title]"><?php echo BackLib_Lang::tr("form_label_company_title"); ?></label>
    <input type="text" name="company[title]" value="<?php echo $company->title; ?>" />
    <div class="clear"></div> 

    <label for="company[description]"><?php echo BackLib_Lang::tr("form_label_company_description"); ?></label>
    <input type="text" name="company[description]" value="<?php echo $company->description; ?>" />
    <div class="clear"></div> 

    <label for="company[address]"><?php echo BackLib_Lang::tr("form_label_company_address"); ?></label>
    <input type="text" name="company[address]" value="<?php echo $company->address; ?>" />
    <div class="clear"></div> 

    <label for="company[zipcode]"><?php echo BackLib_Lang::tr("form_label_company_city_pair"); ?></label>
    <input type="text" class="very-short" name="company[zipcode]" value="<?php echo $company->zipcode; ?>" />
    <input type="text" name="company[city]" value="<?php echo $company->city; ?>" />
    <div class="clear"></div> 

    <label for="company[country]"><?php echo BackLib_Lang::tr("form_label_company_country"); ?></label>
    <select name="company[country]">
        <?php $selected = $company->country; ?>
        <option value="b"<?php echo ($selected == "b" ? 'selected="selected"' : ""); ?>>
            <?php echo BackLib_Lang::tr("opt_country_belgium"); ?></option>
        <option value="us"<?php echo ($selected == "us" ? 'selected="selected"' : ""); ?>>
            <?php echo BackLib_Lang::tr("opt_country_usa"); ?></option>
        <option value="d"<?php echo ($selected == "d" ? 'selected="selected"' : ""); ?>>
            <?php echo BackLib_Lang::tr("opt_country_germany"); ?></option>
        <option value="f"<?php echo ($selected == "f" ? 'selected="selected"' : ""); ?>>
            <?php echo BackLib_Lang::tr("opt_country_france"); ?></option>
    </select>
    <div class="clear"></div> 

    <label for="company[phone]"><?php echo BackLib_Lang::tr("form_label_company_phone"); ?></label>
    <input type="text" name="company[phone]" value="<?php echo $company->phone; ?>" />
    <div class="clear"></div> 

    <label for="company[email]"><?php echo BackLib_Lang::tr("form_label_company_email"); ?></label>
    <input type="text" name="company[email]" value="<?php echo $company->email; ?>" />
    <div class="clear"></div> 

    <label for="company[numtav]"><?php echo BackLib_Lang::tr("form_label_company_numtav"); ?></label>
    <input type="text" name="company[numtav]" value="<?php echo $company->numtav; ?>" />
    <div class="clear"></div> 

    <input type="submit" name="process_edit_company" value="<?php echo BackLib_Lang::tr("form_submit"); ?>" />
</form>
</fieldset>
<hr />

<h2><?php echo BackLib_Lang::tr("txt_h2_list_restaurant"); ?></h2>
<p><?php echo BackLib_Lang::tr("txt_p_list_restaurant"); ?></p>

<div>
    <?php
    /* Configuration pagination */
    echo $this->paginationControl($this->paginator, 'Sliding', 'pagination.phtml');
    ?>
<form method="post" action="<?php echo $this->baseUrl(); ?>/manage/restaurant/company-restaurant-action-grabber">
<table id="restaurant-list" cellpadding="3px" cellspacing="0px">
    <thead>
        <th>&nbsp;</th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_restaurant_id"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_restaurant_title"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_restaurant_zipcode"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_restaurant_city"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_restaurant_address"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_restaurant_date_created"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_restaurant_date_updated"); ?></span></th>
    </thead>
    <tbody>
<?php
        $imageEdit = "<img src='{$this->baseUrl()}/images/16x16_edit.jpg' alt='Edit' />";
        foreach($this->paginator as $record)
        {
            $link = $this->baseUrl() . "/manage/restaurant/modify-restaurant/rid/" . $record->id_restaurant;
            echo "<tr class='restaurant'>";
            echo "<td rel='restaurant-id'><input type='checkbox' name='restaurants[]' value='{$record->id_restaurant}' /></td>";
            echo "<td class='rid txtRight data'>{$record->id_restaurant}</td>";
            echo "<td class='txtCenter data' rel='title'><a href='{$link}' class='nyromodal'><span class='bold'>{$record->title}</span></a></td>";
            echo "<td class='txtCenter data'><span>{$record->zipcode}</span></td>";
            echo "<td class='txtCenter data'><span>{$record->city}</span></td>";
            echo "<td class='txtCenter data'><span>{$record->address}</span></td>";
            echo "<td class='txtCenter data'><span>" . date("d-m-Y H:i", strtotime($record->date_created)) . "</span></td>";
            echo "<td class='txtCenter data'><span>" . (null === $record->date_updated ? "N/A" : date("d-m-Y H:i", strtotime($record->date_updated)))
                    . "</span></td>";
            echo "</tr>";
        }

?>
        <tr class="actions">
            <td colspan="8">
                <div>
                    <div>
                    <span><?php echo BackLib_Lang::tr("txt_tf_restaurant_select_actions"); ?></span>
                    &nbsp;&nbsp;<select name="selections_action" onchange="submit()">
                            <option value=""><?php echo BackLib_Lang::tr("txt_tf_restaurant_select_action_opt"); ?></option>
                            <option value="delete"><?php echo BackLib_Lang::tr("txt_tf_restaurant_action_delete"); ?></option>
                        </select>
                    </div>
                </div>
            </td>
        </tr>
    </tbody>
</table>
</form>
<?php
    /* Configuration pagination */
    echo $this->paginationControl($this->paginator, 'Sliding', 'pagination.phtml');
?>
</div>
<br />
<hr />

<script type="text/javascript">
var itemClick = function(evt) {
    /* Produce click on link. */
    var line = $(this.parentNode);
    $(line.children("td.data")[1]).children("a").click();
}

$("table#restaurant-list tbody tr.restaurant td.data").each(function(i) 
{
    /* handle click on item */
    this.addEventListener("click", itemClick, false);
});
</script>
