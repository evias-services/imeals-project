<?php

class eVias_Service_Catalogue 
	extends eVias_Service_Abstract
{
	public static function initServiceCatalogue() {
		parent::_initDb();
	}
	
	/**
	 * @todo: move into Abstract
	 *		  define special-edit only in this service.
	 *		  this one is pretty generic and works fine
	 */
	public function editCatalogue($catObject, $newData=array()) {
		foreach ($newData as $key => $value) {
			if (! in_array($key, $catObject->fieldNames()))
				throw new eVias_Service_Catalogue_Exception('Trying to change key: ' . $key . ', which doesn\'t exist');

			if (! $this->_validateField($key, $value)) {
				throw new eVias_Service_Catalogue_Exception('Could not validate the field: ' . $key . ' with : ' .$value);
			}
			$catObject->$key = $value;

		}

		return $catObject;
	}
	/**
	 * @todo: Use validators
	 */
	protected function _validateField($key, $value) {
		if (empty($key)) 
			throw new eVias_Service_Catalogue_Exception('Validating an empty key.');

		switch($key) {
			case 'title':
			{
				if (empty($value)) 
					return false;
			}

			default:
		}

		return true;
	}
}
