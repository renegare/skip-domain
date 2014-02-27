<?php

namespace Skip\Model\Test;

use Skip\Model\Manager;
use Skip\Model\ModelStorageHandlerInterface;

class ManagerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * test get method
	 */
	public function testGet() {
		$manager = new Manager();

		$mockModel = $this->getMock('Skip\Model\ModelInterface', array('setStorageHandler'), array(), '', FALSE);
		$mockModelClass = get_class($mockModel);
		$mockFinder = $this->getMock('Skip\Model\ModelFinderInterface', array('getModelClassList'), array(), '', FALSE);
		$mockFinder->expects($this->once())
			->method('getModelClassList')
			->will($this->returnValue(array('model.class.a' => $mockModelClass)));
		$manager->setFinder($mockFinder);

		$mockStorageHandler = $this->getMock('Skip\Model\ModelStorageHandlerInterface', array(), array(), '', FALSE);
		$manager->setDefaultStorageHandler($mockStorageHandler);

		$modelA = $manager->get('model.class.a');

		$this->assertInstanceOf($mockModelClass, $modelA);

		$modelB = $manager->get('model.class.a');

		$this->assertSame($modelA, $modelB);
	}

	/**
	 * test get method model with multiple storage handlers
	 */
	public function testGetModelsWithMultipleStorageHandlers() {

		$mockStorageHandlerDefault = $this->getMock('Skip\Model\ModelStorageHandlerInterface');
		$mockStorageHandlerCustom = $this->getMock('Skip\Model\ModelStorageHandlerInterface');
		$this->assertNotSame($mockStorageHandlerDefault, $mockStorageHandlerCustom);

		$mockModelA = $this->getMock('Skip\Model\ModelInterface', array('setStorageHandler'), array(), '', FALSE);
		$mockModelA->expects($this->once())
			->method('setStorageHandler')
			->will($this->returnCallback(function(ModelStorageHandlerInterface $handler) use ($mockStorageHandlerDefault) {
				$this->assertSame($mockStorageHandlerDefault, $handler);
			}));

		$mockModelB = $this->getMock('Skip\Model\ModelInterface', array('setStorageHandler'), array(), '', FALSE);
		$mockModelB->expects($this->once())
			->method('setStorageHandler')
			->will($this->returnCallback(function(ModelStorageHandlerInterface $handler) use ($mockStorageHandlerCustom) {
				$this->assertSame($mockStorageHandlerCustom, $handler);
			}));

		$mockModelClassA = get_class($mockModelA);
		$mockModelClassB = get_class($mockModelB);

		$this->assertNotSame($mockModelA, $mockModelB);

		$mockModels = array($mockModelA, $mockModelB);

		$manager = $this->getMock('Skip\Model\Manager', array('loadModel'), array(), '', TRUE);
		$manager->expects($this->any())
			->method('loadModel')
			->will($this->returnCallback(function($class) use (&$mockModels) {
				$model = array_shift($mockModels);
				return $model;
			}));

		$mockFinder = $this->getMock('Skip\Model\ModelFinderInterface', array('getModelClassList'), array(), '', FALSE);
		$mockFinder->expects($this->once())
			->method('getModelClassList')
			->will($this->returnValue(array(
				'model.class.a' => $mockModelClassA,
				'model.class.b' => $mockModelClassB)));
		$manager->setFinder($mockFinder);

		$manager->setModelStorageHandler($mockStorageHandlerDefault);
		$manager->setModelStorageHandler($mockStorageHandlerCustom, 'storage.handler.b');

		$manager->setModelToStorageHandlerMap(array(
			'model.class.b' => 'storage.handler.b'
			));

		$modelA = $manager->get('model.class.a');
		$modelB = $manager->get('model.class.b');

		$this->assertNotSame($modelA, $modelB);
	}

	/**
	 * test get method model for unregisterd model
	 * @expectedException Skip\Model\ModelNotFoundException
	 */
	public function testGetInvalidModel() {
		$manager = new Manager();

		$mockFinder = $this->getMock('Skip\Model\ModelFinderInterface', array('getModelClassList'), array(), '', FALSE);
		$mockFinder->expects($this->once())
			->method('getModelClassList')
			->will($this->returnValue(array()));
		$manager->setFinder($mockFinder);

		$manager->get('unknown.model');
	}

	/**
	 * test get method model for unregisterd model
	 * @expectedException Skip\Model\StorageHandlerNotFoundException
	 */
	public function testGetModelWithNoStorageHandler() {
		$manager = new Manager();

		$mockModel = $this->getMock('Skip\Model\ModelInterface', array('setStorageHandler'), array(), '', FALSE);
		$mockModelClass = get_class($mockModel);
		$mockFinder = $this->getMock('Skip\Model\ModelFinderInterface', array('getModelClassList'), array(), '', FALSE);
		$mockFinder->expects($this->once())
			->method('getModelClassList')
			->will($this->returnValue(array('model.class.a' => $mockModelClass)));
		$manager->setFinder($mockFinder);

		$manager->get('model.class.a');
	}
}