<?php

class eVias_Catalogue_Category
	extends eVias_ArrayObject_Db
{
	protected $_tableName = 'evias_catalogue_category';

	protected $_pk = 'category_id';

	protected $_fields = array(
		'parent_category_id', // recursive foreign key
		'title',
		'description',
		'access_level',
		'category_publishing_data_id',
	);

	public static function loadById($id) {
		return $this->_load($id);
	}

	public static function fetchArticleCollection($cntLimit = null) {
		$articleCollection  = new eVias_Catalogue_Article_Collection();

		return $articleCollection;
	}
}
