<?php

namespace SilverStripe\Addons\Controllers;

/**
 * Renders custom error pages when an error response is returned.
 */
class SiteErrorPageFilter 
{

	public function postRequest($request, $response) 
	{
		if($response->getStatusCode() == 404) {
			$controller = new SiteController();
			$controller = $controller->customise(array('Title' => 'Page Not Found'));
			$body = $controller->renderWith(array('ErrorPage_404', 'ErrorPage', 'Page'));

			$response->addHeader('Content-Type', 'text/html');
			$response->setBody($body);
		}
	}

}
