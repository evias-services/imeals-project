<h2><?php echo BackLib_Lang::tr("txt_h2_delete_categories"); ?></h2>
<?php if (empty($this->categories)) : ?>
    <div class="error">,
        <p><?php echo BackLib_Lang::tr("txt_error_delete_no_categories"); ?></p>
    </div>
<?php else : ?>
    <p><?php echo BackLib_Lang::tr("txt_p1_delete_categories"); ?></p>
    <br />
    <p><?php echo BackLib_Lang::tr("txt_p2_delete_categories"); ?></p>
    <br />
    <ul>
    <?php foreach ($this->categories as $category) :  ?>
        <li>&quot;<span class="bold"><?php echo $category->title; ?></span>&quot;</li>
    <?php endforeach; ?>
    </ul>
    <div class="actions">
    <form action="<?php echo $this->baseUrl(); ?>/manage/menu/delete-category" method="post">
        <?php foreach ($this->categories as $category) :  ?>
        <input type="hidden" name="categories[]" value="<?php echo $category->id_category; ?>" />
        <?php endforeach; ?>
        <input type="submit"
               name="is_confirmed"
               value="<?php echo BackLib_Lang::tr("txt_form_submit_delete_category"); ?>" />

        <a href="<?php echo $this->baseUrl(); ?>/manage/menu/list-categories"><?php echo BackLib_Lang::tr("txt_form_cancel_delete_category"); ?></a>
        <div class="clear"></div>
    </form>
    </div>
<?php endif; ?>
