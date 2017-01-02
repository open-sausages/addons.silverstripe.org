<?php

namespace SilverStripe\Addons\Controllers;

use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;

/**
 * Instructions on how to submit a module.
 * Doesn't actually handle the submission itself,
 * that's left to packagist.org.
 */
class SubmitAddonController extends SiteController 
{

	private static $allowed_actions = [
		'index',
	];

	public function index() 
	{
		return $this->renderWith(array('SubmitAddon', 'Page'));
	}

	public function Title() 
	{
		return 'Submit';
	}

	public function Link() 
	{
		return Controller::join_links(Director::baseURL(), 'submit');
	}

	public function MenuItemType() 
	{
		return 'button';
	}

}
