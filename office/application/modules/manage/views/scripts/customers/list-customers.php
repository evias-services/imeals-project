<h2><?php echo BackLib_Lang::tr("txt_h2_list_customers"); ?></h2>

<div>
    <?php
    echo $this->filterForm($this->filter, array(
        /* default filter field type is "text" */
        "realname" => array("label" => BackLib_Lang::tr("l_filter_realname")),
        "phone" => array("label" => BackLib_Lang::tr("l_filter_phone")),
        "email" => array("label" => BackLib_Lang::tr("l_filter_email")),
    ));
    ?>
<fieldset>
<legend><?php echo BackLib_Lang::tr("legend_listresults"); ?></legend>
    <?php
    /* Configuration pagination */
    echo $this->paginationControl($this->paginator, 'Sliding', 'pagination.phtml');
    ?>
<table id="customers-list" cellpadding="3px" cellspacing="0px">
    <thead>
        <th><span><?php echo BackLib_Lang::tr("txt_th_customer_id"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_customer_realname"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_customer_phone"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_customer_email"); ?></span></th>
        <th><span><?php echo BackLib_Lang::tr("txt_th_customer_orderscount"); ?></span></th>
    </thead>
    <tbody>
<?php
        foreach($this->paginator as $record)
        {
            echo "<tr class='customer'>";
            echo "<td class='txtCenter data'><span>{$record->id_customer}</span></td>";
            echo "<td class='txtCenter data'><span>{$record->realname}</span></td>";
            echo "<td class='txtCenter data'><span>{$record->phone}</span></td>";
            echo "<td class='txtCenter data'><span>{$record->email}</span></td>";
            echo "<td class='txtCenter data'><span>{$record->counter}</span></td>";
            echo "</tr>";
        }

?>
    </tbody>
</table>
<?php
    /* Configuration pagination */
    echo $this->paginationControl($this->paginator, 'Sliding', 'pagination.phtml');
?>
</fieldset>
</div>
<br />
<hr />

