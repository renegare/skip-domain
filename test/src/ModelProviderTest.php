<?php

namespace Skip\Model\Test;

use Skip\Model\ModelProvider;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

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

		
		$request = Request::createFromGlobals();
        $response = $app->handle($request);
        $app->terminate($request, $response);
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

	/**
	 * test model storage handler can an externallu configured service
	 */
	public function testExternalServiceAsStorageHandler() {
		$app = new Application();
		$app->register(new ModelProvider(), array(
				'model.finder.namespace' => 'Fake\NameSpace',
				'model.finder.path' => '/tmp/fake'
			));
		
		$app['model.storage.handlers'] = array('mock.storage' => 'external.service.name');

		$app['external.service.name'] = function() {
			$mockStorage = $this->getMock('Skip\Model\ModelStorageHandlerInterface');
			$this->assertTrue(true);
			return $mockStorage;
		};

		$modelManager = $app['model'];

		$this->assertInstanceOf('Skip\Model\Manager', $modelManager);
	}

	/**
	 * test model storage handler map is set
	 */
	public function testStorageHandlerMapIsSet() {
		$app = new Application();
		$app->register(new ModelProvider(), array(
				'model.finder.namespace' => 'Fake\NameSpace',
				'model.finder.path' => '/tmp/fake'
			));

		$mockStorage = $this->getMock('Skip\Model\ModelStorageHandlerInterface');
		$app['model.storage.handlers'] = array(
			'mock.storage.a' => $mockStorage,
			'mock.storage.b' => $mockStorage);

		$app['model.storage.handler.map'] = array(
			'model.a' => 'mock.storage.a',
			'model.b' => 'mock.storage.b');

		$modelManager = $app['model'];

		$this->assertInstanceOf('Skip\Model\Manager', $modelManager);
	}

	/**
	 * test exception when 'model.storage.handlers' has NOT been set with an array
	 * @expectedException InvalidArgumentException
	 */
	public function testStorageHandlersNotArrayException() {
		$app = new Application();
		$app->register(new ModelProvider(), array(
				'model.finder.namespace' => 'Fake\NameSpace',
				'model.finder.path' => '/tmp/fake'
			));

		$mockStorage = $this->getMock('Skip\Model\ModelStorageHandlerInterface');
		$app['model.storage.handlers'] = $mockStorage;

		$modelManager = $app['model'];

		$this->assertInstanceOf('Skip\Model\Manager', $modelManager);
	}

	/**
	 * test exception when 'model.storage.handlers' has been set with an empty array
	 * @expectedException InvalidArgumentException
	 */
	public function testStorageHandlersEmptyArrayException() {
		$app = new Application();
		$app->register(new ModelProvider(), array(
				'model.finder.namespace' => 'Fake\NameSpace',
				'model.finder.path' => '/tmp/fake'
			));

		$app['model.storage.handlers'] = array();

		$modelManager = $app['model'];

		$this->assertInstanceOf('Skip\Model\Manager', $modelManager);
	}

}