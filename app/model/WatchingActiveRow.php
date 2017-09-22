<?php
namespace JanDrabek\Database;

use Nette\ArrayHash;
use Nette\Database\Table\ActiveRow;

class WatchingActiveRow extends ActiveRow {

	private $tempModified = array();

	public function __set($key, $value) {
		$this->tempModified[$key] = $value;
	}

	public function addAll($values) {
		if(!is_array($values) && !($values instanceof ArrayHash)) {
			return;
		}
		foreach($values as $key => $value) {
			$this->__set($key, $value);
		}
	}

	public function getModified() {
		return $this->tempModified;
	}

	public static function fromActiveRow($row) {
		if(!($row instanceof ActiveRow)) {
			return false;
		}
		return new self($row->toArray(), $row->getTable());
	}
	public function getCurrentToArray() {
		return array_merge($this->toArray(), $this->getModified());
	}
}
