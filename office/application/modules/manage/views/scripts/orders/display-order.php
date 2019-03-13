<?php
$items = $this->order->getCart()->getItems(true);

if (! empty($items))
    echo $this->orderTicket($this->order, $this->action_dialog);
