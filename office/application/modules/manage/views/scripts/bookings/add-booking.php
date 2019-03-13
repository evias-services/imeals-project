<?php
function isEdit($booking)
{
    return null !== $booking && is_object($booking);
}

$currentID      = isEdit($this->booking) ? $this->booking->getPK() : "";
$currentType    = isEdit($this->booking) ? $this->booking->id_booking_type : "";
$currentRest    = isEdit($this->booking) ? $this->booking->id_restaurant : "";
$currentCust    = isEdit($this->booking) ? $this->booking->id_customer : "";
$currentObject  = isEdit($this->booking) ? $this->booking->id_booked_object : "";
$currentCntP    = isEdit($this->booking) ? $this->booking->count_people : "";
$currentBkStart = isEdit($this->booking) ? $this->booking->date_book_start : "";
$currentBkEnd   = isEdit($this->booking) ? $this->booking->date_book_end : "";
$currentTimeStart = isEdit($this->booking) ? $this->booking->time_book_start : "";
$currentTimeEnd   = isEdit($this->booking) ? $this->booking->time_book_end : "";

$currentCustStr = isEdit($this->booking) ? $this->booking->getCustomer()->getTitle() : "";
?>
<div class="content" style="margin: 0px;">
    <h2><?php echo BackLib_Lang::tr("h2_bookings_addbooking"); ?></h2>
    <?php echo BackLib_Lang::tr("p_bookings_addbooking"); ?>

    <form method="post" action="<?php echo $this->baseUrl(); ?>/manage/bookings/add-booking">
        <h4><?php echo BackLib_Lang::tr("h3_bookings_steps"); ?></h4>

        <p><?php echo BackLib_Lang::tr("p_bookings_restaurant"); ?></p>
        <br />

        <!-- XXX restaurant select only if admin -->
        <p>
            <label for="booking[id_restaurant]"><?php echo BackLib_Lang::tr("label_bookings_restaurant"); ?></label>
            <select name="booking[id_restaurant]" id="booking_restaurant">
                <option value=""><?php echo BackLib_Lang::tr("opt_filter_select"); ?></option>
                <?php foreach ($this->restaurants as $restaurant) {
                        $selected = $currentRest == $restaurant->id_restaurant ? " selected='selected'" : "";
                        echo "<option{$selected} value='{$restaurant->id_restaurant}'>{$restaurant->title}</option>"; } ?>
            </select>
        </p>

        <p>
            <label for="booking[id_booking_type]"><?php echo BackLib_Lang::tr("label_bookings_booking_type"); ?></label>
            <select name="booking[id_booking_type]" id="booking_type">
                <option value=""><?php echo BackLib_Lang::tr("opt_filter_select"); ?></option>
                <option rel="<?php echo $this->baseUrl(); ?>/manage/bookings/select-rooms"
                        value="1"<?php echo ($currentType == 1 ? 'selected="selected"' : ""); ?>>
                    <?php echo BackLib_Lang::tr("opt_booking_type_room"); ?>
                </option>
                <option rel="<?php echo $this->baseUrl(); ?>/manage/bookings/select-tables"
                        value="2"<?php echo ($currentType == 2 ? 'selected="selected"' : ""); ?>>
                    <?php echo BackLib_Lang::tr("opt_booking_type_table"); ?>
                </option>
            </select>
        </p>

        <p>
            <label for="booking[id_customer]"><?php echo BackLib_Lang::tr("label_bookings_customer"); ?></label>
            <input type="hidden" name="booking[id_customer]" id="customer_id" value="<?php echo $currentCust; ?>" />
            <input type="text" class="ajax-search-field" ajax-display="#customer_form" ajax-setvalue="#customer_id" ajax-url="<?php echo $this->baseUrl(); ?>/manage/customers/search-customer" id="booking_customer_search" name="booking[customer_str]" value="<?php echo $currentCustStr; ?>" />
        </p><br />

        <div id="customer_form" style="display:none;">
            <p>
                <label for="booking[customer][realname]"><?php echo BackLib_Lang::tr("label_customer_realname"); ?></label>
                <input type="text" name="booking[customer][realname]" value="" />
            </p><br />

            <p>
                <label for="booking[customer][phone]"><?php echo BackLib_Lang::tr("label_customer_phone"); ?></label>
                <input type="text" name="booking[customer][phone]" value="" />
            </p><br />

            <p>
                <label for="booking[customer][email]"><?php echo BackLib_Lang::tr("label_customer_email"); ?></label>
                    <input type="text" name="booking[customer][email]" value="" />
            </p><br />
        </div>

        <p>
            <label for="booking[count_people]"><?php echo BackLib_Lang::tr("label_bookings_booking_count_people"); ?></label>
            <input class="very-short spinner" name="booking[count_people]" id="booking_count_people" value="<?php echo $currentCntP; ?>" />
        </p>

        <p>
            <label for="booking[date_book_start]"><strong><?php echo BackLib_Lang::tr("label_bookings_booking_book_start"); ?></strong></label>
            <input type="text"
                   id="booking_date_start"
                   name="booking[date_book_start]"
                   class="periodpicker-from very-short"
                   rel="booking_date_end"
                   style="text-align:center;"
                   value="<?php echo $currentBkStart; ?>" />
            <label style="width:50px;" for="openingam_timepicker_start"><?php echo BackLib_Lang::tr("txt_from_hour"); ?></label>
                <input  type="text"
                        name="booking[time_book_start]"
                        class="hasTimePicker timepicker"
                        id="booking_time_start"
                        value="<?php echo $currentTimeStart; ?>"
                        style="width: 70px;" />

        </p><br />

        <p>
            <label for="booking[date_book_end]"><strong><?php echo BackLib_Lang::tr("label_bookings_booking_book_end"); ?></strong></label>
            <input type="text"
                   id="booking_date_end"
                   name="booking[date_book_end]"
                   class="periodpicker-to very-short"
                   rel="booking_date_start"
                   style="text-align:center;"
                   value="<?php echo $currentBkEnd; ?>" />
            <label style="width:50px;" for="opening_timepicker_end"><?php echo BackLib_Lang::tr("txt_to_hour"); ?></label>
                <input  type="text"
                        class="hasTimePicker timepicker"
                        id="booking_time_end"
                        name="booking[time_book_end]"
                        value="<?php echo $currentTimeEnd; ?>"
                        style="width: 70px;" />
        </p><br />

        <p>
            <div id="actions_booking_availability" style="width: 200px;">
                <button class="button" id="check_availability">
                    <?php echo BackLib_Lang::tr("button_check_availability"); ?>
                </button>
            </div>
        </p>
        <div id="availability_response">
        </div><br />

        <input type="hidden" id="booking_id" name="booking[id_booking]" value="<?php echo $currentID; ?>" />

        <div id="actions_booking_form" style="display:none">
            <input type="submit" class="button "name="process_edit_booking" value="<?php echo BackLib_Lang::tr("form_submit"); ?>" />
            <a class="nyroModalClose" class="button" href="#"><?php echo BackLib_Lang::tr("form_cancel"); ?></a>
        </div>
    </form>
</div>

<script type="text/javascript">

    $(document).ready(function(evt)
    {
        $.eRestaurant._init_checkAvailabilityButton('<?php echo $this->baseUrl() . "/manage/bookings/get-available-objects"; ?>');

        /* set minDate as today */
        $(".periodpicker-from").datepicker("option", "minDate", new Date());
        $(".periodpicker-to").datepicker("option", "minDate", new Date());
    });
</script>
