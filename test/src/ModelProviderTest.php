<?php

namespace Skip\Model\Test;

use Skip\Model\ModelProvider;
use Silex\Application;

class ModelProviderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * test model manager service is available
	 */
	public function testModelManagerServiceIsAvailable() {
		$app = new Application();
		$app->register(new ModelProvider());

		$modelManager = $app['model'];

		$this->assertInstanceOf('Skip\Model\Manager', $modelManager);
	}
}