<?php

namespace Skip\Model\Finder;

use Symfony\Component\Finder\Finder;

class FileFinder implements \Skip\Model\ModelFinderInterface {

	/** @var Finder */
	protected $finder;

	/** @var string */
	protected $namespace;

	/** @var string */
	protected $basePath;

	public function __construct() {
		$this->setFileFinder(new Finder());
	}

	/**
	 * set finder instance. Useful in testing for mocking out finder instance.
	 * @param Finder $finder
	 * @return null
	 */
	public function setFileFinder(Finder $finder) {
		$finder->files();
		$finder->name('*.php');
		$this->finder = $finder;
	}

	/**
	 * Set found class namespace
	 * @param string $namespace
	 * @return null
	 */
	public function setNamespace($namespace) {
		$this->namespace = $namespace;
	}

	/**
	 * Set class files basePath location
	 * @param string $basePath
	 * @return null
	 */
	public function setBasePath($basePath) {
		$this->basePath = $basePath;
	}

	/**
	 * @see Skip\ModelFinderInterface::GetModelClassList
	 */
	public function getModelClassList() {
		$finder = $this->finder;
		$finder->in($this->basePath);

		$modelMap = array();

		foreach($finder as $file) {
			$class = $this->namespace . '\\' . $file->getBasename('.php');
			$modelName = $file->getBasename('.php');
			$modelName = preg_split('/(?=[A-Z])/', $modelName);
			array_shift($modelName);
			$modelName = strtolower(implode('.', $modelName));
			$modelMap[$modelName] = $class;
		}
		
		return $modelMap;
	}
}