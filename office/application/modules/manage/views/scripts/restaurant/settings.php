<?php
    function getSetting($field, $settings)
    {
        return isset($settings[$field]) ? $settings[$field] : "";
    }

    $sel_start_mon = getSetting("opening_day_start", $this->settings) == "0" ? " selected='selected'" : "";
    $sel_start_tue = getSetting("opening_day_start", $this->settings) == "1" ? " selected='selected'" : "";
    $sel_start_wed = getSetting("opening_day_start", $this->settings) == "2" ? " selected='selected'" : "";
    $sel_start_thu = getSetting("opening_day_start", $this->settings) == "3" ? " selected='selected'" : "";
    $sel_start_fri = getSetting("opening_day_start", $this->settings) == "4" ? " selected='selected'" : "";
    $sel_start_sat = getSetting("opening_day_start", $this->settings) == "5" ? " selected='selected'" : "";
    $sel_start_sun = getSetting("opening_day_start", $this->settings) == "6" ? " selected='selected'" : "";

    $sel_end_mon = getSetting("opening_day_end", $this->settings) == "0" ? " selected='selected'" : "";
    $sel_end_tue = getSetting("opening_day_end", $this->settings) == "1" ? " selected='selected'" : "";
    $sel_end_wed = getSetting("opening_day_end", $this->settings) == "2" ? " selected='selected'" : "";
    $sel_end_thu = getSetting("opening_day_end", $this->settings) == "3" ? " selected='selected'" : "";
    $sel_end_fri = getSetting("opening_day_end", $this->settings) == "4" ? " selected='selected'" : "";
    $sel_end_sat = getSetting("opening_day_end", $this->settings) == "5" ? " selected='selected'" : "";
    $sel_end_sun = getSetting("opening_day_end", $this->settings) == "6" ? " selected='selected'" : "";

?>
<h2><?php echo BackLib_Lang::tr("txt_h2_settings"); ?></h2>
<?php echo BackLib_Lang::tr("txt_p_settings"); ?>
<br />

