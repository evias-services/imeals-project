

if (typeof eVias == "undefined")
    var eVias = {};

eVias.eMenuEditor = {
    initialize : function()
    {
        this._init_menuEditor();

        return this;
    },
    
    _init_menuEditor : function()
    {
        this._init_toolbar();
        this._init_selectables();
        this._init_actions();
    },

    _init_toolbar : function()
    {
        $(".menu-addcategory").each(function(el, elm) { $(elm).click(function(evt)
        {
            var next_num = $(".category").length + 1;
            var next_idx = next_num - 1;

            /* XXX input fields */

            $( "<div class='category' rel='" + next_idx + "'>"
                + "<div class='category-presentation ui-widget-content'>"
                    + "<p align='center'>"
                        + "<input type='text' class='very-short' name='menu[categories][" + next_idx + "][title]' value='" + $("#category_default_title").html() + "' />"
                        + "<br />"
                        + "<input type='text' class='very-short' name='menu[categories][" + next_idx + "][description]' value='" + $("#category_default_desc").html() + "' />"
                    + "</p>"
                + "</div>"
                + "<div class='category-actions'>"
                    + "<ul>"
                        + "<li><img src='/images/ico-delete.gif' alt='Delete Category' /></li>"
                        + "<li><img src='/images/edit.png' alt='Edit Category' /></li>"
                    + "</ul>"
                + "</div>"
             + "</div>")
                .appendTo($(".categories-list"));

            $("<div class='meals' style='display:none;' rel='" + next_idx + "'>"
                + "<div class='meal ui-widget-content' rel='0'>"
                    + "<p align='left'>"
                        + "<input type='text' class='short' name='menu[categories][" + next_idx + "][meals][0][title]' value='" + $("#meal_default_title").html() + "' />"
                        + "<br />"
                        + "<input type='text' class='short' name='menu[categories][" + next_idx + "][meals][0][small_desc]' value='" + $("#meal_default_desc").html() + "' />"
                        + "<br />"
                        + "<span style='float:left'><strong>" + $("#label_meal_menu_number").html() + "</strong></span>"
                        + "<input style='width: 15px; float:left; margin-left:0px;' class='spinner' type='text' name='menu[categories][" + next_idx + "][meals][0][meal_menu_number]' value='' />"
                        + "<div class='clear'></div>"
                        + "<input type='checkbox' name='menu[categories][" + next_idx + "][meals][0][can_be_customized]' />"
                        + "<span><strong>" + $("#label_can_be_customized").html() + "</strong></span>"
                    + "</p>"
                + "</div>"
            + "</div>")
            .appendTo($(".meals-list"));

            eVias.eUI._init_spinners();
            eVias.eMenuEditor._init_selectables();

            return false;
        }); }); /* end .menu-addcategory.each */

        $(".menu-addmeal").each(function(el,elm) { $(elm).click(function(evt)
        {
            var _sel = $(".ui-selected");

            if (! _sel.length)
                alert($("#error_select_category_for_meal").html());

            _sel.each(function(cidx, category_elm)
            {                
                if (! $(category_elm).hasClass("category"))
                    return true;

                var _cat_idx   = parseInt($(category_elm).attr("rel"));                
                var _meals_elm = $(".meals[rel='" + _cat_idx + "']")[0];
                var _meal_idx  = $(_meals_elm).children(".meal").length;

                $("<div class='meal ui-widget-content' rel='" + _meal_idx + "'>"
                    + "<p align='left'>"
                        + "<input type='text' class='short' name='menu[categories][" + _cat_idx + "][meals][" + _meal_idx + "][title]' value='" + $("#meal_default_title").html() + "' />"
                        + "<br />"
                        + "<input type='text' class='short' name='menu[categories][" + _cat_idx + "][meals][" + _meal_idx + "][small_desc]' value='" + $("#meal_default_desc").html() + "' />"
                        + "<br />"
                        + "<span style='float:left'><strong>" + $("#label_meal_menu_number").html() + "</strong></span>"
                        + "<input style='width: 15px; float:left; margin-left:0px;' class='spinner' type='text' name='menu[categories][" + _cat_idx + "][meals][" + _meal_idx + "][meal_menu_number]' value='' />"
                        + "<div class='clear'></div>"
                        + "<input type='checkbox' name='menu[categories][" + _cat_idx + "][meals][" + _meal_idx + "][can_be_customized]' />"
                        + "<span><strong>" + $("#label_can_be_customized").html() + "</strong></span>"
                    + "</p>"
                + "</div>")
                .appendTo($(_meals_elm));
                    
                eVias.eUI._init_spinners();
            });

            return false;
        })});
    },

    _init_selectables : function()
    {
        $("#menu-editor .categories-list").selectable({
            selected : function(evt, obj)
            {
                if (! $(obj.selected).hasClass("category"))
                    /* $.selectable() sets all children elements
                       to be "ui-selectee"s. This feature should be
                       restricted in this case, as only categories
                       should be selectable. */
                    return ;
                
                /* work around for "one-selection-only" */
                var _selections = $(".ui-selected");
                if (_selections.length > 1)
                    _selections.each(function(i, e) { $(e).removeClass("ui-selected"); });

                $(obj.selected).addClass("ui-selected");
                $($(obj.selected).children(".category-presentation")[0]).addClass("ui-selected");
                /* end work around */

                /* hide current displayed meals and display
                   selected category's meals. */
                var _cat_ix = $(obj.selected).attr("rel");

                $(".meals").each(function(i, e) { $(e).css("display", "none"); });
                $(".meals[rel='" + _cat_ix + "']").css("display", "block");
            }
        });
    },

    _init_actions : function()
    {
        $(".category-actions").each (function(cix, actions_elm)
        {
            var _list_elm = $(actions_elm).children("ul");

            $(_list_elm).children("li.delete").each(function(dix, del_li)
            {
                var _del_button = $(del_li).children("button")[0];

                $(_del_button).click(function(evt)
                {
                    var m = $("#confirm_delete_category").html();
                    //m = m.replace(/%s/, '\n');
                    var c = confirm(m);
                    if (c != true)
                        return false;
                    
                    var _li_elm = $(this).parent("li");

                    var _cat_idx        = $(_li_elm).attr("rel");
                    
                    var _elm_category   = $(".category[rel='" + _cat_idx + "']")[0];
                    var _elm_meals      = $(".meals[rel='" + _cat_idx + "']");

                    _elm_category.remove();
                    _elm_meals.remove();

                    return false;
                });
            });
            
            $(_list_elm).children("li.modify").each(function(dix, mod_li)
            {
                var _mod_button = $(mod_li).children("button")[0];
                
                $(_mod_button).click(function(evt)
                {
                    var _li_elm = $(this).parent("li");
                    
                    var _cat_idx        = $(_li_elm).attr("rel");
                    var _elm_category   = $(".category[rel='" + _cat_idx + "']")
                                                .children(".category-presentation")[0];

                    var _elm_write = $(_elm_category).children(".write")[0];
                    var _elm_read  = $(_elm_category).children(".read")[0];

                    if (! $(_elm_write).hasClass("hidden"))
                        /* edition mode cannot be reverted. */
                        return false;

                    $(_elm_write).removeClass("hidden");
                    $(_elm_read).addClass("hidden");
                    
                    return false;
                });
            });
        }); /* end each .category-actions */

        
        $(".meal-actions").each (function(cix, actions_elm)
        {
            var _list_elm = $(actions_elm).children("ul");

            $(_list_elm).children("li.delete").each(function(dix, del_li)
            {
                var _del_button = $(del_li).children("button")[0];

                $(_del_button).click(function(evt)
                {
                    var m = $("#confirm_delete_meal").html();
                    var c = confirm(m);
                    if (c != true)
                        return false;
                    
                    var _li_elm = $(this).parent("li");

                    var _m_idx     = $(_li_elm).attr("rel");
                    var _elm_meals = $(_li_elm).parent("ul")
                                               .parent("div.meal-actions")
                                               .parent("div.meal")
                                               .parent("div.meals");
                                               
                    var _elm_meal  = $(_elm_meals).children(".meal[rel='" + _m_idx + "']");

                    _elm_meal.remove();

                    return false;
                });
            });
            
            $(_list_elm).children("li.modify").each(function(dix, mod_li)
            {
                var _mod_button = $(mod_li).children("button")[0];
                
                $(_mod_button).click(function(evt)
                {
                    var _li_elm = $(this).parent("li");
                    
                    var _m_idx     = $(_li_elm).attr("rel");
                    var _elm_meals = $(_li_elm).parent("ul")
                                               .parent("div.meal-actions")
                                               .parent("div.meal")
                                               .parent("div.meals");
                                               
                    var _elm_meal  = $(_elm_meals).children(".meal[rel='" + _m_idx + "']").children(".meal-data");

                    var _elm_write = $(_elm_meal).children(".write")[0];
                    var _elm_read  = $(_elm_meal).children(".read")[0];

                    if (! $(_elm_write).hasClass("hidden"))
                        /* edition mode cannot be reverted. */
                        return false;

                    $(_elm_write).removeClass("hidden");
                    $(_elm_read).addClass("hidden");
                    
                    return false;
                });
            });
        }); /* end each .meal-action */
    }
};
