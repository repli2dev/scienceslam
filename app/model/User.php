<?php
namespace Muni\ScienceSlam\Model;

use JanDrabek\Database\DAO;
use JanDrabek\Database\WatchingActiveRow;
use Nette\Database\Table\ActiveRow;

class User extends DAO {

	const ROLE_ADMIN = 'ADMIN';
	const ROLE_MANAGER = 'MANAGER';
	const ROLE_GUEST = 'GUEST';

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

	public function findByNickname($nickname) {
		return $this->findAll()->where('nickname = ?', $nickname)->fetch();
	}

	public static function getRoles() {
		return array(
			self::ROLE_MANAGER => 'Manažer',
			self::ROLE_ADMIN => 'Administrátor',
			self::ROLE_GUEST => 'Host (= zakázaný)'
		);
	}

}