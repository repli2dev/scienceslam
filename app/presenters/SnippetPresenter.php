<?php

use Muni\ScienceSlam\Utils\TexyFactory;
use Nette\Application\UI;
use Nette\Utils\Html;

class SnippetPresenter extends BasePresenter {

	protected $resource = 'snippet';

	/** @var \Muni\ScienceSlam\Model\Snippet */
	private $snippetDAO;

	public function injectSnippetDAO(\Muni\ScienceSlam\Model\Snippet $snippetDAO) {
		$this->snippetDAO = $snippetDAO;
	}

	public function startup() {
		parent::startup();
		$this->perm();
	}

	public function actionAdd() {

	}

	public function actionDefault() {
		$data = $this->snippetDAO->findAll();
		$this->template->data = $data;
	}

	public function actionEdit($id) {
		$data = $this->snippetDAO->find($id);
		if($data === FALSE) {
			throw new \Nette\Application\BadRequestException();
		}
		$this->getComponent('editForm')->setDefaults($data);
		$this->template->data = $data;
	}
	public function actionDelete($id) {
		$data = $this->snippetDAO->find($id);
		if($data === FALSE) {
			throw new \Nette\Application\BadRequestException();
		}
		$this->template->protected = $data->is_protected;
		$this->template->data = $data;
	}

	protected function createComponentAddForm() {
		$form = $this->prepareForm();
		$form->addSubmit('send', 'Přidat');
		$form->onSuccess[] = $this->addFormSucceeded;
		return $form;
	}

	public function addFormSucceeded(\Nette\Forms\Form $form) {
		$values = $form->getValues();
		$values['inserted'] = new \Nette\DateTime();
		$values['key'] = \Nette\Utils\Strings::webalize($values['key']);

		$row = $this->snippetDAO->create();
		$row->addAll($values);

		try {
			$this->snippetDAO->save($row);
		} catch(PDOException $ex) {
			$form->addError('Snippet s tímto klíčem již existuje, zadejte, prosím, jiný klíč.');
			return;
		}
		$this->flashMessage('Snippet byl úspěšně přidán.', 'success');
		$this->redirect('default');
	}

	protected function createComponentEditForm() {
		$form = $this->prepareForm();
		$form->addSubmit('send', 'Uložit');
		$form->onSuccess[] = $this->editFormSucceeded;
		return $form;
	}

	public function editFormSucceeded(\Nette\Forms\Form $form) {
		$values = $form->getValues();
		$values['key'] = \Nette\Utils\Strings::webalize($values['key']);

		$row = $this->snippetDAO->find($this->getParameter('id'));
		$row = \JanDrabek\Database\WatchingActiveRow::fromActiveRow($row);
		$row->addAll($values);

		try {
			$this->snippetDAO->save($row);
		} catch(PDOException $ex) {
			$form->addError('Snippet s tímto klíčem již existuje, zadejte, prosím, jiný klíč.');
			return;
		}
		$this->flashMessage('Snippet byl úspěšně upraven.', 'success');
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
		$snippet = $this->snippetDAO->find($this->getParameter('id'));
		if (!$snippet) {
			$form->addError('Snippet, který chcete smazat neexistuje');
			return;
		}
		if ($snippet->is_protected) {
			$form->addError('Snippet, který chcete smazat je momentálně chráněn proti smazání.');
			return;
		}
		if($form['yes']->isSubmittedBy()) {
			$this->snippetDAO->delete($this->getParameter('id'));
			$this->flashMessage('Snippet byl úspěšně smazán.', 'success');
		}
		$this->redirect('default');
	}

	private function prepareForm() {
		$form = new UI\Form;
		$form->getElementPrototype()->class('wide');
		$form->addGroup('Obecné a servisní');
		$form->addText('key', 'Klíč')
			->setRequired('Vyplňte, prosím, klíč snippetu pod kterým bude dostupný.')
			->setOption('description', 'musí být unikátní');
		$form->addCheckbox('is_protected', 'Chránit před smazáním');
		$form->addGroup('Obsah');
		$form->addTextArea('content', '', 53, 15)
			->setOption('description', TexyFactory::getSyntaxHelp('snippet'))
			->getControlPrototype()->class = 'full-width';
		$form->setCurrentGroup(null);

		return $form;
	}
}


