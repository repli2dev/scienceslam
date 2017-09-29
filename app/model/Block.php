<?php
namespace Muni\ScienceSlam\Model;

use JanDrabek\Database\DAO;
use JanDrabek\Database\WatchingActiveRow;
use Nette\Database\Table\ActiveRow;

class Block extends DAO {

	/** Saves given objects into database (peforms insert or update)
	 * @param ActiveRow $object
	 * @return mixed Value of primary key (new or updated)
	 */
	public function save(ActiveRow $object) {
		if($object instanceof WatchingActiveRow) {
			$data = $object->getModified();
		} else {
			$data = $object->toArray();
		}
		$key = $object->getPrimary(false);
		if(empty($key)) { // Insert
			return $this->getTable()->insert($data);
		} else { // Update
			return $this->wherePrimary($this->getTable(), $key)->update($data);
		}
	}

	public function findByPageId($pageId) {
		return $this->findAll()->where('page_id = ?', $pageId)->order('weight ASC')->fetchAll();
	}

	public function toggle($blockId)
	{
		$this->getConnection()->query('UPDATE block SET hidden = NOT hidden WHERE block_id = ?', $blockId);
	}

	public function moveUp($blockId)
	{
		$this->getConnection()->query('UPDATE block SET weight = weight - 1 WHERE block_id = ?', $blockId);
	}
	public function moveDown($blockId)
	{
		$this->getConnection()->query('UPDATE block SET weight = weight + 1 WHERE block_id = ?', $blockId);
	}
}