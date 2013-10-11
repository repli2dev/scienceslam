<?php

use Nette\Application\UI;

class SlamPresenter extends BasePresenter {

	protected $resource = 'slam';

	/** @var \Muni\ScienceSlam\Model\Event */
	private $eventDAO;

	public function injectEventDAO(\Muni\ScienceSlam\Model\Event $eventDAO) {
		$this->eventDAO = $eventDAO;
	}

	public function startup() {
		parent::startup();
		$this->perm();
	}

	public function actionList() {
		$data = $this->eventDAO->findAll();
		$this->template->data = $data;
	}

	public function actionEdit($id) {
		$data = $this->eventDAO->find($id);
		if($data === FALSE) {
			throw new \Nette\Application\BadRequestException();
		}
		$this->getComponent('editForm')->setDefaults($data);
		$this->template->data = $data;
	}
	public function actionDelete($id) {
		$data = $this->eventDAO->find($id);
		if($data === FALSE) {
			throw new \Nette\Application\BadRequestException();
		}
		$this->template->data = $data;
	}

	protected function createComponentAddForm() {
		$form = $this->prepareForm();
		$form->addSubmit('send', 'Přidat slam');
		$form->onSuccess[] = $this->addFormSucceeded;
		return $form;
	}

	public function addFormSucceeded(\Nette\Forms\Form $form) {
		$values = $form->getValues();
		// Add checks for dates etc.

		$row = $this->eventDAO->create();
		$row->addAll($values);
		try {
			$this->eventDAO->save($row);
		} catch(PDOException $ex) {
			$form->addError('URL identifikace již existuje, zadejte, prosím, jinou.');
			return;
		}
		$this->flashMessage('Slam byl úspěšně přidán.', 'success');
		$this->redirect('list');
	}

	protected function createComponentEditForm() {
		$form = $this->prepareForm();
		$form->addSubmit('send', 'Upravit slam');
		$form->onSuccess[] = $this->editFormSucceeded;
		return $form;
	}

	public function editFormSucceeded(\Nette\Forms\Form $form) {
		$values = $form->getValues();

		$row = $this->eventDAO->find($this->getParameter('id'));
		$row = \JanDrabek\Database\WatchingActiveRow::fromActiveRow($row);
		$row->addAll($values);
		try {
			$this->eventDAO->save($row);
		} catch(PDOException $ex) {
			$form->addError('URL identifikace již existuje, zadejte, prosím, jinou.');
			return;
		}
		$this->flashMessage('Slam byl úspěšně upraven.', 'success');
		$this->redirect('list');
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
		if($form['yes']->isSubmittedBy()) {
			$this->eventDAO->delete($this->getParameter('id'));
			$this->flashMessage('Slam byl úspěšně smazán.', 'success');
		}
		$this->redirect('list');
	}

	private function prepareForm() {
		$form = new UI\Form;
		$form->addGroup('Obecné');
		$form->addText('name', 'Název')
			->setRequired('Vyplňte, prosím, název.');
		$form->addTextArea('description', 'Popis', 40, 15)
			->setRequired('Vyplňte, prosím, popis.');
		$form->addGroup('Termíny');
		$form->addText('date', 'Den konání')
			->setRequired('Vyplňte, prosím, termín konání.')
			->setOption('description', 've tvaru 2012-12-24');
		$form->addText('time', 'Čas konání')
			->setRequired('Vyplňte, prosím, čas konání.')
			->setOption('description', 've tvaru 10:12:14');
		$form->addText('registration_opened', 'Otevření registrace')
			->setRequired('Vyplňte, prosím, čas otevření registrace')
			->setOption('description', 've tvaru 2012-12-24 10:12:14');
		$form->addText('registration_closed', 'Uzavření registrace')
			->setRequired('Vyplňte, prosím, čas uzavření registrace')
			->setOption('description', 've tvaru 2012-12-24 10:12:14');
		$form->addGroup('Servisní');
		$form->addText('url', 'URL identifikace')
			->setRequired('Vyplňte, prosím, identifikaci slamu pro potřeby URL adresy.')
			->setOption('description', 'nutné pro URL adresu, unikátní');
		$form->addTextArea('extra_styles', 'Extra CSS', 40, 15);
		$form->addCheckbox('hidden', 'Skrytý');

		$form->setCurrentGroup(null);
		return $form;
	}

}
