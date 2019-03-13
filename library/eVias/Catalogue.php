<?php

class eVias_Catalogue
	extends eVias_ArrayObject_Db
{
	protected $_tableName = 'evias_catalogue';

	protected $_pk = 'catalogue_id';

	protected $_fields = array(
		'title',
		'description',
		'date_creation',
		'date_updated'
	);

	public static function loadById($id) {
		$object = new self;

		return $object->_load($id);
	}
}
