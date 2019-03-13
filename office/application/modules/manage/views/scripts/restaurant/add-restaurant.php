<?php
function isEdit($res)
{
    return null !== $res && is_object($res);
}

$currentID      = isEdit($this->restaurant) ? $this->restaurant->getPK() : "";
$currentTitle   = isEdit($this->restaurant) ? $this->restaurant->getTitle() : "";
$currentAddr    = isEdit($this->restaurant) ? $this->restaurant->address : "";
$currentZip     = isEdit($this->restaurant) ? $this->restaurant->zipcode : "";
$currentCity    = isEdit($this->restaurant) ? $this->restaurant->city : "";
$currentCountry = isEdit($this->restaurant) ? $this->restaurant->country : "";
$currentPhone   = isEdit($this->restaurant) ? $this->restaurant->phone : "";
$currentEmail   = isEdit($this->restaurant) ? $this->restaurant->email : "";
$currentNumtav  = isEdit($this->restaurant) ? $this->restaurant->numtav : "";
?>
<div class="content" style="margin: 0px;">
    <h2><?php echo BackLib_Lang::tr("txt_h2_restaurant_edit"); ?></h2>
    <?php echo BackLib_Lang::tr("txt_p_restaurant_edit"); ?>
    <br />

    <fieldset>
    <legend><?php echo BackLib_Lang::tr("txt_form_restaurant_legend"); ?></legend>
    <form method="post" action="<?php echo $this->baseUrl(); ?>/manage/restaurant/add-restaurant">

        <label for="restaurant[title]"><?php echo BackLib_Lang::tr("form_label_restaurant_title"); ?></label>
        <input type="text" id="restaurant_title" name="restaurant[title]" value="<?php echo $currentTitle; ?>" />
        <div class="clear"></div>

        <label for="restaurant[address]"><?php echo BackLib_Lang::tr("form_label_restaurant_address"); ?></label>
        <input type="text" id="restaurant_address" name="restaurant[address]" value="<?php echo $currentAddr; ?>" />
        <div class="clear"></div>

        <label for="restaurant[zipcode]"><?php echo BackLib_Lang::tr("form_label_restaurant_city_pair"); ?></label>
        <input type="text" class="very-short" id="restaurant_zipcode" name="restaurant[zipcode]" value="<?php echo $currentZip; ?>" />
        <input type="text" id="restaurant_city" name="restaurant[city]" value="<?php echo $currentCity; ?>" />
        <div class="clear"></div>

        <label for="restaurant[country]"><?php echo BackLib_Lang::tr("form_label_restaurant_country"); ?></label>
        <select name="restaurant[country]" id="restaurant_country">
            <?php $selected = $currentCountry; ?>
            <option id="restaurant_country_b" value="b"<?php echo ($selected == "b" ? 'selected="selected"' : ""); ?>>
                <?php echo BackLib_Lang::tr("opt_country_belgium"); ?></option>
            <option id="restaurant_country_us" value="us"<?php echo ($selected == "us" ? 'selected="selected"' : ""); ?>>
                <?php echo BackLib_Lang::tr("opt_country_usa"); ?></option>
            <option id="restaurant_country_d" value="d"<?php echo ($selected == "d" ? 'selected="selected"' : ""); ?>>
                <?php echo BackLib_Lang::tr("opt_country_germany"); ?></option>
            <option id="restaurant_country_f" value="f"<?php echo ($selected == "f" ? 'selected="selected"' : ""); ?>>
                <?php echo BackLib_Lang::tr("opt_country_france"); ?></option>
        </select>
        <div class="clear"></div>

        <label for="restaurant[phone]"><?php echo BackLib_Lang::tr("form_label_restaurant_phone"); ?></label>
        <input type="text" id="restaurant_phone" name="restaurant[phone]" value="<?php echo $currentPhone; ?>" />
        <div class="clear"></div>

        <label for="restaurant[email]"><?php echo BackLib_Lang::tr("form_label_restaurant_email"); ?></label>
        <input type="text" id="restaurant_email" name="restaurant[email]" value="<?php echo $currentEmail; ?>" />
        <div class="clear"></div>

        <label for="restaurant[numtav]"><?php echo BackLib_Lang::tr("form_label_restaurant_numtav"); ?></label>
        <input type="text" id="restaurant_numtav" name="restaurant[numtav]" value="<?php echo $currentNumtav; ?>" />
        <div class="clear"></div>

        <input type="hidden" id="restaurant_id" name="restaurant[id_restaurant]" value="<?php echo $currentID; ?>" />
        <input type="submit" class="button" name="process_edit_restaurant" value="<?php echo BackLib_Lang::tr("form_submit"); ?>" />
        <a class="nyroModalClose" href="#"><?php echo BackLib_Lang::tr("form_cancel"); ?></a>
    </form>
    </fieldset>
</div>
