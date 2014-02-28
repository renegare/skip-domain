<?php

namespace Skip\Model\Test;

use Skip\Model\ModelProvider;
use Silex\Application;

class ModelProviderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * test model service is available
	 */
	public function testModelManagerServiceIsAvailable() {
		$app = new Application();
		$app->register(new ModelProvider(), array(
				'model.finder.namespace' => 'Fake\NameSpace',
				'model.finder.path' => '/tmp/fake'
			));
		
		$mockStorage = $this->getMock('Skip\Model\ModelStorageHandlerInterface');
		$app['model.storage.handlers'] = array('mock.storage' => $mockStorage);

		$modelManager = $app['model'];

		$this->assertInstanceOf('Skip\Model\Manager', $modelManager);
	}

	/**
	 * test model.finder service is available
	 */
	public function testModelFinderServiceIsAvailable() {
		$app = new Application();
		$app->register(new ModelProvider(), array(
				'model.finder.namespace' => 'Fake\NameSpace',
				'model.finder.path' => '/tmp/fake'
			));

		$finder = $app['model.finder'];

		$this->assertInstanceOf('Skip\Model\ModelFinderInterface', $finder);
	}

	/**
	 * test model service can find a model!
	 */
	public function testModelServiceCanFindAModel() {
		$app = new Application();
		$app->register(new ModelProvider(), array(
				'model.finder.namespace' => 'Fake\NameSpace',
				'model.finder.path' => '/tmp/fake'
			));
		
		$mockModel = $this->getMock('Skip\Model\ModelInterface', array('setStorageHandler'), array(), '', FALSE);
		$mockModelClass = get_class($mockModel);
		$mockFinder = $this->getMock('Skip\Model\ModelFinderInterface', array('getModelClassList'), array(), '', FALSE);
		$mockFinder->expects($this->once())
			->method('getModelClassList')
			->will($this->returnValue(array('a.model' => $mockModelClass)));
		$app['model.finder'] = $mockFinder;

		$mockStorage = $this->getMock('Skip\Model\ModelStorageHandlerInterface');
		$app['model.storage.handlers'] = array('mock.storage' => $mockStorage);

		$this->assertInstanceOf('Skip\Model\ModelInterface', $app['model']->get('a.model'));
	}
}