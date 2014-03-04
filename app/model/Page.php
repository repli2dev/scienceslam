<?php
namespace Muni\ScienceSlam\Model;

use JanDrabek\Database\DAO;
use JanDrabek\Database\WatchingActiveRow;
use Nette\Database\Table\ActiveRow;

class Page extends DAO {

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

	public function findByEventId($eventId) {
		return $this->findAll()->where('event_id = ?', $eventId)->fetchAll();
	}

	public function findDefaultInEvent($eventId) {
		return $this->findAll()->where('event_id = ? AND is_default = 1', $eventId)->fetch();
	}

	public function findByUrlAndEventId($url, $eventId) {
		return $this->findAll()->where('event_id = ? AND url = ?', $eventId, $url)->fetch();
	}

}