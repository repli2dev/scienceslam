<?php

use Nette\Application\UI;

class UserPresenter extends BasePresenter {

	protected $resource = 'user';

	/** @var \Muni\ScienceSlam\Model\User */
	private $userDAO;

	public function injectUserDAO(\Muni\ScienceSlam\Model\User $userDAO) {
		$this->userDAO = $userDAO;
	}

	public function startup() {
		parent::startup();
		$this->perm();
	}

	public function actionDefault() {
		$data = $this->userDAO->findAll();
		$this->template->data = $data;
		$this->template->roles = \Muni\ScienceSlam\Model\User::getRoles();
	}

	public function actionEdit($id) {
		$data = $this->userDAO->find($id);
		if($data === FALSE) {
			throw new \Nette\Application\BadRequestException();
		}
		$this->getComponent('editForm')->setDefaults($data);
		$this->template->data = $data;
	}
	public function actionDelete($id) {
		$data = $this->userDAO->find($id);
		if($data === FALSE) {
			throw new \Nette\Application\BadRequestException();
		}
		$this->template->data = $data;
	}

	protected function createComponentAddForm() {
		$form = $this->prepareForm();
		$form['password']->setRequired('Zadejte, prosím, heslo.');
		$form->addSubmit('send', 'Přidat uživatele');
		$form->onSuccess[] = $this->addFormSucceeded;
		return $form;
	}

	public function addFormSucceeded(\Nette\Forms\Form $form) {
		$values = $form->getValues();
		$values['password'] = $this->user->getAuthenticator()->calculateHash($values['password']);
		$values['inserted'] = new \Nette\DateTime();

		$user = $this->userDAO->create();
		$user->addAll($values);
		try {
			$this->userDAO->save($user);
		} catch(PDOException $ex) {
			$form->addError('Použitá přezdívka již existuje, zadejte, prosím, jinou.');
			return;
		}
		$this->flashMessage('Uživatel byl úspěšně přidán.', 'success');
		$this->redirect('default');
	}

	protected function createComponentEditForm() {
		$form = $this->prepareForm();
		$form['password']->setOption('description', '(pouze pokud se mění)');
		$form->addSubmit('send', 'Upravit uživatele');
		$form->onSuccess[] = $this->editFormSucceeded;
		return $form;
	}

	public function editFormSucceeded(\Nette\Forms\Form $form) {
		$values = $form->getValues();
		if(empty($values['password'])) {
			unset($values['password']);
		} else {
			$values['password'] = $this->user->getAuthenticator()->calculateHash($values['password']);
		}

		$user = $this->userDAO->find($this->getParameter('id'));
		$user = \JanDrabek\Database\WatchingActiveRow::fromActiveRow($user);
		$user->addAll($values);
		try {
			$this->userDAO->save($user);
		} catch(PDOException $ex) {
			$form->addError('Použitá přezdívka již existuje, zadejte, prosím, jinou.');
			return;
		}
		$this->flashMessage('Uživatel byl úspěšně upraven.', 'success');
		$this->redirect('default');
	}

	protected function createComponentDeleteForm() {
		$form = new UI\Form();
		$form->addSubmit('yes', 'Ano');
		$form->addSubmit('no', 'Ne');
		$form->onSuccess[] = $this->deleteFormSucceeded;
		return $form;
	}

	public function deleteFormSucceeded(\Nette\Forms\Form $form) {
		$values = $form->getValues();
		$userCount = $this->userDAO->findAll()->count();
		if($userCount == 1) {
			$form->addError('Poslední uživatel nemůže být smazán.');
			return;
		}
		if($form['yes']->isSubmittedBy()) {
			$this->userDAO->delete($this->getParameter('id'));
			$this->flashMessage('Uživatel byl úspěšně smazán.', 'success');
		}
		$this->redirect('default');
	}

	private function prepareForm() {
		$form = new UI\Form;
		$form->addGroup('Přihlašování');
		$form->addText('nickname', 'Přihlašovací jméno:')
			->setRequired('Zadejte, prosím, přihlašovací jméno.');
		$form->addPassword('password', 'Heslo:');

		$form->addGroup('Ostatní');
		$form->addText('name', 'Skutečné jméno:')
			->setRequired('Zadejte, prosím, skutečné jméno.');
		$form->addSelect('role', 'Role:', \Muni\ScienceSlam\Model\User::getRoles())
			->setRequired('Vyberte, prosím, roli.');

		$form->setCurrentGroup(null);
		return $form;
	}

}
