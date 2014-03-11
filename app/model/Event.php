<?php
namespace Muni\ScienceSlam\Model;

use JanDrabek\Database\DAO;
use JanDrabek\Database\WatchingActiveRow;
use Nette\Database\Table\ActiveRow;

class Event extends DAO {

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

	public function findByUrl($url) {
		if(empty($url)) {
			return FALSE;
		}
		return $this->findAll()->where('url = ?', $url)->fetch();
	}
	public function findWithOpenedRegistration() {
		return $this->findAll()->where('NOW() BETWEEN registration_opened AND registration_closed')->fetch();
	}

}