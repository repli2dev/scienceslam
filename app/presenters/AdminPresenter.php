<?php

use Muni\ScienceSlam\Utils\TexyFactory;
use Nette\Application\UI;

class AdminPresenter extends BasePresenter {

	protected $resource = 'user-shared';

	const DASHBOARD_FILE = __DIR__ . '/../../data/dashboard.texy';

	/** @var \Muni\ScienceSlam\Model\User */
	private $userDAO;

	public function injectUserDAO(\Muni\ScienceSlam\Model\User $userDAO) {
		$this->userDAO = $userDAO;
	}

	public function actionDefault($edit = false) {
		$this->perm();
		$this->template->edit = (bool) $edit;
		if (file_exists(static::DASHBOARD_FILE) && is_file(static::DASHBOARD_FILE) && is_readable(static::DASHBOARD_FILE)) {
			$this->template->dashboard = file_get_contents(static::DASHBOARD_FILE);
			if ($edit) {
				$form = $this->getComponent('editForm');
				$form->setDefaults(['modified' => filemtime(static::DASHBOARD_FILE), 'content' => $this->template->dashboard]);
			}
		}
	}

	public function actionLogin() {
		if($this->can('user-shared')) {
			$this->redirect('default');
		}
	}

	public function actionLogout() {
		$this->perm();
		$this->user->logout(true);
		$this->flashMessage('Byli jste úspěšně odhlášeni.', 'success');
		$this->redirect('login');
	}


	protected function createComponentLoginForm() {
		$form = new UI\Form;
		$form->addText('nickname', 'Přihlašovací jméno:')
			->setRequired('Zadejte, prosím, své přihlašovací jméno.');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Zadejte, prosím, své heslo.');

		$form->addCheckbox('remember', 'Zapamatovat si mě');

		$form->addSubmit('send', 'Přihlásit');

		$form->onSuccess[] = $this->loginFormSucceeded;
		return $form;
	}

	protected function createComponentChangeForm() {
		$form = new UI\Form;
		$form->addPassword('old', 'Současné heslo')
			->setRequired('Zadejte, prosím, své současné heslo.');
		$form->addPassword('new', 'Nové heslo')
			->setRequired('Zadejte, prosím, nové heslo.');
		$form->addPassword('new2', 'Nové heslo znovu')
			->setRequired('Zadejte, prosím, nové heslo pro kontrolu.')
			->addRule(\Nette\Forms\Form::EQUAL, 'Nová hesla se musejí shodovat.', $form['new']);
		$form->addSubmit('send', 'Změnit heslo');
		$form->onSuccess[] = $this->changeFormSucceeded;
		return $form;
	}


	public function changeFormSucceeded($form) {
		$values = $form->getValues();
		$user = $this->userDAO->find($this->user->getId());
		$user = \JanDrabek\Database\WatchingActiveRow::fromActiveRow($user);
		if($user->password != $this->user->getAuthenticator()->calculateHash($values['old'])) {
			$form->addError('Zadané heslo není správně.');
			return;
		}
		$user->password = $this->user->getAuthenticator()->calculateHash($values['new']);
		$this->userDAO->save($user);
		$this->flashMessage('Vaše heslo bylo úspěšně změněno.', 'success');
		//$this->redirect('this');
	}

	public function loginFormSucceeded($form) {
		$values = $form->getValues();

		if ($values->remember) {
			$this->getUser()->setExpiration('14 days', FALSE);
		} else {
			$this->getUser()->setExpiration('20 minutes', TRUE);
		}

		try {
			$this->getUser()->login($values->nickname, $values->password);
			$this->redirect('Admin:');
			
		} catch (Nette\Security\AuthenticationException $e) {
			if($e->getCode() == Nette\Security\IAuthenticator::IDENTITY_NOT_FOUND) {
				$form->addError('Učet s touto přezdívkou neexistuje.');
			} else {
				$form->addError('Heslo je špatné.');
			}
		}
	}

	public function createComponentEditForm()
	{
		$form = new UI\Form();
		$form->getElementPrototype()->class('wide');
		$form->addTextArea('content', '', 53, 15)
			->setOption('description', TexyFactory::getSyntaxHelp('dashboard'))
			->getControlPrototype()->class = 'full-width';
		$form->addHidden('modified');
		$form->addSubmit('cancel', 'Zrušit')
			->setValidationScope(false)
			->getControlPrototype()->class = 'button button-gray';
		$form->addSubmit('send', 'Uložit stránku');
		$form->onSuccess[] = $this->editFormSucceeded;
		return $form;
	}

	public function editFormSucceeded($form)
	{
		if ($form['cancel']->isSubmittedBy()) {
			$this->redirect('this', ['edit' => false]);
		}
		$values = $form->getValues();
		$fileModification = file_exists(static::DASHBOARD_FILE) && is_file(static::DASHBOARD_FILE) && is_readable(static::DASHBOARD_FILE) ? filemtime(static::DASHBOARD_FILE) : null;
		if ($fileModification !== null && $values->modified < $fileModification) {
			$form->addError('Nástěnka byla během vaší editace upravena, zazálohujte si své změny a začněte znovu.');
			return;
		}
		file_put_contents(static::DASHBOARD_FILE, $values->content);
		$this->flashMessage('Nástěnka byla úspěšně upravena.');
		$this->redirect('this', ['edit' => false]);
	}
}
