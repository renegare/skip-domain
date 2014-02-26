<?php

namespace Skip\Model\Test;

use Skip\Model\Manager;

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
}