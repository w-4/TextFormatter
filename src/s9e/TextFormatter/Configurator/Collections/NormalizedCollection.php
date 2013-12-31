<?php

/**
* @package   s9e\TextFormatter
* @copyright Copyright (c) 2010-2013 The s9e Authors
* @license   http://www.opensource.org/licenses/mit-license.php The MIT License
*/
namespace s9e\TextFormatter\Configurator\Collections;

use ArrayAccess;
use InvalidArgumentException;
use RuntimeException;

class NormalizedCollection extends Collection implements ArrayAccess
{
	/**
	* @var string Action to take when add() is called with a key that already exists
	*/
	protected $onDuplicateAction = 'error';

	/**
	* Query and set the action to take when add() is called with a key that already exists
	*
	* @param  string $action Either "error", "ignore" or "replace"
	* @return string         Old action
	*/
	public function onDuplicate($action = null)
	{
		// Save the old action so it can be returned
		$old = $this->onDuplicateAction;

		if (func_num_args() && $action !== 'error' && $action !== 'ignore' && $action !== 'replace')
		{
			throw new InvalidArgumentException("Invalid onDuplicate action '" . $action . "'. Expected: 'error', 'ignore' or 'replace'");
		}

		$this->onDuplicateAction = $action;

		return $old;
	}

	//==========================================================================
	// Overridable methods
	//==========================================================================

	/**
	* Normalize an item's key
	*
	* This method can be overridden to implement keys normalization or implement constraints
	*
	* @param  string $key Original key
	* @return string      Normalized key
	*/
	public function normalizeKey($key)
	{
		return $key;
	}

	/**
	* Normalize a value for storage
	*
	* This method can be overridden to implement value normalization
	*
	* @param  mixed $value Original value
	* @return mixed        Normalized value
	*/
	public function normalizeValue($value)
	{
		return $value;
	}

	//==========================================================================
	// Items access/manipulation
	//==========================================================================

	/**
	* Add an item to this collection
	*
	* NOTE: relies on exists() to check the key for invalid values and on set() to normalize it
	*
	* @param  string $key   Item's key
	* @param  mixed  $value Item's value
	* @return mixed         Normalized value
	*/
	public function add($key, $value = null)
	{
		// Test whether this key is already in use
		if ($this->exists($key))
		{
			// If the action is "ignore" we return the old value, if it's "error" we throw an
			// exception. Otherwise, we keep going and replace the value
			if ($this->onDuplicateAction === 'ignore')
			{
				return $this->get($key);
			}
			elseif ($this->onDuplicateAction === 'error')
			{
				throw new RuntimeException("Item '" . $key . "' already exists");
			}
		}

		return $this->set($key, $value);
	}

	/**
	* Test whether a given value is present in this collection
	*
	* @param  mixed $value Original value
	* @return bool         Whether the normalized value was found in this collection
	*/
	public function contains($value)
	{
		return in_array($this->normalizeValue($value), $this->items);
	}

	/**
	* Delete an item from this collection
	*
	* @param  string $key Item's key
	* @return void
	*/
	public function delete($key)
	{
		$key = $this->normalizeKey($key);

		unset($this->items[$key]);
	}

	/**
	* Test whether an item of given key exists
	*
	* @param  string $key Item's key
	* @return bool        Whether this key exists in this collection
	*/
	public function exists($key)
	{
		$key = $this->normalizeKey($key);

		return array_key_exists($key, $this->items);
	}

	/**
	* Return a value from this collection
	*
	* @param  string $key Item's key
	* @return mixed       Normalized value
	*/
	public function get($key)
	{
		if (!$this->exists($key))
		{
			throw new RuntimeException("Item '" . $key . "' does not exist");
		}

		$key = $this->normalizeKey($key);

		return $this->items[$key];
	}

	/**
	* Find the index of a given value
	*
	* Will return the first key associated with the given value, or FALSE if the value is not found
	*
	* @param  mixed $value Original value
	* @return mixed        Index of the value, or FALSE if not found
	*/
	public function indexOf($value)
	{
		return array_search($this->normalizeValue($value), $this->items);
	}

	/**
	* Set and overwrite a value in this collection
	*
	* @param  string $key   Item's key
	* @param  mixed  $value Item's value
	* @return mixed         Normalized value
	*/
	public function set($key, $value)
	{
		$key = $this->normalizeKey($key);

		$this->items[$key] = $this->normalizeValue($value);

		return $this->items[$key];
	}

	//==========================================================================
	// ArrayAccess stuff
	//==========================================================================

	public function offsetExists($offset)
	{
		return $this->exists($offset);
	}

	public function offsetGet($offset)
	{
		return $this->get($offset);
	}

	public function offsetSet($offset, $value)
	{
		$this->set($offset, $value);
	}

	public function offsetUnset($offset)
	{
		$this->delete($offset);
	}
}