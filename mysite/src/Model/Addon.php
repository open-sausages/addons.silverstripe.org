<?php

namespace SilverStripe\Addons\Model;

use Elastica\Document;
use Elastica\Type\Mapping;
use SilverStripe\ORM\ArrayData;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\ORM\DataObject;

/**
 * An add-on with one or more versions.
 */
class Addon extends DataObject 
{

	private static $db = [
		'Name' => 'Varchar(255)',
		'Description' => 'Text',
		'Type' => 'Varchar(100)',
		'Readme' => 'HTMLText',
		'Released' => 'SS_Datetime',
		'Repository' => 'Varchar(255)',
		'Downloads' => 'Int',
		'DownloadsMonthly' => 'Int',
		'Favers' => 'Int',
		'LastUpdated' => 'SS_Datetime',
		'LastBuilt' => 'SS_Datetime',
		'BuildQueued' => 'Boolean',
		'HelpfulRobotData' => 'Text',
		'HelpfulRobotScore' => 'Int',
	];

	private static $has_one = [
		'Vendor' => 'SilverStripe\Addons\Model\AddonVendor'
	];

	private static $has_many = [
		'Versions' => 'SilverStripe\Addons\Model\AddonVersion'
	];

	private static $many_many = [
		'Keywords' => 'SilverStripe\Addons\Model\AddonKeyword',
		'Screenshots' => 'SilverStripe\Assets\Image',
		'CompatibleVersions' => 'SilverStripe\Addons\Model\SilverStripeVersion'
	];

	private static $default_sort = 'Name';

	private static $extensions = [
		'SilverStripe\\Elastica\\Searchable'
	];

	/**
	 * Gets the addon's versions sorted from newest to oldest.
	 *
	 * @return ArrayList
	 */
	public function SortedVersions() 
	{
		$versions = $this->Versions()->toArray();

		usort($versions, function($a, $b) {
			return version_compare($b->Version, $a->Version);
		});

		return new ArrayList($versions);
	}

	public function MasterVersion() 
	{
		return $this->Versions()->filter('PrettyVersion', ['dev-master', 'trunk'])->First();
	}

	public function Authors() 
	{
		return $this->Versions()->relation('Authors');
	}

	public function VendorName() 
	{
		return substr($this->Name, 0, strpos($this->Name, '/'));
	}

	public function VendorLink() 
	{
		return Controller::join_links(
			Director::baseURL(), 'add-ons', $this->VendorName()
		);
	}

	public function PackageName() 
	{
		return substr($this->Name, strpos($this->Name, '/') + 1);
	}

	public function Link() 
	{
		return Controller::join_links(
			Director::baseURL(), 'add-ons', $this->Name
		);
	}

	public function DescriptionText() 
	{
		return $this->Description;
	}

	public function RSSTitle() 
	{
		return sprintf('New module release: %s', $this->Name);
	}

	public function PackagistUrl()
	{
		return "https://packagist.org/packages/$this->Name";
	}

	/**
	 * Remove the effect of code of conduct Helpful Robot measure that we currently don't include in the Supported module definition
	 *
	 * @return integer Adjusted Helpful Robot score
	 */
	public function getAdjustedHelpfulRobotScore()
	{
		return round(min(100, $this->HelpfulRobotScore / 92.9 * 100));
	}

	public function getElasticaMapping() 
	{
		return new Mapping(null, [
			'name' => ['type' => 'string'],
			'description' => ['type' => 'string'],
			'type' => ['type' => 'string'],
			'compatibility' => ['type' => 'string'],
			'vendor' => ['type' => 'string'],
			'tags' => ['type' => 'string'],
			'released' => ['type' => 'date'],
			'downloads' => ['type' => 'string'],
			'readme' => ['type' => 'string']
		]);
	}

	public function getElasticaDocument() 
	{
		return new Document($this->ID, [
			'name' => $this->Name,
			'description' => $this->Description,
			'type' => $this->Type,
			'compatibility' => $this->CompatibleVersions()->column('Name'),
			'vendor' => $this->VendorName(),
			'tags' => $this->Keywords()->column('Name'),
			'released' => $this->obj('Released')->Format('c'),
			'downloads' => (int) $this->Downloads,
			'readme' => strip_tags($this->Readme),
			'_boost' => sqrt($this->Downloads)
		]);
	}

	public function onBeforeDelete() 
	{
		parent::onBeforeDelete();

		// Partially cascade delete. Leave author and keywords in place,
		// since they might be related to other addons.
		foreach($this->Screenshots() as $image) {
			$image->delete();
		}
		$this->Screenshots()->removeAll();

		foreach($this->Versions() as $version) {
			$version->delete();
		}

		$this->Keywords()->removeAll();
		$this->CompatibleVersions()->removeAll();
	}

	public function getDateCreated() 
	{
		return date('Y-m-d', strtotime($this->Created));
	}

	/**
	 * @return array
	 */
	public function HelpfulRobotData()
	{
		$data = json_decode($this->HelpfulRobotData, true);

		return new ArrayData($data["inspections"][0]);
	}
}
