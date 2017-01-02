<?php

namespace SilverStripe\Addons\Model;

use SilverStripe\ORM\DataObject;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;

/**
 * An author who can be linked to several add-ons.
 */
class AddonAuthor extends DataObject 
{

	private static $db = [
		'Name' => 'Varchar(255)',
		'Email' => 'Varchar(255)',
		'Homepage' => 'Varchar(255)',
		'Role' => 'Varchar(255)'
	];

	private static $belongs_many_many = [
		'Versions' => 'SilverStripe\Addons\Model\AddonVersion'
	];

	private static $default_sort = 'Name';

	public function GravatarUrl($size, $default = 'mm') 
	{
		return sprintf(
			'http://www.gravatar.com/avatar/%s?s=%d&d=%s',
			md5(strtolower(trim($this->Email))),
			$size,
			$default
		);
	}

	public function Link() 
	{
		return Controller::join_links(Director::baseURL(), 'authors', $this->ID);
	}

	public function Addons() 
	{
		return Addon::get()->filter('ID', $this->Versions()->column('AddonID'));
	}

}
