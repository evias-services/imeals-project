<?php
function isEdit($user)
{
    return null !== $user && is_object($user);
}

$currentID    = isEdit($this->user) ? $this->user->getPK() : "";
$currentRole  = isEdit($this->user) ? $this->user->id_acl_role : "";
$currentLogin = isEdit($this->user) ? $this->user->login : "";
$currentEmail = isEdit($this->user) ? $this->user->email : "";
$currentName  = isEdit($this->user) ? $this->user->realname : "";
$currentRestaurants = isEdit($this->user) ? $this->user->getAccessibleRestaurants() : array();
?>
<div class="content" style="margin: 0px;">
    <h2><?php echo BackLib_Lang::tr("txt_h2_users_adduser"); ?></h2>
    <?php echo BackLib_Lang::tr("txt_p_users_adduser"); ?>
    <br />

    <fieldset class="user-form">
        <legend><?php echo BackLib_Lang::tr("txt_form_user_legend"); ?></legend>

    <form method="post" action="<?php echo $this->baseUrl(); ?>/manage/users/add-user">

        <label for="user[login]"><?php echo BackLib_Lang::tr("txt_form_user_login"); ?></label>
        <input type="text" id="user_login" name="user[login]" value="<?php echo $currentLogin; ?>" />

        <div class="clear"></div>
        <label for="user[email]"><?php echo BackLib_Lang::tr("txt_form_user_email"); ?></label>
        <input type="text" id="user_email" name="user[email]" value="<?php echo $currentEmail; ?>" />

        <div class="clear"></div>
        <label for="user[realname]"><?php echo BackLib_Lang::tr("txt_form_user_realname"); ?></label>
        <input type="text" id="user_realname" name="user[realname]" value="<?php echo $currentName; ?>" />

        <div class="clear"></div><br />
        <label for="user[id_acl_role]"><?php echo BackLib_Lang::tr("txt_form_user_role"); ?></label>
        <select name="user[id_acl_role]" id="user_role">
            <option value=""><?php echo BackLib_Lang::tr("opt_filter_select"); ?></option>
            <?php foreach ($this->roles as $rid => $role) {
                    $selected   = $currentRole == $rid ? " selected='selected'" : "";
                    echo "<option{$selected} value='{$rid}'>{$role}</option>"; } ?>
        </select>

        <!-- minimum 1 restaurant. -->
        <fieldset class="user-restaurants">
            <legend><?php echo BackLib_Lang::tr("legend_user_restaurants"); ?></legend>
        <div class="clear"></div><br />
        <label for="user[restaurants][]" id="label_user_restaurant_0"><?php echo BackLib_Lang::tr("txt_form_user_restaurants"); ?></label>
        <select name="user[restaurants][]" rel="restaurants-list" id="user_restaurant_0">
            <option value=""><?php echo BackLib_Lang::tr("opt_filter_select"); ?></option>
            <?php foreach ($this->restaurants as $rid => $restaurant) {
                    $selected   = isset($currentRestaurants[0]) && $currentRestaurants[0]->id_restaurant == $restaurant->id_restaurant ? " selected='selected'" : "";
                    echo "<option{$selected} value='{$restaurant->id_restaurant}'>{$restaurant->title}</option>"; } ?>
        </select>
<?php
if (count($currentRestaurants) <= 1) { ?>
        &nbsp;<img src="<?php echo $this->baseUrl(); ?>/images/plus-sign.png" class="add-restaurant-access" alt="Add" rel="1" />
<?php
} ?>
        <!-- dynamic generation for the rest -->
        <div id="dynamic_user_restaurants">
<?php
if (count($currentRestaurants) > 1) :
    for ($i = 1, $max = count($currentRestaurants); $i < $max; $i++) {
    ?>
        <div class="clear"></div><br />
        <label for="user[restaurants][]" id="label_user_restaurant_<?php echo $i; ?>">&nbsp;</label>
        <select name="user[restaurants][]" id="user_restaurant_<?php echo $i; ?>">
            <option value=""><?php echo BackLib_Lang::tr("opt_filter_select"); ?></option>
            <?php foreach ($this->restaurants as $rid => $restaurant) {
                    $selected   = isset($currentRestaurants[$i]) && $currentRestaurants[$i]->id_restaurant == $restaurant->id_restaurant ? " selected='selected'" : "";
                    echo "<option{$selected} value='{$restaurant->id_restaurant}'>{$restaurant->title}</option>"; } ?>
        </select>
    <?php
        if ($i == $max - 1) { ?>
        &nbsp;<img src="<?php echo $this->baseUrl(); ?>/images/plus-sign.png" class="add-restaurant-access" alt="Add" rel="<?php echo $i + 1; ?>" />
    <?php
        }
    }
endif; ?>
            <div id="end_dynamic_user_restaurants">&nbsp;</div>
        </fieldset>
<?php
if (! isEdit($this->user)) : ?>

        <div class="clear"></div>
        <label for="user[password]"><?php echo BackLib_Lang::tr("txt_form_user_password"); ?></label>
        <input type="password" id="user_password" name="user[password]" value="" />

        <div class="clear"></div>
        <label for="user[password_re]"><?php echo BackLib_Lang::tr("txt_form_user_password_confirm"); ?></label>
        <input type="password" id="user_password_re" name="user[password_re]" value="" />

<?php
endif; ?>

        <div class="clear"></div>
        <input type="hidden" id="user_id" name="user[id_e_user]" value="<?php echo $currentID; ?>" />
        <input type="submit" class="button" name="user[process]" value="<?php echo BackLib_Lang::tr("submit_adduser"); ?>" />
        <a class="nyroModalClose" href="#"><?php echo BackLib_Lang::tr("form_cancel"); ?></a>
    </form>
    </fieldset>
</div>

<script type="text/javascript">
    var addRestaurantAccess = function()
    {
        var restaurants = $($("select[rel='restaurants-list']")[0]).children("option");
        var nextIndex = parseInt($(this).attr("rel"));

        var r_html = "";
        restaurants.each(function(elm) {
            r_html = r_html + "<option value='" + $(this).attr("value") + "'>" + $(this).html() + "</option>";
        });

        next_restaurant_html = "<div class='clear'></div><br />"
            + "<label for='user[restaurants][]' id='label_user_restaurant_" + nextIndex + "'>&nbsp;</label>"
            + "<select name='user[restaurants][]' id='user_restaurant_" + nextIndex + "'>"
            + r_html
            + "</select>"
            + "&nbsp;"
            + "<img src='<?php echo $this->baseUrl(); ?>/images/plus-sign.png' class='add-restaurant-access' alt='Add' rel='" + (nextIndex+1) + "' />";

        $(next_restaurant_html).insertBefore($("div#end_dynamic_user_restaurants"));

        /* remove clicked element */
        $("img.add-restaurant-access[rel='" + nextIndex + "']").remove();

        $("img.add-restaurant-access").each(function(elm) {
            this.addEventListener("click", addRestaurantAccess, false);
        });
        return false;
    };

    $("img.add-restaurant-access").each(function(elm) {
        this.addEventListener("click", addRestaurantAccess, false);
    });

</script>
