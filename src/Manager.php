<?php

namespace Skip\Model;

/**
 * @todo explain
 */
class Manager {

	/** @var ModelStorageHandlerInterface */
	protected $defaultStorageHandler;

	/** @var ModelStorageHandlerInterface */
	protected $finder;

	/** 
	 * @var array of ModelInterface 
	 * @todo make this a proper typed array class
	 */
	protected $models = array();

	/**
	 * @todo explain
	 */
	public function setDefaultStorageHandler(ModelStorageHandlerInterface $handler) {
		$this->defaultStorageHandler = $handler;
	}

	/**
	 * @todo explain
	 */
	public function setFinder(ModelFinderInterface $finder) {
		$this->finder = $finder;
	}

	/**
	 * @todo explain
	 */
	public function getModelClass($modelName) {
		$classlist = $this->finder->getModelClassList();
		return $classlist[$modelName];
	}

	/**
	 * @todo explain
	 */
	public function get($modelName) {

		if(!isset($this->models[$modelName])) {
			$class = $this->getModelClass($modelName);

			$model = new $class();
			$model->setStorageHandler($this->defaultStorageHandler);

			$this->models[$modelName] = $model;
		} else {
			$model = $this->models[$modelName];
		}

		return $model;
	}
}