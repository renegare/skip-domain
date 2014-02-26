<?php

namespace Lib\Model;

/**
 * @todo explain
 */
interface ModelFinderInterface {

	/**
	 * Set the storage handler that model ought to use
	 * @return array of 'modelName' => 'Fully\Qualified\Class\Name' key pairs
	 */
	public function getModelClassList();
}