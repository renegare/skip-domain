<?php

namespace Skip\Model\Test\Finder;

use Skip\Model\Finder\FileFinder;

class FileFinderTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * test getModelClassList method
	 */
	public function testGetModelClassList() {
		$mockClassList = array(
			'Mock\Namespace\ModelClassA',
			'Mock\Namespace\ModelClassB',
			'Mock\Namespace\ModelClassC'
			);
		$mockIterator = new \ArrayIterator($mockClassList);

		$finder = new FileFinder();
		$finder->setNamespace('Mock\Namespace');

		$symfonyFinder = $this->getMock('Symfony\Component\Finder\Finder');

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

		$finder = $finder->setFileFinder($symfonyFinder);

		$finder->setBasePath('/mock-existent-path');

		$classList = $finder->getModelClassList();

		$this->assertEquals($mockIterator->count(), count($classList));
		foreach($classList as $file) {
			$this->assertTrue(in_array($file, $mockClassList));
		}
	}
}