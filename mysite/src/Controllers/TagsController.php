<?php

namespace SilverStripe\Addons\Controllers;

use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\ORM\MySQLQuery;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\ArrayData;

/**
 * Lists tags that are associated with add-ons.
 */
class TagsController extends SiteController 
{

	private static $allowed_actions = [
		'index'
	];

	public function index() 
	{
		return $this->renderWith(['Tags', 'Page']);
	}

	public function Title() 
	{
		return 'Tags';
	}

	public function Link() 
	{
		return Controller::join_links(Director::baseURL(), 'tags');
	}

	public function Tags() 
	{
		$query = new MySQLQuery();
		$result = new ArrayList();

		$query
			->setSelect('"AddonKeyword"."ID", "Name"')
			->selectField('COUNT("AddonKeywordID")', 'Count')
			->setFrom('AddonKeyword')
			->addLeftJoin('Addon_Keywords', '"AddonKeywordID" = "AddonKeyword"."ID"')
			->setGroupBy('"ID"')
			->setOrderBy(array('"Count"' => 'DESC', '"Name"' => 'ASC'));

		foreach ($query->execute() as $row) {
			$link = Controller::join_links(
				Director::baseURL(), 'add-ons', '?' . http_build_query(array(
					'tags[]' => $row['Name']
				))
			);

			$result->push(new ArrayData($row + array('Link' => $link)));
		}

		return $result;
	}

}
