<?php

use Nette\Application\UI;

class PagePresenter extends BasePresenter {

	protected $resource = 'page';

	/** @var \Muni\ScienceSlam\Model\Event */
	private $eventDAO;

	/** @var \Muni\ScienceSlam\Model\Page */
	private $pageDAO;

	/** @var \Muni\ScienceSlam\Model\Block */
	private $blockDAO;

	public function injectEventDAO(\Muni\ScienceSlam\Model\Event $eventDAO) {
		$this->eventDAO = $eventDAO;
	}
	public function injectPageDAO(\Muni\ScienceSlam\Model\Page $pageDAO) {
		$this->pageDAO = $pageDAO;
	}
	public function injectBlockDAO(\Muni\ScienceSlam\Model\Block $blockDAO) {
		$this->blockDAO = $blockDAO;
	}

	public function startup() {
		parent::startup();
		if($this->getAction() !== 'show') {	// Only show is for public access
			$this->perm();
		}
	}

	public function actionShow($eventUrl, $pageUrl) {
		$event = $this->eventDAO->findByUrl($eventUrl);
		if($event === FALSE) {
			throw new \Nette\Application\BadRequestException;
		}
		$page = $this->pageDAO->findByUrlAndEventId($pageUrl, $event->event_id);
		if($page === FALSE) {
			throw new \Nette\Application\BadRequestException;
		}
		$blocks = $this->blockDAO->findByPageId($page->page_id);
		$this->template->page = $page;
		$this->template->event = $event;
		$this->template->blocks = $blocks;
		$this->template->admin = $this->user->isInRole('manager') || $this->user->isInRole('admin');
	}

	public function actionAdd($eventId) {
		$data = $this->eventDAO->find($eventId);
		if($data === FALSE) {
			throw new \Nette\Application\BadRequestException;
		}
		$this->template->event = $data;
	}

	public function actionList($eventId) {
		$event = $this->eventDAO->find($eventId);
		if($event === FALSE) {
			throw new \Nette\Application\BadRequestException;
		}
		$this->template->event = $event;
		$data = $this->pageDAO->findByEventId($event);
		$this->template->data = $data;
	}

	public function actionEdit($id) {
		$data = $this->pageDAO->find($id);
		if($data === FALSE) {
			throw new \Nette\Application\BadRequestException();
		}
		$event = $this->eventDAO->find($data->event_id);
		$this->getComponent('editForm')->setDefaults($data);
		$this->template->data = $data;
		$this->template->event = $event;
	}
	public function actionDelete($id) {
		$data = $this->pageDAO->find($id);
		if($data === FALSE) {
			throw new \Nette\Application\BadRequestException();
		}
		$event = $this->eventDAO->find($data->event_id);
		$this->template->data = $data;
		$this->template->event = $event;
	}

	protected function createComponentAddForm() {
		$form = $this->prepareForm();
		$form->addSubmit('send', 'Přidat stránku');
		$form->onSuccess[] = $this->addFormSucceeded;
		return $form;
	}

	public function addFormSucceeded(\Nette\Forms\Form $form) {
		$values = $form->getValues();
		$values['event_id'] = $this->getParameter('eventId');
		$values['inserted'] = new \Nette\DateTime();
		// Add checks for dates etc.

		$row = $this->pageDAO->create();
		$row->addAll($values);
		try {
			$this->pageDAO->save($row);
		} catch(PDOException $ex) {
			$form->addError('URL identifikace již existuje, zadejte, prosím, jinou.');
			return;
		}
		$this->flashMessage('Stránka byla úspěšně přidána.', 'success');
		$this->redirect('list', $this->getParameter('eventId'));
	}

	protected function createComponentEditForm() {
		$form = $this->prepareForm();
		$form->addSubmit('send', 'Upravit stránku');
		$form->onSuccess[] = $this->editFormSucceeded;
		return $form;
	}

	public function editFormSucceeded(\Nette\Forms\Form $form) {
		$values = $form->getValues();

		$row = $this->pageDAO->find($this->getParameter('id'));
		$row = \JanDrabek\Database\WatchingActiveRow::fromActiveRow($row);
		$row->addAll($values);
		try {
			$this->pageDAO->save($row);
		} catch(PDOException $ex) {
			$form->addError('URL identifikace již existuje, zadejte, prosím, jinou.');
			return;
		}
		$this->flashMessage('Stránka byla úspěšně upravena.', 'success');
		$this->redirect('list', $row->event_id);
	}

	protected function createComponentDeleteForm() {
		$form = new UI\Form();
		$form->addSubmit('yes', 'Ano');
		$form->addSubmit('no', 'Ne');
		$form->onSuccess[] = $this->deleteFormSucceeded;
		return $form;
	}

	public function deleteFormSucceeded(\Nette\Forms\Form $form) {
		$page = $this->pageDAO->find($this->getParameter('id'));
		if($form['yes']->isSubmittedBy()) {
			$this->pageDAO->delete($this->getParameter('id'));
			$this->flashMessage('Stránka byla úspěšně smazána.', 'success');
		}
		$this->redirect('list', $page->event_id);
	}

	private function prepareForm() {
		$form = new UI\Form;
		$form->addGroup('Obecné a servisní');
		$form->addText('name', 'Název')
			->setRequired('Vyplňte, prosím, název.');
		$form->addText('url', 'URL identifikace')
			->setRequired('Vyplňte, prosím, identifikaci slamu pro potřeby URL adresy.')
			->setOption('description', 'nutné pro URL adresu, unikátní');
		$form->addCheckbox('is_default', 'Výchozí stránka slamu');

		$form->addGroup('Bloky');
		$form->addCheckbox('is_block_page', 'Použít');
		$form->addGroup('Textová stránka');
		$form->addTextArea('content', '', 53, 15);
		$form->addGroup('Galerie');
		$form->addText('gallery_path', 'Cesta ke galerii');
		$form->setCurrentGroup(null);
		return $form;
	}

	private function populateByLayout($layoutId) {

	}

}
