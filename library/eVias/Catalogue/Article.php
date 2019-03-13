<?php

class eVias_Catalogue_Article
	extends eVias_ArrayObject_Db
{

	/**
	 * _tableName : database table name
	 * @var string protected
	 */
	protected $_tableName = 'evias_catalogue_article';

	/**
	 * _pk : primary key field name
	 * @var string protected
	 */
	protected $_pk = 'article_id';

	/**
	 * _fields : list of table's field, pk excluded
	 * @var array of string protected
	 */
	protected $_fields = array(
		'title',
		'description',
		'article_publishing_data_id', // FK
		'date_creation',
		'date_updated',
	);

	public function __construct($data){
		try {
			$this->_load($data);
		} catch (eVias_Catalogue_Article_Exception $e) {
			trigger_error('could not load the article');
		}
	}
}
