<?php
namespace Muni\ScienceSlam\Model;

use JanDrabek\Database\WatchingActiveRow;
use Nette\DateTime;
use Nette\InvalidStateException;
use Nette\Object;
use Nette\Security,
	Nette\Utils\Strings;

/**
 * Users authenticator.
 */
class Authenticator extends Object implements Security\IAuthenticator {

	/** @var User */
	private $user;
	private $salt;

	public function injectUser(User $user) {
		$this->user = $user;
	}
	public function injectSalt($salt) {
		$this->salt = $salt;
	}

	/**
	 * Performs an authentication.
	 * @return Security\Identity
	 * @throws Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($nickname, $password) = $credentials;
		$user = $this->user->findByNickname($nickname);
		$user = WatchingActiveRow::fromActiveRow($user);
		$user->last_login = new DateTime();
		$this->user->save($user);

		if (!$user) {
			throw new Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
		}

		if ($user->password !== $this->calculateHash($password)) {
			throw new Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
		}

		$arr = $user->toArray();
		unset($arr['password']);
		$arr['role'] = Strings::lower($arr['role']);
		return new Security\Identity($user->user_id, $arr['role'], $arr);
	}



	/**
	 * Computes salted password hash.
	 * @param  string
	 * @return string
	 */
	public function calculateHash($password) {
		if(empty($this->salt)) {
			throw new InvalidStateException('No salt present, check config.');
		}
		return hash('sha256', $this->salt . "#" . $password);
	}

}
