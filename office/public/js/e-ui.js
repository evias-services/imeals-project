
if (typeof eVias == "undefined")
    var eVias = {};

eVias.eUI = {
    initialize : function()
    {
        this._init_ajaxLinks();
        this._init_ajaxSearches();
        this._init_datePickers();
        this._init_periodPickers();
        this._init_timePickers();
        this._init_spinners();
        this._init_buttons();
        return this;
    },

    _init_ajaxLinks: function()
    {
        /* nyromodal is the popin plugin. */
        var depr_links = $(".nyromodal");

        if (typeof depr_links != "undefined" && depr_links.length) {
            depr_links.nyroModal();
            $(".nyromodal").each(function(elm) {
                console.log("deprecated [class='nyromodal']: " + this);
            });
        }

        var links = $(".ajax-link");
        if (typeof links != "undefined" && links.length)
            links.nyroModal();

        /* configure afterShow callback,
           initialize eRestaurant widgets. */
        $.nmFilters({basic: {
            afterShowCont : function(nm)
            {
                $(document).eRestaurant();

                var _widget_roomorg    = $("#room-organisator");
                var _widget_menueditor = $("#menu-editor");

                if (_widget_roomorg.length)
                    eVias.eRoomOrganisator.initialize();

                if (_widget_menueditor.length)
                    eVias.eMenuEditor.initialize();
            },
            close: function(nm)
            {
                $(document).eRestaurant();

                var _widget_roomorg    = $("#room-organisator");
                var _widget_menueditor = $("#menu-editor");

                if (_widget_roomorg.length)
                    eVias.eRoomOrganisator.initialize();

                if (_widget_menueditor.length)
                    eVias.eMenuEditor.initialize();
            }}
        });
    },

    _init_ajaxSearches: function()
    {
        var elms = $(".ajax-search-field");
        if (typeof elms == "undefined")
            return ;

        $(".ajax-search-field").each(function(e) {

            var _url         = $(this).attr("ajax-url");
            var _elm_val     = $(this).attr("ajax-setvalue");
            var _elm_display = $(this).attr("ajax-display");

            if (typeof _url == "undefined") {
                console.log("Missing 'ajax-url' attribute on ajax-search-field element.");
                return ;
            }

            $(this).autocomplete({
                minLength: 2,
                source: _url,
                select: function( event, ui ) {

                    if (typeof _elm_val != "undefined")
                        $(_elm_val).val(ui.item.id);
                    if (typeof _elm_display != "undefined")
                        /* hide 'ajax-display' element if available */
                        $(_elm_display).css("display", "none");
                },
                response: function(event, items) {
                    if (typeof items.content != "undefined"
                        && ! items.content.length ) {
                        if (typeof _elm_val != "undefined")
                            $(_elm_val).removeAttr("value");
                        if (typeof _elm_display != "undefined")
                            $(_elm_display).css("display", "block");
                    }
                    else {
                        if (typeof _elm_val != "undefined")
                            $(_elm_val).removeAttr("value");
                        if (typeof _elm_display != "undefined")
                            $(_elm_display).css("display", "none");
                    }
                }
            }).data("ui-autocomplete")._renderItem = function( ul, item )
            {
                /* overload autocomplete._renderItem */
                return $( "<li>" )
                  .append( "<a>" + item.label + "<br />" + item.desc + "</a>" )
                  .appendTo( ul );
            };
        });
    },

    _init_datePickers: function()
    {
        var elms = $(".datepicker");
        if (typeof elms == "undefined")
            return ;

        $(".datepicker").datepicker();

        /* configure datepickers */
        $(".datepicker").datepicker("option", "dateFormat", "dd/mm/yy");
        $(".datepicker").datepicker("option", "showAnim", "clip");
    },

    _init_periodPickers: function()
    {
        var elms_from  = $(".periodpicker-from");
        var elms_to    = $(".periodpicker-to");

        if (typeof elms_from != "undefined") {

            $(".periodpicker-from").datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                onClose: function(selectedDate) {
                    var to_id = "#" + $(this).attr("rel");
                    $(to_id).datepicker("option", "minDate", selectedDate);
                }
            });

            $(".periodpicker-from").datepicker("option", "dateFormat", "D dd/mm/yy");
            $(".periodpicker-from").datepicker("option", "showAnim", "clip");
        }


        if (typeof elms_to != "undefined") {

            $(".periodpicker-to").datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                onClose: function( selectedDate ) {
                    var from_id = "#" + $(this).attr("rel");
                    $(from_id).datepicker("option", "maxDate", selectedDate);
                }
            });

            $(".periodpicker-to").datepicker("option", "dateFormat", "D dd/mm/yy");
            $(".periodpicker-to").datepicker("option", "showAnim", "clip");
        }
    },

    _init_timePickers: function()
    {
        if (typeof $(".timepicker-from") != "undefined") {
            /* configure timepickers */
            $('.timepicker-from').timepicker({
                showLeadingZero: false,
                showAnim: "fadeIn",
                duration: 200,
                onHourShow: function(hour) {
                    var to_id = "#" + $(this).attr("rel");

                    var tpEndHour = $(to_id).timepicker('getHour');
                    if ($(to_id).val() == '') { return true; }
                    if (hour <= tpEndHour) { return true; }
                    return false;
                },
                onMinuteShow: function(hour, minute) {
                    var to_id = "#" + $(this).attr("rel");

                    var tpEndHour = $(to_id).timepicker('getHour');
                    var tpEndMinute = $(to_id).timepicker('getMinute');
                    if ($(to_id).val() == '') { return true; }
                    if (hour < tpEndHour) { return true; }
                    if ( (hour == tpEndHour) && (minute < tpEndMinute) ) { return true; }
                    return false;
                }
            });
        }

        if (typeof $(".timepicker-to") != "undefined") {
            $('.timepicker-to').timepicker({
                showLeadingZero: false,
                showAnim: "fadeIn",
                duration: 200,
                onHourShow: function(hour) {
                    var from_id = "#" + $(this).attr("rel");

                    var tpStartHour = $(from_id).timepicker('getHour');
                    if ($(from_id).val() == '') { return true; }
                    if (hour >= tpStartHour) { return true; }
                    return false;
                },
                onMinuteShow: function(hour, minute) {
                    var from_id = "#" + $(this).attr("rel");

                    var tpStartHour = $(from_id).timepicker('getHour');
                    var tpStartMinute = $(from_id).timepicker('getMinute');
                    if ($(from_id).val() == '') { return true; }
                    if (hour > tpStartHour) { return true; }
                    if ( (hour == tpStartHour) && (minute > tpStartMinute) ) { return true; }
                    return false;
                }
            });
        }

        if (typeof $(".timepicker") != "undefined") {
            $('.timepicker').timepicker({
                showAnim: "fadeIn",
                duration: 200
            });
        }
    },

    _init_spinners : function()
    {
        if (typeof $(".spinner") != "undefined")
            $(".spinner").spinner({min: 1});
    },

    _init_buttons : function()
    {
        if (typeof $(".button") != "undefined")
            $(".button").button();
    }
    
};
