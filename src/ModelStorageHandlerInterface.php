<?php

namespace Skip\Model;

/**
 * The intensions of this interface is to force concrete classes to support at the very least a subset of
 * highly generic read/write methods, namely Create, Read, Update and Delete (CRUD).
 * 
 * Additional methods can be implemented but they are specific to that super concrete|abstract|interface type.
 */
interface ModelStorageHandlerInterface {

	/**
	 * get the count of records matching the defined options
	 * @param array $filter search|filter params
	 * @param array $options unspecified args specific to the storage handler (e.g table, limit, start)
	 * @return int a count of items
	 */
	public function count(array $filter = array(), array $options = array());

	/**
	 * create a record
	 * @param array $data to create with
	 * @param array $options unspecified args specific to the storage handler (e.g table, limit, start)
	 * @return int $id of newly created object
	 */
	public function create(array $data, array $options = array());

	/**
	 * get matching the first matching record
	 * @param array $filter search|filter params
	 * @param array $options unspecified args specific to the storage handler (e.g table, limit, start)
	 * @return array a single item which is an array of data for that specific item
	 */
	public function get(array $filter = array(), array $options = array());

	/**
	 * update matching records
	 * @param array $data to update with
	 * @param array $filter search|filter params
	 * @param array $options unspecified args specific to the storage handler (e.g table, limit, start)
	 * @return bool true if successful, false otherwise
	 */
	public function update(array $data, array $filter = array(), array $options = array());

	/**
	 * delete matching records
	 * @param array $filter search|filter params
	 * @param array $options unspecified args specific to the storage handler (e.g table, limit, start)
	 * @return bool true if successful, false otherwise
	 */
	public function delete(array $filter = array(), array $options = array());
}