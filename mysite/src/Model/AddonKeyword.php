<?php

namespace SilverStripe\Addons\Model;

use SilverStripe\ORM\DataObject;

/**
 * A keyword which is attached to add-ons and versions.
 */
class AddonKeyword extends DataObject 
{

	private static $db = [
		'Name' => 'Varchar(255)'
	];

	private static $belongs_many_many = [
		'Addons' => 'SilverStripe\Addons\Model\Addon',
		'Versions' => 'SilverStripe\Addons\Model\AddonVersion'
	];

	/**
	 * Gets a keyword object by name, creating one if it does not exist.
	 *
	 * @param string $name
	 * @return AddonKeyword
	 */
	public static function get_by_name($name) 
	{
		$name = strtolower($name);
		$kw = AddonKeyword::get()->filter('Name', $name)->first();

		if (!$kw) {
			$kw = new AddonKeyword();
			$kw->Name = $name;
			$kw->write();
		}

		return $kw;
	}

}
