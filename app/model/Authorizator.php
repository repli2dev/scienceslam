<?php
namespace Muni\ScienceSlam\Model;

use Nette\Object;
use Nette\Security\IAuthorizator;
use Nette\Security\Permission;

class Authorizator extends Object implements IAuthorizator {
	/** @var Permission */
	private $acl;
	public function __construct(){
		$this->acl = new Permission();

		// Add resources
		$this->acl->addResource("user-shared");
		$this->acl->addResource("user");
		$this->acl->addResource("page");
		$this->acl->addResource("slam");
		$this->acl->addResource("registration");

		// Adding of user's role
		$this->acl->addRole('guest');
		$this->acl->addRole('manager');
		$this->acl->addRole('admin');

		// Settings of allowed/denied resources
		$this->acl->allow('admin','page', Permission::ALL);
		$this->acl->allow('admin','user', Permission::ALL);
		$this->acl->allow('admin','user-shared', Permission::ALL);
		$this->acl->allow('admin','page', Permission::ALL);
		$this->acl->allow('admin','slam', Permission::ALL);

		$this->acl->allow('manager','page', Permission::ALL);
		$this->acl->allow('manager','user-shared', Permission::ALL);
		$this->acl->allow('manager','page', Permission::ALL);
		$this->acl->allow('manager','slam', Permission::ALL);

		$this->acl->deny('guest');
	}

	public function isAllowed($role = self::ALL, $resource = self::ALL, $privilege = self::ALL) {
		if(!$this->acl->hasResource($resource)) {
			return self::DENY;
		}
		return $this->acl->isAllowed($role, $resource, $privilege);
	}


}