<?php

namespace SilverStripe\Addons\Admin;

use SilverStripe\Admin\ModelAdmin;

/**
 * A basic interface for managing add-ons.
 */
class AddonsAdmin extends ModelAdmin 
{

	public static $title = 'Add-ons';

	public static $url_segment = 'add-ons';

	public static $managed_models = [
		'Addon',
		'AddonVendor',
		'AddonAuthor'
	];

	public static $model_importers = [];

}
