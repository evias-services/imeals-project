
if (typeof eVias == "undefined")
    var eVias = {};

eVias.eRoomOrganisator = {
    initialize : function()
    {
        this._init_roomOrganisator();
    },

    _init_roomOrganisator : function()
    {
        this._init_draggables(".ui-widget-header", "table-draggable", false, false);

        $("#room-addtable").click(function(evt)
        {
            var next_num = $(".table-draggable").length + 1;
            var next_idx = next_num - 1;

            $( "<div class='table-draggable draggable ui-widget-content'>"
                + "<input type='hidden' rel='number' name='room[tables][" + next_idx +"][table_number]' value='" + next_num + "' />"
                + "<input type='hidden' rel='position' name='room[tables][" + next_idx +"][room_position]' value='' />"
                + "<p align='center'><strong>" + $("#table_default_title").html() + " " + next_num + "</strong></p>"
                + "<p><input style='width: 15px; margin-left:0px;' class='spinner' name='room[tables][" + next_idx + "][count_places]' value='0' />"
                + "<span><strong>&nbsp;" + $("#object_count_places_text").html() + "</strong></span></p>"
                + "</div>" )
                .appendTo($(".room-snaptarget"));

            eVias.eRoomOrganisator._init_draggables(".ui-widget-header", "table-draggable", false);
            eVias.eUI._init_spinners();

            return false;
        }); /* end .room-addtable.click() */
    },
    
    _init_draggables: function(snap_target, class_name, with_oob, _axis)
    {
        var _selector = "." + class_name;
        if (! $(_selector).length)
            return;

        var __move_to_xy = function(elm, left, top)
        {
            elm.css("left", left + "px");
            elm.css("top", top + "px");
        };

        if (_axis != "x" && _axis != "y")
            _axis = false;

        /* Initialize draggable plugin */
        $(_selector).draggable({
            axis: _axis,
            snap: snap_target,
            snapMode: "inner",
            snapTolerance: 2,
            stop: function(event, ui)
            {
                /* if out_of_bounds should be handled */
                if (with_oob) {
                    var _parent_width  = $(this).parent("div" + snap_target)[0].offsetWidth;
                    var _parent_height = $(this).parent("div" + snap_target)[0].offsetHeight;
                    var _my_width  = $(this)[0].offsetWidth;
                    var _my_height = $(this)[0].offsetHeight;

                    var current_left = ui.position.left;
                    var current_top  = ui.position.top;

                    /* current_elm is previous draggable object
                       (siblings of $(this)).
                       This small algorithm calculates the real END top
                       and left positions of the object being dragged. */
                    var current_elm = $(this).prev("div.ui-draggable");
                    do {
                        if (! current_elm.length)
                            break;

                        /*
                         XXX margins
                         */

                        var __h = current_elm[0].offsetHeight/* + margins.top + margins.bottom */;
                        var __w = current_elm[0].offsetWidth/* + margins.left + margins.right*/;

                        current_top = current_top + __h;
                        current_elm = current_elm.prev("div.ui-draggable");
                    }
                    while (current_elm.length);

                    /* on drop make sure position is not
                       out of bounds. */
                    var oob_left   = current_left < -1;
                    var oob_top    = current_top < -1;
                    var oob_right  = (current_left + _my_width) > _parent_width;
                    var oob_bottom = (current_top + _my_height) > _parent_height;

                    if (oob_left || oob_right)
                        $(this).css("left", ui.originalPosition.left);

                    if (oob_top || oob_bottom)
                        $(this).css("top", ui.originalPosition.top);

                    /* check for drop HOVER other meal. */

                } /* end with_oob */

                /* on drop save new position to
                   input[rel='position'] */
                $(this).children("input[type='hidden'][rel='position']").val(ui.position.left + "," + ui.position.top);
            }
        });

        /* position objects correctly according
           to the input[rel='position'] value. */
        $(_selector).each(function(e)
        {
            if (! $(this).children("input[type='hidden'][rel='position']").length)
                return;

            var _val_pos  = $(this).children("input[type='hidden'][rel='position']")[0].value;

            if (! _val_pos.length)
                return ;

            var _val_left = _val_pos.split(",")[0];
            var _val_top = _val_pos.split(",")[1];

            __move_to_xy($(this), _val_left, _val_top);
        });
    },
};
