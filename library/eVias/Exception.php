<?php

class eVias_Exception 
	extends Exception
{
	// upper level of exception in this lib
	public function getCustomMessage() {
		$date		= new DateTime();
		$messageOut = '[' . $date->format('d/m/Y H:i:s') . '] - eVias Exception (' . __CLASS__ . ') : ';
		$messageOut .= parent::getMessage();

		return $messageOut;
	}
}
