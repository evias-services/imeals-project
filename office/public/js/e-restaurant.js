
(function($)
{
    $.extend($.ui, { eRestaurant: { version: "0.3.0"} });

    var PROP_NAME = 'eRestaurant',
        erestuuid = new Date().getTime();

    function eRestaurant()
    {
        this.language = "fr";
        this.e_ui = null;
    }

/**
 * implementation of class eRestaurant
 **/

    $.extend(eRestaurant.prototype, {

        initialize : function()
        {
            this.e_ui = eVias.eUI.initialize();
            this.initialized = true;
        },

/*****************************************
 * bookings/get-available-objects button *
 *****************************************/

        _init_checkAvailabilityButton : function(_uri_availability_request)
        {
            $("#check_availability").click(function(evt)
            {

                $("#actions_booking_availability").addClass("ui-loading-widget");

                var _val_rid = $("#booking_restaurant").children("option")[$("#booking_restaurant")[0].selectedIndex].value;
                var _val_tid = $("#booking_type").children("option")[$("#booking_type")[0].selectedIndex].value;
                var _val_cp  = $("#booking_count_people")[0].value;
                var _val_dstart  = $("#booking_date_start")[0].value;
                var _val_dend    = $("#booking_date_end")[0].value;
                var _val_tstart  = $("#booking_time_start")[0].value;
                var _val_tend    = $("#booking_time_end")[0].value;

                $.ajax({
                    url : _uri_availability_request,
                    method: 'get',
                    async: false,
                    data: "rid=" + _val_rid
                        + "&tid=" + _val_tid
                        + "&dstart=" + _val_dstart
                        + "&dend=" + _val_dend
                        + "&tstart=" + _val_tstart
                        + "&tend=" + _val_tend
                        + "&cp=" + _val_cp,
                    success: function(data, status, xhr)
                    {
                        $("#availability_response").html(data);

                        $("input[type='radio'][group='booked_object']").click(function(evt) {
                            /* An available object was selected */

                            var _val_stime = $(this).attr("rel").split("-")[0];
                            var _val_etime = $(this).attr("rel").split("-")[1];

                            $("#booking_time_start")[0].value = _val_stime;

                            if ($("#booking_time_end")[0].value.length)
                                $("#booking_time_end")[0].value = _val_etime;

                            /* Hide availability check and display form actions */
                            $("#actions_require_select_option").css("display", "none");
                            $("#actions_booking_form").css("display", "block");

                            $("#actions_booking_availability").removeClass("ui-loading-widget");
                            $("#actions_booking_availability").removeAttr("class");
                        });

                        /* Register ul.suggestions li.click */
                        $("ul.availability-suggestions li").click(function(evt) {

                            if ($(this).hasClass("selected")) {
                                /* remove selection */
                                $(this).children("input[type='radio']")[0].checked = false;
                                $(this).removeClass("selected");
                            }
                            else {
                                /* radiogroup logic, only one selection. */
                                $("ul.availability-suggestions li input[type='radio']").each(function(el) {
                                    this.checked = false;
                                });
                                $("ul.availability-suggestions li").each(function(el) {
                                    $(this).removeClass("selected");
                                });

                                $(this).children("input[type='radio']")[0].checked = true;
                                $(this).addClass("selected");
                            }

                            /* check if any selected => form_actions display
                                     if none selected => form_actions hide */
                            var has_selected = false;
                            $("ul.availability-suggestions li input[type='radio']").each(function(e) {
                                if (this.checked == true)
                                    has_selected = true;
                            });

                            if (has_selected) {
                                $("#actions_require_select_option").css("display", "none");
                                $("#actions_booking_form").css("display", "block");
                            }
                            else {
                                $("#actions_require_select_option").css("display", "block");
                                $("#actions_booking_form").css("display", "none");
                            }
                        }); /* end li.click */

                        var _interval_id = setInterval(function() {
                            $("#actions_booking_availability").removeClass("ui-loading-widget");
                            $("#actions_booking_availability").removeAttr("class");

                            clearInterval(_interval_id);
                            return false;
                        }, 400);
                    }
                });
                /* end ajax */

                return false;
            }); /* end #check_availability.click */
        }
    });

/**
 * implementation of $.eRestaurant({})
 **/

    $.fn.eRestaurant = function(options)
    {
        /* Verify an empty collection wasn't passed */
        if ( !this.length )
            return this;

        /* Initialise the user interface. */
        $.eRestaurant.initialize();

        /*
         * XXX get language from options
         */

        var language = $.eRestaurant.language;
        if (language != "en")
            $.extend($.datepicker._defaults, $.datepicker.regional[language]);
    }

/**
 * eRestaurant uses the singleton pattern.
 */

    $.eRestaurant = new eRestaurant();

/**
 * Fixes
 * - Add another global to avoid noConflict issues with inline event handlers
 */
    window['EREST_jQuery_' + erestuuid] = $;

})(jQuery);
