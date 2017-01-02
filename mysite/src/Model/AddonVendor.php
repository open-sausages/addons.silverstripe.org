<?php

namespace SilverStripe\Addons\Model;

use SilverStripe\ORM\DataObject;

/**
 * An add-one vendor, derived from the vendor part of a package name,
 */
class AddonVendor extends DataObject 
{

	public static $db = [
		'Name' => 'Varchar(255)'
	];

	public static $has_many = [
		'Addons' => 'SilverStripe\Addons\Model\Addon'
	];

	public function Authors() 
	{
		return $this->Addons()->relation('Versions')->relation('Authors');
	}

}
