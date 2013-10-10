<?php
namespace JanDrabek\Database;

use Nette\ArrayHash;
use Nette\Database\Table\ActiveRow;

class WatchingActiveRow extends ActiveRow {

	private $tempModified = array();

	public function __set($key, $value) {
		$this->tempModified[$key] = $value;
		parent::__set($key, $value);
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

	public static function fromActiveRow(ActiveRow $row) {
		return new self($row->toArray(), $row->getTable());
	}
}