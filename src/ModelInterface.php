<?php

namespace Skip\Model;

/**
 * @todo explain
 */
interface ModelInterface {

	/**
	 * Set the storage handler that model ought to use
	 * @param ModelStorageHandlerInterface $handler
	 */
	public function setStorageHandler(ModelStorageHandlerInterface $handler);
}