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
	 * @var array of ModelInterface in the form of 'model.name' => $instance
	 * @todo make this a proper typed array class
	 */
	protected $models = array();

	/** 
	 * @var array of ModelStorageHandlerInterface in the form of 'storage.handler.name' => $instance
	 * @todo make this a proper typed array class
	 */
	protected $modelStorageHandlers = array();

	/** 
	 * @var array of key pair stings 'model.name' => 'storage.handler.name'
	 * @todo make this a proper typed array class
	 */
	protected $modelToStorageHandlerMap = array();

	/** @var array of key pair stings 'model.name' => 'Full\Namespaced\Model\Class' */
	protected $modelClassList;

	/** @var int used to create unique name for every registered anonymous storage handler */
	protected $anonymousHandlerCount = 0;

	/**
	 * @todo explain
	 */
	public function setFinder(ModelFinderInterface $finder) {
		$this->finder = $finder;
	}

	/**
	 * @throws Exception when the requested model name does not exist
	 * @todo explain and better exception
	 */
	public function getModelClass($modelName) {

		if(!$this->modelClassList) {
			$this->modelClassList = $this->finder->getModelClassList();
		}

		if(!isset($this->modelClassList[$modelName])) {
			throw new \Exception(sprintf('No model found registered under the name %s', $modelName));
		}

		return $this->modelClassList[$modelName];
	}

	public function loadModel($class) {
		return new $class;
	}

	/**
	 * get a preconfiguered model 
	 * Note: setStorageHandler is called on the Model every time it is requested. Seems like a good idea to allow
	 * for storage handler to be updated during runtime.
	 * @todo explain
	 */
	public function get($modelName) {

		if(!isset($this->models[$modelName])) {
			$class = $this->getModelClass($modelName);
			$this->models[$modelName] = $this->loadModel($class);
		}

		$model = $this->models[$modelName];
		$model->setStorageHandler($this->getModelsStorageHandler($modelName));

		return $model;
	}

	/**
	 * @todo explain
	 */
	public function setModelStorageHandler(ModelStorageHandlerInterface $handler, $storageName = null) {
		if(!$storageName) {
			$this->anonymousHandlerCount += 1;
			$storageName = 'storage.handler.' . $this->anonymousHandlerCount;
		}
		
		if(!$this->defaultStorageHandler) {
			$this->setDefaultStorageHandler($handler);
		}

		$this->modelStorageHandlers[$storageName] = $handler;
	}

	/**
	 * @todo explain
	 */
	public function setDefaultStorageHandler(ModelStorageHandlerInterface $handler) {
		$this->defaultStorageHandler = $handler;
	}

	/**
	 * @todo explain
	 */
	public function setModelToStorageHandlerMap(array $map) {
		$this->modelToStorageHandlerMap = $map;
	}

	/**
	 * @throws Exception when handler cannot be found
	 * @todo explain
	 */
	public function getModelsStorageHandler($modelName) {
		$handler = $this->defaultStorageHandler;
		if(isset($this->modelToStorageHandlerMap[$modelName])) {
			$handlerName = $this->modelToStorageHandlerMap[$modelName];
			$handler = $this->modelStorageHandlers[$handlerName];
		}

		if(!$handler) {
			throw new \Exception(sprintf("No storage handler can be found for the model '%s'", $modelName));
		}

		return $handler;
	}


}