<?php

namespace JanDrabek\Database;

use Nette\Database\Connection;
use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\InvalidArgumentException;
use Nette\Object;
use Nette\Utils\Validators;

abstract class DAO extends Object {

	protected $tableName; // Can override name based on class name
	protected $primary;

	/** @var Context */
	private $context;

	public function __construct(Context $context) {
		$this->context = $context;
	}

	public function create() {
		return new WatchingActiveRow(array(), $this->getTable());
	}

	/** Saves given objects into database (peforms insert or update)
	 * @param ActiveRow $object
	 * @return ActiveRow or FALSE in case of an error
	 */
	abstract public function save(ActiveRow $object);

	/**
	 * Delete records according to value of primary key
	 * @param $primary
	 * @return Selection
	 */
	public function delete($key) {
		return $this->wherePrimary($this->getTable(), $key)->delete();
	}

	/**
	 * Returns records according to value of primary key
	 * @param string|mixed
	 * @return Selection
	 */
	public function find($key) {
		return $this->wherePrimary($this->getTable(), $key)->fetch();
	}

	/**
	 * Return all records
	 * @return Selection
	 */
	public function findAll() {
		return $this->getTable();
	}

	/**
	 * Adds condition for primary key.
	 * @param  mixed
	 * @return Selection provides a fluent interface
	 */
	protected function wherePrimary(Selection $table, $key)
	{
		$primaryColumns = $this->getPrimary();
		if (is_array($primaryColumns) && Validators::isList($key)) {
			foreach ($primaryColumns as $i => $primary) {
				$table->where($primary, $key[$i]);
			}
		} elseif (is_array($key)) { // key contains column names
			$table->where($key);
		} else {
			$table->where($primaryColumns, $key);
		}

		return $table;
	}

	protected function getName() {
		if(!empty($this->tableName)) {
			return $this->tableName;
		}
		$class = new \ReflectionClass($this);
		$ns = $class->getNamespaceName();
		$name = $class->getName();
		$name = substr($name, strlen($ns)+1, strlen($name));
		$this->tableName = $this->fromCamelCase($name);
		return $this->tableName;
	}

	/**
	 * Returns primary column(s) of table
	 * @return array|string
	 */
	protected function getPrimary() {
		if(empty($this->primary)) {
			$this->primary = $this->getTable()->getPrimary();
		}
		return $this->primary;
	}

	/**
	 * Returns table selection
	 * @return Selection
	 */
	protected function getTable() {
		return $this->context->table($this->getName());
	}

	protected function getConnection() {
		return $this->context->getConnection();
	}

	/**
	 * Returns table_name from TableName
	 * @param $str Input string in camel case
	 * @return mixed Output string in underscore syntax
	 */
	private function fromCamelCase($str) {
		$str[0] = strtolower($str[0]);
		$func = create_function('$c', 'return "_" . Nette\Utils\Strings::lower($c[1]);');
		return preg_replace_callback('/([A-Z])/', $func, $str);
	}
}