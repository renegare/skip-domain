<?php

namespace Skip\Model;

use Silex\ServiceProviderInterface;
use Silex\Application;

/**
 * @todo explain
 */
class ModelProvider implements ServiceProviderInterface {

	public function register(Application $app) {
		$app['model'] = function(Application $app) {
			return new Manager();
		};
	}

	public function boot(Application $app) {

	}
}