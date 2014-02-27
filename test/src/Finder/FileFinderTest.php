<?php

namespace Skip\Model\Test\Finder;

use Skip\Model\Finder\FileFinder;

class FileFinderTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * test getModelClassList method
	 */
	public function testGetModelClassList() {
		$mockPath = '/mock-existent-path';
		$mockClassList = array(
			'model.class.a' => 'Mock\Namespace\ModelClassA',
			'model.class.b' => 'Mock\Namespace\ModelClassB',
			'model.class.c' => 'Mock\Namespace\ModelClassC'
			);
		$mockFileList = array(
			new \SplFileInfo($mockPath . '/ModelClassA'),
			new \SplFileInfo($mockPath . '/ModelClassB'),
			new \SplFileInfo($mockPath . '/ModelClassC')
			);
		$mockIterator = new \ArrayIterator($mockFileList);

		$finder = new FileFinder();
		$finder->setNamespace('Mock\Namespace');
		$finder->setBasePath($mockPath);

		$symfonyFinder = $this->getMock('Symfony\Component\Finder\Finder', array(
			'files', 
			'in', 
			'name',
			'getIterator'), array(), '', FALSE);

		$symfonyFinder->expects($this->once())
			->method('files')
			->will($this->returnValue($symfonyFinder));

		$symfonyFinder->expects($this->once())
			->method('in')
			->will($this->returnCallback(function($basePath) use ($symfonyFinder) {
				$this->assertEquals('/mock-existent-path', $basePath);
				return $symfonyFinder;
			}));

		$symfonyFinder->expects($this->once())
			->method('name')
			->will($this->returnCallback(function($filter) use ($symfonyFinder) {
				$this->assertEquals('*.php', $filter);
				return $symfonyFinder;
			}));

		$symfonyFinder->expects($this->once())
			->method('getIterator')
			->will($this->returnValue($mockIterator));

		$finder->setFileFinder($symfonyFinder);


		$classList = $finder->getModelClassList();

		$this->assertEquals($mockIterator->count(), count($classList));
		foreach($mockClassList as $key => $classPath) {
			$this->assertArrayHasKey($key, $classList);
			$this->assertEquals($classPath, $classList[$key]);
		}
	}
}