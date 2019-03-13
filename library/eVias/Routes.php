<?php

class eVias_Routes
{
	/**
     * return url representation of text, replaces special characters
	 * with '-' sign
     *
	 * @todo : replace äüïîûêâéèç by corresponding alpha char
     *
     * @param  string $url
     * @return string
     */
	static public function cleanUrl($url)
	{
		$url = strtolower($url);
		$url = preg_replace(array('/&szlig;/',
		'/&(..)lig;/',
		'/&([aeiouAEIOU])uml;/',
		'/&(.)[^;]*;/'),
		array('ss',
		"$1",
		"$1",
		"$1"),
		$url);

		/* strip non alpha characters */
		$url = preg_replace(array('/[^[:alpha:]\d\.]/', '/-+/'), '-', $url);

		// remove eventual leading/trailing hyphens due to leading/trailing non-alpha chars
		return trim($url, '-');
	}

	/**
	 * Fetch all application's routes
	 *
	 * @todo : read config file to fetch routes
	 *
	 */
	public static function fetch() {
		static $routes;

		if (! isset($routes)) {
			$routes = array(
				'home'		=> new Zend_Controller_Router_Route(
					self::cleanUrl('Accueil'),
					array(
						'module'	=> 'default',
						'controller'=> 'index',
						'action'	=> 'index')),
				'home/customize' => new Zend_Controller_Router_Route(
					self::cleanUrl('Personnalisation Accueil'),
					array(
						'module'	=> 'default',
						'controller'=> 'index',
						'action'	=> 'customizeindex')),
				'catalogue'	=> new Zend_Controller_Router_Route(
					self::cleanUrl('Catalogue Boutique'),
					array(
						'module'	=> 'catalogue',
						'controller'=> 'index',
						'action'	=> 'index')),
				'member'	=> new Zend_Controller_Router_Route(
					self::cleanUrl('Escape membre'),
					array(
						'module'	=> 'member',
						'controller'=> 'index',
						'action'	=> 'index')),

			);
		}

		return $routes;
	} 
}
