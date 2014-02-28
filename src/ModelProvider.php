<?php

namespace Skip\Model;

use Silex\ServiceProviderInterface;
use Silex\Application;

/**
 * @todo explain
 */
class ModelProvider implements ServiceProviderInterface {

	public function register(Application $app) {
		$app['model'] = function(Application $app) {
			$manager = new Manager();
			$manager->setFinder($app['model.finder']);

			$storageHandlerServices = $app['model.storage.handlers'];

			if(!is_array($storageHandlerServices)) {
				throw new \Exception("Service 'model.storage.handlers' must contain an array of storage handlers");
			}

			if(!count($storageHandlerServices)) {
				throw new StorageHandlerNotFoundException("No storage handler service(s) found. Please register atleast one.");
			}

			foreach($storageHandlerServices as $storageHandlerName => $storageHandlerService) {
				if(is_string($storageHandlerService)) {
					$storageHandlerService = $app[$storageHandlerService];
				}
				$manager->setModelStorageHandler($storageHandlerService, $storageHandlerName);
			}

			$storageMap = isset($app['model.storage.handler.map']) ? $app['model.storage.handler.map'] : array();
			foreach($storageMap as $modelName => $handlerService) {
				$manager->setModelHandlerOverride($modelName, $app[$handlerService]);
			}

			return $manager;
		};

		$app['model.finder'] = function(Application $app) {
			$finder = new Finder\FileFinder();
			$finder->setNamespace($app['model.finder.namespace']);
			$finder->setBasePath($app['model.finder.path']);
			return $finder;
		};

	}

	public function boot(Application $app) {

	}
}