<fieldset>
<legend><?php echo BackLib_Lang::tr("txt_form_restaurant_settings_legend"); ?></legend>
<form method="post" action="<?php echo $this->baseUrl(); ?>/manage/restaurant/settings">

    <label for="domain_name"><?php echo BackLib_Lang::tr("form_label_front_domain_name"); ?></label>
    <input  type="text"
            name="settings[domain_name]"
            class="hasTimePicker"
            id="domain_name"
            value="<?php echo getSetting("domain_name", $this->settings); ?>"
            style="width: 150px;" />
    <br />

    <label for="settings[opening_day_start]"><?php echo BackLib_Lang::tr("form_label_front_openingdays"); ?></label>
    <label style="width:50px;" for="opening_day_start"><?php echo BackLib_Lang::tr("txt_from_day"); ?></label>
    <select name="settings[opening_day_start]" id="opening_day_start">
        <option value="0"<?php echo $sel_start_mon; ?>><?php echo BackLib_Lang::tr("opt_day_monday"); ?></option>
        <option value="1"<?php echo $sel_start_tue; ?>><?php echo BackLib_Lang::tr("opt_day_tuesday"); ?></option>
        <option value="2"<?php echo $sel_start_wed; ?>><?php echo BackLib_Lang::tr("opt_day_wednesday"); ?></option>
        <option value="3"<?php echo $sel_start_thu; ?>><?php echo BackLib_Lang::tr("opt_day_thursday"); ?></option>
        <option value="4"<?php echo $sel_start_fri; ?>><?php echo BackLib_Lang::tr("opt_day_friday"); ?></option>
        <option value="5"<?php echo $sel_start_sat; ?>><?php echo BackLib_Lang::tr("opt_day_saturday"); ?></option>
        <option value="6"<?php echo $sel_start_sun; ?>><?php echo BackLib_Lang::tr("opt_day_sunday"); ?></option>
    </select>
    <div class="clear"></div>
    <label>&nbsp;&nbsp;&nbsp;&nbsp;</label>
    <label style="width:50px;" for="opening_day_end"><?php echo BackLib_Lang::tr("txt_to_day"); ?></label>
    <select name="settings[opening_day_end]" id="opening_day_end">
        <option value="0"<?php echo $sel_end_mon; ?>><?php echo BackLib_Lang::tr("opt_day_monday"); ?></option>
        <option value="1"<?php echo $sel_end_tue; ?>><?php echo BackLib_Lang::tr("opt_day_tuesday"); ?></option>
        <option value="2"<?php echo $sel_end_wed; ?>><?php echo BackLib_Lang::tr("opt_day_wednesday"); ?></option>
        <option value="3"<?php echo $sel_end_thu; ?>><?php echo BackLib_Lang::tr("opt_day_thursday"); ?></option>
        <option value="4"<?php echo $sel_end_fri; ?>><?php echo BackLib_Lang::tr("opt_day_friday"); ?></option>
        <option value="5"<?php echo $sel_end_sat; ?>><?php echo BackLib_Lang::tr("opt_day_saturday"); ?></option>
        <option value="6"<?php echo $sel_end_sun; ?>><?php echo BackLib_Lang::tr("opt_day_sunday"); ?></option>
    </select>
    <div class="clear"></div>
    <br />

    <label><?php echo BackLib_Lang::tr("form_label_front_openinghoursam"); ?></label>
    <label style="width:50px;" for="openingam_timepicker_start"><?php echo BackLib_Lang::tr("txt_from_hour"); ?></label>
        <input  type="text"
                name="settings[opening_am_start]"
                class="hasTimePicker timepicker-from"
                id="openingam_timepicker_start"
                rel="openingam_timepicker_end"
                value="<?php echo getSetting("opening_am_start", $this->settings); ?>"
                style="width: 70px;" />
    <label style="width:50px;" for="opening_timepicker_end"><?php echo BackLib_Lang::tr("txt_to_hour"); ?></label>
        <input  type="text"
                class="hasTimePicker timepicker-to"
                id="openingam_timepicker_end"
                rel="openingam_timepicker_start"
                name="settings[opening_am_end]"
                value="<?php echo getSetting("opening_am_end", $this->settings); ?>"
                style="width: 70px;" />
    <div class="clear"></div>

    <label><?php echo BackLib_Lang::tr("form_label_front_openinghourspm"); ?></label>
    <label style="width:50px;" for="openingpm_timepicker_start"><?php echo BackLib_Lang::tr("txt_from_hour"); ?></label>
        <input  type="text"
                name="settings[opening_pm_start]"
                class="hasTimePicker timepicker-from"
                id="openingpm_timepicker_start"
                rel="openingpm_timepicker_end"
                value="<?php echo getSetting("opening_pm_start", $this->settings); ?>"
                style="width: 70px;" />
    <label style="width:50px;" for="openingpm_timepicker_end"><?php echo BackLib_Lang::tr("txt_to_hour"); ?></label>
        <input  type="text"
                class="hasTimePicker timepicker-to"
                id="openingpm_timepicker_end"
                rel="openingpm_timepicker_start"
                name="settings[opening_pm_end]"
                value="<?php echo getSetting("opening_pm_end", $this->settings); ?>"
                style="width: 70px;" />
    <div class="clear"></div>
    <br />

    <!-- delivery -->
    <label><?php echo BackLib_Lang::tr("form_label_front_deliveryhours"); ?></label>
    <label style="width:50px;" for="delivery_timepicker_start"><?php echo BackLib_Lang::tr("txt_from_hour"); ?></label>
        <input  type="text"
                name="settings[delivery_start]"
                class="hasTimePicker timepicker-from"
                id="delivery_timepicker_start"
                rel="delivery_timepicker_end"
                value="<?php echo getSetting("delivery_start", $this->settings); ?>"
                style="width: 70px;" />
    <label style="width:50px;" for="delivery_timepicker_end"><?php echo BackLib_Lang::tr("txt_to_hour"); ?></label>
        <input  type="text"
                class="hasTimePicker timepicker-to"
                name="settings[delivery_end]"
                id="delivery_timepicker_end"
                rel="delivery_timepicker_start"
                value="<?php echo getSetting("delivery_end", $this->settings); ?>"
                style="width: 70px;" />
    <div class="clear"></div>
    <br />
    <input type="hidden"
           name="id_restaurant"
           value="<?php echo Zend_Registry::get("restaurant")->id_restaurant; ?>" />
    <input type="submit" class="button" name="process_edit_front" value="<?php echo BackLib_Lang::tr("form_submit"); ?>" />
    <a href="<?php echo $this->baseUrl(); ?>/manage/restaurant/settings"><?php echo BackLib_Lang::tr("form_cancel"); ?></a>

</form>
</fieldset>
