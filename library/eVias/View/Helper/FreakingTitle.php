<?php

class eVias_View_Helper_FreakingTitle
	extends Zend_View_Helper_HeadTitle
{
	/**
     * Registry key for placeholder
     * @var string
     */
    protected $_regKey = 'eVias_Framework_View_Helper_FreakingTitle';

	public function freakingTitle($title=null) {
		parent::headTitle('GREG' . $title,
						  Zend_View_Helper_PlaceHolder_Container_Abstract::SET
						 );
	}
}
