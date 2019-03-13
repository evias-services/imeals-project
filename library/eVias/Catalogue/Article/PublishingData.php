<?php

class eVias_Catalogue_Article_PublishingData
	extends eVias_ArrayObject_Db
{
	protected $_tableName = 'evias_catalogue_article_publishing_data';

	protected $_pk = 'publishing_data_id';

	protected $_fields = array(
		'date_publish_begin',
		'date_publish_end',
		'access_level',
		'category_id',
		'article_id',
		'catalogue_id',
		'prize_data_id',
		'date_creation',
		'date_updated',
	);

	public static function loadById($id) {
		return $this->_load($id);
	}

	public static function loadByArticleId($article_id) {
		$collection = new eVias_Collection();

		$query = "
			SELECT
				" . implode(', ', $this->_fields) . "
			FROM
				$this->_tableName
			WHERE
				article_id = :article_id
			ORDER BY
				date_creation DESC
		";

		$result = $this->_db->fetchAll($query, array('article_id' => $article_id));

		if (! empty($result)) {
			$count = count($result);
			for ($i = 0; $i < $count; $i++) {
				$row = $result[$i];
				$tmpObject = new self;
				$tmpObject->bind($row);
				$collection->add($tmpObject);
				// make sure data is deleted
				unset($tmpObject);
				unset($row);
			}
		}

		return $collection;
	}
}
