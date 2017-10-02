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
		if(empty($eventId)) {
			return $this->findAll()->where('event_id IS NULL')->fetchAll();
		} else {
			return $this->findAll()->where('event_id = ?', $eventId)->fetchAll();
		}
	}
	public function findByUrl($url) {
		return $this->findAll()->where('url', $url)->fetchAll();
	}

	public function findDefaultInEvent($eventId) {
		return $this->findAll()->where('event_id = ? AND is_default = 1', $eventId)->fetch();
	}

	public function findDefaultNotInEvent() {
		return $this->findAll()->where('event_id IS NULL AND is_default = 1')->fetch();
	}

	public function findGalleries()
	{
		return $this->findAll()->where('gallery_meta')->order('gallery_meta_weight ASC');
	}

	public function findByUrlAndEventId($url, $eventId) {
		if(empty($eventId)) {
			return $this->findAll()->where('event_id IS NULL AND url = ?', $url)->fetch();
		} else {
			return $this->findAll()->where('event_id = ? AND url = ?', $eventId, $url)->fetch();
		}
	}

	public function moveGalleryUp($pageId)
	{
		$this->getConnection()->query('UPDATE page SET gallery_meta_weight = gallery_meta_weight - 1 WHERE page_id = ?', $pageId);
	}
	public function moveGalleryDown($pageId)
	{
		$this->getConnection()->query('UPDATE page SET gallery_meta_weight = gallery_meta_weight + 1 WHERE page_id = ?', $pageId);
	}
}